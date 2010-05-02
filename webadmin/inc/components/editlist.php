<?
/*
*  Компонент "Связанный список с редактированием в текущем контексте"
*  Автор Тушев Максим
*  v. 1.0
*/

class T_editlist {
  var $PROP=array(
    "caption"=>"",
    "visibility"=>"yes",
    "name"=>"",
    "distTable"=>"",
    "distID"=>"",
    "selfID"=>"",
    "distFolds"=>array(),
    "width"=>200,
    "props"=>array("add","edit","del","updown"),
    "filter"=>array()
    );
  
  var $type="editlist";

  var $blockCod="";
  var $selectors=array();
  var $EVENTS=array(
    "onAfterDbChanging"=>1,
    "onBeforeDbChanging"=>1
      );

  function str2params ($name,$pString) {
    global $db;
    $this->PROP["name"]=$name;
    $pString=explode("|",$pString);
        
    $this->PROP["caption"]=$pString[2];

    $pString[1]=str_replace("\,","{**zpt**}",$pString[1]);
    $s=explode(",",$pString[1]);

    $this->PROP["distTable"]=$s[0];
    $this->PROP["distID"]=$s[1];
    $this->PROP["selfID"]=$s[2];
  
    for($i=3;$i<count($s);$i++) {
    
      $s[$i]=explode(":",str_replace("{**zpt**}",",",$s[$i])."::::");
      $this->PROP["distFolds"][$s[$i][0]]=array($s[$i][1],$s[$i][2],$s[$i][3]);
    }
//print_r( $this->PROP["distFolds"]); 
    if(isset($pString[3]) && $pString[3]) {
      $s=explode(",",$pString[3]);
      foreach($this->PROP["props"] as $k=>$v) {
       if(!in_array($v,$s)) unset($this->PROP["props"][$k]); 
      }
    }
    
    if(isset($pString[4])) { 
      $this->PROP["filter"]=explode(",",$pString[4]);
    }
  
   // Делаем селекторы
   foreach($this->PROP["distFolds"] as $k=>$v) {
     if($v[1]=="s") {
       $v[2]=explode("*",$v[2]);
        if(count($v[2])>=3) {
      
          if(strpos(" ".$v[2][2],"%")) {
            $a=array();
            $i=0;
            while($i<strlen($v[2][2])) {
              if($v[2][2][$i]=="%") {
                $i++;
                $f="";
                while($i<strlen($v[2][2]) && $v[2][2][$i]!="%") {
                  $f.=$v[2][2][$i];
                  $i++;
                }                
                if($f) $a[]=$f;
              }
              $i++;
            }
            $f=$v[2][2];
          } else {
            $f="%".$v[2][2]."%";
            $a=array($v[2][2]);
          } 
      
          $this->selectors[$k]=array();
          $db->query("SELECT ".$v[2][1].",".join(",",$a)." FROM ".$v[2][0]." ".(isset($v[2][3])? $v[2][3]:"ORDER BY id"));
    
          while($db->next_record()) {
            $this->selectors[$k][$db->Record[0]]=$f;
            foreach($db->Record as $kk=>$vv) 
             $this->selectors[$k][$db->Record[0]]=str_replace("%$kk%",$vv,$this->selectors[$k][$db->Record[0]]);
          }
        }
      }
    }
  }

  function params2str () {
    $s="editlist|".$this->PROP["distTable"].",".$this->PROP["distID"].",".$this->PROP["selfID"];
    foreach($this->PROP["distFolds"] as $k=>$v) $s.=",$k:".join(":",$v);
    $s.="|".$this->PROP["caption"];
    $s.="|".join(",",$this->PROP["props"]);
    if(count($this->PROP["filter"])) {
      $s.="|".join(",",$this->PROP["filter"]);
    }
    return $s;
  }

