<?
if(isset($_POST["AdminCurrentSession"])) {
	$_CONFIG=array();
	include dirname(__FILE__)."/inc/config.inc.php";
	if($_CONFIG["DISTANCE_MODE"]==2) {
		$_SESSION=unserialize(stripslashes($_POST["AdminCurrentSession"]));
		unset($_POST["AdminCurrentSession"]);
	}
} else session_start();

if(isset($_GET["ext"]) && $_GET["ext"]) {

	include_once dirname(__FILE__)."/inc/function.php";
	if(file_exists("./extensions/".$_GET["ext"].".php") ||
		($_USERDIR && file_exists("../www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/".$_GET["ext"].".php"))) {
		$_SESSION["sess_ext"]=$_GET["ext"];
		$EXT=$_GET["ext"];
	}
} elseif(isset($_SESSION["sess_ext"])) {
	$EXT=$_SESSION["sess_ext"];
}
unset($_SESSION["sess_cmpW"]);

$AdminLogin="root";
$ACCESS=0;
$ACCESS_DETAILS=array("all"=>1);
$lenguagesList=1;
$SPELL=1;
//$db->query("UPDATE settings SET AdminUID = '".session_id()."' WHERE AdminLogin='root'");
$LENGUAGE="";
$LOGPOST=1;
require ("./output_interface.php");
