$(document).ready(function(){makeAjaxRequest(1)});
function makeAjaxRequest(a){
	if(a==1){
		$.ajax({url:"adminView_exe.php",
		type:"post",
		data:{x:a},
		success:function(b){
			$("div#viewForm").html(b)}
		});
	}else if(a==2){
		$.ajax({url:"adminView_exe.php",
		type:"post",
		data:{x:a,cssValType:cssValType,cssDateFrom:cssDateFrom,cssDateTo:cssDateTo},
		success:function(b){
			$("div#tblDisplay").html(b)}
		});
	}else if(a==3){
		$.ajax({url:"adminView_exe.php",
		type:"post",
		data:{x:a},
		success:function(b){
			$("div#showCPModal").html(b)}
		});
	}else if(a==4){
		$.ajax({url:"adminView_exe.php",
		type:"post",
		data:{x:a, pwPrev:pwPrev},
		success:function(b){
			if(b.indexOf("yes") > -1){
				saveCp();
			}else if(b.indexOf("no") > -1){
				alert("Wrong current Password!");
			}
		}
		});
	}else if(a==5){
		$.ajax({url:"adminView_exe.php",
		type:"post",
		data:{x:a, pwNew:pwNew, pwNew2:pwNew2},
		success:function(b){
			alert("Successfully Changed Password!");
			window.location.href = "adminLogout.php";
		}
		});
	}
}
var cssValType,cssDateFrom,cssDateTo;function selectCssType(){cssValType=$("#cssValType").val();cssDateFrom=$("#cssDateFrom").val();cssDateTo=$("#cssDateTo").val();var a="";if(cssDateFrom==""||cssDateFrom=="0000-00-00"){a+="Please enter Date From.\n"}if(cssDateTo==""||cssDateTo=="0000-00-00"){a+="Please enter Date To.\n"}if(cssValType=="n/a"){a+="Please select CSS Type.\n"}if(a!=""){alert(a)}else{makeAjaxRequest(2)}}function exportF(g){var a="";exportID=g;if(exportID=="routineBtn"){a="routineTBL"}else{if(exportID=="workshopBtn"){a="workshopTBL"}else{if(exportID=="resourceBtn"){a="resourceTBL"}}}var f="<table border='2px'><tr bgcolor='#87AFC6'>";var e;var c=0;tab=document.getElementById(a);for(c=0;c<tab.rows.length;c++){f=f+tab.rows[c].innerHTML+"</tr>"}f=f+"</table>";f=f.replace(/<A[^>]*>|<\/A>/g,"");f=f.replace(/<img[^>]*>/gi,"");f=f.replace(/<input[^>]*>|<\/input>/gi,"");var d=window.navigator.userAgent;var b=d.indexOf("MSIE ");if(b>0||!!navigator.userAgent.match(/Trident.*rv\:11\./)){txtArea1.document.open("txt/html","replace");txtArea1.document.write(f);txtArea1.document.close();txtArea1.focus();sa=txtArea1.document.execCommand("SaveAs",true,"Say Thanks to Sumit.xls")}else{sa=window.open("data:application/vnd.ms-excel,"+encodeURIComponent(f))}return(sa)};

function cpass(){
				makeAjaxRequest(3);
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
					makeAjaxRequest(4);
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
					makeAjaxRequest(5);
				}
			}