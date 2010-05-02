<?






function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
  
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
  
    $bytes /= pow(1024, $pow);
  
    return round($bytes, $precision) . ' ' . $units[$pow];
} 

function goToItselfLink($intab, $HTML_FILE) {
global $CurrentId,$db; 
  $db->query("SELECT pgid FROM $intab WHERE rowidd='".intval($HTML_FILE)."' and pgpid='$CurrentId'");
  if($db->next_record()) $db->query("DELETE FROM $intab WHERE pgid='".mkPathFromCod($db->Record["pgid"])."'");
}

function GetEditableDbRecords($edt) {
	global $db;
	foreach($edt as $k) if(isset($db->Record[$k])) {
		$db->Record[$k]=$db->RecordEdit[$k];
	}
	return $db->Record;
}

function mkSpecBlock($specName,$pageCod=0,$pageDir="",$pageHTML=0,$cmpWhere="",$privat="") {

  $BLK="";
 
 $privat="./webadmin/extensions/$specName.php";
 
  $fp=fopen($privat,"r");
  $seval=fread($fp,filesize($privat));
  fclose($fp);
  $spos1=strpos($seval,"//HEAD//");
  $spos2=strpos($seval,"//ENDHEAD//");
  $seval=substr($seval,$spos1,$spos2-$spos1);
  //if(isset($MAINFRAME) && $MAINFRAME)  eval($seval);// or write2errorLog ($privat);
  //else 
//echo $seval."<hr>";  
  eval($seval) ;
  if($cmpWhere) eval(str_replace("&#39;","'",str_replace('&quot;','"',$cmpWhere)));

$tpl="";
if(isset($PROPERTIES["template_list"]) && $PROPERTIES["template_list"]) $tpl=$PROPERTIES["template_list"];
elseif(isset($PROPERTIES["template_row"]) && $PROPERTIES["template_row"]) $tpl=$PROPERTIES["template_row"];




  
  
  if(!trim($PROPERTIES["tbname"])) return "";

  if(isset($PROPERTIES["alongconect"]) && isset($PROPERTIES["conect"])) {
    $alongconect=$PROPERTIES["conect"];
  }
  if(isset($PROPERTIES["editable"])) {
    $PROPERTIES["editable"]=explode(",",$PROPERTIES["editable"]);
  } else $PROPERTIES["editable"]=array();

  if(isset($PROPERTIES["alongconect"]) && isset($alongconect)) {
    $this->db->query("SELECT rowidd FROM ".$PROPERTIES["tbname"]."catalogue WHERE pgID='$CurrentCod' LIMIT 1");
    if($db->next_record()) {
      $pageHTML=$db->Record[0];      
    } else return "";
  }
  
  

  /*$fp=array();
  foreach($F_ARRAY as $k=>$v) {
    if(is_array($v)) {
      foreach($v as $key=>$val) $fp[$key]=$val;
    } else $fp[$k]=$v;
  }
  $F_ARRAY=$fp; */

  include "./".$_CONFIG["ADMINDIR"]."/inc/mkObjects.php";

 
  
  if(!isset($PROPERTIES["nolang"])) {
    $fOUT=mkLangArrays($F_ARRAY,$f_array,$LANG);
    $F_ARRAY=$fOUT[0];
    $f_array=$fOUT[1];
    $assArr=$fOUT[2];
  }



  if(!$pageHTML && isset($PROPERTIES["template_list"]) && $PROPERTIES["template_list"]) {


    if(!$BLK) $BLK=parse_template($TemplatesPath."spec/".$PROPERTIES["template_list"]);
    
    $LIST=parse_tmp("LIST","BLK");
    $sql="";

    // Если эта переменная определена в тюнинге, нужно вывести список всех записей этого модула на дочерних страницах
    if(isset($ReadModList) && isset($PROPERTIES["conect"])) {
      $cods=array();
      $db->query("SELECT id FROM catalogue_fin WHERE pid='".$CurrentId."'");
      while($db->next_record()) $cods[]=$db->Record[0];
      
      $sql=",".$PROPERTIES["tbname"]."catalogue ".(isset($PROPERTIES["usrleftjoin"])? $PROPERTIES["usrleftjoin"]:"")." WHERE ".((isset($PROPERTIES["publication"]) && $PROPERTIES["publication"])? $PROPERTIES["tbname"].".LastPublAdmin='0' and ":"").$PROPERTIES["tbname"]."catalogue.rowidd=".$PROPERTIES["tbname"].".".$PROPERTIES["conect"]." and (".$PROPERTIES["tbname"]."catalogue.pgID='".join("' or ".$PROPERTIES["tbname"]."catalogue.pgID='",$cods)."')";


    } elseif(isset($PROPERTIES["conect"])) {
      $sql=",".$PROPERTIES["tbname"]."catalogue ".(isset($PROPERTIES["usrleftjoin"])? $PROPERTIES["usrleftjoin"]:"")." WHERE ".((isset($PROPERTIES["publication"]) && $PROPERTIES["publication"])? "(".$PROPERTIES["tbname"].".LastPublAdmin='0' or ".$PROPERTIES["tbname"].".LastPublAdmin is NULL) and ":"").$PROPERTIES["tbname"]."catalogue.rowidd=".$PROPERTIES["tbname"].".".$PROPERTIES["conect"]." and ".$PROPERTIES["tbname"]."catalogue.pgID='$pageCod'";
    } elseif(isset($PROPERTIES["publication"]) && $PROPERTIES["publication"]) {
      $PROPERTIES["usrwhere"]=(isset($PROPERTIES["usrwhere"])? $PROPERTIES["usrwhere"]." and (".$PROPERTIES["tbname"].".LastPublAdmin='0' or ".$PROPERTIES["tbname"].".LastPublAdmin is NULL)":" ".(isset($PROPERTIES["usrleftjoin"])? $PROPERTIES["usrleftjoin"]:"")." ".(isset($PROPERTIES["usrleftjoin"])? $PROPERTIES["usrleftjoin"]:"")." WHERE (".$PROPERTIES["tbname"].".LastPublAdmin='0' or ".$PROPERTIES["tbname"].".LastPublAdmin is NULL) ");
    }
    
    if(isset($PROPERTIES["usrwhere"])) $sql.=" ".$PROPERTIES["usrwhere"];

    if(isset($PROPERTIES["step"])) $step=$PROPERTIES["step"];
    else $step=10;
    if(isset($PROPERTIES["pg2pg"])) $pg2pg=$PROPERTIES["pg2pg"];
    else $pg2pg=10;

    $list=array();
    $folds=$db->folders_names($PROPERTIES["tbname"]);
    foreach($f_array as $k=>$v) if(isset($folds[$k]) || isset($folds[$LANG.$k])) {
      if($LANG && strpos(" $k", $LANG)==1) $list[]=$PROPERTIES["tbname"].".$k as ".substr($k,strlen($LANG));
      else $list[]=$PROPERTIES["tbname"].".$k";
    }

    if(!isset($_GET[$specName."_ob"]) && isset($PROPERTIES["usrorderby"])) $ob=$PROPERTIES["usrorderby"];
    elseif(isset($_GET[$specName."_ob"])) $ob=$_GET[$specName."_ob"];
    elseif(isset($_GET["orderby"])) $ob=$_GET["orderby"];
    
    if(isset($PROPERTIES["keywords"]) && $PROPERTIES["keywords"]) {
      global $LINK_KEYWORDS;
      
      if(isset($LINK_KEYWORDS) && is_array($LINK_KEYWORDS) && count($LINK_KEYWORDS))       
        $sql=($sql? $sql." and ":" WHERE ")." id in (SELECT keywordslink.cod FROM keywords,keywordslink WHERE keywordslink.modname='$specName' and keywords.wrd in ('".strip_tags(join("','",$LINK_KEYWORDS))."') and keywords.id=keywordslink.wcod)";
      
    }

    $sql="SELECT ".(isset($PROPERTIES["usrfolders"])? $PROPERTIES["usrfolders"]:join(",",$list))." FROM ".$PROPERTIES["tbname"].$sql."  ORDER BY ".(isset($ob)? $ob:"id");
     
    unset($ob);
   
    $db->query($sql,"",$step,$pg2pg);
    
   //echo $db->LastQuery;

//echo "UW=".$PROPERTIES["usrwhere"]."<br>";$sql;  
    if(isset($PROPERTIES["openone"]) && $PROPERTIES["openone"] && $db->num_rows()==1 && $db->next_record()) {
      if(isset($PROPERTIES["FIX_ID_TO_COD"])) {
        $pageHTML=$db->Record[$PROPERTIES["FIX_ID_TO_COD"]];
      }
      else {
        $pageHTML=$db->Record["id"];
      }
      $BLK=""; 
    }

 
    if(!$pageHTML) {

      send2blk("num_rows",$db->num_rows());
     
      $list="";
      $cods=array();
      $records=array();
      while($db->next_record()) {    
      
                
        $records[]=GetEditableDbRecords($PROPERTIES["editable"]);//$db->Record;
        if(isset($alongconect)) $cods[]=$db->Record[$alongconect];
      }


      
      send2blk("count_rows",count($records));
      // Чтение директорий для страниц с одиночной привязкой
      if(count($cods)) {
        $db->query("SELECT catalogue_fin.dir,".$PROPERTIES["tbname"]."catalogue.rowidd FROM catalogue_fin, ".$PROPERTIES["tbname"]."catalogue WHERE catalogue_fin.id= ".$PROPERTIES["tbname"]."catalogue.pgid and (".$PROPERTIES["tbname"]."catalogue.rowidd='".join("' or ".$PROPERTIES["tbname"]."catalogue.rowidd='",$cods)."')");
        $cods=array();
        while($db->next_record()) {
        $cods[$db->Record["rowidd"]]=$db->Record["dir"];
        }
      }
      global $WRITED_ROWS;
      $WRITED_ROWS=array();

       

   
      foreach($records as $Record) {
        $st=$LIST;
        if(isset($PROPERTIES["FIX_ID_TO_COD"])) $WRITED_ROWS[]=$Record[$PROPERTIES["FIX_ID_TO_COD"]];
        else $WRITED_ROWS[]=$Record["id"];
 
        foreach($f_array as $k=>$v) if(isset($OBJECTS[$k]) || (isset($assArr[$k]) && isset($OBJECTS[$assArr[$k]]))) {
        
          if(!isset($OBJECTS[$k]) && isset($OBJECTS[$assArr[$k]])) $k=$assArr[$k];
        
        
          if(!array_key_exists($k,$Record)) {
            if(isset($PROPERTIES["FIX_ID_TO_COD"])) $v=$Record[$PROPERTIES["FIX_ID_TO_COD"]];
            else $v=$Record["id"];
          } else $v=$Record[$k];
          
   
          $x=$OBJECTS[$k]->mkList($v,1);
          $st=str_replace("%".(isset($assArr[$k])? $assArr[$k]:$k)."%",$x,TMP_if_blocks($st,(isset($assArr[$k])? $assArr[$k]:$k),($x? 1:0)));
       
          if($x!=$v) {
            $st=str_replace("%".(isset($assArr[$k])? $assArr[$k]:$k)."_Code%",$v,$st);
          }
        }
        if(isset($alongconect) && isset($cods[$Record[$alongconect]])) $st=str_replace("%catdir%",$cods[$Record[$alongconect]],$st);
        $list.=$st;
        
      }
      return str_replace("%LIST%",$list,TMP_if_blocks($BLK,"LIST",($list? 1:0)));
    }
  }




  if($pageHTML) {
 

  
    $pageHTML=explode(".",$pageHTML);
    $pageHTML=$pageHTML[0];
    if(isset($PROPERTIES["conect"])) {
      goToItselfLink($PROPERTIES["tbname"]."catalogue", $pageHTML);      
    }


    if(isset($PROPERTIES["template_row"])) {   
      if(!$BLK && $PROPERTIES["template_row"]) $BLK=parse_template($TemplatesPath."spec/".$PROPERTIES["template_row"]);
      else $BLK="";
      $db->query("SELECT * FROM ".$PROPERTIES["tbname"]." WHERE ".(isset($PROPERTIES["FIX_ID_TO_COD"])? $PROPERTIES["FIX_ID_TO_COD"]:"id")."='".addslashes($pageHTML)."'");
      
     
      if($db->next_record()) {
        
        
        
        $rec=GetEditableDbRecords($PROPERTIES["editable"]);
            if(isset($PROPERTIES["BLOCKTITLE"]) && isset($rec[$PROPERTIES["BLOCKTITLE"]])) {
          make_out("blockTitle",$rec[$PROPERTIES["BLOCKTITLE"]]);
        }
        if(isset($PROPERTIES["BLOCKKEYWORDS"]) && isset($rec[$PROPERTIES["BLOCKKEYWORDS"]])) {
          make_out("blockKeywords",strip_tags($rec[$PROPERTIES["BLOCKKEYWORDS"]]));
        }
        
        if(isset($PROPERTIES["PathCurrent"]) && isset($rec[$PROPERTIES["PathCurrent"]])) {
          global $CURPATH;
          make_out("CURPATH",(isset($PROPERTIES["PathCurrentPref"])? $PROPERTIES["PathCurrentPref"]:"").str_replace('%title%',$rec[$PROPERTIES["PathCurrent"]],$CURPATH));
        } elseif(isset($PROPERTIES["PathCurrentPref"])) $CURPATH=$PROPERTIES["PathCurrentPref"].$CURPATH; 
  
        if($LANG) {
          foreach($rec as $k=>$v) if(!intval($k)) {
            if(isset($rec[$LANG.$k])) {
              $rec[$k]=$rec[$LANG.$k];
              unset($rec[$LANG.$k]);
            }
          }
        }
      
        // Делаем список кейвордов
        if(isset($PROPERTIES["keywords"]) && $PROPERTIES["keywords"]) {
          $KEYWORDS=parse_tmp("KEYWORDS","BLK");
          if($KEYWORDS) {
            $db->query("SELECT keywords.id, keywords.wrd FROM keywords,keywordslink WHERE keywordslink.cod='".$rec["id"]."' and keywordslink.modname='$specName' and keywordslink.wcod=keywords.id ORDER BY keywords.wrd");
            $list="";
            while($db->next_record(1)) {
              $list.= sendAr2blk($db->Record,$KEYWORDS);
            }
            send2blk("KEYWORDS",$list);
          }
        }
        // K keywords
                 
        foreach($F_ARRAY as $k=>$v) {
          
          if(!isset($OBJECTS[$k]) && isset($assArr[$k]) && isset($OBJECTS[$assArr[$k]])) $k=$assArr[$k];
          
          if(!array_key_exists($k,$rec)) {
            if(isset($PROPERTIES["FIX_ID_TO_COD"])) $v=$rec[$PROPERTIES["FIX_ID_TO_COD"]];
            else $v=$rec["id"];
          } else $v=$rec[$k];
          if(isset($OBJECTS[$k]) && method_exists($OBJECTS[$k],'mkList')) {
  
          $x=$OBJECTS[$k]->mkList($v,1);
            
            //$x=$OBJECTS[$k]->mkList($v,1);
          
            send2blk(((is_array($assArr) && isset($assArr[$k]))? $assArr[$k]:$k),$x);
            if(isset($PROPERTIES["pageTitle"]) && $k==$PROPERTIES["pageTitle"])
              $pageTitle=$x;
            if(isset($PROPERTIES["wintitle"]) && $k==$PROPERTIES["wintitle"])
              $winTitle=$x;   
            if($x!=$v) send2blk(((is_array($assArr) && isset($assArr[$k]))? $assArr[$k]:$k)."_Code",$v);
          }
        }
        
        
        unset($PROPERTIES);
        
      }
                 
      
      
      return $BLK;
      //return "";
    }
  }  
}


?>