<? 
function EchoBlank() {
  global $_CONFIG;
  readfile($_SERVER['DOCUMENT_ROOT']."/".$_CONFIG["ADMINDIR"]."/img/nopic.png");
  exit;
}

if(isset($_GET["f"]) && isset($_GET["t"]) && isset($_GET["wf"]) && isset($_GET["cod"])) {

  include $_SERVER["DOCUMENT_ROOT"]."/function.php";
  
  $db->query("SELECT ".AddSlashes($_GET["f"])." FROM ".AddSlashes($_GET["t"])." WHERE ".AddSlashes($_GET["wf"])."='".intval($_GET["cod"])."'");
  if($db->next_record() && $db->Record[0]!='deleted') {
    if(isset($_GET["size"])) {
      $imm = imagecreatefromstring(base64_decode($db->Record[0]));
      
      $size=explode("x",$_GET["size"]);
      $log0=strpos($size[0],"?");
      $log1=strpos($size[1],"?");
      $size[0]=intval($size[0]);
      $size[1]=intval($size[1]);
      
      if(!$size[0] && !$size[1]) EchoBlank();
      
      $szsrc=array(imagesx($imm),imagesy($imm));
 
      if($log0 && $size[0] && $szsrc[0]<=$size[0]) {
        $size[0]=$szsrc[0];
        $size[1]=$szsrc[1];
      }
      elseif($log1 && $size[1] && $szsrc[1]<=$size[1]) {
        $size[0]=$szsrc[0];
        $size[1]=$szsrc[1];        
      }
      elseif(!$size[0]) $size[0]=intval($szsrc[0]*$size[1]/$szsrc[1]);
      elseif(!$size[1]) $size[1]=intval($szsrc[1]*$size[0]/$szsrc[0]);
      else {
	$k=$size[0]/$size[1];
	if($szsrc[0]>$k*$szsrc[1]) {
          $szsrc[0]=$k*$szsrc[1];
        } else {
          $szsrc[1]=$szsrc[0]/$k;
        }
      }
	
      $im=imagecreatetruecolor($size[0],$size[1]);
	
      imagecopyresampled ($im, $imm, 0, 0, 0, 0, $size[0],$size[1], $szsrc[0], $szsrc[1]);

      if(!isset($_GET["ext"])) $_GET["ext"]="jpg";
      if($_GET["ext"]=="jpg" || $_GET["ext"]=="jpeg") {
         header('Content-Type: image/jpeg');
         imagejpeg($im); 
      }
      elseif($_GET["ext"]=="gif") {
         header('Content-Type: image/gif');
         imagegif($im); 
      }
      elseif($_GET["ext"]=="png") {
         header('Content-Type: image/png');
         imagepng($im); 
      } else
        EchoBlank(); 
      imagedestroy($im);
      imagedestroy($imm);
      exit;       
      
    } else {
      header('Content-Type: image/jpeg');
      echo base64_decode($db->Record[0]);
      exit;
    }     
  } 
}
EchoBlank();
?>