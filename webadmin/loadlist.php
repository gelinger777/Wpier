<?
if(!isset($_GET["EXT"])) exit;
include_once "./autorisation.php";


// Смена установок просмотра
if(isset($_GET["settings2module"])) {
	
	$db->query("SELECT id FROM gridsettings WHERE usr='".$ADMIN_ID."' and modname='".addslashes($_GET["EXT"])."'");
	if($db->next_record()) {
		if(isset($_POST["clearsets"])) {
			$db->query("DELETE FROM gridsettings WHERE id='".$db->Record[0]."'");
			echo "OK";
			exit;
		} elseif(isset($_POST["mode"])) 
			$db->query("UPDATE gridsettings SET mode_='".intval($_POST["mode"])."' WHERE id='".$db->Record[0]."'");
		elseif(isset($_POST["cells"])) {
			$db->query("UPDATE gridsettings SET sizes_='".($_POST["cells"])."' WHERE id='".$db->Record[0]."'");
		}
	} elseif(!isset($_POST["clearsets"])) {
		if(isset($_POST["mode"])) {
			$sz="";
			if($_SESSION['adminlogin']!='root') {
				$db->query("SELECT sizes_ FROM gridsettings WHERE global='1' and modname='".$_GET["EXT"]."'");
				if($db->next_record()) $sz=$db->Record[0];
			}
			$db->query("INSERT INTO gridsettings (usr,modname,mode_,sizes_,global) VALUES ('".$ADMIN_ID."', '".$_GET["EXT"]."', '".intval($_POST["mode"])."','$sz','".($_SESSION['adminlogin']=='root'? "1":"")."')");
		}
		elseif(isset($_POST["cells"])) {
			$sz="";
			if($_SESSION['adminlogin']!='root') {
				$db->query("SELECT mode_ FROM gridsettings WHERE global='1' and modname='".$_GET["EXT"]."'");
				if($db->next_record()) $sz=$db->Record[0];
			}
			$db->query("INSERT INTO gridsettings (usr,modname,mode_,sizes_,global) VALUES ('".$ADMIN_ID."', '".$_GET["EXT"]."', '$sz', '".($_POST["cells"])."','".($_SESSION['adminlogin']=='root'? "1":"")."')");
		}
	}
	
	echo "LQ:".$db->LastQuery;
	
	exit;
}


if(isset($_GET["EXT"])) {
  $EXT=$_GET["EXT"]; 
  $PERMIS=array(1,1,1,1);
  $db->query("SELECT  rd, ad, ed, dl, grp  FROM accessmodadmins WHERE mdl='$EXT'");
  if($db->num_rows()>0) {
    $PERMIS=array();
  }
  while($db->next_record()) {
    if($ADMINGROUP==$db->Record["grp"]) {
      $PERMIS=array($db->Record[0],$db->Record[1],$db->Record[2],$db->Record[3]);
      break;
    }
  }
  
  if(!$PERMIS[0]) exit;
  
  if($_USERDIR && file_exists("../www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/$EXT.php")) {
    include "../www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/$EXT.php";
  } else {
    include "./extensions/$EXT.php";
  }
}

if(isset($_GET["blcod"]) && $_GET["blcod"]) {
  $db->query("SELECT cmpW FROM content WHERE id='".intval($_GET["blcod"])."'");
  if($db->next_record() && $db->Record[0]) {
    eval(str_replace('&quot;','"',$db->Record[0]));
  }
}

if(isset($_GET["ChangeVal"])) {
  include "./inc/mkObjects.php";
  $v=AddSlashes(unescape($_POST["data"],"Windows-1251"));
  if(method_exists($OBJECTS[$_GET["ChangeVal"]],'acceptEditor')) {
    $v=$OBJECTS[$_GET["ChangeVal"]]->acceptEditor($v);
  }
  $db->query("UPDATE ".$PROPERTIES["tbname"]." SET ".AddSlashes($_GET["ChangeVal"])."='$v' WHERE id='".intval($_POST["id"])."'");
  echo $db->LastQuery;
  exit;
}

