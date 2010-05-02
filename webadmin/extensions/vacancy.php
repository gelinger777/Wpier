<?
/*DESCRIPTOR
1,1
Вакансии:Вакансии


files:
group: inforcombest
version:0.1
author: Геворк ака DR.GEWA
*/

//HEAD//

$PROPERTIES=array(
"tbname"=>"vacancy",
"pagetitle"=>"Вакансии",
"template_row"=>"vacancy_show.php",
"template_list"=>"vacancy_list.php",
"orderby"=>"id",
"updown"=>"id",
//"egrid"=>1
);

$F_ARRAY=Array (
"id" => "hidden||",
"name" => "text|size=70|Название ",
"short_descr"=>"editor| cols=80 rows=5|Короткое Описание",
"doxod" => "text|size=70|Уровень Дохода",
"tip_raboti" => "text|size=70|Тип работы",
"uslovia_raboti"=>"editor| cols=80 rows=5|Условия Работы",
"objazannosti"=>"editor| cols=80 rows=5|Обязанности",
"pol" => "select||Пол|1/Мужской|2/Женский|3/Не имеет Значения",
"vozrast" => "text|size=70|Возраст",
"obrazovanie" => "text|size=70|Образование",
"trebovania"=>"editor| cols=80 rows=5|Требования",
//"spec" => "checkbox||Спецпредложение|0",
//"spec_name" => "text|size=70|Заголовик Предложения",
);

$f_array=Array(
"id" => "порядок",

"name" => "Название Категории",
"short_descr"=>"Короткое Описание",
"doxod" => "Уровень Дохода",
"tip_raboti" => "Тип работы",
"uslovia_raboti"=>"Условия Работы",
"objazannosti"=>"Обязанности",
"pol" => "Пол",
"vozrast" => "Возраст",
"obrazovanie" => "Образование",
"trebovania"=>"Требования",
);
//ENDHEAD//

?>