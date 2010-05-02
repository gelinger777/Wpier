function MakeSelectorFolder(id,title,val,url,json_prop,json_arr,tpl,displayField,width,onselect) {

      var ds = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: url
        }),
        reader: new Ext.data.JsonReader(json_prop, json_arr)
    });

    // Custom rendering Template
    
	var resultTpl = new Ext.XTemplate(tpl);

    FORMFOLDS[FORMFOLDS.length]=new Ext.form.ComboBox({
		id:id,
		store: ds,
        displayField:displayField,
        typeAhead: false,
        loadingText: 'Searching...',
        width: width,
        pageSize:10,
        hideTrigger:true,
        tpl: resultTpl,
        //renderTo:'selector'+id,
        applyTo:id,
		itemSelector: 'div.search-item',
		value:val,
		minChars:1,
        onSelect:onselect
    });
  
 }