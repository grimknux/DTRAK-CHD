$(document).ready(function() {
			if(cssType=="routine"){
				makeAjaxRequest(1);
			}else if(cssType=="workshop"){
				makeAjaxRequest(3);
			}else if(cssType=="resource"){
				makeAjaxRequest(5);
			}
		});
		
		function makeAjaxRequest(x) {
			if(x==1){ //display equipments
				$.ajax({
					url: 'cssSurvey_exe.php',
					type: 'post',
					data: {x:x, cssType:cssType}, 
					success: function(response) {
					//alert(response);
					$('div#cssform').html(response);
					}
				});
			}else if(x==2){ //display equipments
				$.ajax({
					url: 'cssSurvey_exe.php',
					type: 'post',
					data: {x:x, cssclientfirst:cssclientfirst, cssclientlast:cssclientlast, cssdate:cssdate, cssstafffirst:cssstafffirst, cssstafflast:cssstafflast, csssex:csssex, csssemp:csssemp, overallexpect:overallexpect, cssstmt1:cssstmt1, cssstmt2:cssstmt2, cssstmt3:cssstmt3, cssstmt4:cssstmt4, cssstmt5:cssstmt5, cssstmt6:cssstmt6, overallservice:overallservice, csscomment:csscomment, officeSel:officeSel}, 
					success: function(response) {
					alert("Your survey has been recorded. Thank you!");
					makeAjaxRequest(1);
					//alert(response);
					}
				});
			}else if(x==3){ //display equipments
				$.ajax({
					url: 'cssSurvey_exe.php',
					type: 'post',
					data: {x:x, cssType:cssType}, 
					success: function(response) {
					//alert(response);
					$('div#cssform').html(response);
					}
				});
			}else if(x==4){ //display equipments
				$.ajax({
					url: 'cssSurvey_exe.php',
					type: 'post',
					data: {x:x, cssclientfirst:cssclientfirst, cssclientlast:cssclientlast, cssdate:cssdate, csssex:csssex, csssemp:csssemp, overallexpect:overallexpect, cssstmt1:cssstmt1, cssstmt2:cssstmt2, cssstmt3:cssstmt3, cssstmt4:cssstmt4, cssstmt5:cssstmt5, cssstmt6:cssstmt6,  cssstmt7:cssstmt7,  cssstmt8:cssstmt8,  cssstmt9:cssstmt9, overallservice:overallservice, csscomment:csscomment}, 
					success: function(response) {
					//$("#overlay").attr("hidden",false);
					//alert(response);
					/*setTimeout(function(){
						makeAjaxRequest(1);
						//$("#overlay").attr("hidden",true);
						//$("#cssdate").focus();
					}, 1000);*/
					//alert(response);
					//$('div#cssform').html(response);
					alert("Your survey has been recorded. Thank you!");
					makeAjaxRequest(3);
					//alert(response);
					}
				});
			}else if(x==5){ //display equipments
				$.ajax({
					url: 'cssSurvey_exe.php',
					type: 'post',
					data: {x:x, cssType:cssType}, 
					success: function(response) {
					//alert(response);
					$('div#cssform').html(response);
					}
				});
			}else if(x==6){ //display equipments
				$.ajax({
					url: 'cssSurvey_exe.php',
					type: 'post',
					data: {x:x, cssclientfirst:cssclientfirst, cssclientlast:cssclientlast, cssdate:cssdate, csssex:csssex, csssemp:csssemp, overallexpect:overallexpect, cssstmt1:cssstmt1, cssstmt2:cssstmt2, cssstmt3:cssstmt3, cssstmt4:cssstmt4, cssstmt5:cssstmt5, cssstmt6:cssstmt6,  cssstmt7:cssstmt7,  cssstmt8:cssstmt8,  cssstmt9:cssstmt9, overallservice:overallservice, csscomment:csscomment, cssResourcePerson:cssResourcePerson}, 
					success: function(response) {
					alert("Your survey has been recorded. Thank you!");
					makeAjaxRequest(5);
					
					//alert(response);
					}
				});
			}
		}
	
		
		var cssclientfirst;
		var cssclientlast;
		var cssdate;
		var cssstafffirst;
		var cssstafflast;
		var csssex;
		var csssemp;
		var overallexpect;
		var cssstmt1;
		var cssstmt2;
		var cssstmt3;
		var cssstmt4;
		var cssstmt5;
		var cssstmt6;
		var cssstmt7;
		var cssstmt8;
		var cssstmt9;
		var overallservice;
		var csscomment;
		var cssType;
		var officeSel;
		
		
		function submitcss_routine(){
			
			var err="";
			cssclientfirst = $('#cssclientfirst').val();
			cssclientlast = $('#cssclientlast').val();
			cssdate = $('#cssdate').val();
			cssstafffirst = $('#cssstafffirst').val();
			cssstafflast = $('#cssstafflast').val();
			var csssex_val = document.querySelector('input[name = "csssex"]:checked');
			var csssemp_val = document.querySelector('input[name = "csssemp"]:checked');
			var overallexpect_val = document.querySelector('input[name = "overallexpect"]:checked');
			var cssstmt1_val = document.querySelector('input[name = "cssstmt1"]:checked');
			var cssstmt2_val = document.querySelector('input[name = "cssstmt2"]:checked');
			var cssstmt3_val = document.querySelector('input[name = "cssstmt3"]:checked');
			var cssstmt4_val = document.querySelector('input[name = "cssstmt4"]:checked');
			var cssstmt5_val = document.querySelector('input[name = "cssstmt5"]:checked');
			var cssstmt6_val = document.querySelector('input[name = "cssstmt6"]:checked');
			var overallservice_val = document.querySelector('input[name = "overallservice"]:checked');
			csscomment = $('#csscomment').val();
			officeSel = $('officeSel').val();
			
			
			
			if(officeSel=="" || officeSel=="0"){
				err+="Please select Office.\n"
			}
			if(cssclientfirst==""){
				err+="Please enter Client First Name.\n"
			}
			if(cssclientlast==""){
				err+="Please enter Client Last Name.\n"
			}
			if(cssdate=="" || cssdate=="0001-01-01"){
				err+="Please enter date.\n"
			}
			if(csssex_val == null){
				err+="Please select sex/gender.\n";
			}else{
				csssex = csssex_val.value;
			}
			if(csssemp_val == null){
				err+="Please select DOH Employee.\n";
			}else{
				csssemp = csssemp_val.value;
			}
			if(overallexpect_val == null){
				overallexpect="n/a";
			}else{
				overallexpect = overallexpect_val.value;
			}
			if(cssstmt1_val == null){
				cssstmt1="n/a";
			}else{
				cssstmt1 = cssstmt1_val.value;
			}
			if(cssstmt2_val == null){
				cssstmt2="n/a";
			}else{
				cssstmt2 = cssstmt2_val.value;
			}
			if(cssstmt3_val == null){
				cssstmt3="n/a";
			}else{
				cssstmt3 = cssstmt3_val.value;
			}
			if(cssstmt4_val == null){
				cssstmt4="n/a";
			}else{
				cssstmt4 = cssstmt4_val.value;
			}
			if(cssstmt5_val == null){
				cssstmt5="n/a";
			}else{
				cssstmt5 = cssstmt5_val.value;
			}
			if(cssstmt6_val == null){
				cssstmt6="n/a";
			}else{
				cssstmt6 = cssstmt6_val.value;
			
			}
			if(overallservice_val == null){
				overallservice="n/a";
			}else{
				overallservice = overallservice_val.value;
			}
			
			if(err!=""){
				alert(err);
				//alert(cssstmt1+" "+cssstmt2+" "+cssstmt3+" "+cssstmt4+" "+cssstmt5+" "+cssstmt6);
			}else{
				makeAjaxRequest(2);
				//alert(cssclientfirst);
			}
			
			//alert(cssdate+', '+csstime+', '+csssemp+', '+cssoffice+', '+cssstaff+', '+cssmin+', '+csssatisfaction+', '+csscomment+', '+clientname+', '+clientoffice+', '+clientno+', '+clientemail+', '+csspurpose+', '+cssrate[0]+', '+cssrate[1]+', '+cssrate[2]+', '+cssrate[3]+', '+cssrate[4]+', '+cssrate[5]);

			
		}
		
		function submitcss_workshop(){
			
			var err="";
			cssclientfirst = $('#cssclientfirst').val();
			cssclientlast = $('#cssclientlast').val();
			cssdate = $('#cssdate').val();
			var csssex_val = document.querySelector('input[name = "csssex"]:checked');
			var csssemp_val = document.querySelector('input[name = "csssemp"]:checked');
			var overallexpect_val = document.querySelector('input[name = "overallexpect"]:checked');
			var cssstmt1_val = document.querySelector('input[name = "cssstmt1"]:checked');
			var cssstmt2_val = document.querySelector('input[name = "cssstmt2"]:checked');
			var cssstmt3_val = document.querySelector('input[name = "cssstmt3"]:checked');
			var cssstmt4_val = document.querySelector('input[name = "cssstmt4"]:checked');
			var cssstmt5_val = document.querySelector('input[name = "cssstmt5"]:checked');
			var cssstmt6_val = document.querySelector('input[name = "cssstmt6"]:checked');
			var cssstmt7_val = document.querySelector('input[name = "cssstmt7"]:checked');
			var cssstmt8_val = document.querySelector('input[name = "cssstmt8"]:checked');
			var cssstmt9_val = document.querySelector('input[name = "cssstmt9"]:checked');
			var overallservice_val = document.querySelector('input[name = "overallservice"]:checked');
			csscomment = $('#csscomment').val();
			
			if(cssdate=="" || cssdate=="0000-00-00"){
				err+="Please enter date.\n"
			}
			if(csssex_val == null){
				err+="Please select sex/gender.\n";
			}else{
				csssex = csssex_val.value;
			}
			if(csssemp_val == null){
				err+="Please select DOH Employee.\n";
			}else{
				csssemp = csssemp_val.value;
			}
			if(overallexpect_val == null){
				overallexpect="n/a";
			}else{
				overallexpect = overallexpect_val.value;
			}
			if(cssstmt1_val == null){
				cssstmt1="n/a";
			}else{
				cssstmt1 = cssstmt1_val.value;
			}
			if(cssstmt2_val == null){
				cssstmt2="n/a";
			}else{
				cssstmt2 = cssstmt2_val.value;
			}
			if(cssstmt3_val == null){
				cssstmt3="n/a";
			}else{
				cssstmt3 = cssstmt3_val.value;
			}
			if(cssstmt4_val == null){
				cssstmt4="n/a";
			}else{
				cssstmt4 = cssstmt4_val.value;
			}
			if(cssstmt5_val == null){
				cssstmt5="n/a";
			}else{
				cssstmt5 = cssstmt5_val.value;
			}
			if(cssstmt6_val == null){
				cssstmt6="n/a";
			}else{
				cssstmt6 = cssstmt6_val.value;
			
			}
			if(cssstmt7_val == null){
				cssstmt7="n/a";
			}else{
				cssstmt7 = cssstmt7_val.value;
			
			}
			if(cssstmt8_val == null){
				cssstmt8="n/a";
			}else{
				cssstmt8 = cssstmt8_val.value;
			
			}
			if(cssstmt9_val == null){
				cssstmt9="n/a";
			}else{
				cssstmt9 = cssstmt9_val.value;
			
			}
			if(overallservice_val == null){
				overallservice="n/a";
			}else{
				overallservice = overallservice_val.value;
			}
			if(err!=""){
				alert(err);
			}else{
				makeAjaxRequest(4);
			}
		}
		
		var cssResourcePerson;
		function submitcss_resource(){
			
			var err="";
			cssResourcePerson = $('#cssResourcePerson').val();
			cssclientfirst = $('#cssclientfirst').val();
			cssclientlast = $('#cssclientlast').val();
			cssdate = $('#cssdate').val();
			var csssex_val = document.querySelector('input[name = "csssex"]:checked');
			var csssemp_val = document.querySelector('input[name = "csssemp"]:checked');
			var overallexpect_val = document.querySelector('input[name = "overallexpect"]:checked');
			var cssstmt1_val = document.querySelector('input[name = "cssstmt1"]:checked');
			var cssstmt2_val = document.querySelector('input[name = "cssstmt2"]:checked');
			var cssstmt3_val = document.querySelector('input[name = "cssstmt3"]:checked');
			var cssstmt4_val = document.querySelector('input[name = "cssstmt4"]:checked');
			var cssstmt5_val = document.querySelector('input[name = "cssstmt5"]:checked');
			var cssstmt6_val = document.querySelector('input[name = "cssstmt6"]:checked');
			var cssstmt7_val = document.querySelector('input[name = "cssstmt7"]:checked');
			var cssstmt8_val = document.querySelector('input[name = "cssstmt8"]:checked');
			var cssstmt9_val = document.querySelector('input[name = "cssstmt9"]:checked');
			var overallservice_val = document.querySelector('input[name = "overallservice"]:checked');
			csscomment = $('#csscomment').val();
			
			if(cssResourcePerson==0){
				err+="Please select Resource Person \n";
			}
			if(cssdate=="" || cssdate=="0001-01-01"){
				err+="Please enter date.\n"
			}
			if(csssex_val == null){
				err+="Please select sex/gender.\n";
			}else{
				csssex = csssex_val.value;
			}
			if(csssemp_val == null){
				err+="Please select DOH Employee.\n";
			}else{
				csssemp = csssemp_val.value;
			}
			if(overallexpect_val == null){
				overallexpect="n/a";
			}else{
				overallexpect = overallexpect_val.value;
			}
			if(cssstmt1_val == null){
				cssstmt1="n/a";
			}else{
				cssstmt1 = cssstmt1_val.value;
			}
			if(cssstmt2_val == null){
				cssstmt2="n/a";
			}else{
				cssstmt2 = cssstmt2_val.value;
			}
			if(cssstmt3_val == null){
				cssstmt3="n/a";
			}else{
				cssstmt3 = cssstmt3_val.value;
			}
			if(cssstmt4_val == null){
				cssstmt4="n/a";
			}else{
				cssstmt4 = cssstmt4_val.value;
			}
			if(cssstmt5_val == null){
				cssstmt5="n/a";
			}else{
				cssstmt5 = cssstmt5_val.value;
			}
			if(cssstmt6_val == null){
				cssstmt6="n/a";
			}else{
				cssstmt6 = cssstmt6_val.value;
			
			}
			if(cssstmt7_val == null){
				cssstmt7="n/a";
			}else{
				cssstmt7 = cssstmt7_val.value;
			
			}
			if(cssstmt8_val == null){
				cssstmt8="n/a";
			}else{
				cssstmt8 = cssstmt8_val.value;
			
			}
			if(cssstmt9_val == null){
				cssstmt9="n/a";
			}else{
				cssstmt9 = cssstmt9_val.value;
			
			}
			if(overallservice_val == null){
				overallservice="n/a";
			}else{
				overallservice = overallservice_val.value;
			}
			if(err!=""){
				alert(err);
			}else{
				makeAjaxRequest(6);
			}
		}