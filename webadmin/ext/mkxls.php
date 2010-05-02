<?  


echo "111";

$db->query($sql);
$RECS=array();
$i=0;
while($db->next_record()) {
  $RECS[$i]=array();
  foreach($db->Record as $k=>$v) if(is_string($k)) {
    $RECS[$i][$k]=$v;
  }
  $i++;
}



echo 1;

$ft=substr($_COOKIE["_xls"],strrpos($_COOKIE["_xls"],"."));

if($ft=='.xsl' || $ft=='.csv') {

if($ft=='.xls') {
// Тут генерим xls
} else {
// В противном случае делаем csv

echo 2;

  $f=fopen($_SERVER["DOCUMENT_ROOT"].$_COOKIE["_xls"],'w+');

  foreach($RECS as $rec) {
    $s=array();
  
    foreach($f_array as $k=>$v) if($k!="id") {
      if(isset($rec[$k])) $v=$rec[$k];
      else $v=$rec["id"];

      echo 3;
    
      //if(method_exists($OBJECTS[$k],'SetID'))
      //  $OBJECTS[$k]->SetID($rec["id"]); 		
    
      $s[]=htmlspecialchars($OBJECTS[$k]->mkList($v));
    }
    fwrite($f,join(";",$s)."\n");
  }
  
  fclose($f);
}}

echo 4;


?>