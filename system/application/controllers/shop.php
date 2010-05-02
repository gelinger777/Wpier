<?php

class Shop extends Controller {

	function Shop()
	{
		parent::Controller();	
		
		$this->load->model('shop_model');
		#  // Load library  
$this->load->library('DX_Auth');  
	}
	
	function index()
	{
		exit('Entschuldigung, Aber Sie haben Hier Nix zu suchen.. :))) ');
	}
	
	
function checkout(){

	
	$this->load->helper(array('form', 'url'));
		
		$this->load->library('form_validation');
	
	$this->form_validation->set_rules('delivery_first_name', 'Vorname', 'required');
 $this->form_validation->set_rules('delivery_last_name', 'Nachname', 'required');
 $this->form_validation->set_rules('delivery_street1', 'Strasse', 'required');
 $this->form_validation->set_rules('delivery_country_id', 'Land', 'required');
 $this->form_validation->set_rules('delivery_region_id', 'Bundesland', 'required');
 $this->form_validation->set_rules('delivery_city_id', 'Ort', 'required');	
if($this->input->post('copy_address')!=1){

	$this->form_validation->set_rules('billing_first_name', 'Vorname', 'required');
 $this->form_validation->set_rules('billing_last_name', 'Nachname', 'required');
 $this->form_validation->set_rules('billing_street1', 'Strasse', 'required');
 $this->form_validation->set_rules('billing_country_id', 'Land', 'required');
 $this->form_validation->set_rules('billing_region_id', 'Bundesland', 'required');
 $this->form_validation->set_rules('billing_city_id', 'Ort', 'required');

}


if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('show_kassa');
		}
		else
		{
			$this->load->view('check_out');
		}
	
	//print_r($order_data);
	
	
	///$customer=$order_data['customer'];
	
	
	
	

$this->output->enable_profiler(TRUE);


}


