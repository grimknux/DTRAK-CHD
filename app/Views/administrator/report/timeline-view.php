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
                <h2>Document Timeline</h2>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    <form class="form-horizontal" onsubmit="return false;" id="timeline_form_report">
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="example-if-email">Office </label>
                             <div class="col-md-8">
                                <select name="office" id="office" style="width: 100%;" class="select-select2 form-control" data-placeholder="Select office">
                                    <option value=""></option>                     
                                    <?php foreach ($getOffice as $office): ?>

                                        <option value="<?= $office['officecode'] ?>">
                                            <?= $office['officename'] . " (" . $office['shortname'] . ")" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="example-if-email">Document Type: </label>
                            <div class="col-md-8">
                                <select name="document_type" id="document_type" style="width: 100%;" class="select-select2 form-control" data-placeholder="Select document type">
                                    <option value=""></option>                     
                                    <?php foreach ($getDocType as $doctype): ?>

                                        <option value="<?= $doctype['type_code'] ?>">
                                            <?= $doctype['type_desc'] ?>
                                        </option>
                                    <?php endforeach; ?>                  
                                   
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="example-if-password">Document Status: </label>
                            <div class="col-md-8">
                                <select name="document_status" id="document_status" style="width: 100%;" class="select-select2 form-control" data-placeholder="Select document status">
                                    <option value='all'>All Documents</option>
                                    <option value='done'>All Done Documents</option>                
                                    <option value='ongoing'>All Ongoing Documents</option>
                                </select>
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
            <button class="btn btn-effect-ripple btn-secondary" onclick="printTable('#adminreport-timeline-table')"><i class="fa fa-print"></i> Print Report</button>
            <br>
            <div class="table-responsive">
                <table style="width: 100%" class="table table-striped table-condensed table-vcenter table-bordered table-sm " id="adminreport-timeline-table">
                    <thead>
                        <tr>
                            <th style="text-align:center;" colspan="13"><strong style="font-size: 2em;">Document Timeline</strong> <br>  <div id="table_office"></div> <div style="font-size: 10px;" id="table_report_date"></div></th>
                        </tr>
                        <tr>
                            <th rowspan="2" style="width: 3%;font-size: 10px;text-align:center;"></th>
                            <th rowspan="2" style="width: 8%;font-size: 10px;text-align:center;">Document Control No.</th>
                            <th rowspan="2" style="width: 8%;font-size: 10px;text-align:center;">Originating Office.</th>
                            <th rowspan="2" style="width: 8%;font-size: 10px;text-align:center;">Last Office</th>
                            <th rowspan="2" style="width: 22%;font-size: 10px;text-align:center;">Subject</th>
                            <th rowspan="2" style="width: 8%;font-size: 10px;text-align:center;">Document Type</th>
                            <th colspan="3" style="font-size: 10px;text-align:center;">Received to Received</th>
                            <th colspan="3" style="font-size: 10px;text-align:center;">Received to Release</th>
                            <th rowspan="2" style="width: 13%;font-size: 10px;text-align:center;">Remarks</th>
                        </tr>
                        <tr>
                            <th style="width: 5%;font-size: 10px;text-align:center;">Day/s</th>
                            <th style="width: 5%;font-size: 10px;text-align:center;">Hour/s</th>
                            <th style="width: 5%;font-size: 10px;text-align:center;">Minutes/s</th>
                            <th style="width: 5%;font-size: 10px;text-align:center;">Day/s</th>
                            <th style="width: 5%;font-size: 10px;text-align:center;">Hour/s</th>
                            <th style="width: 5%;font-size: 10px;text-align:center;">Minute/s</th>
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

<script src="<?= base_url(); ?>public/js/pages/report.Timeline.Table.js"></script>

<script>

$(document).ready(function(){

    UiTables.init(base_url,csrfToken);
    
    $('#toggleLink_edit').click(function() {
        $('#formGroup_edit').slideToggle();
    });

    $('#toggleLink').click(function() {
        $('#formGroup').slideToggle();
    });


});

</script>
<?= $this->endSection(); ?>

