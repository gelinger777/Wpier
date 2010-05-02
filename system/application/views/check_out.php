<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>



<title>Buratino-Shop Home</title>


<meta name="robots" content="index, follow" />

<meta name="author and developer" content="G.Grigorian" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="/style/style_shop.css" rel="stylesheet"  type="text/css" />

<link href="/style/suckerfish.css" rel="stylesheet"  type="text/css" />
<link href="/style/template_css.css" rel="stylesheet"  type="text/css" />
<link href="/style/style1.css" rel="stylesheet"  type="text/css" />
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/highslide-with-html.packed.js"></script>
<link rel="stylesheet" type="text/css" media="screen, projection" href="/style/gcart.css" />

<link rel="stylesheet" type="text/css" href="/style/colorbox.css" />
<link href="/style/suckerfish.css" rel="stylesheet"  type="text/css" />
<link href="/style/template_css.css" rel="stylesheet"  type="text/css" />
<link href="/style/style1.css" rel="stylesheet"  type="text/css" />
  <script type="text/javascript" src="/js/simple.carousel.0.1.min_.js"></script>
  <script type="text/javascript" src="/js/uc_cart.js"></script>
  <script src="/js/jquery.validate.js" type="text/javascript"></script>
   <script src="/js/validate_cart.js" type="text/javascript"></script>
  
  

<!--[if IE 6]>
<style type="text/css">
img { behavior: url(/style/iepngfix.htc); }


div#flashwrap {
background: none;
filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="/img/icon_shop.png", sizingMethod="crop");}

</style>
<link href="/style/ie6_css.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if IE 7]>
<link href="/style/ie7_css.css" rel="stylesheet" type="text/css" />
<![endif]-->

<script language="javascript" type="text/javascript">


hs.graphicsDir = '/js/highslide/graphics/';
hs.showCredits = false;
hs.loadingText = 'Загрузка...';
hs.loadingTitle = 'Нажмите для отмены';
hs.restoreTitle = 'Удерживая левую кнопку мыши, можно перетаскивать изображение.';
hs.outlineWhileAnimating = true;
hs.outlineType = 'drop-shadow';
hs.marginLeft = 0;
hs.marginRight = 0;
hs.marginTop = 0;
hs.marginBottom = 0;
hs.minWidth = 500;
hs.minHeight = 480;
hs.maxHeight = 680;





</script>
<script type="text/javascript" src="/js/jquery.colorbox.js"></script>

		<script type="text/javascript">
			$(document).ready(function(){
				//Examples of how to assign the ColorBox event to elements
			$("a[rel='example4']").colorbox({slideshow:false});

				$("#click").click(function(){
					$('#click').css({"background-color":"#f00", "color":"#fff", "cursor":"inherit"}).text("Open this window again and this message will still be here.");
					return false;
				});
			});
		</script>


<script language="javascript" type="text/javascript">

function copyAddress() {
  

  // Hide the target information fields.
  $('#copyAdres').slideToggle("slow");
  

}


function addToCart(prod_id,token) {
	// we want to store the values from the form input box, then send via ajax below
	var qty     = $('#qty_'+prod_id).attr('value');

	//var token     = $('#token').attr('value');
	
	//var lname     = $('#lname').attr('value');
		$.ajax({
			type: "POST",
			url: "/shop/add_to_cart/",
			data: "prod_id="+ prod_id +"&token="+ token+"&qty="+ qty,
		
			success: function(data){
		
		
		
		
			    if(data==='OK'){
				
				var new_cart=$.post();


$.post("/shop/show_top_cart/", function(new_cart){
  $("#korzinka").html(new_cart);

 });



	$("#internal_add-to-cart_"+prod_id).fadeOut("slow");
	$("#add-to-cart_"+prod_id).html("Danke :) Die Ware ist im Warenkorb :) <img src='/img/basket_remove' src='12'>");

//$("#korzinka").fadeOut("slow");

				
				
				
				
					
				}
				else{
					
					
					alert(data);
				}


			}
		});




	}



	

</script>

<style type="text/css">
	pre { text-align: left; }
</style>



</head>

