<?
/*DESCRIPTOR
0,1
Настройки пользователя


files:registration.htm,rempassword.htm
group: golfstream
version:0.1
author:
*/

$PROPERTIES=array(
"tbname"=>"settings",
"pagetitle"=>"Настройки пользователя <i>".$AdminLogin."</i>",
"spell"=>"no",
"nolang"=>1,
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
"skin" =>"select|/ onchange='alert(1)'|Темы оформления|/стандартная",
"wallpaper"=>"file|jpg,jpeg*../".$_CONFIG["USERFILES_DIR"]."/wallpaper/*maxlength=255|Обои рабочего стола",
//"striptags" =>"checkbox||Удалять теги <a href='#' title='Автоматически предлагать удалять лишние теги в редакторе при вставке из буфера обмена'><b>(?)</b></a>|",
//"adminopnwin"=>"checkbox||Открывать в отдельном окне|",
);

$f_array=array(
"id"=>"",
);

$d = dir("ext/css");
while (false !== ($entry = $d->read())) {
    if(substr($entry,0,7)=='xtheme-') {
       $entry=str_replace(".css","",substr($entry,7));
       $F_ARRAY["skin"].="|$entry/$entry";
    }
}

if(isset($HTTP_POST_FILES["wallpaper"])) {
    $HTTP_POST_FILES["wallpaper"]["name"]=$ADMIN_ID.".jpg";
}

$_GET["ch"]=$ADMIN_ID;
$_POST["id"]=$ADMIN_ID;

$F_ARRAY["separator_1"].="<script>
	function ask_reload_win(){
            ParentW.Ext.MessageBox.show({
                title: ParentW.DLG.t('Changing skin'),
                msg: ParentW.DLG.t('Skin was changed. Reload the interface?'),
                width:350,
                buttons: ParentW.Ext.MessageBox.YESNO,
                fn: function(btn) {
                  if(btn=='yes') {
                    ParentW.location=ParentW.location;
                  }
                },
                icon:'ext-mb-warning'

              });
	}

          </script>";


if(isset($_POST["skin"])) {
    $db->query("SELECT skin FROM settings WHERE id='$ADMIN_ID'");
    if($db->next_record() && $db->Record[0]!=$_POST["skin"]) {
        function user_function() {
            echo "<script>parent.ask_reload_win();</script>";
        }
    }
}



$folds=$db->folders_names("settings");
if(!isset($folds["lock_time"])) $db->query("ALTER TABLE `settings` ADD `lock_time` INT NULL");
if(!isset($folds["lock_user"])) $db->query("ALTER TABLE `settings` ADD `lock_user` varchar(20) NULL");

if(isset($ADMINGROUP)) unset($ADMINGROUP);
