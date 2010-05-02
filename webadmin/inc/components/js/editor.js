function MakeEditorFolder(id,title,w,h) {
  if(w<530) w=530;
  var x=document.getElementById('editor'+id).innerHTML;
  document.getElementById('editor'+id).innerHTML='';
  FORMFOLDS[FORMFOLDS.length]=new Ext.form.HtmlEditor({       
    name:id,
    renderTo:'editor'+id,
    fieldLabel:title,
    height:parseInt(h),
    width:parseInt(w),
    //enableFont:false,
    value:x,
    anchor:'98%',
    listeners:{editmodechange:function(edt,mode) {
                                edt.curmode=mode;
                              }
     }
  });
}