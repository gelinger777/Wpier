<?
$POSITION_DIV_NUMB=0;
$REQUIRED_FOLDERS=array();

function mkFormFolder($REC,$key) {
global $db,$tbname,$OBJECTS,$POSITION_DIV_NUMB,$REQUIRED_FOLDERS,$F_ARRAY_LOCATION;

  if(isset($_POST[$key])) $val=$_POST[$key];
  elseif(isset($REC[$key])) $val=$REC[$key];
  else $val="";
  $a=$OBJECTS[$key]->mkForm($key,$val);

  if(isset($F_ARRAY_LOCATION[$key])) $a[0]=$F_ARRAY_LOCATION[$key];

  if(isset($OBJECTS[$key]->required) && $OBJECTS[$key]->required) {
		$a[0].="<sup class='require'>*</sup>";
		$REQUIRED_FOLDERS[]=$key;
  }
  return array($a,$val);
}

function make_form($id_val) {
  global $db;
  global $AdminLogin;
  global $error;
  global $F_ARRAY;
  global $helpstring;
  global $LOCK_TIMEOUT,$ADMINGROUP,$EXT,$SIGNATURE,$ADMIN_ID;
  global $PROPERTIES,$tbname;
  global $PANELS,$InPanel,$PanelKey;



  if(isset($PROPERTIES["tbname"])) $tbname=$PROPERTIES["tbname"];
  if(!$tbname) return "";

  $REC=array();
  $id_val=intval($id_val);
  $db->query("SELECT * FROM $tbname WHERE id='$id_val'");
  if ($db->next_record()) {

      if(isset($db->Record["LastPublAdmin"]) && $db->Record["LastPublAdmin"]==$ADMIN_ID)
      $SIGNATURE=$db->Record["id"];
      /*if(((isset($ADMINGROUP) && isset($ADMINGROUP["modedit"]) && in_array($EXT,$ADMINGROUP["modedit"])) || $AdminLogin=="root") && ($db->Record["lock_user"] && $db->Record["lock_user"]!=$AdminLogin && $db->Record["lock_time"]+$LOCK_TIMEOUT>mktime())) {
        echo "<SCRIPT LANGUAGE='JavaScript'>alert ('Отказанно в доступе\\nЗапись занята пользователем ".$db->Record["lock_user"].".');</SCRIPT>";
        exit;
      }
      if(isset($db->Record["access"]) && isset($db->Record["owner"]) && !checkAccess($db->Record["access"],$db->Record["owner"])) {
        echo "<SCRIPT LANGUAGE='JavaScript'>alert ('Отказанно в доступе\\nУ Вас недостаточно прав для работы с этой записью');</SCRIPT>";
        exit;
      }*/
      $REC=$db->Record;
  }

  $db->query("UPDATE $tbname SET lock_time='".mktime()."', lock_user='$AdminLogin' WHERE id='$id_val'");

  $form=array();$TABLE_CHECK="";
  $PANELS=array();
  $PanelKey=0;
  $InPanel=array();
  foreach ($F_ARRAY as $key=>$val) {
    if(!isset($PANELS[$PanelKey])) $PANELS[$PanelKey]=array("prop"=>array(),"items"=>array());
    if(is_array($val)) {
      //foreach($val as $k=>$v) {
        $f=mkFormFolder($REC,$key);
        if($f[0]) {
          $form[$key]=$f;
          if(!in_array($key,$InPanel)) $PANELS[$PanelKey]["items"][]=$key;
        }
      //  if($f[0]) {
      //    $form[$k]=$f;
      //    if(!in_array($k,$InPanel)) $PANELS[$PanelKey]["items"][]=$k;
      //  }
      //}
    } else {
      $f=mkFormFolder($REC,$key);
      if($f[0]) {
        $form[$key]=$f;
        if(!in_array($key,$InPanel)) $PANELS[$PanelKey]["items"][]=$key;
      }
    }
  }
  return $form;
}
