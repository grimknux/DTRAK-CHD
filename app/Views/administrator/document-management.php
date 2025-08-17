<!-- Login Container -->
<?= $this->extend("layouts/base"); ?>

<?= $this->section("content"); ?>

<div id="overlay">
    <div class="loader"></div>
</div>
<style>
    .themed-background-danger {
        background-color: #b71c1c !important; /* dark red */
        color: #fff !important; /* white text */
    }

    .themed-background-danger-light {
        background-color: #f8d7da !important; /* light red */
        color: #721c24 !important; /* dark red text */
    }
</style>
<script>
    setTimeout(function() {
        $('.error-message').html("");
        $('.form-error').html("");
        $('.error-box').hide();
    }, 3000);
</script>
<div id="overlay">
    <div class="loader"></div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="block full">
            <div class="block-title themed-background-dark text-light">
                <h2>Document Management</h2>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    <form class="form-horizontal" onsubmit="return false;" id="document_management_form">
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="example-if-email">Route No.; </label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="route_no" id="route_no" placeholder="Route No.">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="example-if-email">Document Control No.: </label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="control_no" id="control_no" placeholder="Document Control No.">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="example-if-email">Subject: </label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="subject" id="subject" placeholder="Subject">
                            </div>
                        </div>
                        <div class="form-group form-actions">
                            <div class="col-md-9 col-md-offset-3">
                                <button type="submit" class="btn btn-effect-ripple btn-primary">Submit</button>
                                <button id="resetFilters" type="button" class="btn btn-effect-ripple btn-danger">Reset</button>
                                <button id="cancelSearch" style="display:none;" type="reset" class="btn btn-effect-ripple btn-warning">Cancel Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <br>
            <div class="table-responsive ">
                <table class="table table-striped table-condensed table-vcenter table-bordered table-sm" id="document-management-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;font-size: 10px;text-align:center;">Action</th>
                            <th style="width: 6%;font-size: 10px;text-align:center;">Route Number</th>
                            <th style="width: 8%;font-size: 10px;text-align:center;">Document No.</th>
                            <th style="width: 8%;font-size: 10px;text-align:center;">Document Reference No.</th>
                            <th style="width: 18%;font-size: 10px;text-align:center;">Subject</th>
                            <th style="width: 8%;font-size: 10px;text-align:center;">Document Type</th>
                            <th style="width: 8%;font-size: 10px;text-align:center;">Internal Originating Office</th>
                            <th style="width: 10%;font-size: 10px;text-align:center;">Entry by</th>
                            <th style="width: 4%;font-size: 10px;text-align:center;">Page Count</th>
                            <th style="width: 8%;font-size: 10px;text-align:center;">Attached Documents</th>
                            <th style="width: 8%;font-size: 10px;text-align:center;">Remarks</th>
                            <th style="width: 6%;font-size: 10px;text-align:center;">Status</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>
</div>


<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
<script>
    var baseUrl = '<?= base_url(); ?>';
</script>

<script src="<?= base_url(); ?>public/js/pages/admin.DocMgmt.Table.js"></script>
<script src="<?= base_url(); ?>public/js/pages/adminDocMgmt.Function.js"></script>

<script>

$(document).ready(function(){
    
    $('#toggleLink_edit').click(function() {
        $('#formGroup_edit').slideToggle();
    });

    $('#toggleLink').click(function() {
        $('#formGroup').slideToggle();
    });

    UiTables.init(base_url,csrfToken);

    adminDocManagement.init();

});

</script>
<?= $this->endSection(); ?>

