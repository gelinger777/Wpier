<?
include_once "./inc/translit.php";
include_once "./inc/rustypo.php";
include "./editor/scripts/editor/resizeimg.php";

if(!isset($_CONFIG["RESURCE_LED"])) $_CONFIG["RESURCE_LED"]=0;

if(isset($_CONF["LOCK_TIMEOUT"])) $LOCK_TIMEOUT=$_CONF["LOCK_TIMEOUT"];
else $LOCK_TIMEOUT=120;

$error=0;

// Обнуляем кэш
/*if(isset($_POST["ins"]) || isset($_POST["upd"]) || isset($_GET["del"])) {
  $db->query("DELETE FROM cash");
} */

if(isset($_POST["delCheck"]) && is_array($_POST["delCheck"]) && isset($PROPERTIES["tbname"])) {
  foreach($_POST["delCheck"] as $k=>$v) {
    $_GET["del"]=$v;
    delete_row($PROPERTIES["tbname"]);
  }
  unset($_GET["del"]);
}

// Обрабатываем ключевые слова
function provide_keywords_del($ext,$id) {
  global $db;

  $wrd=array();
  // Для начала удалим все связанные слова
  $db->query("SELECT wcod FROM keywordslink WHERE cod='".$id."' and modname='".$ext."'");
  while($db->next_record()) $wrd[$db->Record["wcod"]]=1;
  $db->query("DELETE FROM keywordslink WHERE cod='".$id."' and modname='".$ext."'");
  $db->query("SELECT DISTINCT wcod FROM keywordslink WHERE wcod in (".kjoin(",",$wrd).")");
  while($db->next_record()) unset($wrd[$db->Record["wcod"]]);
  if(count($wrd)) {
    $db->query("DELETE FROM keywords WHERE id in (".kjoin(",",$wrd).")");
  }
}

function provide_keywords($ext,$id,$words) {
  global $db;

  provide_keywords_del($ext,$id);

  $words=explode(",",$words);
  $wrd=array();
  foreach($words as $k=>$w) if($w) {
    $w=strtolower(trim(addslashes($w)));
    if($w) $wrd[$w]=0;
  }

  unset($words);
  if(count($wrd)) {

    $db->query("SELECT id,wrd FROM keywords WHERE wrd in ('".kjoin("','",$wrd)."')");
    while($db->next_record()) {
      $wrd[$db->Record["wrd"]]=$db->Record["id"];
    }
    foreach($wrd as $k=>$v) {
      if(!$v) {
        $db->query("INSERT INTO keywords (wrd) VALUES ('$k')");
        $v= getLastID();
      }
      $db->query("INSERT INTO keywordslink (modname,cod,wcod) VALUES ('$ext',$id,$v)");
    }
  }
}

function copyuserfile($key,$ext="",$n=-1) {
  global $HTTP_POST_FILES, $_POST, $FILE_NEW_NAME, $_USERDIR;
    $sp=explode("*",$ext);
    if(isset($sp[1]) && $sp[1]) $dir=$sp[1];else $dir="../".$_CONFIG["USERFILES_DIR"]."/";

    if(isset($_USERDIR) && $_USERDIR) {
      $dir="../www/$_USERDIR/".str_replace("../","",$dir);
    }

    if(!$sp[0]) $ext=array();
    else {
      $ext=explode(",",$sp[0]);
    }

    if (isset($HTTP_POST_FILES[$key])) {
      if($n<0) $userfile=$HTTP_POST_FILES[$key];
      elseif(isset($HTTP_POST_FILES[$key]["tmp_name"][$n]))
        $userfile=array("tmp_name"=>$HTTP_POST_FILES[$key]["tmp_name"][$n],"name"=>$HTTP_POST_FILES[$key]["name"][$n]);
      else return "";

      if (file_exists($userfile["tmp_name"])) {
        $userfile["name"]=str_translit($userfile["name"]);
        if ((!count($ext) || in_array(substr($userfile["name"],strrpos($userfile["name"],".")+1),$ext)) && $userfile["tmp_name"]) {
          if(isset($FILE_NEW_NAME) && isset($_POST[$FILE_NEW_NAME]) && $_POST[$FILE_NEW_NAME])
            $userfile["name"]=$_POST[$FILE_NEW_NAME];
          else $_POST[$FILE_NEW_NAME]=$userfile["name"];
          copy($userfile["tmp_name"],$dir.$userfile["name"]);
          return $dir.$userfile["name"];
        }
      }
    }
    return 0;
}


