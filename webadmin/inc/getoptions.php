<?    
if(isset($_GET["e"]) && isset($_GET["v"]) && isset($_GET["d"])) {
	include "../autorisation.php";
	ReadNoEdiTable();
	/*if(!$_GET["v"]) {
           echo "YYYYY";
	   exit;
	} */
	$dd=explode(";",$_GET["d"]);
	$out=array();
	$n=0;
	$TestStr="";
$TestStr.=$_GET["d"]." ";
	foreach($dd as $tset) {
		$tset=explode("|",$tset);
		if(count($tset)<5 || !$tset[0] || !$tset[1] || !$tset[2] ||!$tset[3] || !$tset[4]) {
                  echo "";

                   exit;
                } 
                $out[$n]=$tset[0];
                

$TestStr.=$out[$n]." ";

                
                
                if($_GET["v"]) {
                  $db->query("SELECT ".$tset[3].",".$tset[4]." FROM ".$tset[2].($_GET["v"]? " WHERE ".$tset[1]."='".htmlspecialchars($_GET["v"])."'":"")." ORDER BY ".$tset[4]);
		  while($db->next_record()) {
	  		$out[$n].="|".str_replace('/','&#47;',$db->Record[0])."/".str_replace('/','&#47;',$db->Record[1]);
	   	  }
		} 
		$n++;
	}
	
	if(isset($_GET["func"])) {?>
<html>
<body onload="parent.<?=$_GET["func"]?>(document.body.innerHTML)"><?echo join("*n*",$out);?></body>
</html>
<?	} else echo join("*n*",$out);
}

?>