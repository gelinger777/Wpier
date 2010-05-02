<?
copy($_FILES["file"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/".$_CONFIG["TEMP_DIR"]."/".$_FILES["file"]["name"]);
echo str_replace("\\","/",$_SERVER["DOCUMENT_ROOT"]."/".$_CONFIG["ADMINDIR"]."/".$_CONFIG["TEMP_DIR"]."/".$_FILES["file"]["name"]);
