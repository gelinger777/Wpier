<?
include "autorisation.php";

$finSuf="";
$db->query("SELECT  tree FROM usergroups WHERE id='".$ADMINGROUP."'");
if($db->next_record() && $db->Record[0]<3) $finSuf="_fin";

if(isset($_GET["findmodpage"])) {
  $db->query("SELECT catalogue_ID FROM content WHERE spec='".AddSlashes($_GET["findmodpage"])."'");
  if($db->next_record()) echo $db->Record[0];
  exit;
}elseif(isset($_GET["sitemap"]) && isset($_POST["data"])) {
  $db->query("UPDATE catalogue SET map=''");
  $x=explode(",",$_POST["data"]);
  foreach($x as $k=>$v) $x[$k]=intval($v);
  $db->query("UPDATE catalogue SET map='1' WHERE id='".join("' or id='",$x)."'");
  echo "OK";
  exit;
} elseif(isset($_GET["getlink"])) {
  $db->query("SELECT cod, title FROM catalogue WHERE id='".intval($_GET["getlink"])."'");
  if($db->next_record()) {
    echo "/".$db->Record[0].".html<spacer>".$db->Record[1];
  }
  exit;
}

// ------------------------
/* ob_start();
print_r($_GET);
print_r($_POST);
$s= ob_get_contents();
ob_end_clean();

$fp=fopen("testtree.txt","w+");
fwrite($fp,$s);
fclose($fp);  */
//-------------------


if (!isset($_POST['node'])) {
 $_POST['node']=0;
} else {
 $_POST['node']=intval($_POST['node']);
}


$nodes = array();
$checkboxes=array();
if(isset($_GET["connect"])) {
  $loggb=0;
  $db->query("SELECT catalogue_ID,GlobalBlock FROM content WHERE spec='".AddSlashes($_GET["ext"])."'");

  while($db->next_record()) {
    if($db->Record[1]) {
      $loggb=1;
      break;
    }
    $checkboxes[$db->Record[0]]=0;
  }

  if($loggb) {
    $checkboxes=array();
    $db->query("SELECT id FROM catalogue");
    while($db->next_record()) $checkboxes[$db->Record[0]]=0;
  }

  $ids=array();
  $_GET["id"]=explode(",",$_GET["id"]);
  foreach($_GET["id"] as $v) $ids[]=intval($v);

  $t=AddSlashes($_GET["t"]);
  if($_GET["fold"]!='id') {
    $db->query("SELECT ".AddSlashes($_GET["fold"])." FROM ".$t." WHERE id='".join("' or id='",$ids)."'");
    $ids=array();
    while($db->next_record()) {
      $ids[]=$db->Record[0];
    }
  }

  if(count($ids)) {
    $db->query("SELECT pgID FROM ".$t."catalogue WHERE rowIDD in (".join(",",$ids).")");

    while($db->next_record()) {
      if(isset($checkboxes[$db->Record[0]])) $checkboxes[$db->Record[0]]=1;
    }
  }
}

if(isset($_GET["extendtree"]) && $_POST['node']==0)
	$db->query("SELECT id,title,map, attr, hiddenlink FROM catalogue$finSuf WHERE cod!='0' and cod is not NULL ".($finSuf? " and (hiddenlink is NULL or hiddenlink='')":"")." ORDER BY cod");
else
	$db->query("SELECT id,title,map, attr,hiddenlink FROM catalogue$finSuf WHERE pid='".($_POST['node'])."'".(isset($_GET["chmodgroup"])? "":" and (cod=0 or cod is NULL)").($finSuf? " and (hiddenlink is NULL or hiddenlink='')":"")." ORDER BY indx");

//echo   $db->LastQuery;

//fwrite($fp,$ADMINGROUP.":".$db->LastQuery."\n\n");

$tree=array();
//$ax=($AdminLogin=='root'? 2:1);
while($db->next_record()) {$tree[$db->Record[0]]=array($db->Record[1],0,$db->Record[2],1,($finSuf? "":$db->Record[3]),$db->Record[4]);}

//ob_start();
//print_r($tree);
//fwrite($fp,ob_get_contents());
//ob_end_clean();

