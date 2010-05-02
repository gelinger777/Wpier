<?
/*DESCRIPTOR
1,1
Задачи



group: workagoliks
version:0.1
author:
*/

//HEAD//
$PROPERTIES=array(
"tbname"=>"task",
"pagetitle"=>"Задачи",
"template_row"=>"",
"template_list"=>"",
"filters" => "yes",
"SPELL"=>0,
"parentcode"=>"proj",
"orderby"=>"id DESC"
);

$F_ARRAY=Array (
"id" => "hidden||",
"proj"=>"hidden||",
"name"=>"textarea| cols=70 rows=5|Задача",
"comment"=>"textarea| cols=70 rows=5|Комментарий",
"date_start"=>"date||Дата начала",
"date_end"=>"date||Дата завершения",
"employer"=>"select||Исполнитель|/выбрать",

);

$f_array=Array(
"id" => "*hide*",
"name"=>"Задача",
"comment"=>"Комментарий",
"date_start"=>"Дата начала",
"date_end"=>"Дата завершения",
"employer"=>"Исполнитель",

);
//ENDHEAD//

$db->query("SELECT id, adminname, adminlogin FROM settings ORDER BY adminname,adminlogin");
while($db->next_record()) {
	$F_ARRAY["employer"].="|".$db->Record["id"]."/".($db->Record["adminname"]? $db->Record["adminname"]:$db->Record["adminlogin"]);
}