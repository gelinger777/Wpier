 	 <h2>Наши координаты</h2>


<?php

$this->db->where('publ','1');

$pdfs=$this->db->get($tbname);


if($pdfs->num_rows()>0){


foreach($pdfs->result()as $pdf){

?>





	<b><?=$pdf->name;?>:</b><br>


<?=$pdf->adres;?>  <br>


Телефоны/факс: <?=$pdf->tel;?><br>


E-mail: <?=$pdf->mail;?><br>


<b>Время работы:</b><br>


<?=$pdf->time;?><br>


<b>Схема проезда:</b><br>

<a href="<?=str_replace("../userfiles/preview","/userfiles",$pdf->map);?>"  rel="example4"><img src="<?=str_replace("..","",$pdf->map);?>"  alt="" border="0" align=""> </a>


<br>
<br>












<?php }}?>
