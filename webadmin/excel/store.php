<?
ob_end_clean();
global $EXT,$db, $f_array,$F_ARRAY,$XLS_PROCCESS_LOG;

header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=$EXT.csv");

$i=0;
foreach($f_array as $k=>$v) {
	if($v!="*hide*") {
		$v=explode("|",$v);
		echo strip_tags($v[0]).";";
	}
}



$db->query($sqlQ);
$line=1;
$RecordAll=array();
while($db->next_record()) $RecordAll[]=$db->Record;
$XLS_PROCCESS_LOG=1;
foreach($RecordAll as $Record) {
	echo "\n";
	$i=0;
	foreach($f_array as $k=>$v) {
		if($v!="*hide*") {
			if(!isset($Record[$k])) {
				$Record[$k]=$Record["id"];
			}
			$x=explode("|",$F_ARRAY[$k]);
			$v=strip_tags($OBJECTS[$k]->mkList($Record[$k]));
            echo "\t".str_replace(";",",",str_replace("&nbsp;"," ",str_replace("\n"," ", str_replace("\r","",str_replace("\n"," ",$v)))))." ;";
		}
	}
	$line++;
}
exit;
?>