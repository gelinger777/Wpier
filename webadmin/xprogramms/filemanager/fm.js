function iCommanderClass(obj) {
  
  this.conf={
    title:'sw-Commander',                                     
    buttons:null,
    copy:true,
    stores:true,                            
    modal:false,
    rpanelmode:'list',
    width:600,
    height:500,
    minimizable:true,
    maximizable:true,
    resizable:true,
    filestring:false,
    singleSelect:false,
    handler:function(f) {},
    mask:[['*.*','All formats (*.*)']]
  };
  if(obj!=null) {
    for (var prop in obj) {
     eval('this.conf.'+prop+'=obj.'+prop);
    }
  }
  this.currmask=this.conf.mask[0][0];
  this.FileName=''; 
  this.lpanel=null;
  this.rpanel=null;
  this.gr_l = null;
  this.gr_r = null;
  this.CurrentGrid=null;
  this.Window=null;
  
  this.stores = [];
  var d = new Date();
  this.id = 'iCmd'+d.getHours()+d.getMinutes()+d.getSeconds()+d.getMilliseconds();

  this.getUrl=function(url) {
    return url;
  }
  
  this.Url=this.getUrl(STORE_LOCATIONS[0][0]);
  this.Mode=STORE_LOCATIONS[0][2];
  
  this.addmask=function(ar) {
    var j,l,x,out=[];     
    var re=this.currmask.replace(/\*/g,'[\\w-~]*');
    re=re.replace(/\./,'\\.');
    re=re.split(',')
    var rg=[];
    for(var i=0;i<re.length;i++) out[out.length]='rg['+i+']=/^'+re[i]+'$/i'
    eval(out.join(';'));
    out=[];      
    for(var i=0;i<ar.length;i++) {
      if(ar[i][2]=="\t[DIR]") l=true;
      else {
        l=false;
        for(j=0;j<rg.length;j++) {
          if(rg[j].test(ar[i][0].replace('|*e*|','.'))) {
            l=true;
            break;
          }
        }
      }
      if(l) out[out.length]=ar[i]; 
    }
    return out;
  }
  
  this.setStore=function(url,mode) {
    return new Ext.data.SimpleStore({
        sortInfo:{
          field: "e", 
          direction: "ASC" 
        },
        fields: [
           {id:'f',name: 'f'},
           {name: 'd'},
           {name: 'e'},           
           {name: 's', type: 'float'},
           {name: 't'},
           {name: 'pt'}
        ]
      });
  
  }
  
  this.setCm=function() {
    var o=this;
    return new Ext.grid.ColumnModel([
        {id:'f',header: 'File', dataIndex: 'f', sortable: true, renderer:IO.RendFileType},
        {header: 'Type', width: 30, dataIndex: 'e', sortable: true},
        {header: 'Size', width: 50, dataIndex: 's', sortable: true,align:'right'},
        {header: 'Date', width: 80, dataIndex: 'd', sortable: true}
      ]);
  }
  
  this.ExeFile=function(fn,grid) {
    // Запускаем файл
    if(this.filename!=null) {
      var s=this.filename.getValue();
      if(s!='') {
        this.FileName=grid.currentStore+':'+grid.currentPath+s;
        this.conf.handler(this.FileName);        
      }
      this.Window.close();
    }      
  }
  
  this.CopyWin=null;
  
  this.Copy=function(data,grid) {
    if(this.CopyWin==null) {
      var s=STORE_LOCATIONS[grid.currentStore][1];
      var p='';
      //alert(STORE_LOCATIONS[grid.currentStore][1]);
      if(data.selections.length==1) {
        s=DLG.t('IO_CopyFromTo',[data.selections[0].data.f.replace('|*e*|','.'),s]);
        p+=grid.currentPath.replace('./','/')+data.selections[0].data.f.replace('|*e*|','.');
      } else {
        s=DLG.t('IO_CopyFiles',[data.selections.length,s]);
        p+=grid.currentPath.replace('./','/')+'*.*';
      }      
      var o=this;
      this.CopyWin=new Ext.Window({
        title:'sw-Commander',
        items: [new Ext.FormPanel({
          labelAlign: 'top',
          bodyStyle:'padding:10px 10px 0;background:none;border:none;',
          items:[{
            xtype:'textfield',
            fieldLabel : s,
            value : p,
            name: 'path',
            width:360,
            allowBlank:true
          }]         
        })],
        width:400,
        height:130,
        minimizable:false,
        maximizable:false,
        plain: true,
        resizable:false,
        closeAction:'close',
        listeners:{
          close:function(w) {
            o.CopyWin.destroy();
            o.CopyWin=null;
          }
        },
        buttons:[{ 
          text:DLG.t('But_OK'),
          handler:function(){
            var p=o.CopyWin.items.items[0].items.items[0].getValue();
            o.CopyWin.close();            
            var files=new Array();
            for(var i=0;i<data.selections.length;i++) {
              files[files.length]=data.selections[i].data.f.replace('|*e*|','.');
            }
            new IO.Copy(data.grid.currentStore,data.grid.currentPath,files,grid.currentStore,p,o.Refresh,grid.id,o);
          }
        },{
          text:DLG.t('But_Cancel'),
          handler:function(){
            o.CopyWin.close()
          }          
        }] 
      });       
    }
    this.CopyWin.show();
  }
  
  this.DeleteFiles=function(grid){
    if(!DLG.c('IO_DelConfirm',[grid.getSelectionModel().getCount()])) return false;
    var f=new Array();
    if(grid==null) grid=this.CurrentGrid;
    var g=grid.getSelectionModel().selections;//.getSelected();
    g.each(function(o) {
      f[f.length]=o.data.f.replace('|*e*|','.');
    });
    IO.Delete(grid.currentStore,grid.currentPath,f,this.Refresh,grid.id,this);
  }
  
  this.GoToDir=function(dir,grid,ref) {
    //this.ResizeGrid();
    grid.getStore().removeAll();
    var o=[[],[]];
    if(ref==null) ref=false;
    for(var i=0;i<STORE_LOCATIONS[grid.currentStore][3].length;i++) {
      if(dir==STORE_LOCATIONS[grid.currentStore][4][i]) {
        if(!ref) {
          grid.getStore().loadData(grid.parent.addmask(STORE_LOCATIONS[grid.currentStore][3][i]));
          grid.setTitle(STORE_LOCATIONS[grid.currentStore][4][i].replace('./','/'));
          grid.currentPath=STORE_LOCATIONS[grid.currentStore][4][i];
          return;
        }
      } else if(ref) {
        o[0][o[0].length]=STORE_LOCATIONS[grid.currentStore][3][i];
        o[1][o[1].length]=STORE_LOCATIONS[grid.currentStore][4][i];
      }
    }
    if(ref) {
      STORE_LOCATIONS[grid.currentStore][3]=o[0];
      STORE_LOCATIONS[grid.currentStore][4]=o[1];
    } 
    i=STORE_LOCATIONS[grid.currentStore][0];  
    IO.sendScriptQuery(i+(i.indexOf('?')==-1? '?':'&')+'store='+grid.currentStore+'&grid='+grid.id+'&dir='+dir);
  }
  
  this.GoBack=function(grid) {
    var s=grid.currentPath;
    s=s.split('/');
    if(s.length>2) {
      s.length-=2;
      s=s.join('/')+'/';                 
    } else {
      s=s.join('/');
    }
    this.GoToDir(s,grid);
  }
  
  this.Refresh=function(grid,o) {
    if(o==null) this.GoToDir(grid.currentPath,grid,true);
    else  o.GoToDir(grid.currentPath,grid,true);
  }

  this.Rename=function(grid) {
    var gs=grid.getSelectionModel().selections;
    if(gs.items.length==1) {
      var f=gs.items[0].data.f.replace('|*e*|','.');
      var s=prompt(DLG.t('IO_RnmPrompt'),f);
      if(s!=null && s!=f && s!='') {
        var i=STORE_LOCATIONS[grid.currentStore][0];
        IO.sendScriptQuery(i+(i.indexOf('?')==-1? '?':'&')+'store='+grid.currentStore+'&grid='+grid.id+'&dir='+grid.currentPath+'&rename='+f+'&to='+s);
      }
    }
  }
  this.PreviewTimeout=null;
  this.ChSelRows=function(sm) {
    if(this.filename!=null) {
      var f=[];
      var s;
      for(var i=0;i<sm.selections.length;i++) {
        if(sm.selections.items[i].data.t!='dir') {
          s=sm.selections.items[i].data.f.split('|*e*|');
          if(s.length==2 && s[1]!='') s=s[0]+'.'+s[1];
          else s=s[0]; 
          if(s!='') f[f.length]=s;
        }
      }
      if(f.length!=0) {
        this.FileName=f.join(',');
        this.filename.setValue(this.FileName);
        this.FileName=sm.grid.currentStore+':'+sm.grid.currentPath+this.FileName;
      }
      if(this.conf.rpanelmode=='preview' && sm.selections.length==1) {
        var o=this;
        if(sm.selections.items[0].data.t!='dir') {
          if(this.PreviewTimeout!=null) window.clearTimeout(this.PreviewTimeout);
          this.PreviewTimeout=window.setTimeout(function() {
            IO.sendScriptQuery(STORE_LOCATIONS[sm.grid.currentStore][0]+'?preview='+sm.grid.currentPath+sm.selections.items[0].data.f.replace('|*e*|','.')+'&size='+(o.rpanel.getInnerWidth()-30)+'?x&ret=IO.ShowPreview&oid='+o.id+'PP');
            o.PreviewTimeout=null; 
          },1000);
        }
      }
    }      
  }

  this.dg_conf=function(g) {
    var o=this;
    return {
      id:o.id+g,
      title:DLG.t('IO_Load'),
      listeners:{
          keydown:function(e) {
            try {document.all();}
			catch(e) {return false;}

			if(event.keyCode==46) {
              o.DeleteFiles(this);
              e.stopEvent();
              return;
            }
            if(event.keyCode==118) {
              o.AddFolder(this);
              e.stopEvent();
              return;
            }
            if(e.ctrlKey && e.getKey()==82) {
              o.Refresh(this);
              e.stopEvent();
              return;
            }
            
            if(event.keyCode==113) {
            // Переименовка файла (F2)
              o.Rename(this);
              event.returnValue = false; 
              event.keyCode = 0; 
              e.stopEvent();
              return;
            }
            if(event.keyCode==116) {
            // Копирование  по Ф5
              if(!o.conf.copy) return;
              var gr;
              if(this==o.gr_l) gr=o.gr_r;
              else gr=o.gr_l;        
              var data={};
              data.selections=this.getSelectionModel().selections.items;
              data.grid=this;       
              o.Copy(data,gr);              
              event.returnValue = false; 
              event.keyCode = 0; 
              e.stopEvent();
            }
			
          },
          rowdblclick :function(g,i,e) {
            var ob=this.getSelectionModel().getSelected();
      //alert(ob.id);      
            if(ob.data!=null && ob.data.f!=null) {
              var fn=ob.data.f.replace('|*e*|','.');        
              if(ob.data.e=='\t[DIR]') {
                var s=this.currentPath;
                if(fn=='[..]') {
                  o.GoBack(this);
                } else {
                  s=s+fn+'/';
                  o.GoToDir(s,this);
                }              
              } else {
                o.ExeFile(this.currentPath+ob.data.f.replace('|*e*|','.'),this);
              }
            }
          },
          click:function(e) {
            var gd;
            if(g=='_l') { 
              gd=o.gr_r;
              o.CurrentGrid=o.gr_l;
            } else {
              gd=o.gr_l;
              o.CurrentGrid=o.gr_r;
            }  
            gd=gd.getSelectionModel();
            if(gd.getCount()==1) gd.clearSelections();  
             
          }
      },        
      ds: o.setStore(o.Url,o.Mode),
      cm: o.setCm(),  
      viewConfig: {
        forceFit:true
      },
      sm: new Ext.grid.RowSelectionModel({
        singleSelect:o.conf.singleSelect,
        listeners:{
          selectionchange:function(sm) {
            o.ChSelRows(sm);
          }
        }
      }),
      enableColLock: false,
      loadMask: true,
      //autoHeight:true, 
      width:400,
      height:380,
      frame:false,
      border:false,
      bodyBorder:false,
      hideBorders:true,
      AutoScroll:true,
      trackMouseOver: true,
      enableColumnMove: true,
      enableDragDrop:true,
      ddGroup: 'desktop'
    }
  }
  
  this.SaveStore=function() {
    var s=new Array();
    var s1=new Array();
    for(var i=0;i<STORE_LOCATIONS.length;i++) {
      s1[s1.length]=[STORE_LOCATIONS[i][0],STORE_LOCATIONS[i][1],STORE_LOCATIONS[i][2]];
      s[s.length]="['"+STORE_LOCATIONS[i][0]+"','"+STORE_LOCATIONS[i][1]+"',"+STORE_LOCATIONS[i][2]+"]";
    }
    try {
      AJAX.post('ajaxfunc',{},{stores:'['+s.join(',')+']'});
    } catch(e) {}
    s1=s1.concat([['NEW','Edit stories...','']]);
    
    this.gr_l.StoreStore.loadData(s1);
    if(this.conf.rpanelmode=='list') this.gr_r.StoreStore.loadData(s1);
  }
  
  // Редактирование списка хранилищ STORIES
  this.AddStore=function() {
    var o=this;
    var pnl=new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'background:none;padding:10px 10px 0',
        width:313,
        height:160,
        items:[{
            xtype:'textfield',
            fieldLabel : DLG.t('IO_ChStor_Name'),
            value : '',
            name: 'name',
            width:295,
            disabled:true,
            allowBlank:true
          },{
            xtype:'textfield',
            fieldLabel : DLG.t('IO_ChStor_Path'),

            value : '',
            name: 'path',
            width:295,
            disabled:true,
            allowBlank:true
          },{
            xtype:'checkbox',
            boxLabel : DLG.t('IO_ChStor_Passv'), 
            value : '1',
            hideLabel: true,
            disabled:true,
            name: 'passive'
          }]         
    });
    
    var comb=new Ext.form.ComboBox({
        store: new Ext.data.SimpleStore({
          fields: ['srl', 'sname', 'mode'],
          data : STORE_LOCATIONS
        }),
        listeners: {
          select : function(c,r,n) {
            comb.CurNum=n;
            w.buttons[1].enable();
            w.buttons[2].enable();
            pnl.items.items[0].enable();
            pnl.items.items[0].setValue(r.data.sname);
            pnl.items.items[1].enable();
            pnl.items.items[1].setValue(r.data.srl);
            pnl.items.items[2].enable(); 
            pnl.items.items[2].setValue(r.data.mode);
          }           
        },
        displayField:'sname',
        typeAhead: true,
        mode: 'local',
        width:315,
        triggerAction: 'all',
        emptyText:DLG.t('IO_SelStore'),
        selectOnFocus:true
    });
    comb.CurNum=-1;
    
    var w=new Ext.Window({
      title:DLG.t('IO_EdtStoreList'),
      bodyStyle:'padding:10px 10px 0',
      width:350,
      height:300,
      minimizable:false,
      maximizable:false,
      plain: true,
      resizable:false,
      closeAction:'close',
      buttons:[{
        text:DLG.t('IO_AddStore'),
        handler:function() {
          comb.CurNum=-1;
          w.buttons[1].enable();
          w.buttons[2].disable();
          pnl.items.items[0].enable();
          pnl.items.items[0].setValue('');
          pnl.items.items[1].enable();
          pnl.items.items[1].setValue('');
          pnl.items.items[2].enable(); 
          pnl.items.items[2].setValue(false);
          comb.setValue('');
        }
      },{
        text:DLG.t('IO_Save'),  
        disabled:true,
        handler:function() {
          var i=comb.CurNum;
          if(i==-1) i=STORE_LOCATIONS.length;
          comb.CurNum=i;          
          STORE_LOCATIONS[i]=new Array(
              pnl.items.items[1].getValue(),
              pnl.items.items[0].getValue(),
              pnl.items.items[2].getValue()
          );
          comb.store.loadData(STORE_LOCATIONS);
          o.SaveStore();
        }
      },{
        text:DLG.t('IO_Delete'), 
        disabled:true,
        handler:function() { 
          var i=comb.CurNum;
          if(!DLG.c('IO_DelStoreConfirm',[STORE_LOCATIONS[i][1]])) return;

		  if(i!=-1) {
            var ar=new Array();
            for(var j=0;j<STORE_LOCATIONS.length;j++) {
              if(j!=i) ar[ar.length]=STORE_LOCATIONS[j];
            }
            STORE_LOCATIONS=ar;
            comb.store.loadData(STORE_LOCATIONS);
            comb.CurNum=-1;
            w.buttons[1].disable();
            w.buttons[2].disable();
            pnl.items.items[0].disable();
            pnl.items.items[0].setValue('');
            pnl.items.items[1].disable();
            pnl.items.items[1].setValue('');
            pnl.items.items[2].disable(); 
            pnl.items.items[2].setValue(false);
            comb.setValue('');
            o.SaveStore();
          }
        }
      },{
        text:DLG.t('IO_Close'),
			
        handler:function() {
          w.close();
        }
      }],
      items:[
        comb,
        {html:'<br><br>',bodyStyle:'border:none;background:none;'},
        pnl
      ]
    });
    w.show();
  }
  // К STORIES edit
  
  this.AddFile=function(g) {
    var o=this;
    var w=new Ext.Window({
          title:DLG.t('IO_AddFile'),  
          bodyStyle:'padding:10px 10px 0',
          items: [{
            html: '<iframe src="'+STORE_LOCATIONS[g.currentStore][0]+(STORE_LOCATIONS[g.currentStore][0].indexOf('?')==-1? '?':'&')+'addfile='+g.currentPath+'" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>',
            bodyStyle: 'padding-bottom:10px;border:none;background:none;'
          }],
          width:300,
          height:130,
          minimizable:false,
          maximizable:false,
          plain: true,
          resizable:false,
          closeAction:'close',
          buttons:[{
            text:DLG.t('IO_Close'), 
            handler:function() {
              o.Refresh(g,o);
              w.close();
            }
          }]
    });
    w.show();
    
  }
  
  this.ChangeStore=function(n,grid) {
    grid.currentStore=n;
    grid.getStore().removeAll();
    if(STORE_LOCATIONS[n][3]==null) {
      var url=STORE_LOCATIONS[n][0];      
      IO.sendScriptQuery(url+(url.indexOf('?')==-1? '?':'&')+'store='+n+'&grid='+grid.id);
    } else {
      var i=STORE_LOCATIONS[n][3].length-1;
      grid.getStore().loadData(grid.parent.addmask(STORE_LOCATIONS[n][3][i]));
      grid.setTitle(STORE_LOCATIONS[n][4][i]);
      grid.currentPath=STORE_LOCATIONS[n][4][i];
    }
    //this.ResizeGrid();
  }
  
  this.AddFolder=function(grid) {
    var s=DLG.p('IO_NewDir','');
	 
    if(s!=null && s!='') {
      var url=STORE_LOCATIONS[grid.currentStore][0];
      url=url+(url.indexOf('?')==-1? '?':'&')+'store='+grid.currentStore+'&newdir='+s+'&dir='+grid.currentPath+'&grid='+grid.id
      //alert(url);
      IO.sendScriptQuery(url);
    }
  }
  
  this.MkTbar=function(grid) {
    var o=this; 
    var str=STORE_LOCATIONS.concat([['NEW',DLG.t('IO_EditStor'),'']]);

	
    grid.StoreStore=new Ext.data.SimpleStore({          
          fields: ['srl', 'sname', 'mode'],
          data : str
    });
    
    var btn=[];
    
    if(o.conf.stores) btn=btn.concat([new Ext.form.ComboBox({
        store:grid.StoreStore ,
        listeners: {
          select : function(c,r,n) {
            if(r.data.srl=='NEW') o.AddStore();
            else o.ChangeStore(n,grid);
          }           
        },
        displayField:'sname',
        typeAhead: true,
        mode: 'local',
        width:100,
        pgrid:grid,
        triggerAction: 'all',
        emptyText:str[0][1],
        selectOnFocus:true
    }),'-']);
    btn=btn.concat([{
      tooltip:DLG.t('IO_TB_Back'),		  
      iconCls:'top-ext',
      handler:function() {
        o.GoBack(grid);
      }
    },{

      tooltip:DLG.t('IO_TB_Refresh'),
      iconCls:'top-refr',
      handler:function() {
        o.Refresh(grid);
      }
    },{
      tooltip:DLG.t('IO_AddFile'),		  
      iconCls:'fmng-add',
      handler:function() {
        o.AddFile(grid);
      }
    },{
      tooltip:DLG.t('IO_TB_Del'),
      iconCls:'fmng-delete',
      handler:function() {
        o.DeleteFiles(grid);
      }
    },{
      tooltip:DLG.t('IO_TB_Newdir'),	
      iconCls:'top-addfold',
      handler:function() {
        o.AddFolder(grid);
      }
    }]);
    if(this.conf.copy) btn=btn.concat([{
      tooltip:DLG.t('IO_TB_Copy'),
      iconCls:'fmng-copy',
      handler:function() {
        var gr;
        if(grid==o.gr_l) gr=o.gr_r;
        else gr=o.gr_l;
        
        var data={};
        data.selections=grid.getSelectionModel().selections.items;
        data.grid=grid;
        
        o.Copy(data,gr);
      }
    }]);
    btn=btn.concat([{		
      tooltip:DLG.t('IO_TB_SelAll'),
      iconCls:'top-selall',
      handler:function() {
        var sm=grid.getSelectionModel();
        if(sm.getCount()<=1) sm.selectAll();
        else sm.clearSelections();
      }
    }]);
    
    
    
    return btn;
  }
  
  this.ResizeGrid=function(){//g,w,h) {    
    if(this.conf.rpanelmode=='hide') {
      this.lpanel.setWidth(this.Window.getInnerWidth());
      this.gr_l.setWidth(this.lpanel.getInnerWidth()-2);
      this.gr_l.setHeight(this.lpanel.getInnerHeight()-2);
    } else {
      this.gr_l.setWidth(this.lpanel.getInnerWidth()-2);
      this.gr_r.setWidth(this.rpanel.getInnerWidth()-2);
      this.gr_l.setHeight(this.lpanel.getInnerHeight()-2);
      this.gr_r.setHeight(this.rpanel.getInnerHeight()-2);
    }
  }
  
  this.ddcong=function(grid) {
      var o=this;
      return {
        ddGroup : 'desktop',
        copy:true,
        notifyDrop : function(dd, e, data){           
          o.Copy(data,grid);          
        }      
      }
  }
  

  
  this.MkWin=function() {
    
    var xg=Ext.grid;
    var o=this;
 
    this.gr_l = new xg.GridPanel(o.dg_conf('_l'));
    this.gr_r = new xg.GridPanel(o.dg_conf('_r'));
    
    this.gr_l.parent=o;
    this.gr_r.parent=o;
    
    if(STORE_LOCATIONS.length>0) {
      this.ChangeStore(0,this.gr_l);
      this.ChangeStore(0,this.gr_r);
    }
    
    var split=true;
    
    // Режим отображения правой панели
    if(this.conf.rpanelmode=='hide') {
      split=false;
    } else
    if(this.conf.rpanelmode=='preview') {
    // режим предпросмотра
      var tbar=null;
      var items=[{id:o.id+'PP',bodyStyle: 'padding:10px;border:none;background:none;',html:''}];
    } else {
    // режим файлменеджера
      var tbar=this.MkTbar(this.gr_r);
      var items=[this.gr_r];
    }  
    this.rpanel=new Ext.Panel({
      //id:'fmRightPanel',
      region:'center',
      tbar:tbar,
      items:items,
      bodyStyle: 'background:none;',
      listeners: {
        resize : function(Comp, adjWidth, adjHeight, rawWidth, rawHeight) {
          if(adjWidth!=null && adjHeight!=null) o.ResizeGrid()
        }
      }
    });
    
    var lp={
      region:'west',       
      split:split,
      tbar:this.MkTbar(this.gr_l),
      items:[this.gr_l],
      listeners: {
        resize : function(Comp, adjWidth, adjHeight, rawWidth, rawHeight) {
          if(adjWidth!=null && adjHeight!=null) o.ResizeGrid()
        }
      }
    }
    
    if(split) {
      lp.width=300;
      lp.minSize=100;
      lp.maxSize=550;
    }
    
    this.lpanel=new Ext.Panel(lp);
    
    var bbar=null;
    if(this.conf.filestring) {
      this.filename=new Ext.form.TextField({
        emptyText:'File name',
        width:300,
        value:'',
        listeners:{
          change : function(fld, newValue, oldValue) {
            o.FileName=o.gr_l.currentStore+':'+o.gr_l.currentPath+newValue;
          }
        }
      });
      bbar=[
        this.filename,' ',
        new Ext.form.ComboBox({
        store:new Ext.data.SimpleStore({          
            fields: ['mask', 'mname'],
            data : o.conf.mask
          }) ,
          listeners: {
            select : function(c,r,n) {
              o.currmask=r.data.mask;
              o.GoToDir(o.gr_l.currentPath,o.gr_l);
            }           
          },
          displayField:'mname',
          typeAhead: true,
          mode: 'local',
          width:200,
          readOnly:true,
          triggerAction: 'all',
          value:o.conf.mask[0][1],
          selectOnFocus:true
      })];
    }
        
    this.Window=new Ext.Window({
      title:o.conf.title,
      shadow:false,
      layout:'border',
      items: [this.lpanel,this.rpanel],
      id:'iFileManager'+o.id,
      width:o.conf.width,
      height:o.conf.height,
      minimizable:o.conf.minimizable,
      maximizable:o.conf.maximizable,
      plain: false,
      resizable:o.conf.resizable,
      allowDomMove:false,
      modal:o.conf.modal,
      bufferResize:300,
      bodyBorder:false,
      bbar:bbar,
      listeners:{
        minimize:function(w){w.hide();},
        close:function(w) {
          if(w.tbbut!=null) w.tbbut.destroy();
          o=null;
        } 
      },
      buttons:o.conf.buttons
    });
    return this.Window;
  }
}  