  function mkForm($key,$val="") {
  global $PROPERTIES, $db,$LOAD_EDITLIST_JS_LOG,$TEXTS,$_CONFIG;
  if(!isset($_GET["ch"])) $_GET["ch"]=0;
  $id=0;
  if($this->PROP["selfID"]=="id") $id=intval($_GET["ch"]);
  else {
    $db->query("SELECT ".$this->PROP["selfID"]." FROM ".$PROPERTIES["tbname"]." WHERE id='".intval($_GET["ch"])."'");
    if($db->next_record()) $id=$db->Record[0];
  }

  $form="";
  if(!isset($LOAD_EDITLIST_JS_LOG)) {
    $form.="<SCRIPT src='/".$_CONFIG["ADMINDIR"]."/js/editlist.js'></SCRIPT>";
    $LOAD_EDITLIST_JS_LOG=1;
  }
  $form.="<table id='editlist_".$this->PROP["name"]."' class='editlist'><tr id='EditListHead'>";
  if(!isset($_GET["prn"])) $form.="<td>&nbsp;</td>";
  foreach($this->PROP["distFolds"] as $k=>$v) {
    if($v[1]=='i') {$v[0]=explode("*",$v[0]);$v[0]=$v[0][0];}
    $form.="<td class='editlist_th'>".$v[0]."</td>";
  }
  //if(in_array("del",$this->PROP["props"])) {
  if(!isset($_GET["prn"])) $form.="<td class='editlist_th'>&nbsp;</td>";
  //}
  $form.="</tr>";

  $db->query("SELECT * FROM ".$this->PROP["distTable"]." WHERE ".$this->PROP["distID"]."='".$id."' ORDER BY id");
  while($db->next_record()) {
    $form.="<tr>";
    if(!isset($_GET["prn"])) $form.="<td><div style='CURSOR:default' onclick='return upRowEditList(this)' ><img src='/".$_CONFIG["ADMINDIR"]."/img/up.gif' width='15' height='15' alt='".$TEXTS["TreePopupUp"]."' /></div></td>";
    foreach($this->PROP["distFolds"] as $k=>$v) {

      if(isset($db->Record[$k])) $v=$db->Record[$k];
      else $v="";
    switch($this->PROP["distFolds"][$k][1]) {
        case "s":{
      $form.="<td><select name='".$this->PROP["name"]."_".$k."[]'><option value=''>".$TEXTS["Change"]."</option>";
      if(isset($this->selectors[$k])) foreach($this->selectors[$k] as $ks=>$vs) {
        $form.="<option value='$ks'".($v==$ks? " selected":"").">$vs</option>";
      }
      $form.="</select></td>";
    } break;
        case "i":$form.="<td><INPUT type='hidden' name='".$this->PROP["name"]."_".$k."[]' value='".$v."'>".($v? "<img src='".$v."' />":"")."</td>";break;
        case "f":$form.="<td><INPUT type='hidden' name='".$this->PROP["name"]."_".$k."[]' value='".$v."'>".($v? "<a href='$v' target='_blank'>$v</a>":"")."</td>";break;
        case "t":{
          if($this->PROP["distFolds"][$k][2]) $sz=explode("/",$this->PROP["distFolds"][$k][2]);
          else $sz=array(60,5);
          $form.="<td><TEXTAREA name='".$this->PROP["name"]."_".$k."[]' cols='".$sz[0]."' rows='".$sz[1]."'>".html_entity_decode($v)."</TEXTAREA></td>";
        } break;
        
        case "c":$form.="<td><SELECT name='".$this->PROP["name"]."_".$k."[]'><OPTION value='0'>".$TEXTS["No"]."<OPTION value='1' ".($v? "selected":"").">".$TEXTS["Yes"]."</select></td>";break;
        
        default: $form.="<td><INPUT type='text' name='".$this->PROP["name"]."_".$k."[]' value='".$v."' size='".$this->PROP["distFolds"][$k][1]."'></td>";
      }
    }
        $form.=(in_array("del",$this->PROP["props"]) && !isset($_GET["prn"])? "<td class='editlist_td' align='center'><a style='cursor:hand'  onclick='return delRowEditList(this)'><img src='/".$_CONFIG["ADMINDIR"]."/img/del.gif' width='11' height='11' alt='".$TEXTS["Delete"]."' /></a></td>":"")."</tr>";
  }

  $form.="<tr style='display:none'><td><a style='cursor:hand' onclick='return upRowEditList(this)' ><img src='/".$_CONFIG["ADMINDIR"]."/img/up.gif' width='15' height='15' alt='".$TEXTS["TreePopupUp"]."' /></a></td>";
    foreach($this->PROP["distFolds"] as $k=>$v) {
    switch($v[1]) {
     case "s":{
      $form.="<td><select name='".$this->PROP["name"]."_".$k."[]'><option value=''>".$TEXTS["Change"]."</option>";
      if(isset($this->selectors[$k])) foreach($this->selectors[$k] as $ks=>$vs) {
   $form.="<option value='$ks'>$vs</option>";
      }
      $form.="</select></td>";
     } break;
     case "i":$form.="<td><INPUT type='file' name='".$this->PROP["name"]."_".$k."[]' style='width:120px'></td>";break;
     case "f":$form.="<td><INPUT type='file' name='".$this->PROP["name"]."_".$k."[]'></td>";break;
     case "t":{
      if($v[2]) $sz=explode("/",$v[2]);
      else $sz=array(60,5);
      $form.="<td><TEXTAREA name='".$this->PROP["name"]."_".$k."[]' cols='".$sz[0]."' rows='".$sz[1]."'></TEXTAREA></td>";
     }break;
     case "c":$form.="<td><SELECT name='".$this->PROP["name"]."_".$k."[]'><OPTION value='0'>".$TEXTS["No"]."<OPTION value='1'>".$TEXTS["Yes"]."</select></td>";break;
    
     default: $form.="<td><INPUT type='text' name='".$this->PROP["name"]."_".$k."[]' value='' size='".$this->PROP["distFolds"][$k][1]."'></td>";
    }
  }
   $form.=(in_array("del",$this->PROP["props"])? "<td class='editlist_td' align='center'><a style='cursor:hand'  onclick='return delRowEditList(this)'><img src='/".$_CONFIG["ADMINDIR"]."/img/del.gif' width='11' height='11' alt='".$TEXTS["Delete"]."' /></a></td>":"")."</tr>";

   $form.="</table>";
   
   if(!isset($_GET["prn"])) $form.="<table><tr><td><a href='#' onclick='return addRowEditList(\"editlist_".$this->PROP["name"]."\")' style='text-decoration:none'><b><big><big>+</big></big></a></td><td><a href='#' onclick='return addRowEditList(\"editlist_".$this->PROP["name"]."\")' style='text-decoration:none'><b>".$TEXTS["AddString"]."</b></a></td></tr></table>";
   return array("<table cellpadding=0 cellspacing=0 border=0 width='100%' height='100%'><tr><td valign='top'><b><nobr>".$this->PROP["caption"]."</nobr></b></td></tr></table>",$form);
  
  }

