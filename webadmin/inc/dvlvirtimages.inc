<?
$ext=strtolower(substr($HTML_FILE,strrpos($HTML_FILE,".")+1));
$FilesExtensions=array("gif","png","jpg", "jpeg","swf");
if(isset($_USERDIR) && $_USERDIR && $HTML_FILE && file_exists($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR".$_SERVER["REQUEST_URI"])) {
	
	if(in_array($ext,$FilesExtensions)) {
		header('Content-Type: image/'.$ext);
		header('Content-Length: '.filesize($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR".$_SERVER["REQUEST_URI"]));
	} elseif($ext="css") {
		header('Content-Type: text/css');
		header('Content-Length: '.filesize($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR".$_SERVER["REQUEST_URI"]));
	} else {
		header("HTTP/1.1 200 OK");
	}
	header('Last-Modified: '.gmdate('D, d M Y H:i:s',0).' GMT');
	header('Cache-control: public');	
	
	readfile($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR".$_SERVER["REQUEST_URI"]);

	$fn="./tmp/".md5($_SERVER["REMOTE_ADDR"]);
	if(file_exists($fn)) {
		$fp=fopen($fn,"r");
		$s=fread($fp,filesize($fn));
		$x=unserialize($s);
		fclose($fp);

		$x["files"][$_SERVER["REQUEST_URI"]]=filesize($_SERVER["DOCUMENT_ROOT"]."/www/$_USERDIR".$_SERVER["REQUEST_URI"]);

		$fp=fopen($fn,"w+");
		fwrite($fp,serialize($x));
		fclose($fp);
	}
	exit();
} elseif($ext && $ext!='html') {
	exit();
} else {
	if(file_exists("./tmp/".md5($_SERVER["REMOTE_ADDR"]))) unlink("./tmp/".md5($_SERVER["REMOTE_ADDR"]));
	header("HTTP/1.1 200 OK");
}
?>