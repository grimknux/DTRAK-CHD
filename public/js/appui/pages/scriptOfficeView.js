$(document).ready(function() {
				
				makeAjaxRequest(101);
				makeAjaxRequest(102);
				makeAjaxRequest(103);
				makeAjaxRequest(2);
				makeAjaxRequest(3);
				
				
			});
			
			var jsonR = [];
			var jsonW = [];
			var jsonWR = [];
			function makeAjaxRequest(x) {
			if(x==101){ 
				$.ajax({
					url: 'json.php',
					type: 'post',
					data: {x:x,}, 
					datatype: 'json',
					cache: 'false',
					success: function(response) {
					//alert(response);
					//$('div#samplejson').html(response);
					jsonR=response;
					
					
					}
				});
			}else if(x==102){ 
				$.ajax({
					url: 'json.php',
					type: 'post',
					data: {x:x,}, 
					datatype: 'json',
					cache: 'false',
					success: function(response) {
					//alert(response);
					//$('div#samplejson').html(response);
					jsonW=response;
					
					
					}
				});
			}else if(x==103){ 
				$.ajax({
					url: 'json.php',
					type: 'post',
					data: {x:x,}, 
					datatype: 'json',
					cache: 'false',
					success: function(response) {
					//alert(response);
					//$('div#samplejson').html(response);
					jsonRW=response;
					
					
					}
				});
			}else if(x==2){ 
				$.ajax({
					url: 'officeView_exe.php',
					type: 'post',
					data: {x:x, }, 
					success: function(response) {
					//alert(response);
					$('div#viewDashboard').html(response);
					
					}
				});
			}else if(x==3){ 
				$.ajax({
					url: 'officeView_exe.php',
					type: 'post',
					data: {x:x, responseMonth:responseMonth, responseYear:responseYear}, 
					success: function(response) {
					//alert(response);
					$('div#responsedisplay').html(response);
					//alert(response);
					
					}
				});
			}else if(x==4){ 
				$.ajax({
					url: 'officeView_exe.php',
					type: 'post',
					data: {x:x, }, 
					success: function(response) {
					$('div#graphView').html(response);
					//alert(response);
					
					}
				});
			}
			
			}
			
			function isNumberKey(evt)
			{
				var charCode = (evt.which) ? evt.which : event.keyCode;
					if (charCode != 46 && charCode > 31 
						&& (charCode < 48 || charCode > 57))
					return false;
			  return true;
			}
			
			var responseMonth, responseYear;
			
			function submitResponse(){
				responseMonth = $('#responsemonth').val();
				responseYear = $('#responseyear').val();
				var err="";
				
				if(responseYear==''){
					err+="Please enter Year.";
				}
				
				if(err!=""){
					alert(err);
				}else{
					makeAjaxRequest(3);
					//alert(responseMonth);
				}
				
			}
			
			
		