<!-- Login Container -->
<?= $this->extend("layouts/base"); ?>

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
<div id="overlay">
    <div class="loader"></div>
</div>
<div id="overlay2" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(255,255,255,0.7); z-index:9999; text-align:center;">
    <div style="position: relative; top: 40%; font-size: 24px;">
        <img src="spinner.gif" width="50" alt="Loading..."><br>Loading...
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="block full">
            <div class="block-title themed-background-dark text-light">
                <h2>Released Documents</h2>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    <form class="form-horizontal" onsubmit="return false;" id="released_form_report">
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="example-if-email">Office </label>
                             <div class="col-md-9">
                                <select name="office_emp" id="office_emp" style="width: 100%;" class="select-select2 form-control" data-placeholder="Select Office Destination">                           
                                    <?php foreach ($getEmpOffice as $office): ?>

                                        <option value="<?= $office['officecode'] ?>">
                                            <?= $office['officename'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="example-if-email">Date From: </label>
                            <div class="col-md-9">
                                <input type="date" id="filter_datefrom" name="filter_datefrom" class="form-control" placeholder="Date From">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="example-if-password">Date To: </label>
                            <div class="col-md-9">
                                <input type="date" id="filter_dateto" name="filter_dateto" class="form-control" placeholder="Date To">
                            </div>
                        </div>
                       <div class="form-group form-actions">
                            <div class="col-md-9 col-md-offset-3">
                                <button type="submit" class="btn btn-effect-ripple btn-primary">Submit</button>
                                <button id="resetFilters" type="reset" class="btn btn-effect-ripple btn-danger">Reset</button>
                                <button id="cancelSearch" style="display:none;" type="reset" class="btn btn-effect-ripple btn-warning">Cancel Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <br>
            <button class="btn btn-effect-ripple btn-secondary" onclick="printTable('#report-released-table')"><i class="fa fa-print"></i> Print Report</button>
            <br>
            <div class="table-responsive">
                <table class="table table-striped table-condensed table-vcenter table-bordered table-sm" id="report-released-table">
                    <thead>
                        <tr>
                            <th style="text-align:center;" colspan="12"><strong style="font-size: 2em;">Released and Processed Documents</strong> <br>  <div id="table_office"></div> <div style="font-size: 10px;" id="table_report_date"></div></th>
                        </tr>
                        <tr>
                            <th style="width: 3%;font-size: 10px;text-align:center;"></th>
                            <th style="width: 9%;font-size: 10px;text-align:center;">Document Control No.</th>
                            <th style="width: 6%;font-size: 10px;text-align:center;">Originating Office.</th>
                            <th style="width: 6%;font-size: 10px;text-align:center;">Previous Office</th>
                            <th style="width: 20%;font-size: 10px;text-align:center;">Subject</th>
                            <th style="width: 10%;font-size: 10px;text-align:center;">Document Type</th>
                            <th style="width: 6%;font-size: 10px;text-align:center;">Date/Time Received</th>
                            <th style="width: 8%;font-size: 10px;text-align:center;">Received By</th>
                            <th style="width: 6%;font-size: 10px;text-align:center;">Date/Time Action</th>
                            <th style="width: 8%;font-size: 10px;text-align:center;">Action By</th>
                            <th style="width: 6%;font-size: 10px;text-align:center;">Action Taken</th>
                            <th style="width: 12%;font-size: 10px;text-align:center;">Office Destination/Filed</th>
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

<script src="<?= base_url(); ?>js/pages/report.Released.Table.js"></script>

<script>

$(document).ready(function(){

    UiTables.init(base_url,csrfToken);
    
    $('#toggleLink_edit').click(function() {
        $('#formGroup_edit').slideToggle();
    });

    $('#toggleLink').click(function() {
        $('#formGroup').slideToggle();
    });

    $('#filter_datefrom').on('change', function () {
        const dateFrom = $(this).val();
        const dateTo = $('#filter_dateto').val();

        if (dateTo && dateFrom > dateTo) {
            $('#filter_dateto').val(''); // Clear Date To if invalid
        }

        // Set min attribute on Date To
        $('#filter_dateto').attr('min', dateFrom);
    });

    $('#filter_dateto').on('change', function () {
        const dateFrom = $('#filter_datefrom').val();
        const dateTo = $(this).val();

        if (dateFrom && dateFrom > dateTo) {
            $(this).val(''); // Clear if invalid
        }
    });

});


</script>
<?= $this->endSection(); ?>

