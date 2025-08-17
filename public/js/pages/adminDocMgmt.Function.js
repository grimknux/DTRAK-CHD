
const adminDocManagement = {
    init: function() {
        this.bindEvents();
    },

    bindEvents: function() {
        $(document).on('click', '#document-management-table tbody .delete_route', this.confirmDelete.bind(this));

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

    
    confirmDelete: function(event){

        const target = $(event.currentTarget);
        var routeno = target.closest('tr').attr('routeno');

        Swal.fire({
            title: "Are you sure you want to delete document with Route No.: " + routeno + "?",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Confirm",
            denyButtonText: "Cancel"

        }).then((result) => {

            if (result.isConfirmed) {
                adminDocManagement.deleteDocumentRoute(routeno);
            } else if (result.isDenied) {
                console.log("User cancelled deletion.");
            }

        });
        
    },

    deleteDocumentRoute: function(routeno) {
        
        var formData = new FormData();
        formData.append('routeno', routeno);

        adminDocManagement.ajaxRequest({
            url: base_url + '/admin/document_management/delete',
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

                        $('#document-management-table').dataTable().api().ajax.reload();

                    } else {

                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                        $('#document-management-table').dataTable().api().ajax.reload();
                        
                    }
                } catch (error) {
                    console.error("Error processing response:", error);
                }
            },
        });

    },

};
