<?
$tbnames=$db->table_names();

if(!isset($tbnames["cmsstandards"])) {
$db->query("CREATE TABLE cmsstandards (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
user INT NOT NULL ,
modname VARCHAR( 100 ) NOT NULL ,
sets TEXT NOT NULL ,
INDEX ( `user` ) 
);");
}

?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function SaveStandard() {
	var obj=new Array();
	var s="";
	<?foreach($F_ARRAY as $k=>$v) echo "obj[obj.length]='$k';";?>
    for(var i=0;i<obj.length;i++) {
		if(document.forms[0].elements(obj[i])!=null) {
			switch(document.forms[0].elements(obj[i]).type) {
				case "text":s+=obj[i]+"|text|"+document.forms[0].elements(obj[i]).value+"*n*";break
				case "checkbox":if(document.forms[0].elements(obj[i]).checked) s+=obj[i]+"|checkbox|*n*";break;
				case "select-one":s+=obj[i]+"|select|"+document.forms[0].elements(obj[i]).value+"*n*";break;
			}			
		}
	}
	lookup("/<?=$_CONFIG["ADMINDIR"]?>/inc/savestandards.php?m=<?=$EXT?>&s="+s,"StandardAlert");
}

function StandardAlert(txt) {
	alert('Установки сохранены');
}

<?if(isset($_GET["new"])) {?>
function StandardSetDef() {
	<?
    if(isset($ADMIN_ID)) {
	$db->query("SELECT sets FROM cmsstandards WHERE user='".$ADMIN_ID."' and modname='".$EXT."'");
	if($db->next_record()) {
		$s=explode("*n*",$db->Record[0]);
		foreach($s as $ss) {
			$ss=explode("|",$ss);
			if(isset($ss[2])) echo "StandardElmSet('".$ss[0]."','".$ss[1]."','".$ss[2]."');";
		}
	}
	}
	?>
}

function StandardElmSet(nm,tp,val) {
/*	if(document.forms[0].elements(nm)!=null) {
		var o=document.forms[0].elements(nm);
		switch(tp) {
			case "text":o.value=val;break;
			case "checkbox":o.checked=true;break;
			case "select":{
				for(var i=0;i<o.options.length;i++) {
					if(o.options[i].value==val) o.options[i].selected=true;
				}
			} break;
		}
	}  */
}
<?}?>
//-->
</SCRIPT>