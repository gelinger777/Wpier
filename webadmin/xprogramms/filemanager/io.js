AssociateFileTypes={
   jpeg:'jpg-ext',
   jpg:'jpg-ext',
   png:'jpg-ext',
   gif:'gif-ext',
   doc:'doc-ext',
   txt:'txt-ext' 
}

function iIO(){
  this.handlers=[];
  this._SID='';
  this.sendScriptQuery=function(url,time) {
    var o = new Date();

    url+=(url.indexOf('?')==-1? '?':'&')+'_dc='+o.getHours()+o.getMinutes()+o.getSeconds()+o.getMilliseconds()+'&_sid='+this._SID; 

   
    o=document.body.insertBefore(document.createElement("script"), document.body.firstChild);
    o.setAttribute("language", "Javascript");
    o.setAttribute("type", "text/javascript");
    o.setAttribute("src", url);	
    if(time==null) time=10000; 	
    setTimeout(function(){document.body.removeChild(o);}, time);
  }
  
  this.store=function(store,grid,path,data) {
    var g=Ext.getCmp(grid);
    var ldat=new Array();
    if(STORE_LOCATIONS[store][3]==null) {
      STORE_LOCATIONS[store][3]=[];
      STORE_LOCATIONS[store][4]=[];
    }
    var i=STORE_LOCATIONS[store][3].length;    
    var n;
    var ex;
    for(var j=0;j<data.length;j++) {
      n=data[j][0].length;
      ex='';
      
      if(data[j][2]=='1') ex='\t[DIR]';
      else {
        while(n>0 && data[j][0].charAt(n)!='.') n--;
   
        if(n>0) {
          ex=data[j][0].substr(n+1);
          data[j][0]=data[j][0].substr(0,n)+'|*e*|'+ex;
        } else data[j][0]+='|*e*|';
        if(ex=='') ex='-'; 
      }
      
      ldat[j]=[data[j][0],FormatDates(data[j][1]),ex,data[j][3],(data[j][2]=='1'? 'dir':'')];
      if(j==0) ldat[j][5]=data[j][4];
    }
    STORE_LOCATIONS[store][3][i]=ldat;
    STORE_LOCATIONS[store][4][i]=path;
    g.getStore().loadData(g.parent.addmask(ldat));
    g.setTitle(path.replace('./','/'));
    g.currentPath=path;
    g.currentStore=store;
  }
  
  this.cnf_progress_win=function(title,text) {
    var d = new Date();
    var pbr=new Ext.ProgressBar({
          text:'',
          cls:'left-align'
    });
    return [pbr,{
        id:'idlg-win'+d.getHours()+d.getMinutes()+d.getSeconds()+d.getMilliseconds(),
        title:title,
        bodyStyle:'padding:10px 10px 0',
        items: [{
          html: text,
          bodyStyle: 'padding-bottom:10px;border:none;background:none;'
        },pbr],
        width:400,
        height:150,
        minimizable:false,
        maximizable:false,
        plain: true,
        resizable:false,
        closeAction:'close'
     }];   
  }
  
  this.Delete=function(store,path,files,met,oid,pw) {
    var w=IO.cnf_progress_win(DLG.t('IO_Delete'),DLG.t('IO_DeleteText',[files.length,STORE_LOCATIONS[store][1]]));

    var pbr=w[0];
    w=w[1];    
    w.buttons=[{
      text:DLG.t('IO_Stop'),
      handler:function(){
        w.close()
      }          
    }];    
 
    w.listeners={
      close:function(w) {
        w.destroy();
        if(met!=null) {
          met(Ext.getCmp(oid),pw);
        }
      }
    }
    
    w=new Ext.Window(w);
    
    w.CurrentFile=0;
    w.DelNext=function() {
      if(w.CurrentFile>=files.length) {
        w.close();
        return;
      }
      
      pbr.updateProgress(w.CurrentFile/files.length, files[w.CurrentFile]+DLG.t('IO_Progress',[(files.length-w.CurrentFile)]));
      
      var url=STORE_LOCATIONS[store][0]+(STORE_LOCATIONS[store][0].indexOf('?')==-1? '?':'&')+'delete='+path+files[w.CurrentFile]+'&ret=IO.DeleteNext&oid='+w.id;
      IO.sendScriptQuery(url);
      w.CurrentFile++;
    }
    
    w.show();
    w.DelNext();
  }
  
  this.DeleteNext=function(oid) {
    Ext.getCmp(oid).DelNext();
  }
  
  // Функция вызовется STORE после проверки файлов
  this.CopyCheckStep=function(oid,cod) {
    var w=Ext.getCmp(oid);
    cod=cod.split('|');
    if(cod[0]==0) { // Если ОК, продолжаем копирование
      w.CopyNext(); 
      return;
    }
    if(cod[0]=='1') { // Файл уже существует
      if(w.OwerWriteAll==null && w.SkipAll==null) { 
      // Если не включен режим тотальной замены или пропуска, нужно спросить разрешения
        var dw=new Ext.Window({
          title:DLG.t('IO_Copy'),
          bodyStyle:'padding:10px 10px 0',
          items: [{
            html: DLG.t('IO_Replace',[STORE_LOCATIONS[w.DistStore][1],cod[1]]),
            bodyStyle: 'padding-bottom:10px;border:none;background:none;'
          }],
          width:430,
          height:130,
          minimizable:false,
          maximizable:false,
          plain: true,
          resizable:false,
          closeAction:'close',
          buttons:[{
            text:DLG.t('IO_Yes'),
            handler:function(){
              w.CopyNext();
              dw.close();
            }            
          },{
            text:DLG.t('IO_YesToAll'),
            handler:function(){
              w.OwerWriteAll=true;
              w.CopyNext();
              dw.close();
            }
          },{
            text:DLG.t('IO_Skip'),
            handler:function(){
              w.CurrentFile++;
              w.CheckNext();
              dw.close();
            }
          },{
            text:DLG.t('IO_SkipAll'),
            handler:function(){
              w.SkipAll=true;
              w.CurrentFile++;
              w.CheckNext();
              dw.close();
            }
          },{
            text:DLG.t('IO_Cancel'),
            handler:function(){
              dw.close();
              w.close();
            }
          }]
        });
        dw.show();
      } else {
      // В противном случе копируем поверх
        if(w.OwerWriteAll!=null) w.CopyNext();
        else {
          w.CurrentFile++;
          w.CheckNext();
        }        
      }
      return;
    }
    if(cod[0]==2) { // Говорим, что указанной директории не существует
    
    }
  }
  // Функция вызывается сервером после завершения первого этапа копирования (файл на сервере)
  
  this.CopySecondStep=function(oid,file) {
    Ext.getCmp(oid).CopySecond(file);
  }  
  // На втором шаге файл с сервера нужно забросить в конечный STORE
  this.CopyNextStep=function(oid) {
    Ext.getCmp(oid).CheckNext();     
  }
  
  // Копирование файлов из одного STORE в другой
  // src_store (int) - STORE источника, src_files (Array) - список файлов источника
  // dist_store (int) - STORE приемника , dist_files (str) - путь куда копировать
  // met - метод, который нужно вызвать после копирования
  // oid - объект, цель метода
  // pw - объект, владелец метода
  
  this.Copy=function(src_store,src_path,src_files,dist_store,dist_files,met,oid,pw) {  
    var src=src_path.split('/');
    if(src[src.length-1]!='') src.length--; 
    src[0]='';
    src=src.join('/');
    var dst=dist_files.split('/');
    if(dst[dst.length-1]!='') dst.length--; 
    dst[0]='';
    dst=dst.join('/');
    
    var w=IO.cnf_progress_win(DLG.t('IO_Copy'),'<b>'+STORE_LOCATIONS[src_store][1]+'</b>'+':'+src+'=>'+'<b>'+STORE_LOCATIONS[dist_store][1]+'</b>'+':'+dst);
    
    var pbr=w[0];
    w=w[1];
       
    w.listeners={
      close:function(w) {
        w.destroy();
        if(met!=null) {
          met(Ext.getCmp(oid),pw);
        }
      }
    }
    
    w.buttons=[{
      text:DLG.t('IO_Stop'),
      handler:function(){
        w.close()
      }          
    }];
    
    w.CurrentFile=0;
    w.CheckNext=function() {
      //  На первом шаге проверим возможность копирования 
      if(this.CurrentFile>=src_files.length) {
         this.close();
         return;
      } 
      pbr.updateProgress(this.CurrentFile/src_files.length, src_files[this.CurrentFile]+DLG.t('IO_Progress',[(src_files.length-this.CurrentFile)]));
      
      url=STORE_LOCATIONS[dist_store][0]+(STORE_LOCATIONS[dist_store][0].indexOf('?')==-1? '?':'&')+'checkcopy='+dist_files+'&file='+src_files[this.CurrentFile];  
      url+="&ret=IO.CopyCheckStep&oid="+this.id;
      
      //prompt('',url);
      IO.sendScriptQuery(url);    
    }
    
    w.CopyNext=function() {
      // На втором шаге очередной файл оказывается на центральном сервере CMS (разные STORE)
      // или копируется в рамках одного STORE  
          var url=src_path+src_files[this.CurrentFile];
          if(src_store==dist_store) { 
          // Если файлы на одном STORE, шлем команду копировть и переходим к следующему файлу
            url=STORE_LOCATIONS[src_store][0]+(STORE_LOCATIONS[src_store][0].indexOf('?')==-1? '?':'&')+'copy='+src_files[this.CurrentFile]+'&path='+src_path+'&to='+dist_files;
            url+="&ret=IO.CopyNextStep&oid="+this.id;
            this.CurrentFile++;
          } else {
          // Если на разных, то копирование происходит через сервер с CMS
            if(STORE_LOCATIONS[src_store][2]) { // режим пассивный, качает сервер
              url=STORE_GATE+'&loadfrom='+STORE_LOCATIONS[src_store][0].replace('?','&')+'&file='+url;
            } else { // режим активный файлы на сервер заливает клиент
              url=STORE_LOCATIONS[src_store][0]+(STORE_LOCATIONS[src_store][0].indexOf('?')==-1? '?':'&')+'uploadto='+STORE_GATE.replace('?','&')+'&file='+url;
            }          
            url+='&ret=IO.CopySecondStep&oid='+this.id;
          }
          
          IO.sendScriptQuery(url);

    }
    
    w.CopySecond=function(file) {
        // На последнем шаге файл с сервера переносится на конечный STORE  
          var url=dist_files+'&fn='+src_files[this.CurrentFile]+'&tmp='+file;
          if(STORE_LOCATIONS[dist_store][2]) { // режим пассивный, аплоад сервера
            url=STORE_GATE+'&uploadto='+STORE_LOCATIONS[dist_store][0].replace('?','&')+'&file='+url;
          } else { // режим активный файлы с сервера забирает клиент
            url=STORE_LOCATIONS[dist_store][0]+(STORE_LOCATIONS[dist_store][0].indexOf('?')==-1? '?':'&')+'loadfrom='+STORE_GATE.replace('?','&')+'&file='+url;
          }            
          url+="&ret=IO.CopyNextStep&oid="+this.id;
          
          this.CurrentFile++;
          //prompt('',url);          
          IO.sendScriptQuery(url);
          
          
          //this.CopyNext();
    }
         
    w=new Ext.Window(w);
    w.DistStore=dist_store;
    w.show();
    w.CheckNext();
  }
  
  this.AddStoresComplit=false;
  
  this.AddStores=function(stores) {
    var log,j;
    this.AddStoresComplit=true;
    for(var i=0;i<stores.length;i++){
      log=true;
      for(j=0;j<STORE_LOCATIONS.length;j++) {
        if(STORE_LOCATIONS[j][0]==stores[i][0]) {
          log=false;
          break;
        }
      }
      if(log) STORE_LOCATIONS[STORE_LOCATIONS.length]=stores[i];
    }    
  }
  
  // END COPY ------------------------------------------------------------------
  
  this.RendFileType=function(x,m) {
      if(x=='[..]') {
        m.css='top-ext';
        x="\t[..]";
      } else {
        x=x.split('|*e*|');
        if(x.length==2) {
          x[1]=x[1].toLowerCase();
          try {
            eval('m.css=AssociateFileTypes.'+x[1]);
            if(m.css==null) m.css='txt-ext'; 
          } catch(e) {m.css='txt-ext';}
          x=x[0];
        } else {
          m.css='folder-ext';
          x=x[0];
        }
      }   
      m.attr='style="padding-left:18px"';
      return x;
  }
  
  this.ShowPreview=function(html,oid) {
    try {
      Ext.get(oid).dom.innerHTML=html;
    } catch(e){}
  }
  
  this.SaveFileDlg=function(title,mask,handler) {
    var W=new iCommanderClass({
      title:title,
      copy:false,
      modal:true,
      rpanelmode:'hide',
      width:520,
      height:400,
      minimizable:false,
      maximizable:false,
      resizable:false,
      filestring:true,
      handler:handler,
      singleSelect:true,
      mask:mask,
      buttons:[
      {
        text:DLG.t('IO_Save'),
        handler:function() {          
		  if(W.FileName!='') handler(W.FileName);
		  W.Refresh(W.gr_l);		  
          win.close();
        }
      },{
        text:DLG.t('IO_Cancel'),
        handler:function() {
          win.close();
        }
      }]
    });
    var win=W.MkWin();
    win.show();
    W.ResizeGrid();
    //var dd1=new Ext.dd.DropTarget(W.gr_l.container,W.ddcong(W.gr_l));
    return false;
  }

  this.OpenFileDlg=function(title,mask,handler) {
    var W=new iCommanderClass({
      title:title,
      copy:false,
      modal:true,
      rpanelmode:'preview',
      width:520,
      height:400,
      minimizable:false,
      maximizable:false,
      resizable:false,
      filestring:true,
      handler:handler,
      mask:mask,
      buttons:[{
        text:DLG.t('IO_Open'),
        handler:function() {
          if(W.FileName!='') handler(W.FileName);
          win.close();
        }
      },{
        text:DLG.t('IO_Cancel'),
        handler:function() {
          win.close();
        }
      }]
    });
    var win=W.MkWin();
    win.show();
    W.ResizeGrid();
    //var dd1=new Ext.dd.DropTarget(W.gr_l.container,W.ddcong(W.gr_l));
    return false;
  }
  
  this.RunHandler=function(n) {
    n=parseInt(n);
    if(n<this.handlers.length) {
      this.handlers[n](true);
      this.handlers[n]=null;
    }
  }
  
  this.SaveFile=function(fn,content,handler) {
    fn=fn.split(':');
    fn[0]=parseInt(fn[0]);
    if(STORE_LOCATIONS[fn[0]]!=null) {
      if(STORE_LOCATIONS[fn[0]][2]) { // Пассивный режим, постим на сторе
        var s=AJAX.post(STORE_GATE,{},{savevalue:escape(content),fn:fn[1],sendto:STORE_LOCATIONS[fn[0]][0]});
        if(s=='OK') handler(true);
        else handler(false);
      } else { // Сторе в активном режиме, отправляем команду "забрать временный файл"
         var s=AJAX.post(STORE_GATE,{},{savevalue:escape(content)});
         
         if(s!='') {
           var f=fn[1].split('/');
           s=STORE_LOCATIONS[fn[0]][0]+(STORE_LOCATIONS[fn[0]][0].indexOf('?')==-1? '?':'&')+'loadfrom='+STORE_GATE.replace('?','&')+'&tmp='+s+'&file='+fn[1]+'&fn='+f[f.length-1];     
           s+="&ret=IO.RunHandler&oid="+this.handlers.length;
           this.handlers[this.handlers.length]=handler;
           this.sendScriptQuery(s);
         } else handler(false);
      }      
    }
  }
}

