id_timeout=0;
dot=":";
clocktype=0;
currentItem="td_struct";
mshow="";




function chmainitem(elm) {
	td_struct.className="topmenuoff";
	td_spec.className="topmenuoff";
	td_set.className="topmenuoff";
	td_struct_con.className="tdconoff";
	td_spec_con.className="tdconoff";
	td_set_con.className="tdconoff";
	
	if(elm==1) return false;
		
	leftdiv.style.display="";	
	if(window.frames["editorframe"].document.all("toptools")!=null)
		window.frames["editorframe"].document.all("toptools").style.left=1;
	cur=document.all(elm);
	cur1=document.all(elm+"_con");

	struct_div.style.display="none";
	spec_div.style.display="none";
	sys_div.style.display="none";
	
	cur.className="topmenuon"
	cur1.className="tdconon"

	if(elm=="td_struct") {currentItem=elm;struct_div.style.display="";return true;}
	if(elm=="td_spec") {currentItem=elm;spec_div.style.display="";return true;}
	if(elm=="td_set") {currentItem=elm;sys_div.style.display="";return true;}
		
}

function chclocktype() {
	if(clocktype==0) clocktype=1;
	else clocktype=0;
}

function clock() {
	sec++;
	if(dot==":") dot="<b>:</b>";else dot=":";
	if(sec==60) {
		min++;
		sec=0;
	}
	if(min==60) {
		hour++;
		min=0;
	}
	if(hour==24) hour=0;
	if(sec<10) ss="0"+sec;else ss=sec;
	if(min<10) mm="0"+min;else mm=min;
	if(hour<10) hh="0"+hour;else hh=hour;
	if(clocktype==0) time.innerHTML=hh+dot+mm;
	else time.innerHTML=hh+":"+mm+":"+ss;
	window.clearTimeout(id_timeout);
	window.setTimeout('clock()', 1000);
}	
specYes=0;
function showSpecDiv() {
	if(document.all("specdiv").style.display!="none") {
		specYes=1;
		document.all("specdiv").style.display="none"
	} else specYes=0;
	document.frames["specBlkframe"].navigate("./specblk.php");
	document.all('specBlkdiv').style.display="";
	document.all('editordiv').style.display="none";
	document.all('mainfrimediv').style.display="none";
}
function hideSpecDiv() {
	if(specYes==1) document.all("specdiv").style.display=""
	document.all('specBlkdiv').style.display="none";
	document.all('editordiv').style.display="none";
	document.all('mainfrimediv').style.display="";
}
function chSelectedIndx(xxx) {
	if(window.frames["mainframe"].document.all("spec")!=null) {
		f=window.frames["mainframe"].document.all("spec");
		for(iS=0;iS<f.options.length;iS++) {
			if(f.options[iS].value==xxx) {
				f.selectedIndex=iS;
				return 1;
			}
		}
	}
}

selIDRow=0;
selElmROw=0;
selElmClass="";
selPageLocation="";
function mvRw(wLocation, elm,UDid,UDPid,startPosRow) {
	if(UDid==selIDRow && selElmClass!="") {
		elm.className=selElmClass;
		selIDRow=0;
		selElmROw=0;
		selElmClass="";
		selPageLocation="";
		return 0;
	} 
	
	if(selIDRow==0) {
		selIDRow=UDid;
		selElmROw=elm;
		selElmClass=elm.className;
		selPageLocation=wLocation;
		elm.className="td_selrow";
		return 0;
	} else {
	        if(selIDRow==UDid) {
                  s=0;
		} else {
                  s=selPageLocation+"?start="+startPosRow+"&idprev="+selIDRow+"&id="+UDid+"&aft="+UDPid;	
                }
                elm.className=selElmClass;
		selIDRow=0;
		selElmROw=0;
		selElmClass="";
		selPageLocation="";
		return s;
	}
}

function hideShowSpc() {

	if(specdivF.style.display=="none") {
		specdiv.style.height=document.body.clientHeight-115;
		specdivF.style.display="";
	} else {
		specdiv.style.height="15";
		specdivF.style.display="none";
	}
}

CurrentPageID=0;
function SetCurrentPgCode(id) {
	CurrentPageID=id;
}

function RequireSignature(mod,id) {
  document.all('SignatureTD').innerHTML='<IMG SRC="./img/sign_a.gif" WIDTH="15" HEIGHT="15" BORDER=0 ALT="требуется подписать электронный документ" style="cursor:hand" onclick="document.frames[\'mainframe\'].navigate(\'./readext.php?ext='+mod+'&ch='+id+'\')">';
}

function NoSignature() {
  document.all('SignatureTD').innerHTML='<IMG SRC="./img/sign.gif" WIDTH="15" HEIGHT="15" BORDER=0 ALT="подписи не требуется">';
}