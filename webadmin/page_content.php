<?
include "./autorisation.php";

$PROPERTIES=array(
"tbname"=>"content",
"pagetitle"=>"",
"alongpage"=>"y",
);

$F_ARRAY_PROPS=array(
"title"=>$TEXTS["PContPropTitle"],
"collapsed"=>1,
"items"=>array(
"nocash",
"access_",
"godef",
"globalblock",
"nohtml",
//"spec"
));

$PERSONALPAGE=1;
$id=0;
if(isset($_POST["id"])) {
	$db->query("SELECT spec FROM content WHERE id='".intval($_POST["id"])."'");
	if($db->next_record() && $_POST["spec"]!=$db->Record["spec"]) $PROPERTIES["refresh_win"]="parent.location";

	$_GET["ch"]=$_POST["id"];
}
if(isset($_GET["ch"])) $id=intval($_GET["ch"]);

$F_ARRAY=Array (
"id" => "hidden||",
//"block1"=>"block|noCash,access,noHTML,goDef,GlobalBlock,cmpW|".$TEXTS["PContProp"]."|1",
"nohtml" => "select||".$TEXTS["PContShow"]."|/".$TEXTS["PContShow1"]."|1/".$TEXTS["PContShow2"]."|2/".$TEXTS["PContShow3"]."|3/".$TEXTS["PContShow4"],
//"nohtml" => "text|size=20|".$TEXTS["PContShow"],

"godef" => "checkbox||".$TEXTS["PContOpen"]."|",
"globalblock" => "checkbox||".$TEXTS["PContGlob"]."|",
"nocash" => "checkbox||".$TEXTS["PContNoCash"]."|",
"spec" => "select||".$TEXTS["PContModule"]."|/".$TEXTS["PContNoModule"],
"catalogue_id"=>"hidden||",
"title" => "text|style='width:450px'|".$TEXTS["PContBlockName"],
"text" => "editor|width=450 height=300|".$TEXTS["PContText"],
);

$F_ARRAY_PANELS=array(
  $TEXTS["PContTuning"]=>array(
  "prop"=>array("hide"=>1,"txtStyle"=>"display:none"),
  "items"=>array("cmpw")
  ),
  array(
   "prop"=>array("txtStyle"=>"float:none"),
   "items"=>array("spec","title","text")
  )

);


include "./menu.inc";

if(isset($_GET["num"])) $_SESSION["ses_bnum"]=intval($_GET["num"]);
if(isset($_SESSION["ses_bnum"]) && isset($_POST["globalblock"])) {
  $_POST["globalblock"]=intval($_SESSION["ses_bnum"]);
}

$f_array=Array(
"id" => "",
"title" => ""
);

$spec="";
$block=0;
$log=0;


$db->query("SELECT content.catalogue_id, content.spec, content.cmpw,catalogue.id FROM content,catalogue WHERE content.id='$id' and content.catalogue_id=catalogue.id");
if($db->next_record()) {
  $UserButtons=array(

	  array($TEXTS["ToPageSettings"],"window.location='page.php?ch=".$db->Record["catalogue_id"]."'","ext/img/arrow-left.gif"),
	  array($TEXTS["TreePopupPreview"],"ParentW.page_preview(".$db->Record["id"].")","ext/img/preview.png")
  );

  $catalogueID=$db->Record["catalogue_id"];
  if(isset($_POST["spec"])) $db->Record["spec"]=$_POST["spec"];
  if($db->Record["spec"] && (file_exists("./extensions/".$db->Record["spec"].".php") || file_exists("../www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/".$db->Record["spec"].".php"))) {
    $spec=$db->Record["spec"];
    $_SESSION["sess_cmpW"]=$db->Record["cmpw"];
    $db->query("SELECT id FROM content WHERE catalogue_id='".$db->Record["catalogue_id"]."' ORDER BY id");
    while($db->next_record()) {
      $block++;
      if($id==$db->Record["id"]) {$log=1;break;}
    }
  } else {
    unset($F_ARRAY["nocash"]);
  }
}

// Выводим список остальных блоков
$db->query("SELECT id,title,spec FROM content WHERE catalogue_id='$catalogueID' ORDER BY id");
//$pagetitle.="<select onchange='window.location=\"?ch=\"+this.value'>";
while($db->next_record()) {
  if(!$db->Record["title"] && !$db->Record["spec"])  $db->Record["title"]="Empty";
  elseif(!$db->Record["title"] && $db->Record["spec"] && isset($menu_items[$db->Record["spec"]])) $db->Record["title"]=$menu_items[$db->Record["spec"]][0];

  //$pagetitle.="<option value='".$db->Record["id"]."' ".($_GET["ch"]==$db->Record["id"]? "selected":"").">".$db->Record["title"]."</option>";
}
//$pagetitle.="</select>";

