<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">

        <title>CHD - I Document Tracking System</title>

        <meta name="description" content="Document Tracking System of the Center for Health Development Ilocos">

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <!-- Icons -->
        <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
        <link rel="shortcut icon" href="<?= base_url(); ?>public/img/favicon.png">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>public/img/icon57.png" sizes="57x57">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>public/img/icon72.png" sizes="72x72">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>public/img/icon76.png" sizes="76x76">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>public/img/icon114.png" sizes="114x114">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>public/img/icon120.png" sizes="120x120">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>public/img/icon144.png" sizes="144x144">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>public/img/icon152.png" sizes="152x152">
        <link rel="apple-touch-icon" href="<?= base_url(); ?>public/img/icon180.png" sizes="180x180">
        <!-- END Icons -->

        <!-- Stylesheets -->
        <!-- Bootstrap is included in its original form, unaltered -->
        <link rel="stylesheet" href="<?= base_url(); ?>public/css/appui/bootstrap.min.css">

        <!-- Related styles of various icon packs and plugins -->
        <link rel="stylesheet" href="<?= base_url(); ?>public/css/appui/plugins.css">

        <!-- The main stylesheet of this template. All Bootstrap overwrites are defined in here -->
        <link rel="stylesheet" href="<?= base_url(); ?>public/css/appui/main.css">
        <link rel="stylesheet" href="<?= base_url(); ?>public/css/appui/buttons.dataTables.min.css">

        <!-- Include a specific file here from css/themes/ folder to alter the default theme of the template -->

        <!-- The themes stylesheet of this template (for using specific theme color in individual elements - must included last) -->
        <link rel="stylesheet" href="<?= base_url(); ?>public/css/appui/themes.css">

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

        </style>
        <!-- END Stylesheets -->

        <!-- Modernizr (browser feature detection library) -->
        <script src="<?= base_url(); ?>public/js/appui/vendor/modernizr-3.3.1.min.js"></script>
    </head>

    <body>
        <img src="<?= base_url(); ?>public/img/background.png" alt="Full Background" class="full-bg animation-pulseSlow">    


        <?= $this->renderSection("content"); ?> 

        

        <script src="<?= base_url(); ?>public/js/appui/jquery3.1.min.js"></script>
        <script src="<?= base_url(); ?>public/js/appui/vendor/bootstrap.min.js"></script>
        <script src="<?= base_url(); ?>public/js/appui/plugins.js"></script>
        <script src="<?= base_url(); ?>public/js/appui/app.js"></script>
        <script src="<?= base_url(); ?>public/js/appui/pages/uiTables.js"></script>
        <!--<script src="<.?= base_url(); ?>public/js/appui/pages/formsValidation.js"></script>-->
        <script src="<?= base_url(); ?>public/js/appui/pages/formsComponents.js"></script>
        <script src="<?= base_url(); ?>public/js/appui/pages/uiUsers.js"></script>
        <script src="<?= base_url(); ?>public/js/appui/pages/appSocial.js"></script>
        <script src="<?= base_url(); ?>public/js/appui/pages/compGallery.js"></script>
        <script src="<?= base_url(); ?>public/js/appui/dataTables.buttons.min.js"></script>
        <script src="<?= base_url(); ?>public/js/appui/buttons.print.min.js"></script>


        <?= $this->renderSection("script"); ?>

    </body>
</html>
