<?
if(!isset($db)) include_once "./autorisation.php";

if(isset($_GET["_copy_from_buffer_to"])) {
  $_POST["id"]=intval($_GET["_copy_from_buffer_to"]);
}

if(isset($_CONFIG["DISTANCE_MODE"]) && $_CONFIG["DISTANCE_MODE"]==1 && count($_GET) && !count($_POST)) {
  $noActivGet=array("ext","ch","new","start","catalog","block");
  $_get=$_GET;
  foreach($_get as $k=>$v) if(in_array($k,$noActivGet)) unset($_get[$k]);
  if(count($_get) || (isset($_CONFIG["DISTANCE_EXT"]) && isset($EXT) && in_array($EXT,$_CONFIG["DISTANCE_EXT"]))) include $_SERVER['DOCUMENT_ROOT']."/".$_CONFIG["ADMINDIR"]."/inc/get2get.php";
}

$PROPERTIES["formCss"]="";
$PERMIS=array(1,1,1,1);

function NoAccess() {
  include 'noaccesspage.php';
  exit;
}

if(isset($EXT)) {
  // Проверяем права доступа
  $db->query("SELECT  rd, ad, ed, dl, grp  FROM accessmodadmins WHERE mdl='$EXT'");
  if($db->num_rows()>0) {
    $PERMIS=array();
  }
  while($db->next_record()) {
    if($ADMINGROUP==$db->Record["grp"]) {
      $PERMIS=array($db->Record[0],$db->Record[1],$db->Record[2],$db->Record[3]);
      break;
    }
  }
  if(!count($PERMIS)) NoAccess();
  if((isset($_GET["ch"]) || isset($_GET["del"])) && !$PERMIS[2]) NoAccess();
  if(isset($_GET["new"]) && !$PERMIS[1]) NoAccess();

  if($_USERDIR && file_exists("../www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/$EXT.php")) {
                include "../www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/$EXT.php";
                $PROPERTIES["formCss"]="/www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/$EXT.css";
  } else {
    include "./extensions/$EXT.php";
    $PROPERTIES["formCss"]="/".$_CONFIG["ADMINDIR"]."/extensions/$EXT.css";
  }
} else {
    $EXT=substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
    $EXT=substr($EXT,0,strpos($EXT,"."));
}

// Сохраним группировки фильтров
if(isset($PROPERTIES["grop2filters"]) && $PROPERTIES["grop2filters"]) {

  if($PERMIS[2] && isset($_POST["inouter_filter_group_ids"]) && ereg("^[0-9,]{1,}",$_POST["inouter_filter_group_ids"]) &&
  isset($_POST["inouter_filter_group_name"])) {

    if(isset($PROPERTIES["FIX_ID_TO_COD"]) && $PROPERTIES["FIX_ID_TO_COD"]) {
      $db->query("SELECT ".$PROPERTIES["FIX_ID_TO_COD"]." FROM ".$PROPERTIES["tbname"]." WHERE id in (".$_POST["inouter_filter_group_ids"].")");
      $ids=array();
      while($db->next_record()) $ids[]=$db->Record[0];
      $ids=join(",",$ids);
    } else $ids=$_POST["inouter_filter_group_ids"];

    $db->query("INSERT INTO gridfiltersgroups (tab,cods,name) VALUES ('".$PROPERTIES["tbname"]."','".$ids."','".addslashes($_POST["inouter_filter_group_name"])."')");

    echo getLastID();
    exit;

  } elseif($PERMIS[2] && isset($_POST["inouter_filter_group_edit"]) && isset($_POST["inouter_filter_group_id"])) {
    $db->query("UPDATE gridfiltersgroups SET name='".addslashes($_POST["inouter_filter_group_edit"])."' WHERE id='".intval($_POST["inouter_filter_group_id"])."' and tab='".$PROPERTIES["tbname"]."'");
    echo "ok";
    exit;
  }elseif($PERMIS[3] && isset($_POST["inouter_filter_group_del"]) ) {
    $db->query("DELETE FROM gridfiltersgroups WHERE id='".intval($_POST["inouter_filter_group_del"])."' and tab='".$PROPERTIES["tbname"]."'");
    echo "ok";
    exit;
  }
}

