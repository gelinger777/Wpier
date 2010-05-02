<?
include "conf/config.inc.php";
if(!$_CONFIG["MAINFRAME"]) {
	session_start();
	if(!isset($_SESSION['adminlogin'])) exit;
}

if (file_exists($_GET["i"])) {
	$type=strtolower(substr($_GET["i"],strrpos($_GET["i"],".")+1));
	if($type!="php") {
		header("Content-type: application/$type");
		header("Content-Disposition: attachment; filename=".$_GET["i"]);
		readfile($_GET["i"]);
	}
	exit();
}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> Download error</TITLE>
</HEAD>
<BODY>
This file already has deleted/
</BODY>
</HTML>