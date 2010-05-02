/**
 * $Id: editor_plugin.js $
 *
 * @author Max Tushev
 * @copyright Copyright � 2004-2009, Mox Tushev, All rights reserved.
 */

(function() {
	var DOM = tinymce.DOM;

	tinymce.create('tinymce.plugins.FullScreenPlugin', {
		init : function(ed, url) {
			var t = this, s = {}, vp;

			t.editor = ed;

			if (!ed.getParam('fullscreen_is_enabled')) {
				_SAVEFUNC[_SAVEFUNC.length]=function() {
				  if(ed.currSaveFunc!=null) ed.currSaveFunc();
				  var prv=new ParentW.PrevObj();
				  var s=prv.PrepStorePics(ed.getContent(),ParentW.UPLOAD_IMG_DIR+'.files/');
				  ed.setContent(s);
				}
			}

			// Register commands
			ed.addCommand('mceFullScreen', function() {
				var win, de = DOM.doc.documentElement;

				if (ed.getParam('fullscreen_is_enabled')) {
					ed.currSaveFunc=function(){}
					if (ed.getParam('fullscreen_new_window'))
						closeFullscreen(); // Call to close in new window
					else {

						DOM.win.setTimeout(function() {
							tinymce.dom.Event.remove(DOM.win, 'resize', t.resizeFunc);
							tinyMCE.get(ed.getParam('fullscreen_editor_id')).setContent(ed.getContent({format : 'raw'}), {format : 'raw'});
							tinyMCE.remove(ed);
							DOM.remove('mce_fullscreen_container');
							de.style.overflow = ed.getParam('fullscreen_html_overflow');
							DOM.setStyle(DOM.doc.body, 'overflow', ed.getParam('fullscreen_overflow'));
							DOM.win.scrollTo(ed.getParam('fullscreen_scrollx'), ed.getParam('fullscreen_scrolly'));
							tinyMCE.settings = tinyMCE.oldSettings; // Restore old settings
						}, 10);
					}

					return;
				}

				if (ed.getParam('fullscreen_new_window')) {
					win = DOM.win.open(url + "/fullscreen.htm", "mceFullScreenPopup", "fullscreen=yes,menubar=no,toolbar=no,scrollbars=no,resizable=yes,left=0,top=0,width=" + screen.availWidth + ",height=" + screen.availHeight);
					try {
						win.resizeTo(screen.availWidth, screen.availHeight);
					} catch (e) {
						// Ignore
					}
				} else {


					tinyMCE.oldSettings = tinyMCE.settings; // Store old settings
					s.fullscreen_overflow = DOM.getStyle(DOM.doc.body, 'overflow', 1) || 'auto';
					s.fullscreen_html_overflow = DOM.getStyle(de, 'overflow', 1);
					vp = DOM.getViewPort();
					s.fullscreen_scrollx = vp.x;
					s.fullscreen_scrolly = vp.y;

					// Fixes an Opera bug where the scrollbars doesn't reappear
					if (tinymce.isOpera && s.fullscreen_overflow == 'visible')
						s.fullscreen_overflow = 'auto';

					// Fixes an IE bug where horizontal scrollbars would appear
					if (tinymce.isIE && s.fullscreen_overflow == 'scroll')
						s.fullscreen_overflow = 'auto';

					// Fixes an IE bug where the scrollbars doesn't reappear
					if (tinymce.isIE && (s.fullscreen_html_overflow == 'visible' || s.fullscreen_html_overflow == 'scroll'))
						s.fullscreen_html_overflow = 'auto';

					if (s.fullscreen_overflow == '0px')
						s.fullscreen_overflow = '';

					DOM.setStyle(DOM.doc.body, 'overflow', 'hidden');
					de.style.overflow = 'hidden'; //Fix for IE6/7
					vp = DOM.getViewPort();
					DOM.win.scrollTo(0, 0);

					if (tinymce.isIE)
						vp.h -= 1;

					n = DOM.add(DOM.doc.body, 'div', {id : 'mce_fullscreen_container', style : 'position:' + (tinymce.isIE6 || (tinymce.isIE && !DOM.boxModel) ? 'absolute' : 'fixed') + ';top:25px;left:0;width:' + vp.w + 'px;height:' + (vp.h-25) + 'px;z-index:200000;'});
					DOM.add(n, 'div', {id : 'mce_fullscreen'});

					tinymce.each(ed.settings, function(v, n) {
						s[n] = v;
					});

					s.id = 'mce_fullscreen';
					s.width = n.clientWidth;
					s.height = n.clientHeight - 15;
					s.fullscreen_is_enabled = true;
					s.fullscreen_editor_id = ed.id;
					s.theme_advanced_resizing = false;

// MT

 s.plugins = "insertobject,safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,syntaxhl";
 s.theme_advanced_buttons1 = "fullscreen,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect";
 s.theme_advanced_buttons2 = "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,insertobject,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor";
 s.theme_advanced_buttons3 =  "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl";
 s.theme_advanced_buttons4 = "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,syntaxhl";
 s.theme_advanced_toolbar_location = "top";
 s.theme_advanced_toolbar_align = "left";
 s.content_css = "/style/text.css";
 s.template_external_list_url = "xprogramms/tiny_mce/js/template_list.js";
 s.external_link_list_url = "xprogramms/tiny_mce/js/link_list.js";
 s.external_image_list_url = "xprogramms/tiny_mce/js/image_list.js";
 s.media_external_list_url = "xprogramms/tiny_mce/js/media_list.js";
 s.relative_urlss = false;


// ������� ������� ����������
ed.currSaveFunc=function() {
  if(tinyMCE.get('mce_fullscreen')!=null) ed.setContent(tinyMCE.get('mce_fullscreen').getContent());
}
//_SAVEFUNC[_SAVEFUNC.length]=tinyMCE.currSaveFunc;

// MT


					s.save_onsavecallback = function() {
						ed.setContent(tinyMCE.get(s.id).getContent({format : 'raw'}), {format : 'raw'});
						ed.execCommand('mceSave');
					};

					tinymce.each(ed.getParam('fullscreen_settings'), function(v, k) {
						s[k] = v;
					});

					if (s.theme_advanced_toolbar_location === 'external')
						s.theme_advanced_toolbar_location = 'top';

					t.fullscreenEditor = new tinymce.Editor('mce_fullscreen', s);
					t.fullscreenEditor.onInit.add(function() {
						t.fullscreenEditor.setContent(ed.getContent());
						t.fullscreenEditor.focus();
					});

					t.fullscreenEditor.render();
					tinyMCE.add(t.fullscreenEditor);

					t.fullscreenElement = new tinymce.dom.Element('mce_fullscreen_container');
					t.fullscreenElement.update();
					//document.body.overflow = 'hidden';

					t.resizeFunc = tinymce.dom.Event.add(DOM.win, 'resize', function() {
						var vp = tinymce.DOM.getViewPort();

						t.fullscreenEditor.theme.resizeTo(vp.w, (vp.h-25));
					});
				}
			});

			// Register buttons
			ed.addButton('fullscreen', {title : 'fullscreen.desc', cmd : 'mceFullScreen'});



			ed.onNodeChange.add(function(ed, cm) {
				cm.setActive('fullscreen', ed.getParam('fullscreen_is_enabled'));
			});
		},

		getInfo : function() {
			return {
				longname : 'Fullscreen',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/fullscreen',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('fullscreeninn', tinymce.plugins.FullScreenPlugin);
})();
