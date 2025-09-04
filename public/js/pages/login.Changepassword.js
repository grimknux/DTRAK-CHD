
const changePassword = {
    init: function () {
        this.bindEvents();
    },

    bindEvents: function () {
        $(document).on('submit', '#form_change_pass', this.handleSubmitForm.bind(this));
        $(document).on("click", "#btnCloseSidebar", this.closeSidebar);
    
    },

    ajaxRequest: function (options) {
        var defaultOptions = {
            url: '',
            type: 'POST',
            data: {},
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                $("#overlay").show();
            },
            success: function (response) { },
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
        };

        // Extend default options with custom options
        $.extend(defaultOptions, options);

        // Perform the AJAX request
        $.ajax(defaultOptions);
    },

    handleSubmitForm: function (event) {
        event.preventDefault();
        var formData = new FormData(event.target);

        changePassword.ajaxRequest({
            url: base_url + '/login/changepassword',
            type: 'POST',
            data: formData,
            success: function (response) {
                try {
                    if (response.success) {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2000,
                        }).then(() => {
                            window.location.href = "/logout";
                        });

                    } else {

                        if (response.formnotvalid) {
                            handleValidationErrors(response.data);
                        } else {
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

    clearForm: function (event) {

        $("#prof_old_password").val("");
        $("#prof_new_password").val("");
        $("#prof_new_password_confirm").val("");

        changePassword.clearValidation();

    },

    clearValidation: function (event) {

        $(".prof_old_password").removeClass('has-success has-error').removeClass('has-error');
        $(".prof_new_password").removeClass('has-success has-error').removeClass('has-error');
        $(".prof_new_password_confirm").removeClass('has-success has-error').removeClass('has-error');
        $(".help-block").html('');
    },

    closeSidebar: function () {
        App.sidebar('close-sidebar-alt');
        changePassword.clearForm();
    }


};
