const docDestination = {
    init: function() {
        this.bindEvents();
    },
  
    bindEvents: function() {

        $(document).on('click', '.change-desti', this.changeDestinationModal);

        $('#change_office_destination').on('change', this.populateActionOfficerChangeByOffice.bind(this));

        docDestination.loadDocumentControls(routeno);
        
        $('#submitChangeDestination').on('click', this.submitChangeDestination.bind(this));

        $(document).on('click', '.delete-desti', this.deleteDestination);

        $(document).on('click', '.undone-doc', this.undoneDocument);

    },

    changeDestinationModal: function() {

        var id = $(this).data('id');
        var modal = $('#destination-modal-change');

        $.ajax({
            url: base_url + '/changeDestinationData',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            beforeSend: function(xhr) {

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();

            },
            success: function(data) {

                if (data.success) {

                    docDestination.populateModalChangeDest(modal, data);
                    modal.modal('show');

                } else {
                    alert(data.message);
                }

            },
            error: function(xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert("Error Code " + xhr.status + ": " + error + "\n" +
                        "Message: " + xhr.responseJSON.error);
                } else {
                    alert('An unknown error occurred.');
                }
            },
            complete: function() {
                $("#overlay").hide();

            }
        });

    },

    populateModalChangeDest: function(modal, data) {
        
        var detaildata = data.detaildata;
        //docDestination.clearFormValidation();
        $('#dd').val(detaildata.doc_detailno);
        docDestination.populateOfficeDestinationChange(data.office, detaildata.office_destination, data.otherDestinations);
        docDestination.populateActionOfficerChange(data.officeuser, detaildata.action_officer);
        docDestination.populateActionRequiredChange(data.action_required, detaildata.action_required);

    },

    populateOfficeDestinationChange: function(options, selected, othdest) {

        const selectElement = $('#change_office_destination').empty(); 

        let selectedArray = [];
        let otherDestArray = [];

        if (Array.isArray(selected)) {
            selectedArray = selected.map(office => office.trim());
        } else if (selected !== "") {
            selectedArray = selected.split(',').map(office => office.trim());
        }

        if (Array.isArray(othdest)) {
            otherDestArray = othdest.map(office => office.trim());
        } else if (othdest !== "") {
            otherDestArray = othdest.split(',').map(office => office.trim());
        }

        options.forEach(function(office) {
            const option = new Option(office.officename, office.officecode);
    
            if (selectedArray.includes(office.officecode)) {
                option.selected = true;
            }

            if (otherDestArray.includes(office.officecode)) {
                option.disabled = true;
            }
    
            selectElement.append(option);
        });

    },

    populateActionOfficerChange: function(options, selected) {

        const selectElement = $('#change_action_officer').empty(); 

        var selectedArray = [];

        if (Array.isArray(selected)) {
            selectedArray = selected.map(actionofficer => actionofficer.trim());
        } else if (selected !== "") {
            selectedArray = selected.split(',').map(actionofficer => actionofficer.trim());
        }

        options.forEach(function(actionofficer) {
            const option = new Option(actionofficer.lastname + ", " + actionofficer.firstname + " " + actionofficer.middlename.charAt(0) + ".", actionofficer.empcode);
    
            if (selectedArray.includes(actionofficer.empcode)) {
                option.selected = true;
            }

            selectElement.append(option);
        });

    },

    populateActionOfficerChangeByOffice: function(event) {

        const selectElement = $('#change_action_officer').empty(); 

        const selectElementOffice = $(event.target);
        officedestination = selectElementOffice.val();

        $.ajax({
            url: base_url + '/populateActOffByOffice',
            type: 'POST',
            data: { officedestination: officedestination },
            dataType: 'json',
            beforeSend: function(xhr) {

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();

            },
            success: function(data) {

                let options = data.officeuser;

                if (data.success) {
                    
                    options.forEach(function(actionofficer) {
                        const option = new Option(actionofficer.lastname + ", " + actionofficer.firstname + " " + actionofficer.middlename.charAt(0) + ".", actionofficer.empcode);
            
                        selectElement.append(option);
                    });

                } else {
                    alert(data.message);
                }

            },
            error: function(xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert("Error Code " + xhr.status + ": " + error + "\n" +
                        "Message: " + xhr.responseJSON.error);
                } else {
                    alert('An unknown error occurred.');
                }
            },
            complete: function() {
                $("#overlay").hide();

            }
        });


    },

    populateActionRequiredChange: function(options, selected) {

        const selectElement = $('#change_action_required').empty(); 

        let selectedArray = [];

        if (Array.isArray(selected)) {
            selectedArray = selected.map(actionrequired => actionrequired.trim());
        } else if (selected !== "") {
            selectedArray = selected.split(',').map(actionrequired => actionrequired.trim());
        }

        options.forEach(function(actionrequired) {
            const option = new Option(actionrequired.reqaction_desc, actionrequired.reqaction_code);
    
            if (selectedArray.includes(actionrequired.reqaction_code)) {
                option.selected = true;
            }

            selectElement.append(option);
        });

    },


    initSelect2ForNewRow: function(newRow) {
        
        $(newRow).find('.select-select2').select2({
            width: '100%'
        });

    },

    getSelectedOffices: function() {
        // Collect selected values from all dropdowns
        return $('#destinationContainer .office_destination select').map(function() {
            return $(this).val();
        }).get();

    },


    handleValidationErrorsDynamic: function(errors) {
        
        $('.help-block').text('');
        $('.help-block').removeClass('text-error');

        $.each(errors.office_destination, function(index, error) {
            var errorElement = $('.destination-group').eq(index).find('.office_destinationMessage');
            var parentDiv = $('.destination-group').eq(index).find('.office_destinationDiv');
            
            errorElement.text(error);
            
            parentDiv.addClass('has-error');
        });
    
        if (errors.action_officer) {
            $.each(errors.action_officer, function(index, error) {
                var errorElement = $('.destination-group').eq(index).find('.action_officerMessage');
                var parentDiv = $('.destination-group').eq(index).find('.action_officerDiv');
                
                errorElement.text(error);
                
                parentDiv.addClass('has-error');
            });
        }

        if (errors.action_required) {
            $.each(errors.action_required, function(index, error) {
                var errorElement = $('.destination-group').eq(index).find('.action_requiredMessage');
                var parentDiv = $('.destination-group').eq(index).find('.action_requiredDiv');
                
                errorElement.text(error);
                
                parentDiv.addClass('has-error');
            });
        }
    },

    loadDocumentControls: function(routeno){
        $.ajax({
            url: base_url + 'admin/document_management/destination/table',
            type: 'POST',
            data: {routeno: routeno},
            dataType: 'json',
            beforeSend: function(xhr) {
    
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
    
            },
            success: function(e) {
                const response = e.data;

                let content = '';
                response.forEach(control => {

                    var theme = 'block-background-success-light';
                    var del = '';
                    if(control.deleted){
                        theme = 'block-background-danger';
                    }
                    if(control.delete){
                        del = `<a class="btn btn-effect-ripple btn-danger enable-tooltip delete-desti" data-control="${control.controlno}" data-id="${control.detailno}" data-toggle='modal' title='Delete Destination'><i class="fa fa-trash"></i> Delete Document Destination</a>`;
                    }
                    content += `
                    <div class="block">
                        <div class="block-title ${theme}">
                            <div class="block-options pull-right">
                                ${del}
                            </div>
                            <h2>Destination Details for <b><u>${control.control_id}</u></b></h2>
                        </div>
                        <div>
                            Date and Time Created:  <b>${control.date_time_log}</b><br>
                            <table class="table table-vcenter table-bordered table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <th style="width: 1%;font-size: 10px;text-align:center;">Sequence No.</th>
                                        <th style="width: 8%;font-size: 10px;text-align:center;">Office Destination</th>
                                        <th style="width: 10%;font-size: 10px;text-align:center;">Action Officer</th>
                                        <th style="width: 5%;font-size: 10px;text-align:center;">Action Required</th>
                                        <th style="width: 10%;font-size: 10px;text-align:center;">Received By</th>
                                        <th style="width: 6%;font-size: 10px;text-align:center;">Received Date/Time</th>
                                        <th style="width: 10%;font-size: 10px;text-align:center;">Action Taken By</th>
                                        <th style="width: 6%;font-size: 10px;text-align:center;">Action Date/Time</th>
                                        <th style="width: 6%;font-size: 10px;text-align:center;">Action Done</th>
                                        <th style="width: 10%;font-size: 10px;text-align:center;">Released By</th>
                                        <th style="width: 6%;font-size: 10px;text-align:center;">Released Date/Time</th>
                                        <th style="width: 8%;font-size: 10px;text-align:center;">Remarks</th>
                                        <th style="width: 7%;font-size: 8px;text-align:center;">Time Elapsed (Received to Received)</th>
                                        <th style="width: 7%;font-size: 8px;text-align:center;">Time Elapsed (Received to Release)</th>
                                        <th style="width: 7%;font-size: 8px;text-align:center;">Remarks</th>
                                        <th style="width: 7%;font-size: 8px;text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                    
                    control.destinations.forEach(destination => {
                        content += `
                            <tr>
                                <td>${destination.sequence}</td>
                                <td>${destination.officeshort}</td>
                                <td><b>${destination.action_officer}</b></td>
                                <td>${destination.action_required}</td>
                                <td><b>${destination.action_officer_rcv}</b></td>
                                <td>${destination.datetime_rcv}</td>
                                <td><b>${destination.action_officer_act}</b></td>
                                <td>${destination.datetime_act}</td>
                                <td>${destination.action_done}</td>
                                <td><b>${destination.action_officer_rel}</b></td>
                                <td>${destination.datetime_rel}</td>
                                <td>${destination.remarks}</td>
                                <td>${destination.rcvTorcv}</td>
                                <td>${destination.rcvTorel}</td>
                                <td>${destination.remarks2}</td>
                                <td class='text-center'>${destination.action}</td>
                            </tr>`;
                    });
                    
                    content += `
                                </tbody>
                            </table>
                        </div>
                    </div>`;

                    if(control.oth_dest){
                        control.oth_dest.forEach(oth_dest => {
                            content += `
                            <div class="widget">
                                <div class="widget-content widget-content-mini themed-background-warning text-dark-op">
                                    <span class="text-dark">Destination Details for <b><u>${oth_dest.ref_control_id}</u></b></span>
                                </div>
                                <div class="widget-content">
                                Originating Office: <b>${oth_dest.reference.office}</b><br>
                                Subject:  <b>${oth_dest.reference.subject}</b><br>
                                Document Type:  <b>${oth_dest.reference.doctype}</b><br>
                                Date and Time Created:  <b>${oth_dest.reference.date_log} ${oth_dest.reference.time_log}</b><br>
                                    <table class="table table-vcenter table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th style="width: 1%;font-size: 10px;text-align:center;">Sequence No.</th>
                                                <th style="width: 8%;font-size: 10px;text-align:center;">Office Destination</th>
                                                <th style="width: 10%;font-size: 10px;text-align:center;">Action Officer</th>
                                                <th style="width: 5%;font-size: 10px;text-align:center;">Action Required</th>
                                                <th style="width: 10%;font-size: 10px;text-align:center;">Received By</th>
                                                <th style="width: 6%;font-size: 10px;text-align:center;">Received Date/Time</th>
                                                <th style="width: 10%;font-size: 10px;text-align:center;">Action Taken By</th>
                                                <th style="width: 6%;font-size: 10px;text-align:center;">Action Date/Time</th>
                                                <th style="width: 6%;font-size: 10px;text-align:center;">Action Done</th>
                                                <th style="width: 10%;font-size: 10px;text-align:center;">Released By</th>
                                                <th style="width: 6%;font-size: 10px;text-align:center;">Released Date/Time</th>
                                                <th style="width: 8%;font-size: 10px;text-align:center;">Remarks</th>
                                                <th style="width: 7%;font-size: 8px;text-align:center;">Time Elapsed (Received to Received)</th>
                                                <th style="width: 7%;font-size: 8px;text-align:center;">Time Elapsed (Received to Release)</th>
                                                <th style="width: 7%;font-size: 8px;text-align:center;">Remarks</th>
                                                <th style="width: 7%;font-size: 8px;text-align:center;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                            
                            oth_dest.ref_destinations.forEach(ref_destination => {
                                content += `
                                    <tr>
                                        <td>${ref_destination.sequence}</td>
                                        <td>${ref_destination.officeshort}</td>
                                        <td><b>${ref_destination.action_officer}</b></td>
                                        <td>${ref_destination.action_required}</td>
                                        <td><b>${ref_destination.action_officer_rcv}</b></td>
                                        <td>${ref_destination.datetime_rcv}</td>
                                        <td><b>${ref_destination.action_officer_act}</b></td>
                                        <td>${ref_destination.datetime_act}</td>
                                        <td>${ref_destination.action_done}</td>
                                        <td><b>${ref_destination.action_officer_rel}</b></td>
                                        <td>${ref_destination.datetime_rel}</td>
                                        <td>${ref_destination.remarks}</td>
                                        <td>${ref_destination.rcvTorcv}</td>
                                        <td>${ref_destination.rcvTorel}</td>
                                        <td>${ref_destination.remarks2}</td>
                                        <td class='text-center'>${ref_destination.action}</td>
                                    </tr>`;
                            });
                            
                            content += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>`;
                        });
                    }

                });



    
                // Inject content into the DOM
                $('#documentControlsContainer').html(content);

                requestAnimationFrame(() => {
                    $("#overlay").hide();
                });
            },
            error: function(xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert("Error Code " + xhr.status + ": " + error + "\n" +
                        "Message: " + xhr.responseJSON.error);
                } else {
                    alert('An unknown error occurred...' + error);
                }

                $("#overlay").hide();
            }
        });
    },


    submitChangeDestination: function(event) {
        event.preventDefault();

        var form = $('#changeDestinationForm')[0];

        var formData = new FormData(form);

        var formData = $('#changeDestinationForm').serialize();

        docDestination.clearFormValidation();
        // Make AJAX request
        $.ajax({
            url: base_url + '/submitChangeDestination',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function(xhr) {

                $("#overlay").show();

            },
            success: function(response) {
                try {
                    if (response.success) {
                        
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        $('#changeDestinationForm')[0].reset();

                        $('#destination-modal-change').modal('hide');
                        
                        docDestination.clearFormValidation();

                        docDestination.loadDocumentControls(response.routeno);

                    } else {
                        if(response.formnotvalid){
                            handleValidationErrors(response.data);
                            
                        }else{
                            alert(response.message);
                        }

                    }
                } catch (error) {
                    console.error("Error processing response:", error);
                }
            },
            error: function(xhr, errorType, thrownError) {
                if (xhr.status === 403 || xhr.status === 405) {
                    alert(xhr.responseText);
                    console.log("Server error: " + xhr.responseText);
                } else {
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
                    console.log("Server error: " + xhr.responseText);
                }
                
            },
            complete: function() {
            
                $("#overlay").hide();
            }
        });
    },


    deleteDestination: function() {
            
        var id = $(this).data('id');
        var control = $(this).data('control');
    
        Swal.fire({
            title: 'Are you sure you want to delete this Office Destination?\n' + control,
            text: "You will need to enter your password to delete this destination.",
            icon: 'warning',
            input: 'password',
            inputPlaceholder: 'Enter your password',
            inputAttributes: {
                autocapitalize: 'off',
                autocomplete: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            preConfirm: (password) => {
                if (!password) {
                    Swal.showValidationMessage('Password is required');
                }

                return docDestination.deleteDestinationAjax(password,id,control)
                    .then((response) => {
                        if (response.status) {
                            return response; // triggers Swal confirm result
                        } else {
                            Swal.showValidationMessage(response.message);
                            return null;
                        }
                    })
                    .catch((error) => {
                        Swal.showValidationMessage(error.message || 'An error occurred');
                        return null;
                    });
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                Swal.fire(
                    'Document Deleted!',
                    'Successfully deleted document.',
                    'success'
                ).then(() => {
                    docDestination.loadDocumentControls(result.value.routeno);
                })
            }
        });
    },


    deleteDestinationAjax: function(password,id,control) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: base_url + '/admin/document_management/destination/delete',
                type: 'POST',
                data: { id: id, password: password, control: control },
                dataType: 'json',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                    $("#overlay").show();

                },
                success: function(data) {
                    if (data.success) {
                        resolve({ status: true, message: data.message, routeno: data.routeno });
                    } else {
                        resolve({ status: false, message: data.message });
                    }
                },
                error: function(xhr, status, error) {
                    let message = 'An unknown error occurred.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        message = "Error Code " + xhr.status + ": " + error + "\n" +
                                "Message: " + xhr.responseJSON.error;
                    }
                    reject({ status: false, message });
                },
                complete: function() {
                    $("#overlay").hide();

                }
            });
        });
    },


    //UNDONE METHODS
    undoneDocument: function() {
            
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure you want to undone this document?',
            text: "You will need to enter your password to undone this document.",
            icon: 'warning',
            input: 'password',
            inputPlaceholder: 'Enter your password',
            inputAttributes: {
                autocapitalize: 'off',
                autocomplete: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Undone',
            cancelButtonText: 'Cancel',
            preConfirm: (password) => {
                if (!password) {
                    Swal.showValidationMessage('Password is required');
                }

                return docDestination.undoneDocumentAjax(id, password)
                    .then((response) => {
                        if (response.status) {
                            return response; // triggers Swal confirm result
                        } else {
                            Swal.showValidationMessage(response.message);
                            return null;
                        }
                    })
                    .catch((error) => {
                        Swal.showValidationMessage(error.message || 'An error occurred');
                        return null;
                    });
            }
        }).then((result) => {

            if (result.isConfirmed) {

                Swal.fire(
                    'Document Undone!',
                    'Please check "For Release" Page.',
                    'success'
                ).then(() => {
                    docDestination.loadDocumentControls(result.value.routeno);
                })
            }

        });
    },


    undoneDocumentAjax: function(id, password) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: base_url + '/admin/document_management/destination/undone',
                type: 'POST',
                data: { id: id, password: password },
                dataType: 'json',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                    $("#overlay").show();
                },
                success: function(data) {
                    if (data.success) {
                        resolve({ status: true, message: data.message, routeno: data.routeno });
                    } else {
                        resolve({ status: false, message: data.message });
                    }
                },
                error: function(xhr, status, error) {
                    let message = 'An unknown error occurred.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        message = "Error Code " + xhr.status + ": " + error + "\n" +
                                "Message: " + xhr.responseJSON.error;
                    }
                    reject({ status: false, message });
                },
                complete: function() {
                    $("#overlay").hide();

                }
            });
        });
    },

    clearFormValidation: function() {

        $('.has-success').removeClass('has-success');
        $('.has-error').removeClass('has-error');
        $('.help-block').empty();

        $('.select-select2').trigger("change.select2");

    },

  };