function post_order(){
if ($this->session->userdata('DX_logged_in')==true)  
        {  

        	
        	
        	
        	
        	
        	
        	
        	
        	
        	
        	
        	
	$this->load->helper(array('form', 'url'));
		
		$this->load->library('form_validation');
	
	$this->form_validation->set_rules('delivery_first_name', 'Vorname', 'required');
 $this->form_validation->set_rules('delivery_last_name', 'Nachname', 'required');
 $this->form_validation->set_rules('delivery_street1', 'Strasse', 'required');
 $this->form_validation->set_rules('delivery_country_id', 'Land', 'required');
 $this->form_validation->set_rules('delivery_region_id', 'Bundesland', 'required');
 $this->form_validation->set_rules('delivery_city_id', 'Ort', 'required');	
if($this->input->post('copy_address')!=1){

	$this->form_validation->set_rules('billing_first_name', 'Vorname', 'required');
 $this->form_validation->set_rules('billing_last_name', 'Nachname', 'required');
 $this->form_validation->set_rules('billing_street1', 'Strasse', 'required');
 $this->form_validation->set_rules('billing_country_id', 'Land', 'required');
 $this->form_validation->set_rules('billing_region_id', 'Bundesland', 'required');
 $this->form_validation->set_rules('billing_city_id', 'Ort', 'required');

}


if ($this->form_validation->run() == FALSE)
		{
			
			
			
			echo validation_errors(); 
		}
		else
		{
			
			
			
			
			
			$order_data['delivery_first_name']=$this->input->post('delivery_first_name');
		$order_data['delivery_last_name']=$this->input->post('delivery_last_name');
			
		$order_data['delivery_company']=$this->input->post('delivery_company');	
		$order_data['delivery_street1']=$this->input->post('delivery_street1');	
		$order_data['delivery_street2']=$this->input->post('delivery_street2');	
		$order_data['delivery_city_id']=$this->input->post('delivery_city_id');	
		$order_data['delivery_region_id']=$this->input->post('delivery_region_id');	
		$order_data['delivery_country_id']=$this->input->post('delivery_country_id');	
		$order_data['delivery_postal_code']=$this->input->post('delivery_postal_code');	
		$order_data['delivery_phone']=$this->input->post('delivery_phone');	
			
			
			
			
			
			
			
			
			
		if($this->input->post('copy_address')!=true){	
			
			
			
			$order_data['billing_first_name']=$this->input->post('billing_first_name');
		$order_data['billing_last_name']=$this->input->post('billing_last_name');
			
		$order_data['billing_company']=$this->input->post('billing_company');	
		$order_data['billing_street1']=$this->input->post('billing_street1');	
		$order_data['billing_street2']=$this->input->post('billing_street2');	
		$order_data['billing_city_id']=$this->input->post('billing_city_id');	
		$order_data['billing_region_id']=$this->input->post('billing_region_id');	
		$order_data['billing_country_id']=$this->input->post('billing_country_id');	
		$order_data['billing_postal_code']=$this->input->post('billing_postal_code');	
		$order_data['billing_phone']=$this->input->post('billing_phone');	
		
		//generating date
		$this->load->helper('date');
		
		$datestring = "%Y-%m-%d %H:%i:%s";
$time = time();
		


		$order_data['date']= mdate($datestring, $time);	
			
		}
		else{
		
				
			$order_data['billing_first_name']=$order_data['delivery_first_name'];
		$order_data['billing_last_name']=$order_data['delivery_last_name'];
			
		$order_data['billing_company']=$order_data['delivery_company'];	
		$order_data['billing_street1']=$order_data['delivery_street1'];	
		$order_data['billing_street2']=$order_data['delivery_street2'];	
		$order_data['billing_city_id']=$order_data['delivery_city_id'];	
		$order_data['billing_region_id']=$order_data['delivery_region_id'];	
		$order_data['billing_country_id']=$order_data['delivery_country_id'];	
		$order_data['billing_postal_code']=$order_data['delivery_postal_code'];	
		$order_data['billing_phone']=$order_data['delivery_phone'];
		
		
		}	
			
			
			
			
		}
		
		$order_data['user_id']=$this->session->userdata('user_id');
		//$order_data['session_id']=$this->session->userdata('username');
	
		$order_data['comments']=$this->input->post('comments');
	$order_data['uniq_id']=$this->session->userdata('session_id');
	//print_r($order_data);
	
	$cart_content=$this->cart->contents();

$i=1;
foreach($cart_content as $items){

	
	
	
	//discount stuff
if(!$this->session->userdata('zexch')){



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
	$gutschein_text=$zexch.' Euro von gesamtes Wert , gutschein';
	
}	





}
	
	
	
	
	//end discount stuff


$order_data['subtotal']=$this->cart->total();
$order_data['promocode']=$this->session->userdata('promo_code');
$order_data['discount']=$zexch_sum;
$order_data['discount_reason']=$gutschein_text;




$last_q=$this->db->get_where('orders',array('uniq_id'=>$order_data['uniq_id'],'user_id'=>$order_data['user_id']));

if($last_q->num_rows()>0){
$my_order=$last_q->row_array();
$order_id=$my_order['id'];

//echo "KA";
}
else{

	$this->db->insert('orders',$order_data);
//echo "CHKA";

$last_q=$this->db->get_where('orders',array('uniq_id'=>$order_data['uniq_id'],'user_id'=>$order_data['user_id']));



$my_order=$last_q->row_array();
$order_id=$my_order['id'];

//echo "KA";




     }

      
	
	
$item['price']=$items['price'];
$item['order_id']=$order_id;
	  $item['qty']=$items['qty']; 
	  
		$item['item_name']= $this->shop_model->get_product_name($items['id']); 
					
$this->db->insert('order_items',$item);













}
	$this->cart->destroy();
	    	
	 redirect('/auth/login','refresh');
	//print_r($cart_content);
	
	//print_r($order_data);
	///$customer=$order_data['customer'];
	
        }
        else{
        	$this->session->set_userdata('redirect_url','/shop/show_cart');
    
        	
       redirect('/auth/login','refresh');	
        }
	
$this->output->enable_profiler(TRUE);
}
function add_to_cart(){
	
	
	
$this->load->library('cart');


$prod_id=$this->input->post('prod_id');
$pquery=$this->db->get_where('products',array('id'=>$prod_id));
if($pquery->num_rows()>0 AND intval($this->input->post('qty'))>0){

$prod=$pquery->row();



$price=$prod->price;

if(!$prod->price){


$price=1;
}
$prod_qty=$this->input->post('qty');


if($prod_qty<1){

$prod_qty=1;
}

//We would take names from the  database in the cart, as we use german and russian names also
//and trigering  Cart Class is not a good idea from my point of view
$meta = array(
               'id'      => $prod_id,
               'qty'     => $prod_qty,
               'price'   => "$price",
               'name'    => 'No_name',
               'options' => array()
            );

$insert=$this->cart->insert($meta); 

if($insert){

echo "OK";
}
//print_r($meta);

}
else{

echo "ERROR";
}

}

