<h2>Filiale</h2>



<?
//$this->db->where('publ','1');
$this->db->limit(30);
//$this->db->order_by('dt');
$news=$this->db->get('filial');

echo '

';
if($news->num_rows()>0){



  foreach($news->result() as $new){

       if(strlen($new->map)>0){

       $titul='<a rel="example4" href="'.str_replace('..','',$new->map).'">'.$new->mesto.'</a>';
       }
       else{
       	$titul=$new->mesto;
       }
  	

  echo ''.$titul.'<br>

   '.$new->name.'<br> <!-- Название -->

    '.$new->adres.'
    Öffnungszeiten: '.$new->phone.'<br><br>

  

    ';

   }
   }
   ?>




