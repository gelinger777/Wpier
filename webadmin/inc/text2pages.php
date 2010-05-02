<?
$B=$BLK;
$BLOCKS_V["text"]=explode("<P>---</P>",$BLOCKS_V["text"]);
if(count($BLOCKS_V["text"])>1) {
  parse_blk($_CONFIG["TEXT_TO_PAGES"],"PGS");
  
  $l=count($BLOCKS_V["text"]);
  if(isset($_GET["p"])) $x=intval($_GET["p"]);
  else $x=1;
  
  $list="";
  for($i=1;$i<=$l;$i++) {
    $list.= sendAr2blk(array("pg"=>$i,"activ"=>($x==$i? 1:0)),$PGS);
  }
  send2blk("PGS",$list);
  
  $x1="";
  if($x>2) $x1=1;
  $x2="";
  if($x>1) $x2=$x-1;
  $x3="";
  if($x<($l)) $x3=$x+1;
  $x4="";
  if($x<($l-1)) $x4=$l;
  
  send2blk("first",$x1);
  send2blk("prev",$x2);
  send2blk("next",$x3);
  send2blk("last",$x4);

  //

  $BLOCKS_V["text"]=$BLOCKS_V["text"][$x-1].$BLK;
  $BLK=$B;
  unset($B);
					
} else $BLOCKS_V["text"]=$BLOCKS_V["text"][0];
?>