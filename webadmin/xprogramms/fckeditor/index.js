function FCKEditorAPI() {
  
  this.OE=new Array();
  
  this.FindWinByElm=function(elm) {
    for(var i=0;i<this.OE.length;i++) {
      if(this.OE[i].elm==elm) return i;
    }
    return -1;
  }
  
  this.FindWinByWid=function(wid) {
    for(var i=0;i<this.OE.length;i++) {
      if(this.OE[i].wid==wid) return i;
    }
    return -1;
  }
  
  this.delOE=function(i) {
    var o=new Array();
    for(var j=0;j<this.OE.length;j++) {
      if(i!=j) o[o.length]=this.OE[j];
    }
    this.OE=o;
  }
  
  this.GetTextFromHtml=function(s,l) {
    var o='';
    var i=0;
    var log=true;
    while(i<s.length && l>0) {
      if(s.charAt(i)=='<') log=false;
      else if(s.charAt(i)=='>' && !log) log=true;
      else if(log) {
        o+=s.charAt(i);
        l--;
      }
      i++;
    }
    return o;
  }
  
  this.SetTitle=function(txt) {
    var title=this.GetTextFromHtml(txt,8);
    if(title=='') title=DLG.t('EmptyText');
    else title+='...';
    return title+' - FCK';
  }
  
  this.EditText=function(elm) {
    var i=this.FindWinByElm(elm);
    var txt=elm.value;
  
    var title=this.SetTitle(txt);
    
    if((i==-1) || !App.ShowByWid(this.OE[i].wid)) {
      if(i!=-1) this.delOE(i);
      var wid=App.Counter;
      App.run('xprogramms/fckeditor/fckeditor.php?wid='+wid,title,wid,700,400);
      this.OE[this.OE.length]={elm:elm,wid:wid,text:txt,win:null};
      
    } 
    
    if(i!=-1 && this.OE[i].win!=null) {
      this.OE[i].win.UpdateHTML(txt);
    }
  }
  
  this.save=function(wid,txt) {
    var i=this.FindWinByWid(wid);
    if(i!=-1) {
      var t=this.SetTitle(txt);
      App.settitle(wid,t);
      
      if(this.OE[i].elm.tbname!=null) {
        //AJAX.post('/webadmin/ajaxfunc',{chrow:'yes'},{tab:this.OE[i].elm.tbname,fold:this.OE[i].elm.name,id:this.OE[i].elm.tbid,data:rusescape(txt)});
        var o=this;
        Ext.Ajax.request({
        url: './ajaxfunc.php?chrow=yes',
        success: function(response) {
          if(response.responseText!="OK") return false; 
          else return true;          
        },
        params: {
          tab:o.OE[i].elm.tbname,
          fold:o.OE[i].elm.name,
          id:o.OE[i].elm.tbid,
          data:rusescape(txt)
        }
      });
      
      
      }      
      try {
       this.OE[i].elm.value=txt;
      } catch(e){} 
    }
  }
  
  this.GetText=function(wid,win) {
    var i=this.FindWinByWid(wid);
    if(i==-1) return '';
    this.OE[i].win=win;
    return this.OE[i].text;
  }
  
  this.OpenFile=function(fn) {
    var wid=App.Counter;
    var s='';
    var i=fn.length;
    while(i>0 && fn.charAt(i)!='/') i--;
    s=fn.substr(i+1);
    App.run('xprogramms/fckeditor/fckeditor.php?file='+fn,s,wid,700,400);
  }
}

FCK=new FCKEditorAPI();

// Добавляем обработчик для форм
FACT.RunMethods[FACT.RunMethods.length]="FindEditors";   
FACT.FindEditors=function() {
 
 
  if(FACT.Win.FORMFOLDS!=null) {
    for(var i=0;i<FACT.Win.FORMFOLDS.length;i++){
      var f=FACT.Win.FORMFOLDS[i];       
    
      /*var b=new FACT.Win.Ext.Toolbar.Button({
        handler: function() {
          FCK.EditText(f);
        }, 
        iconCls:'editor',
        tooltip:DLG.t('EditExt')
      }); 
      
      f.on('render',function(el) {
        el.getToolbar().addButton({
          handler: function() {FCK.EditText(f);}, 
          iconCls:'editor',
          tooltip:DLG.t('EditExt')
        });
      })*/
      
      /*var x=FACT.Win.GetTabId();
      if(x.length==2) {
        f.tbname=x[0];
        f.tbid=x[1];
      } else {
        f.tbname=null;
        f.tbid=null;
      } 
      FACT.Win.FORMFOLDS[i].getToolbar().addButton(b);*/
      
        
    }
  }
}

// переопределяем метод (делаем, fck - редактором по-умолчанию)
EDITOR.editfolder=function(table,folder,id,title) {
  /*if(!LOCKOBJ.add(table,id)) {
    alert('Редактирование не возможно!\nЕлемент занят другим пользователем или приложением.');
    return;
  } */
  App.run('xprogramms/fckeditor/fckeditor.php?editfolder='+folder+'&t='+table+'&id='+id,title+' - FCK',App.Counter++,700,400);
}  

// Добавляем обработчик ДРАГ-н-ДРОП
App.DDmethods.fckeditor=function(obj) {
  
 
  if(obj.grid!=null) {
//print_r(obj.selections);
    var url;
    for(var i=0;i<obj.selections.length;i++) {      
      url=STORY_LOCATIONS[obj.grid.currentStore][0];
      url=url+(url.indexOf('?')==-1? '?':'&')+'gethtml='+obj.grid.currentPath+obj.selections[i].data.f.replace('|*e*|','.')+'&handler='+HostName+'./getfiles.php&ret=FCK.OpenFile';
      AJAX.sendScriptQuery(url);
      //prompt('',url);
    }    
  } else if(obj.node!=null) {

    // Если дропнули страницу из дерева сайта, нужно открыть текстовые блоки
    if(obj.node.ownerTree.id=='StructTree') {
      var s=AJAX.get('xprogramms/fckeditor/gettreetext',{id:obj.node.id});
      if(s!='') {
        s=s.split('|');
        for(var i=0;i<s.length;i++) {
          App.run('xprogramms/fckeditor/fckeditor.php?editfolder=text&t=content&id='+s[i],obj.node.text+' - FCK',App.Counter,700,400);
        }
      } 
    } 
  } 
} 