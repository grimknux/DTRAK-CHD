<!-- Login Container -->
<?= $this->extend("layouts/login"); ?>

<?= $this->section("content"); ?>
<script>
    setTimeout(function() {
        $('#hidemsg').hide();
    }, 3000);
</script>

<img src="<?php echo base_url() ?>img/placeholders/layout/login2_full_bg.jpg" alt="Full Background" class="full-bg animation-pulseSlow">

<div id="login-container">
    <!-- Login Header -->
    <h1 class="h2 text-light text-center push-top-bottom animation-slideDown">
        <img src="<?= base_url();?>img/dohlogo.png" style="width: 30px; height: 30px;"> <strong><?= $page_heading; ?></strong>
    </h1>
    <!-- END Login Header -->

    <!-- Login Block -->
    <div class="block animation-fadeInQuickInv" style="background-color:#eaf2eb; background-image: url('<?= base_url();?>img/dohlogo_opac_5.png');background-size: 378px 378px;">

        <!-- Login Title  style="background-color:#a7d9a7"  f9fafc-->
        <div class="block-title" style="background-color:#f9fafc">
            <div class="block-options pull-right">
                <a href="#" class="btn btn-effect-ripple btn-primary" data-toggle="tooltip" data-placement="left" title="Forgot your password?"><i class="fa fa-exclamation-circle"></i></a>
                <a href="<?php echo base_url('register-form'); ?>" class="btn btn-effect-ripple btn-primary" data-toggle="tooltip" data-placement="left" title="Create new account"><i class="fa fa-plus"></i></a>
            </div>
            <h2><?= $sub_head; ?></h2>
        </div>
        <!-- END Login Title -->

        <?php
            if(session()->getTempdata('success')): ?>
            
            <div class="alert alert-success alert-dismissable" id="hidemsg">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><strong>Success</strong></h4>
                <p><?= session()->getTempdata('success'); ?></p>
            </div>
        </div>
        <?php endif; ?>
        <?php
            if(session()->getTempdata('error')): ?>

            <div class="alert alert-danger  alert-dismissable" id="hidemsg">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><strong>Error</strong></h4>
                <p><?= session()->getTempdata('error'); ?></p>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form id="form_data" class="form-horizontal" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username" class="col-xs-12">Email address</label>
                <div class="col-xs-12">
                    <input type="text" id="email" name="email" class="form-control" placeholder="Email address.." value="<?= set_value('email'); ?>">
                    <span class="text-danger" id="erremail"><?= display_error($validation, 'email'); ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-xs-12">Password</label>
                <div class="col-xs-12">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password.." value="<?= set_value('password'); ?>">
                    <span class="text-danger" id="erremail"><?= display_error($validation, 'password'); ?></span>
                </div>
            </div>
            <div class="form-group form-actions">
                <div class="col-xs-5">
                    <?php if(isset($loginButton)): ?>
                            <a href="<?= $loginButton; ?>" class="btn btn-effect-ripple btn-sm btn-danger btn-block"><i class="fa fa-google"></i> Login with Google</a>
                    <?php endif; ?>
                </div>
                <div class="col-xs-7 text-right">
                    <button type="submit" class="btn btn-effect-ripple btn-sm btn-warning"><i class="fa fa-check"></i> Login Now</button>
                </div>

            </div>
        </form>

        <!-- END Login Form -->
    </div>
    <!-- END Login Block -->
    
    <!-- Footer -->
    <footer class="text-muted text-center animation-pullUp">
        <small><span id="year-copy"></span> &copy; <a href="https://ro1.doh.gov.ph" target="_blank">DOH CHD Ilocos</a></small>
    </footer>
    <!-- END Footer -->
</div>
<!-- END Login Container -->

<?= $this->endSection(); ?>