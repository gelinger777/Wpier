<?
/*
*	Компонент "Выбор из списка"
*	Автор Тушев Максим
*	v. 2.0
*/

class T_select {
  var $PROP=array(
    "length"=>255,
    "caption"=>"",
    "type"=>"varchar",
    "attr"=>"",
    "size"=>"",
    "righttext"=>"",
    "visibility"=>"yes",
    "name"=>"",
    "valtable"=>"",
    "valkey"=>"",
    "valfold"=>"",
    "valwhere"=>"",
    "valorder"=>"id",
    "items"=>array(),
    "items_db"=>array(),

    );

  var $max_filter_size=3;
  var $type="select";
  var $Selected="";
  var $required=0;
  var $csv_mode=0;

  var $PROPTAB=array(
	 "valtable"=>array("valkey", "valfold"),
  );

  var $PROPHIDE=array("items_db");

 function try_post() {
	if(isset($_POST["load_options_obj"]) && $_POST["load_options_obj"]==$this->PROP["name"]) {
		ob_end_clean();
		global $db;

		if(isset($_POST["load_options_folder_dep"]) && ereg("[a-z0-9_]{1,}",$_POST["load_options_folder_dep"])) {
			$db->query("SELECT ".$this->PROP["valkey"].",".$this->PROP["valfold"]." FROM ".$this->PROP["valtable"]." ".($_POST["load_options_values"]? "WHERE ".$_POST["load_options_folder_dep"]."='".join("' or ".$_POST["load_options_folder_dep"]."='",explode(",",addslashes($_POST["load_options_values"])))."'":"")."  ORDER BY ".$this->PROP["valfold"]);
			$i=0;
			while($db->next_record()) if($db->Record[1]) echo ($i++? ",":"")."['".$db->Record[0]."','".str_replace("'","`",$db->Record[1])."']";
		}
		exit;
	}
  }

  function unhtmlentities($string)
{
    $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
    $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return strtr($string, $trans_tbl);
}

  function str2params ($name,$pString) {
  global $db;
    $this->PROP["name"]=$name;
    $pString=explode("|",$pString);

    if($pString[1]) {
      $s=explode("/",$pString[1]);
      if(isset($s[1]) && $s[1]) {
        //$this->PROP["attr"]=$s[1];
        $s1=explode("maxlength=",$s[1]);
        if(isset($s1[0])) $this->PROP["attr"]=$s1[0];
        if(isset($s1[1])) $this->PROP["length"]=$s1[1];
        if(isset($s[2])) $this->PROP["type"]=$s[2];
      }
      if($s[0]) {
        $s=explode("*",$s[0]);
        if(isset($s[0]) && $s[0]) $this->PROP["valtable"]=$s[0];
        if(isset($s[1]) && $s[1]) $this->PROP["valkey"]=$s[1];
        if(isset($s[2]) && $s[2]) {
          $this->PROP["valfold"]=$s[2];
          $this->PROP["valorder"]=$s[2];
        }
        if(isset($s[3]) && $s[3]) $this->PROP["valwhere"]=$s[3];
	if(isset($s[4]) && $s[4]) $this->PROP["valorder"]=$s[4];

      }
    }

    $s=explode("*rtext*",$pString[2]);
    $this->PROP["caption"]=$s[0];
    if(isset($s[1])) $this->PROP["righttext"]=$s[1];

    if(isset($pString[3])) {
      for($i=3;$i<count($pString);$i++) {
        $s=explode("/",$pString[$i]);
        $this->PROP["items"][$s[0]]=$s[1];
      }
    }

    if($this->PROP["valtable"]) {
      $db->query("SELECT ".$this->PROP["valkey"].",".$this->PROP["valfold"]." FROM ".$this->PROP["valtable"].($this->PROP["valwhere"]? " WHERE ".$this->PROP["valwhere"]:"").($this->PROP["valorder"]? " ORDER BY ".$this->PROP["valorder"]:""));

      while($db->next_record()) {
        $a=array();
        foreach($db->Record as $kr=>$vr) if(intval($kr)) {
          $a[]=str_replace('/','&#47;',$vr);
        }
        $this->PROP["items_db"][$db->Record[0]]=join(", ",$a);
      }
    }
  }

