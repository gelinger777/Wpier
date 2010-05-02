<?
// $dbf_file - имя файла ДБФ
// $table - название таблицы куда импортируем
// $folders - массив с перечнем полей конечной таблицы. Формат:

// $folders=array(
//	"posCod"=>"check" - проверит наличие такой-же записи в конечной табице. Если есть, обновит.
// "cpCod" => array("имя таблицы",имя столбца привязки","столбец имени","ins") - проверит это поле, если ассоциация не найдена, создаст запись в указанной таблице и сохранит в аут если ins=1
function dbf2db ($dbf_file, $table, $cod, $folders,$show=0) {
global $db;
	$outstr="";
	$iddb= dbase_open($dbf_file, 2);
	$cols=dbase_numfields ($iddb);
	$num=dbase_numrecords ($iddb);
	for($i=1;$i<$num;$i++) {
		$z[]=dbase_get_record($iddb,$i);
	}
	$out=array();

	$checkASS=array();
	foreach($folders as $k=>$v) {
		if(is_array($v)) {
			$checkASS[$v[0]]=array();
			$db->query("SELECT ".$v[1].", ".$v[2]." FROM ".$v[0]);
			while($db->next_record()) {
				$checkASS[$v[0]][$db->Record[1]]=$db->Record[0];
			}
		}
	}

	
	if($show && count($z)>200) $stop=200;
	else $stop=count($z);

	foreach($z as $k => $v ) {		
		if($show) {
			if($stop<=0) return $out;
			$stop--;
		}
		$i=0;
		$id=0;
		$action="ins";
		$keys=array();
		$vals=array();
		$cheks=array();
		foreach($folders as $key => $val ) {
			if($key && !intval($key)) { 
				if(isset($v[$i])) {
					$g=trim(str_replace("'","&#39;",convert_cyr_string ($v[$i], "d", "w")));
					if($show) echo "$key => $g<BR>" ;
					else {
						if($val=="check") {
						// Проверяем на наличие в БД такого-же поля
							$cheks[]="$key='$g'";							
						} elseif(is_array($val)) {
							if(isset($val[4]) && $val[4]=="explode") {
								$g=explode($val[5],$g);
								foreach($g as $gV) if($gV) {
									if(!isset($checkASS[$val[0]][$gV])) {
										if(!isset($out[$val[6]])) $out[$val[6]]=array();
										$out[$val[6]][]=$gV;		
									}
								} 
								$g=join(",",$g);
							} else {
								if(isset($checkASS[$val[0]][$g])) {
									$g=$checkASS[$val[0]][$g];
								} elseif($val[3]) {
									$nameG=$g;
									$db->query("INSERT INTO ".$val[0]." (".$val[2].") VALUES ('$g')");
									$db->query("SELECT LAST_INSERT_ID()");
									$db->next_record();
									$g=$db->Record[0];
									$checkASS[$val[0]][$nameG]=$g;
									$db->query("UPDATE ".$val[0]." SET ".$val[1]."='$g' WHERE id='$g'");
									if(!isset($out[$val[6]])) $out[$val[6]]=array();
									$out[$val[6]][$g]=$nameG;							
								} 
							}
						}
						$keys[]=$key;
						$vals[]=$g;
					}
				}
			}
			$i++;	
		} 
		if($show) echo"<hr>";
		elseif(count($keys)) {
			if(count($cheks)) {
				$db->query("SELECT id FROM $table WHERE ".join(" and ",$cheks));
				if($db->next_record()) {
					$action="upd";
					$id=$db->Record[0];
				}
			}
			if($action=="ins") {
				$db->query("INSERT INTO $table (".join(",",$keys).") VALUES ('".join("','",$vals)."')");
				if($cod) {
					$db->query("SELECT LAST_INSERT_ID()");
					$db->next_record();
					$db->query("UPDATE $table SET ".$cod."='".$db->Record[0]."'");
				}
			} elseif($id) {
				$sql=array();
				foreach($keys as $i=>$val) {
					$sql[]="$val='".$vals[$i]."'";
				}
				$db->query("UPDATE $table SET ".join(",",$sql)." WHERE id='$id'");
			}
		}
	}
	dbase_close ($iddb);

	echo "<p><b>Импорт завершен. Импортировано записей: ".count($z)."</b></p>";
	return $out;
}
?>