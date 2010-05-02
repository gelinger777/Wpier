function addRowEditList(tabN) {
  var el=document.getElementById(tabN);
  var ht=el.lastChild.lastChild.cloneNode(true);
  ht.style.display="";
  el.lastChild.lastChild.parentNode.insertBefore(ht,el.lastChild.lastChild);//,el.lastChild.lastChild)
  //el.lastChild.lastChild.insertAdjacentElement("beforeBegin",ht);
  return false;
}

function delRowEditList(o) {
  o.parentNode.parentNode.parentNode.removeChild(o.parentNode.parentNode);
  //o.parentNode.parentNode.removeNode(true);
  return false;
}

function upRowEditList(o) {
  if(o.parentNode.parentNode.previousSibling.id!='EditListHead') {
    var o1=o.parentNode.parentNode;
    var o2=o.parentNode.parentNode.previousSibling;
    var ht=o1.cloneNode(true);
    o1.parentNode.removeChild(o1);
    o2.parentNode.insertBefore(ht,o2);
    //o1.swapNode(o2);
     
  }
  return false;
}