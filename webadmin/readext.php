<?
if(isset($_POST["AdminCurrentSession"])) {
	$_CONFIG=array();
	include $_SERVER["DOCUMENT_ROOT"]."/config.inc.php";
	if($_CONFIG["DISTANCE_MODE"]==2) {
		$_SESSION=unserialize(stripslashes($_POST["AdminCurrentSession"]));
		unset($_POST["AdminCurrentSession"]);
	}
} 

include_once "./autorisation.php";

if(isset($_GET["ext"]) && $_GET["ext"]) {
	//include_once "./autorisation.php";
	if(file_exists("./extensions/".$_GET["ext"].".php") || 
		($_USERDIR && file_exists("../www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/".$_GET["ext"].".php"))) {
		//$_SESSION["sess_ext"]=$_GET["ext"];
		$EXT=$_GET["ext"];
	}
} elseif(isset($_SESSION["sess_ext"])) {
	$EXT=$_SESSION["sess_ext"];
}
//unset($_SESSION["sess_cmpW"]);

if(isset($_COOKIE["COOK_NEWMOD"])) {
  $_POST= unserialize($_COOKIE["COOK_NEWMOD"]);
  setcookie("COOK_NEWMOD","",0,"/");
  if(isset($_POST["id"]) && $_POST["id"]) $_GET["ch"]=intval($_POST["id"]); 
  else $_GET["new"]=1;
}

require ("./output_interface.php");
