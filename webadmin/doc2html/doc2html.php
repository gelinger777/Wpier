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
   $txt="";
   if(file_exists($f)) {

    $s=file_get_contents($f);

    $id=intval($_GET["id"]);

    $so=new Prepare_Html('');

    $so->Tmpdir=$_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["TEMP_DIR"]."/";
    $t=mktime();
    if(!file_exists($_SERVER["DOCUMENT_ROOT"].$_CONFIG["TEXT_LINKED_FILES_DIR"]."content")) {
     mkdir($_SERVER["DOCUMENT_ROOT"].$_CONFIG["TEXT_LINKED_FILES_DIR"]."content");
    }
    if(!file_exists($_SERVER["DOCUMENT_ROOT"].$_CONFIG["TEXT_LINKED_FILES_DIR"]."content/$t")) {
     mkdir($_SERVER["DOCUMENT_ROOT"].$_CONFIG["TEXT_LINKED_FILES_DIR"]."content/$t");
    }
    $so->Imgdir=$_CONFIG["TEXT_LINKED_FILES_DIR"]."content/$t/";

    $txt=trim($so->CleaneTags($s));

    if(substr($txt,strlen($txt)-5)=="</td>") $txt.="</tr></table>"; // Кастыль для WV (оно при конвертации не закрывает последнюю таблицу)
	unlink($f);
   }


   if(trim($txt)) {
    $_GET["text"]=(isset($_POST["title"]) && $_POST["title"]? $_POST["title"]:$so->Title);


    if(isset($_GET["mode"]) && $_GET["mode"]==2) {
      $db->query("SELECT id FROM content WHERE catalogue_ID='$id' and (spec='' or spec is NULL) ORDER BY id LIMIT 1");
      if($db->next_record()) {
         $db->query("UPDATE content SET text='".str_replace("'","&#39;",$txt)."' WHERE id='".$db->Record[0]."'");
         $db->query("UPDATE catalogue SET attr='1' WHERE id=$id and (attr='' or attr is NULL)");
      }
      ?>
     <script>
        ParentW.Ext.getCmp('doc2html-win').close();
     </script>
      <?
      exit;
     }

     $InsertedId=0;
     include "../inc/tree/treefunction.php";
     include "../inc/tree/add.php";

     if(isset($_POST["dir"]) && $_POST["dir"] && $InsertedId) {
      $db->query("UPDATE catalogue SET dir='".addslashes($_POST["dir"])."' WHERE id=$InsertedId");
     }

     if($txt) {
        $db->query("SELECT id FROM content WHERE catalogue_ID='$InsertedId' ORDER BY id LIMIT 1");

        if($db->next_record()) {
          $db->query("UPDATE content SET text='".str_replace("'","&#39;",$txt)."' WHERE id='".$db->Record[0]."'");
        }
     }

    ?>
    <script>

    <?if(intval($_GET["id"])){?>
    ParentW.contextmenu.curNode.leaf=false;
    <?}?>
    ParentW.CurTreeNode.appendChild(new ParentW.Ext.tree.TreeNode({id:'<?=$InsertedId?>',text:'<?=$_GET["text"]?>',leaf:true, cls:'file',iconCls:'file1'}));

    ParentW.Ext.getCmp('doc2html-win').close();
    </script>
    <?
    exit;
   } else {?>
   <script>
   alert('Не удалось прочитать файл.');
   </script>

   <?}
}
?>
<style>
body {
    padding:10px;
    font-size:12px;
    font-family:Tahoma,sans-serif,Arial;
    background:#ededed;
}

input {
    width:200px;
    border:1px solid #575757;
}
</style>
<FORM ENCTYPE='multipart/form-data' name='MainEditForm' id='MainEditForm' METHOD='POST'>
<?if(isset($_GET["mode"]) && $_GET["mode"]==1){?>
Заголовок окна<br><input type="text" name="title" value="" ><br>
Директория<br><input type="text" name="dir" value=""><br>
<?}?>
Файл MS Word 97-2000 (.doc)<br><input type="file" name="file" style="width:150px" onchange="ParentW.Ext.getCmp('doc2html-ok').enable()"><br>
<?if($_GET["mode"]==1){?>Содержимое файла будет вставлено в новую страницу<?}else{?>
Содержимое файла заменит текстовые данные текущей страницы.
<?}?>
</FORM>
</body>
</html>
