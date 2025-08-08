<!-- Login Container -->
<?= $this->extend("layouts/register"); ?>

<?= $this->section("content"); ?>

<div class="container mt-5">
        
    <?php if(isset($success)): ?>
        <div class="alert alert-success" role="alert">
            <h3><?= $success ?></h3> 
            <p id="countdown"></p>
            <?php
                header('Refresh:3; url= '. base_url('login-page')); 
            ?>
        </div>
    <?php endif; ?>

    <?php if(isset($already)): ?>
        <div class="alert alert-warning" role="alert">
            <h3><?= $already ?></h3> 
            <p id="countdown"></p>
            <?php
                header('Refresh:3; url= '. base_url('login-page')); 
            ?>
        </div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <h3><?= $error ?></h3> <br>
            <p id="countdown"></p>
            <?php
                header('Refresh:3; url= '. base_url('login-page')); 
            ?>
        </div>
    <?php endif; ?>
  </div>
<!-- END Login Container -->

<?= $this->endSection(); ?>

<?= $this->section("script"); ?>

<script>
    const countdownElement = document.getElementById('countdown');
    let countdownValue = 3;

    function updateCountdown() {
      countdownElement.textContent = "You will be redirected to the login page in " + countdownValue + " seconds";
      countdownValue--;

      if (countdownValue > 0) {
        setTimeout(updateCountdown, 1000);
      } 
    }

    updateCountdown();
  </script>
<?= $this->endSection(); ?>
