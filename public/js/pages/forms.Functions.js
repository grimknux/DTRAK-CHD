
var selectedIdsRel = [];

const receiveApp = {

    init: function() {
        this.bindEvents();
    },
  
    bindEvents: function() {
        $('#receive-table tbody').on('click', '.rcv-modal', this.handleModalClick);

        $('#receive-table tbody').on('click', '.insta-rcv', this.handleInstaRcvClick);

        $('#submitBulkReceive').on('click', this.handleBulkRcvClick);

        $('#receive-form').on('submit', this.handleFormSubmit.bind(this));

        //ACTION EVENTS
        $('#action-table tbody').on('click', '.act-modal', this.handleModalClickAct);

        $('#actionForm').on('submit', this.submitAction.bind(this));

        $('#submitBulkAction').on('click', this.handleBulkActClick);

        $('#action-table tbody').on('click', '.insta-act', this.handleInstaActClick);

        //RELEASE EVENTS
        $('#release-table tbody').on('click', '.rel-modal', this.handleModalClickRel);

        $('#releaseForm').on('submit', this.submitRelease.bind(this));
 
        $('#submitBulkRelease').on('click', this.handleBulkRelClick);
         
        $('#rel_destination').on('change', this.selectActionOfficerRel.bind(this));
 
        $('#release-table tbody').on('click', '.done-act', this.handleDoneActClick); 

        $('#release-table').on('change', '.row-checkbox', this.handleReleaseCheckbox);

        $('#bulkRelease').on('click', this.bulkReleaseModal);
         
        $('#bulkrel_officedestination').on('change', this.selectActionOfficerBulkRel.bind(this));

        $('#release-table tbody').on('click', '.diss-modal', this.handleModalClickDiss);

        $('#destinationContainer').on('change', '.diss_office_destination select', this.updateOptions.bind(this));

        $('#destinationContainer').on('change', '.diss_office_destination select', this.updateActionOfficers.bind(this));

        $('#addDisseminationBtn').on('click', this.addDisseminationRow).bind(this);

        $('#destinationContainer').on('click', '.removeDestinationBtn', this.removeDestinationRow.bind(this));
        
        $('#addDisseminate').on('click', this.disseminationFormSubmit.bind(this));

        $('#resetDisseminationForm').on('click', this.resetDestination.bind(this));

        $('#disseminate-modal-add').on('hidden.bs.modal', function () {
            receiveApp.closeDestinationModal(); // Call the function when the modal closes
        });

        $('#release-table tbody').on('click', '.done-rel', this.handleModalClickTag);

        //FORWARD EVENTS
        $('#action-table tbody').on('click', '.fwd-modal', this.handleModalClickFwd);

        $('#fwd_destination').on('change', this.selectActionOfficerFwd.bind(this));

        $('#forwardForm').on('submit', this.submitForward.bind(this));
        
        //RETURN EVENTS
        $('#action-table tbody').on('click', '.ret-modal', this.handleModalClickRet);

        $('#returnForm').on('submit', this.submitReturn.bind(this));


        //RELEASED EVENTS
        $('#released-table tbody').on('click', '.change-desti', this.handleModalClickRelChange);

        $('#submitChangeDestination').on('click', this.submitChangeDestination.bind(this));

        $('#change_office_destination').on('change', this.populateActionOfficerChangeByOffice.bind(this));

        $('#add_office_destination').on('change', this.populateActionOfficerAddByOffice.bind(this));

        $('#released-table tbody').on('click', '.add-desti', this.handleModalClickRelAdd);

        $('#submitAddDestination').on('click', this.submitAddDestination.bind(this));


        //UNDOne DOCUMENT
        $('#undone-table tbody').on('click', '.undone-doc', this.undoneDocument);

    },
  
    handleModalClick: function() {

        var docdetail = $(this).data('docdetail');
        var modal = $('#receive-modal');
    
        $.ajax({
            url: base_url + '/receiveData',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function(xhr) {

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();

            },
            success: function(data) {

                if (data.success) {
                    receiveApp.populateModalRcv(modal, data);
                    modal.modal('show');

                } else {
                    //alert(data.message);
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: data.message,
                    });
                    if(data.reload){
                        $('#receive-table').DataTable().ajax.reload(null, false);
                    }
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

    handleInstaRcvClick: function() {
        var a = $(this).data('did');

        var formData = new FormData();
        formData.append('detailno', a);
        formData.append('csrf_token', csrfToken);

        $.ajax({
            url: base_url + "/receiveDoc",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(xhr) {

                $("#overlay").show();

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function(response) {
                try {
                    if (response.success) {
                        //alert(response.message);
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        $('#receive-table').DataTable().ajax.reload(null, false); 

                    } else {

                        //alert(response.message);
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        if(response.reload){
                            $('#receive-table').DataTable().ajax.reload(null, false);
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
                    //alert("Server error: " + errorMessage);
                    console.log("Server error: " + xhr.responseText);
                }
                
            },
            complete: function() {
            
                $("#overlay").hide();
            }
        });

    },

    handleBulkRcvClick: function() {

        var formData = new FormData();
        formData.append('detailno', JSON.stringify(selectedIdsRcv));
        formData.append('csrf_token', csrfToken);

        $.ajax({
            url: base_url + "/receiveBulkDoc",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(xhr) {

                $("#overlay").show();

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function(response) {
                try {
                    if (response.success) {
                        //alert(response.message);
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        $('#viewReceiveData').modal('hide');

                        $('#receive-table').DataTable().ajax.reload(null, false); 
                        
                        selectedIdsRcv = [];
                        
                    } else {

                        //alert(response.message);
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        if(response.reload){
                            $('#viewReceiveData').modal('hide');
                            $('#receive-table').DataTable().ajax.reload(null, false);
                            selectedIdsRcv = [];
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
                    //alert("Server error: " + errorMessage);
                    console.log("Server error: " + xhr.responseText);
                }
                
            },
            complete: function() {
            
                $("#overlay").hide();
            }
        });

    },


    handleFormSubmit: function(event) {
        event.preventDefault();
    
        var formData = $(event.target).serialize();
    
        $.ajax({
            url: base_url + "/receiveDoc",
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function(xhr) {

                $("#overlay").show();

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function(response) {
                try {
                    if (response.success) {
                        //alert(response.message);
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        $('#receive-modal').modal('hide');

                        $('#receive-form')[0].reset();

                        $('#receive-table').DataTable().ajax.reload(null, false); 

                    } else {

                        //alert(response.message);
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        if(response.reload){
                            $('#receive-modal').modal('hide');
                            $('#receive-form')[0].reset();
                            $('#receive-table').DataTable().ajax.reload(null, false);
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

    
    populateModalRcv: function(modal, data) {
        modal.find('#routeno').html(data.routeno);
        modal.find('#controlno').html(data.controlno);
        modal.find('#detailno').val(data.detailno);
        modal.find('#subject').html(data.subject);
        modal.find('#doctype').html(data.doctype);
        modal.find('#origoffice').html(data.origoffice);
        modal.find('#prevoffice').html(data.prevoffice);
        modal.find('#origemp').html(data.origemp);
        modal.find('#exofficecode').html(data.exofficecode);
        modal.find('#exempname').html(data.exempname);
        modal.find('#pageno').html(data.pageno);
        modal.find('#attachment').html(data.attachment);
        modal.find('#emp').html(data.receiveby);
        modal.find('#daterec').val(data.daterec);
        modal.find('#timerec').val(data.timerec);
    },


    //ACTION FUNCTIOns
    handleModalClickAct: function() {

        var docdetail = $(this).data('docdetail');
        var modal = $('#action-modal');
    
        $.ajax({
            url: base_url + '/actionData',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function(xhr) {

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();

            },
            success: function(data) {

                if (data.success) {

                    $('#actionForm')[0].reset();
                    $('#remarksRow').remove();
                    receiveApp.populateModalAct(modal, data);
                    modal.modal('show');

                } else {
                    //alert(data.message);
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: data.message,
                    });
                    if(data.reload){
                        $('#action-table').DataTable().ajax.reload(null, false);
                       
                    }
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


    populateModalAct: function(modal, data) {

        modal.find('#routeno').html(data.routeno);
        modal.find('#dcon').html(data.controlno);
        modal.find('#controlno').html(data.controlno);
        modal.find('#detailno').val(data.detailno);
        modal.find('#subject').html(data.subject);
        modal.find('#doctype').html(data.doctype);
        modal.find('#origoffice').html(data.origoffice);
        modal.find('#prevoffice').html(data.prevoffice);
        modal.find('#origemp').html(data.origemp);
        modal.find('#exofficecode').html(data.exofficecode);
        modal.find('#exempname').html(data.exempname);
        modal.find('#pageno').html(data.pageno);
        modal.find('#attachment').html(data.attachment);
        modal.find('#emp').html(data.actionby);
        modal.find('#dateact').val(data.daterec);
        modal.find('#timeact').val(data.timerec);

        let selectActionTaken = modal.find('#act_taken');

        receiveApp.populateActionTaken(selectActionTaken, data.actiontaken, data.actiondone);

        selectActionTaken.trigger("change.select2");
    },

    populateActionTaken: function(selectElement, options, selected = ""){

        selectElement.empty();

        const selectedArray = selected !== "" ? selected.split(',').map(at => at.trim()) : [];
    
        options.forEach(function(at) {
            const option = new Option(at.action_desc, at.action_code); 
    
            if (selectedArray.includes(at.action_code)) {
                option.selected = true;
            }
    
            selectElement.append(option);
        });
    },

    submitAction: function(event) {
        event.preventDefault();

        // Explicitly reference the form
        var form = $('#actionForm')[0]; // Get the raw DOM element

        // Create a new FormData object
        var formData = new FormData(form);


        receiveApp.clearFormValidation();
        // Make AJAX request
        $.ajax({
            url: base_url + '/actionDoc',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
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

                        $('#action-modal').modal('hide');

                        $('#actionForm')[0].reset();

                        $('#action-table').DataTable().ajax.reload(null, false); 
                        
                        receiveApp.clearFormValidation();

                    } else {
                        if(response.formnotvalid){
                            handleValidationErrors(response.data);
                            
                        }else{
                            //alert(response.message);
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: response.message,
                            });
                            if(response.reload){
                                $('#action-table').DataTable().ajax.reload(null, false);
                                $('#actionForm')[0].reset();
                                $('#action-modal').modal('hide');
                            }
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
                    //alert("Server error: " + errorMessage);
                    console.log("Server error: " + xhr.responseText);
                }
                
            },
            complete: function() {
            
                $("#overlay").hide();
            }
        });
    },


    handleInstaActClick: function() {
        var a = $(this).data('did');

        var formData = new FormData();
        formData.append('detailno', a);
        formData.append('csrf_token', csrfToken);

        $.ajax({
            url: base_url + "/instaActionDoc",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(xhr) {

                $("#overlay").show();

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function(response) {
                try {
                    if (response.success) {
                        //alert(response.message);
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        $('#action-table').DataTable().ajax.reload(null, false); 

                    } else {

                        //alert(response.message);
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        if(response.reload){
                            $('#action-table').DataTable().ajax.reload(null, false);
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
                    //alert("Server error: " + errorMessage);
                    console.log("Server error: " + xhr.responseText);
                }
                
            },
            complete: function() {
            
                $("#overlay").hide();
            }
        });

    },


    handleBulkActClick: function() {

        var formData = new FormData();
        formData.append('detailno', JSON.stringify(selectedIdsAct));
        formData.append('csrf_token', csrfToken);

        $.ajax({
            url: base_url + "/actionBulkDoc",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(xhr) {

                $("#overlay").show();

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function(response) {
                try {
                    if (response.success) {
                        //alert(response.message);
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        $('#viewActionData').modal('hide');

                        $('#action-table').DataTable().ajax.reload(null, false); 
                        
                        selectedIdsAct = [];
                        
                    } else {

                        //alert(response.message);
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        if(response.reload){
                            $('#viewActionData').modal('hide');
                            $('#action-table').DataTable().ajax.reload(null, false);
                            selectedIdsAct = [];
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
                    //alert("Server error: " + errorMessage);
                    console.log("Server error: " + xhr.responseText);
                }
                
            },
            complete: function() {
            
                $("#overlay").hide();
            }
        });

    },


    //RELEASE FUNCTIONS
    handleModalClickRel: function() {

        var docdetail = $(this).data('docdetail');
        var modal = $('#release-modal');
    
        $.ajax({
            url: base_url + '/releaseData',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function(xhr) {

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();

            },
            success: function(data) {

                if (data.success) {

                    $('#releaseForm')[0].reset();
                    $('#remarksRow').remove();
                    receiveApp.populateModalRel(modal, data);
                    modal.modal('show');

                } else {
                    //alert(data.message);
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: data.message,
                    });
                    if(data.reload){
                        $('#release-table').DataTable().ajax.reload(null, false);
                       
                    }
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


    populateModalRel: function(modal, data) {

        modal.find('#rel_routeno').html(data.routeno);
        modal.find('#rel_dcon').html(data.controlno);
        modal.find('#rel_detailno').val(data.detailno);
        modal.find('#rel_subject').html(data.subject);
        modal.find('#rel_doctype').html(data.doctype);
        modal.find('#rel_origoffice').html(data.origoffice);
        modal.find('#rel_prevoffice').html(data.prevoffice);
        modal.find('#rel_origemp').html(data.origemp);
        modal.find('#rel_exofficecode').html(data.exofficecode);
        modal.find('#rel_exempname').html(data.exempname);
        modal.find('#rel_pageno').html(data.pageno);
        modal.find('#rel_attachment').html(data.attachment);
        modal.find('#rel_relremarks').val(data.remarks);
        modal.find('#rel_emp').html(data.forwardby);
        modal.find('#daterel').val(data.daterec);
        modal.find('#timerel').val(data.timerec);

        let selectRelDestination = modal.find('#rel_destination');
        let selectRelActRequire = modal.find('#rel_actionrequire');
        let selectRelEmployee = modal.find('#rel_destemp').empty();
        selectRelEmployee.append('<option value="">Please select Destination Employee</option>');

        receiveApp.populateReleaseDest(selectRelDestination, data.officelist);
        receiveApp.populateReleaseActionReq(selectRelActRequire, data.actionrequirelist);

        selectRelDestination.trigger("change.select2");
        selectRelEmployee.trigger("change.select2");
    },


    populateReleaseDest: function(selectElement, options){

        selectElement.empty();
        selectElement.append('<option value="">Please select Office Destination</option>');
        options.forEach(function(fd) {
            const option = new Option(fd.officename, fd.officecode); 

            selectElement.append(option);
        });
    },

    populateReleaseActionReq: function(selectElement, options){

        selectElement.empty();
        selectElement.append('<option value="">Please select Action Required</option>');
        options.forEach(function(ar) {
            const option = new Option(ar.reqaction_desc, ar.reqaction_code); 

            selectElement.append(option);
        });
        
    },

    selectActionOfficerRel: function(event) {

        var selectElement = $('#rel_destemp').empty(); 
        const selectElementOffice = $(event.target);
        var officedestination = selectElementOffice.val();

        receiveApp.selectActionOfficer(selectElement,officedestination)

    },

    submitRelease: function(event) {
        event.preventDefault();

        // Explicitly reference the form
        var form = $('#releaseForm')[0]; // Get the raw DOM element

        // Create a new FormData object
        var formData = new FormData(form);


        receiveApp.clearFormValidation();
        // Make AJAX request
        $.ajax({
            url: base_url + '/releaseDoc',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
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

                        $('#release-modal').modal('hide');

                        $('#releaseForm')[0].reset();

                        $('#release-table').DataTable().ajax.reload(null, false); 
                        
                        receiveApp.clearFormValidation();

                    } else {
                        if(response.formnotvalid){
                            handleValidationErrors(response.data);
                            
                        }else{
                            //alert(response.message);
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: response.message,
                            });
                            if(response.reload){
                                $('#release-table').DataTable().ajax.reload(null, false);
                                $('#releaseForm')[0].reset();
                                $('#release-modal').modal('hide');
                            }
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
                    //alert("Server error: " + errorMessage);
                    console.log("Server error: " + xhr.responseText);
                }
                
            },
            complete: function() {
            
                $("#overlay").hide();
            }
        });
    },


    handleDoneActClick: function() {
        var a = $(this).data('did');

        var formData = new FormData();
        formData.append('detailno', a);
        formData.append('csrf_token', csrfToken);

        $.ajax({
            url: base_url + "/instaActionDoc",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(xhr) {

                $("#overlay").show();

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function(response) {
                try {
                    if (response.success) {
                        //alert(response.message);
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        $('#action-table').DataTable().ajax.reload(null, false); 

                    } else {

                        //alert(response.message);
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        if(response.reload){
                            $('#action-table').DataTable().ajax.reload(null, false);
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
                    //alert("Server error: " + errorMessage);
                    console.log("Server error: " + xhr.responseText);
                }
                
            },
            complete: function() {
            
                $("#overlay").hide();
            }
        });

    },


    handleBulkRelClick: function() {

        var formData = new FormData();
        formData.append('detailno', JSON.stringify(selectedIdsRel));
        formData.append('csrf_token', csrfToken);
        formData.append('bulkrel_officedestination', $('#bulkrel_officedestination').val());
        formData.append('bulkrel_actionofficer', $('#bulkrel_actionofficer').val());
        formData.append('bulkrel_actionrequire', $('#bulkrel_actionrequire').val());

        $.ajax({
            url: base_url + "/releaseBulkDoc",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(xhr) {

                $("#overlay").show();

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function(response) {
                try {
                    if (response.success) {
                        //alert(response.message);

                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        $('#viewReleaseData').modal('hide');

                        $('#release-table').DataTable().ajax.reload(null, false); 
                        
                        selectedIdsRel = [];
                        
                        console.log(response.data);
                    } else {

                        if(response.formnotvalid){
                            handleValidationErrors(response.data);
                        }else{
                            //alert(response.message);
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: response.message,
                            });
                            
                            if(response.reload){
                                $('#viewReleaseData').modal('hide');
                                $('#release-table').DataTable().ajax.reload(null, false);
                                selectedIdsRel = [];
                            }
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
                    //alert("Server error: " + errorMessage);
                    console.log("Server error: " + xhr.responseText);
                }
                
            },
            complete: function() {
            
                $("#overlay").hide();
            }
        });

    },


    handleReleaseCheckbox: function() {

        var rowId = $(this).data('id');
        var controlId = $(this).data('control');
        var subj = $(this).data('subject');
        var doctype = $(this).data('doctype');
        var origoffice = $(this).data('origoffice');
        var actioncode = $(this).data('actioncode');
        var actiondesc = $(this).data('actiondesc');
        var thisrow = $(this).closest('tr');
    
        if ($(this).prop('checked')) {
            // Check if the row is already in selectedIdsAct
            var exists = selectedIdsRel.some(item => item.rowId === rowId);
            if (!exists) {
                // Add to selectedIdsAct array if not already present
                selectedIdsRel.push({ rowId: rowId, controlId: controlId, subj: subj, doctype: doctype, origoffice: origoffice, actioncode: actioncode, actiondesc: actiondesc });
            }

            thisrow.addClass('info');
        } else {
            // Remove the unchecked row based on its rowId
            selectedIdsRel = selectedIdsRel.filter(item => item.rowId !== rowId);

            thisrow.removeClass('info');
        }

    },

    bulkReleaseModal: function() {

        if(selectedIdsRel.length == 0){
            Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Please select Document to Release",
              });
        }else{

            var formData = new FormData();
            formData.append('csrf_token', csrfToken);
            
            var modal = $('#viewReleaseData');

            $.ajax({
                url: base_url + "/getBulkReleaseData",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function(xhr) {
    
                    $("#overlay").show();
    
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
                success: function(response) {
                    try {
                        if (response.success) {

                            receiveApp.populateModalBulkRel(modal,response);

                            $('#selectedForRelease').empty();
            
                            selectedIdsRel.forEach(function (item) {
                                    
                                var rowHtml = `
                                    <tr>
                                        <td>${item.controlId}</td>
                                        <td>${item.origoffice}</td>
                                        <td>${item.subj}</td>
                                        <td>${item.doctype}</td>
                                        <td>${item.actiondesc}</td>
                                    </tr>`;
                                $('#selectedForRelease').append(rowHtml);
                            });

                            console.log("Selected Rows: ", selectedIdsRel);
                            
                            $('.select-select2').select2();

                            modal.modal('show');
                            
                        } else {
                            if(response.formnotvalid){
                                handleValidationErrors(response.data);
                                
                            }else{
                                Swal.fire({
                                    icon: "error",
                                    title: "Error!",
                                    text: response.message,
                                });

                                if(response.reload){
                                    $('#release-table').DataTable().ajax.reload(null, false);
                                    $('#releaseForm')[0].reset();
                                    $('#release-modal').modal('hide');
                                }
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
                        //alert("Server error: " + errorMessage);
                        console.log("Server error: " + xhr.responseText);
                    }
                    
                },
                complete: function() {
                
                    $("#overlay").hide();
                }
            });

        }
    },


    populateModalBulkRel: function(modal, data) {

        let selectBulkRelDest = modal.find('#bulkrel_officedestination');
        let selectBulkRelActionReq = modal.find('#bulkrel_actionrequire');
        let selectRelBulkEmployee = modal.find('#bulkrel_actionofficer').empty();
        selectRelBulkEmployee.append('<option value="">Please select Destination Employee</option>');

        receiveApp.populateBulkReleaseDest(selectBulkRelDest, data.officedestination);
        receiveApp.populateBulkReleaseActionReq(selectBulkRelActionReq, data.actionrequirelist);

        selectBulkRelDest.trigger("change.select2");
        selectRelBulkEmployee.trigger("change.select2");
    },

    populateBulkReleaseDest: function(selectElement, options){

        selectElement.empty();
        selectElement.append('<option value="">Please select Office Destination</option>');
        options.forEach(function(fd) {
            const option = new Option(fd.officename, fd.officecode); 

            selectElement.append(option);
        });
    },

    selectActionOfficerBulkRel: function(event) {

        var selectElement = $('#bulkrel_actionofficer').empty(); 
        const selectElementOffice = $(event.target);
        var officedestination = selectElementOffice.val();

        receiveApp.selectActionOfficer(selectElement,officedestination)

    },

    populateBulkReleaseActionReq: function(selectElement, options){

        selectElement.empty();
        selectElement.append('<option value="">Please select Action Required</option>');
        options.forEach(function(ar) {
            const option = new Option(ar.reqaction_desc, ar.reqaction_code); 

            selectElement.append(option);
        });
    },


    handleModalClickDiss: function() {

        var docdetail = $(this).data('docdetail');
        var modal = $('#disseminate-modal-add');
    
        $.ajax({
            url: base_url + '/disseminateData',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function(xhr) {

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();

            },
            success: function(data) {

                if (data.success) {

                    $('#disseminateDestinationForm')[0].reset();
                    receiveApp.populateModalDiss(modal, data);
                    modal.modal('show');

                } else {
                    //alert(data.message);
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: data.message,
                    });
                    if(data.reload){
                        $('#release-table').DataTable().ajax.reload(null, false);
                       
                    }
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


    populateModalDiss: function(modal, data) {

        modal.find('#diss_routeno').val(data.routeno);
        modal.find('#diss_detailno').val(data.detailno);
        /*modal.find('#rel_detailno').val(data.detailno);
        modal.find('#rel_subject').html(data.subject);
        modal.find('#rel_doctype').html(data.doctype);
        modal.find('#rel_origoffice').html(data.origoffice);
        modal.find('#rel_prevoffice').html(data.prevoffice);
        modal.find('#rel_origemp').html(data.origemp);
        modal.find('#rel_exofficecode').html(data.exofficecode);
        modal.find('#rel_exempname').html(data.exempname);
        modal.find('#rel_pageno').html(data.pageno);
        modal.find('#rel_attachment').html(data.attachment);
        modal.find('#rel_relremarks').val(data.remarks);
        modal.find('#rel_emp').html(data.forwardby);
        modal.find('#daterel').val(data.daterec);
        modal.find('#timerel').val(data.timerec);*/

        let selectRelDestination = modal.find('#diss_office_destination');
        let selectRelActRequire = modal.find('#diss_action_required');

        let selectRelEmployee = modal.find('#diss_action_officer').empty();
        selectRelEmployee.append('<option value="">Select Action Officer</option>');

        receiveApp.populateDisseminateDest(selectRelDestination, data.officelist);
        receiveApp.populateDisseminateActionReq(selectRelActRequire, data.actionrequirelist);

        selectRelDestination.trigger("change.select2");
        selectRelActRequire.trigger("change.select2");
        //selectRelEmployee.trigger("change.select2");
    },

    populateDisseminateDest: function(selectElement, options){

        selectElement.empty();
        selectElement.append('<option value="">Select Office Destination</option>');
        options.forEach(function(fd) {
            const option = new Option(fd.officename, fd.officecode); 

            selectElement.append(option);
        });
    },

    populateDisseminateActionReq: function(selectElement, options){

        selectElement.empty();
        selectElement.append('<option value="">Select Action Required</option>');
        options.forEach(function(ar) {
            const option = new Option(ar.reqaction_desc, ar.reqaction_code); 

            selectElement.append(option);
        });
        
    },

    updateOptions: function() {
        
        let selectedOffices = receiveApp.getSelectedOffices();

        $('.diss_office_destination select').each(function() {
            const $this = $(this);
            $this.find('option').each(function() {
                const $option = $(this);
                const optionValue = $option.val();
                
                $option.prop('disabled', false);
                
                if (selectedOffices.includes(optionValue) && optionValue !== $this.val()) {
                    $option.prop('disabled', true);
                }
                
            });

            $this.select2({ width: '100%' });
        });

        
        
    },

    updateActionOfficers: function(event) {
        const office_destination = $(event.target).val();
        const actionOfficerSelect = $(event.target).closest('tr').find('.diss_action_officer select');
        actionOfficerSelect.empty();

        receiveApp.selectActionOfficer(actionOfficerSelect,office_destination);

        actionOfficerSelect.trigger("change.select2");
    },

    getSelectedOffices: function() {
        // Collect selected values from all dropdowns
        return $('#destinationContainer .diss_office_destination select').map(function() {
            return $(this).val();
        }).get();

    },

    addDisseminationRow: function() {

        let selectedOffices = receiveApp.getSelectedOffices();

        var officeDesOptions = '<option value="">Select Office Destination</option>';
        
        officeDestinations.forEach(function(office) {
            
            let isDisabled = selectedOffices.includes(office.officecode);
            
            officeDesOptions += `<option value="${office.officecode}" ${isDisabled ? 'disabled' : ''}>${office.shortname} - ${office.officename}${isDisabled ? ' (selected)' : ''}</option>`;
        });

        var actReqOptions = '<option value="">Select Action Required</option>';
        
        actionReq.forEach(function(actreq) {
            
            actReqOptions += `<option value="${actreq.reqaction_code}">${actreq.reqaction_desc}</option>`;

        });

        var newRow = `
            <tr class="destination-group dynamic-group">

                <td class="diss_office_destination">
                    <div class="diss_office_destinationDiv">
                        <select name="diss_office_destination[]" class="select-select2 office_destination">
                            ${officeDesOptions}
                        </select>
                        <span class="help-block diss_office_destinationMessage"></span>
                    </div>
                </td>

                <td class="diss_action_officer">
                    <div class="diss_action_officerDiv">
                        <select name="diss_action_officer[]" class="select-select2 action_officer ao">
                            <option value="">Select Action Officer</option>
                        </select>
                        <span class="help-block diss_action_officerMessage"></span>
                    </div>
                </td>

                <td class="diss_action_required">
                    <div class="diss_action_requiredDiv">
                        <select name="diss_action_required[]" class="select-select2 action_required ar">
                            ${actReqOptions}
                        </select>
                        <span class="help-block diss_action_requiredMessage"></span>
                    </div>
                </td>

                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm removeDestinationBtn"><i class="fa fa-minus"></i></button>
                </td>

            </tr>`;

            $('#destinationContainer tbody').append(newRow);

            receiveApp.initSelect2ForNewRow($('#destinationContainer tbody tr:last-child'));

            $('#destinationContainer tbody tr:last-child').find('.diss_office_destination').trigger('change.select2');

            $('#destinationContainer tbody tr:last-child').find('.diss_action_officer select').trigger('change.select2');

    },

    initSelect2ForNewRow: function(newRow) {
        
        $(newRow).find('.select-select2').select2({
            width: '100%'
        });

    },

    removeDestinationRow: function(event){
        $(event.currentTarget).closest('tr').remove();

        receiveApp.updateOptions();
    },


    disseminationFormSubmit   : function(event) {
        event.preventDefault();

        var form = $('#disseminateDestinationForm')[0];
        var formData = new FormData(form);
        var formData = $('#disseminateDestinationForm').serialize();

        receiveApp.clearFormValidation();
        // Make AJAX request
        $.ajax({
            url: base_url + '/addDissemination',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function(xhr) {

                $("#overlay").show();

            },
            success: function(response) {
                try {
                    if (response.success) {
                        
                        

                        $('#disseminateDestinationForm')[0].reset();
                        $('#destinationContainer').find('.dynamic-group').remove();
                        $('#disseminate-modal-add').modal('hide');

                        Swal.fire({
                            title: response.message + "Do you want to view document destination?",
                            icon: "success",
                            showDenyButton: true,
                            confirmButtonText: "Confirm",
                            denyButtonText: "Cancel"

                        }).then((result) => {

                            if (result.isConfirmed) {
                                window.location.href = base_url + `/docview/outgoing/destination/${response.rn}`;
                            } else if (result.isDenied) {
                                Swal.fire({
                                    position: "top-end",
                                    icon: "success",
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                $('#release-table').DataTable().ajax.reload(null, false);
                            }

                        });

                        


                        receiveApp.updateOptions();
                        receiveApp.clearFormValidation();

                    } else {
                        if(response.formnotvalid){
                            receiveApp.handleValidationErrorsDynamic(response.data);
                            
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


    resetDestination: function(){
        $('#disseminateDestinationForm')[0].reset();
        $('#diss_action_officer').empty().append('<option value="">Select Action Officer</option>');
        $('#destinationContainer').find('.dynamic-group').remove();

        receiveApp.updateOptions();
        receiveApp.clearFormValidation();
    },

    closeDestinationModal: function(){
        receiveApp.resetDestination();
    },


    handleModalClickTag: function() {

        var docdetail = $(this).data('docdetail');
        var modal = $('#tagdone-modal');
    
        $.ajax({
            url: base_url + '/tagData',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function(xhr) {

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();

            },
            success: function(data) {

                if (data.success) {

                    $('#tagdoneForm')[0].reset();
                    modal.find('#tag_routeno').html(data.routeno);
                    modal.find('#tag_controlno').html(data.controlno);
                    modal.find('#tag_subject').html(data.subject);
                    modal.find('#tag_doctype').html(data.doctype);
                    modal.find('#tag_detailno').val(data.detailno);
                    modal.modal('show');

                } else {
                    //alert(data.message);
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: data.message,
                    });
                    if(data.reload){
                        $('#release-table').DataTable().ajax.reload(null, false);
                       
                    }
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


    /*selectActionOfficerRel: function(event) {

        var selectElement = $('#bulkrel_actionofficer').empty(); 
        const selectElementOffice = $(event.target);
        var officedestination = selectElementOffice.val();

        receiveApp.selectActionOfficer(selectElement,officedestination)

    },*/


    //FOWARD FUNCTIONS
    handleModalClickFwd: function() {

        var docdetail = $(this).data('docdetail');
        var modal = $('#forward-modal');
        $.ajax({
            url: base_url + '/forwardData',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function(xhr) {

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();

            },
            success: function(data) {

                if (data.success) {

                    $('#forwardForm')[0].reset();
                    receiveApp.populateModalFwd(modal, data);
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

    populateModalFwd: function(modal, data) {

        modal.find('#fwd_routeno').html(data.routeno);
        modal.find('#fwd_dcon').html(data.controlno);
        modal.find('#fwd_detailno').val(data.detailno);
        modal.find('#fwd_subject').html(data.subject);
        modal.find('#fwd_doctype').html(data.doctype);
        modal.find('#fwd_origoffice').html(data.origoffice);
        modal.find('#fwd_prevoffice').html(data.prevoffice);
        modal.find('#fwd_origemp').html(data.origemp);
        modal.find('#fwd_exofficecode').html(data.exofficecode);
        modal.find('#fwd_exempname').html(data.exempname);
        modal.find('#fwd_pageno').html(data.pageno);
        modal.find('#fwd_attachment').html(data.attachment);
        modal.find('#fwd_fwdremarks').val(data.remarks);
        modal.find('#fwd_emp').html(data.forwardby);
        modal.find('#datefwd').val(data.daterec);
        modal.find('#timefwd').val(data.timerec);

        let selectFwdDestination = modal.find('#fwd_destination');
        let selectFwdActRequire = modal.find('#fwd_actionrequire');
        let selectFwdEmployee = modal.find('#fwd_destemp').empty();
        selectFwdEmployee.append('<option value="">Please select Destination Employee</option>');

        receiveApp.populateForwardDest(selectFwdDestination, data.officelist);
        receiveApp.populateForwardActionReq(selectFwdActRequire, data.actionrequirelist);

        selectFwdDestination.trigger("change.select2");
        selectFwdEmployee.trigger("change.select2");
    },


    populateForwardDest: function(selectElement, options){

        selectElement.empty();
        selectElement.append('<option value="">Please select Office Destination</option>');
        options.forEach(function(fd) {
            const option = new Option(fd.officename, fd.officecode); 

            selectElement.append(option);
        });
    },

    populateForwardActionReq: function(selectElement, options){

        selectElement.empty();
        selectElement.append('<option value="">Please select Action Required</option>');
        options.forEach(function(ar) {
            const option = new Option(ar.reqaction_desc, ar.reqaction_code); 

            selectElement.append(option);
        });
    },


    selectActionOfficerFwd: function(event) {

        var selectElement = $('#fwd_destemp').empty(); 
        const selectElementOffice = $(event.target);
        var officedestination = selectElementOffice.val();

        receiveApp.selectActionOfficer(selectElement,officedestination)

    },

    submitForward: function(event) {
        event.preventDefault();

        var form = $('#forwardForm')[0];
        var formData = new FormData(form);

        receiveApp.clearFormValidation();
        
        $.ajax({
            url: base_url + '/forwardDoc',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
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

                        $('#forward-modal').modal('hide');

                        $('#forwardForm')[0].reset();

                        $('#action-table').DataTable().ajax.reload(null, false); 
                        
                        receiveApp.clearFormValidation();

                    } else {
                        if(response.formnotvalid){
                            handleValidationErrors(response.data);
                            
                        }else{
                            //alert(response.message);
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: response.message,
                            });
                            if(response.reload){
                                $('#action-table').DataTable().ajax.reload(null, false);
                                $('#forwardForm')[0].reset();
                                $('#forward-modal').modal('hide');
                            }
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
                    //alert("Server error: " + errorMessage);
                    console.log("Server error: " + xhr.responseText);
                }
                
            },
            complete: function() {
            
                $("#overlay").hide();
            }
        });
    },



    //RETURN FUNCTIONS
    handleModalClickRet: function() {

        var docdetail = $(this).data('docdetail');
        var modal = $('#return-modal');
        $.ajax({
            url: base_url + '/returnData',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function(xhr) {

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();

            },
            success: function(data) {

                if (data.success) {

                    $('#returnForm')[0].reset();
                    receiveApp.populateModalRet(modal, data);
                    modal.modal('show');

                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: data.message,
                    });
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

    populateModalRet: function(modal, data) {

        modal.find('#ret_routeno').html(data.routeno);
        modal.find('#ret_dcon').html(data.controlno);
        modal.find('#ret_detailno').val(data.detailno);
        modal.find('#ret_subject').html(data.subject);
        modal.find('#ret_doctype').html(data.doctype);
        modal.find('#ret_origoffice').html(data.origoffice);
        modal.find('#origemp').html(data.origemp);
        modal.find('#ret_exofficecode').html(data.exofficecode);
        modal.find('#ret_exempname').html(data.exempname);
        modal.find('#ret_prevoffice').html(data.prevoffice);
        modal.find('#ret_destination').html(data.origoffice);
        modal.find('#ret_pageno').html(data.pageno);
        modal.find('#ret_attachment').html(data.attachment);
        modal.find('#ret_retremarks').val(data.remarks);
        modal.find('#ret_emp').html(data.forwardby);
        modal.find('#dateret').val(data.daterec);
        modal.find('#timeret').val(data.timerec);

        receiveApp.selectActionOfficerRet(data.officecode);
    
        let selectFwdActRequire = modal.find('#ret_actionrequire');
        receiveApp.populateReturnActionReq(selectFwdActRequire, data.actionrequirelist);
        selectFwdActRequire.trigger("change.select2");
    },

    populateReturnActionReq: function(selectElement, options){

        selectElement.empty();
        options.forEach(function(ar) {

            const option = new Option(ar.reqaction_desc, ar.reqaction_code); 

            selectElement.append(option);
        });
    },

    selectActionOfficerRet: function(officedestination) {

        var officedest = officedestination;
        var selectElement = $('#ret_destemp').empty();
        receiveApp.selectActionOfficer(selectElement,officedest)

        
        selectElement.trigger("change.select2");

    },

    submitReturn: function(event) {
        event.preventDefault();

        var form = $('#returnForm')[0];
        var formData = new FormData(form);

        receiveApp.clearFormValidation();

        $.ajax({
            url: base_url + '/returnDoc',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
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

                        $('#return-modal').modal('hide');
                        $('#returnForm')[0].reset();
                        $('#action-table').DataTable().ajax.reload(null, false); 
                        
                        receiveApp.clearFormValidation();

                    } else {
                        if(response.formnotvalid){
                            handleValidationErrors(response.data);
                            
                        }else{
                            //alert(response.message);
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: response.message,
                            });
                            if(response.reload){
                                $('#action-table').DataTable().ajax.reload(null, false);
                                $('#returnForm')[0].reset();
                                $('#forward-modal').modal('hide');
                            }
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
                    //alert("Server error: " + errorMessage);
                    console.log("Server error: " + xhr.responseText);
                }
                
            },
            complete: function() {
            
                $("#overlay").hide();
            }
        });
    },


    //RELEASED METHODS
        //Change Destination
    handleModalClickRelChange: function() {

        var docdetail = $(this).data('docdetail');
        var destoffice = $(this).data('destoffice');
        var modal = $('#destination-modal-change');
    
        $.ajax({
            url: base_url + '/releasedGetDestinationDataChange',
            type: 'POST',
            data: { id: docdetail, destoffice: destoffice },
            dataType: 'json',
            beforeSend: function(xhr) {

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();

            },
            success: function(data) {

                if (data.success) {

                    receiveApp.populateModalChangeDest(modal, data);
                    modal.modal('show');

                } else {
                    //alert(data.message);
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: data.message,
                    });
                    if(data.reload){
                        $('#released-table').DataTable().ajax.reload(null, false);
                    }
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
        //outgoingApp.clearFormValidation();
        $('#dd').val(detaildata.doc_detailno);
        var selectElementdest = $('#change_office_destination').empty();
        var selectElementao = $('#change_action_officer').empty(); 
        var selectElementar = $('#change_action_required').empty(); 
        receiveApp.populateOfficeDestinationChange(data.office, selectElementdest, detaildata.office_destination);
        receiveApp.populateActionOfficerChange(data.officeuser, selectElementao, detaildata.action_officer);
        receiveApp.populateActionRequiredChange(data.action_required, selectElementar, detaildata.action_required);

    },


    submitChangeDestination: function(event) {
        event.preventDefault();

        var formData = $('#changeDestinationForm').serialize();

        receiveApp.clearFormValidation();
        // Make AJAX request
        $.ajax({
            url: base_url + '/submitChangeDestinationReld',
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
                        $('#released-table').DataTable().ajax.reload(null, false);
                        
                        receiveApp.clearFormValidation();

                    } else {

                        if(response.formnotvalid){
                            handleValidationErrors(response.data);
                            
                        }else{
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: response.message,
                            });
                            if(response.reload){
                                $('#released-table').DataTable().ajax.reload(null, false);
                                $('#destination-modal-change').modal('hide');
                            }
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


        //Add Destination
    handleModalClickRelAdd: function() {

        var docdetail = $(this).data('docdetail');
        var modal = $('#destination-modal-add');
    
        $.ajax({
            url: base_url + '/releasedGetDestinationDataAdd',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function(xhr) {

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();

            },
            success: function(data) {

                if (data.success) {

                    receiveApp.populateModalAddDest(modal, data);
                    modal.modal('show');

                } else {
                    //alert(data.message);
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: data.message,
                    });
                    if(data.reload){
                        $('#released-table').DataTable().ajax.reload(null, false);
                    }
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


    populateModalAddDest: function(modal, data) {
        
        var detaildata = data.detaildata;
        //outgoingApp.clearFormValidation();
        $('#dda').val(detaildata.doc_detailno);
        var selectElementdest = $('#add_office_destination').empty();
        $('#add_action_officer').empty().append('<option value="">Select Action Officer</option>'); 
        var selectElementar = $('#add_action_required').empty(); 
        receiveApp.populateOfficeDestinationChange(data.office, selectElementdest);
        receiveApp.populateActionRequiredChange(data.action_required, selectElementar);

    },


    submitAddDestination: function(event) {
        
        event.preventDefault();

        var formData = $('#addDestinationForm').serialize();

        receiveApp.clearFormValidation();
        // Make AJAX request
        $.ajax({
            url: base_url + '/submitAddDestinationReld',
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

                        $('#addDestinationForm')[0].reset();
                        $('#destination-modal-add').modal('hide');
                        $('#released-table').DataTable().ajax.reload(null, false);
                        
                        receiveApp.clearFormValidation();

                    } else {

                        if(response.formnotvalid){
                            handleValidationErrors(response.data);
                            
                        }else{
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: response.message,
                            });
                            if(response.reload){
                                $('#released-table').DataTable().ajax.reload(null, false);
                                $('#destination-modal-add').modal('hide');
                            }
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


    populateOfficeDestinationChange: function(options, selectElement, selected="", othdest) {

        let selectedArray = [];
        selectElement.append('<option value="">Select Office Destination</option>');
        if (selected) {
            if (Array.isArray(selected)) {
                selectedArray = selected.map(office => office.trim());
            } else if (selected !== "") {
                selectedArray = selected.split(',').map(office => office.trim());
            }
        }

        options.forEach(function(office) {
            const option = new Option(office.officename, office.officecode);
            
            if (selectedArray.includes(office.officecode)) {
                option.selected = true;
            }
    
            selectElement.append(option);
        });

    },

    populateActionOfficerChange: function(options, selectElement, selected="") {

        var selectedArray = [];
        selectElement.append('<option value="">Select Action Officer</option>');

        if (selected) {
            if (Array.isArray(selected)) {
                selectedArray = selected.map(actionofficer => actionofficer.trim());
            } else if (selected !== "") {
                selectedArray = selected.split(',').map(actionofficer => actionofficer.trim());
            }
        }

        options.forEach(function(actionofficer) {
            const option = new Option(actionofficer.lastname + ", " + actionofficer.firstname + " " + actionofficer.middlename.charAt(0) + ".", actionofficer.empcode);
    
            if (selectedArray.includes(actionofficer.empcode)) {
                option.selected = true;
            }

            selectElement.append(option);
        });

    },

    populateActionRequiredChange: function(options, selectElement, selected="") {

        let selectedArray = [];
        selectElement.append('<option value="">Select Action Required</option>');

        if (selected) {
            if (Array.isArray(selected)) {
                selectedArray = selected.map(actionrequired => actionrequired.trim());
            } else if (selected !== "") {
                selectedArray = selected.split(',').map(actionrequired => actionrequired.trim());
            }
        }

        options.forEach(function(actionrequired) {
            const option = new Option(actionrequired.reqaction_desc, actionrequired.reqaction_code);
    
            if (selectedArray.includes(actionrequired.reqaction_code)) {
                option.selected = true;
            }

            selectElement.append(option);
        });

    },
    
    populateActionOfficerChangeByOffice: function(event) {

        var selectElement = $('#change_action_officer').empty(); 
        const selectElementOffice = $(event.target);
        var officedestination = selectElementOffice.val();

        receiveApp.selectActionOfficer(selectElement,officedestination)

    },

    populateActionOfficerAddByOffice: function(event) {

        var selectElement = $('#add_action_officer').empty(); 
        const selectElementOffice = $(event.target);
        var officedestination = selectElementOffice.val();

        receiveApp.selectActionOfficer(selectElement,officedestination)

    },



    //UNDONE METHODS
    undoneDocument: function() {
            
        var id = $(this).data('docdetail');
    
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

                return receiveApp.undoneDocumentAjax(id, password)
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
                );

                $('#undone-table').DataTable().ajax.reload(null, false); 
            }

        });
    },


    undoneDocumentAjax: function(id, password) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: base_url + '/undoneDoc',
                type: 'POST',
                data: { id: id, password: password },
                dataType: 'json',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                    $("#overlay").show();
                },
                success: function(data) {
                    if (data.success) {
                        resolve({ status: true, message: data.message });
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



    //GLOBAL METHODS
    selectActionOfficer: function(selectElement,officedestination) {

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

    clearFormValidation: function() {

        $('.has-success').removeClass('has-success');
        $('.has-error').removeClass('has-error');
        $('.help-block').empty();

        $('.select-select2').trigger("change.select2");

    },



    handleValidationErrorsDynamic: function(errors) {
        
        $('.help-block').text('');
        $('.help-block').removeClass('text-error');

        $.each(errors.diss_office_destination, function(index, error) {
            var errorElement = $('.destination-group').eq(index).find('.diss_office_destinationMessage');
            var parentDiv = $('.destination-group').eq(index).find('.diss_office_destinationDiv');
            
            errorElement.text(error);
            
            parentDiv.addClass('has-error');
        });
    
        if (errors.diss_action_officer) {
            $.each(errors.diss_action_officer, function(index, error) {
                var errorElement = $('.destination-group').eq(index).find('.diss_action_officerMessage');
                var parentDiv = $('.destination-group').eq(index).find('.diss_action_officerDiv');
                
                errorElement.text(error);
                
                parentDiv.addClass('has-error');
            });
        }

        if (errors.diss_action_required) {
            $.each(errors.diss_action_required, function(index, error) {
                var errorElement = $('.destination-group').eq(index).find('.diss_action_requiredMessage');
                var parentDiv = $('.destination-group').eq(index).find('.diss_action_requiredDiv');
                
                errorElement.text(error);
                
                parentDiv.addClass('has-error');
            });
        }
    },

  };




