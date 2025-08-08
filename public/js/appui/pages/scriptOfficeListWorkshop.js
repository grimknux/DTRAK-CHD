$(document).ready(function() {
			
				makeAjaxRequest(1);
				
			
			});
			
			function makeAjaxRequest(x) {
			if(x==1){ 
				$.ajax({
					url: 'officeListWorkshop_exe.php',
					type: 'post',
					data: {x:x, }, 
					success: function(response) {
					//alert(response);
						$('div#workshopView').html(response);
					}
				});
			}else if(x==2){ 
				$.ajax({
					url: 'officeListWorkshop_exe.php',
					type: 'post',
					data: {x:x, datas:datas}, 
					success: function(response) {
					//alert(datas);
						$("div#result").html(response);
					}
				});
			}else if(x==999){ 
				$.ajax({
					url: 'cpass.php',
					type: 'post',
					data: {x:x}, 
					success: function(response) {
					//alert(response);
						$('div#showCPModal').html(response);
					}
				});
			}
			
			}
		
	var	datas;
	function getData(data){
		//alert("fetch!");
		//alert(data
		datas=data;
		makeAjaxRequest(2);
	
	}
	
	function cpass(){
		makeAjaxRequest(999);
	}
	
	function exportF(g){
		var a="";
		exportID=g;
		if(exportID=="workshopBtn"){
			a="workshopTBL_user";
		}else if(exportID=="resourceBtn"){
			a="resourceTBL";
		}
		
		
		var f="<table border='2px'><tr bgcolor='#87AFC6'>";var e;var c=0;tab=document.getElementById(a);for(c=0;c<tab.rows.length;c++){f=f+tab.rows[c].innerHTML+"</tr>"}f=f+"</table>";f=f.replace(/<A[^>]*>|<\/A>/g,"");f=f.replace(/<img[^>]*>/gi,"");f=f.replace(/<input[^>]*>|<\/input>/gi,"");var d=window.navigator.userAgent;var b=d.indexOf("MSIE ");if(b>0||!!navigator.userAgent.match(/Trident.*rv\:11\./)){txtArea1.document.open("txt/html","replace");txtArea1.document.write(f);txtArea1.document.close();txtArea1.focus();sa=txtArea1.document.execCommand("SaveAs",true,"Say Thanks to Sumit.xls")}else{sa=window.open("data:application/vnd.ms-excel,"+encodeURIComponent(f))}return(sa)};