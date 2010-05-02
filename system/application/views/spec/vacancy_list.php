 <h2>Вакансии</h2>
	
<div id="news">


<?
//$this->db->where('publ','1');
$this->db->limit(10,$page);
$this->db->order_by('id');
$news=$this->db->get('vacancy');

echo '<table cellspacing="0" cellpadding="0">';
if($news->num_rows()>0){


  echo ' <ul id="cont">';
  $i=0;
  foreach($news->result() as $new){
  
 
  echo '<li>

	<a href="/content/show_page/vacancy/'.$new->id.'.html">'.$new->name.'</a>
	';
	 
	
echo $new->short_descr.'</li>
     
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

$config1['base_url'] = '/show_mods/news_list/';
$this->db->where('publ','1');
$new_all=$this->db->get('news');
$config1['total_rows'] = $new_all->num_rows();
$config1['per_page'] = '10';

$this->pagination->initialize($config1);

echo $this->pagination->create_links();
}
else{
	echo $pagination;
}
?>



</div>

