<html>
<head>
  <title></title>
  <link rel="stylesheet" type="text/css" href="<?=$RES_PATH?>css/ext-all.css" />
  <?if($ADMIN_SKIN) echo '<link rel="stylesheet" type="text/css" href="'.$RES_PATH.'css/xtheme-'.$ADMIN_SKIN.'.css" />';?>
  <script type="text/javascript" src="<?=$RES_PATH?>ext-base.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>ext-all.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>getparent.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>help_call.js"></script>

  <?if(!isset($PROPERTIES["filters"])) $PROPERTIES["filters"]=0;
  if($PROPERTIES["filters"]){?>
  <script type="text/javascript" src="<?=$RES_PATH?>ux/menu/EditableItem.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>ux/menu/RangeMenu.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>ux/grid/GridFilters.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>ux/grid/filter/Filter.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>ux/grid/filter/StringFilter.js"></script>

  <script type="text/javascript" src="js/print_r.js"></script>

  <?
  $filters=array();
  $s=array("StringFilter.js");
  foreach($f_array as $k=>$v) if($v!='*hide*' && method_exists($OBJECTS[$k],"getFilter")) {
    $v=$OBJECTS[$k]->getFilter($k,"");
    if(is_array($v)) {
      if(!in_array($v[0],$s)) {
        $s[]=$v[0];
        echo '<script type="text/javascript" src="'.$RES_PATH.'ux/grid/filter/'.$v[0].'"></script>';
      }
      $filters[]=$v[1];
    }
  }unset($s);}?>

  <script type="text/javascript" src="<?=$RES_PATH?>RowExpander.js"></script>
  <link rel="stylesheet" type="text/css" href="<?=$RES_PATH?>css/grid-examples.css" />
  <script type="text/javascript" src="<?=$RES_PATH?>locale/ext-lang-<?=$_CONFIG["ADMIN_LOCATION"]?>.js"></script>
  <style>
  .maxheadovf {white-space:pre-wrap;white-space:-moz-pre-wrap;white-space:-pre-wrap;white-space:-o-pre-wrap;word-wrap:break-word;}
  </style>
  <SCRIPT LANGUAGE="JavaScript">
  EXTMod='<?=$EXT?>';
  EXTTable='<?=$tbname?>';
  EXTModTitle='<?if(isset($PROPERTIES["pagetitle"])) {
    $s=explode("\n",$PROPERTIES["pagetitle"]);
    echo trim(strip_tags($s[0]));
  }else echo $EXT;?>';
  EXTConnect='<?=(isset($_GET["catalog"])? "&catalog=".$_GET["catalog"]:"")?>';
  EXTConnectCode=<?=(isset($_GET["catalog"])? "'".$_GET["catalog"]."'":"null")?>;
  PagingCnt=<?=$SETTINGS["COUNT_ROWS"]?>;
  ConnectLog=<?=(isset($PROPERTIES["conect"]) && $PROPERTIES["conect"]? "true":"false")?>;
  EXTConnectFold='<?=(isset($PROPERTIES["conect"])? $PROPERTIES["conect"]:"")?>';
  EXTModIco='modulsico';
  EXTParentCode='<?=(isset($_GET["inouter_parent_code"])? 'inouter_parent_code='.$_GET["inouter_parent_code"].'&':'')?>';

  LOAD_GETSTR='<?=EchoGetStr("EXT",$EXT)?>';

  HELPMOD='grid<?=$tbname?>';
  OPENMODE='<?=(isset($PROPERTIES["formopenmode"])? $PROPERTIES["formopenmode"]:"tab")?>';
  LIMIT=<?=$SETTINGS["COUNT_ROWS"]?>;
  <?if($PROPERTIES["filters"]) {?>
  Ext.ux.menu.RangeMenu.prototype.icons = {
          gt: 'ext/img/greater_then.png',
          lt: 'ext/img/less_then.png',
          eq: 'ext/img/equals.png'
  };
  Ext.ux.grid.filter.StringFilter.prototype.icon = 'ext/img/find.png';
  <?}?>
  EXTBlock='<?=(isset($_GET["blcod"])? '&blcod='.$_GET["blcod"]:'')?>';
  MainGrid=null;
  store=null;
  TabBuffer=new Array();
  BufferType=false;
  filters=null;
  ParentPanel=ParentW.tabs.getActiveTab();


  <?if(isset($PROPERTIES["grop2filters"]) && $PROPERTIES["grop2filters"]){?>
GroupFilter=new function() {

  this.add=function(ids,n,t) {
    Ext.Ajax.request({
      url: '<?=$_SERVER["REQUEST_URI"]?>',  // Запрос самого себя
      success: function(response) {
	if(response.responseText!='') {
	  Ext.getCmp('groupBut').menu.add({
		    id:'GrpFltr-'+response.responseText,
		    handler: function() {GroupFilter.select(ids);},
		    text:n,
		    menu:{
		      items:[{
			text:ParentW.DLG.t('Edit'),
			handler: function() {GroupFilter.edit(response.responseText);}
		      },{
			text:ParentW.DLG.t('Delete'),
			handler: function() {GroupFilter.del(response.responseText);}
		      }]
		    }
	  });
	}
      },
      params: {
	inouter_filter_group_ids:ids,
	inouter_filter_group_name:n,
	inouter_filter_group_tab:t
      }
    });
  }

  this.select=function(ids) {
    ids=ids.split(',');
    MainGrid.getSelectionModel().clearSelections();
    var n;
    var nn=[];
    for(var i=0;i< ids.length;i++) {
      if(MainGrid.getStore().getById(ids[i])!=null) nn[nn.length]=MainGrid.getStore().getById(ids[i]);
    }
    if(nn.length>0) MainGrid.getSelectionModel().selectRecords(nn)
  }

  this.edit=function(id) {
    var s=ParentW.DLG.p('Enter items group name','');
    if(s==null || s=='') return false;

    Ext.Ajax.request({
      url: '<?=$_SERVER["REQUEST_URI"]?>',  // Запрос самого себя
      success: function(response) {
	if(response.responseText=='ok') {
	  Ext.getCmp('GrpFltr-'+id).setText(s);
	}
      },
      params: {
	inouter_filter_group_edit:s,
	inouter_filter_group_id:id
      }
    });
  }

  this.del=function(id) {
    Ext.Ajax.request({
      url: '<?=$_SERVER["REQUEST_URI"]?>',  // Запрос самого себя
      success: function(response) {
	if(response.responseText=='ok') {
	  Ext.getCmp('GrpFltr-'+id).destroy();
	}
      },
      params: {
	inouter_filter_group_del:id
      }
    });
  }
}

<?}?>

  SettingsSetLog=true;

  function ImportCsv() {

    var s=document.getElementById('upload_importcsvfile').value;
    if(s.substr(s.length-3)!='csv') {
      alert('Не верное расширение файла для импорта. Выберите файл CSV');
      return false;
    }


    Ext.MessageBox.show({
      title:'Импортировать файл?',
      msg: 'Данные из выбранного файла будут импортированы в базу данных. <br />Вы хотите заменить (Да) существующие данные?<br />Если хотите дополнить данные, нажмите "Нет".',
      buttons: Ext.MessageBox.YESNOCANCEL,
      fn: function(btn) {
	document.getElementById('upload_importcsv_mode').value=btn;
	if(btn=='yes' || btn=='no') {
	  document.getElementById('form_importcsvfile').submit();
	} else {
	  document.getElementById('upload_importcsvfile').value='';
	  return false;
	}
      },
      //animEl: 'mb4',
      icon: Ext.MessageBox.QUESTION
    });
  }

  GridClassMode='<?
  $db->query("SELECT * FROM gridsettings WHERE (usr='".$ADMIN_ID."' or global='1')  and modname='".$EXT."' ORDER BY global");

  //echo $db->LastQuery;

  $s="";
  while($db->next_record()) {
	if(!$s || !$db->Record["global"]) {
	  if($db->Record["mode_"]) echo '-overflow';
	  $s=stripslashes($db->Record["sizes_"]);
	  break;
	}
  }

  $F_SZ=array();
  if($s) {
	  $FA=array();
	  $s1=explode("],[",substr($s,1,strlen($s)-2));
	  foreach($s1 as $v) {
		$v=explode(",",$v);
		$v[1]=str_replace('"','',$v[1]);
		if(isset($F_ARRAY[$v[1]])) {
		  $F_SZ[$v[1]]=array($v[0],$v[2]);
		  $FA[$v[1]]=$F_ARRAY[$v[1]];
		  unset($F_ARRAY[$v[1]]);
		}
		if(isset($FA) && is_array($FA)) foreach($FA as $k=>$v) $F_ARRAY[$k]=$v;
		unset($FA);
	  }
  }
  ?>';//-overflow'; // Тип отображения колонок
  Columns_W=[<?=$s?>];

  function UserReload() {
    store.reload();
  }

  function gridChangeColumnsMode(xc) {
	if(xc=='clear') {
		if(ParentW.DLG.c('Clean your grid settings?')) {
			Ext.Ajax.request({
			   url: 'loadlist.php?settings2module=yes&EXT='+EXTMod,
			   success: function(response) {window.location=window.location;},
			   params: {
				 clearsets:1
			   }
			 });
		}
		return 0;
	 }

	 if(xc==null) if(GridClassMode=='-overflow') GridClassMode='';else GridClassMode='-overflow';

	 var x=Ext.query("*[class^=x-grid3-cell]");
	 for(var i=0;i<x.length;i++) {
		 if(GridClassMode=='-overflow') x[i].style.height='100%';
		  else x[i].style.height='auto';
	 }

	 var x=Ext.query("*[class^=x-grid3-cell-inner]");
	 for(var i=0;i<x.length;i++) {
		 x[i].className='x-grid3-cell-inner'+GridClassMode+x[i].className.substr(x[i].className.indexOf(' '));

	 }

	 var x=Ext.query("*[class^=x-grid3-hd-inner]");
	 for(var i=0;i<x.length;i++) {
		 if(GridClassMode=='-overflow')
		  x[i].className='x-grid3-hd-inner'+' maxheadovf'+x[i].className.substr(x[i].className.indexOf(' '));
		  else x[i].className=x[i].className.replace(' maxheadovf','');
	 }



	 if(xc!=1 && SettingsSetLog) {
	  SettingsSetLog=false;
	  Ext.Ajax.request({
	   url: 'loadlist.php?settings2module=yes&EXT='+EXTMod,
	   success: function(response) {SettingsSetLog=true;},
	   params: {
	     mode:(GridClassMode=='-overflow'? 1:0)
	   }
	  });
	 }

	 if(GridClassMode=='-overflow') return 'grid_unlock_cells';
	 return 'grid_lock_cells';

  }

  function ExportTab(x) {
	 if(x=='xls' && !confirm(ParentW.DLG.t('exportXlsConfirm'))) return false;

	 var cm=MainGrid.getColumnModel();
	 var a=[];
	 for(var i=1;i<cm.getColumnCount();i++) a[i-1]=(cm.isHidden(i)? 0:1);
     store.loadXls="/<?=$_CONFIG["TEMP_DIR"]."/".$ADMIN_ID."_".$EXT."_".mktime()?>."+x;
     SetCookie('_xls',store.loadXls+'|'+a.join(','));
     store.reload();
     window.setTimeout(function() {SetCookie('_xls','');},500);
  }

  function cutcopy(mod) {
    var s=MainGrid.getSelectionModel().getSelections();
    if(s.length==0) return false;
    TabBuffer=new Array();
    BufferType=mod;
    for(var i=0;i<s.length;i++) {
      TabBuffer[TabBuffer.length]=s[i].id;
      if(mod) store.remove(s[i]);
    }
    MainToolsPanel.getTopToolbar().items.get('PasteButUp').enable();
    MainToolsPanel.getTopToolbar().items.get('PasteButDown').enable();
  }

  function pasterows(mod) {
    var s=ParentW.AJAX.get('loadlist',{EXT:EXTMod,paste:mod,ids:TabBuffer.join(','),cut:(BufferType? "1":"0"),curid:MainGrid.getSelectionModel().getSelected().id});
    if(s=='OK') {
      store.reload();
      MainToolsPanel.getTopToolbar().items.get('PasteButUp').disable();
      MainToolsPanel.getTopToolbar().items.get('PasteButDown').disable();
    }
  }

  function SetCookie(sName, sValue){
    document.cookie = sName + "=" + escape(sValue) + ";expires=Mon, 31 Dec 2009 23:59:59 UTC;path=/;";
  }

  Ext.onReady(function(){

    Ext.QuickTips.init();

    function formatDate(value){
        return value ? value.dateFormat('M d, Y') : '';
    };

    var xg=Ext.grid;

    store = new Ext.data.<?=(isset($PROPERTIES["grouping"])? "GroupingStore":"Store")?>({
        url: 'loadlist.php?'+LOAD_GETSTR,//EXTParentCode+'EXT='+EXTMod+EXTConnect+EXTBlock,
        remoteSort: true,
        listeners: {
	  load:function(x,y,z) {
  	    if(x.loadXls!=null && x.loadXls!='') {
              var s='';
			  var i=x.loadXls.length-1;
			  while(i>0 && x.loadXls.charAt(i)!='.') i--;
			  s=x.loadXls.substr(i);
			  if(s=='.xls') window.location="/<?=$_CONFIG["ADMINDIR"]?>/dwlxsl.php?f="+x.loadXls;
			  else {
				  ParentW.App.run("importcsv.php?f="+x.loadXls,"Import table",null,300,100);
				  //ParentW.(x.loadXls);
			  }
              x.loadXls='';
            }
	    window.setTimeout(function() {gridChangeColumnsMode(1)},1000);
          }
        },
        <?if(isset($PROPERTIES["grouping"])) {?>
           groupField:'<?=$PROPERTIES["grouping"]?>',
           sortInfo:<?=(isset($PROPERTIES["sortInfo"])? "{".$PROPERTIES["sortInfo"]."}":"{field: 'id', direction: 'ASC'}")?>,
        <?}?>
        reader: new Ext.data.XmlReader({
               record: 'Item',
               id: 'id',
               totalRecords: 'TotalResults'
           }, [<?
           $i=0;
           foreach($F_ARRAY as $k=>$v) if(method_exists($OBJECTS[$k],'mkList')) {
             echo ($i++? "":"{name: '$k', mapping: 'ItemAttributes > $k'}").",'$k'";
           }?>])
    });

<?if($PROPERTIES["filters"] && count($filters)) {?>
    filters = new Ext.ux.grid.GridFilters({<?=(!isset($PROPERTIES["filters_autoload"]) || !$PROPERTIES["filters_autoload"]? "autoReload:false,":"")?>filters:[<?=join(",",$filters)?>]});
<?}?>

    var sm = new xg.CheckboxSelectionModel();
    var sm2 = new xg.CheckboxSelectionModel({
      listeners:{
        selectionchange:function() {
          var c=new Array('EditBut','DeleteBut','CutBut','CopyBut');
          if(ConnectLog) c[c.length]='ConnectButton';
          var mod=false;
          if(MainGrid.getSelectionModel().getSelections().length>0) mod=true;
          var o=MainToolsPanel.getTopToolbar().items;
          for(var i=0;i<c.length;i++) {
            if(o.get(c[i])!=null) {
			  if(mod) o.get(c[i]).enable();
              else o.get(c[i]).disable();
			}
          }
        }
      }
    });

	<?if(isset($_EXPANDER) && is_array($_EXPANDER) && count($_EXPANDER)){
	echo "var expander = new xg.RowExpander({enableCaching: false,tpl : new Ext.Template('<div style=\"padding-left:43px!important\">";
	foreach($_EXPANDER as $v) if(isset($OBJECTS[$v])) {

            echo "<p><b>".(isset($f_array[$v]) && $f_array[$v]!='*hide*'? $f_array[$v]:$OBJECTS[$v]->PROP["caption"]).":</b> {".$v."}</p><br>";
			unset($F_ARRAY[$v]);
	}
    echo "</div>')});";
	$_EXPANDER=1;
	} else $_EXPANDER=0;?>

	function onColumnChangeSet(n,s) {
		var c=MainGrid.getColumnModel().config;
		if(n==null && s==null) {
			Columns_W=[];
			for(var i=0;i<c.length;i++) {
				Columns_W[i]=[c[i].width,c[i].dataIndex,(c[i].hidden!=null? c[i].hidden:false)];
			}
		} else {
			Columns_W[n]=[s,c[n].dataIndex,(c[n].hidden!=null? c[n].hidden:false)];
		}
		var w=0;
		var p=[];
		for(var i=0;i<Columns_W.length;i++) {
			p[p.length]=Columns_W[i][0]+',"'+Columns_W[i][1]+'",'+Columns_W[i][2];
			if(!Columns_W[i][2]) w+=Columns_W[i][0];
		}

		MainGrid.setWidth(w);

		if(SettingsSetLog) {
		  SettingsSetLog=false;
		  Ext.Ajax.request({
		     url: 'loadlist.php?settings2module=yes&EXT='+EXTMod,
		     success: function(response) {SettingsSetLog=true;},
		     params: {
			   cells:'['+p.join('],[')+']'
		     }
		  });
		}
	}

	MainGrid = new xg.<?=((($PERMIS[2] || $_SESSION["adminlogin"]=='root') && isset($PROPERTIES["egrid"]) && $PROPERTIES["egrid"])? 'EditorGridPanel':'GridPanel')?>({
        id:'button-grid',
        ds: store,
        clicksToEdit:2,
<?if(isset($PROPERTIES["updown"]) && $PROPERTIES["updown"] && $PERMIS[2]) {?>
	trackMouseOver: true,
    enableColumnMove: true,
    enableDragDrop:true,
	ddGroup : 'grid',
<?}?>
        listeners:{

		  columnresize:function(n,s) {
			onColumnChangeSet(n,s);
			return true;
		  },
		  columnmove:function(n,s) {
			onColumnChangeSet();
			return true;
		  },
<?if($PERMIS[2] && (!isset($PROPERTIES["egrid"]) || !$PROPERTIES["egrid"])){?>
          rowdblclick :function(g,i,e) {
            var gsl=MainGrid.getSelectionModel().getSelected();
            if(gsl==null) return false;
			var id=gsl.id;
            var s='./readext.php?ext='+EXTMod+'&ch='+id;
            if(OPENMODE=='tab')
              ParentW.OpenNewTab(s,EXTModTitle+':ID='+id,EXTModIco,null,null,null,null,ParentPanel);
            else {
              var sz=OPENMODE.split(":");
              ParentW.App.run(s,EXTModTitle+':ID='+id,EXTMod+id,null,sz[1],sz[2]);
            }
          },
          /*keypress:function(e) {
			if(e.ctrlKey && e.keyCode==38) {
				var gsl=MainGrid.getSelectionModel().getSelected();
				var prev=-1;
				for(var i=0;i<store.data.items.length;i++) {
					if(store.data.items[i].id==gsl.id) break;
					else prev=store.data.items[i].id;
				}
				alert(prev);
				return false;
			}
		  },*/
<?}?>
          afteredit:function(e) {
            Ext.Ajax.request({
              url: 'loadlist.php?EXT='+EXTMod+'&ChangeVal='+e.field,
              success: function(response) {
                // изменения сохранились
              },
              params: {
                data:escape(e.value),
                id:e.record.id
              }
            });
            //ParentW.AJAX.post('loadlist',{EXT:EXTMod,ChangeVal:e.field},{data:escape(e.value),id:e.record.id});
          }
        },

        cm: new xg.ColumnModel([
            sm,
            <?
			if($_EXPANDER) echo 'expander,';
           $i=0;
           foreach($F_ARRAY as $k=>$v) if(method_exists($OBJECTS[$k],'mkList') && (isset($OBJECTS[$k]->PROP["caption"]) || isset($f_array[$k]))) {

			 if(isset($f_array[$k])) {
			   $v=explode("|",$f_array[$k]);
			   if($v[0]=='*hide*') $h=-1;
			   elseif(isset($v[1]) && $v[1]==1) $h="hidden:true,";
			   else $h="";
			 } else {
               $h="hidden:true,";
			   $v=array($OBJECTS[$k]->PROP["caption"]);
			 }
             if($h!=-1) {
			  if(isset($F_SZ[$k])) $h="hidden:".$F_SZ[$k][1].",";
              echo ($i++? ",":"")."{header: '".$v[0]."',  width: ".(isset($F_SZ[$k])? $F_SZ[$k][0]:120).", ".$h."dataIndex: '$k', sortable: true".(isset($OBJECTS[$k]) && method_exists($OBJECTS[$k],'mkEditor')? ",".$OBJECTS[$k]->mkEditor():"")."}";
			 }
           }?>
        ]),
        sm:sm2,
        <? if(isset($PROPERTIES["grouping"])){?>
        view: new Ext.grid.GroupingView({
            forceFit:true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
        }),
        <?}else{?>
        viewConfig: {
            forceFit:true
        },
        <?}
		$v=array();
		if($PROPERTIES["filters"]) $v[]="filters";
		if($_EXPANDER) $v[]="expander";
		if(count($v)) echo "plugins:[".join(",",$v)."],";
		?>
	enableColLock: false,
        loadMask: true,
        width:600,
		//height:400,
        autoHeight:true,
        frame:false,
        stripeRows: true,
        border:false,
        bodyBorder:false,
        hideBorders:true,
        title:'',
		region:'center',
        iconCls:'icon-grid'

		<?if(isset($PROPERTIES["warning"]) && $PROPERTIES["warning"]) {?>
		,tbar: [new Ext.form.Label({
			style: '',
			 html:'<div style="white-space:normal!important">'+document.getElementById('hidden_div_warning').innerHTML+'</div>'
		})]
		<?}?>



    });
    store.load({params:{start: 0, limit: LIMIT}});


	MainToolsPanel=new Ext.Panel({

		renderTo: document.body,
		border:false,
        bodyBorder:false,
        hideBorders:true,
		tbar:[<?

		$buts=array();

		if($PERMIS[1]){
		  $buts[]="{
            id:'addbutton',
	    text:ParentW.DLG.t('Add'),
            tooltip:ParentW.DLG.t('AddRowHint'),
            iconCls:'add',
            handler:function() {
	      ParentW.OpenNewTab('./readext.php?'+EXTParentCode+'ext='+EXTMod+'&new=yes'+EXTConnect,EXTModTitle+'::'+ParentW.DLG.t('NewRow'),EXTModIco,null,null,null,null,ParentPanel);
            }
          }";
	    }


		if($PERMIS[2]){
		  $buts[]="{
            text:ParentW.DLG.t('Edit'),
            tooltip:ParentW.DLG.t('EditRowHint'),
            iconCls:'edit',
            id:'EditBut',
            disabled:true,
            handler:function() {
              var s=MainGrid.getSelectionModel().getSelections();
              if(s.length==0) return false;
              for(var i=0;i<s.length;i++) {
                ParentW.OpenNewTab('./readext.php?ext='+EXTMod+'&ch='+s[i].id,EXTModTitle+':ID:'+s[i].id,EXTModIco,null,null,null,null,ParentPanel);
              }
            }
          }";
		}
		if($PERMIS[0])
		$buts[]="new Ext.Toolbar.MenuButton({
			  text:ParentW.DLG.t('RefreshTable'),
			  iconCls:'refresh',
			  id:'refreshbutton',
			  tooltip:ParentW.DLG.t('RefreshTableHint'),
			  handler:function() {
			    store.load({params:{start: 0, limit: LIMIT}});
			  },
			  menu:new Ext.menu.Menu({
				items: [{
				  handler: function() {window.location=window.location;},
				  text: ParentW.DLG.t('Clear filters and refresh')
				}]
			  })
			})";

		if($PERMIS[2] && isset($PROPERTIES["updown"])){
		  $buts[]="{   text:'',
            tooltip:ParentW.DLG.t('TableCut'),
            id:'CutBut',
            iconCls:'cut',
            disabled:true,
            handler:function(){
              cutcopy(true);
            }
		  },{ text:'',
            tooltip:ParentW.DLG.t('TableCopy'),
            id:'CopyBut',
            disabled:true,
            iconCls:'copy',
            handler:function() {
              cutcopy(false);
            }
          },{ text:'',
            id:'PasteButUp',
            disabled:true,
            tooltip:ParentW.DLG.t('TablePasteUp'),
            iconCls:'paste_up',
            handler:function() {
              pasterows('up');
            }
          },{ text:'',
            id:'PasteButDown',
            disabled:true,
            tooltip:ParentW.DLG.t('TablePasteDown'),
            iconCls:'paste_down',
            handler:function() {
              pasterows('down');
            }
          }";
        }

		if(isset($PROPERTIES["conect"]) && $PROPERTIES["conect"]){
          $buts[]="{
            text:'',
            id:'ConnectButton',
            disabled:true,
            tooltip:ParentW.DLG.t('TableConnect'),
            iconCls:'connect',
            handler:function() {
              var s=MainGrid.getSelectionModel().getSelections();
              if(s.length==0) return false;
              var x=new Array();
              for(var i=0;i<s.length;i++) x[x.length]=s[i].id;
              ParentW.connecttabsnew(EXTTable,x.join(','),EXTMod,EXTConnectFold);
            }
          }";
		}


		if($PERMIS[3]){
		  $buts[]="{
            text:ParentW.DLG.t('Delete'),
            tooltip:ParentW.DLG.t('DeleteRowHint'),
            iconCls:'remove',
            id:'DeleteBut',
            disabled:true,
            handler:function() {
               var s=MainGrid.getSelectionModel().getSelections();
               if(s.length==0) return false;
               if(!ParentW.DLG.c((s.length==1? 'DeleteRowQuest':'DeleteRowQuests'))) return false;
               var ids='';
               for(var i=0;i<s.length;i++) ids+=(i>0? ',':'')+s[i].id;

               ids=ParentW.AJAX.get('loadlist',{EXT:EXTMod,catalog:EXTConnectCode,del:ids});

               if(ids=='OK') store.reload();
            }
          }";
		}

	    if(isset($PROPERTIES["xls"]) && $PROPERTIES["xls"] && $PERMIS[0]) {
		  $buts[]="new Ext.Toolbar.MenuButton({
			  text:ParentW.DLG.t('exportButText'),
			  iconCls:'csv',
			  id:'xlsBut',
			  tooltip:ParentW.DLG.t('XlsRowHint'),
			  handler:function() {ExportTab('csv');},
			  menu:new Ext.menu.Menu({
				items: [{
				  handler: function() {ExportTab('csv');},
				  iconCls:'csv',
				  text: 'Текст, разделитель &quot;;&quot;'
				},/*{
				  handler: function() {ExportTab('xls');},
				  iconCls:'xls',
				  text: 'MS Excel'
				},*/{
				  handler: function() {ExportTab('txt');},
				  iconCls:'txt',
				  text: 'Текст, разделитель - табуляция'
				},{
				  handler: function() {ExportTab('xml');},
				  iconCls:'xml',
				  text: 'XML'
				}]
			  })
			})";
		}

		// Кнопка группировки для фильтров
		if(isset($PROPERTIES["grop2filters"]) && $PROPERTIES["grop2filters"] && $PERMIS[2]) {

		  $db->query("SELECT * FROM gridfiltersgroups WHERE tab='".$PROPERTIES["tbname"]."' ORDER BY id");
		  $m=array();
		  while($db->next_record()) {
		    $m[]="{
		    id:'GrpFltr-".$db->Record["id"]."',
		    handler: function() {GroupFilter.select('".$db->Record["cods"]."');},
		    text: '".$db->Record["name"]."',
		    menu:{items:[{
			text:ParentW.DLG.t('Edit'),
			handler: function() {GroupFilter.edit(".$db->Record["id"].");}
		      },{
		      text:ParentW.DLG.t('Delete'),
		      handler: function() {GroupFilter.del(".$db->Record["id"].");}
		    }]}}";
		  }

		  $buts[]="new Ext.Toolbar.MenuButton({
			  text:ParentW.DLG.t('Group to filters'),
			  iconCls:'',
			  id:'groupBut',
			  tooltip:ParentW.DLG.t('Group selected items to filters'),
			  handler:function() {
			    var s=MainGrid.getSelectionModel().getSelections();
			    if(s.length==0) return false;
			    var n=ParentW.DLG.p('Enter items group name','');
			    if(n==null || n=='') return false;
			    var ids='';
			    for(var i=0;i<s.length;i++) ids+=(i>0? ',':'')+s[i].id;
			    if(ids!='') GroupFilter.add(ids,n,'".$PROPERTIES["tbname"]."');
			  },
			  menu:new Ext.menu.Menu({items: [".(count($m)? join(",",$m):"")."]
			  })
			})";
		}

		if(isset($PROPERTIES["importcsv"])){
		  $buts[]="new Ext.Panel({
			id:'importcsvbut',
			style:'padding:0;margin:0;border:none !important;background:none !important',
			html:'<form id=\"form_importcsvfile\" ENCTYPE=\"multipart/form-data\" method=\"post\"><input type=\"hidden\" name=\"upload_importcsv_mode\" id=\"upload_importcsv_mode\"><div style=\"text-align: center; overflow: hidden; width: 76px; height: 16px;\"><div><table><tr><td><img src=\"/".$_CONFIG["ADMINDIR"]."/ext/img/import_csv.png\" alt=\"Имопорт CSV\" /></td><td style=\"color: #000000; font-size: 10px; font-weight: normal;\">импорт csv</td></tr></table></div><input type=\"file\" name=\"upload_importcsvfile\" id=\"upload_importcsvfile\" size=\"1\" style=\"margin-top: -50px; margin-left:-410px; -moz-opacity: 0; filter: alpha(opacity=0); opacity: 0; font-size: 150px; height: 100px;\" onchange=\"ImportCsv()\"></div></form>'
		  })";
		}
		if(isset($USER_GRID_BUTTONS)) foreach($USER_GRID_BUTTONS as $k=>$v) {
			$s="";
			if($v=="-") $s.=($s? ",":"")."'-'";
			else {
				$s.=($s? ",":"")."{
				 text:'".$v[1]."',
                 tooltip:'".$v[2]."',
                 iconCls:'".$k."',
                 id:'".$k."',
                 handler:function() {".$v[3].";}
				}";
			}
			if($s) $buts[]=$s;
		}


		echo join(",'-',",$buts);

		?>,'-',{
			text:ParentW.DLG.t('Lock cells'),
			iconCls:'grid_'+(GridClassMode==''? '':'un')+'lock_cells',
			handler:function() {
				this.setIconClass(gridChangeColumnsMode());
			}
		},{
			text:ParentW.DLG.t('Clear sets'),
			iconCls:'grid_set_clear',
			handler:function() {
				gridChangeColumnsMode('clear');
			}
		}],
        bbar: new Ext.PagingToolbar({
            pageSize: PagingCnt,
            store: store,
            displayInfo: true,
            displayMsg: ParentW.DLG.t('TablePgDisplayRows'),
            emptyMsg: ParentW.DLG.t('TablePgNoRows')

        }),
		items:MainGrid,
		bodyStyle:'overflow:auto'
	});

	ParentW.tabs.getActiveTab().gridstore=store;

    MainToolsPanel.setHeight(document.body.offsetHeight);
    MainToolsPanel.setWidth(document.body.offsetWidth);
    Ext.EventManager.on(document.body, 'click', handleBodyClick);

	if(Columns_W.length==0) {
	  MainGrid.setWidth(MainToolsPanel.getInnerWidth());
	  var c=MainGrid.getColumnModel().config;
	  for(var i=0;i<c.length;i++) {
		Columns_W[i]=[c[i].width,c[i].dataIndex,(c[i].hidden!=null? c[i].hidden:false)];
	  }
	} else {
	  var w=0;
	  for(var i=0;i<Columns_W.length;i++) if(!Columns_W[i][2]) {
    	  w+=Columns_W[i][0];
	  }
	  MainGrid.setWidth(w);
	}

	MainGrid.getColumnModel().on('hiddenchange', function(a,b,c) {onColumnChangeSet();});
	MainGrid.getStore().on("load", function(store, records, options){
	  Ext.select('div.x-grid3-row-expanded').replaceClass('x-grid3-row-expanded', 'x-grid3-row-collapsed');
	  this.state, this.bodyContent = {};
	}, this);

	var ddr=new Ext.dd.DropTarget(MainGrid.container,{
			 ddGroup : 'grid',
			 copy:false,
			 notifyDrop : function(dd, e, data){
				 var ids=[];
				 for(var i=0;i<data.selections.length;i++) ids[ids.length]=data.selections[i].data.id;
				 var cid=dd.getDragData(e).selections[0].data.id;
				 Ext.Ajax.request({
				   url: 'loadlist.php?EXT='+EXTMod+'&paste='+(data.selections[0].data.id>cid? "up":"down")+'&ids='+ids.join(',')+'&cut='+(e.ctrlKey? 0:1)+'&curid='+cid,
				   success: function(response) {
						store.reload();
				   },
				   params: {
					 clearsets:1
				   }
				 });
			 }
	});

