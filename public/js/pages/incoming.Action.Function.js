var selectedIdsAct = [];

const receiveApp = {
    init: function () {
        this.bindEvents();
    },

    bindEvents: function () {
        //ACTION EVENTS
        $('#action-table tbody').on('click', '.act-modal', this.handleModalClickAct);
        $('#actionForm').on('submit', this.submitAction.bind(this));
        $('#submitBulkAction').on('click', this.handleBulkActClick);
        $('#action-table tbody').on('click', '.insta-act', this.handleInstaActClick);

        //FORWARD EVENTS
        $('#action-table tbody').on('click', '.fwd-modal', this.handleModalClickFwd);
        $('#fwd_destination').on('change', this.selectActionOfficerFwd.bind(this));
        $('#forwardForm').on('submit', this.submitForward.bind(this));

        //RETURN EVENTS
        $('#action-table tbody').on('click', '.ret-modal', this.handleModalClickRet);
        $('#returnForm').on('submit', this.submitReturn.bind(this));
    },

    //ACTION FUNCTIOns
    handleModalClickAct: function () {
        var docdetail = $(this).data('docdetail');
        var modal = $('#action-modal');
        $.ajax({
            url: base_url + '/actionData',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (data) {
                if (data.success) {
                    $('#actionForm')[0].reset();
                    $('#remarksRow').remove();
                    receiveApp.populateModalAct(modal, data);
                    modal.modal('show');
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: data.message,
                    });
                    if (data.reload) {
                        $('#action-table').DataTable().ajax.reload(null, false);
                    }
                }
            },
            error: function (xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert("Error Code " + xhr.status + ": " + error + "\n" +
                        "Message: " + xhr.responseJSON.error);
                } else {
                    alert('An unknown error occurred.');
                }
            },
            complete: function () {
                $("#overlay").hide();
            }
        });
    },

    populateModalAct: function (modal, data) {
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

    populateActionTaken: function (selectElement, options, selected = "") {
        selectElement.empty();
        const selectedArray = selected !== "" ? selected.split(',').map(at => at.trim()) : [];
        options.forEach(function (at) {
            const option = new Option(at.action_desc, at.action_code);
            if (selectedArray.includes(at.action_code)) {
                option.selected = true;
            }
            selectElement.append(option);
        });
    },

    submitAction: function (event) {
        event.preventDefault();
        var form = $('#actionForm')[0];
        var formData = new FormData(form);
        receiveApp.clearFormValidation();
        $.ajax({
            url: base_url + '/actionDoc',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            beforeSend: function (xhr) {
                $("#overlay").show();
            },
            success: function (response) {
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
                        if (response.formnotvalid) {
                            handleValidationErrors(response.data);
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: response.message,
                            });
                            if (response.reload) {
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
            error: function (xhr, errorType, thrownError) {
                if (xhr.status === 403 || xhr.status === 405) {
                    alert(xhr.responseText);
                    console.log("Server error: " + xhr.responseText);
                } else {
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
                    console.log("Server error: " + xhr.responseText);
                }
            },
            complete: function () {
                $("#overlay").hide();
            }
        });
    },

    handleInstaActClick: function () {
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
            beforeSend: function (xhr) {
                $("#overlay").show();
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function (response) {
                try {
                    if (response.success) {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $('#action-table').DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        if (response.reload) {
                            $('#action-table').DataTable().ajax.reload(null, false);
                        }
                    }
                } catch (error) {
                    console.error("Error processing response:", error);
                }
            },
            error: function (xhr, errorType, thrownError) {
                if (xhr.status === 403 || xhr.status === 405) {
                    alert(xhr.responseText);
                    console.log("Server error: " + xhr.responseText);
                } else {
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
                    console.log("Server error: " + xhr.responseText);
                }
            },
            complete: function () {
                $("#overlay").hide();
            }
        });
    },

    handleBulkActClick: function () {
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
            beforeSend: function (xhr) {
                $("#overlay").show();
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function (response) {
                try {
                    if (response.success) {
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
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        if (response.reload) {
                            $('#viewActionData').modal('hide');
                            $('#action-table').DataTable().ajax.reload(null, false);
                            selectedIdsAct = [];
                        }
                    }
                } catch (error) {
                    console.error("Error processing response:", error);
                }
            },
            error: function (xhr, errorType, thrownError) {
                if (xhr.status === 403 || xhr.status === 405) {
                    alert(xhr.responseText);
                    console.log("Server error: " + xhr.responseText);
                } else {
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
                    console.log("Server error: " + xhr.responseText);
                }
            },
            complete: function () {
                $("#overlay").hide();
            }
        });
    },

    //FOWARD FUNCTIONS
    handleModalClickFwd: function () {
        var docdetail = $(this).data('docdetail');
        var modal = $('#forward-modal');
        $.ajax({
            url: base_url + '/forwardData',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (data) {
                if (data.success) {
                    $('#forwardForm')[0].reset();
                    receiveApp.populateModalFwd(modal, data);
                    modal.modal('show');
                } else {
                    alert(data.message);
                }
            },
            error: function (xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert("Error Code " + xhr.status + ": " + error + "\n" +
                        "Message: " + xhr.responseJSON.error);
                } else {
                    alert('An unknown error occurred.');
                }
            },
            complete: function () {
                $("#overlay").hide();
            }
        });
    },

    populateModalFwd: function (modal, data) {
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

    populateForwardDest: function (selectElement, options) {
        selectElement.empty();
        selectElement.append('<option value="">Please select Office Destination</option>');
        options.forEach(function (fd) {
            const option = new Option(fd.officename, fd.officecode);
            selectElement.append(option);
        });
    },

    populateForwardActionReq: function (selectElement, options) {
        selectElement.empty();
        selectElement.append('<option value="">Please select Action Required</option>');
        options.forEach(function (ar) {
            const option = new Option(ar.reqaction_desc, ar.reqaction_code);
            selectElement.append(option);
        });
    },

    selectActionOfficerFwd: function (event) {
        var selectElement = $('#fwd_destemp').empty();
        const selectElementOffice = $(event.target);
        var officedestination = selectElementOffice.val();
        receiveApp.selectActionOfficer(selectElement, officedestination)
    },

    submitForward: function (event) {
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
            beforeSend: function (xhr) {
                $("#overlay").show();
            },
            success: function (response) {
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
                        if (response.formnotvalid) {
                            handleValidationErrors(response.data);
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: response.message,
                            });
                            if (response.reload) {
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
            error: function (xhr, errorType, thrownError) {
                if (xhr.status === 403 || xhr.status === 405) {
                    alert(xhr.responseText);
                    console.log("Server error: " + xhr.responseText);
                } else {
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
                    console.log("Server error: " + xhr.responseText);
                }
            },
            complete: function () {
                $("#overlay").hide();
            }
        });
    },

    //RETURN FUNCTIONS
    handleModalClickRet: function () {
        var docdetail = $(this).data('docdetail');
        var modal = $('#return-modal');
        $.ajax({
            url: base_url + '/returnData',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (data) {
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
            error: function (xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert("Error Code " + xhr.status + ": " + error + "\n" +
                        "Message: " + xhr.responseJSON.error);
                } else {
                    alert('An unknown error occurred.');
                }
            },
            complete: function () {
                $("#overlay").hide();
            }
        });
    },

    populateModalRet: function (modal, data) {
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

    populateReturnActionReq: function (selectElement, options) {
        selectElement.empty();
        options.forEach(function (ar) {
            const option = new Option(ar.reqaction_desc, ar.reqaction_code);
            selectElement.append(option);
        });
    },

    selectActionOfficerRet: function (officedestination) {
        var officedest = officedestination;
        var selectElement = $('#ret_destemp').empty();
        receiveApp.selectActionOfficer(selectElement, officedest)
        selectElement.trigger("change.select2");
    },

    submitReturn: function (event) {
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
            beforeSend: function (xhr) {
                $("#overlay").show();
            },
            success: function (response) {
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
                        if (response.formnotvalid) {
                            handleValidationErrors(response.data);
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: response.message,
                            });
                            if (response.reload) {
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
            error: function (xhr, errorType, thrownError) {
                if (xhr.status === 403 || xhr.status === 405) {
                    alert(xhr.responseText);
                    console.log("Server error: " + xhr.responseText);
                } else {
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
                    console.log("Server error: " + xhr.responseText);
                }
            },
            complete: function () {
                $("#overlay").hide();
            }
        });
    },

    //GLOBAL METHODS
    selectActionOfficer: function (selectElement, officedestination) {
        $.ajax({
            url: base_url + '/populateActOffByOffice',
            type: 'POST',
            data: { officedestination: officedestination },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (data) {
                let options = data.officeuser;
                if (data.success) {
                    options.forEach(function (actionofficer) {
                        const option = new Option(actionofficer.lastname + ", " + actionofficer.firstname + " " + actionofficer.middlename.charAt(0) + ".", actionofficer.empcode);
                        selectElement.append(option);
                    });
                } else {
                    alert(data.message);
                }
            },
            error: function (xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert("Error Code " + xhr.status + ": " + error + "\n" +
                        "Message: " + xhr.responseJSON.error);
                } else {
                    alert('An unknown error occurred.');
                }
            },
            complete: function () {
                $("#overlay").hide();
            }
        });
    },

    clearFormValidation: function () {
        $('.has-success').removeClass('has-success');
        $('.has-error').removeClass('has-error');
        $('.help-block').empty();
        $('.select-select2').trigger("change.select2");
    },

    handleValidationErrorsDynamic: function (errors) {
        $('.help-block').text('');
        $('.help-block').removeClass('text-error');
        $.each(errors.diss_office_destination, function (index, error) {
            var errorElement = $('.destination-group').eq(index).find('.diss_office_destinationMessage');
            var parentDiv = $('.destination-group').eq(index).find('.diss_office_destinationDiv');
            errorElement.text(error);
            parentDiv.addClass('has-error');
        });
        if (errors.diss_action_officer) {
            $.each(errors.diss_action_officer, function (index, error) {
                var errorElement = $('.destination-group').eq(index).find('.diss_action_officerMessage');
                var parentDiv = $('.destination-group').eq(index).find('.diss_action_officerDiv');
                errorElement.text(error);
                parentDiv.addClass('has-error');
            });
        }
        if (errors.diss_action_required) {
            $.each(errors.diss_action_required, function (index, error) {
                var errorElement = $('.destination-group').eq(index).find('.diss_action_requiredMessage');
                var parentDiv = $('.destination-group').eq(index).find('.diss_action_requiredDiv');
                errorElement.text(error);
                parentDiv.addClass('has-error');
            });
        }
    },
};
