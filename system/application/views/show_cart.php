<link rel="stylesheet" type="text/css" media="screen, projection" href="/style/gcart.css" />


<script>

function deleteBasketItem(row_id){

	$.ajax({
		type: "POST",
		url: "/shop/remove_from_cart_rowid/"+row_id,
		
		success: function(data){
	
	if(data==='OK'){

		location.reload();


		}
	else{
	
		alert(data);    


		}
	}
	});


	




	
}





</script>

	
<?php if(!$this->session->userdata('zexch')){?>


<form action="/shop/gutschein/" method="post">
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
	$gutschein_text=$zexch.'€ von gesamtes Wert , gutschein';
	
}	





}

?>
<?php echo'<form method="POST" action="/shop/update_cart/">'; 
$cart_content=$this->cart->contents();
//print_r($cart_content);

?>
<?php if(($this->cart->total()-$zexch_sum)<100){

	echo ($this->cart->total()-$zexch_sum);
	
	?>

<font color="red">
<h2>

 
  Bitte Beachten Sie Dass bei Buratino-Shop.at ist Mindestbestellwert €100

</h2>
</font>
<?php }?>

<table cellpadding="6" cellspacing="1" style="width:100%" border="0">

<tr>
  <th>QTY</th>
  <th>Item Description</th>
  <th style="text-align:right">Item Price</th>
  <th style="text-align:right">Sub-Total</th>
    <th style="text-align:right">Actions</th>
</tr>

<?php $i = 1; ?>

<?php foreach($cart_content as $items): ?>

	<?php echo form_hidden('cart['.$i.']'.'[rowid]', $items['rowid']); ?>
	
	
	
	</div></div>
	<tr id='<?=$items['rowid']?>'>
	  <td><?php echo form_input(array('name' => 'cart['.$i.']'.'[qty]', 'value' => $items['qty'], 'maxlength' => '3', 'size' => '5')); ?></td>
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
		  <td style="text-align:right"><a href="#" onclick="javascript:deleteBasketItem('<?=$items['rowid']?>')">Entfernen</a></td>
	</tr>

<?php $i++; ?>

<?php endforeach; ?>

<tr>
  <td colspan="2"> </td>
  <td class="right"><strong>Total zzgl versand</strong></td>
  <td class="right">€<span id="total"><?php echo $this->cart->format_number($this->cart->total()); ?><span> </td>




</tr>
<?php if($zexch_sum>0){?>
<tr>
  <td colspan="2"><?=$gutschein_text?> </td>
  <td class="right">Aktion</td>
  <td class="right">€<span id="gutschein_wert">-<?=$this->cart->format_number($zexch_sum)?></span> </td>
</tr>

<?php }?>
<tr>
  <td colspan="2"> </td>
  <td class="right"><strong>Endsumme zzgl versand</strong></td>
  <td class="right">€<span id="gutschein_wert"><?echo $this->cart->format_number($this->cart->total()-$zexch_sum);?></span></td>
</tr>


</table><br>

</br>

<p><?php echo form_submit('', 'Update your Cart'); ?>

<?php echo anchor('/shop/kassa/', 'Zur Kassa'); ?> 
 <?php echo anchor('/content/show_page/cat', 'Weiter Einkaufen'); ?> 
 
 
  </p>
				
</form>

