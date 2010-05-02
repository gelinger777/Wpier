<?
$_STORCONF=array(
  "parent-access"=>0, // Доступ к родительским каталогам
  "autorisation"=>0,  // Использовать текущую авторизацию
  "ip-autorisation"=>0, // Проверять IP
  "ip-valid"=>array('127.0.0.1'), // Список допустимых IP
  "hidden-mask"=>"*.htaccess|*.sys", // Список скрытых файлов
  "dir" => $_SERVER["DOCUMENT_ROOT"], // Корневой каталог 
  "tmp" => $_SERVER["DOCUMENT_ROOT"]."/tmp", // Для временных файлов
  "forbidden"=>array("php","phtml","php3","pl")
);

// ------------------------ END CONFIG

if($_STORCONF["autorisation"]) 
  include $_SERVER["DOCUMENT_ROOT"]."/webadmin/autorisation.php";

function data_encode($data, $keyprefix = "", $keypostfix = "") {
  assert( is_array($data) );
  $vars=null;
  foreach($data as $key=>$value) {
    if(is_array($value)) $vars .= data_encode($value, $keyprefix.$key.$keypostfix.urlencode("["), urlencode("]"));
    else $vars .= $keyprefix.$key.$keypostfix."=".urlencode($value)."&";
  }
  return $vars;
}

function check_file($fn) {
  if(!isset($_STORCONF["forbidden"]) || !count($_STORCONF["forbidden"])) return 1;
  $fn=strtolower(substr($fn,strrpos($fn,'.')+1));
  if(in_array($fn,array($_STORCONF["forbidden"]))) return 0;
  return 1;
}

class wordconvert { 
  var $filename;
  var $convert_to=0; 
  var $visible=0;    
  function wordconvert($filename){ 
    $filename_path=  substr($filename,0,-4);                           
    $word=new COM("Word.Application") or die("Cannot start MS Word"); 
    $word->visible = 0; 
    $word->Documents->Open($filename)or die("Cannot find file to convert"); 
    $word->ActiveDocument->SaveAs($filename_path,8); 
    $word->quit(0);
  }    
}

function getFn($p,$f) {
  $fn=explode("/",$p);
  $mod=$fn[count($fn)-1];
  if(!$mod || $mod=='*.*') {
    unset($fn[count($fn)-1]);
    $f=join("/",$fn)."/".$f;
  } elseif(strpos(" $mod","*")) {
    unset($fn[count($fn)-1]);
    $f=join("/",$fn)."/".$f;
  } else $f=$p;
  if($f[0]!='.') $f='.'.$f;
  return $f;
}

if(isset($_GET["addfile"])) {
// Тут интерфейс для закачки локальных файлов
?>
<html>
<body style="font-family:Arial;font-size:11px;background:#ffffff;padding:0;margin:0;"><table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%"><tr><td align="center">
<?
$log=1;
$ok=0;
if(isset($_GET["del"])) {
  if(file_exists($_GET["addfile"].$_GET["del"])) 
    unlink($_GET["addfile"].$_GET["del"]);
} elseif(isset($_GET["replace"])) {    
  if(file_exists($_GET["addfile"].$_GET["replace"])) {
    if(check_file($_GET["fn"])) {
	  copy($_GET["addfile"].$_GET["replace"],$_GET["addfile"].$_GET["fn"]);
      $ok=$_GET["fn"];  
	}
  }
}elseif(isset($_FILES["file"])) {
  if(file_exists($_GET["addfile"].$_FILES["file"]["name"])) {
    $f=$_FILES["file"]["name"].mktime();
    if(check_file($f)) {
	  copy($_FILES["file"]["tmp_name"],$_GET["addfile"].$f);
      echo "Файл <b>".$_FILES["file"]["name"]."</b> уже существует.<br>";
      echo "<a href='?addfile=".$_GET["addfile"]."&replace=".$f."&fn=".$_FILES["file"]["name"]."'>[Заменить]</a>
      <a href='?addfile=".$_GET["addfile"]."&del=".$f."'>[Отмена]</a>
      <br><a href='?addfile=".$_GET["addfile"]."'>Новый файл</a>";
	}
    $log=0;
  } else {
    if(check_file($_FILES["file"]["name"])) {
	  copy($_FILES["file"]["tmp_name"],$_GET["addfile"].strtolower($_FILES["file"]["name"]));
      $ok=$_FILES["file"]["name"]; 
	}
  }
}
if($log) {
?>
<form action="?addfile=<?=$_GET["addfile"]?>" ENCTYPE="multipart/form-data" id="frm" method="post">
<?if($ok){?>Файл <?=$ok?> размещен на сервере<br><?}?>
<input type="file" name="file" onchange="document.getElementById('frm').submit();">
</form>
<?}?>
</td></tr></table></body>
</html>
<?
exit;
}

