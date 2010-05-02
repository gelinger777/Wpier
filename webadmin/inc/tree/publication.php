<?  
$ids=array();

$db->query("TRUNCATE TABLE catalogue_fin");
$db->query("INSERT INTO catalogue_fin SELECT * FROM catalogue WHERE attr<'2' or attr is NULL");
$db->query("TRUNCATE TABLE content_fin");
$db->query("INSERT INTO content_fin SELECT * FROM content");

// шукаем измененные страницы, что бы их переиндексировать
$db->query("SELECT id FROM catalogue WHERE attr='1'");
while($db->next_record()) $ids[$db->Record[0]]=$db->Record[0];

$db->query("UPDATE catalogue SET attr='' WHERE attr='1'");
foreach($ids as $v) $LOGS_OBJ->UpdateIndex($v);
?>