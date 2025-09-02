<?= $this->extend("layouts/base_incoming"); ?>
<?= $this->section("content"); ?>
<div id="overlay">
    <div class="loader"></div>
</div>
<div class="block overflow-hidden">
    <div class="block-title clearfix">
        <div class="block-options pull-left">
            <a href="javascript:void(0)" class="btn btn-effect-ripple btn-primary" id="bulkReceive">
                <i class="fa fa-folder-open"></i> Bulk Receive
            </a>
        </div>
    </div>
    <div class="block-content-full table-responsive">
        <table class="table table-striped table-condensed table-vcenter table-bordered table-sm remove-margin" id="receive-table">
            <thead>
                <tr>
                    <th style="width:2%;font-size:10px;text-align:center;vertical-align:middle;"></th>
                    <th style="width:10%;font-size:10px;text-align:center;vertical-align:middle;">DOCUMENT CONTROL No.</th>
                    <th style="width:6%;font-size:10px;text-align:center;vertical-align:middle;">Originating Office</th>
                    <th style="width:6%;font-size:10px;text-align:center;vertical-align:middle;">Previous Office</th>
                    <th style="width:22%;font-size:10px;text-align:center;vertical-align:middle;">Subject</th>
                    <th style="width:15%;font-size:10px;text-align:center;vertical-align:middle;">Remarks</th>
                    <th style="width:7%;font-size:10px;text-align:center;vertical-align:middle;">Document Type</th>
                    <th style="width:6%;font-size:10px;text-align:center;vertical-align:middle;">Date/Time Log</th>
                    <th style="width:12%;font-size:10px;text-align:center;vertical-align:middle;">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<!-- MODALS -->
<!-- RECEIVE BUTTON -->
<div id="receive-modal" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Receive</strong></h3>
            </div>
            <div class="modal-body">
                <form id="receive-form" method="post" class="form-horizontal">
                    <?= csrf_field() ?>
                    <table class="table table-striped table-condensed table-vcenter table-bordered table-sm">
                        <tbody>
                            <tr>
                                <td style="width:40%;font-size:10px;">Route No.</td>
                                <td style="width:60%;font-size:12px;"><strong id="routeno"></strong></td>
                            </tr>
                            <tr>
                                <td style="width:40%;font-size:10px;">Document Control No.</td>
                                <td style="width:60%;font-size:12px;"><strong id="controlno"></strong></td>
                            </tr>
                            <tr>
                                <td style="width:40%;font-size:10px;">Subject</td>
                                <td style="width:60%;font-size:12px;"><strong id="subject"></strong></td>
                            </tr>
                            <tr>
                                <td style="width:40%;font-size:10px;">Document Type</td>
                                <td style="width:60%;font-size:12px;"><strong id="doctype"></strong></td>
                            </tr>
                            <tr>
                                <td style="width:40%;font-size:10px;">Internal Originating Office</td>
                                <td style="width:60%;font-size:12px;"><strong id="origoffice"></strong></td>
                            </tr>
                            <tr>
                                <td style="width:40%;font-size:10px;">Internal Originating Employee</td>
                                <td style="width:60%;font-size:12px;"><strong id="origemp"></strong></td>
                            </tr>
                            <tr>
                                <td style="width:40%;font-size:10px;">Originating Office (External)</td>
                                <td style="width:60%;font-size:12px;"><strong id="exofficecode"></strong></td>
                            </tr>
                            <tr>
                                <td style="width:40%;font-size:10px;">Originating Employee (External)</td>
                                <td style="width:60%;font-size:12px;"><strong id="exempname"></strong></td>
                            </tr>
                            <tr>
                                <td style="width:40%;font-size:10px;">Previous Office</td>
                                <td style="width:60%;font-size:12px;"><strong id="prevoffice"></strong></td>
                            </tr>
                            <tr>
                                <td style="width:40%;font-size:10px;">Page Count</td>
                                <td style="width:60%;font-size:12px;"><strong id="pageno"></strong></td>
                            </tr>
                            <tr>
                                <td style="width:40%;font-size:10px;">Attachment</td>
                                <td style="width:60%;font-size:12px;" id="attachment"></td>
                            </tr>
                            <tr>
                                <td style="width:40%;font-size:10px;">Remarks</td>
                                <td style="width:60%;font-size:12px;"><strong id="remarks"></strong></td>
                            </tr>
                            <tr>
                                <td style="width:40%;font-size:10px;">Received by</td>
                                <td style="width:60%;font-size:12px;"><strong id="emp"></strong></td>
                            </tr>
                            <tr>
                                <td style="width:40%;font-size:10px;color:red;">Received date (*)</td>
                                <td style="width:60%;font-size:12px;">
                                    <input type="text" id="daterec" name="daterec" class="form-control input-datepicker input-sm" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" disabled>
                                    <input type="hidden" name="detailno" id="detailno">
                                </td>
                            </tr>
                            <tr>
                                <td style="width:40%;font-size:10px;color:red;">Received time (*)</td>
                                <td style="width:60%;font-size:12px;">
                                    <input type="time" id="timerec" name="timerec" class="form-control input-sm" disabled>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="form-group form-actions">
                        <div class="col-md-8 col-md-offset-3">
                            <button type="submit" class="btn btn-effect-ripple btn-primary">Submit</button>
                            <button type="reset" class="btn btn-effect-ripple btn-danger">Close</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<!-- BULK RECEIVE MODAL -->
<div id="viewReceiveData" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Receive</strong></h3>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-condensed table-vcenter table-bordered table-sm">
                    <thead>
                        <tr>
                            <th style="width:15%;font-size:10px;text-align:center;">Document No.</th>
                            <th style="width:15%;font-size:10px;text-align:center;">Originating Office</th>
                            <th style="width:50%;font-size:10px;text-align:center;">Subject</th>
                            <th style="width:20%;font-size:10px;text-align:center;">Doctype</th>
                        </tr>
                    </thead>
                    <tbody id="selectedForReceive"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-effect-ripple btn-primary" id="submitBulkReceive">Receive Documents</button>
                <button type="reset" class="btn btn-effect-ripple btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
<?= $this->section("script"); ?>
<script src="<?= base_url(); ?>js/pages/incoming.Receiving.Function.js"></script>
<script src="<?= base_url(); ?>js/pages/incoming.Receiving.Table.js"></script>
<script type="text/javascript">
    setTimeout(function() {
        $('.error-message').html("");
        $('.form-error').html("");
        $('.error-box').hide();
    }, 3000);
    $(document).ready(function(){
        UiTables.init();
        receiveApp.init(base_url);
    });
</script>
<?= $this->endSection(); ?>