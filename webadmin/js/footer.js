function sendform() {
	f=document.forms[0].elements;
	for(i=0;i<multi_select.length;i++) {
		for (j=0;j<f[multi_select[i]+"[]"].options.length;j++){
			f[multi_select[i]+"[]"].options[j].selected=true;
		}
	}
	try {
      UserSendFunction()
    }
    catch(e) {	}	
	return true;
}

function mk_select(tab, fldkey, fldval, elm, whr) {
	if(whr==null) whr="";
	if (event.keyCode==0 || event.keyCode==13) {
		window.frames["operateframe"].navigate("./make_select.php?whr="+whr+"&tab="+tab+"&fldkey="+fldkey+"&fldval="+fldval+"&elm="+elm+"&search=" + document.forms[0].elements[elm+"_text"].value);
		loadprocess.style.display="";
		return false;
	} 	
}

function add_elm(sel,key,val) {
	oOption = document.createElement("OPTION");
	oOption.text=val;
	oOption.value=key;
	sel.add(oOption);
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

 function add_opt(elm, cVal, cTxt) {	
	
	if(document.getElementById(elm)==null) return false;

	elm=document.getElementById(elm);

	for(iV=0;iV<elm.length;iV++) {
		if(elm[iV].value==cVal && elm[iV].value!="") return true;
	}
	
	oOption = document.createElement("OPTION");
	oOption.text=cTxt;
	oOption.value=cVal;
	

	elm.options.add(oOption);
	//elm.(oOption);
	//alert(v+" "+v1)
}