<?
$SYS=1;
$ACCESS_LEVEL=1;
require ("./AdminConsoleHeader.php");
//include ("../access/db_access.php");

/*$AdminLog="";
		$AdminName="";
		$AdminEmail="";
		$MailEncoding="";
		$access="";
		$autoresponder="";
		$helpstring="";
		$access_details="";
		$access_block

$access_block="";  */

foreach($_POST as $key=>$val) $$key=$val;
foreach($_GET as $key=>$val) $$key=$val;

$helpstring="Добавление, редактирование и удаление пользовательских учетных записей.";

if (isset($_POST["access_details"])) {
	$access_details=join(",",$_POST["access_details"]);
} else $access_details="";

if(!isset($access_block)) $access_block="";

if (isset($_POST["upd"])) {
if($access<=0) $access_details="all";
$sql="UPDATE settings SET
					AdminLogin = '$AdminLog',
					".(checkPass($_POST["adminpassword"],$_POST["AdminPassword1"])? "AdminPassword='".wpier_hash($_POST["adminpassword"])."',":"")."
					AdminEmail = '$AdminEmail',
					AdminName = '$AdminName',
					MailEncoding = '$MailEncoding',
					autoresponder = '$autoresponder',
					access = '$access',
					access_details='".(isset($access_details)? $access_details:"")."',
					access_block='$access_block'
WHERE id='$id' ".($ACCESS>0? " and access>'$ACCESS'":"");
	$db->query($sql);
	$helpstring="Информация сохранена";
	//$sub="ch";
}

if (isset($_POST["ins"]) && ($ACCESS<$access || $ACCESS<=0)) {
	if($access<=0) $access_details="all";

	$db->query("INSERT INTO settings (AdminLogin,AdminPassword,AdminName,AdminEmail,NumRows,MailEncoding,PagesInRow,access,autoresponder,access_details,access_block) VALUES ('$AdminLog', '".wpier_hash($_POST["adminpassword"])."', '$AdminName', '$AdminEmail', '10', '$MailEncoding', '0', '$access', '$autoresponder', '$access_details','$access_block')");
	$helpstring="Запись созранена";
	//$sub="ch";

	// Что бы не постили 2 раза одинаковые логины
	header("Location: ./content_admins.php");
	exit;
}

if ($sub=='del') {
	if ($access==count($ACCESS_PROFILE)) {
		$db->query("SELECT id FROM settings WHERE access='0'");
		if($db->num_rows()>1) {
			$db->query("DELETE FROM settings WHERE id='$id'  and access>$ACCESS");
			$helpstring="Запись удалена";
		} else {?>
			<SCRIPT LANGUAGE="JavaScript">
			<!--
			alert("В системе должен присутствовать хотя бы один пользователь с правами администратора.\n Вы пытаетесь удалить последнего.");
			//-->
			</SCRIPT>
	<?
				//$helpstring=$error_del_message;
				}
	} else {
			$db->query("DELETE FROM settings WHERE id='$id'".($ACCESS>0? "and access>$ACCESS":""));
			$helpstring="Запись удалена";
	}
}

$LOGINS=array();
$db->query("SELECT id, AdminLogin FROM settings");
while ($db->next_record()) {$LOGINS[$db->Record[0]]=$db->Record[1];}

if ($sub=='ch') {
	$db->query("SELECT * FROM settings WHERE id='$id'".($ACCESS>0? "and access>'$ACCESS'":""));
	if ($db->next_record()) {
		$AdminLog=$db->Record["adminlogin"];
		$AdminName=$db->Record["adminname"];
		$AdminEmail=$db->Record["adminemail"];
		$MailEncoding=$db->Record["mailencoding"];
		$access=$db->Record["access"];
		if($access<0 && !isset($MAINFRAME))  $access=0;
		$autoresponder=$db->Record["autoresponder"];
		$helpstring="Редактирование учетной записи пользователя <b>$AdminName</b>.";
		$access_details=$db->Record["access_details"];
		$access_block=$db->Record["access_block"];
	}
}
if (!isset($access_details)) $access_details="";
if (!isset($start)) $start=0;
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
LoginsAr=new Array();
id=0;
<?
if(isset($_GET["ch"])) echo "id='".intval($_GET["ch"])."';
";
foreach($LOGINS as $k=>$v) {
	echo "LoginsAr[LoginsAr.length]=new Array('".$k."','".$v."');
";
}
?>
function checkLogin() {
	var log=document.all("AdminLog").value;
	for(var i=0;i<LoginsAr.length;i++) {
		if(LoginsAr[i][0]!=id && LoginsAr[i][1]==log) {
			alert("В системе уже зарегистрирован Администратор с логином '"+log+"'\nВведите другой логин!");
			document.all("AdminLog").focus();
			return false;
		}
	}
	return true;
}
//-->
</SCRIPT>

