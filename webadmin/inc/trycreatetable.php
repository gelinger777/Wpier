<?
if(mysql_errno()==1146) {
if(file_exists($_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/conf/newtables.php")) {
  include $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/conf/newtables.php";
  if(isset($CREAT_TAB)) {
    $s=explode($DB_NAME.".",mysql_error());
    if(isset($s[1])) {
	$s=substr($s[1],0,strpos($s[1],"'"));
	if(isset($CREAT_TAB[$s])) {
          $this->query($CREAT_TAB[$s]);
          echo "Создана отсутствующая таблица $s. Повторите действия.";
          exit;
        }
    }
  }
}
}else{
  if(file_exists($_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/conf/newfolders.php")) {
    include $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/conf/newfolders.php";
    $s=substr(mysql_error(),strpos(mysql_error(),"'")+1);
    $s=substr($s,0,strpos($s,"'"));
    $s=explode(".",$s);
    if(isset($s[1])) $s=$s[1];else $s=$s[0]; 
    $n=0;
    if(isset($NEW_FOLDERS[$s])) {
      foreach($NEW_FOLDERS[$s] as $k=>$v) {
        $folds=$this->folders_names($k);
        if(!isset($folds[$s])) {
          $this->query($v);
          echo "Добавлено поле '$s' в таблицу '$k'<br>";
          $n++;
        }
      }
    }
    if($n) exit;
  }
}
