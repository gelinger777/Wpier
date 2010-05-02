/* 
Derived from a script by Dziudek. 
Author: GavickPro <support@gavick.com>     
*/
window.addEvent("domready", function(){
	
	var gk_blocks_margin_tab = [0,8,8,8,8,8];
	
	for(var j=1;j<6;j++){
		if($("user_position-"+j)){ 
			var gk_blocks_parent_width = $("user_position-"+j).getSize().size.x; 
			var gk_blocks_margin_width = gk_blocks_margin_tab[j]; 
			var gk_blocks_array = []; 
			
			var i = 0; 
			if($E("div.us_"+j+"-left","user_position-"+j)){gk_blocks_array[i] = $E("div.us_"+j+"-left","user_position-"+j);i++;}
			if($E("div.us_"+j+"-center")){gk_blocks_array[i] = $E("div.us_"+j+"-center","user_position-"+j);i++;}
			if($E("div.us_"+j+"-right","user_position-"+j)){gk_blocks_array[i] = $E("div.us_"+j+"-right","user_position-"+j);i++;}
			
			var gk_blocks_width = (( gk_blocks_parent_width - ( gk_blocks_margin_width * (i-1) ) ) / i);
		
			gk_blocks_array.each(function(element,index){element.setStyle("width", gk_blocks_width + "px");
				if(index < (i-1)){element.setStyle("margin-right", gk_blocks_margin_width + "px");}});
		}
	}
});

