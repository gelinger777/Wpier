<?
/*DESCRIPTOR
1,1
Каталог:Заказы


files:
group: inforcombest
version:0.1
author:Геворк
*/

//HEAD//


$PROPERTIES=array(
"tbname"=>"orders",
"pagetitle"=>"Orders",
//"template_row"=>"f_show.php",
//"template_list"=>"partners_list.php",
"filters" => "yes",
"SPELL"=>0,
//"egrid"=>"1",
"keywords"=>"keywords"
);

$F_ARRAY=Array (
"id" => "hidden||",
//"pid" => "select|strana*id*strana*|Страна",

"status" => "select||Статус заказа|New/Новый|Order Accepted/Принят Магазином|Paid/Оплачен|Shipped/Отправлен|Completed/Завершен|Cancelled/Отменен Покупателем или Магазином",
"date"=>"text| size=90 maxlength=255|Дата Формирования ",
"last_update"=>"text| size=90 maxlength=255|Последнее Изменение ",
"discount"=>"text| size=90 maxlength=255|Скидка ",
"discount_reason"=>"text| size=90 maxlength=255|Причина Скидки ",
"promocode"=>"text| size=90 maxlength=255|Код Скидки",

"delivery_cost"=>"text| size=90 maxlength=255|Расходы на  Доставку(lieferungkost) ",

"subtotal"=>"text| size=90 maxlength=255|Сумма к Оплате",


"delivery_first_name"=>"text| size=90 maxlength=255|Доставка: Имя ",
"delivery_last_name"=>"text| size=90 maxlength=255|Доставка: Фамилия ",
"delivery_company"=>"text| size=90 maxlength=255|Доставка: Компания ",
"delivery_country_id"=>"select|country*id*country*/|Доставка: Страна| ",
"delivery_region_id"=>"select|region*id*region*/|Доставка: Регион| ",
"delivery_city_id"=>"text| size=90 maxlength=255|Доставка: Город ",
"delivery_postal_code"=>"text| size=8 maxlength=255|Доставка: Почтовый Индекс ",
"delivery_street1"=>"text| size=90 maxlength=255|Доставка: Улица ",
"delivery_street2"=>"text| size=90 maxlength=255|Доставка: Дом/Место ",
"billing_first_name"=>"text| size=90 maxlength=255|Счет: Имя ",
"billing_last_name"=>"text| size=90 maxlength=255|Счет: Фамилия ",
"billing_company"=>"text| size=90 maxlength=255|Счет: Компания ",
"billing_country_id"=>"select|country*id*country*/|Счет: Страна| ",
"billing_region_id"=>"select|region*id*region*/|Счет: Регион| ",
"billing_city_id"=>"text| size=90 maxlength=255|Счет: Город ",
"billing_postal_code"=>"text| size=8 maxlength=255|Счет: Почтовый Индекс ",
"billing_street1"=>"text| size=90 maxlength=255|Счет: Улица ",
"billing_street2"=>"text| size=90 maxlength=255|Счет: Дом/Место ",
"billing_street2"=>"editor| size=90 maxlength=255|Пожелания ",
"pos"=>"editlist|order_items,order_id,id,item_name:Название позиции:50,qty:Кол-во:5|Заказанные позиции"

);

$f_array=Array(
"id" => "*hide*",
"date"=>"date",
"delivery_first_name"=>"Адрес: Имя ",
"delivery_last_name"=>"Адрес: Фамилия ",
"delivery_company"=>"Адрес: Компания ",
"delivery_country_id"=>"Адрес:Страна",
"delivery_region_id"=>"Адрес:Регион",
"delivery_city_id"=>"Адрес:Город",

"status"=>"Status ",

);
//ENDHEAD//

function user_function() {
}

function last_function() {
}
?>