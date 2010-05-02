<?php
/*
 * Библиотека работы с СУБД через абстрактный класс ADODB
 *
 *  TODO:
 *
 *
 */

include(dirname(__FILE__).'/../adodb/adodb.inc.php');


class DB_Sql {

  /* public: connection parameters */
  var $Host     = "";
  var $Database = "";
  var $User     = "";
  var $Password = "";
  var $LastQuery = "";
  var $InsTable = "";

  /* public: configuration parameters */
  var $Auto_Free     = 1;     ## Set to 1 for automatic free result
  var $Debug         = false;     ## Set to 1 for debugging messages.
  var $Halt_On_Error = "yes"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
  var $Seq_Table     = "db_sequence";

  /* public: result array and current row number */
  var $Record   = array();
  var $Row;

  /* public: current error number and error text */
  var $Errno    = 0;
  var $Error    = "";

  /* public: this is an api revision, not a CVS revision. */
  var $type     = "mysql";
  var $revision = "1.2";

  /* private: link and query handles */
  var $Link_ID  = 0;
  var $Query_ID = 0;

  var $LimitLog = 0;
  var $LimitFrom = 0;
  var $LimitCnt = 0;

  var $RecordEdit=array();
  var $EditMode=array();

  var $Session_start_time=0;
  var $Save_RL=0;

  var $DB=0;

  var $Tables=array();

  var $BREAK_KEYS=array("select","insert","delete","update");

  /* public: constructor */
  function DB_Sql($query = "") {
      $this->query($query);
  }

  /* public: some trivial reporting */
  function link_id() {
    //return $this->Link_ID;
  }

  function query_id() {
    //return $this->Query_ID;
  }

  /* public: коннектимся к базе */
  function connect($Database = "", $Host = "", $User = "", $Password = "") {
    global $_CONFIG;
    $this->Session_start_time=mktime();

	if ("" == $Database) $Database = $this->Database;
    if ("" == $Host) $Host     = $this->Host;
    if ("" == $User) $User     = $this->User;
    if ("" == $Password) $Password = $this->Password;
	$this->DB = ADONewConnection($this->type);
    $this->DB->debug = $this->Debug;
    $this->DB->Connect($Host, $User, $Password, $Database);


	return $this->DB;
  }

  /* public: отключаемся */
  function free() {
	 //$this->Query_ID->Close();
	 $this->Query_ID = 0;
  }

