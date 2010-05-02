<?
$SETTING_TAB="y";
require "./autorisation.php";
foreach($_GET as $k=>$v) $$k=addslashes($v);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> New Document </TITLE>
</HEAD>

<BODY id="bbody" onload="">

<SCRIPT LANGUAGE="JavaScript">
<!--

<?
$db->query("SELECT $fldkey, $fldval FROM $tab WHERE ".((isset($whr) && $whr)? "$whr and ":"")." $fldval like binary '$search%' ORDER BY  $fldval LIMIT 101");
if($db->num_rows()>100) echo "alert('".$TEXTS["LotOfRecordsAlert"]."');";
while($db->next_record()) {
	$db->Record[$fldval]=strip_tags(str_replace("\n"," ",str_replace("\r","",$db->Record[$fldval])));
	if(strlen($db->Record[$fldval])>100) {
		$db->Record[$fldval]=substr($db->Record[$fldval],0,100);
		$db->Record[$fldval]=substr($db->Record[$fldval],0,strrpos($db->Record[$fldval]," "));
	}
	echo "parent.add_opt('".$elm."_ch', '".$db->Record[$fldkey]."', '".$db->Record[$fldval]."');";
}?>
parent.document.getElementById("loadprocess").style.display="none"

//-->
</SCRIPT>

</BODY>
</HTML>