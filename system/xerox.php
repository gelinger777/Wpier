<?

echo "Zhdite";
include_once $_SERVER['DOCUMENT_ROOT']."/webadmin/editor/resizeimg.php";
	

$end=$_GET['end'];


if(!$end){


$end=0;
}
$fn=$_FILES['catalog']['tmp_name'];
    $dir=$_SERVER['DOCUMENT_ROOT']."/upload";
    


$ii=0;
$jj=0;
$d=$_SERVER['DOCUMENT_ROOT']."/userfiles/catalog/kolekcii/";


         
            
$src=$_SERVER['DOCUMENT_ROOT']."/upload";
$dst=$_SERVER['DOCUMENT_ROOT']."/userfiles/catalog/kolekcii";
$dst_small=$_SERVER['DOCUMENT_ROOT']."/userfiles/catalog/kolekcii/small/";

           $dir1 = opendir($src);
   // @mkdir($dst);
    while(false !== ( $file = readdir($dir1)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                continue;
            }
            else {


             $fufu=explode(".",$file);


$ext=$fufu[1];

if($ext=="jpg" or $ext=="JPG" or $ext=="gif"){


$ii++;




if($ii>$end+100){

//$my_end=$ii-1;
 closedir($dir); 

header("Location: xerox.php?end=$ii");



}






if($end>0 and $ii<$end){


continue;



}










           $copy_it=copy($src . '/' . $file,$dst . '/' . $file);
mkResizeImg($dst_small.$file,$dst."/".$file,"85x85");
/*
if($copy_it){

echo $file.'->Ok<br>';
}
else{
echo $file.'->ОшИБКА';
if(is_file($dst . '/' . $file)){
echo" : файл существует";
}

echo '<br>';


}


*/


}

            }
        }
    }
    closedir($dir); 

echo "Импорт Закончен";

?>