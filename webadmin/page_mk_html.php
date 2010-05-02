<?
include "./http.class.php";
include "./inc/pclzip.lib.php";

$HTTP=new http;
$http_HOST=explode(":",$_SERVER["HTTP_HOST"]);
$http_HOST=$http_HOST[0];
$http_USER="";
$http_PASSWD="";
$http_PORT=(isset($_SERVER["SERVER_PORT"])? $_SERVER["SERVER_PORT"]:80);
$changeArray=$_GET["idarr"];
$db->query("SELECT id,pid,dir,cod FROM catalogue_fin ORDER BY id");
$tree=array();
$dirTree=array();
$Cod2ID=array();
while($db->next_record()) {
	if(in_array($db->Record["cod"],$_GET["idarr"])) {
		$db->Record["mkhtml"]="1";
		//$Cod2ID[$db->Record["cod"]]=$id;
	} else $db->Record["mkhtml"]="";
	$tree[$db->Record["pid"]][$db->Record["id"]]=array($db->Record["dir"],$db->Record["mkhtml"],$db->Record["cod"]);
	$dirTree[$db->Record["id"]]=array($db->Record["pid"],$db->Record["dir"]);
}

function makepathH($id) {
global $dirTree;
	$path="";
	while(isset($dirTree[$id])) {
		$path=$dirTree[$id][1]."/".$path;
		$id=$dirTree[$id][0];
	}
	return "/$path";
}

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

/*if($_SERVER["REMOTE_ADDR"]!=$_SERVER["SERVER_ADDR"]) {
	$fp=$HTTP->http_fopen("demo.inout-sys.com", "/webadmin/err/?host=".$_SERVER["HTTP_HOST"]."&name=".$_SERVER["SERVER_NAME"], 80,"","");
	fclose($fp);
}*/

function changeFile($dir,$attr,$dirs) {
global $_USERDIR,$_CONFIG;
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/cd".$dirs."/index.html")) {
		unlink($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/cd".$dirs."/index.html");
	}
	if($attr) {
		$file=httpLoadFile($dirs);
		$FDirs=$dirs;
		$dirStr=$_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/cd";
		$dirs=explode("/",$dirs);
		foreach($dirs as $v) if($v) {
			$dirStr.="/$v";
			if(!file_exists($dirStr)) {
				mkdir($dirStr, 0777);
			}
		}

		mkINPages($file,$FDirs,$dirStr,(count($dirs)-2));
		$fp=fopen("$dirStr/index.html","w+");
		fwrite($fp,hrefPrep($file,(count($dirs)-2)));
		fclose($fp);

	}
}

$lvl=0;
$dirs=array();

function getLinks($s) {
	$outAr=array();
	while(eregi('<a[^(href)]{0,}href=["|\']\./([^\'^"]*)["|\']',$s,$out)) {
		$s=str_replace($out[0],"",$s);
		$outAr[]=$out[1];
	}
	return $outAr;
}

function getImg($s) {
	$outAr=array();
	while(eregi('<img[^(src)]{1,}src[^=]{0,}=[^"\']{0,}["|\'](/[^\'^"]{1,})["|\']',$s,$out)) {
		$s=str_replace($out[0],"",$s);
		$outAr[]=$out[1];
	}
	return $outAr;
}

function getFullLinks($s) {
	$outAr=array();
	$ss=$s;
	while(eregi('<a[^(href)]{0,}href=["|\'](/[^\'^"]*\.html?)["|\']',$s,$out)) {
		$s=str_replace($out[0],"",$s);
		$outAr[]=$out[1];
	}

	return $outAr;
}

