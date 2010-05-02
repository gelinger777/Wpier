<?
/*DESCRIPTOR
1,1
Карта сайта
Карта сайта

files: map.htm
group: структура
*/

//HEAD//
$PROPERTIES=array(
"tbname"=>"",
"pagetitle"=>"Карта сайта",
"template_list"=>"map.htm"
);

//ENDHEAD//

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
  <title>Inreco 1.0-betta</title>
  <META http-equiv="Content-Type" content="text/html; charset=windows-1251">
  <META NAME="Author" CONTENT="Maxim Tushev">
  <link href="styles.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" type="text/css" href="ext/css/ext-all.css" />
  <?if($ADMIN_SKIN) echo '<link rel="stylesheet" type="text/css" href="'.$RES_PATH.'css/xtheme-'.$ADMIN_SKIN.'.css" />';?>
  <script type="text/javascript" src="location/<?=$_CONFIG["ADMIN_LOCATION"]?>/script.js"></script>
  <script type="text/javascript" src="ext/ext-base.js"></script>
  <script type="text/javascript" src="ext/ext-all.js"></script>
  <script type="text/javascript" src="ext/getparent.js"></script>
  <script type="text/javascript" src="ext/locale/ext-lang-<?=$_CONFIG["ADMIN_LOCATION"]?>.js"></script>
  <script type="text/javascript" src="ext/help_call.js"></script>

  <style>
    .save{background-image:url(ext/img/main/save.gif)  !important;}
    .refresh{background-image:url(ext/img/refresh.gif) !important;}
    .openall{background-image:url(ext/img/openall.gif) !important;}
    .closeall{background-image:url(ext/img/closeall.gif) !important;}
    .preview{background-image:url(ext/img/preview.png)  !important;}
  </style>

<SCRIPT LANGUAGE="JavaScript">
HELPMOD='sitemap';

Ext.onReady(function(){
  var Tree = Ext.tree;
  Ext.QuickTips.init();
  tree = new Tree.TreePanel({
          id:'StructTree',
          el:'TreeDiv',
          autoScroll:true,
          animate:true,
          containerScroll: true,
          bodyBorder:false,
          border:false,
          rootVisible:false,
          selModel:new Ext.tree.MultiSelectionModel(),
	  loader: new Tree.TreeLoader({
            dataUrl:'getbranches.php?map=1'
          }),
          tbar:[{
            text:ParentW.DLG.t('Save'),
            id:"BtSave",
            tooltip:ParentW.DLG.t('MapSaveHint'),
            iconCls:'save',
            handler:function() {
              var a=tree.getChecked();
              var d=new Array();
              for(var i=0;i<a.length;i++) {
                d[d.length]=a[i].id;
              }
              d=ParentW.AJAX.post('getbranches',{sitemap:1},{data:d.join(',')});
              if(d=='OK') ParentW.DLG.w('MapSaved');
            }
         },'-',{
            id:"BtRefresh",
            text:ParentW.DLG.t('Refresh'),
            tooltip:ParentW.DLG.t('TreeMenuRefresh'),
            iconCls:'refresh',
            handler:function() {
              tree.getLoader().load(tree.getRootNode());
            }
         },'-',{
            id:"BtOpen",
            text:ParentW.DLG.t('Open'),
            tooltip:ParentW.DLG.t('TreeMenuShowAll'),
            iconCls:'openall',
            handler:function() {
              tree.expandAll();
            }
         },'-',{
            id:"BtCollapse",
            text:ParentW.DLG.t('Collapse'),
            tooltip:ParentW.DLG.t('TreeMenuCollapseAll'),
            iconCls:'closeall',
            handler:function() {
              tree.collapseAll();
            }
         },'-',{
        text:ParentW.DLG.t('Preview'),
        id:'BtPreview',
        tooltip:ParentW.DLG.t('PreviewHint'),
        iconCls:'preview',
        handler:function() {
           var s=ParentW.AJAX.get('getbranches',{findmodpage:'map'});
           s=parseInt(s);
           if(!isNaN(s)) ParentW.page_preview(s);
         }
        }]

        });

        // set the root node
        var root = new Tree.AsyncTreeNode({
          text: 'Root',
          draggable:false,
          id:'0'
       });
       tree.setRootNode(root);

       // render the tree
       tree.render();
       root.expand();
       tree.expandAll();
       Ext.EventManager.on(document.body, 'click', handleBodyClick);
});

</SCRIPT>
<body style="height:100%" scroll="no"><div id="TreeDiv" style="height:100%"></div>
</body>
</html>
<?exit;?>