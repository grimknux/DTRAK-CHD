<?= $this->extend("layouts/base"); ?>

<?= $this->section("content"); ?>
<script>
   
</script>
<div id="overlay">
    <div class="loader"></div>
</div>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><?= $page_heading ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <span data-feather="calendar"></span>This week
        </button>
    </div>
    </div>

    <h3>Add Webex Meeting</h3>
    <div class="row g-3">
        <div class="col-md-4">
            <?php if(session()->getTempdata('success')): ?>
                <div class="success-box messageDiv" id="messageDiv"><?= session()->getTempdata('success')?></div> <!-- success message div -->
            <?php endif; ?>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="success-box messageDiv" id="messageDivSuccess" style="display:none"></div> <!-- success message div -->
        </div>
    </div>
    <form class="row g-3" id="addsched-form" method="post">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12"> 
                    <div class="mb-3">
                        <label for="title" class="form-label">Date of Request: <b style='color: red;'>*</b></label>
                        <input class="form-control daterequest" type="date" id="daterequest" name="daterequest" value="<?php echo date('Y-m-d') ?>">
                        <div class="daterequestMessage"></div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Meeting Title: <b style='color: red;'>*</b></label>
                        <input class="form-control title" type="text" id="title" name="title">
                        <div class="titleMessage"></div>
                    </div>

                    <div class="mb-3">
                        <label for="start" class="form-label">Meeting Start Time <b style='color: red;'>*</b></label>
                        <input class="form-control start" type="datetime-local" id="start" name="start">
                        <div class="startMessage"></div>
                    </div>
                
                    
                    
                </div>
            </div>

            <div class="row">
                <div class="col-md-7">
                    <div class="mb-3">
                        <label for="meetingDuration" class="form-label">Hours: <b style='color: red;'>*</b></label>
                        <select class="form-select meetingDurationHrs" name="meetingDurationHrs" id="meetingDurationHrs">
                            <option value="1">1 hour</option>
                            <option value="2">2 hours</option>
                            <option value="24">24 hours</option>
                        </select>
                        <div class="meetingDurationHrsMessage"></div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="mb-3">
                        <label for="meetingDuration" class="form-label">Minutes: <b style='color: red;'>*</b></label>
                        <select class="form-select meetingDurationMin" name="meetingDurationMin" id="meetingDurationMin">
                            <option value="0">0 minutes</option>
                            <option value="10">10 minutes</option>
                            <option value="20">20 minutes</option>
                            <option value="50">50 minutes</option>
                        </select>
                        <div class="meetingDurationMinMessage"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="meetingPassword" class="form-label">Meeting Password:</label>
                        <input type="text" class="form-control" name="meetingPassword" id="meetingPassword">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="meetingAttendees" class="form-label">Meeting Attendees:</label>
                        <input type="text" class="form-control" name="meetingAttendees" id="meetingAttendees">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="recurrence" name="recurrence" value="1">
                            <label class="form-check-label" for="recurrence">
                                Recurrence
                            </label>
                        </div>
                    </div>
                </div>  
            </div>    

            <div id="recurrenceSettings" style="display: none;">

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="recurrenceType" class="form-label">Recurrence Type: <b style='color: red;'>*</b></label>
                            <select class="form-select recurrenceType" id="recurrenceType" name="recurrenceType">
                                <option value="DAILY">DAILY</option>
                                <option value="WEEKLY">WEEKLY</option>
                            </select>
                            <div class="recurrenceTypeMessage"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    
                    <div class="col-md-12">
                        <div id="daily-recurrence-settings">
                            <div class="mb-3">
                                <label for="daysInterval" class="form-label">Days Interval: <b style='color: red;'>*</b></label>
                                <input class="form-control daysInterval" type="number" name="daysInterval" id="daysInterval" value="1">
                                <div class="daysIntervalMessage"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div id="weekly-recurrence-settings" style="display: none;">
                            <div class="mb-3">
                                <label for="weeksInterval" class="form-label">Weeks Interval: <b style='color: red;'>*</b></label>
                                <input class="form-control weeksInterval" type="number" name="weeksInterval" id="weeksInterval" value="1">
                            <div class="weeksIntervalMessage"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="byday" class="form-label">Days of the week: <b style='color: red;'>*</b></label>
                                <select class="form-select byday custom-select" name="byday[]" id="byday" multiple>
                                    <option value="SU">Sunday</option>
                                    <option value="MO">Monday</option>
                                    <option value="TU">Tuesday</option>
                                    <option value="WE">Wednesday</option>
                                    <option value="TH">Thursday</option>
                                    <option value="FR">Friday</option>
                                    <option value="SA">Saturday</option>
                                </select>
                                <div class="bydayMessage"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="check-enddate" name="check-enddate" value="1">
                                <label class="form-check-label" for="check-enddate">
                                    End Date
                                </label>
                            </div>
                        </div>
                    </div>  
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div id="end-recurrence-date" style="display: none;">
                            <div class="mb-3">
                                <label for="endDate" class="form-label">End on: <b style='color: red;'>*</b></label>
                                <input class="form-control endDate" type="date" name="endDate" id="endDate" >
                                <div class="endDateMessage"></div>
                            </div>
                        </div>
                    </div>
                </div>

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

    $(document).ready(function() {

        setTimeout(function() {
            $('.messageDiv').hide();
        }, 3000);

        //$('#byday').select2();

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


        const input = $('#meetingAttendees')[0]; // Get the DOM element from the jQuery object

        new Tagify(input);

        $('#check-enddate').change(function() {
            if ($(this).is(':checked')) {
                $('#end-recurrence-date').show();
            } else {
                $('#end-recurrence-date').hide();
            }
        });

        $("#start").change(function() {
            var startTime = new Date($(this).val()).getTime();
            var endTime = new Date($("#endDate").val()).getTime();

            if (endTime && startTime >= endTime) {
                // If end time is before or same as start time, reset end time
                $("#endDate").val("");
            }

            /*var stTime = $(this).val();
            $("#endDate").attr("min", stTime);*/
        });

        $("#endDate").change(function() {
            var startTime = new Date($("#start").val()).getTime();
            var endTime = new Date($(this).val()).getTime();

            if (startTime && startTime >= endTime) {
                // If end time is before or same as start time, reset end time
                $(this).val("");
                alert("Cannot select earlier time");
            }
        });

        $("#recurrence").change(function() {
            $("#recurrenceSettings").toggle(this.checked);

            // Uncheck another checkbox if #recurrence is unchecked
            if (!this.checked) {
                $("#check-enddate").prop("checked", false);
            }
        });

        $('#recurrenceType').change(function() {
            if ($(this).val() === 'WEEKLY') {
                $('#daily-recurrence-settings').hide();
                $('#weekly-recurrence-settings').show();
            } else if ($(this).val() === 'DAILY') {
                $('#daily-recurrence-settings').show();
                $('#weekly-recurrence-settings').hide();
            }
        });



        $('#addsched-form').submit(function(e) {
            e.preventDefault();

            // Get the form data.
            var formData = new FormData(this);
            $('#overlay').show();

            // Send the data using Ajax.
            $.ajax({
            url: '<?= base_url("createMeeting") ?>',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function(xhr) {
                // Set the CSRF token header
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function(response) {
                // Do something with the response.
                //alert(response.code);
                //alert(response.authUrl);

                if(response.status == "success"){
                    if(response.url){
                        window.open(response.authUrl);
                    }else{
                        $('#messageDivSuccess').show();
                        $('#messageDivSuccess').html(response.message);
                        //alert(response.message)
                    }
                }else{
                    if(response.status == "error"){
                        alert(response.message);
                    }else{
                        handleValidationErrors(response);
                    }
                }


            },
            error: function(error) {
                // Handle the error.
            },
            complete: function() {
                $('#overlay').hide();
                setTimeout(function() {
                    $('.messageDiv').hide();
                }, 3000);

            }
            });
        });


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
  
    });  




</script>

<?= $this->endSection(); ?>