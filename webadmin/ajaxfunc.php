<?
include "./autorisation.php";

if(isset($_POST["stores"])) {
  $db->query("UPDATE settings SET Stores='".(str_replace("'","&#39;",unescape($_POST["stores"],"Windows-1251")))."' WHERE id='$ADMIN_ID'");
  exit;
}

if(isset($_GET["getpath"]) && $_GET["getpath"]) {
  $path="/";
  $id=intval($_GET["getpath"]);
  while($id) {
    $db->query("SELECT dir,pid FROM catalogue WHERE id='$id'");
    if($db->next_record()) {
      $path="/".$db->Record["dir"].$path;
      $id=$db->Record["pid"];
    }
  }
  if($path!="/") echo "http://".$_SERVER["HTTP_HOST"].$path;
  else echo "";
  exit;
}

if(isset($_GET["changeprop"])) {
  
  // Если меняем свойства у страницы, проверим права доступа к ней
  $ids=explode(",",$_GET["id"]);
  $result="";
  $_GET["t"]=AddSlashes($_GET["t"]);
  $_GET["changeprop"]=AddSlashes($_GET["changeprop"]);
  $_GET["val"]=AddSlashes($_GET["val"]);
  foreach($ids as $id) {
    $id=intval(trim($id));
    if($id) {      
      $db->query("UPDATE ".$_GET["t"]." SET ".$_GET["changeprop"]."='".$_GET["val"]."' ".($_GET["t"]=="catalogue"? ",attr='1'":"")." WHERE id='$id'");
      if($_GET["t"]=="catalogue" && $_GET["changeprop"]=="title") {
	if(!$result) $result='TITLERESET|'.$id.'|'.stripslashes($_GET["val"]);
      } elseif($_GET["t"]=="content" && $_GET["changeprop"]=="spec") {
	if(!$result) $result="RELOAD";
      } elseif($_GET["t"]=="catalogue" && $_GET["changeprop"]=="tpl") {
	include "content_functions.php";
	ChangeTpl(intval($_GET["val"]),$id);
	if(!$result) $result="RELOAD";
      } else if(!$result) $result='OK';      
    }
  }
  echo $result;
} elseif(isset($_GET["chrow"])) {
  include_once "./inc/rustypo.php";
  
  if($_POST["tab"]=='content' && $_POST["fold"]=='text') {
    
  }
  
  $db->query("UPDATE ".AddSlashes($_POST["tab"])." SET ".AddSlashes($_POST["fold"])."='".str_replace("'","&#39;",Proof(unescape($_POST["data"],"Windows-1251")))."' WHERE id='".intval($_POST["id"])."'");
  
  //echo $db->LastQuery;
  if(isset($_GET["pgid"])) {
	$db->query("UPDATE catalogue SET attr='1' WHERE id='".intval($_GET["pgid"])."' and (attr is NULL or attr='')");
  }

  echo "OK";
  exit;
}
