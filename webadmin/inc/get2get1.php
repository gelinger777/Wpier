<?
if(!count($_POST)) {
	srand((double)microtime()*1000000);
	$boundary = "---------------------------".substr(md5(rand(0,32000)),0,10);
	$data = "--$boundary";
	$data.='
content-disposition: form-data; name="AdminCurrentSession"

'.serialize($_SESSION).'
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
	// open the connection
	$f = fsockopen($_CONFIG["DISTANCE_HOST"], $_CONFIG["DISTANCE_PORT"]);
	fputs($f,$msg.$data);
	// get the response
	$i=0;
	while (!feof($f) && $i<120) {
		$result .= fread($f,1000);
		$i++;
	}
	fclose($f);


	//$f=fopen($_SERVER["DOCUMENT_ROOT"]."/log.txt","w");fwrite($f,"http://".$_CONFIG["DISTANCE_HOST"].$_SERVER["REQUEST_URI"]."\n\n\n".$result);fclose($f);
	//echo $result;
	if(!strpos($result,"<***ENDTAG***>")) $LOGPOST=0;

}
?>