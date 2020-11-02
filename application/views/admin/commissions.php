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
            <h1 class="m-0 text-dark"><?= $this->lang->line('commissions') ?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= site_url('dashboard')?>"><?= $this->lang->line('home') ?></a></li>
              <li class="breadcrumb-item active"><?= $this->lang->line('commissions') ?></li>
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
                <h3 class="card-title"><?= $this->lang->line('commissions_history') ?></h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="commissions" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th><?= $this->lang->line('campaign_of') ?></th>
                    <th><?= $this->lang->line('prov_tax') ?></th>
                    <th><?= $this->lang->line('fed_tax') ?></th>
                    <th><?= $this->lang->line('contacts') ?></th>
                    <th><?= $this->lang->line('duration') ?></th>
                    <th><?= $this->lang->line('total') ?></th>
                    <th><?= $this->lang->line('action') ?></th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach($commissions as $comm ): ?>
                  <?php 
                      $now = new DateTime("now"); 
                      $end_date = new DateTime($comm['end_date'] . ' ' . $comm['end_time']);
                      $total_tax = $comm['prov_tax'] + $comm['fed_tax'];
                      $total_contacts = $comm['contacts'];
                      $duration = date_diff(new DateTime($comm['start_date'] . ' ' . $comm['start_time']), new DateTime($comm['end_date'] . ' ' . $comm['end_time']), true);
                  ?>
                    <tr data-toggle="tooltip" title="<?= $this->lang->line('author') . ' : ' . $comm['username']?>">
                      <td ><a href="<?= site_url('dashboard/campaign/view/'.$comm['campaign_id'])?>" ><?= $comm['start_date'] . ' ' . $comm['start_time'] . ' - ' . $comm['end_date'] . ' ' . $comm['end_time'] ?></a></td>
                      <td><?= $comm['prov_tax'] ?></td>
                      <td><?= $comm['fed_tax'] ?></td>
                      <td><?= $comm['contacts'] ?></td>
                      <td><?= $duration->format('%a ' . $this->lang->line('days')) ?></td>
                      <td><b class="float-right"> <?= number_format(($comm['commission_amount'] + ($comm['commission_amount'] * ($total_tax/100) )) * $total_contacts * (intval($duration->format('%a')) == 0 ? 1 : intval($duration->format('%a'))) , 2)  ?> <i class="fas fa-dollar-sign"></i></b> </td>
                      <td class="text-right py-0 align-middle">
                        <div class="btn-group btn-group-sm">
                          <a href="<?= site_url('dashboard/campaign/receipt/'.$comm['campaign_id']) ?>" target="_blank" class="btn btn-success" data-toggle="tooltip" title="<?= $this->lang->line('view_receipt') ?>"><i class="fas fa-file-download"></i></a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach ?>
                  </tbody>
                  <tfoot>
                  <tr>
                  <th><?= $this->lang->line('campaign_of') ?></th>
                    <th><?= $this->lang->line('prov_tax') ?></th>
                    <th><?= $this->lang->line('fed_tax') ?></th>
                    <th><?= $this->lang->line('contacts') ?></th>
                    <th><?= $this->lang->line('duration') ?></th>
                    <th><?= $this->lang->line('total') ?></th>
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
