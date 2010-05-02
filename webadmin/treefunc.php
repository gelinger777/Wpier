<?
include "./autorisation.php";

include "./inc/tree/treefunction.php";

function CopyPage($id,$newpid) {
    global $db;
    $pgid=$id;
    $db->query("SELECT * FROM catalogue WHERE id='$id'");
    if($db->next_record()) {
      $keys=array();
      $vals=array();
      $cod=$db->Record["id"];
      foreach($db->Record as $k=>$v) if(is_string($k) && $k!='id' && $k!='pid') {
        $keys[]=$k;
	if($k=='attr') $v='1';
        $vals[]=$v;
      }
      $items=array();
      $db->query("INSERT INTO catalogue (pid,".join(",",$keys).") VALUES ('$newpid','".join("','",$vals)."')");
      $id= getLastID();
      $db->query("UPDATE catalogue SET dir='$id' WHERE id='$id'");
      $db->query("SELECT * FROM content WHERE catalogue_ID='$cod'");
      while($db->next_record()) $items[]=$db->Record;
      
      foreach($items as $rec) {
        $keys=array();
        $vals=array();
        foreach($rec as $k=>$v) if(is_string($k) && $k!='id' && $k!='catalogue_id') {
          $keys[]=$k;
          $vals[]=$v;
        }
        $db->query("INSERT INTO content (catalogue_ID,".join(",",$keys).") VALUES ('$id','".join("','",$vals)."')");
      }
      
      // Скопируем и права
      $db->query("INSERT INTO ACCESSPGADMINS SELECT grp,$id,rd,ad,ed,dl FROM ACCESSPGADMINS WHERE pg=$pgid");
      
      return $id; 
    }
    return '';
}

function CopyBranche($id,$newpid) {
  global $db;
  $newpid=CopyPage($id,$newpid);
  $db->query("SELECT id FROM catalogue WHERE pid='$id'");
  $ids=array();
  while($db->next_record()) $ids[]=$db->Record[0];
  foreach($ids as $v) CopyBranche($v,$newpid);
  return $newpid; 
}

if(isset($_POST["move2favorit"]) && intval($_POST["move2favorit"])) {
	
	if(!$_POST["mod"]) {
		$db->query("UPDATE catalogue SET cod='0' WHERE id='".intval($_POST["move2favorit"])."'");
		echo "OK";
		exit;
	}
	
	$db->query("SELECT max(cod) FROM catalogue");
	if($db->next_record()) {
		$db->Record[0]=intval($db->Record[0])+1;		
		$db->query("UPDATE catalogue SET cod='".$db->Record[0]."' WHERE id='".intval($_POST["move2favorit"])."'");
		echo "OK";
		exit;
	}
	echo "ERR";
	exit;
}

if(isset($_GET["updatetitle"]) && isset($_GET["text"])) {



$db->query("UPDATE catalogue SET title='".addslashes($_GET["text"])."', attr='1' WHERE id='".intval($_GET["updatetitle"])."'");

echo $db->LastQuery;
  
exit;
}

