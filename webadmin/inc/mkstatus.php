<?
$TMP=eregi_replace("<body ",'<BODY onload="InouterBuilderGetModList()" ',$TMP);

        $s= '<SCRIPT LANGUAGE="JavaScript">
function InouterBuilderGetModList() {

LogLoadModule=true;
window.status="pagecod:'.$CurrentCod.'";
t=window.setTimeout("window.status=\'\'",100);
window.status="moduls:'.((isset($SpecBloksDescriptsArray) && count($SpecBloksDescriptsArray))? str_replace("\n","",str_replace("\r","",join("|",$SpecBloksDescriptsArray))).'|':'').'mtpl~'.str_replace('..','',$CurrentTemplateFile).'~'.$_SERVER["SERVER_ADDR"].':'.$_SERVER["SERVER_PORT"].'/'.$CurrentCod.'|";	
t=window.setTimeout("window.status=\'\'",100);

}
</SCRIPT>'; 

$TMP=eregi_replace("</body>",$s.'</BODY>',$TMP);
?>