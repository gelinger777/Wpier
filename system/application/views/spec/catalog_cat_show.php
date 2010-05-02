
<?php

$this->load->model('shop_model');
$cat_i=$this->db->get_where('catalog_subcats',array('id'=>$my_item_id));
$cati=$cat_i->row();


if($this->uri->segment(5)!=TRUE){
//$this->db->limit(1);
$this->db->where('parent',$my_item_id);
//$this->db->order_by('priority,proizvoditel');
//$this->db->join('proizvoditeli','proizvoditeli.id=products.proizvoditel','left');
$catik=$this->db->get('proizvoditeli');

      $kk=1;
if($catik->num_rows()>0){

foreach($catik->result() as $group){


	
	
	
	if($kk>1){

     echo '<div class="g-clear"></div> ';

     }

     $kk++;
echo '<h2>'.$group->name.'</h2>';
                                  $this->db->order_by('id');
		                    $prods=$this->db->get_where('products',array('proizvoditel'=>$group->id));

	
		               


if($prods->num_rows()>0){

		                    	echo ' <ul id="catalog_9_lvl">
		                    	';
		                    	foreach($prods->result() as $prod){
		                    		
		                    
	
	$mysock = getimagesize(base_url().str_replace('../','',$prod->img_0));
	
			

		                    		                if($prod->novinka!=1){
		                    		                          	$nov_img='/img/0.gif';
		                    		                                  }
		                    		                            else{
		                    		                                  	$nov_img='/img/new.gif';
		                    		                                    }




		                    		                                    
	                    		         $price=$prod->price;                           
		                    		                                    
		                    		   if($price>0){
		                    		   	
		                    		   	$price='Preis:€'.$prod->price;
		                    		   } 

		                    		   if(strlen($prod->za_chto)>0){
		                    		   	
		                    		   	$price=$price.'/'.$prod->za_chto;
		                    		   }
		                    		   
		                    		   
		                    		   	$is_in_cart=$this->shop_model->check_in_basket($prod->id);
		                    		   	
		                    		   	
		                    		   		if($prod->price>0){
		                    		   	if($is_in_cart){
		                    		   		
		                    		   		
		                    		   		$cart_data='<img src="/img/face_smile.png" width="16" style="border:0;">';
		                    		   	}
		                    		   	else{
		                    		   		
		                    		   		
		                    		   		$cart_data='<input size="2" id="qty_waren_'.$prod->id.'" value="1"> <input type="button" class="form-submit node-add-to-cart" value="In den Warenkorb legen" id="add_to_cart" name="add_to_cart" onclick="javascript:addToCart(\''.$prod->id.'\',\'waren\',\''.md5($this->session->userdata('session_id')).'\')">
		                    		   		';
		                    		   	}
		                    		   		}
		                    		   		else{
		                    		   			$cart_data="";
		                    		   		}

		                    		echo '
<li>
		                    		<div><img src="'.$nov_img.'" width="29" height="15" class="none"></div>

		                    		
		                    		
<!--<a class="highslide" href="/gallery/show_product/'.$prod->id.'" onclick=" return hs.htmlExpand(this, { objectType: \'ajax\'} );" >
		                    		
!-->
<a  href="/shop/show_product/'.$prod->id.'" >

		                    		<img  src="'.str_replace('..','',$prod->img_0).'" '.$this->shop_model->imageResize("$mysock[0]","$mysock[1]", 120).'  border="0" ><br>

'.$prod->name.'</a><br>
 

'.$price.'<br><br>

<span id="waren_'.$prod->id.'">'.$cart_data.'</span></li>
';


  



		                    	}



echo '</ul>
<div class="g-clear"></div>
';

		                    }








}





}

}
else{
$mcat=  $cat_i->row_array();
   $idt=$this->uri->segment(5);
echo "<h2>".$mcat["link_".$idt]."</h2><br>";
echo  "".$mcat["text_".$idt]."<br>";







}

?>






