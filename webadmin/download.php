<?
require "./autorisation.php";
$path.="/docs/";
if (file_exists("$path$QUERY_STRING")) {
	$type=substr($QUERY_STRING,strrpos($QUERY_STRING,"."));
	header("Content-type: application/$type");
	header("Content-Disposition: attachment; filename=$QUERY_STRING");
	readfile("$path$QUERY_STRING");
}
?>