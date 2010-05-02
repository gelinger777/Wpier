<?
include_once "./autorisation.php";

$PROPERTIES=array(
"tbname"=>"catalogue",
"pagetitle"=>"",
"alongpage"=>"y",
);
$PERSONALPAGE=1;
$POCKET_UPDATE=array("attr","hiddenlink","wintitle","windescript","winkeywords","mkhtml");

if(isset($_POST["id"])) $_GET["ch"]=$_POST["id"];

$F_ARRAY_PROPS=array(
"title"=>str_replace("##","#".$_GET["ch"],$TEXTS["PageProps"]),
"collapsed"=>0,
"items"=>array(
  "gotopage",
  "title",
  "wintitle",
  "dir",
  //"attr",
  "hiddenlink",
  "mkhtml",
  "deflt",

  "tpl"
  )
);

include "./menu.inc";

//tpl,title,dir,hiddenlink,wintitle,mkhtml,deflt,gotopage,

$F_ARRAY=Array ("id" => "hidden||");
$F_ARRAY["block01"] = "block|windescript,winkeywords|".$TEXTS["PageMeta"]."|1";
//$F_ARRAY["tpl"]="hidden||";

//$F_ARRAY["block02"]="block|gotopage,title,wintitle,dir,hiddenlink,mkhtml,deflt,tpl|Свойства страницы|1";

$F_ARRAY["tpl"]="select|templates*id*tmpName|".$TEXTS["PageTemplate"];
$F_ARRAY["title"] = "text|size=80|".$TEXTS["PageSectionName"];
$F_ARRAY["dir"] = "text|size=20|".$TEXTS["PageDirName"];

// Пакетные изменения
if(isset($_POST["id"])) {
  $id=explode(",",$_POST["id"]);
  $_POST["id"]=$id[0];

  if(count($id>1)) {
    $sql=array();
    $db->query("SELECT * FROM catalogue WHERE id='".intval($_POST["id"])."'");
    if($db->next_record()) {
      foreach($POCKET_UPDATE as $v) {
        if(!isset($_POST[$v])) $_POST[$v]="";
        if($_POST[$v]!=$db->Record[$v]) $sql[]="$v='".(isset($_POST[$v])? htmlspecialchars($_POST[$v]):"")."'";
        }
    }
    if(count($sql)) {
      $sqlSTR="UPDATE catalogue SET ".join(",",$sql)." WHERE id=";

      for($i=1;$i<count($id);$i++) {
        $db->query("SELECT id FROM catalogue WHERE id='".intval($id[$i])."'");
        if($db->next_record()) {
          $bakup_ID=$db->Record["id"];
          include "./page_save_bakup.php";
          // Удаляем последний откат
          $db->query("SELECT id FROM cataloguebakup WHERE id='$bakup_pcod' ORDER BY id DESC LIMIT ".($bakup_steps).",1");
          if($db->next_record()) {
            $bakup_last=$db->Record["id"];
            $db->query("DELETE FROM cataloguebakup WHERE id='$bakup_last'");
            $db->query("DELETE FROM contentbakup WHERE bakup_id='$bakup_last'");
          }
          $db->query($sqlSTR."'".$bakup_ID."'");
        }
      }
    }
  }
}



$db->query("SELECT id, pid, tpl FROM ".$PROPERTIES["tbname"]." WHERE id='".intval($_GET["ch"])."'");
if($db->next_record()) {
  //if(!$db->Record["pid"]) $F_ARRAY["menu"] = "checkbox||Пункт меню|";
  $idPid=$db->Record["pid"];
  $idRow=$db->Record["id"];
  // Если поменялся шаблон, нужно обновить список блоков ---------------------------
  if(isset($_POST["tpl"]) && $_POST["tpl"]!=$db->Record["tpl"]) {
    $_POST["tpl"]=intval($_POST["tpl"]);
    //$_POST["cod"]=intval($_POST["cod"]);
    ChangeTpl($_POST["tpl"],$idRow);
  }
  if(isset($_POST["deflt"])) {
    $db->query("UPDATE catalogue SET deflt='' WHERE pid='$idPid'");
  }
}



