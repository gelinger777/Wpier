// Подключение справочной системы
// Ext.EventManager.on(document.body, 'mouseup', handleBodyClick);
// строку нужно подключить в инициализацию EXT


function handleBodyClick(e) {  
  if(ParentW.HELP.active()) {
    var p=new Array();
    var o=e.target;
    
    //t=new print_r(o,'o');
    
    while(o!=null) {
      if(o.id=='helpPort') return false;
      if(o.id!='') p[p.length]=o.id;
      o=o.parentNode;
    }
    if(e.ctrlKey && e.altKey) {
      ParentW.HELP.create(HELPMOD+'|'+p.join('|'),document);
      return false;
    }else ParentW.HELP.show(HELPMOD+'|'+p.join('|'));
  }
}