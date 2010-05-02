<?
include "autorisation.php";



if(isset($_GET["modgroup"])) {
// Устанавливаем права доступа на модули для определенной группы
  $db->query("SELECT chmod FROM usergroups WHERE id='$ADMINGROUP'");
  if($db->next_record() && $db->Record[0]) { // Проверим, что эта группа может устанавливать права доступа

    $_GET["modgroup"]=intval($_GET["modgroup"]);
    $db->query("DELETE FROM accessmodadmins WHERE grp='".$_GET["modgroup"]."'");
	$mods=array();
	if($_POST["rd"]) {
		$_POST["rd"]=explode(";",$_POST["rd"]);
		foreach($_POST["rd"] as $v) $mods[addslashes($v)]["rd"]=1;
	}
	if($_POST["ad"]) {
		$_POST["ad"]=explode(";",$_POST["ad"]);
		foreach($_POST["ad"] as $v) $mods[addslashes($v)]["ad"]=1;
	}
	if($_POST["ed"]) {
		$_POST["ed"]=explode(";",$_POST["ed"]);
		foreach($_POST["ed"] as $v) $mods[addslashes($v)]["ed"]=1;
	}
	if($_POST["dl"]) {
		$_POST["dl"]=explode(";",$_POST["dl"]);
		foreach($_POST["dl"] as $v) $mods[addslashes($v)]["dl"]=1;
	}

	foreach($mods as $k=>$v) {
		$db->query("INSERT INTO accessmodadmins (grp,mdl,rd,ad,ed,dl) VALUES ('".$_GET["modgroup"]."','".$k."','".(isset($v["rd"])? "1":"")."','".(isset($v["ad"])? "1":"")."','".(isset($v["ed"])? "1":"")."','".(isset($v["dl"])? "1":"")."')");
	}
	echo "OK";
  }
  exit;
}

if(!isset($_GET["act"])) exit;
$db->query("SELECT chmod FROM usergroups WHERE id='".$ADMINGROUP."' and chmod='1'");
if(!$db->next_record()) {
  exit;
}



