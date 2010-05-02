<?
$endChr=array(".","?","!",";");
$mark=array(",",".","?","!",";",":","*","{","}","/",'\\','"',"'","<",">","%","(",")","«","»");

function substri($s,$i,$c) {
  return strtolower(substr($s,$i,$c)); 
}

function get_urls($s) {
	$links=array();
	$i=0;
	$l=strlen($s);
	while($i<$l) {
		if(substri($s,$i,2)=='<a') {
			$i+=3;  
			while($i<$l && $s[$i]!='>' && substri($s,$i,4)!='href') $i++;
			if($i<$l && $s[$i]!='>') {
				$i+=4;  
				while($i<$l && $s[$i]!='>' && ($s[$i]==' ' || $s[$i]=='=')) $i++;
				if($i<$l && $s[$i]!='>' ) {
					if($s[$i]=='"' || $s[$i]=="'") $c=$s[$i];
					else $c=" ";
					$i++;	
					$lnk="";
					while($i<$l && $s[$i]!='>' && $s[$i]!=$c) $lnk.=$s[$i++];
					if(!in_array($lnk,$links)) $links[]=$lnk;
				}
			}
		}
		$i++;
	}
	return $links;
}

// Нужны только линки, ведущие на дочерние страницы
function prep_links($links,$url) {
	$url=explode('?',$url);
	$url=$url[0];
	$url=substr($url,0,strrpos($url,"/"));
	$host=explode("/",$url);
	$host=$host[0]."//".$host[2];
	foreach($links as $k=>$v) {
		if(substr($v,0,2)=='./') {
			$links[$k]=$url.substr($v,1);
		} elseif($v[0]=='/') {
			if(strpos(" ".$host.$v,$url)==1) {
			  $links[$k]=$host.$v;
			} else {
				unset($links[$k]);
			}
		} elseif(substr($v,0,3)=='../') {
			/*$v=explode("../",$v);
			$v1=explode("/",$url);
			if((count($v1)-count($v))>=3) {
			  array_splice($v1,count($v1)-count($v)+1);
			  $v1[]=$v[count($v)-1];
			  $links[$k]=join("/",$v1);
			} else {
				unset($links[$k]);
			}*/
			unset($links[$k]);
		} elseif(strpos(" $v/",$host."/")!=1) {
			unset($links[$k]);
		}
	}
	//echo "test:$url\n";print_r($links);echo "\n";
	return $links;
}

function add_links_to_stack($links) {
	global $_SET;
	
	$fp=fopen($_SET["indexfile"],"r");
	$sprv="";
	while(!feof($fp)) {
		$s=fread($fp,$_SET["read_block"]);
		$s=explode("\n",$s);
		$s[0]=$sprv.$s[0];
		$sprv=$s[count($s)-1];
		unset($s[count($s)-1]);
		foreach($links as $k=>$v) {
			if(in_array($k,$s)) $links[$k]=1;
		}
	}
	fclose($fp);
	$fp=fopen($_SET["indexfile"],"a+");
	foreach($links as $k=>$v) {
		if(!$v) fwrite($fp,$k."\n");
	}
	fclose($fp);
}

function delete_indexes() {
	
}

function makeShortText($txt,$maxlen) {
   if(strlen($txt)>$maxlen) {
        $txt=substr($txt,0,$maxlen);
        $txt=substr($txt,0,strrpos($txt," "));
    }
  return $txt;
}

function getTextPath($w,$txt,$size) {
global $mark,$endChr,$_CONFIG;
  $i=stripos($txt,$w);

  while($i>0 && !in_array($txt[$i],$endChr) && ($txt[$i]<'A' || $txt[$i]>'Я') && ($txt[$i]<'A' || $txt[$i]>'Z')) $i--;

 if(($txt[$i]>='A' && $txt[$i]<='Я') || ($txt[$i]>='A' && $txt[$i]<='Z')) $i--;

  $l=strlen($txt);
  $j=++$i;$n=0;$k=$i;
  while($n<$_CONFIG["descript_length"] && $j<$l) {
     if($txt[$j]==" " || $txt[$j]=="." || $txt[$j]=="?" || $txt[$j]=='!') $k=$j;
	 $j++;
	 $n++;
  }
  $txt=str_replace("'","&#39;",substr($txt, $i, ($j-$i)));
///echo "\n\n\n$txt\n\n\n";
/**/

 // return makeShortText($txt,$size);

return $txt;

  /*$stl=strlen($txt);
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
  return substr($txt, $x, ($z-$x));  */
}

