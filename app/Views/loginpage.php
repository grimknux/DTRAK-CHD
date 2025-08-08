<!-- Login Container -->
<?= $this->extend("layouts/login"); ?>

<?= $this->section("content"); ?>

<div id="overlay">
    <div class="loader"></div>
</div>
<script>
    setTimeout(function() {
        $('.error-message').html("");
        $('.form-error').html("");
        $('.error-box').hide();
    }, 3000);
</script>

<div id="login-container">
    <h1 class="h2 text-light text-center push-top-bottom animation-slideDown">
        <i><img src="<?= base_url(); ?>public/img/icon57.png" width="35" height="35"></img></i> <strong>CHD I DTRAK System!</strong>
    </h1>

    <div class="block animation-fadeInQuickInv">
        <div class="block-title">
            <h2>Please Login</h2>
        </div>

        <?php if(session()->getTempdata('error')): ?>
        <div class="error-box">
            <h5 class="error-message"><b><?= session()->getTempdata('error') ?></b></h5>
        </div>
        <?php endif; ?>   

        <form id="form_data" class="form-horizontal" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="form-group">
                <div class="col-xs-12">
                    <input type="text" id="username" name="username" class="form-control" placeholder="Username.." value='<?= set_value('username'); ?>'>
                    <span class="text-danger form-error"><b><?= error_form($username, 'username'); ?><b></span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password..">
                    <span class="text-danger form-error"><b><?= error_form($password, 'password'); ?><b></span>
                </div>
            </div>
            <div class="form-group form-actions">
               
                <div class="col-xs-12 text-right">
                    <button type="submit" class="btn btn-effect-ripple btn-sm btn-secondary"><i class="hi hi-hand-right"></i> Login</button>
                </div>

            </div>
        </form>

    </div>

    <footer class="text-muted text-center animation-pullUp">
        <small><span id="year-copy"></span> &copy; <a href="https://ro1.doh.gov.ph" target="_blank">DOH CHD Ilocos</a> version 2.0</small>
    </footer>
</div>

<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
<script>

    $(document).ready(function() {
        $('#form_data').on('submit', function() {
            // Show overlay on form submission
            $('#overlay').show();
            
        });
        
        // Optional: Hide overlay and enable form elements on form error
        <?php if (isset($error)) : ?>
            $('#overlay').hide();
        <?php endif; ?>
    });

</script>

<?= $this->endSection(); ?>

