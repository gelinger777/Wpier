<?
/*DESCRIPTOR
1,1
Сертификаты:Сами Сертификаты


files:
group: inforcombest
version:0.1
author:
*/

//HEAD//


$PROPERTIES=array(
"tbname"=>"serts",
"pagetitle"=>"сертификаты",
"template_row"=>"f_show.php",
"template_list"=>"sert_list.php",
//"filters" => "yes",
"SPELL"=>0,
//"usrwhere"=>"WHERE news.publ='1'",
"keywords"=>"keywords"
);

$F_ARRAY=Array (
"id" => "hidden||",

//"name"=>"text| size=90 maxlength=255|Название Сертификата",
//"pid" => "select|sert_cats*id*name*pid!='0'|Привязать к разделу|",
"img" => "img|200x*/userfiles/*/userfiles/preview/|Картинка сертификата",





);

$f_array=Array(
"id" => "*hide*",
//"pid" => "Категория",
//"name"=>"название",

"img"=>"Фотка"
);
//ENDHEAD//

function user_function() {
}

function last_function() {
}
?>