LOCK_TIMEOUT=15000; // ���-�� �.�. ��� ������������� ���������� ������ � ���������� �������
selectedCods=[];
LastOpendPreviewWind=null;
ClipboardData='';
ClipboardDataText='';

(function(){ // ����������� ���� ��������
var ua = navigator.userAgent, av = navigator.appVersion, v, i;
$is={};
$is.Opera = !!(window.opera && opera.buildNumber);
$is.WebKit = /WebKit/.test(ua);
$is.OldWebKit = $is.WebKit && !window.getSelection().getRangeAt;
$is.IE = !$is.WebKit && !$is.Opera && (/MSIE/gi).test(ua) && (/Explorer/gi).test(navigator.appName);
$is.IE6 = $is.IE && /MSIE [56]/.test(ua);
$is.IE5 = $is.IE && /MSIE [5]/.test(ua);
$is.Gecko = !$is.WebKit && /Gecko/.test(ua);
$is.Mac = ua.indexOf('Mac') != -1;
for (i in $is) if (!$is[i]) $is[i]=NaN;
if (!$is.IE5) v = (ua.toLowerCase().match(new RegExp(".+(?:rv|it|ra|ie)[\\/: ]([\\d.]+)"))||[])[1];
switch (true) {
    case ($is.WebKit): $is.WebKit=v=v>599?4:v>499?3:v>399?2:1;break;
    case ($is.Opera): $is.Opera =v=v||9;break;
    case ($is.Gecko): $is.Gecko =v=v.substr(0,3)||1.8;break;
    case ($is.IE): $is.IE =v= window.XMLHttpRequest ? 7 : (/MSIE [5]/.test(av)) ? (/MSIE 5.5/.test(av))?5.5:5 : 6;
    };
$is.verb = v;
$is.ok = !!($is.Opera>=9 || $is.IE>=6 || $is.Gecko || $is.WebKit>2);
})();

function treeChangeAttr(id,attr) {

	var nod=tree.getNodeById(id);
	if(nod==null && tree_extend!=null) nod=tree_extend.getNodeById(id);
	if(nod!=null) {
		if(nod.leaf) nod.ui.iconNode.className="x-tree-node-icon file"+attr;
		else nod.ui.iconNode.className="x-tree-node-icon folder"+attr;
	}
	return nod;
}

function IsConteneditable() {
  if(navigator.userAgent.indexOf('Safari')>0) return true;
  if($is.IE>=5.5) return true;
  if($is.Opera>=9.5) return true;
  if($is.Gecko>=1.9) {return true;}
  return false;
}

function GetUnicum() {
  var o = new Date();
  return o.getHours()+o.getMinutes()+o.getSeconds()+o.getMilliseconds();
}

function rusescape(s) {
  return escape(s);
}

function BranchOnDblClick(id,e) {
  page_preview(id,e.ctrlKey);
  return false;
}

function hide_left() {
  if(document.getElementById('lefttop').style.display=='none') {
    document.getElementById('lefttop').style.display='';
  } else {
    document.getElementById('lefttop').style.display='none';
  }
}

function start_resize(event) {
  if (!event)event=window.event;
  resize_log=true;
  var o=document.getElementById('resize_line');
  o.style.display='';
  o.style.left=event.clientX-6;
}

function resize_left(event) {
  if (!event)event=window.event;
  document.getElementById('resize_line').style.left=event.clientX-6;
}

function end_resize(event) {
  if (!event)event=window.event;
  var o=document.getElementById('resize_line');
  resize_log=false;
  o.style.left=0;
  o.style.display='none';
  document.getElementById('lefttop').style.display='';
  document.getElementById('lefttop').width=event.clientX;
}

function show_control() {
  var o=document.getElementById('left_popup');
  if(o.style.display=='none') o.style.display='';
  else o.style.display='none';
  return false;
}

function change_control(n) {

  var i=1;
  while(document.getElementById('cc'+i)!=null) {
    if(i==n) document.getElementById('cc'+i).style.display='';
    else document.getElementById('cc'+i).style.display='none';
    i++;
  }
  return false;
}

function BranchOnRightClick(id,event) {

  body_on_click(event);

  CurrentTreeId=id;
  if (!event)event=window.event;

  var o=document.getElementById('popup_menu');
  var x=event.clientX;
  o.style.left=x;

  var y=event.clientY;
  if((popupheight+y)>document.body.offsetHeight) {
    y=document.body.offsetHeight-popupheight;
  }
  o.style.top=y;
  o.style.display='';
  o.zIndex=1000;

  if(TreeBuffer=='') document.getElementById('popup_paste').className='menu_dsbl';
  else document.getElementById('popup_paste').className='menu';

  if(tree.hasChildren(id)>0) document.getElementById('popup_cut').className='menu_dsbl';
  else document.getElementById('popup_cut').className='menu';

  if(TreeProps[id]==null) {
    TreeProps[id]=loadHTML('treefunc.php?getprop='+id);
  }
  var s=TreeProps[id].split('|');
  alert(s);
  if(s.length>2) {
    //4-rd,5-ad,6-ed
    if(s[6]==1) Ext.getCm('tppEdit').enable();
    else Ext.getCm('tppEdit').disable();
    //document.getElementById('popup_id').innerHTML=s[0];
    //document.getElementById('popup_owner').innerHTML=s[1];
    //document.getElementById('popup_user').innerHTML=s[2];
  }
}

