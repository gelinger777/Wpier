<?
include dirname(__FILE__)."/inc/function.php";

$tree=array();
$db->query("SELECT id,pid,dir FROM catalogue ORDER BY id");
while($db->next_record()) {
	$tree[$db->Record["id"]]=array($db->Record["pid"],$db->Record["dir"]);
}

function makepath($id) {
global $tree,$_CONFIG;
	$path="";
	while(isset($tree[$id])) {
		$path=$tree[$id][1]."/".$path;
		$id=$tree[$id][0];
	}
	return str_replace("%id%",$path,$_CONFIG["URI_PREVIEW_TPL"]);
}

if(!$_GET["pg"]) {
	$db->query("SELECT id FROM catalogue ORDER BY id LIMIT 1");
	if($db->next_record()) $_GET["pg"]=$db->Record[0];
}

if(intval($_GET["pg"])) {
	header("Location:".makepath($_GET["pg"]));
	exit;
}
