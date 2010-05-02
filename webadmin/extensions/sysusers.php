<?
/*DESCRIPTOR
0,1
Пользователи



group: psbinfo
version:0.1
author: 
*/

//HEAD//
$PROPERTIES=array(
"filters"=>"yes",
"filters_size"=>"1",
"pagetitle"=>"Пользователи",
"pg2pg"=>"10",
"step"=>"10",
"tbname"=>"settings",
//"delwhere"=>"access!=-1",
"nolang"=>1,
//"formopenmode"=>"win:400:500"
);

$F_ARRAY=array(
"id"=>"hidden||",
//"AdminRegion"=>"select|spravplaces*rCod*rName|Регион",
"admingroup"=>"select|usergroups*id*grpname**grpname/maxlength=255|Группа",
"adminlogin"=>"text| size=50 maxlength=|Логин",
"adminname"=>"text| size=50 maxlength=|ФИО",
"adminpos"=>"text| size=50 maxlength=255|Должность",
"admintel"=>"text| size=50 maxlength=255|Телефон",
"adminemail"=>"text| size=50 maxlength=|E-mail",
"adminpublic"=>"checkbox||Может публиковать структуру|",
"numrows"=>"hidden||",
"pagesinrow"=>"hidden||",
"adminpassword"=>"password|size= maxlength=|Пароль",
);

$f_array=Array(
"id" => "*hide*",
"adminlogin"=>"Логин",
"adminname"=>"ФИО",
"adminpos"=>"Должность",
"admintel"=>"Телефон",
"adminemail"=>"E-mail",
"admingroup"=>"Группа",

);


//ENDHEAD//




if(isset($_POST["ins"]) && $_POST["adminlogin"])  {
	$db->query("SELECT id FROM settings WHERE adminlogin='".$_POST["adminlogin"]."'");
	if($db->next_record()) {
		echo "<script>alert('Пользователь с логином \"".$_POST["adminlogin"]."\" уже зарегистрирован!');</script>";
		$_GET["new"]="1";
		unset($_POST["ins"]);
	}
}

if(isset($_GET["move2grp"]) && isset($_GET["ids"]) && $PERMIS[2]) {
	if(ereg("^[0-9,]{1,}$",$_GET["ids"])) {
		$db->query("UPDATE settings SET admingroup='".intval($_GET["move2grp"])."' WHERE id in (".$_GET["ids"].")");
		echo "ok";
	}
	exit;
}

if(!isset($_GET["ch"]) && !isset($_GET["new"])) {
	// на списке выведем доп. кнопку со списком групп, для пакетного перемещения пользователей по группам
	$gr=array();
	$db->query("SELECT id,grpname FROM usergroups ORDER BY grpname");
	while($db->next_record()) {
		$gr[]="{handler: function() {chgrpf('".$db->Record[0]."');},text: '".$db->Record[1]."'}";
	}
	
	$USER_GRID_BUTTONS["togroup"]=array('tools/usersgroups/users.png',"сменить группу',menu:new Ext.menu.Menu({items: [".join(",",$gr)."]}),name:'", "", "");
	
	$GridGlobal="<script>
	function chgrpf(id) {
		var s=MainGrid.getSelectionModel().getSelections();
		if(s.length==0) return false;
		var ids='';
		for(var i=0;i<s.length;i++) ids+=(i>0? ',':'')+s[i].id;
		
		Ext.Ajax.request({
			url: '?ext=sysusers&move2grp='+id+'&ids='+ids,
			success: function(response) {		      
			  if(response.responseText=='ok') {
			    store.reload();
			  }
			}
		});
	}
	</script>";
}

if(isset($_GET["checkuser"])) {
	$db->query("SELECT id FROM settings WHERE adminlogin='".addslashes($_GET["checkuser"])."'".(isset($_GET["ch"])? " and id!=".intval($_GET["ch"]):""));
	if($db->next_record()) echo "err";
	exit;
}

function last_function() {
	echo "<script>	
	document.getElementById('adminlogin').onchange=function() {
		var o=document.getElementById('adminlogin');
		Ext.Ajax.request({
			url: '?ext=sysusers".(isset($_GET["ch"])? "&ch=".$_GET["ch"]:"")."&checkuser='+o.value,
			success: function(response) {		      
			  if(response.responseText=='err') {
			    alert('Пользователь с таким логином уже зарегистрирован в системе.');
			    o.focus(); 
			  }
			}
		});		
		return false;
	}
	</script>";
}



if(isset($_POST["action"]) && intval($_POST["action"])) {
	include_once "./autorisation.php";
	foreach($_POST["delCheck"] as $k=>$v) {
		$db->query("UPDATE settings SET AdminGroup='".intval($_POST["action"])."' WHERE id='".intval($v)."'");
	}
	$_POST=array();
}

function user_function() {
	global $db,$GLOBALLINKS,$FORM_EXTEND;
	
	if (!isset($_GET["new"]) && !isset($_GET["ch"])) {
		
		$GLOBALLINKS="<table width='100%' border=0><tr><td></td>";
		$GLOBALLINKS.="<td align=right><b>включить отмеченных пользователей в группу:</b> <select onchange='if(this.value!=\"\" && confirm(\"Переместить выбранных пользователей в группу «\"+this.options[this.selectedIndex].text+\"»?\")) {document.forms[\"delSelectFrrm\"].action.value=this.value;document.forms[\"delSelectFrrm\"].submit();}'>
		<option value=''>--== Выбрать группу ==--</option>";
		
		$db->query("SELECT id, GrpName FROM usergroups ORDER BY id");
		while($db->next_record()) {
			$GLOBALLINKS.="<option value='".$db->Record["id"]."'>".$db->Record["grpname"]."</option>";
		}
		$GLOBALLINKS.="</select></td></tr></table>";
				
    }
}

