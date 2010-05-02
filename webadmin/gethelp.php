<?
include "autorisation.php";

if(isset($_GET["change"]) && isset($_POST["id"]) && isset($_POST["text"])) {
  $_POST["id"]= AddSlashes($_POST["id"]);
  $_POST["text"]= AddSlashes(unescape($_POST["text"],'Windows-1251'));
  
  $db->query("DELETE FROM help WHERE el_id='".$_POST["id"]."'");   
  if($_POST["text"]) {
    $db->query("INSERT INTO help (el_id,descript) VALUES ('".$_POST["id"]."','".$_POST["text"]."')");


  }                 
} elseif(isset($_GET["topic"])){
  $db->query("SELECT descript FROM help WHERE el_id='".AddSlashes($_GET["topic"])."'");
  if($db->next_record()) echo $db->Record[0];
} elseif(isset($_GET["path"])) {
  $_GET["path"]=explode("|",$_GET["path"]);
  $w= AddSlashes($_GET["path"][0]);
  $a=array();
  for($i=1;$i<count($_GET["path"]);$i++) {
	$a[$w.".".AddSlashes($_GET["path"][$i])]="";
	$_GET["path"][$i]=explode("-",$_GET["path"][$i]);
	if(count($_GET["path"][$i])>1) $a[$w.".".AddSlashes($_GET["path"][$i][0])]="";
  }
  
  $db->query("SELECT el_id,descript FROM help WHERE el_id IN ('".kjoin("','",$a)."')");
  while($db->next_record()) {
    $a[$db->Record[0]]=$db->Record[1];
    //echo $db->Record[0]."|".stripslashes($db->Record[1]);
  }
  
  foreach($a as $k=>$v) {
    if($v) {
      echo "$k|".stripslashes($v);
      exit;
    }
  }
  exit;
  
}elseif(isset($_GET["del"])) {
  $db->query("DELETE FROM help WHERE el_id='".AddSlashes($_GET["del"])."'");
}
?>