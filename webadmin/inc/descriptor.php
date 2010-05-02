<?
function getDescriptor($fnm, $dn="") {
global $dirname;

	if(!$dn) $dn=$dirname;

	$out=array("","","",array(),0,"",array());

	if (!stristr($fnm,".php") || $fnm=="hosts.php") return $out;

	$fs="";
	$fp=fopen($dn.$fnm,"r+");
	$fs=fread($fp,filesize($dn.$fnm));
	fclose($fp);

	$fs=str_replace('"',"&#34;",$fs);

	$spos1=strpos($fs,"/*DESCRIPTOR");
	if($spos1) {
		$spos2=strpos($fs,"*/");
		$fs=explode("\n",str_replace("/*DESCRIPTOR","",substr($fs,$spos1,$spos2-$spos1)));
		$out[0]=$fs[1];
		$out[1]=$fs[2];
		unset($fs[2]);
		unset($fs[1]);
		unset($fs[0]);
		$out[2]=join("\n",$fs);

		foreach($fs as $v) {
			if(strpos(" $v","files:")==1) {
				$v=explode(",",str_replace("files:","",$v));
				foreach($v as $k=>$vv) $v[$k]=trim($vv);
				$out[3]=$v;
			}
			elseif(strpos(" $v","version:")==1) {
				$out[7]=str_replace("version:","",$v);
			}
			elseif(strpos(" $v","author:")==1) {
				$out[8]=str_replace("author:","",$v);
			}
			elseif(strpos(" $v","tools:")==1) {
				$out[4]=1;
				//$out[2]=str_replace("";
			}
			elseif(strpos(" $v","group:")==1) {
				$out[5]=trim(substr($v,6));
			}
			elseif(strpos(" $v","scripts:")==1) {
				$v=explode(",",trim(substr($v,8)));
				foreach($v as $k=>$vv) $v[$k]=trim($vv);
				$out[6]=$v;
			}
		}
	}
	return $out;
}

function getStatusDescript($spec,$prop=array(),$cod=0) {
global $_USERDIR,$_CONFIG;
	if($_USERDIR) {
		$f="$spec.php";
		$d="";
		$dd="";
		$uu="";
		$ud=$_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/".$_CONFIG["TEMPLATES_DIR"]."/spec/";
		$uu="/www/$_USERDIR/".$_CONFIG["TEMPLATES_DIR"]."/";
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/$f")) {
			$d=$_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/";
			$dd="/www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/";
		} elseif(file_exists($_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/extensions/$f")) {
			$d=$_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/extensions/";
			$dd="/".$_CONFIG["ADMINDIR"]."/extensions/";
		}

		if(!count($prop)) {

			$fp=fopen($d.$f,"r");
			$seval=fread($fp,filesize($d.$f));
			fclose($fp);
			$spos1=strpos($seval,"//HEAD//");
			$spos2=strpos($seval,"//ENDHEAD//");
			$seval=substr($seval,$spos1,$spos2-$spos1);

			@eval($seval);

			if(isset($PROPERTIES)) {
				$prop=$PROPERTIES;
			}
		}

		if(count($prop)) {
			$s="";

			if(isset($prop["pagetitle"])) {
				$s=explode("<",$prop["pagetitle"]);
				$s=$s[0];
			}

			$s=($s? $s:$f);
			$s.="~".$spec;
			$s.="~".((file_exists($d."frontend/$f"))? "1":"");

			if(isset($prop["template_list"]) && $prop["template_list"]) {
				$tf="";
				$tfc="";
				if($cod && file_exists($ud."cat_".$cod."_".$prop["template_list"])) {
					$tf="cat_".$cod."_".$prop["template_list"];
					$tfc=2;
				} elseif(file_exists($ud.$prop["template_list"])) {
					$tf=$prop["template_list"];
					$tfc=1;
				}
				$s.="~$tf~$tfc";
			} else $s.="~~";

			if(isset($prop["template_row"]) && $prop["template_row"]) {
				$tf="";
				$tfc="";
				if($cod && file_exists($ud."cat_".$cod."_".$prop["template_row"])) {
					$tf="cat_".$cod."_".$prop["template_row"];
					$tfc=2;
				} elseif(file_exists($ud.$prop["template_row"])) {
					$tf=$prop["template_row"];
					$tfc=1;
				}
				$s.="~$tf~$tfc";
			} else $s.="~~";
			return $s."~$dd~$uu";
		}
	}
	return "";
}