if($_GET["act"]=="chmod") {

// Сохраняем настройки прав доступа для страниц
  if(isset($_POST["ids"])) {

    $_POST["ids"]=explode(",",$_POST["ids"]);
    foreach($_POST["ids"] as $k=>$v) $_POST["ids"][$k]=intval($v);
    $db->query("DELETE FROM accesspgadmins WHERE pg in (".join(",",$_POST["ids"]).")");
    $db->query("DELETE FROM accesspgpubl WHERE pg in (".join(",",$_POST["ids"]).")");

    $_POST["data"]=explode("*",$_POST["data"]);
    foreach($_POST["data"] as $v) {
      $v=explode("|",$v);
      foreach($_POST["ids"] as $pg) if($v[1] || $v[2] || $v[3] || $v[4]) {
        $db->query("INSERT INTO accesspgadmins (grp,pg,rd,ad,ed,dl) VALUES ('".intval($v[0])."','$pg','".($v[1]? "1":"")."','".($v[2]? "1":"")."','".($v[3]? "1":"")."','".($v[4]? "1":"")."')");
      }
    }
    $_POST["datap"]=explode("*",$_POST["datap"]);
    foreach($_POST["datap"] as $v) {
      $v=explode("|",$v);
      foreach($_POST["ids"] as $pg) if($v[1]) {
        $db->query("INSERT INTO accesspgpubl (grp,pg,pbl) VALUES ('".intval($v[0])."','$pg','".($v[1]? "1":"")."')");
      }
    }
  } elseif(isset($_POST["mod"])) {
    $m=array();
	if(isset($_POST["sel"]) && $_POST["sel"]!='') {
	  $_POST["sel"]=explode(",",$_POST["sel"]);
	  foreach($_POST["sel"] as $v) $m[]=AddSlashes($v);
	} else $m[]=AddSlashes($_POST["mod"]);

    $db->query("DELETE FROM accessmodadmins WHERE mdl in ('".join("','",$m)."')");
    $db->query("DELETE FROM accessmodpubl WHERE mdl in ('".join("','",$m)."'");

    $_POST["data"]=explode("*",$_POST["data"]);
    foreach($_POST["data"] as $v)  {
      $v=explode("|",$v);
      if($v[1] || $v[2] || $v[3] || $v[4]) {
		  foreach($m as $mv) $db->query("INSERT INTO accessmodadmins (grp,mdl,rd,ad,ed,dl) VALUES ('".intval($v[0])."','$mv','".($v[1]? "1":"")."','".($v[2]? "1":"")."','".($v[3]? "1":"")."','".($v[4]? "1":"")."')");
	  }
    }
    $_POST["datap"]=explode("*",$_POST["datap"]);
    foreach($_POST["datap"] as $v) {
      $v=explode("|",$v);
      if($v[1]) {
		  foreach($m as $mv) $db->query("INSERT INTO accessmodpubl (grp,mdl,pbl) VALUES ('".intval($v[0])."','$mv','".($v[1]? "1":"")."')");
	  }
    }
  }
  exit;
} elseif($_GET["act"]=="listgrp") {
// Читаем список групп
header("Content-type:text/xml");
header("Expires: Thu, Jan 1 1970 00:00:00 GMT");
header("Pragma: no-cache");
header("Cache-Control: no-cache");


echo '<?xml version="1.0" encoding="UTF-8"?>';

echo "<Items><Request><IsValid>True</IsValid></Request><TotalResults>1</TotalResults>";

$db->query("SELECT id, GrpName,GrpAdminAccess FROM usergroups ORDER BY id");
$grp=array();
$grpp=array();
while($db->next_record()) {
  if($db->Record[2]) $grp[$db->Record[0]]=array($db->Record[1],"","","");
  else $grpp[$db->Record[0]]=array($db->Record[1],"");
}

if(isset($_GET["ids"])) {
  $_GET["ids"]=explode(",",$_GET["ids"]);

  if(count($_GET["ids"])) {
    $db->query("SELECT * FROM accesspgadmins WHERE pg='".intval($_GET["ids"][0])."'");
    while($db->next_record()) {
      if(isset($grp[$db->Record["grp"]])) {
        $grp[$db->Record["grp"]][1]=($db->Record["rd"]? "true":"");
        $grp[$db->Record["grp"]][2]=($db->Record["ad"]? "true":"");
        $grp[$db->Record["grp"]][3]=($db->Record["ed"]? "true":"");
		$grp[$db->Record["grp"]][4]=($db->Record["dl"]? "true":"");
      }
    }
    $db->query("SELECT * FROM accesspgpubl WHERE pg='".intval($_GET["ids"][0])."'");
    while($db->next_record()) {
      if(isset($grpp[$db->Record["grp"]])) {
        $grpp[$db->Record["grp"]][1]=($db->Record["pbl"]? "true":"");
      }
    }
  }
} elseif($_GET["mod"]) {
  $db->query("SELECT * FROM accessmodadmins WHERE mdl='".AddSlashes($_GET["mod"])."'");
  while($db->next_record()) {
    if(isset($grp[$db->Record["grp"]])) {
      $grp[$db->Record["grp"]][1]=($db->Record["rd"]? "true":"");
      $grp[$db->Record["grp"]][2]=($db->Record["ad"]? "true":"");
      $grp[$db->Record["grp"]][3]=($db->Record["ed"]? "true":"");
	  $grp[$db->Record["grp"]][4]=($db->Record["dl"]? "true":"");
    }
  }
  $db->query("SELECT * FROM accessmodpubl WHERE mdl='".AddSlashes($_GET["mod"])."'");
  while($db->next_record()) {
    if(isset($grpp[$db->Record["grp"]])) {
      $grpp[$db->Record["grp"]][1]=($db->Record["pbl"]? "true":"");
    }
  }
}

foreach($grp as $k=>$v) {
  echo "<Item><id>$k</id><ItemAttributes>
    <GroupName><![CDATA[".$v[0]."]]></GroupName>
    <read>".$v[1]."</read>
    <add>".$v[2]."</add>
    <edit>".$v[3]."</edit>
	<del>".$v[4]."</del>
    <code>".$k."</code>
  </ItemAttributes>
 </Item>";
}

foreach($grpp as $k=>$v) {
  echo "<Itemp><id>$k</id><ItemAttributes>
    <GroupName><![CDATA[".$v[0]."]]></GroupName>
    <pbl>".$v[1]."</pbl>
    <code>".$k."</code>
  </ItemAttributes>
 </Itemp>";
}

echo '</Items>';

exit;
// К Списка групп


}elseif($_GET["act"]=="show") {
// Выводим интерфейс модуля управления правами
?>

var url='chmod.php?act=listgrp&<?=(isset($_GET["ids"])? "ids=".$_GET["ids"]:(isset($_GET["mod"])? "mod=".$_GET["mod"]:""))?>';
ids=<?=(isset($_GET["ids"])? "'".$_GET["ids"]."'":"null")?>;
mod=<?=(isset($_GET["mod"])? "'".$_GET["mod"]."'":"null")?>;
selectedmod=<?=(isset($_GET["sel"]) && $_GET["sel"]? "'".$_GET["sel"]."'":"''")?>;

//prompt('',url);

var store = new Ext.data.Store({
        url: url,
        remoteSort: true,
        reader: new Ext.data.XmlReader({
           record: 'Item',
           id: 'id',
           totalRecords: 'TotalResults'
        }, [{name: 'GroupName', mapping: 'ItemAttributes > GroupName'},
        'read','add','edit','code','del'])
});

var store1 = new Ext.data.Store({
        url: url,
        remoteSort: true,
        reader: new Ext.data.XmlReader({
           record: 'Itemp',
           id: 'id',
           totalRecords: 'TotalResults'
        }, [{name: 'GroupName', mapping: 'ItemAttributes > GroupName'},
        'pbl','code'])
});

var chRead = new Ext.grid.CheckColumn({header: "Чтен.",dataIndex: 'read',width: 45,sortable: false});
var chAdd = new Ext.grid.CheckColumn({header: "Доб.",dataIndex: 'add',width: 45,sortable: false});
var chEdit = new Ext.grid.CheckColumn({header: "Ред.",dataIndex: 'edit',width: 45,sortable: false});
var chDel = new Ext.grid.CheckColumn({header: "Удал.",dataIndex: 'del',width: 45,sortable: false});
var chPbl = new Ext.grid.CheckColumn({header: "Доступ",dataIndex: 'pbl',width: 65,sortable: false});

var gr=new Ext.grid.GridPanel({
   store: store,
   width:380,
   plugins:[chRead,chAdd,chEdit,chDel],
   clicksToEdit:1,
   bodyBorder:false,
   cm: new Ext.grid.ColumnModel([
     {header: 'Группа', width: 200, dataIndex: 'GroupName', sortable: false},
     chRead,chAdd,chEdit,chDel,{dataIndex: 'code',hidden:true}
   ]),
   viewConfig: {
    forceFit:true
   }
});

var gr1=new Ext.grid.GridPanel({
   store: store1,
   width:380,
   plugins:[chPbl],
   clicksToEdit:1,
   bodyBorder:false,
   cm: new Ext.grid.ColumnModel([
     {header: 'Группа', width: 315, dataIndex: 'GroupName', sortable: false},
     chPbl,{dataIndex: 'code',hidden:true}
   ]),
   viewConfig: {
    forceFit:true
   }
});

var W = new Ext.Window({
    title:DLG.t('AccessWinTitle'),
    layout:'fit',
    width:400,
    height:300,
    minimizable:false,
    maximizable:false,
    plain: true,
    resizable:false,
    items:[new Ext.TabPanel({
      id:'AccessTab',
      deferredRender:false,
      activeTab:0,
      border:false,
      enableTabScroll:true,
      renderTo:document.body,
      items:[{
	id:'AdminAccess',
        title: 'Администраторский раздел',
        autoScroll:true,
        items:[gr]
      },{
        id:'SiteAccess',
        title: 'Публичный раздел',
        autoScroll:true,
        items:[gr1]
      }]
    })],
    buttons: [{
      text:DLG.t('Save'),
      handler: function(){
        var x=new Array();
        store.each(function(o) {
          x[x.length]=o.data.code+'|'+(o.data.read? "1":"")+'|'+(o.data.add? "1":"")+'|'+(o.data.edit? "1":"")+'|'+(o.data.del? "1":"");
        });
        var y=new Array();
        store1.each(function(o) {
          y[y.length]=o.data.code+'|'+(o.data.pbl? "1":"");
        });
        if(ids!=null)
		  x=AJAX.post('chmod',{act:'chmod'},{ids:ids,data:x.join('*'),datap:y.join('*')});
        else if(mod!=null)
		  x=AJAX.post('chmod',{act:'chmod'},{sel:selectedmod,mod:mod,data:x.join('*'),datap:y.join('*')});
        W.close();
      }
    },{
      text: DLG.t('Close'),
      handler: function(){
        W.close();
      }
    }]
});
store.load();
store1.load();
W.show();
<?
} else {
}?>