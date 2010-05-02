<script id="demo" type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	var validator = $("#resetForm").validate({
		rules: {
			
			password: {
				required: true,
				minlength: 5
			},
			repassword: {
				required: true,
				minlength: 5,
				equalTo: "#password"
			},
			
		},
		messages: {
			
			
			password: {
				required: "Provide a password",
				rangelength: jQuery.format("Weniger als  {0} Buchstaben")
			},
			repassword: {
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



<div class="cwrap"> <div class="content">
 <form autocomplete="off" method="post" action="" id="resetForm"> 
  <div class="registration_head"> 
  <h1>Neues Passwort erstellen</h1>
   <p>Bitte verwende ein Passwort, welches Sie sich 
   <i class="pen">gut merken</i> 
   koenen, ansonsten müssen wir diesen Vorgang bald wiederholen!</p>
    </div>
    
    
    <table>

<tr>

	<td class="label">
     <label for="password">Neues Passwort</label> </td>
     <td class="field"> <input type="password" id="password" value="" class="inputtext" name="password"></td>
       <td class="status"></td>
          </tr>  <tr>

	<td class="label"> <label for="repassword">Neues Passwort wiederholen</label></td>
           <td class="field">   <input type="password" value="" id="repassword" name="repassword"> </td> 
             <td class="status">  </td>
             </tr></table><br>
                <button name="post" type="submit" id="change_password" class="btn_st btn_smt">Passwort ändern</button> 
                
                <input type="hidden" name="doit" value="1">
                </form> </div> </div>