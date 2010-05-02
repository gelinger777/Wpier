IO.AddStoresComplit=false;
AJAX.sendScriptQuery("http://127.0.0.1:3232/?getstores=");

window.setTimeout(function(){
  if(IO.AddStoresComplit) {
     var w = new Ext.Window({
         id:"ProgWin",
         title:"Администратор локальных Store",
         layout:"fit",
         width:600,
         height:300,
         minimizable:false,
         maximizable:false,
         plain: true,       
         items:[{html:"<iframe src='http://127.0.0.1:3232/admin/' width='100%' height='100%' frameborder='0' scrolling='no'></iframe>"}],
         buttons:[
	     {
			 text:"Применить",
			 handler:function() {AJAX.sendScriptQuery("http://127.0.0.1:3232/?getstores");}
		 },
		{
			 text:"Закрыть",
			 handler:function() {w.close();}
		}]
        
       });
    w.show();
  } else {
	  DLG.e('Сервер локальных Store не запущен!<br><a href="">Подробнее о сервере локальных Store</a>');	
  }
  
},1000);