function BranchOnClick(id) {

  if(CheckEditorDiv() && document.all) {  // IE
    CurrenRange.select();
  }
  var log=false;
  if(document.all && event.ctrlKey) {
    event.srcElement.id='sel'+id;
    selectedCods[selectedCods.length]=id;
    log=true;
  }
  if(selectedCods.length>0) {
    for(var i=0;i<selectedCods.length;i++) {
      if(log) {
        document.getElementById('sel'+selectedCods[i]).className='selectedTreeRow';
      } else {
        document.getElementById('sel'+selectedCods[i]).className='';
      }
    }
  }
  if(!log) selectedCods=new Array();

  if(KeyLog) BranchOnDblClick(id);

}

function body_on_click(event) {
  if(resize_log) end_resize(event);
  //document.getElementById('popup_menu').style.display='none';
  //document.getElementById('popup_menu_root').style.display='none';
}

SaveEditor=null;
PWTab=null;
OpenEditors=new Array();
function OpenTextInEditor(e) {
  SaveEditor=e;
  PWTab=tabs.getActiveTab();
  //FCKeditorAPI.GetInstance('FCKeditor1').SetHTML(e.getValue());
  //editorShow(true);
}

function save_editor_text(f) {
  SaveEditor.setValue(FCKeditorAPI.GetInstance('FCKeditor1').GetHTML());
  editorShow(false);
  return false;
}

function editorShow(act) {
  if(act) {
    tabs.items.get(1).enable();
    tabs.items.get(1).show();
  } else {
    tabs.items.get(1).disable();
    PWTab.show();
  }
}

function CheckEditorDiv() {
  // var o=document.getElementById('editorDiv');
  // if(o.style.display!='none' && parseInt(o.style.height)!=0) {
  //   return true;
  // }
  return false;
}

CurrenRange=null;
function fOnSelectionChange() {
   if(CheckEditorDiv() && document.all) {
      CurrenRange=FCKeditorAPI.GetInstance('FCKeditor1').EditorDocument.selection.createRange();
   }
}

function FCKeditor_OnComplete(editorInstance) {
  //tabs.items.get(1).show();
  //tabs.items.get(0).show();
  //tabs.items.get(1).disable();
}

function onLoad(){
  //FCKeditorAPI.GetInstance('FCKeditor1').Events.AttachEvent( 'OnSelectionChange', fOnSelectionChange ) ;
}

BufferTreeAction='';

// tree popup functions
function popup_cut_do(tree) {
  CurTreeNode=tree.getSelectionModel().selNodes[0];
  BufferTreeNode=CurTreeNode;
  BufferTreeAction='cut';
  CurTreeNode.remove();
  tree.getTopToolbar().items.get('ttpPaste').enable();
  return false;
}

function popup_copy_do(mod,tr) {
  // mod='' - �������� ������ ������� ��������, mod='all' - �������� ������� �� ����� �������
  if(mod==null) mod='';
  if(tr==null) tr=tree;
  CurTreeNode=tr.getSelectionModel().selNodes[0];
  ClipboardData='/'+CurTreeNode.id+'.html';
  ClipboardDataText=CurTreeNode.text;
  /*if(IsConteneditable()) {
  // ���� ������� ���������, ������ � ����� ����� ��������
    AJAX.loadHTML_do('ajaxfunc.php?getpath='+CurTreeNode.id, true, function(s) {
        if(s!='') {
          //window.clipboardData.setData('Text',s);
		  ClipboardData=s;
        }
    });
  }*/

  BufferTreeNode=CurTreeNode;
  BufferTreeAction='copy'+mod;
  tr.getTopToolbar().items.get('ttpPaste').enable();
  return false;
}

function popup_paste_do(mod,tree) {
  if(mod!=null && mod=='root') CurTreeNode=tree.getRootNode();
  else CurTreeNode=tree.getSelectionModel().selNodes[0];
  var s=AJAX.get('treefunc',{paste:BufferTreeAction,id:BufferTreeNode.id,pid:CurTreeNode.id});
  if(s!='') {
    CurTreeNode.leaf=false;
    var lf=(BufferTreeAction=='copy'? true:BufferTreeNode.isLeaf());
    var nn=new Ext.tree.TreeNode({
      id:s,
      text:BufferTreeNode.text,
      leaf:lf,
      iconCls:(BufferTreeNode.isLeaf()? "file":"folder")+'1'
    });

    CurTreeNode.appendChild(nn);
    tree.getLoader().load(nn);
    if(BufferTreeAction=='cut') BufferTreeAction='copyall'; // ���� ������� ���� ����� �������, ����������� ������� ������ ���� ������������
  }
  return false;
}

function popup_add_file(id,tree,mode) {

    CurTreeNode=(id==null || id==0? tree.getRootNode():tree.getSelectionModel().selNodes[0]);

    var w=new Ext.Window({
	 id:'doc2html-win',
         title: DLG.t('Import content from file'),
         layout:'fit',
         width:300,
         height:250,
         closeAction:'close',
         plain: true,
	 iconCls:'office-'+(mode==1? 'add':'replace'),

         items:{
           title:'',
           html:'<iframe id="doc2html-frame" width="100%" height="100%" frameborder="0" src="doc2html/doc2html.php?id='+id+'&mode='+mode+'"></iframe>'
         },

         buttons: [{
	   id:'doc2html-ok',
           text:DLG.t('Ok'),
           disabled:true,
           handler: function(){
             Ext.getDom("doc2html-frame").contentDocument.forms[0].submit();
           }
         },{
           text: DLG.t('Close'),
           handler: function(){
             w.close();
           }
         }]
    });



    w.show();
}

