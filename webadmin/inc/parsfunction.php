<?
function prep_get($u) {
  $o="";
  $l=1;
  for($i=0;$i<strlen($u);$i++) {
    $s=substr($u,$i,3);
    if($s=='?x=' || $s=='&x=' || $s=='?y=' ||$s=='&y=') {
      $l=0;
    } elseif(!$l && $u[$i]=='&') {
      $l=1;
      $o.='&';
    } elseif($l) {
      $o.=$u[$i];
    }
  }
  return $o;
}

// для совместимости со старыми модулями
function stri_replace($searchFor, $replaceWith, $string, $offset = 0) {
    return  str_ireplace($searchFor, $replaceWith, $string, $offset);
}

function check_img($img) {
	global $_CONFIG;
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["USERFILES_DIR"]."/$img") && $img) {
		$s=getimagesize($_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["USERFILES_DIR"]."/$img");
		$out=array($img,$s[0],$s[1]);
		return $out;
	}
	return false;
}

function parseImgPath($img) {
	echo str_replace("../","/",$img);
}

function send_mail_tmp($tmpname,$varray,$toemail) {
global $TMP;
		if($tmpname=="SENDTMP") {
			$FROM=parse_tmp("FROM");
			$SUBJ=parse_tmp("SUBJ");
			$EXTEND=parse_tmp("EXTEND");
			$TMP=ereg_replace("%[A-Za-z0-9_]{1,}%","",$TMP);
			$BODY=$TMP;
		} else {
			$fp=fopen($tmpname,"r");
			$BODY= fread($fp, filesize($tmpname));
			fclose($fp);

			foreach ($varray as $key => $val) $BODY=str_replace("%$key%",$val,$BODY);

			$FROM=substr($BODY,0,strpos($BODY,"\n"));
			$BODY=substr($BODY,(strpos($BODY,"\n")+1));
			$SUBJ=substr($BODY,0,strpos($BODY,"\n"));
			$BODY=substr($BODY,(strpos($BODY,"\n")+1));
			$EXTEND=substr($BODY,0,strpos($BODY,"\n"));
			$BODY=substr($BODY,(strpos($BODY,"\n")+1));

		}
		///echo "TO:".$toemail;exit;
//echo "$toemail, $SUBJ, $BODY, From: $FROM\n$EXTEND";exit;
		mail($toemail, $SUBJ, $BODY, "From: ".$FROM."\n".$EXTEND);
}

if(isset($_CONFIG["SMTP_MAIL"]) && ($_CONFIG["SMTP_MAIL"]==1 || $_CONFIG["SMTP_MAIL"]=="yes")) include dirname(__FILE__)."/Mail.php";

function sendXMail($tmpCode,$varray,$toemail="",$SUBJ="",$FROM="",$EXTEND="",$LOGsendXMail=0,$Convert="k",$files=array()) {
global $MAILTMP, $_USERDIR,$LANG,$db,$sendXMailBody,$_CONFIG;
	if($LOGsendXMail && $sendXMailBody) $BODY=$sendXMailBody;
	else {
		$MAILTMP=parse_template($_SERVER["DOCUMENT_ROOT"].($_USERDIR? "/www/".$_USERDIR:"")."/templates_".($LANG? $LANG:"rus")."/mail/".$tmpCode.".htm");
		foreach($varray as $key=>$val) {
			if(is_array($val)) {
				$blk=parse_tmp($key, "MAILTMP");
				$list="";
				foreach($val as $rows) {
					$s=$blk;
					if(is_array($rows)) foreach($rows as $k=>$v) {
						$s=str_replace("%$k%",$v,$s);
					}
					$list.=$s;
				}
				$MAILTMP=str_replace("%$key%",$list,$MAILTMP);
			} else {
				$MAILTMP=str_replace("%$key%",$val,$MAILTMP);
			}
		}

		ob_start();
		eval(mkPHPeval(stripslashes($MAILTMP)));
		$BODY = ob_get_contents();
		ob_end_clean();
		unset($MAILTMP);
		if($LOGsendXMail) $sendXMailBody=$BODY;
	}

	if(!$SUBJ || !$FROM || !$EXTEND) {
		$db->query("SELECT * FROM mailtemplates WHERE id='".$tmpCode."'");
		if($db->next_record()) if(!isset($db->Record["mlAttr"]) || $db->Record["mlAttr"]) {
			if(!$SUBJ) $SUBJ=$db->Record["mlsubject"];
			if(!$FROM) $FROM=$db->Record["mlfrom"];
			if(!$EXTEND) {
                          switch($db->Record["mlcontenttype"]) {
                            case "1":$EXTEND='UTF-8';break;
                            case "2":{
                              $EXTEND='KOI-8R';
                              $BODY=convert_cyr_string($BODY,"w","k");
	                      //$SUBJ=convert_cyr_string($SUBJ,"w","k");
                            } break;
                          }
			}
                        if(!$toemail) $toemail=$db->Record["mlto"];
		}
	}

	if($toemail) {
	$toemail=explode(";",str_replace(",",";",$toemail));

	if(isset($_CONFIG["SMTP_MAIL"]) && ($_CONFIG["SMTP_MAIL"]==1 || $_CONFIG["SMTP_MAIL"]=="yes")) {
		$headers["From"]    = $FROM;
		$headers["Subject"] = $SUBJ;

		$params["host"] = $_CONFIG["SMTP_MAIL_HOST"];
		$params["port"] = $_CONFIG["SMTP_MAIL_PORT"];
		$params["auth"] = true;
		$params["username"] = $_CONFIG["SMTP_MAIL_USER"];
		$params["password"] = $_CONFIG["SMTP_MAIL_PASSWORD"];
		$mail_object =& Mail::factory("smtp", $params);

                foreach($toemail as $eml) {
                  $headers["To"]      = $eml;
		  $mail_object->send($eml, $headers, $BODY);
                }
	} else {
           SendAttachedMail($toemail,$SUBJ,$FROM,$BODY,$files,$EXTEND);
        }
        }
}

