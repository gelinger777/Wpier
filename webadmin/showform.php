<?if(!isset($RES_PATH)) $RES_PATH="./ext/";?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>
  <title></title>
  <link rel="stylesheet" type="text/css" href="<?=$RES_PATH?>css/ext-all.css" />
  <?if($ADMIN_SKIN) echo '<link rel="stylesheet" type="text/css" href="'.$RES_PATH.'css/xtheme-'.$ADMIN_SKIN.'.css" />';?>
  <link rel="stylesheet" type="text/css" href="<?=$RES_PATH?>img/main.css">
  <SCRIPT LANGUAGE='JavaScript' src='<?=$RES_PATH?>forms.js'></SCRIPT>
  <script type="text/javascript" src="<?=$RES_PATH?>ext-base.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>ext-all.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>getparent.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>help_call.js"></script>

  <script type="text/javascript" src="js/print_r.js"></script>
  <script type="text/javascript" src="/<?=$_CONFIG["ADMINDIR"]?>/ext/locale/ext-lang-<?=$_CONFIG["ADMIN_LOCATION"]?>.js"></script>


  <style type="text/css">
    .ed_clr {background-image:url(ext/img/editor/modhtml.gif) !important;}
    .warning {
      font-family:sans-serif,Arial;
    }
    <?foreach($MenuButtons as $k=>$v) if(is_array($v) && $v[4]) {
    echo ".butt$k{background-image:url(".$v[4].")  !important;}\r\n";
    }?>
    </style>
    <script type="text/javascript">
    HELPMOD='form<?=$tbname?>';

    FORMFOLDS=[];
    _WYSIWYG=[];
    _SAVEFUNC=[];
    _LOAD_MASK=null;

    CLEAR_WORD_TAGS_EDITORS=[<?
    if(isset($_CONFIG["CLEAR_WORD_TAGS_IN_EDITORS"]) && $_CONFIG["CLEAR_WORD_TAGS_IN_EDITORS"] && isset($_WYSIWYG) && count($_WYSIWYG)) {
      echo "'".join("','",$_WYSIWYG)."'";
    }
    ?>];

    Ext.onReady(function(){

    // Делаем панель кнопок
    tbar=new Ext.Toolbar({
     autoShow:true,
     autoHeight:true,
     autoWidth:true,
     items:[<?
    foreach($MenuButtons as $k=>$v) if(is_array($v)) {
      echo ($k? ",":"")."{
        text:'".$v[2]."',
		".(isset($v[6])? "type:'".$v[6]."',":"")."
        id:'".($v[0]? $v[0]:"topbut$k")."',
        tooltip:'".$v[1]."',
        ".($v[4]? "iconCls:'butt".$k."',":"")."
        ".(isset($v[5]) && $v[5]? "disabled:true,":"")."
        handler:function() {".$v[3]."}
      }";
    } else echo ($k? ",":"")."'-'";?>]});

    // Инициализация свойств
    PROPS=null;
    <?if(count($PropsList)) {
    $a=array();
    $a1=array();
    $vals=array();
    $keys=array();
    foreach($PropsList as $k=>$v){
      $v[0]=addslashes(trim(strip_tags($v[0])));
      $v[1]=addslashes(trim($v[1]));
      $keys[]='["'.$v[0].'","'.$k.'"]';
      $ClassName=strtolower(get_class($OBJECTS[$k]));
      if($ClassName=='t_select') {
        //$v[1]="";//'"'.addslashes(trim($OBJECTS[$k]->mkList($v[1]))).'"';
		$vll="";
        $items=array();
        //$vals[$k]=array();
        foreach($OBJECTS[$k]->PROP["items"] as $key=>$val) {
          $val=addslashes(trim($val));
          $items[]='["'.$key.'","'.$val.'"]';
		  if($v[1]==$key) $vll=$val;
          $vals[]='["'.$k.'","'.$val.'","'.trim($key).'"]';
        }
        foreach($OBJECTS[$k]->PROP["items_db"] as $key=>$val) {
          $val=addslashes(trim($val));
          $items[]='["'.$key.'","'.$val.'"]';
		  if($v[1]==$key) $vll=$val;
          $vals[]='["'.$k.'","'.$val.'","'.trim($key).'"]';
        }
        $a1[]='"'.$v[0].'":new Ext.grid.GridEditor(new Ext.form.ComboBox({store:new Ext.data.SimpleStore({ fields: ["val","key"],data : ['.join(',',$items).']}),displayField:"key",valueField: "key", typeAhead: true,mode: "local",triggerAction: "all", editable: false}))'; //forceSelection: true,lazyInit: true,
		$v[1]='"'.$vll.'"';
      }elseif($ClassName=='t_checkbox') {
        if($v[1]=='1') $v[1]='true';
        else $v[1]='false';
      } else $v[1]='"'.trim($v[1]).'"';
      $a[]='"'.$v[0].'":'.trim($v[1]);
    }
    echo 'PROPS={'.join(',',$a).'};';
    echo 'KEYS=['.join(',',$keys).'];';
    echo 'VALS=['.join(',',$vals).'];';
    }

    echo "PageTitle='";
    if(isset($PROPERTIES["pagetitle"])) {
      $s=explode("\n",$PROPERTIES["pagetitle"]);
      echo trim(strip_tags($s[0]));
    }else echo $EXT;
    echo "';";
    echo "PageTable='".($PROPERTIES["tbname"])."';";
    echo "PageId='".(isset($_GET["ch"])? intval($_GET["ch"]):0)."';";
    ?>
    // --------------------

    Ext.QuickTips.init();
    Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

    function strreplace(f,r,s) {
      var s1='';
      while(s1!=s) {
        s1=s;
        s=s.replace(' ','');
      }
      return s;
    }

    // Блок свойств
    if(PROPS==null){}
    else {
      PropPnl=new Ext.grid.PropertyGrid({
        listeners:{
          validateedit:function(e) {
            //Тут функция для изменения свойства
            var k='';

            for(var i=0;i<KEYS.length;i++) {
              if(KEYS[i][0]==e.record.id) {
                k=KEYS[i][1];
                break;
              }
            }
            var v=e.value;
            if(v==true) v='1';
            else if(v==false) v='';
	    else {
	      for(var i=0;i<VALS.length;i++) {
	        if(VALS[i][0]==k && VALS[i][1]==v) {
	          v=VALS[i][2];
	          break;
	        }
	      }
	    }
	    var chid=[PageId];
            if(PageTable=='catalogue') {
              var PT=null;
              if(ParentW.tree.getSelectionModel().selNodes.length>1) PT=ParentW.tree;
              else
              if(ParentW.tree_extend!=null && ParentW.tree_extend.getSelectionModel().selNodes.length>1) PT=ParentW.tree_extend;
              if(PT!=null && ParentW.DLG.c('Change selected pages?')) {
		for(var i=0;i<PT.getSelectionModel().selNodes.length;i++) chid[chid.length]=PT.getSelectionModel().selNodes[i].id;
	      }
	    }
            v=ParentW.AJAX.get('ajaxfunc',{changeprop:k,val:v,t:PageTable,id:chid.join(',')});
	    for(var i=0;i<chid.length;i++) ParentW.treeChangeAttr(chid[i],1);
	    if(v=='RELOAD') window.location=window.location;
	  }
        },
        source:PROPS,
        id:'prop-panel'
      });

      PropPnl.getColumnModel().setConfig
      ([
        {header: ParentW.DLG.t('PropKey'), width:50, sortable: false, dataIndex:'name', id: 'name'},
        {header: ParentW.DLG.t('PropValue'), width:50, sortable: false, resizable:false, dataIndex: 'value', id: 'value'}
      ]);

    }

    <?
    if(isset($a1) && count($a1)) echo 'PropPnl.customEditors={'.join(',',$a1).'};';
    echo 'var PropTitle='.(isset($F_ARRAY_PROPS["title"])? '"'.$F_ARRAY_PROPS["title"].'"':'ParentW.DLG.t(\'PropTitle\')').';';

    if(isset($TABPANEL)) {?>
      var central=new Ext.TabPanel({
          id:'wpier_tabpanel',
          region:'center',
          deferredRender:false,
          activeTab:<?=(isset($TABPANEL["activeTab"])? $TABPANEL["activeTab"]:0)?>,
          tabPosition:'<?=(isset($TABPANEL["tabPosition"])? $TABPANEL["tabPosition"]:"bottom")?>',     items:[
          <?
          $i=0;
          foreach($TABPANEL["items"] as $val) {
            echo ($i++? ",{":"{tbar:tbar,");
            $j=0;
            foreach($val as $k=>$v) {
              echo ($j++? ",":"")."$k:".(is_bool($v)? ($v? "true":"false"):$v);
            }
            echo "}";
          }
          ?>
          ]
      });
      //print_r(central.getItem(0));
    <?} else {?>
    var central={
                    region:'center',
                    contentEl: 'IntrContentDiv',
                    collapsible: false,
                    title:'',
                    autoScroll:true,
                    tbar:tbar
                 };
    <?}?>

    var viewport = null;

    if(PROPS!=null) {
        viewport=new Ext.Viewport({
            layout:'border',
            hideBorders:true,
            items:[
                central
                ,{
                    region:'east',
                    title: PropTitle,
                    collapsible: true,
		    <?if(isset($F_ARRAY_PROPS["collapsed"]) && $F_ARRAY_PROPS["collapsed"]) echo "collapsed:true,";?>
                    split:true,
                    width: 220,
                    minSize: 125,
                    maxSize: 400,
                    layout:'fit',
                    margins:'0 5 0 0',
                    items:PropPnl
                 }]
        });
      } else {
        viewport=new Ext.Viewport({
            layout:'border',
            hideBorders:true,
            items:[
            central
            ]
        });
    }
    Ext.EventManager.on(document.body, 'click', handleBodyClick);


    <?
    // TEST EDITOR
    foreach($form as $k=>$v) if(isset($v[0][2]) && $v[0][2]) echo "//".$k."\r\n".$v[0][2];
    $CollapsedPanels=array();
    $i=0;
    if(isset($F_ARRAY_PANELS)) $PANELS=$F_ARRAY_PANELS;

    foreach($PANELS as $key=>$val) if((isset($val["items"]) && count($val["items"])) || (isset($val["html"]) && count($val["html"]))) {?>


      p<?=$i?> = new Ext.Panel({
        <?if(is_string($key)){?>
        title: '<?=$key?>',
	    hideMode:'visibility',
        collapsible:true,
        animCollapse:false,
        <?}
        echo (isset($val["prop"]["hide"]) && $val["prop"]["hide"]? "collapsed:true,":"");
        ?>
        contentEl:'panel<?=$i?>',
        id:'Panel<?=$i?>',
        renderTo: 'IntrPanelsDiv',
        width:'100%',
        style:'margin-top:5px'
      });

   <?$i++;}
     if(isset($FORM_EXTEND) && $FORM_EXTEND) {?>
       p<?=$i?> = new Ext.Panel({
        contentEl:'panel<?=$i?>',
        id:'Panel<?=$i?>',
        renderTo: 'IntrPanelsDiv',
		hideMode:'visibility',
        width:'100%',
        style:'margin-top:5px'
      });

	 <?}

	if(isset($DEPEND_ARRAY)) {
	  $ii=0;
		foreach($DEPEND_ARRAY as $k=>$vl) {
			echo "window.setTimeout(function(){x=Ext.getCmp('$k');";
			$s="";
			foreach($vl as $i=>$v) {
				$v=explode("|",$v);
				echo "y=Ext.getCmp('".$v[0]."');
				if(x.getValue!=null && y.depend_function!=null) y.depend_function(x.getValue(),'".$v[1]."',false);";
				$s.="Ext.getCmp('".$v[0]."').depend_function(v,'".$v[1]."',true);

				";
			}
			echo "x.on('change',function() {
				var v=Ext.getCmp('$k').getValue();
				$s
			});	},".($ii*1000).");";
			$ii++;
		}
	}

	?>

   // Тут вызывается объект для привязки к внешним программам
   ParentW.FACT.run(window);

   // Проверим нужно-ли активировать кнопку "вставить"
   if(ParentW.MOD_BUFFER.<?=$EXT?>!=null) {
      Ext.getCmp('pastebutton').enable();
   }
    // Проверим нужно-ли активировать кнопку "копировать"
   <?if(isset($_GET["ch"])){?>Ext.getCmp('copybutton').enable();<?}?>

   // Запустим плагины, подключенные к формам
   for(var i in ParentW.PLUGINS.form) {
     eval('var o=new ParentW.PLUGINS.form.'+i+'(window)');
     o.init();
   }

   _LOAD_MASK=new Ext.LoadMask(Ext.getBody(), {msg:ParentW.DLG.t('Saving data...')});
});

