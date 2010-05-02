<?
include "autorisation.php"; 

if(isset($_POST["addshortcut"])) {
  $db->query("SELECT desktop FROM settings WHERE id='".$ADMIN_ID."'");
  if($db->next_record()) {
	$_POST["addshortcut"]=($db->Record["desktop"]? str_replace("&#39;","'",$db->Record["desktop"]).",":"").unescape($_POST["addshortcut"],"Windows-1251");
	//echo stripslashes($_POST["addshortcut"]);
	$db->query("UPDATE settings SET desktop='".str_replace("'","&#39;",$_POST["addshortcut"])."' WHERE id='".$ADMIN_ID."'");

echo stripslashes($_POST["addshortcut"]);

  }
  exit;  
}

if(isset($_POST["deleteshortcuts"])) {
	$db->query("SELECT desktop FROM settings WHERE id='".$ADMIN_ID."'");
	if($db->next_record()) {
		$s=explode(",",$_POST["deleteshortcuts"]);
		$db->Record["desktop"]=substr($db->Record["desktop"],1,strlen($db->Record["desktop"])-2);
		$db->Record["desktop"]=explode("],[",$db->Record["desktop"]);
	    foreach($db->Record["desktop"] as $k=>$v) {
			if(in_array($k,$s)) unset($db->Record["desktop"][$k]);
		}
		$db->query("UPDATE settings SET desktop='".(count($db->Record["desktop"])? "[".join("],[",$db->Record["desktop"])."]":"")."' WHERE id='".$ADMIN_ID."'");
	}
}

$db->query("SELECT desktop FROM settings WHERE id='".$ADMIN_ID."'");
if($db->next_record()) {
  echo stripslashes(str_replace("&#39;","'",$db->Record["desktop"]));
}
?>