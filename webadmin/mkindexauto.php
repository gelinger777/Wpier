<?    
error_reporting(E_ALL);

//$_SERVER=array("DOCUMENT_ROOT"=>"f:\www\psbank");

include $_SERVER["DOCUMENT_ROOT"]."/function.php";
 
 /*$d_name="psbankdb";
 $d_user="root";
 $d_pass="";
 $d_host="localhost";
 */
 /*$db=new DB_sql;
 $db->Database=$d_name;
 $db->User =$d_user ;
 $db->Password =  $d_pass;
 $db->Host =  $d_host;
 $db->connect(); 
 */
require $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/menu.inc";
 
$dbw=new DB_sql;
//$dbw->connect( $db->Database, $db->Host,$db->User, $db->Password);
$dbw->Database=$db->Database;
 $dbw->User =$db->User ;
 $dbw->Password =  $db->Password;
 $dbw->Host =   $db->Host;
 $dbw->type = $_CONFIG["DB_TYPE"];
 $dbw->connect();
 
 

$db1=new DB_sql;
//$db1->connect( $db->Database, $db->Host,$db->User, $db->Password);
$db1->Database=$db->Database;
 $db1->User =$db->User ;
 $db1->Password =  $db->Password;
 $db1->Host =   $db->Host;
 $db1->type = $_CONFIG["DB_TYPE"];
 $db1->connect();

$indxFldTypes=array("varchar","text","varchar2","clob");
$wrkFldNames=array("lock_user","dir","spec","id","pid","cmpw");
$mark=array(".",",",";","'","\"",":","/","\\","+","-","%","=","#","$","!","&","(",")","[","]","{","}","?","*");
$endChr=array(".","!","?");
$progress=0;
$progressadd=5;
$textSize=255;

// functions -----------------------------------------------------------------------------------------------------------

if(!function_exists('str_ireplace')) {
   function str_ireplace($search, $replacement, $string){
       $delimiters = array(1,2,3,4,5,6,7,8,14,15,16,17,18,19,20,21,22,23,24,25,
       26,27,28,29,30,31,33,247,215,191,190,189,188,187,186,
       185,184,183,182,180,177,176,175,174,173,172,171,169,
       168,167,166,165,164,163,162,161,157,155,153,152,151,
       150,149,148,147,146,145,144,143,141,139,137,136,135,
       134,133,132,130,129,128,127,126,125,124,123,96,95,94,
       63,62,61,60,59,58,47,46,45,44,38,37,36,35,34);
       foreach ($delimiters as $d) {
           if (strpos($string, chr($d))===false){
               $delimiter = chr($d);
               break;
           }
       }
       if (!empty($delimiter)) {
           return preg_replace($delimiter.quotemeta($search).$delimiter.'i', $replacement, $string);
       }
       else {  
           trigger_error('Homemade str_ireplace could not find a proper delimiter.', E_USER_ERROR);
       }
   }
} 

function ToLowerCase($s) {
  for($i=0;$i<strlen($s);$i++) {
    if(ord($s[$i])>=192 && ord($s[$i])<=223) $s[$i]=Chr(ord($s[$i])+32);
  }
  return strtolower($s);
}

function mp($str) {
  if($str>100) return 0; 
  echo $str."\n";  
}

function makepath($id) {
global $tree;  
  $path="";
  while(isset($tree[$id])) {
    $path=$tree[$id][1]."/".$path;
    $id=$tree[$id][0];
  }
  return "/$path";
}

function getIndxFldStr($tb) {
global $db,$indxFldTypes,$wrkFldNames,$progress,$progressadd;
  if(!$tb) return array();
  $out=array();

  $flds=$db->folders_names($tb);
  foreach($flds as $k=>$v) {
    $v=explode("(",$v);
    if(in_array(strtolower($v[0]),$indxFldTypes) && !in_array($k,$wrkFldNames)) {
		$out[]=$k;
    }
  }
  $progress+=$progressadd;
  mp(intval($progress));
  return $out;
}

function getTextPath($w,$txt,$size) {
global $mark,$endChr;
  $stl=strlen($txt);
  if($stl<=$size) return $txt;
  $x=strpos($txt,$w);
  if(($x+$stl)<=$size) $x=0;
  else {
    $i=0;
    while(!$i && $x!=0) {
      if(in_array($txt[$x],$endChr)) {
        $i++;
      }
      $x--;
    }
  }
  if($x) $x+=2;
  $z=$x+$size;  
  while($z<$stl && $txt[$z]!=" " && !in_array($txt[$z],$mark)) $z++;
  return substr($txt, $x, ($z-$x));  
}

