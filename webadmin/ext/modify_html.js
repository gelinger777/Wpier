/////////////////////////////////////////////////////////////
//
//  Привеодим html к стилю сайта
//  (c) 2009 Maxim Tushev
//
//////////////////////////////////////////////////////////////

// При преобразовании таблицы назначаются следующие классы (нужно их определить в CSS)
// innertablestyle - стиль всей таблицы
// trhead - стиль заголовочных ячеек
// trdark - в чередовании ячеек темная полоса
// trlight - в чередовании ячеек светлая полоса

modify_html=new function() {
	
	// перечисляем тэги, которые не удаляем
	// название тэга, удалять/не удалять атрибуты, удалять/не удалять стили
	this.tags=[
		["p",true,true],
		["a",false,true],
		["ul",true,true],
		["ol",true,true],
		["li",true,true],
		["h1",true,true],
		["h2",true,true],
		["h3",true,true],
		["h4",true,true],
		["img",false,true],
		["br",false,false],
		["b",true,true],
		["strong",true,true],
		["i",true,true],
		["em",true,true],
		["u",true,true],		
		["table",true,true],
		["tr",true,true],
		["td",false,true],
		["span",true,false]
	];

	this.tablines=false; // Если указана эта настройка, в таблицах добавляется декоративные линии под заголовком и внизу таблицы

	this.modif=function(s) {
		var x,ad,os="";
		var tc,j,n,i=0;
		
		//if(s.toLowerCase().indexOf('<table')!=-1) 	s=this.table(s);
		while(i<s.length) {
			
			if(s.substr(i,4)=='<!--') { // Оставим все комментари
				while(i<s.length && s.substr(i,3)!='-->') os+=s.charAt(i++);
				os+="-->";
				i+=3;
			} else
			
			if(s.substr(i,5)=='<xml>') { // удалим все вордовские <xml>
				i+=5;
				while(i<s.length && s.substr(i,6)!='</xml>') i++;
				i+=6;
			} else if(s.substr(i,7)=='<style>') { // удалим все вордовские <xml>
				i+=7;
				while(i<s.length && s.substr(i,8)!='</style>') i++;
				i+=8;			
			} else if(s.charAt(i)=='<') {
				j=i+1;
				if(s.charAt(j)=='/') {
					j++;
					tc=true;
				} else tc=false;
				n=j;
				while(n<s.length && s.charAt(n)!=">") n++;
				x=s.substr(j,(n-j));
				x=x.replace("\n"," ");
				x=x+" ";
				ad="";
				i=n;
				x=x.toLowerCase();
				for(n=0;n<this.tags.length;n++) {
					if(x.indexOf(this.tags[n][0]+" ")==0) {
						ad=this.gettag(n,x);
						break;
					}
				}
				if(ad!='') {
					os+="<"+(tc? "/":"")+ad+">";
				}
				i++;
			} else {
				os+=s.charAt(i++);
			}
			
		}
		return os;
	}

    // преобразуем тэг в зависимости от настроек
	this.gettag=function(n,x) {
		var i,log;

       var os=this.tags[n][0]+' ';
       
		// удаляем атрибуты
		if(this.tags[n][1]) {
			i=this.tags[n][0].length+1;
			while(i<x.length) {
				if(x.substr(i,7)=='style="') {
					os+='style="';
					i+=7;
					while(i<x.length && x.charAt(i)!='"') os+= x.charAt(i++);
					os+='"';					
				} 
				i++;				
			}
		} else {
			os=x;
		}
		// удаляем стили
		x="";
		if(this.tags[n][2]) {
			i=os.indexOf('style="');
			if(i>-1) {
				x=os.substr(0,i);
				i+=7;
				while(i<os.length && os.charAt(i)!='"') i++;
				x+=os.substr(i+1);
			} else x=os;
		} else {
			x=os;
		}
		return x;
	}
 
	this.table=function(s) {
	  if (s!='') { 
		var i=0;
		var lt=false;
		var ls=false;
		var x="";
		var log=true;
		var c="";
		var colspans;
		var j;
		var ss;
		var y;
		var yestab=false;
		while(i<s.length) {
		  if(yestab && !log) {
			if(s.charAt(i)==c) log=true;
			else if(s.charAt(i)=='>') {
			  log=true;
			  x+='>';          
			}
			i++;
		  } else if(yestab && s.substr(i,7)=='style="') {
			log=false;
			i+=7;
			c='"';
		  } else if(yestab && s.substr(i,13)=='<P>&nbsp;</P>') { 
			i+=13;
		  } else if(yestab && s.substr(i,6)=='width=') {
			log=false;
			i+=6;
			c=' ';
		  } else if(yestab && s.substr(i,6)=='class=') {
			log=false;
			i+=6;
			c=' ';
		  } else if(s.substr(i,6).toUpperCase( )=='<TABLE') {
			x+='<TABLE class=innertablestyle width="90%" cellpadding="10"';
			i+=6;
			lt=true;
			colspans=0;
			yestab=true;
		  } else if(s.substr(i,8).toUpperCase( )=='</TABLE>') {
			lt=false;
			if(this.tablines)
				x+='<TR class="bluerow"><td colspan="'+colspans+'"><div style="height: 4;"><spacer type="block" height="1" width="1"></div></td></TR>';
			else
				x+='</TABLE>';
			i+=8;
			yestab=false;
		  } else if(yestab && lt && s.substr(i,5).toUpperCase( )=='</TR>') {
			lt=false;
			ls=false;
			x+='</TR>';
			if(this.tablines)
				x+='<TR class="bluerow"><td colspan="'+colspans+'"><div style="height: 4;"><spacer type="block" height="1" width="1"></div></td></TR>';
			i+=5;
		  } else if(yestab && lt && s.substr(i,3).toUpperCase( )=='<TD') {
			ss='';
			i+=3;
			j=i;
			x+='<TD';
			while(j<s.length && s.charAt(j)!='>') {
			  ss+=s.charAt(j);
			  j++;
			}
			ss=ss.split('colSpan=');
			if(ss.length>1) {
			  y=parseInt(ss[1]);
			  if(isNaN(y)) colspans++;
			  else colspans+=y;
			} else {
			  colspans++;
			}
		  } else if(yestab && s.substr(i,3).toUpperCase( )=='<TR') {
			if(lt) {
			  x+='<TR class=trhead';
			  i+=3;
						
			} else if(ls) {
			  x+='<TR class=trlight';
			  i+=3;
			  ls=false;
			} else {
			  x+='<TR class=trdark';
			  i+=3;
			  ls=true;
			}
		  } else {
			x+=s.charAt(i);
			i++;
		  }
		}
		return x;     
	  }
	}
}
