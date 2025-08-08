
<?= $this->extend("layouts/register"); ?>

<?= $this->section("content"); ?>
<script>
    setTimeout(function() {
        $('#messageDiv').hide();
    }, 3000);
</script>
<div id="overlay">
    <div class="loader"></div>
</div>
<div class="container">
    <div class="registration-container">
        <h2>Register</h2>
            <?php if(session()->getTempdata('error')): ?>
                <div class="error-box" id="messageDiv"><?= session()->getTempdata('error')?></div> <!-- Error message div -->
            <?php endif; ?>
            <?php if(session()->getTempdata('success')): ?>
                <div class="success-box" id="messageDiv"><?= session()->getTempdata('success')?></div> <!-- Success message div -->
            <?php endif; ?>
        <form method="post" enctype="multipart/form-data" novalidate>
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="firstname" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstname" name="firstname" value='<?= set_value('firstname'); ?>'>
                <span class="text-danger"><?= error_form($firstname, 'firstname'); ?></span>
            </div>
            <div class="mb-3">
                <label for="lastname" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastname" name="lastname" value='<?= set_value('lastname'); ?>'>
                <span class="text-danger"><?= error_form($lastname, 'lastname'); ?></span>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" value='<?= set_value('email'); ?>'>
                <span class="text-danger"><?= error_form($email, 'email'); ?></span>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password">
                <span class="text-danger"><?= error_form($password, 'password'); ?></span>
                
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirmPassword" id="confirmPassword">
                <span class="text-danger"><?= error_form($confirmPassword, 'confirmPassword'); ?></span>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
            <a href="<?= base_url('login-page') ?>" type="submit" class="btn btn-success btn-block">Back to Login Page</a>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
<script>
    $(document).ready(function() {
        $('#form_data').on('submit', function() {
            // Show overlay on form submission
            $('#overlay').show();
            
        });
        
        // Optional: Hide overlay and enable form elements on form error
        <?php if (isset($error)) : ?>
            $('#overlay').hide();
        <?php endif; ?>
    });
</script>

<?= $this->endSection(); ?>