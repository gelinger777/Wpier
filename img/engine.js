Fx.Height = Fx.Style.extend({
	initialize: function(el, options){$(el).setStyle('overflow', 'hidden');this.parent(el, 'height', options);},
	toggle: function(){
		var style = this.element.getStyle('height').toInt();
		return this.start((style > 0) ? (style,0) : (0,this.element.scrollHeight));
	},
	show: function(){return this.set(this.element.scrollHeight);}
});

Fx.Opacity = Fx.Style.extend({
	initialize: function(el, options){this.now = 1;this.parent(el, 'opacity', options);},
	toggle: function(){return this.start((this.now > 0) ? (1,0) : (0,1));},
	show:function(){return this.set(1);}
});

window.addEvent("domready",function(){
	$$('.gk_news_block_table').each(function(el,i){
		var TID = el.getProperty('id');
		var OA = 1;
		
		if($E('.gk_news_block_panel',el)){
			$E('.gk_news_block_panel',el).setStyle('display','block');
			var panelSlider1 = new Fx.Opacity($E('.gk_news_block_panel',el), {duration: 500}).hide();
			var panelSlider2 = new Fx.Height($E('.gk_news_block_panel',el), {duration: 500}).hide();
		
			var PO = 2;
		
			if($E('.gk_news_block_panel_tools',el)){
				$E('.gk_news_block_panel_tools',el).addEvent('click',function(){
					PO++;
					$E('.gk_news_block_panel_tools',el).setHTML((PO%2 == 0) ? 'Show options' : 'Hide options');
					$E('.gk_news_block_panel_tools',el).toggleClass('gk_news_block_panel_tools_hidden');
					panelSlider1.toggle();
					panelSlider2.toggle();
				});
			}
			
			var fontS = (Cookie.get('gk_news_block_font'+TID)) ? Cookie.get('gk_news_block_font'+TID).toInt() : 20;
			
			if($E('.gk_news_block_panel_font_path',el)){
				var FontSizeSlider = new Slider($E('.gk_news_block_panel_font_path',el), $E('.gk_news_block_panel_font_knob',el), {
					steps: 70,
					onChange: function(step){
						$E('.gk_news_block_panel_font_value',el).setHTML((step+80)+'%');
						$(TID).getElementsBySelector('h4,img,p,a').setStyle('font-size',(step+80)+'%');
						Cookie.set('gk_news_block_font'+TID, step, {duration: 14,path: "/"});
					}
				}).set(fontS);
			}
		
			if($E('.gk_news_block_panel_amount_value',el)){
			
				var NC = ($A($(TID).getElementsBySelector('tr')[0].getElementsBySelector('td'))).length;
				var NR = (($A($(TID).getElementsBySelector('tr'))).length)-2;
				var NV = 0;
				//var NA = $A($$("#"+TID+" .gk_news_block_tablerow_bottom ul li")).length;

				$A(($(TID).getElementsBySelector('tr'))).each(function(el){(el.getStyle("display") == 'none') ? NV++ : NV = NV;});
			
				$E('.gk_news_block_panel_amount_value',el).setHTML(NR-NV);
		
				var list = $E('.gk_news_block_list_floated',el) || $E('.gk_news_block_list',el) || false;
		
				if(list){
					var listOfLi = list.getElementsBySelector('li');
					var amountOfLi = listOfLi.length;
				}
		
				$E('.gk_news_block_panel_amount_minus',el).addEvent('click',function(){
					if(NV < NR){
						$(TID).getElementsBySelector('tr')[(NR-NV)-1].setStyle('display','none');
						NV++;
						$E('.gk_news_block_panel_amount_value',el).setHTML(NR-NV);
						Cookie.set('gk_news_block_amount'+TID, (NR-NV), {duration: 14,path: "/"});
						
						if(list){
							for(var l=0;l<NC;l++){
							if((((NR-NV)*NC)+l >= 0) && (((NR-NV)*NC)+l < amountOfLi)) listOfLi[((NR-NV)*NC)+l].setStyle('display','block');
							}
						}
					}
				});
		
				$E('.gk_news_block_panel_amount_plus',el).addEvent('click',function(){
					if(NV > 0){
						$(TID).getElementsBySelector('tr')[(NR-NV)].setStyle('display','');
						NV--;
						$E('.gk_news_block_panel_amount_value',el).setHTML(NR-NV);
						Cookie.set('gk_news_block_amount'+TID, (NR-NV), {duration: 14,path: "/"});
						
						if(list){
							for(var k=0;k<NC;k++){
								if(((NR-NV)*NC)-(1+k) < amountOfLi) listOfLi[((NR-NV)*NC)-(1+k)].setStyle('display','none');
							}
						}
					}
				});
			}
		}
	});
});