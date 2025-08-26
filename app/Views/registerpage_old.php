<?php
    $session_page = CodeIgniter\Config\Services::session();
?>

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
        <!-- Login Title -->
        <div class="block-title" style="background-color:#f9fafc">
            <div class="block-options pull-right">
                <a href="<?php echo base_url('login-page'); ?>" class="btn btn-effect-ripple btn-primary" data-toggle="tooltip" data-placement="left" title="Back to login"><i class="fa fa-user"></i></a>
            </div>
            <h2><?= $sub_head; ?></h2>
        </div>
        <!-- END Login Title -->

        <?php
            if($session_page->getTempdata('success')): ?>
            
            <div class="alert alert-success alert-dismissable" id="hidemsg">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><strong>Success</strong></h4>
                <p><?= $session_page->getTempdata('success'); ?></p>
            </div>
        <?php endif; ?>

        <?php
            if($session_page->getTempdata('error')): ?>

            <div class="alert alert-danger alert-dismissable" id="hidemsg">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><strong>Error</strong></h4>
                <p><?= $session_page->getTempdata('error'); ?></p>
            </div>
        <?php endif; ?>

        <!-- Register Form -->
        <form id="form-register" method="post" class="form-horizontal">
            <div class="form-group">
                <label for="username" class="col-xs-12">Username</label>
                <div class="col-xs-12">
                    <input type="text" id="username" name="username" class="form-control" placeholder="Username" value='<?= set_value('username'); ?>'>
                    <span class="text-danger" id="erruname"><?= display_error($validation, 'username'); ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="email" class="col-xs-12">Email</label>
                <div class="col-xs-12">
                    <input type="text" id="email" name="email" class="form-control" placeholder="Email" value='<?= set_value('email'); ?>'>
                    <span class="text-danger" id="erremail"><?= display_error($validation, 'email'); ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-xs-12">Password</label>
                <div class="col-xs-12">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                    <span class="text-danger" id="errpass"><?= display_error($validation, 'password'); ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password" class="col-xs-12">Confirm Password</label>
                <div class="col-xs-12">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Verify Password">
                    <span class="text-danger" id="errcpass"><?= display_error($validation, 'confirm_password'); ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="mobile" class="col-xs-12">Mobile Number</label>
                <div class="col-xs-12">
                    <input type="text" id="mobile" name="mobile" class="form-control" placeholder="Mobile Number" value='<?= set_value('mobile'); ?>'>
                    <span class="text-danger" id="errmobile"><?= display_error($validation, 'mobile'); ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="mobile" class="col-xs-12">User Type</label>
                <div class="col-xs-12">
                    <select class="form-control" id="utype" name="utype">
                        <option value=""></option>
                        <?php foreach($usertype as $utype): ?>
                            <option value="<?= $utype['type_code'];?>"><?= $utype['type']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="text-danger" id="errutype"><?= display_error($validation, 'utype'); ?></span>
                </div>
            </div>
            
            <div class="form-group form-actions">
                <div class="col-xs-12 text-right">
                    <button type="submit" class="btn btn-effect-ripple btn-success"><i class="fa fa-plus"></i> Create Account</button>
                </div>
            </div>
        </form>
        <!-- END Register Form -->

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