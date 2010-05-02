<?
class Logs_Class {
  
  var $log=0;
  var $indexfile="";
  
  // Сохраняем записи при удалении
  function SaveRecord($logcod,$tb,$where) {
    if(!$this->log) return "";
    global $db;
    $db->query("SELECT * FROM $tb WHERE $where");
    $sv=array();
    while($db->next_record()) {
      foreach($db->Record as $k=>$v) if(is_int($k)) unset($db->Record[$k]);
      $sv[]=$db->Record;
    }
    $db->query("INSERT INTO logsave (pid,tb,data) VALUES ('$logcod','".$tb."','".serialize($sv)."')");
  }
  
  // Обрабатываем модуль
  function Module($act,$ext,$descr="",$mod="") {
    if(!$this->log) return "";
    global $PROPERTIES;
    if(isset($PROPERTIES["pagetitle"])) $name=$PROPERTIES["pagetitle"];
    else $name=$ext;
    return $this->Write($act,"",$name,0,$descr,$ext);
  }
  
  // Создаем лог
  function Write($act,$id,$razd=0,$dscr="",$mod="") {
    if(!$this->log) return "";
    global $db;
    if(!$razd) {
      $db->query("SELECT title FROM catalogue WHERE id='".$id."'");
      if($db->next_record()) $razd=$db->Record[0];
    }  
    $db->query("INSERT INTO logs (razdcode, razd,usr,dat,act,dscrpt,modn) VALUES ('$id','".$razd."','".$_SESSION['adminlogin']."','".mktime()."','$act','".$dscr."','".$mod."')");
  
    return getLastID();
  }
  
  function GetPagePath($id) {
    global $db,$_CONFIG;
    $path="/";
    do {
      $db->query("SELECT dir,pid FROM catalogue_fin WHERE id='$id'");
      if($db->next_record()) {
        $id=$db->Record["pid"];
        $path="/".$db->Record["dir"].$path;
      } else $id=0;
    } while($id!=0);
    return $_CONFIG["SERVER"].$path;
  }
  
  // Пишем в файл переиндексации
  function UpdateIndex($pgid, $mod="", $modtab="", $modid=0) {
    
	if(!$this->indexfile) return "";
    
    global $db;
    
    $paths=array();
    
    // Если указан ID страницы, вычисляем путь и записываем в файл
    if($pgid) {      
      $paths[]=$this->GetPagePath($pgid);      
    } elseif($mod) {
       $pages=array();
       // Если указан код записи, посмотрим есть ли таблица связи
       if($modid && $modtab) {
         $tabs=$db->table_names();
         if(isset($tabs[$modtab."catalogue"])) {
           // ели таблица связей есть, шукаем коды страниц по ней
           $db->query("SELECT pgID FROM ".$modtab."catalogue WHERE rowID='".$modid."'");
           while($db->next_record()) {
             $pages[]=$db->Record[0];
           }
         }
       }
       if(!count($pages)) {
         $db->query("SELECT catalogue_ID FROM content_fin WHERE spec='$mod'");
         while($db->next_record()) {
           $pages[]=$db->Record[0];
         }
       }
       foreach($pages as $id) {
         $paths[]=$this->GetPagePath($id);
       }
    }
    if(count($paths)) {
      $fp=fopen($this->indexfile,"a+");
      fwrite($fp,join("\n",$paths)."\n");
      fclose($fp);
    }    
  }
}
?>