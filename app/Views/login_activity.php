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
    <div class="block full">
        <div class="block-title">
            <h2><?= $sub_head; ?></h2>
        </div>
        <div class="row">
            <div class="col-sm-4"> 
                <table class="table table-striped table-borderless table-vcenter">
                    <tbody>
                        <tr>
                            <td style="width: 30%;">
                                <strong>Username:</strong>
                            </td>
                            <td><?= $userdata->username; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Email Address:</strong>
                            </td>
                            <td><?= $userdata->user_email; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Mobile Number:</strong>
                            </td>
                            <td><?= $userdata->mobile_num; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-8"> 
                <table class="table table-striped table-bordered table-vcenter">
                    <thead>
                        <tr>
                            <td>
                                <strong>ID</strong>
                            </td>
                            <td>
                                <strong>Unique ID</strong>
                            </td>
                            <td>
                                <strong>Agent</strong>
                            </td>
                            <td>
                                <strong>IP Address</strong>
                            </td>
                            <td>
                                <strong>Login Time</strong>
                            </td>
                            <td>
                                <strong>Logout Time</strong>
                            </td>
                        
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(count($login_info) > 0): ?>
                            <?php foreach($login_info as $loginfo): ?>
                            <tr>
                                <td>
                                <?= $loginfo->id; ?>
                                </td>
                                <td>
                                <?= $loginfo->uniid; ?>
                                </td>
                                <td>
                                <?= $loginfo->agent; ?>
                                </td>
                                <td>
                                <?= $loginfo->ip; ?>
                                </td>
                                <td>
                                <?= date("l d M Y h:i:s a", strtotime($loginfo->login_time)); ?>
                                </td>
                                <td>
                                <?php if($loginfo->logout_time != '0000-00-00 00:00:00'): ?>
                                        <?= date("l d M Y h:i:s a", strtotime($loginfo->logout_time)); ?>
                                    <?php else: ?>
                                        <em>Current Session</em>
                                <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan='4'>No Data Found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

