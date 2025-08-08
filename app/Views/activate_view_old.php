<!-- Login Container -->
<?= $this->extend("layouts/login"); ?>

<?= $this->section("content"); ?>

<img src="<?php echo base_url() ?>public/img/placeholders/layout/login2_full_bg.jpg" alt="Full Background" class="full-bg animation-pulseSlow">

<div id="activation-container">
    <!-- Login Header 
    <h1 class="h2 text-light text-center push-top-bottom animation-slideDown">
        <img src="<?= base_url();?>public/img/dohlogo.png" style="width: 30px; height: 30px;"> <strong></strong>
    </h1>
    END Login Header -->

    <!-- Login Block -->
    <div class="block animation-fadeInQuickInv">

        <!-- Login Title  style="background-color:#a7d9a7"  f9fafc-->
        
        <!-- END Login Title -->

        <?php if(isset($success)): ?>
            <div class="block-title" style="background-color:#35a442">
                <h2 style="color: #eff7f0;">Success</h2>
            </div>
            <div class="well"><h3><?= $success ?></h3> <br></p></div>
        <?php endif; ?>
        <?php if(isset($already)): ?>
            <div class="block-title" style="background-color:#35a442">
                <h2 style="color: #eff7f0;">Success</h2>
            </div>
            <?php
                header('Refresh:3; url= '. base_url('login-page')); 
            ?>
            <div class="well"><h3><?= $already ?></h3> <br><p>You will be redirected in 3 seconds...</p></div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="block-title" style="background-color:#de815c">
                <h2 style="color: #f8f3f1;">Error!</h2>
            </div>
            <div class="well"><h3><?= $error ?></h3> <br></p></div>
        <?php endif; ?>

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