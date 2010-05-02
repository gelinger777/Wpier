
<?php 

$this->db->where('publ','1');

$pdfs=$this->db->get($tbname);


if($pdfs->num_rows()>0){



foreach($pdfs->result()as $pdf){

?>
<h2><?=$pdf->name?></h2>
	
<div id="cat">
<table cellspacing="0" cellpadding="0">
  <tr>
     <td id="conteiner" valign="top" align="center">

<a href="/<?=$pdf->file?>"><img src="/<?=str_replace('/preview','',$pdf->img)?>" width="200"  alt="" border="0" align="center"></a>
</td>
     <td class="cont_vert">&nbsp;</td>
     <td id="conteiner">

<ul>
<li><a href="/<?=$pdf->file?>"><?=$pdf->name;?></a> <?=formatBytes(filesize($_SERVER['DOCUMENT_ROOT'].str_replace('../','/',$pdf->file)));?></li>
</ul></td>
  </tr>
  <tr>

     <td colspan="3" class="cont"></td>
  </tr>
</table>
<?=$pdf->descr?>
</div><br>


<?php }}?>

