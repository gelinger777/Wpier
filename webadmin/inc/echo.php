<?

if(isset($_SESSION["ses_search"]) && $_SESSION["ses_search"] && !isset($SEARCHPAGE)) {
	$TMP=mksearch($TMP,$_SESSION["ses_search"]);
}

if($_CONFIG["ERROR_REPORT"]) include "./".$_CONFIG["ADMINDIR"]."/inc/errorreporter.php";
if(isset($_CONFIG["STATISTIC_LINKS"]) && $_CONFIG["STATISTIC_LINKS"] && !isset($_SESSION["ShowStatisticLincs"])) include "./".$_CONFIG["ADMINDIR"]."/inc/linksstatistic.php";
//$MAINFRAME=0;

if($_CONFIG["CASH_STATUS"] && !isset($_SESSION["adminlogin"])) {
// Если включен кэш для этой страницы, формируем имя файла из строки-запроса
  $InouterCashFilename=$_CONFIG["CASH_DIR"].'/'.md5($_SERVER["REQUEST_URI"]);
} else {
// В противном случае - из микросекунд
  $InouterCashFilename=$_CONFIG["CASH_DIR"].'/nocash/'.(mktime()+microtime());
}

// Очищаем от лишних меток
$TMP=ereg_replace("%([A-Za-z]){1,}([A-Za-z0-9_]){2,}%","",$TMP);

$TMP='<?
$HTML_FILE="'.$HTML_FILE.'";
$CurrentCod='.$CurrentCod.';
$RootDir="'.$RootDir.'";
$pid='.$pid.';
$CurrentDir="'.$CurrentDir.'";
$CurrentId='.$CurrentId.';
$documentTitle=\''.$documentTitle.'\';
$TemplatesPath="'.$TemplatesPath.'";
?>'.$TMP;

$f=fopen($InouterCashFilename,'w+');
fwrite($f,$TMP);
fclose($f);

// если нужна компрессия
if(isset($_CONFIG["COMPRESSION"]) && $_CONFIG["COMPRESSION"]) ob_start("ob_gzhandler");

include($InouterCashFilename);

if(!$_CONFIG["CASH_STATUS"]) {
// Если страница не кэшируется, удалим ее кэш
  unlink($InouterCashFilename);
}

if(isset($_CONFIG["STATISTIC_LINKS"]) && $_CONFIG["STATISTIC_LINKS"] && isset($_SESSION["ShowStatisticLincs"])) include "./".$_CONFIG["ADMINDIR"]."/inc/showlinksstatistic.php";

if(isset($_SESSION["adminlogin"])) {?>
<SCRIPT LANGUAGE="JavaScript">
<!--
empty_string="";
//-->
</SCRIPT>
<?
}
// Статистика --------------------------
if($_CONFIG["STAT"] && $_SERVER["REQUEST_URI"]!="/err404/") {
	include "./".$_CONFIG["ADMINDIR"]."/stat/stat.php";
}
@ob_end_flush();

//echo "<!-- test time=".(mktime()+microtime()-$_TEST_TIME_)."-->";

