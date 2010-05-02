<?
Header("Content-type: image/png");

if (isset($_GET["i"]) && $_GET["i"] && file_exists($_SERVER["DOCUMENT_ROOT"].$_GET["i"])) {

	$im = imagecreatefrompng ($_SERVER["DOCUMENT_ROOT"].$_GET["i"]);
	$cWhite=imagecolorclosest ($im,205,0,0);
	$cGrn=imagecolorclosest ($im,0,205,0);
	if(isset($_GET["p"])) {
		$p=explode(",",$_GET["p"]);
		if (count($p)>5) 
			ImagePolygon ($im, $p, (count($p)/2), $cWhite);
	}
	if(isset($_GET["point"])) {
		$p=explode(",",$_GET["point"]);
		if(count($p)>=2) {
		  for($i=0;$i<count($p)-1;$i+=2) { 
                    imagearc ($im, $p[$i], $p[$i+1], 7, 7, 0, 360, $cWhite);
                    imagearc ($im, $p[$i], $p[$i+1], 9, 9, 0, 360, $cGrn);
                    imagearc ($im, $p[$i], $p[$i+1], 11, 11, 0, 360, $cGrn);
                    imagearc ($im, $p[$i], $p[$i+1], 13, 13, 0, 360, $cGrn);
                  }
                }
		
	}
	imagepng ($im);
}
?>