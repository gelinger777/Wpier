
function mk_select(keyCode, tab, fldkey, fldval, elm, whr) {
  if(whr==null) whr="";
  if (keyCode==0 || keyCode==13) {
    var s="./make_select.php?whr="+whr+"&tab="+tab+"&fldkey="+fldkey+"&fldval="+fldval+"&elm="+elm+"&search=" + document.forms[0].elements[elm+"_text"].value;   
    document.getElementById("operateframe").src=s;
    document.getElementById("loadprocess").style.display="";   
    return false;
  }   
}

function add_elm(sel,key,val) {
  oOption = document.createElement("OPTION");
  oOption.text=val;
  oOption.value=key;
  sel.options.add(oOption);
}

function move_mult(elm) {
  sel=document.forms[0].elements[elm+"[]"]
  s1=document.forms[0].elements[elm+'_ch'];
  for (i=0;i<s1.options.length;i++) {
    if (s1.options[i].selected) {
      add_elm(sel,s1.options[i].value,s1.options[i].text);
      s1.remove(s1.options[i].index);
      i--;
    }
  }
}

function remove_mult(elm) {
  sel=document.forms[0].elements[elm+"[]"];
  s1=document.forms[0].elements[elm+'_ch'];
  for (i=0;i<sel.options.length;i++)
    if (sel.options[i].selected) {
      add_elm(s1,sel.options[i].value,sel.options[i].text);
      sel.remove(sel.options[i].index);
      i--;
    }
}

function checkSpell() {
  out="";
  for(i=0;i<document.forms[0].elements.length;i++) {
    if(document.forms[0].elements[i].type=="text" || document.forms[0].elements[i].type=="textarea" || document.forms[0].elements[i].type=="hidden") {
      out+=" "+document.forms[0].elements[i].value;
    }
  }
  if(out) {
    spellWait.style.left=document.body.offsetWidth/2-125;
    spellWait.style.top=document.body.scrollTop + document.body.offsetHeight/2-25;
    spellWait.style.display="";
    window.frames["orfoFrame"].document.all("checktxt").value=out;
    window.frames["orfoFrame"].document.forms[0].submit();
  }
}
spellX=0;
 
 function promptError(wERR) {
  return window.showModalDialog("./checkwords.php",wERR,"dialogWidth:330px;dialogHeight:120px;resizable:0;status:0");
 }
 
 function getSpellError(serr) {
  spellWait.style.display="none";
  if(serr!="") {serr=serr.split(",");
    for(ig=0;ig<serr.length;ig++) {
      w=promptError(serr[ig]);
      if(w!=serr[ig] && w!=null && w!="") {
        checkError(serr[ig],w)
      }
    }
  }
  if(spellX==1) document.forms[0].submit();
 }

function str_replace(r,s,st) {
  st=st.split(r);
  return st.join(s);
}

 function checkError(w1,w2) {
  if(document.all("editor")!=null) {
    document.all("editor").innerHTML=str_replace(w1,w2,document.all("editor").innerHTML);
  } 

  for(i=0;i<document.forms[0].elements.length;i++) {
    if(document.forms[0].elements[i].type=="text" || document.forms[0].elements[i].type=="textarea" || document.forms[0].elements[i].type=="hidden") {
      document.forms[0].elements[i].value=str_replace(w1,w2,document.forms[0].elements[i].value);
      if(document.all("div_"+document.forms[0].elements[i].name)!=null) {
        document.all("div_"+document.forms[0].elements[i].name).innerHTML=str_replace(w1,w2,document.all("div_"+document.forms[0].elements[i].name).innerHTML);
      }
    }
  }  
 }

 function add_opt(elm, cVal, cTxt) {
  var elm=document.getElementById(elm); 
  if(elm==null) return false;

  for(iV=0;iV<elm.length;iV++) {
    if(elm[iV].value==cVal && elm[iV].value!="") return true;
  }
  
  oOption = document.createElement("OPTION");
  oOption.text=cTxt;
  oOption.value=cVal;
  elm.options.add(oOption);
  //alert(v+" "+v1)
}