//MainGrid.filters.filters.items[1].store.loadData([['1','1'],['2','2'],['3','3']]);     dataIndex - ключ



//MainGrid.filters.filters.items[1].on('update',function(){alert(111);});

<?
	if(isset($DEPEND_ARRAY)) {
		echo "var mf=MainGrid.filters.filters.items;";
		foreach($DEPEND_ARRAY as $k=>$vl) {
		    echo "for(var i=0;i<mf.length;i++) {
				if(mf[i].dataIndex=='$k') break;
		    }
			if(i<mf.length) {
				mf[i].on('update',function() {
					var v=this.getValue();
					if(typeof(v)=='object'&&(v instanceof Array)) {} else v=[v];
					";
					foreach($vl as $v) {
						$v=explode("|",$v);
						echo "
							Ext.Ajax.request({
							  url: '".$_SERVER["REQUEST_URI"]."',  // Запрос самого себя
							  success: function(response) {
								if(response.responseText=='ERR') return false;
								else {
									var x=[];
									//alert(response.responseText);
									eval('x=['+response.responseText+']');
									GetFilterByFold(mf,'".$v[0]."').store.loadData(x);
								}
							  },
							  params: {
								  load_options_values:v.join(','),
								  load_options_obj:'".$v[0]."',
								  load_options_folder_dep:'".$v[1]."'
							  }
						 });

						";
					}
            echo "
				});
			}";
		}
	}
	?>

