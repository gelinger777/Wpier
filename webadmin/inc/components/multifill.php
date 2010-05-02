<?
/*
*  ��������� "������������ ��� ������"
*  ����� ����� ������
*  v. 1.0
*/

class T_multifill {
  var $PROP=array(
    "caption"=>"",
    "visibility"=>"yes",
    "name"=>"",
    "valtable"=>"",
    "valkey"=>"",
    "valvalue"=>"",
    "valwhere"=>"",
    "linktable"=>"",
    "linkkey"=>"",
    "linkvalue"=>"",
    "thisfolder"=>"",
    "autoload"=>1,
    "toLegend"=>"",
    "fromLegend"=>"",
    "cleartext"=>"",
    "size"=>5,
    "width"=>250,
    "height"=>120
    );
  
  var $required=0;
  var $type="multi";

  var $max_filter_size=19;

  var $PROPTAB=array(
   "valtable"=>array("valkey", "valvalue"),    
   );
  var $THISFOLDERS=array(
   "thisfolder"
  ); 

  var $EVENTS=array(
    "onAfterDbChanging"=>1,
    "onBeforeDbChanging"=>1,
	"onDelete"=>1
      );
  var $blockCod="";

  function try_post() {
	if(isset($_POST["load_options_obj"]) && $_POST["load_options_obj"]==$this->PROP["name"]) {
		ob_end_clean();
		global $db;

		if(isset($_POST["load_options_folder_dep"]) && ereg("[a-z0-9_]{1,}",$_POST["load_options_folder_dep"])) {
			
			$id=0;
			if(isset($_GET["ch"])) {
			  $id=intval($_GET["ch"]);
			  global $PROPERTIES;
			  if(isset($PROPERTIES["FIX_ID_TO_COD"])) {
			    $db->query("SELECT ".$PROPERTIES["FIX_ID_TO_COD"]." FROM ".$PROPERTIES["tbname"]." WHERE id=$id");
			    if($db->next_record()) $id=$db->Record[0];
			  }
			}
			
			$db->query("SELECT ".$this->PROP["valkey"].",".$this->PROP["valvalue"]." FROM ".$this->PROP["valtable"]." ".($_POST["load_options_values"]? "WHERE ".$_POST["load_options_folder_dep"]."='".join("' or ".$_POST["load_options_folder_dep"]."='",explode(",",addslashes($_POST["load_options_values"])))."'":"").($id? " and ".$this->PROP["valkey"]." not in (SELECT ".$this->PROP["linkkey"]." FROM ".$this->PROP["linktable"]." WHERE ".$this->PROP["linkvalue"]."='$id')":"")."  ORDER BY ".$this->PROP["valvalue"]);
			
			$i=0;
			while($db->next_record()) if($db->Record[1]) echo ($i++? ",":"")."['".$db->Record[0]."','".$db->Record[1]."']";
		}
		exit;
	}
  }

  function str2params ($name,$pString) {
    $this->PROP["name"]=$name;
    
    $this->PROP["toLegend"]=DLG("Selected");
    $this->PROP["fromLegend"]=DLG("Available");
    $this->PROP["cleartext"]=DLG("Clear");  
    
    $pString=explode("|",$pString);
        
    $this->PROP["caption"]=$pString[2];

    if(isset($pString[3])) {
      $s=explode("/",$pString[3]);
      $this->PROP["width"]=$s[0];
      $this->PROP["height"]=$s[1];
    }

    $pString=explode("/",$pString[1]);

    $this->PROP["valtable"]=$pString[0];
    $this->PROP["valkey"]=$pString[1];
    $this->PROP["valvalue"]=$pString[2];
    $this->PROP["linktable"]=$pString[3];
    $this->PROP["linkkey"]=$pString[4];
    $this->PROP["linkvalue"]=$pString[5];
    $this->PROP["thisfolder"]=$pString[6];
    if(isset($pString[7])) $this->PROP["valwhere"]=$pString[7];
	if(isset($pString[8])) $this->PROP["autoload"]=$pString[8];
	
  }

