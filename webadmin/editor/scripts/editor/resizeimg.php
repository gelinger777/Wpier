<?


function mkResizeImg($distFile,$srcFile,$size,$water="") {


    $log=strpos($size,"?");

	$size=explode("x",$size);
	$log0=strpos($size[0],"?");
	$log1=strpos($size[1],"?");
	$size[0]=intval($size[0]);
	$size[1]=intval($size[1]);
	if(!$size[0] && !$size[1]) return 0;
	$szsrc=getimagesize($srcFile);

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
		$k=$size[0]/$size[1];
		if($szsrc[0]>$k*$szsrc[1]) {
			$szsrc[0]=$k*$szsrc[1];
		} else {
			$szsrc[1]=$szsrc[0]/$k;
		}
	}

	if($size[0]==$szsrc[0] && $size[1]==$szsrc[1]) {
		copy($srcFile,$distFile);
		return  "";
	}


	$ext=substr($distFile,(strrpos($distFile,".")+1));

	if($ext=="gif") $imm=imagecreatefromgif($srcFile);
	elseif($ext=="png") $imm=imagecreatefrompng($srcFile);
	elseif($ext=="jpg" || $ext=="jpeg") $imm=imagecreatefromjpeg($srcFile);
	else return 0;

	$im=imagecreatetruecolor($size[0],$size[1]);

    imagecopyresampled ($im, $imm, 0, 0, 0, 0, $size[0],$size[1], $szsrc[0], $szsrc[1] );

	if($water && file_exists($water)) {
		$Wim=imagecreatefrompng($water);
		$Wsz=getimagesize($water);
		imagecopy ($im, $Wim, intval($size[0]/2-$Wsz[0]/2),intval($size[1]/2-$Wsz[1]/2), 0, 0, $Wsz[0],$Wsz[1]);
	}

    if($ext=="gif") imagegif($im,$distFile);
	elseif($ext=="png") imagepng($im,$distFile);
	elseif($ext=="jpg" || $ext=="jpeg") imagejpeg($im,$distFile);

	imagedestroy($im);
	imagedestroy($imm);
}

function copyImgs($k,$prop) {
global $HTTP_POST_FILES,$_POST,$_USERDIR;
	if (isset($HTTP_POST_FILES[$k]) && isset($_POST[$k."PREVIEW"])) {
		$userfile=$HTTP_POST_FILES[$k];
		if (file_exists($userfile["tmp_name"]) && $userfile["tmp_name"]) {
			$userfile["name"]=str_translit($userfile["name"]);

			$prop=explode("*",$prop);

			$sz=explode("x",$_POST[$k."PREVIEW"]);
			if(!isset($sz[1])) $sz[1]="";
			if(!isset($sz[2])) $sz[2]="";
			if(!isset($sz[3])) $sz[3]="";

			$water="";

			if(isset($prop[3]) && $prop[3]) {
				$water=$_SERVER["DOCUMENT_ROOT"].($_USERDIR? "/www/$_USERDIR":"").$prop[3];
			}

                        if($prop[2]) {
			  if(!$sz[2] && !$sz[3]) {
				mkResizeImg($_SERVER['DOCUMENT_ROOT'].($_USERDIR? "/www/$_USERDIR":"").$prop[1].$userfile["name"], $userfile["tmp_name"], "2048?x1600?", $water);

				//copy($userfile["tmp_name"],$_SERVER['DOCUMENT_ROOT'].($_USERDIR? "/www/$_USERDIR":"").$prop[1].$userfile["name"],$water);
			  } else {

				mkResizeImg($_SERVER['DOCUMENT_ROOT'].($_USERDIR? "/www/$_USERDIR":"").$prop[1].$userfile["name"], $userfile["tmp_name"], $sz[2]."x".$sz[3], $water);
			  }
		        } else $prop[2]=$prop[1];

			mkResizeImg($_SERVER['DOCUMENT_ROOT'].($_USERDIR? "/www/$_USERDIR":"").$prop[2].$userfile["name"],$userfile["tmp_name"],$sz[0]."x".$sz[1]);

			return array(($_USERDIR? "/www/$_USERDIR":"").$prop[1].$userfile["name"],($_USERDIR? "/www/$_USERDIR":"").$prop[2].$userfile["name"]);
		}
	}
	return 0;
}
