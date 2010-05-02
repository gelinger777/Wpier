var W=new iCommanderClass();//loadlib('iCommanderClass','xprogramms/filemanager/fm.js');
var win=W.MkWin();
win.show();

W.ResizeGrid();
var dd1=new Ext.dd.DropTarget(W.gr_l.container,W.ddcong(W.gr_l));
var dd2=new Ext.dd.DropTarget(W.gr_r.container,W.ddcong(W.gr_r));

win.tbbut=new Ext.Toolbar.Button({
  text:'sw-Commander',
  handler:function(){
    win.show();
  }
});
win.setAnimateTarget(win.tbbut);
TopToolbar.items.get('TopToolbarPanel').addButton(win.tbbut);