  function params2str () {
    return "multifill|".$this->PROP["valtable"]."/".$this->PROP["valkey"]."/".$this->PROP["valvalue"]."/".$this->PROP["linktable"]."/".$this->PROP["linkkey"]."/".$this->PROP["linkvalue"]."/".$this->PROP["thisfolder"].($this->PROP["valwhere"]? "/".$this->PROP["valwhere"]:"")."|".$this->PROP["caption"]."|".$this->PROP["width"]."/".$this->PROP["height"];
  }

  function mkForm($key,$val="") {
  global $PROPERTIES, $db, $TEXTS,$LOG_MULTI_SELECT_LOAD; 

	if(!isset($LOG_MULTI_SELECT_LOAD)) $LOG_MULTI_SELECT_LOAD=0;

	if(isset($_GET["ch"])) $val=intval($_GET["ch"]);

	$rec=array();
    $db->query("SELECT DISTINCT ".$this->PROP["valkey"].",".$this->PROP["valvalue"]." FROM ".$this->PROP["valtable"].($this->PROP["valwhere"]? " WHERE ".$this->PROP["valwhere"]:"")." ORDER BY ".$this->PROP["valvalue"]);
	while($db->next_record()) if($db->Record[1]) $rec[$db->Record[0]]="['".$db->Record[0]."','".$db->Record[1]."']";
   
   $rec1=array();

   if($this->PROP["thisfolder"]) { 
     $db->query("SELECT ".$this->PROP["thisfolder"]." FROM ".$PROPERTIES["tbname"]." WHERE id='".$val."'");
     if ($db->next_record()) {
      $db->query("SELECT ".$this->PROP["valtable"].".".$this->PROP["valkey"].", ".$this->PROP["valtable"].".".$this->PROP["valvalue"]." FROM ".$this->PROP["valtable"]." LEFT JOIN ".$this->PROP["linktable"]." ON ".$this->PROP["valtable"].".".$this->PROP["valkey"]."=".$this->PROP["linktable"].".".$this->PROP["linkkey"]." WHERE ".$this->PROP["linktable"].".".$this->PROP["linkvalue"]."='".$db->Record[0]."' ORDER BY ".$this->PROP["valtable"].".".$this->PROP["valvalue"]);
      while($db->next_record()) if($db->Record[1]) {
        $rec1[]="['".$db->Record[0]."','".$db->Record[1]."']";
	if(isset($rec[$db->Record[0]])) unset($rec[$db->Record[0]]);
      }
     }
    }
    if(!$this->PROP["autoload"]) $rec=array();

   return array($this->PROP["caption"],
	   "<span id='multiselect-$key'></span>".($LOG_MULTI_SELECT_LOAD++? "":'
			<link rel="stylesheet" type="text/css" href="ext/multiselect.css"/>
			<script type="text/javascript" src="ext/DDView.js"></script>
			<script type="text/javascript" src="ext/MultiSelect.js"></script>
            <script type="text/javascript" src="ext/ItemSelector.js"></script>
	   '),
	   "new Ext.ux.ItemSelector({
		    id:'$key',
			name:'$key',
            fieldLabel:'',
            dataFields:['key', 'val'],
            toData:[".join(",",$rec1)."],
            width:".($this->PROP["width"]*2+200).",
			//height:500,
            msWidth:".$this->PROP["width"].",
			el:'multiselect-$key',
            msHeight:".$this->PROP["height"].",
            valueField:'key',
            displayField:'val',
			".($this->required? "allowBlank:false,":"")."
            imagePath:'ext/images/',
            toLegend:'".$this->PROP["toLegend"]."',
            fromLegend:'".$this->PROP["fromLegend"]."',
            fromData:[".join(",",$rec)."],
            /*toTBar:[{
                text:'".$this->PROP["cleartext"]."',
                handler:function(){
                    var i=Ext.getCmp('$key');
					i.reset.call(i);
                }
            }], */
			depend_function:function(cod,fold,res) {
                /*if(res) {
					var i=Ext.getCmp('$key');
				    i.reset.call(i);			    
				}    */
				Ext.Ajax.request({
					  url: '".$_SERVER["REQUEST_URI"]."',  // ������ ������ ����
					  success: function(response) {
						if(response.responseText=='ERR') return false; 
						else {
							var v=[];

    						eval('v=['+response.responseText+']');

							Ext.getCmp('$key').fromMultiselect.store.loadData(v);
						}              
					  },
					  params: {
						  load_options_obj:'$key',
						  load_options_values:cod,
						  load_options_folder_dep:fold
					  }
				   });
				//alert(cod);
			}
        }).render();");
  }

