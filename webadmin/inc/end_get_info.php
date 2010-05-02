<?
$i=md5($INOUTER_GET_INFO_LOG_ARRAY["url"]);

$INOUTER_GET_INFO_LOG_ARRAY["time"]=time()+microtime()-$INOUTER_GET_INFO_LOG_ARRAY["time"];
$INOUTER_GET_INFO_LOG_ARRAY["mm"]=memory_get_peak_usage();
$db->query("UPDATE sys_productivity SET tm=tm+".$INOUTER_GET_INFO_LOG_ARRAY["time"].", cnt=cnt+1, qr=qr+".$INOUTER_GET_INFO_LOG_ARRAY["queries"].", mm=mm+".$INOUTER_GET_INFO_LOG_ARRAY["mm"].", tmsr=tm/cnt, qrsr=qr/cnt, mmsr=mm/cnt  WHERE urlmd5='$i'");

if(!$db->DB->Affected_Rows()) {
    $db->query("INSERT INTO sys_productivity (urlmd5,url, title, tm,cnt,qr,mm,tmsr,qrsr,mmsr) VALUES (
    '$i',
    '".str_replace("'","&#39;",$INOUTER_GET_INFO_LOG_ARRAY["url"])."' ,
    '".$INOUTER_GET_INFO_LOG_ARRAY["title"]."' ,
    ".$INOUTER_GET_INFO_LOG_ARRAY["time"]." ,
    1,
    ".$INOUTER_GET_INFO_LOG_ARRAY["queries"].",
    ".$INOUTER_GET_INFO_LOG_ARRAY["mm"].",
    ".$INOUTER_GET_INFO_LOG_ARRAY["time"]." ,
    ".$INOUTER_GET_INFO_LOG_ARRAY["queries"].",
    ".$INOUTER_GET_INFO_LOG_ARRAY["mm"]."
    )");
}

//echo "TIME:".$INOUTER_GET_INFO_LOG_ARRAY["time"]."<br>";
//echo "MEMORY:".$INOUTER_GET_INFO_LOG_ARRAY["mm"];
//echo "QUERY=".$db->LastQuery;