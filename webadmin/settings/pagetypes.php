<?
$PROPERTIES=array(
"tbname"=>"savedpagetypes",
"pagetitle"=>"Сохраненные типы страниц",
"spell"=>0,
"NOADD"=>"yes",
"nolang"=>1
);

$F_ARRAY=Array (
"id" => "hidden||",
"ptname"=>"text|size=80 maxlength=500|Название типа",
"ptype"=>"textarea| cols=70 rows=5|",

);

$f_array=Array(
"id" => "*hide*",
"ptname"=>"Тип",
);

if(isset($_GET["ch"]) || isset($_POST["id"])) unset($F_ARRAY["ptype"]);

include "./output_interface.php";
