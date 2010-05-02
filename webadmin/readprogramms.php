<?
$log_modlist=1;
if(isset($_GET["chmodgroup"])) {
	include "autorisation.php";
	$log_modlist=0;

	// Прочитаем модули у которых у данной группы есть доступ
	$moduls_access_grp=array();
	$db->query("SELECT mdl,rd,ad,ed,dl FROM accessmodadmins WHERE grp='".intval($_GET["chmodgroup"])."'");
	while($db->next_record()) {
		$moduls_access_grp[$db->Record["mdl"]]=array($db->Record["rd"],$db->Record["ad"],$db->Record["ed"],$db->Record["dl"]);
	}
	$mod_list_ar=array();
}

if($AdminLogin!='root') {
	$moduls_access=array();
	$db->query("SELECT mdl,rd,ad,ed,dl FROM accessmodadmins WHERE grp='".$ADMINGROUP."'");
	while($db->next_record()) {
		if($db->Record["rd"] || $db->Record["ad"] || $db->Record["ed"] || $db->Record["dl"])
			$moduls_access[]=$db->Record["mdl"];
	}
}


$moduls = array();
$ma=array(1,1,1,1);
$d = dir("tools");
while (false !== ($entry = $d->read())) {
 if(strpos($entry,'.exe.php')) {
    include "tools/".$entry;

    if(file_exists("location/".$_CONFIG["ADMIN_LOCATION"]."/extensions/".$entry)) {
    	include "location/".$_CONFIG["ADMIN_LOCATION"]."/extensions/".$entry;
    }

    if(isset($INIT["icon_small"])) {
	  if($AdminLogin=='root' || in_array($INIT["id"],$moduls_access)) {
		if($log_modlist)
			$moduls[strtolower($INIT["name"])] = '["'.$INIT["id"].'", "tools/'.$INIT["basedir"].$INIT["icon_small"].'","'.$INIT["name"].'",\''.$INIT["runstr"].'\']';
		else {
			$ar=array(0,0,0,0);
			if(isset($moduls_access_grp[$INIT["id"]])) $ar=$moduls_access_grp[$INIT["id"]];
			$moduls[strtolower($INIT["name"])]="{mod:'".$INIT["name"]."',
						rd:'tl:".$INIT["id"].":".$ar[0]."',
						ad:'tl:".$INIT["id"].":".$ar[1]."',
						ed:'tl:".$INIT["id"].":".$ar[2]."',
						dl:'tl:".$INIT["id"].":".$ar[3]."',
						uiProvider:'col',
						leaf:true,
						iconCls:'task'}";
			if(!$ar[0]) $ma[0]=0;
			if(!$ar[1]) $ma[1]=0;
			if(!$ar[2]) $ma[2]=0;
			if(!$ar[3]) $ma[3]=0;
		}
	  }
    }
  }
}
$d->close();

ksort($moduls);

if($log_modlist) echo 'MODULS_LIST[1]=['.join(",",$moduls).'];';
elseif(count($moduls)) {
  $mod_list_ar[]="{mod:ParentW.DLG.t('Settings'),
		rd:'tl:".$ma[0]."',
		ad:'tl:".$ma[1]."',
		ed:'tl:".$ma[2]."',
		dl:'tl:".$ma[3]."',
		uiProvider:'col',
		cls:'master-task',
        iconCls:'task-folder',
		children:[".join(",",$moduls)."]}";
}

$dirs = array();
if($_USERDIR) $dirs[]="../www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/";
$dirs[]="extensions/";

$moduls = array();
$ma=array(1,1,1,1);

foreach($dirs as $dir) {
$d = dir($dir);

while($name = $d->read()){
    if(!preg_match('/\.(php)$/', $name)) continue;
	$nm=substr($name,0,strrpos($name,'.'));
	$fp=fopen($dir.$name,"r");
    $str=fread($fp,filesize($dir.$name));
    fclose($fp);
    $str=substr($str,strpos($str,"/*DESCRIPTOR"));
    $str=substr($str,12,strpos($str,"*/"));
    $str=explode("\n",$str);
    if(count($str)>=2) {
	$t=trim($str[2]);
	$str[1]=explode(",",trim($str[1]));

	if(file_exists("location/".$_CONFIG["ADMIN_LOCATION"]."/extensions/".$name)) {
    	include_once "location/".$_CONFIG["ADMIN_LOCATION"]."/extensions/".$name;
    }

	if($str[1][0]=='1' || isset($_GET["chmodgroup"])) {
	  if($AdminLogin=='root' || in_array($nm,$moduls_access)) {
		  $ico=(file_exists($dir.$nm.".png")? $dir.$nm.".png":'ext/img/gear__pencil.png');
		  if($log_modlist)
		    $moduls[strtolower($t)] = '[\''.$nm.'\', \''.$ico.'\',\''.$t.'\',\'runModuleNew("'.$nm.'","'.$t.'","'.$ico.'")\']';
		  else {
			$ar=array(0,0,0,0);
			if(isset($moduls_access_grp[$nm])) $ar=$moduls_access_grp[$nm];
			$moduls[strtolower($t)]="{mod:'".$t."',
						rd:'ex:".$nm.":".$ar[0]."',
						ad:'ex:".$nm.":".$ar[1]."',
						ed:'ex:".$nm.":".$ar[2]."',
						dl:'ex:".$nm.":".$ar[3]."',
						uiProvider:'col',
						leaf:true,
						iconCls:'task'}";
			if(!$ar[0]) $ma[0]=0;
			if(!$ar[1]) $ma[1]=0;
			if(!$ar[2]) $ma[2]=0;
			if(!$ar[3]) $ma[3]=0;
		  }
	  }
	}
	}
}
}

