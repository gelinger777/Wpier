function edit_txt(key,w) {
  if(ParentW!=null) {
    ParentW.editor_text(document.all(key),document.getElementById('div_'+key));
  }
}

TextEditorHtml="";
CurrentSenderTextarea=null;
CurrentSenderDiv=null;
function editor_text(txtar,txtdiv) {
  
  document.getElementById('TmpDiv').innerHTML=txtar.value;
  CurrentSenderTextarea=txtar;
  CurrentSenderDiv=txtdiv;
  
  window.status='cmd:editor';

}

function setEditorHtml() {
  CurrentSenderTextarea.value=document.getElementById('TmpDiv').innerHTML;
  CurrentSenderDiv.innerHTML=document.getElementById('TmpDiv').innerHTML;
}

function EditorLinksTree(TreeString) {
  var out="";
  var log=true;
  for(var i=0;i<TreeString.length;i++) {
     if(TreeString.charAt(i)=="<") log=false;
	 else if(TreeString.charAt(i)==">") log=true;
	 else if(log) out+=TreeString.charAt(i);
  }
  document.getElementById('TmpDiv').innerHTML=out;
  window.status='cmd:reflinks';
}

CurrFormID=null;

function SetCurrFormID(frm) {
  CurrFormID=frm;
}

function SendCurrentForm() {
  if(CurrFormID!=null) {
    CurrFormID.submit();
    CurrFormID=null;
  }  
}

function CheckLocalFiles(frm,EXT) {
  if(ParentW!=null) {
    ParentW.SetCurrFormID(frm);
  }
  var s='';
  var j;
  var log;
  var fn='';
  var c;
  var files;
  var fil;
  var num=0;
  var ex;
  var x;
  for (var i=0; i<document.all.length; i++) {
    if(document.all.item(i).className=='InouterEditableDiv') {
      c=document.all.item(i).id;
      j=c.indexOf('_');
      if(j>=0) {
        c=c.substr(j+1);
        if(document.all(c)!=null) document.all(c).value=document.all.item(i).innerHTML;
      }
     }
  }
  
  for(i=0;i<frm.elements.length;i++) {
    if(frm.elements(i).type=='textarea' || frm.elements(i).type=='text' || frm.elements(i).type=='hidden') {
      
      s=frm.elements[i].value;
      j=0;
      log=false;
      files=new Array();
      
      while(j<s.length) {
        if(s.charAt(j)=='<') log=true;
        else if(s.charAt(j)=='>') log=false;
        else if(log && (s.substr(j,8)=='file:///' || s.substr(j,2)==":\\")) {
          c=' ';
          if(s.substr(j,8)=='file:///') {}
          else j--; 
          if(s.charAt(j-1)=='"' || s.charAt(j-1)=="'") c=s.charAt(j-1);
          else if(s.charAt(j-1)==';') c='&';
          fil='';           
          while(i<s.length && s.charAt(j)!=c && s.charAt(j)!='>') {              
            fil+=s.charAt(j);
            j++;
          }
          fn+='|'+fil;
          files[files.length]=fil;
        }
        j++;
      }
      
   
      if(files.length>0) {
        for(j=0;j<files.length;j++) {
          ex='';
          x=files[j].length-1;
          while(x>0 && files[j].charAt(x)!='/' && files[j].charAt(x)!='\\') {
            ex=files[j].charAt(x)+ex;
            x--;
          }     
          s=s.replace(files[j],'/userfiles/edtr/'+EXT+'/'+ex);
          num++;
        }
        frm.elements[i].value=s;
        
      }       
    }
  }
  if(fn!='') {
    ParentW.document.getElementById('TmpDiv').innerHTML=EXT+fn;
	window.status='cmd:sendfiles';
    return true;
  }
  return false;
}