function undangerstr($inpstr) {
  $inpstr=stripslashes($inpstr);
  $inpstr=str_replace("'","&#39;",$inpstr);
  return $inpstr;
}

function check_key_type($key,$val) {
  global $F_ARRAY_PROPS;

  if(isset($F_ARRAY_PROPS) && in_array($key,$F_ARRAY_PROPS["items"])) return "";

  if (isset($_POST[$key])) $v=$_POST[$key];
  else $v="";
  $v=$val->getUpdateVals($key, $v);
  if(is_array($v)) if($v[0]!=strtolower($v[0])) $v[0]='"'.$v[0].'"';
  return $v;
}

function update_publ_pages($tbname,$id=0) {
  global $db,$EXT,$PROPERTIES;
//  $id=intval($id);
  if($tbname!='content' && $tbname!='catalogue' && isset($EXT) && $EXT!="" && isset($PROPERTIES["conect"])) {
    $ids=array();

    if(isset($PROPERTIES["conect"]) && $id) {
      if(isset($PROPERTIES["FIX_ID_TO_COD"]) && $PROPERTIES["FIX_ID_TO_COD"]!="id") {
         $db->query("SELECT ".$PROPERTIES["FIX_ID_TO_COD"]." FROM $tbname WHERE id='$id'");
   if($db->next_record()) $id=$db->Record[0];
      }

    $db->query("SELECT DISTINCT content.catalogue_id FROM  ".$PROPERTIES["tbname"]."catalogue,content  WHERE content.catalogue_id=".$PROPERTIES["tbname"]."catalogue.pgid and content.spec='$EXT' and ".$PROPERTIES["tbname"]."catalogue.rowidd='".intval($id)."'");
  } else {
      $db->query("SELECT catalogue_id FROM content WHERE spec='$EXT'");
  }
    while($db->next_record()) $ids[]=$db->Record[0];
    if(count($ids)) {
      $db->query("UPDATE catalogue SET attr='1' WHERE (attr<'2' or attr is NULL) and id in (".join(",",$ids).")");
    }
  }
}

function insert_row($tbname) {
  global $db,$_GET,$EXT;
  global $OBJECTS,$CataloguePgID,$PROPERTIES,$ADMIN_ID;

  $f_names=array();
  $f_values=array();
  foreach($OBJECTS as $key=>$val) {

	$out=check_key_type($key,$val);

    if (is_array($out)) {
      for($i=0;$i<count($out);$i++) {
        if($out[$i]!="id") {
                                  $f_names[]=$out[$i++];
          $f_values[]=$out[$i];
                    } else $i++;
      }
    }
  }
  $sql="INSERT INTO $tbname (".join(",",$f_names).") VALUES ('".join("','",$f_values)."')";
  $db->query($sql);

//echo $db->LastQuery;exit;

  $id=getLastID();

  if(isset($PROPERTIES["keywords"]) && $PROPERTIES["keywords"] && isset($_POST[$PROPERTIES["keywords"]])) provide_keywords($EXT,$id,$_POST[$PROPERTIES["keywords"]]);

  if(isset($CataloguePgID) && $CataloguePgID && isset($PROPERTIES["conect"])) {
    $db->query("INSERT INTO ".$tbname."catalogue (pgid,rowidd) VALUES ('$CataloguePgID','$id')");
  } elseif(isset($PROPERTIES["conect"])) {
    $_GET["ch"]=$id;
  }

  //echo $db->LastQuery;exit;


  if(isset($PROPERTIES["publication"]) && $PROPERTIES["publication"]) {
    $db->query("SELECT publposlmem.memb FROM publposl,publposlmem WHERE publposl.id=publposlmem.cod and publposl.modname='$EXT' ORDER BY publposlmem.id LIMIT 2");
    if($db->next_record()) {
      $sql=$db->Record[0];
            if($sql==$ADMIN_ID) {
        if($db->next_record()) {
                $sql=$db->Record[0];
              }
        else $sql=0;
      }
      $db->query("UPDATE ".$tbname." SET lastpubladmin='$sql' WHERE id='$id'");
      if($sql) $db->query("INSERT INTO publicationstatus (admin,mod,idr,tab) VALUES ('$sql','$EXT','$id','$tbname')");
    }
  }
  update_publ_pages($tbname,$id) ;
  return $id;

}

