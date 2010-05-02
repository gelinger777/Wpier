<?
require_once "./autorisation.php";
/*DESCRIPTOR

Группы пользователей


tools: admin
version:0.1
author: 
*/

//HEAD//
$PROPERTIES=array(
"conecttext"=>"Доступ к страницам",
"filters_size"=>"1",
"pagetitle"=>"Группы пользователей",
"pg2pg"=>"10",
"step"=>"10",
"tbname"=>"usergroups",
"delalert"=>"К группе, возможно, привязаны пользователи системы. Удалить?",
"nolang"=>1
);

$F_ARRAY=Array (
"id" => "hidden||",
"grpname"=>"text| size=50 maxlength=|Название",
"grpdescript"=>"textarea| cols=70 rows=5|Описание",
"grpadminaccess"=>"checkbox|onclick='document.forms[0].submit()'|Доступ к администраторской части|",
"block_1"=>"block|separator_1|Права на редактирование модулей|1",
"separator_1"=>"separator|<br>|",
"block_2"=>"block|separator_2|Права доступа к модулям в пользовательской части|1",
"separator_2"=>"separator|<br>|",
);

$f_array=Array(
"id" => "*hide*",
"grpname"=>"Группа",
"grpdescript"=>"Описание",
"grpadminaccess"=>"Доступ в админ"
);
//ENDHEAD//

$tAccess=array("","accessread","accessadd","accesswrite","accessdel");

//$LOGO_STR="";

if(count($_POST)) {
  $F_ARRAY["grpaccess"]="textarea||";
  $_POST["grpaccess"]="";
  $sMODS=array();
  if(isset($_POST["modread"])) $sMODS["modread"]=$_POST["modread"];
  if(isset($_POST["modedit"])) $sMODS["modedit"]=$_POST["modedit"];
  if(isset($_POST["modadd"])) $sMODS["modadd"]=$_POST["modadd"];
  if(isset($_POST["moddel"])) $sMODS["moddel"]=$_POST["moddel"];
  
//print_r($sMODS);  
  
  if(count($sMODS)) $_POST["grpaccess"]=serialize($sMODS);
  $F_ARRAY_NODISPLAY=array("grpaccess");
}



if(isset($_POST["id"])) $_GET["ch"]=$_POST["id"];

