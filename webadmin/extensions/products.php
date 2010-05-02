<?
/*DESCRIPTOR
1,1
Каталог:Продукты Каталога


files:
group: inforcombest
version:0.1
author:
*/

//HEAD//

$PROPERTIES=array(
"tbname"=>"products",
"pagetitle"=>"Главное меню",
"template_row"=>"show_product.php",
"template_list"=>"product_show.php",
"orderby"=>"id",
"updown"=>"id",
"egrid"=>1
);

$F_ARRAY=Array (
"id" => "hidden||",
"name" => "text|size=70|Название Товара",
"img_0" => "img|x140*/userfiles/*/userfiles/preview/||Кaртинка(Главная)",
"proizvoditel" => "select|proizvoditeli*id*name|Привязать к Группе",
"descr"=>"editor| cols=80 rows=5|Текст описания",
//"img_right" =>"img|261?x*/userfiles/*/userfiles/preview/|Картинка справа ",
//"top_descr"=>"textarea| cols=80 rows=5|Верхний текст снизу",
//"img_bot" =>"img|502?x*/userfiles/*/userfiles/preview/|Кaртинка снизу",
//"botom_descr"=>"textarea| cols=80 rows=5|Нижний текст снизу",
//"pid" => "select|catalog_subcats*id*name|Привязать к разделу",
"old_price"=>"text|size=10|Старая Цена(для расчета в ангеботах)",
"price"=>"text|size=15|Цена(ВНИМАНИЕ! Точка разделяет центы,никаких кавычек, только цыфры и точка)",
"za_chto"=>"text|size=10| Цена для(пример. 1kg,1pkg,100g,1pallete)",


"priority"=>"text|size=9|Приоритет",
"novinka" => "checkbox||Новинка|1",
//"novinka_name" => "text|size=70|Загаловка Новинки",
"spec" => "checkbox||Спецпредложение|0",
//"kromka" => "checkbox||Этот Продукт КРОМКА|0",
//"spec_name" => "text|size=70|Заголовик Предложения",
);

/*

$db->query("SELECT *  from products  where `kromka`='1' ");
while($db->next_record()) 
{
$F_ARRAY["krom_1"].="|".$db->Record["id"]."/".strip_tags($db->Record["name"]);
$F_ARRAY["krom_2"].="|".$db->Record["id"]."/".strip_tags($db->Record["name"]);
$F_ARRAY["krom_3"].="|".$db->Record["id"]."/".strip_tags($db->Record["name"]);
$F_ARRAY["krom_4"].="|".$db->Record["id"]."/".strip_tags($db->Record["name"]);
}
*/






$f_array=Array(
"img_0" => "Фотка",
"name" => "Название",
"price" => "Цена",
"proizvoditel" => "Группы",

"priority"=>"Приоритет",
"novinka" => "Новинка",
"spec" => "Спецпредложение"

//"leftM" => "меню №3||<center><big><b>*</b></big></center>",
);
//ENDHEAD//

?>