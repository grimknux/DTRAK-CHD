<?= $this->extend("layouts/base"); ?>

<?= $this->section("content"); ?>

<div id="overlay">
    <div class="loader"></div>
</div>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <?php if($loginG): ?>
                <a href="<?= $loginG ?>" type="button" class="btn btn-sm btn-outline-secondary">Login to Google</a>
            <?php endif; ?>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <span data-feather="calendar"></span>This week
        </button>
    </div>
    </div>
    
    <div class="alert alert-primary" role="alert" >
        <h5>This screen displays 50 records use the search box to spool more records</h5>
    </div>

    <h2>Employee</h2>
            <div class="table-responsive">
                <table class="table table-striped table-md" id="list_table" style="width: 100%;">
                    
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Division</th>
                            <th>Section</th>
                            <th>Type of Employment</th>
                            <th>Photo</th>
                            <th>Signature</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                </table>
            </div>
            <!--<div class="row">
                <div class="col-md-4">
                    <button type="button" class="btn btn-primary" id="bulkSendButton">Primary</button>
                </div>
            </div>-->

</main>

<div class="modal fade " tabindex="-1" id="editModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-xl modal-dialog">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title">Edit user</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editEmployee" name="editEmployee" method="post" enctype="multipart/form-data" class="row g-3" >
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="container-fluid">
                <div class="row">
                <div class="col-sm-3 mb-3">
                        <label for="fname" class="form-label">ID Number <b style='color: red;'>*</b></label>
                        <input type="text" class="form-control idnum" id="idnum" name="idnum" placeholder="ID Number">
                        <div class="idnumMessage"></div>
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label for="fname" class="form-label">First name <b style='color: red;'>*</b></label>
                        <input type="text" class="form-control fname" id="fname" name="fname" placeholder="First Name">
                        <div class="fnameMessage"></div>
                        <input type="hidden" id="employee_id" name="employee_id">
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label for="mname" class="form-label">Middle Name <b style='color: red;'>*</b></label>
                        <input type="text" class="form-control mname" id="mname" name="mname" placeholder="Middle Name">
                        <div class="mnameMessage"></div>
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label for="lname" class="form-label">Last Name <b style='color: red;'>*</b></label>
                        <input type="text" class="form-control lname" id="lname" name="lname" placeholder="Last Name">
                        <div class="lnameMessage"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 mb-3">
                        <label for="nname" class="form-label">Nick Name</label>
                        <input type="text" class="form-control nname" id="nname" name="nname" placeholder="Nick Name">
                        <div class="nnameMessage"></div>
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label for="nameext" class="form-label">Name Extension</label>
                        <input type="text" class="form-control nameext" id="nameext" name="nameext" placeholder="ex.: MD, MPH, MDA, CESO IV">
                        <div class="nameextMessage"></div>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <label for="suffix" class="form-label">Suffix</label>
                        <select class="form-select suffix" id="suffix"  name="suffix">
                            <option value="">Choose suffix</option>
                            <option value="JR">JR</option>
                            <option value="SR">SR</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                            <option value="V">V</option>
                            <option value="VI">VI</option>
                            <option value="VII">CII</option>
                        </select>
                        <div class="suffixMessage"></div>
                        </div>
                </div>
                <div class="row">
                    <div class="col-sm-2 mb-2">
                        <label for="sex" class="form-label">Sex <b style='color: red;'>*</b></label>
                        <select class="form-select sex" id="sex"  name="sex">
                            <option value="">Choose sex</option>
                            <option value="MALE">MALE</option>
                            <option value="FEMALE">FEMALE</option>
                        </select>
                        <div class="sexMessage"></div>
                        </div>
                    <div class="col-sm-4 mb-4">
                        <label for="position" class="form-label">Position <b style='color: red;'>*</b></label>
                        <input type="text" class="form-control position" id="position" name="position" placeholder="Position, ex. COMPUTER PROGRAMMER I">
                        <div class="positionMessage"></div>
                    </div>
                    <div class="col-sm-4 mb-4">
                        <label for="division" class="form-label">Division <b style='color: red;'>*</b></label>
                        <select class="form-select division" id="division"  name="division">
                            <option value="">Choose division</option>
                        </select>
                        <div class="divisionMessage"></div>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <label for="section" class="form-label">Section <b style='color: red;'>*</b></label>
                        <select class="form-select section" id="section"  name="section">
                            <option value="">Choose section</option>
                        </select>
                        <div class="sectionMessage"></div>
                        </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 mb-3">
                        <label for="contract_start" class="form-label">Contract Start</label>
                        <input type="date" class="form-control contract_start" id="contract_start" name="contract_start" placeholder="Contract Start">
                        <div class="contract_startMessage"></div>
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label for="contract_end" class="form-label">Contract End</label>
                        <input type="date" class="form-control contract_end" id="contract_end" name="contract_end" placeholder="Contract End">
                        <div class="contract_endMessage"></div>
                    </div>
                    <div class="col-sm-6 mb-6">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control address" id="address" name="address" placeholder="Address"></textarea>
                        <div class="addressMessage"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 mb-3">
                        <label for="bdate" class="form-label">Birthdate</label>
                        <input type="date" class="form-control bdate" id="bdate" name="bdate" placeholder="Birthdate">
                        <div class="bdateMessage"></div>
                    </div>
                    <div class="col-sm-4 mb-4">
                        <label for="personnotify" class="form-label">Person to Notify</label>
                        <input type="text" class="form-control personnotify" id="personnotify" name="personnotify" placeholder="Person to Notify">
                        <div class="personnotifyMessage"></div>
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label for="cpnum" class="form-label">Person to Notify Contact Number</label>
                        <input type="text" class="form-control cpnum" id="cpnum" name="cpnum" placeholder="Person to Notify Contact">
                        <div class="cpnumMessage"></div>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <label for="bloodtype" class="form-label">Blood Type</label>
                        <select class="form-select bloodtype" id="bloodtype"  name="bloodtype">
                            <option value="">Choose Blood Type</option>
                            <option value="A+">A+</option>
                            <option value="A-">B-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O</option>
                        </select>
                        <div class="bloodtypeMessage"></div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3 mb-3">
                        <label for="tin" class="form-label">TIN Number</label>
                        <input type="number" class="form-control tin restrict-char" id="tin" name="tin" placeholder="TIN Number">
                        <div class="tinMessage"></div>
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label for="phic" class="form-label">Philhealth Number</label>
                        <input type="number" class="form-control phic restrict-char" id="phic" name="phic" placeholder="Philhealth Number">
                        <div class="phicMessage"></div>
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label for="sss" class="form-label">SSS Number</label>
                        <input type="number" class="form-control sss restrict-char" id="sss" name="sss" placeholder="SSS Number">
                        <div class="sssMessage"></div>
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label for="pagibig" class="form-label">Pagibig Number</label>
                        <input type="number" class="form-control pagibig restrict-char" id="pagibig" name="pagibig" placeholder="Pagibig Number">
                        <div class="pagibigMessage"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 mb-3">
                        <label for="typeemployment" class="form-label">Employment Type</label>
                        <select class="form-select typeemployment" id="typeemployment"  name="typeemployment">
                            <option value="">Choose Employment type</option>
                            <option value="PERMANENT">PERMANENT</option>
                            <option value="CONTRACTUAL">CONTRACTUAL</option>
                        </select>
                        <div class="typeemploymentMessage"></div>
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label for="photo" class="form-label">Photo</label>
                        <input type="text" class="form-control photo restrict-char" id="photo" name="photo" placeholder="Photo">
                        <div class="photoMessage"></div>
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label for="signature" class="form-label">Signature</label>
                        <input type="text" class="form-control signature restrict-char" id="signature" name="signature" placeholder="Signature">
                        <div class="signatureMessage"></div>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" >Save changes</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade " tabindex="-1" id="changePhotomodal" aria-labelledby="changePhotomodal" aria-hidden="true">
    <div class="modal-dialog-centered modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadForm" name="uploadForm" method="post" enctype="multipart/form-data" class="row g-3" >
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12 mb-3">
                            <label for="fname" class="form-label">Photo <b style='color: red;'>*</b></label>
                            <input type="file" class="form-control photo" id="photo" name="photoP" placeholder="ID Number">
                            <input type="hidden" name="empid" id="empid" value="">
                            <div>
                                <img id="cropperImage" src="#" alt="Preview" style="max-width: 100%;">
                            </div>
                            <div class="photoMessage"></div>
                        </div>
                    </div>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="cropButton" type="submit">Upload</button>
            </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section("script"); ?>

