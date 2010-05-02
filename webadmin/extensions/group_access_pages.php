<?
if(count($_POST) && isset($_GET["inouter_parent_code"])) {

  $_GET["inouter_parent_code"]=intval($_GET["inouter_parent_code"]);
  if(!isset($_POST["rd"])) $_POST["rd"]=array();
  if(!isset($_POST["ad"])) $_POST["ad"]=array();
  if(!isset($_POST["ed"])) $_POST["ed"]=array();
  if(!isset($_POST["dl"])) $_POST["dl"]=array();
  if(!isset($_POST["nas"])) $_POST["nas"]=array();
  else {
    // Если указана хоть одна страница с наследованием прав, нужно прочитать все дерево
    $db->query("SELECT id,pid FROM catalogue ORDER BY pid,id");
    $tr=array();
    while($db->next_record()) {
      $tr[$db->Record["pid"]][$db->Record["id"]]=1;
    }
  }

  $xch=array();

  function addAcc($id,$a) {
    global $tr,$db,$xch;
    $xch[]=$id;
    if(isset($tr[$id])) {
      foreach($tr[$id] as $k=>$v) {
	$db->query("DELETE FROM accesspgadmins WHERE grp=".$_GET["inouter_parent_code"]." and pg=".$k);
	if(count($a)) {
	  $db->query("INSERT INTO accesspgadmins (pg,grp,".kjoin(",",$a).") VALUES ('$k','".$_GET["inouter_parent_code"]."','".join("','",$a)."')");
	}
	addAcc($k,$a,$db);
      }
    }
  }

  foreach($_POST["is"] as $k) if(!in_array($k,$xch)) {
    $db->query("DELETE FROM accesspgadmins WHERE grp=".$_GET["inouter_parent_code"]." and pg=".$k);
    $a=array();
    if(isset($_POST["rd"][$k])) $a["rd"]=1;
    if(isset($_POST["ed"][$k])) $a["ed"]=1;
    if(isset($_POST["ad"][$k])) $a["ad"]=1;
    if(isset($_POST["dl"][$k])) $a["dl"]=1;

    if(count($a)) {
      $db->query("INSERT INTO accesspgadmins (pg,grp,".kjoin(",",$a).") VALUES ($k,".$_GET["inouter_parent_code"].",'".join("','",$a)."')");
    }

    // Учтем наследование прав
    if(in_array($k,$_POST["nas"])) {
      addAcc($k,$a);
    }
  }
  //exit;
}
?>
<html>
<head>
  <title></title>

  <link rel="stylesheet" type="text/css" href="<?=$RES_PATH?>css/ext-all.css" />
  <?if($ADMIN_SKIN) echo '<link rel="stylesheet" type="text/css" href="'.$RES_PATH.'css/xtheme-'.$ADMIN_SKIN.'.css" />';?>
  <script type="text/javascript" src="<?=$RES_PATH?>ext-base.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>ext-all.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>getparent.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>help_call.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>ColumnNodeUI.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>locale/ext-lang-ru.js"></script>
  <link rel="stylesheet" type="text/css" href="<?=$RES_PATH?>column-tree.css" />
  <link rel="stylesheet" type="text/css" href="<?=$RES_PATH?>styles.css" />


  <SCRIPT LANGUAGE="JavaScript">
  <!--
  tree=null;


  function read_mod_list(x,nm) {
	x=x.split(":");
	if(x.length==2) {
	  return "<input type='checkbox' name='"+nm+"["+x[0]+"]' value='1' "+(x[1]==1? 'checked':'')+">";
	}
	return "";
  }

  function mkGridSize() {
  	tree.setWidth(document.body.clientWidth);
	tree.setHeight(document.body.clientHeight);
  }

  Ext.onReady(function(){

    tree = new Ext.tree.ColumnTree({
        width:500,
	height:500,
        //autoHeight:true,
	lines:true,
        rootVisible:false,
        autoScroll:true,
		border:false,
		tbar:[{
		    text:'Сохранить',
		    tooltip: '',
		    iconCls: 'save',
		    handler:function() {
		        document.forms[0].submit();
		      }
		},{
		    tooltip:ParentW.DLG.t('TreeMenuShowAll'),
		    iconCls:'openall',
		    handler:function() {
		      tree.expandAll();
		    }
		 },{
		    tooltip:ParentW.DLG.t('TreeMenuCollapseAll'),
		    iconCls:'closeall',
		    handler:function() {
		      tree.collapseAll();
		    }
		 }

		],
        title: '',
        renderTo: 'formtree',
        columns:[{
	    id:'ModColumn',
            header:'Страница',
	    width:400,
            dataIndex:'id'
        },{
            header:'Чт.',
            width:60,
            dataIndex:'rd',
			renderer:function(x) {
				return read_mod_list(x,'rd');
			}
        },{
            header:'Доб.',
            width:60,
            dataIndex:'ad',
			renderer:function(x) {
				return read_mod_list(x,'ad');
			}
        },{
            header:'Ред.',
            width:60,
            dataIndex:'ed',
			renderer:function(x) {
				return read_mod_list(x,'ed');
			}
        },{
            header:'Уд.',
            width:60,
            dataIndex:'dl',
			renderer:function(x) {
				return read_mod_list(x,'dl');
			}
        },{
            header:'<a href="#" title="При сохранении, все дочерние страницы наследуют права доступа отмеченных этим признаком разделов">Наследовать.</a>',
            width:80,
            dataIndex:'flid',
			renderer:function(x) {
				x=x.split(":");
				return "<input type='hidden' name='is[]' value='"+x[0]+"' />"+(x[1]=='1'? '<input type="checkbox" name="nas[]" value="'+x[0]+'" />':'');
	    return x;
			}
        }],

        loader: new Ext.tree.TreeLoader({
            dataUrl:'getbranches.php?chmodgroup=<?=$_GET["inouter_parent_code"]?>',
            uiProviders:{
                'col': Ext.tree.ColumnNodeUI
            }
        }),

        root: new Ext.tree.AsyncTreeNode({
            text:'Tasks'
        })
    });
    //tree.render();
	mkGridSize();
	//tree.expandAll();
});

  window.onresize=mkGridSize;

  //-->
  </SCRIPT>

</head>
<body><form method="post"><div id="formtree" style="width:100%;height:100%"></div></form></body>
</html>
<?exit;?>