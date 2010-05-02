<?
/*
*	Компонент "Скрытое поле"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_hidden {
	var $PROP=array(
		"type"=>"int(11)",
		"name"=>""
		);

	function str2params ($name,$pString) {
		$this->PROP["name"]=$name;
	}

	function params2str () {
		return "hidden||";
	}

	function mkForm($name,$val="") {
	        global $FormHiddens;
	        $FormHiddens.="<INPUT type='hidden' id='$name' name='$name' value='".$val."'>";
		return "";
	}

	function mkList($val,$log=0) {
		return $val;
	}

	function mkDBFolder() {
		return $this->PROP["type"];
	}

	function getUpdateVals($key,$val) {
		return array($key, $val);
	}

	function getFilterSearch($key,$val) {
		return "$key='".AddSlashes($val["value"])."'";
	}

	function getFilter($key,$val) {
		return array("StringFilter.js","{type: 'string',dataIndex:'$key'}");
	}
}

