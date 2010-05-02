var insertimgCommand=function(){};
insertimgCommand.prototype.Execute=function(){}

LogTimeChanged=null;

function escape_fn(fn) {
  var x=fn.indexOf('getimage=');
  if(x>0) {
    fn=fn.substr(0,x)+'esc=yes&getimage='+escape(fn.substr(x+9));
  }
  return fn;
}   


function AttachEvt(FCK) {
  var log=false;

/*try {
  var x=FCK.EditorWindow.document;
} catch(e) {
  return false;
}*/
  FCK.prevObj=new parent.parent.PrevObj();
  FCK.prevObj.Win=FCK.EditorWindow;
  
  if(navigator.userAgent.indexOf('MSIE') >= 0){
   // Регистрируем события для осла
    FCK.EditorWindow.document.onmousedown=function() {log=true;}
    FCK.EditorWindow.document.onmousemove=function() {log=false;}
    FCK.EditorWindow.document.onmouseup=function() {
      if(!log) window.setTimeout(function(){FCK.prevObj.CheckResizedImg();},1000);
    }
  } else {
   // события для gekko      
    FCK.Events.AttachEvent( 'OnSelectionChange', function(){
      if(LogTimeChanged!=null) window.clearTimeout(LogTimeChanged);
      LogTimeChanged=window.setTimeout(function() {
        LogTimeChanged=null;
        
        FCK.prevObj.CheckResizedImg();
      },1000);
    }) ;         
  }
}

insertimgCommand.GetState=function() {   
   AttachEvt(FCK);
   FCK.Events.AttachEvent( 'OnAfterSetHTML', function(){AttachEvt(FCK);});
   return FCK_TRISTATE_OFF; //we dont want the button to be toggled
}

insertimgCommand.Execute=function() {
  var d=null;
  FCK.Focus();
  if(navigator.userAgent.indexOf('MSIE') >= 0){
     d=FCK.EditorDocument; 
     d=d.selection.createRange();
  } else {		
     d=FCK.EditorWindow.getSelection();
     d=d.getRangeAt(0);
     //d.collapse();
  }
  parent.parent.IO.OpenFileDlg(parent.parent.DLG.t('Insert image'),[['*.gif,*.jpg,*.jpeg,*.png','Images (gif, jpeg, png)'],['*.*','All formats (*.*)']],function(fn){
    
    fn=FCK.prevObj.ShowPics(fn);
    //FCK.Focus();  
    
    if(navigator.userAgent.indexOf('MSIE') >= 0){
      var r;
      if(r=FCK.EditorDocument.selection.createRange()) {
         r.moveToBookmark(d.getBookmark());         
      } 
      var st='';
      for(var i=0;i<fn.length;i++) st+='<img src="'+escape_fn(fn[i])+'" border="0" alt="" />';
      d.pasteHTML(st);        
    } else {
      var im=null;
      for(var i=0;i<fn.length;i++) { 
        im=FCK.EditorDocument.createElement("IMG");
        im.src=escape_fn(fn[i]);
        d.insertNode(im);
      }
      /*for(var i=0;i<fn.length;i++) {
        FCK.InsertHtml('<img src="'+fn+'" border="0" alt="" />');
      } */
    }
    FCK.ResetIsDirty();
  }); 
}

FCKCommands.RegisterCommand('insertimg', insertimgCommand );
var oinsertimg = new FCKToolbarButton('insertimg', 'insert image');
oinsertimg.IconPath = FCKConfig.PluginsPath + 'insertimg/img.gif'; //specifies the image used in the toolbar
FCKToolbarItems.RegisterItem( 'insertimg', oinsertimg );
