<?
/*
*	��������� "��������� ����"
*	����� ����� ������
*	v. 1.0
*/

class T_text {
	var $PROP=array(
		"length"=>255,
		"caption"=>"",
		"type"=>"varchar",
		"attr"=>"",
		"size"=>"",
		"righttext"=>"",
		"visibility"=>"yes",
		"name"=>"",
		"Label"=>0
		);

        var $type="text";
        var $required=0;

		function T_text($prp=array()) {
          if(count($prp)) foreach($prp as $k=>$v) $this->PROP[$k]=$v;
        }

	function str2params ($name,$pString) {
		$this->PROP["name"]=$name;
		$pString=explode("|",$pString);
		$this->PROP["attr"]=$pString[1];

		if(isset($pString[3])) $this->PROP["type"]=$pString[3];
		$s=explode("maxlength=",$pString[1]);
		if(isset($s[1])) $this->PROP["length"]=intval($s[1]);
		$s=explode("size=",$pString[1]);
		if(isset($s[1]) && intval($s[1])) $this->PROP["size"]=intval($s[1]);
		$s=explode("*rtext*",$pString[2]);
		$this->PROP["caption"]=$s[0];
		if(isset($s[1])) $this->PROP["righttext"]=$s[1];
		if(strpos(' '.$pString[1],'label=yes')) $this->PROP["Label"]=1;
	}

	function params2str () {
		return "text|size=".$this->PROP["size"]." maxlength=".$this->PROP["length"].($this->PROP["Label"]? " label=yes":"")."|".$this->PROP["caption"].($this->PROP["righttext"]? "*rtext*".$this->PROP["righttext"]:"").($this->PROP["type"]!='varchar'? "|".$this->PROP["type"]:"");
	}

	function mkForm($name,$val="") {
		$val=stripslashes($val);
		return array("<b>".$this->PROP["caption"]."</b>",($this->PROP["Label"]? "<b style='color:red'>$val</a><INPUT type='hidden' name='$name' value='".$val."'>":"<INPUT type='text' id='$name' name='$name' value='".$val."' ".$this->PROP["attr"]."> ".$this->PROP["righttext"]));
	}

	function mkList($val,$log=0) {
		return $val;
	}

	function mkEditor() {
	  return "editor:new Ext.form.TextField({allowBlank: false})";
	}

	function mkDBFolder() {
		if($this->PROP["type"]=='varchar') {
			return "varchar(".$this->PROP["length"].")";
		}
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
}