if(isset($_GET["ch"]) || isset($_GET["new"])) {
  // Рисуем дерево за исключением инфосистемы ------------------------------
  $Taccess=array("",array(),array(),array(),array());
  for($i=1;$i<=4;$i++) {
    if(count($_POST)) {
      if(isset($_POST["acs$i"])) foreach($_POST["acs$i"] as $v) $Taccess[$i][$v]=1;
    } elseif(isset($_GET["ch"])) {
      $db->query("SELECT pg FROM ".$tAccess[$i]." WHERE grp='".intval($_GET["ch"])."'");
      while($db->next_record()) $Taccess[$i][$db->Record[0]]=1;
    }
  }

//$LOGO_STR.="78 :";$LOGO_TIME=time()+microtime();
  
  //$db->query("SELECT catalogue.title,catalogue.id,catalogue.pid,catalogue.cod FROM catalogue,mainmenu WHERE catalogue.cod=mainmenu.page or catalogue.pid>0 ORDER BY catalogue.pid, catalogue.id");
  $db->query("SELECT title,id,pid,cod FROM catalogue WHERE pid=0 ORDER BY id");
  $tree=array();
  while($db->next_record()) $tree[$db->Record["pid"]][$db->Record["id"]]=array($db->Record["cod"],$db->Record["title"]);

//$LOGO_STR.=((time()+microtime())- $LOGO_TIME)."\n";
  
  $lvl=0;
  $iRow=0;
  $cods=array();
  function ShowTree($ParentID, $lvl) { 
    global $tree; 
    global $lvl,$F_ARRAY,$INFO,$iRow,$Taccess,$cods; 
    $lvl++; 
    if(isset($tree[$ParentID])) {
        foreach($tree[$ParentID] as $k=>$v) if($v[0]!=$INFO) {
            $ID1 = $k;
            $cods[]=$v[0];
            $F_ARRAY["separator_2"].="<tr bgcolor='#ffffff'>
            <td style='padding-left:".(20*$lvl)."px'>".($lvl==1? '<b>'.$v[1].'</b>':$v[1])."</td>
            <td align=center><input type='checkbox' name='acs1[".$iRow."]' value='".$v[0]."' style='border:' ".(isset($Taccess[1][$v[0]])? "checked":"")."></td>
            <td align=center><input type='checkbox' name='acs2[".$iRow."]' value='".$v[0]."' style='border:' ".(isset($Taccess[2][$v[0]])? "checked":"")."></td>
            <td align=center><input type='checkbox' name='acs3[".$iRow."]' value='".$v[0]."' style='border:' ".(isset($Taccess[3][$v[0]])? "checked":"")."></td>
            <td align=center><input type='checkbox' name='acs4[".$iRow."]' value='".$v[0]."' style='border:' ".(isset($Taccess[4][$v[0]])? "checked":"")."></td></tr>";
            $iRow++;
          
            ShowTree($ID1, $lvl); 
            $lvl--;
        }
    }
  }

//$LOGO_STR.="111 :";$LOGO_TIME=time()+microtime();

  $F_ARRAY["separator_2"].="<script>logSelect_acs1=1;logSelect_acs2=1;logSelect_acs3=1;logSelect_acs4=1;</script><table border=0 cellpadding=2 cellspacing=1 bgcolor='#acacac' width='465'>
  <tr bgcolor='#efefef'><td align=center><b>Страница</b></td>
  <td align='center'><a style='cursor:default' onclick='selectAll(\"acs1\");'>просмотр</a></td>
  <td align='center'><a style='cursor:default' onclick='selectAll(\"acs2\");'>добавление</a></td>
  <td align='center'><a style='cursor:default' onclick='selectAll(\"acs3\");'>изменение</a></td>
  <td align='center'><a style='cursor:default' onclick='selectAll(\"acs4\");'>удаление</a></td></tr>";
  ShowTree(0, 0);
  $F_ARRAY["separator_2"].="</table>";
//$LOGO_STR.=((time()+microtime())- $LOGO_TIME)."\n";

  // Нужно проставить права на модули
  $pagesC=array();
  $Spec2Pages=array();

//$LOGO_STR.="127 :";$LOGO_TIME=time()+microtime();
 
  $db->query("SELECT catalogue_ID,spec FROM content WHERE catalogue_ID='".join("' or catalogue_ID='",$cods)."'");
  while($db->next_record()) {
    if(!isset($pagesC[$db->Record[0]])) $pagesC[$db->Record[0]]=array();
    $pagesC[$db->Record[0]][$db->Record[1]]=1;
    if(!isset($Spec2Pages[$db->Record[1]])) $Spec2Pages[$db->Record[1]]=array();
    $Spec2Pages[$db->Record[1]][]=$db->Record[0];
  }
  
  $s=array(1=>array(),2=>array(),3=>array(),4=>array());
  $pacs=array();
  if(isset($_POST["modread"])) $pacs["acs1"]=$_POST["modread"];
  if(isset($_POST["modedit"])) $pacs["acs2"]=$_POST["modedit"];
  if(isset($_POST["modadd"])) $pacs["acs3"]=$_POST["modadd"];
  if(isset($_POST["moddel"])) $pacs["acs4"]=$_POST["moddel"];

//$LOGO_STR.=((time()+microtime())- $LOGO_TIME)."\n";
  //print_r($Spec2Pages);
 
  if(count($_POST)) {
  
    if(!$_POST["id"]) {
      $db->query("INSERT INTO ".$PROPERTIES["tbname"]." (id) VALUES ('')");
      $_POST["id"]=getLastID();
      unset($_POST["ins"]);
      $_POST["upd"]=1;
    }
  
    for($i=1;$i<=4;$i++) {
     $db->query("DELETE FROM ".$tAccess[$i]." WHERE grp='".intval($_POST["id"])."' and (pg='".join("' or pg='",$cods)."')");
     if(isset($_POST["acs$i"])) {
       foreach($_POST["acs$i"] as $v) if(intval($v)) {
         $db->query("INSERT INTO ".$tAccess[$i]." (grp,pg) VALUES ('".intval($_POST["id"])."','".intval($v)."')");
         if(isset($pagesC[$v])) {
           foreach($pagesC[$v] as $kC=>$vC) {
             $s[$i][]=$kC;
           } 
         }
       }
     } elseif(isset($pacs["acs$i"])) {
  //print_r($pacs["acs$i"]);       
        foreach($pacs["acs$i"] as $val) if(isset($Spec2Pages[$val])) {
          foreach($Spec2Pages[$val] as $v) {
            $db->query("INSERT INTO ".$tAccess[$i]." (grp,pg) VALUES ('".intval($_POST["id"])."','".$v."')");
            if(isset($pagesC[$v])) {
              foreach($pagesC[$v] as $kC=>$vC) {
                $s[$i][]=$kC;
              }
            } 
          }
        }
     }
    }
  }
  
  
  $ss=array();
  if(!isset($_POST["grpadminaccess"])){
    if(count($s[1])) $ss["modread"]=$s[1];
    if(count($s[2])) $ss["modadd"]=$s[2]; 
    if(count($s[3])) $ss["modedit"]=$s[3];
    if(count($s[4])) $ss["moddel"]=$s[4];
    if(count($ss)) $_POST["grpaccess"]=serialize($ss);
  }
  // / Рисуем дерево за исключением инфосистемы ------------------------------

//$LOGO_STR.="194 :";$LOGO_TIME=time()+microtime();
  
  $ar=array();
  $Admin=0;
  if(isset($_GET["ch"])) {
    if(isset($sMODS)) $ar=$sMODS;
    else {
      $db->query("SELECT GrpAccess,GrpAdminAccess FROM usergroups WHERE id='".intval($_GET["ch"])."'");
      if($db->next_record()) {
        if($db->Record[0]) $ar=unserialize($db->Record[0]);
        $Admin=$db->Record[1];
      } 
    }      
  }
  
  if(count($_POST)) if(isset($_POST["grpadminaccess"])) $Admin=1;else $Admin=0;
  //$Admin=1;
  if($Admin) {
  include "./menu.inc";
  foreach($menu_tool as $k=>$v) $menu_items[$k]=array($v);
  $s="<script>logSelect_modread=1;logSelect_modedit=1;logSelect_modadd=1;logSelect_moddel=1;</script><table border='0' cellspacing='1' cellpadding='2' bgcolor='#acacac' width='465'>";
  $hd="<tr bgcolor='#efefef'>";
  $hd.="<td align=center><b>Модуль</b></td>";
  $hd.="<td align='center'><a style='cursor:default' onclick='selectAll(\"modread\");'>просм.</a></td>";
  $hd.="<td align='center'><a style='cursor:default' onclick='selectAll(\"modedit\");'>ред.</a></td>";
  $hd.="<td align='center'><a style='cursor:default' onclick='selectAll(\"modadd\");'>доб.</a></td>";
  $hd.="<td align='center'><a style='cursor:default' onclick='selectAll(\"moddel\");'>удал.</a></td>";
  $hd.="</tr>";
  $s.=$hd;
  $iRow=0;
  
  $k="page";
  $s.="<tr bgcolor='#ffffff'>";
  $s.="<td><b>Управление страницами</b></td>";
  $s.="<td align=center><input type='checkbox' name='modread[$iRow]' value='$k' style='border:' ".((isset($ar["modread"]) && in_array($k,$ar["modread"]))? "checked":"")."></td>";
  $s.="<td align=center><input type='checkbox' name='modedit[$iRow]' value='$k' style='border:' ".((isset($ar["modedit"]) && in_array($k,$ar["modedit"]))? "checked":"")."></td>";
  $s.="<td align=center><input type='checkbox' name='modadd[$iRow]' value='$k' style='border:' ".((isset($ar["modadd"]) && in_array($k,$ar["modadd"]))? "checked":"")."></td>";
  $s.="<td align=center><input type='checkbox' name='moddel[$iRow]' value='$k' style='border:' ".((isset($ar["moddel"]) && in_array($k,$ar["moddel"]))? "checked":"")."></td>";
  $s.="</tr>";
  $iRow++;
  $k="page_content";
  $s.="<tr bgcolor='#ffffff'>";
  $s.="<td><b>Управление блоками</b></td>";
  $s.="<td align=center><input type='checkbox' name='modread[$iRow]' value='$k' style='border:' ".((isset($ar["modread"]) && in_array($k,$ar["modread"]))? "checked":"")."></td>";
  $s.="<td align=center><input type='checkbox' name='modedit[$iRow]' value='$k' style='border:' ".((isset($ar["modedit"]) && in_array($k,$ar["modedit"]))? "checked":"")."></td>";
  $s.="<td align=center><input type='checkbox' name='modadd[$iRow]' value='$k' style='border:' ".((isset($ar["modadd"]) && in_array($k,$ar["modadd"]))? "checked":"")."></td>";
  $s.="<td align=center><input type='checkbox' name='moddel[$iRow]' value='$k' style='border:' ".((isset($ar["moddel"]) && in_array($k,$ar["moddel"]))? "checked":"")."></td>";
  $s.="</tr>";
  $iRow++;
  $k="chmod";
  $s.="<tr bgcolor='#ffffff'>";
  $s.="<td><b>Управление правами доступа</b></td>";
  $s.="<td align=center><input type='checkbox' name='modread[$iRow]' value='$k' style='border:' ".((isset($ar["modread"]) && in_array($k,$ar["modread"]))? "checked":"")."></td>";
  $s.="<td align=center><input type='checkbox' name='modedit[$iRow]' value='$k' style='border:' ".((isset($ar["modedit"]) && in_array($k,$ar["modedit"]))? "checked":"")."></td>";
  $s.="<td align=center><input type='checkbox' name='modadd[$iRow]' value='$k' style='border:' ".((isset($ar["modadd"]) && in_array($k,$ar["modadd"]))? "checked":"")."></td>";
  $s.="<td align=center><input type='checkbox' name='moddel[$iRow]' value='$k' style='border:' ".((isset($ar["moddel"]) && in_array($k,$ar["moddel"]))? "checked":"")."></td>";
  $s.="</tr>";
  $iRow++;
  include "./tools.inc";
  foreach($USER_TOOLS as $v) if(!$v[4]) {
    $k=substr($v[0],strrpos($v[0],"/")+1);
    $k=substr($k,0,strpos($k,"."));
    $s.="<tr bgcolor='#ffffff'>";
    $s.="<td><b>".$v[2]."</b></td>";
    $s.="<td align=center><input type='checkbox' name='modread[$iRow]' value='$k' style='border:' ".((isset($ar["modread"]) && in_array($k,$ar["modread"]))? "checked":"")."></td>";
    $s.="<td align=center><input type='checkbox' name='modedit[$iRow]' value='$k' style='border:' ".((isset($ar["modedit"]) && in_array($k,$ar["modedit"]))? "checked":"")."></td>";
    $s.="<td align=center><input type='checkbox' name='modadd[$iRow]' value='$k' style='border:' ".((isset($ar["modadd"]) && in_array($k,$ar["modadd"]))? "checked":"")."></td>";
    $s.="<td align=center><input type='checkbox' name='moddel[$iRow]' value='$k' style='border:' ".((isset($ar["moddel"]) && in_array($k,$ar["moddel"]))? "checked":"")."></td>";
    $s.="</tr>";
    $iRow++;
  }
  
  foreach($menu_items as $k=>$v) {
    $s.="<tr bgcolor='#ffffff'>";
    $s.="<td>".$v[0]."</td>";
    $s.="<td align=center><input type='checkbox' name='modread[$iRow]' value='$k' style='border:' ".((isset($ar["modread"]) && in_array($k,$ar["modread"]))? "checked":"")."></td>";
    $s.="<td align=center><input type='checkbox' name='modedit[$iRow]' value='$k' style='border:' ".((isset($ar["modedit"]) && in_array($k,$ar["modedit"]))? "checked":"")."></td>";
    $s.="<td align=center><input type='checkbox' name='modadd[$iRow]' value='$k' style='border:' ".((isset($ar["modadd"]) && in_array($k,$ar["modadd"]))? "checked":"")."></td>";
    $s.="<td align=center><input type='checkbox' name='moddel[$iRow]' value='$k' style='border:' ".((isset($ar["moddel"]) && in_array($k,$ar["moddel"]))? "checked":"")."></td>";
    $s.="</tr>";
    $iRow++;
  }
  $s.="$hd</table>";


  
  $F_ARRAY["separator_1"].=$s;
  unset($F_ARRAY["separator_2"]);
  unset($F_ARRAY["block_2"]);
  } else {
    unset($F_ARRAY["separator_1"]);
    unset($F_ARRAY["block_1"]);
  }
}
//$LOGO_STR.=((time()+microtime())- $LOGO_TIME)."\n";

