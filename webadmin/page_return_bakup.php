<?
$bak_id=0;
if(isset($_GET["ch"])) $bak_id=intval($_GET["ch"]);
if(isset($_POST["id"])) $bak_id=intval($_POST["id"]);

if(isset($_GET["ret"])) {
	$_GET["ret"]=intval($_GET["ret"]);
	$bakup_ID=$bak_id;
	require "./page_save_bakup.php";

	$db->query("SELECT * FROM cataloguebakup WHERE id='".$_GET["ret"]."'");
	if($db->next_record()) {
		$bakup_id=$db->Record["id"];
		$bakup_cod=$db->Record["cod"];
		$bakup_keys=array();
		foreach($db->Record as $key=>$val) {
			if(!intval($key) && $key!="id" && $key!="datebackup") {
				$bakup_keys[]="$key='$val'";
			}
		}
		$db->query("UPDATE catalogue SET ".join(",",$bakup_keys)." WHERE id='".$bak_id."'");
		$db->query("DELETE FROM content WHERE catalogue_ID='$bakup_cod'");

		$bakap_sql=array();
		$db->query("SELECT * FROM contentbakup WHERE bakup_ID='$bakup_id' ORDER BY id");
		while($db->next_record()) {
			$bakup_keys=array();
			$bakup_vals=array();
			foreach($db->Record as $key=>$val) {
				if(!intval($key) && $key!="bakup_id" && $key!="id") {
					if($key=="idc") $key="id";
					$bakup_keys[]=$key;
					$bakup_vals[]=$val;
				}
			}
			$bakap_sql[]="INSERT INTO content (".join(",",$bakup_keys).") VALUES ('".join("','",$bakup_vals)."')";
		}
		foreach($bakap_sql as $val) $db->query($val);
		$db->query("DELETE FROM cataloguebakup WHERE id='$bakup_id'");
		$db->query("DELETE FROM contentbakup WHERE bakup_ID='$bakup_id'");
	}
}

/*
$db->query("SELECT cod FROM catalogue WHERE id='$bak_id'");
if($db->next_record()) {
	$db->query("SELECT id, dateBackup FROM cataloguebakup WHERE cod='".$db->Record["cod"]."' ORDER BY dateBackup DESC");
	if(!$db->num_rows()) unset($F_ARRAY["separator_1"]);
	else {
		$F_ARRAY["separator_1"].=" <select onchange='if(this.value!=\"\" && confirm(\"Востановить эту версию?\")) {window.location=\"./page.php?ch=$bak_id&ret=\"+this.value}else this.selectedIndex=0;'><option value=''>Выберите версию</option>";
		while($db->next_record()) {
			$bak_date=date("d.m.y H:i",$db->Record["datebackup"]);
			$F_ARRAY["separator_1"].="<option value='".$db->Record["id"]."'>$bak_date</option> ";
		}
		$F_ARRAY["separator_1"].="</select>";
	}
} else unset($F_ARRAY["separator_1"]);*/
?>