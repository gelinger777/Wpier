<?
/*DESCRIPTOR
1,1
контакты


files:
group: inforcombest
version:0.1
author:
*/

//HEAD//


$PROPERTIES=array(
"tbname"=>"contacts",
"pagetitle"=>"Страна",
"template_row"=>"contacts_list.php",
"template_list"=>"contacts_list.php",
"filters" => "yes",
"SPELL"=>0,
//"usrwhere"=>"WHERE news.publ='1'",
"keywords"=>"keywords"
);

$F_ARRAY=Array (
"id" => "hidden||",
"name" => "text|size=70 maxlength=255|Заголовок",
"adres" => "editor|size=70 maxlength=255|Адрес",
"mail" => "text|size=70 maxlength=255|Email",
"tel" => "text|size=70 maxlength=255|Tel",
"time" => "editor|size=70 maxlength=255|Время Работы",
"map" => "img|300xx600x*/userfiles/*/userfiles/preview/|Карта",
"publ" =>"checkbox||Показывать на главной|1",



);

$f_array=Array(
"id" => "*hide*",
"name"=>"Name",
"adres"=>"Adress" ,
"mail"=>"email",
"tel"=>"tel",
"publ"=>"Показать на сайте ",
);
//ENDHEAD//

function user_function() {
}

function last_function() {
}
?>