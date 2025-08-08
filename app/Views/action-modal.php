<!-- MODALS -->
<!-- ACTION BUTTON -->
<div id="action-modal" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Action Taken</strong></h3>
            </div>
            <div class="modal-body">
                <form id="actionForm" method="post" enctype="multipart/form-data" class="form-horizontal">
                    <?= csrf_field() ?>
                    <table class="table table-striped table-condensed table-vcenter table-bordered table-sm" id="actionFormTbl">
                        <tbody>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Route No.</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="routeno"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Document Control No.</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="dcon"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Subject</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="subject"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Document Type</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="doctype"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Internal Originating Office</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="origoffice"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Internal Originating Employee</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="origemp"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Originating Office (External)</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="exofficecode"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Originating Employee (External)</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="exempname"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Previous Office</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="prevoffice"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Page Count</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="pageno"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Attachment</td>
                                <td style="width: 60%;font-size: 12px;" id="attachment"></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">User</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="userid"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Remarks</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="remarks"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Action by</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="emp"></strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Actions Taken (*)</td>
                                <td style="width: 60%;font-size: 12px;">
                                <div class="form-group">
                                <div class="col-sm-12"> 
                                    <select name="act_taken" style="width: 100%;" id="act_taken" class="form-control select-select2">
                                    </select>
                                </div>
                                </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Action date (*)</td>
                                <td style="width: 60%;font-size: 12px;">
                                    <input type="text" id="dateact" name="dateact" class="form-control input-datepicker input-sm" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" disabled>
                                    <input type="hidden" name="detailno" id="detailno">   
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Action time (*)</td>
                                <td style="width: 60%;font-size: 12px;">
                                <input type="time" id="timeact" name="timeact" class="form-control input-sm" disabled>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; ">Filing Destination?</td>
                                <td style="width: 60%;font-size: 12px;">
                                    <div class="form-group">
                                        <div class="col-sm-12"> 
                                            <label class="switch switch-info" for="filedes">
                                                <input type="checkbox" id="filedes" name="filedes" value="1" onChange="javascript:enableRem();">
                                                <span data-toggle="tooltip" title="Final Destination?"></span>
                                            </label>
                                            <em>
                                                <h5 class="text-danger">Please check (✓) this ONLY if the Document Destination will end here in your office.</h5>
                                            </em>
                                        </div>    
                                    </div>
                                </td>
                            </tr>
                            <div id="actionremarks"></div>

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


<!-- FORWARD BUTTON -->
<div id="forward-modal" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Forward Document</strong></h3>
            </div>
            <div class="modal-body">
                <form id="forwardForm" method="post" enctype="multipart/form-data" class="form-horizontal">
                    <?= csrf_field() ?>
                    <table class="table table-striped table-condensed table-vcenter table-bordered table-sm" id="forwardFormTbl">
                        <tbody>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Route No.</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="fwd_routeno"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Document Control No.</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="fwd_dcon"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Subject</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="fwd_subject"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Document Type</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="fwd_doctype"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Internal Originating Office</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="fwd_origoffice"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Internal Originating Employee</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="fwd_origemp"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Originating Office (External)</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="fwd_exofficecode"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Originating Employee (External)</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="fwd_exempname"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Previous Office</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="fwd_prevoffice"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Page Count</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="fwd_pageno"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Attachment</td>
                                <td style="width: 60%;font-size: 12px;" id="fwd_attachment"></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">User</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="fwd_userid"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Remarks</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="fwd_remarks"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Forwarded by</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="fwd_emp"></strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Forward Destination Office (*)</td>
                                <td style="width: 60%;font-size: 12px;" class="fwd_destination">
                                    <select name="fwd_destination" style="width: 100%;" id="fwd_destination" class="form-control select-select2">
                                    </select>
                                    <span class="help-block fwd_destinationMessage"></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Forward Destination Employee (*)</td>
                                <td style="width: 60%;font-size: 12px;" class="fwd_destemp">
                                    <select name="fwd_destemp" style="width: 100%;" id="fwd_destemp" class="form-control select-select2">
                                        <option value="">Please select Destination Employee</option>
                                    </select>
                                    <span class="help-block fwd_destempMessage has-error"></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Action Required (*)</td>
                                <td style="width: 60%;font-size: 12px;" class="fwd_actionrequire">
                                    <select name="fwd_actionrequire" style="width: 100%;" id="fwd_actionrequire" class="form-control select-select2">
                                        <option value="">Please select Action Required</option>
                                    </select>
                                    <span class="help-block fwd_actionrequireMessage"></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Forward Remarks</td>
                                <td style="width: 60%;font-size: 12px;">
                                    <textarea class="form-control" name="fwd_fwdremarks" id="fwd_fwdremarks"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Forward date (*)</td>
                                <td style="width: 60%;font-size: 12px;">
                                    <input type="text" id="datefwd" name="datefwd" class="form-control input-datepicker input-sm" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" disabled>
                                    <input type="hidden" name="fwd_detailno" id="fwd_detailno">   
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Forward time (*)</td>
                                <td style="width: 60%;font-size: 12px;">
                                <input type="time" id="timefwd" name="timefwd" class="form-control input-sm" disabled>
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



