<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> Inout:er setup </TITLE>
</HEAD>

<BODY>
<?
$fileNameS=array("../db_dump.sql","../db_dump_data.sql");

require $_SERVER["DOCUMENT_ROOT"]."/function.php";

if(!isset($_GET["drop"]) && count($db->table_names($DB_MAIN))) {
	echo "Указанная база данных содержит таблицы! Установка возможна только в пустую базу данных.";
	exit;
}

foreach($fileNameS as $fileName) {
	$fp=fopen($fileName,"r");
	$str=fread($fp,filesize($fileName));
	fclose($fp);

	$str=explode("#INOUTERDUMPSPACER#",$str);
	$n=count($str);
	$tables=$db->table_names($DB_MAIN);

	if($n) {
		for($i=0;$i<$n;$i++) if(trim($str[$i])) {
			$str[$i]=trim($str[$i]);
			if(strpos(" ".$str[$i],"CREATE TABLE")==1) {
				$s=str_replace("CREATE TABLE `","",$str[$i]);
				$s=trim(substr($s,0,strpos($s,"`")));
				if(isset($tables[$s])) {
					$db->query("DROP TABLE $s");
				}
			}
			$db->query($str[$i]);
		}
	}
}
?>
Установка успешно завершена!<BR>
<a href="/<?=$_CONFIG["ADMINDIR"]?>/">Запустить CMS</a>

</BODY>
</HTML>