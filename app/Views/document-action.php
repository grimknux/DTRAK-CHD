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

        <div class="block-options pull-left">
            <a href="javascript:void(0)" class="btn btn-effect-ripple btn-warning" id="bulkAction"><i class="fa fa-folder-open"></i> Bulk Action</a>
        </div>

    </div>

    <div class="block-content-full table-responsive">
        <table class="table table-striped table-condensed table-vcenter table-bordered table-sm remove-margin" id="action-table">
            <thead>
                <tr>
                    <th style="width: 2%;font-size: 10px;text-align:center;"></th>
                    <!--<th style="width: 6%;font-size: 10px;text-align:center;">Attachment</th>-->
                    <th style="width: 13%;font-size: 10px;text-align:center;">DOCUMET CONTROL No.</th>
                    <!--<th style="width: 6%;font-size: 10px;text-align:center;">ROUTE No.</th>-->
                    <th style="width: 6%;font-size: 10px;text-align:center;">Originating Office</th>
                    <th style="width: 6%;font-size: 10px;text-align:center;">Previous Office</th>
                    <th style="width: 23%;font-size: 10px;text-align:center;">Subject</th>
                    <th style="width: 10%;font-size: 10px;text-align:center;">Remarks</th>
                    <th style="width: 7;font-size: 10px;text-align:center;">Document Type</th>
                    <!--<th style="width: 7%;font-size: 10px;text-align:center;">Action Required</th>-->
                    <th style="width: 6%;font-size: 10px;text-align:center;">Date/Time Received</th>
                    <th style="width: 27%;font-size: 10px;text-align:center;">Action</th>
                    <!--<th style="width: 7%;font-size: 10px;text-align:center;">Forward</th>-->
                    <!--<th style="width: 7%;font-size: 10px;text-align:center;">Return</th>-->
                </tr>
            </thead>
        </table>
    </div>

</div>


<?= view('action-modal') ?>

<!-- BULK ACTION MODAL -->
<div id="viewActionData" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Take Action</strong></h3>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-condensed table-vcenter table-bordered table-sm">
                    <thead>
                        <tr>
                            <th style="width: 12%;font-size: 10px; text-align: center">Document No.</th>
                            <th style="width: 13%;font-size: 10px; text-align: center">Originating Office</th>
                            <th style="width: 35%;font-size: 10px; text-align: center">Subject</th>
                            <th style="width: 15%;font-size: 10px; text-align: center">Doctype</th>
                            <th style="width: 30%;font-size: 10px; text-align: center">Action Taken</th>
                        </tr>
                    </thead>
                    <tbody id="selectedForAction">
                        
                    </tbody>
                </table>  
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-effect-ripple btn-primary" id="submitBulkAction">Submit Action!</button>
                    <button type="reset" class="btn btn-effect-ripple btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
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

function enableRem() {
    const filedes = $('#filedes'); // Cache the checkbox selector
    const table = $('#actionFormTbl');
    const remarksRowId = 'remarksRow'; // Unique ID for the row to manage it reliably

    if (filedes.is(':checked')) {
        // Check if the remarks row already exists
        if ($('#' + remarksRowId).length === 0) {
            // Add the row if it doesn't exist
            const newRow = `
                <tr id="${remarksRowId}">
                    <td style="width: 40%; font-size: 10px;">Remarks</td>
                    <td style="width: 60%; font-size: 12px;">
                        <div class="form-group relremarks">
                            <div class="col-md-12">
                                <textarea class="form-control input-sm" id="relremarks" name="relremarks">DONE</textarea>
                                <span class="help-block relremarksMessage"></span>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            table.find('tbody').append(newRow);
        }
    } else {
        // Remove the remarks row if it exists
        $('#' + remarksRowId).remove();
    }
}


</script>
<?= $this->endSection(); ?>

