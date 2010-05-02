<?
/*DESCRIPTOR
1,1
Модуль Новостей


files:news_list.htm,news_show.htm
group: inforcombest
version:0.1
author:Максим Т.
*/

//HEAD//
$EDITABLE_ROWS=array(
"news"=>array("title"=>"text","announce"=>"textarea","ftext"=>"editor"),
);

$PROPERTIES=array(
"tbname"=>"news",
"pagetitle"=>"Новости",
"template_row"=>"news_show",
"template_list"=>"news_list",
"filters" => "yes",
"SPELL"=>0,
"usrwhere"=>"WHERE news.publ='1'",
"keywords"=>"keywords",
"FIX_ID_TO_COD"=>"cod"
);

$F_ARRAY=Array (
"id" => "hidden||",
"cod" => "hidden||",
"dt"=>"date||Дата||1|1",
"title"=>"text| size=90 maxlength=255|Заголовок",
"publ"=>"checkbox||Отображать на сайте|",
"announce"=>"textarea| cols=80 rows=5|Анонс",
"keywords"=>"textarea| cols=80 rows=5|Ключевые слова (через запятую)",
"block1"=>"block|img,ftext|Текст и фото|1",
"img"=>"img|200?x*/userfiles/news/*|Фото",
"ftext"=>"editor| width=450 height=350|Текст",
"myfiles"=>"editlist|myfilestable,cod,id,fname:Файл:f:../userfiles/,fcomment:Комментарий:50|Файл",
);

$f_array=Array(
"id" => "*hide*",
"dt"=>"Дата",
"title"=>"Статья",
"keywords"=>"ключевые слова",
"announce"=>"Анонс",
"img"=>"Фото",
"publ"=>"Публиковать",

);
//ENDHEAD//

