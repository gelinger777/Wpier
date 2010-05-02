<?
require "./autorisation.php";
?>
<HTML>
<HEAD>
<?
if(isset($_POST["wrd"]) && isset($_POST["frm"])) {

	$_POST["frm"]=strtolower(htmlspecialchars($_POST["frm"]));
	$_POST["wrd"]=strtolower(htmlspecialchars($_POST["wrd"]));

	$wrds=array($_POST["wrd"]);
	$_POST["frm"]=explode(",",$_POST["frm"]);
	foreach($_POST["frm"] as $w) {
		$w=trim($w);
		if($w && !in_array($w,$wrds)) {
			$wrds[]=$w;
		}
	}

	$cod=array();
	$db->query("SELECT cod FROM wordsforms WHERE wrd like binary '".join("' or wrd like binary '",$wrds)."'",$DB_MAIN);
	while($db->next_record()) if (!in_array($db->Record["cod"],$cod)) $cod[]=$db->Record["cod"];

	if(count($cod)) {
		if(count($cod)>1) {
			$db->query("UPDATE wordsforms SET cod='".$cod[0]."' WHERE cod='".join("' or cod='",$cod)."'",$DB_MAIN);
		}
		$cod=$cod[0];
	} else {
		$cod=1;
		$db->query("SELECT cod FROM wordsforms ORDER BY cod DESC LIMIT 1",$DB_MAIN);
		if($db->next_record()) $cod=$db->Record["cod"]+1;;
	}

	foreach($wrds as $w) {
		$db->query("INSERT INTO wordsforms (cod,wrd) VALUES ($cod,'$w')",$DB_MAIN);
	}
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function getVal() {
	parent.returnWrd();
}
//-->
</SCRIPT>
<?} else {?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function getVal() {
	ar=parent.getValues();
	document.all("wrd").value=ar[0];
	document.all("frm").value=ar[1];
	document.forms[0].submit();
}
//-->
</SCRIPT>
<?}?>
</HEAD>
<BODY onload="getVal();";>
<form method="post">
<input name="wrd">
<input name="frm">
</form>
</BODY>
</HTML>