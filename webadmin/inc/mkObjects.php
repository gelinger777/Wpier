<?
if(!isset($OBJECTS)) {
	$OBJECTS=array();
	$oFiles=array();
}
foreach($F_ARRAY as $k=>$v) {
	if(is_string($v)) {
         $s=explode("|",$v);
		 $s[0]=explode("*",$s[0]);
		 $required=0;
		 if(count($s[0])==2) $required=1;else $required=0;
		 $s[0]=$s[0][0];
	  if(!isset($oFiles[$s[0]]) && file_exists($_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/inc/components/".$s[0].".php")) {
		include_once $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/inc/components/".$s[0].".php";
		$oFiles[$s[0]]=1;
	  }
	  if(!isset($OBJECTS[$k])) {
		$s='$OBJECTS[$k]=new T_'.$s[0].'();';
//echo $s;
		eval($s);
	  }
	  $OBJECTS[$k]->str2params ($k,$v);
	  if($required && isset($OBJECTS[$k]->required)) $OBJECTS[$k]->required=$required;
        } elseif(is_array($v)) {
          if(!isset($oFiles[$v[0]]) && file_exists($_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/inc/components/".$v[0].".php")) {
		include_once $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/inc/components/".$v[0].".php";
		$oFiles[$v[0]]=1;
	  }
          if(!isset($OBJECTS[$k])) {
            if(isset($v[1]["name"]) && !$v[1]["name"]) $v[1]["name"]=$k;
            $s='$OBJECTS[$k]=new T_'.$v[0].'($v[1]);';
	    eval($s);
          }
        }
    if(method_exists($OBJECTS[$k],'try_post')) $OBJECTS[$k]->try_post() ;
}