// Проверяем возможность копирования по указанному пути
if(isset($_GET["checkcopy"]) && isset($_GET["file"])) {
  $_GET["checkcopy"]=getFn($_GET["checkcopy"],$_GET["file"]);
  $cod=0;
  if(file_exists($_GET["checkcopy"])) {
    $cod=1; // Файл существует
  } else {
    if(!file_exists(dirname($_GET["checkcopy"]))) {
      $cod=2; // Директория не существует
    } 
  }
  echo $_GET["ret"]."('".$_GET["oid"]."','$cod|".$_GET["checkcopy"]."');";
  exit;
}

// Копирование в рамках одного STORE
if(isset($_GET["copy"])) {
  if(file_exists($_GET["path"].$_GET["copy"]) && !is_dir($_GET["path"].$_GET["copy"])) {
    if(check_file($_GET["copy"])) {
	  copy($_GET["path"].$_GET["copy"],getFn($_GET["to"],$_GET["copy"]));
	}
  }
  echo $_GET["ret"]."('".$_GET["oid"]."');";
  exit;
}

// Удаление
if(isset($_GET["delete"])) {
  if(file_exists($_GET["delete"])) {
    if(is_dir($_GET["delete"])) @rmdir($_GET["delete"]);
    else @unlink($_GET["delete"]);
    
    echo $_GET["ret"]."('".$_GET["oid"]."');";
  }
  exit;
}

// Принимаем файл из POST
if(isset($_FILES["uploadfile"])) {
  $_GET["file"]=getFn($_GET["file"],$_GET["fn"]);
  if(file_exists($_FILES["uploadfile"]["tmp_name"]) && !is_dir($_FILES["uploadfile"]["tmp_name"]) && check_file($_GET["file"])) {
    copy($_FILES["uploadfile"]["tmp_name"],$_GET["file"]);
  }
  exit;
}elseif(isset($_GET["loadfrom"])) {
// Качаем файл и размещаем его у себя
  $url=$_GET["loadfrom"];
  if(isset($_GET["tmp"])) {
    $url.="?getfile=".$_GET["tmp"];
  }

  $_GET["file"]=getFn($_GET["file"],$_GET["fn"]);
  
  if(check_file($_GET["file"])) {

    $handle = fopen($url, "rb");
    $contents = '';
    while (!feof($handle)) {
      $contents .= fread($handle, 8192);
    }
  
    $fp=fopen($_GET["file"],"w+");
    fwrite($fp,$contents);
    fclose($fp);
  }
  echo $_GET["ret"]."('".$_GET["oid"]."');";
  
  exit;
} elseif(isset($_GET["getfile"]) && $_GET["getfile"]) {
// Отдаем запрошенный файл
   if(file_exists($_GET["getfile"]) && !is_dir($_GET["getfile"])) {
     if(isset($_GET["encode"])) {
       $fp=fopen($_GET["getfile"],"r");
       $str=fread($fp,filesize($_GET["getfile"]));
       fclose($fp);
       if($_GET["encode"]!="Windows-1251") echo iconv("Windows-1251","UTF-8//IGNORE",$str);
         //echo iconv("Windows-1251",$_GET["encode"],$str);
       else echo $str;  
     } else {
       header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
       header('Content-Description: File Transfer');
       header('Content-Type: application/octet-stream');
       header('Content-Length: ' . filesize($_GET["getfile"]));
       header('Content-Disposition: attachment; filename=' . basename($_GET["getfile"]));

       readfile($_GET["getfile"]);
     } 
   }
   exit;
} elseif(isset($_GET["uploadto"])) { 
// Аплоад файла на сервер
  if(file_exists($_GET["file"]) && !is_dir($_GET["file"])) {
    $data=array();
    $data["uploadfile"]="@".$_STORCONF["dir"].str_replace("./","/",$_GET["file"]);
    $ch = curl_init($_GET["uploadto"]);
   
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);
    curl_close ($ch);
    echo $_GET["ret"]."('".$_GET["oid"]."','".$result."');";
  }
  exit;
} elseif(isset($_FILES["uploadfile"])) {
// Если есть отправленный файл, копируем его в ТМП и возвращаем имя файла


} elseif(isset($_GET["gethtml"]) && isset($_GET["handler"]) && isset($_GET["ret"])) {
// На запрос вернуть HTML, постим на сервер HTML-версию файла
   $data = array();
   
   $log=0;
   $filename=$_STORCONF["dir"].str_replace("./","/",$_GET["gethtml"]); 
   if(strtolower(substr($filename,strrpos($filename,".")))=='.doc') {
     new wordconvert($filename);
     $filename=substr($filename,0,strrpos($filename,".")).".htm";
     $i=0;
     while(!file_exists($filename) && $i<10) {sleep(1);$i++;}
     if(!file_exists($filename)) $log=-1;else $log=1;
   }
   //echo "alert('$filename');"; 
   if($log!=-1) {
     $data["file"]="@".$filename;
     $ch = curl_init($_GET["handler"]);
   
     curl_setopt($ch, CURLOPT_VERBOSE, 1);
     curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); 
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

     $result = curl_exec($ch);
     curl_close ($ch);
     echo $_GET["ret"]."('".trim($result)."');";
     if($log) unlink($filename);
   }
   
   exit();
