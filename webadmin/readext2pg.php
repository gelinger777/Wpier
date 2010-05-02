<?
if(isset($_POST["AdminCurrentSession"])) {
	$_CONFIG=array();
	include $_SERVER["DOCUMENT_ROOT"]."/config.inc.php";
	if($_CONFIG["DISTANCE_MODE"]==2) {
		$_SESSION=unserialize(stripslashes($_POST["AdminCurrentSession"]));
		unset($_POST["AdminCurrentSession"]);
	}
} else {
	 if(isset($_CONFIG["COOKIE_ONLY"]) && $_CONFIG["COOKIE_ONLY"]) include $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/inc/cookie2session.php";
	else	@session_start();
}
if(isset($_GET["ext"]) && $_GET["ext"]) {
	include_once "./autorisation.php";
	if(file_exists("./extensions/".$_GET["ext"].".php") ||
		($_USERDIR && file_exists("../www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/".$_GET["ext"].".php"))) {
		//$_SESSION["sess_ext"]=$_GET["ext"];
		//$_SESSION["sess_cmpW"]=$_GET["cmpw"];
		$EXT=$_GET["ext"];
	}
} elseif(isset($_SESSION["sess_ext"])) {
	$EXT=$_SESSION["sess_ext"];
}

$CataloguePgID=0;
if(isset($_GET["catalog"])) {
	$CataloguePgID=intval($_GET["catalog"]);
	//$_SESSION["sess_CataloguePgID"]=$CataloguePgID;
} elseif(isset($_SESSION["sess_CataloguePgID"])) {
	$CataloguePgID=$_SESSION["sess_CataloguePgID"];
}

require ("./output_interface.php");