function getWordIndx($arr,$fld,$pt,$title="",$text="",$objects=array()) {
global $mark,$textSize;

  $wrds=array();
  if($title) {
   foreach($arr as $k=>$v) if(isset($objects[$k])) $title=str_replace("%$k%",$objects[$k]->mkList($v),$title);
  }
  if($text) {
   foreach($arr as $k=>$v) if(isset($objects[$k])) $text=str_replace("%$k%",$objects[$k]->mkList($v),$text);
  }
  
  foreach($fld as $v) {

    if(isset($arr[$v])) {
     
     
     if(isset($objects[$v])) $arr[$v]=$objects[$v]->mkList($arr[$v]);
    
     $arr[$v]=str_replace("\r","",str_replace("\n"," ",strip_tags($arr[$v])));
     $txt=$arr[$v];
      foreach($mark as $mv) {
        $arr[$v]=str_replace($mv," ",$arr[$v]);
      }

      $x="";
      while($x!=$arr[$v]) {
        $x=$arr[$v];
        $arr[$v]=str_replace("  "," ",$x);
      }
	  
      $arr[$v]=explode(" ",ToLowerCase($arr[$v]));
      foreach($arr[$v] as $k=>$vw) {
        if(strlen($vw)>2) {
          //$vw=strtolower($vw);
          if(!isset($wrds[$vw])) {
            $wrds[$vw]=array();
          }
	  if(!isset($wrds[$vw][$pt])) $wrds[$vw][$pt]=array(getTextPath($vw,$txt,$textSize),0,"");	
          if($title) $wrds[$vw][$pt][2]=$title;
	  if($text) $wrds[$vw][$pt][0]=$text; 
          $wrds[$vw][$pt][1]++;
        }
      }
   
    }
  
  }

  SaveWords($wrds);
}


function SaveWords($wrds) {
global $dbw,$catTitle;
  //if(!$x[2]) $x[2]=$catTitle;
  foreach($wrds as $k=>$v) {
    foreach($v as $p=>$x)
      $dbw->query("INSERT INTO indexes (wrd,url,txt,cnt,title) VALUES ('$k','$p','".str_replace("'","&39;",$x[0])."','".$x[1]."','".str_replace("'","&39;",$x[2])."')");
      
     // echo $dbw->LastQuery."<hr>"; exit;
  }
}
// K functions -----------------------------------------------------------------------------------------------------------

$db->query("DELETE FROM indexes");


// Строим дерево виртуальных страниц
$tree=array();
$db->query("SELECT id,pid,dir FROM catalogue_fin ORDER BY id");
while($db->next_record()) {
  $tree[$db->Record["id"]]=array($db->Record["pid"],$db->Record["dir"]);
}

$fld=getIndxFldStr("catalogue_fin");
$wrds=array();
$codPath=array();

$titlesList=array();

$db->query("SELECT * FROM catalogue_fin");
while($db->next_record()) {
  $p=makepath($db->Record["id"]);
  $codPath[$db->Record["id"]]=$p;
  $titlesList[$db->Record["id"]]=$db->Record["title"];
  getWordIndx($db->Record,$fld,$p,$db->Record["title"]);

}