function popup_add_do(id,tree,mode) {
  if(id==null) id=CurrentTreeId;
  var t=DLG.p('AddNodeText','AddNodeDefaultText');

  if(t!=null && t!='') {
    var s=AJAX.get('treefunc',{text:t,add:id});
	s=parseInt(s);
    if(!isNaN(s)) {
      if(id==0) CurTreeNode=tree.getRootNode();
      CurTreeNode.leaf=false;
      CurTreeNode.appendChild(new Ext.tree.TreeNode({id:s,text:t,leaf:true, cls:'file',iconCls:'file1'}));
    }
  }
  return false;
}

function popup_del_do(tree) {
  contextmenu.hide();

  var n=tree.getSelectionModel().getSelectedNodes();

  if(n.length>1) {
    if(DLG.c('DeleteNodesText')) {
      var sn=new Array();
      for(var i=0;i<n.length;i++) sn[sn.length]=n[i].id

      var s=AJAX.get('treefunc',{del:0,idarr:sn.join(',')});
	  if(s!='') {
        s=";"+s+";";
		for(var i=n.length-1;i>=0;i--) {
			if(s.indexOf(";"+n[i].id+";")!=-1) n[i].remove();
		}
      }
    }
  } else if(n.length==1) {
    if(DLG.c('DeleteNodeText')) {
      var s=AJAX.get('treefunc',{del:n[0].id});
	  if(s==n[0].id) {
        n[0].remove();
      }
    }
  }
  return false;
}


SpecHeightProp=38;
SpecHeightMinus=115;

function runModuleNew(name,title,ico) {
  if(CheckEditorDiv()) {

  } else {
    //hide_spec_div();
    OpenNewTab('./readext.php?ext='+name,title,(ico!=null? ico:'modulsico'));
  }
  return false;
}

function runTool(uri,title) {
  document.getElementById('mainframe').src=uri;
  OpenNewTab(uri,title,'settings');
  return false;
}

function publication() {
  Ext.getCmp('tree-publication-butt').disable();
  Ext.Ajax.request({
	url: 'treefunc.php?publication=y',
	success: function(response) {
	Ext.getCmp('tree-publication-butt').enable();
	    if(response.responseText=="OK") {
		 var ic=Ext.query("*[class=x-tree-node-icon file1]");
		 for(var i=0;i<ic.length;i++) {
		    ic[i].className='x-tree-node-icon';
		 }
		 var ic=Ext.query("*[class=x-tree-node-icon folder1]");
		 for(var i=0;i<ic.length;i++) {
		    ic[i].className='x-tree-node-icon';
		 }

		 DLG.a('PublicationOk');
		 var s=Ext.getDom('struct-pg-count');
		 var v=s.innerHTML.split(' / ');
		 if(v[1]!=null && _TREE_ACCESS_>1) {
		    s.innerHTML='0 / '+v[1];
		 }

	    }
	}
  });

  /*var s=AJAX.get('treefunc',{publication:'y'});
  if(s=='OK') {
    DLG.a('PublicationOk');
  }*/
  return false;
}

function MakePreviewUri(id, access) {
	if(access==null) access='0';
	return _URI_PREVIEW_TPL.replace('%id%',id).replace('%access%',access);
}

function ShowPreview(tree) {
  var o=document.getElementById('PreviewPath');
  var id=tree.getSelectedItemId();

  if(o.style.display=='none') {
    o.style.display='';
    document.getElementById('AdminPath').style.display='none';
    var url='/';
    if(id!=0) {
      if(TreeProps[id]==null) {
        TreeProps[id]=AJAX.get('treefunc',{getprop:id});
      }
      url=TreeProps[id].split('|');
      url=MakePreviewUri(url[0]);//'/'+url[0]+'.html?prev=yes';
    }
    document.getElementById('PreviewFrame').src=url;
    document.getElementById('PreviewFrame').focus();
  } else {
    o.style.display='none';
    document.getElementById('AdminPath').style.display='';
    document.getElementById('mainframe').focus();
  }
  return false;
}

CurBlockID=0;
CurBlockHtid=0;
CurPreviewUrl='';
function ShowBlockMenu(id,htid,event) {
  /*var o=document.getElementById('popup_menu_block');
  o.style.display='';
  var x=parseInt(document.getElementById('west-panel').style.width);
  o.style.left=event.clientX+x+10;
  o.style.top=event.clientY+45;
  CurBlockID=id;
  CurBlockHtid=htid;*/
  //alert(id+'?'+htid);
}

function HidePopup() {
  document.getElementById('popup_menu_block').style.display='none';
}

function BlockEdit() {
  var u='page_content.php?ch='+CurBlockID;
  if(CurBlockHtid!='' && CurBlockHtid!=0)
    u+='&sch='+CurBlockHtid;
  ShowPreview();
  document.getElementById('mainframe').src=u;
  return false;
}

function BlockClear() {
  if(DLG.a('ClearBlockConfirm')) {
    var s=AJAX.get('treefunc',{clearblock:CurBlockID});
    if(s=='OK') {
      document.getElementById('PreviewFrame').src=CurPreviewUrl+'?prev=yes';
    }
  }
  return false;
}

function BlockProps() {
  return false;
}

TreeChecked=new Array();
TreeCheckMode=false;
TreeCheckTable='';
TreeCheckID=0;

