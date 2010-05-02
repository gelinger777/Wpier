<?
/*
*	Компонент "Картинка"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_img64 {
	var $PROP=array(
		"caption"=>"",
		"length"=>"255",
		"visibility"=>"yes",
		"name"=>"",
		"sizes"=>array(),
		"width"=>"100%",
		"water"=>"",
		"type"=>"text"
		);
	
	var $EVENTS=array(
		"onBeforeDbChanging"=>1
	);
		
        var $ID=0;	

	function str2params ($name,$pString) {
		$this->PROP["name"]=$name;
		$pString=explode("|",$pString);
		$this->PROP["attr"]=$pString[1];
		
		$x=explode("*",$pString[1]);
		
		if(isset($x[1]) && $x[1]) {
                  $this->PROP["water"]=$x[1];
		}

		$x=explode("/",$x[0]);

		for($i=0;$i<count($x);$i++) {
			$this->PROP["sizes"][]=$x[$i];
		}
		$this->PROP["caption"]=$pString[2];
	}

	function params2str () {
		return "img|".join("/",$this->PROP["sizes"])."*".$this->PROP["water"]."|".$this->PROP["caption"];
	}

	function mkForm($key,$val="") {
        global $TEXTS;
		$form="<div style='border:1 solid #5c5c5c;background:#dfdfdf;width:".$this->PROP["width"]."'><INPUT type='file' name='$key'>";
		if(count($this->PROP["sizes"])>1) {
			$form.=" размер: <select name='".$key."PREVIEW'>";
			foreach($this->PROP["sizes"] as $k=>$v) {
				$form.="<option value='$v'>$v";
			}
			$form.="</select>";
		} else {
			$form.="<INPUT type='hidden' name='".$key."PREVIEW' value='".$this->PROP["sizes"][0]."'>";
		}
		if ($val && $val!='deleted') {
		  global $PROPERTIES, $_GET,$_CONFIG;
		  $form.="<BR><a href='/".$_CONFIG["ADMINDIR"]."/img64.php?f=$key&t=".$PROPERTIES["tbname"]."&wf=id&cod=".intval($_GET["ch"])."' target='_blank'><img src='/".$_CONFIG["ADMINDIR"]."/img64.php?f=$key&t=".$PROPERTIES["tbname"]."&wf=id&cod=".intval($_GET["ch"])."&size=200?x' border='0' /></a>";
                  $form.="<br><input type='checkbox' name='".$key."DELETE' value='1'> ".$TEXTS["DeleteInSave"]."</a>"; 		
		}
		$form.="</div>";
		return array("<b><nobr>".$this->PROP["caption"]."</nobr></b>",$form);
	}

	function mkList($val,$log=0) { 	
		if (!$log && $val && $val!="deleted") {
  		    global $PROPERTIES,$_CONFIG;
                    return "<img src='/".$_CONFIG["ADMINDIR"]."/img64.php?f=".$this->PROP["name"]."&t=".$PROPERTIES["tbname"]."&wf=id&cod=".$this->ID."&size=120?x' style='border:1 solid #000000;' />"; 	
		} 
		return "";
	}
	
	function SetID($id) {
	  $this->ID=$id;
	}

	function mkDBFolder() {
		return "text";
	}

	function getUpdateVals($key,$val) {
		if($val) return array($key, undangerstr($val));
		else return "";
	}

	function getFilterSearch($key,$val) {
		return "$key like '".htmlspecialchars($val)."'";
	}

	function getFilter($key,$val) {
		return "";
	}

	function onBeforeDbChanging() {
	   global $_USERDIR;
	        if(!file_exists("../".($_USERDIR? "www/$_USERDIR/":"")."userfiles/tmp/")) { 
                  mkdir("../".($_USERDIR? "www/$_USERDIR/":"")."userfiles/tmp");
                }
		$a=copyImgs($this->PROP["name"],"*/userfiles/tmp/**".$this->PROP["water"]);
	        
                if(isset($a[1]) && $a[1]) {
                  $a[1]=$_SERVER["DOCUMENT_ROOT"].$a[1];
                  $fp=fopen($a[1],"r");
                  $str=fread($fp,filesize($a[1]));
                  fclose($fp);                           
                  $_POST[$this->PROP["name"]]=base64_encode($str);
                  unset($a[1]);
                }
		else unset($_POST[$this->PROP["name"]]);
		if(isset($_POST[$this->PROP["name"]."DELETE"])) {
                  $_POST[$this->PROP["name"]]="deleted";                    
                }

	}
}
?>