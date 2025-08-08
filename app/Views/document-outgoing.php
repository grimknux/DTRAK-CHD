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
<div class="row">
    <div class="col-lg-12">
        <div class="block full">
            <div class="block-title themed-background-dark text-light">
                <h2>Create Outgoing Document</h2>
            </div>
            <div class="row ">
                <!-- Simple Stats Widgets -->
                <div class="col-sm-3 add-btn">
                    <a href="#" class="widget outgo-modal" data-toggle='modal'>
                        <div class="widget-content widget-content-mini text-right clearfix widget-light">
                            <div class="widget-icon pull-left themed-background-info">
                                <i class="fa fa-plus text-light-op"></i>
                            </div>
                            <h2 class="widget-heading h4 text-info">
                                <strong><span data-toggle="counter" data-to="2835">Add New Document</span></strong>
                            </h2>
                            <span class="text-muted">Create Document Tracking</span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-condensed table-vcenter table-bordered table-sm" id="outgoing-table">
                    <thead>
                        <tr>
                            <th style="width: 10%;font-size: 10px;text-align:center;">Action</th>
                            <th style="width: 6%;font-size: 10px;text-align:center;">Date Released</th>
                            <th style="width: 8%;font-size: 10px;text-align:center;">Document Control No.</th>
                            <th style="width: 8%;font-size: 10px;text-align:center;">Document Reference No.</th>
                            <th style="width: 18%;font-size: 10px;text-align:center;">Subject</th>
                            <th style="width: 6%;font-size: 10px;text-align:center;">Document Type</th>
                            <th style="width: 8;font-size: 10px;text-align:center;">Internal Originating Office</th>
                            <th style="width: 10%;font-size: 10px;text-align:center;">Entry by</th>
                            <th style="width: 4%;font-size: 10px;text-align:center;">Page Count</th>
                            <th style="width: 8%;font-size: 10px;text-align:center;">Attached Documents</th>
                            <th style="width: 8%;font-size: 10px;text-align:center;">Remarks</th>
                            <th style="width: 1%;font-size: 10px;text-align:center;">Attachment</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>
</div>



