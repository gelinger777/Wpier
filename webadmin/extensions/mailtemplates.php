<?
/*DESCRIPTOR

Шаблоны писем

tools: admin
*/

$PROPERTIES=array(
"tbname"=>"mailtemplates",
"pagetitle"=>"Шаблоны писем",
"nolang"=>1
);

$F_ARRAY=Array (
"id" => "hidden||",
"mlname"=>"text| size=50 maxlength=255|Название",
"mlsubject"=>"text| size=50 maxlength=255|Тема",
"mlto"=>"text| size=50 maxlength=50|Куда",
"mlfrom"=>"text| size=50 maxlength=50|Обратный адрес",
"mlcontenttype"=>"select||Content-Type|1/Content-type: text&#47;html; charset=\"windows-1251\"|2/Content-type: text&#47;html; charset=\"koi-8r\"",
);

$f_array=Array(
"id" => "Код",
"mlname"=>"Название",
"mlsubject"=>"Тема",
"mlto"=>"Куда",
"mlfrom"=>"Обратный адрес",
);

if(isset($_GET["ch"]) && isset($MAINFRAME)) {
    $file="../".(isset($_USERDIR)? "www/$_USERDIR/":"").$_CONFIG["TEMPLATES_DIR"]."/mail/".intval($_GET["ch"]).".htm";
    if(!file_exists($file)) {
        copy("./editorhtml/new/mail.src",$file);
    }
    $FORM_EXTEND="<tr><td></td><td><INPUT type='button' value='Редактировать шаблон' onclick='runHtmlEditor(\"".$file."\")'>&nbsp;";
	if($FTP_UPLOAD_LOG) {
			$FORM_EXTEND.="<input type='button' onclick='document.frames[\"operateframe\"].navigate(\"./editorhtml/ftpcopy.php?file=".$file."\")'  value='сохранить на FTP'>";
		}
	$FORM_EXTEND.="</td></tr>";
}
if(isset($_GET["del"])) {
    unlink("../".(isset($_USERDIR)? "www/$_USERDIR/":"").$_CONFIG["TEMPLATES_DIR"]."/mail/".intval($_GET["del"]).".htm");
}