//print_r(MainGrid.filters.filters.items[1]);

<?//if(isset($PROPERTIES["warning"]) && $PROPERTIES["warning"]) echo "ParentW.DLG.w('".str_replace("\n"," ",str_replace("\r","",str_replace("'","&#39;",$PROPERTIES["warning"])))."');";?>


	// Запустим плагины, подключенные к таблице
   for(var i in ParentW.PLUGINS.grid) eval('ParentW.PLUGINS.grid.'+i+'.init(window)');


});
function GetFilterByFold(f,n) {
  for(var i=0;i<f.length;i++) if(f[i].dataIndex==n) return f[i];
  return null;
}

Ext.grid.CheckColumn = function(config){
    Ext.apply(this, config);
    if(!this.id){
        this.id = Ext.id();
    }
    this.renderer = this.renderer.createDelegate(this);
};

Ext.grid.CheckColumn.prototype ={
    init : function(grid){
        this.grid = grid;
        this.grid.on('render', function(){
            var view = this.grid.getView();
            view.mainBody.on('mousedown', this.onMouseDown, this);
        }, this);
    },

    onMouseDown : function(e, t){
        if(t.className && t.className.indexOf('x-grid3-cc-'+this.id) != -1){
            e.stopEvent();
            var index = this.grid.getView().findRowIndex(t);
            var record = this.grid.store.getAt(index);
            record.set(this.dataIndex, !record.data[this.dataIndex]);
        }
    },

    renderer : function(v, p, record){
        p.css += ' x-grid3-check-col-td';
        return '<div class="x-grid3-check-col'+(v?'-on':'')+' x-grid3-cc-'+this.id+'">&#160;</div>';
    }
};