<div id="outgoing-modal" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Add New Document</strong></h3>
            </div>
            <div class="modal-body">
                <form id="outgoing-form" method="post" class="form-horizontal ">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label input-sm" for="example-text-input">Entry By</label>
                    <div class="col-sm-8">
                        <b><p class="form-control-static" id="entryby"></p></b>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="form-group orig_office">
                    <label class="col-sm-4 control-label input-sm required-label" for="orig_office">Internal Originating Office </label>
                    <div class="col-sm-8">
                        <select class="select-select2" style="width: 100%;" id="orig_office" name="orig_office">
                        </select>
                        <span class="help-block orig_officeMessage"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label input-sm required-label" for="example-text-input">Route No.</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control input-sm" placeholder="AUTO" readonly />
                        <span class="help-block"></span>
                    </div>
                </div>
                
                <div class="form-group doc_type">
                    <label class="col-sm-4 control-label input-sm required-label" for="doc_type">Document Type</label>
                    <div class="col-sm-8">
                        <select class="select-select2" style="width: 100%;" id="doc_type" name="doc_type[]" data-placeholder="Choose Document Type" multiple>
                            <option></option>
                        </select>
                        <span class="help-block doc_typeMessage"></span>
                    </div>
                </div>

                <div class="form-group office_controlno">
                    <label class="col-sm-4 control-label input-sm required-label" for="office_controlno">Office Control No.</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control input-sm control-no" id="office_controlno" name="office_controlno" placeholder="e.g. PTC No., PR Number, Voucher No., etc...">
                        <span class="help-block office_controlnoMessage"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label input-sm" for="docref_controlno">Reference Document Control No.</label>
                    <div class="col-sm-8">
                        <select class="select-select2" style="width: 100%;" id="docref_controlno" name="docref_controlno[]" data-placeholder="Choose Document Reference..." multiple>
                            <option></option>
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="form-group doc_subject">
                    <label class="col-sm-4 control-label input-sm required-label" for="doc_subject">Subject</label>
                    <div class="col-sm-8">
                        <textarea class="form-control input-sm" id="doc_subject" name="doc_subject"></textarea>
                        <span class="help-block doc_subjectMessage"></span>
                    </div>
                </div>
                <div class="form-group doc_attachment">
                    <label class="col-sm-4 control-label input-sm required-label" for="doc_attachment">Attachment (PDF file only)</label>
                    <div class="col-sm-8">
                        <input type="file" class="form-control file" id="doc_attachment" name="doc_attachment" accept=".pdf">
                        <span class="help-block doc_attachmentMessage"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label input-sm" for="attach_docs">Attached Documents</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control input-sm" id="attach_docs" name="attach_docs">
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label input-sm" for="doc_page">No. of Pages</label>
                    <div class="col-sm-3">
                        <input type="number" class="form-control input-sm" id="doc_page" name="doc_page" >
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="form-group doc_source">
                    <label class="col-sm-4 control-label input-sm required-label" for="example-text-input">Source Type</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="doc_source" name="doc_source">
                            <option value="">Please select Source Type</option>
                            <option value="O" selected>Internal</option>
                            <option value="E">External</option>
                        </select>
                        <span class="help-block doc_sourceMessage"></span>
                    </div>
                </div>
                <div id="formGroup" style="display: none;">
                    <div class="form-group">
                        <label class="col-sm-4 control-label input-sm" for="orig_docnoEx">Document Control No. (External)</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control input-sm" id="orig_docnoEx" name="orig_docnoEx">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group orig_officeEx">
                        <label class="col-sm-4 control-label input-sm required-label" for="orig_officeEx">Originating Office (External)</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control input-sm" id="orig_officeEx" name="orig_officeEx">
                            <span class="help-block orig_officeExMessage"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label input-sm" for="orig_empEx">Originating Employee (External)</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control input-sm" id="orig_empEx" name="orig_empEx">
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label input-sm" for="doc_remarks">Remarks</label>
                    <div class="col-sm-8">
                        <textarea class="form-control input-sm" id="doc_remarks" name="doc_remarks"></textarea>
                        <span class="help-block"></span>
                    </div>
                </div>                 
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-effect-ripple btn-primary" id="submitOutgoing">Save</button>
                <button type="button" class="btn btn-effect-ripple btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="outgoing-modal-edit" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Edit Document</strong></h3>
            </div>
            <div class="modal-body">
                <form id="outgoing-form-edit" method="post" class="form-horizontal ">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label input-sm" for="entryby_edit">Entry By</label>
                    <div class="col-sm-8">
                        <b><p class="form-control-static" id="entryby_edit"></p></b>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="form-group orig_office_edit">
                    <label class="col-sm-4 control-label input-sm required-label" for="orig_office_edit">Internal Originating Office </label>
                    <div class="col-sm-8">
                        <select class="select-select2" style="width: 100%;" id="orig_office_edit" name="orig_office_edit">
                        </select>
                        <span class="help-block orig_office_editMessage"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label input-sm required-label" for="example-text-input">Route No.</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control input-sm" placeholder="AUTO" id="route_no_edit" name ="route_no_edit" readonly />
                        <input type="hidden" class="form-control input-sm" placeholder="AUTO" id="route_no_edit_code" readonly />
                        <span class="help-block"></span>
                    </div>
                </div>
                
                <div class="form-group doc_type_edit">
                    <label class="col-sm-4 control-label input-sm required-label" for="doc_type">Document Type</label>
                    <div class="col-sm-8">
                        <select class="select-select2" style="width: 100%;" id="doc_type_edit" name="doc_type_edit[]" data-placeholder="Choose Document Type" multiple>
                            <option></option>
                        </select>
                        <span class="help-block doc_type_editMessage"></span>
                    </div>
                </div>

                <div class="form-group office_controlno_edit">
                    <label class="col-sm-4 control-label input-sm required-label" for="office_controlno_edit">Office Control No.</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control input-sm control-no" id="office_controlno_edit" name="office_controlno_edit" placeholder="e.g. PTC No., PR Number, Voucher No., etc...">
                        <span class="help-block office_controlno_editMessage"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label input-sm" for="docref_controlno_edit">Reference Document Control No.</label>
                    <div class="col-sm-8">
                        <select class="select-select2" style="width: 100%;" id="docref_controlno_edit" name="docref_controlno_edit[]" data-placeholder="Choose Document Reference..." multiple>
                            <option></option>
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="form-group doc_subject_edit">
                    <label class="col-sm-4 control-label input-sm required-label" for="doc_subject_edit">Subject</label>
                    <div class="col-sm-8">
                        <textarea class="form-control input-sm" id="doc_subject_edit" name="doc_subject_edit"></textarea>
                        <span class="help-block doc_subject_editMessage"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label input-sm" for="attach_docs_edit">Attached Documents</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control input-sm" id="attach_docs_edit" name="attach_docs_edit">
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label input-sm" for="doc_page_edit">No. of Pages</label>
                    <div class="col-sm-3">
                        <input type="number" class="form-control input-sm" id="doc_page_edit" name="doc_page_edit" >
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="form-group doc_source_edit">
                    <label class="col-sm-4 control-label input-sm required-label" for="doc_source_edit">Source Type</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="doc_source_edit" name="doc_source_edit">
                            <option value="">Please select Source Type</option>
                            <option value="O" selected>Internal</option>
                            <option value="E">External</option>
                        </select>
                        <span class="help-block doc_source_editMessage"></span>
                    </div>
                </div>
                <div id="formGroup_edit" style="display: none;">
                    <div class="form-group">
                        <label class="col-sm-4 control-label input-sm" for="orig_docnoEx_edit">Document Control No. (External)</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control input-sm" id="orig_docnoEx_edit" name="orig_docnoEx_edit">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label input-sm required-label" for="orig_officeEx_edit">Originating Office (External)</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control input-sm" id="orig_officeEx_edit" name="orig_officeEx_edit">
                            <span class="help-block orig_docnoEx_editMessage"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label input-sm" for="orig_empEx_edit">Originating Employee (External)</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control input-sm" id="orig_empEx_edit" name="orig_empEx_edit">
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label input-sm" for="doc_remarks_edit">Remarks</label>
                    <div class="col-sm-8">
                        <textarea class="form-control input-sm" id="doc_remarks_edit" name="doc_remarks_edit"></textarea>
                        <span class="help-block"></span>
                    </div>
                </div>                 
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-effect-ripple btn-primary" id="submitOutgoingEdit">Save</button>
                <button type="button" class="btn btn-effect-ripple btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div id="outgoing-modal-attachment" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Change/Edit Attachment</strong></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <h5><strong>Document Route Number:</strong></h5>
                        <h4 id="attach_routeno"></h4>

                        <h5><strong>Subject:</strong></h5>
                        <h4 id="attach_subject"></h4>

                        <h5><strong>Document Type:</strong></h5>
                        <h4 id="attach_doctype"></h4>

                        <hr>

                        <form id="change-attach-form" method="post">
                        <?= csrf_field() ?>
                        <div class="form-group attach_attachment">
                            <label class="control-label input-sm" for="attach_attachment">New Attachment/File:</label>
                            <div class="col-sm-12">
                                <input type="file" class="form-control" name="attach_attachment" id="attach_attachment">
                                <input type="hidden" class="form-control" name="attach_routeno_code" id="attach_routeno_code">
                                <span class="help-block attach_attachmentMessage" style="margin-bottom: 10px;"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-effect-ripple btn-primary" id="submitOutgoingAttach">Save</button>
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

<script src="<?= base_url(); ?>public/js/pages/forms.Outgoing.js"></script>
<script src="<?= base_url(); ?>public/js/pages/table.Functions.js"></script>

<script>

$(document).ready(function(){
    
    $('#toggleLink_edit').click(function() {
        $('#formGroup_edit').slideToggle();
    });

    $('#toggleLink').click(function() {
        $('#formGroup').slideToggle();
    });

    UiTables.init(base_url,csrfToken);

    outgoingApp.init(base_url,csrfToken);

});



</script>
<?= $this->endSection(); ?>

