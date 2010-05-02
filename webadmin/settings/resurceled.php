<?
session_start();
require ("./autorisation.php");
if($ACCESS>1) {
	header("Location: ./access_defined.php");
	exit();
}

$PROPERTIES=array(
"tbname"=>"resurceled",
"pagetitle"=>"Корзина",
"orderby"=>"id DESC",
"filters" => "yes",
"spell"=>0,
"NOADD"=>"yes",
"nolang"=>1
);

$F_ARRAY=Array (
"id" => "hidden||",
"modname"=>"select|/DISABLED=true maxlength=40|Модуль",
"tabname"=>"select|/DISABLED=true maxlength=40|Таблица",
"deltime"=>"unitime||Время удаления",
"user"=>"text|size=20 maxlength=20 DISABLED=true|Пользователь",
"dataled"=>"textarea| cols=70 rows=5|Запись",

);

$f_array=Array(
"id" => "*hide*",
"modname"=>"Модуль",
"tabname"=>"Таблица",
"deltime"=>"Время",
"user"=>"Пользователь",

);

if(isset($_GET["ch"])) unset($F_ARRAY["dataled"]);

if(isset($_CONFIG["RESURCE_LED"])) $_CONFIG["RESURCE_LED"]=0;

if(isset($_GET["ch"])) {
	$F_ARRAY["modname"]="text|size=20 DISABLED=true|Модуль";
	$F_ARRAY["tabname"]="text|size=20 DISABLED=true|Таблица";
} else {
	include_once "./menu.inc";
	foreach($menu_items as $k=>$v) $F_ARRAY["modname"].="|$k/".$v[0];
	foreach($menu_tool as $k=>$v) $F_ARRAY["modname"].="|$k/$v";
	$tabs=$db->table_names();
	foreach($tabs as $k=>$v) $F_ARRAY["tabname"].="|$k/$k";
}

if(isset($_GET["rlclear"])) {
	$db->query("DELETE FROM resurceled");
} elseif(isset($_POST["action"]) && $_POST["action"]) {
	if($_POST["action"]=='del') {
		foreach($_POST["delCheck"] as $v) {
			$db->query("DELETE FROM resurceled WHERE id='".intval($v)."'");
		}
	}
	elseif($_POST["action"]=='ret') {
		foreach($_POST["delCheck"] as $v) {
			$db->query("SELECT dataled FROM resurceled WHERE id='".intval($v)."'");
			if($db->next_record()) {
				$XA=unserialize(str_replace("**#39#**","'",$db->Record[0]));
				
				$keys=array();
				$vals=array();
				foreach($XA["raw"] as $key=>$val) if(is_string($key)) {
					$keys[]=$key;
					$vals[]=$val;
				}
				
				$db->query("INSERT INTO ".$XA["tab"]." (".join(",",$keys).") VALUES ('".join("','",$vals)."')");

				foreach($XA["deptabs"] as $key=>$val) {
					foreach($val as $va) {
						$keys=array();
						$vals=array();
						foreach($va as $kk=>$vv) if(is_string($kk)) {
							$keys[]=$kk;
							$vals[]=$vv;
						}
						$db->query("INSERT INTO ".$key." (".join(",",$keys).") VALUES ('".join("','",$vals)."')");
					}
				}
				$db->query("DELETE FROM resurceled WHERE id='".intval($v)."'");
			}			
		}
	}

	$_POST=array();
}

function user_function() {
	global $db,$GLOBALLINKS,$FORM_EXTEND;
	
	$db->query("SELECT count(*) FROM resurceled");
    $db->next_record();
	if($db->Record[0]) {
		$GLOBALLINKS="<table width='100%' border=0><tr><td><a href='?rlclear=yes' style='padding:5px;text-decoration:none' class='button' onclick='if(!confirm(\"Очистить всю корзину?\")) return false;'>очистить корзину</a></td>";
		if (!isset($_GET["new"]) && !isset($_GET["ch"])) {
			$GLOBALLINKS.="<td align=right><b>с отмеченными</b> <select onchange='document.forms[\"delSelectFrrm\"].action.value=this.value;document.forms[\"delSelectFrrm\"].submit();'>
			<option value=''>--== Выбрать действие ==--</option>
			<option value='ret'>востановить</option>
			<option value='del'>удалить</option>
			</select></td>
			";
		}
		$GLOBALLINKS.="</tr></table>";
    } else {
		echo "<h2>корзина пуста</h2>";
		exit;
	}

	if(isset($_GET["ch"])) {
		$FORM_EXTEND="<tr><td colspan=2><hr><h2>Содержимое удаленной записи</h2></td></tr>";
		$db->query("SELECT dataled FROM resurceled WHERE id='".intval($_GET["ch"])."'");
		if($db->next_record()) {
			$XA=unserialize(str_replace("**#39#**","'",$db->Record[0]));
			$l=0;
			foreach($XA["raw"] as $key=>$val) if(is_string($key) && $key!="lock_time" && $key!="lock_user") {
				$FORM_EXTEND.="<tr ".($l? "bgcolor='#ededed'":"")."><td><b style='color:red'>$key</b></td><td>$val</td></tr>";
				if($l) $l=0;else $l=1;
			}
		}
		$FORM_EXTEND.="<tr><td colspan=2><hr></td></tr>";
	}
}

function last_function() {
}



include "./output_interface.php";
?>