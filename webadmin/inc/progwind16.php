<html>
<head>
<title>Копирование...</title>
<META http-equiv="Content-Type" content="text/html; charset=windows-1251">
<META NAME="Author" CONTENT="Maxim Tushev">
<link href="../img/main.css" rel="stylesheet" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
insArrMod = new Array ("","ftp_mkfiles.php","ftp_mkspec.php","ftp_mktemplates.php","ftp_mktmpimg.php","ftp_mkuserimg.php","ftp_mkeditorimg.php","db_mkstruct.php","db_mkdata.php","db_mkmorf.php");
insArrModMess = new Array ("","Идет копирование файлов ядра системы...","Идет копирование функциональных модулей...","Идет копирование шаблонов...","Идет копирование картинок шаблонов...","Идет копирование пользовательских файлов...","Идет копирование картинок редактора...","Идет установка структуры БД...","Идет импорт данных в БД...","Идет установка морфологического словаря...");
finishString='Установка завершена!';

InstallStep=0;
idRAW=0;

function progress(p) {
	progressDiv.style.width=p;
	cnt.innerHTML=p+'%';
}
	
function stopindx() {
	while(InstallStep<insArr.length) {
		if(insArr[InstallStep]==1) {
			startProgress(insArrModMess[InstallStep],"/webadmin/install.php?mod="+insArrMod[InstallStep]+"&ch="+idRAW);
			InstallStep++;
			return 0;
		}
		InstallStep++;
	}
	window.close();
}

function stopErr(str) {
	alert(str);
	window.close();
}

function startProgress(txt,mod) {
	progress(0);
	progressText.innerHTML=txt;
	document.frames["progressFrame"].navigate(mod);
}

InstallStep=1;

insArr=window.dialogArguments;
//insArr=new Array(8,0,0,0,0,0,0,0,1,0,0);
idRAW=insArr[0];

while(insArr[InstallStep]!=1 && InstallStep<insArr.length) {
	InstallStep++;
}

function goAct() {
	if(insArr.length==2) {
		startProgress(insArr[0],insArr[1]);
	} else {
		startProgress(insArrModMess[InstallStep],"/webadmin/install.php?mod="+insArrMod[InstallStep]+"&ch="+idRAW);
		InstallStep++;
	}
}

function newAction(aMess,aScript) {
	startProgress(aMess,aScript);
}


//-->
</SCRIPT>
</head>

<basefont size="1" face="MS Sans serif,Tahoma,Arial">
<body bgcolor="#ffffff"  style=';background:#cccccc;padding:10px' onload="goAct()" SCROLL="no">
<div id="progressText"></div>
<BR><BR><table border=0 cellspacing=0 cellpadding=2 width=106 height=23 style='border:1 solid #000000' bgcolor='#ffffff' align='center'>
<tr>
<td><div id='progressDiv' style='position:absolute;width:0;height:12px;background:#ff0000'></div><div id='cnt' style='position:absolute;width:100%;height:100%;text-align:center'></div></td>
</tr>
</table>
<iframe id='progressFrame' src='' width=0 height=0 frameborder=0></iframe>
<!-- / Progress block -->
</body>
</html>