function PasteFromBuffer(e) {

}

LogLoadModule=false;
function refresh_parent() {

}
multi_select=[];

function onUnLoadWin() {
  if(ParentW!=null) {
    try {UnlockRow();}catch(e){}
  }
}

function onLoadWin() {
  if(ParentW!=null) {
    CheckPasteButton('<?=$EXT?>');
    try {
      ParentW.document.getElementById('left_div_1').focus();
    } catch(e) {}
  }
  <?
  if(isset($PROPERTIES["standard"]) && $PROPERTIES["standard"]=="yes" && isset($_GET["new"])) echo "StandardSetDef();";
  if(isset($DEPEND_ARRAY)) echo "LoadAjaxWin();";
  ?>
}

function GetTabId() {
  return [<?=(isset($_GET["ch"])? "'".$PROPERTIES["tbname"]."','".$_GET["ch"]."'":"")?>];
}

KeyFormProcessor=new function() {
  this.press=function(e) {
    if(e.ctrlKey && (e.keyCode==19 || e.charCode==115)) return this.pressmkey('savebutton');
  }

  this.down=function(e) {
    if(e.keyCode==27) return this.pressmkey('closebutton');
  }

  this.pressmkey=function(bn) {
    var b=Ext.getCmp(bn);
    if(b!=null) b.handler();
    return false;
  }
}

