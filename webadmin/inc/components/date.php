<?
/*
*	Компонент "Строковая дата"
*	Автор: Тушев Максим
*	v. 1.0
*/

class T_date {
	var $PROP=array(
		"caption"=>"",
		"type"=>"varchar(8)",
		"attr"=>"",
		"size"=>"10",
		"righttext"=>"",
		"visibility"=>"yes",
		"name"=>"",
		"PrevDates"=>0,
		"NULL"=>0,
		"calendar"=>0,
		"Label"=>0
		);
	 
	 var $required=0;
	 var $type='date';

        function T_date($prp=array()) {
          if(count($prp)) foreach($prp as $k=>$v) $this->PROP[$k]=$v;
        }

	function str2params ($name,$pString) {
		$this->PROP["name"]=$name;
		$pString=explode("|",$pString);
				
		if(isset($pString[3]) && $pString[3]) $this->PROP["type"]=$pString[3];
		if(isset($pString[4]) && $pString[4]) $this->PROP["calendar"]=1;
		if(isset($pString[5]) && $pString[5]) $this->PROP["PrevDates"]=1;
		if(isset($pString[6]) && $pString[6]) $this->PROP["NULL"]=1;

		$s=explode("size=",$pString[1]);
		if(isset($s[1]) && intval($s[1])) $this->PROP["size"]=intval($s[1]);
		$this->PROP["attr"]=trim(str_replace("size=".$this->PROP["size"],"",$pString[1]));

		$s=explode("*rtext*",$pString[2]);
		$this->PROP["caption"]=$s[0];
		if(isset($s[1])) $this->PROP["righttext"]=$s[1];
		if(strpos(' '.$pString[1],'label=yes')) $this->PROP["Label"]=1;
	}

	function params2str () {
		return "date|".($this->PROP["size"]? "size=".$this->PROP["size"]." ":"")."".$this->PROP["attr"].($this->PROP["Label"]? " label=yes":"")."|".$this->PROP["caption"].($this->PROP["righttext"]? "*rtext*".$this->PROP["righttext"]:"").($this->PROP["type"]!='varchar'? "|".$this->PROP["type"]:"");
	}

	function mkForm($name,$val="") {
		if($val) $val=substr($val,6,2).".".substr($val,4,2).".".substr($val,0,4);
		elseif(isset($_GET["new"])) $val=date("d.m.Y");
		
		if($this->PROP["Label"]) {
			return array("<b>".$this->PROP["caption"]."</b>","<INPUT type='hidden' id='$name' name='$name' value='".($val? $val:"")."'>".$val);	
		}
		
		return array("<b>".$this->PROP["caption"]."</b>","<INPUT id='$name' type='text' name='$name' value='".$val."' size='".$this->PROP["size"]."' /> ".$this->PROP["righttext"],"
		new Ext.form.DateField({
			   id:'$name',
			   allowBlank:true,
			   name:'$name',
			   applyTo:'$name',
			   format:'d.m.Y'
			   //".($this->PROP["PrevDates"]? "":",minValue:'".date("d.m.Y")."'")."
		});	
		");		
	}

	function mkList($val,$log=0) {
		if($val) $val=substr($val,6,2).".".substr($val,4,2).".".substr($val,0,4);
		return $val;
	}

	function mkDBFolder() {
		return $this->PROP["type"];
	}

	function getUpdateVals($key,$val) {
		global $_POST;
		
		if($val) {
			$val=substr($val,6,4).substr($val,3,2).substr($val,0,2);
			if(isset($_POST[$key])) $_POST[$key]=$val;
		}
		return array($key, $val);
	}

        function codedate($dt) {
           $dt=explode(".",$dt);
           if(count($dt)>=3) return intval($dt[2].$dt[1].$dt[0]);
        }
        
        function uncodedate($dt) {
           return substr($dt,6,2).".".substr($dt,4,2).".".substr($dt,0,4);
        }
        
        function mkEditor() {
	  return "editor:new Ext.form.TextField({allowBlank: false})";
	}

	function getFilterSearch($key,$val) {      
                $val["value"]=intval(substr($val["value"],6,4).substr($val["value"],0,2).substr($val["value"],3,2));
	        if($val["comparison"]=='lt') return "$key<='".$val["value"]."'";
	        if($val["comparison"]=='gt') return "$key>='".$val["value"]."'";
	        if($val["comparison"]=='eq') return "$key='".$val["value"]."'";
	        return "";
	}

	function getFilter($key,$val) {
		return array("DateFilter.js","{type: 'date',  dataIndex: '$key'}");
	}
	
	function acceptEditor($val) {
	  return $this->codedate($val);
	}
}
?>
