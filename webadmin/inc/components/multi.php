<?
/*
*  Компонент "Мультиселект"
*  Автор Тушев Максим
*  v. 1.0
*/

class T_multi {
  var $PROP=array(
    "caption"=>"",
    "visibility"=>"yes",
    "name"=>"",
    "valtable"=>"",
    "valkey"=>"",
    "valvalue"=>"",
    "valwhere"=>"",
    "linktable"=>"",
    "linkkey"=>"",
    "linkvalue"=>"",
    "thisfolder"=>"",
    "size"=>5,
    "width"=>200,
    "widthBlock"=>450,
    );
  
  var $blockCod="";
  var $type="multi";

  var $PROPTAB=array(
   "valtable"=>array("valkey", "valvalue"),    
   );
  var $THISFOLDERS=array(
   "thisfolder"
  ); 

  var $EVENTS=array(
    "onAfterDbChanging"=>1,
    "onBeforeDbChanging"=>1
      );

  function str2params ($name,$pString) {
    $this->PROP["name"]=$name;
    $pString=explode("|",$pString);
        
    $this->PROP["caption"]=$pString[2];

    if(isset($pString[3])) {
      $s=explode("/",$pString[3]);
      $this->PROP["width"]=$s[0];
      $this->PROP["size"]=$s[1];
    }

    $pString=explode("/",$pString[1]);

    $this->PROP["valtable"]=$pString[0];
    $this->PROP["valkey"]=$pString[1];
    $this->PROP["valvalue"]=$pString[2];
    $this->PROP["linktable"]=$pString[3];
    $this->PROP["linkkey"]=$pString[4];
    $this->PROP["linkvalue"]=$pString[5];
    $this->PROP["thisfolder"]=$pString[6];
    if(isset($pString[7])) $this->PROP["valwhere"]=$pString[7];    
  }

  function params2str () {
    return "multi|".$this->PROP["valtable"]."/".$this->PROP["valkey"]."/".$this->PROP["valvalue"]."/".$this->PROP["linktable"]."/".$this->PROP["linkkey"]."/".$this->PROP["linkvalue"]."/".$this->PROP["thisfolder"].($this->PROP["valwhere"]? "/".$this->PROP["valwhere"]:"")."|".$this->PROP["caption"]."|".$this->PROP["width"]."/".$this->PROP["size"];
  }

  function mkForm($key,$val="") {
  global $PROPERTIES, $db, $TEXTS; 
    if(isset($_GET["ch"])) $val=intval($_GET["ch"]);

    $form="<div style='border:1 solid #acacac;width:".$this->PROP["widthBlock"]."' bgcolor='#dedede'>";
    $form.='<SCRIPT LANGUAGE="JavaScript">multi_select[multi_select.length]="'.$key.'";</SCRIPT><table border="0" style="background:#efefef;" width="100%"><tr><td width="'.$this->PROP["width"].'"><input id="'.$key.'_text" onkeydown="return mk_select(event.keyCode,\''.$this->PROP["valtable"].'\', \''.$this->PROP["valkey"].'\', \''.$this->PROP["valvalue"].'\', \''.$key.'\''.($this->PROP["valwhere"]? ",'".$this->PROP["valwhere"]."'":"").');" style="width:100%" value="'.$TEXTS["search"].'" style="color:#979797" onfocus="this.value=\'\';this.style.color=\'black\';" title="поиск"><div id="'.$key.'_div" style="width:100%"  style="color:#979797">'.$TEXTS["Accessable"].':<br><SELECT ID="'.$key.'_ch" name="'.$key.'_ch" SIZE="'.$this->PROP["size"].'" MULTIPLE style="width:100%"></SELECT></div></td><td align="center"><br><br><input type="button" onclick="move_mult(\''.$key.'\')" value=" => "><BR><BR><input type="button" onclick="remove_mult(\''.$key.'\')" value=" <= "></td><td width="'.$this->PROP["width"].'" valign="bottom"  style="color:#979797">'.$TEXTS["Changed"].':<br><SELECT name="'.$key.'[]"  size='.$this->PROP["size"].' MULTIPLE style="width:100%;">';
    if($this->PROP["thisfolder"]) { 
     $fld=$db-> folders_names($this->PROP["linktable"]);
     $db->query("SELECT ".$this->PROP["thisfolder"]." FROM ".$PROPERTIES["tbname"]." WHERE id='".$val."'");
     if ($db->next_record()) {
      
      $db->query("SELECT ".$this->PROP["valtable"].".".$this->PROP["valkey"].", ".$this->PROP["valtable"].".".$this->PROP["valvalue"]." FROM ".$this->PROP["valtable"]." LEFT JOIN ".$this->PROP["linktable"]." ON ".$this->PROP["valtable"].".".$this->PROP["valkey"]."=".$this->PROP["linktable"].".".$this->PROP["linkkey"]." WHERE ".$this->PROP["linktable"].".".$this->PROP["linkvalue"]."='".$db->Record[0]."'".(isset($fld["id"])? " ORDER BY ".$this->PROP["linktable"].".id":""));
      while($db->next_record()) {
        $form.="<OPTION value='".$db->Record[0]."'>".$db->Record[1]."</OPTION>";
      }
     }
    }
    $form.='</SELECT></td></tr></table></div>';
    return array("<b><nobr>".$this->PROP["caption"]."</nobr></b>",$form);
  }

