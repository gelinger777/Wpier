document.write('<script type="text/javascript" src="../../ext/modify_html.js"></script>');

var modifyhtmlCommand=function(){};
modifyhtmlCommand.prototype.Execute=function(){}

modifyhtmlCommand.GetState=function() {   
   //AttachEvt(FCK);
   //FCK.Events.AttachEvent( 'OnAfterSetHTML', function(){AttachEvt(FCK);});
   return FCK_TRISTATE_OFF; //we dont want the button to be toggled
}

modifyhtmlCommand.Execute=function() {
  var d=null;
  
  FCK.Focus();
  if(navigator.userAgent.indexOf('MSIE') >= 0){
	 d=FCK.EditorDocument; 
     d=d.selection.createRange();
  } else {
     d=FCK.EditorWindow.getSelection();
     //d=d.getRangeAt(0);
	 d.text="";

  }
  if(d.text=='') {
	  if(parent.parent.DLG.c('Prepare full text?')) 
		  FCK.EditorDocument.body.innerHTML=parent.parent.modify_html.modif(FCK.EditorDocument.body.innerHTML);
  } else {
	  var x=parent.parent.modify_html.modif(d.htmlText);
	  d.pasteHTML(x);
  }
  ///FCK.ResetIsDirty();

}

FCKCommands.RegisterCommand('modifyhtml', modifyhtmlCommand );
var onmodifyhtml = new FCKToolbarButton('modifyhtml', parent.parent.DLG.t('Cleane HTML code from Word format'));
onmodifyhtml.IconPath = FCKConfig.PluginsPath + 'modifyhtml/modhtml.gif'; //specifies the image used in the toolbar
FCKToolbarItems.RegisterItem( 'modifyhtml', onmodifyhtml);
