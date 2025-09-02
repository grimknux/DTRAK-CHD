var selectedIdsRcv = [];

const receiveApp = {
    init: function () {
        this.bindEvents();
    },

    bindEvents: function () {
        $('#receive-table tbody').on('click', '.rcv-modal', this.handleModalClick);
        $('#receive-table tbody').on('click', '.insta-rcv', this.handleInstaRcvClick);
        $('#submitBulkReceive').on('click', this.handleBulkRcvClick);
        $('#receive-form').on('submit', this.handleFormSubmit.bind(this));
    },

    handleModalClick: function () {
        var docdetail = $(this).data('docdetail');
        var modal = $('#receive-modal');
        $.ajax({
            url: base_url + '/receiveData',
            type: 'POST',
            data: { id: docdetail },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (data) {
                if (data.success) {
                    receiveApp.populateModalRcv(modal, data);
                    modal.modal('show');
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: data.message,
                    });
                    if (data.reload) {
                        $('#receive-table').DataTable().ajax.reload(null, false);
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

    handleInstaRcvClick: function () {
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
                        $('#receive-table').DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        if (response.reload) {
                            $('#receive-table').DataTable().ajax.reload(null, false);
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

    handleBulkRcvClick: function () {
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
                        $('#viewReceiveData').modal('hide');
                        $('#receive-table').DataTable().ajax.reload(null, false);
                        selectedIdsRcv = [];
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        if (response.reload) {
                            $('#viewReceiveData').modal('hide');
                            $('#receive-table').DataTable().ajax.reload(null, false);
                            selectedIdsRcv = [];
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

    handleFormSubmit: function (event) {
        event.preventDefault();
        var formData = $(event.target).serialize();
        $.ajax({
            url: base_url + "/receiveDoc",
            type: 'POST',
            data: formData,
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
                        $('#receive-modal').modal('hide');
                        $('#receive-form')[0].reset();
                        $('#receive-table').DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        if (response.reload) {
                            $('#receive-modal').modal('hide');
                            $('#receive-form')[0].reset();
                            $('#receive-table').DataTable().ajax.reload(null, false);
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

    populateModalRcv: function (modal, data) {
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