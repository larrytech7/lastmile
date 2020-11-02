<?php

/**
 * @author [zeufack]
 * @email [zeufackp@gmail.com]
 * @create date 2020-09-25 13:26:00
 * @modify date 2020-09-25 13:26:00
 * @desc []
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
					<h1 class="m-0 text-dark"><?= $this->lang->line('rdvs') ?></h1>
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
							<h3 class="card-title"><?= $this->lang->line('rdvs') ?></h3>
							<a href="#" data-target="#addRdv" data-toggle="modal" class="float-right btn btn-primary" data-toggle="popup" data-title="<?= $this->lang->line('add_rdv') ?>">
								<i class="fas fa-plus-square"></i>
							</a>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<table id="rdvs" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th><?= $this->lang->line('rdv_date') ?></th>
										<th><?= $this->lang->line('rdv_time') ?></th>
										<th><?= $this->lang->line('last_name') ?></th>
										<th><?= $this->lang->line('first_name') ?></th>
										<th><?= $this->lang->line('phone_number') ?></th>
										<th><?= $this->lang->line('email') ?></th>
										<th><?= $this->lang->line('client_category') ?></th>
										<th><?= $this->lang->line('action') ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($rdvs as $rdv) : ?>
										<tr>
											<td><?= $rdv['rdv_date'] ?></td>
											<td><?= $rdv['rdv_time'] ?></td>
											<td><?= $rdv['last_name'] ?></td>
											<td><?= $rdv['first_name'] ?></td>
											<td><?= $rdv['phone_number'] ?></td>
											<td><?= $rdv['client_email'] ?></td>
											<td><?= $rdv['client_category'] ?></td>
											<td class="text-right py-0 align-middle">
												<div class="btn-group btn-group-sm">
													<a href="<?= site_url('dashboard/rdv/view/' . $rdv['rdv_id']) ?>" class="btn btn-info"><i class="fas fa-eye"></i></a>
													<a href="<?= site_url('dashboard/rdv/view/' . $rdv['rdv_id']) ?>" class="btn btn-primary"><i class="fas fa-edit"></i></a>
													<a href="<?= site_url('dashboard/rdv/delete/' . $rdv['rdv_id']) ?>" class="btn btn-danger" onclick="javascript:return confirm('<?= $this->lang->line('confirm_delete') ?>')"><i class="fas fa-trash"></i></a>
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
		</div>
	</section>
</div>
