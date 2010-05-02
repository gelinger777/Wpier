<?
/*
*	��������� "��������� �����"
*	����� ����� ������
*	v. 1.0
*/

class T_time {
	var $PROP=array(
		"caption"=>"",
		"type"=>"varchar(4)",
		"attr"=>"",
		"size"=>"5",
		"righttext"=>"",
		"visibility"=>"yes",
		"name"=>""
		);

	function str2params ($name,$pString) {
		$this->PROP["name"]=$name;
		$pString=explode("|",$pString);
				
		if(isset($pString[3])) $this->PROP["type"]=$pString[3];

		$s=explode("size=",$pString[1]);
		if(isset($s[1]) && intval($s[1])) $this->PROP["size"]=intval($s[1]);
		$this->PROP["attr"]=trim(str_replace("size=".$this->PROP["size"],"",$pString[1]));

		$s=explode("*rtext*",$pString[2]);
		$this->PROP["caption"]=$s[0];
		if(isset($s[1])) $this->PROP["righttext"]=$s[1];
	}

	function params2str () {
		return "time|".($this->PROP["size"]? "size=".$this->PROP["size"]." ":"")."".$this->PROP["attr"]."|".$this->PROP["caption"].($this->PROP["righttext"]? "*rtext*".$this->PROP["righttext"]:"").($this->PROP["type"]!='varchar'? "|".$this->PROP["type"]:"");
	}

	function mkForm($name,$val="") {
		if($val) $val=substr($val,0,2).":".substr($val,2,2);
		else $val=date("H:i");
		return array("<b>".$this->PROP["caption"]."</b>","<INPUT type='text' name='$name' value='".$val."' ".$this->PROP["attr"]." size='".$this->PROP["size"]."'> ".$this->PROP["righttext"]);		
	}

	function mkList($val,$log=0) {
		if($val) $val=substr($val,0,2).":".substr($val,2,2);
		return $val;
	}

	function mkDBFolder() {
		return $this->PROP["type"];
	}

	function getUpdateVals($key,$val) {
		if($val) $val=substr($val,0,2).substr($val,3,2);
		return array($key, $val);
	}

	function getFilterSearch($key,$val) {
		return "$key like '".addslashes($val)."'";
	}

	function getFilter($key,$val) {
		return "<input name='$key' value='".addslashes($val)."' style='width:100%'>";
	}
}