$F_ARRAY["attr"] = "select||".$TEXTS["PageAction"]."|1/".$TEXTS["PageAction1"]."|2/".$TEXTS["PageAction2"]."|3/".$TEXTS["PageAction3"];
$F_ARRAY["hiddenlink"] = "select||".$TEXTS["PageStatus"]."|/".$TEXTS["PageStatus0"]."|1/".$TEXTS["PageStatus1"]."|2/".$TEXTS["PageStatus2"]."";
$F_ARRAY["spec"] = "text|size=80 maxlength=255|".DLG("Redirect link");
$F_ARRAY["wintitle"] = "text|size=80 maxlength=255|".$TEXTS["PageWinTitle"];
$F_ARRAY["windescript"] = "textarea|cols=80 rows=3|".$TEXTS["PageDescript"];
$F_ARRAY["winkeywords"] = "textarea|cols=80 rows=3|".$TEXTS["PageKeywords"];
$F_ARRAY["mkhtml"] = "checkbox||".$TEXTS["PageNoCash"]."|";
$F_ARRAY["deflt"] = "checkbox||".$TEXTS["PageMkStandard"]."|";

if(isset($_GET["ch"])) {
  $F_ARRAY["gotopage"] = "select|catalogue*id*title*pid='".intval($_GET["ch"])."'*id|".$TEXTS["PageGoto"]."|/".$TEXTS["PageGotoNone"];
}
//$F_ARRAY["hiddenlink"] = "checkbox||спрятать в структуре каталога|";

//$F_ARRAY["separator_1"] = "separator|<div id='reserv_div'>|<b>".$TEXTS["PageKickback"]."</b>";
//$F_ARRAY["separator_2"] = "separator|<span></span>|<h2>".$TEXTS["PageTplName"]."</h2>";



$f_array=Array(
"id" => "",
"title" => ""
);

$alongpage="y";
$FORM_EXTEND="";
$DELTABS=array("content"=>"catalogue_id","accessread"=>"pg","accesswrite"=>"pg");

// Смотрим есть ли  на этом уровне директория с тем-же названием
$doubleDir=0;
if(isset($_POST["dir"])) {
  $db->query("SELECT id FROM ".$PROPERTIES["tbname"]." WHERE id!='".intval($_GET["ch"])."' and dir='".$_POST["dir"]."' and pid='$idPid'");
  if($db->num_rows()) {
    $_POST["dir"]=$idRow;
    $doubleDir=1;
  }
}

// Сохраняем откат
if(isset($_POST["upd"])) {
  $bakup_ID=intval($_POST["id"]);
  include "./page_save_bakup.php";
  // Удаляем последний откат
  $db->query("SELECT id FROM cataloguebakup WHERE id='$bakup_pcod' ORDER BY id DESC LIMIT ".($bakup_steps).",1");
  if($db->next_record()) {
    $bakup_last=$db->Record["id"];
    $db->query("DELETE FROM cataloguebakup WHERE id='$bakup_last'");
    $db->query("DELETE FROM contentbakup WHERE bakup_id='$bakup_last'");
  }
}

require "./page_return_bakup.php";

// Добавление доп. блока
if(isset($_GET["addsb"]) && intval($_GET["addsb"]) && isset($_GET["cod"]) && intval($_GET["cod"])) {
  $db->query("INSERT INTO content (catalogue_id,cpid,ins2text,spec) VALUES ('".intval($_GET["cod"])."','".intval($_GET["addsb"])."',".(isset($_GET["instext"])? "'1','".$_GET["instext"]."'":"'',''").")");

  if(isset($_GET["instext"])) {
    ?>

  <SCRIPT LANGUAGE="JavaScript">
  parent.addModuleToEditor('<?=getLastID()?>');

  </SCRIPT>

  <?exit;
  }
}

