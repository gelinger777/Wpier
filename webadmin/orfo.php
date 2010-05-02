<?
require "./autorisation.php";?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<head>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ldf() {
<?
$rus=array("а","б","в","г","д","е","ж","з","и","к","л","м","н","о","п","р","т","у","ф","х","ц","ч","ш","щ","ъ","ы","ь","э","ю","я","й","с");
$mark=array(".",",",";","'","\"",":","/","\\","+","-","%","=","#","$","!","&","(",")","[","]","{","}","?");

function spell($w) {
global $db,$DB_MAIN;
	$db->query("SELECT cod FROM wordsforms WHERE wrd like binary '$w' LIMIT 1",$DB_MAIN);
	if($db->next_record()) return 0;
	return 1;
}

function clineW($w) {
global $mark;
	foreach($mark as $m) $w=str_replace($m," ",$w);
	return $w;
}

if(isset($_POST["checktxt"])) {
	
	$txt=trim(str_replace("ё","е",strip_tags(str_replace("<"," <",$_POST["checktxt"]))));
	$t="";
	while($t!=$txt) {
		$t=$txt;
		$txt=str_replace("  "," ",$txt);		
	}
	$txt=clineW($txt);
	$txt=explode(" ",$txt);
	$out=array();
	foreach($txt as $w) {
		$W=trim($w);
		$w=strtolower($W);
		//echo $w[0]."=<br>";
		if($w && in_array($w[0],$rus) && strlen($w)>3 && spell($w) && !in_array($w,$out)) {
			$out[]=$W;
		}
	}
?>
parent.getSpellError('<?echo join(",",$out);?>');
<?}?>
}
//-->
</SCRIPT>
</head>
<BODY onload="ldf()">
<form method="post"><input name="checktxt"></form>
</BODY>
</HTML>
<?exit;?>