 function mkList($val,$log=0) {
    global $db,$BLK;
  if(isset($BLK) && !$this->blockCod) {
    $this->blockCod=parse_tmp(strtoupper($this->PROP["name"]),"BLK");      
  }

  $db->query("SELECT * FROM ".$this->PROP["distTable"]." WHERE ".$this->PROP["distID"]."='".$val."' ORDER BY id");

  $list="";
  if($this->blockCod) send2blk($this->PROP["name"]."_count",$db->num_rows());
  while($db->next_record()) {
    $s=$this->blockCod;
    foreach($db->Record as $k=>$v) {
      if(isset($this->PROP["distFolds"][$k])) {
        if($this->PROP["distFolds"][$k][1]=='f') $v=str_replace("../","/",$v);
        elseif($this->PROP["distFolds"][$k][1]=='s' && isset($this->selectors[$k]) && isset($this->selectors[$k][$v])) 
          $v=$this->selectors[$k][$v];
        if(!$this->blockCod) $s.="$v ";
      }
      if($this->blockCod) $s=str_replace("%$k%",$v,$s);
    }
    $list.=$s;
    if(!$this->blockCod) $list.="<br>"; 
  }
  return $list;
  }

  function mkDBFolder() {
    return "";
  }

  function getUpdateVals($key,$val) {
    return "";
  }

