

<?
$this->db->where('novinka','1');
//$this->db->limit(10);
//$this->db->order_by('dt');
$news=$this->db->get('products');

echo '

 <h2>НОВИНКИ</h2>
 <table cellpadding="0" cellspacing="0">
  <tr>
     <td colspan="2" class="td_col"></td>
  </tr>
  <tr>

     <td class="td_news_pos">
	 <ul id="catalog_0_lvl">';
if($news->num_rows()>0){



  foreach($news->result() as $new){


  echo '


    <li>
	<div><img src="/img/new.gif"></div>
	<a class="highslide" href="/gallery/show_product/'.$new->id.'" onclick=" return hs.htmlExpand(this, { objectType: \'ajax\'} );" >
	<img src="'.str_replace('..','',$new->img_0).'" width="60" height="60" alt="" border="0" align=""><br>'.$new->name.'</a>

	</li>


    ';

   }
   }
   ?>

  </ul>
</td>
    <td></td>
  </tr>

</table>

