<!-- Login Container -->
<?= $this->extend("layouts/base_incoming"); ?>

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


<div class="block overflow-hidden">
    <div class="block-title clearfix">
        <h2>List of Documents tag as <b>DONE</b></h2>
    </div>

    <div class="block-content-full table-responsive">
        <table class="table table-striped table-condensed table-vcenter table-bordered table-sm remove-margin" id="undone-table">
            <thead>
                <tr>
                    <th style="width: 13%;font-size: 10px;text-align:center;">DOCUMET CONTROL No.</th>
                    <th style="width: 6%;font-size: 10px;text-align:center;">Originating Office</th>
                    <th style="width: 6%;font-size: 10px;text-align:center;">Previous Office</th>
                    <th style="width: 32%;font-size: 10px;text-align:center;">Subject</th>
                    <th style="width: 10%;font-size: 10px;text-align:center;">Remarks</th>
                    <th style="width: 7;font-size: 10px;text-align:center;">Document Type</th>
                    <th style="width: 10%;font-size: 10px;text-align:center;">Date/Time Received</th>
                    <th style="width: 14%;font-size: 10px;text-align:center;">Action</th>
                </tr>
            </thead>
        </table>
    </div>

</div>

<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
<script>
    var baseUrl = '<?= base_url(); ?>';
</script>

<script src="<?= base_url(); ?>js/pages/forms.Functions.js"></script>
<script src="<?= base_url(); ?>js/pages/table.Functions.js"></script>

<script>

$(document).ready(function(){
    UiTables.init(base_url,csrfToken);
    receiveApp.init(base_url);

    $('.select-select2').trigger("change.select2");

});


</script>
<?= $this->endSection(); ?>

