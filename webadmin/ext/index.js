HELPMOD='index';
viewport=null;
tabs=null;
tree=null;
tree_extend=null;
CurTreeNode=null;
CurrentTree=null;
BufferTreeNode=null;
contextmenu=null;
TreeBuffer='';
CurrentTreeId=0;
TreeProps=[];
ParentW=window;
TopToolbar=null;
modulsmenu=null;
desktopmenu=null;
LogResizeViewPort=false;
modulsstore=null;
modulstpl=null;
moduls_view=[null,null,null];
_TAB_TITLE_LENGTH_=10;
_desktop_store_=null;
_DRAG_DROP={};

function getTreeProps(id) {
  if(TreeProps[id]==null) {
    // Синхронный запрос свойств узла дерева
    TreeProps[id]=AJAX.loadHTML_do('treefunc.php?getprop='+id);
  }
  return TreeProps[id].split('|');
}

function IndexInit() {
      Ext.QuickTips.init();
      Ext.BLANK_IMAGE_URL='./s.gif';
      Ext.state.Manager.setProvider(new Ext.state.CookieProvider());


      // Шаблоны ярлыков на рабочем столе
      modulstpl_desktop=new Ext.XTemplate(
	  '<tpl for=".">',
        '<div class="thumb-wrap" id="{name}" title="{descript}">',
	    '<div class="thumb"><img src="{ico}" id="{name}_ico" title="{descript}"></div>',
          '{descript}</div>',
      '</tpl>',
      '<div class="x-clear"></div>'
      );
      modulstpl=new Ext.XTemplate(
	  '<tpl for=".">',
        '<div class="thumb-wrap" id="{name}" title="{descript}" style="border:1px solid #ffffff;width:95%;text-align:left;padding:2px;height:auto;"><div style="height:auto;background:url({ico}) no-repeat left center;padding-left:20px;font-size:12px">',
	    '{descript}</div></div>',
      '</tpl>',
      '<div class="x-clear"></div>'
      );

      var left_column_items=[]

	  if(_TREE_ACCESS_>0) {
		left_column_items[left_column_items.length]={}
	  }

      function sModContextMenu(dw, n, nod, ev) {
	    modulsmenu.curData=dw.getRecord(nod);
	    ModMenuShow(ev,nod.id,dw.getSelectedRecords());
	    //modulsmenu.show(nod.ui.getAnchor());
      }

      function sModContextMenu1(dw, n, nod, ev) {
  	    if(ev.shiftKey) {
	      sModContextMenu(dw, n, nod, ev);
	      return false;
	    }
      }


        // Баян для модулей -----------------
		if(MODULS_LIST[0]!=null) {
			moduls_view[0]=new Ext.DataView({
							id:'moduls-view',
							store: new Ext.data.SimpleStore({
							  fields: ['name', 'ico', 'descript','runstr'],
							  data: MODULS_LIST[0]
						    }),
							tpl: modulstpl,
							autoHeight:true,

							multiSelect: true,
							overClass:'x-view-over',
							itemSelector:'div.thumb-wrap',
							emptyText: '',
							plugins: [
								new Ext.DataView.DragSelector()//,
								//new Ext.DataView.LabelEditor({dataIndex: 'descript'})
							],
							listeners:{
								contextmenu:sModContextMenu,
								click:sModContextMenu1,
								dblclick:function(dw, n, nod, ev) {
									eval(MODULS_LIST[0][n][3]);
								}
							}

			});
			if(MODULS_LIST[0].length>0) {
				left_column_items[left_column_items.length]={

                        id:'ModulsList',
                        title:'&nbsp;',
						tooltip:DLG.t('Moduls'),
                        border:false,
                        autoScroll:true,
                        iconCls:'webserv',

						// Тут вьюпорт для веб-сервисов
						items:moduls_view[0]

				}
			}
		}
		// К Баян для модулей -----------------

		// Баян для панели управления -----------
		if(MODULS_LIST[1]!=null) {
			moduls_view[1]=new Ext.DataView({
							id:'tools-view',
							store: new Ext.data.SimpleStore({
							  fields: ['name', 'ico', 'descript','runstr'],
							  data: MODULS_LIST[1]
						    }),
							tpl: modulstpl,
							autoHeight:true,

							multiSelect: true,
							overClass:'x-view-over',
							itemSelector:'div.thumb-wrap',
							emptyText: '',
							plugins: [
								new Ext.DataView.DragSelector()//,
								//new Ext.DataView.LabelEditor({dataIndex: 'descript'})
							],
							listeners:{
								contextmenu:sModContextMenu,
								click:sModContextMenu1,
								dblclick:function(dw, n, nod, ev) {
									eval(MODULS_LIST[1][n][3]);
								}
							}

			});

			if(MODULS_LIST[1].length>0) {
				left_column_items[left_column_items.length]={
						id:'ToolsList',
                        title:'&nbsp;',
						tooltip:DLG.t('Settings'),
                        border:false,
                        autoScroll:true,
                        iconCls:'settings',
						items:moduls_view[1]
				}
			}
		}
		// К Баян для панели управления -----------


		// Баян для программ -------------------
		if(MODULS_LIST[2]!=null) {
			moduls_view[2]=new Ext.DataView({
							id:'xprogramms-view',
							store: new Ext.data.SimpleStore({
							  fields: ['name', 'ico', 'descript','runstr'],
							  data: MODULS_LIST[2]
						    }),
							tpl: modulstpl,
							autoHeight:true,

							multiSelect: true,
							overClass:'x-view-over',
							itemSelector:'div.thumb-wrap',
							emptyText: '',
							plugins: [
								new Ext.DataView.DragSelector()//,
								//new Ext.DataView.LabelEditor({dataIndex: 'descript'})
							],
							listeners:{
								contextmenu:sModContextMenu,
								click:sModContextMenu1,
								dblclick:function(dw, n, nod, ev) {
									eval(MODULS_LIST[2][n][3]);
								}
							}

			});
			if(MODULS_LIST[2].length>0) {
				left_column_items[left_column_items.length]={
						id:'ProgrammsList',
                        title:'&nbsp;',
						tooltip:DLG.t('Programms'),
                        border:false,
                        autoScroll:true,
                        iconCls:'modulsico',
						items:moduls_view[2]
				}
			}
		}
		// К Баян для программ -------------------


	   _desktop_store_=new Ext.data.SimpleStore({
							  fields: ['name', 'ico', 'descript','runstr'],
							  data: _DESKTOP_SHORTCUTS_
	   });

	   function dtContextMenu(dw, n, nod, ev) {
				desktopmenu.curData=dw.getRecord(nod);
				desktopmenu.selData=dw.getSelectedRecords();
				desktopmenu.selIndexes=dw.getSelectedIndexes();
				desktopmenu.selIndex=n;
				desktopmenu.showAt(ev.xy);
				ev.stopEvent();
	   }

	   var DeskTopPanels=[new Ext.DataView({
			            id:'desktop-view',
			            region:(_VENDOR_CHANEL==''? 'center':'north'),
			            split:true,
			            store:_desktop_store_,
			            tpl: modulstpl_desktop,
			            height:200,
			            //autoHeight:true,
			            multiSelect: true,
			            overClass:'x-view-over',
			            itemSelector:'div.thumb-wrap',
			            emptyText: '',


			            plugins: [
			             new Ext.DataView.DragSelector()
			            ],
						listeners:{
						    contextmenu: dtContextMenu,
						    click:function(dw, n, nod, ev) {
						      if(ev.shiftKey) dtContextMenu(dw, n, nod, ev);
						    },
						    dblclick:function(dw, n, nod, ev) {
						      eval(_DESKTOP_SHORTCUTS_[n][3]);
						    }
						  }
						})];

		if(_VENDOR_CHANEL!='') {
			// Если указан путь к серверу вендора, покажем панель с rss-каналом обновлений
			// от производителя дистрибутива
			DeskTopPanels[DeskTopPanels.length]=VendorRssPanel;
		}

       tabs = new Ext.TabPanel({
                    region:'center',
                    id:'ModulsTab',
                    deferredRender:false,
                    activeTab:(_TREE_ACCESS_>1? 0:null),
                    enableTabScroll:true,
                    draggable:false,
                    items:(_TREE_ACCESS_>1? [{
		              id:'DescTopArea',
                      title: TEXTS.Desktop,
                      layout:'border',
					  ddGroup:'desktop',
                      autoScroll:true,
			          bodyStyle:'background:url('+_DESKTOP_BACKGROUND_+') no-repeat left top;',
		 	          items:DeskTopPanels,
                    }]:null),
				    listeners:{
				      beforetabchange : function(tp, nt, ct ) {
						if(nt!=null && nt.storechanged!=null && nt.storechanged) {
						  if(nt.gridstore!=null) nt.gridstore.reload();
						  nt.storechanged=null;
						}
						if(ct!=null) {
						  ct.tooltip=ct.title;
						  var tl=(ct.title.length>_TAB_TITLE_LENGTH_? ct.title.substr(0,_TAB_TITLE_LENGTH_)+"...":ct.title);
						  ct.setTitle(tl);
						  if(nt.tooltip!=null && nt.title!=nt.tooltip) {
						    nt.setTitle(nt.tooltip);
						  }
						}
				      }
				    }
                });

	var mainToolbarItems=[
	  {
	      text:DLG.t('Exit'),
              tooltip:DLG.t('ExitTooltip'),
              iconCls:'exit',
              handler:function(){
                if(DLG.c('ExitAsk')) window.location='logoff.php';
              }
            },'-',{
              text:'',
              tooltip:DLG.t('CloseAll'),
              iconCls:'top_closeall',
              handler:function(){
                for(var i=tabs.items.length-1;i>0;i--) {
                  tabs.remove(tabs.items.items[i]);
                }
              }
            },'-',
	    new Ext.form.Label({
		id:'struct-pg-count',
		html:'',
		tooltip:DLG.t('Edited pages | All pages')
	    }),'-'];

	if(_ADMIN_SEARCH!=''){
	  mainToolbarItems=mainToolbarItems.concat([new Ext.form.TextField({
	      width:150,
              id:'searchfield',
	      emptyText:DLG.t('search')
	    }),{
	      iconCls:'go',
              handler:function(){

		var url=_ADMIN_SEARCH+Ext.getCmp('searchfield').getValue();

		if(LastOpendPreviewWind!=null && LastOpendPreviewWind.Win!=null) {
			LastOpendPreviewWind.TabPanel.show();
			LastOpendPreviewWind.Win.location=url;
			return false;
		}

		 var o=new PrevObj();
		 o.MakePrevTab(null,null,url);
		 o.TabPanel.on("destroy",function() {if(o==LastOpendPreviewWind) LastOpendPreviewWind=null;});
		 LastOpendPreviewWind=o;

	        //OpenNewTab('/search/?search='+Ext.getCmp('searchfield').getValue(),'','previco',null,null,null,this);
              }
	    },'-']);
	}

        // Верхняя панель
        TopToolbar = new Ext.Panel({
          border: false,
          layout:'column',
          region:'north',
	  hideBorders:true,
	  bodyBorder:false,
	  height:25,
          items: [{
	    columnWidth: 1,
	    items:new Ext.Toolbar({

              id:'TopToolbarPanel',
              items:mainToolbarItems

          })},{

	      width: 30,
	      items:new Ext.Toolbar({
		items:[{
		  tooltip:DLG.t('About wpier'),
		  iconCls:'info',
		  handler:function(){
		    HELP.about();
		  }
		}]
	      })
	  }]
        });
        // К Верхняя панель
	var Tree = Ext.tree;
        // Панель слева

	function shTreeContextMenu(node, e) {
	      if(_TREE_ACCESS_<3) return false;
	      var s=getTreeProps(node.id);

              contextmenu.tree=node.ownerTree;
	      contextmenu.curNode=node;

			  contextmenu.items.get('tppId').setText('ID: <b>'+node.id+'</b>');

			  if(node.ownerTree.id=='ExtendTreePanel') {
				  Ext.getCmp('tppMoveToFav').setText(DLG.t('FavDel'));
				  contextmenu.items.get('tppPaste').disable();
  				  contextmenu.items.get('tppCopyAll').disable();
				  contextmenu.items.get('tppCut').disable();
  				  contextmenu.items.get('tppCopy').disable();
			  } else {
				  Ext.getCmp('tppMoveToFav').setText(DLG.t('FavAdd'));
				  if(s.length>2) {
				  //4-rd,5-ad,6-ed
					if(s[4]==1) Ext.getCmp('tppPreview').enable();
					else Ext.getCmp('tppPreview').disable();
					if(s[5]==1) Ext.getCmp('tppAdd').enable();
					else Ext.getCmp('tppAdd').disable();
					if(s[6]==1) {
						Ext.getCmp('tppEdit').enable();
					} else {
						Ext.getCmp('tppEdit').disable();
					}
					if(s[7]==1) Ext.getCmp('tppDelete').enable();
					else Ext.getCmp('tppDelete').disable();
				  }
				  if(BufferTreeNode!=null)
					contextmenu.items.get('tppPaste').enable();
				  else
					contextmenu.items.get('tppPaste').disable();
				  if(node.leaf)
					contextmenu.items.get('tppCopyAll').disable();
				  else
					contextmenu.items.get('tppCopyAll').enable();
			  }

              CurrentTreeId=node.id;
              CurTreeNode=node;


              node.ownerTree.getSelectionModel().select(node);
              contextmenu.show(node.ui.getAnchor());
        }

	    // Обработчики событий для деревьев
	    treeListeners={
            contextmenu:shTreeContextMenu,
            dblclick:function(node,e) {
              if(_TREE_ACCESS_<3) return false;
			  //BranchOnDblClick(node.id,e);
			  page_edit(node.id);
            },
            nodedragover:function(o){
			  this.currDDPoint=o.point;
            },
            click:function(node,e) {
              if(e.shiftKey) {
					shTreeContextMenu(node, e);
					return false;
			  }
	          CurrentTree=node.ownerTree;
			  if(_TREE_ACCESS_<3) {
				  BranchOnDblClick(node.id,e);
				  return false;
			  }
              CurrentTreeId=node.id;
              if(CAN_CHMOD) Ext.getCmp('ttpAccess').enable();
              Ext.getCmp('ttpDelete').enable();
            },
            enddrag : function( tr, nd, e) {
            	var el=e.target;
            	while(el!=null && _DRAG_DROP[el.id]==null) el=el.parentNode;
            	if(el!=null && _DRAG_DROP[el.id]!=null && _DRAG_DROP[el.id].tree!=null) {
            		return _DRAG_DROP[e.target.id].tree(tr,nd);
            	}
            }
          };

	  var LeftPanelItems=[{}];

		if(_IS_EXTEND_TREE_) {

			// Подключаем дополнительное дерево к левой панели
			tree_extend = new Tree.TreePanel({
			  height:100,
			  title:DLG.t('FavTitle'),
			  border:false,
			  split:true,
			  id:'ExtendTreePanel',
			  region:'south',
			  autoScroll:true,
			  animate:true,
			  iconCls:'favorit',
			  enableDD:(_TREE_ACCESS_>1? true:false),
			  ddGroup:'desktop',
			  containerScroll: false,
			  bodyBorder:false,
			  border:false,
			  rootVisible:false,
			  selModel:new Ext.tree.MultiSelectionModel(),
			  loader: new Tree.TreeLoader({
				dataUrl:'getbranches.php?extendtree=1'
			  }),
			  listeners:treeListeners
			});

			LeftPanelItems[LeftPanelItems.length]=tree_extend;

			// set the root node
			var root1 = new Tree.AsyncTreeNode({
			  text: 'Root',
			  draggable:false,
			  id:'0'
		   });
		   tree_extend.setRootNode(root1);


		   var treeEditor1 = new Ext.tree.TreeEditor(tree_extend, {
			allowBlank: false,
			cancelOnEsc:true,
			completeOnEnter:true,
			editDelay: 1000,
			listeners:{
				beforestartedit:function(x,y,z) {
					return false;
				}
			}
		   });

		   treeEditor1.on("complete",function(a,b,c) {
			  var s=AJAX.get('treefunc',{text:b,updatetitle:a.editNode.id});
			  treeChangeAttr(a.editNode.id,1);
		   });


		   root1.expand();
		}




       function MoveToFavPanel(id,tr) {

			var mod=1;
			if(tr.id=='ExtendTreePanel') mod=0;

			Ext.Ajax.request({
					  url: 'treefunc.php',
					  success: function(response) {
						if(response.responseText=="ERR") return false;
						else {
							if(Ext.getDom('ExtendTreePanel')==null) {
								if(confirm(DLG.t('FavMakePanel'))) {
									window.location=window.location;
								}
							} else {
								tree.getLoader().load(tree.getRootNode());
								tree_extend.getLoader().load(tree_extend.getRootNode());
							}
						}
					  },
					  params: {
						  move2favorit:id,
						  mod:mod
					  }
		    });

	   }



		desktopmenu = new Ext.menu.Menu({
			id:'deskTopMenu',
			items: [{
				id:'deskTopMenu-del',
				text:DLG.t("dtDeleteShortcut"),
				iconCls:'delete',
				handler: function() {
				  var s="";
				  if(desktopmenu.selIndexes!=null && desktopmenu.selIndexes.length==1) {
					if(DLG.c("dtDeleteItem")) s=desktopmenu.selIndexes[0];
				  }else if(desktopmenu.selIndexes!=null && desktopmenu.selIndexes.length>1) {
					if(DLG.c("dtDeleteItems")) s=desktopmenu.selIndexes.join(',');
				  } else {
					if(DLG.c("dtDeleteItem")) s=desktopmenu.selIndex;
				  }
				  if(s!=='') {
					Ext.Ajax.request({
					  url: 'desktop.php',
					  success: function(response) {
						if(response.responseText=="ERR") return false;
						else {
							eval('_DESKTOP_SHORTCUTS_=['+response.responseText+']');
							_desktop_store_.loadData(_DESKTOP_SHORTCUTS_);
						}
					  },
					  params: {
						  deleteshortcuts:s
					  }
				   });
				  }
				}
			}]
		});




	var cmitems=[{
	      id:'tppId',
	      text:''
	   },'-',{
             id:'tppCut',
             handler: function() {popup_cut_do(contextmenu.tree);},
             iconCls:'cut',
             text: DLG.t("TreePopupCut")
           },{
             id:'tppCopy',
             handler: function() {popup_copy_do(null,contextmenu.tree);},
             iconCls:'copy',
             text: DLG.t("TreePopupCopy")+' (Ctrl+C)'
           },{
		id:'tppCopyAll',
		handler: function() {popup_copy_do('all',contextmenu.tree);},
		iconCls:'copyall',
		text: DLG.t("TreePopupCopyAll")
	      },{
             id:'tppPaste',
             disabled:true,
             handler: function() {popup_paste_do(null,contextmenu.tree);},
             iconCls:'paste',
             text: DLG.t("TreePopupPaste")+' (Ctrl+V)'
           },'-',{
             id:'tppAdd',
             handler: function() {popup_add_do(CurrentTreeId,contextmenu.tree);},
             iconCls:'add',
             text: DLG.t("TreePopupMake")+' (Ctrl+N)'
           },{
             id:'tppEdit',
             handler: function() {page_edit(CurrentTreeId);},
             iconCls:'editor',
             text: DLG.t("TreePopupEdit")+' (Shift+Enter)'
           },{
             id:'tppPreview',
             handler: function() {page_preview(CurrentTreeId);},
             iconCls:'preview',
             text: DLG.t("TreePopupPreview")+' (Enter)'
           }];


	  if(CAN_CHMOD) cmitems[cmitems.length]={
             id:'tppAccess',
             handler: function() {ChangeAccess(null,null,contextmenu.tree);},
             iconCls:'acc',
             text: DLG.t("TreePopupAccess")
           };


	  if(_DOCS_IMPORT && _TREE_ACCESS_>2) {
	    cmitems[cmitems.length]='-';
	    cmitems[cmitems.length]={text:DLG.t('Make child page from file'),
		id:'tppImportMS',
		iconCls:'office-add',
		handler:function(){popup_add_file(contextmenu.curNode.id,contextmenu.tree,1);}};
	    cmitems[cmitems.length]={text:DLG.t('Change current page content from file'),
		id:'tppReplaceTextMS',
		iconCls:'office-replace',
		handler:function(){popup_add_file(contextmenu.curNode.id,contextmenu.tree,2);}}
	  }

	  if(_TREE_ACCESS_>2) {
	    cmitems[cmitems.length]='-';
	    cmitems[cmitems.length]={
	     id:'tppMoveToFav',
             handler: function() {MoveToFavPanel(CurrentTreeId,contextmenu.tree)},
             iconCls:'fav-add',
             text: ''};
	  }

	  cmitems[cmitems.length]='-';
	  cmitems[cmitems.length]={
             id:'tppDelete',
             handler: function() {popup_del_do(contextmenu.tree);},
             iconCls:'delete',
             text: DLG.t("TreePopupDelete")
          };



	 contextmenu = new Ext.menu.Menu({
           id:"treeMenuContext",
           items:cmitems});

        helpmenu = new Ext.menu.Menu({
           id:"helpMenuContext",
           listeners:{
             beforehide:function() {
               HELP.Log=true;
             }
           },
           items: [{
             id:'hlpEdit',
             handler: function() {HELP.EditCur();},
             iconCls:'editor',
             text: DLG.t("helpMenuEdit")
           },{
             id:'hlpDel',
             handler: function() {HELP.DelCur();},
             iconCls:'delete',
             text: DLG.t("helpMenuDel")
           }]
        });

        var items=[{
             id:'mkShortCad',
             handler: function() {
			 // Создаем ярлык на рабочем столе
			  var s=[];

			  s[s.length]="['"+modulsmenu.curData.data.name+"','"+modulsmenu.curData.data.ico+"', '"+modulsmenu.curData.data.descript+"','"+modulsmenu.curData.data.runstr+"']";

			  /*if(modulsmenu.SelectedModuls!=null && modulsmenu.SelectedModuls.length>0) {
				for(var i=0;i<modulsmenu.SelectedModuls.length;i++) {
					s[s.length]="['"+modulsmenu.SelectedModuls[i].data.name+"','"+modulsmenu.SelectedModuls[i].data.ico+"', '"+modulsmenu.SelectedModuls[i].data.descript+"','"+modulsmenu.SelectedModuls[i].data.runstr+"']";
				}
			  }*/

			  Ext.Ajax.request({
				  url: 'desktop.php',
				  success: function(response) {
				    if(response.responseText=="ERR") return false;
				    else {
						eval('_DESKTOP_SHORTCUTS_=['+response.responseText+']');
						_desktop_store_.loadData(_DESKTOP_SHORTCUTS_);
					}
				  },
				  params: {
					  addshortcut:s.join(',')
				  }
			   });
			 },
             text: DLG.t("mkShortCad")
           }];
        if(CAN_CHMOD) {
           items[items.length]={
             id:'modAccess',
             handler: function() {
				 var sel=[];
				 for(var i=0;i<modulsmenu.SelectedModuls.length;i++) {
					sel[sel.length]=modulsmenu.SelectedModuls[i].data.name;
				 }
				 ChangeAccess('mod',sel,CurrentTree);
			 },
             iconCls:'acc',
             text: DLG.t("TreePopupAccess")
           }
        }

        /*,{
             id:'mod2DeskTop',
             handler: function() {Mod2Desktop();},
             iconCls:'',
             text: 'На рабочий стол'
        }*/

        modulsmenu = new Ext.menu.Menu({
           id:"modMenuContext",
           items:items
        });

        Ext.EventManager.on("helpDiv", 'contextmenu', function(e){
          helpmenu.showAt(e.xy);
          HELP.Log=false;
          e.stopEvent();
        });

        tree = new Tree.TreePanel({
          id:'StructTree',
          //el:'TreeDiv',
          autoScroll:true,
          animate:true,
          enableDD:(_TREE_ACCESS_>1? true:false),
          ddGroup:'desktop',
          containerScroll: false,
          bodyBorder:false,
          border:false,
          rootVisible:false,
	  selModel:new Ext.tree.MultiSelectionModel(),
          loader: new Tree.TreeLoader({
            dataUrl:'getbranches.php'
          }),
          listeners:treeListeners,
          tbar:[(CAN_PUBLIC? {
            id:'tree-publication-butt',
	    tooltip:DLG.t('TreeMenuPublication'),
            iconCls:'publ1',
            handler:function() {
              publication();
            }
         }:''),(_TREE_ACCESS_>2? {
            id:'ttpAdd',
			tooltip:DLG.t('TreeMenuMakePage'),
            iconCls:'add',
            handler:function() {
              popup_add_do(0, tree);
            }
         }:''),
	  ((_DOCS_IMPORT && _TREE_ACCESS_>2)? {tooltip:DLG.t('Make child page from file'),
		id:'ttpImportMS',
		iconCls:'office-add',
		handler:function(){popup_add_file(0,tree,1);}}:''),

	  {
            id:'ttpRefresh',
			tooltip:DLG.t('TreeMenuRefresh'),
            iconCls:'refresh',
            handler:function() {
              TreeProps=[];
              tree.getLoader().load(tree.getRootNode());
            }
         },{
            tooltip:DLG.t('TreeMenuShowAll'),
            iconCls:'openall',
            handler:function() {
              tree.expandAll();
            }
         },{
            tooltip:DLG.t('TreeMenuCollapseAll'),
            iconCls:'closeall',
            handler:function() {
              tree.collapseAll();
            }
         },(_TREE_ACCESS_>2? {
           id:'ttpPaste',
           disabled:true,
           handler: function() {popup_paste_do('root',tree);},
           iconCls:'paste',
           tooltip: DLG.t("TreeRootPaste")
         }:''),
         (CAN_CHMOD? {
             id:'ttpAccess',
             disabled:true,
             handler: function() {ChangeAccess(null,null,CurrentTree);},
             iconCls:'acc',
             tooltip: DLG.t("TreeRootAccess")
         }:''),(_TREE_ACCESS_>2? {
            id:'ttpDelete',
            tooltip:DLG.t('TreeMenuDelete'),
            iconCls:'delete',
            disabled:true,
            handler:function() {
              popup_del_do(tree);
            }
         }:'')]
        });

        // set the root node
        var root = new Tree.AsyncTreeNode({
          text: 'Root',
          draggable:false,
          id:'0'
       });

			if(_TREE_ACCESS_>0) {
			left_column_items[0]={
                        //contentEl: 'TreeDiv',
			id:'StructTreePanel',
                        title:DLG.t('Structure'),
			tooltip:DLG.t('Structure'),
                        border:false,
                        iconCls:'nav',
                        autoScroll:false,
			items:tree,
                        listeners:{
                          resize:function(x,y,h,w) {
                            if(LogResizeViewPort) {
                              if(h!=null) tree.setHeight(h);
                              if(w!=null) tree.setWidth(w);
                            }
                          }
                        }
			}
		}

       tree.setRootNode(root);

       LeftPanelItems[0]=new Ext.TabPanel({
							id:'ToolsTabPanel',
							region:'center',//'north',
							border:false,
							//height:200,
							deferredRender:false,
							activeTab:0,
							enableTabScroll:true,
							draggable:false,
							split:true,
							listeners:{
								beforetabchange : function(tp, nt, ct ) {
									if(ct!=null) {
										ct.setTitle('&nbsp;');
										nt.setTitle(nt.tooltip);
									}
								}
							},
							items:left_column_items
			});

	var vpitm=[];
	vpitm[vpitm.length]=TopToolbar;
	if(_HELP_STATUS) vpitm[vpitm.length]={
                    region:'south',
                    id:'helpPort',
		    iconCls:'help',
                    contentEl: 'helpDiv',
                    split:true,
                    height: 100,
                    minSize: 100,
                    maxSize: 200,
                    collapsible: true,
                    title:TEXTS.Help,
                    margins:'0 0 0 0'

                };
	vpitm[vpitm.length]=new Ext.Panel({
		    title:ServerName+' :: '+AdminLogin,
                    region:'west',
		    bodyBorder:false,
		    border:true,
            id:'west-panel',
            split:true,
            width: 250,
            minSize: 175,
            maxSize: 400,
            collapsible: true,
            margins:'0 0 0 5',
		    layout: 'border',
		    items:LeftPanelItems
	});
	vpitm[vpitm.length]=tabs;

       viewport = new Ext.Viewport({
            layout:'border',
            items:vpitm
        });

       treeEditor = new Ext.tree.TreeEditor(tree, {
        allowBlank: false,
        cancelOnEsc:true,
        completeOnEnter:true,
		editDelay: 1000,
		listeners:{
		  beforestartedit:function(x,y,z) {
		    return false;
		  }
		}
       });

       treeEditor.on("complete",function(a,b,c) {
          var s=AJAX.get('treefunc',{text:b,updatetitle:a.editNode.id});
	  treeChangeAttr(a.editNode.id,1);
       });

       root.expand();
       LogResizeViewPort=true;

       Ext.EventManager.on(document.body, 'mouseup', handleBodyClick);
       if(firebug_log) HELP.echo(DLG.t('FirebugMessage'));
       else HELP.show('index|bbody');

      window.setTimeout(function(){
	tree.setHeight(Ext.getCmp('StructTreePanel').getInnerHeight());
	tree.setWidth(Ext.getCmp('StructTreePanel').getInnerWidth());

	if(Ext.isOpera) {
	  Ext.MessageBox.show({
	    title: DLG.t('Opera specified'),
	    msg: DLG.t('In Opera to open popup menus do:<br /><b>Shift+Click</b><br /><i>Show this message next time?</i>'),
	    width:450,
	    buttons: Ext.MessageBox.YESNOCANCEL,
	    fn: function(btn) {
	      if(btn=='no') {
		// Нужно сохранить статус для данного юзера больше не показывать это окно
	      }
	    },
	    icon:'ext-mb-warning'

	  });

	}

      },1000);


    }

    function FormActions() {
      this.Win=null;
      this.RunMethods=new Array();
      this.run=function(win) {
        this.Win=win;
        for(var i=0;i<this.RunMethods.length;i++) {
          eval("this."+this.RunMethods[i]+"()");
        }
      }
    }

    FACT=new FormActions();
