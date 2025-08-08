

$(document).ready(function(){makeAjaxRequest(1)});

function makeAjaxRequest(a){
	if(a==1){
		$.ajax({url:"cssWorkShopDetails_exe.php",
		type:"post",
		data:{x:a},
		success:function(b){
			$("div#cssform").html(b)}
		});
		
	}else if(a==2){
		$.ajax({
		url:"cssWorkShopDetails_exe.php",
		type:"post",
		data:{x:a,codeQR:codeQR},
		success:function(b){
			$("#genQR").prop("disabled",true);alert("Success!");makeAjaxRequest(1)
		}});
		
	}else if(a==3){
		$.ajax({url:"cssWorkShopDetails_exe.php",
		type:"post",
		data:{x:a,cssResourceName:cssResourceName,cssResourceOff:cssResourceOff,cssResourceTopic:cssResourceTopic},
		success:function(b){
			alert("Success!");makeAjaxRequest(1)
		}});
	}else if(a==4){
		$.ajax({url:"cssWorkShopDetails_exe.php",
		type:"post",
		data:{x:a,cssCodeURL:cssCodeURL},
		success:function(b){
			alert("Successfully Generated!");makeAjaxRequest(1)
		}});
	}else if(a==5){
		$.ajax({url:"cssWorkShopDetails_exe.php",
		type:"post",
		data:{x:a,cssDataRes:cssDataRes},
		success:function(b){
			//alert("Successfully Generated!");
			$("div#showeFormRes").html(b)
		}});
	}else if(a==6){
		$.ajax({url:"cssWorkShopDetails_exe.php",
		type:"post",
		data:{x:a,eResourcePerson:eResourcePerson, eResourceTopic:eResourceTopic, eResourceOff:eResourceOff, cssDataRes:cssDataRes},
		success:function(b){
			//alert("Successfully Generated!");
			$("div#showeFormRes").html(b)
			alert("Successfully Updated!!");
			$("#eFormRes").modal("hide");
			makeAjaxRequest(1)
		}});
	}



}


var codeQR;
function generateQR(a){
	codeQR=a;makeAjaxRequest(2);
}

var cssResourceName,cssResourceOff,cssResourceTopic;

function submitResource(){
	cssResourceName=$("#cssResourceName").val();
	cssResourceOff=$("#cssResourceOff").val();
	cssResourceTopic=$("#cssResourceTopic").val();
	var a="";
	if(cssResourceName==""){
		a+="Please enter Resource Speaker Name \n"
	}
	if(cssResourceOff==""){
		a+="Please enter Resource Speaker Office/Designation"
	}
	
	if(a!=""){
		alert(a)
	}else{
		makeAjaxRequest(3)
	}
};


function exportF(g){
	var a="";
	exportID=g;
	
	if(exportID=="workshopBtn"){
		a="workshopTBL_user";
	}else if(exportID=="resourceBtn"){
		a="resourceTBL";
	}
	var f="<table border='2px'><tr bgcolor='#87AFC6'>";var e;var c=0;tab=document.getElementById(a);for(c=0;c<tab.rows.length;c++){f=f+tab.rows[c].innerHTML+"</tr>"}f=f+"</table>";f=f.replace(/<A[^>]*>|<\/A>/g,"");f=f.replace(/<img[^>]*>/gi,"");f=f.replace(/<input[^>]*>|<\/input>/gi,"");var d=window.navigator.userAgent;var b=d.indexOf("MSIE ");if(b>0||!!navigator.userAgent.match(/Trident.*rv\:11\./)){txtArea1.document.open("txt/html","replace");txtArea1.document.write(f);txtArea1.document.close();txtArea1.focus();sa=txtArea1.document.execCommand("SaveAs",true,"Say Thanks to Sumit.xls")}else{sa=window.open("data:application/vnd.ms-excel,"+encodeURIComponent(f))}return(sa)};


var cssCodeURL = "";
function genURL(wsCode){
	cssCodeURL = wsCode
	//alert(cssCodeURL);
	makeAjaxRequest(4);
	
}


var cssDataRes;
function editResource(data){
	cssDataRes = data;
	makeAjaxRequest(5);
}


var eResourcePerson, eResourceTopic, eResourceOff;
function saveRS(){
	eResourcePerson = $('#eResourcePerson').val();
	eResourceTopic = $('#eResourceTopic').val();
	eResourceOff = $('#eResourceOff').val();
	var a="";
	if(eResourcePerson==""){
		a+="Please enter Resource Speaker Name \n";
	}
	if(eResourceOff==""){
		a+="Please enter Resource Speaker Office/Designation";
	}
	
	if(a!=""){
		alert(a)
	}else{
		makeAjaxRequest(6)
	}
}