function RefChecked(mod,tree) {
  var s;
  for(var i=0;i<TreeChecked.length;i++) {
    s=TreeChecked[i].split('/');
    tree.showItemCheckbox(s[0],mod);
    if(mod==1) {
      tree.setCheck(s[0],s[1]);
    }
  }
  if(mod==0) {
    TreeChecked=new Array();
    TreeCheckMode=false;
    document.getElementById('TreeToolsMod1').style.display='';
    document.getElementById('TreeToolsMod2').style.display='none';
  }
}

TreeCheckIdFold='';

function connecttabsnew(t,id,ext,fold) {

  var s=AJAX.get("setconnect",{act:'show',t:t,id:id,ext:ext,fold:fold});
  if(s!='') eval(s);

  /*RefChecked(0);

  if(fold!=null) {
   TreeCheckIdFold=fold;
   TreeCheckID=id.split(',');
   id=0;
  } else {
    TreeCheckIdFold='';
    TreeCheckID=[id];
  }

  var s=AJAX.get('treefunc',{connect:t,id:id,ext:ext});
  if(s!='') {
    TreeCheckTable=t;
    TreeChecked=s.split('|');
    RefChecked(1);
    TreeCheckMode=true;
    document.getElementById('TreeToolsMod1').style.display='none';
    document.getElementById('TreeToolsMod2').style.display='';
  } */
}

function BranchOnOpen(id) {
  return true;
}

function tree_check_accept() {
  var s=new Array();
  var x='';
  for(var i=0;i<TreeChecked.length;i++) {
    x=TreeChecked[i].split('/');
    if(tree.getLevel(1)==0 && x[1]==1) {
      s[s.length]=x[0];
    } else if(tree.isItemChecked(x[0])) {
      s[s.length]=x[0];
    }
  }
  var s=AJAX.post('treefunc',{f:TreeCheckIdFold,doconnect:TreeCheckTable,id:TreeCheckID.join(',')},{cods:s.join('|')});
  RefChecked(0);
  return false;
}

function tree_change_mode() {
  RefChecked(0);
  return false;
}

function tree_selectall(mod) {
  for(var i=0;i<TreeChecked.length;i++) {
    s=TreeChecked[i].split('/');
    tree.setCheck(s[0],mod);
  }
  return false;
}

function KeyProcess(k) {
  if(k==33) popup_updown_do(1); // PgUp
  else if(k==34) popup_updown_do(2);
}

function ChangeAccess(mod,sel,tree) {

  if(mod==null) {
  // �������� ������ ������� � �������
    var n=tree.getSelectionModel().getSelectedNodes();
    var sn=new Array();
    for(var i=0;i<n.length;i++) sn[sn.length]=n[i].id

    sn=AJAX.get("chmod",{act:'show',ids:sn.join(',')});

  } else if(mod=='mod') {
  // �������� ������ ������� � ��������
    sn=AJAX.get("chmod",{act:'show',mod:CurrentModName,sel:sel.join(",")});
  }

  if(sn!='') {
    eval(sn);
  }
  return false;
}

CurrentModName='';
function ModMenuShow(e,mod,sel){
  if(modulsmenu.items.length==0) return false;

  CurrentModName=mod;
  modulsmenu.showAt(e.xy);
  modulsmenu.SelectedModuls=sel;
  e.stopEvent();

  //alert(sel[0].data.name);
}

function GetPageProps(cod) {
  if(TreeProps[cod]==null) {
    TreeProps[cod]=AJAX.get('treefunc',{getprop:cod});
  }
  return TreeProps[cod].split('|');
}


function page_preview(cod,newwin) {
  if(newwin==null) newwin=false;
  var s=getTreeProps(cod);
  if(s.length>4) {
    if(s[4]!=1) {
      //DLG.a('NoAccess');
      return false;
    }
  }



 if(!newwin && LastOpendPreviewWind!=null && LastOpendPreviewWind.Win!=null) {
	 LastOpendPreviewWind.TabPanel.show();
	 LastOpendPreviewWind.Win.location=MakePreviewUri(cod,_TREE_ACCESS_);//'/'+cod+'.html?prev='+_TREE_ACCESS_;
	 return false;
 }

  var o=new PrevObj();
  o.MakePrevTab(cod);
  o.TabPanel.on("destroy",function() {if(o==LastOpendPreviewWind) LastOpendPreviewWind=null;});
  LastOpendPreviewWind=o;

  return false;
}

function ChangeCurrentTitle(win,ttl,texts,moduls,tplfile,pgcod, rd, ad, ed, attr) {
  CurPreviewUrl=win.location.href;
  var t=tabs.getActiveTab();
  t.setTitle(ttl);


  t.iplugin.HistoryAdd(win,texts,moduls,tplfile,pgcod, rd, ad, ed, attr);
}

function CloseCurrTab(RefParent) {
  if(RefParent!=null && RefParent) {
    var s=tabs.getActiveTab().id.split('-');
    if(s[0]=='TabP') {
      s=parseInt(s[1]);
      if(ConectTab[s]!=null) {
        tabs.remove(tabs.getActiveTab().id);
        tabs.getItem('TabP-'+ConectTab[s]).show();
        try {
          document.getElementById('TabF-'+ConectTab[s]).contentWindow.UserReload();
        } catch(e){}
        return false;
      }
    }
  }
  tabs.remove(tabs.getActiveTab().id);
}

function page_edit(id) {
  var s=GetPageProps(id);
  OpenNewTab('page.php?ch='+id+'&nogo=y',s[3],'nav','Page-'+id);
  return false;
}

Iframes=0;
ConectTab=new Array();
AccTab2Id=new Array();

