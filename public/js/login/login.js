$('document').ready(function() { 
	/* handling form validation */
	$("#login-form").validate({
		rules: {
			password: {
				required: true,
			},
			user_email: {
				required: true,
				email: true
			},
		},
		messages: {
			password:{
			  required: "<span style='position: absolute; left: 100px; top: -17px; color:#ddd;'>Please enter your password</span>"
			 },
			user_email: "<span style='position: absolute; left: 60px; top: -17px; color:#ddd;'>Please enter valid email address</span>",
		},
		submitHandler: submitForm	
	});	   
	/* Handling login functionality */
	function submitForm() {		
		var data = $("#login-form").serialize();				
		 $.ajax({				
			type : 'POST',
			url  : 'login.php',
			data : data,
			beforeSend: function(){	
				$("#error").fadeOut();
				$("#login_button").html('<span class="glyphicon glyphicon-transfer"></span> &nbsp; Signing In ...');
			},
			success : function(response){						
				if(response=="ok"){									
					window.location.href = "admin.php";
				} 
				else if(response=="member"){									
					window.location.href = "member.php";
				}
				else if(response=="Invalid Email or Password"){
					alert("Invalid Email or Password"); // show alert box
					$("#login_button").prop('disabled', false);
					$("#login_button").html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Sign In');
				}
				else {									
					$("#error").fadeIn(1000, function(){						
						$("#error").html('<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response+'</div>');
						$("#login_button").prop('disabled', false);
						$("#login_button").html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Sign In');
					});
				}
			},
			error: function() {
				alert("Error: Failed to submit form.");
				$("#login_button").prop('disabled', false);
				$("#login_button").html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Sign In');
			}
		});
		return false;
	}   
});