var insertbrCommand=function(){}

insertbrCommand.prototype.Execute=function(){}

insertbrCommand.GetState=function() {   
   return FCK_TRISTATE_OFF; //we dont want the button to be toggled
}

insertbrCommand.Execute=function() {
  var d=null;
  FCK.Focus();
  if(navigator.userAgent.indexOf('MSIE') >= 0){
     d=FCK.EditorDocument; 
     d=d.selection.createRange();
  } else {		
     d=FCK.EditorWindow.getSelection();
     d=d.getRangeAt(0);
  }
  
  if(navigator.userAgent.indexOf('MSIE') >= 0){
      var r;
      if(r=FCK.EditorDocument.selection.createRange()) r.moveToBookmark(d.getBookmark()); 
      d.pasteHTML('<br>');
      d+=5;
  } else {
      var im=FCK.EditorDocument.createElement("BR");
      d.insertNode(im);
      d.setStartAfter(im);
  }
  FCK.ResetIsDirty();   
}

FCKCommands.RegisterCommand('insertbr', insertbrCommand);
var oinsertbr = new FCKToolbarButton('insertbr', 'Insert br-tag');
oinsertbr.IconPath = FCKConfig.PluginsPath + 'insertbr/br.gif';
FCKToolbarItems.RegisterItem('insertbr', oinsertbr);
