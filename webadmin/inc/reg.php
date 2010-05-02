<?
require "./function.php";
if(isset($_GET["id"]) && isset($_GET["key"])) {
	$_GET["id"]=intval($_GET["id"]);
	$_GET["key"]=htmlspecialchars($_GET["key"]);
	$db->query("UPDATE subscrusers SET endDate='',subscrAttr='' WHERE id='".$_GET["id"]."' and sesKey='".$_GET["key"]."'");
	header("Location: /registered/");
	exit();
}
