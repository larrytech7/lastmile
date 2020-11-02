<?php

/**
 * @author [zeufack]
 * @email [zeufackp@gmail.com]
 * @create date 2020-10-01 12:12:02
 * @modify date 2020-10-01 12:12:02
 * @contributors []
 */
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark"><?= $this->lang->line('message') ?></h1>
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
				<div class="col-md-12">
					<div class="card">
						<div class="card-body">
							<div class="tab-content">
								<div class="active tab-pane" id="update">
									<?= validation_errors() ?>
									<?= form_open(site_url('dashboard/client/edit/'), array('class' => 'form form-horizontal', 'role' => 'form')) ?>

									<div class="form-group">
										<label for="message_subject" class="col-form-label"><?= $this->lang->line('name') ?> </label>
										<input type="text" class="form-control" id="message_subject" name="message_subject" value="<?= $message['name'] ?? '' ?>" placeholder="<?= $this->lang->line('name') ?>" required>
									</div>
									<div class="form-group">
										<label for="message_subject" class="col-form-label"><?= $this->lang->line('email') ?> </label>
										<input type="email" class="form-control" id="message_subject" value="<?= $message['email'] ?? '' ?>" name="message_subject" placeholder="<?= $this->lang->line('email') ?>" required>
									</div>
									<div class="form-group">
										<label for="message_subject" class="col-form-label"><?= $this->lang->line('status') ?> </label>
										<input type="text" class="form-control" id="message_subject" value="<?= $message['status'] ?? '' ?>" name="message_subject" placeholder="<?= $this->lang->line('status') ?>" required>
									</div>
									<div class="form-group">
										<label for="subject" class="col-form-label"><?= $this->lang->line('message_subject') ?> </label>
										<input type="text" class="form-control" id="subject" value="<?= $message['subject'] ?? '' ?>" name="subject" placeholder="<?= $this->lang->line('message_subject') . ' Du Message' ?>" required>
									</div>

									<div class="form-group">
										<label for="message"><?= $this->lang->line('message') ?></label>
										<textarea type="text" class="form-control" id="message"   name="message" rows="3" placeholder="<?= $message['message'] ?? 'Votre ' . $this->lang->line('message') ?>"></textarea>
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
