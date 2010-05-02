<?
/*
*	Компонент "Текстовый блок"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_textarea {
	var $PROP=array(
		"caption"=>"",
		"type"=>"text",
		"attr"=>"",
		"cols"=>"",
		"rows"=>"",
		"visibility"=>"yes",
		"name"=>"",
		"righttext"=>""
		);
	var $required=0;

	function str2params ($name,$pString) {
		$this->PROP["name"]=$name;
		$pString=explode("|",$pString);
				
		if(isset($pString[3])) $this->PROP["type"]=$pString[3];

		$s=explode("cols=",$pString[1]);
		if(isset($s[1])) $this->PROP["cols"]=intval($s[1]);

		$s=explode("rows=",$pString[1]);
		if(isset($s[1])) $this->PROP["rows"]=intval($s[1]);

		$this->PROP["attr"]=trim(str_replace("cols=".$this->PROP["cols"],"",str_replace("rows=".$this->PROP["rows"],"",$pString[1])));

		$s=explode("*rtext*",$pString[2]);
		$this->PROP["caption"]=$s[0];
		if(isset($s[1])) $this->PROP["righttext"]=$s[1];
	}

	function params2str () {
		return "textarea|cols=".$this->PROP["cols"]." rows=".$this->PROP["rows"]." ".$this->PROP["attr"]."|".$this->PROP["caption"].($this->PROP["righttext"]? "*rtext*".$this->PROP["righttext"]:"").($this->PROP["type"]!='text'? "|".$this->PROP["type"]:"");
	}

	function mkForm($name,$val="") {
		return array("<b>".$this->PROP["caption"]."</b>","<TEXTAREA name='$name' cols=".$this->PROP["cols"]." rows=".$this->PROP["rows"]." ".$this->PROP["attr"].">".$val."</TEXTAREA> ".$this->PROP["righttext"]);		
	}

	function mkList($val,$log=0) {
		return $val;
	}

	function mkDBFolder() {
		return $this->PROP["type"];
	}

	function getUpdateVals($key,$val) {
		return array($key, undangerstr(Proof($val)));
	}

	function getFilterSearch($key,$val) {
		return "upper($key) like '%".strtoupper(AddSlashes($val["value"]))."%'";
	}

	function getFilter($key,$val) {
		return array("StringFilter.js","{type: 'string',dataIndex:'$key'}");
	}
	function mkEditor() {
	  return "editor:new Ext.form.TextField({allowBlank: false})";
	}
}
?>