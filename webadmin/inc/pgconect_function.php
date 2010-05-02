<?
require ("AdminConsoleHeader.php");
$form=0;
if(isset($_GET["form"])) $form=intval($_GET["form"]);
if(isset($_POST["savepg"])) {
	$db->query("DELETE FROM $cnctTable WHERE $cnctFold1='$form'");
	if(isset($_POST["chpg"])) {
		foreach($_POST["chpg"] as $k=>$v) {
			if(isset($_POST["pg"][$k])) {
				$_POST["pg"][$k]=intval($_POST["pg"][$k]);
				$db->query("INSERT INTO $cnctTable ($cnctFold2,$cnctFold1, pageBlock) VALUES ('$k', '$form','".$_POST["pg"][$k]."')");
				$db->query("SELECT id FROM content WHERE catalogue_ID='".$k."' and spec='' ORDER BY id LIMIT ".($_POST["pg"][$k]-1).",1");
				if($db->next_record()) {
					$db->query("UPDATE content SET spec='forms' WHERE id='".$db->Record["id"]."'");
					$db->query("UPDATE catalogue SET attr='1' WHERE cod='".$k."'");
				}
			}
		}
	}
	echo '<SCRIPT LANGUAGE="JavaScript">
	<!--
	opener.refresh_parent();
	//-->
	</SCRIPT>';
}
?>
<b>Привязка формы к страницам</b>
<FORM action="?form=<?=$form?>" method="post">

<INPUT type="submit" name="savepg" value="Сохранить">
<INPUT type="button" value="Закрыть" onclick="window.close()">
<hr>
<?
$selArr=array();

$db->query("SELECT $cnctFold2, pageBlock FROM $cnctTable WHERE $cnctFold1='$form'");
while($db->next_record()) {
	$selArr[$db->Record[$cnctFold2]]=$db->Record["pageBlock"];
}

$db->query("SELECT catalogue.title,catalogue.id,catalogue.pid, catalogue.cod, templates.tmpSchema FROM catalogue LEFT JOIN templates  ON catalogue.tpl=templates.tmpCod ORDER BY catalogue.id");
$tree=array();
while($db->next_record()) $tree[$db->Record["pid"]][$db->Record["id"]]=array($db->Record["title"],$db->Record["cod"],$db->Record["tmpschema"]);

//print_r($tree);

$lvl=0;

function ShowTree($ParentID, $lvl) { 

global $tree,$selArr; 
global $lvl; 
$lvl++; 

	if(isset($tree[$ParentID])) {
		echo("<UL>");
		foreach($tree[$ParentID] as $k=>$v) {
			$ID1 = $k;
			echo "<LI>";
			echo "<input type='checkbox' name='chpg[".$v[1]."]' onclick=\"if(this.checked) div_".$v[1].".style.display='';else div_".$v[1].".style.display='none';\" ".(isset($selArr[$v[1]])? "checked":"")."> <A HREF='/$ID1.htm' target='_blank'><b>".$v[0]."</b></A><BR>\n";
			echo "<div id='div_".$v[1]."' ".(!isset($selArr[$v[1]])? "style='display:none'":"").">";
			for($i=1;$i<=$v[2];$i++)
				echo "<INPUT type='radio' name='pg[".$v[1]."]' ".((isset($selArr[$v[1]]) && $selArr[$v[1]]==$i)? "checked":"")." value='$i' style='border:0' > блок №$i<br>";
			echo "</div>";
			ShowTree($ID1, $lvl); 
			$lvl--;
		}
		echo("</UL>");
	}
}

ShowTree(0, 0); 
?>
<hr>
<INPUT type="submit" name="savepg" value="Сохранить">
<INPUT type="button" value="Закрыть" onclick="window.close()">
</FORM>
<SCRIPT LANGUAGE="JavaScript">
<!--
window.focus();
//-->
</SCRIPT>
<?
require ("AdminConsoleFooter.php");
exit;
?>