<?
function CheckPages() {
  global $db,$ADMINGROUP_CODE,$AdminLogin;
  
  if($AdminLogin=='root') return 1;

  if(isset($_GET["id"]) && $_GET["id"]) {
      
    $db->query("SELECT accesswrite.grp FROM catalogue,accesswrite WHERE catalogue.id=accesswrite.pg and catalogue.id='".intval($_GET["id"])."' and accesswrite.grp='".$ADMINGROUP_CODE."'");
    if($db->next_record()) return 1;
    $_GET["id"]="";
    return 0;
  }
   
  if(isset($_GET["idarr"])) {
    $_GET["idarr"]=explode(",", AddSlashes($_GET["idarr"]));
    $db->query("SELECT DISTINCT pg FROM accesswrite WHERE grp='".$ADMINGROUP_CODE."' and (pg='".join("' or pg='",$_GET["idarr"])."')");
    $_GET["idarr"]=array();
 
    while($db->next_record()) {
      $_GET["idarr"][]=$db->Record[0];
    }
    if(count($_GET["idarr"])) {
      $_GET["idarr"]=join(",",$_GET["idarr"]);
      return 1;
    }
    unset($_GET["idarr"]);
  }
  return 0;
}

function delete($file) {
	chmod($file,0777);
	if (is_dir($file)) {
		 $handle = opendir($file); 
		 while($filename = readdir($handle)) {
			  if ($filename != "." && $filename != "..") {
				  if(is_dir($file."/".$filename)) {
					delete($file."/".$filename);
				  } else {
					unlink($file."/".$filename);
				  }
			  }
		 }
		 closedir($handle);
		 echo "$file<br>";
		 rmdir($file);
	} else {
		 unlink($file);
	}
}

function CopyAccessRates($pageCode,$lid) {
global $db;
  // Наследуем права доступа на чтение
  $cods=array();
  $db->query("SELECT grp,rd,ad,ed, dl FROM accesspgadmins WHERE pg='$pageCode'");
  while($db->next_record()) $cods[]=$db->Record;
  foreach($cods as $gr) $db->query("INSERT INTO accesspgadmins (grp,pg,rd,ad,ed,dl) VALUES ('".$gr[0]."','$lid','".$gr[1]."','".$gr[2]."','".$gr[3]."','".$gr[4]."')");

  // Наследуем права доступа на запись
  $cods=array();
  $db->query("SELECT grp,pbl FROM accesspgpubl WHERE pg='$pageCode'");
  while($db->next_record()) $cods[]=$db->Record;
  foreach($cods as $gr) $db->query("INSERT INTO accesspgpubl (grp,pg,pbl) VALUES ('".$gr[0]."','$lid','".$gr[1]."')");
}

