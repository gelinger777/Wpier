<ul class="nav" id="horiznav">

     <?php 
   $this->db->order_by('mainmenu.id');
     $this->db->select('catalogue_fin.dir,mainmenu.item');
       $this->db->where('topm','1');
     $this->db->join('catalogue_fin','catalogue_fin.id=mainmenu.page','left');
     $items=$this->db->get('mainmenu');
     
     //berem segment URI chtob znat gde aktivirovat 
     
     $dir_id = $this->uri->segment(3,'home');
     //echo $dir_id;
     
     
     
     
     if($dir_id=='cat_cat' or $dir_id=='cat_subcat' or $dir_id=='products' or $dir_id=='show_producer'){
     	$dir_id='cat';
     }
     
     
     
     if($items->num_rows()>0){
     
     
     foreach($items->result() as $item){
     
     	
     	if("$dir_id"==="$item->dir" ){
     			
     		$class='class="active"';
     	}
     	else{
     		
     
     		$class='';
     	}
     			$link_it=anchor('/content/show_page/'.$item->dir,$item->item);
     	
     	
     	
     	
     
     echo '<li '.$class.'>'.$link_it.'</span></li>
     ';
     
     
     }
     
     }
     ?>
   
</ul>       