// ---- К POST ------------------ 
}

if(isset($_GET["newdir"])) {
  @mkdir($_GET["dir"].$_GET["newdir"]);
}
if(isset($_GET["rename"]) && isset($_GET["to"])) {
  if(file_exists($_GET["dir"].$_GET["rename"])) {
    if(!rename($_GET["dir"].$_GET["rename"],$_GET["dir"].$_GET["to"])) exit;
  }
}

// Превью картинок -------------------------------------------------------
function EchoBlank() {  
  readfile($_SERVER['DOCUMENT_ROOT']."/webadmin/img/nopic.png");
  exit;
}

if(isset($_GET["getimage"])) {
//ob_start();
  if(isset($_GET["size"])) {
    $ext= strtolower( substr($_GET["getimage"],strrpos($_GET["getimage"],'.')+1));
    if($ext=="jpg") $ext='jpeg';    
    
    $size=explode("x",$_GET["size"]);
    $log0=strpos($size[0],"?");
    $log1=strpos($size[1],"?");
    $size[0]=intval($size[0]);
    $size[1]=intval($size[1]);
    
    $sizes=GetImageSize($_GET["getimage"]);
    
	$fn="";
    if(isset($_GET["sendto"])) {
      $fn=$_STORCONF["tmp"].strtolower(substr($_GET["getimage"],strrpos($_GET["getimage"],"/")));      
    }

	$logImg=0;

    if($sizes[0]<$size[0] && $sizes[1]<$size[1]) {
      if(isset($_GET["sendto"])) {
		copy($_GET["getimage"],$fn);
	  } else {
		  header('Content-Type: image/'.$ext);
          readfile($_GET["getimage"]);
          exit;
	  }
    } else { 
    
		eval('$imm =imagecreatefrom'.$ext.'("'.$_GET["getimage"].'");');
				  
		if(!$size[0] && !$size[1]) EchoBlank();
		  
		$szsrc=array(imagesx($imm),imagesy($imm));
	 
		$xx=0;
		$yy=0;
		$iw=0;
		$ih=0;
		if($log0 && $size[0] && $szsrc[0]<=$size[0]) {
		    $size[0]=$szsrc[0];
		    $size[1]=$szsrc[1];
		}
		elseif($log1 && $size[1] && $szsrc[1]<=$size[1]) {
		    $size[0]=$szsrc[0];
		    $size[1]=$szsrc[1];        
		}
		elseif(!$size[0]) $size[0]=intval($szsrc[0]*$size[1]/$szsrc[1]);
		elseif(!$size[1]) $size[1]=intval($szsrc[1]*$size[0]/$szsrc[0]);
		else {
		    $dx=$szsrc[0]/$size[0];
		    $dy=$szsrc[1]/$dx;
		    $iw=$size[0];
		    $ih=$size[1];
			
		    if($dy==$size[1]) {
			    
		    } elseif($dy>$size[1]) { // Если картинка по высоте не влезает, пересчитываем высоту
			$xx=intval(($size[0]-($szsrc[0]/($szsrc[1]/$size[1])))/2);
			$size[0]-=2*$xx;
		    } else {
			$yy=intval(($size[1]-($szsrc[1]/($szsrc[0]/$size[0])))/2);
			$size[1]-=2*$yy;
		    }
		}
		$im=imagecreatetruecolor(($iw? $iw:$size[0]),($ih? $ih:$size[1]));
		$white = imagecolorallocate($im, 255, 255, 255);
		imagefill($im, 0, 0, $white); 
		imagecolortransparent($im, $white);
	    
		imagecopyresampled ($im, $imm, $xx, $yy, 0, 0, $size[0],$size[1], $szsrc[0], $szsrc[1] );
    
    
		$logImg=1;

		if($ext=='jpeg') {
		  if($fn) imagejpeg($im,$fn);
		  else{
			header('Content-Type: image/jpeg');
			imagejpeg($im,NULL);
		  } 
		} elseif($ext=="gif") {
		  if($fn) imagegif($im,$fn);
		  else {
			header('Content-Type: image/gif');
			imagegif($im);
		  } 
		} elseif($ext=="png") {
		   if($fn) imagepng($im,$fn);
		   else {
			 header('Content-Type: image/png');
			 imagepng($im,NULL);
		   } 
		} else EchoBlank();
    }

    if($fn) {
		
      // если исходная картинка на том же сервере, просто копируем в нужный каталог
      $x=explode("/",$_GET["sendto"]);
	  if($_SERVER["HTTP_HOST"]==$x[0]."//".$x[2] || $_SERVER["HTTP_HOST"]==$x[2]) {
		$x=explode("/",$_GET["dir"]);
		$d=$_SERVER["DOCUMENT_ROOT"];
		foreach($x as $v) if($v) {
			if(!file_exists($d."/".$v)) {
			  mkdir($d."/".$v,0777);
            }
			$d.="/".$v;
		}
		copy($fn,$_SERVER["DOCUMENT_ROOT"].$_GET["dir"].substr($_GET["getimage"],strrpos($_GET["getimage"],'/')+1));
	  } else {
		  $data["saveto"]=$_GET["dir"];
		  $data["file"]="@".$fn;
		  $ch = curl_init($_GET["sendto"]);
	   
		  curl_setopt($ch, CURLOPT_VERBOSE, 1);
		  curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
		  curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); 
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		  $result = curl_exec($ch);
		  curl_close ($ch);
      }
	  unlink($fn);
      //echo "R=$result";
    }
     
    if($logImg) {
		imagedestroy($im);
		imagedestroy($imm);
	}
  }

  exit;
}
// ККК Превью картинок -------------------------------------------------------