  function params2str () {
    $s="select|";
    if($this->PROP["valtable"])
      $s.=$this->PROP["valtable"]."*".$this->PROP["valkey"]."*".$this->PROP["valfold"]."*".($this->PROP["valwhere"]? $this->PROP["valwhere"]:"")."*".($this->PROP["valorder"]? $this->PROP["valorder"]:"");
    if($this->PROP["length"]) $s.="/maxlength=".$this->PROP["length"];
    if($this->PROP["type"]!='varchar') $s.="/".$this->PROP["type"];
    $s.="|".$this->PROP["caption"].($this->PROP["righttext"]? "*rtext*".$this->PROP["righttext"]:"");

	foreach($this->PROP["items"] as $k=>$v) {
      $s.="|$k/$v";
    }
    return $s;
  }



  function bldOptions($val,$mod=true) {
  global $db;
    if($this->csv_mode) {return ($this->PROP["items"]+$this->PROP["items_db"]);}

    if(!is_array($val)) if($val==="") $val=array();else $val=array($val);

    $s=array();
    if(count($this->PROP["items"])) {
      foreach($this->PROP["items"] as $k=>$v) {

	  if(in_array($k,$val)) $this->Selected=($mod? $k:$v);
	  $v=str_replace("'","`",$this->unhtmlentities($v));
          $s[]="['".($mod? $k:$v)."','".($v? str_replace("\n"," ",str_replace("\r","",str_replace("'","`",$v))):"&nbsp;")."']";
      }
    }
    if(count($this->PROP["items_db"])) {
      foreach($this->PROP["items_db"] as $k=>$v) {
	if(in_array($k,$val)) $this->Selected=($mod? $k:$v);
        $s[]="['".($mod? $k:$v)."','".str_replace("\n"," ",str_replace("\r","",str_replace("'","`",$this->unhtmlentities($v))))."']";
      }
    }
    return join(",",$s);
  }