  /* public: делаем запрос */
  function query($Query_String,$dbName="",$step=0,$pg2pg=0,$blk="BLK") {
  global $DB_NAME,$MAINFRAME,$INOUTER_GET_INFO_LOG_ARRAY,$CALCULATE_QUERIES_LOG,$_CONFIG;

    if ($Query_String == "") return 0;
    if (!$this->DB) return 0;
	if ($this->Query_ID) $this->free();

	if(isset($_CONFIG["RESOURCES_LOG"]) && $_CONFIG["RESOURCES_LOG"] && isset($INOUTER_GET_INFO_LOG_ARRAY)) {
		$INOUTER_GET_INFO_LOG_ARRAY["queries"]++;
		$time=time()+microtime();
	}

	$Query_String=trim($Query_String);

	if(count($this->Tables)) $Query_String=$this->prp($Query_String);

	$this->InsTable="";
	$l=strlen($Query_String);
	// Если вставка, сохраним имя таблицы куда вставляем
	if(strtolower(substr($Query_String,0,11))=='insert into') {
		$i=11;
		while($i<$l && $Query_String[$i]==' ') $i++;
		while($i<$l && $Query_String[$i]!=' ') $this->InsTable.=$Query_String[$i++];
	}

	// Эмулируем LIMIT mysql для других баз
	$i=$l-1;
	$s='';
	$input=false;
	if($this->type!='mysql') {

		if(in_array(strtolower(substr($Query_String,0,6)),array('update','insert')) && strlen($Query_String)>4000) {
		// Для диных запросов вставки
			$input=array();
			$i=0;
			$j=0;
			$l=strlen($Query_String);
			$log=0;
			while($i<$l) {
				if(!$log && $Query_String[$i]=="'") {
					$log=$i;
					$input[$j]="";
				} elseif($log && $Query_String[$i]=="'" && $Query_String[($i-1)]!="\\") {
					if(strlen($input[$j])>2000) {
					  $Query_String=substr($Query_String,0,$log).":$j".substr($Query_String,($i+1));
					  $i=$log+strlen(":$j");

					  $l=strlen($Query_String);

				          $j++;
                                        } unset($input[$j]);
                                        $log=0;
				} elseif($log) {
					$input[$j].=$Query_String[$i];
				}
				$i++;
			}

/*ob_end_clean();
ob_start();
print_r($input);
$fff=fopen("../tmp/loglong1.txt","w+");
fwrite($fff, ob_get_contents());
fclose($fff);
ob_end_clean();*/
		}

		if(strtolower(substr($Query_String,0,6))=='select') {
		  while($i>0 && $Query_String[$i]!=' ' && $Query_String[$i]!=',') $s=$Query_String[$i--].$s;
		  if($s==strval(intval($s))) { // если в конце целое число, возможно это LIMIT
			while($i>0 && ($Query_String[$i]==' ' || $Query_String[$i]==',')) $i--;
			$s1="";$s2="";
			while($i>0 && $Query_String[$i]!=' ') $s1=$Query_String[$i--].$s1;

			if($s1==strval(intval($s1))) { // если второе тоже число
				while($i>0 && $Query_String[$i]==' ') $i--;
				while($i>0 && $Query_String[$i]!=' ') $s2=$Query_String[$i--].$s2;
			}
			if(strtolower($s1)=="limit") {
				$s=intval($s);
				$s1=-1;
			}
			elseif(strtolower($s2)=="limit") {
				$s=intval($s);
				$s1=intval($s1);
			} else $s="";

			if($s) {
				$Query_String=substr($Query_String,0,$i);
				$this->Query_ID = $this->DB->SelectLimit($Query_String,$s,$s1);
				$this->LimitFrom=0;
			}
		  } else $s="";
		} else $Query_String=str_replace("''","NULL",$Query_String);
	} elseif(in_array(strtolower(substr($Query_String,0,6)),array('update','insert'))) {
	  $Query_String=str_replace("''","NULL",$Query_String);
	}

	$this->LastQuery = $Query_String;

	// Если данные удаляются в админке, сохраним в корзине
	if($_CONFIG["RESURCE_LED"] && isset($_SESSION['adminlogin']) && $this->Save_RL && strtolower(substr($Query_String,0,6))=='delete') {
		$this->saveRL($Query_String);
	}

	if(!$s) {
	  if($step) {
		$start=0;
		if(isset($_GET["start"])) $start=intval($_GET["start"]);

		// Посчитаем количество записей по данному запросу
		$this->Query_ID = $this->DB->Execute("SELECT count(*) ".substr($Query_String,stripos($Query_String,"from ")));
		$this->next_record();
		$this->Count_rows=$this->Record[0];

        	$this->Query_ID = $this->DB->SelectLimit($Query_String,$step,($start? $start:0));
		$this->LimitFrom=$this->mkPages($step,$pg2pg,$blk);

	  } else {




	  /*if($input && count($input)) {
            $ret=OCIParse($this->DB->_connectionID,$Query_String);
            foreach($input as $k=>$v) {
	      OCIBindByName($ret, ":$k", $v);
	      unset($v);
            }

            if($ret) {

              OCIExecute($ret);
            }

	  } else {*/
		$this->Query_ID = $this->DB->Execute($Query_String,$input);
          //}
		$this->LimitFrom=0;
	  }
	}



    // Если запустили запрос на сервере разработки, нужно записать лог со временем
   	/*if(isset($CALCULATE_QUERIES_LOG) && $CALCULATE_QUERIES_LOG) {
	  $time=time()+microtime()-$time;
	  $x=debug_backtrace();
	  $fp=fopen("./tmp/sql.log","a+");
	  $s=$x[0]["file"]."|".$x[0]["line"]."|".$time;
	  fwrite($fp,strlen($Query_String).":".$Query_String.strlen($s).":".$s);
	  fclose($fp);
	}*/

	$this->Row   = 0;

	// Сохраним багтрэйс в случае ошибки
	/*if (!$this->Query_ID) {
        $x=debug_backtrace();
		$Query_String="SQL Error: in query \"".str_replace("\r","",str_replace("\n"," ",$Query_String))."\" in ".$x[0]["file"]." on line ".$x[0]["line"];

		$fn=ini_get("error_log");
		if($fn) {
			$fp=fopen($fn,"w+");
			fwrite($fp,$Query_String);
			fclose($fp);
		}

		if(!count($this->table_names($DB_NAME))) {
			header("Location: /webadmin/setup.php");
			exit;
		}
		echo $Query_String;
		exit;
    }*/


    return $this->Query_ID;
  }

