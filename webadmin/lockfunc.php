<?
$LOCK_TIME=30;
session_start();
require "inc/function.php";

if(!isset($_SESSION['adminlogin'])) {
	echo "error";
	exit;
}
$t=mktime();

if(isset($_GET["lock"]) && $_GET["lock"] && isset($_GET["id"]) && $_GET["id"]) {
  $_GET["lock"]=AddSlashes($_GET["lock"]);
  $_GET["id"]=intval($_GET["id"]);
  $db->query("SELECT usr FROM onlinelocks WHERE tab='".$_GET["lock"]."' and id=".$_GET["id"]);
  if($db->next_record()) {
    echo "locked";
    exit;
  }
  $db->query("INSERT INTO onlinelocks (usr,tab,id,tm) VALUES ('".$_SESSION['adminlogin']."','".$_GET["lock"]."','".$_GET["id"]."','$t')");
  echo "OK";
  exit;
}


$LOCK_TIME=mktime()-$LOCK_TIME;

$db->query("DELETE FROM onlineusers WHERE tm<".$LOCK_TIME);
$db->query("UPDATE onlineusers SET tm='".$t."' WHERE usr='".$_SESSION['adminlogin']."'");

$db->query("DELETE FROM onlinelocks WHERE tm<".$LOCK_TIME);
$db->query("UPDATE onlinelocks SET tm='".$t."' WHERE usr='".$_SESSION['adminlogin']."'");
if(isset($_POST["unlock"]) && $_POST["unlock"]) {
  $_POST["unlock"]=explode(";",$_POST["unlock"]);
  $s=array();
  foreach($_POST["unlock"] as $v) {
    $v=explode(":",$v);
    if(count($v)==2) $s[]="(tab='".AddSlashes($v[0])."' and id='".intval($v[1])."')";
  }
  if(count($s)) {
    $db->query("DELETE FROM onlinelocks WHERE usr='".$_SESSION['adminlogin']."' and (".join(" or ",$s).")");
  }
}

// посчитаем количество
/*echo filemtime("../tmp/cnt.php");
if(file_exists("../tmp/cnt.php") && (filemtime("../tmp/cnt.php")+30)>$t) {



	include "../tmp/cnt.php";
} else {*/
	$db->query("SELECT id FROM catalogue WHERE attr='1'");
	$ids=array();
	while($db->next_record()) {
		$ids[]=$db->Record[0];
	}
	$db->query("SELECT count(*) FROM catalogue");
	if($db->next_record()) $CNT_2=intval($db->Record[0]);else $CNT_2=0;
	/*$fp=fopen("../tmp/cnt.php","w+");
	fwrite($fp,'<?$CNT_1='.$CNT_1.';$CNT_2='.$CNT_2.';');
	fclose($fp);
	chmod("../tmp/cnt.php",0x777);
}*/

echo "cnt:".count($ids).":".$CNT_2.":".join(",",$ids);


// Подключаем расширения
if(file_exists("./crone")) {
  $d = dir("crone");
  while (false !== ($entry = $d->read())) {
   if(strpos($entry,'.php')) include "crone/".$entry;
  }
}

