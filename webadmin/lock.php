<?
require "./autorisation.php";

if ($AdminLogin!="guest" && isset($_GET["id"]) && intval($_GET["id"])) {
  if (isset($_GET["tb"]) && isset($_GET["id"]) && $_GET["tb"] && $_GET["id"]) {
    if($_USERDIR && $_GET["tb"]=="hosts") $dbn=$_CONFIG["DB_MAIN"];
    else $dbn="";
    if(isset($_GET["un"])) {
       @$db->query("UPDATE ".htmlspecialchars($_GET["tb"])." SET lock_user='', lock_time=0 WHERE lock_user='$AdminLogin' and id='".intval($_GET["id"])."'",$dbn);
    } else {
       @$db->query("UPDATE ".htmlspecialchars($_GET["tb"])." SET lock_time='".mktime()."' WHERE lock_user='$AdminLogin' and id='".intval($_GET["id"])."'",$dbn);
    }
  }
}
if(isset($_GET["lock_user"])) {
  if(isset($_CONFIG["NO_OWER_LOGIN"]) && $_CONFIG["NO_OWER_LOGIN"]) 
    $db->query("UPDATE settings SET lock_user='$AdminLogin', lock_time='".mktime()."' WHERE AdminLogin='$AdminLogin'");
  $db->query("SELECT * FROM publicationstatus WHERE admin='$ADMIN_ID'");
  $recs=array();
  while($db->next_record())  $recs[]=$db->Record;
  $log=0;
  foreach($recs as $Record) {
    $db->query("SELECT id FROM ".$Record["tab"]." WHERE id='".$Record["idr"]."'");
    if($db->next_record()) {
      if(!$log) {
        echo "<SCRIPT>parent.RequireSignature('".$Record["mod"]."','".$Record["idr"]."');</SCRIPT>";
        $log=1;
      }
    } else {
      $db->query("DELETE FROM publicationstatus WHERE admin='$ADMIN_ID' and tab='".$Record["tab"]."' and idr='".$Record["idr"]."'");
    }
  }
}
?>