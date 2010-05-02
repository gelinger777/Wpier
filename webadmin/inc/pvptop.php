<?
include "./".$_CONFIG["ADMINDIR"]."/inc/pfunc.php";

$QUERY_STRING="";
$LANG="";

//$_SESSION["len"]=($LANG? $LANG:"rus");
$spec="";

$_SERVER["REQUEST_URI"]=addslashes($_SERVER["REQUEST_URI"]);
$REQUEST_URI=$_SERVER["REQUEST_URI"];
if (strpos($_SERVER["REQUEST_URI"],"?")) {
  $QUERY_STRING=substr($_SERVER["REQUEST_URI"],(strpos($_SERVER["REQUEST_URI"],"?")+1));
  $_SERVER["REQUEST_URI"]=str_replace("?$QUERY_STRING","",$_SERVER["REQUEST_URI"]);
  parse_str($QUERY_STRING,$_GET);
}

if(isset($_GET["search"])) {
  $_SESSION["ses_search"]=$_GET["search"];
}
if($_CONFIG["SESSION_POST"] && isset($_SESSION["POST"])) {
  $db->query("SELECT pdata, sid FROM postdata WHERE sid='".session_id()."'");
  if($db->next_record()) {
    $_POST=unserialize(stripslashes($db->Record[0]));  
  }
  $HTTP_POST_FILES=$_SESSION["HTTPPOST_FILES"];
}

if(isset($_GET["prev"])) {
  $_SESSION["ses_preview"]=(intval($_GET["prev"])<3? "":"1");
}
$FinSuf="_fin";
if(isset($_SESSION["ses_preview"]) && $_SESSION["ses_preview"] && !isset($_GET["show_public_page"])) $FinSuf="";

$uri_arr=explode("/",$_SERVER["REQUEST_URI"]);
if($uri_arr[0]=="http:" || $uri_arr[0]=="https:") {
  unset($uri_arr[0]);
  unset($uri_arr[1]);
  unset($uri_arr[2]);
  $x=array("","");$i=0;
  foreach($uri_arr as $v) $x[$i++]=$v;
  $uri_arr=$x;  
} 



if(isset($_CONFIG["LANG_IN_DIR"]) && $_CONFIG["LANG_IN_DIR"] && $uri_arr[1]) {
  if(isset($lenguagesList[$uri_arr[1]])) {
    $LANG=$uri_arr[1];
    $x=array();
    foreach($uri_arr as $k=>$v) if($k!=1) $x[]=$v;
    $uri_arr=$x;
  }
} elseif(isset($_SERVER["HTTP_HOST"]) && isset($lenguagesDomain[$_SERVER["HTTP_HOST"]])) {
  $LANG=$lenguagesDomain[$_SERVER["HTTP_HOST"]];
}


if(!$uri_arr[0] && !$uri_arr[1]) {
  $db->query("SELECT dir FROM catalogue_fin WHERE pid=0 or pid is NULL ORDER BY indx LIMIT 1");
  if($db->next_record()) {
    $uri_arr[1]=$db->Record["dir"];
  }
}

$sql=array();$i=0;
foreach($uri_arr as $v) {
  $sql[$i++]=$v;
}
$uri_arr=$sql;

$sql="";$curdir="";$HTML_FILE="";

$RootDir="";
$RootTitle="";
foreach($uri_arr as $k=>$v) if($k && $v) {

  if(!$RootDir) $RootDir=$v;
  if(strpos($v,".")) {
    unset($uri_arr[$k]);
    $HTML_FILE=$v;
    $v=substr($HTML_FILE,(strrpos($HTML_FILE,".")+1));
    if($k==1 && ($v=="html" || $v=="htm") && intval($HTML_FILE)) {  
      if(isset($_CONFIG["COOKIE_ONLY"]) && $_CONFIG["COOKIE_ONLY"]) include $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/inc/session2cookie.php";    
      mkPathFromCod($HTML_FILE);
    }
  } else {
    if($sql) $sql.=" or ";
    $sql.="dir='$v'";
    $curdir=$v;
  }
} elseif(!$v) unset($uri_arr[$k]);
?>