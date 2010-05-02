<?
$EMULATIONMODE=0; // 1 - эмуляция через http, 2 - эмуляция на файлах, 0 - рабочий режим
$SERVSET=array(
 "IP"=>"84.47.186.200",
 "port"=>"9193"
);
$CASH_PATH=$_SERVER["DOCUMENT_ROOT"]."/userfiles/cash/"; // путь к кэшу
$CASH_TIMEOUT=5; // через сколько минут обновлять кэш


$LIDARR=array(16, 35, 36, 37, 41, 38, 39, 40, 17, 18, 22, 23, 24, 25, 26, 27);

require_once $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/inc/xmlfunctions.php";

// ДЛЯ ЭМУЛЯЦИИ ---------------------- //
include $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/http.class.php";

$HTTP=new http;
$http_HOST="titanik-cinema.xpd.ru";
$http_USER="masha";
$http_PASSWD="vfif";
$http_PORT=80;

function httpLoadFile($path) {
global $HTTP,$http_HOST, $http_PORT,$http_USER,$http_PASSWD;
	$fp=$HTTP->http_fopen($http_HOST, $path, $http_PORT,$http_USER,$http_PASSWD);
	$cont="";
	while(!feof($fp)) {
	   $cont.= fread($fp,2048);
	}
	fclose($fp);

	return $cont;
}
// ДЛЯ ЭМУЛЯЦИИ -------------------------------- //

function protocol($query) {
global $SERVSET, $CASH_PATH, $CASH_TIMEOUT;

	$fn=md5($query);
	if(file_exists($CASH_PATH.$fn) && (mktime()-filemtime($CASH_PATH.$fn))<($CASH_TIMEOUT*60)) {
		$fp=fopen($CASH_PATH.$fn,"r");
		$cont=fread($fp,filesize($CASH_PATH.$fn));
		fclose($fp);
	} else {	
		$fp = fsockopen( $SERVSET["IP"], $SERVSET["port"],$errno,$errstr);
		if (!$fp) return false;

		fputs ($fp,$query);
		$cont="";
		$cnt= intval(fread($fp,10));
		while($cnt>0) {
			$s = fread($fp,$cnt);
			$cnt-=strlen($s);
			$cont.=$s;
		}
		fclose($fp);
		/*$fp=fopen($CASH_PATH.$fn,"w+");
		fwrite($fp,$cont);
		fclose($fp);
		clearstatcache();*/
	}
	return convert_cyr_string (substr($cont,strpos($cont,"<")),"d", "w");
}

function makeQuery($str) {
	$cnt="";
	foreach($str as $k=>$v) {
		if(is_array($v)) $v=join(",",$v); // Здесь можно поменять разделитель для массивных данных в запросе
		$cnt.="&$k=$v";
	}
	$str=strval(strlen($cnt));
	while(strlen($str)<10) {
		$str="0".$str;
	}
	return $str.$cnt;
}
$QUERIES="";
function getXML($query,$REPL=array()) {
global $QUERIES, $EMULATIONMODE;
	$query=str_replace(",",";",$query);
	$QUERIES.="$query<BR>";
	$s="";
	
	// Эмуляция по http ----------------------------------------------------------
	if($EMULATIONMODE==1) {
		$s=httpLoadFile("/httpemu.php?q=".str_replace("&","|",$query));
	} elseif($EMULATIONMODE==2) {
	
		// ЭМУЛЯЦИЯ ПРОТОКОЛА НА ФАЙЛАХ ---------------------------------------
		$CMNDASS=array();
		
		if(isset($CMNDASS[$query])) {
			$file=$_SERVER["DOCUMENT_ROOT"]."/source/".$CMNDASS[$query].".xml";
			$fp=fopen($file,"r+");
			$s=fread($fp,filesize($file));
			fclose($fp);		
		}
	} else {
		$s=protocol($query);
	}

	if(count($REPL)) {
		foreach($REPL as $k=>$v)  $s=eregi_replace($k,$v,$s);
	}
	if($s) {
		$s=str_replace("</pos>","|/pos|",$s);
		$s=str_replace('<pos ','|pos ',$s);
		return  xml2array(str_replace("<ndate>","|ndate|",str_replace("</ndate>","|/ndate|",str_replace("</sl>","|/sl|", str_replace("<sl>","|sl|",str_replace("</sl>","|/sl|", $s))))));}
	return 0;
}

