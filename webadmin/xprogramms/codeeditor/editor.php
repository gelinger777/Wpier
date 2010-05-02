<html>
<head>
<title>EditArea Test</title>
<script language="javascript" type="text/javascript" src="edit_area_full.js"></script>
<script language="javascript" type="text/javascript">

FileName="<?=(isset($_GET["file"])? $_GET["file"]:"")?>";

SvMess='Не найден объект для сохранения!\nВозможно, Вы уже закрыли окно с редактируемым элементом.\nСохранить код в файле?';
EditingElementId='';
//FileName="0:./2.php";

CONTENT="";
fn="html";
SYN_ASS={
 "php":"php",
 "phtml":"php",
 "css":"css",
 "js":"js",
 "htm":"html",
 "html":"html",
 "xhtm":"htm" 
}

if(FileName!="") {

  fn=FileName.split(":");
  fn[0]=parseInt(fn[0]);
  if(!isNaN(fn[0])) {
    fn[0]=parent.STORE_LOCATIONS[fn[0]][0];
    o=new Date();     
    url=fn[0]+(fn[0].indexOf('?')<0? '?':'&')+'getfile='+fn[1]+"&encode=Windows-1251&_dc="+o.getHours()+o.getMinutes()+o.getSeconds()+o.getMilliseconds();
    
	CONTENT=parent.AJAX.loadHTML_do(url);

    if(CONTENT!="") {
      fn=fn[1];
      i=fn.length-1;
      while(i>0 && fn.charAt(i)!=".") i--;
      if(i>0) {
        fn=fn.substr(i+1);
        eval("fn=SYN_ASS."+fn.toLowerCase());
        if(fn!=null && fn!="undefined") SYN=fn;
      }
    }
  }
}

editAreaLoader.init({
	id : "textarea_1"
	,syntax: fn
	,start_highlight: false
	,allow_toggle:false
	,allow_resize:'no'
	,language: 'ru'
	,toolbar:"new_document, save, search, go_to_line, undo, redo, highlight, reset_highlight, help"

    ,save_callback: "code_save"	
});

function code_save(id, content){
	if(EditingElementId!='') {		
		try{
			var w=window.etitElement.Win.document;
		} catch(e) {
			if(confirm(SvMess)) {
				window.etitElement=null;
				EditingElementId='';
				
			} else return false;
		} 
		if(EditingElementId!='') {
			if(window.etitElement.saveOut(EditingElementId,content)) return false;
			else 	if(confirm(SvMess)) {
				window.etitElement=null;
				EditingElementId='';				
			} else return false;
		}
  }
  if(FileName!="") {    
    parent.IO.SaveFile(FileName,content,function(e){alert('файл сохранен');});
    return true;
  }
  parent.IO.SaveFileDlg('Сохранить как',[['*.*','All formats (*.*)'],['*.css','CSS (*.css)'],["*.php","PHP (*.php)"],["*.htm","HTML (*.htm)"],["*.js","JScript (*.js)"]],function(fn){
    FileName=fn;
    parent.IO.SaveFile(fn,content,function(e){alert('файл сохранен');});     
  })
}
</script>
</head>
<body SCROLL="no" style="padding:0;margin:0"><textarea id="textarea_1" name="content" style="width:100%;height:100%"></textarea><SCRIPT LANGUAGE="JavaScript">
if(window.etitElement!=null )  { 
	EditingElementId=window.etitElement.CurrentElm.className;
	document.getElementById("textarea_1").value=window.etitElement.CurrentElm.innerHTML;
}
else if(CONTENT!="") document.getElementById("textarea_1").value=CONTENT;
</SCRIPT>
</body>
</html>