function getSpecProperties($spec) {
global $_USERDIR,$_CONFIG;
  //if($_USERDIR) {
    $f="$spec.php";
    $d="";
    if(file_exists($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/$f")) {
      $d=$_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/";
    } elseif(file_exists($_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/extensions/$f")) {
      $d=$_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/extensions/";
    }
    if(file_exists($d.$f)) {
      $fp=fopen($d.$f,"r");
      $seval=fread($fp,filesize($d.$f));
      fclose($fp);
      $spos1=strpos($seval,"//HEAD//");
      $spos2=strpos($seval,"//ENDHEAD//");
      $seval=substr($seval,$spos1,$spos2-$spos1);
      @eval($seval);
      if(isset($PROPERTIES)) return $PROPERTIES;
    }
    return array();
  //}
}

function deletePage($delIDArr) {
global $db, $LOGS_OBJ;
	/*$paths=array();
	$db->query("SELECT id, pid, dir FROM catalogue");
	while($db->next_record()) {
		$paths[$db->Record["id"]]=array($db->Record["pid"],$db->Record["dir"]);
	} */
	
	$out_ar=array();
	$Mods=array();
	foreach($delIDArr as $id) {
		$id=intval($id);

		// Проверим, какие страницы из выделенных может удалять данный пользователь
		$log_=1;

		$db->query("SELECT  dl, grp  FROM accesspgadmins WHERE pg='".$id."'");
		if($db->num_rows()>0) $log_=0;

		while($db->next_record()) {
		  if($_SESSION['admingroup']==$db->Record["grp"] && $db->Record["dl"]) {
			$log_=1;  
		  }
		}
		if($log_) {
			
			$delID=array();
			
			$db->query("SELECT dir, pid,id FROM catalogue WHERE id='$id'");
			if($db->next_record()) {
				
				$out_ar[]=$db->Record["id"]; 

				$mID=$db->Record["pid"];
				$dPath="/".$db->Record["dir"];
				$delID[]=$db->Record["id"];
			
			
				// на переиндексацию отправляем
				foreach($delID as $v) $LOGS_OBJ->UpdateIndex($v);
				
				$Mods[$id]=array();
				$db->query("SELECT spec FROM content WHERE catalogue_ID='$id'");
				while($db->next_record()) if($db->Record["spec"]) $Mods[$id][$db->Record["spec"]]=1;
			
				$db->query("DELETE FROM catalogue WHERE id='$id'");
				//$db->query("DELETE FROM catalogue_fin WHERE id='$id'");
				$db->query("DELETE FROM content WHERE catalogue_ID='$id'");
				//$db->query("DELETE FROM content_fin WHERE catalogue_ID='$id'");
				
				$pid_arr=array($id);
				while(count($pid_arr)) {
					$db->query("DELETE FROM catalogue WHERE id in (".join(",",$pid_arr).")");
					$db->query("DELETE FROM accesspgadmins WHERE pg in (".join(",",$pid_arr).")");		
					$db->query("DELETE FROM accesspgpubl WHERE pg in (".join(",",$pid_arr).")");		
					$db->query("SELECT id FROM catalogue WHERE pid in (".join(",",$pid_arr).")");
					$pid_arr=array();
					while($db->next_record()) $pid_arr[]=$db->Record[0];
					if(count($pid_arr)) {
						$db->query("SELECT spec,catalogue_id FROM content WHERE catalogue_id in (".join(",",$pid_arr).")");
						while($db->next_record()) if($db->Record["spec"]) {
							$Mods[$db->Record["catalogue_id"]][$db->Record["spec"]]=1;
						}
						$db->query("DELETE FROM content WHERE catalogue_id in (".join(",",$pid_arr).")");
						
					}				
				}
			}
		}
	}
		
	if(count($Mods)) {
	   foreach($Mods as $key=>$val) {

		  foreach($val as $k=>$v) {
			 $p=getSpecProperties($k);
			 if(isset($p["tbname"]) && isset($p["conect"])) {
				if(isset($p["alongconect"])) {
				  $id=array();
				  $db->query("SELECT rowidd FROM ".$p["tbname"]."catalogue WHERE pgid='$key'");
				  while($db->next_record()) $id[]=$db->Record[0];
				  if(count($id)) $db->query("DELETE FROM ".$p["tbname"]." WHERE ".$p["conect"]." in (".join(",",$id).")");
				}                    
				$db->query("DELETE FROM ".$p["tbname"]."catalogue WHERE pgID='$key'");
			 }
		  }
	   }
	}

	return $out_ar;
}

function mkContentCopy($defCod,$lid) {		
global $db;
	$db->query("SELECT id,cpid,spec FROM content WHERE catalogue_ID='$defCod' ORDER BY id");
	$sql=array();
	while($db->next_record()) {
		if(!$db->Record["cpid"]) {
			if(!isset($sql[$db->Record["id"]])) $sql[$db->Record["id"]]=array("spec"=>"","ext"=>array());
			$sql[$db->Record["id"]]["spec"]=$db->Record["spec"];
		} else {
			if(!isset($sql[$db->Record["cpid"]])) $sql[$db->Record["cpid"]]=array("spec"=>"","ext"=>array());
			$sql[$db->Record["cpid"]]["ext"][]=$db->Record["spec"];
		}				
	}
	foreach($sql as $k=>$v) {
		$db->query("INSERT INTO content (spec, catalogue_ID) VALUES ('".$v["spec"]."','$lid')");
		$cpid= getLastID();
		foreach($v["ext"] as $val) {
			$db->query("INSERT INTO content (spec, catalogue_ID, cpid) VALUES ('".$val."','$lid', '$cpid')");
		}
	}             
}

function makepath($id) {
global $tree, $_CONFIG;
	$path="";
	$ids=array();
	while(isset($tree[$id]) && !in_array($id,$ids)) {
		$path=$tree[$id][1]."/".$path;
		$id=$tree[$id][0];
		$ids[]=$id;
	}
	return str_replace("%id%",$path,$_CONFIG["URI_PREVIEW_TPL"]);
}
?>