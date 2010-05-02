// Регистрируем метод, для запуска редактора при редактировании текста
EDITOR.editfolder=function(table,folder,id,title) {
  App.run('xprogramms/tiny_mce/editor.php?editfolder='+folder+'&t='+table+'&id='+id,title+' - TinyMCE',App.Counter++,700,400);
}

// Подключаем редактор к мотору драг-н-дроп
_DRAG_DROP['tinymce']={
	tree:function(tree,node) {
		Ext.Ajax.request({
			url: 'xprogramms/tiny_mce/editor.php?getpageinfo='+node.id,
			 success: function(response) {
				if(response.responseText=="ERR") return false;
				var id=response.responseText.substr(0,response.responseText.indexOf(':'));
				var title=response.responseText.substr(response.responseText.indexOf(':')+1);
				EDITOR.editfolder('content','text',id,title);
			 }
		});
		return false;
	}
}

// Зарегистрируем плагин для отображения редактора в форме
PLUGINS.form.tinymce=function(win) {
    this.win=win; // В док передается ссылка на document формы
    this.BaseDir='xprogramms/tiny_mce/'; // базовый каталог редактора относительно каталога админки
    this.EditorCode="";

	this.init=function() {
		// В переменной _WYSIWYG передается список id-элементов, подключаемых к редактору
		if(this.win._WYSIWYG.length>0) {

			var th=this.win;

			// Подключим редакторы ко всем нужным техтареам (таймаут нужен для оперы)
			window.setTimeout(function(){

			for(var i=0;i<th._WYSIWYG.length;i++) {
				th.tinyMCE.init({
						mode : "textareas",
						theme: "advanced",
						plugins : "style,advlink,paste,fullscreeninn",
						// Theme options
						theme_advanced_buttons1 : "fullscreen,newdocument,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,|,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,code,|",
						theme_advanced_buttons2 : false,
						theme_advanced_buttons3 :false,
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "left",
						theme_advanced_statusbar_location : "bottom",
						theme_advanced_resizing : true,
						relative_urls : false,
						width           : th._WYSIWYG[i].w,
						height          : th._WYSIWYG[i].h,
						editor_selector : th._WYSIWYG[i].id
				});


			}},100);
		}
	}
}
