<?
if(!isset($_GET["f"])) exit;

include "autorisation.php";

$_GET["f"]=$_SERVER["DOCUMENT_ROOT"].$_GET["f"];

if (file_exists($_GET["f"])) {
	$type=strtolower(substr($_GET["f"],strrpos($_GET["f"],".")+1));
	if($type!="php") {
		header("Content-type: application/$type");
		header("Content-Disposition: attachment; filename=".substr($_GET["f"],strrpos($_GET["f"],"/")+1));
		readfile($_GET["f"]);
		unlink($_GET["f"]);
	}
	exit();
}
?>