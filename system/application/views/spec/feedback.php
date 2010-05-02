<?
$show='';


if($this->input->post('captcha_text')==TRUE){

//$check=validate_captcha();




	    if ($this->Capcha_model->check($this->input->ip_address(), $this->input->post('captcha_text'))) {
	       $posted_data= "<H1>Ihre Anfrage ist geschickt . </H1>";

             $show='style="display:none"';















	       $mail_text='<table width="100%">
  <tr>
     <td>Titul</td>
     <td>'.$this->input->post('tema').'</td>
  </tr>
    <tr>
     <td>Frage</td>
     <td>'.$this->input->post('vopros').'></td>
  </tr>
    <tr>
     <td>Nachname und Vorname </td>
     <td>'.$this->input->post('avtor').'</td>
  </tr>
    <tr>
     <td>E-mail</td>
     <td>'.$this->input->post('email').'</td>
  </tr>

    <tr>
     <td>Datum</td>
     <td>'.date("Y-d-m",time()).'</td>
  </tr>
</table>
	       ';



	$em=$this->db->get_where('labels',array('keyname'=>'feedback_email'));

	$ema=$em->row();

	$email=$ema->valtext;
	//echo $email;
	//echo $mail_text;
	//$email2=$this->config->item('')



$this->email->from('noreply@ruslaminat.ru', 'Ruslaminat Site');
$this->email->to($email);
//$this->email->cc('another@another-example.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('FeedBack Form Submit ');
$this->email->message($mail_text);

$this->email->send();

//echo $this->email->print_debugger();







	    } else {
	        //$this->form_validation->set_message('validate_capthca', '%s does not match the image.');
	      $posted_data= "<H1> Error: Bildkod ist Falsch angegeben.</H1>";
	    }






}
?>












<h2>Anfrageformular</h2>
<?echo @$posted_data;?>
<div id="feedback" <?=$show?>>
<form id="feedback_form" name="feedback_form" action="" method="post">
<p style=" margin-left:15px;">Titul</p>

<input type="text" name="tema" value="<?=$this->input->post('tema');?>" style="width: 120px; margin-left:15px;">

<p style=" margin-left:15px;">Frage</p>
<textarea style="width: 210px; margin-left:15px;" name="vopros"><?=$this->input->post('vopros');?></textarea>

<p style=" margin-left:15px;">Vorname,Nachname</p>
<input type="text" name="avtor"value="<?=$this->input->post('avtor');?>" style="width: 120px; margin-left:15px;">
<p style=" margin-left:15px;">E-mail</p>
<input type="text" name="email" value="<?=$this->input->post('email');?>" style="width: 120px; margin-left:15px;">

<p style=" margin-left:15px;">Robocode <br>





<?php

// add the captcha image to the view data
$captcha_img= $this->Capcha_model->make($this->input->ip_address());
// load the view
//$this->load->view('xyz', $page_data);

	$form_captcha_text = form_input(array(
    'name'=>'captcha_text'
    , 'id'=>'captcha_text'
    , 'value'=>''
    , 'maxlength'=>8
    , 'size'=>8
    , 'class'=>'text'));



     echo $captcha_img;
echo '<br><label for="user_website">Bitte, geben Sie Robocode an *</label><br>';
echo $form_captcha_text;

?>
</p>
<br>
<br>

 <p style=" margin-left:15px;">
 <a id="makaka2" href="" onclick="$('#feedback_form').submit();return false;">Speichern</a>






	</a></p>
    </form>
	 </div>