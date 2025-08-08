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

        Start your creative project!
    </div>
</div>
<?= $this->endSection(); ?>

