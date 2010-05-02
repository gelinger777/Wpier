<?
/*
*	Компонент "Связанный список"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_list {
	var $PROP=array(
		"caption"=>"",
		"type"=>"int",
		"righttext"=>"",
		"visibility"=>"yes",
		"name"=>"",
		"mod"=>"",
		"properties"=>array(),
		"width"=>500,
		"height"=>400,
		"style"=>"",
		);

	function str2params ($name,$pString) {
		$this->PROP["name"]=$name;
		$pString=explode("|",$pString);
		if(isset($pString[3])) $this->PROP["type"]=$pString[3];
		$this->PROP["caption"]=$pString[2];
		
		$pString=explode("{",$pString[1]);
		$x=explode("[",$pString[0]);
		$this->PROP["mod"]=$x[0];
		$i=1;
		while($i<count($x)) {
			$this->PROP["properties"][$x[$i]]=str_replace("]","",$x[(++$i)]);
			$i++;
		}
		if(isset($pString[1])) {
			$x=explode(",",str_replace("}","",$pString[1]));
			if(isset($x[0]) && trim($x[0])) $this->PROP["width"]=trim($x[0]);
			if(isset($x[1]) && trim($x[1])) $this->PROP["height"]=trim($x[1]);
			if(isset($x[2]) && trim($x[2])) $this->PROP["style"]=trim($x[2]);
		}
	}

	function params2str () {
		$x="";
		foreach($this->PROP["properties"] as $k=>$v) $x.="[".$k."[".$v."]]";
		return "list|".$this->PROP["mod"].$x."{".$this->PROP["width"].",".$this->PROP["height"].",".$this->PROP["style"]."}|".$this->PROP["caption"].($this->PROP["type"]!='int'? "|".$this->PROP["type"]:"");
	}

	function mkForm($name,$val="") {
		$x="";
		foreach($this->PROP["properties"] as $k=>$v) $x.="&".$k."=".$v;
		return "<tr><td colspan=2>".($this->PROP["caption"]? "<h2>".$this->PROP["caption"]."</h2>":"")."<IFRAME src='./readext.php?ext=".$this->PROP["mod"]."$x&filtersGoSubmit=Go' width='".$this->PROP["width"]."' height='".$this->PROP["height"]."' style='".$this->PROP["style"]."' frameborder=0 scrolling=1></IFRAME></td></tr>";
	}

	function mkList($val,$log=0) {
		return "";
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
?>