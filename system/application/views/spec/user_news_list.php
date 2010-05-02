<?
if($this->dx_auth->is_logged_in()){
echo "<h1><font color=\"#F47305\"> Kabinet</font> </h1>";
echo "<h2>Warenkorb Data</h2>";
	
	

			
       echo'     <div>'.$this->shop_model->show_top_basket().'</div>';    
	
	if($this->cart->total()>0){
		echo'<a  href="/shop/show_cart/">
               Zur Warenkorb</a><br>';
	}

		$this->db->where('user_id',$this->session->userdata('user_id'));
		$orders=$this->db->get('orders');
echo "<h2>Bestellungen(".($orders->num_rows()).")</h2>";

	?>
	
	<table  id="gradient-style" >
    <thead>
    	<tr>
        	<th scope="col">BestellungID</th>
            <th scope="col">Datum</th>
            <th scope="col">Total</th>
            <th scope="col">Status</th>
             <th scope="col">Verwaltung</th>
        </tr>
    </thead>
    <tfoot>
    	<tr>
        	<td colspan="4"><img src="/img/view.png" width="16" height="16" > Bestellung zu sehen<br>
        	
        	<img src="/img/reload.png" width="16" height="16" >== Eine Gleiche Bestellung erneut erstellen oder abgebrochene Bestellung  aktivieren.
        	
        	<br><br>
        	<img src="/img/cancel.png" width="16" height="16" > == Wenn Bestellung hat noch status "New" , koenen sie  es mit dieser knopf abbrechen.
        	
        	
        	
        	
        	</td>
        </tr>
    </tfoot>
    <tbody>
    	<?
    	
    	foreach($orders->result() as $order){
    		$cancel="";
    		$reload="";
    		
    		if($order->status=='New'){
    			
    			$cancel=' | <a href="/shop/cancel_order/'.$order->id.'/" rel="example7" title="Bestellung Loeschen"><img src="/img/cancel.png" width="16" height="16"></a>';
    		}
    		else{
    			
    			$reload='| <a href="/shop/repeat_order/'.$order->id.'/" rel="example7" title="Bestellung Wiederholen "><img src="/img/reload.png" width="16" height="16" ></a>';
    		}
    		
    		
    		
    	echo '	
    	<tr>
        	<td><strong><a href="/shop/view_order/'.$order->id.'/" rel="example7" title="Bestellung Anzeigen">'.$order->id.'</a></strong></td>
            <td>'.$order->date.'</td>
            <td>'.$order->subtotal.'</td>
            <td>'.$order->status.'</td>
            <td><a href="/shop/view_order/'.$order->id.'/" rel="example7" title="Bestellung Anzeigen"><img src="/img/view.png" width="16" height="16"></a> '.$reload.$cancel.' </td>
        </tr>';	
    		
    		
    	}
    	
    	?>
    	
    	
    
    </tbody>
</table>
	<?php 
		
}
else{
	
  redirect('/auth/login','refresh');
	
}
	
		
		?>