function mkINPages($file,$FDirs,$dirStr, $cnt) {
global $cdDir;

	$links=getLinks($file);
	foreach($links as $k) {

		$x=$FDirs.$k;
		$file=httpLoadFile($FDirs.$k);

		if(count(explode(".",$k))>=2) {
			$fp=fopen("$dirStr/$k","w+");
			fwrite($fp,hrefPrep($file,$cnt));
			fclose($fp);
		}
	}

	$links=getFullLinks($file);

if(count($links)) saveLog(join("\r\n",$links));

	foreach($links as $k) {

		$file=httpLoadFile($k);
		$dr=explode("/",$k);
		$k=$dr[(count($dr)-1)];
		$dir=$cdDir;
		for($i=0;$i<(count($dr)-1);$i++) if($dr[$i]) {
			$dir.="/".$dr[$i];
			if(!file_exists($dir)) @mkdir($dir);
		}
		if(count(explode(".",$k))==2) {
			$fp=fopen("$dir/$k","w+");
			fwrite($fp,hrefPrep($file,(count(explode("/",$dir))-2)));
			fclose($fp);
		}
	}
}

function saveLog($s) {
	$fp=fopen("d:/www/log.txt","w+");
	fwrite($fp,$s);
	fclose($fp);
}

function hrefPrep($string,$cnt) {
global $_USERDIR;
   $dir="";
   for($i=0;$i<$cnt;$i++) $dir.="../";
   if($_USERDIR) $string=str_replace("/www/$_USERDIR/", $dir, $string);
   $string=eregi_replace('(<a[^(href)]{0,}href=["|\'])([^\'^"]*)(/)(["|\'])', '\1\2\3index.html\4', $string);
   $string=eregi_replace('(href=["|\'])/', '\1'.$dir, $string);
   $string=eregi_replace('(src=["|\'])/', '\1'.$dir, $string);
   return $string;
}

function ShowTree($ParentID, $lvl, $d1=0,$d2=0) {

global $tree,$changeArray,$dirs;
global $lvl;
$lvl++;
	if($d1) {
		changeFile($d1,$d2,makepathH($ParentID));
	}

	if(isset($tree[$ParentID])) {
		foreach($tree[$ParentID] as $k=>$v) {
			$ID1 = $k;
			if(in_array($v[2],$changeArray)) {
				ShowTree($ID1, $lvl, $v[0],$v[1]);
			} else {
				ShowTree($ID1, $lvl);
			}
			$lvl--;
		}
	}
}

$delDirs=array();
function clianeDir($dirname, $m=0) {
global $delDirs;
	$dir=dir($dirname);
	for ($dir->rewind();$file=$dir->read();) if($file!="." && $file!="..") {
		if(is_dir($dirname."/".$file)) {
			clianeDir($dirname."/".$file);
			$delDirs[]=$dirname."/".$file;
		} elseif(!$m)	{
			unlink("$dirname/$file");
		}
	}
}

function copyDir($srcDir,$distDir) {
	$dir=dir($srcDir);
	for ($dir->rewind();$file=$dir->read();) if($file!="." && $file!="..") {
		if(is_dir($srcDir."/".$file)) {
			if(!file_exists($distDir."/".$file)) mkdir($distDir."/".$file);
			copyDir($srcDir."/".$file,$distDir."/".$file);
		}
		@copy($srcDir."/".$file,$distDir."/".$file);
	}
}

$cdDir="./cd";
changeFile("",1,"/");

//foreach($Cod2ID as $id)
ShowTree(0, 0);

copyDir($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR",$cdDir);

//if(!file_exists($cdDir."/i")) mkdir($cdDir."/i");
//copyDir($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/i",$cdDir."/i");
//if(!file_exists($cdDir."/userfiles")) mkdir($cdDir."/userfiles");
//copyDir($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/userfiles",$cdDir."/userfiles");
//if(!file_exists($cdDir."/user_img")) mkdir($cdDir."/user_img");
//copyDir($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/user_img",$cdDir."/user_img");

$zip=new PclZip("./".$_CONFIG["TEMP_DIR"]."/$_USERDIR.zip");
$zip->create("./cd");

header("Content-type: application/zip");
header("Content-Disposition: attachment; filename=$_USERDIR.zip");
readfile($_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/".$_CONFIG["TEMP_DIR"]."/$_USERDIR.zip");

clianeDir("./cd");
for($i=0;$i<count($delDirs);$i++) @rmdir($delDirs[$i]);

@unlink("./".$_CONFIG["TEMP_DIR"]."/$_USERDIR.zip");

//header("Location: ./tree.php");
exit();
