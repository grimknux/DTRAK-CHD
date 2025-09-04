
const refActTaken = {
    init: function () {
        this.bindEvents();
    },

    bindEvents: function () {
        $(document).on(
            "submit",
            "#action_taken_form",
            this.handleSubmitForm.bind(this)
        );

        $(document).on(
            "click",
            "#action_taken_table tbody .edit_action_taken",
            this.editActTaken.bind(this)
        );
        $(document).on(
            "click",
            "#action_taken_table tbody .delete_action_taken",
            this.confirmDelete.bind(this)
        );
        $(document).on(
            "click",
            "#action_taken_table tbody .inactive_action_taken",
            this.confirmInactive.bind(this)
        );
        $(document).on(
            "click",
            "#action_taken_table tbody .reactivate_action_taken",
            this.confirmReactivate.bind(this)
        );
        $(document).on("click", ".cancel_btn_action", this.cancelEdit.bind(this));
        $(document).on("click", ".reset_btn_action", this.clearForm.bind(this));
    },

    ajaxRequest: function (options) {
        var defaultOptions = {
            url: "",
            type: "POST",
            data: {},
            dataType: "json",
            processData: false,
            contentType: false,
            beforeSend: function (xhr) {
                xhr.setRequestHeader("X-CSRF-Token", csrfToken);
                $("#overlay").show();
            },
            success: function (response) { },
            error: function (xhr, errorType, thrownError) {
                if (xhr.status === 403 || xhr.status === 405) {
                    alert(xhr.responseText);
                    console.log("Server error: " + xhr.responseText);
                } else {
                    var errorMessage =
                        xhr.responseJSON && xhr.responseJSON.error
                            ? xhr.responseJSON.error
                            : xhr.statusText;
                    console.log("Server error: " + xhr.responseText);
                }
            },
            complete: function () {
                $("#overlay").hide();
            },
        };

        // Extend default options with custom options
        $.extend(defaultOptions, options);

        // Perform the AJAX request
        $.ajax(defaultOptions);
    },

    handleSubmitForm: function (event) {
        event.preventDefault();
        var formData = new FormData(event.target);

        var form = $(event.target); // or $('#document_type_form')
        var mode = $("#action_taken_form").attr("data-mode");
        var action_code = $("#action_taken_form").attr("data-id");
        formData.append("action_code", action_code);
        let url =
            mode === "edit"
                ? "/admin/reference/action_taken/update"
                : "/admin/reference/action_taken/add";

        refActTaken.ajaxRequest({
            url: base_url + url,
            type: "POST",
            data: formData,
            success: function (response) {
                try {
                    if (response.success) {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2500,
                        });

                        if (mode == "edit") {
                            refActTaken.cancelEdit();
                        } else {
                            refActTaken.clearForm();
                        }
                        $("#action_taken_table").DataTable().ajax.reload(null, false);
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

    editActTaken: function (event) {
        const target = $(event.currentTarget);
        var action_code = target.closest("tr").attr("action_code");
        $("#action_taken_form")
            .attr("data-mode", "edit")
            .attr("data-id", action_code);
        $("#submit_btn_action")
            .html('<i class="gi gi-disk_save"></i> Update')
            .removeClass("btn-primary")
            .addClass("btn-success");
        $("#cancel_btn_action")
            .html('<i class="fa fa-user-times"></i> Cancel edit')
            .removeClass("reset_btn_action")
            .addClass("cancel_btn_action");

        var formData = new FormData();
        formData.append("action_code", action_code);
        refActTaken.ajaxRequest({
            url: base_url + "/admin/reference/action_taken/get",
            type: "POST",
            data: formData,
            success: function (response) {
                try {
                    if (response.success) {
                        var data = response.data;

                        $("#action_taken").val(data.action_desc);

                        refActTaken.clearValidation();
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

    confirmDelete: function (event) {
        const target = $(event.currentTarget);
        var action_code = target.closest("tr").attr("action_code");
        var action_desc = target.closest("tr").attr("action_desc");

        Swal.fire({
            title: "Are you sure you want to delete " + action_desc + "?",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                refActTaken.deleteActionTaken(action_code);
            } else if (result.isDenied) {
                console.log("User cancelled deletion.");
            }
        });
    },

    deleteActionTaken: function (action_code) {
        var formData = new FormData();
        formData.append("action_code", action_code);

        refActTaken.ajaxRequest({
            url: base_url + "/admin/reference/action_taken/delete",
            type: "POST",
            data: formData,
            success: function (response) {
                try {
                    if (response.success) {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2500,
                        });

                        refActTaken.cancelEdit();
                        $("#action_taken_table").DataTable().ajax.reload(null, false);
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

    confirmInactive: function (event) {
        const target = $(event.currentTarget);
        var action_code = target.closest("tr").attr("action_code");
        var action_desc = target.closest("tr").attr("action_desc");

        Swal.fire({
            title: "Are you sure you want to Deactivate " + action_desc + "?",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                refActTaken.inactiveActionTaken(action_code);
            } else if (result.isDenied) {
                console.log("User cancelled inactive.");
            }
        });
    },

    inactiveActionTaken: function (action_code) {
        var formData = new FormData();
        formData.append("action_code", action_code);

        refActTaken.ajaxRequest({
            url: base_url + "/admin/reference/action_taken/inactive",
            type: "POST",
            data: formData,
            success: function (response) {
                try {
                    if (response.success) {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2500,
                        });

                        refActTaken.cancelEdit();
                        $("#action_taken_table").DataTable().ajax.reload(null, false);
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

    confirmReactivate: function (event) {
        const target = $(event.currentTarget);
        var action_code = target.closest("tr").attr("action_code");
        var action_desc = target.closest("tr").attr("action_desc");

        Swal.fire({
            title: "Are you sure you want to Reactivate " + action_desc + "?",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                refActTaken.reactivateActionTaken(action_code);
            } else if (result.isDenied) {
                console.log("User cancelled inactive.");
            }
        });
    },

    reactivateActionTaken: function (action_code) {
        var formData = new FormData();
        formData.append("action_code", action_code);

        refActTaken.ajaxRequest({
            url: base_url + "/admin/reference/action_taken/reactivate",
            type: "POST",
            data: formData,
            success: function (response) {
                try {
                    if (response.success) {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2500,
                        });

                        refActTaken.cancelEdit();
                        $("#action_taken_table").DataTable().ajax.reload(null, false);
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

    cancelEdit: function (event) {
        $("#action_taken_form").removeAttr("data-mode").removeAttr("data-id");
        $("#submit_btn_action")
            .html('<i class="fa fa-user-plus"></i> Create')
            .removeClass("btn-success")
            .addClass("btn-primary");
        $("#cancel_btn_action")
            .html('<i class="fa fa-refresh"></i> Reset')
            .removeClass("cancel_btn_action")
            .addClass("reset_btn_action");

        refActTaken.clearForm();
    },

    clearForm: function (event) {
        $("#action_taken_form")[0].reset();
        $("#action_taken").val("");
        $("#action_taken").trigger("change.select2");

        refActTaken.clearValidation();
    },

    clearValidation: function (event) {
        $(".action_taken")
            .removeClass("has-success has-error")
            .removeClass("has-error");
        $(".action_taken")
            .removeClass("has-success has-error")
            .removeClass("has-error");
        $(".help-block").html("");
    },
};