function isOpen(id) {
  var out=new Array();
  var x=0;
  for(var i=0;i<AccTab2Id.length;i++) {
    if(tabs.getItem(AccTab2Id[i][1])!=null) {
      if(AccTab2Id[i][0]==id) x=AccTab2Id[i][1];
      out[out.length]=AccTab2Id[i];
    }
  }
  AccTab2Id=out;
  return x;
}

IconsClass=new Array();

function CheckClass(fn) {
  var s='';
  var i=fn.length-1;
  while(i>0 && fn.charAt(i)!='/') i--;
  s=fn.substr(i+1);
  s=s.replace('.','');
  for(i=0;i<IconsClass.length;i++) {
    if(IconsClass[i]==s) return s;
  }
  IconsClass[IconsClass.length]=s;

  if(fn.indexOf('://')==-1) fn='../'+fn;
  if($is.IE>=5.5)
    document.styleSheets[0].addRule("."+s,"background-image:url("+fn+") !important;");
  else
    document.styleSheets[0].insertRule("."+s+" {background-image:url("+fn+") !important;}",0);

  return s;
}

function OpenNewTab(url,title,ico,id,tbar,bbar,plugin,ppanel) {
  if(id!=null) {
    var p=isOpen(id);
    if(p!=0) {
      tabs.getItem(p).show();
      p=p.split('-');
      p[0]=document.getElementById('TabF-'+p[1]);
      if(p[0].src.indexOf(url)==-1) p[0].src=url;
      return null;
    }
  }

  var s=tabs.getActiveTab();
  if(s!=null) {
    s=s.id.split('-')
    if(s[0]=='TabP') {
      ConectTab[Iframes]=parseInt(s[1]);
    }
  }



  if(ico==null) ico='';
  ico+='';
  if(ico.indexOf('.')!=-1) {
    ico=CheckClass(ico);
  }

  if(tbar!=null) {
     tbar=new Ext.Toolbar({
     autoShow:true,
     autoHeight:true,
     autoWidth:true,
     items:tbar
    });
  }
  if(bbar!=null) {
    bbar=new Ext.Toolbar({
      autoShow:true,
      autoHeight:true,
      autoWidth:true,
      items:bbar
    });
  }

  var t=tabs.add({
    parentpanel:ppanel,
    title: title,
    tooltip: title,
    id: 'TabP-'+Iframes,
    iconCls:ico,
    html:'<iframe id="TabF-'+Iframes+'" src="'+url+'" width="100%" height="100%" frameborder="0"></iframe>',
    tbar:tbar,
    bbar:bbar,
    closable:true
  });

  if(plugin!=null) {
    t.iplugin=plugin;
    t.iplugin.PPanel=t;
  }
  t.show();

  if(tbar!=null) {
    t.getTopToolbar().setPosition(0,0);
  }

  if(id!=null) {
    AccTab2Id[AccTab2Id.length]=new Array(id,'TabP-'+Iframes);
  }
  s=Iframes;
  Iframes++;
  return t;
}

function tree_delete_selected() {

}

var WindowHelp=null;

HELP=new function() {

  this.doc=null;
  this.CurID='';
  this.Log=true;

  this.about=function() {

    var w=Ext.getCmp('about-win');

    if(w==null) {
	var w=new Ext.Window({
	     id:'about-win',
	     title: DLG.t('About wpier'),
	     layout:'fit',
	     width:450,
	     height:350,
	     closeAction:'close',
	     plain: true,
	     iconCls:'info',

	     items:{
	       title:'',
	       html:'<iframe width="100%" height="100%" frameborder="0" src="'+_LOCALE_DIR+'about.html"></iframe>'
	     },

	     buttons: [{
	       text: DLG.t('Close'),
	       handler: function(){
		 w.close();
	       }
	     }]
	});
    }


    w.show();
  }

  this.active=function() {
    if(!_HELP_STATUS) return false;
    if(viewport.items.get('helpPort').collapsed) return false;
    return this.Log;
  }

  this.echo=function(s) {
	if(s!=null && s!='') document.getElementById('helpDiv').innerHTML=s;
  }

  this.show=function(id) {
    if(this.active()) {
      var o=this;
      document.getElementById('helpDiv').innerHTML='';
      AJAX.loadHTML_do('gethelp.php?path='+id, true, function(s) {
        if(s!='') {
          o.CurID=s.substr(0,s.indexOf('|'));
          s=s.substr(s.indexOf('|')+1);
		  document.getElementById('helpDiv').innerHTML=s;
        }
      });
    }
  }

  this.EditCur=function() {
    this.makehelpwin();
    this.get(this.CurID);
  }

  this.DelCur=function() {
    if(DLG.c("HelpTopicDelAsc")) {
      AJAX.get('gethelp',{del:this.CurID});
      document.getElementById('helpDiv').innerHTML='';
    }
  }

  this.get=function(id) {
    //WindowHelp.items.get('HelpEditor').setTitle(id,'');
    var text=AJAX.get('gethelp',{topic:id});
    document.getElementById('HelpEditorDiv').innerHTML='<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%"><tr height="25"><td><input id="HelpEditorID" value="'+id+'" style="width:100%"></td></tr><tr><td><textarea id="HelpEditorText" lang="ru" style="width:100%;height:100%">'+text+'</textarea></td></tr></table>';
    WindowHelp.buttons[0].enable();
    return false;
  }

  var intv=null;
  var curstyle='';
  var curelm=null;

  this.light=function(id) {
    if(intv!=null) {
      curelm.style.border=curstyle;
      window.clearTimeout(intv);
    }
    curelm=this.doc.getElementById(id);
    curstyle=curelm.style.border;
    curelm.style.border='2px solid red';

    intv=window.setTimeout(function(){curelm.style.border=curstyle;window.clearTimeout(intv);intv=null;},1000);
    return false;
  }

  this.makehelpwin=function() {
    if(!WindowHelp) {
      document.getElementById('HelpWinDiv').style.display='';

      WindowHelp = new Ext.Window({
         el:'HelpWinDiv',
         title: DLG.t('Help editor'),
         layout:'fit',
         width:500,
         height:300,
         closeAction:'hide',
         plain: true,

         items:{
           title:'',
           id:'HelpEditor',
           html:'<div id="HelpEditorDiv" style="width:100%;height:100%"></div>'
         },

         buttons: [{
           text: DLG.t('Submit'),
           id:'HelpSubmit',
           disabled:true,
           handler: function(){
             Ext.Ajax.request({
               url: 'gethelp.php?change=true',
               success: function(response) {WindowHelp.hide();},
               params: {
                 id:document.getElementById('HelpEditorID').value,
                 text:escape(document.getElementById('HelpEditorText').value)
               }
             });
           }
         },{
           text: DLG.t('Close'),
           handler: function(){
             WindowHelp.hide();
           }
         }]
      });
    }
    WindowHelp.show();
  }

  this.create=function(id,docum) {
    this.doc=docum;
    this.makehelpwin();
    WindowHelp.items.get('HelpEditor').setTitle('&nbsp;','');
    id=id.split('|');
    var html='';
    for(var i=1;i<id.length;i++) {
      html+='<div style="overflow:auto;padding-left:'+(4+i*15)+'px"><a href="" onclick="return HELP.light(\''+id[i]+'\')" ondblclick="HELP.get(\''+id[0]+'.'+id[i]+'\')">'+id[i]+'</a></div>';
    }
    document.getElementById('HelpEditorDiv').innerHTML=html;
    WindowHelp.buttons[0].disable();
    return false;
  }
}
// End HElp class

