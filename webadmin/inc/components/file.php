<?
/*
*  Component "Files"
*  Author "AIT"
*  v. 1.0
*/

class T_file {
  var $PROP=array(
    "caption"=>"",
    "length"=>"255",
    "dir"=>"../userfiles/",
    "visibility"=>"yes",
    "name"=>"",
    "filetypes"=>array("jpg","jpeg","gif","png"),
    "show"=>0
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
    if(isset($x[0]) && $x[0]) $this->PROP["filetypes"]=explode(",",$x[0]);

    if(isset($x[1]) && $x[1]) $this->PROP["dir"]=$x[1];

    if(isset($x[2]) && $x[2]) {
      $xx=intval($x[2]);
      if(!$xx) $xx=intval(trim(str_replace("maxlength=","",$x[2])));
      if($xx) $this->PROP["length"]=$xx;
    }

    $this->PROP["caption"]=$pString[2];
    if(isset($pString[3])) $this->PROP["show"]=1;
  }

  function params2str () {
    return "file|".join(",",$this->PROP["filetypes"])."*".$this->PROP["dir"]."*".$this->PROP["length"]."|".$this->PROP["caption"].($this->PROP["show"]? "|show":"");
  }

  function mkForm($key,$val="") {
    global $_USRDIR,$EXT;
    $form="<INPUT type='file' id='$key' name='$key'>";
    if($_USRDIR && strpos(" $val","../")) $val=str_replace("../","../www/$_USRDIR/",$val);
    if ($val) {

      if (file_exists($val)) {
        if($this->PROP["show"]) {
          $form.="<BR><img src='./dwl.php?i=".$val."' style='border:1 solid #000000' />";
        }
        else $form.="<BR><a href='./dwl.php?i=".$val."' target='_blank'>".substr($val,strrpos($val,"/")+1)."</a>";
        $form.="&nbsp;<a href='?ext=$EXT&deletefile=".$val;
        if (isset($_GET["ch"])) $form.="&dfn=$key&ch=".$_GET["ch"];
        $form.="' style='color:red' onclick='if(!confirm(\"Remove file?\")) return false;'><b>[<script>document.write(ParentW.DLG.t('Delete'));</script>]</b></a>";
      }
    }

    return array($this->PROP["caption"],$form);
  }

  function mkList($val,$log=0) {
    if($log) {
      $val=str_replace("../","./",$val);
      if ($val) {
        return $val;
      }
    }
    if ($val && file_exists($val)) {
      return "<a href='./dwl.php?i=".$val."' >".substr($val,strrpos($val,"/")+1)."</a>";
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
    return "upper($key) like '%".strtoupper(AddSlashes($val["value"]))."%'";
  }

  function getFilter($key,$val) {
    return array("StringFilter.js","{type: 'string',dataIndex:'$key'}");
  }

  function onBeforeDbChanging() {
    $a=copyuserfile($this->PROP["name"],join(",",$this->PROP["filetypes"])."*".$this->PROP["dir"]);
    if($a) {
    	$_POST[$this->PROP["name"]]=$a;
    	echo "<script>var o=parent.document.getElementById('".$this->PROP["name"]."');if(o!=null) {o.value='';}</script>";
    }
    else unset($_POST[$this->PROP["name"]]);
  }
}
