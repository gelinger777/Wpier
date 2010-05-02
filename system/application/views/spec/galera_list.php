












<h2>Фотогалерея</h2>




<?
//$this->db->where('publ','1');
$this->db->limit(10,$page);
//$this->db->order_by('dt');
$news=$this->db->get('galera');

echo ' <table cellspacing="0" cellpadding="10">';
if($news->num_rows()>0){


  echo ' <tr>';
  $i=0;
  foreach($news->result() as $new){


if(($i%2)==0){

	echo '</tr>
  <tr>
    ';
}

  echo ' <td><a href="'.str_replace('..','',str_replace('/preview','',$new->img)).'"  rel="example9" title="'.$new->descr.'"><img src="'.str_replace('..','',$new->img).'" width="300" alt="" border="0" class="b1"></a></td>

    ';
  $i++;
   }
   }
   ?>


</table>



</div>
<div id="list">

<?
if(!@$pagination){

$config1['base_url'] = '/content/show_page/gallery/0/';
//$this->db->where('publ','1');
$new_all=$this->db->get($tbname);
$config1['total_rows'] = $new_all->num_rows();
$config1['per_page'] = '10';
$config1['uri_segment'] = 5;

$this->pagination->initialize($config1);

echo $this->pagination->create_links();
}
else{
	echo $pagination;
}
?>



