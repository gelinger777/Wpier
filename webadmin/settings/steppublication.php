<?
require_once ("./autorisation.php");

//HEAD//
$PROPERTIES=array(
"tbname"=>"publposl",
"pagetitle"=>"Последовательность публикации",
"spell"=>0,
"nolang"=>1
);

$F_ARRAY=Array (
"id" => "hidden||",
"modname"=>"select||Модуль",
"members"=>"editlist|publposlmem,cod,id,memb:Сотрудник:s:settings*id*AdminName|Цепочка публикации"
);

$f_array=Array(
"id" => "*hide*",
"modname"=>"Модуль",
"members"=>"Посл. просмотра"
);
//ENDHEAD//

include "./menu.inc";

function IsPubl($specName) {
global $_USERDIR,$_CONFIG;
  $privat="";
  if($_USERDIR) $privat="../www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/$specName.php";
  if($privat && !file_exists($privat)) $privat="";
  if(!$privat) $privat="../".$_CONFIG["ADMINDIR"]."/extensions/$specName.php";
  
  $fp=fopen($privat,"r");
  $seval=fread($fp,filesize($privat));
  fclose($fp);
  $spos1=strpos($seval,"//HEAD//");
  $spos2=strpos($seval,"//ENDHEAD//");
  $seval=substr($seval,$spos1,$spos2-$spos1);
  eval($seval) ;
  if(isset($PROPERTIES["publication"]) && $PROPERTIES["publication"]) return 1;
  else return 0;
}
foreach($menu_items as $k=>$v) if($v[1] && IsPubl($k)) {
   $F_ARRAY["modname"].="|$k/$v[0]";
}

require ("./output_interface.php");
?>