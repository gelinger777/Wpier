<?
/*DESCRIPTOR
1,1
Каталог:Категории Каталога


files:
group: inforcombest
version:0.1
author:
*/

//HEAD//


$PROPERTIES=array(
"tbname"=>"catalog_subcats",
"pagetitle"=>"Kategorii Kataloga",
"template_row"=>"catalog_cat_show.php",
"template_list"=>"catalog_cat_list.php",
"filters" => "yes",
//"SPELL"=>0,
//"usrwhere"=>"WHERE news.publ='1'",
"keywords"=>"keywords"
);

$F_ARRAY=Array (
"id" => "hidden||",
"name"=>"text| size=90 maxlength=255|Название",
//"alias"=>"text|size=50 maxlength=255|Alias",
"img" => "img|128x128xx*/userfiles/*/userfiles/preview/|Картинка",
"priority"=>"text|size=10 maxlength=255|Приоритет",

);

$f_array=Array(
"id" => "*hide*",
'img'=>'Icon',
"name"=>"Название",
"priority"=>"Приоритет",
);

//ENDHEAD//


?>