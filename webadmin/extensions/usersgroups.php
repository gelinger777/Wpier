<?
/*DESCRIPTOR
0,0
Группы пользователей


tools: admin
version:0.1
author:
*/

//HEAD//
$PROPERTIES=array(
"conecttext"=>"Доступ к страницам",
"filters_size"=>"1",
"pagetitle"=>"Группы пользователей",
"pg2pg"=>"10",
"step"=>"10",
"filters"=>"1",
"tbname"=>"usergroups",
"delalert"=>"К группе, возможно, привязаны пользователи системы. Удалить?",
"nolang"=>1,
"geditable"=>1
//"formopenmode"=>"win:400:500"
//"warning"=>"Аккуратнее с группами!"
);

$F_ARRAY=Array (
"id" => "hidden||",
//"grpadminaccess" => "hidden||",
"grpname"=>"text| size=50 maxlength=|Название",
"grpdescript"=>"textarea| cols=70 rows=5|Описание",
"grpadminaccess"=>"checkbox||Доступ в админ. часть|",
"tree"=>"select||Доступ к структуре|/нет доступа|1/только просмотр|2/управление порядком следования|3/редактирование|4/ полный доступ",
"chmod"=>"checkbox||Админ. пользователей|",
//"block_1"=>"block|separator_1|Права на редактирование модулей|1",
//"separator_1"=>"separator|<br>|",
//"block_2"=>"block|separator_2|Права доступа к модулям в пользовательской части|1",
//"separator_2"=>"separator|<br>|",
);

$f_array=Array(
"id" => "*hide*",
"grpname"=>"Группа",
"grpdescript"=>"Описание",
"grpadminaccess"=>"Доступ в админ",
"tree"=>"Доступ к структуре",
"chmod"=>"Админ. пользователей"
);
//ENDHEAD//

$PAGECONTROL=array(
"Свойства группы",
"Доступ к модулям"=>"group_access_moduls",
"Доступ к страницам"=>"group_access_pages"
//"Доступ к страницам"=>"group_access_pages",
);

if(isset($_GET["del"]) && ereg("[0-9,]{1,}",$_GET["del"]) && $PERMIS[3]) {
  $db->query("DELETE FROM accessmodadmins WHERE grp in (".$_GET["del"].")");
  $db->query("DELETE FROM accesspgadmins WHERE grp in (".$_GET["del"].")");
}

function user_function() {
global $db,$id;
  // копирование прав доступа
  if(isset($_POST["_copy_from_buffer_from"]) && isset($_POST["id"]) && intval($_POST["_copy_from_buffer_from"])) {

    $db->query("DELETE FROM accessmodadmins WHERE grp=$id");
    $db->query("DELETE FROM accesspgadmins WHERE grp=$id");

    $db->query("INSERT INTO accessmodadmins (SELECT $id, mdl, rd,ad,ed,dl FROM accessmodadmins WHERE grp='".intval($_POST["_copy_from_buffer_from"])."')");
    $db->query("INSERT INTO accesspgadmins (SELECT $id, pg, rd,ad,ed,dl FROM accesspgadmins WHERE grp='".intval($_POST["_copy_from_buffer_from"])."')");

  }
}

