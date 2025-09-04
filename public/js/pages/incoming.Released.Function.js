var selectedIdsRel = [];

const receiveApp = {
    init: function () {
        this.bindEvents();
    },

    bindEvents: function () {
        $('#released-table tbody').on('click', '.change-desti', this.handleModalClickRelChange);
        $('#submitChangeDestination').on('click', this.submitChangeDestination.bind(this));
        $('#change_office_destination').on('change', this.populateActionOfficerChangeByOffice.bind(this));
        $('#add_office_destination').on('change', this.populateActionOfficerAddByOffice.bind(this));
        $('#released-table tbody').on('click', '.add-desti', this.handleModalClickRelAdd);
        $('#submitAddDestination').on('click', this.submitAddDestination.bind(this));
    },

    handleModalClickRelChange: function () {
        var docdetail = $(this).data('docdetail');
        var destoffice = $(this).data('destoffice');
        var modal = $('#destination-modal-change');
        $.ajax({
            url: base_url + '/releasedGetDestinationDataChange',
            type: 'POST',
            data: { id: docdetail, destoffice: destoffice },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (data) {
                if (data.success) {
                    receiveApp.populateModalChangeDest(modal, data);
                    modal.modal('show');
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: data.message,
                    });
                    if (data.reload) {
                        $('#released-table').DataTable().ajax.reload(null, false);
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

    populateModalChangeDest: function (modal, data) {
        var detaildata = data.detaildata;
        $('#dd').val(detaildata.doc_detailno);
        var selectElementdest = $('#change_office_destination').empty();
        var selectElementao = $('#change_action_officer').empty();
        var selectElementar = $('#change_action_required').empty();
        receiveApp.populateOfficeDestinationChange(data.office, selectElementdest, detaildata.office_destination);
        receiveApp.populateActionOfficerChange(data.officeuser, selectElementao, detaildata.action_officer);
        receiveApp.populateActionRequiredChange(data.action_required, selectElementar, detaildata.action_required);
    },

    submitChangeDestination: function (event) {
        event.preventDefault();
        var formData = $('#changeDestinationForm').serialize();
        receiveApp.clearFormValidation();
        $.ajax({
            url: base_url + '/submitChangeDestinationReld',
            type: 'POST',
            data: formData,
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
                        $('#changeDestinationForm')[0].reset();
                        $('#destination-modal-change').modal('hide');
                        $('#released-table').DataTable().ajax.reload(null, false);
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
                                $('#released-table').DataTable().ajax.reload(null, false);
                                $('#destination-modal-change').modal('hide');
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

    handleModalClickRelAdd: function () {
        var docdetail = $(this).data('docdetail');
        var modal = $('#destination-modal-add');
        $.ajax({
            url: base_url + '/releasedGetDestinationDataAdd',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (data) {
                if (data.success) {
                    receiveApp.populateModalAddDest(modal, data);
                    modal.modal('show');
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: data.message,
                    });
                    if (data.reload) {
                        $('#released-table').DataTable().ajax.reload(null, false);
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

    populateModalAddDest: function (modal, data) {
        var detaildata = data.detaildata;
        $('#dda').val(detaildata.doc_detailno);
        var selectElementdest = $('#add_office_destination').empty();
        $('#add_action_officer').empty().append('<option value="">Select Action Officer</option>');
        var selectElementar = $('#add_action_required').empty();
        receiveApp.populateOfficeDestinationChange(data.office, selectElementdest);
        receiveApp.populateActionRequiredChange(data.action_required, selectElementar);
    },

    submitAddDestination: function (event) {
        event.preventDefault();
        var formData = $('#addDestinationForm').serialize();
        receiveApp.clearFormValidation();
        $.ajax({
            url: base_url + '/submitAddDestinationReld',
            type: 'POST',
            data: formData,
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
                        $('#addDestinationForm')[0].reset();
                        $('#destination-modal-add').modal('hide');
                        $('#released-table').DataTable().ajax.reload(null, false);
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
                                $('#released-table').DataTable().ajax.reload(null, false);
                                $('#destination-modal-add').modal('hide');
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

    populateOfficeDestinationChange: function (options, selectElement, selected = "", othdest) {
        let selectedArray = [];
        selectElement.append('<option value="">Select Office Destination</option>');
        if (selected) {
            if (Array.isArray(selected)) {
                selectedArray = selected.map(office => office.trim());
            } else if (selected !== "") {
                selectedArray = selected.split(',').map(office => office.trim());
            }
        }
        options.forEach(function (office) {
            const option = new Option(office.officename, office.officecode);
            if (selectedArray.includes(office.officecode)) {
                option.selected = true;
            }
            selectElement.append(option);
        });
    },

    populateActionOfficerChange: function (options, selectElement, selected = "") {
        var selectedArray = [];
        selectElement.append('<option value="">Select Action Officer</option>');
        if (selected) {
            if (Array.isArray(selected)) {
                selectedArray = selected.map(actionofficer => actionofficer.trim());
            } else if (selected !== "") {
                selectedArray = selected.split(',').map(actionofficer => actionofficer.trim());
            }
        }
        options.forEach(function (actionofficer) {
            const option = new Option(actionofficer.lastname + ", " + actionofficer.firstname + " " + actionofficer.middlename.charAt(0) + ".", actionofficer.empcode);
            if (selectedArray.includes(actionofficer.empcode)) {
                option.selected = true;
            }
            selectElement.append(option);
        });
    },

    populateActionRequiredChange: function (options, selectElement, selected = "") {
        let selectedArray = [];
        selectElement.append('<option value="">Select Action Required</option>');
        if (selected) {
            if (Array.isArray(selected)) {
                selectedArray = selected.map(actionrequired => actionrequired.trim());
            } else if (selected !== "") {
                selectedArray = selected.split(',').map(actionrequired => actionrequired.trim());
            }
        }
        options.forEach(function (actionrequired) {
            const option = new Option(actionrequired.reqaction_desc, actionrequired.reqaction_code);
            if (selectedArray.includes(actionrequired.reqaction_code)) {
                option.selected = true;
            }
            selectElement.append(option);
        });
    },

    populateActionOfficerChangeByOffice: function (event) {
        var selectElement = $('#change_action_officer').empty();
        const selectElementOffice = $(event.target);
        var officedestination = selectElementOffice.val();
        receiveApp.selectActionOfficer(selectElement, officedestination)
    },

    populateActionOfficerAddByOffice: function (event) {
        var selectElement = $('#add_action_officer').empty();
        const selectElementOffice = $(event.target);
        var officedestination = selectElementOffice.val();
        receiveApp.selectActionOfficer(selectElement, officedestination)
    },

    // GLOBAL METHODS
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
