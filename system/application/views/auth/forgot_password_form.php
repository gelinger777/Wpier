<?php

$login = array(
	'name'	=> 'login',
	'id'	=> 'login',
	'maxlength'	=> 80,
	'size'	=> 30,
	'value' => set_value('login')
);

?>

<fieldset>

Du hast dein Passwort vergessen? Gib hier die E-Mail Adresse oder Benutzername ein mit der du dich angemeldet hast und wir schicken dir eine Mail. In dieser E-Mail findest du dann einen Link, der dir helfen wird ein neues Passwort festzulegen..

<form method="POST" action="/auth/forgot_password/">

<?php echo @$errors;
//echo $this->input->post('login');
?>

<dl>
	<dt><?php echo form_label('Bitte Ihr Login oder E-mail eingeben', $login['id']);?></dt>
	<dd>
		<?php echo form_input($login); ?> 
		<?php echo form_error($login['name']); ?>
		<?php echo form_submit('reset', 'Reset Now'); ?>
	</dd>
</dl>

</form>
</fieldset>