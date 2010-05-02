<?
$this->db->where('partner','1');
//$this->db->limit(10);
//$this->db->order_by('dt');
$news=$this->db->get($tbname);


if($news->num_rows()>0){


  $i=1;
  foreach($news->result() as $new){


  echo '
      <br>
<br>
<table width="100%" cellspacing="10" cellpadding="0">
<tr valign="top">
<td>
<b>'.$new->name.'</b><br>
<img src="/'.$new->img.'"   alt="" border="0" style="max-width:283,max-height:142;margin: 7px 0px; border: 1px solid #780017;">
<a href="'.$new->url.'" target="_blank">'.$new->url.'</a>
</td>
<td>'.$new->descr.'</tr>
</table>



    ';
  $i++;
   }
   }
   ?>


