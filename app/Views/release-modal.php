<div id="viewReleaseData" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Bulk Release</strong></h3>
            </div>
            <div class="modal-body">

                <div class="row" style="margin-bottom:5px">
                    <div class="col-sm-6">
                        <form id="bulkReleaseForm">
                            <div class="form-group bulkrel_officedestination" style="font-size: 12px;">
                                <label for="bulkrel_officedestination" style="color: red;">Office Destination (*)</label>
                                <select name="bulkrel_officedestination" style="width: 100%;" id="bulkrel_officedestination" class="form-control select-select2">
                                </select>
                                <span class="help-block bulkrel_officedestinationMessage" ></span>
                            </div>
                            <div class="form-group bulkrel_actionofficer" style="font-size: 12px;">
                                <label for="bulkrel_actionofficer" style="color: red;">Action Officer (*)</label>
                                <select name="bulkrel_actionofficer" style="width: 100%;" id="bulkrel_actionofficer" class="form-control select-select2">
                                </select>
                                <span class="help-block bulkrel_actionofficerMessage"></span>
                            </div>
                            <div class="form-group bulkrel_actionrequire" style="font-size: 12px;">
                                <label for="bulkrel_actionrequire" style="color: red;">Action Required (*)</label>
                                <select name="bulkrel_actionrequire" style="width: 100%;" id="bulkrel_actionrequire" class="form-control select-select2">
                                </select>
                                <span class="help-block bulkrel_actionrequireMessage"></span>
                            </div>
                        </form>
                        
                    </div>
                </div>

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
                    <tbody id="selectedForRelease">
                        
                    </tbody>
                </table>  
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-effect-ripple btn-primary" id="submitBulkRelease">Submit</button>
                    <button type="reset" class="btn btn-effect-ripple btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div id="disseminate-modal-add" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Disseminate Document</strong></h3>
            </div>
            <div class="modal-body">
                <div class="row ">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <form id="disseminateDestinationForm" method="post" >
                                <?= csrf_field() ?>
                                <input type="hidden" name="diss_routeno" id="diss_routeno">
                                <input type="hidden" name="diss_detailno" id="diss_detailno">
                                <div class="form-group">
                                    <label for="diss_remarks">Remarks</label>
                                    <textarea id="diss_remarks" name="diss_remarks" rows="3" class="form-control" placeholder="Description.."></textarea>
                                    <!--<span class="help-block">Please enter your email</span>-->
                                </div>
                                <label for="diss_remarks" style="color: red;">Disseminate to the following Office/s: (*)</label>
                                <table class="table table-vcenter table-bordered table-striped" id="destinationContainer">
                                    <thead>
                                        <tr>
                                            <th style="font-size: 12px" class="text-center">Destination Office</th>
                                            <th style="font-size: 12px" class="text-center">Action Officer</th>
                                            <th style="font-size: 12px" class="text-center">Action Required</th>
                                            <th style="font-size: 12px; width: 80px;" class="text-center"><i class="fa fa-flash"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="destination-group">
                                            <td class="diss_office_destination" style="width: 30%;">
                                                <div class="diss_office_destinationDiv">
                                                    <select name="diss_office_destination[]" id="diss_office_destination" style="width: 100%;" class="select-select2">
                                                        <option value="">Select Office Destination</option>
                                                    </select>
                                                    <span class="help-block diss_office_destinationMessage"></span>

                                                </div>
                                            
                                            </td>
                                            <td class="diss_action_officer" style="width: 30%;">
                                                <div class="diss_action_officerDiv">
                                                    <select name="diss_action_officer[]" id="diss_action_officer" style="width: 100%;" class="select-select2 diss_action_officer ao">
                                                        <option value="">Select Action Officer</option>
                                                    </select>
                                                    <span class="help-block diss_action_officerMessage"></span>
                                                </div>
                                            </td>
                                            <td class="diss_action_required" style="width: 30%;">
                                                <div class="diss_action_requiredDiv">
                                                    <select name="diss_action_required[]" id="diss_action_required" style="width: 100%;" class="select-select2 diss_action_required ar">
                                                        <option value="">Select Action Required</option>
                                                    </select>
                                                    <span class="help-block diss_action_requiredMessage"></span>
                                                </div>
                                            </td>
                                            <td style="width: 10%;" class="text-center">
                                                -
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                </form>
                                <div class="pull-right">
                                    <button class="btn btn-sm btn-primary" id="addDisseminate">Add/Submit Destination</button>
                                    <button class="btn btn-sm btn-danger" id="resetDisseminationForm">Reset</button>
                                </div>
                                    <button class="btn btn-sm btn-success" id="addDisseminationBtn"><i class="fa fa-plus"></i> Add Multiple Destination</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>


<!-- TAG AS DONE MODAL -->
<div id="tagdone-modal" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Tag as Done!</strong></h3>
            </div>
            <div class="modal-body">
                <form id="tagdoneForm" method="post" enctype="multipart/form-data" class="form-horizontal">
                    <?= csrf_field() ?>
                    <table class="table table-striped table-condensed table-vcenter table-bordered table-sm" id="tagdoneFormTbl">
                        <tbody>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Route No.</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="tag_routeno"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Document Control No.</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="tag_controlno"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Subject</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="tag_subject"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Document Type</td>
                                <td style="width: 60%;font-size: 12px;"><strong id="tag_doctype"></strong></td>
                            </tr>
                            <tr>
                                <td style="width: 40%;font-size: 10px;">Done Remarks</td>
                                <td style="width: 60%;font-size: 12px;">
                                    <textarea class="form-control" name="tag_remarks" id="tag_remarks">DONE</textarea>
                                    <input type="hidden" name="tag_detailno" id="tag_detailno">
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