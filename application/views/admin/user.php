<?php
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * @date 15/08/2020
 * Contributors : 
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"><?= $user['role_name'] ?></h1>
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
                <h3><?= $statistics['franchises'] ?? 0?></h3>

                <p><?= $this->lang->line('franchises') ?></p>
              </div>
              <div class="icon">
                <i class="fa fa-user-tie"></i>
              </div>
              <a href="<?= site_url('dashboard/franchises')?>" class="small-box-footer"><?= $this->lang->line('see_more') ?> <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $statistics['clients'] ?? 0?></h3>

                <p><?= $this->lang->line('clients') ?></p>
              </div>
              <div class="icon">
                <i class="fa fa-users"></i>
              </div>
              <a href="<?= site_url('dashboard/clients')?>" class="small-box-footer"><?= $this->lang->line('see_more') ?> <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= $statistics['rdvs'] ?? 0?></h3>

                <p><?= $this->lang->line('rdvs') ?></p>
              </div>
              <div class="icon">
                <i class="fa fa-handshake"></i>
              </div>
              <a href="<?= site_url('dashboard/rdvs')?>" class="small-box-footer"><?= $this->lang->line('see_more') ?> <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?= $statistics['campaigns'] ?? 0?></h3>

                <p><?= $this->lang->line('campaigns') ?></p>
              </div>
              <div class="icon">
                <i class="fa fa-bullhorn"></i>
              </div>
              <a href="<?= site_url('dashboard/campaigns')?>" class="small-box-footer"><?= $this->lang->line('see_more') ?> <i class="fas fa-arrow-circle-right"></i></a>
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
                  <img class="profile-user-img img-fluid img-circle"
                       src="<?= base_url('resources/dist/img/avatar.png')?>"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?= $user['name']  ?></h3>

                <p class="text-muted text-center"><?= $user['email'] ?></p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item" title="<?= $this->lang->line('username') ?>">
                  <i class="fas fa-user"></i>
                    <b><?= $user['username'] ?></b> <a class="float-right"></a>
                  </li>
                  <li class="list-group-item" title="<?= $this->lang->line('phone_number') ?>">
                    <i class="fas fa-phone"></i>
                    <b><?= $user['user_phone_number'] ?></b> <a class="float-right"></a>
                  </li>
                  <li class="list-group-item" title="<?= $this->lang->line('address') ?>">
                    <i class="fas fa-globe"></i>
                    <b><?= $user['user_address'] ?></b> <a class="float-right"></a>
                  </li>
                  
                  <li class="list-group-item " title="<?= $this->lang->line('create_date') ?>">
                    <i class="fas fa-clock"></i>
                    <b><?= $user['create_time'] ?></b> <a class="float-right"></a>
                  </li>
                  <li class="list-group-item " title="<?= $this->lang->line('last_update_date') ?>">
                    <i class="fas fa-clock"></i>
                    <b><?= $user['update_time'] ?></b> <a class="float-right"></a>
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
                  <?= form_open(site_url('dashboard/user/edit/'.$user['user_id']), array('class'=>'form form-horizontal','role'=>'form'))?>
                      
                      <div class="form-group row">
                        <label for="reseller_name" class="col-sm-3 col-form-label"><?= $this->lang->line('name') ?></label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="reseller_name" name="name" value="<?= $user['name'] ?? '' ?>" placeholder="<?= $this->lang->line('name') ?>" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="reseller_username" class="col-sm-3 col-form-label"><?= $this->lang->line('username') ?></label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="reseller_username" value="<?= $user['username'] ?? ''  ?>" name="username" placeholder="<?= $this->lang->line('username') ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="email" class="col-sm-3 col-form-label"><?= $this->lang->line('email') ?></label>
                        <div class="col-sm-9">
                          <?= form_error('email'); ?>
                          <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?? ''  ?>" placeholder="<?= $this->lang->line('email') ?>" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="user_phone" class="col-sm-3 col-form-label"><?= $this->lang->line('phone_number') ?></label>
                        <div class="col-sm-9">
                          <input type="phone" class="form-control" id="user_phone" name="phone_number" value="<?= $user['user_phone_number'] ?? ''  ?>" placeholder="<?= $this->lang->line('phone_number') ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="user_address" class="col-sm-3 col-form-label"><?= $this->lang->line('address') ?></label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="user_address" name="user_address" value="<?= $user['user_address'] ?? ''  ?>" placeholder="<?= $this->lang->line('address') ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="user_status" class="col-sm-3 col-form-label"><?= $this->lang->line('status') ?></label>
                        <div class="col-sm-9">
                          <select class="form-control bg-danger" id="user_status" name="status">
                            <option <?= $user['user_status'] == 'ACTIVE' ? 'selected' : '' ?>>ACTIVE</option>
                            <option <?= $user['user_status'] == 'DISABLED' ? 'selected' : '' ?> >DISABLED</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="password" class="col-sm-3 col-form-label"><?= $this->lang->line('password') ?></label>
                        <div class="col-sm-9">
                          <?= form_error('password'); ?>
                          <input type="password" class="form-control" id="password" name="password" value="" placeholder="<?= $this->lang->line('password') ?>">
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
