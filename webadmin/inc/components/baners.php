<?
//HEAD//
$PROPERTIES=array(
"tbname"=>"baners",
"pagetitle"=>"Банеры",
"orderby"=>"id",
"template_list"=>"baners.htm",

);

$F_ARRAY=Array (
"id" => "hidden||",
//"banCod" => "hidden||",
//"banDate"=>"date||Дата размещения",
"banImg"=>"file|gif,jpg,jpeg*../userfiles/baners/|Изображение баннера|show",
"banLink"=>"text|size=30|Ссылка перехода",
/*"banMaxClicks"=>"text|size=3|Ограничение по количеству кликов",
"banMaxShow"=>"text|size=3|Ограничение по количеству показов",
"banDateOff"=>"date||Дата отключения",
"banCountShow"=>"text|size=3|Число показов",
"banCountClicks"=>"text|size=3|Число кликов",
"banCountOrders"=>"text|size=3|Число заказов после клика",
"banLogin"=>"text|size=10|Логин для просмотра",
"banPasswd"=>"text|size=10|Пароль для просмотра",
"banPubl" => "checkbox||Опубликовать|1",*/
);

$f_array=Array(
"id" => "порядок",
//"banCod" => "Код",
//"banDate"=>"Дата размещения",
"banImg"=>"Изображение баннера|show",
"banLink"=>"Ссылка перехода",
//"banDateOff"=>"Дата отключения",
//"banPubl" => "Публ.||<center><b>x</b></center>",
);
//ENDHEAD//
?>