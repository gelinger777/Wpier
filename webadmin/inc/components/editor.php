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

	function mkForm($key,$val="") {
                 global $_CONFIG;
	     //return array($this->PROP["caption"],"<div id='editor$key' style='height:".$this->PROP["height"]."px;float:left'>&nbsp;$val</div>","MakeEditorFolder('".$key."','".$this->PROP["caption"]."','".$this->PROP["width"]."','".$this->PROP["height"]."');");
         if(isset($_CONFIG["CLEAR_WORD_TAGS_IN_EDITORS"]) && $_CONFIG["CLEAR_WORD_TAGS_IN_EDITORS"]) {
			global $_WYSIWYG;
			if(!isset($_WYSIWYG)) $_WYSIWYG=array();
			$_WYSIWYG[]=$key;
		 }
		 return array("<b><nobr>".$this->PROP["caption"]."</nobr></b><br>","<textarea id='$key' name='$key' class='wysiwyg $key'>".htmlspecialchars($val)."</textarea>","_WYSIWYG[_WYSIWYG.length]={id:'$key',w:'".$this->PROP["width"]."',h:'".$this->PROP["heigth"]."'};");
	}

	function mkList($val,$log=0) {
		return $val;
	}

	function mkDBFolder() {
		return $this->PROP["type"];
	}

	function getUpdateVals($key,$val) {
		global $_CONFIG,$EXT;
	        $val=str_replace('/webadmin/editor/scripts/editor/','',$val);
		// Нужно все динамические картинки преобразовать к статическому формату
		/*$i=0;
		$l=strlen($val);
		$v=strtolower($val);
		$vo="";
		$dir="";
		$ii=0;
		$fn=mktime()+microtime();
		while($i<$l) {
			if(substr($v,$i,4)=='<img') {
				$i+=4;
				$vo.="<img";
				while($i<$l && substr($v,$i,5)!='src="') $vo.=$val[$i++];
				$i+=5;
				$s="";
				while($i<$l && $v[$i]!='"') $s.=$val[$i++];
				if(substr($s,".php")) {
					if(!$dir) {
						$dir=$_CONFIG["TEXT_LINKED_FILES_DIR"].$EXT;
						if(!file_exists($_CONFIG["BASE_DIR"].$dir)) mkdir($_CONFIG["BASE_DIR"].$dir);
					}
					$fl=file_get_contents($_CONFIG["SERVER"].str_replace("../","/",$s));
					if($fl) {
						$fp=fopen($_CONFIG["BASE_DIR"].$dir."/".$fn."_".$ii.".jpg","w+");
						fwrite($fp,$fl);
						fclose($fp);
					}
					$s=$dir."/".$fn."_".($ii++).".jpg";
				}
				$vo.='src="'.$s.'"';
				$i++;
			} else {
				$vo.=$val[$i++];
			}
		}*/
		return array($key, undangerstr(str_replace('$','&#36;',$val)));
	}

	function getFilterSearch($key,$val) {
		return "$key like '%".str_replace(" ","%",htmlspecialchars($val))."%'";
	}

	function getFilter($key,$val) {
		return "<input name='$key' value='".htmlspecialchars($val)."' style='width:100%'>";
	}
}

