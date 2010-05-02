<?
if(!isset($ADD)) $ADD="";

$db->query("SELECT * FROM catalogue WHERE id='".intval($_GET["sf"])."'");
if($db->next_record()) {
  $sql=array(array("attr"),array("1"));
  $cod=$db->Record["id"];
  foreach($db->Record as $k=>$v) if(is_string($k) && $k!="id" && $k!="attr" && $k!="cod" && $k!="gotopage") {
    if($ADD && $k=="title") $v=str_replace("'","&#39;",$_GET["text"]);
    $sql[0][]=$k;
    $sql[1][]="'$v'";
  }
  $db->query("INSERT INTO catalogue (".join(",",$sql[0]).") VALUES (".join(",",$sql[1]).")");

//echo $db->LastQuery;

  $pid= getLastID();
  echo $pid;  
  $InsertedId=$pid;
  $db->query("UPDATE catalogue SET dir='$pid',attr='1',pid='$id', indx='$MaxSortIndx' WHERE id='$pid'");

  $db->query("SELECT * FROM content WHERE catalogue_ID='$cod' ORDER BY id");
  $i=0;
  $sql=array();
  while($db->next_record()) {
    $sql[$db->Record["id"]]=array(array("catalogue_id"),array("'$pid'"),$db->Record["cpid"]);
    foreach($db->Record as $k=>$v) if(!intval($k) && $k!="id" && $k!="catalogue_id" && $k!="cpid" && $v) {
      if($ADD && $k=="text") $v=""; 
      $sql[$db->Record["id"]][0][]=$k;
      $sql[$db->Record["id"]][1][]="'$v'";
    }
    $i++;
  }
  foreach($sql as $k=>$v) if(!$v[2]) {
    $db->query("INSERT INTO content (".join(",",$v[0]).") VALUES (".join(",",$v[1]).")");
    $sql[$k][0]=getLastID();
  }
  foreach($sql as $v) if($v[2] && isset($sql[$v[2]])) {
    $db->query("INSERT INTO content (cpid,".join(",",$v[0]).") VALUES ('".$sql[$v[2]][0]."',".join(",",$v[1]).")");
  }

  CopyAccessRates($cod,$pid);

} else {
  $db->query("UPDATE catalogue SET pid='$id' WHERE id='".intval($_GET["sf"])."'");
}
