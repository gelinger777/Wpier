<?
$TMP=eregi_replace("<body",'<BODY onclick="InouterGetClickStatistic()"',$TMP);
$TMP=eregi_replace("</body>",'<SCRIPT LANGUAGE="JavaScript">
function InouterGetClickStatistic() {

	document.cookie="InouterStatCounter=" + escape(window.event.srcElement.sourceIndex) + ";path=/;";
	document.cookie="InouterStatCounterPath=" + escape("'.$_SERVER["REQUEST_URI"].'") + ";path=/;";
	'.(isset($_SERVER["HTTP_REFERER"])? 'document.cookie="InouterStatCounterRef=" + escape("'.$_SERVER["HTTP_REFERER"].'") + ";path=/;";':'').'
	'.(isset($_SERVER["REMOTE_ADDR"])? 'document.cookie="InouterStatCounterIP=" + escape("'.$_SERVER["REMOTE_ADDR"].'") + ";path=/;";':'').'

}
</SCRIPT></BODY>',$TMP);

if(isset($_COOKIE["InouterStatCounter"]) && isset($_COOKIE["InouterStatCounterPath"])) {
	$db->query("SELECT id FROM statisticlinks WHERE PgUrl='".$_COOKIE["InouterStatCounterPath"]."' and LinkIndex='".intval($_COOKIE["InouterStatCounter"])."'");
	if($db->next_record()) {
          $id=$db->Record[0];
          $db->query("UPDATE statisticlinks SET Clicks=Clicks+1 WHERE id='".$db->Record[0]."'");
          
	} else {
          $db->query("INSERT INTO statisticlinks (PgUrl,LinkIndex,Clicks,PgTo) VALUES ('".$_COOKIE["InouterStatCounterPath"]."','".intval($_COOKIE["InouterStatCounter"])."','1','".$_SERVER["REQUEST_URI"]."')");
          $id=getLastID();
	}
        
	if(isset($_COOKIE["InouterStatCounterRef"])) {
	  $db->query("INSERT INTO statisticlinksurl (id,referal,rTime,ip) VALUES ('$id','".AddSlashes($_COOKIE["InouterStatCounterRef"])."','".mktime()."','".AddSlashes($_COOKIE["InouterStatCounterIP"])."')");
	  unset($_COOKIE["InouterStatCounterRef"]);
	  unset($_COOKIE["InouterStatCounterIP"]);
	}  
        unset($_COOKIE["InouterStatCounter"]);
}
//setcookie("InouterStatCounterPath",$_SERVER["REQUEST_URI"],time(),"/");
?>