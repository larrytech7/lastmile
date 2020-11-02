<?php

/**
 * @author [zeufack]
 * @email [zeufackp@gmail.com]
 * @create date 2020-09-24 09:36:24
 * @modify date 2020-09-24 09:36:24
 * @desc [show client informations]
 * @contributor:[]
 */
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark"><?= $client['first_name'] ?></h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><?= $this->lang->line('home') ?></a></li>
						<li class="breadcrumb-item active">Dashboard</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->

	<!-- Main content -->
	<section class="content">
		<div class="container-fluid">
			<!-- Small boxes (Stat box) -->
			<div class="row">
				<div class="col-lg-3 col-6">
					<!-- small box -->
					<div class="small-box bg-info">
						<div class="inner">
							<h3><?= $statistics['franchises'] ?? 0 ?></h3>

							<p><?= $this->lang->line('franchises') ?></p>
						</div>
						<div class="icon">
							<i class="fa fa-client-tie"></i>
						</div>
						<a href="<?= site_url('dashboard/franchises') ?>" class="small-box-footer"><?= $this->lang->line('see_more') ?> <i class="fas fa-arrow-circle-right"></i></a>
					</div>
				</div>
				<!-- ./col -->
				<div class="col-lg-3 col-6">
					<!-- small box -->
					<div class="small-box bg-success">
						<div class="inner">
							<h3><?= $statistics['clients'] ?? 0 ?></h3>

							<p><?= $this->lang->line('clients') ?></p>
						</div>
						<div class="icon">
							<i class="fa fa-clients"></i>
						</div>
						<a href="<?= site_url('dashboard/clients') ?>" class="small-box-footer"><?= $this->lang->line('see_more') ?> <i class="fas fa-arrow-circle-right"></i></a>
					</div>
				</div>
				<!-- ./col -->
				<div class="col-lg-3 col-6">
					<!-- small box -->
					<div class="small-box bg-warning">
						<div class="inner">
							<h3><?= $statistics['rdvs'] ?? 0 ?></h3>

							<p><?= $this->lang->line('rdvs') ?></p>
						</div>
						<div class="icon">
							<i class="fa fa-handshake"></i>
						</div>
						<a href="<?= site_url('dashboard/rdvs') ?>" class="small-box-footer"><?= $this->lang->line('see_more') ?> <i class="fas fa-arrow-circle-right"></i></a>
					</div>
				</div>
				<!-- ./col -->
				<div class="col-lg-3 col-6">
					<!-- small box -->
					<div class="small-box bg-danger">
						<div class="inner">
							<h3><?= $statistics['campaigns'] ?? 0 ?></h3>

							<p><?= $this->lang->line('campaigns') ?></p>
						</div>
						<div class="icon">
							<i class="fa fa-elementor"></i>
						</div>
						<a href="<?= site_url('dashboard/campaigns') ?>" class="small-box-footer"><?= $this->lang->line('see_more') ?> <i class="fas fa-arrow-circle-right"></i></a>
					</div>
				</div>
				<!-- ./col -->
			</div>
			<!-- /.row -->
			<div class="row">
				<div class="col-md-3">

					<!-- Profile Image -->
					<div class="card card-primary card-outline">
						<div class="card-body box-profile">
							<div class="text-center">
								<img class="profile-client-img img-fluid img-circle" src="<?= base_url('resources/dist/img/avatar.png') ?>" alt="client profile picture">
							</div>

							<h3 class="profile-clientname text-center"><?= $client['first_name'] . '  ' . $client['last_name'] ?></h3>

							<p class="text-muted text-center"><?= $client['client_email'] ?></p>

							<ul class="list-group list-group-unbordered mb-3">
								<li class="list-group-item" title="<?= $this->lang->line('clientname') ?>">
									<i class="fas fa-client"></i>
									<b><?= $client['first_name'] . '  ' . $client['last_name'] ?></b> <a class="float-right"></a>
								</li>
								<li class="list-group-item" title="<?= $this->lang->line('phone_number') ?>">
									<i class="fas fa-phone"></i>
									<b><?= $client['phone_number'] ?></b> <a class="float-right"></a>
								</li>
								<li class="list-group-item" title="<?= $this->lang->line('address') ?>">
									<i class="fas fa-globe"></i>
									<b><?= $client['client_email'] ?></b> <a class="float-right"></a>
								</li>

								<li class="list-group-item " title="<?= $this->lang->line('create_date') ?>">
									<i class="fas fa-clock"></i>
									<b><?= $client['create_time'] ?></b> <a class="float-right"></a>
								</li>
								<li class="list-group-item " title="<?= $this->lang->line('last_update_date') ?>">
									<i class="fas fa-clock"></i>
									<b><?= $client['update_time'] ?></b> <a class="float-right"></a>
								</li>
							</ul>

						</div>
						<!-- /.card-body -->
					</div>
					<!-- /.card -->
				</div>
				<!-- /.col -->
				<div class="col-md-9">
					<div class="card">
						<div class="card-header p-2">
							<ul class="nav nav-pills">
								<li class="nav-item"><a class="nav-link active" href="#update" data-toggle="tab"><?= $this->lang->line('profile') ?></a></li>
							</ul>
						</div><!-- /.card-header -->
						<div class="card-body">
							<div class="tab-content">
								<div class="active tab-pane" id="update">
									<?= validation_errors() ?>
									<?= form_open(site_url('dashboard/client/edit/' . $client['client_id']), array('class' => 'form form-horizontal', 'role' => 'form')) ?>
									<div class="form-group row">
										<label for="last_name" class="col-sm-3 col-form-label"><?= $this->lang->line('last_name') ?></label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="last_name" value="<?= $client['last_name'] ?? ''  ?>" name="last_name" placeholder="<?= $this->lang->line('clientname') ?>">
										</div>
									</div>
									<div class="form-group row">
										<label for="first_name" class="col-sm-3 col-form-label"><?= $this->lang->line('first_name') ?></label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="first_name" name="first_name" value="<?= $client['first_name'] ?? '' ?>" placeholder="<?= $this->lang->line('name') ?>" required>
										</div>
									</div>

									<div class="form-group row">
										<label for="email" class="col-sm-3 col-form-label"><?= $this->lang->line('email') ?></label>
										<div class="col-sm-9">
											<?= form_error('email'); ?>
											<input type="email" class="form-control" id="email" name="email" value="<?= $client['client_email'] ?? ''  ?>" placeholder="<?= $this->lang->line('email') ?>" required>
										</div>
									</div>
									<div class="form-group row">
										<label for="client_phone" class="col-sm-3 col-form-label"><?= $this->lang->line('phone_number') ?></label>
										<div class="col-sm-9">
											<input type="phone" class="form-control" id="client_phone" name="phone_number" value="<?= $client['phone_number'] ?? ''  ?>" placeholder="<?= $this->lang->line('phone_number') ?>">
										</div>
									</div>
									<div class="form-group row">
										<label for="client_category" class="col-sm-3 col-form-label"><?= $this->lang->line('client_category') ?></label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="client_category" name="client_category" value="<?= $client['client_category'] ?? ''  ?>" placeholder="<?= $this->lang->line('client_category') ?>">
										</div>
									</div>
									<div class="form-group row">
										<label for="occupation" class="col-sm-3 col-form-label"><?= $this->lang->line('occupation') ?></label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="occupation" name="occupation" value="<?= $client['occupation'] ?? ''  ?>" placeholder="<?= $this->lang->line('occupation') ?>">
										</div>
									</div>
									<div class="form-group row">
										<label for="remark" class="col-sm-3 col-form-label"><?= $this->lang->line('remark') ?></label>
										<div class="col-sm-9">
											<?= form_error('remark'); ?>
											<input type="text" class="form-control" id="remark" name="remark" value="<?= $client['remark'] ?>" placeholder="<?= $this->lang->line('password') ?>">
										</div>
									</div>
									<div class="form-group row">
										<div class="col-offset-2 col-sm-12">
											<button type="submit" class="btn btn-danger float-right"><?= $this->lang->line('update') ?></button>
										</div>
									</div>
									</form>
								</div>
								<!-- /.tab-pane -->
							</div>
							<!-- /.tab-content -->
						</div><!-- /.card-body -->
					</div>
					<!-- /.nav-tabs-custom -->
				</div>
				<!-- /.col -->
			</div>
		</div><!-- /.container-fluid -->
	</section>
	<!-- /.content -->
