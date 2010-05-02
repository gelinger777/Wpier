<?
include_once $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/editor/scripts/editor/resizeimg.php";

class Prepare_Html {
  
  var $Title="";
  var $Text="";
  var $HTML="";
  var $Type="";
  var $Files=array();
  var $MainFile="";
  var $MainFileName="";
  var $Dir="";
  
  // Описание тэгов
  // 0 - удаляются все атрибуты
  // 1 - удаляются атрибуты стилей
  // 2 - атрибуты не удаляются
  // 3 - удаляются таги со всеми вложениями
  // Если
  // Не перечисленные здесь таги удаляются
  var $Tags=array(
                  "br"=>0,
                  "p"=>0,
                  "a"=>1,
                  "img"=>1,
                  "i"=>0,
                  "b"=>0,
                  "li"=>0,
                  "ol"=>0,
                  "li"=>0,
                  "script"=>3,
                  "style"=>3,
                  "o:DocumentProperties"=>3,
                  "xml"=>3,
                  "table"=>0,
                  "tr"=>0,
                  "td"=>array("colspan","rowspan"),
                  "h1"=>0,
                  "h2"=>0,
                  "h3"=>0,
                  "u"=>0,
                  "strong"=>0); 

  
  function Prepare_Html($s) { 
    global $_CONFIG;
    
    if(!$s) return $this;
    
    $this->Dir=(isset($_CONFIG["TEXT_LINKED_FILES_DIR"])? $_CONFIG["TEXT_LINKED_FILES_DIR"]:"/files.images/").mktime().rand(1,100);
    $i=0;
    $n=strlen($s);
    $log=0;
    
    while($i<$n) {
      $l="";
      while($i<$n && $s[$i]!=':') $l.=$s[$i++];
      $i++;
      $fn=substr($s,$i,$l); $i+=$l;
      $l="";
    
      while($i<$n && $s[$i]!=':') $l.=$s[$i++];     
   
      $i++;      
      if($log) {       
        $file=substr($s,$i,$l);
        $this->Files[$fn]=$file;
        
      } else {
        $this->MainFileName=$fn;
        $this->MainFile=substr($s,$i,$l);         
        $log=1;
      }
      $i+=$l;  
    }      
    $fn=explode("-",$this->MainFileName);
//echo "file:".$fn;    
    if($fn[0]=="excel") {
      $this->HTML=$this->ParseExcel($this->MainFile);
    } elseif($fn[0]=="word") {
      $this->HTML=$this->ParseWord($this->MainFile);
    } 
  }
  
  function ParseExcel($str) {
    $s="";
    $fn="";
    $str=$this->CleaneTags($str);
    if(count($this->Files)) {             
      foreach($this->Files as $k=>$v) if($k && $v) {
        if(strpos(" $k","sheet")==1) {
          $s.=($s? "<br>":"").$v;
        } elseif($k!="filelist.xml" && $k!="filelist.xml" && $k!="stylesheet.css") {
          if(!file_exists($_SERVER["DOCUMENT_ROOT"].$this->Dir)) {
            $fn=substr($this->MainFileName,0,strrpos($this->MainFileName,"."));
            mkdir($_SERVER["DOCUMENT_ROOT"].$this->Dir);
          }
          $fp=fopen($_SERVER["DOCUMENT_ROOT"].$this->Dir."/".$k,"w+");
          fwrite($fp,$v);
          fclose($fp);
        }
      }
    }
    if(!$s) $s=$str;  
    else $s=$this->CleaneTags($s);
    if($fn) $s=str_replace("src=image","src=".$this->Dir."/image",$s);
   
    return $s;
  }
  
  function ParseWord($s) {
    $s=$this->CleaneTags($s);  
    
    $fn=substr($this->MainFileName,0,strrpos($this->MainFileName,"."));
    $s=str_replace("./".$fn.".files/",$this->Dir."/",$s);
    
    if(count($this->Files)>0) {
      mkdir($_SERVER["DOCUMENT_ROOT"].$this->Dir);
    }
    
    foreach($this->Files as $k=>$v) if($k && $v) {
      $fp=fopen($_SERVER["DOCUMENT_ROOT"].$this->Dir."/".$k,"w+");
      fwrite($fp,$v);
      fclose($fp);
    }
    return $s;
  }
  
