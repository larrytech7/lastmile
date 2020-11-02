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
            <h1 class="m-0 text-dark"><i class="fas fa-clock-o"></i> <?= $campaign['start_date'] . ' ' . $campaign['start_time'] ?></h1>
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
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <?= $this->lang->line('bill') ?>
                  <span>
                    <i class="fas fa-receipt"></i>
                  </span>
                </div>

                <h3 class="profile-username text-center"><?= $user['name'] ?? ''  ?></h3>

                <p class="text-muted text-center"><?= $user['email'] ?? '' ?></p>
                <?php 
                  $total_tax = $campaign['prov_tax'] + $campaign['fed_tax'];
                  $total_contacts = $campaign['contacts'];
                  $duration = date_diff(new DateTime($campaign['start_date'] . ' ' . $campaign['start_time']), new DateTime($campaign['end_date'] . ' ' . $campaign['end_time']), true);

                ?>
                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item" title="<?= $this->lang->line('commission') ?>">
                    <i class="fas fa-money-bill-wave"></i>
                    <span><?= $this->lang->line('commission') ?></span> <b class="float-right"><?= number_format($campaign['commission_amount'] ?? '',2) ?> <i class="fas fa-dollar-sign"></i></b>
                  </li>
                  <li class="list-group-item" title="<?= $this->lang->line('duration') ?>">
                    <i class="fas fa-hourglass-half"></i>
                    <span><?= $this->lang->line('duration') ?></span> <b class="float-right"><?= $duration->format('%a ' . $this->lang->line('days')) ?> <i class="fas fa-clock"></i></b>
                  </li>
                  <li class="list-group-item" title="<?= $this->lang->line('fed_tax') ?>">
                    <i class="fas fa-percentage"></i>
                    <span><?= $this->lang->line('fed_tax') ?></span> <b class="float-right"><?= number_format($campaign['fed_tax']?? '', 2) ?> <i class="fas fa-percent"></i></b>
                  </li>
                  <li class="list-group-item" title="<?= $this->lang->line('prov_tax') ?>">
                    <i class="fas fa-percentage"></i>
                    <span><?= $this->lang->line('prov_tax') ?></span> <b class="float-right"><?= number_format($campaign['prov_tax']?? '' , 2) ?> <i class="fas fa-percent"></i></b>
                  </li>
                  <li class="list-group-item" title="<?= $this->lang->line('address') ?>">
                    <i class="fas fa-percentage"></i>
                    <span><?= $this->lang->line('total_tax') ?></span> <b class="float-right"><?= number_format($campaign['prov_tax'] + $campaign['fed_tax']?? '',2) ?> <i class="fas fa-percent"></i></b>
                  </li>
                  
                  <li class="list-group-item bg-success " title="<?= $this->lang->line('total') ?>">
                    <i class="fas fa-coins"></i>
                    <span>Total</span> <b class="float-right"> <?= number_format(($campaign['commission_amount'] + ($campaign['commission_amount'] * ($total_tax/100) )) * $total_contacts * (intval($duration->format('%a')) == 0 ? 1 : intval($duration->format('%a'))) , 2)  ?> <i class="fas fa-dollar-sign"></i></b> 
                  </li>
                </ul>

              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-8">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#update" data-toggle="tab"><?= $this->lang->line('campaign') ?></a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="update">
                    <?= validation_errors() ?>
                  <?= form_open(site_url('dashboard/campaign/edit/'.$campaign['campaign_id']), array('class'=>'form form-horizontal','role'=>'form'))?>
                      <div class="form-group row">
                        <div class="col-sm-9">
                          <label for="client_edit" class="col-form-label"><?= $this->lang->line('client') ?></label>
                          <select class="form-control bg-danger" id="client_edit" name="campaign_client_edit">
                            <?php foreach($clients as $cli) : ?>
                              <option <?= $cli['client_id'] == $campaign['client_id'] ? 'selected' : '' ?> value="<?= $cli['client_id']?>"><?= $cli['first_name'] . ' ' . $cli['last_name'] . '(' . $cli['client_category'] .')' ?></option>
                            <?php endforeach ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="col-sm-9">
                          <label for="campaign_period_edit" class="col-form-label"><?= $this->lang->line('period') ?> (<?= $this->lang->line('select_period') ?>)</label>
                          <input type="text" class="form-control" id="campaign_period_edit" name="campaign_period_edit" value="<?= $user['name'] ?? '' ?>" required>
                        </div>
                      </div>
                      <div class="form-group row">                        
                        <div class="col-sm-9">
                          <label for="campaign_contacts_edit" class="col-form-label"><?= $this->lang->line('contacts') ?></label>
                          <input type="number" min="1" max="1000" class="form-control" id="campaign_contacts_edit" value="<?= $campaign['contacts'] ?? ''  ?>" name="campaign_contacts_edit" required >
                        </div>
                      </div>
                      
                      <div class="form-group row">
                        <div class="col-offset-2 col-sm-9">
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
	<!-- /.control-sidebar -->
	<!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
	<div class="control-sidebar-bg"></div>
