ParentW=null;
p=parent;
i=0;
while(p!=null && i<10) {
  if(p.document.getElementById('IndexElement')!=null) {
    ParentW=p;
    break;
  }
  p=p.parent;
  i++;
}

try{ Ext.BLANK_IMAGE_URL='./s.gif';}catch(e){}