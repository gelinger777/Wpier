<?
if(!isset($_POST["gateurl"])) exit;

$url=$_POST["gateurl"];
unset($_POST["gateurl"]);

require "autorisation.php";

function data_encode($data, $keyprefix = "", $keypostfix = "") {
  assert( is_array($data) );
  $vars=null;
  foreach($data as $key=>$value) {
    if(is_array($value)) $vars .= data_encode($value, $keyprefix.$key.$keypostfix.urlencode("["), urlencode("]"));
    else $vars .= $keyprefix.$key.$keypostfix."=".urlencode($value)."&";
  }
  return $vars;
}

$cook="";
foreach($_COOKIE as $k=>$v) if($k!='PHPSESSID') {
    $cook.="$k=$v";
}
$ch = curl_init($url);
//curl_setopt($ch, CURLOPT_REFERER, "http://online.tes.ru/scripts/cwisapi.dll");  
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_USERAGENT, "HTTPGATE/1.0 (Inouter 1.0b)");
curl_setopt($ch, CURLOPT_COOKIE, $cook);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, substr(data_encode($_POST), 0, -1) );

echo curl_exec($ch);
curl_close ($ch);
?>