if(isset($_GET["paste"])) {
// Вставка строк
  $ids=explode(',',$_GET["ids"]);
  $curid=intval($_GET["curid"]);
  
  if($_GET["paste"]=='up') { 
    $W="UPDATE ".$PROPERTIES["tbname"]." SET id=id+1 WHERE id>='%curid%' ORDER BY id DESC";
    $CI='$insid=$curid;$curid++;';
    if(isset($PROPERTIES["orderby"]) && strtolower($PROPERTIES["orderby"])=='id DESC') {
      $W="UPDATE ".$PROPERTIES["tbname"]." SET id=id+1 WHERE id>'%curid%' ORDER BY id DESC";
      $CI='$insid=$curid+1;';
    }
  } else {
    $W="UPDATE ".$PROPERTIES["tbname"]." SET id=id+1 WHERE id>'%curid%' ORDER BY id DESC";
    $CI='$insid=$curid+1;';
    if(isset($PROPERTIES["orderby"]) && strtolower($PROPERTIES["orderby"])=='id DESC') {
      $W="UPDATE ".$PROPERTIES["tbname"]." SET id=id+1 WHERE id>='%curid%' ORDER BY id DESC";
      $CI='$insid=$curid;$curid++;';
    }
  }
  
  foreach($ids as $k=>$v) if($v) {
    $v=intval($v);
    $db->query("SELECT * FROM ".$PROPERTIES["tbname"]." WHERE id='$v'");
    if($db->next_record()) {
      $rec=$db->Record;
      if($_GET["cut"]) $db->query("DELETE FROM ".$PROPERTIES["tbname"]." WHERE id='$v'");

      $db->query(str_replace("%curid%",$curid,$W));
      
      if(!$_GET["cut"] && isset($PROPERTIES["FIX_ID_TO_COD"])) {
      // Если это копирование, нужно создать уникальные поля
         $db->query("SELECT max(id) FROM ".$PROPERTIES["tbname"]);
         if($db->next_record()) {
           $rec[$PROPERTIES["FIX_ID_TO_COD"]]=$db->Record[0];
         }
      }
      
      eval($CI);
      $keys=array();
      $vals=array();
      foreach($rec as $k=>$v) if(is_string($k) && $k!='id') {
        $keys[]=$k;
        $vals[]=$v;
      }      
      $db->query("INSERT INTO ".$PROPERTIES["tbname"]." (id,".join(",",$keys).") VALUES ('$insid','".join("','",$vals)."')");      
    }
  }
  echo "OK";
  exit;   
}

if(isset($_GET["del"]) && $_GET["del"]) {  
// Удаление позиции 
  
  $_GET["del"]=explode(',',$_GET["del"]);
  foreach($_GET["del"] as $k=>$v) {
    $v=intval($v);
    if($v) $_GET["del"][$k]=$v;
    else unset($_GET["del"][$k]); 
  }
  if(count($_GET["del"])) {
	include_once "./inc/mkObjects.php";
    include "content_functions.php";
    foreach($_GET["del"] as $v) {
      
   
      delete_row($PROPERTIES["tbname"],$v);
    }
    echo "OK";
    exit;
  }
}
include_once "./inc/mkObjects.php";
@header("Content-type:text/xml");
@header("Expires: Thu, Jan 1 1970 00:00:00 GMT"); 
@header("Pragma: no-cache"); 
@header("Cache-Control: no-cache");
echo '<?xml version="1.0" encoding="UTF-8"?'.'>';


//if(!$step) $step=10;
$tbname=$PROPERTIES["tbname"];
$f=$db->folders_names($tbname);
$dbFolders=array();
foreach($f as $k=>$v) $dbFolders[strtolower($k)]=$v;
unset($f);

$s=array();

