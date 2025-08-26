<!doctype html>
<html lang="en">
	<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title>CHD ILOCOS - ID Generator System</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/dashboard/">

    <link rel="icon" href="<?php echo base_url(); ?>favicon/favicon.ico" type="image/gif">

    <!-- Bootstrap core CSS -->
	<link href="<?= base_url(); ?>css/bootstrap.min.css" rel="stylesheet">
	<style>
		body {
			background-color: #66666f;
		}
		.registration-container {
			max-width: 400px;
			margin: auto;
			margin-top: 50px;
			padding: 20px;
			background-color: #f0f0f0; /* Updated background color */
			border-radius: 10px;
			box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
		}
		.registration-container h2 {
			text-align: center;
			margin-bottom: 20px;
		}

		.error-box {
			background-color: #f8d7da;
			border: 1px solid #f5c6cb;
			color: #721c24;
			padding: 10px;
			border-radius: 5px;
			text-align: center;
			margin-bottom: 10px;
		}

		.success-box {
			background-color: #d7f8d9;
			border: 1px solid #c6f5cd;
			color: #1c7226;
			padding: 10px;
			border-radius: 5px;
			text-align: center;
			margin-bottom: 10px;
		}

		.error-form {
			color: #bc0000;
			text-align: left;
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
	</style>
	</head>
	<body>

        <?= $this->renderSection("content"); ?>

	
  </body>
</html>

    <!-- jQuery, Bootstrap, jQuery plugins and Custom JS code -->
    <script src="<?= base_url(); ?>js/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url(); ?>js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url(); ?>js/feather.min.js" crossorigin="anonymous"></script>
	<script src="<?= base_url(); ?>js/font-awesome.js" crossorigin="anonymous"></script>

    <?= $this->renderSection("script"); ?>