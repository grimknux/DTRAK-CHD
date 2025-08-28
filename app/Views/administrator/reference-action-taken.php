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
                <h2>Action Taken</h2>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="side-form">
                        <form class="form-horizontal" id="action_taken_form">
                            <?= csrf_field() ?>
                            <div class="form-group action_taken">
                                <label class="col-sm-4 control-label input-sm label-require" for="action_taken">Action Taken</label>
                                <div class="col-md-8">
                                    <input type="text" id="action_taken" name="action_taken" class="col-sm-7 form-control" placeholder="Action Taken">
                                    <span class="help-block action_takenMessage"></span>
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
                        <table class="table table-striped table-condensed table-vcenter table-bordered table-sm" id="action_taken_table">
                            <thead>
                                <tr>
                                    <th style="width: 10%;font-size: 10px;text-align:center;">#</th>
                                    <th style="width: 25%;font-size: 10px;text-align:center;">Action Taken Code</th>
                                    <th style="width: 40%;font-size: 10px;text-align:center;">Action to be Taken</th>
                                    <th style="width: 25%;font-size: 10px;text-align:center;">Action</th>
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

<script src="<?= base_url(); ?>js/pages/ref.ActionTaken.Function.js"></script>
<script src="<?= base_url(); ?>js/pages/reference.ActionTaken.Table.js"></script>

<script>

$(document).ready(function(){

    uiRefActionTaken.init(base_url,csrfToken);
    refActTaken.init();


});

</script>
<?= $this->endSection(); ?>