  // Сохраним в корзине то, что удаляем
  function saveRL($qs) {
	global $ADMIN_ID,$PROPERTIES;

	$modname="unknown";
	if(isset($PROPERTIES) && isset($PROPERTIES["pagetitle"])) $modname=$PROPERTIES["pagetitle"];

	$i=0;
	$l=strlen($qs);
	while($i<$l && strtolower(substr($qs,$i,4))!='from') $i++;
	if($i<$l) {
		$sql="SELECT * ".substr($qs,$i);
		$qid=$this->DB->Execute($sql);
		$rec=array();
		if(!$qid) {}else{
			while($r = $qid->FetchRow()) {
				foreach($r as $k=>$v) if(!is_string($k)) unset($r[$k]);
				$rec[]=$r;
			}

			// Прочитаем таблицу
			$i+=5;
			while($i<$l && ($qs[$i]==' ' || $qs[$i]=="\t" || $qs[$i]=="\n")) $i++;
			$t='';
			if($i<$l) {
				while($i<$l && $qs[$i]!=' ' && $qs[$i]!="\t" && $qs[$i]!="\n") $t.=$qs[$i++];
				if($t!='') {
					foreach($rec as $v) {
						$v="INSERT INTO resurceled (tabname,deltime,user_,dataled,modname) VALUES (
					   '".$t."',
					   '".$this->Session_start_time."',
					   '".$ADMIN_ID."',
					   '".str_replace("'","**#39#**",serialize($v))."',
					   '$modname'
					   )";
					  $this->query($v);
					}
				}
			}
		}
	}
  }

  function next_record($noint=0,$seek=0) {

	if(!$this->Query_ID) return 0;

	if($seek) $this->Query_ID->Move($seek);
	$rec = $this->Query_ID->FetchRow();

	// Переведем все имена полей в нижний регистр
	// Заодно уберем в массиве целочисленные ключи если надо
	if(is_array($rec)) {
		$this->Record=array();
		foreach($rec as $k=>$v) {
			if(!$v) $v="";
			else {
                         $v=stripslashes(trim($v));
                        }
			if(is_int($k)) {
			  if(!$noint) $this->Record[$k]=$v;
			} elseif(strtolower($k)==$k || strtoupper($k)==$k) $this->Record[strtolower($k)]=$v;
			else $this->Record[$k]=$v;
		}
	} else $this->Record=0;
	unset($rec);
	$this->RecordEdit = $this->Record;
	if(is_array($this->Record) && count($this->EditMode)){
      include dirname(__FILE__)."/parsquery.php";
    }
    $this->Row   += 1;
    $stat = is_array($this->Record);
    if (!$stat && $this->Auto_Free) {
      $this->free();
    }
    return $stat;
  }

  function seek($pos = 0) {
    return $this->Query_ID->Move($pos);
  }

  function lock($table, $mode="write") {
    return 0;
  }

  function unlock() {
    return 0;
  }
  function affected_rows() {
    return $this->Query_ID->Affected_Rows();
  }

  function num_rows() {
    if(!$this->Query_ID) return 0;
	return $this->Query_ID->RecordCount();
  }

  function num_fields() {
    return $this->Query_ID->FieldCount();
  }

  function nf() {
    return $this->num_rows();
  }

  function np() {
    print $this->num_rows();
  }

  function f($Name) {
    return $this->Record[$Name];
  }

  function p($Name) {
    print $this->Record[$Name];
  }


  /* private: error handling */
  function halt($msg) {

  }

  function haltmsg($msg) {
    printf("</td></tr></table><b>Database error:</b> %s<br>\n", $msg);
    printf("<b>MySQL Error</b>: %s (%s)<br>\n",
      $this->Errno,
      $this->Error);
  }

  function table_names($dbName="") {
    return $this->DB->MetaTables('TABLES');
  }

  function get_folders_name($tbName) {
	$rec=array();
	$r=$this->DB->MetaColumns($tbName);
	if(is_array($r)) {
		foreach($r as $k=>$v) {
			$k=$v->name;
			$v=$v->type;
			if(strtoupper($k)==$k || strtolower($k)==$k) $rec[strtolower($k)]=$v;
			else $rec[$k]=$v;
		}
	} else return false;
	return $rec;
  }

  function folders_names($tbname="") {
    return $this->get_folders_name($tbname);
  }

  function get_keys_name($tbName) {
	return $this->DB->MetaPrimaryKeys($tbName);
  }

  function get_inserted_id($table="") {
    return $this->DB->Insert_ID($table);
  }

