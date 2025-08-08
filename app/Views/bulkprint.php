<?= $this->extend("layouts/base"); ?>

<?= $this->section("content"); ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Bulk Print</h1>
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

    <div class="row">
        <div class="col-md-4">
            <h2>Bulk Print Form</h2>
            <h4>Printing per Type, per Division, per Section</h4>
            <form id="bulkprint-form" action="<?= base_url('check-bulk') ?>" method="post" target="_blank">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="employeetype" class="form-label">Employee Type <b style='color: red;'>*</b></label>
                        <select class="form-select employeetype custom-select" id="employeetype"  name="employeetype">
                            <option value="">Select Employee Type</option>
                            <option value="PERMANENT">PERMANENT</option>
                            <option value="CONTRACTUAL">CONTRACTUAL</option>
                            
                        </select>
                        <div class="employeetypeMessage"></div>
                    </div>
                    <div class="mb-3">
                        <label for="division" class="form-label">Division</label>
                        <select class="form-select division custom-select" id="division"  name="division">
                            <option value="">Select Division</option>
                            <?php foreach ($division as $divs) : ?>
                                <option value="<?= $divs['divcode'] ?>"><?= $divs['division'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="section" class="form-label">Section</label>
                        <select class="form-select section custom-select" id="section"  name="section">
                            <option value="">Select Section</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>


        <div class="col-md-4 offset-md-2">
            <h2>Bulk Print Form</h2>
            <h4>Printing per Employee</h4>
            <form id="bulkprint-form-emp" action="<?= base_url('check-bulk-emp') ?>" method="post" target="_blank">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="employee" class="form-label">Employee <b style='color: red;'>*</b></label>
                        <select class="form-select employee custom-select" id="employee"  name="employee[]" multiple>
                            <?php foreach ($staff as $staffs) : ?>
                                <option value="<?= $staffs['ID'] ?>"><?= strtoupper($staffs['FirstName']." ".substr($staffs['MiddleName'], 0, 1).". ".$staffs['LastName']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="employeeMessage"></div>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
</main>

<?= $this->endSection(); ?>

<?= $this->section("script"); ?>

<script>
var csrfToken = $('meta[name="csrf-token"]').attr('content');
	
$(document).ready(function(){   

    $('#employee').select2();
    
    $('.custom-select').change(function() {
        // Get the selected option's value and text (name)
        var selectedValue = $(this).val();
        var selectId = this.id;

        if(selectedValue != ''){
            $('.' + selectId + 'Message').removeClass('invalid-feedback').empty();

            $('.' + selectId).removeClass('is-invalid').addClass('is-valid');

        }else{
            $('.' + selectId + 'Message').removeClass('valid-feedback').empty();

            $('.' + selectId).removeClass('is-invalid').removeClass('is-valid');
        }
       

    });

    // Prevent the default form submission
    $("#bulkprint-form").submit(function(event) {
        event.preventDefault();

        // Get the form data
        var formData = new FormData(this);

        // Send the form data using AJAX
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
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

                    //alert(response.message);
                    window.open(response.redirect_url, '_blank');
                } else if (response.status === 'error') {
                    // Handle validation errors
                    handleValidationErrors(response.errors);
                } else {
                    // Handle other types of responses if needed
                }
              
            },
            error: function(xhr, errorType, thrownError) {
                var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
                alert("Server error: " + errorMessage);
                console.log("Server error: " + xhr.responseText);
            },
        });
    });


    // Prevent the default form submission
    $("#bulkprint-form-emp").submit(function(event) {
        event.preventDefault();

        // Get the form data
        var formData = new FormData(this);

        // Send the form data using AJAX
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
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

                    //alert(response.message);
                    window.open(response.redirect_url, '_blank');
                } else if (response.status === 'error') {
                    // Handle validation errors
                    handleValidationErrors(response.errors);
                } else {
                    // Handle other types of responses if needed
                }
              
            },
            error: function(xhr, errorType, thrownError) {
                var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
                alert("Server error: " + errorMessage);
                console.log("Server error: " + xhr.responseText);
            },
        });
    });


    // Function to handle validation errors
    function handleValidationErrors(response) {
        // Reset validation messages
        $('.is-invalid').removeClass('is-invalid');
        $('.is-valid').removeClass('is-valid');
        $('.invalid-feedback').empty();
        $('.valid-feedback').empty();
        
        // Iterate over the response object and display validation errors
        var hasErrors = false; // Flag to track if any errors are found

        for (var field in response) {
            if (response.hasOwnProperty(field)) {
                var error = response[field];

                if (error) {
                    var $element = $('.' + field);
                    var $messageElement = $('.' + field + 'Message');

                    $element.addClass('is-invalid').removeClass('is-valid');
                    $messageElement.addClass('invalid-feedback').html(error);

                    hasErrors = true; // Set the flag to indicate error
                } else {
                    var $validElement = $('.' + field);
                    var $validMessageElement = $('.' + field + 'Message');

                    $validElement.removeClass('is-invalid').addClass('is-valid');
                    $validMessageElement.removeClass('invalid-feedback').empty();
                }
            }
        }
    }


// Handle division change
    $('#division').on('change', function() {
        var divisionValue = $(this).val();
        getSection(divisionValue);
    }); 



});


// Function to populate sections based on the selected division
function getSection(division) {

    if (!division) {
        // If the selected division is blank, clear the section select box and return early.
        $('#section').html('<option value="">Choose Section</option>');
        return;
    }
    $.ajax({
        url: '<?= base_url("get-section") ?>',
        type: 'POST',
        data: {
            division: division
        },
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
        },
        dataType: 'JSON',
        success: function(response) {
            var optionsHtml = '<option value="">Choose Section</option>';
            if(response && response.length == 0){
                optionsHtml = '<option value="">Choose Section</option>';
            }else{
                response.forEach(function(item) {
                    var value = item.value;
                    var section = item.section;
                    optionsHtml += `<option value="${value}">${section}</option>`;
                });
            }
            
            $('#section').html(optionsHtml);
        },
        error: function(xhr, status, error) {
            // Handle the error response
            console.log(xhr.responseText);
            // Optionally, display an error message or perform any other actions
        }
    });
}
</script>
<?= $this->endSection(); ?>