function show_cart(){

$this->load->helper('form');
$this->load->library('cart');
$data['title']="Warenkorb";
if($this->cart->total_items()<1){
	
	$data['message']="Warenkorb ist Leer";
$this->load->view('show_messages',$data);

}
else{
$data['message']=$this->load->view('show_cart','',true);
$this->load->view('show_messages',$data);
}





}

function kassa(){
if ($this->session->userdata('DX_logged_in'))  
        {  
$this->load->helper('form');
$this->load->library('cart');
if($this->cart->total_items()<1){
	$data['title']="Warenkorb";
	$data['message']="Warenkorb ist Leer";
$this->load->view('show_messages',$data);

}
else{
$this->load->view('show_kassa');
}
        }
        else{
        	$this->session->set_userdata('redirect_url','/shop/kassa');
        	
       redirect('/auth/login','refresh');	
        }






}
function test(){

print_r($this->cart->contents());
$this->load->helper('date');
		
		$datestring = "%Y-%m-%d %H:%i:%s";
$time = time();


		echo mdate($datestring, $time);	





}


function view_order($order_id=0){

if($order_id<1){exit('wrong_order_id');}


$user_id=$this->session->userdata('user_id');

if($user_id!=true and intval('user_id')<1){
exit('Bitte Einloggen');
}


$this->db->where('id',$order_id);
$ord=$this->db->get('orders');

if($ord->num_rows()<0){

exit('Bestellung existiert nicht im Datenbank');
}


$order=$ord->row();

if($user_id!=$order->user_id){

exit('Dieser Bestellung ist nicht von Ihnen gemacht, d.h Sie haben keine zugriff.');

}


$this->db->where('order_id',$order_id);
$it=$this->db->get('order_items');
echo '<link href="/style/style1.css" rel="stylesheet"  type="text/css" />
';
echo '<table  id="gradient-style">
    <thead>
    	<tr>
        	<th scope="col">WarenID</th>
            <th scope="col">Ware</th>
            <th scope="col">Price</th>
            <th scope="col">Qty</th>
          
        </tr>
    </thead>
    <tfoot>
    	<tr>
        	<td colspan="4">Klicken Sie auf BestellungID um Bestellung zu sehen</td>
        </tr>
    </tfoot>
    <tbody>';


    	foreach($it->result() as $item){
    		
    		
    		
    		
    		
    	echo '	
    	<tr>
        	<td><strong>'.$item->id.'</strong></td>
            <td>'.$item->item_name.'</td>
            <td>'.$item->price.'</td>
            <td>'.$item->qty.'</td>
        
        </tr>';	
    		
    		
    	}
    	
   echo '
    	
    
    </tbody>
</table>';



}

function update_cart()
{

//print_r($_POST);

$cart=$this->input->post('cart');

//print_r($cart);

//echo $this->input->post('makaka');

$this->cart->update($cart);
redirect('/shop/show_cart/');


}
	


function gutschein(){


$gutschein=$this->input->post('gutschein',true);

if(strlen($gutschein)<=3){

exit('Gutschein kann nicht weniger als 4 zeichen haben'.anchor('/shop/show_cart','Zueruck zur Warenkorb'));



}


$gutschein_q=$this->db->get_where('promos',array('promo_code'=>$gutschein));

if($gutschein_q->num_rows()>0){

$promo=$gutschein_q->row();




//value of promo code
$this->session->set_userdata('zexch', $promo->zexch);
//type of promo code
$this->session->set_userdata('zexch_type', $promo->type);
$this->session->set_userdata('promo_code', $gutschein);
redirect('/shop/show_cart','refresh');

}
else{
echo '<font color="red">Fehlender Gutschein!!! Probieren Sie nochmal...</font>';
echo '
<form action="/shop/gutschein.html" method="post">
 Gutschein Einlösen:'.form_input('gutschein','',array('size'=>'10')).'
 <input type="submit" value="Aktualisieren">
</form><br> Oder 
'.anchor('/shop/show_cart','Zurueck zur Warenkorb');


}





}



