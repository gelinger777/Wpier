<? header('Content-type: text/html; charset=utf-8');
$pr=$this->db->get_where('products',array('id'=>$product_id));
$prod=$pr->row();
?>

  <div class="popup">
                    <span class="close">
                        <a href="#" class="highslide-move" onclick="return false">двигать</a>
                        <a href="#" onclick="return hs.close(this)">закрыть</a>
                        
                    </span>
                    <img width="275" height="275" alt="" src="<?=str_replace('..','',str_replace('preview/','/',$prod->img_0));?>" />                    <strong>
                    
                    <?=$prod->name;?>
                    </strong>

                    <p>
                     
                     <?$kr=$this->db->query("SELECT DISTINCT * from `products` where id IN('$prod->krom_1','$prod->krom_2','$prod->krom_3','$prod->krom_4') ");
                     
                     if($kr->num_rows>0){
                     	$i=0;
                     echo "Кромка <table><tr>"	;
                     foreach ($kr->result() as $krom){

                     	
                     	if(($i%2)==0 and $i>=2){
                     		
                     		echo "</tr><tr>";
                     	}
                     echo '<td><img src="/'.str_replace('../','',$krom->img_0).'"></td>';	

                     
                     $i++;
                     }	
                     	
                     echo "</tr></table>";	
                     	
                     }
                     
                     ?>
                     
                     
                        
                    </p>
                
                
                
                
                
                
                
                
                
                
                </div>