function user_function() {
  global $db,$FORM_EXTEND,$id;
 
  if(!isset($id) || !$id) return "";
  if(!isset($_GET["ch"]))   $_GET["ch"]=$id;
global $LOGO_STR;
$LOGO_STR.="295 :";$LOGO_TIME=time()+microtime();  
  $ids_all=array();
  $ids_read=array();
  $ids_write=array();
  $db->query("SELECT catalogue.id FROM catalogue,accessread WHERE catalogue.cod=accessread.pg and accessread.grp='".$_GET["ch"]."'");
 
  while($db->next_record()) $ids_read[]=$db->Record[0];
  
  $ids_all=$ids_read;
  
  $db->query("SELECT catalogue.id FROM catalogue,accesswrite WHERE catalogue.cod=accesswrite.pg and accesswrite.grp='".$_GET["ch"]."'");
  while($db->next_record()) {
    $ids_write[]=$db->Record[0];
    if(!isset($db->Record[0],$ids_all)) $ids_all[]=$db->Record[0];  }
  
  if(isset($_GET["ch"])) {
    $FORM_EXTEND='<tr><td></td><td>';
    $FORM_EXTEND.='<input type="button" value="показать доступные страницы" onclick="parent.chmainitem(\'td_struct\');parent.document.frames[\'mainmenu\'].SetSelectItems(\''.join(",",$ids_all).'\',\'none\');parent.document.frames[\'mainmenu\'].SetSelectItems(\''.join(",",$ids_read).'\',\'#55cc55\');parent.document.frames[\'mainmenu\'].SetSelectItems(\''.join(",",$ids_write).'\',\'#ffaaaa\');">&nbsp;&nbsp;';
    
    $FORM_EXTEND.='<input type="button" value="убрать выделение" onclick="parent.document.frames[\'mainmenu\'].SetSelectItems(\''.join(",",$ids_all).'\',\'none\');"><br>';
    
    $FORM_EXTEND.='<br><table border=0><tr>';
    $FORM_EXTEND.='<td bgcolor="#55cc55" width="25">&nbsp;</td><td> - только чтение</td>';
    $FORM_EXTEND.='<td bgcolor="#ffaaaa" width="25">&nbsp;</td><td> - чтение и запись</td>';
    $FORM_EXTEND.='</table><br>';
    
       
  } else {
    echo "<script>parent.document.frames['mainmenu'].SetSelectItems('".join(",",$ids_all)."','none');</script>";
  }
//$LOGO_STR.=((time()+microtime())- $LOGO_TIME)."\n";
}

require ("./output_interface.php");

/*$fp=fopen($_SERVER["DOCUMENT_ROOT"]."/userfiles/group.log","w+");
fwrite($fp,$LOGO_STR);
fclose($fp);
*/
?>