// Удаление доп. блока
if(isset($_GET["dblk"]) && intval($_GET["dblk"])) {
  $db->query("DELETE FROM content WHERE id='".intval($_GET["dblk"])."'");
}
// Меняние местами блоков
if(isset($_GET["pblk"]) && intval($_GET["pblk"]) && isset($_GET["nblk"]) && intval($_GET["nblk"])) {
  $pb=intval($_GET["pblk"]);
  $nb=intval($_GET["nblk"]);
  $db->query("SELECT * FROM content WHERE id='$pb' or id='$nb'");
  $buf=array();
  while($db->next_record()) {
    $buf[$db->Record["id"]]=$db->Record;
  }
  $db->query("DELETE FROM content WHERE id='$pb' or id='$nb'");
  if(!$buf[$pb]["cpid"]) {
    if($buf[$nb]["cpid"]) {
      $buf[$pb]["cpid"]=$pb;
      $buf[$nb]["cpid"]=0;
    }
  }
  $buf[$pb]["id"]=$nb;
  $buf[$nb]["id"]=$pb;

  foreach($buf as $val) {
    $sql=array(array(),array());
    foreach($val as $k=>$v) if(is_string($k)) {
      $sql[0][]=$k;
      $sql[1][]="'$v'";
    }
    $db->query("INSERT INTO content (".join(",",$sql[0]).") VALUES (".join(",",$sql[1]).")");
  }
}

