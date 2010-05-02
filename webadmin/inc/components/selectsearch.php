<?
/*
*	Компонент "Селектор с поиском"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_selectsearch {
	var $PROP=array(
		"caption"=>"",
		"visibility"=>"yes",
		"name"=>"",
		"type"=>"int(11)",
		"valtable"=>"",
		"valkey"=>"",
		"valvalue"=>"",
		"valwhere"=>"",
		"size"=>5,
		"width"=>200
		);

	var $PROPTAB=array(
	 "valtable"=>array("valkey", "valvalue"), 
    );

	function str2params ($name,$pString) {
		$this->PROP["name"]=$name;
		$pString=explode("|",$pString);
				
		$this->PROP["caption"]=$pString[2];

		if(isset($pString[3]) && $pString[3]) {
			$s=explode("/",$pString[3]);
			$this->PROP["width"]=$s[0];
			$this->PROP["size"]=$s[1];
		}

		if(isset($pString[4]) && $pString[4]) $this->PROP["type"]=$pString[4];

		$pString=explode("*",$pString[1]);

		$this->PROP["valtable"]=$pString[0];
		$this->PROP["valkey"]=$pString[1];
		$this->PROP["valvalue"]=$pString[2];
	}

	function params2str () {
		return "selectsearch|".$this->PROP["valtable"]."*".$this->PROP["valkey"]."*".$this->PROP["valvalue"]."|".$this->PROP["caption"]."|".$this->PROP["width"]."/".$this->PROP["size"];
	}

	function mkForm($key,$val="") {
	global $PROPERTIES, $db; 

		$form="";
					
		$sel="";
		if($val) {
			$db->query("SELECT ".$this->PROP["valkey"].", ".$this->PROP["valvalue"]." FROM ".$this->PROP["valtable"]." WHERE ".$this->PROP["valkey"]."='".$val."'	");
			if($db->next_record()) {
				$sel="<OPTION value='".$db->Record[0]."' selected>".$db->Record[1]."</OPTION>";
			}
		}
					
		$form.='<table border="0"><tr><td width="'.$this->PROP["width"].'"><input id="'.$key.'_text" onkeydown="return mk_select(\''.$this->PROP["valtable"].'\', \''.$this->PROP["valkey"].'\', \''.$this->PROP["valvalue"].'\', \''.$key.'\');" style="width:100%" value=""><div id="'.$key.'_div" style="width:100%"><SELECT ID="'.$key.'_ch" SIZE="'.$this->PROP["size"].'"  style="width:100%" onchange="document.all(\''.$key.'\').value=this.value;ChangeOption(document.all(\''.$key.'\'))"><option value="">--== пусто ==--</option>'.$sel.'</SELECT></div></td></table><input type="hidden" name="'.$key.'" value="'.($val? $val:"").'">';
		
		return array("<b><nobr>".$this->PROP["caption"]."</nobr></b>",$form);
	}

	function mkList($val,$log=0) {
		return $val;
	}

	function mkDBFolder() {
		return $this->PROP["type"];
	}

	function getUpdateVals($key,$val) {
		return array($key, undangerstr($val));
	}

	function getFilterSearch($key,$val) {
		if($val) return "$key='".htmlspecialchars($val)."'";
		return "";
	}

	function getFilter($key,$val) {
		return "<input name='$key' value='".htmlspecialchars($val)."' style='width:100%'>";
	}
}
?>