$LogExt=1;
$specAr=array();
$db->query("SELECT spec,id FROM content WHERE spec!=''");
while($db->next_record()) {
  $specAr[$db->Record["spec"]]=1;
  if($db->Record["id"]==$id && isset($menu_items[$db->Record["spec"]])) {
    $F_ARRAY["spec"].="|".$db->Record["spec"]."/".$menu_items[$db->Record["spec"]][0];
    if(!$menu_items[$db->Record["spec"]][1]) $LogExt=0;
    unset($menu_items[$db->Record["spec"]]);
  }
}
$tbnames=$db->table_names();

foreach($menu_items as $k=>$v) {
  if(!isset($specAr[$k]) || isset($tbnames[$k."catalogue"]) || (isset($v[2]) && $v[2])) $F_ARRAY["spec"].="|$k/".$v[0];

}


if(isset($_POST["godef"]) && $_POST["godef"]) {
  $db->query("UPDATE content  SET godef='' WHERE catalogue_id='$catalogueID'");
}



if($spec && $block && $log) {
  $SPEC_BLOCK="";

  $file=($_USERDIR? "../www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/":"")."extensions/$spec.php";
  $fp=fopen($file,"r");
  $str=fread($fp,filesize($file));
  fclose($fp);

  $str=substr($str,strpos($str,"/*DESCRIPTOR")+12);
  $str=trim(substr($str,0,strpos($str,"*/")));
  if($str[0]=='1') {

  $TABPANEL=array(
   "activeTab"=>1,
   "items"=>array(
    array(
       "title"=>"ParentW.DLG.t('Block')",
       "contentEl"=>"'IntrContentDiv'",
       "autoScroll"=>true
    ),
    array(
       "title"=>"ParentW.DLG.t('Module')",
       "html"=>"\"<iframe src='readext2pg.php?blcod=".$_GET["ch"]."&".(isset($_GET["sch"])? "ch=".intval($_GET["sch"])."&":"")."ext=$spec&catalog=$catalogueID&block=$block' width='100%' height='100%' frameborder='0'></iframe>\"",
       "autoScroll"=>true
    )
   )
  );
  }

  unset($F_ARRAY["text"]);
  if($ACCESS<=0) {
    $F_ARRAY["cmpw"]="textarea|style='width:450px;height:150px;'|";
  }
  $SPEC_BLOCK.='
    <SCRIPT LANGUAGE="JavaScript">
    LogLoadModule=true;
    </SCRIPT>';
} else {
  unset($F_ARRAY_PANELS[$TEXTS["PContTuning"]]);
}


function user_function() {
global $id,$db, $pagetitle,$FORM_EXTEND,$ROWID,$F_ARRAY_PANELS;
  $db->query("SELECT catalogue.attr, catalogue.id, catalogue.title FROM content,catalogue WHERE content.id='$id' and catalogue.id=content.catalogue_id");
  if($db->next_record()) {
    $FORM_EXTEND="<SCRIPT LANGUAGE='JavaScript'>
    <!--
    function getPageId() {
      return ".$db->Record["id"].";
    }
	".(isset($_POST["upd"]) && $db->Record["attr"]<2? "ParentW.treeChangeAttr('".$db->Record["id"]."','1');":"")."
    //-->
    </SCRIPT>";
    //$pagetitle="<a href='./page.php?nogo=1&ch=".$db->Record["id"]."'>".$db->Record["title"]."</a> / $pagetitle";
    $ROWID=$db->Record["id"];
    $idd=$db->Record["id"];
    if(isset($_POST["upd"]) && $db->Record["attr"]<2) {
      $db->query("UPDATE catalogue SET attr='1' WHERE id='$idd'");
      $db->query("UPDATE catalogue_fin SET attr='1' WHERE id='$idd'");
    }
  }
  ?>
<SCRIPT LANGUAGE="JavaScript">
<!--
    <?if(isset($_GET["ch"])) {?>
      window.status="block:<?=$_GET["ch"]?>";
      t=window.setTimeout("window.status=''",100);
    <?
    $db->query("SELECT cpid, catalogue_id FROM content WHERE id='".intval($_GET["ch"])."'");
    if($db->next_record()) {
      if($db->Record[0]) $parentBlock=$db->Record[0];
      else $parentBlock=intval($_GET["ch"]);
    ?>
    //  parent.setParentBlock('<?=$parentBlock?>','<?=$db->Record[1]?>');
    <?}}?>
//-->
</SCRIPT>
  <?
  $F_ARRAY_PANELS[0]["html"]=$FORM_EXTEND;
  $FORM_EXTEND="";
}


//-----------------------------------------------------------------------------------------------------------------------------------------------------
require ("./output_interface.php");
?>