function user_function() {
global $id,$db, $_POST,$_GET,$FORM_EXTEND, $menu_items,$doubleDir, $_CONFIG, $_USERDIR,$ADMINGROUP,$TEXTS;

  if(isset($_CONFIG["MAINFRAME"]) && $_CONFIG["MAINFRAME"]) include "./inc/descriptor.php";

  if($doubleDir) echo '
    <SCRIPT LANGUAGE="JavaScript">
    <!--
      alert("'.$TEXTS["PageDublDirAlert"].'");
    //-->
    </SCRIPT>

    ';

  if(isset($_POST["attr"])) $db->query("UPDATE catalogue_fin SET attr='".$_POST["attr"]."' WHERE id='$id'");

  $db->query("SELECT tpl, id FROM catalogue WHERE id='$id'");
  if($db->next_record()) {
    $FORM_EXTEND="<SCRIPT LANGUAGE='JavaScript'>
    <!--
    function getPageId() {
      return ".$db->Record["id"].";
    }
    //-->
    </SCRIPT>";

    $tpl=$db->Record["tpl"];
    $cod=$db->Record["id"];
    $mtpl="";


	$db->query("SELECT id, tmpname, tmpimg, tmpschema,tmpfile FROM templates ORDER BY id");


	$i=0;$j=0;$tmpSchema=0;
    $CURR="";

    global $UserButtons;
    $UserButtons=array();

    $NOSELTPL="<div id='tplDiv' style='display:none'>";

    $FORM_EXTEND.="<SCRIPT LANGUAGE='JavaScript' src='./js/chTpl.js'></SCRIPT>%NOSELTPL%<hr><h2>Доступные шаблоны</h2><br><table border='1' cellpadding='10'>";
    while($db->next_record()) {


      $STR="";

      if(file_exists($db->Record["tmpimg"]) && $db->Record["tmpimg"]) {
        if(!$i && $tpl!=$db->Record["id"]) $STR.="<tr valign='top'>";
        $STR.="<div style='float:left;text-align:center;padding-right:20px;'><img src='".$db->Record["tmpimg"]."' id='tpl$j' alt='".$db->Record["tmpname"]."'         ".($tpl==$db->Record["id"] ? "style='border:2px solid red'":"style='cursor:hand;border:1px solid #000000'  onclick='chTpl(\"".$_GET["ch"]."\",\"".$db->Record["id"]."\")'")."'><br>".$db->Record["tmpname"]."</div> ";
        if($tpl==$db->Record["id"])  {
          $tmpSchema=$db->Record["tmpschema"];
          $mtpl=$db->Record["tmpfile"];
          $CURR="<td>".$STR."</td>";
        } else {
          $i++;$j++;
          $FORM_EXTEND.=$STR;
        }
      }
    }
    if($i<4) $FORM_EXTEND.="<td colspan='".(5-$i)."'>&nbsp;</td></tr>";
    $FORM_EXTEND.="</table>".($tmpSchema? "</div>":"")."<SCRIPT LANGUAGE='JavaScript'>tplCount=$j</SCRIPT><BR>";
    if($CURR) {
      $CURR="<table border=0><tr valign=top>$CURR<td>";
      $i=1;

      $db->query("SELECT id, title,text,spec, cpid, godef, globalblock, catalogue_id, ins2text FROM content WHERE catalogue_id='$cod' or globalblock!=0 ORDER BY id");
      $blocksRec=array();
      $blocksRecGlobal=array();
      while($db->next_record()) {
        if($db->Record["godef"] && !isset($_GET["nogo"])) {
          header("Location: ./page_content.php?ch=".$db->Record["id"]);
          exit();
        }
        if($db->Record["globalblock"] && $cod!=$db->Record["catalogue_id"]) {
          if(!isset($blocksRecGlobal[$db->Record["globalblock"]])) {
            $blocksRecGlobal[$db->Record["globalblock"]]=array();
          }
          $blocksRecGlobal[$db->Record["globalblock"]][]=$db->Record;
        } elseif($db->Record["cpid"]) {
          if(!isset($blocksRec[$db->Record["cpid"]])) $blocksRec[$db->Record["cpid"]]=array("subblock"=>array(),"rec"=>array());
          $blocksRec[$db->Record["cpid"]]["subblock"][]=$db->Record;
        } else {
          if(!isset($blocksRec[$db->Record["id"]])) $blocksRec[$db->Record["id"]]=array("subblock"=>array(),"rec"=>array());
          $blocksRec[$db->Record["id"]]["rec"]=$db->Record;
        }
      }

      $bloksArr=array();

      // Проверка соответствия фактического кол-ва блоков и шаблона
      $n=count($blocksRec);
      for($i=$n;$i<$tmpSchema;$i++) {
        $db->query("INSERT INTO content (catalogue_id) VALUES ('".$cod."')");
        $blocksRec[getLastID()]=array("subblock"=>array(),"rec"=>array());
      }

      $i=1;

      $jastReadSpec=array();

      $CURR.="%SAVETYPE%<br>";

      foreach($blocksRec as $key=>$val) if(isset($val["rec"]["id"])) {

        if(isset($_CONFIG["MAINFRAME"]) && $_CONFIG["MAINFRAME"] && isset($menu_items[$val["rec"]["spec"]][0]) && !in_array($val["rec"]["spec"],$jastReadSpec)) {
          $bloksArr[]=getStatusDescript($val["rec"]["spec"],array(),$cod);
          $jastReadSpec[]=$val["rec"]["spec"];
        }

        //if(!isset($ADMINGROUP) || (isset($ADMINGROUP["modadd"]) && in_array("page_content",$ADMINGROUP["modadd"])))
          $CURR.="<a href='?ch=$id&cod=$cod&addsb=".$val["rec"]["id"]."'><IMG SRC='./img/add.gif' WIDTH='11' HEIGHT='11' BORDER=0 ALT='Добавить подблок'></a>&nbsp;";
        //else
        //  $CURR.="<IMG SRC='./img/dot.gif' WIDTH='11' HEIGHT='11' BORDER=0 ALT=''>&nbsp;";

        $CURR.="<a href='./page_content.php?ch=".$val["rec"]["id"]."&num=$i' class='block' style='color:black'>";
        if($val["rec"]["title"]) {
          $CURR.="<b>".$TEXTS["PageBlock"]."$i</b> (".$val["rec"]["title"].")";
        } elseif(isset($menu_items[$val["rec"]["spec"]][0])) {
          $CURR.="<b>".$TEXTS["PageBlock"]."$i</b> (".$menu_items[$val["rec"]["spec"]][0].")";
        } elseif(!$val["rec"]["title"] && !$val["rec"]["text"]) {
          $CURR.="<span style='color:red'><b>".$TEXTS["PageBlock"]."$i</b> (".$TEXTS["PageEmptyBlock"].")</span>";
        } else $CURR.="<b>".$TEXTS["PageBlock"]."$i</b>";
        $CURR.="</a><BR>";
        $prev=$val["rec"]["id"];
        foreach($val["subblock"] as $v) {
          //$CURR.="&nbsp;&nbsp;&nbsp;&nbsp;";
          $CURR.="<a href='?ch=$id&pblk=$prev&nblk=".$v["id"]."'><IMG SRC='./img/upb.gif' WIDTH='11' HEIGHT='11' BORDER=0 ALT='".$TEXTS["PageBlockUp"]."'></a>&nbsp;";

          //if(!isset($ADMINGROUP) || (isset($ADMINGROUP["moddel"]) && in_array("page_content",$ADMINGROUP["moddel"])))
            $CURR.="<IMG SRC='./img/del.gif' WIDTH='11' HEIGHT='11' BORDER=0 ALT='".$TEXTS["PageBlockDel"]."' onclick='if(confirm(\"".$TEXTS["PageBlockDelAlert"]."\")) window.location=\"./page.php?ch=$id&dblk=".$v["id"]."\"' style='cursor:hand'>&nbsp;";
          //else
            //$CURR.="<IMG SRC='./img/dot.gif' WIDTH='11' HEIGHT='11' BORDER=0 ALT=''>&nbsp;";

          if($v["ins2text"]) $CURR.="<IMG SRC='./img/t.gif' WIDTH='11' HEIGHT='11' BORDER=0 ALT='блок, привязанный в редакторе'>&nbsp;";

          $CURR.="<a href='./page_content.php?ch=".$v["id"]."&num=$i' style='color:#000000'>";

          if(isset($_CONFIG["MAINFRAME"]) && $_CONFIG["MAINFRAME"] && isset($menu_items[$v["spec"]][0]) && !in_array($v["spec"],$jastReadSpec)) {
            $bloksArr[]=getStatusDescript($v["spec"],array(),$cod);
            $jastReadSpec[]=$v["spec"];
          }

          if($v["title"])
            $CURR.=$TEXTS["PageExBlock"]." (".$v["title"].")";
          elseif(isset($menu_items[$v["spec"]][0]))
            $CURR.=$TEXTS["PageExBlock"]." (".$menu_items[$v["spec"]][0].")";
          elseif(!$v["title"] && !$v["text"])
            $CURR.="<span style='color:red'>".$TEXTS["PageExBlock"]." (".$TEXTS["PageEmptyBlock"].")</span>";
          else $CURR.=$TEXTS["PageExBlock"];
          $CURR.="</a><BR>";
          $prev=$v["id"];
        }


        if(isset($blocksRecGlobal[$i])) {
          foreach($blocksRecGlobal[$i] as $v) {

            $CURR.="<img src='./img/g.gif' width=22 height=11 alt='' />&nbsp;<a href='./page_content.php?ch=".$v["id"]."&num=$i' style='color:#007700'>";

            if(isset($_CONFIG["MAINFRAME"]) && $_CONFIG["MAINFRAME"] && isset($menu_items[$v["spec"]][0]) && !in_array($v["spec"],$jastReadSpec)) {
              $bloksArr[]=getStatusDescript($v["spec"],array(),$cod);
              $jastReadSpec[]=$v["spec"];
            }

            if($v["title"])
              $CURR.=$TEXTS["PageGlobBlock"]." (".$v["title"].")";
            elseif(isset($menu_items[$v["spec"]][0]))
              $CURR.=$TEXTS["PageGlobBlock"]." (".$menu_items[$v["spec"]][0].")";
            elseif(!$v["title"] && !$v["text"])
              $CURR.="<span style='color:red'>".$TEXTS["PageGlobBlock"]." (".$TEXTS["PageEmptyBlock"].")</span>";
            else $CURR.=$TEXTS["PageGlobBlock"];
            $CURR.="</a><BR>";
          }
        }
        $i++;
      }
      $CURR.="</td></tr></table>";
      global $RES_PATH;

      $UserButtons[]=array($TEXTS["PageAllTpl"],"if(tplDiv.style.display==\"none\") tplDiv.style.display=\"\";else tplDiv.style.display=\"none\";",$RES_PATH."img/main/tpl.gif");
      $UserButtons[]=array($TEXTS["PageSaveType"],"SaveCurrentPageType(".$_GET["ch"].")",$RES_PATH."img/main/save_etalon.gif");
      $UserButtons[]=array($TEXTS["TreePopupPreview"],"ParentW.page_preview(".$_GET["ch"].")",$RES_PATH."img/preview.png");

      $FORM_EXTEND=str_replace("%NOSELTPL%",$NOSELTPL,$FORM_EXTEND);
      //$SAVETYPE=;
    } else {
      $FORM_EXTEND=str_replace("%NOSELTPL%","<b style='color:red'>".$TEXTS["PageNoSelTpl"]."</b><br><br>%SAVETYPE%",$FORM_EXTEND);

    }
    $SAVETYPE="";
    $db->query("SELECT id,ptname FROM savedpagetypes ORDER BY id");
    while($db->next_record()) {
        $SAVETYPE.="<option value='".$db->Record[0]."'>".$db->Record[1]."</option>";
    }

    if($SAVETYPE) $SAVETYPE=" <select onchange='SetNewPageType(this, ".$_GET["ch"].")'><option value=''>--== ".$TEXTS["PageSavedTypes"]." ==--</option>$SAVETYPE</select>";

    $FORM_EXTEND=str_replace("%SAVETYPE%",$SAVETYPE,"$CURR$FORM_EXTEND");

    $FORM_EXTEND.='
    <SCRIPT LANGUAGE="JavaScript">
    <!--

'.(isset($_POST["id"])? '

	ParentW.treeChangeAttr("'.$_POST["id"].'","'.$_POST["attr"].'");

':'').'

    LogLoadModule=true;
    window.status="pagecod:'.$cod.'";
    t=window.setTimeout("window.status=\'\'",100);
    window.status="moduls:'.((isset($bloksArr) && count($bloksArr))? str_replace("\n","",str_replace("\r","",join("|",$bloksArr))).'|':'').'mtpl~'.str_replace('..','',$mtpl).'~'.$_SERVER["SERVER_ADDR"].':'.$_SERVER["SERVER_PORT"].'/'.$cod.'|";
    t=window.setTimeout("window.status=\'\'",100);
    //-->
    </SCRIPT>
    ';
  }
}

