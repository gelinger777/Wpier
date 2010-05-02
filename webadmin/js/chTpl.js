tplCount=0;
function chTpl(id,TplID) {
  ParentW.AJAX.get('ajaxfunc',{changeprop:'tpl',t:'catalogue',id:id,val:TplID});
  window.location=window.location;
}