function SendAttachedMail($to,$subject,$from,$bodyText,$files=array(),$encode="UTF-8") {
  global $_CONFIG;
  $eol="\n";
  if(isset($_CONFIG["MAIL_EOL"])) $eol=$_CONFIG["MAIL_EOL"];

//  $subject = '=?koi8-r?B?'.base64_encode(convert_cyr_string($subject, "w","k")).'?=';
//  $subject = '=?UTF-8?B?'.base64_encode($subject).'?=';

  $headers = "From: $from".$eol;
  $headers .= "MIME-Version: 1.0".$eol;
  $boundary = uniqid("INOUTERSENDER");
  $headers .= "Content-Type: multipart/mixed; boundary = $boundary".$eol.$eol;
  $body="";
  foreach($files as $Fname=>$path) if(file_exists($path)) {
    $ext=substr($Fname,strrpos($Fname,".")+1);
    $body .= "--$boundary".$eol .
   "Content-Type: application/php; name=\"$Fname\"".$eol .
   "Content-Disposition: attachment; filename=\"$Fname\"".$eol.
   "Content-Transfer-Encoding: base64".$eol.$eol;

    $fp = fopen($path, 'r');
    $content="";
    do {
       $data = fread($fp, 8192);
       if (strlen($data) == 0) break;
       $content .= $data;
    } while (true);
    fclose($fp);
    $body .= chunk_split(base64_encode($content));
  }
  $body .= "--$boundary\n" .
  "Content-Type: text/html; charset=$encode".$eol .
  "Content-Transfer-Encoding: base64".$eol.$eol;

//echo $body;

  $body .= chunk_split(base64_encode($bodyText));

  //send message
  if(is_array($to)) {
    foreach($to as $t) {
      mail($t, $subject, $body, $headers);
    }
  } else mail($to, $subject, $body, $headers);
}

function parse_date($date) {
	if(strpos($date,".")) {
		$date=explode(".",$date);
		return array(substr($date[2],2,2),$date[2],$date[1],$date[0]);
	}
	return array(substr($date,2,2),substr($date,0,4),substr($date,4,2),substr($date,6,2));
}

function echoIMG($img,$alt="", $prop="") {
	if(!$img) return "";
	$img=str_replace("../","/",$img);
	if(file_exists($_SERVER["DOCUMENT_ROOT"].$img)) {
		$size=getimagesize($_SERVER["DOCUMENT_ROOT"].$img);
		return "<img src='$img' ".$size[3]." border='0' alt='$alt' $prop />";
	}
	return "";
}

function echoDate($format,$str,$monthsNames=array(),$log=0) {
	$d=parse_date($str);
	if($log) return date($format,mktime(0,0,0,$d[2],$d[3],$d[1]));
	if(isset($monthsNames[intval($d[2])])) {
		$d[2]=$monthsNames[intval($d[2])];
		$d[3]=intval($d[3]);
	}
	$format=str_replace("y",$d[0],$format);
	$format=str_replace("Y",$d[1],$format);
	$format=str_replace("m",$d[2],$format);
	$format=str_replace("d",$d[3],$format);
	return $format;
}

function echoTime($format,$str) {
	$format=str_replace("h",substr($str,0,2),$format);
	$format=str_replace("m",substr($str,2),$format);
	return $format;
}

