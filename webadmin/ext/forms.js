
st_inner="";
currentField="";
currentModName="";
currentBlockID=0;
currentPageID=0;

function setParentBlock(id,pg) {
	currentBlockID=id;
	currentPageID=pg;
}

function retCurrentField(){
	return currentField;
 }	


function checkPage(table,id,ext) {

        if(ParentW!=null) {
		ParentW.connecttabsnew(table,id,ext);
		
	}
}

selIDRow=0;
selElmROw=0;
selElmClass="";
startPosRow=0;
wLocat="";
function mvRw1(elm,UDid,UDPid) {
	lc=ParentW.mvRw(wLocat, elm,UDid,UDPid,startPosRow);
	if(lc!=0) window.location=lc; // && confirm('Переместить запись?')
}


function ChangeOption(elm) {
	try
	{
		if(DEPEND_ARRAY!=null && DEPEND_ARRAY.length>0) DependFunction(elm);
	}
	catch (e)
	{
	}
	
}

function in_array(x,ar) {
	for(var i=0;i<ar.length;i++) if(ar[i]==x) return true;
	return false;
}


CopyBuffer=new Array();
function AddToBuffer(modname,buff) {
  for(var i=0;i<CopyBuffer.length;i++) {
    if(CopyBuffer[i][0]==modname) {
      CopyBuffer[i][1]=buff;
      return true;
    }
  }
  CopyBuffer[CopyBuffer.length]=new Array(modname,buff);
}

function CheckBuffer(modname) {
  for(var i=0;i<CopyBuffer.length;i++) {
    if(CopyBuffer[i][0]==modname) {
      return CopyBuffer[i][1];
    }
  }
  return false;
}

function CheckPasteButton(modname) {
  if (document.getElementById('pastebutton')==null) return false;
  if(ParentW.CheckBuffer(modname)) {
    document.getElementById('pastebutton').parentNode.style.display="";
  } 
}

function MakeSignature(mod,id) {
  if(confirm("Подписать для публикации?")) window.location="?ext="+mod+"&ch="+id+"&sign=yes";
}

function CheckFile(e,dir) {
  var x=e.value;
  if(x!='') {
    x=x.split("\\");
    x=x[x.length-1];
    
    Ext.Ajax.request({
      url: './inc/check_img.php?f='+dir+x,
      success: function(response) {
        if(response.responseText=="1") {
          alert('Такой файл уже есть на сервере. Если сохраните текущую запись, файл будет заменен.'); 
          return false;
        }
      }
    });
  }
  return true;
}

function edit_txt (field,wi) {
	 if( ParentW!=null) {
                ParentW.EditTextInEditor(document.getElementById(field),document.getElementById("div_"+field));
         }

} 

function addModuleToEditor(id) {
		//document.frames["editorframe"].document.all("editor").focus();
		//document.frames["editorframe"].insCode2Carret("<img ID=BLOCK_"+id+" width=150 height=45 src='/webadmin/img/mod.gif' alt='"+currentModName+"'>");
		//currentModName="";
}