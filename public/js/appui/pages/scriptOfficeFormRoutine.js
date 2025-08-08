$(document).ready(function() {
			
				makeAjaxRequest(1);
				
			
			});
			
			function makeAjaxRequest(x) {
			if(x==1){ 
				$.ajax({
					url: 'officeFormRoutine_exe.php',
					type: 'post',
					data: {x:x, }, 
					success: function(response) {
					//alert(response);
					$('div#officeCssView').html(response);
					}
				});
			}else if(x==2){
				$.ajax({
					url: 'officeFormRoutine_exe.php',
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
					url: 'officeFormRoutine_exe.php',
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
					url: 'officeFormRoutine_exe.php',
					type: 'post',
					data: {x:x, unitID:unitID}, 
					success: function(response) {
					//$( "#genQR" ).prop( "disabled", true );
					alert("Success!");
					makeAjaxRequest(1);
					}
				});
			}else if(x==6){
				$.ajax({url:"officeFormRoutine_exe.php",
				type:"post",
				data:{x:x, pwPrev:pwPrev},
				success:function(b){
					if(b.indexOf("yes") > -1){
						saveCp();
					}else if(b.indexOf("no") > -1){
						alert("Wrong current Password!");
					}
				}
				});
			}else if(x==7){
				$.ajax({url:"officeFormRoutine_exe.php",
				type:"post",
				data:{x:x, pwNew:pwNew, pwNew2:pwNew2},
				success:function(b){
					//alert(b);
					alert("Successfully Changed Password!");
					window.location.href = "adminLogout.php";
				}
				});
			}else if(x==999){
				$.ajax({url:"cpass.php",
				type:"post",
				data:{x:x},
				success:function(b){
					$("div#showCPModal").html(b)}
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
			
			function cpass(){
				makeAjaxRequest(999);
			}
			
			var pwPrev, pwNew, pwNew2;
			
			function checkPw(){
				pwPrev = $("#curPw").val();
				var err="";
				
				if(pwPrev==""){
					err+="Please enter current password! \n";
				}
				if(err!=""){
					alert(err);
				}else{
					makeAjaxRequest(6);
				}
					
			}
			
			function saveCp(){
				
				pwNew = $("#newPw").val();
				pwNew2 = $("#newPwRe").val();
				var err="";
				
				
				if(pwNew==""){
					err+="Please enter new password! \n";
				}
				if(pwNew2==""){
					err+="Please re-enter new password! \n";
				}
				if(pwNew!=pwNew2){
					err+="Password doesn't match! \n";
				}
				
				if(err!=""){
					alert(err);
				}else{
					makeAjaxRequest(7);
					//alert(pwNew);
				}
			}
			
	