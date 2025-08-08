<?php
    $session_page = CodeIgniter\Config\Services::session();
?>

<?= $this->extend("layouts/base"); ?>

<?= $this->section("content"); ?>

<script>
    setTimeout(function() {
        $('#hidemsg').hide();
    }, 3000);
</script>

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
        <div class="col-md-8">
            <div class="block full">
                <div class="block-title">
                    <h2><?= $sub_head; ?></h2>
                </div>

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

                    <div class="alert alert-danger  alert-dismissable" id="hidemsg">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><strong>Error</strong></h4>
                        <p><?= $session_page->getTempdata('error'); ?></p>
                    </div>
                <?php endif; ?>
                
                <form class="form-horizontal form-bordered" id="contact" method="post" accept-charset="utf-8">
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="username">Username</label>
                        <div class="col-md-9">
                            <input type="text" id="uname" name="uname" class="form-control" value='<?= set_value('uname') ?>'>
                            <span class="text-danger" id="erruname"><?= display_error($validation, 'uname'); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="email">Email</label>
                        <div class="col-md-9">
                            <input type="text" id="email" name="email" class="form-control" value='<?= set_value('email') ?>'>
                            <span class="text-danger" id="erremail"><?= display_error($validation, 'email'); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="mobile">Mobile</label>
                        <div class="col-md-9">
                            <input type="text" id="mobile" name="mobile" class="form-control" value='<?= set_value('mobile') ?>'>
                            <span class="text-danger" id="errmobile"><?= display_error($validation, 'mobile'); ?></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="mobile">Message</label>
                        <div class="col-md-9">
                            <textarea id="msg" name="msg" class="form-control"><?= set_value('msg') ?></textarea>
                            <span class="text-danger" id="errmobile"><?= display_error($validation, 'msg'); ?></span>
                        </div>
                    </div>

                    <div class="form-group form-actions">
                        <div class="col-md-9 col-md-offset-3">
                            <button type="submit" class="btn btn-effect-ripple btn-primary">Submit</button>
                            <button type="reset" class="btn btn-effect-ripple btn-danger">Reset</button>
                        </div>
                    </div>
                    Start your creative project!

                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

