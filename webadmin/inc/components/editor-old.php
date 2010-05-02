<?
/*
*	Компонент "Он-лайн редактор"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_editor {
	var $PROP=array(
		"caption"=>"",
		"type"=>"text",
		"attr"=>"",
		"width"=>"400",
		"height"=>"300",
		"visibility"=>"yes",
		"name"=>"",
		"righttext"=>"",
		"html"=>0
		);

	var $js="inc/components/js/editor.js";

    var $required=0;

	function str2params ($name,$pString) {
		global $_CONFIG;
		
        $this->PROP["name"]=$name;
		$pString=explode("|",$pString);
		$this->PROP["attr"]=$pString[1];
		
		if(isset($_CONFIG["EDITOR_DEFAULT"])) 
                   $this->PROP["html"]=$_CONFIG["EDITOR_DEFAULT"];
		if(isset($pString[3])) $this->PROP["html"]=$pString[3];

		$s=explode("width=",$pString[1]);
		if(isset($s[1]) && intval($s[1])) $this->PROP["width"]=intval($s[1]);

		$s=explode("height=",$pString[1]);
		if(isset($s[1]) && intval($s[1])) $this->PROP["height"]=intval($s[1]);

		$this->PROP["caption"]=$pString[2];
	}

	function params2str () {
		return "editor|width=".$this->PROP["width"]." height=".$this->PROP["height"]."|".$this->PROP["caption"].($this->PROP["righttext"]? "*rtext*".$this->PROP["righttext"]:"").($this->PROP["html"]? "|1":"");
	}

	function mkForm($name,$val="") {
		global $_CONFIG;
		
		// Если установлена настройка очистки ворд-форматирования при сохранении в формах,
		// добавим имя этого поля в список полей для очистки
		if(isset($_CONFIG["CLEAR_WORD_TAGS_IN_EDITORS"]) && $_CONFIG["CLEAR_WORD_TAGS_IN_EDITORS"]) {
			global $_WYSIWYG;
			if(!isset($_WYSIWYG)) $_WYSIWYG=array();
			$_WYSIWYG[]=$name;
		}
		
		return array("<b><nobr>".$this->PROP["caption"]."</nobr></b><br><a href='' onclick='ParentW.FCK.EditText(document.getElementById(\"$name\"));return false;' title='".DLG("Run WYSIWYG editor")."'><img src='/".$_CONFIG["ADMINDIR"]."/ext/img/document__pencil.png' border='0' /></a>","<TEXTAREA id='$name' name='$name' style='width:".$this->PROP["width"]."px;height:".$this->PROP["height"]."px'>".stripslashes($val)."</TEXTAREA> ".$this->PROP["righttext"]);		
	}

	function mkList($val,$log=0) {
		return $val;
	}

	function mkDBFolder() {
		return $this->PROP["type"];
	}

	function getUpdateVals($key,$val) {
		global $_CONFIG;
	        $val=str_replace('/'.$_CONFIG["ADMINDIR"].'/editor/scripts/editor/','',$val);
		return array($key, undangerstr(str_replace('$','&#36;',Proof($val))));
	}

	function getFilterSearch($key,$val) {
		return "upper($key) like '%".strtoupper(AddSlashes($val["value"]))."%'";
	}

	function getFilter($key,$val) {
		return array("StringFilter.js","{type: 'string',dataIndex:'$key'}");
	}
}
?>
