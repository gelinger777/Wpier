<?
	if (isSet($_POST["AdminLoginPOST"]) and isSet($_POST["AdminPasswordPOST"])){
	$sql="SELECT * FROM settings WHERE AdminLogin like binary '".str_replace("'","",$_POST["AdminLoginPOST"])."' and AdminPassword='".str_replace("'","",$_POST["AdminPasswordPOST"])."'";
	$db->query($sql);
	if ($db->next_record()) {
		if(isset($_POST["AdminCurrentSession"])) $_SESSION=unserialize($_POST["AdminCurrentSession"]);
		$AdminLogin=$_POST["AdminLoginPOST"];
		$_SESSION['adminlogin']=$db->Record["adminlogin"];
		$ADMIN_ID=$db->Record["id"];
		$ADMIN_EMAIL=$db->Record["adminemail"];
		$ACCESS=$db->Record["access"];
		if($ACCESS<0 && !isset($MAINFRAME))  $ACCESS=0;
		$LENGUAGE=$db->Record["lenguage"];
		$NOHELP=$db->Record["nohelp"];
		$ADMINOPNWIN=$db->Record["adminopnwin"];
		$COUNT_ROWS=$db->Record["numrows"];
		$STRIPTAGS=$db->Record["striptags"];
		$SPELL=$db->Record["spell"];
		$sp=explode(",", $db->Record["access_details"]);
		$ACCESS_DETAILS=array();
		for ($i=0;$i<count($sp);$i++) $ACCESS_DETAILS[$sp[$i]]="y";
	}
}

?>