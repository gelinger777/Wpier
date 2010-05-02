<?
$tree=array();
$db->query("SELECT * FROM catalogue_fin");
while($db->next_record()) {
	$tree[$db->Record["pid"]][$db->Record["id"]]=$db->Record["dir"];
}

function read_tree($pid,$dir) {
	global $tree,$fp;
	foreach($tree[$pid] as $k=>$v) {
		fwrite($fp,$dir.$v."/\n");
		if(isset($tree[$k])) read_tree($k,$dir.$v."/");
	}
}

$fp=fopen($_SET["indexfile"],"w+");
read_tree(0,$_SET["host"]."/");
fclose($fp);

echo "read tree - OK\n";
?>