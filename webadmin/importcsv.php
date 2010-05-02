<?
include_once "./autorisation.php";

//ReadNoEdiTable();

if(!file_exists($_SERVER["DOCUMENT_ROOT"].$_GET["f"])) exit;

// Если файл есть, проверим наш-ли он и можно, ли нам смотреть этот модуль
$fn=explode("/",$_GET["f"]);
$fn=$fn[count($fn)-1];
$usr=substr($fn,0,strpos($fn,"_"));
if($usr!=$ADMIN_ID) exit;

$fp=fopen($_SERVER["DOCUMENT_ROOT"].$_GET["f"],"r");
$s=fread($fp,filesize($_SERVER["DOCUMENT_ROOT"].$_GET["f"]));
fclose($fp);

$IMPCNF=unserialize($s);

if(isset($IMPCNF["php"])) {
  eval($IMPCNF["php"]);
}

$mult=array();

function prep($k,$v,$id) {
 global $IMPCNF;
 if(isset($IMPCNF["dics"][$k]) && isset($IMPCNF["dics"][$k][$v]))
  return $IMPCNF["dics"][$k][$v];
 if(in_array($k,$IMPCNF["unitime"])) return date("d.m.Y H:i:s",$v);
 if(in_array($k,$IMPCNF["date"])) return substr($v,6,2).".".substr($v,4,2).'.'.substr($v,0,4);

 if(isset($IMPCNF["editlist"][$k])) {
  global $db;
  $a=array();
  $db->query("SELECT * FROM ".$IMPCNF["editlist"][$k]["distTable"]." WHERE ".$IMPCNF["editlist"][$k]["distID"]."='".$id."' ORDER BY id");

  while($db->next_record()) {
   $aa=array();
   foreach($db->Record as $key=>$val) if(is_string($key) && $key!="id" && $key!=$IMPCNF["editlist"][$k]["distID"]) {
    $aa[]=$val;
   }
   $a[]=join(" ",$aa);
  }
  return join(";",$a);
 }

 if(isset($IMPCNF["multi"][$k])) {
  // Прочитаем все данные
  if(!$mult[$k]) {
   global $db,$recs;
   $t=$IMPCNF["multi"][$k];
   $db->query("SELECT ".$t["linktable"].".".$t["linkvalue"].",".$t["valtable"].".".$t["valvalue"]." FROM ".$t["linktable"].",".$t["valtable"]." WHERE ".$t["linktable"].".".$t["linkvalue"]." in (".kjoin(",",$recs).") and ".$t["linktable"].".".$t["linkkey"]."=".$t["valtable"].".".$t["valkey"]);
   while($db->next_record()) $mult[$k][$db->Record[0]][]=$db->Record[1];
  }
  if(isset($mult[$k][$id])) return join(", ",$mult[$k][$id]);
 }
 return $v;
}

function dbr($s) {
 $s=str_replace("\n"," ",str_replace("\r","",str_replace(";",",",strip_tags($s))));
 // Для номеров кредиток выделяем по 4 разряда
 /*if(ereg("[0-9]{16}",$s)) {
   $i=0;
   $s1=array();
   while($i<strlen($s)) {$s1[]=substr($s,$i,4);$i+=4;}
   return join("-",$s1);
 }
 // Для номеров форматируем строку (обрабатываются номена вида 89091112233 -> 8 909 111 2233)
 if(ereg("[0-9]{11}",$s)) {
   return substr($s,0,1)." ".substr($s,1,3)." ".substr($s,4,3)." ".substr($s,7);
 }*/
 return "	".$s;
}

if(isset($_POST["start"])) {

 $ext=substr($fn,strrpos($fn,"."));

 $_POST["start"]=intval($_POST["start"]);

 if(!$_POST["start"]) {
  $fp=fopen($_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["TEMP_DIR"]."/imp_".$fn,"w+");
  if($ext==".xml") fwrite($fp,"<?xml version=\"1.0\" encoding=\"Windows-1251\"?"."><items>");
  else {
   $s="";
   foreach($IMPCNF["columns"] as $k=>$v) {
    if($ext=='.csv') $s.=$v.";";
    else $s.='"'.$v.'"'."\t";
   }
   fwrite($fp,$s."\r\n");
 }
} else $fp=fopen($_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["TEMP_DIR"]."/imp_".$fn,"a+");
 $db->query($IMPCNF["sql"]." LIMIT ".$_POST["start"].",50");
 //echo "LQ=".$db->LastQuery;
 if(!$db->num_rows()) {
  echo "/".$_CONFIG["TEMP_DIR"]."/imp_".$fn;
  if($ext==".xml") fwrite($fp,"</items>");
  fclose($fp);
  unlink($_SERVER["DOCUMENT_ROOT"].$_GET["f"]);
  exit;
 }

//$fp=fopen($_SERVER["DOCUMENT_ROOT"].$_GET["f"],"r");

 $recs=array();
 while($db->next_record()) {
  $recs[$db->Record[$IMPCNF["id"]]]=$db->Record;
 }

 foreach($recs as $key=>$val) {
  $s="";
  if($ext=='.xml') {
   $s.="<item>";
   foreach($IMPCNF["columns"] as $k=>$v) {
    $s.="<$k>".dbr(prep($k,$val[$k],$key))."</$k>";
   }
   $s.="</item>";
  } else {
   foreach($IMPCNF["columns"] as $k=>$v) {
    $val[$k]=html_entity_decode($val[$k]);
    if($ext=='.csv') {

      $s.=dbr(prep($k,$val[$k],$key)).";";
    }
    else $s.='"'.dbr(prep($k,$val[$k],$key)).'"'."\t";
   }
  }
  fwrite($fp,$s."\r\n");
   $_POST["start"]++;
  }
  fclose($fp);
  echo $_POST["start"];
  exit;
}

$cnt=0;
if(isset($IMPCNF["sql_cnt"])) {
	$db->query($IMPCNF["sql_cnt"]);
	if($db->next_record()) $cnt=$db->Record[0];
}
if($cnt==0) exit;
?>
<html>
<head>
<SCRIPT LANGUAGE="JavaScript">
<!--
P=100/<?=$cnt?>;

function fin() {
 document.getElementById('readedcnt').innerHTML='<center><b>Файл отправлен</b></center>';
}

function read_(n) {
  global $_CONFIG;
   var y=parseInt(n);
   if(isNaN(y)) {
	   //alert(n);
	   document.getElementById('readedcnt').innerHTML='<a href="/'.$_CONFIG["ADMINDIR"].'/dwlxsl.php?f='+n+'" onclick="fin()">'+n+'</a>';
	   document.getElementById('pbar_border').style.display='none';
	   return false;
   }
   var x=parseInt(n*P);
   document.getElementById('readedcnt').innerHTML=x+'%';
   document.getElementById('pbar').style.width=x+'%';
   parent.Ext.Ajax.request({
              url: 'importcsv.php?f=<?=$_GET["f"]?>',
              success: function(response) {
                // изменения сохранились
				 read_(response.responseText);
              },
              params: {
                start:n
              }
   });
}

window.onload=function() {read_(0);}
//-->
</SCRIPT>
<style>
body {
  background:#dfe8f6;
}
#pbar_border {
  width:260px;
  border:1px solid #1545f9;
}
#pbar {
  background:#1545f9;
}
#readedcnt {
	font: 11px Arial, Helvetica, sans-serif;
}
</style>
</head>
<body>

<div id="readedcnt">0</div>
<div id="pbar_border">
<div id="pbar">&nbsp;</div>
</div>
</body>
</html>