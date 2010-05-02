
<?php
//$this->db->limit(1);
$this->db->order_by('priority');
$cati=$this->db->get('catalog_subcats');

?>

<table cellspacing="0" cellpadding="0" width="100%">  
  <tbody><tr>
    <td colspan="4" class="componentheading">Produkt Kategorien<?echo (3%5);?></td>
  </tr>
 
 <tr>
 
 <?php 
 
 
 
     if($cati->num_rows()>0){
 	
         
 	            $i=3;
 	       foreach($cati->result() as $kat){
 		
 		  
 	       
 		if(($i%3)==0){
 			
 			
 			echo "</tr><tr>";
 		}
 		    
 		$image='/img/no_photo.png';
 		
 		if(strlen($kat->img)>0 ){
 			
 			$image=str_replace('..','',$kat->img);
 		}
 		
 		
 		
 		
 		
 		   echo '
        <td width="25%" valign="top" style="text-align: center;">
          <a href="/content/show_page/cat/'.$kat->id.'/" title="'.$kat->name.'"> 
          <img border="0"  alt="'.$kat->name.'" src="'.$image.'"><br><h2>'.$kat->name.' </h2>     </a>
        </td>';
 		
 		
 	           
 	       	$i++;
 		
 	}
 	
 	
 	echo '</tr>' ; 
 	
 }?>
 
 
 
      
      
      
      
      </tbody></table>