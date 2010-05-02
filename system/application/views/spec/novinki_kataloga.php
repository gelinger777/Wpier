

<?
$this->db->where('novinka','1');
$this->db->limit(10);
//$this->db->order_by('dt');
$news=$this->db->get('products');

echo '

	<center>
 <h2>Neuigkeiten</h2>
';
if($news->num_rows()>0){



  foreach($news->result() as $new){


  echo '



	
	<a class="highslide" href="/gallery/show_product/'.$new->id.'" onclick=" return hs.htmlExpand(this, { objectType: \'ajax\'} );" >
	<img src="'.str_replace('..','',$new->img_0).'"  width="30"  alt="" style="max-width:30;max-height:30;" border="0" align=""><br>'.$new->name.'</a>

<br><br>


    ';

   }
   }
   ?>

  </center>
