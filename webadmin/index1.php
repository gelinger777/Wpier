<? 
setcookie("COOK_RES_PATH","");
include "./autorisation.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>        
<head>
  <title>Wpier 1.0-betta</title>
  <META http-equiv="Content-Type" content="text/html; charset=windows-1251">
  <META NAME="Author" CONTENT="Maxim Tushev">
  <link href="<?=$RES_PATH?>styles.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" type="text/css" href="<?=$RES_PATH?>css/ext-all.css" />
  
  <SCRIPT LANGUAGE="JavaScript">
    resize_log=false;
    KeyLog=false;
    CtrlKeyLog=false;
    ServerName='<?=$_SERVER["SERVER_NAME"]?>';
    HostName='<?=$_CONFIG["SERVER"]?>';
    _DOCS_IMPORT=<?=(isset($_CONFIG["DOCS_IMPORT"]) && $_CONFIG["DOCS_IMPORT"]? "true":"false")?>;
    AdminLogin='<?=$AdminLogin?>';
    UPLOAD_IMG_DIR='<?=$_CONFIG["TEXT_LINKED_FILES_DIR"]?>';
    <?if(isset($_CONFIG["MAINFRAME"]) && $_CONFIG["MAINFRAME"]) {?>
    window.status='wintitle: :: <?=($_CONFIG["PROJECT_NAME"]."|http://".$_SERVER["SERVER_ADDR"].":".$_SERVER["SERVER_PORT"]."|".$_USERDIR."|".$DB_NAME."|".$FTP_UPLOAD_LOG)?>';
    window.setTimeout("window.status=''",1000);
    <?}?>
  </SCRIPT>
    
  <SCRIPT LANGUAGE='JavaScript' src='<?=$RES_PATH?>io.js'></SCRIPT>
  <SCRIPT LANGUAGE='JavaScript' src='<?=$RES_PATH?>fm.js'></SCRIPT>
  <script type="text/javascript" src="location/<?=$_CONFIG["ADMIN_LOCATION"]?>/script.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>preview.js"></script> 
  <script type="text/javascript" src="<?=$RES_PATH?>modify_html.js"></script> 
  <script type="text/javascript" src="<?=$RES_PATH?>ext-base.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>ext-all.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>locale/ext-lang-ru.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>help_call.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>index.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>RowExpander.js"></script>
  <script type="text/javascript" src="<?=$RES_PATH?>data-view-plugins.js"></script>
  
  
  <script type="text/javascript" src="js/print_r.js"></script>

  <script type="text/javascript">
    firebug_log=true;try {console.count('Test Inreco');} catch(e) {firebug_log=false;}
    MENU_STYLES=null;
    _DESKTOP_BACKGROUND_='ext/img/wallpapers/blue.jpg';
    STORE_LOCATIONS=[];
    CAN_CHMOD=false;
    _TREE_ACCESS_=0;
    _HELP_STATUS=<?=(isset($_CONFIG["HELP"]) && $_CONFIG["HELP"]? "true":"false")?>;
    <?
	if($AdminLogin=='root') echo "CAN_CHMOD=true;_TREE_ACCESS_=4;";
	else {
		$db->query("SELECT chmod, tree FROM usergroups WHERE id='".$ADMINGROUP."'");
		if($db->next_record()) {
		  if($db->Record["chmod"]) echo "CAN_CHMOD=true;";
		  if($db->Record["tree"]) echo "_TREE_ACCESS_=".intval($db->Record["tree"]).";";
		}
	}
    echo "CAN_PUBLIC=".($ADMIN_PUBLIC? "true":"false").";";
    $db->query("SELECT stores, desktop FROM settings WHERE id='$ADMIN_ID'");
    if($db->next_record()) {
      if($db->Record["stores"]) 
		  echo "STORE_LOCATIONS=".str_replace("&#39;","'",str_replace('\\','',$db->Record[0])).";";
	  echo "_DESKTOP_SHORTCUTS_=[".stripslashes(str_replace("&#39;","'",$db->Record["desktop"]))."];";
    }
	if(isset($_CONFIG["DEFAULT_STORE"]) && $_CONFIG["DEFAULT_STORE"]) {
		echo "STORE_LOCATIONS=[['".$_CONFIG["DEFAULT_STORE"]."','Public',false]].concat(STORE_LOCATIONS);";
	}

	$db->query("SELECT id FROM catalogue WHERE cod!=0 and cod is not NULL LIMIT 1");
	if($db->next_record()) echo "_IS_EXTEND_TREE_=true;";
	else echo "_IS_EXTEND_TREE_=false;";
    ?>

	STORE_GATE='<?=$_CONFIG["SERVER"]."/".$_CONFIG["ADMINDIR"]?>/xprogramms/filemanager/storegate.php?url='; 

     onReadyFunctions=[];
    
	MODULS_LIST=[];
	<?include "readprogramms.php";?>


    Ext.onReady(function(){     
		

       IndexInit();          
       // Формируем список программ и зоны drag-n-drop
       //var dz2 = new Ext.dd.DropZone('Garb-Bask', {ddGroup:'desktop'});
       <?
       //include "readprogramms.php";
       include "inc/createstylesmenu.php";
       ?> 
             
       DD.ReadDesktop();
       //IO=new iIO();
       // Запрос локальных сторе на стандартном порту
       //IO.sendScriptQuery('http://127.0.0.1:3232/?getstores');   	

	   for(var i=0;i<onReadyFunctions.length;i++) {
		 onReadyFunctions[i]();  
	   }

	   LOCKOBJ_TO=window.setTimeout(LOCKOBJ.lock,LOCK_TIMEOUT);

	   <?if(isset($_CONFIG["DEFAULT_OPEN_CODE"])) 
		   echo 'window.setTimeout(function() {page_preview("'.$_CONFIG["DEFAULT_OPEN_CODE"].'",false);},1000);';?>
    });     
 
	 