if(isset($PROPERTIES["FIX_ID_TO_COD"]) && $PROPERTIES["FIX_ID_TO_COD"] && isset($_POST[$PROPERTIES["FIX_ID_TO_COD"]]) && isset($_POST["upd"])) $_POST[$PROPERTIES["FIX_ID_TO_COD"]]=$_POST["id"];

if(!isset($PROPERTIES["warning"]) && strpos($PROPERTIES["pagetitle"],"<")) {
	$PROPERTIES["warning"]=(substr($PROPERTIES["pagetitle"],strpos($PROPERTIES["pagetitle"],"<")));
	$PROPERTIES["pagetitle"]=substr($PROPERTIES["pagetitle"],0,strpos($PROPERTIES["pagetitle"],"<"));
}

if(isset($PROPERTIES["parentcode"])) {
   if(isset($_GET["inouter_parent_code"])) {
     $_GET["inouter_parent_code"]=intval($_GET["inouter_parent_code"]);
     $PROPERTIES["WHERE"]=((isset($PROPERTIES["WHERE"]) && $PROPERTIES["WHERE"])? $PROPERTIES["WHERE"]." and ":"WHERE ").$PROPERTIES["tbname"].".".$PROPERTIES["parentcode"]."='".$_GET["inouter_parent_code"]."'";
     $_POST[$PROPERTIES["parentcode"]]=$_GET["inouter_parent_code"];
   }
}

//print_r($_POST);exit;

if(isset($_GET["blcod"]) && $_GET["blcod"]) {
  $db->query("SELECT cmpW FROM content WHERE id='".intval($_GET["blcod"])."'");
  if($db->next_record() && $db->Record[0]) {
    eval(str_replace('&quot;','"',$db->Record[0]));
  }
}

if(!isset($PROPERTIES["spell"])) $PROPERTIES["spell"]=0;

if (function_exists("user_first_function")) user_first_function();

//if(isset($PROPERTIES)) foreach($PROPERTIES as $k=>$v) $$k=$v;
if(isset($PROPERTIES["tbname"])) $tbname=$PROPERTIES["tbname"];

include "./content_functions.php";

if(!isset($PROPERTIES["nolang"]) && function_exists("mkLangArrays")) {
  //$fOUT=
  mkLangArrays($F_ARRAY,$f_array,$LENGUAGE);
 // $F_ARRAY=$fOUT[0];
 // $f_array=$fOUT[1];
}

$F_ARRAY_LOCATION=array();
if(file_exists("location/".$_CONFIG["ADMIN_LOCATION"]."/extensions/".$EXT.".php")) {
	include "location/".$_CONFIG["ADMIN_LOCATION"]."/extensions/".$EXT.".php";
}

//$fOUT=$F_ARRAY;
//$F_ARRAY=mk1Lev($F_ARRAY);

include "./inc/mkObjects.php";

if(!isset($PROPERTIES["nochecktables"]) && isset($PROPERTIES["tbname"])) checkDbTable($PROPERTIES["tbname"]);

if(isset($PROPERTIES["conect"]) && isset($_GET["catalog"])) $CataloguePgID=intval($_GET["catalog"]);

if(isset($CataloguePgID) && $CataloguePgID && isset($PROPERTIES["conect"])) {
  $db->query("SELECT id FROM ".$tbname."catalogue,$tbname WHERE ".$tbname."catalogue.rowidd=$tbname.".$PROPERTIES["conect"]." and ".$tbname."catalogue.pgID='$CataloguePgID' and pgPID>0 LIMIT 1");
  if($db->next_record()) {
    $_GET["ch"]=$db->Record["id"];
    $alongpage=1;
  }
}

// Импорт данных из CSV
if(isset($PROPERTIES["importcsv"]) && isset($HTTP_POST_FILES["upload_importcsvfile"])) {
  importCsvData($PROPERTIES["tbname"],$PROPERTIES["importcsv"],$HTTP_POST_FILES["upload_importcsvfile"]["tmp_name"],$_POST["upload_importcsv_mode"]);
}

