<?
include "../../autorisation.php";

if(isset($_GET["getpageinfo"]) && $_GET["getpageinfo"]) {
	$db->query("SELECT catalogue.title, content.title as ttl, content.id FROM catalogue,content WHERE catalogue.id='".intval($_GET["getpageinfo"])."' and catalogue.id=content.catalogue_id and (content.spec='' or content.spec is NULL) ORDER BY content.id");
	if($db->next_record()) {
		echo $db->Record["id"].":".$db->Record["title"].($db->Record["ttl"]? ": ".$db->Record["ttl"]:"");
	} else echo "ERR";
	exit;
}

$RES_PATH=substr($_SERVER["SCRIPT_NAME"],0,strrpos($_SERVER["SCRIPT_NAME"],"/"))."/";

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<title>TinyMCE</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
	// General options
	mode : "textareas",
	theme: "advanced",
	plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,insertobject,syntaxhl",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,insertobject,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,syntaxhl",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	relative_urls : false,

	// Example content CSS (should be your site CSS)
	content_css : "/style/text.css",

	// Перечисляем названия стилей
	theme_advanced_styles: "Address=address",

	// для подсветки кода
	remove_linebreaks : false,
	extended_valid_elements : "textarea[cols|rows|disabled|name|readonly|class]",


	// Drop lists for link/image/media/template dialogs
	template_external_list_url : "js/template_list.js",
	external_link_list_url : "js/link_list.js",
	external_image_list_url : "js/image_list.js",
	media_external_list_url : "js/media_list.js",

	oninit:function() {
		//print_r(tinymce.ui.Toolbar());
		tinyMCE.activeEditor.execCommands.mceFullScreen.func();
	}
});

function GetHtml(t,f) {
  var prevObj=new parent.PrevObj();
  return prevObj.PrepStorePics(tinyMCE.activeEditor.getContent(),parent.UPLOAD_IMG_DIR+t+'/'+f+'/');
}

<?
$text="";
if(isset($_GET["wid"])){?>
function savetext() {
  //FCKeditorAPI.GetInstance('FCKeditor1').GetXHTML();
  var s=GetHtml('.files','<?=(mktime()+microtime())?>');
  FCKeditorAPI.GetInstance('FCKeditor1').SetHTML(s);
  parent.FCK.save('<?=$_GET["wid"]?>',s);
  return false;
}
<?}elseif(isset($_GET["editfolder"])){
  $db->query("SELECT ".AddSlashes($_GET["editfolder"])." FROM ".AddSlashes($_GET["t"])." WHERE id='".intval($_GET["id"])."'");
  if($db->next_record()) {$text=$db->Record[0];?>

function savetext() {

  parent.Ext.Ajax.request({
    url: '/<?=$_CONFIG["ADMINDIR"]?>/ajaxfunc.php?chrow=yes<?
    $pgid=0;
    if($_GET["t"]=="content" && $_GET["editfolder"]=="text") {
      $db->query("SELECT catalogue_id FROM content WHERE id='".intval($_GET["id"])."'");
      if($db->next_record()) {
        echo "&pgid=".$db->Record[0];
        $pgid=$db->Record[0];
      }
    }
    ?>',
    success: function(response) {
      //alert(response.responseText);
      if(response.responseText!="OK") return false;

      parent.treeChangeAttr(<?=$pgid?>,1);

      return true;
    },
    params: {
      tab:'<?=$_GET["t"]?>',
      fold:'<?=$_GET["editfolder"]?>',
      id:'<?=$_GET["id"]?>',
      data:escape(GetHtml('<?=$_GET["t"]?>','<?=$_GET["editfolder"]?>'))
    }
  });
  return false;
}
<?}}elseif(isset($_GET["file"])){
$fp=fopen($_GET["file"],"r");
$text=fread($fp,filesize($_GET["file"]));
fclose($fp);
?>

<?}else{?>
function  savetext() {
  alert('Действие не определено');
}
<?}?>


</script>
</head>
<body style="padding:0;margin:0;" scroll="no"><div style="width:100%;height:100%"><form style="margin:0;padding:0" action="#" method="post" onsubmit="savetext();return false;"><textarea id="text" style="width:1px;height:1px"><?=htmlspecialchars($text)?></textarea></form></body>
</html>