  function CleaneTags($s) {  
     $tags=$this->Tags;
     $i=0;
     $n= strlen($s);
     $t="";
     $quot="";
     $res="";
     $ok=0;
     
     while($i<$n && strtolower(substr($s,$i,5))!="<body") $i++;
     
     while($i<$n) {
       $surchar=$s[$i];
       // Удаляем комментарий
       if(substr($s,$i,4)=="<!--") {
         $i+=4;
         while($i<$n && substr($s,$i,3)!="-->") $i++;
         $i+=3;
       } elseif(substr($s,$i,2)=="</") {
       // Если закрывающийся таг
         $i+=2;
         $t=""; 
         while($i<$n && $s[$i]!=">") $t.=$s[$i++];
         $i++;
         $t=strtolower($t); 
         if(isset($tags[$t]) && $tags[$t]!=3) {
           if(!$this->Title) {
            $this->Title=trim(substr($res,strrpos($res,">")+1));
           }
           $res.="</$t>";
         }
       } elseif($s[$i]=='<') {
       // Начало тэга, ищем его название
         $i++;
         $t="";            
         while($i<$n && $s[$i]!=' ' && $s[$i]!='>') $t.=$s[$i++];
         
         $t=strtolower($t);
         
         /*if($t=="title" && !$this->Title) {
           $ttl="";
           $j=$i+1;
           while($j<$n && $s[$j]!='<') $ttl.=$s[$j++];
           if($ttl) $this->Title=$ttl;
         }*/
         
         if(isset($tags[$t])) {
           
          if(is_array($tags[$t])) {
            $res.="<$t";
            while($i<$n && $s[$i]!='>') {
              while($i<$n && $s[$i]==' ' && $s[$i]!='>') $res.=$s[$i++];
              $a="";
              while($i<$n && $s[$i]!=' ' && $s[$i]!='>' && $s[$i]!='=') $a.=$s[$i++];
              $a=strtolower($a);
              $lga=0;
              if(in_array($a,$tags[$t])) {
                $lga=1;
                $res.=$a;
              }
              while($i<$n && $s[$i]!='=' && $s[$i]!='>') $i++;
              while($i<$n && $s[$i]==' ' && $s[$i]!='>') $i++;
              $sp=" ";
              if($s[$i]=='"' || $s[$i]=="'") $sp=$s[$i];
              while($i<$n && $s[$i]!=$sp) {
                if($sp==" " && $s[$i]=='>') break;
                if($lga) $res.=$s[$i++];else $i++;
              }
                 
            }
            
          } else {           
            
            if($t=="img") {
              $src="";
              $wd="";
              $hd="";
              // если это картинка, нужно скопировать в нужном размере
              while($i<$n && $s[$i]!=">") {
                
                if(strtolower(substr($s,$i,4))=="src=") {
                  $i+=4;
                  $sp=$s[$i++];                  
                  while($i<$n && $s[$i]!=$sp) $src.=$s[$i++];
                } else
                if(strtolower(substr($s,$i,6))=="width=") {
                  $i+=6;
                  $sp=$s[$i++];                  
                  while($i<$n && $s[$i]!=$sp) $wd.=$s[$i++];
                } else
                if(strtolower(substr($s,$i,7))=="height=") {
                  $i+=7;
                  $sp=$s[$i++];                  
                  while($i<$n && $s[$i]!=$sp) $hd.=$s[$i++];
                } else $i++;
              }
             
              if($src && file_exists($this->Tmpdir."$src")) {
                $res.="<img src=\"".$this->Imgdir.$src."\" />";
                mkResizeImg($_SERVER["DOCUMENT_ROOT"].$this->Imgdir.$src,$this->Tmpdir.$src,$wd."x".$hd);
                unlink($this->Tmpdir.$src);
              }
              $i++;
              
            } else {            
              switch($tags[$t]) {
                case 0: {
                  $res.="<$t>";
                  while($i<$n && $s[$i]!=">") $i++;
                  $i++;
                }break;
                case 1: {
                // Удаляем class и style  
                  $res.="<$t";
                  while($i<$n && $s[$i]!='>') {
                    $s1=strtolower(substr($s,$i,6));
                    if($s1==' class' || $s1==' style') {
                      $i+=7;
                      while($i<$n && $s[$i]==' ') $i++;
                      $i++;
                      while($i<$n && $s[$i]==' ') $i++;
                      if($s[$i]=='"' || $s[$i]=="'") $quot=$s[$i++];
                      else $quot=" ";
                      while($i<$n && $s[$i]!=$quot && $s[$i]!='>') $i++;                   
                    } else $res.=$s[$i++];
                  }
                  
                } break;
                case 2: {
                // Ничего не трогаем
                  $res.="<$t";
                  $quot="";
                  while($i<$n && ($s[$i]!='>' || ($s[$i]=='>' && $quot))) {
                    if($quot && $s[$i]==$quot) $quot="";
                    else if($s[$i]=='"' || $s[$i]=="'") $quot=$s[$i];
                    $res.=$s[$i++];
                  }
                  
                } break;
                case 3: {
                // Удаляем всё
                   $l= strlen($t)+3; 
                   while($i<$n && strtolower(substr($s,$i,$l))<>"</$t>") $i++;
                   
                } break;             
              }
            }
           }
         
         } else {
           while($i<$n && $s[$i]!=">") $i++;
           $i++;
         }         
       } else $res.=$s[$i++];
     }
     $r=0;
     $res=str_replace("\r\n"," ",$res);
     while($r!=strlen($res)) {$r=strlen($res);$res=str_replace("  "," ",$res);}
     return str_replace("<?","&lt;?",str_replace("?>","?&gt;",str_replace('$','&#36;',str_replace("%","&#37;",$res))));
  }
  
  
}
?>