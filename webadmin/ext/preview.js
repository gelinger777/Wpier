function str_replace(x,y,s) {
  var o=s.replace(x,y);
  while(o!=s) {
    s=o;
    o=s.replace(x,y);
  }
  return o;
}

function PrevObj() {

  this.Win=null;
  this.HistoryAr=[];
  this.PPanel=null;
  this.HistPos=-1;
  this.HistLoad=false;
  this.EditMode=false;
  this.ChangedElm=[];
  this.CurrentElm=null;
  this.CurrentPageCode=0;
  this.CurrentPageAttr='';
  this.EditMenu=new Ext.menu.Menu();
  this.EditingBlocks=[];
  this.TabPanel=null;
  this.CurrentImg=null;
  this.UID=GetUnicum();
  this.DependWindows=[];
  this.MenuMode='';
  this.PublicMode=false;
  this.urlString=null;

  this.GetCurEl=function() {
    return this.CurrentElm;
  }

  this.GenerateEditMenu=function(texts,moduls,pgcod,tplfile) {
    this.EditMenu.removeAll();
    this.TextBlocks=[];
    if(texts!=null && texts.length>0) {
      for(var i=0;i<texts.length;i++) {
        if(texts[i][0]=='empty') texts[i][0]=DLG.t('Empty text');
        eval("this.EditMenu.add({text:'"+(texts[i][0]=='&nbsp;'? DLG.t('Empty text'):texts[i][0])+"',handler:function() {EDITOR.editfolder('content','text',"+texts[i][1]+",'"+texts[i][0]+"');}});");
      }
      if(moduls!=null && moduls.length>0) this.EditMenu.addSeparator();
    }

    for(var i=0;i<moduls.length;i++) {
      if(moduls[i][2]) {
        var st="this.EditMenu.add({text:'"+moduls[i][0]+"',handler:function(){EDITOR.modul_edt('"+moduls[i][1]+"','"+pgcod+"');},menu:{items:[{text:'"+DLG.t('vmAdd')+"',handler:function() {EDITOR.modul_add('"+moduls[i][1]+"','"+pgcod+"');}},{text:'"+DLG.t('vmEdit')+"',handler:function() {EDITOR.modul_edt('"+moduls[i][1]+"','"+pgcod+"');}}";

        if(moduls[i][3]!='') {
          var s=moduls[i][3].split(":");
          s=parseInt(s[0]);
          if(!isNaN(s) && STORE_LOCATIONS[s]!=null) {
            st+=",'-',{text:DLG.t('Template'),handler:function(){EDITOR.tpl_edit('"+moduls[i][3]+"','"+pgcod+"');}}";
          }
        }

        st+="]}});";
        eval(st);
      } else {
        eval("this.EditMenu.add({text:'"+moduls[i][0]+"',handler:function() {EDITOR.modul_edt('"+moduls[i][1]+"','"+pgcod+"');}});");
      }
    }
    if(tplfile!='') {
      this.EditMenu.addSeparator();
      var s=tplfile.split(":");
      s=parseInt(s[0]);
      if(!isNaN(s) && STORE_LOCATIONS[s]!=null) {
        eval("this.EditMenu.add({text:DLG.t('Page template'),handler:function() {EDITOR.tpl_edit('"+tplfile+"');}})");
      }
    }
    this.Test++;
  }

  this.MkImageTbar=function(img) {
    this.CurrentImg=img;
    var t=this.TabPanel.getTopToolbar();
    var l=false;
    var lsz=false;

   this.ChangeBar('editimage');

	if(img.src.indexOf('size=')>0) lsz=true;
   	for(var i=0;i<t.items.length;i++) {
      if(t.items.items[i].el.name!=null) {
        if(t.items.items[i].el.name=='edi_width') {
          t.items.items[i].el.value=img.width;
          if(lsz) t.items.items[i].enable();
          else t.items.items[i].disable();
        } else if(t.items.items[i].el.name=='edi_height') {
          t.items.items[i].el.value=img.height;
          if(lsz) t.items.items[i].enable();
          else t.items.items[i].disable();
        } else if(t.items.items[i].el.name=='edi_vspace')
          t.items.items[i].el.value=img.style.paddingTop+','+img.style.paddingBottom;
        else if(t.items.items[i].el.name=='edi_hspace')
          t.items.items[i].el.value=img.style.paddingLeft+','+img.style.paddingRight;
      }
    }
  }

  this.MkTextTbar=function() {
	  this.ChangeBar('edittext');
  }

  this.ClickEvnt=function(elm) {
    if(this.EditMode) {
      var e=elm;
      if(e.className.indexOf(':editor')!=(e.className.length-7)) {
        elm.parentNode;
        while(e!=null && (e.className==null || (e.className.indexOf(':editor')!=(e.className.length-7)))) {
          e=e.parentNode;
        }
      }
      if(e!=null) {
        this.CurrentElm=e;
        this.ChangeBar('edittext');
        var o=this;
        window.setTimeout(function(){
          if(elm.tagName=='IMG') o.MkImageTbar(elm);
          else o.MkTextTbar();
        },100);
      } else {
        this.ChangeBar('editstring');
      }
    }
  }

  this.AddWinEvents=function(win) {
    var o=this;
    if(navigator.userAgent.indexOf('MSIE') >= 0){
      win.document.onmousedown=function() {
        o.ClickEvnt(win.event.srcElement)
      }
    }else{
      win.document.onblur=function(event) {
        event.stopPropagation();
        return false;
      };
      win.onclick=function(event) {
        o.ClickEvnt(event.target);
      }
    }
  }

  this.HistoryAdd=function(win,texts,moduls,tplfile,pgcod, rd, ad, ed, attr) {

	Ext.getCmp('pageurl'+this.UID).setValue(win.location.href);
	this.Win=win;

	this.GenerateEditMenu(texts,moduls,pgcod,tplfile);
    if(!this.HistLoad) {
      if(this.HistPos<(this.HistoryAr.length-1)) {
       this.HistoryAr=this.HistoryAr.slice(0,this.HistPos);
       this.HistPos--;
      }
      this.HistoryAr[this.HistoryAr.length]=win.location.href;
      this.HistPos++;

      this.SetNavButt();
    } else this.HistLoad=false;
    this.DsblBtbar(true);
    this.EditMode=false;
    this.ChangedElm=[];
    this.CurrentPageCode=pgcod;
    this.CurrentPageAttr=attr;

	this.PPanel.getTopToolbar().items.get('btSave').disable();
    if(ed!=null && ed==1) {
      this.PPanel.getTopToolbar().items.get('bEdit').enable();
    } else {
      this.PPanel.getTopToolbar().items.get('bEdit').disable();
    }
    var s=this.PPanel.getTopToolbar().items.get('bEdit');
    s.setIconClass('editor');
    s.setText(DLG.t("vmEdit"));

	Ext.getCmp('pageurl'+this.UID).setValue(win.location.href);

	this.AddWinEvents(this.Win);
  }

  this.SetNavButt=function() {
    var tt=this.PPanel.getTopToolbar();
    if(this.HistPos<=0) tt.items.get('bBack').disable();
    else tt.items.get('bBack').enable();
    if(this.HistPos>=(this.HistoryAr.length-1)) tt.items.get('bForw').disable();
    else tt.items.get('bForw').enable();
  }

  this.Go=function(n) {
    this.HistPos+=n;
    this.Win.location=this.HistoryAr[this.HistPos];
    this.SetNavButt();
    this.HistLoad=true;
  }

  this.Ref=function() {
    this.HistLoad=true;
    this.Win.location=this.Win.location;
  }

  this.ShowHistory=function() {
    return this.HistoryAr.join("\n");
  }

  this.DsblBtbar=function(mod) {
    if(!IsConteneditable()) return 0;
    this.ChangeBar('nav');
  }

  this.ShowPics=function(fn) {
    var html=[];
    fn=fn.split(',');
    var st=fn[0].split('/');
    fn[0]=st[st.length-1];
    st[0]=st[0].split(':');
    var dir=STORE_LOCATIONS[parseInt(st[0][0])][0]+'?size=2048?x&_sid='+IO._SID+'&getimage='+st[0][1]+'/';
    for(var i=1;i<st.length-1;i++) dir+=st[i]+'/';
    var sh=[];
    for(var i=0;i<fn.length;i++) {
      sh[i] = new Image(); sh[i].src=dir+fn[i];
      html[html.length]=dir+fn[i];
    }
    return html;
  }

  this.GetSelectedRange=function(collapse) {
    if(collapse==null) collapse=true;
    var d=null;
    if(navigator.userAgent.indexOf('MSIE') >= 0){
      this.CurrentElm.focus();
      d=this.Win.document;
      d=d.selection.createRange();
    } else {
      d=this.Win.getSelection();
      d=d.getRangeAt(0);
      //if(collapse) d.collapse();
    }
    return d;
  }

  this.InsertImage=function() {
    this.Win.focus();
    var o=this;
    var d=this.GetSelectedRange();
    IO.OpenFileDlg(DLG.t("vmImgInsertDlg"),[['*.gif,*.jpg,*.jpeg,*.png','Images (gif, jpeg, png)'],['*.*',DLG.t('vmImgAllFormats')]],function(fn){
       var s=o.ShowPics(fn);
       o.CurrentElm.focus();

       if(navigator.userAgent.indexOf('MSIE') >= 0){
          var r;
          if(r=o.Win.document.selection.createRange()) {
            r.moveToBookmark(d.getBookmark());
          }
          var st='';
          for(var i=0;i<s.length;i++) st+='<img src="'+s[i]+'" border="0" alt="" />';
          d.pasteHTML(st);
       } else {
            var im=null;
            for(var i=0;i<s.length;i++) {
              im=o.Win.document.createElement("IMG");
              im.src=s[i];
              d.insertNode(im);
            }
       }
       o.EdtOnKey(o.CurrentElm);
    });
  }

  this.CopyImages=function(dir,imgs) {
    var j;
    for(var i=0;i<imgs.length;i++) {
      for(j=0;j<STORE_LOCATIONS.length;j++) {
        if(unescape(imgs[i]).indexOf(unescape(STORE_LOCATIONS[j][0]))!=-1) {
          if(STORE_LOCATIONS[j][2]) {
          // äëÿ ïàññèâíîãî ñòîðå, íóæíî êà÷íóòü êàðòèíêó

          } else {
          // äëÿ àêòèâíîãî, øëåì êîìàíäó çàïîñòèòü ôîòêó íà SG
            var url=imgs[i].replace(/&amp;/g,'&')+'&dir='+dir+'&sendto='+STORE_GATE.replace('?','&');
//prompt('',url);
            IO.sendScriptQuery(url);
          }
          break;
        }
      }
    }
  }

  this.PrepStorePics=function(txt,dir) {
    var fn,f,c,s='';
    var i=0;
    var j=0;
    var k,n=txt.length;
    var imgs=[];
    while(i<n) {
      if(txt.substr(i,4).toLowerCase()=='<img') {
        if((i-j)>0) {
          s+=txt.substr(j,i-j);
          j=i;
        }
        while(i<n && txt.charAt(i)!=">" && txt.substr(i,4).toLowerCase()!='src=') s+=txt.charAt(i++);
       if(i<n && txt.charAt(i)!=">") {
          i+=4;
          while(i<n &&  txt.charAt(i)==' ') i++;
          if(txt.charAt(i)=='"' || txt.charAt(i)=="'") c=txt.charAt(i++);
          else c=" ";
          fn='';
          while(i<n && txt.charAt(i)!=">" && txt.charAt(i)!=c) fn+=txt.charAt(i++);
          if(i<n && txt.charAt(i)!=">") i++;
          j=i;
          k=fn.indexOf('getimage=');
          if(k!=-1) {
            k=k+9;
            f='';
            while(k<fn.length && fn.charAt(k)!='&') k++;
            while(k>0 && fn.charAt(k)!='/' && fn.charAt(k)!='=') f=fn.charAt(k--)+f;
            s+='src='+c+dir+f+c;
            imgs[imgs.length]=fn;
          } else {
            s+='src='+c+fn+c;
          }
        }

      } else i++;
    }
    if((i-j)>0) s+=txt.substr(j,i-j);

    this.CopyImages(dir,imgs);
    return s;
  }

  this.CheckResizedImg=function() {
    var s=this.Win.document.getElementsByTagName('IMG');
    var j,w,h;
    for(var i=0;i<s.length;i++) {
      j=s[i].src.indexOf('?size=');
      if(j!=-1) {
	w='';
        j+=6;
        while(s[i].src.charAt(j)!='?') w+=s[i].src.charAt(j++);
        h='';
        j+=2;
        while(j<s[i].src.length && s[i].src.charAt(j)!='?' && s[i].src.charAt(j)!='&') h+=s[i].src.charAt(j++);
        if(w!=s[i].width || h!=s[i].height) {
          var sr=s[i].src;
	  if(h!='') h+='?';
          s[i].src=sr.replace('?size='+w+'?x'+h,'?size='+s[i].width+'?x'+s[i].height+'?');
	  this.EdtOnKey(this.CurrentElm);
          return true;
        }
      }
    }
  }

  this.OnKeyPress=null;
  this.OnKeyUp=null;
  this.OnKeyDown=null;

  this.FocusedDiv=null;

  this.saveOut=function(cid,txt) {
	var s=this.Win.document.getElementsByTagName('span');
	 for(var i=0;i<s.length;i++) {
		if(s[i].className==cid) {
			s[i].innerHTML=txt;
			if(this.MenuMode=='nav') {
			  this.TabPanel.getTopToolbar().items.get('btSave').setVisible(true);
			}
			this.EdtOnKey(s[i]);
			return true;
		}
	 }
	 return false;
  }

  this.Edit=function() {


    if(!IsConteneditable()) return false;

    var s=this.Win.document.getElementsByTagName('span');
    var o=this;
    var st;
    var log1=true;
    for(var i=0;i<s.length;i++) {

      if(s[i].className.indexOf('edt:')==0) {
        if(this.EditMode) {
          s[i].style.background='none';
          s[i].onclick=null;
          s[i].onkeydown=null;
          s[i].onfocus=null;
          o.FocusedDiv=null;
          if(s[i].emode!=null && !s[i].emode) {
            s[i].emode=true;
            st=str_replace('&lt;','<',s[i].innerHTML);
            s[i].innerHTML=str_replace('&gt;','>',st);
          }
        } else {
          if(log1) s[i].style.background='url(./ext/img/white50.png)';
          else s[i].style.background='url(./ext/img/white20.png)';

    //s[i].onclick=function(){return o.EdtOnClick(this);}
          s[i].onkeydown=function() {return o.EdtOnKey(this);}
          var log=false;
          s[i].onfocus=function() {
            if(o.FocusedDiv!=null) {
			  o.FocusedDiv.style.background='url(./ext/img/white20.png)';
              o.FocusedDiv=this;
              o.FocusedDiv.style.background='url(./ext/img/white50.png)';
			}
          }

          s[i].onmousedown=function() {log=true;}
          s[i].onmousemove=function() {log=false;}
          s[i].onmouseup=function() {
             if(!log) {
               window.setTimeout(function(){
                 o.CheckResizedImg();
               },1000);
             }
          }
        }
        s[i].contentEditable=!this.EditMode;
        if(s[i].contentEditable && log1) {
          log1=false;
          s[i].focus();
          o.MkTextTbar();
          o.FocusedDiv=s[i];
        }
      }
    }
    var ico;
    var t;
    if(!this.EditMode) {
      ico='preview';
      //t=DLG.t("vmVis");
    } else {
      ico='editor';
      //t=DLG.t("vmEdit");
    }
    s=this.PPanel.getTopToolbar().items.get('bEdit');
    s.setIconClass(ico);
    //s.setText(t);

    this.EditMode=!this.EditMode;


    if(!this.EditMode) {
      this.DsblBtbar(true);

      this.Win.document.body.onkeypress=this.OnKeyPress;this.OnKeyPress=null;
      this.Win.document.body.onkeyup=this.OnKeyUp;this.OnKeyUp=null;
      this.Win.document.body.onkeydown=this.OnKeyDown;this.OnKeyDown=null;

    } else {
      this.OnKeyPress=this.Win.document.body.onkeypress;
      this.OnKeyUp=this.Win.document.body.onkeyup;
      this.OnKeyDown=this.Win.document.body.onkeydown;
      var ww=this.Win;
      var th=this;
      this.Win.document.body.onkeypress=function(e){

	if(navigator.userAgent.indexOf('MSIE') >= 0) e=ww.event;

	if(e.keyCode==13) {
	  if($is.IE) {
	    if(e.shiftKey) {
	      if (r=document.selection.createRange()) {r.pasteHTML("<BR>");r+=5;}
	      return false;
	    }
	  } else {
	    if(e.shiftKey) var b=th.Win.document.createElement("br");
	    else var b=th.Win.document.createElement("p");
	    var d=th.GetSelectedRange();
	    d.insertNode(b);
	    d.setStartAfter(b);
	    return false;
	  }
	}

	if(e.keyCode==27) {
	  th.Edit();
	  return false;
	}
      }


      this.Win.document.body.onkeyup=function(e) {

	if(navigator.userAgent.indexOf('MSIE') >= 0) {
	  e=ww.event;
	  if(e.keyCode==46 && th.MenuMode=='image') th.ChangeBar('edittext');
	}

	if(e.ctrlKey && e.keyCode==83) {
	  th.Save();
	}
      }

      var o=this;
      this.Win.document.body.onkeydown=function(e) {
	if(navigator.userAgent.indexOf('MSIE') >= 0) {
	  e=ww.event;
	}


	if(e.ctrlKey && e.keyCode==76) {
	  var d=o.GetSelectedRange();
	  if(navigator.userAgent.indexOf('MSIE') >= 0) {
	    if(d.text=='')  {
	      var r;
	      if(r=o.Win.document.selection.createRange()) {
		r.moveToBookmark(d.getBookmark());
	      }
	      d.pasteHTML('<a href="'+ClipboardData+'">'+ClipboardDataText+'</a>');
	    } else {
	      o.Win.document.execCommand('createlink',false,ClipboardData);
	    }

	  } else {
	    if(d.endOffset!=d.startOffset) o.Win.document.execCommand('createlink',false,ClipboardData);
	    else {
	      var im=o.Win.document.createElement("A");
	      im.href=ClipboardData;
	      im.innerHTML=ClipboardDataText;
	      d.insertNode(im);
	    }
	  }
	  return false;
	}
      }
    }
    return true;
  }


  this.EdtOnKey=function(elm) {
    this.CurrentElm=elm;
    for(var i=0;i<this.ChangedElm.length;i++) {
      if(this.ChangedElm[i]==elm) return true;
    }
    if(this.ChangedElm.length==0) {
      try {
        this.PPanel.getTopToolbar().items.get('btSave').enable();
      } catch(e){}
    }
    this.ChangedElm[this.ChangedElm.length]=elm;

  }

  this.EdtOnClick=function(elm) {
    if(this.CurrentElm==elm) return false;
    this.CurrentElm=elm;
    if(elm.className.indexOf(':editor')==(elm.className.length-7)) {
      this.ChangeBar('edittext');
    } else {
      this.ChangeBar('editstring');
    }
    return false;
  }

  this.Save=function() {
    var s;
    var o=this;
	var s1='';
	//alert('Ñòàðò ïðîâåðêè:'+this.CurrentPageAttr);



	//if(this.CurrentPageAttr=='') {
	  //alert('Ïðîâåðêà: id='+this.CurrentPageCode+';attr='+attr);
		treeChangeAttr(this.CurrentPageCode,1);
		s1='&pgid='+this.CurrentPageCode;
	//}

    for(var i=0;i<this.ChangedElm.length;i++) {
      s=this.ChangedElm[i].className.split(":");
      s[4]=this.ChangedElm[i].innerHTML;
      s[4]=this.PrepStorePics(s[4],UPLOAD_IMG_DIR+s[1]+'/'+s[3]+'/');

      Ext.Ajax.request({
        url: 'ajaxfunc.php?chrow=yes'+(i==0? s1:''),
        success: function(response) {
      if(response.responseText!="OK") return false;
          else {
            o.ChangedElm=new Array();
            o.PPanel.getTopToolbar().items.get('btSave').disable();
            o.CurrentElm.focus();
          }
        },
        params: {
          tab:s[1],
          fold:s[2],
          id:s[3],
          data:rusescape(s[4])
        }
      });
    }

  }

  this.command=function(cmd,ui) {
    if(ui=null) ui=false;
    if($is.IE>=5.5) {
      if(cmd=='createlink') {
	s=prompt(DLG.t('CreateLink'),(ClipboardData.substr(0,4)=='http'? ClipboardData:''));
	if(s!=null && s!='' ) this.Win.document.execCommand(cmd,false,s);
      } else this.Win.document.execCommand(cmd,ui);
    } else {
      if(cmd=='createlink') {
	s=prompt(DLG.t('CreateLink'),(ClipboardData.substr(0,4)=='http'? ClipboardData:''));
	if(s!=null && s!='' ) {



	  this.Win.document.execCommand(cmd,false,s);
	}
      } else this.Win.document.execCommand(cmd,ui,null);
    }
    this.EdtOnKey(this.CurrentElm);
  }

  this.ChEdtMode=function() {

    try{
      if(EditHtmlCodeRuntime!=null) {
	EditHtmlCodeRuntime(this);
	return false;
      }
    } catch(e) {}

    if(this.CurrentElm.emode==null) this.CurrentElm.emode=true;
    this.CurrentElm.emode=!this.CurrentElm.emode;

    var s=this.CurrentElm.innerHTML;
    var tb=this.PPanel.getTopToolbar();
    if(this.CurrentElm.emode) {
      s=str_replace('&lt;','<',s);
      s=str_replace('&gt;','>',s);
      this.CurrentElm.innerHTML=s;
      for(var i=0;i<tb.items.length;i++) {
        if(tb.items.item(i).id!='ed_HTML') tb.items.item(i).enable();
      }
    } else {
      s=str_replace('<','&lt;',s);
      s=str_replace('>','&gt;',s);
      this.CurrentElm.innerHTML=s;
      for(var i=0;i<tb.items.length;i++) {
        if(tb.items.item(i).id!='ed_HTML') tb.items.item(i).disable();
      }
    }
  }

  this.ChStyle=function(s,t) {
    var d=this.GetSelectedRange(false);
    if(navigator.userAgent.indexOf('MSIE') >= 0){
      if(d.text!='') {
        s='<'+t+s+'>'+d.text+'</'+t+'>';
        if(d.parentElement().innerText==d.text) {
          d.parentElement().outerHTML=s;
        } else {
          d.pasteHTML(s);
        }
        this.EdtOnKey(this.CurrentElm);
      }
    } else {
       var ss=d.startContainer.parentNode.innerText;
       var st=d.extractContents().textContent;
       if(st!='') {
         if(ss==st) {
           d.startContainer.parentNode.outerHTML='<'+t+s+'>'+st+'</'+t+'>';
         } else {
           var e=this.Win.document.createElement(t);
           e.innerHTML=st;
           d.insertNode(e);
         }
         this.EdtOnKey(this.CurrentElm);
       }
    }

  }

  this.tagClear=function() {
    var d=this.GetSelectedRange(false);
    if(navigator.userAgent.indexOf('MSIE') >= 0){
      if(d.text!='') {
        if(d.parentElement().innerText==d.text) {
          d.parentElement().outerHTML=modify_html.modif(d.outerHTML);
        } else {
          d.pasteHTML(modify_html.modif(d.htmlText));
        }
        this.EdtOnKey(this.CurrentElm);
      } else {
    if(DLG.c('editorPrepAllTextQ')) {
      this.CurrentElm.innerHTML=modify_html.modif(this.CurrentElm.innerHTML);
      this.EdtOnKey(this.CurrentElm);
    }
    }
    } else {
       var ss=d.startContainer.parentNode.innerText;
       var st=d.extractContents().textContent;

     if(st!='') {
         d.startContainer.parentNode.outerHTML=modify_html.modif(d.startContainer.parentNode.outerHTML);
     this.EdtOnKey(this.CurrentElm);
     } else {
           if(DLG.c('editorPrepAllTextQ')) {
         //alert(this.CurrentElm.innerHTML);
        this.CurrentElm.innerHTML=modify_html.modif(this.CurrentElm.innerHTML);
         this.EdtOnKey(this.CurrentElm);
       }
       }
    }
  }

  this.ChangeBar=function(mod) {

	var t=this.TabPanel.getTopToolbar();
    for(var i=0;i<t.items.length;i++) {
		//alert(t.items.items[i].id);
		t.items.items[i].setVisible(false);
	}
	var log=false;

	if(this.PublicMode) mod='nav-only';


	if(_TREE_ACCESS_>2 && mod!='nav-only') {
		t.items.get('btPublview').setVisible(true);
	}

	if(mod=='editstring') {
	// Ïîêàçûâàåì ïàíåëü äëÿ ðåäàêòèðîâàíèÿ òîëüêî ñòðîêè
	    this.MenuMode='string';
		for(var i=0;i<t.items.length;i++) {
			 if(t.items.items[i].id=='endEdit') t.items.items[i].setVisible(true);
			 if(t.items.items[i].id=='btSave') {
				 t.items.items[i].setVisible(true);
				 t.items.items[i-1].setVisible(true);
			 }
		 }
	} else
	if(mod=='edittext') {
	// Ïîêàçûâàåì ïàíåëü ðåäàêòèðîâàíèÿ	òåêñòà
		 this.MenuMode='text';
		 for(var i=0;i<t.items.length;i++) {
			 if(t.items.items[i].id=='endEdit') log=true;
			 if(log) t.items.items[i].setVisible(true);
			 if(t.items.items[i].id=='ed_HTML') return false;
		 }
	} else
	if(mod=='editimage') {
	// Ïîêàçûâàåì ïàíåëü ðåäàêòèðîâàíèÿ	êàðòèíîê
	    this.MenuMode='image';
		for(var i=0;i<t.items.length;i++) {
			 if(t.items.items[i].id=='endEdit') t.items.items[i].setVisible(true);
			 if(t.items.items[i].id=='btSave') t.items.items[i].setVisible(true);
			 if(log) t.items.items[i].setVisible(true);
			 if(t.items.items[i].name=='edi_sep') log=true;
			 if(t.items.items[i].name=='ed_i_alt') return false;
		 }
	} else
	if(mod=='nav' || mod=='nav-only') {
	// Ïîêàçûâàåì ïàíåëü íàâèãàöèè
	    this.MenuMode='nav';
		log=true;
		for(var i=0;i<t.items.length;i++) {
			if(log) {

				if(mod=='nav-only' && t.items.items[i].id=='bEdit') {return false;}
				else t.items.items[i].setVisible(true);
			}
			if(t.items.items[i].id=='bEdit') return false;
		}
	}


  }

  this.MakePrevTab=function(cod, ico, urlcod) {
    //var url=GetPageProps(cod);
    //var Win=null;

	var plg=this;
    var EditButton=null;

	var urlString=new Ext.form.TextField({
		width:350,
        id:'pageurl'+plg.UID,
		emptyText:''
	 });


    if(IsConteneditable()) {
      EditButton=new Ext.Toolbar.MenuButton({
        text:DLG.t("vmEdit"),
        iconCls:'editor',
        id:'bEdit',
        tooltip:DLG.t("vmEditHint"),
        handler:function() {
          plg.Win.focus();
          plg.Edit();
        },
        menu:plg.EditMenu
     });
    } else {
      EditButton={
        text:DLG.t("vmEdit"),
        iconCls:'editor',
        id:'bEdit',
        tooltip:DLG.t("vmEditHint"),
        menu:plg.EditMenu
      };
    }

	if(ParentW._TREE_ACCESS_<3) EditButton='';

    var tbar=[{
      id:'bBack',
      tooltip:DLG.t("vmBack"),
      iconCls:'back',
      handler:function() {
        plg.Go(-1);
      }
    },{
      id:'bForw',
      tooltip:DLG.t("vmForw"),
      iconCls:'forv',
      handler:function() {
        plg.Go(1);
      }
    },{
     tooltip:DLG.t("vmRef"),
     iconCls:'refresh',
     handler:function() {
       plg.Ref();
     }
    },urlString,{
		tooltip:DLG.t("vmRef"),
		iconCls:'go',
		handler:function() {
			plg.Win.location=urlString.getValue();
		}
	 },'-',EditButton
    ];

	if(_TREE_ACCESS_>2) {
		tbar=tbar.concat(['-',{
			text:'',
			tooltip:DLG.t("vmViewPublpage"),
			iconCls:'publview',
			id:'btPublview',
			handler:function() {
				var o=new PrevObj();
				var url=plg.Win.location+'';
				url+=(url.indexOf('?')!=-1? '&':'?')+'show_public_page=yes';
                o.MakePrevTab(url,'publviewtab');
				o.PublicMode=true;
			}
		}]);
	}

    if(IsConteneditable() && ParentW._TREE_ACCESS_>2) {
    bbar=['-',{
		text:DLG.t('wmToViewMode'),
		iconCls:'preview',
		id:'endEdit',
		handler:function() {plg.Edit();}
	},'-',{
      text:'',
      id:'btSave',
      iconCls:'save',
      disabled:true,
      tooltip:DLG.t("vmSaveHint"),
      handler:function() {
        plg.Save();
      }
    },'-',{
      id:'ed_styles'+plg.UID,
      text:'<b><i style="color:red">S</i><b>',
      tooltip:DLG.t("vmStyles"),
      handler:function() {
        MENU_STYLES.prw=plg;
      },
      menu:MENU_STYLES
    },{
    text:'',
    iconCls:'ed_clr',
      id:'ed_clr'+plg.UID,
      tooltip:DLG.t("vmClear"),
      handler:function() {
        plg.tagClear();
      }
  },'-',{
      text:'',
      iconCls:'ed_b',
      id:'ed_b'+plg.UID,
      tooltip:DLG.t("vmBold"),
      handler:function() {
        plg.command('Bold');
      }
    },{
      text:'',
      iconCls:'ed_i',
      id:'ed_itl'+plg.UID,
      tooltip:DLG.t("vmItalic"),
      handler:function() {
        plg.command('Italic');
      }

    },{
      text:'',
      iconCls:'ed_u',
      id:'ed_u'+plg.UID,
      tooltip:DLG.t("vmUnderline"),
      handler:function() {
        plg.command('Underline');
      }
    },'-',/*{
      text:'',
      iconCls:'ed_a1',
      tooltip:DLG.t("vmFontInc"),
      handler:function() {
        plg.command('Indent');
      }
    },{
      text:'',
      iconCls:'ed_a2',
      tooltip:DLG.t("vmFontDesc"),
      handler:function() {
        plg.command('Outdent');
      }
    },'-',*/{
      text:'',
      iconCls:'ed_Indent',
      id:'ed_Indent'+plg.UID,
      tooltip:DLG.t("vmIndent"),
      handler:function() {
        plg.command('Indent');
      }
    },{
      text:'',
      iconCls:'ed_Outdent',
      id:'ed_Outdent'+plg.UID,
      tooltip:DLG.t("vmOutdent"),
      handler:function() {
        plg.command('Outdent');
      }
    },'-',{
      text:'',
      iconCls:'ed_ol',
      id:'ed_ol'+plg.UID,
      tooltip:DLG.t("vmOl"),
      handler:function() {
        plg.command('InsertOrderedList');
      }
    },{
      text:'',
      iconCls:'ed_ul',
      id:'ed_ul'+plg.UID,
      tooltip:DLG.t("vmUl"),
      handler:function() {
        plg.command('InsertUnorderedList');
      }
    },'-',{
      text:'',
      iconCls:'ed_ta1',
      id:'ed_ta1'+plg.UID,
      tooltip:DLG.t("vmAlLeft"),
      handler:function() {
        plg.command('JustifyLeft');
      }
    },{
      text:'',
      iconCls:'ed_ta2',
      id:'ed_ta2'+plg.UID,
      tooltip:DLG.t("vmAlCentr"),
      handler:function() {
        plg.command('JustifyCenter');
      }
    },{
      text:'',
      iconCls:'ed_ta3',
      id:'ed_ta3'+plg.UID,
      tooltip:DLG.t("vmAlRight"),
      handler:function() {
        plg.command('JustifyRight');
      }
    },'-',{
      text:'',
      id:'ed_href'+plg.UID,
      iconCls:'ed_href',
      tooltip:DLG.t("vmLink"),
      handler:function() {
        plg.command('createlink');
      }
    },{
      text:'',
      iconCls:'ed_img',
      id:'ed_img'+plg.UID,
      tooltip:DLG.t("vmImg"),
      handler:function() {
        plg.InsertImage();
      }
    },'-',{
      text:'HTML',
      id:'ed_HTML',
      name:'edi_sep',
      iconCls:'',
      tooltip:'',
      handler:function() {
        plg.ChEdtMode();
      }
    },'-',
   {
      text:'',
      id:'ed_ial_bottom'+plg.UID,
      iconCls:'ed_ial_bottom',
      tooltip:DLG.t("vmImgBottom"),
      handler:function() {
        plg.CurrentImg.align='bottom';
        plg.EdtOnKey(plg.CurrentElm);
      }
    },{
      text:'',
      iconCls:'ed_ial_middle',
      id:'ed_ial_middle'+plg.UID,
      tooltip:DLG.t("vmImgCenter"),
      handler:function() {
        plg.CurrentImg.align='middle';
        plg.EdtOnKey(plg.CurrentElm);
      }
    },{
      text:'',
      iconCls:'ed_ial_top',
      id:'ed_ial_top'+plg.UID,
      tooltip:DLG.t("vmImgTop"),
      handler:function() {
        plg.CurrentImg.align='top';
        plg.EdtOnKey(plg.CurrentElm);
      }
    },{
      text:'',
      iconCls:'ed_ial_left',
      id:'ed_ial_left'+plg.UID,
      tooltip:DLG.t("vmImgLeft"),
      handler:function() {
        plg.CurrentImg.align='left';
        plg.EdtOnKey(plg.CurrentElm);
      }
    },{
      text:'',
      iconCls:'ed_ial_right',
      id:'ed_ial_right'+plg.UID,
      tooltip:DLG.t("vmImgRight"),
      handler:function() {
        plg.CurrentImg.align='right';
        plg.EdtOnKey(plg.CurrentElm);
      }
    },'-',
      new Ext.form.NumberField({
      id:'ed_i_width'+plg.UID,
      //itemCls:'ed_i_width',
      style:'padding-left:17px;background:#ffffff url(ext/img/editor/im_width.gif) no-repeat center left',
      name:'ed_i_width',
      width:50,
      maxLength:4,
      maxValue:5000,
      listeners:{
        change:function(Field, newValue, oldValue ) {
          if(newValue!=oldValue) {
            plg.CurrentImg.width=newValue;
            plg.CheckResizedImg();
          }
        }
      }
    }),'-',
    new Ext.form.NumberField({
      //ctCls:'ed_i_height',
      style:'padding-left:17px;background:#ffffff url(ext/img/editor/im_height.gif) no-repeat center left',
      id:'ed_i_height'+plg.UID,
      name:'edi_height',
      width:50,
      maxLength:4,
      maxValue:5000,
      listeners:{
        change:function(Field, newValue, oldValue ) {
          if(newValue!=oldValue) {
            plg.CurrentImg.height=newValue;
            plg.CheckResizedImg();
          }
        }
      }
    }),'-',
    new Ext.form.TextField({
      //ctCls:'ed_i_vspace',
      style:'padding-left:17px;background:#ffffff url(ext/img/editor/im_vspace.gif) no-repeat center left',
      name:'edi_vspace',
      id:'ed_i_vspace'+plg.UID,
      width:50,
      maxLength:4,
      maxValue:5000,
      listeners:{
        change:function(Field, newValue, oldValue ) {
          if(newValue!=oldValue) {
            newValue=newValue.split(',');
            if(newValue.length==2) {
            	plg.CurrentImg.style.paddingTop=newValue[0]+'px';
            	plg.CurrentImg.style.paddingBottom=newValue[1]+'px';
            } else {
            	plg.CurrentImg.style.paddingTop=newValue[0]+'px';
            	plg.CurrentImg.style.paddingBottom=newValue[0]+'px';
            }
            plg.EdtOnKey(plg.CurrentElm);
          }
        }
      }
    }),'-',
    new Ext.form.TextField({
      name:'edi_hspace',
      //ctCls:'ed_i_hspace',
      style:'padding-left:17px;background:#ffffff url(ext/img/editor/im_hspace.gif) no-repeat center left',
      id:'ed_i_hspace'+plg.UID,
      width:50,
      maxLength:10,
      maxValue:5000,
      listeners:{
        change:function(Field, newValue, oldValue ) {
          if(newValue!=oldValue) {
            //plg.CurrentImg.hspace=newValue;
            newValue=newValue.split(',');
            if(newValue.length==2) {
            	plg.CurrentImg.style.paddingLeft=newValue[0]+'px';
            	plg.CurrentImg.style.paddingRight=newValue[1]+'px';
            } else {
            	plg.CurrentImg.style.paddingLeft=newValue[0]+'px';
            	plg.CurrentImg.style.paddingRight=newValue[0]+'px';
            }
            plg.EdtOnKey(plg.CurrentElm);
          }
        }
      }
    }),'-',{
      text:'',
      id:'ed_i_alt'+plg.UID,
      name:'ed_i_alt',
      iconCls:'ed_i_alt',
      tooltip:DLG.t("vmImgAlt"),
      handler:function() {
        var p=prompt(DLG.t("vmImgAlt"),plg.CurrentImg.alt);
        if(p!=null && p!='undefined') {
          plg.CurrentImg.alt=p;
          plg.EdtOnKey(plg.CurrentElm);
        }
      }
    }];
    tbar=tbar.concat(bbar);
    }

    if(urlcod==null) {
      urlcod='/'+cod+'.html?prev='+_TREE_ACCESS_;
    } else {
      ico='preview';
    }

    if(ico==null) {
      //alert(urlcod);
      this.TabPanel=OpenNewTab(urlcod,'',(_TREE_ACCESS_>2? 'previco':'publviewtab'),null,tbar,null,this);
      this.ChangeBar('nav');
    } else {
      //alert(cod);
      this.TabPanel=OpenNewTab(urlcod,'',ico,null,tbar,null,this);
      this.ChangeBar('nav-only');
    }
  }
}