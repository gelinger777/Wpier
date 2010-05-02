<?
/*
*	Компонент "Вкладка"
*	Автор Тушев Максим
*	v. 1.0
*  "..."=>"page|start|Основные"
*/

class T_page {
	var $PROP=array(
		"caption"=>"",
		"visibility"=>"yes",
		"type"=>"",
		"position"=>"start"
		);

	function str2params ($name,$pString) {
		$pString=explode("|",$pString);				
		$this->PROP["caption"]=$pString[2];
		if($pString[1]) {
			$this->PROP["blocks"]=$pString[1];
		}
		$this->PROP["hide"]=$pString[3];
		if($this->PROP["hide"]) {
			global $F_ARRAY_NODISPLAY;
			if(!isset($F_ARRAY_NODISPLAY)) $F_ARRAY_NODISPLAY=array();
			$F_ARRAY_NODISPLAY=array_merge($F_ARRAY_NODISPLAY,explode(",",$this->PROP["blocks"]));
		}
	}

	function params2str () {
		return "block|".$this->PROP["blocks"]."|".$this->PROP["caption"]."|".$this->PROP["hide"];
	}

	function mkForm($name,$val="") {
		return "<tr><td colspan=2><h2 id='BLOCK_$name' onclick='ShowHideFormTr(this,\"".$this->PROP["blocks"]."\")' class='".($this->PROP["hide"]? "show":"hide")."' style='cursor:hand'>".$this->PROP["caption"]."</h2></td></tr>";			
	}

	function mkList($val,$log=0) {
		return "";
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