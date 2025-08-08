
const refDocType = {
    init: function() {
        this.bindEvents();
    },

    bindEvents: function() {
        $(document).on('submit', '#document_type_form', this.handleSubmitForm.bind(this));
        $(document).on('click', '#document_type_table tbody .edit_document_type', this.editDocType.bind(this));
        $(document).on('click', '#document_type_table tbody .delete_document_type', this.confirmDelete.bind(this));
        $(document).on('click', '#document_type_table tbody .inactive_document_type', this.confirmInactive.bind(this));
        $(document).on('click', '#document_type_table tbody .reactivate_document_type', this.confirmReactivate.bind(this));
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
        var mode = $('#document_type_form').attr('data-mode');
        var type_code =  $('#document_type_form').attr('data-id');
        formData.append('type_code', type_code);
        let url = mode === 'edit' ? '/admin/reference/document_type/update' : '/admin/reference/document_type/add';
        

        refDocType.ajaxRequest({
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
                            refDocType.cancelEdit();
                        }else{
                            refDocType.clearForm();
                        }
                        $('#document_type_table').DataTable().ajax.reload(null, false);

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

    editDocType: function(event) {
        
        const target = $(event.currentTarget);
        var type_code = target.closest('tr').attr('type_code');
        $('#document_type_form').attr('data-mode', 'edit').attr('data-id', type_code);
        $('#submit_btn_action').html('<i class="gi gi-disk_save"></i> Update').removeClass('btn-primary').addClass('btn-success');
        $('#cancel_btn_action').html('<i class="fa fa-user-times"></i> Cancel edit').removeClass('reset_btn_action').addClass('cancel_btn_action');

        var formData = new FormData();
        formData.append('type_code', type_code);
        refDocType.ajaxRequest({
            url: base_url + '/admin/reference/document_type/get',
            type: 'POST',
            data: formData,
            success: function(response) {
                try {
                    if (response.success) {

                        var data = response.data;
                        
                        $("#doc_type").val(data.type_desc);

                        refDocType.clearValidation();

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
        var type_code = target.closest('tr').attr('type_code');
        var type_desc = target.closest('tr').attr('type_desc');

        Swal.fire({
            title: "Are you sure you want to delete " + type_desc + "?",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel"

        }).then((result) => {

            if (result.isConfirmed) {
                refDocType.deleteDocumentType(type_code);
            } else if (result.isDenied) {
                console.log("User cancelled deletion.");
            }

        });
        
    },

    deleteDocumentType: function(type_code) {
        
        var formData = new FormData();
        formData.append('type_code', type_code);

        refDocType.ajaxRequest({
            url: base_url + '/admin/reference/document_type/delete',
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

                        refDocType.cancelEdit();
                        $('#document_type_table').DataTable().ajax.reload(null, false);

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
        var type_code = target.closest('tr').attr('type_code');
        var type_desc = target.closest('tr').attr('type_desc');

        Swal.fire({
            title: "Are you sure you want to Deactivate " + type_desc + "?",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel"

        }).then((result) => {

            if (result.isConfirmed) {
                refDocType.inactiveDocumentType(type_code);
            } else if (result.isDenied) {
                console.log("User cancelled inactive.");
            }

        });
        
    },

    inactiveDocumentType: function(type_code) {
        
        var formData = new FormData();
        formData.append('type_code', type_code);

        refDocType.ajaxRequest({
            url: base_url + '/admin/reference/document_type/inactive',
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

                        refDocType.cancelEdit();
                        $('#document_type_table').DataTable().ajax.reload(null, false);

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
        var type_code = target.closest('tr').attr('type_code');
        var type_desc = target.closest('tr').attr('type_desc');

        Swal.fire({
            title: "Are you sure you want to Reactivate " + type_desc + "?",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel"

        }).then((result) => {

            if (result.isConfirmed) {
                refDocType.reactivateDocumentType(type_code);
            } else if (result.isDenied) {
                console.log("User cancelled inactive.");
            }

        });

    },

    reactivateDocumentType: function(type_code) {
        
        var formData = new FormData();
        formData.append('type_code', type_code);

        refDocType.ajaxRequest({
            url: base_url + '/admin/reference/document_type/reactivate',
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

                        refDocType.cancelEdit();
                        $('#document_type_table').DataTable().ajax.reload(null, false);

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
        $('#document_type_form').removeAttr('data-mode').removeAttr('data-id');
        $('#submit_btn_action').html('<i class="fa fa-user-plus"></i> Create').removeClass('btn-success').addClass('btn-primary');
        $('#cancel_btn_action').html('<i class="fa fa-refresh"></i> Reset').removeClass('cancel_btn_action').addClass('reset_btn_action');

        refDocType.clearForm();
    },

    clearForm: function(event){

        $('#document_type_form')[0].reset();
        $("#doc_type").val('');


        refDocType.clearValidation();

    },

    clearValidation: function(event){
        
        $(".doc_type").removeClass('has-success has-error').removeClass('has-error');
        $(".help-block").html('');
    }



};
