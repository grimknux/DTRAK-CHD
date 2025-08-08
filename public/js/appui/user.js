
$(document).ready(function(){

    $('#userform').on('submit', function(event){
        //alert("greg");
        event.preventDefault();

        $.ajax({
            url:"<?php echo base_url('submit-form'); ?>",
            method:"POST",
            data:$(this).serialize(),
            dataType:"json",
            beforeSend:function(){

            $('#userform').attr('disabled', 'disabled');

            },

            success:function(data)
            {
            if(data.error)
                {
                    if(data.name_error != '')
                        {
                            $('#username').html(data.name_error);
                        }
                    else
                        {
                            $('#username').html('');
                        }
                    if(data.email_error != '')
                        {
                            $('#email').html(data.email_error);
                        }
                    else
                        {
                            $('#email').html('');
                        }
                    if(data.subject_error != '')
                        {
                            $('#mobile').html(data.subject_error);
                        }
                    else
                        {
                            $('#mobile').html('');
                        }
                }

                if(data.success)
                    {
                        alert("Success");
                    }
                $('#contact').attr('disabled', false);
            }
        })
        
    });

});