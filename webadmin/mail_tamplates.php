<?
require ("./AdminConsoleHeader.php");

$tb_name="email_tamplates";
$pagetitle="Шаблоны писем автореспондера";

// Оформляем отправленные формой ТЕКСТОВЫЕ данные в корректную форму
if(isset($subject)) $subject=undangerstr($subject);
if(isset($fromemail)) $fromemail=undangerstr($fromemail);
if(isset($text)) $text=undangerstr($text);

//************************************************************************************
// Внесение изменений в таблицы
//************************************************************************************
$updaterez=0;
@$db->lock($tb_name);
if(isset($lastupdatetime)) {
	@$db->query("SELECT lastupdatetime FROM $tb_name WHERE id='$id'");
	if ($db->next_record()) if ($lastupdatetime==$db->Record["lastupdatetime"]) $updaterez=1; else $helpstring=$error_save_message;
}
if (isset($upd) && $updaterez) {
	$lastupdatetime=date("YmdHis");

// Сохраняем изменения в таблице, ПОЛЕ LASTUPDATETIME НЕ ТРОГАТЬ
	@$db->query("UPDATE $tb_name SET subject='$subject', fromemail='$fromemail', text='$text', lastupdatetime='$lastupdatetime' WHERE id='$id'");
	
	$helpstring=$save_message;// Значение строки состояния, хранится в файле messages.inc
	$veight_status=1; // 1 -- очистить строку состояния через 5 сек., 0 -- не очищать
}
@$db->unlock($tb_name);
//************************************************************************************
// К Внесение изменений в таблицы
//*********************************************************************************************

//************************************************************************************
// Удаление записи
//************************************************************************************
if ($sub=='del') {
		$db->query("DELETE FROM $tb_name WHERE id='$id'");
		$helpstring=$del_message;
		$veight_status=1;
}

//************************************************************************************
// Чтение записи по определенному id
// ПЕРЕМЕННЫЕ, ПОЛЯ ФОРМЫ И ПОЛЯ ТАБЛИЦЫ ДОЛЖНЫ СОВПАДАТЬ 
// ПО НАЗВАНИЯМ!!!!!!!!!!!!!!!!!
//************************************************************************************
if (!$upd && !$updaterez) {
	
	$db->query("SELECT * FROM $tb_name WHERE id='$id'");
	if ($db->next_record()) {
		$subject=$db->Record["subject"];
		$fromemail=$db->Record["fromemail"];
		$text=$db->Record["text"];

		$attr=$db->Record["attr"];	
		$lastupdatetime=$db->Record["lastupdatetime"]; // НЕ МЕНЯТЬ
	}
}
if (!isset($start)) $start=0;
?>
<h2><?=$pagetitle?></h2>
<a href="./settings.php"><b>[назад]</b></a><hr>
<?if (($sub=="new") or ($sub=="ch") or (isset($upd) && !$updaterez)) {?>

<FORM ENCTYPE="multipart/form-data"  name="addnews" METHOD=POST>
<INPUT type="hidden" name="id" value="<?=$id?>">
<INPUT type="hidden" name="lastupdatetime" value="<?=$lastupdatetime?>">
<table border=0 cellspacing=0 cellpadding=4>

<tr><td><b>Обратный адрес:</b></td><td><INPUT name="fromemail" value="<?=$fromemail?>" size=30></td></tr>
<tr><td><b>Тема письма:</b></td><td><INPUT name="subject" value="<?=$subject?>" size=100></td></tr>
<script language="JavaScript">
<!--
function insertString(string) {
  oldString = document.forms[0].text.value;
  newString = oldString + string;
 document.forms[0].text.value = newString;
 document.forms[0].text.focus();
}
// -->
</script>
<tr valign="top"><td><b>Текст:</b></td><td><table border=0 cellspacing=0 cellpadding=0>
<tr valign="top"><td><TEXTAREA name="text" cols=60 rows=8><?=$text?></TEXTAREA></td>
<td>&nbsp;&nbsp;</td>
<td>
<b>Доступные переменные:</b><BR>
<a href="#" onclick="insertString('%ФИО%')">%ФИО%</a><BR>
<a href="#" onclick="insertString('%Должность%')">%Должность%</a><BR>
<a href="#" onclick="insertString('%Название компании%')">%Название компании%</a><BR>
<a href="#" onclick="insertString('%Логин%')">%Логин%</a><BR>
<a href="#" onclick="insertString('%Пароль%')">%Пароль%</a><BR>
	
</td></tr>
</table>	
</td></tr>

<tr><td>&nbsp;</td><td>
<INPUT type="hidden" <?if ($sub=="ch") echo "name='upd'"; else  echo "name='ins';"?> value="y">
<INPUT TYPE="submit"   VALUE="Сохранить">
<INPUT TYPE="button" value="Обновить" onclick="window.location='<?="$SCRIPT_NAME?id=$id&sub=ch"?>'">
<INPUT TYPE="button" value="Закрыть" onclick="window.location='<?=$SCRIPT_NAME?>'">
</FORM>
</td></tr></table>
<hr>
<?
if ($upd && !$updaterez) include "updateerror.inc";
} else {

 //*****************************************************************************
 // Первичная сортировка
  //*****************************************************************************
 if (!isset($orderby)) $orderby="id";else  if ($orderby=="") $orderby="iв";

$sql="SELECT id, subject FROM $tb_name ORDER BY ".$orderby." LIMIT ".$start.", ".$COUNT_ROWS;
$db->query($sql);
if ($db->num_rows()) {
echo "<table border=0 cellspacing=1 cellpadding=4 class='td2'>";
echo "<TR class='td_top'>";
echo "<td>&nbsp;Письмо&nbsp;</td>";

echo "<td>&nbsp;</td></TR>";
$i=0;
while ($db->next_record()) {
	
	echo "<tr valign='top'>";
	echo "<td class='td_b1'><b>".$db->Record["subject"]."</b></td>";

	echo "<td class='td_b2'><a href='$SCRIPT_NAME?id=".$db->Record["id"]."&sub=ch'><IMG SRC='./img/edit.gif' WIDTH='15' HEIGHT='13' BORDER=0 ALT='Редактировать'></a></td>";
	
	echo "</tr>";

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
require ("./AdminConsoleFooter.php");
?>