// Список фильмов ----------------------------------------------------------------
function GetMovieList($QUERY,$LIST, $DATE="", $KEYS=array(), $CNT=0, $FN=0) {
global $SEANS, $LIDARR;
	// Вырезаем из LIST блок SEANS с оформлением записи сеансов
	$SEANS=$LIST;
	$seans=parse_tmp("SEANS","SEANS");
	$LIST=$SEANS;
	// -------------------------------------------------------------------------------------------

	if($FN) {
		$QUERY["DtLst"]="";
	}
	$arr=getXML(makeQuery($QUERY));
	$list="";
	if($arr) {
		$idf=array();
		// Вытаскиваем идентификаторы фильмов ----------------
		if(isset($arr["DATA"][0][0]["HTM"]) && isset($arr["DATA"][0][0]["HTM"][0][0]["ITM"])) foreach($arr["DATA"][0][0]["HTM"][0][0]["ITM"] as $flm) if(!$FN || ($FN && $FN==$flm["attr"]["FID"])) {
			$idf[]=$flm["attr"]["FID"];
			
			if($FN) {
				foreach($flm[0] as $k=>$v) {
					if($k=="REM") {
						$v=str_replace("[","<",$v);
						$v=str_replace("]",">",$v);
						$v=str_replace("<url=","<a href=",$v);
						$v=str_replace("</url","</a",$v);

						$nst=strpos($v,"|ndate|");
						if($nst>0) {
							$nend=strpos($v,"|/ndate|");
							$LIST=str_replace("%ndate%",substr($v,($nst+7),($nend-$nst-7)),$LIST);
						}						
					}
					$LIST=str_replace("%$k%",str_replace("'","&#39;",$v),$LIST);
				}
			}
		}

		$out=$idf;
		$log=5;
		do {
			if($CNT && $CNT<count($idf)) {
				srand ((float) microtime() * 10000000);
				$r=array_rand ($idf,$CNT);

				if(!is_array($r)) $r=array($r);
				$out=array();
				foreach($r as $flm) $out[]=$idf[$flm];
			}

			if($FN) {
				$idf=array($FN);
				$out=array($FN);
			}

			// По идентификаторам тащим список сеансов ------------------------
			$QUERY=array(
			"QrCd" => 11,
			"DtLst"=>$DATE,
			"MvLst"=>$out,
			"LvlLst"=>join(";",$LIDARR),
			"LstSrt" => "MDH",
			"TO" => 120,
			"LstTp" => "CenterOnly"
			);

			$sns=getXML(makeQuery($QUERY));
			$snsarr=array();
			if($sns) {
				if(isset($sns["DATA"][0][0]["HTM"]) && isset($sns["DATA"][0][0]["HTM"][0]) && isset($sns["DATA"][0][0]["HTM"][0][0]) &&  isset($sns["DATA"][0][0]["HTM"][0][0]["ITM"]))  
					foreach($sns["DATA"][0][0]["HTM"][0][0]["ITM"] as $sn) {
					if(isset($sn[0]["FID"])) {
						$sn[0]["SID"]=$sn["attr"]["SID"];
						if(!isset($snsarr[$sn[0]["FID"]])) $snsarr[$sn[0]["FID"]]=array();
						$snsarr[$sn[0]["FID"]][]=$sn[0];
					}
				}
			}
			$log--;
		} while(!count($snsarr) && $log && !$FN);
		// Финальный вывод ---------------------------------------------------------
		$log=count($KEYS);
			
		$II=0;

		if(isset($arr["DATA"][0][0]["HTM"]) && isset($arr["DATA"][0][0]["HTM"][0][0]["ITM"])) foreach($arr["DATA"][0][0]["HTM"][0][0]["ITM"] as $flm) if(!$CNT || ($CNT && $II<$CNT)) {//if(in_array($flm["attr"]["FID"],$idf) && $II<$CNT) {
			$II++;
			$s=str_replace("%FID%",$flm["attr"]["FID"],$LIST);
			$l="";
			if(isset($snsarr[$flm["attr"]["FID"]])) {
				foreach($snsarr[$flm["attr"]["FID"]] as $sn) {
					$st=$seans;
					$lg=0;
					foreach($sn as $k=>$v) {
						$v=trim($v);
						if($log && isset($KEYS[$k]) && $KEYS[$k]==$v) {
							$lg=1;
						}
						$st=str_replace("%$k%",str_replace("'","&#39;",$v),$st);						
					}
					if(!$log || $lg) $l.=$st;
				}
				$s=str_replace("%SEANS%",$l,$s);
			}
			if($l) {
				foreach($flm[0] as $k=>$v) $s=str_replace("%$k%",str_replace("'","&#39;",$v),$s);
				$list.=$s;	
			}
		}
	}
	if($FN && !$list) {return $LIST;}
	else {return $list;}
}

