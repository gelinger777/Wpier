<?
/*DESCRIPTOR
1,1
Список проектов

files:projects_list.htm
*/
$dirname=$_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/extensions/";
//$ApacheConfigPath="c:/Program Files/Apache Group/Apache/conf/httpd.conf";

require "./autorisation.php";
$db->query("USE ".$_CONFIG["DB_MAIN"]);
$db->query("SELECT id FROM hosts WHERE hostName='".$_SERVER["SERVER_NAME"]."'");
if($db->next_record()) {
	$_GET["ch"]=$db->Record[0];
} else exit;

require "./inc/descriptor.php";

//HEAD//
$PROPERTIES=array(
"tbname"=>"hosts",
"pagetitle"=>"Список проектов",
"orderby"=>"id",
"nolang"=>"yes",
"template_list" => "projects_list.htm",
"step"=>1000
);

$F_ARRAY=Array (
"id" => "hidden||",
"menu" => "hidden||",
"prjName"=>"text|size=50|Название проекта",
"separator_0"=>"separator||<h2>Локальные настройки</h2>",
"hostName"=>"text|size=50|Имя хоста",
"hostIP"=>"text|size=50|Адрес хоста",
"dbName"=>"text|size=10|Имя базы",
"dbUser"=>"text|size=10|Пользователь БД",
"dbPasswd"=>"text|size=10|Пароль БД",
"dirName"=>"text|size=10|Директория проекта",
"separator_1"=>"separator||<h2>Данные удаленного сервера проекта</h2>",
"www_host"=>"text|size=50|WWW-адрес сервера",
"ftp_host"=>"text|size=50|Адрес FTP сервера",
"ftp_user"=>"text|size=50|Имя пользователя FTP",
"ftp_passwd"=>"text|size=50|Пароль FTP",
"ftp_dir"=>"text|size=50|Web-директория на FTP",
"ftp_passive"=>"checkbox||Пассивный режим|",
"ftp_prepare"=>"checkbox||Oracle-преобразование кода|",
"ftp_sftp"=>"checkbox||Использовать SFTP|",
"db_host"=>"text|size=50|Адрес сервера БД",
"db_name"=>"text|size=50|Имя БД",
"db_user"=>"text|size=50|Пользователь БД",
"db_passwd"=>"text|size=50|Пароль БД",
"separator_2"=>"separator||",
"projTZ"=>"file|doc,zip*../userfiles/*maxlength=100|Техническое задание",
);

$f_array=Array(
"id" => "#",
"hostIP"=>"Адрес хоста|url|hostIP",
"prjName"=>"Проект|url|hostIP",
"www_host"=>"Адрес сайта|url|www_host",
"projTZ"=>"*hide*"
);
//ENDHEAD//

$_POST["menu"]="";

$sel=array();

if(isset($_POST["host"])) {
    $_POST["menu"]="";
	foreach($_POST["host"] as $v) if(file_exists($_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/extensions/$v")) {
        $sel[$v]=1;
        $fs=getDescriptor($v);
        if(isset($fs[1]) && $fs[1]) {
			$v=explode(".",$v);
			if($fs[4]) {
				$s='$menu_tool["'.$v[0].'"]="'.$fs[1].'";';
				$_POST["menu"].=$s;
			} else {
				$s='$menu_items["'.$v[0].'"]=array("'.$fs[1].'",'.$fs[0].',"'.$fs[2].'");';
				$_POST["menu"].=$s;
			}
	}

		// Копируем подшаблоны спецблоков
        foreach($fs[3] as $f) if($f) {
			$f1=$_SERVER["DOCUMENT_ROOT"]."/www/".$_POST["dirName"]."/templates_rus/spec/".$f;
            $f2=$_SERVER["DOCUMENT_ROOT"]."/templates_rus/spec/".$f;
            if(file_exists($f2) && !file_exists($f1)) {
				copy($f2,$f1);
            } elseif($fs[5]) {
				$f2=$_SERVER["DOCUMENT_ROOT"]."/www/".$fs[5]."/templates_rus/spec/".$f;
				 if(file_exists($f2) && !file_exists($f1)) {
					copy($f2,$f1);
				}
			}
        }
	}
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions")) {
		$dir=dir($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions");
		for ($dir->rewind();$file=$dir->read();) if(is_file($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/".$file)) {
			$fs=getDescriptor($file,$_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR/".$_CONFIG["ADMINDIR"]."/extensions/");
			$v=explode(".",$file);
			if(isset($fs[1]) && $fs[1]) {
				if($fs[4]) $_POST["menu"].='$menu_tool["'.$v[0].'"]="'.$fs[1].'";';
				elseif(count($fs)>=3) $_POST["menu"].='$menu_items["'.$v[0].'"]=array("'.$fs[1].'",'.$fs[0].',"'.$fs[2].'");';
			}
		}		
    }
}

//echo $_POST["menu"];

if(isset($_GET["ch"]) || isset($_GET["new"])) {
    $hostIP=0;
    if(isset($_GET["ch"]) && !count($sel)) {
        $db->query("SELECT menu, hostIP FROM hosts WHERE id='".intval($_GET["ch"])."'");
        if($db->next_record()) {
            $menu_items= array ();
            $menu_tool= array ();
            $hostIP=$db->Record["hostIP"];
            eval($db->Record[0]);
            foreach($menu_items as $k=>$v) {
                $sel[$k.".php"]=1;
            }
            foreach($menu_tool as $k=>$v) {
                $sel[$k.".php"]=1;
            }
        }
    }

    $FORM_EXTEND="<tr><td colspan=2><hr>";
    if($hostIP) {
        $FORM_EXTEND.="        
        <b>ФУНКЦИОНАЛЬНЫЕ МОДУЛИ</b>
        <hr><div id='pre0'>";
    }
    $i=0;
    $ADMTOOL="";
    $dir=dir($dirname);
    $files=array(array(),array());
    for ($dir->rewind();$file=$dir->read();) {
        $fs=getDescriptor($file);
        if($fs[1]) {
            if(!isset($files[$fs[4]][$fs[5]])) $files[$fs[4]][$fs[5]]=array();
            $files[$fs[4]][$fs[5]][]=array($file,$fs);
        }
    }
    $i=1;    
    foreach($files as $kk=>$fPart) {
        ksort($fPart);
        reset($fPart);
        foreach($fPart as $group=>$fList) {
            $st="";
            foreach($fList as $file) {
            
                        $st.="<INPUT type='checkbox' name='host[".($i++)."]' value='".$file[0]."'".(isset($sel[$file[0]])? " checked":"")."> <a href='' onclick='return shwPre($i);'><b>".$file[1][1]."</b></a><BR> ";
                        $st.="<pre id='pre$i' class='rsl' style='display:none'>".$file[1][2]."</pre>";
                        $i++;
            }
            if(!$group) $group="Общие модули";
            if($kk) $ADMTOOL.="&#149;&nbsp;<a href='' onclick='return shwPre($i);' class='menu'><b>$group</b></a><div id='pre$i' style='display:none'>$st</div><BR>";
            else $FORM_EXTEND.="&#149;&nbsp;<a href='' onclick='return shwPre($i);' class='menu'><b>$group</b></a><div id='pre$i' style='display:none'>$st</div><BR>";
            $i++;
        }
    }
    $FORM_EXTEND="$FORM_EXTEND<hr><b>АДМИНИСТРАТОРСКИЕ УТИЛИТЫ</b><hr>$ADMTOOL</div></td></tr><SCRIPT LANGUAGE='JavaScript' src='./js/host.js'></SCRIPT>";
}

require ("./output_interface.php");
?>