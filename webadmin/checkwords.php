<?require "./autorisation.php";?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Ошибка!</TITLE>
<META NAME="Author" CONTENT="Maxim Tushev">
<link href="./img/main.css" rel="stylesheet" type="text/css">
<SCRIPT LANGUAGE="JavaScript" src="./js/footer.js"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
<!--
stat=false;
function addWrd() {
	if(!stat) {
		window.dialogHeight="260px";
		addDiv.style.display="";
		stat=true;
	} else {
		addDiv.style.display="none";
		window.dialogHeight="120px";
		stat=false;
	}
}

function returnWrd() {
	window.returnValue=document.all('wrd').value;
	//alert(document.all('wrd').value);
	window.close();
}

function saveWrd() {
	document.frames["progressFrame"].navigate("./addwrd.php");
}

function getValues() {
	ar=new Array();
	ar[0]=document.all('wrd').value;
	ar[1]=str_replace("\n",",",document.all('wrdForms').value);
	return ar;
}
//-->
</SCRIPT>
</HEAD>
<BODY bgcolor="#cFcFcF" onload="document.all('wrd').value=window.dialogArguments;document.all('wrdDiv').innerText=window.dialogArguments;" SCROLL="no">
<!-- Check spell block -->
<table border=0 cellspacing=0 cellpadding=5 width="100%" height="100%">
<tr><form>
<td valign="top">
	<table border=0 cellspacing=0 cellpadding=1 width="100%">
	<tr><td>
		Слово <b id="wrdDiv"></b>&nbsp;возможно содержит ошибку<BR><BR>
		<input name="wrd" style="width:100%">
	</td></tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=1 width="100%">
	<tr>
	<td><input type="button" value="Заменить" onclick="returnWrd()" style="width:100%"></td>
	<td><input type="button" value="Добавить в словарь" onclick="addWrd()" style="width:100%"></td>
	<td><input type="button" value="Закрыть" onclick="window.close()" style="width:100%"></td>
	</table>

	<table border=0 cellspacing=0 cellpadding=1 width="100%" id="addDiv" style="display:none">
	<tr>
		<td><hr>Введите все возможные формы данного слова:</td>
	</tr>
	<tr>
		<td height="100">
		<textarea name="wrdForms" style="height:100%;width:100%;border:1 solid #000000"></textarea>
		</td>
	</tr>
	<tr>
		<td>
		<input type="button" value="Сохранить" onclick="saveWrd()" style="width:100%">
		</td>
	</tr>
	</table>
	<iframe id='progressFrame' src='' width=0 height=0 frameborder=0></iframe>
</td>
</tr></form>
</table>

<!-- / Check spell block -->
</BODY>
</HTML>