    function mkList($val,$log=0) {
    global $db,$XLS_PROCCESS_LOG,$PROPERTIES,$BLK;

    if(isset($BLK) && !$this->blockCod) {
      $this->blockCod=parse_tmp(strtoupper($this->PROP["name"]),"BLK");      
    }
	$id=0;
	if(!$this->PROP["thisfolder"] || $this->PROP["thisfolder"]=="id") $id=$val;
	else {
		$db->query("SELECT ".$this->PROP["thisfolder"]." FROM ".$PROPERTIES["tbname"]." WHERE id='".$val."'");
		if ($db->next_record()) $id=$db->Record[0];
	}
       
    
	if($id) {
	  $db->query("SELECT ".$this->PROP["valtable"].".* FROM ".$this->PROP["valtable"].",".$this->PROP["linktable"]." WHERE ".$this->PROP["valtable"].".".$this->PROP["valkey"]."=".$this->PROP["linktable"].".".$this->PROP["linkkey"]." and ".$this->PROP["linktable"].".".$this->PROP["linkvalue"]."='".$id."' ORDER BY ".$this->PROP["valtable"].".".$this->PROP["valvalue"]);
	  //echo $db->LastQuery."<br>";
	  $val=array(); 
          if($this->blockCod) send2blk($this->PROP["name"]."_count",$db->num_rows());    
          while($db->next_record()) {
              if($this->blockCod) {
                $s=$this->blockCod;
                foreach($db->Record as $k=>$v) $s=str_replace("%$k%",$v,$s);
                $val[]=$s;
              } else $val[]=$db->Record[$this->PROP["valvalue"]];
          }
          if($this->blockCod) $val=join(" ",$val);
          else {
           if(isset($XLS_PROCCESS_LOG)) $val=join("\r\n",$val);
           else $val=join("<br>",$val);
          }          
	}
	return $val;
  }
  /*function mkList($val,$log=0) {
    global $db,$XLS_PROCCESS_LOG;
	
	$id=0;
	if(!$this->PROP["thisfolder"] || $this->PROP["thisfolder"]=="id") $id=$val;
	else {
		$db->query("SELECT ".$this->PROP["thisfolder"]." FROM ".$PROPERTIES["tbname"]." WHERE id='".$val."'");
		if ($db->next_record()) $id=$db->Record[0];
	}
    $val="";
	if($id) {
	      $db->query("SELECT ".$this->PROP["valtable"].".".$this->PROP["valvalue"]." FROM ".$this->PROP["valtable"].",".$this->PROP["linktable"]." WHERE ".$this->PROP["valtable"].".".$this->PROP["valkey"]."=".$this->PROP["linktable"].".".$this->PROP["linkkey"]." and ".$this->PROP["linktable"].".".$this->PROP["linkvalue"]."='".$id."'");
		  while($db->next_record()) {
              $val.=$db->Record[0].(isset($XLS_PROCCESS_LOG)? "; \r\n":"<br>");
          }
	}
	return $val;
  } */