</div>
<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
	<!-- Create the tabs -->
	<ul class="nav nav-tabs nav-justified control-sidebar-tabs">
		<li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
		<li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
	</ul>
	<!-- Tab panes -->
	<div class="tab-content">
		<!-- Home tab content -->
		<div class="tab-pane" id="control-sidebar-home-tab">
			<h3 class="control-sidebar-heading">Recent Activity</h3>
			<ul class="control-sidebar-menu">
				<li>
					<a href="javascript:void(0)">
						<i class="menu-icon fa fa-birthday-cake bg-red"></i>

						<div class="menu-info">
							<h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

							<p>Will be 23 on April 24th</p>
						</div>
					</a>
				</li>
				<li>
					<a href="javascript:void(0)">
						<i class="menu-icon fa fa-user bg-yellow"></i>

						<div class="menu-info">
							<h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

							<p>New phone +1(800)555-1234</p>
						</div>
					</a>
				</li>
				<li>
					<a href="javascript:void(0)">
						<i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

						<div class="menu-info">
							<h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

							<p>nora@example.com</p>
						</div>
					</a>
				</li>
				<li>
					<a href="javascript:void(0)">
						<i class="menu-icon fa fa-file-code-o bg-green"></i>

						<div class="menu-info">
							<h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

							<p>Execution time 5 seconds</p>
						</div>
					</a>
				</li>
			</ul>
			<!-- /.control-sidebar-menu -->

			<h3 class="control-sidebar-heading">Tasks Progress</h3>
			<ul class="control-sidebar-menu">
				<li>
					<a href="javascript:void(0)">
						<h4 class="control-sidebar-subheading">
							Custom Template Design
							<span class="label label-danger pull-right">70%</span>
						</h4>

						<div class="progress progress-xxs">
							<div class="progress-bar progress-bar-danger" style="width: 70%"></div>
						</div>
					</a>
				</li>
				<li>
					<a href="javascript:void(0)">
						<h4 class="control-sidebar-subheading">
							Update Resume
							<span class="label label-success pull-right">95%</span>
						</h4>

						<div class="progress progress-xxs">
							<div class="progress-bar progress-bar-success" style="width: 95%"></div>
						</div>
					</a>
				</li>
				<li>
					<a href="javascript:void(0)">
						<h4 class="control-sidebar-subheading">
							Laravel Integration
							<span class="label label-warning pull-right">50%</span>
						</h4>

						<div class="progress progress-xxs">
							<div class="progress-bar progress-bar-warning" style="width: 50%"></div>
						</div>
					</a>
				</li>
				<li>
					<a href="javascript:void(0)">
						<h4 class="control-sidebar-subheading">
							Back End Framework
							<span class="label label-primary pull-right">68%</span>
						</h4>

						<div class="progress progress-xxs">
							<div class="progress-bar progress-bar-primary" style="width: 68%"></div>
						</div>
					</a>
				</li>
			</ul>
			<!-- /.control-sidebar-menu -->

		</div>
		<!-- /.tab-pane -->
		<!-- Stats tab content -->
		<div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
		<!-- /.tab-pane -->
		<!-- Settings tab content -->
		<div class="tab-pane" id="control-sidebar-settings-tab">
			<form method="post">
				<h3 class="control-sidebar-heading">General Settings</h3>

				<div class="form-group">
					<label class="control-sidebar-subheading">
						Report panel usage
						<input type="checkbox" class="pull-right" checked>
					</label>

					<p>
						Some information about this general settings option
					</p>
				</div>
				<!-- /.form-group -->

				<div class="form-group">
					<label class="control-sidebar-subheading">
						Allow mail redirect
						<input type="checkbox" class="pull-right" checked>
					</label>

					<p>
						Other sets of options are available
					</p>
				</div>
				<!-- /.form-group -->

				<div class="form-group">
					<label class="control-sidebar-subheading">
						Expose author name in posts
						<input type="checkbox" class="pull-right" checked>
					</label>

					<p>
						Allow the user to show his name in blog posts
					</p>
				</div>
				<!-- /.form-group -->

				<h3 class="control-sidebar-heading">Chat Settings</h3>

				<div class="form-group">
					<label class="control-sidebar-subheading">
						Show me as online
						<input type="checkbox" class="pull-right" checked>
					</label>
				</div>
				<!-- /.form-group -->

				<div class="form-group">
					<label class="control-sidebar-subheading">
						Turn off notifications
						<input type="checkbox" class="pull-right">
					</label>
				</div>
				<!-- /.form-group -->

				<div class="form-group">
					<label class="control-sidebar-subheading">
						Delete chat history
						<a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
					</label>
				</div>
				<!-- /.form-group -->
			</form>
		</div>
		<!-- /.tab-pane -->
	</div>
</aside>
<!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
