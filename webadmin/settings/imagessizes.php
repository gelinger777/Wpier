<?
session_start();
require ("./autorisation.php");
if($ACCESS>1) {
	header("Location: ./access_defined.php");
	exit();
}

$PROPERTIES=array(
"tbname"=>"imagessizes",
"pagetitle"=>"Типовые размеры картинок",
"orderby"=>"id",
"updown"=>"id",
"nolang"=>"yes"
);

$F_ARRAY=Array (
"id" => "hidden||",
"imgsize" => "text|size=20|Размер",
"descript" => "text|size=80 maxlength=80|Описание"
);

$f_array=Array(
"id" => "порядок",
"imgsize" => "Размер",
"descript" => "Описание"
);

$PROPERTIES["FORM_EXTEND"]="<tr valign=top><td><b>Важно:</b></td><td>Формат размера: WxH, где W - ширина, H-высота. <BR>Если одна из величин не указана, она будет пересчитана на основе второй величины и пропорций исходной картинки.</td></tr>";

require ("./output_interface.php");
