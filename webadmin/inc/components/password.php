<?
/*
*	Компонент "Пароль"
*	Автор Тушев Максим
*	v. 1.0
*/

class T_password {
  var $PROP=array(
    "length"=>32,
    "caption"=>"",
    "attr"=>"",
    "size"=>"",
    "righttext"=>"",
    "visibility"=>"yes",
    "name"=>""
    );

	var $required=0;

  function str2params ($name,$pString) {
    $this->PROP["name"]=$name;
    $pString=explode("|",$pString);
    $this->PROP["attr"]=$pString[1];

    if(isset($pString[3])) $this->PROP["type"]=$pString[3];
    $s=explode("maxlength=",$pString[1]);
    if(isset($s[1])) $this->PROP["length"]=intval($s[1]);
    $s=explode("size=",$pString[1]);
    if(isset($s[1]) && intval($s[1])) $this->PROP["size"]=intval($s[1]);
    $s=explode("*rtext*",$pString[2]);
    $this->PROP["caption"]=$s[0];
    if(isset($s[1])) $this->PROP["righttext"]=$s[1];
  }

  function params2str () {
    return "password|size=".$this->PROP["size"]." maxlength=".$this->PROP["length"]."|".$this->PROP["caption"].($this->PROP["righttext"]? "*rtext*".$this->PROP["righttext"]:"");
  }

  function mkForm($name,$val="") {
    return array("<b>".$this->PROP["caption"]."</b>","<INPUT type='password' name='$name' id='$name' value=''> ".$this->PROP["righttext"],"new Ext.form.TextField({
				id:'$name',
			   name:'$name',
			   type:'password',
			   ".($this->required? "allowBlank:false,":"")."
			   applyTo:'$name'
				});");
  }


  function mkDBFolder() {
    return "varchar(".wpier_hash_length().")";
  }

  function getUpdateVals($key,$val) {
    if ($val) return array($key, wpier_hash($val));
    return "";
  }

  function getFilterSearch($key,$val) {
    return "";
  }

  function getFilter($key,$val) {
    return "";
  }
}
