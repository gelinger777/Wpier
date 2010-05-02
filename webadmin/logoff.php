<?
$SYS=1;
require("autorisation.php");

$db->query("UPDATE settings SET AdminUID='', lock_time=0, lock_user='' WHERE AdminLogin='".$_SESSION['adminlogin']."'");
$db->query("DELETE FROM onlineusers WHERE usr='".$_SESSION['adminlogin']."'");
$db->query("DELETE FROM onlinelocks WHERE usr='".$_SESSION['adminlogin']."'");
$_SESSION=array();
header("Location: login.php");
exit;
?>