// Подписываем документ и передаем дальше по списку
if(isset($PROPERTIES["publication"]) && $PROPERTIES["publication"] && isset($_GET["ch"]) && isset($_GET["sign"])) {
  $id=0;
  $db->query("SELECT publposlmem.memb FROM publposl,publposlmem WHERE publposl.modname='$EXT' and publposl.id=publposlmem.cod ORDER BY publposlmem.id");
  while($db->next_record()) {
    if($ADMIN_ID==$db->Record[0]) $id=-1;
    elseif($id==-1) $id=$db->Record[0];
  }
  $db->query("UPDATE ".$PROPERTIES["tbname"]." SET LastPublAdmin='".($id>0? $id:0)."' WHERE id='".intval($_GET["ch"])."' and LastPublAdmin='".$ADMIN_ID."'");
  if($id>0)
    $db->query("INSERT INTO publicationstatus (admin,mod,idr,tab) VALUES ('$id','$EXT','".intval($_GET["ch"])."','".$PROPERTIES["tbname"]."')");
  $db->query("DELETE FROM publicationstatus WHERE idr='".intval($_GET["ch"])."' and mod='$EXT' and admin='$ADMIN_ID'");
}

onBeforeDbChanging();


//print_r($_POST);exit;

if($LOGPOST) {
  if(isset($_GET["ch"]) && isset($_GET["delimg"])) delete_img(intval($_GET["ch"]), htmlspecialchars($_GET["delimg"]),1);
  if(isset($_GET["ch"]) && isset($_GET["deletefile"]) && isset($_GET["dfn"])) delete_img(intval($_GET["ch"]), htmlspecialchars($_GET["dfn"]),0,htmlspecialchars($_GET["deletefile"]));
  if (isset($_GET["id"]) && isset($_GET["idprev"]))
  if ($_GET["id"] && $_GET["idprev"] && !isset($_GET["ch"]) && !isset($_GET["new"])) {
    if (function_exists("user_updown")) user_updown($tbname,intval($_GET["id"]),intval($_GET["idprev"]));
    elseif(isset($_GET["aft"])) updown($tbname,intval($_GET["id"]),intval($_GET["idprev"]),intval($_GET["aft"]));
    else updown($tbname,intval($_GET["id"]),intval($_GET["idprev"]));
    unset($_GET["idprev"]);
  }
  if (isset($_GET["ch"])) $id=$_GET["ch"]; else $id="";

 // if(isset($ADMINGROUP) && (!isset($ADMINGROUP["modedit"]) || !in_array($EXT,$ADMINGROUP["modedit"]))) {}
  if (isset($_POST["upd"]) && $PERMIS[2]) $id=update_row($tbname);

 // if(isset($ADMINGROUP) && (!isset($ADMINGROUP["modadd"]) || !in_array($EXT,$ADMINGROUP["modadd"]))) {}
  if (isset($_POST["ins"]) && $PERMIS[1]) {
    $id=insert_row($tbname);
    if(isset($PROPERTIES["FIX_ID_TO_COD"])) {
                  $db->query("UPDATE $tbname SET ".$PROPERTIES["FIX_ID_TO_COD"]."='$id' WHERE id='$id'");
    }
  }

//  if(isset($ADMINGROUP) && (!isset($ADMINGROUP["moddel"]) || !in_array($EXT,$ADMINGROUP["moddel"]))) {$NODEL=1;}
  //else
  if ($PERMIS[3] && isset($_GET["del"])) delete_row($tbname, "id");

  if (!isset($id)) $id="";
  if (!isset($start)) $start=0;

  onAfterDbChanging();

  if(isset($_GET["copy_from_buffer"])) {
	  $_POST["id"]="";
  }

  if (function_exists("user_function")) user_function();

  if(isset($_CONFIG["COOKIE_ONLY"]) && $_CONFIG["COOKIE_ONLY"]) include $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/inc/session2cookie.php";

  // При сохранении остаемся на тойже странице
  if((isset($_POST["ins"]) || isset($_POST["upd"])) && isset($_CONFIG["STAY_AFTER_SAVE"]) && $_CONFIG["STAY_AFTER_SAVE"]) $_GET["ch"]=$id;

  if(isset($PROPERTIES["alongconect"]) && $PROPERTIES["alongconect"]  && isset($PROPERTIES["conect"]) && isset($CataloguePgID)) {

    $db->query("SELECT rowidd FROM ".$PROPERTIES["tbname"]."catalogue WHERE pgid='".$CataloguePgID."'");
     if($db->next_record()) {
      if($PROPERTIES["conect"]=="id") {
        $_GET["ch"]=$db->Record[0];
        $id=$db->Record[0];
      } else {
        $db->query("SELECT id FROM ".$PROPERTIES["tbname"]." WHERE ".$PROPERTIES["conect"]."='".$db->Record[0]."'");
        if($db->next_record()) {
          $_GET["ch"]=$db->Record[0];
          $id=$db->Record[0];
        } else {
	  $db->query("DELETE FROM ".$PROPERTIES["tbname"]."catalogue WHERE pgid='".$CataloguePgID."'");
	}
      }
    }
    else $_GET["new"]="";
    $alongpage=1;

  }

} else {?>
<SCRIPT LANGUAGE="JavaScript">
<!--
alert("Потеряно соединение с удаленным сервером! Сохранение не возможно!");
//-->
</SCRIPT>
<?}



