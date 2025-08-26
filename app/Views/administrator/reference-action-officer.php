<!-- Login Container -->
<?= $this->extend("layouts/base"); ?>

<?= $this->section("content"); ?>

<div id="overlay">
    <div class="loader"></div>
</div>
<style>
    .side-form {
        background-color: #f5f5f5; /* light gray */
        border: 1px solid #ccc;    /* slightly darker gray border */
        border-radius: 8px;        /* curved borders */
        padding: 16px;             /* spacing inside the box */
    }
    
</style>
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
                <h2>Action Officer</h2>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="side-form">
                        <div class="form-group has-info">
                            <label class="control-label" for="pmis_emp">Select Employee from PMIS</label>
                            <select id="pmis_emp" name="pmis_emp" class="select-select2 input-sm" style="width: 100%;" data-placeholder="Choose one.." required>
                                <option></option><!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                <?php foreach ($pmis_emp as $emp): ?>
                                    <option value="<?= $emp['uname'] ?>" data-firstname="<?= $emp['FirstName'] ?>" data-lastname="<?= $emp['LastName'] ?>" data-middlename="<?= $emp['MiddleName'] ?>">
                                        <?= $emp['uname'] . ' - ' . $emp['FirstName'] . ' ' . ucfirst($emp['MiddleName'][0]) . '. ' . $emp['LastName'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                        <form class="form-horizontal" id="action_officer_form">
                            <?= csrf_field() ?>
                            <div class="form-group username">
                                <label class="col-sm-4 control-label input-sm label-require" for="username">Username</label>
                                <div class="col-md-8">
                                    <input type="text" id="username" name="username" class="col-sm-7 form-control" placeholder="Username">
                                    <span class="help-block usernameMessage"></span>
                                </div>
                            </div>
                            <div class="form-group firstname">
                                <label class="col-sm-4 control-label input-sm label-require" for="firstname">First Name</label>
                                <div class="col-md-8">
                                    <input type="text" id="firstname" name="firstname" class="form-control" placeholder="First Name">
                                    <span class="help-block firstnameMessage"></span>
                                </div>
                            </div>
                            <div class="form-group middlename">
                                <label class="col-sm-4 control-label input-sm label-require" for="middlename">Middle Name</label>
                                <div class="col-md-8">
                                    <input type="text" id="middlename" name="middlename" class="form-control" placeholder="Middle Name">
                                    <span class="help-block middlenameMessage"></span>
                                </div>
                            </div>
                            <div class="form-group lastname">
                                <label class="col-sm-4 control-label input-sm label-require" for="lastname">Last Name</label>
                                <div class="col-md-8">
                                    <input type="text" id="lastname" name="lastname" class="form-control" placeholder="Last Name">
                                    <span class="help-block lastnameMessage"></span>
                                </div>
                            </div>
                            <div class="form-group office">
                                <label class="col-sm-4 control-label input-sm label-require" for="office">Office/s</label>
                                <div class="col-md-8">
                                    <select id="office" name="office[]" class="col-sm-4 select-select2" style="width: 100%;" data-placeholder="Office Assignment" multiple>
                                        <option></option>
                                        <?php foreach ($offices as $offs): ?>
                                            <option value="<?= $offs['officecode'] ?>">
                                                <?= $offs['shortname'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="help-block officeMessage"></span>
                                </div>
                            </div>
                            <div class="form-group office_rep">
                                <label class="col-sm-4 control-label input-sm label-require" for="office_rep">Office Representative</label>
                                <div class="col-md-8">
                                    <select id="office_rep" name="office_rep" class="col-sm-4 select-select2" style="width: 100%;" data-placeholder="Office Representative">
                                        <option></option>
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                    <span class="help-block office_repMessage"></span>
                                </div>
                            </div>
                            <div class="form-group user_level">
                                <label class="col-sm-4 control-label input-sm label-require" for="user_level">User Level</label>
                                <div class="col-md-8">
                                    <select id="user_level" name="user_level" class="col-sm-4 select-select2" style="width: 100%;" data-placeholder="User Level">
                                        <option></option>
                                        <?php foreach ($user_levels as $ul): ?>
                                            <option value="<?= $ul['UserLevelID'] ?>">
                                                <?= $ul['UserLevelName'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="help-block user_levelMessage"></span>
                                </div>
                            </div>
                            <div class="form-group admin_menu" style="display: none;">
                                <label class="col-sm-4 control-label input-sm label-require" for="admin_menu">Menu Assignment</label>
                                <div class="col-md-8">
                                    <select id="admin_menu" name="admin_menu[]" class="col-sm-4 select-select2" style="width: 100%;" data-placeholder="Menu Assignment" multiple>
                                        <option></option>
                                        <?php foreach ($admin_menus as $am): ?>
                                            <option value="<?= $am['id'] ?>">
                                                <?= $am['id'].'. '.$am['admin_menu'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="help-block admin_menuMessage"></span>
                                </div>
                            </div>
                            <div class="form-group form-actions">
                                <div class="col-md-10 col-md-offset-2">
                                    <button type="submit" class="btn btn-effect-ripple btn-primary" id="submit_btn_action"><i class="fa fa-user-plus"></i> Create</button>
                                    <button type="button" class="btn btn-effect-ripple btn-danger reset_btn_action" id="cancel_btn_action"><i class="fa fa-refresh"></i> Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed table-vcenter table-bordered table-sm" id="action_officer_table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;font-size: 10px;text-align:center;">#</th>
                                    <th style="width: 15%;font-size: 10px;text-align:center;">Employee ID</th>
                                    <th style="width: 25%;font-size: 10px;text-align:center;">Name</th>
                                    <th style="width: 20%;font-size: 10px;text-align:center;">Office</th>
                                    <th style="width: 10%;font-size: 10px;text-align:center;">Representative?</th>
                                    <th style="width: 10%;font-size: 10px;text-align:center;">User Level</th>
                                    <th style="width: 15%;font-size: 10px;text-align:center;">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            

        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
<script>
    var baseUrl = '<?= base_url(); ?>';
</script>

<script src="<?= base_url(); ?>js/pages/ref.ActionOfficer.Function.js"></script>
<script src="<?= base_url(); ?>js/pages/reference.ActionOffice.Table.js"></script>

<script>

$(document).ready(function(){

    uiRefActionOfficer.init(base_url,csrfToken);
    refActionOfficer.init();


    <?php if (!$pmis_success): ?>
        alert("<?= esc($pmis_message) ?>");
        $('#pmis_emp').prop('disabled', true);
    <?php endif; ?>

});

</script>
<?= $this->endSection(); ?>