function get_index($s,$url) {
	global $db, $mark, $_CONFIG;

    if(!strpos($s,"<!-- INDEXAREA -->")) return "";
	$words=array();
	$title="";
	$text="";
	$i=0;
	$l=strlen($s);
    $log=0;

	while($i<$l) {
		if(substri($s,$i,7)=='<title>') {
			$i+=7;
			while($i<$l && $s[$i]!='<') $title.=$s[$i++];
		} elseif(substr($s,$i,18)=='<!-- INDEXAREA -->') {
			$i+=18;
			$log=1;

		} elseif($log) {
			$lg=1;			
			$p=" ";
			while($i<$l && substr($s,$i,19)!='<!-- /INDEXAREA -->') {

				if($s[$i]=='<')  $lg=0;
				elseif($s[$i]=='>') {$lg=1;$text.=" ";}
				elseif($lg && $s[$i]!="\r") {
					if($s[$i]=="\n") $s[$i]=" ";
					elseif($s[$i]=="\t") $s[$i]=" ";
					if($s[$i]==' ' && $p==' ') {}
					else {
						$text.=$s[$i];						
					}	
					$p=$s[$i];				
				}
				$i++;
			}
			$i+=19;
			$log=0;
		} else $i++;
	}
	$text=str_replace("&nbsp;"," ",$text);
	$txt=str_replace("  "," ",$text);
	while($txt!=$text) {
		$text=$txt;
		$txt=str_replace("  "," ",$text);
	}

	$txt=html_entity_decode($txt);

    foreach($mark as $mv) {
        $txt=str_replace($mv," ",$txt);
    }

//echo "\n\n\n$txt\n\n\n";

	$txt=explode(" ",strtolower($txt));
	$words=array();

	foreach($txt as $w) if(strlen($w)>=$_CONFIG["min_word"] && !isset($words[$w])) {
		$words[$w]=getTextPath($w,$text,$_CONFIG["descript_length"]);
	}

    // Удалим для начала индексы текущего урл
	$db->query("DELETE FROM indexes WHERE url like '$url%'");

	foreach($words as $k=>$v) {
		//echo $k.":$url:$title:$v\n\n";
		//echo "\n\n\nINSERT INTO indexes (wrd,url,txt,title) VALUES ('$k','$url','".$v."','".str_replace("'","&#39;",$title)."')\n\n\n";
		$k=strtolower($k);
		$db->query("INSERT INTO indexes (wrd,url,txt,title) VALUES ('$k','$url','".$v." ','".str_replace("'","&#39;",$title)."')");
	}
}

function ErrorShow($url,$en){
	global $_SET;
	if($en<10) $en="0$en";
	
	if($_SET["debug"]) {
       echo "Err$en\n";
	}
	if($_SET["save_error_log"] && $_SET["save_error_log_file"]) {
		$fp=fopen($_SET["save_error_log_file"],"a+");
		fwrite($fp,date("H:i:s")." ".$url." Error(".$en.")\n");
		fclose($fp);
	}
}


function readHTTPDigestAuthenticatedFile($host,$file)
{
   global $COOK;
    if (!$fp=fsockopen($host,80, $errno, $errstr, 15))
        return false;
       
    //first do the non-authenticated header so that the server
    //sends back a 401 error containing its nonce and opaque
    $out = "GET $file HTTP/1.1\r\n";
    $out .= "Host: $host\r\n";
    $out .= "Connection: Close\r\n\r\n";

     fwrite($fp, $out);

    //read the reply and look for the WWW-Authenticate element
    while (!feof($fp))
    {
        $line=fgets($fp, 512);
       
        if (strpos($line,"WWW-Authenticate:")!==false)
            $authline=trim(substr($line,18));
    }
   
    fclose($fp);
      
    //split up the WWW-Authenticate string to find digest-realm,nonce and opaque values
    //if qop value is presented as a comma-seperated list (e.g auth,auth-int) then it won't be retrieved correctly
    //but that doesn't matter because going to use 'auth' anyway
    $authlinearr=explode(",",$authline);
    $autharr=array();
   
    foreach ($authlinearr as $el)
    {
        $elarr=explode("=",$el);
        //the substr here is used to remove the double quotes from the values
        $autharr[trim($elarr[0])]=substr($elarr[1],1,strlen($elarr[1])-2);
    }
   
    foreach ($autharr as $k=>$v)
        echo("$k ==> $v\r\n");
   
    //these are all the vals required from the server
    $nonce=$autharr['nonce'];
    $opaque=$autharr['opaque'];
    $drealm=$autharr['Digest realm'];
   
    //client nonce can be anything since this authentication session is not going to be persistent
    //likewise for the cookie - just call it MyCookie
    $cnonce="sausages";
   
    //calculate the hashes of A1 and A2 as described in RFC 2617
    $a1="$username:$drealm:$password";$a2="GET:/$file";
    $ha1=md5($a1);$ha2=md5($a2);
   
    //calculate the response hash as described in RFC 2617
    $concat = $ha1.':'.$nonce.':00000001:'.$cnonce.':auth:'.$ha2;
    $response=md5($concat);
   
    //put together the Authorization Request Header
    $out = "GET /$file HTTP/1.1\r\n";
    $out .= "Host: $host\r\n";
    $out .= "Connection: Close\r\n";
    $out .= "Cookie: $COOK\r\n";
    $out .= "\r\n";
   
   
    if (!$fp=fsockopen($host,80, $errno, $errstr, 15))
        return false;
   
    fwrite($fp, $out);
   
    //read in a string which is the contents of the required file
    while (!feof($fp))
    {
        $str.=fgets($fp, 512);
    }
   
    fclose($fp);
   
    return $str;
}

function wgc($URL)
{  
   return file_get_contents($URL);
   
   $s=explode("/",$URL);

   $host=$s[2];
   unset($s[0]);
   unset($s[1]);
   unset($s[2]);
   $s="/".join("/",$s);


   $s=readHTTPDigestAuthenticatedFile($host,$s);

   return $s;
}