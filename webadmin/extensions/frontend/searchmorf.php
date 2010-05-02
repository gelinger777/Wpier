<?
parse_blk("search_results.htm","LIST");
                          

if(isset($_GET["search"])) $search=htmlspecialchars($_GET["search"]);
else $search="";

send2blk("search",$search);
  
// поделим запрос на отдельные слова  
$s=explode(" ",$search);
$str=array();
foreach($s as $v) {
  if($v) $str[$v]="%$v%";
}

// Выберем различные словоформы из справочника
$db->query("SELECT wrd FROM wordsforms WHERE cod in (SELECT cod FROM wordsforms WHERE wrd like '".join("' or wrd like binary '",$str)."')");
while($db->next_record()) {
  if(!isset($str[$db->Record[0]])) $str[$db->Record[0]]=$db->Record[0];
}

// Ищем страницы, где втречается хотя бы одно слово из поисковой строки 
$sql="FROM indexes WHERE wrd like '".join("' or wrd like '",$str)."'";

// посчитаем количество
$db->query("SELECT count(*) $sql");
$db->next_record();
send2blk("count",$db->Record[0]);

// Выводим результаты по 20 штук на странице и не более 20 страниц
$db->query("SELECT * $sql","",20,20);
$list="";
while($db->next_record()) {
  $list.=sendAr2blk($db->Record,$LIST);
}

send2blk("LIST",$list);

