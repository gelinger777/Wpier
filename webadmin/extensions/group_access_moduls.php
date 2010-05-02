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

  function chck_(e) {
	var s=e.name.split(":");
	var x='';
	var inp=document.getElementsByTagName('INPUT');
	for(var i=0;i<inp.length;i++) {
		x=inp[i].name.split(":");
		if((x[0]+':'+x[1])==(s[0]+':'+s[1])) inp[i].checked=e.checked;
	}
  }

  function read_mod_list(x,nm) {
	x=x.split(":");
	if(x.length==2) {
	// если 2 элемента, то это раздел
	  return "<input type='checkbox' onclick='chck_(this)' name='"+x[0]+":"+nm+"' value='1' "+(x[1]==1? 'checked':'')+">";
	}
	if(x.length==3) {
	// если 3 элемента, то это модуль
	  return "<center><input type='checkbox' name='"+x[0]+":"+nm+":"+x[1]+"' value='1' "+(x[2]==1? 'checked':'')+"></center>";
	}
	return false;
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
        rootVisible:false,
        autoScroll:true,
		border:false,
		tbar:[{
			text:'Сохранить',
		    tooltip: '',
			iconCls: 'save',
			handler:function() {
				var inp=document.getElementsByTagName('INPUT');
				var m=[[],[],[],[]];
				for(var i=0;i<inp.length;i++) if(inp[i].checked) {
					x=inp[i].name.split(":");
					if(x.length==3) {
						if(x[1]=='rd') m[0][m[0].length]=x[2];
						if(x[1]=='ad') m[1][m[1].length]=x[2];
						if(x[1]=='ed') m[2][m[2].length]=x[2];
						if(x[1]=='dl') m[3][m[3].length]=x[2];
					}
				}
				Ext.Ajax.request({
					  url: 'chmod.php?modgroup=<?=$_GET["inouter_parent_code"]?>',
					  success: function(response) {
						  //alert(response.responseText);
						if(response.responseText=="OK") alert('Данные сохранены!');
					  },
					  params: {
						rd:m[0].join(';'),
						ad:m[1].join(';'),
						ed:m[2].join(';'),
						dl:m[3].join(';')
					  }
				});
			}
		}

		],
        title: '',
        renderTo: document.body,
        columns:[{
			id:'ModColumn',
            header:'Модуль',
			width:400,
            dataIndex:'mod'
        },{
            header:'Чтение',
            width:80,
            dataIndex:'rd',
			renderer:function(x) {
				return read_mod_list(x,'rd');
			}
        },{
            header:'Добавление.',
            width:80,
            dataIndex:'ad',
			renderer:function(x) {
				return read_mod_list(x,'ad');
			}
        },{
            header:'Редактир.',
            width:80,
            dataIndex:'ed',
			renderer:function(x) {
				return read_mod_list(x,'ed');
			}
        },{
            header:'Удал.',
            width:80,
            dataIndex:'dl',
			renderer:function(x) {
				return read_mod_list(x,'dl');
			}
        }],

        loader: new Ext.tree.TreeLoader({
            dataUrl:'readprogramms.php?chmodgroup=<?=$_GET["inouter_parent_code"]?>',
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
	tree.expandAll();
});

  window.onresize=mkGridSize;

  //-->
  </SCRIPT>

</head>
<body></body>
</html>
<?exit;?>