//if(isset($F_ARRAY["separator_1"])) $F_ARRAY["separator_1"].="</div>";

//-----------------------------------------------------------------------------------------------------------------------------------------------------
require ("./output_interface.php");

?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function UserSendFunction() {
  var s=parent.frames["mainmenu"].GetSelectedCods();
  if(s!="") {
    if(ParentW.DLG.c('PageSvChangeOnSelected')) {
      s=document.forms[0].elements("id").value+","+s;
      document.forms[0].elements("id").value=s;
    }
  }
}

function SaveCurrentPageType(id) {
  var s=ParentW.DLG.p('PageInputTypeName');
  if(s!='' && s!=null ) {
    //document.getElementById('loadprocess').display='';
    s=ParentW.AJAX.get('savepagetype',{id:id,name:s});
    if(s=='saved') {
      ParentW.DLG.a("PageTypeSaved");
    }
  }
}
function SetNewPageType(elm,id) {
  var newtype=elm.value;

  if(newtype!='' && ParentW.DLG.c("PageChangeTypeQuest")) {
    newtype=ParentW.AJAX.get('savepagetype',{id:id,newtype:newtype});

    if(newtype=='changed') {
      window.location=window.location;
    }
  } else {
    elm.selectedIndex=0;
  }
}

//-->
</SCRIPT>
