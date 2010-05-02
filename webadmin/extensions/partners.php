<?
/*DESCRIPTOR
1,1
Партнеры


files:
group: inforcombest
version:0.1
author:
*/

//HEAD//


$PROPERTIES=array(
"tbname"=>"partners",
"pagetitle"=>"Страна",
//"template_row"=>"f_show.php",
"template_list"=>"partners_list.php",
"filters" => "yes",
"SPELL"=>0,
//"usrwhere"=>"WHERE news.publ='1'",
"keywords"=>"keywords"
);

$F_ARRAY=Array (
"id" => "hidden||",
//"pid" => "select|strana*id*strana*|Страна",
"name"=>"text| size=90 maxlength=255|Компания",
"url"=>"text| size=90 maxlength=255|WWW",
"descr"=>"textarea| cols=80 rows=5|Описание",
"img" => "img|150?x*/userfiles/*/userfiles/preview/|Logo ",
"partner"=>"checkbox||Отображать на<br> старнице партнеры|"

);

$f_array=Array(
"id" => "*hide*",
"pid"=>"страна",
"name"=>"Компания",
'img'=>'Logo',
'partner'=>'Партнер'

);
//ENDHEAD//

function user_function() {
}

function last_function() {
}
?>