function update_row($tbname) {
  global $db,$EXT;
  global $OBJECTS,$PROPERTIES;
  global $helpstring;
  global $lastupdatetime;

  $f_where="";
  $f_values=array();
  $i=0;
  foreach($OBJECTS as $key=>$val) {
    $out=check_key_type($key,$val);
    if (is_array($out)) {
      for($i=0;$i<count($out);$i++) {
        $f_values[]=$out[$i]."='".$out[++$i]."'";
      }
    }
  }


  if(isset($_POST["id"])) {

    $db->query("UPDATE $tbname SET ".join(",",$f_values)." WHERE id='".intval($_POST["id"])."'");

//echo $db->LastQuery;exit;
    update_publ_pages($tbname,intval($_POST["id"])) ;

    if(isset($PROPERTIES["keywords"]) && $PROPERTIES["keywords"] && isset($_POST[$PROPERTIES["keywords"]])) provide_keywords($EXT,intval($_POST["id"]),$_POST[$PROPERTIES["keywords"]]);

    return intval($_POST["id"]);
  }
}

/*if(isset($_CONFIG["RESURCE_LED"]) && $_CONFIG["RESURCE_LED"]) {} else $_CONFIG["RESURCE_LED"]=0;*/

function delete_row($tbname,$id) {
  global $db, $DELTABS,$F_ARRAY,$PROPERTIES,$_CONFIG,$EXT,$AdminLogin,$OBJECTS;

  if($EXT!='trash') $db->Save_RL=1; // Включим сохранение удаленных записей (если только удаление не в корзине)

  $gtid=$id;

  if(isset($PROPERTIES["keywords"]) && $PROPERTIES["keywords"])
    provide_keywords_del($EXT,$id);

  if(isset($PROPERTIES["conect"])) {
          $db->query("SELECT ".$PROPERTIES["conect"]." FROM $tbname WHERE id='$id'");
          if($db->next_record()) {
            $cod=$db->Record[0];
            if(isset($PROPERTIES["alongconect"])) {
              $idar=array();
              $db->query("SELECT pgid FROM ".$tbname."catalogue WHERE rowidd='$cod'");
              while($db->next_record()) $idar[]=$db->Record[0];
              $db->query("DELETE FROM catalogue WHERE id in (".join(",",$idar).")");
              //$db->query("DELETE FROM catalogue_fin WHERE cod='".join("' or cod='",$idar)."'");
              $db->query("DELETE FROM content WHERE catalogue_id in (".join(",",$idar).")");
              //$db->query("DELETE FROM content_fin WHERE catalogue_ID='".join("' or catalogue_ID='",$idar)."'");
              //echo '<script>parent.frames["mainmenu"].navigate("./tree.php");</script>';
            }
            if($cod) $db->query("DELETE FROM ".$tbname."catalogue WHERE rowidd='$cod'");
          }
  }

  if(isset($PROPERTIES["delattach"]) && $PROPERTIES["delattach"]) {
    $db->query("SELECT * FROM $tbname WHERE id='".$gtid."'");
    if($db->next_record()) {
      foreach($F_ARRAY as $k=>$v) {
        $v=explode("|",$v);
        if($v[0]=="file" && file_exists($db->Record[$k])) unlink($db->Record[$k]);
      }
    }
  }

   /* if($_CONFIG["RESURCE_LED"]) {
       if(!isset($EXT)) {
            $EXT=substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
            $EXT=substr($EXT,0,strpos($_SERVER["SCRIPT_NAME"],"."));
       }
       $_SVRL=array("mod"=>$EXT,"tab"=>$tbname,"id"=>1,"raw"=>array(),"deptabs"=>array());
       if(isset($PROPERTIES["FIX_ID_TO_COD"]) && $PROPERTIES["FIX_ID_TO_COD"]) $_SVRL["id"]=0;
       $db->query("SELECT * FROM $tbname WHERE id='".$gtid."'");
       if($db->next_record(1)) $_SVRL["raw"]=$db->Record;
    }*/
  $db->query("DELETE FROM $tbname WHERE id='".$gtid."'".(isset($PROPERTIES["delwhere"])? " and ".$PROPERTIES["delwhere"]:""));


  if(isset($PROPERTIES["FIX_ID_TO_COD"])) {
    $db->query("SELECT ".$PROPERTIES["FIX_ID_TO_COD"]." FROM $tbname WHERE id='".$gtid."'");
    if($db->next_record()) $gtid=$db->Record[$PROPERTIES["FIX_ID_TO_COD"]];
  }

  foreach($OBJECTS as $v) if(isset($v->EVENTS) && isset($v->EVENTS["onDelete"])) {
    $v->onDelete($gtid);
  }

  if(isset($DELTABS)) {

    foreach($DELTABS as $k=>$v) {
      //if($_CONFIG["RESURCE_LED"]) $_SVRL["deptabs"][$k]=array();
      //$db->query("SELECT * FROM $k WHERE $v='".$gtid."'");
      //while($db->next_record(1)) $_SVRL["deptabs"][$k][]=$db->Record;

      $db->query("DELETE FROM $k WHERE $v='".$gtid."'");
    }
  }
  if(isset($PROPERTIES["conect"])) {
    if(isset($PROPERTIES["FIX_ID_TO_COD"]) && $PROPERTIES["FIX_ID_TO_COD"]!=$PROPERTIES["conect"]) {
      $db->query("SELECT ".$PROPERTIES["conect"]." FROM $tbname WHERE id='$id'");
      if($db->next_record()) $id=$db->Record[$PROPERTIES["conect"]];
    } else $id=$gtid;

    //if($_CONFIG["RESURCE_LED"]) $_SVRL["deptabs"][$tbname."catalogue"]=array();
    //$db->query("SELECT * FROM ".$tbname."catalogue WHERE rowidd='".$id."'");
    //while($db->next_record(1)) $_SVRL["deptabs"][$tbname."catalogue"][]=$db->Record;

    $db->query("DELETE FROM ".$tbname."catalogue WHERE rowidd='$id'");
  }

  /*if($_CONFIG["RESURCE_LED"]) {
     $db->query("INSERT INTO resurceled (modname,tabname,deltime,user,dataled) VALUES (
           '".$_SVRL["mod"]."',
           '".$_SVRL["tab"]."',
           '".time()."',
           '".$AdminLogin."',
           '".str_replace("'","**#39#**",serialize($_SVRL))."'
           )");
  }*/

  $db->Save_RL=0; // Отключаем сохранение удаленных записей

}

