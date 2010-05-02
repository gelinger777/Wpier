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
					<!-- BEGIN GewaCART -->
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
	$gutschein_text=$zexch.'EUR von gesamtes Wert , gutschein';
	
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
 $i= 1; ?>

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
<tr>
  <td colspan="2"><?=$gutschein_text?> </td>
  <td class="right" style="text-align:right">Aktion</td>
  <td class="right" style="text-align:right" ><span id="gutschein_wert">-<?=$this->cart->format_number($zexch_sum)?></span> €</td>
</tr>
<tr>
  <td colspan="2"> </td>
  <td class="right" style="text-align:right"><strong>Endsumme zzgl versand</strong></td>
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


<?
$this->db->limit('1');
$this->db->order_by('id','DESC');
$this->db->where('user_id',$this->session->userdata('user_id'));
$last_ad=$this->db->get('orders');

if($last_ad->num_rows()>0){
	
	$ladres=$last_ad->row_array();
}

?>

<font color="red"><h1><?php echo validation_errors(); ?></h1></font>

 <form action="/shop/checkout/"  method="post" id="validator" autocomplete="off" >
<div><div id="checkout-instructions"><p>Derzeit ist das Zustellservice für Wien und Umgebung aktiviert.<br />
Zahlungsmethode ist 'Sonstige' = Barzahlung bei Übernahme der Lieferung</p>
</div><fieldset id="cart-pane"><legend>Warenkorb Inhalt</legend>

</fieldset>
<fieldset id="customer-pane" class=" collapsible"><legend>Kunden Informationen</legend><div class="description">Informationen zur Bestellung werden an die unten aufgeführte eMail-Adresse gesandt.</div><input type="hidden" name="panes[customer][primary_email]" id="edit-panes-customer-primary-email" value="admin@buratino-shop.at"  />
</fieldset>
<fieldset id="delivery-pane" class=" collapsible"><legend>Lieferadresse</legend><div class="description">Lieferadresse und -informationen bitte hier angeben.</div><div class="address-pane-table">

<table><tr>
<td class="label"><span class="form-required">*</span>Vorname:</td>
<td class="field">
 <input type="text" maxlength="32" name="delivery_first_name" id="delivery_first_name" size="32" value="<?echo @$ladres['delivery_first_name']?>"/>

</td>
<td class="status"></td>

</tr>














<tr><td class="label"><span class="form-required">*</span>Nachname:</td>

<td class="field">
 <input type="text" maxlength="32" name="delivery_last_name" id="delivery-delivery-last-name" size="32" value="<?echo @$ladres['delivery_last_name']?>" />

</td>
<td class="status"></td>

</tr>
<tr>
<td class="field-label">Firma:</td>
<td>
 <input type="text" maxlength="64" name="delivery_company" id="delivery-delivery-company" size="32" value="<?echo @$ladres['delivery_company']?>" />


</td>
<td class="status"></td>





</tr><tr><td class="field-label"><span class="form-required">*</span>Adresszeile 1:</td><td>
 <input type="text" maxlength="64" name="delivery_street1" id="delivery-delivery-street1" size="32" value="<?echo @$ladres['delivery_street1']?>"  />

</td>
<td class="status"></td>

</tr>
<tr><td class="field-label"> </td><td>
<div class="form-item" id="delivery-delivery-street2-wrapper">
 <input type="text" maxlength="64" name="delivery_street2" id="delivery-delivery-street2" size="32" value="<?echo @$ladres['delivery_street2']?>" />
</div>
</td><td class="status"></td>
</tr><tr>


<td class="field-label"><span class="form-required">*</span>Land:</td>
<td>
 
  
     
      <?php
$this->db->where('id','197');
$this->db->or_where('id','200');
      $cs=$this->db->get('country');
$strana['0']="Land";
     foreach($cs->result() as $country){


     $strana[$country->id]=$country->country;


     }
     
     
echo form_dropdown('delivery_country_id', $strana, @$ladres['delivery_country_id'],'id="delivery_country_id"');
     
      ?>
   
  
   

</td>
<td class="status"></td>

</tr>
<tr><td class="field-label"><span class="form-required">*</span>Bundesland:</td><td>
 
 
 <?
 $rr['0']="Bundesland";
 if(intval(@$ladres['delivery_country_id'])>0){
 	$this->db->where('country_id',$ladres['delivery_country_id']);
 	$regions=$this->db->get('region');
 	
 	
 	foreach($regions->result() as $regioncho){
 		
 		$rr[$regioncho->id]=$regioncho->region;
 	}
 }
 

 echo form_dropdown('delivery_region_id', $rr, @$ladres['delivery_region_id'],'id="delivery_region_id"');
 ?>
 
</td>
<td class="status"></td>


</tr>


