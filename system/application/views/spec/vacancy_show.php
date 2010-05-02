<?php

$show='style="display:none"';


if($this->input->post('captcha_text')==TRUE){
$show='';

//$check=validate_captcha();




	    if ($this->Capcha_model->check($this->input->ip_address(), $this->input->post('captcha_text'))) {
	       $posted_data= "<H1>Ваша заявка отправлена . </H1>";
















	       $mail_text='<table width="100%">
  <tr>
     <td>На какую должность Вы претендуете</td>
     <td>'.$this->input->post('vacancy_name').'</td>
  </tr>
    <tr>
     <td>ФИО</td>
     <td>'.$this->input->post('fio').'></td>
  </tr>
    <tr>
     <td>Дата рождения</td>
     <td>'.$this->input->post('birth_date').'</td>
  </tr>
    <tr>
     <td>Семейное положение</td>
     <td>'.$this->input->post('family_status').'</td>
  </tr>
  <tr>
     <td>Адрес фактического проживания (можно с пометкой "там же")</td>
     <td>'.$this->input->post('adres').'</td>
  </tr>
    <tr>
     <td>Адрес прописки</td>
     <td>'.$this->input->post('propiska').'</td>
  </tr>
    <tr>
     <td>Телефон для связи</td>
     <td>'.$this->input->post('tel').'</td>
  </tr>
   <tr>
     <td>Базовое образование (укажите наименование учебного заведения, год окончания, специальность)</td>
     <td>'.$this->input->post('obrazovanie').'</td>
  </tr>
     <tr>
     <td>Дополнительное образование, повышение квалификации</td>
     <td>'.$this->input->post('other_obrazovanie').'</td>
  </tr>
     <tr>
     <td>Военная служба</td>
     <td>.'.$this->input->post('voennaja_sluzhba').'</td>
  </tr>
<tr>
     <td>Опыт работы (начиная с последнего места работы): название предприятия, даты, занимаемая должность, обязанности, Ваши достижени</td>
     <td>'.$this->input->post('opit').'</td>
  </tr>
<tr>
     <td>Почему хотите занять данное вакантное место?</td>
     <td>'.$this->input->post('pochemu').'</td>
  </tr>
<tr>
     <td>Как Вы представляете обязанности сотрудника в этой должности?</td>
     <td>'.$this->input->post('objazannosti').
	       '</td>
  </tr>
<tr>
     <td>На какую зар плату рассчитываете?</td>
     <td>'.$this->input->post('zarplata').'</td>
  </tr>
  <tr>
     <td>Как скоро сможете приступить к своим обязанностям?</td>
     <td>'.$this->input->post('kak_skoro').'</td>
  </tr>
  <tr>
     <td>Знание компьютера, оргтехники</td>
     <td>'.$this->input->post('znanie_pc').'</td>
  </tr>
  <tr>
     <td>Знание иностранных языков</td>
     <td>'.$this->input->post('znanie_jazik').'</td>
  </tr>
    <tr>
     <td>Дополнительные сведения о себе</td>
     <td>'.$this->input->post('o_sebe').'</td>
  </tr>
    <tr>
     <td>Из какого источника Вы узнали об имеющейся вакансии?</td>
     <td>'.$this->input->post('istochnik').'</td>
  </tr>
    <tr>
     <td>Дата заполнения</td>
     <td>'.date("Y-d-m",time()).'</td>
  </tr>
</table>
	       ';



	$em=$this->db->get_where('labels',array('keyname'=>'vac_email'));

	$ema=$em->row();

	$email=$ema->valtext;
	//echo $email;
	//echo $mail_text;
	//$email2=$this->config->item('')



$this->email->from('noreply@avtochexol.ru', 'Your Name');
$this->email->to($email);
//$this->email->cc('another@another-example.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('Vacancy Reply ');
$this->email->message($mail_text);

$this->email->send();

//echo $this->email->print_debugger();







	    } else {
	        //$this->form_validation->set_message('validate_capthca', '%s does not match the image.');
	      $posted_data= "<H1> Ваша заявка не отправлено. Проверьте правильность кода капчи.</H1>";
	    }






}
?>


<?php
$this->db->limit(1);
$this->db->where('id',$my_item_id);

$mnews=$this->db->get($tbname);


if($mnews->num_rows()<1){
	echo ('вакансия с ID:'.$my_item_id.'  не существует');
}
else{
	$news=$mnews->row();
$this->load->helper('html');
}
$pol=array('1'=>'Мужской','2'=>'Женский','3'=>'Не имеет Значения');


?>






	 <h2>Вакансии</h2>


	<? echo @$posted_data;?>

<span><?=$news->name;?>.</span><br>
<?=$news->short_descr;?><br>
<br>


<span>Уровень дохода:</span>   <br>
<?=$news->doxod;?>
  <br>
<br>


<span>Тип работы:</span><br>
<?=$news->tip_raboti;?>

<br>
<br>


<span>Условия работы и компенсации:</span><br>
<?=$news->uslovia_raboti;?>
<span>
Должностные обязанности:</span><br>


