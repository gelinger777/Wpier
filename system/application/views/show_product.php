<?
$pr=$this->db->get_where('products',array('id'=>$product_id));
$prod=$pr->row();
	$is_in_cart=$this->shop_model->check_in_basket($product_id);
                                    
	
?>
<link href="/style/style.css" rel="stylesheet" type="text/css" media="screen, all" />

<!--[if lte IE 6]><link rel="stylesheet" href="ie6.css" type="text/css"><![endif]-->


<style>

.product {
  max-width: 200px;
  max-height: 330px;
}


</style>


 <div class="popup">
                    <span class="close">
                   <!--     <a href="#" class="highslide-move" onclick="return false">Move</a>
                        <a href="#" onclick="return hs.close(this)">Schliesen</a>
<br><br><h3><?=$prod->name;?></h3>-->
                    </span>

<table cellspacing="10" cellpadding="0">
  <tr>
     <td><!-- ?????? ????? -->

                    <img class="product" alt="" border="0" align="" src="<?=str_replace('..','',str_replace('/preview/','/',$prod->img_0));?>"  />
                 
                 
                 
</td>
<td>

               
                    <div class="add-to-cart" id="add-to-cart_<?=$prod->id;?>">
                    
               <?php

               
               if($prod->price>0){
               if($is_in_cart!=TRUE ){?>  
              <h3 style="margin: 0px 0px 0.5em; font-size: 20px; font-weight: normal; line-height: 20px; color: rgb(83, 83, 83); padding: 0px;">Versandinfo:</h3>   
                    <div id="internal_add-to-cart_<?=$prod->id;?>">
                    <form id="uc-product-add-to-cart-form-16-1" method="post" accept-charset="UTF-8" action="/content/akhasheni-rot">
<div><input type="text" value="1"  id="qty_<?=$prod->id;?>" size="3">Stk.<br>
<input type="button" class="form-submit node-add-to-cart" value="In den Warenkorb legen" id="add_to_cart" name="add_to_cart" onclick="javascript:addToCart('<?=$prod->id;?>','prod','<?=md5($this->session->userdata('session_id'));?>')">

</div></form>
</div>
     <div class="product-info product sell"><span class="uc-price-product uc-price-sell uc-price"><span class="price-prefixes">Preis: </span>€<?=$prod->price?><span class="price-suffixes"><span class="price-vat-suffix"> inkl. 20% MwSt</span></span></span></div>                   
     </div> 
     
     
     <?php }else{
     	
     	
     	echo ' Das Produkt ist im Warenkorb<img src="/img/face_smile.png">';
     	
     	
     	
     	
     }} ?>  
     
      <div class="content">
        <div id="content-body">
        <?=$prod->descr;?>     </div>

        <div class="clear" id="product-details">
          <div id="field-group">
                                                           
                                                           
                                                           
                                                           
                                                           
                                                                       </div>

          <div id="price-group">
                        
</div>          </div>
        </div><!-- /product-details -->

        
               
              </div><!-- /content -->

            

</td>
  </tr>
  
     <td>

     
	 
	 </td>
  </tr>
</table>




                </div>
                
