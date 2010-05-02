<?
/**
 * Captcha model
 *
 * Interacts with the captcha table and creates and validates
 * the captcha using the captcha plugin
 *
 * @package		openviz
 * @subpackage          admin
 * @author		Robert Tooker
 * @since		Version 1.0
 */

class Shop_model extends Model {
// private  $_model_table = 'captcha';
    /**
     * Defines the table for the model, used for active record functions
     * @access private
     * @var string
     */
 


    /**
     * Constructor
     */
    function __construct() {
        parent::Model();
    }

    /**
     * Makes a captcha image
     * @param text $ip_address the IP address of the user
     * @return text a link to the image or an error message if image could not be created
     */
    
    
    
    
    
  function get_product_name($prod_id){
  	
  	
  	$ppquery=$this->db->get_where('products',array('id'=>$prod_id));
  	
  	if($ppquery->num_rows()<1){
  		
  		
  		exit( "Product is Deleted or Moved");
  	}
  	else{
  		
  		
  		$produkt=$ppquery->row();
  		
  	return $produkt->name;	
  	
  	}
  	
  
  	
  }  
    
    
   	
  function show_top_basket(){
  	
  	
  	
  	return '<img src="/img/basket.png" width="12">'.$this->cart->total_items().' Produkte...€'.$this->cart->total().'';
  }	
  	 
    
 function check_in_basket($prod_id){
 	$return=false;
 $basket=$this->cart->contents();
//print_r($basket);	
 	$return=false;
 foreach($basket as $key=>$value){
 	//print_r($value);
 	if($value['id']==$prod_id){
 		
 	$return=TRUE;	
 	}
 	
 	
	
 }	
 	
 	
 	
 	
  return $return;	
 }   

 
 
 
 function imageResize($width, $height, $target) {

//takes the larger size of the width and height and applies the  formula accordingly...this is so this script will work  dynamically with any size image

if ($width > $height) {
$percentage = ($target / $width);
} else {
$percentage = ($target / $height);
}

//gets the new value and applies the percentage, then rounds the value
$width = round($width * $percentage);
$height = round($height * $percentage);

//returns the new sizes in html image tag format...this is so youcan plug this function inside an image tag and just get the

return "width=\"$width\" height=\"$height\"";

} 
 
 
 function reset_password($login_mail){
 	
 	//first see if it is an e-mail
 	if(substr_count($login_mail,'@')){
 		
 	$this->db->where('email',$login_mail);	
 		
 	}
 	else{
 	$this->db->where('username',$login_mail);	
 		
 		
 	}
 	
 	$lost_user=$this->db->get('my_users');
 	
 	if($lost_user->num_rows()>0){
 		$userdata=$lost_user->row();
 		return $userdata->id;
 	}
 	else{
 		
 	return False;
 		
 	}
 	
 	
 }
 
 
 function send_pass_key($user_id){
 	
 $uses=	$this->db->get_where('my_users',array('id'=>$user_id));
 
 	$user=$uses->row();
 	
 	
 	$user_email=$user->email;
 	
 	$this->load->helper('string');
 	
$ins['newpass_key']=random_string('unique','');
$ins['newpass']=random_string('alnum', 16);
 	$phptime=time();
 	 $ins['newpass_time'] = date ("Y-m-d H:i:s", $phptime);
 	 
 	 $this->db->where('id',$user_id);
 	 $this->db->update('my_users',$ins);
 	 
 $this->load->library('email');
 $config['mailtype'] = 'html';

$config['charset'] = 'utf-8';
$config['wordwrap'] = TRUE;

$this->email->initialize($config);

$this->email->from('noreply@buratino-shop.at', 'Buratino Shop');
$this->email->to($user_email);
//$this->email->cc('another@another-example.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('Passwort zurücksetzen');
$this->email->message('

<h2 style="font: 24px/27px Arial,sans-serif; margin: 0pt; padding: 0pt 0pt 18px; color: black;">Passwort zur&uuml;cksetzen</h2>
<p>Um ein neues Passwort zu erstellen klick einfach folgenden Link:<br /> <br /> <big style="font: 16px/18px Arial,sans-serif;"><strong><a style="color: #3366cc;" rel="nofollow" href="'.base_url().'auth/reset_passwort/'.$user_id.'/'.$ins['newpass_key'].'/" target="_blank">
<span id="lw_1272727168_0" class="yshortcuts">Neues Passwort erstellen</span></a></strong></big><br /> <br /> Funktioniert der Link nicht? Kopier einfach folgende Zeile in deine  Browser-Leiste:<br />
 <a style="color: #3366cc; white-space: nowrap;" rel="nofollow" href="'.base_url().'auth/reset_passwort/'.$user_id.'/'.$ins['newpass_key'].'" target="_blank"><span id="lw_1272727168_1" class="yshortcuts">'.base_url().'auth/reset_passwort/'.$user_id.'/'.$ins['newpass_key'].'</span></a><br />
  <br /> Du erh&auml;ltst diese E-Mail aufgrund einer von dir angefragten Passwort  Aktualisierung. Solltest du diese E-Mail aus Versehen bekommen haben  dann l&ouml;sch sie einfach und dein Passwort bleibt wie gehabt.<br /> <br /> <br /> Bis sp&auml;ter!<br /> Dein Buratino-Shop Team</p>



');







$this->email->send();

 
 	 
 	
 	
 }
 
 
 function get_basket_key($prod_id){
 	
 	$return=false;
 $basket=$this->cart->contents();
//print_r($basket);	
 	$return=false;
 foreach($basket as $key=>$value){
 	//print_r($value);
 	if($value['id']==$prod_id){
 		
 	$return=$key;	
 	}
 	
 	
	
 }	
 	
 	
 return $return;	
 	
 }
 
 
 
 
 
 
 
    

}

?>