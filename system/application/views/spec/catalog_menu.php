
<!--<?echo $this->uri->segment(4);   ?>-->
	<h2>Produkt Kategorien</h2>

<?
$this->db->order_by('priority');
$cats=$this->db->get('catalog_subcats');











echo '

<ul id="cont">';

//$this->load->helper('inflector');
   if($cats->num_rows()>0){

       $cikl=1;

      foreach($cats->result_array() as $new){






           echo ' <a style="display:block;" class="mainlevel"  href="/content/show_page/cat/'.$new['id'].'">'.$new['name'].'</a><br>';


         













          $cikl++;
         }


      }



   else{



   	echo 'No CAT';



   }



   ?>

  </ul>


