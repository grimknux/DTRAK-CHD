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
        <link rel="stylesheet" href="<?= base_url(); ?>css/appui/buttons.dataTables.min.css">

        <!-- Include a specific file here from css/themes/ folder to alter the default theme of the template -->

        <!-- The themes stylesheet of this template (for using specific theme color in individual elements - must included last) -->
        <link rel="stylesheet" href="<?= base_url(); ?>css/appui/themes.css">

        <style <?= csp_style_nonce() ?>>
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
                                <a><strong style="font-size: 15px">Department of Health - Ilocos Center For Health Development</strong></a>
                            </li>
                            <!-- END Header Link -->
                        </ul>
                        <!-- END Left Header Navigation -->

                        <!-- Right Header Navigation -->
                        <ul class="nav navbar-nav-custom pull-right">

                            <!-- User Dropdown -->
                            <li class="dropdown">
                                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="<?= base_url('img/placeholders/avatars/avatar9.jpg'); ?>" alt="avatar">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-header">
                                        Welcome!
                                    </li>
                                    <li>
                                        <a href="changepw.php" title="Change Password" data-placement="left">
                                            <i class="fa fa-inbox fa-fw pull-right"></i>
                                            Change Password
                                        </a>
                                    </li>
                                    <li>
                                        <a href="changelogs.php" title="Change Password" data-placement="left">
                                            <i class="fa fa-inbox fa-fw pull-right"></i>
                                            Changelogs
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?= base_url('logout'); ?>" id="btnlogout" method="post" data-toggle="tooltip" title="Change Password" onclick="cpass()" data-placement="left" title='Logout'>
                                            <i class="fa fa-power-off fa-fw pull-right"></i>
                                            Log out
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </header>
                    <!-- END NAVIGATION BAR SECTION -->
                    
                    <div id="page-content" class="inner-sidebar-left">
                        <!-- Inner Sidebar -->
                        <div id="page-content-sidebar">
                            <!-- Compose Message (Modal markup is at the bottom of this page before including JS scripts) -->
                            <div class="block-section text-center">
                                <h4><i class="fa fa-download"></i> <?= $header ?></h4>
                            </div>
                            <!-- END Compose Message -->

                            <!-- Collapsible Navigation -->
                            <a href="javascript:void(0)" class="btn btn-block btn-effect-ripple btn-default visible-xs" data-toggle="collapse" data-target="#email-nav">Click Here to view actions</a>
                            <div id="email-nav" class="collapse navbar-collapse remove-padding">
                                <!-- Menu -->
                                <div class="block-section">
                                    <ul class="nav nav-pills nav-stacked">
                                        <li class="<?= ($rnav === 'receive') ? 'active' : '' ?>">
                                            <a href="<?= base_url('doctoreceive/receive') ?>">
                                                <i class="fa fa-fw fa-inbox icon-push"></i> <strong>Incoming/Inbox</strong>
                                            </a>
                                        </li>
                                        <li class="<?= ($rnav === 'action') ? 'active' : '' ?>">
                                            <a href="<?= base_url('doctoreceive/action') ?>">
                                                <i class="fa fa-fw fa-star icon-push"></i> <strong>For Action</strong>
                                            </a>
                                        </li>
                                        <li class="<?= ($rnav === 'release') ? 'active' : '' ?>">
                                            <a href="<?= base_url('doctoreceive/release') ?>">
                                                <i class="fa fa-share icon-push"></i> <strong>For Release</strong>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <!-- END Menu -->

                                <!-- Labels -->
                                <div class="block-section">
                                    <h4 class="inner-sidebar-header">
                                        <a href="javascript:void(0)" class="btn btn-effect-ripple btn-xs btn-default pull-right"><i class="fa fa-plus"></i></a>
                                        Miscellaneous
                                    </h4>
                                    <ul class="nav nav-pills nav-stacked nav-icons">
                                        <li class="<?= ($rnav === 'released') ? 'active' : '' ?>">
                                            <a href="<?= base_url('miscellaneous/released') ?>">
                                                <i class="fa fa-fw fa-circle icon-push text-info"></i> <strong>Released Documents</strong>
                                            </a>
                                        </li>
                                        <li class="<?= ($rnav === 'undone') ? 'active' : '' ?>">
                                        <a href="<?= base_url('miscellaneous/undonedocs') ?>">
                                                <i class="fa fa-fw fa-circle icon-push text-warning"></i> <strong>Undone Document</strong>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <!-- END Labels -->
                            </div>
                            <!-- END Collapsible Navigation -->
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
        <script <?= csp_script_nonce() ?>>
            var base_url = "<?php echo base_url(); ?>";
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

        </script>

        <?= $this->renderSection("script"); ?>


    </body>
</html>
