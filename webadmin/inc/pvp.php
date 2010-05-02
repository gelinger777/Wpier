<?
////////////////////////////////////////////////////////////////////////////
//
//      Virtual html-pages builder v5.0
//      Author: Maxim Tushev
//      Copyright 2004-2008
//
////////////////////////////////////////////////////////////////////////////

if(isset($MAINFRAME) && $MAINFRAME) include "./".$_CONFIG["ADMINDIR"]."/inc/descriptor.php";

include "./".$_CONFIG["ADMINDIR"]."/inc/pvptop.php";

if(isset($MAINFRAME) && $MAINFRAME=="yes") {
  include $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/inc/dvlvirtimages.inc";
}

if(!$HTML_FILE && $_SERVER["REQUEST_URI"][(strlen($_SERVER["REQUEST_URI"])-1)]!="/") {
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: ".$_SERVER["REQUEST_URI"]."/");
  exit();
}

if(isset($MAINFRAME) && $MAINFRAME) {
// Очистка Лога запросов
	 if(file_exists('./'.$_CONFIG["TEMP_DIR"].'/sql.log')) unlink('./'.$_CONFIG["TEMP_DIR"].'/sql.log');
	 $CALCULATE_QUERIES_LOG=1;
}

$CurrentCod=0;

function retNextDir() {
global $uri_arr, $CurrentCod;
  foreach($uri_arr as $k=>$v) {
    if($k>=$CurrentCod) {
      $CurrentCod=$k+1;
      return $v;
    }
  }
  return "";
}

function GetFullDbRecords($db) {
  $out=$db->Record;
  if(isset($db->RecordEdit)) foreach($db->RecordEdit as $k=>$v) {
    $out[$k."_edit"]=$v;
  }
  return $out;
}
$RootId=0;

