window.addEvent("load",function(){
	gk_mv_scroller_vars = {};
	
	$$('.gk_vm_scroller_container_1').each(function(el){
		// spaces beetween blocks
		var space = 8;
		
		gk_mv_scroller_vars.actual = 0;
		
		gk_mv_scroller_vars.tableOfDivs1 = $E('.gk_vm_scroller_container_3',el).getElementsBySelector('div.gk_vm_product_block');
		gk_mv_scroller_vars.amountOfDivs1 = gk_mv_scroller_vars.tableOfDivs1.length;
		
		// animation
		
		if(gk_mv_scroller_vars.amountOfDivs1 > 3){
		
			gk_mv_scroller_vars.visibleDivs = (($E('.gk_vm_scroller_container_2',el).getSize().size.x)/(gk_mv_scroller_vars.tableOfDivs1[0].getSize().size.x)).round(0);
		
			gk_mv_scroller_vars.tableOfDivs2 = [];
		
			for(var i=0;i<gk_mv_scroller_vars.visibleDivs;i++){
				gk_mv_scroller_vars.tableOfDivs2[i] = gk_mv_scroller_vars.tableOfDivs1[(gk_mv_scroller_vars.amountOfDivs1-gk_mv_scroller_vars.visibleDivs)+i].clone();
				gk_mv_scroller_vars.tableOfDivs2[i].injectBefore(gk_mv_scroller_vars.tableOfDivs1[0]);
			}
		
			var j = 0;
		
			for(var i=gk_mv_scroller_vars.visibleDivs;i<(gk_mv_scroller_vars.amountOfDivs1+gk_mv_scroller_vars.visibleDivs);i++){
				gk_mv_scroller_vars.tableOfDivs2[i] = gk_mv_scroller_vars.tableOfDivs1[j];
				j++;
			}
		
			var k = 0;
		
			for(var i=(gk_mv_scroller_vars.visibleDivs+gk_mv_scroller_vars.amountOfDivs1);i<(gk_mv_scroller_vars.visibleDivs+(gk_mv_scroller_vars.visibleDivs+gk_mv_scroller_vars.amountOfDivs1));i++){
				gk_mv_scroller_vars.tableOfDivs2[i] = gk_mv_scroller_vars.tableOfDivs1[k].clone();
				gk_mv_scroller_vars.tableOfDivs2[i].injectAfter(gk_mv_scroller_vars.tableOfDivs2[(i-1)]);
				k++;
			}
		
			gk_mv_scroller_vars.jumpLeft = 0;
			gk_mv_scroller_vars.jumpRight = (gk_mv_scroller_vars.tableOfDivs2.length - 1);
		
			gk_mv_scroller_vars.jumpLeftWhere = gk_mv_scroller_vars.amountOfDivs1;
			gk_mv_scroller_vars.jumpRightWhere = gk_mv_scroller_vars.visibleDivs;
		
			gk_mv_scroller_vars.startWidth = $E('.gk_vm_scroller_container_3',el).getStyle('width').toInt();
			$E('.gk_vm_scroller_container_3',el).setStyle('width',(gk_mv_scroller_vars.startWidth + ((gk_mv_scroller_vars.visibleDivs*2)*(gk_mv_scroller_vars.startWidth/gk_mv_scroller_vars.amountOfDivs1)) + (space * (gk_mv_scroller_vars.tableOfDivs2.length-1)))+'px');
		
			gk_mv_scroller_vars.ScrollingFast = new Fx.Scroll($E('.gk_vm_scroller_container_2',el),{duration: 0});
		
			gk_mv_scroller_vars.ScrollingFast.toElement(gk_mv_scroller_vars.tableOfDivs2[gk_mv_scroller_vars.visibleDivs]);
			gk_mv_scroller_vars.actual = gk_mv_scroller_vars.visibleDivs;
		
			gk_mv_scroller_vars.direct = 'right';
		
			gk_mv_scroller_vars.ScrollingNormal = new Fx.Scroll($E('.gk_vm_scroller_container_2',el),{transition: Fx.Transitions.Expo.easeOut,duration: 750});
		
			$E('.gk_vm_scroller_left', el).addEvent("mouseover",function(){gk_mv_scroller_vars.direct = 'left';});
			$E('.gk_vm_scroller_right', el).addEvent("mouseover",function(){gk_mv_scroller_vars.direct = 'right';});
			
			(function(){(gk_mv_scroller_vars.direct == 'left')?jumpLeft():jumpRight();}).periodical(2500);
			
			// reseting margins
			el.getElementsBySelector('.gk_vm_product_block')[0].setStyle("margin-left","");
			
			for(var f=1;f<gk_mv_scroller_vars.tableOfDivs2.length;f++){
				el.getElementsBySelector('.gk_vm_product_block')[f].setStyle("margin-left",space+'px');
			}
			
			new Fx.Scroll($E('.gk_vm_scroller_container_2',el),{duration: 0}).toElement(gk_mv_scroller_vars.tableOfDivs2[gk_mv_scroller_vars.visibleDivs]);
		}
	});
});

// jump
function jumpLeft(){	
	gk_mv_scroller_vars.actual--;
			
	if(gk_mv_scroller_vars.actual == gk_mv_scroller_vars.jumpLeft){
		gk_mv_scroller_vars.ScrollingNormal.toElement(gk_mv_scroller_vars.tableOfDivs2[gk_mv_scroller_vars.actual]);
		(function(){gk_mv_scroller_vars.ScrollingFast.toElement(gk_mv_scroller_vars.tableOfDivs2[gk_mv_scroller_vars.jumpLeftWhere]);}).delay(800);
		gk_mv_scroller_vars.actual = gk_mv_scroller_vars.jumpLeftWhere;
	}
	else{
		gk_mv_scroller_vars.ScrollingNormal.toElement(gk_mv_scroller_vars.tableOfDivs2[gk_mv_scroller_vars.actual]);
	}
}

function jumpRight(){			
	gk_mv_scroller_vars.actual++;
			
	if(gk_mv_scroller_vars.actual == (gk_mv_scroller_vars.jumpRight-(gk_mv_scroller_vars.visibleDivs-1))){
		gk_mv_scroller_vars.ScrollingNormal.toElement(gk_mv_scroller_vars.tableOfDivs2[gk_mv_scroller_vars.actual]);
		(function(){gk_mv_scroller_vars.ScrollingFast.toElement(gk_mv_scroller_vars.tableOfDivs2[gk_mv_scroller_vars.jumpRightWhere]);}).delay(800);
		gk_mv_scroller_vars.actual = gk_mv_scroller_vars.visibleDivs;
	}
	else{
		gk_mv_scroller_vars.ScrollingNormal.toElement(gk_mv_scroller_vars.tableOfDivs2[gk_mv_scroller_vars.actual]);
	}
}