<?
/*DESCRIPTOR
1,1
Модуль Касса для юзеров


files:news_list.htm,news_show.htm
group: inforcombest
version:0.1
author:Геворк.
*/

//HEAD//


$PROPERTIES=array(
"tbname"=>"user_news",
"pagetitle"=>"Новости",
"template_row"=>"user_news_show",
"template_list"=>"user_news_list",
"filters" => "yes",
"SPELL"=>0,
"usrwhere"=>"WHERE news.publ='1'",
"keywords"=>"keywords",
"FIX_ID_TO_COD"=>"cod"
);

$F_ARRAY=Array (
"id" => "hidden||",
"cod" => "hidden||",
"dt"=>"date||Дата||1|1",
"title"=>"text| size=90 maxlength=255|Заголовок",
"publ"=>"checkbox||Отображать на сайте|",
"user" => "select|my_users*username*username|Новость для ",
"announce"=>"textarea| cols=80 rows=5|Анонс",
//"keywords"=>"textarea| cols=80 rows=5|Ключевые слова (через запятую)",
//"block1"=>"block|img,ftext|Текст и фото|1",
"img"=>"img|200?x*/userfiles/news/*|Фото",
"ftext"=>"editor| width=450 height=350|Текст",
"myfiles"=>"editlist|myfilestable,cod,id,fname:Файл:f:../userfiles/files/,fcomment:Комментарий:50|Файл",
);

$f_array=Array(
"id" => "*hide*",
"dt"=>"Дата",
"title"=>"Статья",
"keywords"=>"ключевые слова",
"announce"=>"Анонс",
"img"=>"Фото",
"publ"=>"Публиковать",

);
//ENDHEAD//
if(isset($_POST['ins'])){

$user=$_POST['user'];
$header = 'From: noreply@'.$_SERVER['SERVER_NAME'] . "\r\n" .
    'Reply-To: noreply@'.$_SERVER['SERVER_NAME'] . "\r\n" .
    'X-Mailer: GEWA-MAIL/' . phpversion();

$db->query("SELECT *  from `my_users`  WHERE `username`='%$user%' LIMIT 1 ");
while($db->next_record()) 
{

$nachricht = "Новая Новость на Сайте. ";

$nachricht = wordwrap($nachricht, 70);



mail($db->Record["email"], 'Новая Новость', $nachricht,$header);

//mail('maridansoft@yahoo.com','News Added',$nachricht,$header);
}


}
