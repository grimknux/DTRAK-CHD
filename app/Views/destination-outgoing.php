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
            <div class="block-title">
                <div class="block-options pull-right">
                    <a href="<?= base_url('docview/outgoing') ?>" class="btn btn-effect-ripple btn-default" data-toggle="tooltip" title="Go Back"><i class="fa fa-arrow-circle-left"></i> Go Back</a>
                </div>
                <h2>Document Destination</h2>
            </div>
            <div class="row ">
                <!-- Simple Stats Widgets -->
                 <?php if($checkdest){ ?>
                <div class="col-sm-3 add-btn-dest">
                    <a href="#" class="widget desti-modal" data-toggle='modal'>
                        <div class="widget-content widget-content-mini text-right clearfix widget-light">
                            <div class="widget-icon pull-left themed-background-info">
                                <i class="fa fa-paper-plane-o text-light-op"></i>
                            </div>
                            <h2 class="widget-heading h4 text-info">
                                <strong><span data-toggle="counter" data-to="2835">Add Destination</span></strong>
                            </h2>
                            <span class="text-muted">Assign the Document Destination</span>
                        </div>
                    </a>
                </div>
                <?php } ?>
                <div class="col-sm-6">
                    <table class="table table-vcenter table-bordered table-striped table-condensed">
                        <tbody>
                            <tr>
                                <td>Entry by</td>
                                <td><b><?= $entryby ?></b></td>
                            </tr>
                            <tr>
                                <td>Internal Originating Office</td>
                                <td><b><?= $docdata['officename'] . " (" . $docdata['officeshort'] . ")" ?></b></td>
                            </tr>
                            <tr>
                                <td>Document No.</td>
                                <td><b><?= $docdata['route_no'] ?></b></td>
                            </tr>
                            <tr>
                                <td>Document Type</td>
                                <td><b><?= $docdata['ddoctype_desc'] ?></b></td>
                            </tr>
                            <tr>
                                <td>Office Control No.</td>
                                <td><b><?= $docdata['office_controlno'] ?></b></td>
                            </tr>
                            <tr>
                                <td>Reference No.</td>
                                <td><b><?= $docdata['ref_office_controlno'] ?></b></td>
                            </tr>
                            <tr>
                                <td>Subject</td>
                                <td><b><?= $docdata['subject'] ?></b></td>
                            </tr>
                            <tr>
                                <td>Attached Documents</td>
                                <td><b><?= $docdata['attachlist'] ?></b></td>
                            </tr>
                            <tr>
                                <td>Page Count</td>
                                <td><b><?= $docdata['no_page'] ?></b></td>
                            </tr>
                            <tr>
                                <td>Originating Office (External)</td>
                                <td><b><?= $docdata['exofficecode'] ?></b></td>
                            </tr>
                            <tr>
                                <td>Originating Employee (External)</td>
                                <td><b><?= $docdata['exempname'] ?></b></td>
                            </tr>
                            <tr>
                                <td>Document Control No. (External)</td>
                                <td><b><?= $docdata['exdoc_controlno'] ?></b></td>
                            </tr>
                            <tr>
                                <td>Attachment</td>
                                <td>
                                    <b>
                                    <?php
                                        if(!empty($docdata['filename']) || $docdata['filename'] !== "" ){
                                            echo "<a href='".base_url().'docview/outgoing/viewfile/'.$docdata['filename']."' target='_blank'><div class='media-items-content'><i class='fa fa-file-pdf-o fa-2x text-danger'></i></div>".$docdata['filename']."</a>";
                                        }else{
                                            echo "No Attachment";
                                        }
                                    ?>
                                    </b>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td><b><?= $docdata['registry_status'] ?></b></td>
                            </tr>
                            <tr>
                                <td>Remarks</td>
                                <td><b><?= $docdata['remarks'] ?></b></td>
                            </tr>
                            <tr>
                                <td>Date and Time Created</td>
                                <td><b><?= $docdata['datelog'] . " " . $docdata['timelog'] ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="documentControlsContainer"></div>

<div id="destination-modal-add" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong>Add Destination</strong></h3>
            </div>
            <div class="modal-body">
                <div class="row ">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <form id="documentDestinationForm" method="post" class="form-horizontal">
                                <?= csrf_field() ?>
                                <input type="hidden" name="routeno" value="<?= $routeno ?>">
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
                                            <td class="office_destination" style="width: 30%;">
                                                <div class="office_destinationDiv">
                                                    <select name="office_destination[]" style="width: 100%;" class="select-select2" data-placeholder="Select Office Destination" >
                                                        <option value=""></option>
                                                    
                                                        <?php foreach ($officeDestinations as $office): ?>

                                                            <option value="<?= $office['officecode'] ?>"
                                                                <?php if (in_array($office['officecode'], $officedest)) echo 'disabled'; ?>>
                                                                <?= $office['shortname'] ." - ". $office['officename'] ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <span class="help-block office_destinationMessage"></span>
                                                </div>
                                            
                                            </td>
                                            <td class="action_officer" style="width: 30%;">
                                                <div class="action_officerDiv">
                                                    <select name="action_officer[]" style="width: 100%;" class="select-select2 action_officer ao">
                                                        <option value="">Select Action Officer</option>
                                                    </select>
                                                    <span class="help-block action_officerMessage"></span>
                                                </div>
                                            </td>
                                            <td class="action_required" style="width: 30%;">
                                                <div class="action_requiredDiv">
                                                    <select name="action_required[]" style="width: 100%;" class="select-select2 action_required ar">
                                                        <option value="">Select Action Required</option>
                                                        <?php foreach ($actionReq as $actreq): ?>
                                                            <option value="<?= $actreq['reqaction_code'] ?>"><?= $actreq['reqaction_desc'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <span class="help-block action_requiredMessage"></span>
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
                                    <button class="btn btn-sm btn-primary" type="submit" id="addDestination">Add/Submit Destination</button>
                                    <button class="btn btn-sm btn-danger" type="submit" id="resetDestinationForm">Reset</button>
                                </div>
                                    <button class="btn btn-sm btn-success" id="addDestinationBtn"><i class="fa fa-plus"></i> Add Multiple Destination</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
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


<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
<script>
    var baseUrl = '<?= base_url(); ?>';
</script>

<script src="<?= base_url(); ?>public/js/pages/forms.Outgoing.js"></script>
<script src="<?= base_url(); ?>public/js/pages/table.Functions.js"></script>

<script>
var officeDestinations = <?= json_encode($officeDestinations, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
var officedest = <?= json_encode($officedest, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
var actionReq = <?= json_encode($actionReq, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
var routeno = '<?= htmlspecialchars($routeno, ENT_QUOTES, 'UTF-8') ?>';

$(document).ready(function(){

    outgoingDes.init(base_url,csrfToken,officeDestinations,routeno);

});


</script>
<?= $this->endSection(); ?>

