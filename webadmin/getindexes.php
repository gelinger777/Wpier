<?
include dirname(__FILE__)"/inc/function.php";

$db->query("SELECT * FROM indexes ORDER BY wrd, cnt");
while($db->next_record()) echo $db->Record["wrd"]."|".$db->Record["url"]."\r\n";