function mkPathFromCod($cod,$go=true) {
global $db,$FinSuf;
	$dir="";
	$cod=explode("_",$cod);
	$db->query("SELECT dir, pid FROM catalogue$FinSuf WHERE id='".intval($cod[0])."'");
	while($db->next_record()) {
		$dir="/".$db->Record["dir"].$dir;
		if($db->Record["pid"]) {
			$db->query("SELECT dir, pid FROM catalogue$FinSuf WHERE id='".$db->Record["pid"]."'");
		} else {
			if($go) {
                          header("Location: $dir/");
			  exit();
	  		} else return "$dir/";
		}
	}
	return $cod;
}

function mkPHPeval($TMP) {
	$TMP=str_replace("<?xml","|?xml",$TMP);
	$TMP=ereg_replace("%[A-Za-z0-9_]{1,}%","",$TMP);
        if (strpos($TMP,"<?") || strval(strpos($TMP,"<?"))!="") {
          $s=substr($TMP,(strpos($TMP,"<?")+2));
          $s=substr($s,0,strpos($s,"?>"));
	} else $s="";

	$OUT="";
	while ($s) {
		$ss=str_replace('\"','\\\\"',substr($TMP,0,strpos($TMP,$s)-2));
		$OUT.='echo "'.str_replace('"','\"',$ss).'";';
		if ($s) {
			$ss=trim($s);
			if(strpos(" $ss","=")==1) {
				$OUT.="echo ".substr($ss,1).";";
			} else $OUT.=$s;
		}
		$TMP=substr($TMP,strpos($TMP,$s)+strlen($s)+2);
		if (strpos($TMP,"<?") || strval(strpos($TMP,"<?"))!="") {
                  $s=substr($TMP,(strpos($TMP,"<?")+2));
                  $s=substr($s,0,strpos($s,"?>"));
		} else $s="";
	}
	$OUT=str_replace("|?xml","<?xml",$OUT);
	return $OUT.'echo "'.str_replace('"','\"',str_replace("|?xml","<?xml",$TMP)).'";';
}

function parse_template($file) {
global $_USERDIR,$CurrentCod,$BLOCK_TEMPLATE_FILE;
	$BLOCK_TEMPLATE_FILE=str_replace($_SERVER["DOCUMENT_ROOT"],"",$file);
	if($_USERDIR) $BLOCK_TEMPLATE_FILE=str_replace("/www/$_USERDIR","",$BLOCK_TEMPLATE_FILE);
	$fl=explode("/",$file);
	if($fl[(count($fl)-2)]=="spec") {
		if(isset($CurrentCod) && $CurrentCod) {
			$fl[(count($fl)-1)]="cat_".$CurrentCod."_".$fl[(count($fl)-1)];
			$fl=join("/",$fl);
			if($fl && file_exists($fl)) $file=$fl;
		}
	}

        $BLOCK_TEMPLATE_FILE=$file;

	$fp=fopen($file,"r");
	$TMP= fread($fp, filesize($file)); // Глобальная переменная
	fclose($fp);

	$t=explode("<!-- include ",$TMP);
	$s="";
	for ($i=0;$i<count($t);$i++) {
		$sp=explode(" /-->", $t[$i]);
		if (isset($sp[1])) {
			$file=$_SERVER["DOCUMENT_ROOT"].($_USERDIR? "/www/$_USERDIR":"").$sp[0];
			$TMP="";
			if (file_exists($file)) {
				$fp=fopen($file,"r");
				$TMP= fread($fp, filesize($file)); // Глобальная переменная
				fclose($fp);
			}
			$s.=$TMP.$sp[1];
		}
		else  $s.=$t[$i];
	}
	return $s;
}

function parse_tmp($pname, $block="TMP") {
global $$block;
	$x=strpos(" ".$$block,"%$pname%");
        if(!$x) return "";
	$x--;
        $str=substr($$block, $x+strlen("%$pname%"),strpos($$block,"%/$pname%")-($x+strlen("%$pname%")));
	$$block=str_replace("%$pname%$str%/$pname%","%$pname%",$$block);
	return $str;
}

function make_out($pname,$pvar) {
global $TMP,$_IF;
	$TMP=str_replace("%$pname%",$pvar,TMP_if_blocks($TMP,$pname,($pvar? 1:0)));
}

function TMP_if_blocks($tmp,$pname,$pvar) {
	if($pvar) {
		$a1="$pname";
		$a2="!$pname";
	} else {
		$a1="!$pname";
		$a2="$pname";
	}
	$tmp=str_replace("{if($a1)}","",$tmp);
	$tmp=str_replace("{/if($a1)}","",$tmp);

	$x=explode("{if($a2)}",$tmp);
	if(count($x)>1) {
		$i=1;
		$tmp=$x[0];
		while($i<count($x)) {
			$y=explode("{/if($a2)}",$x[$i]);
			if(count($y)==2) {
				$tmp.=$y[1];
			} else $tmp.=$x[$i];
			$i++;
		}
	}
	return $tmp;
}

