/**
 * $Id: editor_plugin.js 
 *
 * @author Max Tushev
 * @copyright Copyright © 2004-2009, Max Tushev, All rights reserved.
 */

(function() {
	var DOM = tinymce.DOM;

	tinymce.create('tinymce.plugins.InsertObjectPlugin', {
		init : function(ed, url) {
			var t = this;
			
			ed.prevObj=new parent.PrevObj();
			t.editor = ed;
			
			ed.escape_fn=function(fn) {
			  var x=fn.indexOf('getimage=');
			  if(x>0) {
			    fn=fn.substr(0,x)+'esc=yes&getimage='+escape(fn.substr(x+9));
			  }
			  return fn;
			} 
			
			// Register commands
			ed.addCommand('mceInsertObject', function() {
				var win, de = DOM.doc.documentElement;
				parent.IO.OpenFileDlg(parent.DLG.t('Insert image'),[['*.gif,*.jpg,*.jpeg,*.png','Images (gif, jpeg, png)'],['*.*','All formats (*.*)']],function(fn){
					fn=ed.prevObj.ShowPics(fn);
					var st='';
      					for(var i=0;i<fn.length;i++) st+='<img src="'+ed.escape_fn(fn[i]).replace("../","/")+'" border="0" alt="" />';
					ed.execCommand('mceInsertContent', 0, st);
				});

				//

			});

			// Register buttons
			ed.addButton('insertobject', {title : 'insertobject.desc', cmd : 'mceInsertObject'});

			/*ed.onNodeChange.add(function(ed, cm) {
				cm.setActive('fullscreen', ed.getParam('fullscreen_is_enabled'));
			});*/
		},

		getInfo : function() {
			return {
				longname : 'Insertobject',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://www.wpier.ru',
				infourl : 'http://www.wpier.ru',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('insertobject', tinymce.plugins.InsertObjectPlugin);
})();