// Обрабатываем пейджинг
	function mkPages($step,$pg2pg,$block="BLK") {
		global $$block;


		$get="";
		foreach($_GET as $k=>$v) {
			if($k!="start" && $k!="jump") {
			if(is_array($v)) {
				foreach($v as $key=>$val) {
					$val=htmlspecialchars(addslashes($val));
					$get.="&$k%5B$key%5D=".str_replace('%','&#37;',urlencode($val));
				}
		    } else
			  $v=htmlspecialchars(addslashes($v));
		      $get.="&$k=".str_replace('%','&#37;',urlencode($v));
			}
		}
		$$block=str_replace("%qs%",$get,$$block);
       		$CURRPAGE=parse_tmp("CURRPAGE",$block);
		$PAGES=parse_tmp("PAGES",$block);
		$MORE=parse_tmp("MORE");

		$start=0;
		if(isset($_GET["jump"])) {
			$_GET["start"]=(intval($_GET["jump"])-1)*$step;
		}
		if(isset($_GET["start"])) $start=$_GET["start"];
		else $start=0;
		$pages="";
		$countRows=0;
		if($this->Count_rows) {
			$countRows=$this->Count_rows;
			$$block=str_replace("%numrows%",$countRows,$$block);
			$n=intval($countRows/$step);
			if ($countRows>$n*$step) $n++;
		} else return 0;

		$list="";
		$i=0;
		$$block=str_replace("%allpages%",$n,$$block);
		$$block=str_replace("%allpagesrows%",$countRows,$$block);
		$$block=str_replace("%pgPrev%",($start-$step),$$block);
		$$block=str_replace("%pgNext%",($start+$step),$$block);
		if(isset($pg2pg) && $pg2pg) {
			$nn=intval($pg2pg/2);
			$i=intval($start/$step);
			if($i) $i-=$nn;
			if($i<0) $i=0;
			$nn=$i+$pg2pg;
			if($nn>$n) $i=$n-$pg2pg;
			else $n=$i+$pg2pg;
			if($i<0) $i=0;

			$nn=intval($countRows/$step);
			$nn=$countRows-$nn*$step;
			if(!$nn) $ne=$countRows-$step;
			else $ne=$countRows-$nn;
			if($ne>$start) {$$block=str_replace("%pageEnd%",$ne,$$block);}

			$nn=($i-$pg2pg)*$step;
			if($nn>0) $$block=str_replace("%pageDown%",$nn,$$block);
			$nn=($i+$pg2pg)*$step;
			if($nn>=$countRows || $nn==$ne) $nn=0;
			$$block=str_replace("%pageUp%",$nn,$$block);
		}

		$list="";
		if ($n>1) for ($i;$i<$n;$i++) {
			if ($start==strval($i*$step)) {
				$st=$CURRPAGE;
				$BLK=str_replace("%currentpage%",strval($i+1),$BLK);
			}
			else $st=$PAGES;
			$st=str_replace("%start%",strval($i*$step),$st);
			$st=str_replace("%page%",strval($i+1),$st);
			$list.=$st;
		}

		$$block=str_replace("%pages%",$list, TMP_if_blocks($$block,"pages",($list? 1:0)));
		$$block=str_replace("%PAGES%",$list, TMP_if_blocks($$block,"PAGES",($list? 1:0)));
		return $start;
	}

