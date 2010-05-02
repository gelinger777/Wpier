<?
$LANG="";
$_USERDIR="";

//setlocale(LC_ALL,'ru_RU');// 'ru_RU.CP1251');//, 'rus_RUS.CP1251', 'Russian_Russia.1251');

if(!isset($_POST)) $_POST=array();
if(!isset($HTTP_POST_FILES) && isset($_FILES)) $HTTP_POST_FILES=$_FILES;
elseif(!isset($HTTP_POST_FILES)) $HTTP_POST_FILES=array();

$_CONFIG=array();
include dirname(__FILE__)."/../conf/config.inc.php";
require dirname(__FILE__)."/db_sql.php";

if(!isset($_CONFIG["LOCK_TIMEOUT"]) || !$_CONFIG["LOCK_TIMEOUT"]) $_CONFIG["LOCK_TIMEOUT"]=60000;

$DB_MAIN=$_CONFIG["DB_MAIN"];
$HOST=$_CONFIG["HOST"];
$USER=$_CONFIG["USER"];
$PASSWD=$_CONFIG["PASSWD"];
$_CONFIG["PROJECT_NAME"]="Администратор проектов";
$FTP_UPLOAD_LOG=0;
if(isset($_CONFIG["MAINFRAME"]) && $_CONFIG["MAINFRAME"]) $MAINFRAME="yes";

$_SERVER["IPR_DIR"]=$_SERVER["DOCUMENT_ROOT"];

if(!isset($db)) {
  $DB_NAME="";
  $db=new DB_sql;
  if(isset($MAINFRAME)) {
    $db->Database=$DB_MAIN;
    $db->User = $USER;
    $db->Password = $PASSWD;
    $db->Host = $HOST;
    $db->connect();
    $db->query("SELECT dbName,dirName,dbUser,dbPasswd, ftp_host, prjName FROM hosts WHERE hostName='".$_SERVER["SERVER_NAME"]."'");
    if($db->next_record()) {
      $_CONFIG["PROJECT_NAME"]=$db->Record["prjName"];
      if($db->Record["dbName"]) $DB_NAME=$db->Record["dbName"];
      if($db->Record["dbUser"]) $USER=$db->Record["dbUser"];
      if($db->Record["dbPasswd"]) $PASSWD=$db->Record["dbPasswd"];
      if($db->Record["ftp_host"]) $FTP_UPLOAD_LOG=1;
      if($db->Record["dirName"]) {
        $_USERDIR=$db->Record["dirName"];
        $_SERVER["IPR_DIR"]=$_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR";
        if(file_exists($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/config.inc.php"))
          include $_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/config.inc.php";
      }
    }
  }
  if(!$DB_NAME) $DB_NAME=$DB_MAIN;
  $db=new DB_sql;
  $db->Database=$DB_NAME;
  $db->User = $USER;
  $db->Password = $PASSWD;
  $db->Host = $HOST;
  $db->type = (isset($_CONFIG["DB_TYPE"])? $_CONFIG["DB_TYPE"]:'mysql');
  $db->connect($DB_NAME,$HOST,$USER,$PASSWD);
  $DB_NAME1=$DB_NAME;
}

if($_CONFIG["SESSION"]) {
  if(isset($_CONFIG["COOKIE_ONLY"]) && $_CONFIG["COOKIE_ONLY"]) include $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/inc/cookie2session.php";
  else @session_start();
}

function checkEmail($email) {
    if(eregi("^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+))*$",$email, $regs)) return true;
    return false;
}

function isFolderLengDepend($type) {
  if(is_string($type)) $type=explode("|",$type);
  else {
    $x=1;
  }
  switch ($type[0]) {
    case "text": return 1;break;
    case "textarea": return 1;break;
    case "editor": return 1;break;
    case "table": return 1;break;
    case "file": return 1;break;
  }
  return 0;
}

function renameUndependFoldrs($type) {
  if(is_string($type)) $type=explode("|",$type);
  switch ($type[0]) {
    case "textUnd": $type[0]="text";break;
    case "textareaUnd": $type[0]="textarea";break;
    case "editorUnd": $type[0]="editor";break;
    case "tableUnd": $type[0]="table";break;
    case "fileUnd": $type[0]="file";break;
  }
  //return join("|",$type);
}

function mkLangArrays($F_ARRAY,$f_array,$LENGUAGE="") {
    //$fOUT=array();
    $assoc=array();
    if(isset($F_ARRAY)) {
      foreach($F_ARRAY as $k=>$v) {
          if(isFolderLengDepend($v)) {
            if(isset($f_array[$k]) && $LENGUAGE) {
              $f_array[$LENGUAGE.$k]=$f_array[$k];
              unset($f_array[$k]);
            }
            $assoc[$LENGUAGE.$k]=$k;
            $k=$LENGUAGE.$k;
          }
          renameUndependFoldrs($v);
      }
    }
    return array($F_ARRAY,$f_array,$assoc);
}

function mksearch($string,$search) {
  $string=explode(">",$string);
  $out="";
  foreach($string as $i=>$s) {
    if($i) $out.=">";
    $out.=checkselect($search,substr($s,0,strpos($s,"<"))).substr($s,strpos($s,"<"));
  }
  return $out;
}

function checkselect($searchFor, $string, $offset = 0) {
     $lsearchFor = strtolower($searchFor);
     $lstring = strtolower($string);
     $newPos = strpos($lstring, $lsearchFor, $offset);
     if (strlen($newPos) == 0) {
        return($string);
     } else{
        $left = substr($string, 0, $newPos);
        $right = substr($string, $newPos + strlen($searchFor));
        $center = '<span class=select>'.substr($string, $newPos, strlen($searchFor)).'</span>';
    $newStr = $left . $center . $right;
        return checkselect($searchFor, $newStr, $newPos + strlen($center));
     }
}

function echonobuf($str) {
  ob_start ();
  echo $str;
  ob_end_flush();
  ob_flush();
  flush();
  ob_flush();
  flush();
  ob_flush();
  flush();
}

function makeShortText($txt,$maxlen) {
   if(strlen($txt)>$maxlen) {
        $txt=substr($txt,0,$maxlen);
        $txt=substr($txt,0,strrpos($txt," "));
    }
  return $txt;
}

function mk1Lev($arr) {
  $out=array();
  if(!is_array($arr) || !count($arr)) return $out;
  foreach($arr as $key => $val) {
    if(is_array($val)) {
      foreach($val as $k=>$v) {
        $out[$k]=$v;
      }
    } else {
      $out[$key]=$val;
    }
  }
  return $out;
}

function kjoin($c,$a) {
  $o=array();
  foreach($a as $k=>$v) $o[]=$k;
  return join($c,$o);
}

function strtolowerrus($str) {
  $str=strtolower($str);
  for($i=0;$i<strlen($str);$i++) {
    if(ord($str[$i])>=192 && ord($str[$i])<=223) {
      $str[$i]=chr(ord($str[$i])+32);
    }
  }
  return $str;
}

if(!function_exists('stripos')) {
   function stripos($s,$ss) {
     return strpos(strtolower($s),strtolower($ss));
   }
}

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

function is_email($eml) {
  if(!eregi("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$", $eml)) {
     return 0;
  }
  return 1;
}

function EchoGetStr($var,$val) {
  $s=array();
  if(!$val && isset($_GET[$var])) {}//unset($_GET[$var]);
  elseif(!is_array($val)) $s[]="$var=".urlencode($val);
  foreach($_GET as $k=>$v) {
    if($k!=$var && !is_array($v)) $s[]="$k=".urlencode($v);
    elseif(is_array($v) && $k!=$var) {
      foreach($v as $key=>$val)
        $s[]="&$k%5B$key%5D=".str_replace('%','&#37;',urlencode($val));
    }
  }
  return "&".join("&",$s);
}

function password_generate($l) {
  $passw="";
  $letters=array("q","w","e","r","t","y","u","i","o","p","l","k","j","h","g","f","d","s","a","z","x","c","v","b","n","m","Q","W","E","R","T","Y","U","I","O","P","L","K","J","H","G","F","D","S","A","Z","X","C","V","B","N","M","1","2","3","4","5","6","7","8","9","0");
  srand((float) microtime() * 10000000);
  $x=array_rand($letters,$l);
  foreach($x as $l) $passw.=$letters[$l];
  return $passw;
}

function GetWords($arr,$dopchars="",$len=1) {
  $chars=" abcdefghijklmnopqrstuvwxyzабвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ".$dopchars;
  $wrds=array();
  foreach($arr as $k=>$v) if(is_string($k) && is_string($v) && $k!="lock_time" && $k!="lock_user") {
    $i=0;
    $v=strtolower($v);
    while($i<strlen($v)) {
      $w="";
      while($i<strlen($v) && strpos($chars,$v[$i])) $w.=$v[$i++];
      if($w && strlen($w)>$len && !in_array($w,$wrds)) $wrds[]=$w;
      $i++;
    }
  }
  return $wrds;
}

function IsICE2() {
  if(isset($_SERVER["HTTP_USER_AGENT"]) && strpos($_SERVER["HTTP_USER_AGENT"],"Ice-2")) {
    return 1;
  }
  return 0;
}


function html_entity_decode_utf8($string)
{
    static $trans_tbl;

    // replace numeric entities
    $string = preg_replace('~&#x([0-9a-f]+);~ei', 'code2utf(hexdec("\\1"))', $string);
    $string = preg_replace('~&#([0-9]+);~e', 'code2utf(\\1)', $string);

    // replace literal entities
    if (!isset($trans_tbl))
    {
        $trans_tbl = array();

        foreach (get_html_translation_table(HTML_ENTITIES) as $val=>$key)
            $trans_tbl[$key] = utf8_encode($val);
    }

    return strtr($string, $trans_tbl);
}

// Returns the utf string corresponding to the unicode value (from php.net, courtesy - romans@void.lv)
function code2utf($num)
{
    if ($num < 128) return chr($num);
    if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
    if ($num < 65536) return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    return '';
}

function unescape($strIn, $iconv_to = 'UTF-8') {
  $strOut = '';
  $iPos = 0;
  $len = strlen ($strIn);
  while ($iPos < $len) {
    $charAt = substr ($strIn, $iPos, 1);
    if ($charAt == '%') {
      $iPos++;
      $charAt = substr ($strIn, $iPos, 1);
      if ($charAt == 'u') {
        // Unicode character
        $iPos++;
        $unicodeHexVal = substr ($strIn, $iPos, 4);
        $unicode = hexdec ($unicodeHexVal);
        $strOut .= code2utf($unicode);
        $iPos += 4;
      }
      else {
        // Escaped ascii character
        $hexVal = substr ($strIn, $iPos, 2);
        if (hexdec($hexVal) > 127) {
          // Convert to Unicode
          $strOut .= code2utf(hexdec ($hexVal));
        }
        else {
          $strOut .= chr (hexdec ($hexVal));
        }
        $iPos += 2;
      }
    }
    else {
      $strOut .= $charAt;
      $iPos++;
    }
  }
 /* if ($iconv_to != "UTF-8") {
    $strOut = iconv("UTF-8", $iconv_to."//TRANSLIT", $strOut);
  }*/
  return $strOut;
}

function utf8_urldecode($str) {
    $str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
    if(phpversion()<5) return html_entity_decode_utf8($str);
    return html_entity_decode($str,null,'UTF-8');
}

function DLG($s,$p=array()) {
  global $TEXTS,$_CONFIG;
  if(!isset($TEXTS)) include dirname(__FILE__)."/../location/".$_CONFIG["ADMIN_LOCATION"]."/prog.inc";
  if(isset($TEXTS[$s])) $s=$TEXTS[$s];
  foreach($p as $k=>$v) $s=str_replace("%$k%",$v,$s);
  return $s;
}

function wpier_hash($s) {
	if (version_compare(PHP_VERSION, '5.0.0', '<')) {
		return md5($s);
	} else {
		global $_CONFIG;
		if(!isset($_CONFIG["PASSWORD_HASH_ALGO"]) || !$_CONFIG["PASSWORD_HASH_ALGO"])
			$_CONFIG["PASSWORD_HASH_ALGO"]="md5";
		return hash($_CONFIG["PASSWORD_HASH_ALGO"],$s);
	}
}

function wpier_hash_length() {
	return strlen(wpier_hash('test'));
}