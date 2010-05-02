<?
/*
*	Компонент "Флаг" (Checkbox)
*	Автор Тушев Максим
*	v. 1.0
*/

class T_checkbox {
	var $PROP=array(
		"caption"=>"",
		"type"=>"char(1)",
		"attr"=>"",
		"default"=>0,
		"visibility"=>"yes",
		"name"=>"",
		"checkedmark"=>"ДА",
		"nocheckedmark"=>"НЕТ",
		"righttext"=>""
		);
        
        var $type="checkbox";
        
	function str2params ($name,$pString) {
		$this->PROP["name"]=$name;
		$pString=explode("|",$pString);
				
		if(isset($pString[3]) && $pString[3]) $this->PROP["default"]=1;
		if(isset($pString[4])) $this->PROP["type"]=$pString[4];
		$this->PROP["attr"]=$pString[1];

		$s=explode("*rtext*",$pString[2]);
		$this->PROP["caption"]=$s[0];
		if(isset($s[1])) $this->PROP["righttext"]=$s[1];

		global $f_array;
		
		if(isset($f_array) && isset($f_array[$this->PROP["name"]])) {
			$s=explode("|",$f_array[$this->PROP["name"]]);
			if(count($s)>=3) {
				$this->PROP["checkedmark"]=$s[2];
				$this->PROP["nocheckedmark"]=$s[1];
			}
		}
	}

	function params2str () {
		return "checkbox|".$this->PROP["attr"]."|".$this->PROP["caption"].($this->PROP["righttext"]? "*rtext*".$this->PROP["righttext"]:"")."|".($this->PROP["default"]? "1":"").($this->PROP["type"]!='char(1)'? "|".$this->PROP["type"]:"");
	}

	function mkForm($name,$val="") {
		//if(!$val) $val=1;
		
		return array("<b><nobr>".$this->PROP["caption"]."</nobr></b>","<INPUT type='checkbox' name='$name'  ".$this->PROP["attr"]." ".(($val || (isset($_GET["new"]) && $this->PROP["default"]))? "checked":"")." value='1'> ".$this->PROP["righttext"]);		
	}

	function mkList($val,$log=0) {
		return $val;
		//if($val) return $this->PROP["checkedmark"];
		//else return $this->PROP["nocheckedmark"];
	}

	function mkDBFolder() {
		return $this->PROP["type"];
	}

	function getUpdateVals($key,$val) {
		if($val) $val=1;
		else $val="";
		return array($key, $val);
	}

	function getFilterSearch($key,$val) {
		if($val["value"]=='false') return $val="($key='' or $key is NULL)";
        return "$key='1'";
	}

	function getFilter($key,$val) {
          return array("BooleanFilter.js","{type: 'boolean', dataIndex: '$key'}");
	}
	function mkEditor() {
	  global $GridGlobal;
	  
	  if(!isset($GridGlobal)) $GridGlobal="";
	  $GridGlobal.="<script>selectdata_".$this->PROP["name"]."=[['1',ParentW.DLG.t('Yes')],['',ParentW.DLG.t('No')]];</script>";
	  
      return "editor: new Ext.form.ComboBox({
               typeAhead: true,
    		   mode: 'local',
			   triggerAction: 'all',
               listClass: 'x-combo-list-small',
			   displayField:'val',
			   valueField: 'key',
			   store: new Ext.data.SimpleStore({
							fields: ['key','val'],
							data : selectdata_".$this->PROP["name"]."
				})
          }),renderer: function(x) {if(x==1) return ParentW.DLG.t('Yes');return ParentW.DLG.t('No');}";

       }
       /*function acceptEditor($val) {
         if($val==$this->PROP["checkedmark"]) return '1';
         return '';
       }*/
}


?>