KeyGridProcessor=new function() {
  
  this.press=function(e) {
  //if(e.keyCode!=17) alert('1:'+e.keyCode);
	if(!e.ctrlKey && e.keyCode==13) {
		var sn=tree.getSelectionModel().selNodes;
		if(sn.length>0) {
			BranchOnDblClick(sn[0].id);
		}
		return false;	
	}
	//if(e.ctrlKey && e.keyCode==67) alert('press');
	//if(e.ctrlKey && (e.keyCode==19 || e.charCode==115)) return this.pressmkey('savebutton');
	
  }
  
  this.down=function(e) {
	
	// Shift+Enter
	if(e.shiftKey && !e.ctrlKey && e.keyCode==13) {
		var sn=tree.getSelectionModel().selNodes;
		if(sn.length>0) page_edit(sn[0].id);
		return false;
	}
	// Ctrl-X
	//if(e.ctrlKey && e.keyCode==88) return this.pressmkey('tppCut'); 
	// Ctrl-C
	if(e.ctrlKey && e.keyCode==67) {
	  popup_copy_do(null,tree);
	  return this.pressmkey('tppCopy');
	}
	// Ctrl-V
	if(e.ctrlKey && e.keyCode==86) return this.pressmkey('tppPaste');


	if(e.keyCode==113) {
		var sn=tree.getSelectionModel().selNodes;
  		if(sn.length==1) treeEditor.triggerEdit(sn[0]);
		return false;
	}
	//if(e.keyCode==46) return this.pressmkey('ttpDelete');
	if(e.ctrlKey && e.keyCode==82) return this.pressmkey('ttpRefresh');
	if(e.keyCode==78 && e.ctrlKey) {
		window.setTimeout(function(){
			var sn=tree.getSelectionModel().selNodes;
  		    if(sn.length>0) {
				CurrentTreeId=sn[0].id;
				CurTreeNode=sn[0];
				popup_add_do(CurrentTreeId);
			}
			else popup_add_do(0);
		},100);
		return false;
	}
  }

  this.up=function(e) {
    //if(e.keyCode!=17) alert('3:'+e.keyCode);
  }

  this.pressmkey=function(bn) {
    var b=Ext.getCmp(bn);
    if(b!=null) b.handler();
    return false;
  }
}

</script>
<script type="text/javascript" src="<?=$RES_PATH?>bot_big.js"></script>
<?
if(isset($AddJS) && count($AddJS)) foreach($AddJS as $v) {
  echo "<SCRIPT LANGUAGE='JavaScript' src='".$v."'></SCRIPT>";
}
if(isset($AddCSS) && count($AddCSS)) foreach($AddCSS as $v) {
  echo '<link rel="stylesheet" type="text/css" href="'.$v.'"></SCRIPT>';
}
?>
</head>

<body id="IndexElement" topmargin="0" leftmargin="0" onload="parent.OnLoadProg();" onmousemove="if(resize_log) resize_left(event)" onclick="body_on_click(event)" onfocus="return false" SCROLLING="no" onkeypress="return KeyGridProcessor.press(event)" onkeydown="return KeyGridProcessor.down(event)"  onkeyup="return KeyGridProcessor.up(event)">
<div id="centerColumn"></div>
<div id="helpDiv"></div>
<div id="HelpWinDiv" style="display:none"></div></body>
</html>