<tr><td class="field-label"><span class="form-required">*</span>Ort:</td><td>
<?php 

 echo form_input('delivery_city_id',  @$ladres['delivery_city_id'],'id="delivery_city_id"');
 ?>
    

</td>
<td class="status"></td>


</tr><tr><td class="field-label"><span class="form-required">*</span>Postleitzahl:</td><td>
 <input type="text" maxlength="10" name="delivery_postal_code" id="delivery-delivery-postal-code" size="10" value="<?=@$ladres['delivery_postal_code'];?>"  />

</td><td class="status"></td>

</tr><tr>
<td class="field-label">Telefonnummer:</td><td>

 <input type="text" maxlength="32" name="delivery_phone" id="delivery-delivery-phone" size="16" value="<?=@$ladres['delivery_phone'];?>" class="form-text" />

</td>
<td class="status"></td>



</tr></table></div></fieldset>
<fieldset id="my_billing_fieldset" class="collapsible">

<legend>Rechnungsadresse</legend>
<div class="description">Rechnungsadresse und -informationen bitte hier angeben.</div>

 <label class="option" for="billing-copy-address">
 <input type="checkbox" name="copy_address" id="billing-copy-address" value="1"   onclick="javascript:copyAddress();" class="form-checkbox" /> Rechnungs- und Versandadresse sind identisch.</label>

</div>
<div id="copyAdres">
<table><tr><td class="field-label"></td><td>
</td></tr><tr><td class="field-label">
<span class="form-required">*</span>Vorname:</td>
<td>
 
<input type="text" maxlength="32" name="billing_first_name" id="billing-billing-first-name" size="32" value=""  />

</td></tr><tr><td class="field-label"><span class="form-required">*</span>Nachname:</td>
<td>

 <input type="text" maxlength="32" name="billing_last_name" id="billing-billing-last-name" size="32" value="" />

</td></tr><tr><td class="field-label">Firma:</td><td>

 <input type="text" maxlength="64" name="billing_company" id="billing-billing-company" size="32" value="" />

</td></tr><tr><td class="field-label"><span class="form-required">*</span>Adresszeile 1:</td><td>
 <input type="text" maxlength="64" name="billing_street1" id="billing-billing-street1" size="32" value=""  />

</td></tr><tr><td class="field-label"> </td><td>
 <input type="text" maxlength="64" name="billing_street2" id="billing-billing-street2" size="32" value="" />


</td></tr>

<td class="field-label"><span class="form-required">*</span>Land:</td>
<td>
 
    <select name="billing_country_id" id="billing_country_id">
        <option value="0">- Land   -</option>
      <?php
$this->db->where('id','197');
$this->db->or_where('id','200');
      $cs=$this->db->get('country');

     foreach($cs->result() as $country){


     echo ' <option value="'.$country->id.'">'.$country->country.'</option>';


     }
      ?>
    </select><br>
  
   

</td>
<td class="status"></td>

</tr>
<tr><td class="field-label"><span class="form-required">*</span>Bundesland:</td><td>
 <select name="billing_region_id" id="billing_region_id" disabled="disabled">
        <option value="0">--Bundesland -- </option>
    </select><br>
</td>
<td class="status"></td>


</tr>


<tr><td class="field-label"><span class="form-required">*</span>Ort:</td><td>
   
   
   <?echo form_input('billing_city_id',@$lorder['billing_city_id']);?>
   
   
   <br>
    

</td>
<td class="status"></td>


</tr><tr><td class="field-label"><span class="form-required">*</span>Postleitzahl:</td><td>

 <input type="text" maxlength="10" name="billing_postal_code" id="billing-billing-postal-code" size="10" value="" />

</td></tr><tr><td class="field-label">Telefonnummer:</td><td>
 <input type="text" maxlength="32" name="billing_phone" id="billing-billing-phone" size="16" value=""  />

</td></tr></table>
</fieldset>
</div>

<fieldset id="payment-pane" class=" collapsible"><legend>Zahlungsmethode</legend><div style="padding: .5em 1em; margin-bottom: 1em; border: dashed 1px #bbb;" id="line-items-div">


</div><input type="hidden" name="current_total" id="payment-current-total" value=""  />
<div class="form-item">
 <label>Zahlungsmethode: <span class="form-required" title="Dieses Feld wird benötigt.">*</span></label>
 <div class="form-radios">
<select name="zahlung_method">

<option value="1">per Vorauskasse</option>
<option value="2">per Nachname</option>
</select>

</div>
</div>
<div id="payment_details" class="solid-border display-none"></div></fieldset>
<fieldset id="comments-pane" class=" collapsible"><legend>Bestellkommentar</legend><div class="description">Nutzen Sie dieses Feld für spezielle Wünsche oder Fragen zu Ihrer Bestellung</div><div class="form-item" id="comments-comments-wrapper">
 <label for="comments-comments">Bestellkommentar: </label>
 <textarea cols="60" rows="5" name="comments" id="comments-comments"  class="form-textarea resizable"><?=$this->input->post('comments');?></textarea>

