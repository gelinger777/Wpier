
  

	<h2><?=$current_page_name;?></h2>

<?
$page=$this->db->get_where('catalogue_fin',array('id'=>$current_page_id));   	
   	
 $page_data=$page->row();  	
   	
//echo $current_page_id;

$this->db->where('pid',$current_page_id);
$this->db->limit(10);
$this->db->order_by('indx');
$news=$this->db->get('catalogue_fin');

if($news->num_rows()<1){
	

$this->db->where('pid',$page_data->pid);
$this->db->where('pid !=',0);
$this->db->limit(10);
$this->db->order_by('indx');
$news=$this->db->get('catalogue_fin');	
	
	
	
	
	
	
}








echo '
	 
<ul id="cont">';
if($news->num_rows()>0){


  
  foreach($news->result() as $new){
  
     
  echo ' 
  

     <li><a href="/content/show_page/'.$new->dir.'">'.$new->title.'</a></li>
  
  
    ';
  
   }
   }
   else{
   	

   	
   	
   	
   	
   	
   }
   
   
   
   ?>
  
  </ul>