  function mkList($val,$log=0) {
    global $db,$XLS_PROCCESS_LOG,$PROPERTIES,$BLK;

   $this->try_post();
 
    if(isset($BLK) && !$this->blockCod) {
      $this->blockCod=parse_tmp(strtoupper($this->PROP["name"]),"BLK");      
    }
	$id=0;
	if(!$this->PROP["thisfolder"] || $this->PROP["thisfolder"]=="id") $id=$val;
	elseif(isset($PROPERTIES["tbname"]) && $PROPERTIES["tbname"]) {

		$db->query("SELECT DISTINCT ".$this->PROP["thisfolder"]." FROM ".$PROPERTIES["tbname"]." WHERE id='".$val."'");
		if ($db->next_record()) $id=$db->Record[0];
	} else {
	  return "";
	}
       
    
	if($id) {
	  $db->query("SELECT DISTINCT ".$this->PROP["valtable"].".* FROM ".$this->PROP["valtable"].",".$this->PROP["linktable"]." WHERE ".$this->PROP["valtable"].".".$this->PROP["valkey"]."=".$this->PROP["linktable"].".".$this->PROP["linkkey"]." and ".$this->PROP["linktable"].".".$this->PROP["linkvalue"]."='".$id."' ORDER BY ".$this->PROP["valtable"].".".$this->PROP["valvalue"]);
	  //echo $db->LastQuery."<br>";
	  $val=array(); 
          if($this->blockCod) send2blk($this->PROP["name"]."_count",$db->num_rows());    
          while($db->next_record()) {
              if($this->blockCod) {
                $s=$this->blockCod;
                foreach($db->Record as $k=>$v) $s=str_replace("%$k%",$v,$s);
                $val[]=$s;
              } else $val[]=$db->Record[$this->PROP["valvalue"]];
          }
          if($this->blockCod) $val=join(" ",$val);
          else {
           if(isset($XLS_PROCCESS_LOG)) $val=join("\r\n",$val);
           else $val=join("<br>",$val);
          }          
	}
	return $val;
  }

  function mkDBFolder() {
    return "";
  }

  function getUpdateVals($key,$val) {
    return "";
  }

