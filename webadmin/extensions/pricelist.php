<?
/*DESCRIPTOR
1,1
Прайс Листы


files:
group: inforcombest
version:0.1
author:
*/

//HEAD//


$PROPERTIES=array(
"tbname"=>"price_list",
"pagetitle"=>"Страна",
"template_row"=>"price_list.php",
"template_list"=>"price_list.php",
//"filters" => "yes",
//"SPELL"=>0,
//"usrwhere"=>"WHERE news.publ='1'",
"keywords"=>"keywords"
);

$F_ARRAY=Array (
"id" => "hidden||",

"name"=>"text| size=90 maxlength=255|Название ",
"file" => "file|pdf,zip,rar*../userfiles/price_list/|Файл прайса(pdf,zip,rar только)",
//"main" => "checkbox||Показывать как общий<br> прайс лист в начале блока|0"
"short_descr"=>"editor| size=90 maxlength=255|Краткое Описание ",

);

$f_array=Array(
"id" => "*hide*",

"name"=>"Название",
"file"=>"Файл",
//"main"=>"Общая"
);
//ENDHEAD//

function user_function() {
}

function last_function() {
}
?>