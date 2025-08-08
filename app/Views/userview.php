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
        <div class="col-md-5">
            <div class="block full">
                <div class="block-title">
                    <h2><?= $sub_head; ?> - <?= $customlib?></h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-condensed table-vcenter table-bordered table-sm" id="inbox-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;font-size: 10px;text-align:center;">Username</th>
                                <th style="width: 8%;font-size: 10px;text-align:center;">Email Address</th>
                                <th style="width: 8%;font-size: 10px;text-align:center;">Status</th>
                                <th style="width: 8%;font-size: 10px;text-align:center;">Mobile number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($users)): ?>
                                <?php foreach($users as $user): ?>
                                <tr>
                                    <td><?= $user->username; ?></td>
                                    <td><?= $user->user_email; ?></td>
                                    <td><?= $user->user_status; ?></td> 
                                    <td><?= $user->mobile_num; ?></td>            
                                </tr>
                                
                                <?php endforeach; ?>

                            <?php else: ?>
                                <tr><td colspan="4"><h5> Sorry! No Records Found! </h5></td></tr>

                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>

                Start your creative project!
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

