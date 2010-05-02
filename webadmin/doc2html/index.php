<?
include "../autorisation.php";
include "../inc/preparehtml.php";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="en">
<head>
    <title></title>
    <script type="text/javascript" src="<?=$RES_PATH?>getparent.js"></script>
</head>
<body>
<?
if(isset($HTTP_POST_FILES["file"]) && isset($_CONFIG["DOCS_IMPORT"]) && $_CONFIG["DOCS_IMPORT"]) {
   $f=$_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["TEMP_DIR"]."/".mktime().".html";
   exec(str_replace("%fOut%",$f,str_replace("%fInn%",$HTTP_POST_FILES["file"]["tmp_name"],$_CONFIG["DOCS_IMPORT"])));
   $s=file_get_contents($f);

   $s=new Prepare_Html($s);



   ?>
   <script>
   ParentW.contextmenu.curNode.leaf=false;
   ParentW.contextmenu.curNode.appendChild(new ParentW.Ext.tree.TreeNode({id:'111111',text:'<??>',leaf:true, cls:'file',iconCls:'file1'}));
   ParentW.Ext.getCmp('doc2html-win').close();
   </script>
   <?
   exit;
}
?>
<FORM ENCTYPE='multipart/form-data' name='MainEditForm' id='MainEditForm' METHOD='POST'>
Заголовок окна <input type="text" name="title" value=""><br>
Директория <input type="text" name="dir" value=""><br>
Файл (doc, rtf, pdf) <input type="file" name="file" onchange="ParentW.Ext.getCmp('doc2html-ok').enable()"><br>
</FORM>
</body>
</html>