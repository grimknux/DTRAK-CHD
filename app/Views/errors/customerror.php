<!-- app/Views/errors/custom_error.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CHD - I Document Tracking System - <?= esc($error ?? 'Unexpected Error') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link rel="stylesheet" href="<?= base_url(); ?>public/css/appui/style.css">
    <link rel="stylesheet" href="<?= base_url(); ?>public/css/appui/buttons.dataTables.min.css">

    <!-- Include a specific file here from css/themes/ folder to alter the default theme of the template -->

    <!-- The themes stylesheet of this template (for using specific theme color in individual elements - must included last) -->
    <link rel="stylesheet" href="<?= base_url(); ?>public/css/appui/themes.css">
</head>
<body>
    <!--<img src="</?= base_url(); ?>public/images/layout/mg.jpg" alt="Full Background" class="full-bg full-bg-bottom animation-pulseSlow">-->
        <!-- END Full Background -->

        <!-- Error Container -->
        <div id="error-container">
            <div class="row text-center">
                <div class="col-md-6 col-md-offset-3">
                    <h1 class="text-light animation-fadeInQuick"><strong>ERROR 404</strong></h1>
                    <hr>
                    <h2 class="text-muted animation-fadeInQuickInv"><em>We sorry but this page can't be found..</em></h2>
                </div>
                <div class="col-md-4 col-md-offset-4">
                    <a href="<?= base_url(); ?>" class="btn btn-effect-ripple btn-default"><i class="fa fa-arrow-left"></i> Go back</a>
                </div>
            </div>
        </div>

    <!-- AppUI Core JS (adjust path if needed) -->
    <script src="<?= base_url(); ?>public/js/appui/jquery3.1.min.js"></script>
    <script src="<?= base_url(); ?>public/js/appui/app.js"></script>
    <script src="<?= base_url(); ?>public/js/appui/vendor/bootstrap.min.js"></script>
    <script src="<?= base_url(); ?>public/js/appui/plugins.js"></script>
    <script src="<?= base_url(); ?>public/js/appui/pages/uiTables.js"></script>
</body>
</html>