// Programms class
function Programm() {

   this.OppWin=new Array();
   this.Counter=0;

   this.ddrun=function(tr,nod,dd) {
     if(nod.processed==null) {
       var s=dd.id.split('-');
       alert(s);
       if(s.length==3) {
         try {
           eval('this.DDmethods.'+s[2]+'(tr,nod)');
         }catch(e){}
       }
       nod.processed=true;
     } else nod.processed=null;
     return false;
   }

   this.DDmethods=new function() {}

   this.RunOutApp=function(url) {
     if(url.indexOf('://')>0) var s=AJAX.post("httpgate",{},{gateurl:url});
     else var s=AJAX.get(url);
     if(s!='') eval(s);
   }

   this.winmin=function(w) {
     w.hide();
   }

   this.winclose=function(w) {
     var i=parseInt(w.id.substr(7));
     Ext.get('ProgBut'+i).remove();
     i=this.FindByWid(i);
     if(i!=-1) this.OppWin[i]=null;
   }

   this.GetWinByWid=function(wid) {
     var i=this.FindByWid(wid);
     if(i==-1) return null;
     return this.OppWin[i];
   }

   this.FindByWid=function(wid){
     for(var i=0;i<this.OppWin.length;i++) {
       if(this.OppWin[i]!=null && this.OppWin[i].id=='ProgWin'+wid) {
         return i;
       }
     }
     return -1;
   }

   this.ShowByWid=function(wid) {
     var i=this.FindByWid(wid);
     if(i==-1) return false;
     this.OppWin[i].show();
     return true;
   }

   this.settitle=function(wid,title) {
     Ext.getCmp('ProgWin'+wid).setTitle(title);
     Ext.getCmp('ProgBut'+wid).setText(title);
   }

   this.makewin=function(src,title,id,w,h,mod,wconf) {

      if(id==null) id=this.Counter;
      if(mod==null) mod='';
      var pnl=new Ext.Panel({
            title: '',
            region: 'center',
            html:'<iframe id="PrgIFr'+id+'" src="'+src+'" width="100%" scrolling="no" height="100%" frameborder="0"></iframe>'
         });
      if(wconf==null) {

       var WPrg = new Ext.Window({
         id:'ProgWin'+id,
         title:title,
         layout:'fit',
         width:parseInt(w),
         height:parseInt(h),
         minimizable:(mod=='dlg'? false:true),
         maximizable:(mod=='dlg'? false:true),
         plain: true,
         items:[pnl],
         listeners:{
           minimize:function(w){App.winmin(w);},
           close:function(w) {if(mod!='dlg') App.winclose(w);}
         }

       });
      } else {
        var WPrg = new Ext.Window(wconf);
      }

      WPrg.show();

      if(mod=='dlg'){}
      else {
        Ext.getCmp('TopToolbarPanel').addButton({
          text:title,
          id:'ProgBut'+id,
          handler:function(){
            WPrg.show();
          }
        });
      }
      this.OppWin[this.OppWin.length]=WPrg;
      this.Counter++;
	  return WPrg;

  }

  this.run=function(fn,title,id,w,h,mod,wconf) {

    if(w==null) w=550;
    if(h==null) h=400;
    return this.makewin(fn,title,id,w,h,mod);
  }
}

