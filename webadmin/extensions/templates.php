<?
/*DESCRIPTOR
0,0
Шаблоны


tools: admin
version:0.1
author:
*/

if(!isset($_SESSION["len"])) $_SESSION["len"]="rus";

$PROPERTIES=array(
"tbname"=>"templates",
"pagetitle"=>"Шаблоны",
"orderby"=>"id",
"nolang"=>"yes",
//"formopenmode"=>"win:400:500",
"orderby"=>"id",
"FIX_ID_TO_COD"=>"tmpcod"
);

$F_ARRAY=Array (
"id" => "hidden||",
"tmpcod" => "hidden||",
"tmpname" => "text|size=100|Название шаблона",
"tmpimg"=>"file|*../".$_CONFIG["USERFILES_DIR"]."/|Картика схемы|show",
"tmpschema" => "text|size=2 maxlength=2|Количество блоков",
"tmpfile" => "file|htm,php*../".$_CONFIG["TEMPLATES_DIR"]."/|Файл шаблона",
"tmpdeflt" => "checkbox||По-умолчанию|",
);

$f_array=Array(
"id" => "*hide*",
"tmpcod" => "код",
"tmpname" => "Шаблон",
"tmpdeflt" => "По-умолчанию",
);

//$updown="id";
$FORM_EXTEND="";
if(isset($_GET["ch"])) {
	$db->query("SELECT tmpschema FROM templates WHERE id='".intval($_GET["ch"])."'");
	if($db->next_record()) {
		$FORM_EXTEND="<script>
			_COUNT_BLOCKS=".$db->Record[0].";
			_SAVEFUNC[_SAVEFUNC.length]=function() {
				var cnt=parseInt(document.getElementById('tmpschema').value);
				if(isNaN(cnt)) return false;
				if(cnt<_COUNT_BLOCKS) {
					if(ParentW.DLG.c('Some data blocks may be removed. Are you sure?')) {
						_COUNT_BLOCKS=cnt;
						return true;
					}
					return false;
				}
				_COUNT_BLOCKS=cnt;
				return true;
			}
		</script>";
	}
}

if(isset($_POST["id"]) && $_POST["id"]) {
	$db->query("SELECT tmpschema, tmpcod FROM templates WHERE id='".intval($_POST["id"])."'");
	if($db->next_record() && $db->Record[0]!=$_POST["tmpschema"]) {
		$tcode=$db->Record["tmpcod"];
		$db->query("SELECT content.id, content.catalogue_id FROM catalogue,content WHERE catalogue.tpl='".$db->Record["tmpcod"]."' and catalogue.id=content.catalogue_id and content.cpid is NULL ORDER BY content.id");
		$a=array();
		while($db->next_record()) {
			$a[$db->Record["catalogue_id"]][]=$db->Record["id"];
		}
		foreach($a as $k=>$v) {
			for($i=0;$i<$_POST["tmpschema"];$i++) {
				if(!isset($v[$i])) $db->query("INSERT INTO content (catalogue_id) VALUES ($k)");
			}
			if($i<count($v)) {
				for($i;$i<count($v);$i++) {
					$db->query("DELETE FROM content WHERE id=".$v[$i]." or cpid=".$v[$i]);
				}
			}
		}
	}
}

if(isset($_GET["ch"]) && isset($MAINFRAME)) {
	$db->query("SELECT tmpFile FROM templates WHERE id='".intval($_GET["ch"])."'");
	if($db->next_record() && file_exists($db->Record[0]) && $db->Record[0]) {
		$FORM_EXTEND.="<tr><td></td><td><input type='button' onclick='runHtmlEditor(\"".$db->Record[0]."\")' value='редактировать шаблон'>&nbsp;";

		if($FTP_UPLOAD_LOG) {
			$FORM_EXTEND.="<input type='button' onclick='document.frames[\"operateframe\"].navigate(\"./editorhtml/ftpcopy.php?file=".$db->Record[0]."\")'  value='сохранить на FTP'>";
		}
		$FORM_EXTEND.="</td>";
	}
}