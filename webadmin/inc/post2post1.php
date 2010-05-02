<?
function delUsrDir($str) {
	global $_USERDIR;
	if(isset($_USERDIR) && $_USERDIR) {
		$str=str_replace("/www/$_USERDIR/","/",$str);
	}
	return $str;
}

srand((double)microtime()*1000000);
$boundary = "---------------------------".substr(md5(rand(0,32000)),0,10);
//$boundary = substr(md5(rand(0,32000)),0,10);
$data = "--$boundary";

if(count($HTTP_POST_FILES)) {
	foreach($HTTP_POST_FILES as $k=>$v) if($v["name"]) {
	   $content_file = join("", file($v["tmp_name"]));

	   $data.="
content-disposition: form-data; name=\"$k\"; filename=\"".$v["name"]."\"; content-type: ".$v["type"]." 

$content_file
--$boundary";
	}
}

if(count($_POST)) {
	foreach($_POST as $k=>$v) {
		if(is_array($v)) {
			foreach($v as $key=>$val) {
				$data.='
content-disposition: form-data; name="'.$k.'['.$key.']"

'.delUsrDir($val).'
--'.$boundary;
			}
		} else {
		$data.='
content-disposition: form-data; name="'.$k.'"

'.delUsrDir($v).'
--'.$boundary;
		}
	}
}

$data.='
content-disposition: form-data; name="AdminCurrentSession"

'.serialize($_SESSION).'
--'.$boundary.'
content-disposition: form-data; name="AdminPasswordPOST"

'.md5($_CONFIG["DISTANCE_PASSWORD"]).'
--'.$boundary.'
content-disposition: form-data; name="AdminLoginPOST"

'.$_CONFIG["DISTANCE_LOGIN"].'
--'.$boundary;

$data.="--\r\n\r\n";

$msg =
"POST ".$_SERVER["REQUEST_URI"]." HTTP/1.0
Content-Type: multipart/form-data; boundary=$boundary
Content-Length: ".strlen($data)."\r\n\r\n";

$result="";

$f = fsockopen($_CONFIG["DISTANCE_HOST"], $_CONFIG["DISTANCE_PORT"]);

fputs($f,$msg.$data);

$i=0;
while (!feof($f) && $i<200) {
	$result .= fread($f,1000);
	$i++;
}

fclose($f);

$f=fopen($_SERVER["DOCUMENT_ROOT"]."/log.txt","w");fwrite($f,"http://".$_CONFIG["DISTANCE_HOST"].$_SERVER["REQUEST_URI"]."\n\n\n".$result);fclose($f);

if(!strpos($result,"<***ENDTAG***>")) $LOGPOST=0;
?>