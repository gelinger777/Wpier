<?
/*
*	Компонент "Ссылка"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_link {
	var $PROP=array(
		"length"=>40,
		"name"=>"",
		"caption"=>"",
		"url"=>"",
		);

	function str2params ($name,$pString) {
		$this->PROP["name"]=$name;
		$pString=explode("|",$pString);
		
		$s=	explode("*",$pString[1]); 
		$this->PROP["url"]=$s[0];
		if(isset($s[1]) && intval($s[1])) $this->PROP["length"]=intval($s[1]);
		$this->PROP["caption"]=$pString[2];
	}

	function params2str () {
		return "link|".$this->PROP["url"]."*".$this->PROP["length"]."|".$this->PROP["caption"]; 
	}

	function mkForm($key,$val="") {
		return array("","<INPUT type='hidden' name='$key' value='".($val? $val:"")."'><a href='./".$this->PROP["url"].$val."' target='_blank'><b>".$this->PROP["caption"]."</b></a>"); 	
	}

	function mkList($val,$log=0) {
		return "<a href='./".$this->PROP["url"].$val."' target='_blank'>".$this->PROP["caption"]."</a>";
	}

	function mkDBFolder() {
		return "varchar(".$this->PROP["length"].")";
	}

	function getUpdateVals($key,$val) {
		else return "";
	}

	function getFilterSearch($key,$val) {
		return "$key like '%".str_replace(" ","%",$val)."%'";
	}

	function getFilter($key,$val) {
		return "<input name='$key' value='".htmlspecialchars($val)."' style='width:100%'>";
	}
}
?>