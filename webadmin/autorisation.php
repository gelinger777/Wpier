<?



if(!isset($ACCESS)) {

    $DOCUMENT_ROOT=$_SERVER["DOCUMENT_ROOT"];
    $SCRIPT_NAME=$_SERVER["SCRIPT_NAME"];
    if (!isset($sub)) $sub="";
    require_once dirname(__FILE__)."/inc/function.php";

    $ADMIN_PUBLIC="";

    if(isset($_GET["newmod"])) {
      setcookie("COOK_NEWMOD", serialize($_GET),0,'/');
    }

    if(isset($_GET["local"])) {
      $RES_PATH=$_GET["local"];
      setcookie("COOK_RES_PATH",$RES_PATH);
    }
    elseif(isset($_COOKIE["COOK_RES_PATH"]) && $_COOKIE["COOK_RES_PATH"]) $RES_PATH=$_COOKIE["COOK_RES_PATH"];
    else $RES_PATH="/".$_CONFIG["ADMINDIR"]."/ext/";

    // Тут поля настроек, которые нужно убрать в настроечный раздел
    $SETTINGS=array(
    "SaveAndClose"=>1,
    "COUNT_ROWS"=>25,
    );

    include dirname(__FILE__)."/lenguages.inc";
    include dirname(__FILE__)."/version.inc";
    include dirname(__FILE__)."/inc/logs_save.inc";

    if(!isset($_CONFIG["ADMIN_LOCATION"])) $_CONFIG["ADMIN_LOCATION"]='rus';
    include dirname(__FILE__)."/location/".$_CONFIG["ADMIN_LOCATION"]."/prog.inc";

    if(!isset($_CONFIG["LOCK_TIMEOUT"]) || !$_CONFIG["LOCK_TIMEOUT"]) $_CONFIG["LOCK_TIMEOUT"]=60000;

    if(isset($_CONFIG["COOKIE_ONLY"]) && $_CONFIG["COOKIE_ONLY"])  {
		 include dirname(__FILE__)."/inc/cookie2session.php";
	} else	@session_start();

    $ACCESS=0;
	$ADMINOPNWIN="";
	$LENGUAGE="";

	if (isset($_SESSION['adminlogin'])) {
		$AdminLogin=$_SESSION['adminlogin'];

		if(isset($_CONFIG["GUEST_ENTER"]) && $_CONFIG["GUEST_ENTER"] && isset($_SESSION['AdminGuest'])  && $_SESSION['AdminGuest'])
			$db->query("SELECT * FROM settings WHERE AdminLogin='".$AdminLogin."' and admingroup='".intval($_CONFIG["GUEST_ENTER"])."'");
        else 	$db->query("SELECT * FROM settings WHERE AdminLogin='".$AdminLogin."' and AdminUID='".session_id()."'");

		if ($db->next_record()) {

			if($db->Record["access_block"]) {
				header("Location: /".$_CONFIG["ADMINDIR"]."/login.php");
				exit();
			}
			$ADMIN_ID=$db->Record["id"];
			$ADMIN_SKIN=(isset($db->Record["skin"])? $db->Record["skin"]:"");

			//echo "SKIN=$ADMIN_SKIN";exit;

			$ADMIN_EMAIL=$db->Record["adminemail"];
			$ADMIN_NAME=$db->Record["adminname"];
			$ADMIN_WALLPAPER=(isset($db->Record["wallpaper"])? $db->Record["wallpaper"]:"");
			$ADMIN_PUBLIC=(isset($db->Record["adminpublic"])? $db->Record["adminpublic"]:"1");
			if(isset($db->Record["currenteditor"])) $ADMIN_EDITOR=$db->Record["currenteditor"];
			else $ADMIN_EDITOR="";
			$LENGUAGE=$db->Record["lenguage"];
			$NOHELP=$db->Record["nohelp"];
			$ADMINOPNWIN=$db->Record["adminopnwin"];
			if($db->Record["numrows"]) $SETTINGS["COUNT_ROWS"]=$db->Record["numrows"];
			$STRIPTAGS=$db->Record["striptags"];
			$SPELL=$db->Record["spell"];
			$ADMINGROUP=$db->Record["admingroup"];
            $_SESSION['admingroup']=$ADMINGROUP;

            if(isset($_SESSION['AdminLocation'])) $_CONFIG["ADMIN_LOCATION"]=$_SESSION['AdminLocation'];


		} else {
			header("Location: /".$_CONFIG["ADMINDIR"]."/login.php");
			exit();
		}
	} else {
		header("Location: /".$_CONFIG["ADMINDIR"]."/login.php");
		exit();
	}
	function checkPass($pas,$pas1) {
		if($pas && $pas==$pas1 && strlen($pas)>=6) return 1;
		return 0;
	}
}

if(isset($_GET["len"]) && isset($lenguagesList[$_GET["len"]])) {
	$db->query("UPDATE settings SET lenguage='".$_GET["len"]."' WHERE id='$ADMIN_ID'");
	$LENGUAGE=$_GET["len"];
}

if($ACCESS!=-1 && isset($MAINFRAME)) unset($MAINFRAME);

$LOGPOST=1;
if(isset($_CONFIG["DISTANCE_MODE"]) && $_CONFIG["DISTANCE_MODE"]==1 && count($_POST) && (!isset($HTMLCODESAVE) || $_CONFIG["DISTANCE_POST_MODULES"])) {
	include dirname(__FILE__)."/inc/post2post.php";
}

if(isset($_CONFIG["GLOBAL_INCLUDE"]) && $_CONFIG["GLOBAL_INCLUDE"]) include $_CONFIG["GLOBAL_INCLUDE"];
if(isset($_CONFIG["BACKEND_GLOBAL_INCLUDE"]) && $_CONFIG["BACKEND_GLOBAL_INCLUDE"]) {
  include $_CONFIG["BACKEND_GLOBAL_INCLUDE"];
}

// инициализируем логи
$LOGS_OBJ=new Logs_Class();
if(isset($_CONFIG["INDEXER_PATH"])) $LOGS_OBJ->indexfile=$_CONFIG["INDEXER_PATH"];
if(isset($_CONFIG["LOG_ON"])) $LOGS_OBJ->log=$_CONFIG["LOG_ON"];
