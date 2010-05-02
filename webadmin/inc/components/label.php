<?
/*
*	Компонент "Ссылка"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_label {
	var $PROP=array(
		"type"=>"varchar(40)",
		"name"=>"",
		"caption"=>"",
		"value"=>"",
		"color"=>"#000000"
		);

	function str2params ($name,$pString) {
		$this->PROP["name"]=$name;
		$pString=explode("|",$pString);
		
		if($pString[1]) $this->PROP["type"]=$pString[1];
		if(isset($pString[3])) $this->PROP["color"]=$pString[3];
		$this->PROP["caption"]=$pString[2];
	}

	function params2str () {
		return "label|".($this->PROP["type"]!='varchar(40)'? $this->PROP["type"]:"")."|".$this->PROP["caption"].($this->PROP["color"]!='red'? $this->PROP["color"]:""); 
	}

	function mkForm($key,$val="") {
		return array("<b><nobr>".$this->PROP["caption"]."</nobr></b>","<INPUT type='hidden' name='$key' value='".($val? $val:"")."'>".$val); 	
	}

	function mkList($val,$log=0) {
		if($log) {return $val;}
		//return "<b style='color:".$this->PROP["color"]."'>".$val."</b>";
		return $val;
	}

	function mkDBFolder() {
		return $this->PROP["type"];
	}

	function getUpdateVals($key,$val) {
		return array($key, addslashes(Proof($val)));;
	}

	function getFilterSearch($key,$val) {
		return "upper($key) like '%".strtoupper($val["value"])."%'";
	}

	function getFilter($key,$val) {
	  return array("StringFilter.js","{type: 'string',dataIndex:'$key'}");
	}
}
?>