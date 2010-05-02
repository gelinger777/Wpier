<?
if(!count($_POST) || (isset($EXT) && isset($_CONFIG["DISTANCE_EXT"]) && in_array($EXT,$_CONFIG["DISTANCE_EXT"]))) {
	$data=array(); 
	
	$data["AdminPasswordPOST"]=md5($_CONFIG["DISTANCE_PASSWORD"]);	
	$data["AdminCurrentSession"]=serialize($_SESSION);
	//$data["AdminLoginTEST"]=111111;
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
	if(isset($EXT) && isset($_CONFIG["DISTANCE_EXT"]) && in_array($EXT,$_CONFIG["DISTANCE_EXT"])) {
		echo substr($result,strpos($result,"<"));
		exit;
	}

	if(!strpos($result,"<***ENDTAG***>")) $LOGPOST=0;
}
?>