  function mkForm($name,$val="") {
    if(!$val) {
		if(count($this->PROP["items"])) {
			reset($this->PROP["items"]);
			$val=key($this->PROP["items"]);
		}elseif(count($this->PROP["items_db"])) {
			reset($this->PROP["items_db"]);
			$val=key($this->PROP["items_db"]);
		}
	}
	$l="";
	if(!$val) {
	  if(count($this->PROP["items"])) {
	    foreach($this->PROP["items"] as $k=>$v) {
		$val=str_replace("'","`",$this->unhtmlentities($k));
		$l=str_replace("'","`",$this->unhtmlentities($v));
		if(!$l) $l=" ";
		break;
	    }
	  }
	  if(!$l) {
	    if(count($this->PROP["items_db"])) {
	      foreach($this->PROP["items_db"] as $k=>$v) {
		  $val=str_replace("'","`",$this->unhtmlentities($k));break;
	      }
	    }
	  }
	}

	return array("<b>".$this->PROP["caption"]."</b>","<input type='text' id='select-$name' value='".str_replace("'","`",$this->unhtmlentities($val))."' style='width:450px'/>".($this->PROP["righttext"]? $this->PROP["righttext"]:""),"new Ext.form.ComboBox({
          id:'$name',
	      applyTo:'select-$name',
	      //typeAhead: true,
    	  mode: 'local',
	      width:450,
		  minListWidth:450,
	      triggerAction: 'all',
	      ".($this->required? "allowBlank:false,":"")."
          listClass: 'x-combo-list-small',
	      displayField:'val',
	      valueField: 'key',
	      ".(!$val && $l? "emptyText:'$l',":"")."
	      editable: false,
	      logsel:".(isset($_GET["ch"])? "false":"true").",
	      hiddenName:'$name',
	      store: new Ext.data.SimpleStore({
		  	fields: ['key','val'],
		  	data : [".$this->bldOptions($val)."]
	      }),
          depend_function:function(cod,fold,res) {
                if(res) {
		  			var i=Ext.getCmp('$name');
		  			i.reset.call(i);
				}
				Ext.Ajax.request({
				  url: '".$_SERVER["REQUEST_URI"]."',  // Запрос самого себя
				  success: function(response) {
				    if(response.responseText=='ERR') return false;
				    else {
				      var x=[];
				      eval('v=['+response.responseText+']');

				      var th=Ext.getCmp('$name');
				      th.store.loadData(v);
				      if(th.logsel) th.setValue(v.length>0? v[0][0]:'');
				      else th.logsel=true;
				      th.fireEvent('change');
				    }
				  },

				  params: {
				    load_options_obj:'$name',
				    load_options_values:cod,
				    load_options_folder_dep:fold
				  }
				});
	     }
     });");
  }

  function mkList($val,$log=0) {
    global $_EXPANDER;

    /*$fp=fopen("../userfiles/logselect.log","a+");
    ob_start();
    echo $this->PROP["name"]."|$val|\n";
    print_r($this->PROP["items"]);
    echo "\n\n";

    fwrite($fp,ob_get_contents());
    ob_end_clean();
    fclose($fp);*/

    if(isset($_EXPANDER) && in_array($this->PROP["name"],$_EXPANDER)) {
      if(count($this->PROP["items"])) {
	foreach($this->PROP["items"] as $k=>$v) {
	    if($k==$val) {$val=$v;break;}
	}
      }
      if(count($this->PROP["items_db"])) {
	foreach($this->PROP["items_db"] as $k=>$v) {
	  if($k==$val) {$val=str_replace("'","`",$v);break;}
	}
      }
    }

    return $val;
  }

  function mkDBFolder() {
    if($this->PROP["type"]=='varchar') {
      return "varchar(".$this->PROP["length"].")";
    }
    return $this->PROP["type"];
  }

  function getUpdateVals($key,$val) {
    return array($key, undangerstr($val));
  }

  function getFilterSearch($key,$val) {

    if(!$val["value"]) return "($key is NULL or $key='')";

    $val["value"]=explode(",",$val["value"]);
    $w=array();

	//if((count($this->PROP["items"])+count($this->PROP["items_db"]))<$this->max_filter_size) {
        	foreach($val["value"] as $v) {
			$w[]="$key like '".addslashes($v)."'";
		}
		if(count($w)) {

		  //echo "(".join(" or ",$w).")";

		  return "(".join(" or ",$w).")";
		}
	/*} else {
		foreach($val["value"] as $v) if(trim($v)) {
			$w[]="lower(".$this->PROP["valfold"].") like '".strtolower(addslashes(trim($v)))."%'";
		}
		if(count($w)) {
                        return "$key in (SELECT ".$this->PROP["valkey"]." FROM ".$this->PROP["valtable"]." WHERE ".join(" or ",$w).")";
		}
	} */
	return "";
  }

  function getFilter($key,$val) {
	$s=array();

	global $db,$_FILTER_SIZES;

	$db->query("SELECT cods,name FROM gridfiltersgroups WHERE tab='".$this->PROP["valtable"]."'");
	while($db->next_record()) {$s[]="['".$db->Record["cods"]."','".$db->Record["name"]."']";}

	if(count($this->PROP["items"])) {
	  foreach($this->PROP["items"] as $k=>$v) $s[]="['$k','".($v? $v:"-")."']";
	}
	if(count($this->PROP["items_db"])) {
	  foreach($this->PROP["items_db"] as $k=>$v) $s[]="['$k','$v']";
	}

	if((count($this->PROP["items"])+count($this->PROP["items_db"]))<$this->max_filter_size) {
	  $s="{type: 'list',dataIndex: '$key',
	    options: [".join(",",$s)."],
	    phpMode: true}";
	  return array("ListFilter.js",$s);
	}

	// Если стравочник слишком большой, выводим селектор
	return array("ComboFilter.js","{type: 'combo',dataIndex:'$key',options: [".join(",",$s)."],
	    phpMode: true".(isset($_FILTER_SIZES) && isset($_FILTER_SIZES[$key])? ",width:".$_FILTER_SIZES[$key]:"")."}");

  }

   function mkEditor() {
	  global $GridGlobal;

	  if(!isset($GridGlobal)) $GridGlobal="";
	  $GridGlobal.="<script>selectdata_".$this->PROP["name"]."=[";
	  $nn=0;
	  if(count($this->PROP["items"])) {
        foreach($this->PROP["items"] as $k=>$v) $GridGlobal.=($nn++? ",":"")."['$k','$v']";
      }
      if(count($this->PROP["items_db"])) {
        foreach($this->PROP["items_db"] as $k=>$v) $GridGlobal.=($nn++? ",":"")."['$k','$v']";
      }
	  $GridGlobal.="];</script>";

          return "editor: new Ext.form.ComboBox({
               typeAhead: true,
    			mode: 'local',
				triggerAction: 'all',
               listClass: 'x-combo-list-small',
			   displayField:'val',
			   valueField: 'key',
			   store: new Ext.data.SimpleStore({
							fields: ['key','val'],
							data : selectdata_".$this->PROP["name"]."
				})
          }),renderer: function(x) {var a=selectdata_".$this->PROP["name"].";for(var i=0;i<a.length;i++) if(a[i][0]==x) return a[i][1];return '';}";
   }

   function acceptEditor($val) {
     return $val;
   }

}
?>
