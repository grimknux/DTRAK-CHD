var selectedIdsRel = [];

const receiveApp = {
    init: function () {
        this.bindEvents();
    },

    bindEvents: function () {
        // RELEASE EVENTS
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
            receiveApp.closeDestinationModal();
        });
        $('#release-table tbody').on('click', '.done-rel', this.handleModalClickTag);
        $('#tagdoneForm').on('submit', this.submitTagDoneForm.bind(this));
    },

    // RELEASE FUNCTIONS
    handleModalClickRel: function () {
        var docdetail = $(this).data('docdetail');
        var modal = $('#release-modal');
        $.ajax({
            url: base_url + '/releaseData',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (data) {
                if (data.success) {
                    $('#releaseForm')[0].reset();
                    $('#remarksRow').remove();
                    receiveApp.populateModalRel(modal, data);
                    modal.modal('show');
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: data.message,
                    });
                    if (data.reload) {
                        $('#release-table').DataTable().ajax.reload(null, false);
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

    populateModalRel: function (modal, data) {
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


    populateReleaseDest: function (selectElement, options) {

        selectElement.empty();
        selectElement.append('<option value="">Please select Office Destination</option>');
        options.forEach(function (fd) {
            const option = new Option(fd.officename, fd.officecode);

            selectElement.append(option);
        });
    },

    populateReleaseActionReq: function (selectElement, options) {

        selectElement.empty();
        selectElement.append('<option value="">Please select Action Required</option>');
        options.forEach(function (ar) {
            const option = new Option(ar.reqaction_desc, ar.reqaction_code);

            selectElement.append(option);
        });

    },

    selectActionOfficerRel: function (event) {

        var selectElement = $('#rel_destemp').empty();
        const selectElementOffice = $(event.target);
        var officedestination = selectElementOffice.val();

        receiveApp.selectActionOfficer(selectElement, officedestination)

    },

    submitRelease: function (event) {
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

                        $('#release-modal').modal('hide');

                        $('#releaseForm')[0].reset();

                        $('#release-table').DataTable().ajax.reload(null, false);

                        receiveApp.clearFormValidation();

                    } else {
                        if (response.formnotvalid) {
                            handleValidationErrors(response.data);

                        } else {
                            //alert(response.message);
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: response.message,
                            });
                            if (response.reload) {
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
            error: function (xhr, errorType, thrownError) {
                if (xhr.status === 403 || xhr.status === 405) {
                    alert(xhr.responseText);
                    console.log("Server error: " + xhr.responseText);
                } else {
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
                    //alert("Server error: " + errorMessage);
                    console.log("Server error: " + xhr.responseText);
                }

            },
            complete: function () {

                $("#overlay").hide();
            }
        });
    },


    handleDoneActClick: function () {
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
                    //alert("Server error: " + errorMessage);
                    console.log("Server error: " + xhr.responseText);
                }

            },
            complete: function () {

                $("#overlay").hide();
            }
        });

    },


    handleModalClickTag: function () {

        var docdetail = $(this).data('docdetail');
        var modal = $('#tagdone-modal');

        $.ajax({
            url: base_url + '/tagData',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function (xhr) {

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();

            },
            success: function (data) {

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
                    if (data.reload) {
                        $('#release-table').DataTable().ajax.reload(null, false);

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


    submitTagDoneForm: function (event) {

        event.preventDefault();

        var form = $('#tagdoneForm')[0];
        var formData = new FormData(form);

        receiveApp.clearFormValidation();

        Swal.fire({
            title: "Are you sure you want to tag this Document as done?",
            icon: "info",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel"

        }).then((result) => {

            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + '/tagDocumentDone',
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

                                $('#tagdone-modal').modal('hide');
                                $('#tagdoneForm')[0].reset();
                                $('#release-table').DataTable().ajax.reload(null, false);

                                receiveApp.clearFormValidation();

                            } else {
                                if (response.formnotvalid) {
                                    handleValidationErrors(response.data);

                                } else {
                                    //alert(response.message);
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error!",
                                        text: response.message,
                                    });
                                    if (response.reload) {
                                        $('#release-table').DataTable().ajax.reload(null, false);
                                        $('#tagdoneForm')[0].reset();
                                        $('#tagdone-modal').modal('hide');
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
                            //alert("Server error: " + errorMessage);
                            console.log("Server error: " + xhr.responseText);
                        }

                    },
                    complete: function () {

                        $("#overlay").hide();
                    }
                });

            } else if (result.isDenied) {
                console.log("User cancelled deletion.");
            }
        });

    },


    handleBulkRelClick: function () {

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
            beforeSend: function (xhr) {

                $("#overlay").show();

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function (response) {
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

                        if (response.formnotvalid) {
                            handleValidationErrors(response.data);
                        } else {
                            //alert(response.message);
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: response.message,
                            });

                            if (response.reload) {
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
            error: function (xhr, errorType, thrownError) {
                if (xhr.status === 403 || xhr.status === 405) {
                    alert(xhr.responseText);
                    console.log("Server error: " + xhr.responseText);
                } else {
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
                    //alert("Server error: " + errorMessage);
                    console.log("Server error: " + xhr.responseText);
                }

            },
            complete: function () {

                $("#overlay").hide();
            }
        });

    },


    handleReleaseCheckbox: function () {

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

    bulkReleaseModal: function () {

        if (selectedIdsRel.length == 0) {
            Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Please select Document to Release",
            });
        } else {

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
                beforeSend: function (xhr) {

                    $("#overlay").show();

                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
                success: function (response) {
                    try {
                        if (response.success) {

                            receiveApp.populateModalBulkRel(modal, response);

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
                            if (response.formnotvalid) {
                                handleValidationErrors(response.data);

                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error!",
                                    text: response.message,
                                });

                                if (response.reload) {
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
                error: function (xhr, errorType, thrownError) {
                    if (xhr.status === 403 || xhr.status === 405) {
                        alert(xhr.responseText);
                        console.log("Server error: " + xhr.responseText);
                    } else {
                        var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
                        //alert("Server error: " + errorMessage);
                        console.log("Server error: " + xhr.responseText);
                    }

                },
                complete: function () {

                    $("#overlay").hide();
                }
            });

        }
    },


    populateModalBulkRel: function (modal, data) {

        let selectBulkRelDest = modal.find('#bulkrel_officedestination');
        let selectBulkRelActionReq = modal.find('#bulkrel_actionrequire');
        let selectRelBulkEmployee = modal.find('#bulkrel_actionofficer').empty();
        selectRelBulkEmployee.append('<option value="">Please select Destination Employee</option>');

        receiveApp.populateBulkReleaseDest(selectBulkRelDest, data.officedestination);
        receiveApp.populateBulkReleaseActionReq(selectBulkRelActionReq, data.actionrequirelist);

        selectBulkRelDest.trigger("change.select2");
        selectRelBulkEmployee.trigger("change.select2");
    },

    populateBulkReleaseDest: function (selectElement, options) {

        selectElement.empty();
        selectElement.append('<option value="">Please select Office Destination</option>');
        options.forEach(function (fd) {
            const option = new Option(fd.officename, fd.officecode);

            selectElement.append(option);
        });
    },

    selectActionOfficerBulkRel: function (event) {

        var selectElement = $('#bulkrel_actionofficer').empty();
        const selectElementOffice = $(event.target);
        var officedestination = selectElementOffice.val();

        receiveApp.selectActionOfficer(selectElement, officedestination)

    },

    populateBulkReleaseActionReq: function (selectElement, options) {

        selectElement.empty();
        selectElement.append('<option value="">Please select Action Required</option>');
        options.forEach(function (ar) {
            const option = new Option(ar.reqaction_desc, ar.reqaction_code);

            selectElement.append(option);
        });
    },


    handleModalClickDiss: function () {

        var docdetail = $(this).data('docdetail');
        var modal = $('#disseminate-modal-add');

        $.ajax({
            url: base_url + '/disseminateData',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function (xhr) {

                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();

            },
            success: function (data) {

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
                    if (data.reload) {
                        $('#release-table').DataTable().ajax.reload(null, false);

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


    populateModalDiss: function (modal, data) {

        modal.find('#diss_routeno').val(data.routeno);
        modal.find('#diss_detailno').val(data.detailno);

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

    populateDisseminateDest: function (selectElement, options) {

        selectElement.empty();
        selectElement.append('<option value="">Select Office Destination</option>');
        options.forEach(function (fd) {
            const option = new Option(fd.officename, fd.officecode);

            selectElement.append(option);
        });
    },

    populateDisseminateActionReq: function (selectElement, options) {

        selectElement.empty();
        selectElement.append('<option value="">Select Action Required</option>');
        options.forEach(function (ar) {
            const option = new Option(ar.reqaction_desc, ar.reqaction_code);

            selectElement.append(option);
        });

    },

    updateOptions: function () {

        let selectedOffices = receiveApp.getSelectedOffices();

        $('.diss_office_destination select').each(function () {
            const $this = $(this);
            $this.find('option').each(function () {
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

    updateActionOfficers: function (event) {
        const office_destination = $(event.target).val();
        const actionOfficerSelect = $(event.target).closest('tr').find('.diss_action_officer select');
        actionOfficerSelect.empty();

        receiveApp.selectActionOfficer(actionOfficerSelect, office_destination);

        actionOfficerSelect.trigger("change.select2");
    },

    getSelectedOffices: function () {
        // Collect selected values from all dropdowns
        return $('#destinationContainer .diss_office_destination select').map(function () {
            return $(this).val();
        }).get();

    },

    addDisseminationRow: function () {

        let selectedOffices = receiveApp.getSelectedOffices();

        var officeDesOptions = '<option value="">Select Office Destination</option>';

        officeDestinations.forEach(function (office) {

            let isDisabled = selectedOffices.includes(office.officecode);

            officeDesOptions += `<option value="${office.officecode}" ${isDisabled ? 'disabled' : ''}>${office.shortname} - ${office.officename}${isDisabled ? ' (selected)' : ''}</option>`;
        });

        var actReqOptions = '<option value="">Select Action Required</option>';

        actionReq.forEach(function (actreq) {

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

    initSelect2ForNewRow: function (newRow) {

        $(newRow).find('.select-select2').select2({
            width: '100%'
        });

    },

    removeDestinationRow: function (event) {
        $(event.currentTarget).closest('tr').remove();

        receiveApp.updateOptions();
    },


    disseminationFormSubmit: function (event) {
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
            beforeSend: function (xhr) {

                $("#overlay").show();

            },
            success: function (response) {
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
                        if (response.formnotvalid) {
                            receiveApp.handleValidationErrorsDynamic(response.data);

                        } else {
                            alert(response.message);
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


    resetDestination: function () {
        $('#disseminateDestinationForm')[0].reset();
        $('#diss_action_officer').empty().append('<option value="">Select Action Officer</option>');
        $('#destinationContainer').find('.dynamic-group').remove();

        receiveApp.updateOptions();
        receiveApp.clearFormValidation();
    },

    closeDestinationModal: function () {
        receiveApp.resetDestination();
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