<h2>Управление пользователями</h2>

<?if (($sub=="new") or ($sub=="ch")) {?>

<hr>
<table border=0 cellspacing=1 cellpadding=0 class='td2'>
	<FORM action='<?=$SCRIPT_NAME?>' method='post' name='form_1' onsubmit="return checkLogin()">
	<INPUT type="hidden" name="id" value="<?=(isset($id)? $id:"")?>">
	<tr>
		<td class=td_b1>&nbsp;Уровень доступа:</td>
		<td class='td_b2'><SELECT name="access"><?
foreach($ACCESS_PROFILE as $key=>$val) if($key>$ACCESS || $ACCESS<=0) {
	if(isset($MAINFRAME) && $key<0) echo "<option value='-1' ".((isset($access) && $access==-1)? "selected":"").">Разработчик";
	else {
		echo "<OPTION value='$key' ";
		if (isset($access)) if ($key==$access) echo "selected";
		echo ">$val</OPTION>";
	}
}

	?></SELECT>
		</td>
	</tr>
	<tr>
		<td class=td_b1>&nbsp;Кодировка Email:</td>
		<td class='td_b2'><select name='mailencoding'>
		<?if(!isset($MailEncoding)) $MailEncoding="k";?>
	<option value='k'>koi8-r</option>
	<option value='w' <?=($MailEncoding=="w"? "selected":"")?>>win-1251</option></select></td>
	</tr>
	<tr>
		<td class=td_b1>&nbsp;Имя администратора:</td>
		<td class='td_b2'><input type='text' name='adminname' value='<?=(isset($AdminName)? $AdminName:"")?>'></td>
	</tr>
	<tr>
		<td class=td_b1>&nbsp;Email администратора:</td>
		<td class='td_b2'><input type='text' name='adminemail' value='<?=(isset($AdminEmail)? $AdminEmail:"")?>'></td>
	</tr>
	<tr>
		<td class=td_b1>&nbsp;Логин администратора:</td>
		<td class='td_b2'><input type='text' name='AdminLog' value='<?=(isset($AdminLog)? $AdminLog:"")?>'></td>
	</tr>
	<tr>
		<td class=td_b1>&nbsp;Пароль:</td>
		<td class='td_b2'><input type='password' name='adminpassword' value=''>
		</td>
	</tr>
	<tr>
		<td class=td_b1>&nbsp;Пароль (подтверждение):</td>
		<td class='td_b2'><input type='password' name='AdminPassword1' value=''>
		</td>
	</tr>
	<tr>
		<td class=td_b1>&nbsp;Подпись в письмах:</td>
		<td class='td_b2'><TEXTAREA name='autoresponder' rows=5 cols=50><?=(isset($autoresponder)? $autoresponder:"")?></TEXTAREA>
		</td>
	</tr>

	<tr>
		<td class=td_b1>&nbsp;Блокировка пользователя:</td>
		<td class='td_b2'><input type='checkbox' name='access_block' value='1' <?=(isset($access_block) && $access_block? "checked":"")?>> заблокировать
		</td>
	</tr>

<?if(isset($ACCESS) || $ACCESS<2) {?>
	<tr>
		<td class=td_b1>&nbsp;Доступ к спец. разделам:</td>
		<td class='td_b2'>
		<?
			$i=1;
			$sp=explode(",", $access_details);
			$access_details=array();
			for ($i=0;$i<count($sp);$i++) $access_details[$sp[$i]]=1;

			echo "<b>&nbsp;Special blocks:</b><BR><BR><p style='margin-left: 10pt; margin-top:0; margin-bottom:0'><INPUT type='checkbox' name='access_details[0]' value='all'";
			if (isset($access_details["all"])) echo " checked";
			echo "> <b>Доступ ко всем разделам</b><br>";
			include "./menu.inc";
			if(isset($menu_items) && is_array($menu_items)) foreach($menu_items as $key=>$val) if($val[1]) {
				$sp=explode("_",$key);
				echo "<p style='margin-left: ".(count($sp)*10)."pt; margin-top:0; margin-bottom:0'><INPUT type='checkbox' name='access_details[$i]' value='$key'";
				if (isset($access_details[$key]) || isset($access_details["all"])) echo " checked";
				echo "> ".$val[0]."<br>";
				$i++;
			}
			echo "<hr><b>&nbsp;Admin tools:</b><BR><BR>";
			if(isset($menu_tool) && is_array($menu_tool)) foreach($menu_tool as $key=>$val) {
				$sp=explode("_",$key);
				echo "<p style='margin-left: ".(count($sp)*10)."pt; margin-top:0; margin-bottom:0'><INPUT type='checkbox' name='access_details[$i]' value='$key'";
				if (isset($access_details[$key]) || isset($access_details["all"])) echo " checked";
				echo "> ".$val."<br>";
				$i++;
			}
	?><BR>
		</td>
	</tr>
<?}?>
			</table><BR>


<INPUT type="hidden" <?if ($sub=="ch") echo "name='upd'"; else  echo "name='ins';"?> value="y">

<INPUT TYPE="submit"   VALUE="Сохранить">
<INPUT TYPE="button" value="Закрыть" onclick="window.location='<?=$SCRIPT_NAME?>'">
</FORM>
<hr>
</form>

<?
} else {
echo "<a href='".$SCRIPT_NAME."?sub=new'><b>[Добавить пользователя]</b></a> ";
echo "<hr>";

 if (!isset($orderby)) $orderby="access DESC, AdminLogin";else  if ($orderby=="") $orderby="access DESC, AdminLogin";

$sql="SELECT id, AdminLogin, AdminName, access, access_block FROM settings ".($ACCESS>0? "WHERE access>'$ACCESS'":"")." ORDER BY ".$orderby." LIMIT ".$start.", ".$COUNT_ROWS;

$db->query($sql);
if ($db->num_rows()) {
echo "<table border=0 cellspacing=1 cellpadding=0 class='td2'>";
echo "<TR class='td_top'>";
echo "<td>&nbsp;<a href='$SCRIPT_NAME?orderby=AdminLogin' style='color:#FFFFFF'>Логин</a>&nbsp;</td>";
echo "<td>&nbsp;<a href='$SCRIPT_NAME?orderby=AdminName' style='color:#FFFFFF'>Пользователь</a>&nbsp;</td>";
echo "<td>&nbsp;<a href='$SCRIPT_NAME?orderby=access DESC, AdminLogin' style='color:#FFFFFF'>Статус</a>&nbsp;</td>";
echo "<td>&nbsp;Блокировка&nbsp;</td>";
echo "<td>&nbsp;</td><td>&nbsp;</td></TR>";
$i=0;
while ($db->next_record()) {
	if($db->Record["access"]<0 && !isset($MAINFRAME))  $db->Record["access"]=0;
	echo "<tr valign='top'>";
	echo "<td class='td_b1'><p class='ptd'><b>".$db->Record["adminlogin"]."</b></p></td>";
	echo "<td class='td_b1'><p class='ptd'><b>".$db->Record["adminname"]."</b></p></td>";
	echo "<td class='td_b1'><p class='ptd'><b>".$ACCESS_PROFILE[$db->Record["access"]]."</b></p></td>";
	echo "<td class='td_b1'>".($db->Record["access_block"]? "<center><b style='color:red'>блок.</b></center>":"")."</td>";
	echo "<td class='td_b1'><p class='ptd'><a href='$SCRIPT_NAME?id=".$db->Record["id"]."&sub=ch'><IMG SRC='./img/edit.gif'  BORDER=0 ALT='Редактировать'></a></p></td>";

	echo "<td class='td_b2'><p class='ptd'><a style='cursor:hand' onclick='if (confirm(\"Удалить пользователя?\")) window.location=\" $SCRIPT_NAME?access=".$db->Record["access"]."&id=".$db->Record["id"]."&start=".$start."&sub=del\";'><IMG SRC='./img/del.gif'  BORDER=0 ALT='Удалить'></a></p></td></tr>";

	$i++;
}
echo "</table><center>";

if (intval($start)>0) {   $startpos=$start-$COUNT_ROWS;
   echo "<a href='$SCRIPT_NAME?orderby=".$orderby."&start=".$startpos."'>&lt;&lt;&lt;</a> ";}
echo "&nbsp;&nbsp;&nbsp;<b>".$start." &#150; ".($start+$db->num_rows())."</b>&nbsp;&nbsp;&nbsp;";
if ($COUNT_ROWS==$i) {
   $startpos=$start+$COUNT_ROWS;
   echo "<a href='$SCRIPT_NAME?orderby=".$orderby."&start=".$startpos."'>&gt;&gt;&gt;</a>";
}
echo "</center>";
}}
require ("./AdminConsoleFooter.php");?>