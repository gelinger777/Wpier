<?
function xml2array($text) {
	$reg_exp = '/<(\w+)([^>]*)>(.*?)<\/\\1>/s';
	preg_match_all($reg_exp, $text, $match);
	foreach ($match[1] as $key=>$val) {
		if ( preg_match($reg_exp, $match[3][$key]) ) {
			$array[$val][] = array("attr"=>parseAttributs($match[2][$key]),xml2array($match[3][$key]));		   
		} else {
			$array[$val] = $match[3][$key];			
		}
	}
	return $array;
} 

function parseAttributs($attr) {
	$out=array();
	$attr=explode(" ",trim($attr));
	foreach($attr as $k=>$v) if($v) {
		$v=explode("=",$v);
		if(isset($v[1])) {
			$v[1]=str_replace("'","",str_replace('"','',trim($v[1])));
			if($v[1]) {
				$out[trim($v[0])]=$v[1];
			}	
		}
	}
	return $out;
}

?>