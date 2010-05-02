<?
$file=$_SERVER["IPR_DIR"]."/style/styles.xml";

if(!file_exists($file)) {
  $file=$_SERVER["DOCUMENT_ROOT"]."/style/styles.xml";
  if(!file_exists($file)) $file=""; 
} 

if($file) {
  $fp=fopen($file,"r");
  $str=fread($fp,filesize($file));
  fclose($fp);
 
  
  include_once $_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/inc/xmlparser_php4.php";
  
  $dom=new XMLParser($str);
  $dom->Parse();
  //print_r($dom->document);
  
  
  $i=0;
  echo "MENU_STYLES=new Ext.menu.Menu({id:'MenuStyles',items:[";
  while(isset($dom->document->tagChildren[$i])) {
    $e=$dom->document->tagChildren[$i]->tagAttrs;
    $t=(string)$e["element"];
    $n=(string)$e["name"];
    if(isset($e["style"])) $stl=' style="'.(string)$e["style"].'"';
    else $stl='';
    $s='';
    /*if(isset($dom->Style[$i]->Attribute) && count($dom->Style[$i]->Attribute)) {
      foreach($dom->Style[$i]->Attribute as $v) {
        $e=$v->Attributes();
        if((string)$e["name"]=='style') $stl='';
        $s.=" ".(string)$e["name"]='"'.(string)$e["value"].'"';
      }      
    }*/   
       
    echo ($i? ",":"")."{text: '<span".$s.$stl.">$n</span>', handler: function(){MENU_STYLES.prw.ChStyle('$s','$t');}}";
    $i++;
  }
  echo "]});";
}
?>