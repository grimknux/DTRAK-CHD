
const refActionOfficer = {
    init: function() {
        this.bindEvents();
    },

    bindEvents: function() {
        $('#pmis_emp').on('change', this.handlePMISEmployee.bind(this));
        //$('#action_officer_form').on('submit', this.handleAddForm.bind(this));
        $(document).on('submit', '#action_officer_form', this.handleSubmitForm.bind(this));
        $(document).on('click', '#action_officer_table tbody .edit_action_officer', this.editActionOfficer.bind(this));
        $(document).on('click', '#action_officer_table tbody .delete_action_officer', this.confirmDelete.bind(this));
        $(document).on('click', '#action_officer_table tbody .inactive_action_officer', this.confirmInactive.bind(this));
        $(document).on('click', '#action_officer_table tbody .reactivate_action_officer', this.confirmReactivate.bind(this));
        $(document).on('click', '#action_officer_table tbody .reset_password_action_officer', this.confirmResetPassword.bind(this));
        $(document).on('change', '#user_level', (e) => { const val = $(e.target).val(); this.handleIfAdmin(val);});
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

    handlePMISEmployee: function(event) {
        const selectElement = $(event.target); // the <select> element
        const selectedOption = selectElement.find('option:selected');

        const firstname = selectedOption.data('firstname');
        const middlename = selectedOption.data('middlename');
        const lastname = selectedOption.data('lastname');
        const username = selectElement.val();

        $('#username').val(username);
        $('#firstname').val(firstname || '');
        $('#middlename').val(middlename || '');
        $('#lastname').val(lastname || '');
    },

    handleSubmitForm: function(event) {
        event.preventDefault();
        var formData = new FormData(event.target);

        var form = $(event.target); // or $('#action_officer_form')
        var mode = $('#action_officer_form').attr('data-mode');
        var empid =  $('#action_officer_form').attr('data-id');
        formData.append('empid', empid);
        let url = mode === 'edit' ? '/admin/reference/action_officer/update' : '/admin/reference/action_officer/add';
        

        refActionOfficer.ajaxRequest({
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
                            refActionOfficer.cancelEdit();
                        }else{
                            refActionOfficer.clearForm();
                        }
                        $('#action_officer_table').DataTable().ajax.reload(null, false);

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

    editActionOfficer: function(event) {
        
        const target = $(event.currentTarget);
        var empid = target.closest('tr').attr('empid');
        $('#action_officer_form').attr('data-mode', 'edit').attr('data-id', empid);
        $('#submit_btn_action').html('<i class="gi gi-disk_save"></i> Update').removeClass('btn-primary').addClass('btn-success');
        $('#cancel_btn_action').html('<i class="fa fa-user-times"></i> Cancel edit').removeClass('reset_btn_action').addClass('cancel_btn_action');
        $('#pmis_emp').val(null).trigger('change'); // Clear the selection
        $('#pmis_emp').prop('disabled', true);

        var formData = new FormData();
        formData.append('empid', empid);
        refActionOfficer.ajaxRequest({
            url: base_url + '/admin/reference/action_officer/get',
            type: 'POST',
            data: formData,
            success: function(response) {
                try {
                    if (response.success) {

                        var data = response.data;
                        
                        
                        $("#username").val(data.empcode).prop('disabled', true);
                        $("#firstname").val(data.firstname);
                        $("#middlename").val(data.middlename);
                        $("#lastname").val(data.lastname);
                        $("#office").val(data.offices).trigger("change");
                        $("#office_rep").val(data.office_rep).trigger("change.select2");
                        $("#user_level").val(data.userlevel).trigger("change.select2");
                        if (Array.isArray(data.admin_menu)) {
                            const validAdminMenus = data.admin_menu.filter(v => v && v !== '');
                            $("#admin_menu").val(validAdminMenus).trigger("change.select2");
                        } else {
                            $("#admin_menu").val([]).trigger("change.select2");
                        }

                        refActionOfficer.handleIfAdmin(data.userlevel);

                        refActionOfficer.clearValidation();

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
        var empid = target.closest('tr').attr('empid');
        var name = target.closest('tr').attr('name');

        Swal.fire({
            title: "Are you sure you want to delete " + name + "?",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel"

        }).then((result) => {

            if (result.isConfirmed) {
                refActionOfficer.deleteActionOfficer(empid);
            } else if (result.isDenied) {
                console.log("User cancelled deletion.");
            }

        });
        
    },

    deleteActionOfficer: function(empid) {
        
        var formData = new FormData();
        formData.append('empid', empid);

        refActionOfficer.ajaxRequest({
            url: base_url + '/admin/reference/action_officer/delete',
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

                        refActionOfficer.cancelEdit();
                        $('#action_officer_table').DataTable().ajax.reload(null, false);

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
        var empid = target.closest('tr').attr('empid');
        var name = target.closest('tr').attr('name');

        Swal.fire({
            title: "Are you sure you want to Deactivate " + name + "?",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel"

        }).then((result) => {

            if (result.isConfirmed) {
                refActionOfficer.inactiveActionOfficer(empid);
            } else if (result.isDenied) {
                console.log("User cancelled inactive.");
            }

        });
        
    },

    inactiveActionOfficer: function(empid) {
        
        var formData = new FormData();
        formData.append('empid', empid);

        refActionOfficer.ajaxRequest({
            url: base_url + '/admin/reference/action_officer/inactive',
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

                        refActionOfficer.cancelEdit();
                        $('#action_officer_table').DataTable().ajax.reload(null, false);

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
        var empid = target.closest('tr').attr('empid');
        var name = target.closest('tr').attr('name');

        Swal.fire({
            title: "Are you sure you want to Reactivate " + name + "?",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel"

        }).then((result) => {

            if (result.isConfirmed) {
                refActionOfficer.reactivateActionOfficer(empid);
            } else if (result.isDenied) {
                console.log("User cancelled inactive.");
            }

        });

    },

    reactivateActionOfficer: function(empid) {
        
        var formData = new FormData();
        formData.append('empid', empid);

        refActionOfficer.ajaxRequest({
            url: base_url + '/admin/reference/action_officer/reactivate',
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

                        refActionOfficer.cancelEdit();
                        $('#action_officer_table').DataTable().ajax.reload(null, false);

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

    confirmResetPassword: function(event) {
        
        const target = $(event.currentTarget);
        var empid = target.closest('tr').attr('empid');
        var name = target.closest('tr').attr('name');

        Swal.fire({
            title: "Are you sure you want to Reset Password? " + name + "?",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel"

        }).then((result) => {

            if (result.isConfirmed) {
                refActionOfficer.resetpasswordActionOfficer(empid);
            } else if (result.isDenied) {
                console.log("User cancelled reset password.");
            }

        });

    },

    resetpasswordActionOfficer: function(empid) {
        
        var formData = new FormData();
        formData.append('empid', empid);

        refActionOfficer.ajaxRequest({
            url: base_url + '/admin/reference/action_officer/reset_password',
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

                        refActionOfficer.cancelEdit();
                        $('#action_officer_table').DataTable().ajax.reload(null, false);

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

    handleIfAdmin(value) {
        const selectedValue = value;

        if (selectedValue === '-1') {
            $('.admin_menu').show();
        } else {
            $('.admin_menu').hide();
            $(".admin_menu").removeClass('has-success has-error').removeClass('has-error');
            $("#admin_menu").val([]).trigger("change.select2");
            $(".admin_menuMessage").html('');
        }
    },
    

    cancelEdit: function(event){
        $('#action_officer_form').removeAttr('data-mode').removeAttr('data-id');
        $('#submit_btn_action').html('<i class="fa fa-user-plus"></i> Create').removeClass('btn-success').addClass('btn-primary');
        $('#cancel_btn_action').html('<i class="fa fa-refresh"></i> Reset').removeClass('cancel_btn_action').addClass('reset_btn_action');
        $('#pmis_emp').prop('disabled', false);

        refActionOfficer.clearForm();
    },

    clearForm: function(event){

        $('#action_officer_form')[0].reset();
        $("#username").prop('disabled', false);
        $("#firstname").val('');
        $("#middlename").val('');
        $("#lastname").val('');
        $("#office").trigger("change.select2");
        $("#office_rep").trigger("change.select2");
        $("#user_level").trigger("change.select2");
        $("#admin_menu").trigger("change.select2");
        $('.admin_menu').hide();
        $('#pmis_emp').val('').trigger('change');


        refActionOfficer.clearValidation();

    },

    clearValidation: function(event){
        
        $(".username").removeClass('has-success has-error').removeClass('has-error');
        $(".firstname").removeClass('has-success has-error').removeClass('has-error');
        $(".middlename").removeClass('has-success has-error').removeClass('has-error');
        $(".lastname").removeClass('has-success has-error').removeClass('has-error');
        $(".office").removeClass('has-success has-error').removeClass('has-error');
        $(".office_rep").removeClass('has-success has-error').removeClass('has-error');
        $(".user_level").removeClass('has-success has-error').removeClass('has-error');
        $(".admin_menu").removeClass('has-success has-error').removeClass('has-error');
        $(".help-block").html('');
    }



};