$specMod=array();
foreach($menu_items as $k=>$v) {
   $fnm=$_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/extensions/$k.php";
  // Читаем данные из спец.модуля
  if(file_exists($fnm)) {
    $fp=fopen($fnm,"r");
    $seval=fread($fp,filesize($fnm));
    fclose($fp);
    $spos1=strpos($seval,"//HEAD//");
    $spos2=strpos($seval,"//ENDHEAD//");
    $seval=substr($seval,$spos1,$spos2-$spos1);
    if(trim($seval)) {
      @eval($seval);
      if(isset($PROPERTIES["tbname"]) && !isset($PROPERTIES["no_index"])) {
        $specMod[$k]=array($PROPERTIES,$F_ARRAY,array());
        $oFiles=array();
        foreach($F_ARRAY as $kk=>$vv) {
	  $s=explode("|",$vv);
	  if(!isset($oFiles[$s[0]]) && file_exists($_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/inc/components/".$s[0].".php")) {
		include_once $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/inc/components/".$s[0].".php";
		$oFiles[$s[0]]=1;
	  }
	  if(!isset($specMod[$k][2][$kk])) {
		$s='$specMod[$k][2][$kk]=new T_'.$s[0].';';
		@eval($s);
	  }
	  if(isset($specMod[$k][2][$kk])) $specMod[$k][2][$kk]->str2params ($kk,$vv);
        }
      }
    }
  }
}

// Создаем массив "путь"=>"спецраздел" для отслеживания первых на странице спецразделов
$specPat=array();

$fld=getIndxFldStr("content_fin");
$db->query("SELECT content_fin.*,catalogue_fin.title as \"catTitle\", catalogue_fin.id as \"cid\" FROM content_fin,catalogue_fin WHERE  catalogue_fin.id=content_fin.catalogue_id ORDER BY content_fin.id");
while($db->next_record()) {
  if(isset($codPath[$db->Record["catalogue_id"]])) {
    $p=$codPath[$db->Record["catalogue_id"]];
    getWordIndx($db->Record,$fld,$p,$db->Record["catTitle"]);	
    //getWordIndx($db->Record,$fld,$p,(isset($PROPERTIES["title_folder"])? $PROPERTIES["title_folder"]:""));

    if(isset($specMod[$db->Record["spec"]])) {
      if(!isset($specPat[$p])) $specPat[$p]=array();
      $specPat[$p][]=array($db->Record["spec"],$db->Record["catalogue_id"],$db->Record["cid"]);
    }
  }
} 

if(count($specPat)) $progressadd=90/count($specPat);
$sqlArr=array();

foreach($specPat as $k=>$val) foreach($val as $v) {
  if(isset($specMod[$v[0]])) {
    if(isset($specMod[$v[0]][0]["html"])) $pfld=$specMod[$v[0]][0]["html"];
    else $pfld="id";
 
    $fld=getIndxFldStr($specMod[$v[0]][0]["tbname"]);
  
    if(isset($specMod[$v[0]][0]["conect"]) && !isset($specMod[$v[0]][0]["showlink"]) && $specMod[$v[0]][0]["tbname"]) {
      $sql="SELECT ".$specMod[$v[0]][0]["tbname"].".*,".$specMod[$v[0]][0]["tbname"]."catalogue.pgID  FROM ".$specMod[$v[0]][0]["tbname"].",".$specMod[$v[0]][0]["tbname"]."catalogue WHERE ".$specMod[$v[0]][0]["tbname"].".".$specMod[$v[0]][0]["conect"]."=".$specMod[$v[0]][0]["tbname"]."catalogue.rowidd and ".$specMod[$v[0]][0]["tbname"]."catalogue.pgID='".$v[1]."'";
    } elseif($specMod[$v[0]][0]["tbname"]) {
      $sql="SELECT * FROM ".$specMod[$v[0]][0]["tbname"];
    }
   
    if(isset($sql) && !in_array($sql, $sqlArr)) {
      $sqlArr[]=$sql;
      
	  if(isset($specMod[$v[0]][0]["showlink"])) {
		  $k=$specMod[$v[0]][0]["showlink"];

	  }

      if(isset($sql) && $sql) {
        $db1->query($sql);
        while($db1->next_record()) if($db1->Record[$pfld]) {
          if(isset($db1->Record["catTitle"])) $catTitle=$db1->Record["catTitle"];
          else $catTitle="";
           

          if(isset($specMod[$v[0]][0]["openone"]) && $specMod[$v[0]][0]["openone"]==1) 
            $p=makepath($v[2]);
          else         
            $p=$k.$db1->Record[$pfld].".html";
          
		   $t="";
		   if(isset($specMod[$v[0]][0]["index_title"])) $t=$specMod[$v[0]][0]["index_title"];
		   elseif(isset($db1->Record["pgid"]) && isset($titlesList[$db1->Record["pgid"]])) $t=$titlesList[$db1->Record["pgid"]];
          getWordIndx($db1->Record,$fld,$p,$t,(isset($specMod[$v[0]][0]["index_text"])? $specMod[$v[0]][0]["index_text"]:""),$specMod[$v[0]][2]);

        }
      }
    }
  }
}
echo "fin";
exit;?>