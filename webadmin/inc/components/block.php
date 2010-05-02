<?
/*
*	Компонент "Блок"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_block {
	var $PROP=array(
		"caption"=>"",
		"visibility"=>"yes",
		"blocks"=>"",
		"hide"=>"1"
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
	        global $PANELS,$PanelKey,$InPanel;
                $PanelKey++;
                $InPanel=array_merge($InPanel,explode(',',$this->PROP["blocks"]));
                $PANELS[$this->PROP["caption"]]=array("prop"=>array("hide"=>$this->PROP["hide"]),"items"=>explode(',',$this->PROP["blocks"]));
		return "";//<tr><td colspan=2 style='padding-top:10px'><a href='' id='BLOCK_$name' onclick='ShowHideFormTr(this,\"".$this->PROP["blocks"]."\");parent.focus();return false;' class='".($this->PROP["hide"]? "show":"hide")."'>".$this->PROP["caption"]."</a></td></tr>";
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
