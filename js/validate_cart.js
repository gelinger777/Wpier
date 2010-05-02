
$(document).ready(function() {
	// validate signup form on keyup and submit
	var validator = $("#validator").validate({
		rules: {
			delivery_first_name: "required",
			delivery_last_name: "required",
			delivery_street1: "required",
			delivery_city_id: "required",
			delivery_region_id: "required",
			delivery_country_id: "required",
			delivery_postal_code: {
				required: true,
				minlength: 4
			},
			
			
		},
		messages: {
			delivery_first_name: "Enter your firstname",
			delivery_last_name: "Enter your lastname",
			delivery_street1: {
				required: "Bitte Adresse hingeben",
			
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
	
	

});
