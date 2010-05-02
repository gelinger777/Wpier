<?
///////////////////////////////////////////////////////
//
//			Генератор виртуальных страниц v6.0
//			WPIER
//			Автор: Maxim Tushev
//			Copyright 2004-2009
//          CASH SCRIPT
//
///////////////////////////////////////////////////////

// Укажите каталог размещения администратора
define("_WPIER_ADMIN_DIR_","webadmin");

error_reporting(E_ALL);

//$_TEST_TIME_=mktime()+microtime();
include _WPIER_ADMIN_DIR_."/inc/function.php";

if(isset($_CONFIG["RESOURCES_LOG"]) && $_CONFIG["RESOURCES_LOG"]) include _WPIER_ADMIN_DIR_."/inc/begin_get_info.php";

include _WPIER_ADMIN_DIR_."/inc/parsfunction.php";

if($_USERDIR && file_exists("./www/$_USERDIR/"._WPIER_ADMIN_DIR_."/lenguages.inc"))
  include "./www/$_USERDIR/"._WPIER_ADMIN_DIR_."/lenguages.inc";
else
  include _WPIER_ADMIN_DIR_."/lenguages.inc";


////////////////////////////////////////////////////////////////
// Включаем кэшь
////////////////////////////////////////////////////////////////
if($_CONFIG["CASH_STATUS"] && !isset($_SESSION["adminlogin"])) {
	$InouterCashFilename=$_CONFIG["CASH_DIR"].'/'.md5($_SERVER["REQUEST_URI"]);
	if(
	  file_exists($InouterCashFilename) &&
	  (mktime()-filemtime ($InouterCashFilename))<$_CONFIG["CASH_STATUS"]
	) {
		// Если страница уже закэширована, и дата ее последнего изменения меньше настройки кэша,
		// страницу берем из кэша
		header("HTTP/1.1 200 OK");
		if(isset($_CONFIG["COMPRESSION"]) && $_CONFIG["COMPRESSION"]) ob_start("ob_gzhandler");
		include $InouterCashFilename;

		// сохраним лог производительности
		if(isset($_CONFIG["RESOURCES_LOG"]) && $_CONFIG["RESOURCES_LOG"]) include _WPIER_ADMIN_DIR_."/inc/end_get_info.php";

		exit;
	}
}
$_SERVER["REQUEST_URI_SAVED"]=$_SERVER["REQUEST_URI"];

include _WPIER_ADMIN_DIR_."/inc/pvp.php";