function WinOnResize() {
    MainToolsPanel.setHeight(document.body.offsetHeight);
    MainToolsPanel.setWidth(document.body.offsetWidth);
    //MainGrid.setWidth(MainToolsPanel.getInnerWidth());
}

function Up_n_Down(id,id2) {
  alert(id+'=>'+id2);
}

KeyGridProcessor=new function() {
  this.press=function(e) {
	if(!e.ctrlKey && e.keyCode==13) return this.pressmkey('EditBut');
	if(e.ctrlKey && (e.keyCode==19 || e.charCode==115)) return this.pressmkey('savebutton');

  }

  this.down=function(e) {

	if(e.keyCode==78 && e.ctrlKey) return this.pressmkey('addbutton');
	if(e.keyCode==46) return this.pressmkey('DeleteBut');
	if(e.ctrlKey && e.keyCode==82) return this.pressmkey('refreshbutton');
	if(e.keyCode==27) {
		ParentW.CloseCurrTab();
		return false;
	}
  }

  this.up=function(e) {
	//if(e.keyCode==46) {alert('del2');return this.pressmkey('DeleteBut');}
	/*if(e.ctrlKey && e.keyCode==38) {
	// стрелка вверх
	  print_r(MainGrid.getSelectionModel().getSelected());
	  //Up_n_Down();
	}

	if(e.ctrlKey && e.keyCode==40) {
	// стрелка вниз
	  Up_n_Down(1,2);
	}*/

  }

  this.pressmkey=function(bn) {
    var b=Ext.getCmp(bn);
    if(b!=null) b.handler();
    return false;
  }
}

</SCRIPT>

<?if(isset($USER_GRID_BUTTONS)) {
echo "<style>\n";foreach($USER_GRID_BUTTONS as $k=>$v) if(is_array($v)) {
echo ".$k {background-image:url(".$v[0].") !important;}\n";
}
echo "</style>";}?>

</head>
<body onkeypress="return KeyGridProcessor.press(event)" onkeydown="return KeyGridProcessor.down(event)"  onkeyup="return KeyGridProcessor.up(event)" id="bbody" onResize="WinOnResize()" scroll="no" style="width:100%;height:100%"><?echo (isset($GridGlobal)? $GridGlobal:"");

echo "<div style='display:none' id='hidden_div_warning'>".$PROPERTIES["warning"]."</div>";

?></body>
</html>
