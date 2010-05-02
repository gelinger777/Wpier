<?
function list_all($tbname, $f_array, $orderby, $start, $step) {
	global $db,$CataloguePgID,$PROPERTIES,$EXT;
	global $updown;
	global $OBJECTS;
	global $WHERE;
	global $SQL;
	global $SQL_PAGES;
	global $PAGES;
	global $MARK_COLOR,$NODEL, $PREVIEW,$SpanArray,$ADMINGROUP,$EXT,$COLUMNS_SIZE;

        //$WHERE=$PROPERTIES["WHERE"];

	if(!$step) $step=10;
	if(!isset($SpanArray)) $SpanArray=array();
	
	$NumColSpan=0;

	$listtab="<div class='InouterTable' id='$EXT'><table border=0 cellspacing=1 cellpadding=4 width='100%' class='hTable' align='center'>";
	$listtab.="<TR class='td_top'>";

	if (!isset($WHERE)) $WHERE="";
	
	$wArrL=array();
	$sql=array();
	$fltrsList="";
	$foldList="";

	if(isset($PROPERTIES["filters"])) {
		$listtab.="<form><input type='hidden' name='ext' value='$EXT'>".(isset($PROPERTIES["print"])? "<input type='hidden' name='prn' value=''>":"");	
        }
	$dbFolders=$db->folders_names($tbname);

	if(!isset($PROPERTIES["delalert"]) ||  !$PROPERTIES["delalert"]) $PROPERTIES["delalert"]="Удалить?";

	
        
        foreach ($f_array as $key=>$val) if($val!="*hide*") {
                $ob=$key;
		$val=explode("|",$val);
		
		if (isset($_GET["orderby"])) if($_GET["orderby"]==$ob) $ob.=" DESC";
		$listtab.="";
		
		if(isset($PROPERTIES["filters"])  && !in_array($key,$SpanArray)) {
			$fltrsList.="<td>";
			if(!isset($PROPERTIES["updown"]) || ($key!="id" && $PROPERTIES["updown"]) || !$PROPERTIES["updown"]) {
				if(isset($_GET[$key])  && (isset($_GET["filtersGoSubmit"]) || isset($_GET["filtersGoSubmitXls"])) && $_GET[$key]) {
					$s=$OBJECTS[$key]->getFilterSearch($key,$_GET[$key]);		
					if($s) $wArrL[]=$s;
				}
				if(!isset($_GET[$key])) $_GET[$key]="";
				$fltrsList.=$OBJECTS[$key]->getFilter($key,$_GET[$key]);
			} else $fltrsList.="&nbsp;";
			$fltrsList.="</td>";
		}

		if(isset($dbFolders[$key])) {
			if(!in_array($key,$SpanArray)) {
				if(count($SpanArray)) $NumColSpan++;
				$foldList.="<td>";
                                if(isset($OBJECTS[$key]->PROP["type"]) && $OBJECTS[$key]->PROP["type"]=="text") $foldList.="<a ";
                                else {                                 
                                  $foldList.="<a href='?ext=$EXT&";
				  foreach($_GET as $k=>$v) if($k!="orderby" && $v!='%') {
					if(is_array($v)) {
					  $foldList.=$k."[]=".join("&".$k."[]=",$v)."&";
					} else $foldList.="$k=$v&";
				  }				
				  $foldList.="orderby=$ob'";
                                }
                                $foldList.="style='color: #ffffff'><b>$val[0]</b></a>".(isset($COLUMNS_SIZE[$key])? "<br><img src='./img/dot.gif' width='".$COLUMNS_SIZE[$key]."' height='1' alt='' border='0' />":"")."</td>";
			}
			$sql[]="$tbname.$key";
		} elseif(!in_array($key,$SpanArray)) {
			if(count($SpanArray)) $NumColSpan++;
			$foldList.="<td><b>$val[0]</b></td>";
		}
	}
	
	if(count($wArrL)) {
		if($WHERE) $WHERE.=" and ";
		else $WHERE="WHERE ";
		$WHERE.=join(" and ",$wArrL);
	}

	foreach ($f_array as $key=>$val) if($val=="*hide*" && isset($dbFolders[$key])) {
		$sql[]=$key;
	}

	$sql="SELECT ".((isset($PROPERTIES["publication"]) && $PROPERTIES["publication"])? "LastPublAdmin,":"").join(",",$sql);
	
	if(isset($_GET["prn"]) && $_GET["prn"]=="yes") {}
	else {
		if (isset($PREVIEW)) {
			if($fltrsList) $fltrsList.="<td></td>";
			$foldList.="<td><b>Preview</b></td>";
		}
		if($fltrsList) $fltrsList.="<td><input type='submit' class='srchinp' value='Go!' name='filtersGoSubmit'></td><td><input type='button' class='srchinp' value='Refr.' onclick='navigate(\"".$_SERVER["SCRIPT_NAME"]."\")'></td>".(isset($PROPERTIES["xls"])? "<td><input type='submit' class='srchinp' value='Xls' name='filtersGoSubmitXls'></td>":"")."</form>";
		if(!isset($PROPERTIES["NOEDIT"]) && ((isset($ADMINGROUP["modedit"]) && in_array($EXT,$ADMINGROUP["modedit"])) || (isset($ADMINGROUP["modread"]) && in_array($EXT,$ADMINGROUP["modread"])) || $_SESSION["adminlogin"]=="root")) $foldList.="<td><b>".((isset($ADMINGROUP["modedit"]) && in_array($EXT,$ADMINGROUP["modedit"]))? "ред.":"просм.")."</b></td>";
		if(!isset($NODEL)) {
			$foldList.="<td><b>уд.</b></td>";
			$foldList.='<form action="?'.EchoGetStr("ext",$EXT).'" name="delSelectFrrm" method="post"><input type="hidden" name="action" value=""><td><IMG SRC="./img/nosel.gif" WIDTH="11" HEIGHT="11" BORDER=0 ALT="выделить/снять выделение" style="border:1 solid #ffffff;cursor:hand" onclick="selectAll()">&nbsp;<IMG SRC="img/new/del.gif" WIDTH="11" HEIGHT="11" BORDER=0 ALT="удалить выделенное" style="border:1 solid #ffffff;cursor:hand" onclick="if(confirm(\'Удалить?\')) document.forms[\'delSelectFrrm\'].submit()"></td>';
		}
	}
	if($fltrsList) $listtab.="$fltrsList</TR><TR class='td_top'>";
	$listtab.=$foldList;
	$listtab.="</TR>";

	if ($SQL) $sqlQ="$SQL $WHERE ORDER BY $orderby";
	else {
		if(isset($CataloguePgID) && $CataloguePgID && isset($PROPERTIES["conect"])) {
                        $sqlQ="$sql FROM $tbname,".$tbname."catalogue ".($WHERE? "$WHERE and ":"WHERE ").$tbname."catalogue.pgID='$CataloguePgID' and ".$tbname."catalogue.rowID=$tbname.".$PROPERTIES["conect"];
		} else {
		        $sqlQ="$sql FROM $tbname $WHERE";
		}
		$sqlQ.=" ORDER BY $tbname.$orderby";
	}

	if(isset($_GET["filtersGoSubmitXls"])) {
		include "./excel/store.php";
	}

	// Рисуем страницы
	if(isset($_GET["prn"]) && $_GET["prn"]=="yes") {$db->query($sqlQ);}
	else {
		$currentpage="<b>%npage%</b>&nbsp;";
		$pages="<a href='?ext=$EXT&";
		if(!isset($_GET["aft"])) foreach($_GET as $k=>$v) if($k!="start" &&  $v!='%' && $k!="len") {
			if(is_array($v)) {
			    foreach($v as $vk=>$vv) {
			      $pages.=$k."[".$vk."]=$vv&";
			    }
			} else {
                            $pages.="$k=$v&";
                        }
		}
		$pages.="start=%start%'>";

		if ($start>0) {$strt=$start-1;$stp=$step+1;}else {$strt=$start;$stp=$step;}
	
		$n=explode("ORDER BY",substr($sqlQ,strpos($sqlQ,"FROM")));
		$db->query("SELECT count(*) ".$n[0]);
		if($db->next_record()) {		
		        $num=$start+$step;
		        if($num>$db->Record[0]) $num=$db->Record[0]; 
			$PAGES="<b>Записей: ".$db->Record[0]."</b> (показано: ".($start+1)." - ".$num.")<br>";
                        $n=intval($db->Record[0]/$step);
			if ($db->Record[0]>$n*$step) $n++;
			
			if ($n>1) {
                                /*for ($i=0;$i<$n;$i++) {
				if ($start==strval($i*$step)) $st=$currentpage;
				else $st=$pages;
				$st=str_replace("%start%",strval($i*$step),$st);
				$st=str_replace("%npage%",strval($i+1),$st);
				$PAGES.=$st;}*/
				$PAGES.="<b>Страница ".(intval($start/$step)+1)."</b> (из $n) ";
				if($strt>1) $PAGES.=str_replace("%start%",($start-$step),$pages)."« пред.</a>&nbsp;&nbsp;"; 
				if(($strt+$stp)<$db->Record[0]) $PAGES.=str_replace("%start%",($start+$step),$pages)."след. »</a>&nbsp;&nbsp;";
				
			}
		}

		$db->query($sqlQ." LIMIT $start, $step");
	}
//echo $db->LastQuery;
	$idprev=0;
	$iRow=0;
	
	$i="td_b1";
	$m="";
	$listtab.="<SCRIPT>startPosRow=".intval($start).";wLocat='".$_SERVER["SCRIPT_NAME"]."';</SCRIPT>";
	$DbRecords=array();
    while ($db->next_record(1)) {
        if ($start>0 && isset($updown) && !$idprev) $idprev=$db->Record[$updown];
		$DbRecords[]=$db->Record;
	}
//exit;	

	foreach($DbRecords as $Record) {
		if (isset($MARK_COLOR)) if (!isset($Record["mark_color"])) $m.="m";
		
		$class=$i.$m.((isset($Record["LastPublAdmin"]) && $Record["LastPublAdmin"])? "_nopubl":"");
		$listtab.="<tr valign='top' class='$class'>";
		$SpanAr=array();
		foreach ($f_array as $key => $val) if($val!="*hide*") {
			$key=explode(".",$key);
			if(isset($key[1])) $key=$key[1];
			else $key=$key[0];
			
			if(!isset($Record[$key])) {
				$Record[$key]=$Record["id"];
			}
			
			$spp=explode("|",$val);
			if (isset($F_ARRAY[$key])) $sp=explode("|",$F_ARRAY[$key]);
			elseif (isset($FF_ARRAY[$key])) $sp=explode("|",$FF_ARRAY[$key]);
			else $sp[0]="";
			
                        if(method_exists($OBJECTS[$key],'SetID'))
                          $OBJECTS[$key]->SetID($Record["id"]); 		
			$listtabb=$OBJECTS[$key]->mkList($Record[$key]);
				
			// Делаем линки где надо
			if (isset($spp[1])) {
				if ($spp[1]=="link") $listtabb="<a href='?lnk=".$Record[$spp[3]]."&ext=".$spp[2]."'>$listtabb</a>";
				elseif ($spp[1]=="url") $listtabb="<a href='http://".$Record[$spp[2]]."' target='_blank'>$listtabb</a>";
				elseif ($spp[1]=="stronglink") $listtabb="<a href='".$spp[2].$Record[$key]."' target='_blank'>".(isset($Record[$spp[3]])? $Record[$spp[3]]:$spp[3])."</a>";
				elseif ($spp[1]=="editlink") $listtabb="<a href='?ext=$EXT&start=$start&ch=".$Record["id"]."'>".$Record[$key]."</a>";
			}

			if(in_array($key,$SpanArray)) {
				$SpanAr[$key]=array($spp[0],$listtabb);
			} else {
			
				$listtab.="<td";
				if (isset($updown)) if ($updown==$key) $listtab.=" onclick='mvRw1(this,".$Record["id"].",".$idprev.")' valign='middle'";
				$listtab.=">";	

				// Делаем вверх-вниз
				if (isset($updown)) if ($updown==$key && $idprev) {
					$listtabb="<center><img src='./img/up.gif' border=0 alt='up'></center>";
				} elseif ($updown==$key) $listtabb="";
				$listtab.="$listtabb</td>";
			}			
		}
		
		if ($i=="td_b1") $i="td_b2"; else $i="td_b1";
		
		if(isset($_GET["prn"]) && $_GET["prn"]=="yes") {}
		else {
                    if (isset($PREVIEW)) {
				$listtab.="<td".($NumColSpan? " rowspan=2":"")."><center><a href='./prev.php?$PREVIEW&ext=$EXT&id=".$Record["id"]."&start=$start' target='_blank'>[preview]</a></center></td>";
			}
		    if(!isset($PROPERTIES["NOEDIT"]) && ((isset($ADMINGROUP["modedit"]) && in_array($EXT,$ADMINGROUP["modedit"])) || (isset($ADMINGROUP["modread"]) && in_array($EXT,$ADMINGROUP["modread"])) || $_SESSION["adminlogin"]=="root")) {
				$listtab.="<td".($NumColSpan? " rowspan=2":"")."><center><a href='".$_SERVER["SCRIPT_NAME"]."?".EchoGetStr("ch",$Record["id"])."'><IMG SRC='./img/".((isset($ADMINGROUP["modedit"]) && in_array($EXT,$ADMINGROUP["modedit"]))? "edit":"view").".gif' BORDER=0 ALT='Редактировать'></a></center></td>";
		    } else $listtab.="<td".($NumColSpan? " rowspan=2":"").">&nbsp;</td>";
		    if(!isset($NODEL)) {
				$listtab.="<td".($NumColSpan? " rowspan=2":"")."><center><a style='cursor:hand' onclick='if (confirm(\"".$PROPERTIES["delalert"]."\")) window.location=\" ".$_SERVER["SCRIPT_NAME"]."?ext=$EXT&del=".$Record["id"]."&start=".$start."\";'><IMG SRC='./img/del.gif' BORDER=0 ALT='Удалить'></a></center></td>";
				$listtab.="<td align='center'".($NumColSpan? " rowspan=2":"")."><INPUT type='checkbox' name='delCheck[".($iRow++)."]' value='".$Record["id"]."' class='smlchk1'></td>";
		    }  else $listtab.="<td".($NumColSpan? " rowspan=2":"").">&nbsp;</td><td".($NumColSpan? " rowspan=2":"").">&nbsp;</td>";
		}   
		$listtab.="</tr>";
		if($NumColSpan && count($SpanAr)) {
			$listtab.="<tr class='$class'><td colspan='$NumColSpan' style='padding-bottom:20px'>";
			foreach($SpanArray as $val) if(isset($SpanAr[$val]) && trim($SpanAr[$val][1])) {
				$listtab.=($SpanAr[$val][0]? $SpanAr[$val][0]:"").$SpanAr[$val][1]."<br>";
			}
			$listtab.="</tr>";
		}
		if (isset($updown)) $idprev=$Record[$updown];
	}
	$listtab.="</form></table></div>";
	return $listtab;
}
?>