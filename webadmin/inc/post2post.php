<?
function delUsrDir($str) {
	global $_USERDIR;
	if(isset($_USERDIR) && $_USERDIR) {
		$str=str_replace("/www/$_USERDIR/","/",$str);
	}
	return $str;
}

$data = array();

if(count($HTTP_POST_FILES)) {
	foreach($HTTP_POST_FILES as $k=>$v) if($v["name"]) {
	   copy($v["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/tmp/".$v["name"]);
	   $data[$k]="@".$_SERVER["DOCUMENT_ROOT"]."/tmp/".$v["name"];
	}
}

if(count($_POST)) {
	foreach($_POST as $k=>$v) {
		if(is_array($v)) {
			foreach($v as $key=>$val) {
				$data[$k.'['.$key.']']=delUsrDir($val);
			}
		} else {
			$data[$k]=delUsrDir($v);
		}
	}
}

$data["AdminCurrentSession"]=serialize($_SESSION);
$data["AdminPasswordPOST"]=md5($_CONFIG["DISTANCE_PASSWORD"]);
$data["AdminLoginPOST"]=$_CONFIG["DISTANCE_LOGIN"];

$ch = curl_init("http://".$_CONFIG["DISTANCE_HOST"].$_SERVER["REQUEST_URI"]);
curl_setopt($ch, CURLOPT_REFERER, $_CONFIG["DISTANCE_HOST"]);  
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, $_CONFIG["DISTANCE_TIMEOUT"]);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$result = curl_exec($ch);
curl_close ($ch);

foreach($HTTP_POST_FILES as $k=>$v) if(file_exists($_SERVER["DOCUMENT_ROOT"]."/tmp/".$v["name"])) {
   //unlink($_SERVER["DOCUMENT_ROOT"]."/tmp/".$v["name"]);
}

//echo "";
//$f=fopen($_SERVER["DOCUMENT_ROOT"]."/log.txt","w");fwrite($f,"http://".$_CONFIG["DISTANCE_HOST"].$_SERVER["REQUEST_URI"]."\n\n\n".$result);fclose($f);

if(!strpos($result,"<***ENDTAG***>")) $LOGPOST=0;
?>