function iTreeFunc() {
  this.node=null;
  this.mode='before';
  this.Insert=function(txt) {
  // ������� ����� � ���� �������� � ��������� �����. ������� ���������� Ajax-�������.
    var s=AJAX.get('treefunc',{file:txt,add:this.node.parentNode.id,before:this.node.id,point:tree.currDDPoint});
    if(s!='') {
      s=s.split("|");
      var id=parseInt(s[0])
      var o=new Ext.tree.TreeNode({id:id,text:s[1],leaf:true, cls:'file'});
      if(tree.currDDPoint=='above') {
        this.node.parentNode.insertBefore(o,this.node);
      } else {
        if(this.node.nextSibling==null) {
          this.node.parentNode.appendChild(o);
        } else {
          this.node.parentNode.insertBefore(o,this.node.nextSibling);
        }
      }
    }
  }
}
TreeFunc=new iTreeFunc();


function DrugDrop() {
// ����-���� �� ������� ����
   this.DD2DT=function(e,id,tid,mod,ico) {
    /*var bd=Ext.get('DeskTopcons');
    var src=document.getElementById('d-shortcut-'+id);
    if(src==null) {
      src=document.getElementById('x-shortcut-'+id).innerHTML;
      bd.createChild({tag: 'dt', id:'d-shortcut-'+id, html: src});
      document.getElementById('d-shortcut-'+id).firstChild.firstChild.src=ico;
      bd=Ext.get('d-shortcut-'+id);
      var dd = new Ext.dd.DDProxy('d-shortcut-'+id, 'desktop');
      dd.onDragDrop=function(e,idd) {DD.DDORDER(this.id,idd);}
      dd.endDrag=function(e) {return false;}
      this.save();
    }*/
    return false;
  }

  // ������� ����-���� ������ �� ������� ����� (��������� �������)
  this.DDORDER=function(id,idd) {
    /*if(idd=='Garb-Bask') {
      Ext.get(id).remove();
      this.save();
      return;
    }
    if(idd.indexOf('d-shortcut-')==0 && idd!=id) {
      var o=Ext.get(idd).dom.nextSibling;
      var log=true;
      while(o!=null) {
        if(o.id==id) {log=false;break;}
        o=o.nextSibling;
      }
      if(!log) Ext.get(id).insertBefore(Ext.get(idd));
      else Ext.get(id).insertAfter(Ext.get(idd));
      this.save();
    } */
  }

  // ������� ���������� �������� �����
  this.save=function() {
    //var s=document.getElementById('DeskTopcons').innerHTML;
	/*var re = /&/g;
	s=s.replace(re,'*amp*')
	AJAX.post('treefunc',null,{DeskTop:s});*/

	/*Ext.Ajax.request({
	   url: 'treefunc.php',
	   params: { desktop: s }
	});*/

  }

  this.ReadDesktop=function() {
    /*var o=Ext.get('DeskTopcons').dom.firstChild;
    while(o!=null) {
      var dd = new Ext.dd.DDProxy(o.id, 'desktop');
      dd.onDragDrop=function(e,idd) {DD.DDORDER(this.id,idd);}
      dd.endDrag=function(e) {return false;}
      o=o.nextSibling;
    }*/
    this.ConfigDD();
  }

  this.MkTreeDropZon=function(tree) {
	new Ext.tree.TreeDropZone(tree,{
      ddGroup : 'desktop',
      copy:false,
      onNodeDrop:function( nodeData, source, e, obj ) {
        if(obj.grid!=null) {
        // ���������� ����
          var url;
          for(var i=0;i<obj.selections.length;i++) {
            url=STORE_LOCATIONS[obj.grid.currentStore][0];
            url=url+(url.indexOf('?')==-1? '?':'&')+'gethtml='+obj.grid.currentPath+obj.selections[i].data.f.replace('|*e*|','.')+'&handler='+HostName+'./getfiles.php&ret=TreeFunc.Insert';
            TreeFunc.node=nodeData.node;
            AJAX.sendScriptQuery(url);
          }
          return true;
        }
        if(obj.node!=null) {
        // ���������� ����� � ������
           if(obj.node.ownerTree.currDDPoint=='above') {
             nodeData.node.parentNode.insertBefore(obj.node,nodeData.node);
           } else {
             if(nodeData.node.nextSibling==null) {
               nodeData.node.parentNode.appendChild(obj.node);
             } else {
               nodeData.node.parentNode.insertBefore(obj.node,nodeData.node.nextSibling);
             }
           }
           var s=AJAX.get('treefunc',{treename:nodeData.node.ownerTree.id,movenode:obj.node.id,pid:nodeData.node.parentNode.id,ind:nodeData.node.parentNode.indexOf(obj.node)});
           return true;
        }
        return false;
      }
    });
  }

  this.ConfigDD=function() {

	if(tree!=null) this.MkTreeDropZon(tree);
	if(tree_extend!=null) this.MkTreeDropZon(tree_extend);

    if(_TREE_ACCESS_>1)
    new Ext.dd.DropTarget(tabs.items.get('DescTopArea').container,{
         ddGroup : 'desktop',
         copy:false,
         notifyDrop : function(dd, e, data){
// ��� �������, ���������� ��� ��������� �������� �� ������� ����
            var o=e.target;
            if(o==null) return false;
            while(o!=null && o.id!=null && o.id!='DescTopArea' && o.id!='centerColumn' && o.id.indexOf('d-shortcut-')!=0) {
	      o=o.parentNode;
	      if(o) return false;
            }
            if(o.id.indexOf('d-shortcut-')==0) {
              try {
                eval('App.DDmethods.'+o.id.substr(11)+'(data)')
              } catch(e) {}
              //alert(o.id.substr(11)+'?'+data)
            } else {
              alert('Put on desktop');
            }
         }
    });
  }
}