function repeat_order($order_id,$do=0){

	$user_id=$this->session->userdata('user_id');
	if($user_id<1){
	
	exit('Bitte nochmals einloggen');
	}

$this->db->where('id',$order_id);
$order=$this->db->get('orders');
if($order->num_rows()<1){
exit("Kein Bestellung mit BestellungID $order_id");
}
$info=$order->row();
if($do!=1){

	
	$this->load->helper('html');

$image_properties = array(
          'src' => 'img/happy.png',
          //'alt' => 'Me, demonstrating how to eat 4 slices of pizza at one time',
          'border' => '0'
      
        
        
);


if($info->status=="Cancelled"){ 
	
	echo "Bestellung aktivieren?<br>";
	 echo anchor('/shop/repeat_order/'.$order_id.'/1','Ja '.img($image_properties));



       }
         else{

         //echo anchor('/shop/repeat_order/'.$order_id.'/1/','Clicken Sie Hier um gleicher Bestellung zu wiederholen');
	exit('Dieser Bestellung kann nicht mehr veraendert werden');

          }

           }
else{

   if($info->status=="Cancelled"){
   $this->db->where('id',$order_id);
   $this->db->update('orders',array('status'=>'New'));
   
   echo "Bestellung Aktiviert";
   
   }
   else{
   
   	exit('Dieser Bestellung kann nicht veraendert werden');
   $new_order=$order->row_array();
   
   
   unset($new_order['uniq_id']);
   
   
   
   
   
   $this->load->helper('date');
   
   
   
		$datestring = "%Y-%m-%d %H:%i:%s";
$time = time();
		


		$new_order['date']= mdate($datestring, $time);	
			unset($new_order['last_update']);
				unset($new_order['uniq_id']);	
		//print_r($new_order);
		
		
		$this->load->helper('string');
		
		$new_order['uniq_id']=random_string('unique','32');
		
		
	
		
		unset($new_order['id']);
		$order_data=$new_order;
		
		
		
	$this->db->insert('orders',$order_data);
	

	

$last_q=$this->db->get_where('orders',array('uniq_id'=>$order_data['uniq_id'],'user_id'=>$order_data['user_id']));



$my_order=$last_q->row_array();
$new_order_id=$my_order['id'];

//echo "CHKA";
$this->db->where('order_id',$order_id);
	$items=$this->db->get('order_items');
	
	if($items->num_rows()>0){
	
	foreach($items->result_array() as $item){
	
	unset($item['id']);
	
	$item['order_id']=$new_order_id;
	
	
	$this->db->insert('order_items',$item);
	
	}

	}


print_r($my_order);
		
	
   
   }


}









}
function show_top_cart(){

echo $this->shop_model->show_top_basket();


}


function show_total(){

echo $this->cart->total();


}



function remove_from_cart($prod_id){

$row_id=$this->shop_model->get_basket_key($prod_id);


$data = array(
               'rowid' => $row_id,
               'qty'   => 0
            );

$this->cart->update($data);

echo "OK";

                                    
//echo $row_id;	



}

function cancel_order($order_id,$yes=0){
$order_id=intval($order_id);
if($order_id<1){
exit('Wrong Order Id Format');
}
	$this->db->where('id',$order_id);
$order=$this->db->get('orders');

if($order->num_rows>0){
	if($yes==0){
	
	echo 'Sind Sie Sicher dass Sie wollen Bestellung abbrechen?<img src="/img/surprise.png"><br>';
	
	echo anchor('/shop/cancel_order/'.$order_id.'/1/','Ja!');
	
	}
	else{
	
		$upik=array('status'=>'Cancelled');
		$this->db->where('id',$order_id);
	 $this->db->update('orders',$upik);
	echo 'Bestellung Abgebrochen <img src="/img/sad.png">';
	
	}
	
}
else{
echo "Keine Bestellung ist mit OrderId:$order_id gefunden";

}


}

function show_product($prod_id=0){

if(!$prod_id or intval($prod_id)==0){


	
	$data['title']="Error";
	$data['message']="ProduktID ist falsch. Es kann nicht null sein.";
$error=$this->load->view('show_messages',$data,true);
exit($error);

}
else{
	$prod_id=intval($prod_id);
$this->db->where('id',$prod_id);
$prod=$this->db->get('products');


if($prod->num_rows()<1){


	$data['title']="Error";
	$data['message']="Produkt mit ProduktID $prod_id ist leider nicht gefunden.";
$error=$this->load->view('show_messages',$data,true);
exit($error);

 }
 else{
 $prr=$prod->row();
 $data['title']=$prr->name;
 $data['winTitle']=$prr->name;
 $data['product_id']=$prod_id;
 $data['tbname']='products';
	$data['message']=$this->load->view('show_product',$data,true);
$this->load->view('show_messages',$data);
 
 }

}




}
function remove_from_cart_rowid($row_id){

//$row_id=$this->shop_model->get_basket_key($prod_id);


$data = array(
               'rowid' => $row_id,
               'qty'   => 0
            );

$this->cart->update($data);

echo "OK";

                                    
//echo $row_id;	



}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */