<?
header("Expires: Thu, Jan 1 1970 00:00:00 GMT\n"); 
header("Pragma: no-cache\n"); 
header("Cache-Control: no-cache\n");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
<title><?= "$CmsName $VersionNum"?></title>
<META http-equiv="Content-Type" content="text/html; charset=windows-1251">
<META NAME="Author" CONTENT="Maxim Tushev">
<link href="./img/main.css" rel="stylesheet" type="text/css">
<link href="/inc/inneradm.css" rel="stylesheet" type="text/css">

</head>

<basefont size="1" face="MS Sans serif,Tahoma,Arial">
<body>
<table border="0" width="100%">
<tr><td class="h2">
Функциональные модули
</td>
<form>
<td align="right">
<input type="button" onclick="parent.hideSpecDiv()" value="Закрыть" class="button">
</td>
</tr>

</table>
<hr>
<?

require "./autorisation.php";
include "./menu.inc";
$specAr=array();
$db->query("SELECT spec FROM content WHERE spec!=''");
while($db->next_record()) {
	$specAr[$db->Record["spec"]]=1;
}
$tbnames=$db->table_names();
foreach($menu_items as $k=>$v) {
	if(!isset($specAr[$k]) || isset($tbnames[$k."catalogue"]) || (isset($v[2]) && $v[2])) echo "<p><input type=button value=' + ' onclick='parent.chSelectedIndx(\"".$k."\");parent.hideSpecDiv();'> ";
	else echo "<p style='color:red'>";
	echo "<b>".$v[0]."</b><pre>".(isset($v[3])? $v[3]:"")."</pre></p>";
}

?>
</form>
</body>
</html>