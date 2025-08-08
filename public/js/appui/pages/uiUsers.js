    $(document).ready(function(){
        callUser();
    });

    function callUser(){
        $.ajax({
            url:"server/user-server.php",
            type:"post",
            data:{},
            success:function(a){
                $("#i").html(a);
            }
        });
    }