if($_CONFIG["DISTANCE_MODE"]==2) {
  if(isset($EXT) && isset($_CONFIG["DISTANCE_EXT"]) && in_array($EXT,$_CONFIG["DISTANCE_EXT"])) {}
    else {
    echo "<***ENDTAG***>";
    exit;
  }
}

ob_start();
//if(isset($_GET["prn"]) && $_GET["prn"]=="yes") {include ("./AdminConsoleHeaderPRN.php");}
//else {include ("./AdminConsoleHeader.php");}

if(isset($EXT) && $EXT) {
  include_once "./inc/descriptor.php";
  if(isset($CataloguePgID) && $CataloguePgID) $s=$CataloguePgID;
  else $s=0;
  $s=getStatusDescript($EXT,$PROPERTIES,$s);
?>
<SCRIPT LANGUAGE="JavaScript">
    LogLoadModule=true;
    <?if(isset($_SESSION["ses_mode"]) && $_SESSION["ses_mode"]==1) {
                   if(isset($PROPERTIES["tbname"]) && $PROPERTIES["tbname"]) echo "window.status='cmd:mod:$EXT';";
                   else "window.status='cmd:nomod';";
                 } else echo "window.status='moduls:$s|';";
                 ?>
    t=window.setTimeout("window.status=''",100);
</SCRIPT>
<?}

if(isset($_GET["prn"]) && $_GET["prn"]=="yes") {}
else {


 /* if(count($lenguagesList)>1 && !isset($PROPERTIES["nolang"])) {
    echo "<td align='right'><table cellspacing=2><tr>";
    foreach($lenguagesList as $k=>$v) {
      if($k==$LENGUAGE) echo "<td class='lenguagesel' align='center'><b>$v</b></td>";
      else echo "<td class='lenguagetd' align='center'><a href='?".EchoGetStr("len",$k)."' onclick='' class='lenguagelink'>$v</td>";
    }
    echo "</tr></table></td>";
  }
  echo "</tr></table>";

  if(isset($GLOBALLINKS)) echo $GLOBALLINKS;
  */
}



if(isset($_POST["ins"]) || isset($_POST["upd"])) {
	if(!isset($PROPERTIES["formopenmode"]) || $PROPERTIES["formopenmode"]=='tab') {

		if(isset($_CONFIG["SAVEANDCLOSE"]) && $_CONFIG["SAVEANDCLOSE"]) {?>

		<script type="text/javascript" src="<?=$RES_PATH?>getparent.js"></script>
		<script type="text/javascript">parent.ParentW.CloseCurrTab(true);</script>
		<?}?>
 		<script type="text/javascript">
		<?if(isset($PROPERTIES["refresh_win"])) {echo "parent.location=".$PROPERTIES["refresh_win"].";</script>";exit;}?>
		parent._LOAD_MASK.hide();
		<?if(isset($_POST["ins"])) {?>
		parent.document.getElementById("wpier_action_fold").name="upd";
		parent.document.getElementById("id").value='<?=$id?>';
		var p=parent.Ext.getCmp('wpier_tabpanel');
		if(p!=null) {
			for(var i=1;i<p.items.length;i++) {
				p.items.items[i].enable();
			}
		}
		<?}?></script><?
	}
	exit;
}


if (isset($_GET["start"])) $start=$_GET["start"]; else $start="0";

