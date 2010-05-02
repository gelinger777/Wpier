<?
function get_table_def($dbName, $table) {
global $db;
    $schema_create = "";
	$db->query("show create table $dbName.$table");
	if($db->next_record()) {
		return str_replace("latin1","cp1251",$db->Record[1]).";\n#INOUTERDUMPSPACER#\n";
	}
	return "";
}

function get_table_content($dbName, $table,$start=0,$limit=0,$csv=0) {
global $db;
	$out="";
	if(!$csv) $out="DELETE FROM $table;\n#INOUTERDUMPSPACER#\n";
	$db->query("SELECT * FROM $dbName.$table".($limit? " LIMIT $start,$limit":""));
	while($db->next_record(1)) {
		$keys=array();
		$vals=array();
		foreach($db->Record as $k=>$v) if($k && !intval($k)) {
			$keys[]=$k;
			$vals[]=str_replace("'","&#39;",$v);
		}
		if($csv) $out.=join(";",$vals)."\n";
		else $out.="INSERT INTO $table (".join(",",$keys).") VALUES ('".join("','",$vals)."');\n#INOUTERDUMPSPACER#\n";
	}
	return $out;
} 


function filePrepare($fname) {
global $dirName,$analitFiles;
	if(!$fname || !filesize($fname) || !in_array(substr($fname,strrpos($fname,".")+1),$analitFiles)) return 0;
	if(file_exists($fname)) {
        $fp=fopen($fname,"r+");
	$str=fread($fp,filesize($fname));
	fclose($fp);
	$str=str_replace("www/$dirName/","",$str);
	$fp=fopen($fname,"w+");
	fwrite($fp,$str);
	fclose($fp);
	}
}
?>