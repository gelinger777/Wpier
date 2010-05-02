<?
/*DESCRIPTOR

Корзина


tools: admin
version:0.1
author: 
*/

//HEAD//
$PROPERTIES=array(
"tbname"=>"resurceled",
"pagetitle"=>"Корзина",
"orderby"=>"id DESC",
"nolang"=>"yes",
"filters"=>"1",
//"formopenmode"=>"win:400:500",
);

$F_ARRAY=Array (
"id" => "hidden||",
"deltime"=>"unitime|d.m.y H:i:s|Дата и время удаления",
"modname"=>"text||Модуль",
"user_"=>"select|settings*id*AdminLogin|Пользователь",
"tabname"=>"text||Таблица",

);


$f_array=Array(
"id" => "*hide*",
"deltime"=>"Дата и время",
"modname"=>"Модуль",
"user_"=>"Пользователь",
"tabname"=>"Таблица",
);
//ENDHEAD//

if(isset($_POST["restoreids"]) && $_POST["restoreids"]) {
// Востановим отмеченные объекты
  $_POST["restoreids"]=explode(",",$_POST["restoreids"]);
  foreach($_POST["restoreids"] as $k=>$v) {
	$v=intval($v);
	if($v) $_POST["restoreids"][$k]=$v;
	else unset($_POST["restoreids"][$k]);
  }
  if(count($_POST["restoreids"])) {
	$db->query("SELECT id, tabname, dataled FROM resurceled WHERE id in (".join(",",$_POST["restoreids"]).")".($_SESSION['adminlogin']!='root'? " and user_='".$ADMIN_ID."'":""));
	$recs=array();
	while($db->next_record()) {
		$recs[$db->Record["id"]]=array($db->Record["tabname"],$db->Record["dataled"]);
	}
	if(count($recs)) {
		foreach($recs as $k=>$v) {
			$v[1]=unserialize(str_replace("**#39#**","'",$v[1]));
			$db->query("INSERT INTO ".$v[0]." (".kjoin(",",$v[1]).") VALUES ('".join("','",$v[1])."')");	
		}
		$db->query("DELETE FROM resurceled WHERE id in (".kjoin(",",$recs).")");
		echo "OK";
		exit;
	}
  }
  exit;
}


if(!$PERMIS[0] || !$PERMIS[1] || !$PERMIS[2] || !$PERMIS[3]) {
  unset($F_ARRAY["user_"]);
  unset($f_array["user_"]);
  $PROPERTIES["WHERE"]="WHERE user_='".$ADMIN_ID."'";
}

$PERMIS=array(1,0,0,1);

$USER_GRID_BUTTONS=array(
"trashview"=>array("xprogramms/trash/view.png","Посмотреть","Посмотреть выделенные записи","
	var s=MainGrid.getSelectionModel().getSelections();
	if(s.length==0) return false;
    for(var i=0;i<s.length;i++) {
		ParentW.App.run('xprogramms/trash/view.php?id='+s[i].id,'Просмотр элемента #'+s[i].id).setIconClass(ParentW.CheckClass('xprogramms/trash/ico_16.png'));
	}
"),
"trashrestore"=>array("xprogramms/trash/undo.png","Востановить","Востановить выделенные объекты","
	var s=MainGrid.getSelectionModel().getSelections();
	if(s.length==0) return false;
	if(!confirm('Востановить отмеченные объекты? ('+s.length+')')) return false;
    var ids=[];
    for(var i=0;i<s.length;i++) ids[ids.length]=s[i].id;
	Ext.Ajax.request({
		url: '?ext=trash',
		success: function(response) {
		  if(response.responseText=='OK') store.reload(); 
		},
		params: {
		  restoreids:ids.join(',')
		}
	});
"),
"trashclean"=>array("xprogramms/trash/eraser.png","Очистить","Очистить корзину","if(confirm('Данные из корзины будут удалены без возможности востановления. Продолжить?')) {
  window.location='?ext=$EXT&clean=all';
}	
"),
);

if(isset($_GET["clean"])) {
	$db->query("DELETE FROM resurceled ".(isset($PROPERTIES["WHERE"]) && $PROPERTIES["WHERE"]? $PROPERTIES["WHERE"]:""));
}
