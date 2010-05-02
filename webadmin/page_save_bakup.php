<?
$bakup_steps=10; // Кол-во сохраняемых откатов
if(isset($_CONFIG["BAKUP_COUNT"])) $bakup_steps=$_CONFIG["BAKUP_COUNT"];

$db->query("SELECT * FROM catalogue WHERE id='$bakup_ID'");
if($db->next_record()) {
	$bakup_pcod=$db->Record["cod"];
	$bakup_keys=array();
	$bakup_vals=array();
	foreach($db->Record as $key=>$val) {
		if(!intval($key) && $key!="lock_user" && $key!="lock_time" && $key!="id" && $key!="owner") {
			$bakup_keys[]=$key;
			$bakup_vals[]=$val;
		}
	}
	$bakap_time=mktime();
	$db->query("INSERT INTO cataloguebakup (id,".join(",",$bakup_keys).",dateBackup) VALUES ('','".join("','",$bakup_vals)."','".$bakap_time."')" );
	$bakup_last= getLastID();
	$bakap_sql=array();
		
	$farr=$db->get_folders_name("contentbakup");
	if(!isset($farr["idc"])) $db->query("ALTER TABLE contentbakup ADD idC int(11) null");

	$db->query("SELECT * FROM content WHERE catalogue_ID='$bakup_pcod' ORDER BY id");
	while($db->next_record()) {
		$bakup_keys=array();
		$bakup_vals=array();
		foreach($db->Record as $key=>$val) {
			if(!intval($key) && $key!="lock_user" && $key!="lock_time") {
				if($key=="id") $key="idc";
				$bakup_keys[]=$key;
				$bakup_vals[]=$val;
			}
		}
		$bakap_sql[]="INSERT INTO contentbakup (id,".join(",",$bakup_keys).",bakup_ID) VALUES ('','".join("','",$bakup_vals)."','".$bakup_last."')";
	}
	foreach($bakap_sql as $val) $db->query($val);

}
?>