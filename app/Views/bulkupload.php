<?= $this->extend("layouts/base"); ?>

<?= $this->section("content"); ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Bulk Upload</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
        </div>
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                <span data-feather="calendar"></span>This week
            </button>
        </div>
    </div>

    <h2>Bulk Upload Form</h2>

    <form id="bulkupload-form" method="post">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="empCSV" class="form-label">Upload CSV File</label>
                <input class="form-control" type="file" id="empCSV" name="empCSV">
            </div>
           
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
    
    
</main>

<?= $this->endSection(); ?>


<?= $this->section("script"); ?>

<script>
var csrfToken = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function(){ 

    // Prevent the default form submission
    $("#bulkupload-form").submit(function(event) {
        event.preventDefault();

        // Get the form data
        var formData = new FormData(this);

        // Send the form data using AJAX
        $.ajax({
            type: "POST",
            url: "<?= base_url('upload-csv') ?>",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function(xhr) {
                // Set the CSRF token header
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function(response) {

                if (response.status === 'success') {
                    // Process the successful response and open the redirect URL in a new tab
                    // ...

                    alert(response.message);
                } else if (response.status === 'error') {
                    // Handle validation errors
                    //handleValidationErrors(response.errors);
                    alert(response.message);
                } else {
                    // Handle other types of responses if needed
                    alert(response.status);
                }
              
            },
            error: function(xhr, errorType, thrownError) {
                var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
                alert("Server error: " + errorMessage);
                console.log("Server error: " + xhr.responseText);
            },
        });
    });

});


</script>
<?= $this->endSection(); ?>