if ((isset($_GET["new"]) or isset($_GET["ch"])) and !$error) {

  include "./inc/mkform.php";

  // ФОРМИРУЕМ ФОРМУ
  $FormHiddens="";
  $SIGNATURE=0;
  $PANELS=array();
  if (isset($tbname)) $form=make_form($id);

  //print_r($form);             exit;

  //if(!isset($F_ARRAY_PANELS)) $F_ARRAY_PANELS=array();

  // Часть полей выносим в свойства
  $PropsList=array();
  if(isset($F_ARRAY_PROPS)) {
    foreach($F_ARRAY_PROPS["items"] as $k) if(isset($form[$k])) {
      $PropsList[$k]=array($form[$k][0][0],$form[$k][1]);
      unset($form[$k]);
    }
  }



  $MenuButtons=array();
  if(isset($_GET["prn"]) && $_GET["prn"]=="yes") {}
  elseif(isset($TEXTS)) {

    if(isset($PROPERTIES["conect"]) && $id) mkConnectTable($id);

    if(isset($_GET["ch"]) && !$_GET["ch"]) unset($_GET["ch"]);
    if(!isset($RES_PATH)) $RES_PATH="";
    if($SPELL=="1") {
      $MenuButtons[]=array("savebutton",$TEXTS["Save"]." (Ctrl-S)",$TEXTS["Save"],"if(sendform('MainEditForm')) document.getElementById('MainEditForm').submit();",$RES_PATH."img/main/save.gif","","submit");
      $MenuButtons[]="-";
      //$MenuButtons[]=array("checkbutton",$TEXTS["CheckOrfo"],"","spellX=0;checkSpell()","img/main/orfo.gif");
    } elseif($SPELL=="2") {
      $MenuButtons[]=array("savebutton",$TEXTS["Save"]." (Ctrl-S)",$TEXTS["Save"],"spellX=1;checkSpell();",$RES_PATH."img/main/save.gif");
      $MenuButtons[]="-";
    } else {
      $MenuButtons[]=array("savebutton",$TEXTS["Save"]." (Ctrl-S)",$TEXTS["Save"],"if(sendform('MainEditForm')) document.getElementById('MainEditForm').submit();",$RES_PATH."img/main/save.gif","","submit");
      $MenuButtons[]="-";
    }

    $MenuButtons[]=array("copybutton",$TEXTS["CopyForm"]." (Ctrl-Alt-C)","","CopyForm(\"$EXT\")",$RES_PATH."img/main/copy.gif",1);

    $MenuButtons[]=array("pastebutton",$TEXTS["PasteForm"]." (Ctrl-Alt-V)","","PasteForm(\"$EXT\")",$RES_PATH."img/main/paste.gif",1);
    $MenuButtons[]="-";
    if(isset($PROPERTIES["standard"]) && $PROPERTIES["standard"]=="yes") {
      $MenuButtons[]=array("saveetalonebutton",$TEXTS["SaveStandard"],"","SaveStandard()",$RES_PATH."img/main/save_etalon.gif");
      $MenuButtons[]="-";
    }

    /*if(isset($PROPERTIES["print"]) && isset($_GET["ch"])) $MenuButtons.="<a id='positionDiv".($POSITION_DIV_NUMB++)."' class='mbut_a' onmouseover='this.className=\"mbut_a_over\"' onmouseout='this.className=\"mbut_a\"'><INPUT type='button' onclick='window.open(\"?".EchoGetStr("prn","yes")."\")' value='' title='".$TEXTS["Print"]."' class='mbut' id='printbutton'></a>";  */

    // Если есть пользовательские кнопки
    if(isset($UserButtons)) {
      foreach($UserButtons as $v) if(is_array($v)) {
        $MenuButtons[]=array("",$v[0],(isset($v[4])? $v[4]:""),$v[1],$v[2],(isset($v[3])? $v[3]:""));
      } else $MenuButtons[]=$v;
      $MenuButtons[]="-";
    }
    // Проверяем, нужна ли подпись
    if($SIGNATURE) {
      $MenuButtons[]=array("signbutton",$TEXTS["Signature"],"","MakeSignature(\"$EXT\",\"$SIGNATURE\")",$RES_PATH."img/main/signat.gif");
      $MenuButtons[]="-";
    }
    // К проверки подписи
    if(!isset($PROPERTIES["formopenmode"]) || $PROPERTIES["formopenmode"]=="tab") $MenuButtons[]=array("closebutton","",$TEXTS["Close"],"ParentW.CloseCurrTab()",$RES_PATH."img/main/close.gif");
  }

  /*  echo "<table border='0' width='100%' height='100%'><tr><td>";

        echo "<FORM ENCTYPE='multipart/form-data' name='MainEditForm' action='?".EchoGetStr((isset($_GET["new"])? "new":"ch"),"")."' METHOD=POST  onsubmit='".((isset($_SESSION["ses_mode"]) && $_SESSION["ses_mode"]==1)? "if(CheckLocalFiles(this,\"".$EXT."\")) return false;":"")." return ".
  ((isset($ADMINGROUP) && (!isset($ADMINGROUP["modedit"]) || !in_array($EXT,$ADMINGROUP["modedit"])) && (!isset($ADMINGROUP["modadd"]) || !in_array($EXT,$ADMINGROUP["modadd"])))? "false":"sendform(\"MainEditForm\");")."' style='padding:0;margin:0;'>";

        if(isset($PAGECONTROL)) {
          include "./inc/pagecontrol.php";
          echo "<div id='MainModulDiv0' class='MainModulDivClass' style='padding:10px'>".$MenuButtons.$form."</div><INPUT type='hidden' ".($id? "name='upd'":"name='ins'")." value='y'>$FormHiddens</FORM>";
        } else {
          echo $MenuButtons."<div id='MainModulDiv0' class='MainModulDivClass' style='padding:0px'>".$form."</div><INPUT type='hidden' ".($id? "name='upd'":"name='ins'")." value='y'>$FormHiddens</FORM>";
        }


  */

  if(isset($PAGECONTROL) && !isset($_GET["copy_from_buffer"])) {
    $TABPANEL=array("activeTab"=>0,"items"=>array(),"tabPosition"=>"top");

    $TABPANEL["items"][]=array(
      "title"=>"'".$PAGECONTROL[0]."'",
      "contentEl"=>"'IntrContentDiv'",
      "autoScroll"=>true

    );
    if(isset($_GET["ch"])) {
      $cod=intval($_GET["ch"]);
	  if(isset($PROPERTIES["FIX_ID_TO_COD"])) {
		 $db->query("SELECT ".$PROPERTIES["FIX_ID_TO_COD"]." FROM ".$PROPERTIES["tbname"]." WHERE id='".$cod."'");
		 if($db->next_record()) $cod=$db->Record[0];
	  }
    } else $cod="";
    foreach($PAGECONTROL as $k=>$v) if(is_string($k)) {
	  $TABPANEL["items"][]=array(
        "title"=>"'$k'",
        "disabled"=>(isset($_GET["ch"])? "false":"true"),
        "html"=>"\"<iframe src='about:blank' width='100%' height='100%' scrolling='no' id='PGCONRTRL$v' frameborder='0'></iframe>\"",
        "autoScroll"=>true,
        "listeners"=>"{show:function(e) {if(document.getElementById('PGCONRTRL$v').src=='about:blank') document.getElementById('PGCONRTRL$v').src='?ext=$v&inouter_parent_code=".($cod? "$cod'":"'+document.getElementById('id').value").";}}"
      );
    }
  }

  include "showform.php";

  /*if(isset($ADMINGROUP)) {
         if(
          (isset($ADMINGROUP["modedit"]) && in_array($EXT,$ADMINGROUP["modedit"])) ||
          (isset($_GET["new"]) && isset($ADMINGROUP["modadd"]) && in_array($EXT,$ADMINGROUP["modadd"]))
          ) {} else {
    echo "<script src='/webadmin/js/disabledform.js'></script>";
    }
  }*/
} elseif(!isset($alongpage)) {
  // Выводим таблицу модуля

  include "showgrid.php";
  exit;
}

if(isset($PROPERTIES["standard"]) && $PROPERTIES["standard"]=="yes") include "./inc/standard.php";

onScriptEnd();

if (function_exists("last_function")) last_function();

if(isset($PROPERTIES["SaveBlock"]) && isset($CataloguePgID) && $CataloguePgID)
   include "./inc/saveblockhtml.php";

//if(isset($_GET["prn"]) && $_GET["prn"]=="yes") {include ("./AdminConsoleFooterPRN.php");}
//else {include ("./AdminConsoleFooter.php");}
ob_end_flush();

