<?
/*
*	Компонент "Картинка"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_img {
	var $PROP=array(
		"caption"=>"",
		"length"=>"255",
		"dir"=>"../userfiles/",
		"dirpreview"=>"",
		"visibility"=>"yes",
		"name"=>"",
		"sizes"=>array(),
		"width"=>"100%",
		"water"=>""
		);
	var $required=0;
		var $EVENTS=array(
			"onBeforeDbChanging"=>1
			);

	function str2params ($name,$pString) {
		$this->PROP["name"]=$name;
		$pString=explode("|",$pString);
		$this->PROP["attr"]=$pString[1];

		$x=explode("*",$pString[1]);
		if(isset($x[1]) && $x[1]) $this->PROP["dir"]=$x[1];
		if(isset($x[2]) && $x[2]) $this->PROP["dirpreview"]=$x[2];

		if(isset($x[2]) && $x[2]) {
			$xx=intval($x[2]);
			if(!$xx) $xx=intval(trim(str_replace("maxlength=","",$x[2])));
			if($xx) $this->PROP["length"]=$xx;
			if(isset($x[4]) && $x[4]) {
				$this->PROP["water"]=$x[4];
			}
		}

		$x=explode("/",$x[0]);

		for($i=0;$i<count($x);$i++) {
			$this->PROP["sizes"][]=$x[$i];
		}
		$this->PROP["caption"]=$pString[2];
	}

	function params2str () {
		return "img|".join("/",$this->PROP["sizes"])."*".$this->PROP["dir"]."*".$this->PROP["dirpreview"]."*".$this->PROP["length"]."*".$this->PROP["water"]."|".$this->PROP["caption"];
	}

	function mkForm($key,$val="") {
	global $TEXTS,$EXT;
		$form="<div style='border:1 solid #5c5c5c;background:#dfdfdf;width:".$this->PROP["width"]."'><INPUT type='file' id='$key' name='$key' onchange='return CheckFile(this,\"".$this->PROP["dir"]."\")'>";
		if(count($this->PROP["sizes"])>1) {
			$form.=" размер превью: <select name='".$key."PREVIEW'>";
			foreach($this->PROP["sizes"] as $k=>$v) {
				$form.="<option value='$v'>$v";
			}
			$form.="</select>";
		} else {
			$form.="<INPUT type='hidden' name='".$key."PREVIEW' value='".$this->PROP["sizes"][0]."'>";
		}
		$form.="<BR><img id='$key-image-box' src='".($val && file_exists($val)? $val:"ext/img/dot.gif")."' style='border:1 solid #000000;cursor:hand' ".($fval? "onclick='window.open(\"".$fval."\")'":"")." />";
		if ($val) {
			if ($val) {
			   $fval="";
			   if($this->PROP["dirpreview"]) $fval=str_replace($this->PROP["dirpreview"],$this->PROP["dir"],$val);

                                $form.="<a href='?ext=".$EXT."&ch=".$_GET["ch"]."&delimg=".$key."' style='color:red' onclick='if(!confirm(\"".$TEXTS["DeleteImg"]."\")) return false;'><b>[".$TEXTS["Delete"]."]</b></a>";
			}
		}
		$form.="</div>";
		return array("<b><nobr>".$this->PROP["caption"]."</nobr></b>",$form);
	}

	function mkList($val,$log=0) {
		if (!$log && $val && file_exists($val)) {
			$fval="";
			   if($this->PROP["dirpreview"]) $fval=str_replace($this->PROP["dirpreview"],$this->PROP["dir"],$val);
			return "<img src='".$val."' style='border:1 solid #000000;cursor:hand' ".($fval? "onclick='window.open(\"".$fval."\")'":"")." />";
		} elseif($log && $val) {
			return str_replace("../","/",$val);
		}
		return "";
	}

	function mkDBFolder() {
		return "varchar(".$this->PROP["length"].")";
	}

	function getUpdateVals($key,$val) {
		if($val) return array($key, undangerstr($val));
		else return "";
	}

	function getFilterSearch($key,$val) {
		return "$key like '".htmlspecialchars($val)."'";
	}

	function getFilter($key,$val) {
		return "<input name='$key' value='".htmlspecialchars($val)."' style='width:100%'>";
	}

	function onBeforeDbChanging() {
		$a=copyImgs($this->PROP["name"],"*".$this->PROP["dir"]."*".$this->PROP["dirpreview"]."*".$this->PROP["water"]);
		if(isset($a[1]) && $a[1]) {
			$_POST[$this->PROP["name"]]="..".$a[1];
			echo "<script>var o=parent.document.getElementById('".$this->PROP["name"]."-image-box');if(o!=null) {o.src='".$_POST[$this->PROP["name"]]."';parent.document.getElementById('".$this->PROP["name"]."').value='';}</script>";
		}
		else unset($_POST[$this->PROP["name"]]);
	}
}