// Проверим права доступа на эти страницы

if($AdminLogin!='root') {
  $db->query("SELECT pg, grp, rd, ad, ed, dl  FROM accesspgadmins WHERE pg in (".kjoin(",",$tree).")");
  while($db->next_record()) {
    if($ADMINGROUP==$db->Record["grp"]) $tree[$db->Record["pg"]][3]=2;
    elseif($ADMINGROUP!=$db->Record["grp"] && $tree[$db->Record["pg"]][3]==1) $tree[$db->Record["pg"]][3]=0;
  }
}

// к права

if(isset($_GET["chmodgroup"])) {
// Если вызов из модуля изменения прав, прочитаем права на дерево для данной группы
  $db->query("SELECT rd,ed,ad,dl,pg FROM accesspgadmins WHERE grp='".intval($_GET["chmodgroup"])."' and pg in (".kjoin(",",$tree).")");
  while($db->next_record()) {
    $tree[$db->Record["pg"]]["rd"]=$db->Record["rd"];
    $tree[$db->Record["pg"]]["ed"]=$db->Record["ed"];
    $tree[$db->Record["pg"]]["ad"]=$db->Record["ad"];
    $tree[$db->Record["pg"]]["dl"]=$db->Record["dl"];
  }
}

/*
if($_SESSION['adminlogin']!='root'){
  // Читаем все дерево и определяем какие страницы нужно отображать, какие нет
  $db->query("SELECT id,pid FROM catalogue$finSuf ".($finSuf? "WHERE hiddenlink is NULL or hiddenlink=''":"")." ORDER BY pid,id");
  $tree_a=array();
  while($db->next_record()) $tree_a[$db->Record["id"]]=array($db->Record["pid"],0);
  $db->query("SELECT pg,rd,ed FROM accesspgadmins WHERE grp='$ADMINGROUP'");
  while($db->next_record()) if($db->Record["rd"] || $db->Record["ed"]) $tree_a[$db->Record["pg"]][1]=1;

  foreach($tree_a as $k=>$v) {
    if($v[1]) {
      $xp=$v[0];
      while($xp && isset($tree_a[$xp])) {
	if(isset($tree[$xp])) $tree[$xp][1]=1;
	$tree_a[$xp][1]=1;
	$xp=$tree_a[$xp][0];
      }
    }
  }
} else {*/
  $db->query("SELECT pid FROM catalogue$finSuf WHERE pid in (".kjoin(",",$tree).")");
  while($db->next_record()) if(isset($tree[$db->Record[0]])) $tree[$db->Record[0]][1]=1;
//}

//print_r();

$log=0;
if(isset($_GET["map"])) $log=1;

foreach($tree as $k=>$v) if($_SESSION['adminlogin']=='root' || $tree[$k][3]) {
   $s1="";
   if(isset($_GET["chmodgroup"])) {
     if(isset($v["rd"])) {
      $s1="uiProvider:'col',rd:'$k:".intval($v["rd"])."',ed:'$k:".intval($v["ed"])."',ad:'$k:".intval($v["ad"])."',dl:'$k:".intval($v["dl"])."',flid:'$k:".$v[1]."',";
     } else {
      $s1="uiProvider:'col',rd:'$k:0',ed:'$k:0',ad:'$k:0',dl:'$k:0',flid:'$k:".$v[1]."',";
     }
   }
   $s2="";
   if(!$finSuf && $v[5]) {
     $s2=" hddnpg";
   }
   if($log) $s=',checked:'.($v[2]? "true":"false");
   elseif(isset($checkboxes[$k])) $s=',checked:'.($checkboxes[$k]? "true":"false");
   else $s="";
   $v[0]=stripslashes($v[0]);
   if($v[1]) $nodes[] = "{".$s1."text:'".$v[0]."',id:'".$k."',iconCls: 'folder".$v[4]."',cls:'folder$s2'$s}";
   else $nodes[] = "{".$s1."text:'".$v[0]."',id:'".$k."', allowChildren:false, leaf:true, iconCls: 'file".($finSuf? "":$v[4])."',cls:'file$s2'$s}";
}
echo "[".join(",",$nodes)."]";
?>