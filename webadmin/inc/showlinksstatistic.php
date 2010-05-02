<?
$tables=$db->table_names();
if(!isset($tables["statisticlinks"])) {
   $db->query("CREATE TABLE statisticlinks (
  id int(11) NOT NULL auto_increment,
  PgUrl varchar(255) NOT NULL default '',
  PgTo varchar(255) NOT NULL default '',
  LinkIndex int(11) NOT NULL default '0',
  Clicks int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY PgURL (PgUrl)
) TYPE=MyISAM;");
}
if(!isset($tables["statisticlinksurl"])) {
   $db->query("CREATE TABLE statisticlinksurl (
  id int(11) NOT NULL,
  referal varchar(255) NOT NULL default '',
  rTime int(11),
  ip varchar(15) NOT NULL default '',  
  KEY PgURL (id)
) TYPE=MyISAM;");
}

$db->query("SELECT * FROM statisticlinks WHERE PgUrl='".$_SERVER["REQUEST_URI"]."'");
$cnt=0;
$links=array();
while($db->next_record()){
	$links[$db->Record["LinkIndex"]]=array($db->Record["Clicks"],$db->Record["PgTo"],$db->Record["id"]);
	$cnt+=$db->Record["Clicks"];
}
if($cnt) {
	$p=100/$cnt;
	$clr=2.55;?>
	<SCRIPT LANGUAGE='JavaScript'>
	
	function InouterLinkStatisticGo(id) {
	  window.open("/<?=$_CONFIG["ADMINDIR"]?>/stat/showrefs.php?id="+id,"statw_"+id);
	}
	
	AllCount=<?=$cnt?>;
	function InouterLinkStatisticShowCnt(indx,val,valper,red,blue,url,id) {
		if(document.all(indx)!=null) {
			var o=document.all(indx);
			var h=o.offsetHeight;
			o.style.border='1 solid red';
			o.outerHTML='<div id="LnouterStatDiv'+indx+'" style="position:absolute;background:#ff'+blue+blue+';border:1 solid #0;cursor:default;" title="Uri страницы: '+url+'\nКол-во кликов:'+val+'\nВсего на странице: '+AllCount+'\nПроцент кликов: '+valper+'%"><table border=0 cellspacing=0 cellpadding=0><tr><td bgcolor="#555555" style="font-family:Arial;font-size:9px;color:#ffffff;padding:2px">'+indx+'</td><td style="font-family:Arial;font-size:9px;color:#0;padding:2px" onclick="InouterLinkStatisticGo('+id+');" title="посмотреть статистику переходов">'+val+'&nbsp;('+parseInt(valper)+'%)</td></tr></table></div>'+o.outerHTML;
			document.all("LnouterStatDiv"+indx).style.top=document.all("LnouterStatDiv"+indx).offsetTop+h+1;
		}
	}
<?
	krsort($links);
        reset($links);
	foreach($links as $k=>$v) {
		$pr=$p*$v[0];
		$red=intval($clr*$pr);
		if($red<16) $red="0".dechex($red);
		else $red=dechex($red);
		$blue=255-intval($clr*$pr);
		if($blue<16) $blue="0".dechex($blue);
		else $blue=dechex($blue);
		echo "InouterLinkStatisticShowCnt($k,'".$v[0]."','".(floatval(number_format($pr, 2, '.', '')))."','$red','$blue','".$v[1]."','".$v[2]."');";
	}
	echo "</SCRIPT>";
}
?>