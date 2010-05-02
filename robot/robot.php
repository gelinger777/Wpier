#!/usr/local/bin/php
<?

//setlocale (LC_ALL, 'ru_RU.UTF8');

// SETTINGS -------------------------------
$_CONFIG=array();

$_CONFIG["DB_TYPE"]="mysql";
//putenv("ORACLE_HOME=/usr/lib/oracle/xe/app/oracle/product/10.2.0/server"); // äëÿ Oracle

$_CONFIG["DB_MAIN"]="wpiernewdb";
$_CONFIG["HOST"]="localhost";
$_CONFIG["USER"]="root";
$_CONFIG["PASSWD"]="kolobok";

$_CONFIG["URL"]="http://wpier.max/";
$_CONFIG["ADMINDIR"]='webadmin';

$_CONFIG["min_word"]=4;
$_CONFIG["descript_length"]=255;
// end SETTINGS -------------------------------

$COOK="";


$_CONFIG["RESURCE_LED"]=0;

include "../db_sql.php";
include "inc/func.php";

$db=new DB_sql;
$db->Database=$_CONFIG["DB_MAIN"];
$db->User = $_CONFIG["USER"];
$db->Password = $_CONFIG["PASSWD"];
$db->Host = $_CONFIG["HOST"];
$db->type = $_CONFIG["DB_TYPE"];
$db->connect($_CONFIG["DB_MAIN"],$_CONFIG["HOST"],$_CONFIG["USER"],$_CONFIG["PASSWD"]);

$LOG_RUN=10;

echo "Indexer started...\n";

function indexer($url) {
  global $db;
  
  $umd=md5($url);  
  $db->query("SELECT url FROM indexeslinks WHERE url='".$umd."' LIMIT 1");
  if(!$db->next_record()) {
   
    $s=wgc($url);
    
    $links=get_urls($s);
    $links=prep_links($links,$url);
  
    get_index($s,$url);
    
    sleep(1);
    
    $db->query("INSERT INTO indexeslinks (url) VALUES ('".$umd."')");
    
    foreach($links as $u) indexer($u);
    
  }
}

WHILE($LOG_RUN) {
  $db->query("TRUNCATE TABLE indexeslinks");
 
  indexer($_CONFIG["URL"]);  
  sleep(60); // заснем на минуту перед следующей индексацией
}

echo "Indexer stoped";

