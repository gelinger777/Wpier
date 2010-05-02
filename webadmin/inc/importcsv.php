<?
  if (isset($HTTP_POST_FILES["csvPostTmpFile"])) {
    $userfile=$HTTP_POST_FILES["csvPostTmpFile"];
    if (file_exists($userfile["tmp_name"])) {
      
      if(isset($_POST["csvPostTmpDel"])) {
        $db->query("DELETE FROM ".$PROPERTIES["tbname"]);
      }
      
      $fp=fopen($userfile["tmp_name"],"r+");
      $str=fread($fp,filesize($userfile["tmp_name"]));
      fclose($fp);
      
      $PROPERTIES["importcsv"][1]=explode(",",$PROPERTIES["importcsv"][1]);
      
      $cnt=0;

      $str=explode("\n",$str);
      foreach($str as $s) {
        $s=trim($s);        
        if($s) {
          $sKeys=array();
          $sVals=array();
          $s=explode($PROPERTIES["importcsv"][0],$s);
          $log=1;
          if(isset($PROPERTIES["csvcontrol"])) {
             if($s[$PROPERTIES["csvcontrol"][0]]==$PROPERTIES["csvcontrol"][3]) $log=0;
             else $log=2;
             unset($s[$PROPERTIES["csvcontrol"][0]]);
             $x=array();
             foreach($s as $v) $x[]=$v;
             $s=$x;                        
            }
          $where="";
          foreach($PROPERTIES["importcsv"][1] as $k=>$v) {
            if(isset($s[$k]) && $v) {
              $v=trim($v);
              if($log!=1 && $v==$PROPERTIES["csvcontrol"][1]) $where="$v='".$s[$k]."'";
              $sKeys[]=$v;
              $sVals[]=$s[$k];
            }
          }
          if($where) $db->query("DELETE FROM ".$PROPERTIES["tbname"]." WHERE $where");  

          if(count($sKeys) && $log) {
            $cnt++;
            $db->query("INSERT INTO ".$PROPERTIES["tbname"]." (".join(",",$sKeys).") VALUES ('".join("','",$sVals)."')");
          }
        }
      }
      if($cnt) {?>

<SCRIPT LANGUAGE="JavaScript">
<!--
alert("Импорт завершен. Вставлено записей: <?=$cnt?>");
//-->
</SCRIPT>

    <?}
    }
  }
?>
&nbsp;<INPUT type='button' onclick='if(importCsvDiv.style.display=="none") importCsvDiv.style.display="";else importCsvDiv.style.display="none";' value='CSV' class='button'>
<div id="importCsvDiv" style="display:none">
<hr>
<FORM ENCTYPE='multipart/form-data'  METHOD=POST onsubmit="if(document.all('csvPostTmpDel').checked) return confirm('Текущие значения будут удалены из базы. Продолжить?');">
<input type='file' name='csvPostTmpFile'>
<input type='checkbox' name='csvPostTmpDel' checked> Замена 
<input type='submit' value='Импорт CSV'>
</FORM>
</div>