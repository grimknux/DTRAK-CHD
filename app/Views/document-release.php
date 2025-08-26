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
            <a href="javascript:void(0)" class="btn btn-effect-ripple btn-primary" id="bulkRelease"><i class="fa fa-folder-open"></i> Bulk Release</a>
        </div>

    </div>

    <div class="block-content-full table-responsive">
        <table class="table table-striped table-condensed table-vcenter table-bordered table-sm remove-margin" id="release-table">
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


<!-- FORWARD BUTTON -->
<div id="release-modal" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Release Document</strong></h3>
            </div>
            <div class="modal-body">
                <form id="releaseForm" method="post" enctype="multipart/form-data" class="form-horizontal">
                    <?= csrf_field() ?>
                    <table class="table table-striped table-condensed table-vcenter table-bordered table-sm" id="releaseFormTbl">
                        <tbody>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Route No.</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="rel_routeno"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Document Control No.</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="rel_dcon"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Subject</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="rel_subject"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Document Type</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="rel_doctype"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Internal Originating Office</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="rel_origoffice"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Internal Originating Employee</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="rel_origemp"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Originating Office (External)</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="rel_exofficecode"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Originating Employee (External)</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="rel_exempname"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Previous Office</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="rel_prevoffice"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Page Count</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="rel_pageno"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Attachment</td>
                                <td style="width: 60%;font-size: 12px;" id="rel_attachment"></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">User</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="rel_userid"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Remarks</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="rel_remarks"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Forwarded by</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="rel_emp"></strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Release Destination Office (*)</td>
                                <td style="width: 60%;font-size: 12px;" class="rel_destination">
                                    <select name="rel_destination" style="width: 100%;" id="rel_destination" class="form-control select-select2">
                                    </select>
                                    <span class="help-block rel_destinationMessage"></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Release Destination Employee (*)</td>
                                <td style="width: 60%;font-size: 12px;" class="rel_destemp">
                                    <select name="rel_destemp" style="width: 100%;" id="rel_destemp" class="form-control select-select2">
                                        <option value="">Please select Destination Employee</option>
                                    </select>
                                    <span class="help-block rel_destempMessage has-error"></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Action Required (*)</td>
                                <td style="width: 60%;font-size: 12px;" class="rel_actionrequire">
                                    <select name="rel_actionrequire" style="width: 100%;" id="rel_actionrequire" class="form-control select-select2">
                                        <option value="">Please select Action Required</option>
                                    </select>
                                    <span class="help-block rel_actionrequireMessage"></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Release Remarks</td>
                                <td style="width: 60%;font-size: 12px;">
                                    <textarea class="form-control" name="rel_relremarks" id="rel_fwdremarks"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Release date (*)</td>
                                <td style="width: 60%;font-size: 12px;">
                                    <input type="text" id="daterel" name="daterel" class="form-control input-datepicker input-sm" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" disabled>
                                    <input type="hidden" name="rel_detailno" id="rel_detailno">   
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Release time (*)</td>
                                <td style="width: 60%;font-size: 12px;">
                                <input type="time" id="timerel" name="timerel" class="form-control input-sm" disabled>
                                </td>
                            </tr>

                        </tbody>
                    </table> 
                    <div class="form-group form-actions">
                        <div class="col-md-8 col-md-offset-3">
                            <button type="submit" class="btn btn-effect-ripple btn-primary">Submit</button>
                            <button type="reset" class="btn btn-effect-ripple btn-danger">Reset</button>
                        </div>
                    </div>                       
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('release-modal') ?>


<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
<script>
    var baseUrl = '<?= base_url(); ?>';
</script>

<script src="<?= base_url(); ?>js/pages/forms.Functions.js"></script>
<script src="<?= base_url(); ?>js/pages/table.Functions.js"></script>

<script>

var officeDestinations = <?= json_encode($officeDestinations, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
var actionReq = <?= json_encode($actionReq, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

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

