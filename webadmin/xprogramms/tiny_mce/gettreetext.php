<?
if(isset($_GET["id"])) {
  include "../../autorisation.php";

  $db->query("SELECT id,spec,text FROM content WHERE catalogue_ID='".intval($_GET["id"])."'");
  $texts=array();
  $first=0;
  while($db->next_record()) {
    if($db->Record["spec"]=='') {
      if(!$first) $first=$db->Record["id"];
      if($db->Record["text"]!='') $texts[]=$db->Record["id"];
    }
  }
  echo count($texts)? join("|",$texts):$first;
}
