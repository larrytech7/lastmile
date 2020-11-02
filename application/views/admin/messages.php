<?php

/**
 * @author [zeufack]
 * @email [zeufackp@gmail.com]
 * @create date 2020-09-30 16:48:24
 * @modify date 2020-09-30 16:48:24
 * @contributors []
 */
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark"><?= $this->lang->line('clients') ?></h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>"><?= $this->lang->line('home') ?></a></li>
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
							<i class="fa fa-user-tie"></i>
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
							<i class="fa fa-users"></i>
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
			<!-- Main row -->
			<div class="row">
				<!-- Left col -->
				<section class="col-lg-12 connectedSortable">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title"><?= $this->lang->line('messages') ?></h3>
							<a href="#" data-target="#addMessage" data-toggle="modal" class="float-right btn btn-primary" title="<?= $this->lang->line('add_message') ?>"><i class="fas fa-comments"></i></a>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<table id="messages" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th><?= $this->lang->line('name') ?></th>
										<th><?= $this->lang->line('phone_number') ?></th>
										<th><?= $this->lang->line('email') ?></th>
										<th><?= $this->lang->line('client_category') ?></th>
										<th><?= $this->lang->line('franchise') ?></th>
										<th><?= $this->lang->line('action') ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($messages as $message) : ?>
										<tr>
											<td><?= $message['first_name'] . ' ' . $message['last_name'] ?></td>
											<td><?= $message['phone_number'] ?></td>
											<td><?= $message['client_email'] ?></td>
											<td><?= $message['client_category'] ?></td>
											<td><a href="<?= site_url('dashboard/user/view/' . $message['users_user_id']) ?>"><?= $message['name'] ?></a></td>
											<td class="text-right py-0 align-middle">
												<div class="btn-group btn-group-sm">
													<a href="<?= site_url('dashboard/message/' . $message['message_id']) ?>" class="btn btn-info"><i class="fas fa-eye"></i></a>
													<a href="<?= site_url('dashboard/message_edit/' . $message['message_id']) ?>" class="btn btn-primary"><i class="fas fa-edit"></i></a>
													<a href="<?= site_url('dashboard/message_delete/' . $message['message_id']) ?>" class="btn btn-danger" onclick="javascript:return confirm('<?= $this->lang->line('confirm_delete') ?>')"><i class="fas fa-trash"></i></a>
												</div>
											</td>
										</tr>
									<?php endforeach ?>
								</tbody>
							</table>
						</div>
						<!-- /.card-body -->
					</div>
					<!-- /.card -->
				</section>
				<!-- /.Left col -->
			</div>
			<!-- /.row (main row) -->
		</div><!-- /.container-fluid -->
	</section>
	<!-- /.content -->
</div>
<!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
