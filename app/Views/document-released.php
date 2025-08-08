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
        <h2>List of Documents released, forwarded, and returned that are not yet receieved</h2>
    </div>

    <div class="block-content-full table-responsive">
        <table class="table table-striped table-condensed table-vcenter table-bordered table-sm remove-margin" id="released-table">
            <thead>
                <tr>
                    <th style="width: 10%;font-size: 10px;text-align:center;">DOCUMET CONTROL No.</th>
                    <th style="width: 20%;font-size: 10px;text-align:center;">Subject</th>
                    <th style="width: 12%;font-size: 10px;text-align:center;">Remarks</th>
                    <th style="width: 9%;font-size: 10px;text-align:center;">Document Type</th>
                    <th style="width: 8%;font-size: 10px;text-align:center;">Action Done</th>
                    <th style="width: 9%;font-size: 10px;text-align:center;">Destination</th>
                    <th style="width: 8%;font-size: 10px;text-align:center;">Action Require</th>
                    <th style="width: 10%;font-size: 10px;text-align:center;">Date/Time Released</th>
                    <th style="width: 14%;font-size: 10px;text-align:center;">Action</th>
                </tr>
            </thead>
        </table>
    </div>

</div>


<div id="destination-modal-change" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Change Destination</strong></h3>
            </div>

            <div class="modal-body">
                <form id="changeDestinationForm" method="post" class="form-horizontal">
                <?= csrf_field() ?>
                <input type="hidden" name="dd" id="dd">
                <table class="table table-vcenter table-bordered table-striped table-condensed">
                    <tbody>
                        <tr>
                            <td>Destination Office</td>
                            <td>
                                <div class="change_office_destination">
                                    <select class="select-select2" style="width: 100%;" id="change_office_destination" name="change_office_destination" data-placeholder="Choose Office Destination">
                                        <option value="">Select Office Destination</option>
                                    </select>
                                    <span class="help-block change_office_destinationMessage"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Action Officer</td>
                            <td>
                                <div class="change_action_officer">
                                    <select class="select-select2" style="width: 100%;" id="change_action_officer" name="change_action_officer" data-placeholder="Select Action Officer">
                                        <option value=""></option>
                                    </select>
                                    <span class="help-block change_action_officerMessage"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Action Required</td>
                            <td>
                                <div class="change_action_required">
                                    <select class="select-select2" style="width: 100%;" id="change_action_required" name="change_action_required" data-placeholder="Select Action Required">
                                        <option value=""></option>
                                    </select>
                                    <span class="help-block change_action_requiredMessage"></span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-effect-ripple btn-primary" id="submitChangeDestination">Save</button>
                <button type="button" class="btn btn-effect-ripple btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div id="destination-modal-add" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Add Destination</strong></h3>
            </div>

            <div class="modal-body">
                <form id="addDestinationForm" method="post" class="form-horizontal">
                <?= csrf_field() ?>
                <input type="hidden" name="dda" id="dda">
                <table class="table table-vcenter table-bordered table-striped table-condensed">
                    <tbody>
                        <tr>
                            <td>Destination Office</td>
                            <td>
                                <div class="add_office_destination">
                                    <select class="select-select2" style="width: 100%;" id="add_office_destination" name="add_office_destination">
                                        <option value="">Select Office Destination</option>
                                    </select>
                                    <span class="help-block add_office_destinationMessage"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Action Officer</td>
                            <td>
                                <div class="add_action_officer">
                                    <select class="select-select2" style="width: 100%;" id="add_action_officer" name="add_action_officer">
                                        <option value=""></option>
                                    </select>
                                    <span class="help-block add_action_officerMessage"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Action Required</td>
                            <td>
                                <div class="add_action_required">
                                    <select class="select-select2" style="width: 100%;" id="add_action_required" name="add_action_required">
                                        <option value=""></option>
                                    </select>
                                    <span class="help-block add_action_requiredMessage"></span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-effect-ripple btn-primary" id="submitAddDestination">Save</button>
                <button type="button" class="btn btn-effect-ripple btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
<script>
    var baseUrl = '<?= base_url(); ?>';
</script>

<script src="<?= base_url(); ?>public/js/pages/forms.Functions.js"></script>
<script src="<?= base_url(); ?>public/js/pages/table.Functions.js"></script>

<script>

$(document).ready(function(){
    UiTables.init(base_url,csrfToken);
    receiveApp.init(base_url);

    $('.select-select2').trigger("change.select2");

});


</script>
<?= $this->endSection(); ?>

