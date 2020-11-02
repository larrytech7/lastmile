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
            <h1 class="m-0 text-dark"><?= $this->lang->line('campaigns') ?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= site_url('dashboard')?>"><?= $this->lang->line('home') ?></a></li>
              <li class="breadcrumb-item active"><?= $this->lang->line('campaign') ?></li>
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
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-lg-12 connectedSortable">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><?= $this->lang->line('campaigns') ?></h3>
                <a href="#" data-target="#addcampaign" data-toggle="modal" class="float-right btn btn-primary" title="<?= $this->lang->line('add_campaign') ?>"><i class="fas fa-bullhorn"></i></a>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="campaigns" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th><?= $this->lang->line('start') ?></th>
                    <th><?= $this->lang->line('end') ?></th>
                    <th><?= $this->lang->line('contacts') ?></th>
                    <th><?= $this->lang->line('client') ?></th>
                    <th><?= $this->lang->line('date_created') ?></th>
                    <th><?= $this->lang->line('action') ?></th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach($campaigns as $camp ): ?>
                  <?php $now = new DateTime("now"); 
                        $end_date = new DateTime($camp['end_date'] . ' ' . $camp['end_time']);
                  ?>
                    <tr data-toggle="tooltip" title="<?= $this->lang->line('author') . ' : ' . $camp['username']?>">
                      <td ><?= $camp['campaign_id'] ?></td>
                      <td><?= $camp['start_date'] . ' ' . $camp['start_time'] ?></td>
                      <td class="<?= ($end_date <= $now) ? 'bg-danger' : ''?>"><?= $camp['end_date'] . ' ' . $camp['end_time'] ?></td>
                      <td><?= $camp['contacts'] ?></td>
                      <td><?= $camp['first_name'] . ' ' .$camp['last_name'] ?></td>
                      <td><span data-toggle="tooltip" title="<?= $this->lang->line('last_update') .  $camp['update_time'] ?>"><?= $camp['create_time'] ?></span></td>
                      <td class="text-right py-0 align-middle">
                        <div class="btn-group btn-group-sm">
                          <a href="<?= site_url('dashboard/campaign/receipt/'.$camp['campaign_id']) ?>" target="_blank" class="btn btn-success" data-toggle="tooltip" title="<?= $this->lang->line('view_receipt') ?>"><i class="fas fa-file-download"></i></a>
                          <a href="<?= site_url('dashboard/campaign/end/'.$camp['campaign_id']) ?>" class="btn btn-warning" data-toggle="tooltip" title="<?= $this->lang->line('end_campaign') ?>" onclick="javascript:return confirm('<?= $this->lang->line('confirm_end')?>')"><i class="fas fa-ban"></i></a>
                          <a href="<?= site_url('dashboard/campaign/view/'.$camp['campaign_id']) ?>" class="btn btn-primary" data-toggle="tooltip" title="<?= $this->lang->line('edit_campaign') ?>"><i class="fas fa-edit"></i></a>
                          <a href="<?= site_url('dashboard/campaign/delete/'.$camp['campaign_id']) ?>" class="btn btn-danger" data-toggle="tooltip" title="<?= $this->lang->line('delete_campaign') ?>" onclick="javascript:return confirm('<?= $this->lang->line('confirm_delete')?>')"><i class="fas fa-trash"></i></a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach ?>
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>#</th>
                    <th><?= $this->lang->line('start') ?></th>
                    <th><?= $this->lang->line('end') ?></th>
                    <th><?= $this->lang->line('contacts') ?></th>
                    <th><?= $this->lang->line('client') ?></th>
                    <th><?= $this->lang->line('date_created') ?></th>
                    <th><?= $this->lang->line('action') ?></th>
                  </tr>
                  </tfoot>
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
