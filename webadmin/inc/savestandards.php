<?
if(isset($_GET["m"]) && isset($_GET["s"])) {
	include "../autorisation.php";
	$_GET["m"]=htmlspecialchars($_GET["m"]);
	$db->query("DELETE FROM cmsstandards WHERE user='".$ADMIN_ID."' and modname='".$_GET["m"]."'");
	$db->query("INSERT INTO cmsstandards (user,modname,sets) VALUES ('".$ADMIN_ID."','".$_GET["m"]."','".htmlspecialchars($_GET["s"])."')");
	echo "OK";
}
?>