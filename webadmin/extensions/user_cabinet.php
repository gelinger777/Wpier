<?
/*DESCRIPTOR
1,1
Модуль Касса для юзеров


files:news_list.htm,news_show.htm
group: inforcombest
version:0.1
author:Геворк.
*/

//HEAD//


$PROPERTIES=array(
"tbname"=>"user_news",
"pagetitle"=>"Новости",
"template_row"=>"user_cabinet",
"template_list"=>"user_cabinet",
"filters" => "yes",
"SPELL"=>0,
"usrwhere"=>"WHERE news.publ='1'",
"keywords"=>"keywords",
"FIX_ID_TO_COD"=>"cod"
);

//ENDHEAD//
