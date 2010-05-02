<?
session_start();
require ("./autorisation.php");
if($ACCESS>1) {
	header("Location: ./access_defined.php");
	exit();
}

if(!isset($_SESSION["len"])) $_SESSION["len"]="rus";

$PROPERTIES=array(
"tbname"=>"templates",
"pagetitle"=>"Шаблоны",
"orderby"=>"id",
"nolang"=>"yes"
);

$F_ARRAY=Array (
"id" => "hidden||",
"tmpcod" => "hidden||",
"tmpname" => "text|size=100|Название шаблона",
"tmpimg"=>"file|*../userfiles/|Картика схемы|show",
"tmpschema" => "text|size=2 maxlength=2|Количество блоков",
"tmpfile" => "file|htm*../templates_".$_SESSION["len"]."/|Файл шаблона" 
);

$f_array=Array(
"id" => "порядок",
"tmpcod" => "код",
"tmpname" => "Шаблон"
);

//$updown="id";
$orderby="id";
$FIX_ID_TO_COD="tmpcod";

if(isset($_GET["ch"]) && isset($MAINFRAME)) {
	$db->query("SELECT tmpFile FROM templates WHERE id='".intval($_GET["ch"])."'");
	if($db->next_record() && file_exists($db->Record[0]) && $db->Record[0]) {
		$FORM_EXTEND="<tr><td></td><td><input type='button' onclick='runHtmlEditor(\"".$db->Record[0]."\")' value='редактировать шаблон'>&nbsp;";
		
		if($FTP_UPLOAD_LOG) {
			$FORM_EXTEND.="<input type='button' onclick='document.frames[\"operateframe\"].navigate(\"./editorhtml/ftpcopy.php?file=".$db->Record[0]."\")'  value='сохранить на FTP'>";
		}
		$FORM_EXTEND.="</td>";
	}
}

require ("./output_interface.php");
?>