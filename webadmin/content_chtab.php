<?
$rows=0;
$cols=0;
if (isset($_GET["rows"])) $rows=$_GET["rows"];
if (isset($_GET["cols"])) $cols=$_GET["cols"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE></TITLE>

<SCRIPT LANGUAGE="JavaScript">
<!--
alg="";

function ins_tab() {
	f=document.forms[0];
	st=f.rows.value+"|"+f.cols.value;
	window.returnValue=st; 
	window.close();
}
//-->
</SCRIPT>

<link href="./img/main.css" rel="stylesheet" type="text/css">


</head>

<body bgcolor="#ECECEC" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="document.forms[0].cols.focus()">
<table border="0" bgcolor="#FFE218" width="100%" cellspacing="0">
<FORM method="post" onsubmit="ins_tab(); return false;">
<tr>
	<td align="left"><input type="button"  value="Изменить" onclick="ins_tab();" class="button"></td>
	<td align="right"><input type="button"  value="Закрыть" onclick="window.close();" class="button"></td>
</tr>
</table>
<BR><BR>
<center>
<table  border="0" cellspacing="0">
<FORM method="post">
<tr>
	<td>Столбцов</td>
	<td><input type="text"  value="<?=$cols?>" name="cols" size=6></td>
</tr>
<tr>
	<td>Строк</td>
	<td><input type="text"  value="<?=$rows?>" name="rows" size=6></td>
</tr>
</table>
</center>
</form>
</BODY>
</HTML>