function checkDbTable($tbname) {
global $db, $OBJECTS,$PROPERTIES,$F_ARRAY,$_CONFIG;
  if($_CONFIG["DB_TYPE"]!='mysql') return 0;
  if(!$tbname) return 0;
  $tbnames=$db->table_names();

  if(isset($PROPERTIES["conect"]) && !isset($tbnames[$PROPERTIES["tbname"]."catalogue"])) {
    $db->query("CREATE TABLE ".$PROPERTIES["tbname"]."catalogue ( pgid int(11) DEFAULT '0' NULL,   rowidd int(11) DEFAULT '0' NULL,   pgpid int(11) DEFAULT '0' NULL, INDEX ( pgid , rowidd));");
  }

  if(!in_array(strtolower($tbname),$tbnames)) {
    $quer="CREATE TABLE $tbname (id int(10) unsigned NOT NULL auto_increment,";
    foreach($OBJECTS as $k=>$v) if($k!="id") {
      $x=$v->mkDBFolder();
      if($x) $quer.="$k ".$x.",";
    }
    $quer.="lock_user varchar(20) NULL, lock_time int(11) DEFAULT '0' NULL, PRIMARY KEY (id));";
    $db->query($quer);

  } else {
    $farr=$F_ARRAY;
    $fX=$db->get_folders_name($tbname);
    foreach($fX as $k=>$v) {
      if(isset($farr[$k])) unset($farr[$k]);
    }
    foreach($farr as $k=>$v) {
      $q=$OBJECTS[$k]->mkDBFolder();
      if($q) {
        $db->query("ALTER TABLE $tbname ADD $k $q NULL");
        if($tbname=="catalogue" || $tbname=="content") {
          $db->query("ALTER TABLE ".$tbname."_fin ADD $k $q null");
          $db->query("ALTER TABLE ".$tbname."bakup ADD $k $q null");
        }
      }
    }
    if(isset($PROPERTIES["publication"]) && $PROPERTIES["publication"] && !isset($fX["LastPublAdmin"])) {
                   $db->query("ALTER TABLE ".$tbname." ADD LastPublAdmin int null");
                }
  }
  if(isset($PROPERTIES["indexes"])) {
    $keys=$db->get_keys_name($PROPERTIES["tbname"]);
    $indxs=$PROPERTIES["indexes"];
    foreach($indxs as $k=>$v) {
      if(isset($keys[$v])) {
        unset($keys[$v]);
        unset($indxs[$k]);
      }
    }
    if(count($keys)) {
      foreach($keys as $k=>$v) {
        $db->query("ALTER TABLE ".$PROPERTIES["tbname"]." DROP INDEX $k");
      }
    }
    if(count($indxs)) {
      foreach($indxs as $k=>$v) {
        $db->query("ALTER TABLE ".$PROPERTIES["tbname"]." ADD INDEX($v)");
      }
    }
  }
}

