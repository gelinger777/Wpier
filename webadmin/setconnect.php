<?if(!isset($_GET["act"])) exit;
include "autorisation.php";             

if($_GET["act"]=='chng' && isset($_POST["pages"]) && isset($_POST["val"])) {  
  $ids=array();
  $_GET["id"]=explode(",",$_GET["id"]);
  foreach($_GET["id"] as $v) $ids[]=intval($v);
  $t=AddSlashes($_GET["t"]);

  if(!isset($_GET["fold"])) $_GET["fold"]="id";
  
  if($_GET["fold"]!='id') {
    $db->query("SELECT ".AddSlashes($_GET["fold"])." FROM ".$t." WHERE id='".join("' or id='",$ids)."'");
    $ids=array();
    while($db->next_record()) {
      $ids[]=$db->Record[0];
    }
  } 
  $_POST["pages"]=intval($_POST["pages"]); 

  $db->query("DELETE FROM ".$t."catalogue WHERE pgid='".$_POST["pages"]."' and rowidd in (".join(",",$ids).")");
  if($_POST["val"]) {   
	  foreach($ids as $id) {
		  $db->query("INSERT INTO ".$t."catalogue (pgid,rowidd) VALUES ('".$_POST["pages"]."','$id')");
	  }
  }
  echo "OK";
  exit;
}
if($_GET["act"]=="show") {
	if(!isset($_GET["fold"])) $_GET["fold"]="id";
// Выводим интерфейс модуля управления связями
?>
var gstr='ext=<?=$_GET["ext"]?>&t=<?=$_GET["t"]?>&id=<?=$_GET["id"]?>&fold=<?=$_GET["fold"]?>';
var Tree = Ext.tree;
var tree = new Tree.TreePanel({
          autoScroll:true,
          animate:true,
          containerScroll: true, 
          bodyBorder:false,
          border:false,
          rootVisible:false,
		  listeners:{
			checkchange:function(n,c) {
				Ext.Ajax.request({
				   url: 'setconnect.php?act=chng&'+gstr,
				   success: function(response) {},
				   params: {
					 pages:n.id,
					 val:(c? 1:0)
				   }
				 });
			}
		  },
	      loader: new Tree.TreeLoader({
            dataUrl:'getbranches.php?connect=1&'+gstr
          }),
          tbar:[{
            id:"BtOpen",
            text:DLG.t('Open'),
            tooltip:DLG.t('TreeMenuShowAll'),
            iconCls:'openall',
            handler:function() {              
              tree.expandAll();
            }
         },'-',{
            id:"BtCollapse",
            text:DLG.t('Collapse'),
            tooltip:DLG.t('TreeMenuCollapseAll'),
            iconCls:'closeall',
            handler:function() {              
              tree.collapseAll();
            }
         }
          
          ]    
        });

        // set the root node
        var root = new Tree.AsyncTreeNode({
          text: 'Root',
          draggable:false,
          id:'0'
});
tree.setRootNode(root);

       
var W = new Ext.Window({
    title:DLG.t('ConnWinTitle'),
    layout:'fit',
    width:300,
    height:500,
    minimizable:false,
    maximizable:false,
    plain: true,
    resizable:true,       
    items:[tree],
    buttons: [/*{
      text:DLG.t('Save'),
      handler: function(){
        var a=tree.getChecked();
        var d=new Array();
        for(var i=0;i<a.length;i++) {
          d[d.length]=a[i].id;
        }
        a=AJAX.post('setconnect',{act:'save',ext:'<?=$_GET["t"]?>',t:'<?=$_GET["t"]?>',id:'<?=$_GET["id"]?>',fold:'<?=$_GET["fold"]?>'},{pages:d.join(',')});
        W.close();
      }
    },*/{
      text: DLG.t('Close'),
      handler: function(){
        W.close();
      }
    }]
});

W.show();
<?
} else {
}?>