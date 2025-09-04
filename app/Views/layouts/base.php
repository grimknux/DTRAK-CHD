<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">

        <title>CHD - I Document Tracking System</title>

        <meta name="description" content="Document Tracking System of the Center for Health Development Ilocos">

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        
        <meta name="csrf-token" content="<?= csrf_hash() ?>">

        <!-- Icons -->
        <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
        <link rel="shortcut icon" href="<?= base_url(); ?>img/favicon.png">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>img/icon57.png" sizes="57x57">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>img/icon72.png" sizes="72x72">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>img/icon76.png" sizes="76x76">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>img/icon114.png" sizes="114x114">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>img/icon120.png" sizes="120x120">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>img/icon144.png" sizes="144x144">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>img/icon152.png" sizes="152x152">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>img/icon180.png" sizes="180x180">
        <!-- END Icons -->

        <!-- Stylesheets -->
        <!-- Bootstrap is included in its original form, unaltered -->
        <link rel="stylesheet" href="<?= base_url(); ?>css/appui/bootstrap.min.css">

        <!-- Related styles of various icon packs and plugins -->
        <link rel="stylesheet" href="<?= base_url(); ?>css/appui/plugins.css">

        <!-- The main stylesheet of this template. All Bootstrap overwrites are defined in here -->
        <link rel="stylesheet" href="<?= base_url(); ?>css/appui/main.css">
        <link rel="stylesheet" href="<?= base_url(); ?>css/appui/style.css">
        <link rel="stylesheet" href="<?= base_url(); ?>css/appui/buttons.dataTables.min.css">

        <!-- Include a specific file here from css/themes/ folder to alter the default theme of the template -->

        <!-- The themes stylesheet of this template (for using specific theme color in individual elements - must included last) -->
        <link rel="stylesheet" href="<?= base_url(); ?>css/appui/themes.css">

        <style>
            .modal-dialog{
                overflow-y: initial !important
            }
            td { font-size: 10px; }

            .error-box {
                background-color: #f8d7da;
                border: 1px solid #f5c6cb;
                color: #721c24;
                padding: 5;
                border-radius: 5px;
                text-align: center;
                margin-bottom: 10px;
                vertical-align: middle;
            }

            .form-error {
                font-weight: bold;
                font-style: italic;
            }

            #overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black background */
                display: none; /* Hidden by default */
                z-index: 9999; /* Make sure it appears above other elements */
            }

            .loader {
                border: 4px solid #f3f3f3; /* Light grey border for the spinner */
                border-top: 4px solid #3498db; /* Blue border for the spinner */
                border-radius: 50%;
                width: 50px;
                height: 50px;
                animation: spin 2s linear infinite;
                position: absolute;
                top: 50%;
                left: 50%;
                margin-top: -25px; /* Center the spinner vertically */
                margin-left: -25px; /* Center the spinner horizontally */
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }


            .dataTables_processing {
                position: absolute;
                top: 0 !important;
                left: 0 !important;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.3); /* dark semi-transparent overlay */
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 999;
                font-size: 18px;
                font-weight: bold;
                pointer-events: none; /* so it doesn't block buttons etc. */
            }



            #workshop-table tbody {
                font-size: 11px;  /* Adjust the font size as needed */
            }


            table input {
                padding: 1px;
                border: 1px solid #cccc;
                border-radius: 5px;
            }

            .widget-light{
                background-color: #dce8f2;
                
            }

            .required-label {
                color: red; /* Make the asterisk red */
            }

            .required-label::after {
                content: ' (*)'; /* Add an asterisk after the label text */
                color: red; /* Make the asterisk red */
                font-weight: bold; /* Optional: Make the asterisk bold */
            }

            .modal-xl {
                max-width: 90%; /* Adjust as needed */
            }

            .grey-link {
                color: #737574;
            }

            .has-error .chosen-container {

                border: 1px solid #de815c; /* or any error indication style */
                border-radius: 5px;

            }
            
            .has-success .chosen-container {

                border: 1px solid #afde5c; /* or any error indication style */
                border-radius: 5px;

            }

            .help-block {
                margin-bottom: -10px;
                margin-top: -1px;
            }

            td .help-block,
            tr .help-block {
                margin-bottom: 0; /* or any value you prefer */
                margin-top: 0; /* or any value you prefer */
            }

            th, td {
                word-wrap: break-word; /* Enables breaking long words */
                overflow-wrap: break-word; /* Newer standard for word wrapping */
                white-space: normal; /* Allows text to wrap */
            }

            .iframe-container {
                height: 100%;
            }

            .large-checkbox {
                transform: scale(1.5); /* Scale size */
                margin: 10px;
            }

            .label-require::after {
                content: " *"; /* Adds an asterisk with a space before it */
                color: rgb(231, 87, 87); /* Same color as the text */
            }

            /* Error: single and multiple */
            .has-error .select2-container--default .select2-selection--single,
            .has-error .select2-container--default .select2-selection--multiple {
                border: 1px solid #de815c !important; /* Red border */
            }

            /* Success: single and multiple */
            .has-success .select2-container--default .select2-selection--single,
            .has-success .select2-container--default .select2-selection--multiple {
                border: 1px solid #afde5c !important; /* Green border */
            }

            </style>
        <!-- END Stylesheets -->

        <!-- Modernizr (browser feature detection library) -->
        <script src="<?= base_url(); ?>js/appui/vendor/modernizr-3.3.1.min.js"></script>
    </head>

    <body>

        <div id="page-wrapper" class="page-loading">
            <div class="preloader">
                <div class="inner">
                    <div class="preloader-spinner themed-background hidden-lt-ie10"></div>
                    <h3 class="text-primary visible-lt-ie10"><strong>Loading..</strong></h3>
                </div>
            </div>
            <div id="page-container" class="header-fixed-top sidebar-visible-lg-full">

                <!-- SIDEBAR SECTION -->

                <?= view('layouts/sidebar') ?>
                
                <!-- END SIDEBAR SECTION -->

                <div id="main-container">
                    <!-- NAVIGATION BAR SECTION -->
                    <header class="navbar navbar-inverse navbar-fixed-top">
                        <ul class="nav navbar-nav-custom">
                            <li>
                                <a href="javascript:void(0)" onclick="App.sidebar('toggle-sidebar');this.blur();">
                                    <i class="fa fa-ellipsis-v fa-fw animation-fadeInRight" id="sidebar-toggle-mini"></i>
                                    <i class="fa fa-bars fa-fw animation-fadeInRight" id="sidebar-toggle-full"></i>
                                </a>
                            </li>
                            <!-- END Main Sidebar Toggle Button -->

                            <!-- Header Link -->
                            <li class="hidden-xs animation-fadeInQuick">
                                <a><strong style="font-size: 15px">Deparment of Health CHD - I Document Tracking System</strong></a>
                            </li>
                            <!-- END Header Link -->
                        </ul>
                        <!-- END Left Header Navigation -->

                        <!-- Right Header Navigation -->
                        <?= view('layouts/user_navi') ?>
                        <!-- END Right Header Navigation -->
                         
                    </header>
                    <!-- END NAVIGATION BAR SECTION -->
                    
                    <div id="page-content">

                        <div class="content-header">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="header-section">
                                        <h1><?= $header ?></h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <?= $bread ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- PAGE CONTENT HERE -->
                        <?= $this->renderSection("content"); ?>    

                        
                    </div>
                </div>
            </div>
        </div>


        

        <script src="<?= base_url(); ?>js/appui/jquery-3.7.1.min.js"></script>
        <script src="<?= base_url(); ?>js/appui/app.js"></script>
        <script src="<?= base_url(); ?>js/appui/vendor/bootstrap.min.js"></script>
        <script src="<?= base_url(); ?>js/appui/plugins.js"></script>
        <script src="<?= base_url(); ?>js/appui/pages/uiTables.js"></script>
        <script src="<?= base_url(); ?>js/appui/pages/formsComponents.js"></script>
        <script src="<?= base_url(); ?>js/appui/pages/appSocial.js"></script>
        <script src="<?= base_url(); ?>js/appui/pages/compGallery.js"></script>
        <script src="<?= base_url(); ?>js/appui/dataTables.buttons.min.js"></script>
        <script src="<?= base_url(); ?>js/appui/buttons.print.min.js"></script>
        <script src="<?= base_url(); ?>js/appui/sweetalert2.all.min.js"></script>
        <script src="<?= base_url(); ?>js/pages/handleValidationErrors.js"></script>
        <script src="<?= base_url(); ?>js/pages/login.Changepassword.js"></script>
        <script>
            var base_url = "<?php echo base_url(); ?>";
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            $(document).ready(function(){   
                changePassword.init();
            });
            function printTable(table) {
                var tableContent = document.querySelector(table).outerHTML;

                // Center the popup
                var width = 1000;
                var height = 700;

                var left = window.screenX + (window.outerWidth - width) / 2;
                var top = window.screenY + (window.outerHeight - height) / 2;

                var printWindow = window.open('', '', `width=${width},height=${height},top=${top},left=${left}`);

                printWindow.document.write('<html><head><title>Acted-upon Documents</title>');
                printWindow.document.write('<style>');
                printWindow.document.write('body { font-family: Arial, sans-serif; font-size: 12px; }');
                printWindow.document.write('table { width: 100%; border-collapse: collapse; font-size: 12px; }');
                printWindow.document.write('th { font-size: 13px; font-weight: bold; }');
                printWindow.document.write('td, th { border: 1px solid black; padding: 8px; }');
                printWindow.document.write('td:nth-child(1), th:nth-child(1) { text-align: center; }');
                printWindow.document.write('td:nth-child(2), th:nth-child(2) { text-align: center; }');
                printWindow.document.write('td:nth-child(3), th:nth-child(3) { text-align: center; }');
                printWindow.document.write('td:nth-child(4), th:nth-child(4) { text-align: center; }');
                printWindow.document.write('td:nth-child(7), th:nth-child(7) { text-align: center; }');
                printWindow.document.write('td:nth-child(8), th:nth-child(8) { text-align: center; }');
                printWindow.document.write('td:nth-child(9), th:nth-child(9) { text-align: center; }');
                printWindow.document.write('td:nth-child(10), th:nth-child(10) { text-align: center; }');
                printWindow.document.write('td:nth-child(11), th:nth-child(11) { text-align: center; }');
                printWindow.document.write('</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write(tableContent);
                printWindow.document.write('</body></html>');

                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }
        </script>

        <?= $this->renderSection("script"); ?>


    </body>
</html>