function mkConnectTable($id) {
global $db,$PROPERTIES,$EXT,$RES_PATH;
  if(!$PROPERTIES["tbname"]) return false;

  $tbnames=$db->table_names();
  if(!isset($tbnames[$PROPERTIES["tbname"]."catalogue"])) {
    $db->query("CREATE TABLE ".$PROPERTIES["tbname"]."catalogue ( pgid int(11) DEFAULT '0' NULL,   rowidd int(11) DEFAULT '0' NULL,   pgpid int(11) DEFAULT '0' NULL, INDEX ( pgid , rowidd));");
  }
  if($PROPERTIES["conect"]!="id") {
    $db->query("SELECT ".$PROPERTIES["conect"]." FROM ".$PROPERTIES["tbname"]." WHERE id='$id'");
    if($db->next_record()  && $db->Record[0]) $id=$db->Record[0];
  }
  if(!isset($PROPERTIES["alongconect"]) && (!isset($PROPERTIES["conecttext"]) || $PROPERTIES["conecttext"])) {
    global $UserButtons,$TEXTS;
    if(!isset($UserButtons)) $UserButtons=array();
    $UserButtons[]=array((isset($PROPERTIES["conecttext"])? $PROPERTIES["conecttext"]:$TEXTS["ConnectTextDefault"]),"checkPage(\"".$PROPERTIES["tbname"]."\",$id,\"".$EXT."\")",$RES_PATH."img/magnet.png");
  }
  return "";
}

