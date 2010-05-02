<?
$TMP=eregi_replace("<body",'<BODY onkeypress="SendErrorReport(\''.$_CONFIG["ERROR_REPORT_MESS"].'\')"',$TMP);
$TMP=eregi_replace("</body>",'<IFRAME id="SendErrorFrame" src="/senderror.php" width=0 height=0 frameborder=0></IFRAME><SCRIPT LANGUAGE="JavaScript" src="/js/errorreport.js"></SCRIPT></BODY>',$TMP);
?>