if(isset($_GET["preview"])) {
  $ext=substr($_GET["preview"],strrpos($_GET["preview"],'.')+1);
  $FORMS=array("","GIF","JPG","PNG","SWF");
  if(in_array($ext,array('gif','jpg','jpeg','png'))) {
    $sizes=GetImageSize($_GET["preview"]);
    $html="<table width='100%' height='100%'><tr><td align='center'><img src='http://".$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"]."?getimage=".$_GET["preview"]."&size=".$_GET["size"]."' /></td></tr><tr><td height='70' style='font-size:11px;padding:10px'>Формат: ".$FORMS[$sizes[2]]."<br>Ширина: ".$sizes[0]."<br>Высота: ".$sizes[1]."</td></tr></table>";
  } else {
    $html='NONE';
  }  
  echo $_GET["ret"].'("'.$html.'","'.$_GET["oid"].'");'; 
  exit;
}

header("Expires: Thu, Jan 1 1970 00:00:00 GMT\n"); 
header("Pragma: no-cache\n"); 
header("Cache-Control: no-cache\n");

ob_start();

if(isset($_GET["dir"])) $d=$_GET["dir"];
else $d="./";

echo "IO.store('".$_GET["store"]."','".$_GET["grid"]."',"; 

$d = dir($d);

echo "'".$d->path."',[";

$c=0;$k=0;
while (false !== ($entry = $d->read())) if($entry!='.' && $entry!='..') {
  $i="";
  $t="";
  $s=0;
  $dt=0;
  if(is_dir($d->path.$entry)) {
    $i=1;
    $dt= fileatime($d->path.$entry);
  } else {
    $i=0;
    $s= filesize($d->path.$entry);
    $dt= fileatime($d->path.$entry);
    /*if(strpos(" $entry",".")) {
      $i=strtolower(substr($entry,strrpos($entry,'.')+1));
      $entry=substr($entry,0,strrpos($entry,'.'))."|*e*|".$i;
    } else {
      $i=" ";
      $entry.="|*e*|";       
    } */
  }
  $c++;

  if($entry!='store.php') echo ($k? ",":"")."['$entry','".($dt? $dt:"")."','$i','".($i? "":"$s")."','".(!$k++? $d->path:"")."']";
}
$d->close();
echo ']);'; 

//$fp=fopen("test.log","w+");
//fwrite($fp,ob_get_contents());
//fclose($fp);
ob_end_flush();    
?>