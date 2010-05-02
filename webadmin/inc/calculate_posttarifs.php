<?
// Почтовый сбор
function postSbor() {
	return 85;
}

function postDostavka($usrPostIndex,$usrPostOff,$MASSA) {
global $db;
	
	$mas=intval($MASSA/500);
	if(($mas*500)<$MASSA) $mas++;

	$AVIA=0;
	$Ground=0;
	$PaintFrom="";
	$curDate=date("Ymd");
	$curDate2000=date("2000md");

	$db->query("SELECT * FROM dlvlim WHERE pIndex='$usrPostIndex' and PrBegDate<='$curDate2000' and PrEndDate>='$curDate2000' and ActDate<='$curDate'");
	if($db->next_record()) {
		do {
			$rec=$db->Record;
			$PaintFrom=$rec["DelivPnt"];
			if(trim($rec["DelivType"])=="Запрещена") {
			// Доставка запрещена
				return "none";
			} elseif(trim($rec["DelivType"])=="Наземная") {
			// Наземная	
				$Ground+=postCalcGround($mas, $rec["RateZone"]);	
			} elseif(trim($rec["DelivType"])=="Прямой авиа") {
			// Прямой авиа
				$AVIA+=postCalcAvia($rec["BaseCoeff"],$rec["BaseRate"],$rec["TransfCnt"],$mas);
				if($rec["RateZone"]>0) {
					$Ground+=postCalcGround($mas, $rec["RateZone"]);	
				}
			} else {
			// Ошибка данных
				return "error";
			}			
			// Поиск по ДБ ограничений
			$db->query("SELECT * FROM dlvlim WHERE OPSName='$PaintFrom' and PrBegDate<='$curDate2000' and PrEndDate>='$curDate2000' and ActDate<='$curDate'");
		} while($db->next_record());
		
		if($PaintFrom!=$usrPostOff) {
		// Рассчет наземной доставки до $PaintFrom
			$Ground+=postCalcGround($mas, $rec["RateZone"]);	
		}

		if($AVIA) {
		// Рассчет почтового сбора
			$AVIA+=postSbor();
		}
		return ($AVIA+$Ground);
	} else {
		// Рассчет наземной доставки	
		return postCalcGround($mas);
	}
}

// Рассчетa наземной доставки
function postCalcGround($mass,$RateZone=0) {
global $db;
	$db->query("SELECT cost, makeup FROM costway WHERE zone='".$RateZone."'");
	if($db->next_record()) {
		return ($db->Record["cost"]+intval($mass/500)*$db->Record["makeup"]);
	}
	return 0;
}

// Рассчетa авиа доставки
function postCalcAvia($BaseCoeff,$BaseRate,$TransCnt, $m) {
	return ($BaseCoeff*$BaseRate*0.5*$m + $TransCnt *$m);
}

?>