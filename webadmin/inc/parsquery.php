<?
if(count($this->Record)) {

  if(isset($this->Record["id"])) {
  
    $s=strtolower($this->LastQuery);      
    $i= strpos($s,"from ");
    $tbls=array();
    if($i>0 && strpos(" ".$s,"select")==1) {
      $w=" abcdefghijklmnopqrstuvwxyz_1234567890";
      $i+=5;
      while($i<strlen($s) && $s[$i]==" ") $i++;
  
      while($i<strlen($s)) {
        $ss="";
        while($i<strlen($s) && strpos($w,$s[$i])) $ss.=$s[$i++];
        $tbls[$ss]=array();
        $log=0;
        while($i<strlen($s) && !strpos($w,$s[$i])) if($s[$i++]==",") $log=1;

        if(!$log) break;
      }
      $s=substr($s,7,strpos($s,"from")-8);
      $s=str_replace(" ","",$s);
      $s=str_replace("\r","",$s);
      $s=str_replace("\n","",$s);
      $s=str_replace("\t","",$s);
      $s=explode(",",$s);
      foreach($s as $k=>$v) {
        $v=explode(".",$v);
        if(isset($v[1])) $tbls[$v[0]][]=$v[1];
      }     
    }
 
    foreach($this->Record as $k=>$v) if(is_string($k)) {
      $tab="";
      foreach($tbls as $key=>$val) {
        if(!$tab) $tab=$key;
        if(in_array($k,$val)) {
          $tab=$key;
          break;
        }
      }
      if($v && isset($this->EditMode[$tab]) && isset($this->EditMode[$tab][$k])) $this->Record[$k]="<span class=\"edt:$tab:$k:".$this->Record["id"].":".$this->EditMode[$tab][$k]."\">$v</span>";
    }

  } 
}
?>