GLOBAL_LIBS={};

function loadlib(lname,url) {
   var v=null;
   try {
     window.eval('v=new GLOBAL_LIBS.'+lname);
   } catch(e) {
     var s=AJAX.loadHTML(url);
     if(s!='') {
       window.eval(s,'JScript'); 
       eval('GLOBAL_LIBS.'+lname+'='+lname);
       eval('v=new GLOBAL_LIBS.'+lname);        
     }
   }
   return v;
}

AJAX=new function() {
  
 this.sendScriptQuery=function(url) {
   var o = new Date();

   url+=(url.indexOf('?')==-1? '?':'&')+'_dc='+o.getHours()+o.getMinutes()+o.getSeconds()+o.getMilliseconds(); 

   
   o=document.body.insertBefore(document.createElement("script"), document.body.firstChild);
   o.setAttribute("language", "Javascript");
   o.setAttribute("type", "text/javascript");
   o.setAttribute("src", url);		
   setTimeout(function(){document.body.removeChild(o);}, 15000);
 } 
 
 this.get=function(mod,param) {
   var x;
   var q=new Array();
   for (var prop in param) {
     eval('x=param.'+prop);
     if(x!=null) q[q.length]=prop+'='+x;
   }
 //prompt('',mod+'.php?'+q.join('&'))  
   return this.loadHTML(mod+'.php?'+q.join('&'));
 }
 
 this.post=function(mod,get,post) {
   var x;
   var q='';
   
   if(get!=null) {
     q=new Array();
     for (var prop in get) {
       eval('x=get.'+prop);
       if(x!=null) q[q.length]=prop+'='+x;
     }
     q='?'+q.join('&');
   }
   mod=mod+'.php'+q;
   
   q=new Array();
   if(post!=null) {
     for (var prop in post) {
       eval('x=post.'+prop);
       if(x!=null) q[q.length]=new Array(prop,x);
     }
   }   
   return this.loadHTML(mod,false,null,'POST',q);
 } 

 this.loadHTML=function(sURL, mod, func, method, data) {
  var s=this.loadHTML_do(sURL, mod, func, method, data);
  if(s.substr(0,11)=='TITLERESET|') {
    s=s.split('|');
    tree.getNodeById(s[1]).setText(s[2]);
    s[1]=isOpen('Page-'+s[1]);
    if(s[1]!=-1) {
      tabs.getItem(s[1]).setTitle(s[2]);
    }
    return 'OK';
  } else return s;
 }

 this.loadHTML_do=function(sURL, mod, func, method, data) {
  if(mod==null) mod=false;        
  if(method==null) method="GET"; 
     
  var request=null;
  if(!request) try {
    request=new ActiveXObject('Msxml2.XMLHTTP');
  } catch (e){}

  if(!request) try {
    request=new ActiveXObject('Microsoft.XMLHTTP');
  } catch (e){}

  if(!request) try {
    request=new XMLHttpRequest();
  } catch (e){}

  if(!request) return "";   
 
  if(method=='GET') request.open('GET', sURL, mod);
  else {    
    var param = "";
    if(data!=null) {
      for(var i=0;i<data.length;i++) {
        if(i>0) param+='&';
      
        param+=data[i][0]+'='+data[i][1];
      }
    }
    request.open("POST", sURL, false); 
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=windows-1251"); 
    request.setRequestHeader("Content-length", param.length); 
    request.setRequestHeader("Connection", "close"); 
    request.send(param);
    return request.responseText;      
  }   
  
  if(mod) {     
    request.send(null);
    request.onreadystatechange = function() {
      if (request.readyState == 4) {
        if (func!=null) {
          func(request.responseText);
        }       
      }
    }
    return 0;
  }   
  request.send(null); 
  return request.responseText;
 }
} 