<?
include "../autorisation.php";
if(isset($_GET["id"]) && isset($_GET["name"])) {

	$db->query("SELECT * FROM catalogue WHERE id='".intval($_GET["id"])."'");
	if($db->next_record()) {
		foreach($db->Record as $k=>$v) if(!is_string($k)) unset($db->Record[$k]);
		$OUT=array("page"=>$db->Record,"blocks"=>array());
		$db->query("SELECT * FROM content WHERE catalogue_id='".$db->Record["id"]."' ORDER BY id");
		while($db->next_record()) {
			foreach($db->Record as $k=>$v) if(!is_string($k)) unset($db->Record[$k]);
			$OUT["blocks"][]=$db->Record;
		}
		$db->query("INSERT INTO savedpagetypes (ptname,ptype) VALUES ('".htmlspecialchars($_GET["name"])."','".str_replace("'","**q**",serialize($OUT))."')");

		$db->query("UPDATE catalogue SET spec='".getLastID()."' WHERE id='".intval($_GET["id"])."'");

		echo "saved";
	}
} elseif(isset($_GET["id"]) && isset($_GET["newtype"])) {
	$_GET["newtype"]=intval($_GET["newtype"]);
	$db->query("SELECT * FROM savedpagetypes WHERE id='".$_GET["newtype"]."'");
	if($db->next_record()) {
		$OUT=unserialize(str_replace("**q**","'",$db->Record["ptype"]));	
		
           
                //$db->query("SELECT cod FROM catalogue WHERE id='".intval($_GET["id"])."'");
		//if($db->next_record()) {
			$cod=intval($_GET["id"]);//$db->Record[0];
			$db->query("UPDATE catalogue SET attr='1', tpl='".$OUT["page"]["tpl"]."', spec='".$_GET["newtype"]."' WHERE id='$cod'");
			$db->query("DELETE FROM content WHERE catalogue_id='$cod'");
			$Ass=array();
			foreach($OUT["blocks"] as $val) {
                                
                                $va1=array();
                                foreach($val as $k=>$v) {
                                  $k=strtolower($k);
                                  if($k=='access') $k="access_";
                                  $va1[$k]=$v;
                                }
                                $val=$va1;unset($va1);
			
				if($val["cpid"] && isset($Ass[$val["cpid"]])) $val["cpid"]=$Ass[$val["cpid"]];
				$val["catalogue_id"]=$cod;
				$keys=array();
				$vals=array();
				foreach($val as $k=>$v) if($k!="id" && $k!="lock_user" && $k!="lock_time") {                           				         
					$keys[]=$k;
					$vals[]=$v;
				}
				$db->query("INSERT INTO content (".join(",",$keys).") VALUES ('".join("','",$vals)."')");
				
				//echo $db->LastQuery;
				
				$id=getLastID();
				$Ass[$val["id"]]=$id;
			}
			echo "changed";
		//}
	}	
}
?>