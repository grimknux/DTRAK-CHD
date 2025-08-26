<!doctype html>
<html lang="en">
	<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title>CHD ILOCOS - ID Generator System</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/dashboard/">

    <link rel="icon" href="<?php echo base_url(); ?>favicon/favicon.ico" type="image/gif">

    <!-- Bootstrap core CSS -->
	<link href="<?= base_url(); ?>css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap DataTable Core CSS -->
	<link href="<?= base_url(); ?>css/jquery.dataTables.min.css" rel="stylesheet">
	
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css">
	<link rel="stylesheet" href="<?= base_url('css/cropper/cropper.css'); ?>">
	<link rel="stylesheet" href="<?= base_url('css/tag/tagify.css'); ?>">
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
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

		.image-container {
			position: relative;
			max-width: 100%;
		}

		#image {
			max-width: 100%;
			height: auto;
		}

		.controls {
			margin-top: 10px;
		}

		.cropper-container {
			overflow: visible !important;
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
    </style>

    
		<!-- Custom styles for this template -->
	<link href="<?= base_url(); ?>css/style.css" rel="stylesheet">

	</head>
	<body>
    
	<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
		<a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">CHD-ILOCOS</a>
		<button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<!--<input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">-->
		<div class="navbar-nav">
			<div class="nav-item text-nowrap">
			  <a href="<?php echo base_url('logoutnow');?>" class="nav-link px-3" href="#">Sign out</a>
			</div>
		</div>
	</header>

	<div class="container-fluid">
		<div class="row">
			<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
				<div class="position-sticky pt-3">
					<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
						<span>ID Generation</span>
							<a class="link-secondary" href="<?= base_url() ?>" aria-label="ID Generation">
								<i class="fa-regular fa-id-card"></i>
							</a>
					</h6>
					<ul class="nav flex-column">
						<li class="nav-item">
							<a class="nav-link <?= ($navactive === 'db') ? 'active' : '' ?>" aria-current="page" href="<?= base_url() ?>">
							  <span class="fa-solid fa-table-columns"></span> Dashboard
							</a>
						</li>
						<li class="nav-item">
						<a class="nav-link <?= ($navactive === 'bp') ? 'active' : '' ?>" href="<?= base_url("bulk-print") ?>">
							<span class="fa-solid fa-print"></span> Bulk Print
						</a>
						</li>
						<li class="nav-item ">
							<a class="nav-link <?= ($navactive === 'bu') ? 'active' : '' ?>" href="<?= base_url("bulk-upload") ?>">
								<span class="fa-solid fa-upload"></span> Bulk Upload
							</a>
						</li>
					  
					</ul>
					<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
						<span>Webex Link Creation</span>
							<a class="link-secondary" href="#" aria-label="Add a new report">
								<i class="fa-solid fa-video"></i>
							</a>
					</h6>
					<ul class="nav flex-column">
						<li class="nav-item">
							<a class="nav-link <?= ($navactive === 'webmeet') ? 'active' : '' ?>" aria-current="page" href="<?= base_url("webex-meeting") ?>">
							<span class="fa-solid fa-table-list"></span> Webex Meetings
							</a>
						</li>
						<li class="nav-item">
						<a class="nav-link <?= ($navactive === 'addmeet') ? 'active' : '' ?>" href="<?= base_url("add-webex-schedule") ?>">
						<span class="fa-regular fa-square-plus"></span> Schedule Meeting
						</a>
						</li>
					  
					</ul>
	  				<!--
					<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
						<span>Saved reports</span>
						<a class="link-secondary" href="#" aria-label="Add a new report">
							<span data-feather="plus-circle"></span>
						</a>
					</h6>
					
					<ul class="nav flex-column mb-2">
						<li class="nav-item">
							<a class="nav-link" href="#">
							<span data-feather="file-text"></span>Current month
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#">
							<span data-feather="file-text"></span>Last quarter
							</a>
						</li>
					</ul>

					-->
				</div>
			</nav>

            <?= $this->renderSection("content"); ?>

		</div>
	</div>
	
	
	
  </body>
</html>

    <!-- jQuery, Bootstrap, jQuery plugins and Custom JS code -->
    <script src="<?= base_url(); ?>js/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url(); ?>js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url(); ?>js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url(); ?>js/feather.min.js" crossorigin="anonymous"></script>
	<script src="<?= base_url(); ?>js/font-awesome.js" crossorigin="anonymous"></script>
	<script src="<?= base_url(); ?>js/style.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
	<script src="<?= base_url('js/cropper/cropper.js'); ?>"></script>
	<script src="<?= base_url('js/tag/tagify.min.js'); ?>"></script>

    <?= $this->renderSection("script"); ?>