<!-- RETURN BUTTON -->
<div id="return-modal" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Return Document</strong></h3>
            </div>
            <div class="modal-body">
                <form id="returnForm" method="post" enctype="multipart/form-data" class="form-horizontal">
                    <?= csrf_field() ?>
                    <table class="table table-striped table-condensed table-vcenter table-bordered table-sm" id="returnFormTbl">
                        <tbody>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Route No.</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="ret_routeno"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Document Control No.</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="ret_dcon"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Subject</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="ret_subject"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Document Type</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="ret_doctype"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Internal Originating Office</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="ret_origoffice"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Internal Originating Employee</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="ret_origemp"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Originating Office (External)</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="ret_exofficecode"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Originating Employee (External)</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="ret_exempname"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Previous Office</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="ret_prevoffice"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Page Count</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="ret_pageno"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Attachment</td>
                                <td style="width: 60%;font-size: 12px;" id="ret_attachment"></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">User</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="ret_userid"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Remarks</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="ret_remarks"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Return Destination Office (*)</td>
                                <td style="width: 60%;font-size: 12px;" class="ret_destination"><strong id="ret_destination"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Return Destination Employee (*)</td>
                                <td style="width: 60%;font-size: 12px;" class="ret_destemp">
                                    <select name="ret_destemp" style="width: 100%;" id="ret_destemp" class="form-control select-select2">
                                        <option value="">Please select Destination Employee</option>
                                    </select>
                                    <span class="help-block ret_destempMessage has-error"></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Action Required (*)</td>
                                <td style="width: 60%;font-size: 12px;" class="ret_actionrequire">
                                    <select name="ret_actionrequire" style="width: 100%;" id="ret_actionrequire" class="form-control select-select2">
                                        <option value="">Please select Action Required</option>
                                    </select>
                                    <span class="help-block ret_actionrequireMessage"></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Return Reason/Remarks</td>
                                <td style="width: 60%;font-size: 12px;">
                                    <textarea class="form-control" name="ret_retremarks" id="ret_retremarks"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Forwarded by</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="ret_emp"></strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Forward date (*)</td>
                                <td style="width: 60%;font-size: 12px;">
                                    <input type="text" id="dateret" name="dateret" class="form-control input-datepicker input-sm" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" disabled>
                                    <input type="hidden" name="ret_detailno" id="ret_detailno">   
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px; color: red;">Forward time (*)</td>
                                <td style="width: 60%;font-size: 12px;">
                                <input type="time" id="timeret" name="timeret" class="form-control input-sm" disabled>
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