</div>
</fieldset>
<div id="checkout-form-bottom">
<input type="submit" name="op" id="edit-cancel" value="Abbrechen"  class="form-submit" />
<input type="submit" name="op" id="edit-continue" value="Überprüfung der Bestellung"  class="form-submit" />
<input type="hidden" name="form_id" id="edit-uc-cart-checkout-form" value="uc_cart_checkout_form"  />
</div>
</div></form>




<script type="text/javascript">



/*
* При полной загрузке документа
* мы начинаем определять события
*/
$(document).ready(function () {
    /*
     * На выборе селекта страны — вешаем событие,
     * функция будет брать значение этого селекта
     * и с помощью ajax запроса получать список
     * регионов для вставки в следующий селект

     
     */




     
    $('#delivery_country_id').change(function () {
        /*
         * В переменную country_id положим значение селекта
         * (выбранная страна)
         */
        var country_id = $(this).val();
        /*
         * Если значение селекта равно 0,
         * т.е. не выбрана страна, то мы
         * не будем ничего делать
         */
        if (country_id == '0') {
            $('#delivery_region_id').html('');
            $('#delivery_region_id').attr('disabled', true);
            return(false);
        }
        /*
         * Очищаем второй селект с регионами
         * и блокируем его через атрибут disabled
         * туда мы будем класть результат запроса
         */
        $('#delivery_region_id').attr('disabled', true);
        $('#delivery_region_id').html('<option>loading...</option>');
        /*
         * url запроса регионов
         */
        var url = '/places/get_regions/'+ country_id+'/';

        /*
         * GET'овый AJAX запрос
         * подробнее о синтаксисе читайте
         * на сайте http://docs.jquery.com/Ajax/jQuery.get
         * Данные будем кодировать с помощью JSON
         */
        $.post(
            url,
            "country_id=" + country_id,
            function (result) {
                /*
                 * В случае неудачи мы получим результат с type равным error.
                 * Если все прошло успешно, то в type будет success,
                 * а также массив regions, содержащий данные по регионам
                 * в формате 'id'=>'1', 'title'=>'название региона'.
                 */
                if (result.type == 'error') {
                    /*
                     * ошибка в запросе
                     */
                    alert('Error Accured During Parsing Geographical Data. Sorry for Inconviniense');
                    return(false);
                }
                else {
                    /*
                     * проходимся по пришедшему от бэк-энда массиву циклом
                     */
                    var options = '';
                    $(result.regions).each(function() {
                        /*
                         * и добавляем в селект по региону
                         */
                        options += '<option value="' + $(this).attr('id') + '">' + $(this).attr('title') + '</option>';
                    });
                    $('#delivery_region_id').html(options);
                    $('#delivery_region_id').attr('disabled', false);
                }
            },
            "json"
        );
    });









    $('#delivery_region_id').change(function () {
        /*
         * В переменную country_id положим значение селекта
         * (выбранная страна)
         */
        var region_id = $(this).val();
        /*
         * Если значение селекта равно 0,
         * т.е. не выбрана страна, то мы
         * не будем ничего делать
         */
        if (region_id == '0') {
            $('#delivery_city_id').html('');
            $('#delivery_city_id').attr('disabled', true);
            return(false);
        }
        /*
         * Очищаем второй селект с регионами
         * и блокируем его через атрибут disabled
         * туда мы будем класть результат запроса
         */
        $('#delivery_city_id').attr('disabled', true);
        $('#delivery_city_id').html('<option>loading...</option>');
        /*
         * url запроса регионов
         */
        var url = '/places/get_city/'+ region_id+'/';

        /*
         * GET'овый AJAX запрос
         * подробнее о синтаксисе читайте
         * на сайте http://docs.jquery.com/Ajax/jQuery.get
         * Данные будем кодировать с помощью JSON
         */
        $.post(
            url,
            "region_id=" + region_id,
            function (result) {
                /*
                 * В случае неудачи мы получим результат с type равным error.
                 * Если все прошло успешно, то в type будет success,
                 * а также массив regions, содержащий данные по регионам
                 * в формате 'id'=>'1', 'title'=>'название региона'.
                 */
                if (result.type == 'error') {
                    /*
                     * ошибка в запросе
                     */
                    alert('Error Accured During Parsing Geographical Data. Sorry for Inconviniense');
                    return(false);
                }
                else {
                    /*
                     * проходимся по пришедшему от бэк-энда массиву циклом
                     */
                    var options = '';
                    $(result.regions).each(function() {
                        /*
                         * и добавляем в селект по региону
                         */
                        options += '<option value="' + $(this).attr('id') + '">' + $(this).attr('title') + '</option>';
                    });
                    $('#delivery_city_id').html(options);
                    $('#delivery_city_id').attr('disabled', false);
                }
            },
            "json"
        );
    });


















    
    $('#billing_country_id').change(function () {
        /*
         * В переменную country_id положим значение селекта
         * (выбранная страна)
         */
        var country_id = $(this).val();
        /*
         * Если значение селекта равно 0,
         * т.е. не выбрана страна, то мы
         * не будем ничего делать
         */
        if (country_id == '0') {
            $('#billing_region_id').html('');
            $('#billing_region_id').attr('disabled', true);
            return(false);
        }
        /*
         * Очищаем второй селект с регионами
         * и блокируем его через атрибут disabled
         * туда мы будем класть результат запроса
         */
        $('#billing_region_id').attr('disabled', true);
        $('#billing_region_id').html('<option>loading...</option>');
        /*
         * url запроса регионов
         */
        var url = '/places/get_regions/'+ country_id+'/';

        /*
         * GET'овый AJAX запрос
         * подробнее о синтаксисе читайте
         * на сайте http://docs.jquery.com/Ajax/jQuery.get
         * Данные будем кодировать с помощью JSON
         */
        $.post(
            url,
            "country_id=" + country_id,
            function (result) {
                /*
                 * В случае неудачи мы получим результат с type равным error.
                 * Если все прошло успешно, то в type будет success,
                 * а также массив regions, содержащий данные по регионам
                 * в формате 'id'=>'1', 'title'=>'название региона'.
                 */
                if (result.type == 'error') {
                    /*
                     * ошибка в запросе
                     */
                    alert('Error Accured During Parsing Geographical Data. Sorry for Inconviniense');
                    return(false);
                }
                else {
                    /*
                     * проходимся по пришедшему от бэк-энда массиву циклом
                     */
                    var options = '';
                    $(result.regions).each(function() {
                        /*
                         * и добавляем в селект по региону
                         */
                        options += '<option value="' + $(this).attr('id') + '">' + $(this).attr('title') + '</option>';
                    });
                    $('#billing_region_id').html(options);
                    $('#billing_region_id').attr('disabled', false);
                }
            },
            "json"
        );
    });









    $('#billing_region_id').change(function () {
        /*
         * В переменную country_id положим значение селекта
         * (выбранная страна)
         */
        var region_id = $(this).val();
        /*
         * Если значение селекта равно 0,
         * т.е. не выбрана страна, то мы
         * не будем ничего делать
         */
        if (region_id == '0') {
            $('#billing_city_id').html('');
            $('#billing_city_id').attr('disabled', true);
            return(false);
        }
        /*
         * Очищаем второй селект с регионами
         * и блокируем его через атрибут disabled
         * туда мы будем класть результат запроса
         */
        $('#billing_city_id').attr('disabled', true);
        $('#billing_city_id').html('<option>loading...</option>');
        /*
         * url запроса регионов
         */
        var url = '/places/get_city/'+ region_id+'/';

        /*
         * GET'овый AJAX запрос
         * подробнее о синтаксисе читайте
         * на сайте http://docs.jquery.com/Ajax/jQuery.get
         * Данные будем кодировать с помощью JSON
         */
        $.post(
            url,
            "region_id=" + region_id,
            function (result) {
                /*
                 * В случае неудачи мы получим результат с type равным error.
                 * Если все прошло успешно, то в type будет success,
                 * а также массив regions, содержащий данные по регионам
                 * в формате 'id'=>'1', 'title'=>'название региона'.
                 */
                if (result.type == 'error') {
                    /*
                     * ошибка в запросе
                     */
                    alert('Error Accured During Parsing Geographical Data. Sorry for Inconviniense');
                    return(false);
                }
                else {
                    /*
                     * проходимся по пришедшему от бэк-энда массиву циклом
                     */
                    var options = '';
                    $(result.regions).each(function() {
                        /*
                         * и добавляем в селект по региону
                         */
                        options += '<option value="' + $(this).attr('id') + '">' + $(this).attr('title') + '</option>';
                    });
                    $('#billing_city_id').html(options);
                    $('#billing_city_id').attr('disabled', false);
                }
            },
            "json"
        );
    });

























    
});

</script>













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
                        		
		                </div>
                                <!--contentwrap-->
                        </div>
         <!--pagewrap-->
        <div id="footer">     
    
<div id="upinfo">Shop-Hotline: 0676 510 29</div> 

        
<!-- Footer Hyperlinks -->
<div id="links">

  </div><!-- /block-inner -->

	
	</div>

<!-- Copyright Information -->
<div id="copyright">Development and Support &copy; <a href="http://www.gevork.ru" target="_blank">G.Grigorian</a>. All rights reserved.</div>
     </div>
</div>
</body>
</html>
