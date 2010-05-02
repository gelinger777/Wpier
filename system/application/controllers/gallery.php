<?php

class Gallery extends Controller {

	function Gallery()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$this->load->view('welcome_message');
	}
	
	
	
	function show_product($product_id=FALSE){
	
	//echo 'aaaaaaaaaaaaaaaa';
	if(!$product_id){
	exit('No Product id');
	
	
	
	}
	
	
	$data['product_id']=$product_id;
	
	
	$this->load->view('show_product',$data);
	
	
	
	
	
	
	
	}
	
	
	
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */