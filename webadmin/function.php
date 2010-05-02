<?
$_USErdir="";
if(!isset($_POST)) $_POST=array();
if(!isset($HTTP_POST_FILES) && isset($_FILES)) $HTTP_POST_FILES=$_FILES;
elseif(!isset($HTTP_POST_FILES)) $HTTP_POST_FILES=array();
require $_SERVER["DOCUMENT_ROOT"]."/db_sql.php";
$_CONFIG=array();
include $_SERVER["DOCUMENT_ROOT"]."/config.inc.php";

if(!isset($_CONFIG["lock_timeOUT"]) || !$_CONFIG["lock_timeOUT"]) $_CONFIG["lock_timeOUT"]=60000;

if($_CONFIG["SESSION"]) {
	@session_start();
}
$DB_MAIN=$_CONFIG["DB_MAIN"];
$HOST=$_CONFIG["HOST"];
$USER=$_CONFIG["USER"];
$PASSWD=$_CONFIG["PASSWD"];
$_CONFIG["PROJECT_NAME"]="Администратор проектов";
$FTP_UPLOad_LOG=0;
if(isset($_CONFIG["MAINFRAME"]) && $_CONFIG["MAINFRAME"]) $MAINFRAME="yes";

$_SERVER["IPR_dir"]=$_SERVER["DOCUMENT_ROOT"];

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
			if($db->Record["ftp_host"]) $FTP_UPLOad_LOG=1;
			if($db->Record["dirName"]) {
				$_USErdir=$db->Record["dirName"];
				$_SERVER["IPR_dir"]=$_SERVER["DOCUMENT_ROOT"]."/www/$_USErdir";
				if(file_exists($_SERVER["DOCUMENT_ROOT"]."/www/$_USErdir/config.inc.php")) 
					include $_SERVER["DOCUMENT_ROOT"]."/www/$_USErdir/config.inc.php";
			}			
		}
	}
	if(!$DB_NAME) $DB_NAME=$DB_MAIN;
	$db=new DB_sql;
	$db->Database=$DB_NAME;
    $db->User = $USER;
    $db->Password = $PASSWD;
	$db->Host = $HOST;
	if(isset($_CONFIG["DB_TYPE"]) && $_CONFIG["DB_TYPE"]) $db->type = $_CONFIG["DB_TYPE"];
	$db->connect();
	$DB_NAME1=$DB_NAME;
}

function checkEmail($email) {
		if(eregi("^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+))*$",$email, $regs)) return true;
		return false;
}

function isFolderLengDepend($type) {
	$type=explode("|",$type);
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
	$type=explode("|",$type);
	switch ($type[0]) {
		case "textUnd": $type[0]="text";break;
		case "textareaUnd": $type[0]="textarea";break;
		case "editorUnd": $type[0]="editor";break;
		case "tableUnd": $type[0]="table";break;
		case "fileUnd": $type[0]="file";break;
	}
	return join("|",$type);
}

function mkLangArrays($F_ARRAY,$f_array,$lenguage="") {
		$fOUT=array();
		$assoc=array();
		if(isset($F_ARRAY)) {
			foreach($F_ARRAY as $k=>$v) {
				if(is_array($v)) {
					$out=array();
					foreach($v as $key=>$val) {
						if(isFolderLengDepend($val)) {
							if(isset($f_array[$key]) && $lenguage) {
								$f_array[$lenguage.$key]=$f_array[$key];
								unset($f_array[$key]);
							}
							$assoc[$lenguage.$key]=$key;
							$key=$lenguage.$key;
						} 
						$val=renameUndependFoldrs($val);
						$out[$key]=$val;
					}
					$fOUT[$k]=$out;	
				} else {
					if(isFolderLengDepend($v)) {
						if(isset($f_array[$k]) && $lenguage) {
							$f_array[$lenguage.$k]=$f_array[$k];
							unset($f_array[$k]);
						}
						$assoc[$lenguage.$k]=$k;
						$k=$lenguage.$k;
					} 
					$v=renameUndependFoldrs($v);
					$fOUT[$k]=$v;		
				}
			}
			$F_ARRAY=$fOUT;
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

function makeShorttext($txt,$maxlen) {
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

function EchoGetStr($var,$val) {
  $s=array();
  if(!$val && isset($_GET[$var])) {}//unset($_GET[$var]);
  elseif(!is_array($val)) $s[]="$var=".urlencode($val);
  foreach($_GET as $k=>$v) {
    if($k!=$var && !is_array($v)) $s[]="$k=".urlencode($v);
  }
  return "&".join("&",$s);
}
?>