foreach($F_ARRAY as $key=>$val) {
  if($key!='id' && isset($dbFolders[strtolower($key)])) {
    $s[]="$tbname.$key";
  }
}

$SQL="FROM $tbname";

// Фильтрация
$ww=array();
if(isset($_POST["filter"])) {
  foreach($_POST["filter"] as $v) {
    $v=$OBJECTS[$v["field"]]->getFilterSearch($v["field"],$v["data"]);
    if($v) $ww[]=$v;
  }
}

$w=array();
$t=array();
if(isset($_GET["catalog"]) && isset($PROPERTIES["conect"])) {
  $t[]=$tbname."catalogue";
  $w[]=$tbname."catalogue.pgID='".intval($_GET["catalog"])."' and $tbname.".$PROPERTIES["conect"]."=".$tbname."catalogue.rowIDD";
}
$W=(isset($PROPERTIES["WHERE"])? " ".$PROPERTIES["WHERE"]:"");

if(isset($PROPERTIES["parentcode"]) && $PROPERTIES["parentcode"] && isset($_GET["inouter_parent_code"])) {
  $W=' '.($W? 'and':'WHERE').' '.$PROPERTIES["parentcode"]."='".intval($_GET["inouter_parent_code"])."'";
}

if(count($t)) $SQL.=",".join(",",$t);
if(count($w)) {
  $SQL.=($W? $W." and ":" WHERE ").join(" and ",$w).(count($ww)? " and ".join(" and ",$ww):"");
} elseif($W) $SQL.=$W.(count($ww)? " and ".join(" and ",$ww):"");
elseif(count($ww)) $SQL.=" WHERE ".join(" and ",$ww);

$db->query("SELECT count(*) $SQL");
if($db->next_record()) {
?>
<Items>
  <Request>
    <IsValid>True</IsValid>
  </Request>
  <TotalResults><?=$db->Record[0]?></TotalResults>
<?
}

$sql="SELECT $tbname.id,".join(",",$s)." $SQL ORDER BY ".(isset($_POST["sort"])? $tbname.".".AddSlashes($_POST["sort"])." ".AddSlashes($_POST["dir"]):(isset($PROPERTIES["orderby"])? $tbname.".".$PROPERTIES["orderby"]:(isset($PROPERTIES["updown"])? "$tbname.id":"$tbname.id DESC")));

if(isset($_COOKIE["_xls"]) && $_COOKIE["_xls"]) {
  include "./inc/mkxls.php";
}

$db->query($sql." LIMIT ".(isset($_POST["start"])? intval($_POST["start"]).",".intval($_POST["limit"]):"0,".$SETTINGS["COUNT_ROWS"]));

//echo $db->LastQuery;exit;
// print_r($db->Tables);

$RECS=array();
$i=0;
while($db->next_record()) {
  $RECS[$i]=array();
  foreach($db->Record as $k=>$v) if(is_string($k)) {
    $RECS[$i][$k]=$v;
  }
  $i++;
} 

foreach($RECS as $rec) {
 
  echo "<Item><id>".$rec["id"]."</id><ItemAttributes>";  
  
  foreach($F_ARRAY as $k=>$v) if($k!="id" && method_exists($OBJECTS[$k],'mkList')) {
    if(isset($rec[$k])) $v=$rec[$k];
    else $v=$rec["id"];
    
    if(method_exists($OBJECTS[$k],'SetID'))
      $OBJECTS[$k]->SetID($Record["id"]); 		
    
    $v=$OBJECTS[$k]->mkList($v);
    echo "<".$k.">".($v? "<![CDATA[".str_replace("<![CDATA[","",str_replace("]]>","",str_replace("<?","",str_replace("?>","",$v))))."]]>":"")."</".$k.">";
  }
  
  //print_r($rec);print_r($F_ARRAY);     exit;
  
  echo "</ItemAttributes></Item>";
}
?>        
</Items>