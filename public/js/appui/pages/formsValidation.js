/*
 *  Document   : formsValidation.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Forms Validation page
 */

var FormsValidation = function() {

    return {
        init: function() {
            /*
             *  Jquery Validation, Check out more examples and documentation at https://github.com/jzaefferer/jquery-validation
             */

            /* Initialize Form Validation */
            $.validator.setDefaults({ ignore: ":hidden:not(select)" });
            $.validator.addMethod( "time12h", function( value, element ) {
                return this.optional( element ) || /^((0?[1-9]|1[012])(:[0-5]\d){1,2}(\ ?[AP]M))$/i.test( value );
            }, "Please enter a valid time in 12-hour am/pm format." );




            $('#example').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'val-username': {
                        required: true,
                        minlength: 3
                    },
                    'val-email': {
                        required: true,
                        email: true
                    },
                    'val-password': {
                        required: true,
                        minlength: 5
                    },
                    'val-confirm-password': {
                        required: true,
                        equalTo: '#val-password'
                    },
                    'val-suggestions': {
                        required: true,
                        minlength: 5
                    },
                    'val-skill': {
                        required: true
                    },
                    'val-website': {
                        required: true,
                        url: true
                    },
                    'val-digits': {
                        required: true,
                        digits: true
                    },
                    'val-number': {
                        required: true,
                        number: true
                    },
                    'val-range': {
                        required: true,
                        range: [1, 5]
                    },
                    'val-terms': {
                        required: true
                    }
                },
                messages: {
                    'val-username': {
                        required: 'Please enter a username',
                        minlength: 'Your username must consist of at least 3 characters'
                    },
                    'val-email': 'Please enter a valid email address',
                    'val-password': {
                        required: 'Please provide a password',
                        minlength: 'Your password must be at least 5 characters long'
                    },
                    'val-confirm-password': {
                        required: 'Please provide a password',
                        minlength: 'Your password must be at least 5 characters long',
                        equalTo: 'Please enter the same password as above'
                    },
                    'val-suggestions': 'What can we do to become better?',
                    'val-skill': 'Please select a skill!',
                    'val-website': 'Please enter your website!',
                    'val-digits': 'Please enter only digits!',
                    'val-number': 'Please enter a number!',
                    'val-range': 'Please enter a number between 1 and 5!',
                    'val-terms': 'You must agree to the service terms!'
                }
            });

            $('#add-form-doc').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'doc-subject': {
                        required: true,
                        minlength: 5
                    },
                    'doc-type': {
                        required: true
                    },
                    'doc-source': {
                        required: true
                    }
                },
                messages: {
                    'doc-subject': {
                        required: 'Please enter Subject!',
                        minlength: 'Your subject must be at least 5 characters long',
                    },
                    'doc-type': 'Please select Document!',
                    'doc-source': 'Please select Source!'
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#add-form-doc')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/docview-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
			                $("#add-modal").modal("hide");
                            call_ajax(1);
                            //alert(a);
                        }
                    });
                }

            });

            $('#add-form-des').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'des-office-des': {
                        required: true
                    },
                    'des-employee': {
                        required: true
                    },
                    'des-actionrequire': {
                        required: true
                    }
                },
                messages: {
                    'des-office-des': 'Please select Office Destination!',
                    'des-employee': 'Please select Action Officer!',
                    'des-actionrequire': 'Please select Action Required!'
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#add-form-des')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/docview-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
			                $("#add-modal-des").modal("hide");
                            call_ajax(1);
                            //alert(a);
                        }
                    });
                }

            });

            $('#update-form-des').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'daterec': {
                        required: true,
                        date: true
                    },
                    'timerec': {
                        required: true
                    }
                },
                messages: {
                    'daterec': {
                        required: 'Please enter date',
                        date: 'Please enter valid date'
                    },
                    'timerec': 'Please enter time'
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#update-form-des')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/doctoreceive-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
                            //alert(a);
			                $("#update-modal-des").modal("hide");
                            call_ajax(1);
                        }
                    });
                }

            });

            $('#update-form-des-act').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'act': {
                        required: true,
                    }
                },
                messages: {
                    'act': {
                        required: 'Please enter date'
                    }
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#update-form-des-act')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/doctoreceive-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
                            //alert(a);
			                $("#update-modal-des-act").modal("hide");
                            call_ajax(1);
                        }
                    });
                }

            });

            $('#update-form-des-rel').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'reloffice': {
                        required: true
                    },
                    'relactionofficer': {
                        required: true
                    },
                    'relactionrequire': {
                        required: true
                    },
                    'relpage': {
                        number: true
                    }
                },
                messages: {
                    'reloffice': {
                        required: 'Please select Office Destination'
                    },
                    'relactionofficer': {
                        required: 'Please select Action Officer'
                    },
                    'relactionrequire': {
                        required: 'Please select Action Required'
                    },
                    'relpage': {
                        required: 'Number only'
                    }
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#update-form-des-rel')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/doctoreceive-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
                            //alert(a);
			                $("#update-modal-des-rel").modal("hide");
                            call_ajax(1);
                        }
                    });
                }

            });

            $('#update-form-des-done').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                },
                messages: {
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#update-form-des-done')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/doctoreceive-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
                            //alert(a);
			                $("#update-modal-des-done").modal("hide");
                            call_ajax(1);
                        }
                    });
                }

            });

            $('#update-form-des-fwd').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'reloffice': {
                        required: true
                    },
                    'relactionofficer': {
                        required: true
                    },
                    'relactionrequire': {
                        required: true
                    },
                    'relpage': {
                        number: true
                    }
                },
                messages: {
                    'reloffice': {
                        required: 'Please select Office Destination'
                    },
                    'relactionofficer': {
                        required: 'Please select Action Officer'
                    },
                    'relactionrequire': {
                        required: 'Please select Action Required'
                    },
                    'relpage': {
                        required: 'Number only'
                    }
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#update-form-des-fwd')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/doctoreceive-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
                            //alert(a);
			                $("#update-modal-des-fwd").modal("hide");
                            call_ajax(1);
                        }
                    });
                }

            });

            $('#update-form-des-ret').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'reloffice': {
                        required: true
                    },
                    'relactionofficer': {
                        required: true
                    },
                    'relactionrequire': {
                        required: true
                    },
                    'relpage': {
                        number: true
                    }
                },
                messages: {
                    'reloffice': {
                        required: 'Please select Office Destination'
                    },
                    'relactionofficer': {
                        required: 'Please select Action Officer'
                    },
                    'relactionrequire': {
                        required: 'Please select Action Required'
                    },
                    'relpage': {
                        required: 'Number only'
                    }
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#update-form-des-ret')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/doctoreceive-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
                            //alert(a);
			                $("#update-modal-des-ret").modal("hide");
                            call_ajax(1);
                        }
                    });
                }

            });

            $('#add-form-act-off').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'actoff-empid': {
                        required: true,
                        remote: {
                            url: "server/checkUser.php",
                            type: "post"
                        }
                    },
                    'actoff-lastn': {
                        required: true
                    },
                    'actoff-firstn': {
                        required: true
                    },
                    'actoff-middlen': {
                        required: true
                    },
                    'actoff-office': {
                        required: true
                    },
                    'actoff-rep': {
                        required: true
                    },
                    'actoff-email': {
                        required: true,
                        email: true
                    },
                    'actoff-pass': {
                        required: true,
                        minlength: 4
                    },
                    'actoff-confirm-pass': {
                        required: true,
                        equalTo: '#actoff-pass'
                    },
                    'actoff-level': {
                        required: true
                    }
                },
                messages: {
                    'actoff-empid': {
                        required: 'Please enter Employee ID',
                        remote: "User already exists!"
                    },
                    'actoff-lastn': {
                        required: 'Please enter Lastname!'
                    },
                    'actoff-firstn': {
                        required: 'Please enter Firstname!'
                    },
                    'actoff-middlen': {
                        required: 'Please enter Middlename!'
                    },
                    'actoff-office': {
                        required: 'Please select Office!'
                    },
                    'actoff-rep': {
                        required: 'Please select one!'
                    },
                    'actoff-email': {
                        required: 'Please enter e-mail!',
                        email: 'Please enter a valid email!'
                    },
                    'actoff-pass': {
                        required: 'Please enter password!',
                        minlength: 'Minimum of 4 Characters!'
                    },
                    'actoff-confirm-pass': {
                        required: 'Please confirm password!',
                        equalTo: 'Must be the same with the password!'
                    },
                    'actoff-level': {
                        required: 'Please select user level!'
                    }
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#add-form-act-off')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/actionofficer-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
                            //alert(a);
                            call_ajax(1);
                            call_ajax(2);
                        }
                    });
                }

            });

            $('#edit-form-act-off').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'actoff-empid': {
                        required: true
                    },
                    'actoff-lastn': {
                        required: true
                    },
                    'actoff-firstn': {
                        required: true
                    },
                    'actoff-middlen': {
                        required: true
                    },
                    'actoff-office': {
                        required: true
                    },
                    'actoff-rep': {
                        required: true
                    },
                    'actoff-email': {
                        required: true,
                        email: true
                    },
                    'actoff-level': {
                        required: true
                    }
                },
                messages: {
                    'actoff-empid': {
                        required: 'Please enter Employee ID'
                    },
                    'actoff-lastn': {
                        required: 'Please enter Lastname!'
                    },
                    'actoff-firstn': {
                        required: 'Please enter Firstname!'
                    },
                    'actoff-middlen': {
                        required: 'Please enter Middlename!'
                    },
                    'actoff-office': {
                        required: 'Please select Office!'
                    },
                    'actoff-rep': {
                        required: 'Please select one!'
                    },
                    'actoff-email': {
                        required: 'Please enter e-mail!',
                        email: 'Please enter a valid email!'
                    },
                    'actoff-level': {
                        required: 'Please select user level!'
                    }
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#edit-form-act-off')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/actionofficer-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
                            //alert(a);
                            call_ajax(1);
                            call_ajax(2);
                        }
                    });
                }

            });

            $('#add-form-doc-type').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'docType': {
                        required: true,
                        remote: {
                            url: "server/checkDocType.php",
                            type: "post"
                        }
                    }
                },
                messages: {
                    'docType': {
                        required: 'Please enter Document Type',
                        remote: "Document type already exists!"
                    }
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#add-form-doc-type')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/documenttype-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
                            //alert(a);
                            call_ajax(1);
                            call_ajax(2);
                        }
                    });
                }

            });


            $('#edit-form-doc-type').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'docType': {
                        required: true,
                        remote: {
                            url: "server/checkDocTypeEdit.php",
                            type: "post",
                            data: {
                                id: function(){
                                var cc = $('#docCode').val();
                                return cc;
                                }
                            }
                        }
                    }
                },
                messages: {
                    'docType': {
                        required: 'Please enter Document Type',
                        remote: "Document type already exists!"
                    }
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#edit-form-doc-type')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/documenttype-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
                            //alert(a);
                            call_ajax(1);
                            call_ajax(2);
                        }
                    });
                }

            });

            $('#add-form-act-req').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'actionReq': {
                        required: true,
                        remote: {
                            url: "server/checkActReq.php",
                            type: "post"
                        }
                    },'actionTaken': {
                        required: true,
                        remote: {
                            url: "server/checkActReqTaken.php",
                            type: "post"
                        }
                    }
                },
                messages: {
                    'actionReq': {
                        required: 'Please enter Action Required',
                        remote: "Action Required already exists!"
                    },'actionTaken': {
                        required: 'Please enter Action Taken',
                        remote: "Action Taken already exists!"
                    }
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#add-form-act-req')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/actionrequired-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
                            //alert(a);
                            call_ajax(1);
                            call_ajax(2);
                        }
                    });
                }

            });

            $('#add-form-act-take').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'actionTaken': {
                        required: true,
                        remote: {
                            url: "server/checkActReqTaken.php",
                            type: "post"
                        }
                    }
                },
                messages: {
                    'actionTaken': {
                        required: 'Please enter Action Taken',
                        remote: "Action Taken already exists!"
                    }
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#add-form-act-take')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/actiontaken-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
                            //alert(a);
                            call_ajax(1);
                            call_ajax(2);
                        }
                    });
                }

            });

            $('#edit-form-req-act').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'actionReq': {
                        required: true,
                        remote: {
                            url: "server/checkActReqEdit.php",
                            type: "post",
                            data: {
                                id: function(){
                                var cc = $('#reqActCode').val();
                                return cc;
                                }
                            }
                        }
                    },
                    'actionTaken': {
                        required: true,
                        remote: {
                            url: "server/checkActReqTakenEdit.php",
                            type: "post",
                            data: {
                                id: function(){
                                var cc = $('#actTakeCode').val();
                                return cc;
                                }
                            }
                        }
                    }
                },
                messages: {
                    'actionReq': {
                        required: 'Please enter Document Type',
                        remote: "Action Required already exists!"
                    },
                    'actionTaken': {
                        required: 'Please enter Document Type',
                        remote: "Action Taken already exists!"
                    }
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#edit-form-req-act')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/actionrequired-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
                            //alert(a);
                            call_ajax(1);
                            call_ajax(2);
                        }
                    });
                }

            });

            $('#edit-form-take-act').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    
                    'actionTaken': {
                        required: true,
                        remote: {
                            url: "server/checkActReqTakenEdit.php",
                            type: "post",
                            data: {
                                id: function(){
                                var cc = $('#actTakeCode').val();
                                return cc;
                                }
                            }
                        }
                    }
                },
                messages: {
                    
                    'actionTaken': {
                        required: 'Please enter Document Type',
                        remote: "Action Taken already exists!"
                    }
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#edit-form-take-act')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/actiontaken-server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Success!");
                            //alert(a);
                            call_ajax(1);
                            call_ajax(2);
                        }
                    });
                }

            });

            $('#scan-code').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'bcode': {
                        required: true,
                        remote: {
                            url: "server/checkRouteCode.php",
                            type: "post",
                            data: {
                                id: function(){
                                var cc = $('#bcode').val();
                                return cc;
                                }
                            }
                        }

                    }
                },
                messages: {
                    'bcode': {
                        required: 'Please enter Route No.',
                        remote: 'Document not existing or document already received!!'
                    }
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#scan-code')[0];
                   var formData = new FormData(forms);
                   var bc = $('#bcode').val();
                   $('#update-modal-des').modal('show');
                   btnaction(bc,'A','2');
                }

            });

            $('#update-pw').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'currpw': {
                        required: true,
                        remote: {
                            url: "server/checkpw.php",
                            type: "post"
                        }
                    },
                    'newpw': {
                        required: true,
                        minlength: 4
                    },
                    'connewpw': {
                        required: true,
                        equalTo: '#newpw'
                    }
                },
                messages: {
                    'currpw': {
                        required: 'Please enter current Password',
                        remote: "Incorrect password!"
                    },
                    'newpw': {
                        required: 'Please enter password!',
                        minlength: 'Minimum of 4 Characters!'
                    },
                    'connewpw': {
                        required: 'Please confirm password!',
                        equalTo: 'Must be the same with the password!'
                    }
                },

                submitHandler: function(form, event) {
                   event.preventDefault();
                   var forms = $('#update-pw')[0];
                   var formData = new FormData(forms);


                   $.ajax({
                        url:"server/changepw_server.php",
                        type:"post",
                        enctype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success:function(a){
                            alert("Successfully Changed Password!");
                            //alert(a);
                            //call_ajax(1);
                            window.location.replace("logout.php");
                        }
                    });
                }

            });
        }
    };
}();