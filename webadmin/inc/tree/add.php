<?
$pageCode=$id;
$access='22';

$rec=array();
$db->query("SELECT tpl,map,mkhtml,hiddenlink,wintitle,windescript,winkeywords,id FROM catalogue WHERE pid='$id' and deflt='1'");
if($db->next_record()) {
  $rec=$db->Record;
}

$db->query("SELECT max(indx) FROM catalogue WHERE pid='$id'"); 
$db->next_record();
$MaxSortIndx=$db->Record[0]+1;

if(!count($rec) && $id) {
  $_GET["sf"]=$id;
  $ADD="yes";

  include dirname(__FILE__)."/copy.php";  
} else {
  $tpl="";

  $db->query("INSERT INTO catalogue (indx, pid, title, owner) VALUES ('".$MaxSortIndx."','$id', '".str_replace("'","&#39;",$_GET["text"])."', '".$AdminLogin."')");
  
 // echo $db->LastQuery;

  
  $lid= getLastID();
  echo $lid;
  $InsertedId=$lid;
  if(count($rec)) {
    $defCod=$rec["id"];
    $db->query("UPDATE catalogue SET 
    tpl='".$rec["tpl"]."',
    map='".$rec["map"]."',
    mkHTML='".$rec["mkhtml"]."',
    hiddenLink='".$rec["hiddenlink"]."',
    winTitle='".$rec["wintitle"]."',
    winDescript='".$rec["windescript"]."',
    winKeywords='".$rec["winkeywords"]."',
    dir='$lid',
    attr='1'
    WHERE id='$lid'");
    
    CopyAccessRates($pageCode,$lid);
    
    mkContentCopy($defCod,$lid);

  } else {    
    $db->query("UPDATE catalogue SET dir='$lid', attr='1' WHERE id='$lid'");

	$db->query("SELECT * FROM templates WHERE tmpDeflt='1'");
	if($db->next_record()) {
      $cnt=intval($db->Record["tmpschema"]);
	  $db->query("UPDATE catalogue SET tpl='".$db->Record["tmpcod"]."' WHERE id='".$lid."'");
	  for($i=0;$i<$cnt;$i++) {
		  $db->query("INSERT INTO content (catalogue_ID) VALUES ('".$lid."')");
	  }
	}

  }  
}
?>
