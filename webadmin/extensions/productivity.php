<?
/*DESCRIPTOR
0,-1
Монитор производительности


files:news_list.htm,news_show.htm
group: inforcombest
version:0.1
author: 
*/

//HEAD//

$PROPERTIES=array(
"tbname"=>"sys_productivity",
"pagetitle"=>"Производительность",
"filters" => "yes",
"SPELL"=>0,
"orderby"=>"tmsr DESC",

);

$F_ARRAY=Array (
"id" => "hidden||",
"url"=>"text| size=90 maxlength=255|Адрес",
"tmsr"=>"text| size=90 maxlength=255|время генерации",
"qrsr"=>"text| size=90 maxlength=255|Кол-во SQL-запросов",
"mmsr"=>"text| size=90 maxlength=255|Потребность в памяти (Бт)",
);

$f_array=Array(
"id" => "*hide*",
"url"=>"Url",
"tmsr"=>"время генерации",
"qrsr"=>"Кол-во SQL-запросов",
"mmsr"=>"Потребность в памяти (Бт)",

);
//ENDHEAD//

$PERMIS=array(1,0,0,1);
