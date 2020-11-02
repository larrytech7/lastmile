<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * @date 15/08/2020
 * Contributors : 
 */
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?= $this->lang->line('title') . ' - ' . $this->session->user->role_name ?></title>
	<link rel="icon" href="<?= base_url('resources/dist/img/icon.png') ?>">
	<!-- Tell the browser to be responsive to screen width -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?= base_url('resources/') ?>plugins/fontawesome-free/css/all.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
	<!-- DataTables -->
	<link rel="stylesheet" href="<?= base_url('resources/') ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" href="<?= base_url('resources/') ?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
	<!-- iCheck -->
	<link rel="stylesheet" href="<?= base_url('resources/') ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
	<!-- Select2 -->
	<link rel="stylesheet" href="<?= base_url('resources/') ?>plugins/select2/css/select2.min.css">
	<link rel="stylesheet" href="<?= base_url('resources/') ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
	<!-- SweetAlert2 -->
	<link rel="stylesheet" href="<?= base_url('resources/') ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
	<!-- Toastr -->
	<link rel="stylesheet" href="<?= base_url('resources/') ?>plugins/toastr/toastr.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="<?= base_url('resources/') ?>dist/css/adminlte.min.css">
	<!-- overlayScrollbars -->
	<link rel="stylesheet" href="<?= base_url('resources/') ?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
	<!-- Daterange picker -->
	<link rel="stylesheet" href="<?= base_url('resources/') ?>plugins/daterangepicker/daterangepicker.css">
	<!-- summernote -->
	<link rel="stylesheet" href="<?= base_url('resources/') ?>plugins/summernote/summernote-bs4.css">
	<!-- fullCalendar -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />


	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<body class="hold-transition sidebar-mini layout-boxed">
	<div class="wrapper">

		<!-- Navbar -->
		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
			<!-- Left navbar links -->
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
				</li>
			</ul>

			<!-- Right navbar links -->
			<ul class="navbar-nav ml-auto">
				<!-- Notifications Dropdown Menu -->
				<?php if ($this->session->user->role_name == 'ADMIN') : ?>
					<li class="nav-item">
						<a class="nav-link" href="<?= site_url('dashboard/settings') ?>" title="<?= $this->lang->line('config') ?>">
							<i class="fa fa-cog"></i>
						</a>
					</li>
				<?php endif; ?>
				<li class="nav-item">
					<a class="nav-link" href="<?= site_url('dashboard/logout') ?>" title="<?= $this->lang->line('sign_out') ?>">
						<i class="fa fa-sign-out-alt"></i>
					</a>
				</li>

			</ul>
		</nav>
		<!-- /.navbar -->

		<!-- Main Sidebar Container -->
		<aside class="main-sidebar sidebar-dark-primary elevation-4">
			<!-- Brand Logo -->
			<a href="<?= site_url('dashboard') ?>" class="brand-link">
				<img src="<?= base_url('resources/dist/img/icon.png') ?>" alt="Cafe Revendeur Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
				<span class="brand-text font-weight-light"><?= $this->lang->line('title') ?></span>
			</a>

			<!-- Sidebar -->
			<div class="sidebar">
				<!-- Sidebar user panel (optional) -->
				<div class="user-panel mt-3 pb-3 mb-3 d-flex">
					<div class="image">
						<img src="<?= base_url('resources/dist/img/avatar.png') ?>" class="img-circle elevation-2" alt="User Image">
					</div>
					<div class="info">
						<a href="<?= site_url('dashboard') ?>" class="d-block"><?= $this->session->user->username ?? '' ?></a>
					</div>
				</div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
         with font-awesome or any other icon font library -->
         <li class="nav-item">
            <a href="<?= site_url('dashboard/agenda')?>" class="nav-link <?= $this->session->flashdata('agenda_menu')?>">
              <i class="nav-icon fas fa-calendar"></i>
              <p>
                <?= $this->lang->line('agenda') ?>
              </p>
            </a>
        </li>
		<?php if($this->session->user->role_name == 'ADMIN'): ?>
          <li class="nav-item">
            <a href="<?= site_url('dashboard/franchises')?>" class="nav-link <?= $this->session->flashdata('franchise_menu')?>">
              <i class="nav-icon fas fa-user-tie"></i>
              <p>
                <?= $this->lang->line('franchises') ?>
              </p>
            </a>
      </li>
      <li class="nav-item has-treeview">
            <a href="<?= site_url('dashboard/users')?>" class="nav-link <?= $this->session->flashdata('user_menu')?>">
			  <i class="nav-icon fas fa-user-friends"></i>
              <p>
			  <?= $this->lang->line('users') ?>
              </p>
            </a>
		  </li>
		<?php endif; ?>
          <li class="nav-item">
            <a href="<?= site_url('dashboard/clients')?>" class="nav-link <?= $this->session->flashdata('client_menu')?>">
              <i class="nav-icon fas fa-users"></i>
              <p>
			  <?= $this->lang->line('clients') ?>
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="<?= site_url('dashboard/campaigns')?>" class="nav-link <?= $this->session->flashdata('campaign_menu')?>">
              <i class="nav-icon fas fa-elementor"></i>
              <p>
			  <?= $this->lang->line('campaigns') ?>
              </p>
            </a>
		  </li>
		  <li class="nav-item has-treeview">
            <a href="<?= site_url('dashboard/commissions')?>" class="nav-link <?= $this->session->flashdata('commission_menu')?>">
              <i class="nav-icon fas fa-coins"></i>
              <p>
			  <?= $this->lang->line('commissions') ?>
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="<?= site_url('dashboard/rdvs')?>" class="nav-link <?= $this->session->flashdata('rdv_menu')?>">
              <i class="nav-icon fas fa-handshake"></i>
              <p>
			  <?= $this->lang->line('rdv') ?>
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="<?= site_url('dashboard/messages')?>" class="nav-link <?= $this->session->flashdata('message_menu')?>">
              <i class="nav-icon fas fa-comments"></i>
              <p>
			  <?= $this->lang->line('messages') ?>
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="<?= site_url('dashboard/documents')?>" class="nav-link <?= $this->session->flashdata('document_menu')?>">
              <i class="nav-icon fas fa-folder-open"></i>
              <p>
                <?= $this->lang->line('documents') ?>
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
