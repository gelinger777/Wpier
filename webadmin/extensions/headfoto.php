<?
/*DESCRIPTOR
1,1
Fotka Sverxu


files:
group: поиск
*/

$PROPERTIES=array(
"tbname"=>"headphoto",
"pagetitle"=>"Главное меню",
"orderby"=>"id",
"updown"=>"id",
"egrid"=>1
);

$F_ARRAY=Array (
"id" => "hidden||",
"img" => "img|60x60x710x236*/userfiles/*/userfiles/preview/|Картинка 3",
"page" => "select|catalogue*dir*title*dir!='welcome'*indx/|Привязать к странице",
//"topm" => "checkbox||Показать|1",
//"order" => "text|size=70|Priority",
//"botm" => "checkbox||В меню №2|1",
//"leftM" => "checkbox||В меню №3|1"

);

$f_array=Array(
"id" => "порядок",
"img" => "Pic",
"page" => "Раздел",
"topm" => "Показать",
//"order" => "Priority"
//"botm" => "меню №2",
//"leftM" => "меню №3||<center><big><b>*</b></big></center>",
);


?>