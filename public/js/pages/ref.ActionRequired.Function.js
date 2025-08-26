
const refActReq = {
    init: function() {
        this.bindEvents();
    },

    bindEvents: function() {
        $(document).on('submit', '#action_required_form', this.handleSubmitForm.bind(this));

        $(document).on('click', '#action_required_table tbody .edit_action_required', this.editActReq.bind(this));
        $(document).on('click', '#action_required_table tbody .delete_action_required', this.confirmDelete.bind(this));
        $(document).on('click', '#action_required_table tbody .inactive_action_required', this.confirmInactive.bind(this));
        $(document).on('click', '#action_required_table tbody .reactivate_action_required', this.confirmReactivate.bind(this));
        $(document).on('click', '.cancel_btn_action', this.cancelEdit.bind(this));
        $(document).on('click', '.reset_btn_action', this.clearForm.bind(this));

    },

    ajaxRequest: function(options) {
        var defaultOptions = {
            url: '',
            type: 'POST',
            data: {},
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function(response) {},
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
        };

        // Extend default options with custom options
        $.extend(defaultOptions, options);

        // Perform the AJAX request
        $.ajax(defaultOptions);
    },

    handleSubmitForm: function(event) {
        event.preventDefault();
        var formData = new FormData(event.target);

        var form = $(event.target); // or $('#document_type_form')
        var mode = $('#action_required_form').attr('data-mode');
        var reqaction_code =  $('#action_required_form').attr('data-id');
        formData.append('reqaction_code', reqaction_code);
        let url = mode === 'edit' ? '/admin/reference/action_required/update' : '/admin/reference/action_required/add';
        

        refActReq.ajaxRequest({
            url: base_url + url,
            type: 'POST',
            data: formData,
            success: function(response) {
                try {
                    if (response.success) {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2500
                        });
                        
                        if(mode == 'edit'){
                            refActReq.cancelEdit();
                        }else{
                            refActReq.clearForm();
                        }
                        $('#action_required_table').DataTable().ajax.reload(null, false);

                    } else {

                        if(response.formnotvalid){
                            handleValidationErrors(response.data);
                        }else{
                            Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        }
                        
                       
                    }
                } catch (error) {
                    console.error("Error processing response:", error);
                }
            },
        });
    },

    editActReq: function(event) {
        
        const target = $(event.currentTarget);
        var reqaction_code = target.closest('tr').attr('reqaction_code');
        $('#action_required_form').attr('data-mode', 'edit').attr('data-id', reqaction_code);
        $('#submit_btn_action').html('<i class="gi gi-disk_save"></i> Update').removeClass('btn-primary').addClass('btn-success');
        $('#cancel_btn_action').html('<i class="fa fa-user-times"></i> Cancel edit').removeClass('reset_btn_action').addClass('cancel_btn_action');

        var formData = new FormData();
        formData.append('reqaction_code', reqaction_code);
        refActReq.ajaxRequest({
            url: base_url + '/admin/reference/action_required/get',
            type: 'POST',
            data: formData,
            success: function(response) {
                try {
                    if (response.success) {

                        var data = response.data;
                        
                        $("#action_required").val(data.reqaction_desc);
                        $("#action_taken").val(data.reqaction_done).trigger("change.select2");

                        refActReq.clearValidation();

                    } else {

                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                       
                    }
                } catch (error) {
                    console.error("Error processing response:", error);
                }
            },
        });
    },

    confirmDelete: function(event){

        const target = $(event.currentTarget);
        var reqaction_code = target.closest('tr').attr('reqaction_code');
        var reqaction_desc = target.closest('tr').attr('reqaction_desc');

        Swal.fire({
            title: "Are you sure you want to delete " + reqaction_desc + "?",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel"

        }).then((result) => {

            if (result.isConfirmed) {
                refActReq.deleteActionRequired(reqaction_code);
            } else if (result.isDenied) {
                console.log("User cancelled deletion.");
            }

        });
        
    },

    deleteActionRequired: function(reqaction_code) {
        
        var formData = new FormData();
        formData.append('reqaction_code', reqaction_code);

        refActReq.ajaxRequest({
            url: base_url + '/admin/reference/action_required/delete',
            type: 'POST',
            data: formData,
            success: function(response) {
                try {
                    if (response.success) {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2500
                        });

                        refActReq.cancelEdit();
                        $('#action_required_table').DataTable().ajax.reload(null, false);

                    } else {

                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        
                    }
                } catch (error) {
                    console.error("Error processing response:", error);
                }
            },
        });

    },

    confirmInactive: function(event){

        const target = $(event.currentTarget);
        var reqaction_code = target.closest('tr').attr('reqaction_code');
        var reqaction_desc = target.closest('tr').attr('reqaction_desc');

        Swal.fire({
            title: "Are you sure you want to Deactivate " + reqaction_desc + "?",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel"

        }).then((result) => {

            if (result.isConfirmed) {
                refActReq.inactiveActionRequired(reqaction_code);
            } else if (result.isDenied) {
                console.log("User cancelled inactive.");
            }

        });
        
    },

    inactiveActionRequired: function(reqaction_code) {
        
        var formData = new FormData();
        formData.append('reqaction_code', reqaction_code);

        refActReq.ajaxRequest({
            url: base_url + '/admin/reference/action_required/inactive',
            type: 'POST',
            data: formData,
            success: function(response) {
                try {
                    if (response.success) {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2500
                        });

                        refActReq.cancelEdit();
                        $('#action_required_table').DataTable().ajax.reload(null, false);

                    } else {

                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        
                    }
                } catch (error) {
                    console.error("Error processing response:", error);
                }
            },
        });

    },

    confirmReactivate: function(event) {
        
        const target = $(event.currentTarget);
        var reqaction_code = target.closest('tr').attr('reqaction_code');
        var reqaction_desc = target.closest('tr').attr('reqaction_desc');

        Swal.fire({
            title: "Are you sure you want to Reactivate " + reqaction_desc + "?",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel"

        }).then((result) => {

            if (result.isConfirmed) {
                refActReq.reactivateActionRequired(reqaction_code);
            } else if (result.isDenied) {
                console.log("User cancelled inactive.");
            }

        });

    },

    reactivateActionRequired: function(reqaction_code) {
        
        var formData = new FormData();
        formData.append('reqaction_code', reqaction_code);

        refActReq.ajaxRequest({
            url: base_url + '/admin/reference/action_required/reactivate',
            type: 'POST',
            data: formData,
            success: function(response) {
                try {
                    if (response.success) {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2500
                        });

                        refActReq.cancelEdit();
                        $('#action_required_table').DataTable().ajax.reload(null, false);

                    } else {

                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        
                    }
                } catch (error) {
                    console.error("Error processing response:", error);
                }
            },
        });

    },    

    cancelEdit: function(event){
        $('#action_required_form').removeAttr('data-mode').removeAttr('data-id');
        $('#submit_btn_action').html('<i class="fa fa-user-plus"></i> Create').removeClass('btn-success').addClass('btn-primary');
        $('#cancel_btn_action').html('<i class="fa fa-refresh"></i> Reset').removeClass('cancel_btn_action').addClass('reset_btn_action');

        refActReq.clearForm();
    },

    clearForm: function(event){

        $('#action_required_form')[0].reset();
        $("#action_required").val('');
        $("#action_taken").trigger("change.select2");

        refActReq.clearValidation();

    },

    clearValidation: function(event){
        
        $(".action_required").removeClass('has-success has-error').removeClass('has-error');
        $(".action_taken").removeClass('has-success has-error').removeClass('has-error');
        $(".help-block").html('');
    }



};
