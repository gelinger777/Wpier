<?
/*DESCRIPTOR
1,1
Филиалы


files:
group: inforcombest
version:0.1
author:
*/

//HEAD//


$PROPERTIES=array(
"tbname"=>"filial",
"pagetitle"=>"Страна",
"template_row"=>"filial_show.php",
"template_list"=>"filial_list.php",
"filters" => "yes",
"SPELL"=>0,
//"usrwhere"=>"WHERE news.publ='1'",
"keywords"=>"keywords"
);

$F_ARRAY=Array (
"id" => "hidden||",
"mesto"=>"text|size=100|Место(город Например)",

"name" => "text|size=100|Название",

"adres"=>"textarea|cols=50 rоws=10|Адрес",

"phone"=>"textarea|cols=50 rоws=10|Телефоны",

"map" => "img|567x*700x/userfiles/*/userfiles/preview/|Картинка ",



);

$f_array=Array(
"id" => "*hide*",
 "mesto"=>"Место(город Например)",

"name" => "Название",
"adres"=>"Описание",
//"img"=>"Фотка"
);
//ENDHEAD//

function user_function() {
}

function last_function() {
}
?>