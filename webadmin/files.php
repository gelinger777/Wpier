<?
require "./autorisation.php";

$show=-1;
if(isset($_GET["show"])) {
	$_SESSION["Fshow"]=intval($_GET["show"]);
} 
if(isset($_SESSION["Fshow"])) $show=$_SESSION["Fshow"];
$ACCESSASSOC=array("0"=>"клиент.","1"=>"рег.","2"=>"обычн.");
?>
<html>
<head>
<link rel="stylesheet" href="./img/main.css" type="text/css" />
<SCRIPT LANGUAGE="JavaScript">
<!--
function insLnk(title,file) {
	parent.frames["editorframe"].insForLink(1,title,'/go/?dwl=1&f='+file);
	return false;
}
//-->
</SCRIPT>
</head>
<BODY bgcolor="#ffffff" id="bbody" style="padding:0;margin:0">
<select onchange="navigate('./files.php?show='+this.value)" style="width:100%;font-size:9px">
<option value="-1">Все файлы</option>
<option value="2" <?=($show=="2"? "selected":"")?>>Только для обычных посетителей</option>
<option value="1" <?=($show=="1"? "selected":"")?>>Только для зарегистрированных</option>
<option value="0" <?=($show=="0"? "selected":"")?>>Только для клиентов</option>
</select>
<table border="0" cellspacing="2" cellpadding="1" width="100%">
<tr bgcolor="#cfcfcf">
<td><a href="?orderby=fileNewname" class="menu">Имя файла</a></td>
<td><a href="?orderby=fileAccess" class="menu">Доступ</a></td>
</tr>
<?
$orderby="filenewname";
if(isset($_GET["orderby"])) $orderby=htmlspecialchars($_GET["orderby"]);
$db->query("SELECT id, fileNewname, fileAccess FROM files ".($show>-1? "WHERE fileAccess='$show'":"")." ORDER BY $orderby");
while($db->next_record()) {
	echo "<tr bgcolor='#eeeeee'><td>";
	echo "<a href='' onclick='return insLnk(\"".$db->Record["filenewname"]."\",\"".$db->Record["id"]."\")'>".$db->Record["filenewname"]."</a></td>";
	echo "<td>".$ACCESSASSOC[$db->Record["fileaccess"]]."</td></tr>";
}
?>
</table>
</body>
</html>