function RunProgramm(fn,id,mod) {
  alert(fn);
}


DLG=new function() {
  this.c=function(t,v) {
    return confirm(this.t(t,v));
  }
  // Alert
  this.a=function(t,v) {
    alert(this.t(t,v));
  }
  this.e=function(t,v) {
    Ext.MessageBox.show({
       title: this.t('Error'),
       msg: this.t(t,v),
       buttons: Ext.MessageBox.OK,
       icon: "error"
     });
  }

  this.w=function(t,v) {
    Ext.MessageBox.show({
       title: this.t('Warning'),
       msg: this.t(t,v),
       buttons: Ext.MessageBox.OK,
       icon: "warning"
     });
  }

  // Warning
  /*this.w=function(t,v) {
    alert(this.t(t,v));
  }*/
  this.p=function(t,t1,v,v1) {
    return prompt(this.t(t,v),this.t(t1,v1));
  }
  this.t=function(t,val) {
    var tx;
    try {
      eval('tx=TEXTS["'+t+'"];if(tx==null) tx="'+t+'";');
    } catch(e) {tx=t;}
    if(val!=null) {
      for(var i=0;i<val.length;i++) tx=tx.replace('%s',val[i]);
    }
    return tx;
  }
}

// Plugin ��� ��������� � �����
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

EDITOR=new function() {
  this.editfolder=function(table,folder,id,title) {
    DLG.a('Error: did not match any editor!');
  }

  this.modul_add=function(mod,pg) {
    OpenNewTab('readext2pg.php?new=yes&ext='+mod+'&catalog='+pg,mod+':New','modulsico',mod+'-'+GetUnicum());
  }

  this.modul_edt=function(mod,pg) {
    OpenNewTab('readext2pg.php?ext='+mod+'&catalog='+pg,mod,'modulsico',mod+'-'+pg);
    //App.makewin('readext2pg.php?ext='+mod+'&catalog='+pg,mod,600,500);
  }

  this.tpl_edit=function(tpl,pg) {
    OpenFileInCodeEditor(tpl);
  }
}
LOCKOBJ_TO=null;

LOCKOBJ=new function() {

  this.lockarr=new Array();

  this.idschd="";

  this.timefunctions=[];
  this.timeparams={};

  this.lockinarr=function(tab,id) {
    var o=new Date();o=o.format('YmdHis');
    for(var i=0;i<this.lockarr.length;i++) {
      if(this.lockarr[i][0]==tab && this.lockarr[i][1]==id) {
        this.lockarr[i][3]=o;
        return;
      }
    }
  }

  this.check=function(tab,id) {
    if(AJAX.get('lockfunc',{lock:tab,id:id})=='locked') return false;
    return true;
  }

  this.add=function(tab,id) {

    if(!this.check(tab,id)) return false;
    for(var i=0;i<this.lockarr.length;i++) {
      if(this.lockarr[i][0]==tab && this.lockarr[i][1]==id) return true;
    }
    i=new Date();i=i.format('YmdHis');
    this.lockarr[this.lockarr.length]=[tab,id,i];
    return true;
  }

  this.lock=function() {



    if(LOCKOBJ_TO!=null) window.clearTimeout(LOCKOBJ_TO);

    var o=new Date();o=parseInt(o.format('YmdHis'))-4;
    var a=[];
    if(this.lockarr!=null && this.lockarr.length>0) for(var i=0;i<this.lockarr.length;i++) {
      if(this.lockarr[i][3]<=o) {
        a[a.length]=this.lockarr[i][0]+':'+this.lockarr[i][1];
      }
    }

    //AJAX.post('lockfunc',{locked:'yes'},{unlock:a.join(';')},true);

    LOCKOBJ.timeparams.unlock=a.join(';');

    Ext.Ajax.request({
      url: 'lockfunc.php?locked=yes',
      params: LOCKOBJ.timeparams,
	  success: function(response) {

		//alert(LOCKOBJ.timefunctions[0]);

		if(response.responseText=='error') {
			parent.location="login.php";
			return false;
		}


		if(_TREE_ACCESS_>1 && response.responseText.substr(0,4)=='cnt:') {
		    var v=response.responseText.split(":");
		    Ext.getCmp('struct-pg-count').setText((v[1]==0? 0:'<span style="color:red">'+v[1]+'</span>')+' / '+v[2],false);

		    if(LOCKOBJ.idschd!=v[3]) {

			if(LOCKOBJ.idschd) {
			  var l=LOCKOBJ.idschd.split(",");
			  for(var i=0;i<l.length;i++) {
			    treeChangeAttr(l[i],'');
			  }
			}
			LOCKOBJ.idschd=v[3];
			v=v[3].split(",");
			for(var i=0;i<v.length;i++) {
			    treeChangeAttr(v[i],1);
			}

		    }

		}

		if(LOCKOBJ.timefunctions.length>0) {
			for(var i=0;i<LOCKOBJ.timefunctions.length;i++) {
				LOCKOBJ.timefunctions[i](response.responseText);
			}
		}
	  }
    });

    LOCKOBJ_TO=window.setTimeout(LOCKOBJ.lock,LOCK_TIMEOUT);
  }
}

MOD_BUFFER={};

LOCKOBJ_TO=window.setTimeout(LOCKOBJ.lock,LOCK_TIMEOUT);

App=new Programm();
DD=new DrugDrop();