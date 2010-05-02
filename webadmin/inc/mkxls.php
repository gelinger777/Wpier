<?
// ob_start();

$xlscook=explode("|",$_COOKIE["_xls"]);
$ft=substr($xlscook[0],strrpos($xlscook[0],"."));

$xlscook[1]=explode(",",$xlscook[1]);

if(in_array($ft,array('.xls','.csv','.txt','.xml'))) {

if($ft=='.xls') {

$db->query($sql);
$RECS=array();
$i=0;
while($db->next_record()) {
  $RECS[$i]=array();
  foreach($db->Record as $k=>$v) if(is_string($k)) {
    $RECS[$i][$k]=$v;
  }
  $i++;
}   

// Тут генерим xls
  include_once($_SERVER["DOCUMENT_ROOT"].'/'.$_CONFIG["ADMINDIR"].'/pear/PEAR.php');
  include_once($_SERVER["DOCUMENT_ROOT"].'/'.$_CONFIG["ADMINDIR"].'/pear/spreadsheet/excel/Writer.php');

  $xls = new Spreadsheet_Excel_Writer($_SERVER["DOCUMENT_ROOT"].$xlscook[0]);
  
  $xls->setVersion(8);

  $black_9_format =& $xls->addFormat();
  $black_9_format->setSize(9);
  $black_9_format->setColor("black");

  $black_9_format_numb =& $xls->addFormat();
  $black_9_format_numb->setSize(9);
  $black_9_format_numb->setColor("black");
  $black_9_format_numb->setNumFormat('000');

  $black_bold_format = & $xls->addFormat();
  $black_bold_format->setSize(9);
  $black_bold_format->setBold();
  $black_bold_format->setColor("brown");   

  $sheet =& $xls->addWorksheet('Page 1');
  $sheet->setInputEncoding('CP1251');
    
  $i=0;$j=0;
  foreach($F_ARRAY as $k=>$v) if(method_exists($OBJECTS[$k],'mkList')) {	
	if(isset($f_array[$k]) && $f_array[$k]=='*hide*') {}
	elseif($xlscook[1][$i++]) { 
		
      if(isset($f_array[$k])) $v=explode("|",$f_array[$k]);
      else $v=array($OBJECTS[$k]->PROP["caption"]);
	  $sheet->write(0, $j++, strip_tags($v[0]), $black_bold_format);
	}
  }


  $line=1;
  $XLS_PROCCESS_LOG=1;
  
  foreach($RECS as $Record) {
    $i=0;
	$j=0;
    foreach($F_ARRAY as $k=>$v) if(method_exists($OBJECTS[$k],'mkList')) {
      if(isset($f_array[$k]) && $f_array[$k]=='*hide*') {}
      elseif($xlscook[1][$i++]) {	  
	if(!isset($Record[$k]))  $Record[$k]=$Record["id"];      
        $v=strip_tags($OBJECTS[$k]->mkList($Record[$k]));
        if(strval(intval($v))==$v) $frmt=$black_9_format_numb;
        else $frmt=$black_9_format;
        $sheet->write($line, $j++, $v, $frmt);
      }
    }
    $line++;
  }
  $xls->close();


} else {
// В противном случае делаем csv

  $IMPCNF=array(
    "sql"=>$sql,
    "sql_cnt"=>"SELECT count(*) $SQL",
    "type"=>$ft,
    "columns"=>array(),
    "dics"=>array(),
    "unitime"=>array(),
    "date"=>array(),
    "id"=>(isset($PROPERTIES["FIX_ID_TO_COD"])? $PROPERTIES["FIX_ID_TO_COD"]:"id"),
    "multi"=>array()
  );

  $db->query("SELECT sizes_ FROM gridsettings WHERE (usr='".$ADMIN_ID."' or global='1')  and modname='".$EXT."' ORDER BY global");
  $s=array();
  if($db->next_record()) {
	$s=explode("],[",stripslashes($db->Record["sizes_"]));
	foreach($s as $k=>$v) {
	  $v=explode(",",$v);
	  if($v[2]=='true' || $v[2]=='true]') unset($s[$k]);
	  else {
	    $s[$k]=str_replace('"','',$v[1]);
	  }
	}
  }
  

   //foreach($F_ARRAY as $k=>$v) if(method_exists($OBJECTS[$k],'mkList') && (!count($s) || (in_array($k,$s)))) {	


   foreach($F_ARRAY as $k=>$v) if(method_exists($OBJECTS[$k],'mkList') && (!count($s) || (in_array($k,$s)))) {	
	if(isset($f_array[$k]) && $f_array[$k]=='*hide*') {$v=array($OBJECTS[$k]->PROP["caption"]);}
        elseif(isset($f_array[$k])) $v=explode("|",$f_array[$k]);
        else $v=array($OBJECTS[$k]->PROP["caption"]);
	if(!$v[0]) $v[0]=$k;
	$IMPCNF["columns"][$k]=strip_tags($v[0]);
	if(isset($OBJECTS[$k]->type) && $OBJECTS[$k]->type=='unitime') $IMPCNF["unitime"][]=$k;
	elseif(isset($OBJECTS[$k]->type) && $OBJECTS[$k]->type=='date') $IMPCNF["date"][]=$k;
	elseif(isset($OBJECTS[$k]->type) && $OBJECTS[$k]->type=='multi') {
	  $IMPCNF["multi"][$k]=$OBJECTS[$k]->PROP;
	}elseif(isset($OBJECTS[$k]->type) && $OBJECTS[$k]->type=='editlist') {
	  $IMPCNF["editlist"][$k]=$OBJECTS[$k]->PROP;
	}
	elseif(method_exists($OBJECTS[$k],'bldOptions') && isset($OBJECTS[$k]->csv_mode)) {
	  $OBJECTS[$k]->csv_mode=1;
	  $IMPCNF["dics"][$k]=$OBJECTS[$k]->bldOptions(-1);
	}
    }
  }

  $f=fopen($_SERVER["DOCUMENT_ROOT"].$xlscook[0],'w+');
  fwrite($f,serialize($IMPCNF));
  fclose($f);

 // if(=='.csv' || $ft=='.txt') {
   /* $t=($ft=='.csv'? ";":"\t");
	$eol="\r\n";
	
	$s=array();
    foreach($F_ARRAY as $k=>$v) if(method_exists($OBJECTS[$k],'mkList')) {	
	  if(isset($f_array[$k]) && $f_array[$k]=='*hide*') {}
	  elseif($xlscook[1][$i++]) { 
		
        if(isset($f_array[$k])) $v=explode("|",$f_array[$k]);
        else $v=array($OBJECTS[$k]->PROP["caption"]);
	    $s[]=strip_tags($v[0]);
	  }
    }
    fwrite($f,join($t,$s).$eol);

    foreach($RECS as $Record) {
      $i=0;
	  $j=0;
	  $s=array();
      foreach($F_ARRAY as $k=>$v) if(method_exists($OBJECTS[$k],'mkList')) {
        if(isset($f_array[$k]) && $f_array[$k]=='*hide*') {}
	    elseif($xlscook[1][$i++]) {
	      if(!isset($Record[$k]))  $Record[$k]=$Record["id"];
          $s[]=strip_tags($OBJECTS[$k]->mkList($Record[$k]));
	    }
      }
	  fwrite($f,join($t,$s).$eol);
	}
    */
  //}
}