  function getFilterSearch($key,$val) {
  global $db;
 
	$val["value"]=explode(",",$val["value"]);	
	$x=array();
    //if($this->LenSprav()<$this->max_filter_size) {		
		$x=array();
		if(isset($db->Tables[strtoupper($this->PROP["linktable"])])) {		  
		  
		  foreach($val["value"] as $v) $x[]='"'.$this->PROP["linkkey"].'"'."='". addslashes($v)."'";
		  if(count($x)) {
			return $this->PROP["thisfolder"]." in (SELECT \"".$this->PROP["linkvalue"]."\" FROM ".$this->PROP["linktable"]." WHERE ".join(" or ",$x).")";
		  }
		} else {
		  foreach($val["value"] as $v) $x[]=$this->PROP["linkkey"]."='". addslashes($v)."'";

		  if(count($x)) {
			return $this->PROP["thisfolder"]." in (SELECT ".$this->PROP["linkvalue"]." FROM ".$this->PROP["linktable"]." WHERE ".join(" or ",$x).")";
		  }
		}
		
		
		
		
		
	/*} else {
		foreach($val["value"] as $v) if(trim($v)) {
			$x[]="lower(".$this->PROP["valvalue"].") like '".strtolower(addslashes(trim($v)))."%'";
		}
		if(count($x)) {

//echo $this->PROP["thisfolder"]." in (SELECT ".$this->PROP["linkvalue"]." FROM ".$this->PROP["linktable"]." WHERE ".$this->PROP["linkkey"]." in (SELECT ".$this->PROP["valkey"]." FROM ".$this->PROP["valtable"]." WHERE ".join(" or ",$x)."))";

			return $this->PROP["thisfolder"]." in (SELECT ".$this->PROP["linkvalue"]." FROM ".$this->PROP["linktable"]." WHERE ".$this->PROP["linkkey"]." in (SELECT ".$this->PROP["valkey"]." FROM ".$this->PROP["valtable"]." WHERE ".join(" or ",$x)."))";
		}
	} */  


	
	return "";
  }


function LenSprav() {
	global $db;    
	$db->query("SELECT count(*) FROM ".$this->PROP["valtable"].($this->PROP["valwhere"]? " WHERE ".$this->PROP["valwhere"]:""));
	if($db->next_record()) return $db->Record[0];
	return 0;
}


function getFilter($key,$val) {
	if($this->LenSprav()<$this->max_filter_size) {
		$s="{type: 'list',dataIndex: '$key', 
		options: [".$this->bldOptions()."],
		phpMode: true}";   
		return array("ListFilter.js",$s);
	}
	
	return array("ComboFilter.js","{type: 'combo',dataIndex:'$key',options: [".$this->bldOptions()."],
	    phpMode: true}");
	//return array("StringFilter.js","{type: 'string',dataIndex:'$key'}");
  }

  function bldOptions() {
  global $db;
    $db->query("SELECT ".$this->PROP["valkey"].",".$this->PROP["valvalue"]." FROM ".$this->PROP["valtable"].($this->PROP["valwhere"]? " WHERE ".$this->PROP["valwhere"]:""));
    $s="";	
    while($db->next_record()) $s.=($s? ",":"")."['".$db->Record[0]."','".$db->Record[1]."']";
  
    return $s;
  }

  function onAfterDbChanging() {
  global $db,$id,$PROPERTIES;
    if (isset($_POST["upd"]) || isset($_POST["ins"])) {

      $db->query("SELECT ".$this->PROP["thisfolder"]." FROM ".$PROPERTIES["tbname"]." WHERE id='$id'");
      if ($db->next_record()) {
        $kk=$db->Record[0];
        $db->query("DELETE FROM ".$this->PROP["linktable"]." WHERE ".$this->PROP["linkvalue"]."='".$kk."'");
      }
      if (isset($_POST[$this->PROP["name"]]) && $_POST[$this->PROP["name"]]) {
        if(!isset($kk) || !$kk) $kk=$id;
        $_POST[$this->PROP["name"]]=explode(",",$_POST[$this->PROP["name"]]);
        foreach($_POST[$this->PROP["name"]] as $va) {
          $db->query("INSERT INTO ".$this->PROP["linktable"]." (".$this->PROP["linkkey"].", ".$this->PROP["linkvalue"].") VALUES ('".intval($va)."', '".$kk."')");
        }
      }
    }
  }

  function onBeforeDbChanging() {
  global $db;
    $tables=$db->table_names();
    if(!isset($tables[$this->PROP["linktable"]]) && $this->PROP["linktable"] && $this->PROP["linkkey"] && $this->PROP["linkvalue"]) $db->query("CREATE TABLE ".$this->PROP["linktable"]." (".$this->PROP["linkkey"]." INT NOT NULL ,".$this->PROP["linkvalue"]." INT NOT NULL ,INDEX ( ".$this->PROP["linkkey"]." ,".$this->PROP["linkvalue"]." ))");
  }

  function onDelete($id) {
    global $db;
	$db->query("DELETE FROM ".$this->PROP["linktable"]." WHERE ".$this->PROP["linkvalue"]."='".$id."'");
  }
}
?>