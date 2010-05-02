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
"delwhere"=>"access!=-1",
"nolang"=>1
);

$F_ARRAY=array(
"id"=>"hidden||",
//"AdminRegion"=>"select|spravplaces*rCod*rName|Регион",
"admingroup"=>"select|usergroups*id*GrpName**GrpName/maxlength=255|Группа",
"adminlogin"=>"text| size=50 maxlength=|Логин",
"adminname"=>"text| size=50 maxlength=|ФИО",
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
"adminemail"=>"E-mail",
"admingroup"=>"Группа",

);

if(isset($_POST["ins"])) {
	$F_ARRAY["access"]="text||";
	$_POST["access"]=1;
}

//ENDHEAD//


/*
$Agrp=0;
if(isset($_POST["id"]) && $_POST["id"]) {
  $db->query("SELECT AdminGroup FROM settings WHERE id='".intval($_POST["id"])."'");
  if($db->next_record()) $Agrp=$db->Record[0];
}

function user_function() {
global $db,$id,$Agrp;  
  if(count($_POST) && $Agrp!=$_POST["admingroup"]) {
     $db->query("DELETE FROM settingscatalogue WHERE rowID='$id'");
     
     $db->query("SELECT GrpChld FROM usergroups WHERE id='".intval($_POST["admingroup"])."'");
     if($db->next_record()) $_POST["DirChld"]=$db->Record[0];
     
     $db->query("SELECT pgID FROM usergroupscatalogue WHERE rowID='".intval($_POST["admingroup"])."'");
     $Pgs=array();
     while($db->next_record()) $Pgs[]=$db->Record[0];
     foreach($Pgs as $v) 
       $db->query("INSERT INTO settingscatalogue (pgID,rowID) VALUES ('$v','$id')");    
  }
} */
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
?>