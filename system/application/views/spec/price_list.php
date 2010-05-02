

<div  style="align:center"><center>

<h2>Скачать Прайс-Листы </h2>


<?php
//$this->db->where('main >','0');
$prices=$this->db->get('price_list');
if($prices->num_rows()>0){
	echo "";

foreach ($prices->result() as $price){
//@$file_info=get_file_info($_SERVER['DOCUMENT_ROOT'].str_replace('../','',$price->file);
//echo $_SERVER['DOCUMENT_ROOT'];
$file_size=formatBytes(filesize($_SERVER['DOCUMENT_ROOT'].str_replace('../','/',$price->file)));

//print_r($file_info);

echo '

   <p class="news">


<a href="/'.$price->file.'">'.$price->name.'</a>('.$file_size.')<br> ';

echo ''.$price->short_descr.'<br></p>';

 }}?>



 </center>
</div>