function mkPages($TAB,$where,$step,$pg2pg,$block="TMP",$results=0) {
global $pages,$db, $start,$_GET,$$block;


                $get="";
		foreach($_GET as $k=>$v) {
		  if($k!="start" && $k!="jump") {
		    $get.="&$k=$v";
		  }
		}
                $$block=str_replace("%qs%",$get,$$block);
                $CURRPAGE=parse_tmp("CURRPAGE",$block);
		$PAGES=parse_tmp("PAGES",$block);
		$MORE=parse_tmp("MORE");

		$start=0;
		if(isset($_GET["jump"])) {
			$_GET["start"]=(intval($_GET["jump"])-1)*$step;
		}
		if(isset($_GET["start"])) $start=$_GET["start"];
		else $start=0;

		$pages="";

		$countRows=0;

		if($results) {
			$countRows=$results;
			$$block=str_replace("%numrows%",$results,$$block);
			$n=intval($results/$step);
			if ($results>$n*$step) $n++;
		} elseif($TAB) {

			$db->query("SELECT DISTINCT count(*) as cnt FROM $TAB WHERE $where");
			if($db->next_record()) {
				$$block=str_replace("%numrows%",$db->Record["cnt"],$$block);
				$n=intval(($db->Record["cnt"])/$step);
				if ($db->Record["cnt"]>$n*$step) $n++;
				$countRows=$db->Record["cnt"];
			}
		} else {

                  return 0;
                }

		$list="";
		$i=0;
//echo "<div style='background:#ffffff'>".($start-$step)."/".($start+$step)."</div>";

		$$block=str_replace("%allpages%",$n,$$block);
		$$block=str_replace("%allpagesrows%",$db->Record["cnt"],$$block);
		$$block=str_replace("%pgPrev%",($start-$step),$$block);
		$$block=str_replace("%pgNext%",($start+$step),$$block);



		if(isset($pg2pg) && $pg2pg) {
			$nn=intval($pg2pg/2);
			$i=intval($start/$step);
			if($i) $i-=$nn;
			if($i<0) $i=0;
			$nn=$i+$pg2pg;
			if($nn>$n) $i=$n-$pg2pg;
			else $n=$i+$pg2pg;
			if($i<0) $i=0;

			$nn=intval($countRows/$step);
			$nn=$countRows-$nn*$step;
			if(!$nn) $ne=$countRows-$step;
			else $ne=$countRows-$nn;
			if($ne>$start) {$$block=str_replace("%pageEnd%",$ne,$$block);}

			$nn=($i-$pg2pg)*$step;
			if($nn>0) $$block=str_replace("%pageDown%",$nn,$$block);
			$nn=($i+$pg2pg)*$step;
			if($nn>=$countRows || $nn==$ne) $nn=0;
			$$block=str_replace("%pageUp%",$nn,$$block);
		}

		$list="";
		if ($n>1) for ($i;$i<$n;$i++) {
			if ($start==strval($i*$step)) {
				$st=$CURRPAGE;
				$BLK=str_replace("%currentpage%",strval($i+1),$BLK);
			}
			else $st=$PAGES;
			$st=str_replace("%start%",strval($i*$step),$st);
			$st=str_replace("%page%",strval($i+1),$st);
			$list.=$st;
		}
		//send2blk("PAGES",$list);//$BLK=str_replace("%PAGES%",$list,$BLK);
		//send2blk("pages",$list);



		//$$block=str_replace("%pages%",$pages,$$block);
		$$block=str_replace("%pages%",$list, TMP_if_blocks($$block,"pages",($list? 1:0)));
		$$block=str_replace("%PAGES%",$list, TMP_if_blocks($$block,"PAGES",($list? 1:0)));
		return $start;
}

function send2blk($y,$x) {
global $BLK;
	if(isset($BLK) && $BLK) {
		$BLK=str_replace("%".$y."%",$x, TMP_if_blocks($BLK,$y,($x? 1:0)));
	}
}

function sendAr2blk($ar,$blk) {
  if(count($ar) && $blk) {
    foreach($ar as $y=>$x) if(!is_array($x))
      $blk=str_replace("%".$y."%",$x, TMP_if_blocks($blk,$y,($x? 1:0)));
  }
  return $blk;
}

function parse_blk($tname,$out="") {
global $BLK,$TemplatesPath;
	$BLK=parse_template($TemplatesPath."spec/$tname");
	if($out) {
		$out=explode(",",$out);
		foreach($out as $v) {
			$v=trim($v);
			if($v) {
				global $$v;
				$$v=parse_tmp($v,"BLK");
			}
		}
	}
}
