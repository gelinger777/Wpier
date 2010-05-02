<script id="demo" type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	var validator = $("#signupform").validate({
		rules: {
			firstname: "required",
			lastname: "required",
			username: {
				required: true,
				minlength: 5,
				remote: {
			           url:"/auth/username_check/",
			           type:"post",
		                }
			},
			password: {
				required: true,
				minlength: 5
			},
			confirm_password: {
				required: true,
				minlength: 5,
				equalTo: "#password"
			},
			email: {
				required: true,
				email: true,
				remote: {
				           url:"/auth/email_check/",
				           type:"post",
			                }
			},
			
			terms: "required"
		},
		messages: {
			
			username: {
				required: "Enter a username",
				minlength: jQuery.format("Weniger als  {0} Buchstaben"),

				remote: function(){ return jQuery.format("{0} is invalid or already in use", $("#username").val()) }
			},
			password: {
				required: "Provide a password",
				rangelength: jQuery.format("Weniger als  {0} Buchstaben")
			},
			password_confirm: {
				required: "Repeat your password",
				minlength: jQuery.format("Weniger als {0} Buchstaben "),
				equalTo: "Passworte sind nicht Identisch"
			},
			email: {
				required: "Please enter a valid email address",
				minlength: "Please enter a valid email address",
				remote:function() { return jQuery.format("{0} is invalid or already in use", $("#email").val()) }
			},
			
		
		},
		// the errorPlacement has to take the table layout into account
		errorPlacement: function(error, element) {
			if ( element.is(":radio") )
				error.appendTo( element.parent().next().next() );
			else if ( element.is(":checkbox") )
				error.appendTo ( element.next() );
			else
				error.appendTo( element.parent().next() );
		},
		// specifying a submitHandler prevents the default submit, good for the demo
		
		// set this class to error-labels to indicate valid fields
		success: function(label) {
			// set &nbsp; as text for IE
			label.html("&nbsp;").addClass("checked");
		}
	});
	
	// propose username by combining first- and lastname
	

});
</script>



<?php
$username = array(
	'name'	=> 'username',
	'id'	=> 'username',
	'size'	=> 30,
	'value' =>  set_value('username')
);

$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'size'	=> 30,
	'value' => set_value('password')
);

$confirm_password = array(
	'name'	=> 'confirm_password',
	'id'	=> 'confirm_password',
	'size'	=> 30,
	'value' => set_value('confirm_password')
);

$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'maxlength'	=> 80,
	'size'	=> 30,
	'value'	=> set_value('email')
);


?>

<html>
<body>
<fieldset><legend>Register</legend>
<form id="signupform" action="/auth/register/" method="POST" autocomplete="off">

<table>

<tr>

	<td class="label"><?php echo form_label('Username', $username['id']);?></td>
	<td class="field">
		<?php echo form_input($username)?></td>
  <td class="status"> <?php echo form_error($username['name']); ?>
	</td> 
	</tr>
	<tr>

	<td class="label"><?php echo form_label('Password', $password['id']);?></td>
	<td class="field">
		<?php echo form_password($password)?></td>
   <td class="status"> <?php echo form_error($password['name']); ?></td>
	</tr>

	<tr>
	
	<td class="label">
<?php echo form_label('Confirm Password', $confirm_password['id']);?></td>
<td class="field">
		<?php echo form_password($confirm_password);?></td>
	<td class="status">	<?php echo form_error($confirm_password['name']); ?>
</td>

</tr>
<tr>
	<td class="label"><?php echo form_label('Email Address', $email['id']);?></td>
	<td class="field">
	<?php echo form_input($email);?> </td>
	<td class="result">		<?php echo form_error($email['name']); ?>
	
	</td>
		
</tr>

<tr>
<td> </td>

	<td><?php echo form_submit('register','Register');?></td>
</tr>
</table>

<?php echo form_close()?>
</form>
</fieldset>
</body>
</html>