<?
/*
Модуль генерации и сохранения HTML-кода динамических блоков
Запускается, если в модуле включено свойство $PROPERTIES["SaveBlock"]
*/

if(count($_POST)) {

 $pid=0;
 $dir="/";
 do {
  $db->query("SELECT pid,dir FROM catalogue WHERE ".($pid? "id='$pid'":"cod='".$CataloguePgID."'"));
  if($db->next_record()) {
    $dir="/".$db->Record["dir"].$dir;
    $pid= $db->Record["pid"];
  } else $pid=0;
 } while($pid);

 $contentsArr=array();
 $db->query("SELECT id FROM content WHERE catalogue_ID='$CataloguePgID' and spec='$EXT'");
 while($db->next_record()) {
 

  $handle = fopen("http://".$_SERVER["HTTP_HOST"].$dir."?prev=yes&cont=".$db->Record[0], "rb");
  $contents = '';
  while (!feof($handle)) {
    $contents .= fread($handle, 8192);
  }

  fclose($handle);
  if(strpos($contents,"<!-- START BLOCK ".$db->Record[0]." -->")) {
    $contents=substr($contents,strpos($contents,"<!-- START BLOCK ".$db->Record[0]." -->")); 
    $contents=substr($contents,strlen("<!-- START BLOCK ".$db->Record[0]." -->"),strpos($contents,"<!-- END BLOCK -->")-strlen("<!-- START BLOCK ".$db->Record[0]." -->")); 
    $contentsArr[$db->Record[0]]=$contents;
  }
 }
 $db->query("UPDATE catalogue SET attr='1' WHERE cod='".$CataloguePgID."'");
 foreach($contentsArr as $k=>$v) {
   $db->query("UPDATE content SET access='5', text='".str_replace("'","&#39;",$v)."' WHERE id='$k'");
 } 
 }?>