function CopyForm(modname) {
  <?if(isset($_GET["ch"])) echo "ParentW.MOD_BUFFER.".$EXT."=".intval($_GET["ch"]).";";?>
}

function PasteForm(mod) {
  window.location='?ext=<?=$EXT?>&ch='+ParentW.MOD_BUFFER.<?=$EXT?>+'&_copy_from_buffer=yes<?=((isset($_GET["catalog"])? "&catalog=".$_GET["catalog"]:"").(isset($_GET["ch"])? "&_copy_from_buffer_to=".$_GET["ch"]:""))?>';
}

RF=[<?
if(isset($REQUIRED_FOLDERS) && count($REQUIRED_FOLDERS)) {
  foreach($REQUIRED_FOLDERS as $k=>$v) echo ($k? ",":"")."'$v'";
}
?>];

function sendform() {
  _LOAD_MASK.show();

  // Запускаем функции, определенные плагинами (если есть)
  if(_SAVEFUNC.length>0) for(var i=0;i<_SAVEFUNC.length;i++) if(_SAVEFUNC[i]()==false) {return false;}

  if(RF.length>0) {
    var o=null;
    for(var i=0;i<RF.length;i++) {
      o=Ext.getCmp(RF[i]);
      if(o!=null && o.getValue()=='') {
	alert('Не заполнено обязательное поле!');
	o. focus();
	return false;
      } else {
	o=document.getElementById(RF[i]);
	if(o!=null && o.value=='') {
	  alert('Не заполнено обязательное поле!');
	  o.focus();
	  return false;
	}
      }
    }
  }

  try {
    UserSendFunction()
  }
  catch(e) {  }

  var p=ParentW.tabs.getActiveTab().parentpanel;

  if(p!=null) {
    p.storechanged=true;
  }

  // Подчистим HTML
  /*
  if(CLEAR_WORD_TAGS_EDITORS.length>0) {
    var e=null;
    for(var i=0;i<CLEAR_WORD_TAGS_EDITORS.length;i++) {
      e=document.getElementById(CLEAR_WORD_TAGS_EDITORS[i]);
      e.value=ParentW.modify_html.modif(e.value);
    }
  }
*/
  return true;
}