  function mkDBFolder() {
    return "";
  }

  function getUpdateVals($key,$val) {
    return "";
  }

  function getFilterSearch($key,$val) {
  global $db;  
	$x="";
	if(is_array($val)) {		
        if(count($val)) {
			$x=array();
			foreach($val as $v)  if($v) $x[]=$this->PROP["linkkey"]."='".htmlspecialchars($v)."'";
			$x=join(" or ",$x);
		}
	} elseif($val) {
		$x=$this->PROP["linkkey"]."='".htmlspecialchars($v)."'";
	}
	
	if($x) {
		$db->query("SELECT ".$this->PROP["linkvalue"]." FROM ".$this->PROP["linktable"]." WHERE $x");
		$x=array();
		while($db->next_record()) {
			$x[$db->Record[0]]=$this->PROP["thisfolder"]."='".$db->Record[0]."'";
		}
		if(count($x)) {
			return "(".join(" or ",$x).")";
		} else return "id=-1";
	}
	return "";
  }

  function getFilter($key,$val) {
    global $PROPERTIES;
	$sz=1;
	if(isset($PROPERTIES["filters_size"])) $sz=$PROPERTIES["filters_size"];

    $s="<select style='width:100%' onchange='ChangeOption(this)' name='".$key.($sz>1? "[]' MULTIPLE size=$sz":"'")."><option value=''>Все</option>";

    $s.=$this->bldOptions($val);
    return $s."</select>";
  }

  function bldOptions($val) {
  global $db;

    $s="";
	if(!is_array($val)) $val=array($val);
    
	$db->query("SELECT ".$this->PROP["valkey"].",".$this->PROP["valvalue"]." FROM ".$this->PROP["valtable"].($this->PROP["valwhere"]? " WHERE ".$this->PROP["valwhere"]:""));
	while($db->next_record()) $s.="<option value='".$db->Record[0]."'".(in_array($db->Record[0],$val)? "selected":"").">".$db->Record[1]."</option>";

    return $s;
  }

  function onAfterDbChanging() {
  global $db,$id,$PROPERTIES;
    if (isset($_POST["upd"]) || isset($_POST["ins"])) {
      $db->query("SELECT ".$this->PROP["thisfolder"]." FROM ".$PROPERTIES["tbname"]." WHERE id='$id'");
      if ($db->next_record()) {
        $kk=$db->Record[0];
        $db->query("DELETE FROM ".$this->PROP["linktable"]." WHERE ".$this->PROP["linkvalue"]."='".$kk."'");
      }
      if (isset($_POST[$this->PROP["name"]]) && is_array($_POST[$this->PROP["name"]])) {
        if(!$kk) $kk=$id;
        foreach($_POST[$this->PROP["name"]] as $ke=>$va) {
          $db->query("INSERT INTO ".$this->PROP["linktable"]." (".$this->PROP["linkkey"].", ".$this->PROP["linkvalue"].") VALUES ('".$va."', '".$kk."')");
          
          
        }
      }
    }
  }

  function onBeforeDbChanging() {
  global $db;
    $tables=$db->table_names();
    if(!isset($tables[$this->PROP["linktable"]]) && $this->PROP["linktable"] && $this->PROP["linkkey"] && $this->PROP["linkvalue"]) $db->query("CREATE TABLE ".$this->PROP["linktable"]." (".$this->PROP["linkkey"]." INT NOT NULL ,".$this->PROP["linkvalue"]." INT NOT NULL ,INDEX ( ".$this->PROP["linkkey"]." ,".$this->PROP["linkvalue"]." ))");
  }
}
?>