ksort($moduls);

$d->close();
if($log_modlist) echo 'MODULS_LIST[0]=['.join(",",$moduls).'];';
elseif(count($moduls)) {
  $mod_list_ar[]="{mod:ParentW.DLG.t('Moduls'),
		rd:'ex:".$ma[0]."',
		ad:'ex:".$ma[1]."',
		ed:'ex:".$ma[2]."',
		dl:'ex:".$ma[3]."',
		uiProvider:'col',
		cls:'master-task',
        iconCls:'task-folder',
		children:[".join(",",$moduls)."]}";
}

$d = dir("xprogramms");
$moduls = array();
$ma=array(1,1,1,1);
while (false !== ($entry = $d->read())) {
  if(strpos($entry,'.exe.php')) {
    include "xprogramms/".$entry;

	if(file_exists("location/".$_CONFIG["ADMIN_LOCATION"]."/extensions/".$entry)) {
    	include "location/".$_CONFIG["ADMIN_LOCATION"]."/extensions/".$entry;
    }

    if(isset($INIT["js"])) {
    	$INIT["js"]=explode(",",$INIT["js"]);
    	foreach($INIT["js"] as $v) $AddJS[]="xprogramms/".$INIT["basedir"].trim($v);
    }

    if(isset($INIT["form_js"])) {
    	$INIT["form_js"]=explode(",",$INIT["form_js"]);
    	$a=array();
    	foreach($INIT["form_js"] as $v) $a[]="xprogramms/".$INIT["basedir"].trim($v);
    	$_SESSION["form_js"][$entry]=$a;
    	unset($a);
    }
    if(isset($INIT["grid_js"])) {
    	$INIT["grid_js"]=explode(",",$INIT["grid_js"]);
    	$a=array();
    	foreach($INIT["grid_js"] as $v) $a[]="xprogramms/".$INIT["basedir"].trim($v);
    	$_SESSION["grid_js"][$entry]=$a;
    	unset($a);
    }

    if(isset($INIT["css"])) {
        $INIT["css"]=explode(",",$INIT["css"]);
    	foreach($INIT["css"] as $v) $AddCSS[]="xprogramms/".$INIT["basedir"].$v;
    }
    if(isset($INIT["icon_small"])) {
	  if($AdminLogin=='root' || in_array($INIT["id"],$moduls_access)) {
		if($log_modlist) {
			$moduls[strtolower($INIT["name"])] = '["'.$INIT["id"].'", "xprogramms/'.$INIT["basedir"].$INIT["icon_small"].'","'.$INIT["name"].'",\''.(isset($INIT["runstr"])? $INIT["runstr"]:"App.run(\"xprogramms/".$INIT["basedir"].$INIT["runfile"]."\",\"".$INIT["name"]."\",null,".(isset($INIT["width"])? $INIT["width"]:"null").",".(isset($INIT["height"])? $INIT["height"]:"null").")").'\']';
		} else {

			$ar=array(0,0,0,0);
			if(isset($moduls_access_grp[$INIT["id"]])) $ar=$moduls_access_grp[$INIT["id"]];
			$moduls[strtolower($INIT["name"])]="{mod:'".$INIT["name"]."',
						rd:'xp:".$INIT["id"].":".$ar[0]."',
						ad:'xp:".$INIT["id"].":".$ar[1]."',
						ed:'xp:".$INIT["id"].":".$ar[2]."',
						dl:'xp:".$INIT["id"].":".$ar[3]."',
						uiProvider:'col',
						leaf:true,
						iconCls:'task'}";
			if(!$ar[0]) $ma[0]=0;
			if(!$ar[1]) $ma[1]=0;
			if(!$ar[2]) $ma[2]=0;
			if(!$ar[3]) $ma[3]=0;
		}
	  }
    }
  }
}

ksort($moduls);

$d->close();
if($log_modlist) echo 'MODULS_LIST[2]=['.join(",",$moduls).'];';
elseif(count($moduls)) {
  $mod_list_ar[]="{mod:ParentW.DLG.t('Programms'),
		rd:'xp:".$ma[0]."',
		ad:'xp:".$ma[1]."',
		ed:'xp:".$ma[2]."',
		dl:'xp:".$ma[3]."',
		uiProvider:'col',
		cls:'master-task',
        iconCls:'task-folder',
		children:[".join(",",$moduls)."]}";
}

if(isset($mod_list_ar) && count($mod_list_ar)) echo "[".join(",",$mod_list_ar)."]";
