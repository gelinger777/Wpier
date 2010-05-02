<?
if($BLOCKS_MODULS[$BLOCKS_V["spec"]]) {

$BLOCK_MOD_FILE=$BLOCKS_V["spec"].".php";
$INOUTER_LOG_X=0;
if($_USERDIR && file_exists("./www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/frontend/$BLOCK_MOD_FILE"))
  $BLOCK_MOD_FILE="./www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/frontend/$BLOCK_MOD_FILE";
elseif($_USERDIR && file_exists("./www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/$BLOCK_MOD_FILE")) {
  $BLOCK_MOD_FILE="./www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/$BLOCK_MOD_FILE";
  $INOUTER_LOG_X=1;
} elseif(file_exists("./".$_CONFIG["ADMINDIR"]."/extensions/frontend/$BLOCK_MOD_FILE"))  
  $BLOCK_MOD_FILE="./".$_CONFIG["ADMINDIR"]."/extensions/frontend/$BLOCK_MOD_FILE";
elseif(file_exists("./".$_CONFIG["ADMINDIR"]."/extensions/$BLOCK_MOD_FILE")) {
  $BLOCK_MOD_FILE="./".$_CONFIG["ADMINDIR"]."/extensions/$BLOCK_MOD_FILE";
  $INOUTER_LOG_X=1;
} 
if(isset($_SESSION['adminlogin'])) {
    
    $s=str_replace("/frontend/","/",$BLOCK_MOD_FILE);
    $fp=fopen($s,"r");
    $s=fread($fp,filesize($s));
    fclose($fp);
    $spos1=strpos($s,"//HEAD//");
    $spos2=strpos($s,"//ENDHEAD//");
    $s=substr($s,$spos1,$spos2-$spos1);
    eval($s);
	eval(str_replace("&#39;","'",str_replace('&quot;','"',$BLOCKS_V["cmpw"])));
    if(isset($EDITABLE_ROWS)) {
      $db->EditMode=$EDITABLE_ROWS;
      unset($EDITABLE_ROWS);
    }
    
    if(isset($PROPERTIES)) {
      if(!$BLOCKS_V["title"] && isset($PROPERTIES["pagetitle"])) $BLOCKS_V["title"]=$PROPERTIES["pagetitle"];
      if(isset($PROPERTIES["NOADD"]) && $PROPERTIES["NOADD"]) $BLOCKS_V["addmode"]=0;
    } 
}

ob_start();
$BLK=""; 
if(!$INOUTER_LOG_X) {  
  $CheckConnectTable="";
  eval($BLOCKS_V["cmpw"]);
  
  if($_CONFIG["CASH_STATUS"] && $BLOCKS_V["nocash"]) {
  // Если блок не кэшируется, в шаблон добавляем команду инклуда
     echo '<?$BLK="";include "'.$BLOCK_MOD_FILE.'";echo $BLK;?>';
  } else {
  // в противном случае, просто инклудим шаблон
     include $BLOCK_MOD_FILE;
  }
  
  if($CheckConnectTable && $HTML_FILE) {
    goToItselfLink($CheckConnectTable,$HTML_FILE);
  }
} 




if($INOUTER_LOG_X){
  
  if($_CONFIG["CASH_STATUS"] && $BLOCKS_V["nocash"]) {
  // Если блок не кэшируется, в шаблон добавляем вызов функции с нужными параметрами
     echo "<?echo mkSpecBlock('".$BLOCKS_V["spec"]."',$CurrentCod,'$CurrentDir','$HTML_FILE','".addslashes($BLOCKS_V["cmpw"])."','$_USERDIR');?>";
  } else {
  // в противном случае, просто выполняем функцию
     $BLK=mkSpecBlock($BLOCKS_V["spec"],$CurrentCod,$CurrentDir,$HTML_FILE,$BLOCKS_V["cmpw"],$_USERDIR);
  } 
          
  if($HTML_FILE) $HTML_FILE="";
}



$db->EditMode=array();

$BLK=ob_get_contents().$BLK;
ob_clean();
}

?>