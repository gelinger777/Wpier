<?
/*DESCRIPTOR
1,0
Главное меню


files: 
group: поиск
*/

$PROPERTIES=array(
"tbname"=>"mainmenu",
"pagetitle"=>"Главное меню",
"orderby"=>"id",
"updown"=>"id",
"egrid"=>1
);

$F_ARRAY=Array (
"id" => "hidden||",
"item" => "text|size=70|Название пункта",
"page" => "select|catalogue*id*title*pid='0'*indx/|Привязать к разделу",
"topm" => "checkbox||Показать|1",
//"order" => "text|size=70|Priority",
//"botm" => "checkbox||В меню №2|1",
//"leftM" => "checkbox||В меню №3|1"

);

$f_array=Array(
"id" => "порядок",
"item" => "Пункт меню",
"page" => "Раздел",
"topm" => "Показать",
//"order" => "Priority"
//"botm" => "меню №2",
//"leftM" => "меню №3||<center><big><b>*</b></big></center>",
);

if(isset($_POST["page"]) && !$_POST["item"]) {
    $db->query("SELECT title FROM catalogue WHERE id='".intval($_POST["page"])."'");
    if($db->next_record()) $_POST["item"]=$db->Record[0];
}
?>