<?
/*DESCRIPTOR
1,1
Каталог:Группы


files:
group: inforcombest
version:0.1
author: 
*/

//HEAD//


$PROPERTIES=array(
"tbname"=>"proizvoditeli",
"pagetitle"=>"Kategorii Kataloga",
"template_row"=>"catalog_cat_show.php",
"template_list"=>"catalog_cat_list.php",
"filters" => "yes",
"SPELL"=>0,
//"usrwhere"=>"WHERE news.publ='1'",
"keywords"=>"keywords"
);

$F_ARRAY=Array (
"id" => "hidden||",
"name"=>"text| size=90 maxlength=255|Название",
"parent" => "select|catalog_subcats*id*name*|Рубрика статьи",
"sort"=>"text| size=7 maxlength=255|Приоритет",

);

$f_array=Array(
"id" => "*hide*",
"name"=>"Название",
"parent"=>"Категория",
"sort"=>"Приоритет",
);

//ENDHEAD//


?>