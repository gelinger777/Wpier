<?
/*
*	Компонент "Выбор из списка"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_selecteditable {
  var $PROP=array(
    "length"=>255,
    "caption"=>"",
    "type"=>"varchar",
    "width"=>250,
	"url"=>"",
	"tpl"=>"",
	"json"=>"",
	 "onSelect"=>"",
	"displayField"=>""
    );

  var $type="selecteditable";

  var $js="inc/components/js/selecteditable.js";

  function T_selecteditable($prp=array()) {
          if(count($prp)) foreach($prp as $k=>$v) $this->PROP[$k]=$v;
   }
  
  function str2params ($name,$pString) {
  global $db;
    $this->PROP["name"]=$name;
    $s=explode("|",$pString);
    $this->PROP["url"]=$s[1];
    $this->PROP["tpl"]=$s[2];
	$this->PROP["json"]=$s[3];
	$this->PROP["displayField"]=$s[4];
	$this->PROP["width"]=$s[5];
	$this->PROP["caption"]=$s[6];

  }

  function params2str () {
    return "selecteditable|".$this->PROP["url"]."|".$this->PROP["tpl"]."|".$this->PROP["json"]."|".$this->PROP["displayField"]."|".$this->PROP["width"]."|".$this->PROP["caption"];
  }

  function mkForm($name,$val="") {  
   return array("<b>".$this->PROP["caption"]."</b>","<div><div><input type='text' name='".$name."' id='".$name."' /></div></div>","MakeSelectorFolder('".$name."','".$this->PROP["caption"]."','".$val."','".$this->PROP["url"]."',".$this->PROP["json"].",".$this->PROP["tpl"].",'".$this->PROP["displayField"]."',".$this->PROP["width"].($this->PROP["onSelect"]? ",".$this->PROP["onSelect"]:"").");");
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
		return "$key like '".AddSlashes($val["value"])."%'";
	}

	function getFilter($key,$val) {
	  return array("StringFilter.js","{type: 'string',dataIndex:'$key'}");
	}
  
}
?>