<script>

     
var csrfToken = $('meta[name="csrf-token"]').attr('content');
	

let cropper;

$(document).ready(function(){    

    initCropper();
    

    // Handle form submission
    $('#editEmployee').submit(function(e) {
        e.preventDefault(); // Prevent the form from submitting traditionally

        // Get the form data
        var formData = new FormData(this);

        // Send the AJAX request
        $.ajax({
        url: '<?= base_url("submit-data") ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function(xhr) {
            // Set the CSRF token header
            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
        },
        success: function(response) {
            if (response.status === 'success') {
                 // Handle success
                alert(response.message);

                $('#editModal').modal('hide');

                 // Reload or update the table
                $('#list_table').DataTable().ajax.reload();
           
            } else {
                if(response.qstatus === 'error'){
                    alert(response.message);
                }else{
                    handleValidationErrors(response);
                }
                
            }
            console.log(response);
            // Optionally, update the UI or perform any other actions
            },
        
        error: function(xhr, errorType, thrownError) {
            var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
            alert("Server error: " + errorMessage);
            console.log("Server error: " + xhr.responseText);
        },
        });
    });


    // Handle division change
    $('#division').on('change', function() {
        var divisionValue = $(this).val();
        getSection(divisionValue);
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



    $('#list_table').dataTable(
    
        {
        ajax: {
            
            url: "<?php echo base_url('get-employee'); ?>",
            dataSrc: '',
            type: "post",
            beforeSend: function(xhr) {
                // Set the CSRF token header
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            error: function(xhr, errorType, thrownError) {
                // Handle the error here
                alert("Ajax error occurred: "  + thrownError);
                console.log("Ajax error occurred: " + thrownError);
            }
        }, 	
        columns: [
            { data: "cnt" },
            { data: "name" },
            { data: "position" },
            { data: "division" },
            { data: "section" },
            { data: "emptype" },
            { data: "photo" },
            { data: "signature" },
            { data: "btn" },

        ],
        processing: true,
        //columnDefs: [ { orderable: false} ],
        //columnDefs: [ { orderable: false, targets: [ 4 ] } ],
        //dom: 'Bfrtip',
        ordering: true,
        pageLength: 10,
        lengthMenu: [[10, 20, 100, 1000, -1], ['10 rows', '20 rows', '100 rows', '1000 rows', 'Show all']],
        //buttons: ['pageLength', 'excel'],
        bDestroy: true,
            
            
        }
    );


});


function populateEditModal(eid) {

    $('.is-invalid').removeClass('is-invalid');
    $('.is-valid').removeClass('is-valid');
    $('.invalid-feedback').empty();
    $('.valid-feedback').empty();

     $.ajax({
        url: "<?php echo base_url('get-data'); ?>",
        type: "post",
        beforeSend: function(xhr) {
            // Set the CSRF token header
            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
        },
        error: function(xhr, errorType, thrownError) {
            var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
            alert("Server error: " + errorMessage);
            console.log("Server error: " + thrownError);
        },
        dataType: "JSON",
        data: { eid: eid },
        success: function(response) {
            // Destructure the response object for easier access
            const { id, fname, mname, lname, suffix, sex, position, section, division, contract_start, contract_end, address, bdate, personnotify, bloodtype, tin, phic, sss, pagibig, cpnum, typeemployment, nname, nameext, idnum, signature, photo } = response;

            // Set the element values using the destructured variables
            $('#employee_id').val(eid);
            $('#fname').val(fname);employee_id
            $('#mname').val(mname);
            $('#lname').val(lname);
            $('#suffix').val(suffix);
            $('#sex').val(sex);
            $('#position').val(position);
            getSelectedSection(section, division);
            getSelectedDivision(division);
            $('#contract_start').val(contract_start);
            $('#contract_end').val(contract_end);
            $('#address').val(address);
            $('#bdate').val(bdate);
            $('#personnotify').val(personnotify);
            $('#bloodtype').val(bloodtype);
            $('#tin').val(tin);
            $('#phic').val(phic);
            $('#sss').val(sss);
            $('#pagibig').val(pagibig);
            $('#cpnum').val(cpnum);
            $('#typeemployment').val(typeemployment);
            $('#nname').val(nname);
            $('#nameext').val(nameext);
            $('#idnum').val(idnum);
            $('#signature').val(signature);
            $('#photo').val(photo);
            $('#editModal').modal('show');
        }
    });
}

function initCropper() {

    const image = document.getElementById('cropperImage');
    
    // Load the image before initializing the Cropper
    image.onload = function () {
        if (cropper) {
            cropper.destroy(); // Destroy the existing Cropper instance
        }
        cropper = new Cropper(image, {
            aspectRatio: 1,
            //dragMode: 'move', // Enable moving the crop box
            cropBoxResizable: true, // Enable resizing of crop box
            cropBoxMovable: true, // Enable moving of crop box
            zoomable: false,
            autoCropArea: 1, // Allow the crop box to extend outside the picture
        });
    };

    const form = document.getElementById('uploadForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        $("#overlay").show();

        if (cropper) {
            const canvas = cropper.getCroppedCanvas();
            const croppedImageData = canvas.toDataURL('image/png');
            if (croppedImageData.length > 8) {
                var formData = new FormData(document.getElementById('uploadForm'));
                formData.append('croppedImageData', croppedImageData);

                $.ajax({
                    url: '<?= base_url('process-image'); ?>',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function(xhr) {
                        // Set the CSRF token header
                        xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                    },
                    success: function (response) {
                        // Handle the response from the server
                        console.log(response.status);
                        
                        
                        if (response.redirect) {
                            // Redirect to the specified URL
                            window.location.href = 'http://chd1.idprint.local.org/idprint/';
                            alert(response.message);
                        }
                        
                        
                    },
                    error: function (xhr, status, error) {
                        // Handle error cases
                        console.error(xhr);
                        alert("Ajax error occurred: "  + error);
                    },
                    complete: function() {
                        // Hide the overlay when AJAX is complete
                        
                        $("#overlay").hide();
                    }
                });
            }else{
                
                alert('Cropped image data is empty. No image selected or cropped.');
                $("#overlay").hide();
                
            }
        }else{
            //alert("Cropper is not available or initialized.");
            alert('Cropper is not available or initialized.');
            $("#overlay").hide();
        }
        
    });

    // Handle file input change to load the selected image
    const fileInput = document.querySelector('input[name="photoP"]');
    fileInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            image.src = URL.createObjectURL(file);
            // Destroy and reinitialize the Cropper instance
            if (cropper) {
                cropper.destroy();
            }
        }
    });

    
}


