var w=new Ext.Window({
      title:'Local Store Admin',
      shadow:false,
	  id:'LfFiles',
      layout:'border',
      items: [{
		 html:'<iframe src="http://127.0.0.1:3232/admin/" width="100%" height="100%" frameborder="0"></iframe>' 
	  }],
      width:350,
      height:300,
      minimizable:false,
      maximizable:false,
      plain: false,
      resizable:true,
      allowDomMove:false,
      bufferResize:100,
      buttons:[{        
        text:'Принять изменения',
        handler:function() {
         AJAX.sendScriptQuery('http://127.0.0.1:3232/?getstores');
        }
      },{        
        text:'Закрыть',
        handler:function() {
         this.close();
        }
      }]       
    });
w.show();