<?
require_once "./autorisation.php";

$PROPERTIES=array(
"tbname"=>"settings",
"pagetitle"=>"Настройки пользователя <i>".$AdminLogin."</i>",
"spell"=>"no",
"nolang"=>1
);

$F_ARRAY=array(
"id"=>"hidden||",
"adminlogin" => "text|size=20|Логин",
"adminname" => "text|size=20|Имя",
"adminemail" => "text|size=20|E-mail",
"mailencoding" => "select||Кодировка почты|k/koi8-r|w/win-1251",
"adminpassword" => "password|size=20|Новый пароль",
"separator_1"=>"separator||<h2>Настройки интерфейса</h2>",
"separator_2"=>"separator||",
"numrows" => "text|size=5|Кол-во записей на странице",
"spell" =>"select||Проверка орфографии|/не проверять|1/по требованию|2/автоматически при сохранении",
"currenteditor" =>"select||Редактор|/Встроенный|1/FCK Editor",
"striptags" =>"checkbox||Удалять теги <a href='#' title='Автоматически предлагать удалять лишние теги в редакторе при вставке из буфера обмена'><b>(?)</b></a>|",
"adminopnwin"=>"checkbox||Открывать в отдельном окне|",
);

$f_array=array(
"id"=>"",
);

$_GET["ch"]=$ADMIN_ID;

$folds=$db->folders_names("settings");
if(!isset($folds["lock_time"])) $db->query("ALTER TABLE `settings` ADD `lock_time` INT NULL");
if(!isset($folds["lock_user"])) $db->query("ALTER TABLE `settings` ADD `lock_user` varchar(20) NULL");

if(isset($ADMINGROUP)) unset($ADMINGROUP);

require ("./output_interface.php");
