  function do_clear(nm){
   document.getElementById(nm).value="";
    document.getElementById(nm+'_editor').innerHTML="";
  }
  
  
  function make_ordered(nm){
    document.getElementById(nm+"_editor").focus();
    if(Ext.isIE) document.execCommand("insertorderedlist",false);
    else  document.execCommand("insertorderedlist",false,null);
    save_edit_chng(nm)
  }

  function make_unordered(nm){
    document.getElementById(nm+"_editor").focus();
	if(Ext.isIE) document.execCommand("insertunorderedlist",false);
        else  document.execCommand("insertunorderedlist",false,null);
	save_edit_chng(nm)
  }

  function make_indent(nm){
    document.getElementById(nm+"_editor").focus();
	if(Ext.isIE) document.execCommand("Indent",false);
        else  document.execCommand("Indent",false,null);
	save_edit_chng(nm)
  }

  function make_outdent(nm){
    document.getElementById(nm+"_editor").focus();
	document.execCommand("Outdent");
	save_edit_chng(nm)
  }

  function back_color(nm) {
    var arr = showModalDialog( "./editor/lib/editor/img/selcolor.htm","","font-family:Verdana; font-size:12; dialogWidth:30em; dialogHeight:30em" );
    if (arr != null) {
      document.all(nm+"_editor").focus();
	  document.execCommand("BackColor",false, arr);
	  save_edit_chng(nm)
    }
  }

  function fore_color(nm) {
    var arr = showModalDialog( "./editor/lib/editor/img/selcolor.htm","","font-family:Verdana; font-size:12; dialogWidth:30em; dialogHeight:30em" );
    if (arr != null) {
      document.all(nm+"_editor").focus();
	  document.execCommand("ForeColor",false, arr);
	  save_edit_chng(nm)
    }
  }

  function make_link(nm){
   var s=prompt('Введите URL ссылки','http://');
   if(s!=null && s!='') {
      document.execCommand("createlink",false,s);
      save_edit_chng(nm)
      }
  }

  function make_underline(nm){
    document.getElementById(nm+"_editor").focus();
	if(Ext.isIE) document.execCommand("Underline",false);
        else  document.execCommand("Underline",false,null);
	save_edit_chng(nm)
  }
  
  function make_unlink(nm){
    document.getElementById(nm+"_editor").focus();
	if(Ext.isIE) document.execCommand("UnLink",false);
        else  document.execCommand("UnLink",false,null);
	save_edit_chng(nm)
  }

  function make_bold(nm){
   document.getElementById(nm+"_editor").focus();
    if(Ext.isIE) {
      document.execCommand("Bold",false);
      }
      else document.execCommand("Bold",false,null);
    save_edit_chng(nm)
  }
  function make_italic(nm){    
	document.getElementById(nm+"_editor").focus();
        if(Ext.isIE) document.execCommand("Italic",false);
        else  document.execCommand("Italic",false,null);
	save_edit_chng(nm)
  }

  
function reset_formatting(nm) {
  var e=document.getElementById(nm+"_editor");
  e.focus();

 e.innerHTML=ParentW.modify_html.modif(e.innerHTML);
 save_edit_chng(nm);
 
 return false;
   
  var s=(Ext.isIE? e.innerText:e.textContent);
  os="";
  for(var i=0;i<s.length;i++) {
    if(s.charAt(i)=="\n") os+="<br>";
    else os+=s.charAt(i);
  }
     
  e.innerHTML=os;   
   
}  


editor_time=0;
function save_edit_chng(nm) {
  if(editor_time!=0) window.clearTimeout(editor_time);
  editor_time_name=nm;
  editor_time=window.setTimeout("save_edit_chng_text()",100);  
}
function save_edit_chng_text() {
  document.getElementById(editor_time_name).value=document.getElementById(editor_time_name+"_editor").innerHTML;
}

function echoEditor(name,w,h) {
var s="";
s+='<table border="0" bgcolor="#ffffff" style="width:'+w+'px;height:'+h+'px" cellspacing="0">';
s+='<tr><td height="35"><div class="menu" width:100%; height:40;>';
s+='<table cellspacing=0 cellpadding=0 border=0 width="100%" background="./editor/lib/editor/img/empty.gif"><tr><td><img src="./editor/lib/editor/img/prefix.gif" unselectable="on" width=9 height=27 border=0>';

s+='<a onclick="do_clear(\''+name+'\');" title="Очистить"><img name="new" unselectable="on" border="0" src="./editor/lib/editor/img/new_flat.gif" width=23 height=27 border=0></a>';
s+='<img src="./editor/lib/editor/img/separator.gif" unselectable="on" width=6 height=27 border=0>'; 
s+='<a onclick="make_bold(\''+name+'\');" title="Полужирный" ><img name="bold" border="0" unselectable="on" width=23 height=27 src="./editor/lib/editor/img/bold_flat.gif"  border=0></a>';
s+='<a onclick="make_italic(\''+name+'\');" title="Курсив" ><img name="italic" width=23 height=27 border="0" unselectable="on" src="./editor/lib/editor/img/italic_flat.gif"  border=0></a>';
s+='<img src="./editor/lib/editor/img/separator.gif" width=6 height=27 border=0>';
s+='<a onclick="fore_color(\''+name+'\');" title="Цвет текста" ><img name="fg" unselectable="on" width=23 height=27 border="0" src="./editor/lib/editor/img/fg_flat.gif"  border=0></a>';
s+='<a onclick="make_link(\''+name+'\');" title="Добавление гиперссылки" ><img name="link" unselectable="on" width=23 height=27 border="0" unselectable="on" src="./editor/lib/editor/img/link_flat.gif"  border=0></a>';
s+='<a onclick="make_unlink(\''+name+'\');" title="Удаление гиперссылки" ><img name="unlink"  width=23 height=27 border="0" src="./editor/lib/editor/img/unlink_flat.gif"  border=0></a>';
s+='<a onclick="make_ordered(\''+name+'\');" title="Нумерация" ><img name="olist" unselectable="on" width=23 height=27 border="0" src="./editor/lib/editor/img/olist_flat.gif"  border=0></a>';
s+='<a onclick="make_unordered(\''+name+'\');" title="Маркеры" ><img name="uolist" unselectable="on" width=23 height=27 border="0" src="./editor/lib/editor/img/uolist_flat.gif" width=23 height=27 border=0></a>';

s+='<a onclick="reset_formatting(\''+name+'\');" title="Сбросить форматирование" ><img name="resetformat" unselectable="on" width=23 height=27 border="0" src="./editor/lib/editor/img/ntg.gif" width=23 height=27 border=0></a>';

s+='</td></tr></table></div></td><td align=right><img unselectable="on" src="./editor/lib/editor/img/empty_end.gif" border=0></td></tr><tr><td>';
s+='<div id="'+name+'_editor" onkeyup="save_edit_chng(\''+name+'\')" oncut="save_edit_chng(\''+name+'\')" onpaste="save_edit_chng(\''+name+'\')" CONTENTEDITABLE="true" style="overflow:auto; width:'+w+'px;height:'+(h-25)+'px; border:1px solid #595959; background-color:white; visibility:visible; padding-left:3px;" class="easyeditor">'+document.getElementById(name).value+'</div>';
s+='</td></tr></table>';  
document.write(s);
window.setTimeout(function() {document.getElementById(name+'_editor').contenteditable=true;},500);

}
