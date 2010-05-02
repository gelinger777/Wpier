Ext.ux.grid.filter.ComboFilter = Ext.extend(Ext.ux.grid.filter.Filter, {
	labelField:  'text',
	loadingText: 'Loading...',
	loadOnShow:  true,
	value:       [],
	loaded:      false,
	phpMode:     false,
	width:       180,
	icon: './ext/img/find.png',
	
	init: function(){
		this.menu.add('<span class="loading-indicator">' + this.loadingText + '</span>');
		
		if(this.store){
			if(this.loadOnShow)
				this.menu.on('show', this.onMenuLoad, this);
			
		} else if(this.options){
			var options = [];
			for(var i=0, len=this.options.length; i<len; i++) {
				var value = this.options[i];
				switch(Ext.type(value)){
					case 'array':  options.push(value); break;
					case 'object': options.push([value.id, value[this.displayField]]); break;
					case 'string': options.push([value, value]); break;
				}
			}
	
	
			
			this.store = new Ext.data.SimpleStore({
							fields: ['id','val'],
							data : options
			});
			
			this.loaded = true;
			
		}
		
		this.onLoad();
		
		this.bindShowAdapter();
	},
	
	/**
	 * Lists will initially show a 'loading' item while the data is retrieved from the store. In some cases the
	 * loaded data will result in a list that goes off the screen to the right (as placement calculations were done
	 * with the loading item). This adaptor will allow show to be called with no arguments to show with the previous
	 * arguments and thusly recalculate the width and potentially hang the menu from the left.
	 * 
	 */
	bindShowAdapter: function(){
		var oShow    = this.menu.show;
		var lastArgs = null;
		this.menu.show = function(){
			if(arguments.length == 0){
				oShow.apply(this, lastArgs);
			} else {
				lastArgs = arguments;
				oShow.apply(this, arguments);
			}
		};
	},
	
	onMenuLoad: function(){
		if(!this.loaded){
			if(this.options)
				this.store.loadData(this.options);
			else
				this.store.load();
		}
	},
	
	onLoad: function(store, records) {

		var visible = this.menu.isVisible();
		this.menu.hide(false);
		this.menu.removeAll();
		var ww=this.width;
		var item = new Ext.menu.Adapter(new Ext.form.ComboBox({
				store: this.store,
				typeAhead: true,
				name:'combofilter',
				mode: 'local',
				triggerAction: 'all',
				listClass: 'x-combo-list-small',
				displayField:'val',
				valueField: 'id',
				hideOnClick: false,
				width:ww,
				listeners:{
					expand:function(el) {
						// Отключаем реакцию на клик при скроллинге листа
						// что бы не скрывалось остальное меню
						var ic=Ext.query("*[class=x-combo-list-inner]");
						for(var i=0;i<ic.length;i++) ic[i].onmousedown=function(event) {
							if(Ext.isIE) window.event.cancelBubble=true;
							else event.stopPropagation();
						}			
					}
				}
			}),{ hideOnClick: false });


		item.component.on('select', this.checkChange, this);
	
		this.combo = this.menu.add(item);

		
		this.setActive(this.isActivatable());
		this.loaded = true;

		if(visible) this.menu.show();

	},
	
	
	checkChange: function(item, checked) {
		this.value = [checked.data.id];//this.getValue();
		this.setActive(this.isActivatable());	
		this.fireEvent("update", this);
	},
	
	
	isActivatable: function(){
		return this.value.length > 0;
	},
	

	getValue: function(){
		return this.combo.component.getValue();
	},
	
	serialize: function(){
		var args = {type: 'combo', value: this.getValue()};
		this.fireEvent('serialize', args, this);
		return args;
	},
	
	validateRecord: function(record){
		return this.getValue().indexOf(record.get(this.dataIndex)) > -1;
	}
});