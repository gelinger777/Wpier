<?
/*
*	Компонент "Unix время"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_unitime {
	var $PROP=array(
		"type"=>"int(11)",
		"name"=>"",
		"caption"=>"",
		"format"=>"d.m.Y H:i"
		);
	var $type="unitime";

	function str2params ($name,$pString) {
		$this->PROP["name"]=$name;
		$pString=explode("|",$pString);
		if($pString[1]) $this->PROP["format"]=$pString[1];
		$this->PROP["caption"]=$pString[2];
	}

	function params2str () {
		return "unitime|".$this->PROP["format"]."|".$this->PROP["caption"]; 
	}

	function mkForm($name,$val="") {
		return array("<b>".$this->PROP["caption"]."</b>","<b>".date($this->PROP["format"],($val? $val:mktime()))."</b>");		
	}

	function mkList($val,$log=0) {
		return date($this->PROP["format"],$val);
	}

	function mkDBFolder() {
		return "int(11)";
	}

	function getUpdateVals($key,$val) {
		if(isset($_POST["ins"])) return array($key, mktime());
		else return "";
	}

        function codedate($dt,$x=0) {
           $dt=explode(".",$dt);
           if(count($dt)>=3) 
             if($x) return mktime(23,59,59,$dt[1],$dt[0],$dt[2]);
             else return mktime(0,0,0,$dt[1],$dt[0],$dt[2]);
        }

	function getFilterSearch($key,$val) {
		$val["value"]=mktime(0,0,0,substr($val["value"],0,2),substr($val["value"],3,2),substr($val["value"],6,4));
	        if($val["comparison"]=='lt') return "$key<='".$val["value"]."'";
	        if($val["comparison"]=='gt') return "$key>='".$val["value"]."'";
	        if($val["comparison"]=='eq') return "$key between '".$val["value"]."' and '".($val["value"]+3600*24)."'";
	        return "";
	}

	function getFilter($key,$val) {
		return array("DateFilter.js","{type: 'date',  dataIndex: '$key'}");

	}
}
?>