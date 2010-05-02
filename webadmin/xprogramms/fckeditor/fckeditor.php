<?
include "../../autorisation.php";

$RES_PATH=substr($_SERVER["SCRIPT_NAME"],0,strrpos($_SERVER["SCRIPT_NAME"],"/"))."/";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>        
<head>
  <title>Inreco 1.0-betta</title>
  <META http-equiv="Content-Type" content="text/html; charset=windows-1251">
  <META NAME="Author" CONTENT="Maxim Tushev">
  <script type="text/javascript" src="fckeditor.js"></script>
</head>
<body style="padding:0;margin:0;" scroll="no"><div style="width:100%;height:100%"><form style="margin:0;padding:0" action="#" method="post" onsubmit="savetext();return false;">
<script type="text/javascript">
var sBasePath = "<?=$RES_PATH?>" ;
var oFCKeditor = new FCKeditor( 'FCKeditor1' );
oFCKeditor.BasePath	= sBasePath ;
var sSkinPath = sBasePath + 'editor/skins/office2003/' ;
oFCKeditor.Config['SkinPath'] = sSkinPath ;

oFCKeditor.Height	= "100%" ;
oFCKeditor.Value	= '' ;  
oFCKeditor.Create() ; 

function GetHtml(t,f) {
  var fck=FCKeditorAPI.GetInstance('FCKeditor1');
  return fck.prevObj.PrepStorePics(fck.GetXHTML(),parent.UPLOAD_IMG_DIR+t+'/'+f+'/');
}

<?
$text="";
if(isset($_GET["wid"])){?>
function FCKeditor_OnComplete(editorInstance) {
  FCKeditorAPI.GetInstance('FCKeditor1').SetHTML(parent.FCK.GetText('<?=$_GET["wid"]?>',window));
}
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
   
function FCKeditor_OnComplete(editorInstance) {
  FCKeditorAPI.GetInstance('FCKeditor1').SetHTML(document.getElementById('text').value);
}
function savetext() {

  // Регистрация события в FF
  //FCKeditorAPI.GetInstance('FCKeditor1').EditorWindow.document.body.addEventListener("mousedown", function (event){alert('window')}, true);
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
  function FCKeditor_OnComplete(editorInstance) {
    alert(document.getElementById('text').value);
    FCKeditorAPI.GetInstance('FCKeditor1').SetHTML(document.getElementById('text').value);
  }
  function savetext() {
    return false;
  } 
<?}else{?>
function  savetext() {
  alert('Действие не определено');
}
<?}?>

function UpdateHTML(text) {
  FCKeditorAPI.GetInstance('FCKeditor1').SetHTML(text);
  //FCKeditorAPI.GetInstance('FCKeditor1').InsertHtml(text);
}         
</script>
<textarea id="text" style="display:none;width:1px;height:1px"><?=$text?></textarea>
</form></div></body>
</html>