// Пара методов для
  function PrepQuery($qs) {
     $qs=str_replace("\n"," ",$qs);
     $qs=str_replace("\t"," ",$qs);
     $qs=str_replace("\r","",$qs);
     $qs=str_replace(","," , ",$qs);
     $qs=str_replace("+"," + ",$qs);
     $qs=str_replace("-"," - ",$qs);
     $qs=str_replace("*"," * ",$qs);
     $qs=str_replace("/"," / ",$qs);
     $qs=str_replace("!="," != ",$qs);
     $qs=str_replace("="," = ",$qs);
     $qs=str_replace("! =","!=",$qs);
     $qs=str_replace("<"," < ",$qs);
     $qs=str_replace(">"," > ",$qs);
     $qs=str_replace(")"," ) ",$qs);
     $qs=str_replace("("," ( ",$qs);
     return $qs;
  }


  function prp($Query_String) {
    $qs=trim(strtoupper($Query_String));
    $pos=0;
    while(isset($this->BREAK_KEYS[$pos]) && strpos(" $qs",strtoupper($this->BREAK_KEYS[$pos]))!=1) $pos++;

    if($pos<count($this->BREAK_KEYS)) {

      $s=explode("'",$qs);
      $Query_String=explode("'",$Query_String);
      $tbn=array();
      foreach($s as $k=>$v) if(!$k%2) {

            $v=str_replace("`"," ",$this->PrepQuery($s[$k]));
            $v=str_replace("."," ",$v);
            $v=explode(" ",$v);
            foreach($v as $kk=>$vv) {
                if(trim($vv) && isset($this->Tables[trim($vv)])) $tbn[trim($vv)]=$this->Tables[trim($vv)];
            }

      }
      foreach($tbn as $kt=>$folds) {
            foreach($Query_String as $k=>$v) {
                if(!($k%2)) {
					$Query_String[$k]=$this->PrepQuery($Query_String[$k])." ";
					$Query_String[$k]=str_ireplace(" ".$kt." "," ".$kt." ",$Query_String[$k]);
					$Query_String[$k]=str_ireplace(" ".$kt."."," ".$kt.".",$Query_String[$k]);
					foreach($folds as $kk=>$vv) {
						$Query_String[$k]=str_replace(" ".$kk." ",' "'.$kk.'" ',$Query_String[$k]);
						$Query_String[$k]=str_replace(".".$kk." ",'."'.$kk.'" ',$Query_String[$k]);
					}
				}
		 }
	  }

     $Query_String=join("'",$Query_String);
	}
     return trim($Query_String);
   }

}

function getLastID($table="") {
    global $db;
	if(!$table) $table=$db->InsTable;
	return $db->get_inserted_id($table);
}