function updown($tbname,$id,$idprev,$aft=-1) {
global $db;
global $updown;
global $_GET;
global $PROPERTIES;

  $db->query("SELECT * FROM $tbname WHERE id='$idprev'");
  if($db->next_record()) {
    $rec=$db->Record;
    if(!isset($PROPERTIES["orderby"])) $PROPERTIES["orderby"]="id";
    $db->query("SELECT id FROM $tbname ORDER BY ".$PROPERTIES["orderby"]);
    $log=0;
    $ids=array();
    while($db->next_record()) {
      if(!$log && $db->Record[0]==$id) $log=1;
      elseif(!$log && $db->Record[0]==$idprev) $log=2;
      if($log) {
        $ids[]=$db->Record[0];
        if($log==1 && $db->Record[0]==$idprev) break;
        if($log==2 && $db->Record[0]==$id) break;
      }
    }
    if($log==1) krsort($ids);
    $prev=0;
    foreach($ids as $v) {
      if(!$prev) $prev=$v;
      else {
        $db->query("DELETE FROM $tbname WHERE id='$prev'");
        $db->query("UPDATE $tbname SET id='$prev' WHERE id='$v'");
        $prev=$v;
      }
    }
    $ids=array();
    $vals=array();
    $rec["id"]=$id;
    foreach($rec as $k=>$v) if(is_string($k)) {
      $ids[]=$k;
      $vals[]=$v;
    }
    $db->query("INSERT INTO $tbname (".join(",",$ids).") VALUES ('".join("','",$vals)."')");
  }
  update_publ_pages($tbname,$idprev) ;
  update_publ_pages($tbname,$id) ;
}

function delete_img($ch,$delfile,$log=0, $dfile="") {
global $db, $PROPERTIES;
  $db->query("UPDATE ".$PROPERTIES["tbname"]." SET $delfile='' WHERE id='$ch'");
  if($dfile) unlink ($dfile);
  if(isset($_GET["delimg"]))  unset($_GET["delimg"]);
}

function onBeforeDbChanging() {
global $OBJECTS;
  foreach($OBJECTS as $v) if(isset($v->EVENTS) && isset($v->EVENTS["onBeforeDbChanging"])) {
    $v->onBeforeDbChanging();
  }
}

function onAfterDbChanging() {
global $OBJECTS;
  foreach($OBJECTS as $v) if(isset($v->EVENTS) && isset($v->EVENTS["onAfterDbChanging"])) {
    $v->onAfterDbChanging();
  }
}

function onScriptEnd() {
global $OBJECTS;
  foreach($OBJECTS as $v) if(isset($v->EVENTS) && isset($v->EVENTS["onScriptEnd"])) {
    $v->onScriptEnd();
  }
}

function ChangeTpl($tpl,$idRow) {
global $db;
    $db->query("SELECT tmpschema FROM templates WHERE id='".$tpl."'");
    if($db->next_record()) {
      $tmpSchema=$db->Record["tmpschema"];
      $db->query("SELECT id FROM content WHERE catalogue_id='$idRow' and (cpid='0' or cpid is NULL) ORDER BY id");
      if($db->num_rows() && $db->num_rows()>$tmpSchema) {
        $n=0;
        $delid=array();
        while($db->next_record()) {
          if($n<$tmpSchema) $n++;
          else $delid[]=$db->Record["id"];
        }
        if(count($delid)) {
          $db->query("DELETE FROM content WHERE catalogue_id='$idRow' and (id='".(join("' or id='",$delid))."' or cpid='".(join("' or cpid='",$delid))."')");
        }
      } else{
        $db->query("SELECT count(*) as cnt FROM content WHERE catalogue_id='$idRow' and cpid='0'");
        $db->next_record();
        if($db->Record["cnt"]<$tmpSchema) {
          for($i=0;$i<($tmpSchema-$db->Record["cnt"]);$i++)
            $db->query("INSERT INTO content (catalogue_id) VALUES ('$idRow')");
        }
      }
    }
}

// Функция импорта данных из csv
function importCsvData($tb,$im,$fn,$md="no"){
  global $db;

  $fp=fopen($fn,"r");
  $s=fread($fp,filesize($fn));
  fclose($fp);

  $s=explode("\n",$s);
  $l=explode(",",$im[1]);

  $l=count($l);

  if($md=='yes') $db->query("DELETE FROM $tb");

  foreach($s as $st) {
    $st=explode($im[0],trim($st));


    if(count($st)==$l) {
      $db->query("INSERT INTO $tb (".$im[1].") VALUES ('".join("','",$st)."')");

     // echo $db->LastQuery."<br>";
    }

  }
  //exit;

}