if($sql){
  $path_arr=array();
  $id=0;
  $CurrentDir=retNextDir();
  $recs=array();
  $db->query("SELECT dir, ".$LANG."title as title, id, pid, hiddenlink FROM catalogue$FinSuf WHERE $sql ORDER BY pid");
  while($db->next_record()) {
    $recs[]=$db->Record;
    if($RootDir==$db->Record["dir"] && !$db->Record["pid"]) {
      $RootTitle=$db->Record["title"];
      $RootId=$db->Record["id"];
    }
  }



  foreach($uri_arr as $k=>$v) {
    $log=0;
    foreach($recs as $rec) {
      if($rec["pid"]==$id && $rec["dir"]==$v) {
        $path_arr[]=array($rec["title"], $rec["id"],  $rec["hiddenlink"], $rec["dir"], $rec["pid"]);
        $id=$rec["id"];
        $cod=$rec["id"];
        $log=1;
        break;
      }
    }
    if(!$log) {
      header("HTTP/1.0 404 Not Found");
      if(isset($_CONFIG["ERROR_404_PAGE"])) echo file_get_contents($_CONFIG["ERROR_404_PAGE"]);
      else echo "Error 404, page not found!";
      exit;
    }
  }

  if($id) {
  	  if(isset($_SESSION["adminlogin"])) $PERMISSIONS=array(1,1,1,1);
      // Проверяем права доступа на эту страницу
      $db->query("SELECT grp FROM accesspgpubl WHERE pg='$id'");
      $logExit=0;
      while($db->next_record()) {
        if(isset($_SESSION["usr_group"]) && $db->Record[0]==$_SESSION["usr_group"]) {
          $logExit=0;
          break;
        }
        $logExit=1;
      }
      if($logExit) {

        if(isset($_SESSION["adminlogin"])) {

	    	if($_SESSION["adminlogin"]!="root") {
	      		if(isset($_CONFIG["ACCESS_EMPTY_MODE"]) && $_CONFIG["ACCESS_EMPTY_MODE"]) {
	    			$PERMISSIONS=array(0,0,0);
	    			$db->query("SELECT  rd, ad, ed  FROM accesspgadmins WHERE pg='$id' and grp='".$_SESSION['admingroup']."'");
	    			if($db->next_record()) $PERMISSIONS=array(intval($db->Record[0]),intval($db->Record[1]),intval($db->Record[2]));
	  			} else {
	    			$PERMISSIONS=array(1,1,1);
	    			$db->query("SELECT  rd, ad, ed, grp  FROM accesspgadmins WHERE pg='$id'");

				    if($db->num_rows()>0) {
				    	$PERMISSIONS=array(0,0,0);
				    }
	    			while($db->next_record()) {
	      				if($_SESSION['admingroup']==$db->Record["grp"]) {
							$PERMISSIONS=array(intval($db->Record[0]),intval($db->Record[1]),intval($db->Record[2]));
							break;
	      				}
	    			}
	  			}
	  			if(!$PERMISSIONS[0]) {
	    			include 'webadmin/noaccesspage.php';
		    		echo str_replace("\n"," ",str_replace("\r","","<script>try {parent.ChangeCurrentTitle(window,'Нет доступа!',[],[],'',0);}catch(e){}</script>"));
	    			exit;
	  			}
			} else $PERMISSIONS=array(1,1,1,1);
      	} else {
	      	include 'webadmin/noaccesspage.php';
			echo str_replace("\n"," ",str_replace("\r","","<script>try {parent.ChangeCurrentTitle(window,'Нет доступа!',[],[],'',0);}catch(e){}</script>"));
			exit;
	    }
      }

      $db->query("SELECT
        catalogue$FinSuf.".$LANG."title as title,
        catalogue$FinSuf.dir,
        catalogue$FinSuf.pid,
        catalogue$FinSuf.id,
        catalogue$FinSuf.".$LANG."wintitle as wintitle,
        catalogue$FinSuf.".$LANG."windescript as windescript,
        catalogue$FinSuf.".$LANG."winkeywords as winkeywords,
        mkhtml,
		attr,
		spec,
        templates.tmpfile,
        catalogue$FinSuf.hiddenlink,
        catalogue$FinSuf.gotopage
        FROM catalogue$FinSuf LEFT JOIN templates ON templates.id=catalogue$FinSuf.tpl
        WHERE catalogue$FinSuf.id=$id");

	if($db->next_record()) {

		if($db->Record["spec"]) {
    	    header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$db->Record["spec"]);
            exit;
        }


        if($db->Record["gotopage"]) {

          $db->query("SELECT dir FROM catalogue$FinSuf WHERE id='".$db->Record["gotopage"]."' and pid='$id'");
          if($db->next_record()) {
			header("HTTP/1.1 301 Moved Permanently");
            header("Location: ./".$db->Record["dir"]."/");
            exit;
          }
        }

        $CurrentCod=$db->Record["id"];
        $cod=$db->Record["id"];
        $CurrentDir=$db->Record["dir"];
        $CurrentId=$id;
        $documentTitle=$db->Record["title"];

	if(isset($_CONFIG["RESOURCES_LOG"]) && $_CONFIG["RESOURCES_LOG"]) $INOUTER_GET_INFO_LOG_ARRAY["title"]=$db->Record["title"];

	$pageCurrentAttr=$db->Record["attr"];
        if($db->Record["mkhtml"]) $_CONFIG["CASH_STATUS"]=0;

        $pid=intval($db->Record["pid"]);
        if(!$db->Record["tmpfile"]) {
          echo "ОШИБКА: нет привязки к шаблону!";
          exit;
        }

        $TemplatesPath=$_CONFIG["TEMPLATES_DIR"]."/";

        if(isset($_GET["prn"]) && $_GET["prn"]=="yes") {
          $TemplatesPath.="print/";
          unset($_GET["prn"]);
        }


        if($_USERDIR) $TemplatesPath="./www/$_USERDIR/$TemplatesPath";

        $file=$TemplatesPath.substr($db->Record["tmpfile"],strrpos($db->Record["tmpfile"],"/")+1); // Файл шаблона

        if(file_exists($file)) $TMP=parse_template($file);
        else {
        	// Если файл шаблона не задан, создадим простой шаблон с 1м блоком
        	$TMP='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">' .
        			'<head>' .
        			'<title>%winTitle%</title>' .
        			'</head>' .
        			'<body>' .
        			'%BLOCK1%' .
        			'<h1>%title%</h1>' .
        			'%text%' .
        			'%/BLOCK1%' .
        			'</body></html>';
        }

        $MENU=parse_tmp("MENU");
        $MENUB=parse_tmp("MENUB");
        $LMENU3=parse_tmp("LMENU3");
        $LMENU2=parse_tmp("LMENU2");
        $LMENU1=parse_tmp("LMENU1");

        $CurrentTemplateFile=$db->Record["tmpfile"];

        $pageTitle=$db->Record["title"];
        $winTitle=$db->Record["wintitle"];
        $winDescript=$db->Record["windescript"];
        $winKeywords=$db->Record["winkeywords"];

        $crPt=0;
        $l=0;
        $cnt=count($path_arr);
        $InouterFullPath=array();

		// Строим хлебную крошку
        foreach($path_arr as $k=>$v) {
          $l++;
          if($l==$cnt) {
            if(!$crPt) {
              if(!$HTML_FILE) {
                $InouterFullPath[]=array($db->Record["title"],'',1);
                $crPt=1;
              } elseif($db->Record["hiddenlink"]!=2) {
                $InouterFullPath[]=array($db->Record["title"],'./',0);
              }
            }
          } else {
            $dir="";
            for($i=0;$i<($cnt-$l);$i++) {$dir.="../";}
            if($v[2]!=2) $InouterFullPath[]=array($v[0],$dir,0);
          }
        }


        $PATH=parse_tmp("PATH");
        $CURPATH=parse_tmp("CURPATH");
        function EchoFullPath($add="") {
          global $InouterFullPath,$PATH,$CURPATH;
          if(!$PATH){
            $PATH=parse_tmp("PATH");
            $CURPATH=parse_tmp("CURPATH");
          }
          $list="";
          foreach($InouterFullPath as $k=>$v) {
            if($v[2]) $s=str_replace("%title%",$v[0],$CURPATH);
            else $s=str_replace("%title%",$v[0],$PATH);
            $list.=str_replace("%dir%",$v[1],$s);
          }
          make_out("PATH",$list.$add);
        }
        EchoFullPath("%CURRENT_PATH%");

        // Делаем глобальный инклуд (если надо)
        if(isset($_CONFIG["GLOBAL_INCLUDE"]) && $_CONFIG["GLOBAL_INCLUDE"]) include $_CONFIG["GLOBAL_INCLUDE"];
        if(isset($_CONFIG["FRONTEND_GLOBAL_INCLUDE"]) && $_CONFIG["FRONTEND_GLOBAL_INCLUDE"]) include $_CONFIG["FRONTEND_GLOBAL_INCLUDE"];

        $BLOCKS=array();
        $BLOCKS_GLOBAL=array();
        $PAGETYPEROWLIST=$HTML_FILE;
        $BLOCKS_MODULS=array();

        $db->query("SELECT id,".$LANG."title as title, ".$LANG."text as text, spec,cpid, access_, nohtml, cmpw, catalogue_id, globalblock, ins2text, nocash FROM content$FinSuf WHERE catalogue_ID='$CurrentCod' or GlobalBlock!=0 ORDER BY id");

	    if(!$FinSuf) $db->EditMode=array("content"=>array("title"=>"text","text"=>"editor"));


        while($db->next_record()) {
          if($db->Record["spec"]) $BLOCKS_MODULS[$db->Record["spec"]]=-1;
          if($db->Record["globalblock"] && $db->Record["catalogue_id"]!=$CurrentCod) {
            $BLOCKS_GLOBAL[$db->Record["globalblock"]][]=GetFullDbRecords($db);
          }elseif($db->Record["cpid"]) {
            if(!isset($BLOCKS[$db->Record["cpid"]])) $BLOCKS[$db->Record["cpid"]]=array();
            $BLOCKS[$db->Record["cpid"]][]=GetFullDbRecords($db);
          } else {
            if(!isset($blocksRec[$db->Record["id"]])) $BLOCKS[$db->Record["id"]]=array();
            $BLOCKS[$db->Record["id"]][]=GetFullDbRecords($db);
          }
        }




        if(!$FinSuf) $db->EditMode=array();

        $db->query("SELECT grp,mdl FROM accessmodpubl WHERE mdl='".kjoin("' or mdl='",$BLOCKS_MODULS)."'");

        $s=(isset($_SESSION["usr_group"])? $_SESSION["usr_group"]:0);
        while($db->next_record()) {
          if($db->Record["grp"]==$s) $BLOCKS_MODULS[$db->Record["mdl"]]=1;
          elseif($BLOCKS_MODULS[$db->Record["mdl"]]=-1) $BLOCKS_MODULS[$db->Record["mdl"]]=0;
        }

        $BLOCKS_I=1;
        $CASHED_BLOCKS=array();
        $HTML_FILE_SAVED=$HTML_FILE;
        if(isset($_SESSION["adminlogin"])) {
          $BLOCK_TEXTS=array(array(),array());
        }
        foreach($BLOCKS as $BLOCKS_KEYY=>$BLOCKS_VV) {
          $BLKTMP=parse_tmp("BLOCK".$BLOCKS_I);

          if(isset($BLOCKS_GLOBAL[$BLOCKS_I])) {
            $x=array();
            foreach($BLOCKS_GLOBAL[$BLOCKS_I] as $v) $x[]=$v;
            foreach($BLOCKS_VV as $v) $x[]=$v;
            $BLOCKS_VV=$x;
          }

          $BLKOUT="";

          foreach($BLOCKS_VV as $BLOCKS_V) if($BLOCKS_V["nohtml"]!="3") {

            if((!$BLOCKS_V["nohtml"] || ($BLOCKS_V["nohtml"]=="1" && !$PAGETYPEROWLIST) || ($BLOCKS_V["nohtml"]=="2" && $PAGETYPEROWLIST))) {
              $BLK=$BLKTMP;
              $svHTML_FILE=0;

              /*if(!isset($_GET["prev"]) && !isset($_GET["cont"]) && $BLOCKS_V["spec"] && $BLOCKS_V["access_"]==5 && $BLOCKS_V["text"]) {
                $BLK=str_replace("&#39;","'",$BLOCKS_V["text"]);
              } else */

              if($BLOCKS_V["spec"]) {
                if(isset($MAINFRAME) && $MAINFRAME) {
                  $INOUTER_GET_INFO_LOG_ARRAY["moduls"][$BLOCKS_V["spec"]]=array("time"=>time()+microtime());
                }

                // подключаем спецразделы для блоков ------------------------------------------------------------------------
                $svHTML_FILE=$HTML_FILE;
                $BLOCK_MOD_FILE="";
                $BLOCK_TEMPLATE_FILE="";

                include "./".$_CONFIG["ADMINDIR"]."/inc/specblock.php";

                if(isset($MAINFRAME) && $MAINFRAME) {
                  $INOUTER_GET_INFO_LOG_ARRAY["moduls"][$BLOCKS_V["spec"]]["time"] = time()+microtime()-$INOUTER_GET_INFO_LOG_ARRAY["moduls"][$BLOCKS_V["spec"]]["time"];
                  $INOUTER_GET_INFO_LOG_ARRAY["moduls"][$BLOCKS_V["spec"]]["size"] = strlen($BLK);
                }

                if(isset($_SESSION["adminlogin"])) $BLK="<!-- Module :: $BLOCK_MOD_FILE -->".($BLOCK_TEMPLATE_FILE? "\n<!-- Template :: $BLOCK_TEMPLATE_FILE -->":"")."\n<!-- START BLOCK ".$BLOCKS_V["id"]." -->\n$BLK<!-- END BLOCK -->";
                // К подключаем спецразделы для блоков -----------------------------------------------------------

              } else {

                if(isset($_SESSION["adminlogin"]) && !$BLOCKS_V["text"]) {
                  $BLOCKS_V["text"]='<span class="edt:content:text:'.$BLOCKS_V["id"].':editor">&nbsp;</span>';
                }

                if($BLOCKS_V["text"] || $BLOCKS_V["title"]) {

                  if(isset($_CONFIG["TEXT_TO_PAGES"]) && $_CONFIG["TEXT_TO_PAGES"]) include "webadmin/inc/text2pages.php";

                  $BLK=str_replace("%text%",$BLOCKS_V["text"], TMP_if_blocks($BLK,"text",($BLOCKS_V["text"]? 1:0)));
                  $BLK=str_replace("%title%",$BLOCKS_V["title"],TMP_if_blocks($BLK,"title",($BLOCKS_V["title"]? 1:0)));
                } else $BLK="";
              }

              if(isset($_SESSION["adminlogin"])) {
                if(!isset($BLOCKS_V["addmode"])) $BLOCKS_V["addmode"]=1;
                if($BLOCKS_V["spec"]) {
                  if(!$BLOCKS_V["title"]) $BLOCKS_V["title"]=$BLOCKS_V["spec"];
                  $t=explode("\n",$BLOCKS_V["title"]);
                  $t=explode("<",$t[0]);
                  $BLOCK_TEXTS[1][]="['".trim($t[0])."','".$BLOCKS_V["spec"]."',".($BLOCKS_V["addmode"]? "true":"false").",'".(isset($_CONFIG["TPL_STORE"]) && $_CONFIG["TPL_STORE"]? $_CONFIG["TPL_STORE"].$BLOCK_TEMPLATE_FILE:"")."']";
                } else {
                  $BLOCKS_V["title"]=strip_tags($BLOCKS_V["title"]);
                  if(!$BLOCKS_V["title"])
                    $BLOCKS_V["text"]=trim(strip_tags($BLOCKS_V["text"]));
                  $BLOCK_TEXTS[0][]="['".($BLOCKS_V["title"]? $BLOCKS_V["title"]:($BLOCKS_V["text"]? makeShortText($BLOCKS_V["text"],30):"empty"))."',".$BLOCKS_V["id"]."]";
                }
              }
              if(isset($BLOCKS_V["ins2text"]) && $BLOCKS_V["ins2text"]) {
                $BLKOUT=eregi_replace('<IMG id=BLOCK_'.$BLOCKS_V["id"].' [^>]{1,}>',$BLK,$BLKOUT);
              } else $BLKOUT.=$BLK;
            }
          }

          make_out("BLOCK".$BLOCKS_I,"<!-- INDEXAREA -->".$BLKOUT."<!-- /INDEXAREA -->");
          if(!$BLKOUT) make_out("emp_block".$BLOCKS_I,"empty");
          $BLOCKS_I++;
        }

        unset($BLOCKS);
        unset($BLOCKS_GLOBAL);
        unset($BLOCKS_VV);
        unset($BLKOUT);
        unset($BLOCKS_V);
        unset($BLK);

        $BLKOUT="";
        make_out("pageTitle",$pageTitle);
        make_out("wintitle",$winTitle);
        make_out("windescript",$winDescript);
        make_out("winkeywords",$winKeywords);

        $MENU=array(parse_tmp("MENU0"),parse_tmp("MENU1"),parse_tmp("MENU2"));
        $db->query("SELECT mainmenu.*, mainmenu.item as title, catalogue$FinSuf.dir FROM mainmenu, catalogue$FinSuf WHERE mainmenu.page=catalogue$FinSuf.id ORDER BY mainmenu.id");
        $MENUITEMSARR=array();
        while($db->next_record()) {
          $mlst=array();
          foreach($db->Record as $k=>$v) if($v) {
            if(strval($k)=="topm") {$mlst[]=0;}
            elseif(strval($k)=="botm") $mlst[]=1;
            elseif(strval($k)=="leftm") $mlst[]=2;
            elseif(strpos(" $k","MN_")==1) {
              $k=explode("_",$k);
              $mlst[]=intval($k[1]);
            }
          }
          foreach($mlst as $k=>$v) {
            if(!isset($MENU[$v])) $MENU[$v]=parse_tmp("MENU$v");
            $MENUITEMSARR[$v][$db->Record["page"]]=$db->Record;
          }
        }

        foreach($MENUITEMSARR as $key=>$val)   {
          $list="";
          $i=0;
          foreach($val as $k=>$v) {
            $v["last"]=(++$i==count($val)? "1":"");
            $v["sel"]=($CurrentCod==$k? "1":"");
            $v["id"]=$k;
            $list.= sendAr2blk($v,$MENU[$key]);
          }
          make_out("MENU$key", $list);
        }
        make_out("headItem",$pageTitle);
        $id=$CurrentId;
        $key=0;
        $LINKS=parse_tmp("LINKS");
//echo "|$LINKS|";
        $db->query("SELECT id,".$LANG."title as title, dir, spec FROM catalogue$FinSuf WHERE pid='$id' and (hiddenlink<'1' or hiddenlink is NULL) ORDER BY indx");
        $list="";
        while($db->next_record()) {
          $db->Record["title"]=str_replace('"','&quot;',$db->Record["title"]);
          $s=str_replace("%title%",$db->Record["title"], $LINKS);
          $s=str_replace("%cod%",$db->Record["id"], $s);
          $s=str_replace("%id%",$db->Record["id"], $s);
          $s=str_replace("%sel%",($db->Record["id"]==$CurrentCod? "1":""), $s);
          $s=str_replace("%LTitle%",$db->Record["title"], $s);
          $list.=str_replace("%dir%",$db->Record["dir"], $s);
          $key++;
        }
        $ListPid=$pid;

        $links=str_replace('"./%dir%/"','"/%dir%/"',$LINKS);
        $links=str_replace("'./%dir%/'","'/%dir%/'",$links);
        $dirsbak="";

        while(!$list && $ListPid){// && !isset($_CONFIG["NOSHOWPARENTLINKS"])) {
          $dirsbak.="../";
          $linkss=str_replace('"/%dir%/"','"'.$dirsbak.'%dir%/"',$links);
          $linkss=str_replace("'/%dir%/'","'".$dirsbak."%dir%/'",$links);

          $db->query("SELECT id, ".$LANG."title as title, dir, spec FROM catalogue$FinSuf WHERE pid='$ListPid' and (hiddenlink<'1' or hiddenlink is NULL) ORDER BY indx");
          $list="";
          while($db->next_record()) {
            $db->Record["title"]=str_replace('"','&quot;',$db->Record["title"]);
            $s=str_replace("%title%",$db->Record["title"], $linkss);
            $s=str_replace("%cod%",$db->Record["id"], $s);
            $s=str_replace("%id%",$db->Record["id"], $s);
            $s=str_replace("%sel%",($db->Record["id"]==$CurrentCod? "1":""), $s);
            $s=str_replace("%LTitle%",$db->Record["title"], $s);
            $list.=str_replace("%dir%",$db->Record["dir"], $s);
            $key++;
          }
          $db->query("SELECT id,".$LANG."title as title, dir, pid FROM catalogue$FinSuf WHERE id='$ListPid' ORDER BY indx");
          if($db->next_record()) {
             $ListPid=$db->Record["pid"];
             if($list) {
               $s=str_replace("%dir%/","", $linkss);
               $s=str_replace("%cod%",$db->Record["id"], $s);
               $s=str_replace("%id%",$db->Record["id"], $s);
               $s=str_replace("%sel%",($db->Record["id"]==$CurrentCod? "1":""), $s);
               $list=str_replace("%title%",$db->Record["title"], $s).$list;
               $key++;
             }
          } else {
              $ListPid=0;
          }
        }

        if($list && !$dirsbak && !isset($_CONFIG["NOSHOWPARENTLINKS"])) {
          $s=str_replace("%dir%/","", $LINKS);
          $s=str_replace("%sel%","1", $s);
          $list=str_replace("%title%",$documentTitle, $s).$list;
          $key++;
        }

        make_out("LINKS", $list);
        make_out("LinksCount", $key);
        $db->query("SELECT keyname,".$LANG."valtext as valtext FROM labels");
        while($db->next_record()) make_out($db->Record[0],$db->Record[1]);

        include dirname(__FILE__)."/echo.php";

	if($RootDir=="err404") echo "<!--{{ERROR404}}-->";
	else// Сохраним лог производительности
	  if(isset($_CONFIG["RESOURCES_LOG"]) && $_CONFIG["RESOURCES_LOG"]) include "./".$_CONFIG["ADMINDIR"]."/inc/end_get_info.php";

	if(isset($_SESSION["adminlogin"])) {
          /*echo "<style>
          .InouterEditable1{url(/webadmin/ext/img/white50.png)}
          .InouterEditable2{url(/webadmin/ext/img/white20.png)}
          </style>";*/

          /*foreach($BLOCK_TEXTS[1] as $k=>$v) {
             $v=strip_tags($v);
             $v=explode("\n",$v);
             $BLOCK_TEXTS[1][$k]=trim($v[0]);
          } */

	  if(!isset($PERMISSIONS)) $PERMISSIONS=array();
          echo str_replace("\n"," ",str_replace("\r","","<script>try {parent.ChangeCurrentTitle(window,'".$pageTitle."',[".join(",",$BLOCK_TEXTS[0])."],[".join(",",$BLOCK_TEXTS[1])."],'".(isset($_CONFIG["TPL_STORE"]) && $_CONFIG["TPL_STORE"]? $_CONFIG["TPL_STORE"].str_replace("../","./",$CurrentTemplateFile):"")."',".$CurrentCod.(count($PERMISSIONS)? ",":"").join(",",$PERMISSIONS).",'".$pageCurrentAttr."');}catch(e){}</script>"));
        }

        if(isset($_SESSION["ses_mode"]) && $_SESSION["ses_mode"]==1) {
          include "./".$_CONFIG["ADMINDIR"]."/outproc/editable.php";
        }

        $_SESSION["ses_baner"]="";
        if(!isset($_GET["search"])) {
          if(isset($_SESSION["ses_search"])) unset($_SESSION["ses_search"]);
        }

        if(isset($_CONFIG["COOKIE_ONLY"]) && $_CONFIG["COOKIE_ONLY"]) include "./".$_CONFIG["ADMINDIR"]."/inc/session2cookie.php";

        exit();
      }
  } else {
    if($_SERVER["REQUEST_URI"]!="/err404/") {
    header("Location: /err404/");
  }
    exit();
  }
} elseif($_SERVER["REQUEST_URI"]!="/err404/") {
  header("Location: /err404/");
  exit();
}
