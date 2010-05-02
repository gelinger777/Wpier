<?
/*
*	Компонент "Разделитель"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_separator {
	var $PROP=array(
		"caption"=>"",
		"visibility"=>"yes",
		"spacer"=>"<hr>"
		);

	function str2params ($name,$pString) {
		$pString=explode("|",$pString);				
		$this->PROP["caption"]=$pString[2];
		if($pString[1]) $this->PROP["spacer"]=$pString[1];
	}

	function params2str () {
		return "separator|".($this->PROP["spacer"]=="<hr>"? "":$this->PROP["spacer"])."|".$this->PROP["caption"];
	}

	function mkForm($name,$val="") {
		  return array($this->PROP["caption"],"");		
	}

	function mkDBFolder() {
		return "";
	}

	function getUpdateVals($key,$val) {
		return "";
	}

	function getFilterSearch($key,$val) {
		return "";
	}

	function getFilter($key,$val) {
		return "";
	}
}
?>