  function getFilterSearch($key,$val) {
    global $db;
    
    $w=array();
    foreach($val as $k=>$v) if(in_array($k,$this->PROP["filter"]) && trim($v)) 
      $w[]="$k like '%".htmlspecialchars($v)."%'";
    
    if(count($w)) {
      $db->query("SELECT ".$this->PROP["distID"]." FROM ".$this->PROP["distTable"]." WHERE ".join(" and ",$w));
     $w=array();  
      while($db->next_record()) $w[]=$db->Record[0];
      return "(".$this->PROP["selfID"]."='".join("' or ".$this->PROP["selfID"]."='",$w)."')";
    }
    return "";
  }

  function getFilter($key,$val) {

    $s="";
    foreach($this->PROP["filter"] as $v) {
      $vl="";
      if(isset($val[$v])) $vl=$val[$v];
      if($this->PROP["distFolds"][$v][1]=="s") {
        $s.="<select style='width:100%' name='".$key."[".$v."]' title='".$this->PROP["distFolds"][$v][0]."'><option value=''>".$this->PROP["distFolds"][$v][0]."</option>";
        foreach($this->selectors[$v] as $kk=>$vv) 
          $s.="<option value='$kk'".($vl==$kk? " selected":"").">$vv</option>";
        $s.="</select><br>";
      }
      else $s.="<input style='width:100%' name='".$key."[".$v."]' value='".$vl."' title='".$this->PROP["distFolds"][$v][0]."'><br>";
    }
    return $s;
  }

