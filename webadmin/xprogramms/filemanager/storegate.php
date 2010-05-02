<?
include $_SERVER["DOCUMENT_ROOT"]."/function.php";
$TMP_DIR=$_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/".$_CONFIG["TEMP_DIR"];

// принимаем файл и кладем куда надо
if(isset($_POST["saveto"]) && isset($_FILES["file"])) {
  $f=explode("/",$_POST["saveto"]);
  $d=$_SERVER["DOCUMENT_ROOT"]."/".$f[1];
  for($i=2;$i<count($f)-1;$i++) {
    $d.="/".$f[$i];
    if(!file_exists($d)) mkdir($d);
  }
  copy($_FILES["file"]["tmp_name"],$d."/".$_FILES["file"]["name"]);
  echo 'OK';
  exit;
}
// Сохраняем данные во временный файл и возвращаем имя этого файла
if(isset($_POST["savevalue"])) {
  $_POST["savevalue"]=unescape($_POST["savevalue"],"Windows-1251");
  $fn=$TMP_DIR.'/'.md5(mktime().microtime());

  $fp=fopen($fn,"w+");
  fwrite($fp,$_POST["savevalue"]);
  fclose($fp);

  if(isset($_POST["sendto"])) {
    $data=array();
    $data["uploadfile"]="@".$fn;

    $ch = curl_init($_POST["sendto"]."?file=".$_POST["fn"]."&fn=".substr($_POST["fn"],strrpos($_POST["fn"],'/')+1));

    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);
    curl_close ($ch);
    //echo $_GET["ret"]."('".$_GET["oid"]."','".$result."');";
    unlink($fn);
    echo 'OK';
    exit;
  }
  echo $fn;
  exit;
}

if(isset($_GET["delete"])) {
  if(file_exists($_GET["delete"]) && !is_dir($_GET["delete"])) {
    unlink($_GET["delete"]);
    echo $_GET["ret"]."('".$_GET["oid"]."');";
  }
  exit;
}
if(isset($_GET["uploadto"])) {
// Аплоад файла на пассивный клиент
  if(file_exists($_GET["tmp"]) && !is_dir($_GET["tmp"])) {
    $data=array();
    $data["uploadfile"]="@".$_GET["tmp"];

    $ch = curl_init($_GET["uploadto"]."?file=".$_GET["file"]."&fn=".$_GET["fn"]);

    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);
    curl_close ($ch);
    echo $_GET["ret"]."('".$_GET["oid"]."','".$result."');";
    unlink($_GET["tmp"]);
  }
  exit;
}elseif(isset($_FILES["uploadfile"])) {
// Сохраняем аплоад в темпе и отправляем имя файла
  if(file_exists($_FILES["uploadfile"]["tmp_name"])) {
    $fn=$TMP_DIR."/".mktime().rand(1,100);
    copy($_FILES["uploadfile"]["tmp_name"],$fn);
    echo $fn;
  }
  exit;
} elseif(isset($_GET["getfile"]) && $_GET["getfile"]) {
// Отдаем запрошенный файл
   if(file_exists($_GET["getfile"]) && !is_dir($_GET["getfile"])) {
     header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
     header('Content-Description: File Transfer');
     header('Content-Type: application/octet-stream');
     header('Content-Length: ' . filesize($_GET["getfile"]));
     header('Content-Disposition: attachment; filename=' . basename($_GET["getfile"]));
     readfile($_GET["getfile"]);
     if(strpos(" ".$_GET["getfile"],$TMP_DIR)==1)
       unlink($_GET["getfile"]);
   }
   exit;
}
if(isset($_GET["loadfrom"])) {
  //http://127.0.0.1:1011/webadmin/xprogramms/filemanager/storegate.php?url=&loadfrom=http://www.statpro.ru/store.php&file=./parsquery.php&ret=IO.CopySecondStep&oid=iCopyW144054406
  $handle = fopen($_GET["loadfrom"]."?getfile=".$_GET["file"], "rb");
  $contents = '';
  while (!feof($handle)) {
    $contents .= fread($handle, 8192);
  }
  $fn=$TMP_DIR."/".mktime().rand(1,100);
  $fp=fopen($fn,"w+");
  fwrite($fp,$contents);
  fclose($fp);
  echo $_GET["ret"]."('".$_GET["oid"]."','".$fn."');";
  exit();
}

if(!isset($_GET["url"])) exit;
$handle = fopen($_GET["url"].(isset($_GET["dir"])? "?dir=".$_GET["dir"]:""), "rb");
$contents = '';
while (!feof($handle)) {
  $contents .= fread($handle, 8192);
}
fclose($handle);
header("Content-type:text/xml");
header("Expires: Thu, Jan 1 1970 00:00:00 GMT");
header("Pragma: no-cache");
header("Cache-Control: no-cache");
echo $contents;
?>