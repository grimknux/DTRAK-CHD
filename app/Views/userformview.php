<?= $this->extend("layouts/base"); ?>

<?= $this->section("content"); ?>


<div id="page-content">
    <div class="content-header">
        <div class="row">
            <div class="col-sm-12">
                <div class="header-section">
                    <h1><i class="fa fa-gears"></i> <?= $page_heading ?></h1>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="block full">
                <div class="block-title">
                    <h2><?= $sub_head; ?></h2>
                </div>

                <!--<?= form_open('#', 'class="form-horizontal" id="userform"'); ?>-->
                <form class="form-horizontal form-bordered" id="userform" method="post" accept-charset="utf-8">
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="username">Username</label>
                        <div class="col-md-9">
                            <input type="text" id="username" name="username" class="form-control" value='<?= set_value('username'); ?>'>
                            <span class="text-danger" id="erruser"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="email">Email</label>
                        <div class="col-md-9">
                            <input type="text" id="email" name="email" class="form-control" value='<?= set_value('email'); ?>'>
                            <span class="text-danger" id="erremail"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="mobile">Mobile</label>
                        <div class="col-md-9">
                            <input type="text" id="mobile" name="mobile" class="form-control" value=''>
                            <span class="text-danger" id="errmobile"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="test">test</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="test" name="test[]" class="form-control input-sm" value=''>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-effect-ripple btn-success add-textbox input-sm"><i class="hi hi-plus"></i></button>
                                </span>
                            </div>
                            <span class="text-danger" id="errtest"></span>
                        </div>
                    </div>

                    <div class="textbox-wrapper"></div>

                    <div class="form-group form-actions">
                        <div class="col-md-9 col-md-offset-3">
                            <button type="submit" class="btn btn-effect-ripple btn-primary">Submit</button>
                            <button type="reset" class="btn btn-effect-ripple btn-danger">Reset</button>
                        </div>
                    </div>
                    Start your creative project!

                <!--<?= form_close(); ?> -->
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section("script"); ?>

<script>
        $(document).ready(function(){

        var max = 10;
        var cnt = 1;
        $(".add-textbox").on("click", function(e){
            //e.preventDefault();
            if(cnt < max){
                cnt++;
                $(".textbox-wrapper").append('<div class="form-group"><div class="col-md-6 offset-md-3"><div class="input-group"><input type="text" id="test" name="test[]" class="form-control input-sm" ><span class="input-group-btn"><button type="button" class="btn btn-effect-ripple btn-danger remove-textbox input-sm"><i class="hi hi-minus"></i></button></span></div><span class="text-danger" id="errtest'+cnt+'"></span></div></div>');
            }
        });

        $(".textbox-wrapper").on("click",".remove-textbox", function(e){
            e.preventDefault();
            $(this).parents(".form-group").remove();
            cnt--;
        });



        $('#userform').on('submit', function(event){
            //alert("greg");
            event.preventDefault();

            $.ajax({
                url:"<?php echo base_url('submit-form'); ?>",
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                method:"POST",
                data:$(this).serialize(),
                dataType:"json",
                beforeSend:function(){

                    $('#userform').attr('disabled', 'disabled');

                },

                success:function(data)
                {
                    //alert("greg"); 
                if(data.error)
                    {
                        //alert(JSON.stringify(data.validation));

                        //alert(data.validation.test.length);

                        if(data.username != '')
                            {
                                $('#erruser').html(data.username);
                            }
                        else
                            {
                                $('#erruser').html('');
                                alert("success");
                            }
                        if(data.email != '')
                            {
                                $('#erremail').html(data.email);
                            }
                        else
                            {
                                $('#erremail').html('');
                            }
                        if(data.mobile != '')
                            {
                                $('#errmobile').html(data.mobile);
                            }
                        else
                            {
                                $('#errmobile').html('');
                            }
                        if(data.test!= '')
                            {
                                
                                $('#errtest').html(data.test);
                                
                                
                            }
                        else
                            {
                                $('#errtest').html('');
                               
                            }
                    }

                    if(data.success)
                        {
                            $('#erruser').html('');
                            $('#erremail').html('');
                            $('#errmobile').html('');
                            $('#errtest').html('');

                            alert(data.message);
                        }
                    $('#userform').attr('disabled', false);
                }
            })
            
        });

        });
    </script>

<?= $this->endSection(); ?>