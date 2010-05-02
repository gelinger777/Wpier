<?
/*DESCRIPTOR
0,0
Файлы для задания



group: workagoliks
version:0.1
author:
*/

//HEAD//
$PROPERTIES=array(
"tbname"=>"taskfiles",
"pagetitle"=>"Файлы для задания",
"template_row"=>"",
"template_list"=>"",
"filters" => "yes",
"SPELL"=>0,
"parentcode"=>"cod"
);

$F_ARRAY=Array (
"id" => "hidden||",
"cod"=>"hidden||",
"filename"=>"file|*../userfiles/task/|Файл",
"descript"=>"textarea| cols=70 rows=5|Описание",
"ftype"=>"select||Тип|1/К задаче|2/Результат",
"fdate"=>"unitime||Время размещения",

);

$f_array=Array(
"id" => "*hide*",
"cod"=>"*hide*",
"filename"=>"Файл",
"descript"=>"Описание",
"ftype"=>"Тип",
"fdate"=>"Дата, время",

);
//ENDHEAD//

