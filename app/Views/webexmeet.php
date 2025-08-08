<?= $this->extend("layouts/base"); ?>

<?= $this->section("content"); ?>

<div id="overlay">
    <div class="loader"></div>
</div>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><?= $page_heading ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <span data-feather="calendar"></span>This week
        </button>
    </div>
    </div>

    <h3>Scheduled Meetings</h3>
        <div class="table-responsive">
            <table class="table table-striped table-md" id="list_table" style="width: 100%;">
                
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Date Start</th>
                        <th>Date End</th>
                        <th>Requested by</th>
                        <th>Recurrence</th>
                        <th>Password</th>
                        <th>Action</th>
                    </tr>
                    </thead>

            </table>
        </div>
</main>

<?= $this->endSection(); ?>

<?= $this->section("script"); ?>

<script>

</script>

<?= $this->endSection(); ?>