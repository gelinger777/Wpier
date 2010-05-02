<?
include dirname(__FILE__)."/inc/function.php";

$db->query("SELECT cod FROM catalogue_fin ORDER BY cod");
$tree=array();
while($db->next_record()) $tree[]=$db->Record["cod"];

foreach($tree as $v) echo "$v|".mkPathFromCod($v)."*";

function mkPathFromCod($cod) {
global $db,$FinSuf;
	$dir="";
	$db->query("SELECT dir, pid FROM catalogue$FinSuf WHERE cod='".intval($cod)."'");
	while($db->next_record()) {
		$dir="/".$db->Record["dir"].$dir;
		if($db->Record["pid"]) {
			$db->query("SELECT dir, pid FROM catalogue$FinSuf WHERE id='".$db->Record["pid"]."'");
		} else {
			return "$dir/";
		}
	}
	return $cod;
}
