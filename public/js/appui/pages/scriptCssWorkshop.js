$(document).ready(function(){if(errtext=="error"){alert("No Workshop/Training Registered!")}makeAjaxRequest(1)});function makeAjaxRequest(a){if(a==1){$.ajax({url:"cssWorkshop_exe.php",type:"post",data:{x:a},success:function(b){$("div#codeDiv").html(b)}})}else{if(a==2){$.ajax({url:"cssWorkshop_exe.php",type:"post",data:{x:a},success:function(b){$("div#showModalForm").html(b)}})}else{if(a==3){$.ajax({url:"cssWorkshop_exe.php",type:"post",data:{x:a,cssActivityTitle:cssActivityTitle,cssTeam:cssTeam,cssStartDate:cssStartDate,cssEndDate:cssEndDate},success:function(b){alert("Succesfully Registered!");showCode(b)}})}else{if(a==4){$.ajax({url:"cssWorkshop_exe.php",type:"post",data:{x:a,trainingCode:trainingCode},success:function(b){$("div#showCodeModal").html(b)}})}else{if(a==5){$.ajax({url:"cssWorkshop_exe.php",type:"post",data:{x:a,trainingCode:trainingCode},success:function(b){alert(b)}})}}}}}}var cssActivityTitle,cssTeam,cssStartDate,cssEndDate;


function saveWorkshop(){
	cssActivityTitle=$("#cssActivityTitle").val();
	cssTeam=$("#cssTeam").val();
	cssStartDate=$("#cssStartDate").val();
	cssEndDate=$("#cssEndDate").val();
	var a="";
	if(cssActivityTitle==""){
		a+="Please enter Activity Title \n"
	}
	if(cssTeam=="" || cssTeam=="0"){
		a+="Please select Team/Unit/Cluster/Section/Program \n"
	}if(cssStartDate==""){
		a+="Please enter Activity Start Date \n"
	}if(cssEndDate==""){
		a+="Please enter Activity End Date \n"
	}if(a!=""){
		alert(a)
	}else{
		makeAjaxRequest(3)
	}
}
		
		
	function showForm(){makeAjaxRequest(2)}var trainingCode;function showCode(a){trainingCode=a;makeAjaxRequest(4);$("#formModal").modal("hide");$("#showCode").modal("show")}function copyText(){var a=document.getElementById("copyS");a.select();a.setSelectionRange(0,99999);navigator.clipboard.writeText(a.value);alert("Copied the text: "+a.value)}function cssLogin(){trainingCode=$("#loginCode").val();makeAjaxRequest(5)};