// Список сеансов ----------------------------------------------------------------
function GetSessionList($QUERY,$LIST) {
global $FIRSTFN;
	$arr=getXML(makeQuery($QUERY));
	$list="";
	if(isset($arr["DATA"][0][0]["HTM"]) && isset($arr["DATA"][0][0]["HTM"][0][0]["ITM"])) foreach($arr["DATA"][0][0]["HTM"][0][0]["ITM"] as $flm) {
		if(isset($flm[0]["FN"])) $FIRSTFN=$flm[0]["FN"];
		$s=str_replace("%SID%",$flm["attr"]["SID"],$LIST);
		foreach($flm[0] as $k=>$v) $s=str_replace("%$k%",$v,$s);
		$list.=$s;	
	}
	return $list;
}

// Проверяем клубную карту ---------------------------------------------
function checkCrd($pin, $usr) {
	if(!intval($usr))  $usr=1111;
	$QUERY=array(
	"QrCd" => 1,
	"CrdCd"=>$pin, 
	"CrdPN"=>$usr 
	);
	$arr=getXML(makeQuery($QUERY));
	if($arr["DATA"][0][0]["Res"]=="ok" || $arr["DATA"][0][0]["Res"]=="Ok") return 1;
	return 0;
}

// Выводим план зала -------------------------------------------------------
function GetHallPlanInfo($sid,$lid) {
	$QUERY=array(
	"QrCd" => 23,
	"LvlLst"=>$lid,
	"SsLst"=>$sid, 
	"LstTp"=>"" 
	);
	$arr=getXML(makeQuery($QUERY));
//print_r($arr);	
	return $arr["DATA"][0][0]["HTM"][0][0];
}

// Бронируем места ----------------------------------------------------------
function Reserved($sid, $lid, $plcs, $crd) {
	$QUERY=array(
	"QrCd" => 17,
	"CrdCd" => $crd,
	"SsLst"=>$sid, 
	"LvlLst"=>$lid,
	"PlcLst"=>$plcs 
	);
	$arr=getXML(makeQuery($QUERY));
	if($arr["DATA"][0][0]["Res"]=="Ok") return ($arr["DATA"][0][0]["HTM"][0][0]);
	return 0;
}

// Показать текущую бронь ------------------------------------------------
function GetReservationList($crd) {
    $QUERY=array(
	"QrCd" => 18,
	"CrdCd" => $crd,
	"LstTp" => "ReservStatus"
	);
	$arr=getXML(makeQuery($QUERY),array("<ITM>([^I]{1,})</ITM>"=>"<ITMM>\\1</ITMM>"));
	if($arr["DATA"][0][0]["Res"]=="Ok") {
		return ($arr["DATA"][0][0]["HTM"][0][0]["ITM"]);
	}
	return 0;
}

// Отказ от брони -----------------------------------------
function ReservationClear($rid,$crd) {
	 $QUERY=array(
	"QrCd" => 19,
	"CrdCd" => $crd,
	"RsrvID" => $rid
	);
	$arr=getXML(makeQuery($QUERY));
	if($arr["DATA"][0][0]["Res"]=="Ok") ;return 1;
	return 0;
}

// Показать цены на сеанс ------------------------------------------------
function GetSessionPrice($sid, $lid) {
    $QUERY=array(
	"QrCd" => 13,
	"LvlLst"=>$lid,
	"SsLst" => $sid
	);
	$arr=getXML(makeQuery($QUERY));
	if($arr["DATA"][0][0]["Res"]=="Ok") {
		if(is_array($arr["DATA"][0][0]["HTM"])) return ($arr["DATA"][0][0]["HTM"][0][0]["ITM"]);
	}
	return 0;
}
?>