<body id="bg">
<div id="pagewrap">
        <div id="topwrap">
                <a href="./" id="logo"></a>
                                <div id="flashwrap">
                        <div>
                <a class="mainlevel" href="/shop/show_cart/">
                Show Cart</a>
			
            <div id="korzinka"><br /><?=$this->shop_model->show_top_basket();?></div>    

</div>
                </div>
                                <!--topwrap-->
        </div>
        <div id="menu">
                <div id="horiz-menu">
                        <script type="text/javascript">
<!--//--><![CDATA[//><!--

sfHover = function() {
	var sfEls = document.getElementById("horiznav").getElementsByTagName("LI");
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfHover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfHover\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", sfHover);

//--><!]]>
</script>


<?=$this->load->view('main_menu');?>

         </div>
        </div>
        <div id="contentwrap">
                <div id="l_w-1">
                                                <!--leftblock-->
                        <div id="conwrap">
                                                                <div id="r_w1-2">
                                                                                                                        <div id="pathway">

                                                                                       </div>
                                        <div id="mainbody">
                                                			



























<script>

function deleteBasketItem(row_id){

	$.ajax({
		type: "POST",
		url: "/shop/remove_from_cart_rowid/"+row_id,
		
		success: function(data){
	
	if(data==='OK'){

		$("#"+row_id).fadeOut("slow");

		$.get('/shop/show_total.html', function(total) {
			  $('#total').html(total);
			  
			});


		}
	else{
	
		alert(data);    


		}
	}
	});


	




	
}





</script>
			<div id="wrapper">
				<h2>Warenkorb</h2>

				<div id="sidebar">
				</div>


<div id="content">
					<!-- BEGIN JCART -->
<div id='gcart'>
	

<div id='gcart'>
	
<?php if(!$this->session->userdata('zexch')){?>


<form action="/shop/gutschein.html" method="post">
 Gutschein Einlösen: <?=form_input('gutschein','',array('size'=>'20'));?>
 <input type="submit" value="Aktualisieren">
</form>
<?php 
$zexch_sum=0;
$gutschein_text=0;
}
else{
	
// setting the values of promo action if exists	
$zexch=$this->session->userdata('zexch');
$zexch_type=$this->session->userdata('zexch_type');

//0 tokosna 1 gumarajin
if($zexch_type==0){
	
$zexch_sum=($this->cart->total()/100)*$zexch;

$gutschein_text=$zexch.'% von gesamtes Wert , gutschein';
}
elseif($zexch_type==1){
	$zexch_sum=$zexch;
	$gutschein_text=$zexch.'% von gesamtes Wert , gutschein';
	
}	





}

?>
<?php 
$cart_content=$this->cart->contents();
//print_r($cart_content);

?>

<table cellpadding="6" cellspacing="1" style="width:100%" border="0">

<tr>
  <th>QTY</th>
  <th>Item Description</th>
  <th style="text-align:right">Item Price</th>
  <th style="text-align:right">Sub-Total</th>
 
</tr>

<?php 
$i = 1; ?>

<?php foreach($cart_content as $items): ?>

	<?php echo form_hidden($i.'[rowid]', $items['rowid']); ?>
	
	

	<tr id='<?=$items['rowid']?>'>
	  <td><?php echo $items['qty']; ?></td>
	  <td>
		<?php echo $this->shop_model->get_product_name($items['id']); ?>
					
			<?php if ($this->cart->has_options($items['rowid']) == TRUE): ?>
					
				<p>
					<?php foreach ($this->cart->product_options($items['rowid']) as $option_name => $option_value): ?>
						
						<strong><?php echo $option_name; ?>:</strong> <?php echo $option_value; ?><br />
										
					<?php endforeach; ?>
				</p>
				
			<?php endif; ?>
				
	  </td>
	  <td style="text-align:right"><?php echo $this->cart->format_number($items['price']); ?></td>
	  <td style="text-align:right">€<?php echo $this->cart->format_number($items['subtotal']); ?></td>
	
	</tr>

<?php $i++; ?>

<?php endforeach; ?>

<tr>
  <td colspan="2"> </td>
  <td class="right" style="text-align:right"><strong>Total zzgl versand</strong></td>
  <td class="right" style="text-align:right"><span id="total"><?php echo $this->cart->format_number($this->cart->total()); ?><span> €</td>




</tr>

<?if($zexch_sum>0){?>
<tr>
  <td colspan="2"><?=$gutschein_text?> </td>
  <td class="right" style="text-align:right">Aktion</td>
  <td class="right" style="text-align:right" ><span id="gutschein_wert">-<?=$this->cart->format_number($zexch_sum)?></span> €</td>
</tr>

<?php } ?>


<tr>
  <td colspan="2"> </td>
  <td class="right" style="text-align:right"><strong>Versandkosten</strong></td>
  <td class="right" style="text-align:right"> €0.00</td>
</tr>
<tr>
  <td colspan="2"> </td>
  <td class="right" style="text-align:right"><strong>Endsumme zzgl versand</strong></td>
  <td class="right" style="text-align:right"><span id="gutschein_wert"><?echo $this->cart->format_number($this->cart->total()-$zexch_sum);?></span> €</td>
</tr>
<tr>
  <td colspan="2"> </td>
  <td class="right" style="text-align:right"><strong>Total</strong></td>
  <td class="right" style="text-align:right"><span id="gutschein_wert"><?echo $this->cart->format_number($this->cart->total()-$zexch_sum);?></span> €</td>
</tr>






</table><br>

<p>
<?

if(($this->cart->total()-$zexch_sum)>=100){
	
	


?>
 

<?php }
else{
	
	
	
	echo '<font color="red"><h2>Der Mindestbestellwert ist €100,00.</h2></font>';
	echo anchor('/shop/show_cart/', 'Zu Warenkorb'); 
}





?>

 <form action="/shop/post_order/"  method="post" id="validator" autocomplete="off">
<div><div id="checkout-instructions"><p>Derzeit ist das Zustellservice für Wien und Umgebung aktiviert.<br />
Zahlungsmethode ist 'Sonstige' = Barzahlung bei Übernahme der Lieferung</p>
</div>


<fieldset id="customer-pane" class=" collapsible"><h2>Kunden Informationen</h2>
<div class="description">Informationen zur Bestellung werden an die unten aufgeführte eMail-Adresse gesandt.</div><input type="hidden" name="panes[customer][primary_email]" id="edit-panes-customer-primary-email" value="admin@buratino-shop.at"  />
</fieldset>
<fieldset id="delivery-pane" class=" collapsible"><legend>Lieferadresse</legend><div class="description">Lieferadresse und -informationen bitte hier angeben.</div><div class="address-pane-table"><table><tr>
<td class="label"><span class="form-required">*</span>Vorname:</td>
<td class="field">
 <input type="text" maxlength="32" name="delivery_first_name" id="delivery_first_name" size="32" value="<?=$this->input->post('delivery_first_name',true);?>" readonly="true"/>

</td>
<td class="status"></td>

</tr>














<tr><td class="label"><span class="form-required">*</span>Nachname:</td>

<td class="field">
 <input type="text" maxlength="32" name="delivery_last_name" id="delivery-delivery-last-name" size="32" value="<?=$this->input->post('delivery_last_name',true);?>" readonly="true"/>

</td>
<td class="status"></td>

</tr>
<tr>
<td class="field-label">Firma:</td>
<td>
 <input type="text" maxlength="64" name="delivery_company" id="delivery-delivery-company" size="32" value="<?=$this->input->post('delivery_company',true);?>" readonly="true"/>


</td>
<td class="status"></td>





</tr><tr><td class="field-label"><span class="form-required">*</span>Adresszeile 1:</td><td>
 <input type="text" maxlength="64" name="delivery_street1" id="delivery-delivery-street1" size="32" value="<?=$this->input->post('delivery_street1',true);?>" readonly="true"/>
</td>
<td class="status"></td>

</tr>
<tr><td class="field-label"> </td><td>

 <input type="text" maxlength="64" name="delivery_street2" id="delivery-delivery-street2" size="32" value="<?=$this->input->post('delivery_street2',true);?>" readonly="true"/>

</td><td class="status"></td>
</tr>


<tr>


<td class="field-label"><span class="form-required">*</span>Land:</td>
<td>
 

 
   
      <?php
$this->db->where('id','197');
$this->db->or_where('id','200');
      $cs=$this->db->get('country');

     foreach($cs->result() as $country){

$countr[$country->id]=$country->country;

     }
      ?>
  <br>
   <?php 
 


echo form_dropdown('dci', $countr, $this->input->post('delivery_country_id'),'disabled="disabled"');
 ?>
   
<input type="hidden" value="<?=$this->input->post('delivery_country_id');?>" name="delivery_country_id" >
</td>
<td class="status"></td>

</tr>
<tr><td class="field-label"><span class="form-required">*</span>Bundesland:</td><td>

<?
$rq= $this->db->get_where('region',array('id'=>$this->input->post('delivery_region_id')));

$region=$rq->row();

?>
 <select name="delivery_region_id2" id="delivery_region_id" disabled="disabled">
        <option value="<?=$region->id?>"><?=$region->region;?></option>
    </select><br>
    
     
    <input name="delivery_region_id" type="hidden" value="<?=$this->input->post('delivery_region_id');?>">
   
</td>
<td class="status"></td>


</tr>


<tr><td class="field-label"><span class="form-required">*</span>Ort:</td><td>
   
<input type="text" value="<?=$this->input->post('delivery_city_id');?>" readonly="true">
    
<input name="delivery_city_id" type="hidden" value="<?=$this->input->post('delivery_city_id');?>">
</td>
<td class="status"></td>


</tr><tr><td class="field-label"><span class="form-required">*</span>Postleitzahl:</td><td>
 <input type="text" maxlength="10" name="delivery_postal_code" id="delivery-delivery-postal-code" size="10" value="<?=$this->input->post('delivery_postal_code');?>"  readonly="true" />

</td><td class="status"></td>

</tr><tr>
<td class="field-label">Telefonnummer:</td><td>

 <input type="text" maxlength="32" name="delivery_phone" id="delivery-delivery-phone" size="16" value="<?=$this->input->post('delivery_phone');?>" readonly="true">

</td>
<td class="status"></td>



</tr></table></div></fieldset>
<fieldset id="my_billing_fieldset" class="collapsible">

<h2>Rechnungsadresse</h2>


<?if($this->input->post('copy_address')){?>


 <label class="option" for="billing-copy-address">
 <input type="hidden" name="copy_address"  value="1"> Rechnungs- und Versandadresse sind identisch.</label>
<?}
else{
	


?>

<div id="copyAdres">
<table><tr><td class="field-label">
<span class="form-required">*</span>Vorname:</td>
<td>
 
<input type="text" maxlength="32" name="billing_first_name"  size="32" value="<?=$this->input->post('billing_first_name',true);?>" readonly="true"/>

</td></tr><tr><td class="field-label"><span class="form-required">*</span>Nachname:</td>
<td>

 <input type="text" maxlength="32" name="billing_last_name" id="billing-billing-last-name" size="32" value="<?=$this->input->post('billing_last_name',true);?>" readonly="true"/>

</td></tr><tr><td class="field-label">Firma:</td><td>

 <input type="text" maxlength="64" name="billing_company" id="billing-billing-company" size="32" value="<?=$this->input->post('billing_company',true);?>" readonly="true"/>

</td></tr><tr><td class="field-label"><span class="form-required">*</span>Adresszeile 1:</td><td>
 <input type="text" maxlength="64" name="billing_street1" id="billing-billing-street1" size="32" value="<?=$this->input->post('billing_street1',true);?>" readonly="true"/>

</td></tr><tr><td class="field-label"> </td><td>
 <input type="text" maxlength="64" name="billing_street2" id="billing-billing-street2" size="32" value="<?=$this->input->post('billing_street2',true);?>" readonly="true"/>


</td></tr>






<tr>


<td class="field-label"><span class="form-required">*</span>Land:</td>
<td>
 

 
   
      <?php
$this->db->where('id','197');
$this->db->or_where('id','200');
      $cs=$this->db->get('country');

     foreach($cs->result() as $country){

$countr[$country->id]=$country->country;

     }
      ?>
  <br>
   <?php 
 


echo form_dropdown('bdci', $countr, $this->input->post('billing_country_id'),'disabled="disabled"');
 ?>
   
<input type="hidden" value="<?=$this->input->post('billing_country_id');?>" name="billing_country_id" >
</td>
<td class="status"></td>

</tr>
<tr><td class="field-label"><span class="form-required">*</span>Bundesland:</td><td>

<?
$rq= $this->db->get_where('region',array('id'=>$this->input->post('billing_region_id')));

$region=$rq->row();

?>
 <select name="billing_region_id2" id="billing_region_id" disabled="disabled">
        <option value="<?=$region->id?>"><?=$region->region;?></option>
    </select><br>
    
    <input name="billing_region_id" type="hidden" value="<?=$this->input->post('billing_region_id');?>">
    
</td>
<td class="status"></td>


</tr>


<tr><td class="field-label"><span class="form-required">*</span>Ort:</td><td>
   
   

    
<input  type="text" value="<?=$this->input->post('billing_city_id');?>" readonly="true" name="asasas">

    
<input name="billing_city_id" type="hidden" value="<?=$this->input->post('billing_city_id');?>">
</td>
<td class="status"></td>


</tr>



<tr><td class="field-label"><span class="form-required">*</span>Postleitzahl:</td><td>

 <input type="text" maxlength="10" name="billing_postal_code" id="billing-billing-postal-code" size="10" value="<?=$this->input->post('billing_postal_code',true);?>" readonly="true"/>

</td></tr><tr><td class="field-label">Telefonnummer:</td><td>
 <input type="text" maxlength="32" name="billing_phone" id="billing-billing-phone" size="16" value="<?=$this->input->post('billing_phone',true);?>" readonly="true"/>

</td></tr></table>
</fieldset>
</div>
<?php }?>
<fieldset id="payment-pane" class=" collapsible"><legend><h2>Zahlungsmethode</h2></legend>
<div class="form-item">
 <label>Zahlungsmethode: <span class="form-required" title="Dieses Feld wird benötigt.">*</span></label>
 <div class="form-radios">
<?

$method=array('','per Vorauskasse','per Nachname');

?>
 <label class="option" for="payment-payment-method-other"><input type="radio" id="payment-payment-method-other" name="payment_method" value="<?=$this->input->post('zahlung_method');?>"  checked="checked"   disabled="disabled" class="form-radio" /> <?echo $method[$this->input->post('zahlung_method')]?></label>

</div>
</div>
<div id="payment_details" class="solid-border display-none"></div></fieldset>
<fieldset id="comments-pane" class=" collapsible"><legend>Bestellkommentar</legend><div class="description">Nutzen Sie dieses Feld für spezielle Wünsche oder Fragen zu Ihrer Bestellung</div><div class="form-item" id="comments-comments-wrapper">
 <label for="comments-comments">Bestellkommentar: </label>
 <textarea cols="60" rows="5" name="comments" id="comments-comments"  ><?=$this->input->post('comments');?></textarea>

</div>
</fieldset>
<div id="checkout-form-bottom">
<input type="submit" name="op" id="edit-cancel" value="Bestellen!"  class="form-submit" />


</div>
</div></form>














				</div>
			</div>



		</div>

		<!-- end content <> start footer -->



















		                                        </div>
                                                                                                                        <!--prawa zagniezdzona -->
                                </div>

                                <!--conwrap -->
                        </div>
                </div>
                                <div id="r_w">
                        		<div class="module-default icon5">
			<div>
				<div>
					<div>


Warenkorb Data 


Komt Bald 



					</div>

				</div>
			</div>
		</div>
		                </div>
                                <!--contentwrap-->
                        </div>
         <!--pagewrap-->
        <div id="footer">     
    
<div id="upinfo">Shop-Hotline: 0676 510 29</div> 

    <div id="stylearea">    
       <a href="#bg" id="top"></a>

       <a href="#" class="style_switcher" id="st_icon-0"></a> 
       <a href="#" class="style_switcher" id="st_icon-1"></a> 
       <a href="#" class="style_switcher" id="st_icon-2"></a>      
    </div>
        
<!-- Footer Hyperlinks -->
<div id="links">
	
<a title="agb" href="/node/3">AGB</a> | <a title="Impressum" href="/node/4">Impressum &amp; Haftungsausschluss</a> | <a title="kontakt" href="/content/anfrageformular">Konakt</a>


  </div><!-- /block-inner -->

	
	</div>

<!-- Copyright Information -->
<div id="copyright">Development and Support &copy; <a href="http://www.gevork.ru" target="_blank">G.Grigorian</a>. All rights reserved.</div>
     </div>
</div>
</body>
</html>