  function onAfterDbChanging() {
  global $db,$id,$PROPERTIES,$HTTP_POST_FILES,$_USERDIR;
    if (isset($_POST["upd"]) || isset($_POST["ins"])) {
     $db->query("SELECT ".$this->PROP["selfID"]." FROM ".$PROPERTIES["tbname"]." WHERE id='$id'");
     if ($db->next_record()) {
       $kk=$db->Record[0];    
       $db->query("DELETE FROM ".$this->PROP["distTable"]." WHERE ".$this->PROP["distID"]."='".$kk."'");
     }
     if(!isset($kk) || !$kk) $kk=$id;
      
     $insAr=array();  
     foreach($_POST as $ke=>$va) if(strpos(" $ke",$this->PROP["name"])==1) {
       $ke=explode("_",$ke);
       unset($ke[0]);
       $ke=join("_",$ke);
       foreach($va as $k=>$v) {
         if(!isset($insAr[$k])) $insAr[$k]=array();
         $insAr[$k][$ke]=str_replace("'","&#39;",$v);
       }
     }
     
     if(isset($HTTP_POST_FILES)) foreach($HTTP_POST_FILES as $ke=>$va) 
     if(strpos(" $ke",$this->PROP["name"])==1) {
       if(isset($_POST[$ke]) && is_array($_POST[$ke])) $x=count($_POST[$ke]);
       else $x=0;
       $ke=explode("_",$ke);
       unset($ke[0]);
       $ke=join("_",$ke);
       foreach($va["tmp_name"] as $k=>$v) if(file_exists($v)) {
         if(!isset($insAr[$k+$x])) $insAr[$k+$x]=array();
         $insAr[$k+$x][$ke]=array($x);
       }
     }

     foreach($insAr as $key=>$val) {
      $ik=array();
      $iv=array();
      foreach($val as $k=>$v) if($v && isset($this->PROP["distFolds"][$k])) {
       switch($this->PROP["distFolds"][$k][1]) {
        case "i":if(is_array($v)) {
          $tmpFn=$this->PROP["name"]."_".$k;
          $tmpN=$key-$v[0];
          if (isset($HTTP_POST_FILES[$tmpFn]) && $HTTP_POST_FILES[$tmpFn]["tmp_name"][$tmpN]) {
           
            $userfile=array("tmp_name"=>$HTTP_POST_FILES[$tmpFn]["tmp_name"][$tmpN],"name"=> strtolower($HTTP_POST_FILES[$tmpFn]["name"][$tmpN]));
    
            $prp=explode("*",$this->PROP["distFolds"][$k][0]);
            if(count($prp)>1) {
              $sz=explode("x",$prp[3]);
              if(!isset($sz[1])) $sz[1]="";
              if(!isset($sz[2])) $sz[2]="";
              if(!isset($sz[3])) $sz[3]="";
              
              $wm="";
              if(isset($prp[5]) && file_exists($_SERVER["IPR_DIR"].$prp[5])) {
                $wm=$_SERVER["IPR_DIR"].$prp[5];  
              }
              
              if(!$sz[2] && !$sz[3]) {
                copy($userfile["tmp_name"],$_SERVER['DOCUMENT_ROOT'].($_USERDIR? "/www/$_USERDIR":"").$prp[1].$userfile["name"]);
                mkResizeImg($_SERVER['DOCUMENT_ROOT'].($_USERDIR? "/www/$_USERDIR":"").$prp[2].$userfile["name"],$userfile["tmp_name"],$sz[0]."x".$sz[1],$wm);
              } else {
                mkResizeImg($_SERVER['DOCUMENT_ROOT'].($_USERDIR? "/www/$_USERDIR":"").$prp[1].$userfile["name"], $userfile["tmp_name"], $sz[2]."x".$sz[3],$wm);
                mkResizeImg($_SERVER['DOCUMENT_ROOT'].($_USERDIR? "/www/$_USERDIR":"").$prp[2].$userfile["name"],$userfile["tmp_name"],$sz[0]."x".$sz[1]);
              }
      
               
               $v=($_USERDIR? "/www/$_USERDIR":"").($prp[2]? $prp[2]:$prp[1]).$userfile["name"];
             
            } else {
              $v=str_replace("../","/",copyuserfile($this->PROP["name"]."_".$k,"jpg,gif,jpeg,png",$tmpN));
            }
          }
        } break;
        case "f":if(is_array($v)) $v=copyuserfile($this->PROP["name"]."_".$k,$this->PROP["distFolds"][$k][2],($key-$v[0]));break;
       }
   
       $ik[]=$k;
       $iv[]="'".$v."'";
      }
      if(count($ik)) {
        $sql="INSERT INTO ".$this->PROP["distTable"]." (".$this->PROP["distID"].", ".join(",",$ik).") VALUES ('".$kk."', ".join(",",$iv).")";
        
        $db->query($sql);
      }
    }
   }
  }

  function onBeforeDbChanging() {
  global $db;
    $tables=$db->table_names();
    if(!isset($tables[$this->PROP["distTable"]])) {
              
    $sql=array("id int(10) unsigned NOT NULL auto_increment,".$this->PROP["distID"]." int(11)");
    foreach($this->PROP["distFolds"] as $k=>$v) if($k!=$this->PROP["distID"]) {
      $ff="varchar(50)";
    switch($v[1]) {
         case "i": $ff="varchar(50)";break;
         case "f":$ff="varchar(50)";break;
         case "d":$ff="varchar(8)";break;
     case "c":$ff="char(1)";break;
     case "s":$ff="int(11)";break;
     case "t":$ff="text";break;
      }
      $sql[]="$k $ff";
    }
    $sql[]="PRIMARY KEY (id)";
    $sql="CREATE TABLE ".$this->PROP["distTable"]." (".join(",",$sql).")";
    
    $db->query($sql);
  }
  }
}
?>