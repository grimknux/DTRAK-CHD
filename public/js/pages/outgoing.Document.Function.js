const outgoingApp = {
    init: function () {
        this.bindEvents();
    },
    bindEvents: function () {
        $('.add-btn').on('click', '.outgo-modal', this.addDocumentModal);
        $('#submitOutgoing').on('click', this.submitAddDocumentForm.bind(this));
        $('#submitOutgoingEdit').on('click', this.submitEditDocumentForm.bind(this));
        $('#submitOutgoingAttach').on('click', this.submitAttachDoc.bind(this));
        $('#outgoing-table tbody').on('click', '.edit-doc-modal', this.editDocumentModal);
        $('#outgoing-table tbody').on('click', '.edit-attach-modal', this.editDocAttachModal);
        $('#outgoing-table tbody').on('click', '.delete-doc', this.deleteDocument);
        $('#orig_office').on('change', this.selectOfficeDocument.bind(this));
        $('#orig_office_edit').on('change', this.selectOfficeDocumentEdit.bind(this));
        $('#doc_source').on('change', this.toggleExternal.bind(this));
        $('#doc_source_edit').on('change', this.toggleExternal.bind(this));
    },
    selectOfficeDocument: function (event) {
        const selectElement = $(event.target);
        const selectedValue = selectElement.val();
        $.ajax({
            url: base_url + '/doneDocuments',
            type: 'POST',
            data: { office: selectedValue },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $('#docref_controlno').prop('disabled', true);
            },
            success: function (data) {
                if (data.success) {
                    const selectDocRef = $('#docref_controlno').empty();
                    var options = data.doc_ref;
                    options.forEach(function (refcode) {
                        selectDocRef.append('<option value=""></option>');
                        selectDocRef.append(new Option(refcode.doc_controlno, refcode.doc_controlno));
                    });
                    selectDocRef.trigger('change.select2');
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
                $('#docref_controlno').prop('disabled', false);
            }
        });
    },

    selectOfficeDocumentEdit: function (event, selectedDocRef = "", office = "") {
        var selectDocRef, selected = "", selectedOffice;
        selectDocRef = $('#docref_controlno_edit').empty();
        if (event.type === 'change') {
            const selectElement = $(event.target);
            selectedOffice = selectElement.val();
            selected = "";
        } else {
            selectedOffice = office;
            selected = selectedDocRef;
        }
        $.ajax({
            url: base_url + '/doneDocuments',
            type: 'POST',
            data: { office: selectedOffice },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $('#docref_controlno_edit').prop('disabled', true);
            },
            success: function (data) {
                if (data.success) {
                    var options = data.doc_ref;
                    selectDocRef.append('<option value=""></option>');
                    const selectedArray = selected !== "" ? selected.split(',').map(refcode => refcode.trim()) : [];
                    options.forEach(function (refcode) {
                        const option = new Option(refcode.doc_controlno, refcode.doc_controlno);
                        if (selectedArray.includes(refcode.doc_controlno)) {
                            option.selected = true;
                        }
                        selectDocRef.append(option);
                    });
                    $('#docref_controlno_edit').trigger('change.select2');
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
                $('#docref_controlno_edit').prop('disabled', false);
            }
        });
    },

    addDocumentModal: function () {
        var modal = $('#outgoing-modal');
        $.ajax({
            url: base_url + '/outgoingData',
            type: 'POST',
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function (data) {
                if (data.success) {
                    outgoingApp.populateModal(modal, data);
                    modal.modal('show');
                    const officeDropdown = $('#orig_office');
                    if (officeDropdown.val()) {
                        outgoingApp.selectOfficeDocument({ target: officeDropdown });
                    }
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
            }
        });
    },

    editDocumentModal: function () {
        var id = $(this).data('id');
        var modal = $('#outgoing-modal-edit');
        $.ajax({
            url: base_url + '/editDocumentData',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (data) {
                if (data.success) {
                    outgoingApp.populateModalEdit(modal, data);
                    modal.modal('show');
                    const officeDropdown = $('#orig_office_edit');
                    if (officeDropdown.val()) {
                        outgoingApp.selectOfficeDocument({ target: officeDropdown });
                    }
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

    deleteDocument: function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure you want to delete this document?',
            text: "You will need to enter your password to delete this document.",
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
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var password = result.value;
                outgoingApp.deleteDocumentAjax(password, id);
            }
        });
    },

    deleteDocumentAjax: function (password, id) {
        $.ajax({
            url: base_url + '/deleteDocument',
            type: 'POST',
            data: { id: id, password: password },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire('Deleted!', data.message, 'success');
                    $('#outgoing-table').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire('Error!', data.message, 'error');
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

    editDocAttachModal: function () {
        var id = $(this).data('id');
        var modal = $('#outgoing-modal-attachment');
        $.ajax({
            url: base_url + '/editAttachmentData',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (data) {
                if (data.success) {
                    outgoingApp.clearFormValidation();
                    modal.modal('show');
                    modal.find('#current_attach').attr('src', base_url + 'docview/outgoing/viewfile/' + data.attachment).show();
                    modal.find('#attach_routeno').html(data.routeno);
                    modal.find('#attach_subject').html(data.subject);
                    modal.find('#attach_doctype').html(data.ddoctype_desc);
                    modal.find('#attach_routeno_code').val(data.routeno);
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

    populateModal: function (modal, data) {
        outgoingApp.clearFormValidation();
        $('#outgoing-form')[0].reset();
        $('#formGroup').slideUp();
        modal.find('#entryby').html(data.entryby);
        let selectDoctype = modal.find('#doc_type');
        let selectOrigOffice = modal.find('#orig_office');
        outgoingApp.populateDocType(selectDoctype, data.doctype);
        outgoingApp.populateOrigOffice(selectOrigOffice, data.office);
        selectDoctype.trigger('change.select2');
        selectOrigOffice.trigger('change.select2');
    },

    populateModalEdit: function (modal, data) {
        outgoingApp.clearFormValidation();
        modal.find('#entryby_edit').html(data.entryby);
        modal.find('#route_no_edit').val(data.routeno);
        modal.find('#route_no_edit_code').val(data.docregistry_id);
        modal.find('#office_controlno_edit').val(data.officecontrolno);
        modal.find('#doc_subject_edit').val(data.subject);
        modal.find('#doc_source_edit').val(data.source);
        modal.find('#attach_docs_edit').val(data.attachdocs);
        modal.find('#doc_page_edit').val(data.nopage);
        if ($.trim(data.exDocNo) !== "" || $.trim(data.exDocOffice) !== "" || $.trim(data.exDocEmp) !== "") {
            $('#formGroup_edit').slideDown();
        } else {
            $('#formGroup_edit').slideUp();
        }
        modal.find('#orig_docnoEx_edit').val(data.exDocNo);
        modal.find('#orig_officeEx_edit').val(data.exDocOffice);
        modal.find('#orig_empEx_edit').val(data.exDocEmp);
        modal.find('#doc_remarks_edit').val(data.remarks);
        let selectDoctype = modal.find('#doc_type_edit');
        let selectOrigOffice = modal.find('#orig_office_edit');
        outgoingApp.populateDocType(selectDoctype, data.doctype, data.doctype_selected);
        outgoingApp.populateOrigOffice(selectOrigOffice, data.orig_office, data.orig_office_selected);
        outgoingApp.selectOfficeDocumentEdit("", data.docrefcontrolno, data.orig_office_selected);
        selectDoctype.trigger("change.select2");
        selectOrigOffice.trigger("change.select2");
    },

    populateDocType: function (selectElement, options, selected = "") {
        selectElement.empty();
        const selectedArray = selected !== "" ? selected.split(',').map(dt => dt.trim()) : [];
        options.forEach(function (dt) {
            const option = new Option(dt.type_desc, dt.type_code);
            if (selectedArray.includes(dt.type_code)) {
                option.selected = true;
            }
            selectElement.append(option);
        });
    },

    populateOrigOffice: function (selectElement, options, selected = "") {
        selectElement.empty();
        let selectedArray = [];
        if (Array.isArray(selected)) {
            selectedArray = selected.map(office => office.trim());
        } else if (selected !== "") {
            selectedArray = selected.split(',').map(office => office.trim());
        }
        options.forEach(function (office) {
            const option = new Option(office.officename, office.officecode);
            if (selectedArray.includes(office.officecode)) {
                option.selected = true;
            }
            selectElement.append(option);
        });
    },

    submitAddDocumentForm: function (event) {
        event.preventDefault();
        var form = $('#outgoing-form')[0];
        var formData = new FormData(form);
        outgoingApp.clearFormValidation();
        $.ajax({
            url: base_url + '/addDocument',
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
                        $('#outgoing-modal').modal('hide');
                        $('#outgoing-form')[0].reset();
                        $('#outgoing-table').DataTable().ajax.reload(null, false);
                        outgoingApp.clearFormValidation();
                    } else {
                        if (response.formnotvalid) {
                            handleValidationErrors(response.data);
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

    submitAttachDoc: function (event) {
        event.preventDefault();
        var form = $('#change-attach-form')[0];
        var formData = new FormData(form);
        $.ajax({
            url: base_url + '/updateAttachment',
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
                        $('#outgoing-modal-attachment').modal('hide');
                        $('#change-attach-form')[0].reset();
                        $('#outgoing-table').DataTable().ajax.reload(null, false);
                        outgoingApp.clearFormValidation();
                        console.log(response.message);
                        console.log(response.edit);
                    } else {
                        if (response.formnotvalid) {
                            handleValidationErrors(response.data);
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

    submitEditDocumentForm: function (event) {
        event.preventDefault();
        var form = $('#outgoing-form-edit')[0];
        var formData = new FormData(form);
        $.ajax({
            url: base_url + '/updateDocument',
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
                        $('#outgoing-modal-edit').modal('hide');
                        $('#outgoing-form-edit')[0].reset();
                        $('#outgoing-table').DataTable().ajax.reload(null, false);
                        outgoingApp.clearFormValidation();
                        console.log(response.message);
                        console.log(response.edit);
                    } else {
                        if (response.formnotvalid) {
                            handleValidationErrors(response.data);
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

    toggleExternal: function (event) {
        const selectElement = $(event.target);
        const sourcetype = selectElement.val();
        const elementId = selectElement.attr('id');
        if (elementId == 'doc_source') {
            if (sourcetype == 'E') {
                $('#formGroup').slideDown();
                $('#formGroup input').val('');
            } else {
                $('#formGroup').slideUp();
                $('#formGroup input').val('');
            }
        } else {
            if (sourcetype == 'E') {
                $('#formGroup_edit').slideDown();
                $('#formGroup_edit input').val('');
            } else {
                $('#formGroup_edit').slideUp();
                $('#formGroup_edit input').val('');
            }
        }
    },

    clearFormValidation: function () {
        $('.has-success').removeClass('has-success');
        $('.has-error').removeClass('has-error');
        $('.help-block').empty();
        $('.select-select2').trigger("change.select2");
    }
};



const outgoingDes = {
    init: function () {
        this.bindEvents();
    },
    bindEvents: function () {
        $('#addDestinationBtn').on('click', this.addDestinationRow).bind(this);
        $('#destinationContainer').on('click', '.removeDestinationBtn', this.removeDestinationRow.bind(this));
        $('#destinationContainer').on('change', '.office_destination select', this.updateOptions.bind(this));
        $('#destinationContainer').on('change', '.office_destination select', this.updateActionOfficers.bind(this));
        $('#addDestination').on('click', this.destinationFormSubmit.bind(this));
        $('#resetDestinationForm').on('click', this.resetDestination.bind(this));
        $('.add-btn-dest').on('click', '.desti-modal', this.addDestinationModal);
        $(document).on('click', '.change-desti', this.changeDestinationModal);
        $('#destination-modal-add').on('hidden.bs.modal', function () {
            outgoingDes.closeDestinationModal();
        });
        $('#change_office_destination').on('change', this.populateActionOfficerChangeByOffice.bind(this));
        outgoingDes.loadDocumentControls(routeno);
        $('#submitChangeDestination').on('click', this.submitChangeDestination.bind(this));
        $(document).on('click', '.delete-desti', this.deleteDestination);
    },

    addDestinationRow: function () {
        let selectedOffices = outgoingDes.getSelectedOffices();
        var officeDesOptions = '<option value="">Select Office Destination</option>';
        officeDestinations.forEach(function (office) {
            let isDisabled = selectedOffices.includes(office.officecode) || officedest.includes(office.officecode);
            officeDesOptions += `<option value="${office.officecode}" ${isDisabled ? 'disabled' : ''}>${office.shortname} - ${office.officename}${isDisabled ? ' (selected)' : ''}</option>`;
        });
        var actReqOptions = '<option value="">Select Action Required</option>';
        actionReq.forEach(function (actreq) {
            actReqOptions += `<option value="${actreq.reqaction_code}">${actreq.reqaction_desc}</option>`;
        });
        var newRow = `
            <tr class="destination-group dynamic-group">
                <td class="office_destination">
                    <div class="office_destinationDiv">
                        <select name="office_destination[]" class="select-select2 office_destination">
                            ${officeDesOptions}
                        </select>
                        <span class="help-block office_destinationMessage"></span>
                    </div>
                </td>
                <td class="action_officer">
                    <div class="action_officerDiv">
                        <select name="action_officer[]" class="select-select2 action_officer ao">
                            <option value="">Select Action Officer</option>
                        </select>
                        <span class="help-block action_officerMessage"></span>
                    </div>
                </td>
                <td class="action_required">
                    <div class="action_requiredDiv">
                        <select name="action_required[]" class="select-select2 action_required ar">
                            ${actReqOptions}
                        </select>
                        <span class="help-block action_requiredMessage"></span>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm removeDestinationBtn"><i class="fa fa-minus"></i></button>
                </td>
            </tr>`;
        $('#destinationContainer tbody').append(newRow);
        outgoingDes.initSelect2ForNewRow($('#destinationContainer tbody tr:last-child'));
        $('#destinationContainer tbody tr:last-child').find('.office_destination').trigger('change.select2');
        $('#destinationContainer tbody tr:last-child').find('.action_officer select').trigger('change.select2');
    },

    changeDestinationModal: function () {
        var id = $(this).data('id');
        var modal = $('#destination-modal-change');
        $.ajax({
            url: base_url + '/changeDestinationData',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (data) {
                if (data.success) {
                    outgoingDes.populateModalChangeDest(modal, data);
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

    populateModalChangeDest: function (modal, data) {
        var detaildata = data.detaildata;
        $('#dd').val(detaildata.doc_detailno);
        outgoingDes.populateOfficeDestinationChange(data.office, detaildata.office_destination, data.otherDestinations);
        outgoingDes.populateActionOfficerChange(data.officeuser, detaildata.action_officer);
        outgoingDes.populateActionRequiredChange(data.action_required, detaildata.action_required);
    },

    populateOfficeDestinationChange: function (options, selected, othdest) {
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
        options.forEach(function (office) {
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

    populateActionOfficerChange: function (options, selected) {
        const selectElement = $('#change_action_officer').empty();
        var selectedArray = [];
        if (Array.isArray(selected)) {
            selectedArray = selected.map(actionofficer => actionofficer.trim());
        } else if (selected !== "") {
            selectedArray = selected.split(',').map(actionofficer => actionofficer.trim());
        }
        options.forEach(function (actionofficer) {
            const option = new Option(actionofficer.lastname + ", " + actionofficer.firstname + " " + actionofficer.middlename.charAt(0) + ".", actionofficer.empcode);
            if (selectedArray.includes(actionofficer.empcode)) {
                option.selected = true;
            }
            selectElement.append(option);
        });
    },

    populateActionOfficerChangeByOffice: function (event) {
        const selectElement = $('#change_action_officer').empty();
        const selectElementOffice = $(event.target);
        officedestination = selectElementOffice.val();
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

    populateActionRequiredChange: function (options, selected) {
        const selectElement = $('#change_action_required').empty();
        let selectedArray = [];
        if (Array.isArray(selected)) {
            selectedArray = selected.map(actionrequired => actionrequired.trim());
        } else if (selected !== "") {
            selectedArray = selected.split(',').map(actionrequired => actionrequired.trim());
        }
        options.forEach(function (actionrequired) {
            const option = new Option(actionrequired.reqaction_desc, actionrequired.reqaction_code);
            if (selectedArray.includes(actionrequired.reqaction_code)) {
                option.selected = true;
            }
            selectElement.append(option);
        });
    },

    initSelect2ForNewRow: function (newRow) {
        $(newRow).find('.select-select2').select2({ width: '100%' });
    },

    removeDestinationRow: function (event) {
        $(event.currentTarget).closest('tr').remove();
        outgoingDes.updateOptions();
    },

    updateOptions: function () {
        let selectedOffices = outgoingDes.getSelectedOffices();
        $('.office_destination select').each(function () {
            const $this = $(this);
            $this.find('option').each(function () {
                const $option = $(this);
                const optionValue = $option.val();
                $option.prop('disabled', false);
                if (selectedOffices.includes(optionValue) && optionValue !== $this.val()) {
                    $option.prop('disabled', true);
                }
                if (officedest.includes(optionValue)) {
                    $option.prop('disabled', true);
                }
            });
            $this.select2({ width: '100%' });
        });
    },

    getSelectedOffices: function () {
        return $('#destinationContainer .office_destination select').map(function () {
            return $(this).val();
        }).get();
    },

    updateActionOfficers: function (event) {
        const office_destination = $(event.target).val();
        const actionOfficerSelect = $(event.target).closest('tr').find('.action_officer select');
        actionOfficerSelect.empty().append('<option value="">Select Action Officer</option>');
        if (office_destination) {
            $.ajax({
                url: base_url + '/getuserbyoffice',
                type: 'POST',
                data: { office_destination: office_destination },
                dataType: 'json',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
                success: function (data) {
                    if (data.success) {
                        const actionoffice = data.data;
                        if (actionoffice.length > 0) {
                            actionoffice.forEach(function (officer) {
                                actionOfficerSelect.append(`<option value="${officer.actionofficerid}">${officer.actionofficername}</option>`);
                            });
                        }
                    } else {
                        alert(data.message + "tets");
                    }
                    actionOfficerSelect.trigger('change.select2');
                    actionOfficerSelect.select2({ width: '100%' });
                },
                error: function (xhr, status, error) {
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        alert("Error Code " + xhr.status + ": " + error + "\n" +
                            "Message: " + xhr.responseJSON.error);
                    } else {
                        alert('An unknown error occurred.');
                    }
                },
            });
        }
    },

    destinationFormSubmit: function (event) {
        event.preventDefault();
        var form = $('#documentDestinationForm')[0];
        var formData = new FormData(form);
        var formData = $('#documentDestinationForm').serialize();
        outgoingApp.clearFormValidation();
        $.ajax({
            url: base_url + '/addDestination',
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
                        $('#documentDestinationForm')[0].reset();
                        $('#destinationContainer').find('.dynamic-group').remove();
                        $('#destination-modal-add').modal('hide');
                        outgoingDes.updateOptions();
                        outgoingApp.clearFormValidation();
                        outgoingDes.loadDocumentControls(response.routeno);
                    } else {
                        if (response.formnotvalid) {
                            outgoingDes.handleValidationErrorsDynamic(response.data);
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

    handleValidationErrorsDynamic: function (errors) {
        $('.help-block').text('');
        $('.help-block').removeClass('text-error');
        $.each(errors.office_destination, function (index, error) {
            var errorElement = $('.destination-group').eq(index).find('.office_destinationMessage');
            var parentDiv = $('.destination-group').eq(index).find('.office_destinationDiv');
            errorElement.text(error);
            parentDiv.addClass('has-error');
        });
        if (errors.action_officer) {
            $.each(errors.action_officer, function (index, error) {
                var errorElement = $('.destination-group').eq(index).find('.action_officerMessage');
                var parentDiv = $('.destination-group').eq(index).find('.action_officerDiv');
                errorElement.text(error);
                parentDiv.addClass('has-error');
            });
        }
        if (errors.action_required) {
            $.each(errors.action_required, function (index, error) {
                var errorElement = $('.destination-group').eq(index).find('.action_requiredMessage');
                var parentDiv = $('.destination-group').eq(index).find('.action_requiredDiv');
                errorElement.text(error);
                parentDiv.addClass('has-error');
            });
        }
    },

    resetDestination: function () {
        $('#documentDestinationForm')[0].reset();
        $('#action_officer').empty();
        $('#action_officer').append('Select Action Officer');
        $('#destinationContainer').find('.dynamic-group').remove();
        outgoingDes.updateOptions();
        outgoingApp.clearFormValidation();
    },

    addDestinationModal: function () {
        var modal = $('#destination-modal-add');
        modal.modal('show');
    },

    closeDestinationModal: function () {
        outgoingDes.resetDestination();
    },
    
    loadDocumentControls: function (routeno) {
        $.ajax({
            url: base_url + 'destinationTbl',
            type: 'POST',
            data: { routeno: routeno },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (response) {
                let content = '';
                response.forEach(control => {
                    content += `
                    <div class="widget">
                        <div class="widget-content widget-content-mini themed-background-dark text-light-op">
                            <span class="pull-right text-muted">1 week ago</span>
                            <span class="text-light">Destination Details for <b><u>${control.control_id}</u></b></span>
                        </div>
                        <div class="widget-content">
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
                    if (control.oth_dest) {
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
                $('#documentControlsContainer').html(content);
            },
            error: function (xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert("Error Code " + xhr.status + ": " + error + "\n" +
                        "Message: " + xhr.responseJSON.error);
                } else {
                    alert('An unknown error occurred...' + status);
                }
            },
            complete: function () {
                $("#overlay").hide();
            }
        });
    },

    submitChangeDestination: function (event) {
        event.preventDefault();
        var form = $('#changeDestinationForm')[0];
        var formData = new FormData(form);
        var formData = $('#changeDestinationForm').serialize();
        outgoingApp.clearFormValidation();
        $.ajax({
            url: base_url + '/submitChangeDestination',
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
                        outgoingApp.clearFormValidation();
                        outgoingDes.loadDocumentControls(response.routeno);
                    } else {
                        if (response.formnotvalid) {
                            handleValidationErrors(response.data);
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

    deleteDestination: function () {
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
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var password = result.value;
                outgoingDes.deleteDestinationAjax(password, id);
            }
        });
    },
    
    deleteDestinationAjax: function (password, id) {
        $.ajax({
            url: base_url + '/deleteDestination',
            type: 'POST',
            data: { id: id, password: password },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire('Deleted!', data.message, 'success');
                    outgoingDes.loadDocumentControls(data.routeno);
                } else {
                    Swal.fire('Error!', data.message, 'error');
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
    }
};