if(isset($_GET["paste"]) && isset($_GET["id"]) && isset($_GET["pid"])) {
// Вставляем страницу из буффера
  
  if($_GET["paste"]=='cut') {  // Перемещение ветки  
    $db->query("UPDATE catalogue SET pid='".intval($_GET["pid"])."' WHERE id='".intval($_GET["id"])."'");
    echo intval($_GET["id"]);
  } elseif($_GET["paste"]=='copy') { // Копируем только текущую страницу     
    echo CopyPage(intval($_GET["id"]),intval($_GET["pid"]));    
  } elseif($_GET["paste"]=='copyall') { // Копируем всю ветку
    echo CopyBranche(intval($_GET["id"]),intval($_GET["pid"]));
  }
  
  
} elseif(isset($_GET["add"])) {
// Добавляем страницу
  $id=intval($_GET["add"]);
  $txt="";
  
  if(isset($_GET["file"]) && file_exists($_GET["file"])) {
  //ob_start();
    include "inc/preparehtml.php";
    $fp=fopen($_GET["file"],"r");
    $str=fread($fp,filesize($_GET["file"]));
    fclose($fp);
    
    //$_GET["text"]= explode("\n",trim(strip_tags($str)));
    $str=new Prepare_Html($str);
    $_GET["text"]=$str->Title;//trim(makeShortText($_GET["text"][0],100));
    $txt=$str->HTML;
    unlink($_GET["file"]);
 
    
  /*$fp=fopen($_SERVER["DOCUMENT_ROOT"]."/testprep.log","w+");
  fwrite($fp,ob_get_contents());
  fclose($fp);
  ob_end_clean(); */
    
  }
  if(!isset($_GET["text"]) || !$_GET["text"])  $_GET["text"]='New';
  $InsertedId=0;
  include "./inc/tree/add.php";	
  if($txt) {
    $db->query("SELECT id FROM content WHERE catalogue_ID='$InsertedId' ORDER BY id LIMIT 1");
    if($db->next_record()) {
      $db->query("UPDATE content SET text='".str_replace("'","&#39;",$txt)."' WHERE id='".$db->Record[0]."'");
    }
    if(isset($_GET["before"])) {
      $db->query("SELECT indx,pid FROM catalogue WHERE id='".intval($_GET["before"])."'");
      if($db->next_record()) {
        if($_GET["point"]=="above") $i=$db->Record[0];
        else $i=$db->Record[0]+1;
        $db->query("UPDATE catalogue SET indx=indx+1 WHERE pid='".$db->Record[1]."' and indx>=$i");
        $db->query("UPDATE catalogue SET indx=$i WHERE id='".$InsertedId."'"); 
      }      
    }
    echo "|".$_GET["text"];
  }
} elseif(isset($_GET["del"])) {// && (!isset($ADMINGROUP) || (isset($ADMINGROUP["moddel"]) && in_array("page",$ADMINGROUP["moddel"])))) {

  $db->Save_RL=1;
  // Удаляем страницы
  if(isset($_GET["idarr"])) {
	echo join(";",deletePage(explode(",",$_GET["idarr"])));
    //echo "OK";
  } else {
     echo join(";",deletePage(array(intval($_GET["del"]))));
      //echo "OK";
  }
  $db->Save_RL=0;

} elseif(isset($_GET["movenode"]) && isset($_GET["pid"]) && isset($_GET["ind"])) { 
// Перемещение ноды в новое место
  $id=intval($_GET["movenode"]);
  $pid=intval($_GET["pid"]);
  $ind=intval($_GET["ind"]);
  
  if($_GET["treename"]=='ExtendTreePanel' && !$pid) {
	 $ind++; 
	 $db->query("UPDATE catalogue SET cod='".$ind."' WHERE id='$id'");	 
	$db->query("SELECT id FROM catalogue WHERE cod!=0 and cod is not NULL ORDER BY cod");
	$idsar=array();
	while($db->next_record()) $idsar[]=$db->Record[0];	
	$i=1;
	foreach($idsar as $v) {
		if($i==$ind) $db->query("UPDATE catalogue SET cod='".$i++."' WHERE id='".$id."'");
		if($v!=$id) $db->query("UPDATE catalogue SET cod='".$i++."' WHERE id='".$v."'");
	}

  } else {
	  $db->query("UPDATE catalogue SET pid='$pid', cod='0' WHERE id='$id'");

	  $db->query("SELECT id FROM catalogue WHERE pid='$pid' and (cod is NULL or cod=0) ORDER BY indx");
	  
	  $idsar=array();
	  $i=0;
	  while($db->next_record()) {
		if($i==$ind) $idsar[$i++]=$id;
		if($db->Record[0]!=$id) $idsar[$i++]=$db->Record[0];
	  }
	  if(!isset($idsar[$ind])) $idsar[$ind]=$id;
	  
	  for($i=0;$i<count($idsar);$i++) {
		$db->query("UPDATE catalogue SET indx='$i' WHERE id='".$idsar[$i]."'");
	  } 
  }
}elseif(isset($_GET["getprop"])) {
// Читаем свойства
  $PERMIS=array(1,1,1,1);
  if($AdminLogin!='root') {     
    
    $db->query("SELECT  rd, ad, ed, dl, grp  FROM accesspgadmins WHERE pg='".intval($_GET["getprop"])."'");
    if($db->num_rows()>0) {
       $PERMIS=array(0,0,0,0);
    }
    while($db->next_record()) {
      if($_SESSION['admingroup']==$db->Record["grp"]) {
        $PERMIS=array($db->Record[0],$db->Record[1],$db->Record[2],$db->Record[3]);
        break;
      }
    }
        
    //$db->query("SELECT grp FROM accesspgadmins WHERE pg='".intval($_GET["getprop"])."' and rd='1' and grp='$ADMINGROUP'");
    
    //$access=0;
    //if($db->next_record()) $access=1;
  }
  $db->query("SELECT id,owner,lock_user,title FROM catalogue WHERE id='".intval($_GET["getprop"])."'");
  if($db->next_record()) {
    echo $db->Record[0]."|".$db->Record[1]."|".$db->Record[2]."|".$db->Record[3]."|".join("|",$PERMIS);
  }
} elseif(isset($_GET["publication"])) {
// Публикуем структуру
  include "inc/tree/publication.php";
  echo "OK";
} elseif(isset($_GET["clearblock"])) {
// Очищаем блок
  $db->query("SELECT cpid, id FROM content WHERE id='".intval($_GET["clearblock"])."'");
  if($db->next_record()) {
    if($db->Record["cpid"]) {
      $db->query("DELETE FROM content WHERE id='".$db->Record["id"]."'");
    } else {
      $db->query("UPDATE content SET title='', text='' WHERE id='".$db->Record["id"]."'");
    }
    echo "OK";
  }
} elseif(isset($_GET["connect"]) && isset($_GET["id"]) && isset($_GET["ext"])) {
// Читаем конекты записей к структуре
  $a=array();
  
  $db->query("SELECT content.catalogue_ID,catalogue.id,content.GlobalBlock FROM content,catalogue WHERE content.catalogue_ID=catalogue.id and content.spec='".AddSlashes($_GET["ext"])."'");
  $logGlobal=0;
  while($db->next_record()) {
    $a[$db->Record[0]]=$db->Record[1].'/0';
    if($db->Record["globalblock"]) $logGlobal=1;
  }
  
  if($logGlobal) {
    $a=array();
    $db->query("SELECT id FROM catalogue");
    while($db->next_record()) {
      $a[$db->Record[0]]=$db->Record[0].'/0';
    }
  }
  if($_GET["id"]) {
    $db->query("SELECT pgID FROM ".AddSlashes($_GET["connect"])." WHERE rowIDD='".intval($_GET["id"])."'");    
    while($db->next_record()) {
      if(isset($a[$db->Record[0]])) {
        $a[$db->Record[0]]=explode("/",$a[$db->Record[0]]);
        $a[$db->Record[0]]=$a[$db->Record[0]][0].'/1';
      }
    }  
  }
  echo join('|',$a);
} if(isset($_GET["doconnect"]) && isset($_GET["id"]) && isset($_POST["cods"])) {
// Соединяем запись к структуре
  $_POST["cods"]=explode("|",$_POST["cods"]);
  $_GET["doconnect"]= AddSlashes($_GET["doconnect"]);

  $_GET["id"]= explode(',',$_GET["id"]);
   
  if(count($_GET["id"])>1) {
    $ids=array();
    foreach($_GET["id"] as $v) if($v) $ids[]=intval($v);
  } else $ids=array(intval($_GET["id"][0]));
  
   
  if($_GET["f"]) {
     $db->query("SELECT ".AddSlashes($_GET["f"])." FROM ".$_GET["doconnect"]." WHERE id in( ".join(",",$ids).")");
     $ids=array();
     while($db->next_record()) $ids[]=$db->Record[0];
     $_GET["doconnect"].="catalogue";
  }
  
  $db->query("DELETE FROM ".$_GET["doconnect"]." WHERE rowIDD in (".join(",",$ids).")");
 
  foreach($_POST["cods"] as $v) if($v) {
    foreach($ids as $id) {
      $db->query("INSERT INTO ".$_GET["doconnect"]." (pgID,rowIDD) VALUES ('".intval($v)."','".$id."')");      
    }
  }
  echo "OK"; 
} elseif(isset($_POST["desktop"])) {
  $db->query("UPDATE settings SET desktop='".str_replace("'","&#39;",$_POST["desktop"])."' WHERE id='".$ADMIN_ID."'");
}
?>