</SCRIPT>
<?
if(isset($_SESSION["form_js"]) && is_array($_SESSION["form_js"])) {
	foreach($_SESSION["form_js"] as $v) echo '<script src="'.join('"></script><script src="',$v).'"></script>';
}
?>
</head>

<body onkeypress="return KeyFormProcessor.press(event)" onkeydown="return KeyFormProcessor.down(event)"><div id="IntrContentDiv" style=""><FORM ENCTYPE='multipart/form-data' name='MainEditForm' id='MainEditForm' action='?<?=EchoGetStr((isset($_GET["new"])? "new":"ch"),"")?>' onsubmit='<?=((isset($_SESSION["ses_mode"]) && $_SESSION["ses_mode"]==1)? "if(CheckLocalFiles(this,\"".$EXT."\")) return false;":"")?>if(sendform("MainEditForm")) return true;else _LOAD_MASK.hide();' METHOD=POST  target="_wpier_post_frame_"><div  id="IntrPanelsDiv" style="padding:5px;">
  <?

if(isset($PROPERTIES["warning"]) && $PROPERTIES["warning"]) echo "<div class='warning'>".$PROPERTIES["warning"]."</div>";

  $i=0;
  foreach($PANELS as $key=>$val) if((isset($val["items"]) && count($val["items"])) || (isset($val["html"]) && $val["html"])) {
    echo '<div id="panel'.($i++).'" style="padding:5px;'.(isset($val["prop"]["style"])? $val["prop"]["style"]:"").'">';
    if(isset($val["html"]) && $val["html"]) {
      echo $val["html"];
    }
    if(isset($val["items"])) foreach($val["items"] as $v) if(isset($form[$v])) {
      echo "<table border=0 cellpadding=0 cellspacing=0><tr><td id='intr-elm-$v'>";
      if(is_string($form[$v][0])) {
        echo '<div>'.$form[$v][0].'</div>';
      } else {
        echo '<div class="inpDiv"><table><tr valign=top><td>'.($form[$v][0][0]? '<div class="txtStyle" '.(isset($val["prop"]["txtStyle"])? 'style="'.$val["prop"]["txtStyle"].'"':'').'>'.$form[$v][0][0].'</div>':'').'</td><td>'.$form[$v][0][1].'</td></tr></table></div>';
      }
      echo "</td></tr></table>";
    }
    echo '</div>';
  }
  echo (isset($FORM_EXTEND) && $FORM_EXTEND? '<div id="panel'.$i.'" style="padding:5px;">'.$FORM_EXTEND.'</div>':"");
  ?>

  </div>
  <SCRIPT LANGUAGE="JavaScript" src="<?=$RES_PATH?>footer.js"></SCRIPT>
   <?
  if(isset($_GET["_copy_from_buffer"])) echo "<INPUT type='hidden' name='_copy_from_buffer_from' value='".$_GET["ch"]."'>";
  echo "<INPUT type='hidden' ".($id && (!isset($_GET["_copy_from_buffer"]) || isset($_GET["_copy_from_buffer_to"]))? "name='upd'":"name='ins'")." id='wpier_action_fold' value='y'>$FormHiddens";
 // <input type="submit" style="width:100;height:30">
  ?>


  </FORM></div><div id="props-panel" style="width:200px;height:200px;overflow:hidden;"></div><iframe id="_wpier_post_frame_" name="_wpier_post_frame_" style="border:none;width:0;height:0;"></iframe></body>
</html>