<?=$news->objazannosti;?>

<br>


<br>






<span>Требования к кандидату</span><br>
<table  width="100%">
  <tr>
     <td>&nbsp;</td>
     <td>&nbsp;</td>
  </tr>
  <tr>
     <td>Возраст:</td>
     <td><?=$news->vozrast;?></td>
  </tr>
  <tr>
     <td>Пол:</td>
     <td><?=$pol[$news->pol];?></td>
  </tr>
  <tr>
     <td>Образование:</td>
     <td><?=$news->obrazovanie;?></td>
  </tr>
  <tr>
     <td>Требования к квалификации:</td>
     <td><?=$news->trebovania;?></td>
  </tr>
</table>
<br>
<br>

<a class="butt" href="#anketa" onclick="$('#anketa').toggle();return false " >Запонить анкету</a>

<br>
<div id="anketa" <?=$show;?>

<form name="vacancy_form" id="vacancy_form"   method="post">
 <h2>Форма отправки резюме</h2>
<table width="100%">
  <tr>
     <td>На какую должность Вы претендуете</td>
     <td><input type="text" name="vacancy_name" value="<?=$this->input->post('vacancy_name');?>"></td>
  </tr>
    <tr>
     <td>ФИО</td>
     <td><input type="text" name="fio" value="<?=$this->input->post('fio');?>"></td>
  </tr>
    <tr>
     <td>Дата рождения</td>
     <td><input type="text" name="birth_date" value="<?=$this->input->post('birth_date');?>"></td>
  </tr>
    <tr>
     <td>Семейное положение</td>
     <td><input type="text" name="family_status" value="<?=$this->input->post('family_status');?>"></td>
  </tr>
  <tr>
     <td>Адрес фактического проживания (можно с пометкой "там же")</td>
     <td><textarea name="adres"><?=$this->input->post('adres');?></textarea></td>
  </tr>
    <tr>
     <td>Адрес прописки</td>
     <td><textarea name="propiska"><?=$this->input->post('propiska');?></textarea></td>
  </tr>
    <tr>
     <td>Телефон для связи</td>
     <td><input type="text" name="tel" value="<?=$this->input->post('tel');?>"></td>
  </tr>
   <tr>
     <td>Базовое образование (укажите наименование учебного заведения, год окончания, специальность)</td>
     <td><textarea name="obrazovanie"><?=$this->input->post('obrazovanie');?></textarea></td>
  </tr>
     <tr>
     <td>Дополнительное образование, повышение квалификации</td>
     <td><textarea name="other_obrazovanie"><?=$this->input->post('other_obrazovanie');?></textarea></td>
  </tr>
     <tr>
     <td>Военная служба</td>
     <td><input type="text" name="voennaja_sluzhba" value="<?=$this->input->post('voennaja_sluzhba');?>"></td>
  </tr>
<tr>
     <td>Опыт работы (начиная с последнего места работы): название предприятия, даты, занимаемая должность, обязанности, Ваши достижени</td>
     <td><textarea name="opit"><?=$this->input->post('opit');?></textarea></td>
  </tr>
<tr>
     <td>Почему хотите занять данное вакантное место?</td>
     <td><textarea name="pochemu"><?=$this->input->post('pochemu');?></textarea></td>
  </tr>
<tr>
     <td>Как Вы представляете обязанности сотрудника в этой должности?</td>
     <td><textarea name="objazannosti"><?=$this->input->post('objazannosti');?></textarea></td>
  </tr>
<tr>
     <td>На какую зар плату рассчитываете?</td>
     <td><input type="text" name="zarplata" value="<?=$this->input->post('zarplata');?>"></td>
  </tr>
  <tr>
     <td>Как скоро сможете приступить к своим обязанностям?</td>
     <td><input type="text" name="kak_skoro" value="<?=$this->input->post('kak_skoro');?>"></td>
  </tr>
  <tr>
     <td>Знание компьютера, оргтехники</td>
     <td><textarea name="znanie_pc"><?=$this->input->post('znanie_pc');?></textarea></td>
  </tr>
  <tr>
     <td>Знание иностранных языков</td>
     <td><textarea name="znanie_jazik"><?=$this->input->post('znanie_jazik');?></textarea></td>
  </tr>
    <tr>
     <td>Дополнительные сведения о себе</td>
     <td><textarea name="o_sebe"><?=$this->input->post('o_sebe');?></textarea></td>
  </tr>
    <tr>
     <td>Из какого источника Вы узнали об имеющейся вакансии?</td>
     <td><input type="text" name="istochnik" value="<?=$this->input->post('istochnik');?>"></td>
  </tr>
    <tr>
     <td>Дата заполнения</td>
     <td><input type="text" name="data" value="<?=date("Y-d-m",time());?>"></td>
  </tr>
</table>



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
echo '<label for="user_website">Please enter text in the captcha on the left *</label><br>';
echo $form_captcha_text;

?>

<br>
<br>

<a class="butt"  id="makaka" onclick="$('#vacancy_form').submit();return false;" type="submit">отправить резюме</a>

<br>


</form>


</div>