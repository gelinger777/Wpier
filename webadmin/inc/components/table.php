<?
/*
*	Компонент "Таблица"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_table {
	var $PROP=array(
		"caption"=>"",
		"visibility"=>"yes",
		"name"=>""
		);

	function str2params ($name,$pString) {
		$this->PROP["name"]=$name;
		$pString=explode("|",$pString);
		$this->PROP["caption"]=$pString[1];
	}

	function params2str () {
		return "table||".$this->PROP["caption"];
	}

	function mkForm($key,$val="") {
		return array("<b><nobr>".$this->PROP["caption"]."</nobr></b>","<INPUT type='file' name='tabcsv_$key'> закачать CSV<BR><BR><div id='tabdiv_$key'></div><br><INPUT type='button' value='изменить таблицу' onclick='edit_table(\"$key\")'><br><INPUT type='hidden' name='$key' value='".$val."'><SCRIPT LANGUAGE=\"JavaScript\">mk_tab('$key');</SCRIPT>");
	}

	function mkList($val,$log=0) {
		return $val;
	}

	function mkDBFolder() {
		return "text";
	}

	function getUpdateVals($key,$val) {
		return array($key, undangerstr($val));
	}

	function getFilterSearch($key,$val) {
		return "";
	}

	function getFilter($key,$val) {
		return "";
	}
}
?>