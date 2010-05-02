<?
error_reporting(E_ALL);

function write2errorLog($mod) {
	
	$fn=ini_get("error_log");

	if($fn && file_exists($fn)) {

		$fp=fopen($fn,"r");
		$s=fread($fp,filesize($fn));
		fclose($fp);
		$s=str_replace("\webadmin\inc\pfunc.php(23) : eval()'d code",str_replace("/","\\",str_replace("./","/",$mod)),$s);
		$i=strrpos($s," ")+1;
		$l=intval(substr($s,$i));
		
		$fp=fopen($mod,"r");
		$ss=fread($fp,filesize($mod));
		fclose($fp);

		$ss=explode("\n",str_replace("\r","",$ss));
		$j=0;
		while($j<count($ss) && !strpos(" ".$ss[$j],"//HEAD//")) $j++;

		if($j<count($ss))	{
			$s=(substr($s,0,$i).($l+$j-1));
			$fp=fopen($fn,"w+");
			fwrite($fp,$s);
			fclose($fp);
			
		}	exit;
	}
}

$INOUTER_GET_INFO_LOG_ARRAY=array(
"title"=>"",
"url"=>$_SERVER["REQUEST_URI"],
"time"=>time()+microtime(),
"queries"=>0,
"longquerytime"=>0,
"files"=>array(),
"moduls"=>array()
);