function changePhoto(eid) {
    $('#empid').val(eid);
    // Reset the image preview
    $('#cropperImage').attr('src', '');
    // Clear the input file field

    const fileInput = document.querySelector('input[name="photoP"]');
    fileInput.value = ''; // Reset the file input value

    if (cropper) {
        cropper.destroy(); // Destroy the existing Cropper instance
    }
    $('#changePhotomodal').modal('show');

}


// Function to populate sections based on the selected division
function getSection(division) {
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
            response.forEach(function(item) {
                var value = item.value;
                var section = item.section;
                optionsHtml += `<option value="${value}">${section}</option>`;
            });
            $('#section').html(optionsHtml);
        },
        error: function(xhr, status, error) {
            // Handle the error response
            console.log(xhr.responseText);
            // Optionally, display an error message or perform any other actions
        }
    });
}



function getSelectedSection(section, division) {
    var sectionValue = section;

    $.ajax({
        url: "<?php echo base_url('get-section'); ?>",
        type: "post",
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
        },
        error: function(xhr, errorType, thrownError) {
            var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
            alert("Server error: " + errorMessage);
            console.log("Server error: " + thrownError);
        },
        dataType: "JSON",
        data: { division: division },
        success: function(response) {
            var optionsHtml = '';
            optionsHtml += `<option value="" >Choose Section</option>`;
            response.forEach(function(item) {
                var value = item.value;
                var section = item.section;
                var selected = (value === sectionValue) ? 'selected' : '';
                optionsHtml += `<option value="${value}" ${selected}>${section}</option>`;
            });
            $('#section').html(optionsHtml);
        }
    });
}


function getSelectedDivision(division) {
    var divValue = division;

    $.ajax({
        url: "<?php echo base_url('get-division'); ?>",
        type: "post",
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
        },
        error: function(xhr, errorType, thrownError) {
            var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
            alert("Server error: " + errorMessage);
            console.log("Server error: " + thrownError);
        },
        dataType: "JSON",
        data: { division: division },
        success: function(response) {
            var optionsHtml = '';
            optionsHtml += `<option value="" >Choose Division</option>`;
            response.forEach(function(item) {
                var value = item.value;
                var division = item.division;
                var selected = (value === divValue) ? 'selected' : '';
                optionsHtml += '<option value="' + value + '" ' + selected + '>' + division + '</option>';
            });
            $('#division').html(optionsHtml);
        }
    });
}




</script>

<?= $this->endSection(); ?>