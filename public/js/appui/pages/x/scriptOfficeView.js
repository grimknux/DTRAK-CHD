$(document).ready(function() {
			
				makeAjaxRequest(1);
			
			});
			
			function makeAjaxRequest(x) {
			if(x==1){ 
				$.ajax({
					url: 'officeView_exe.php',
					type: 'post',
					data: {x:x, }, 
					success: function(response) {
					//alert(response);
					$('div#officeCssView').html(response);
					}
				});
			}else if(x==2){
				$.ajax({
					url: 'officeView_exe.php',
					type: 'post',
					data: {x:x, cCode:cCode}, 
					success: function(response) {
					//$( "#genQR" ).prop( "disabled", true );
					//alert("Success!");
					//alert(response);
					$('div#showUpdateModal').html(response);
					}
				});
			}else if(x==3){
				$.ajax({
					url: 'officeView_exe.php',
					type: 'post',
					data: {x:x, servCode:servCode, cssOth:cssOth, cssCode:cssCode}, 
					success: function(response) {
					//$( "#genQR" ).prop( "disabled", true );
					alert("Success!");
					//alert(response);
					//$('div#showUpdateModal').html(response);
					makeAjaxRequest(1);
					$('#showUpdate').modal('hide');
					}
				});
			}else if(x==4){
				$.ajax({
					url: 'officeView_exe.php',
					type: 'post',
					data: {x:x, unitID:unitID}, 
					success: function(response) {
					//$( "#genQR" ).prop( "disabled", true );
					alert("Success!");
					makeAjaxRequest(1);
					}
				});
			}
			
			}
			
			var cCode;
			function updateCss(cssCode){
				cCode=cssCode;
				makeAjaxRequest(2);
				
				//alert(cCode);
			}
			
			function ifOth(servID){
				var servCode=servID;
				if(servCode=="Others"){
					$("#servOth").show();
				}
			}
			
			var servCode, cssOth, cssCode;
			function saveUpdate(){
				servCode = $('#selectService').val();
				cssOth = $('#cssOth').val();
				cssCode = $('#cssCode').val();
				var err="";
				
				if(servCode=='' || servCode=='n/a'){
					err+="Please select Service Code \n";
				}else if(servCode=='Others'){
					if(cssOth==''){
						err+="Please enter Others Field \n";
					}
				}
				
				if(err!=""){
					alert(err);
				}else{
					makeAjaxRequest(3);
					//alert(cssOth);
					//alert(servCode+", "+cssOth);
				}
				
			}
			
			var unitID;
			function generateQr(sec){
				unitID=sec;
				makeAjaxRequest(4);
			}