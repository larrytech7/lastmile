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
            <h1 class="m-0 text-dark"><i class="fas fa-cog"></i> <?= $this->lang->line('settings') ?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= site_url('dashboard')?>"><?= $this->lang->line('home') ?></a></li>
              <li class="breadcrumb-item active"><?= $this->lang->line('settings') ?></li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
  
        <!-- /.row -->
        <div class="row">
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" href="#commission_config" data-toggle="pill"><?= $this->lang->line('commissions') ?></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#tax_config" data-toggle="pill"><?= $this->lang->line('tax') ?></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#role_config" data-toggle="pill"><?= $this->lang->line('roles') ?></a>
                  </li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="commission_config">
                    <div class="row">
                      <div class="col-md-12">
                        <?= validation_errors() ?>
                        <?= form_open(site_url('dashboard/settings_add'), array('class'=>'form form-horizontal','role'=>'form'))?>
                          <div class="form-group row">
                            <div class="col-sm-4">
                              <label for="comm_type" class="col-form-label"><?= $this->lang->line('comm_type') ?> </label>
                              <select class="form-control comm-type-select2" id="comm_type" name="comm_type" required>
                                <option><?= $this->lang->line('weekly') ?></option>
                                <option><?= $this->lang->line('two_weeks') ?></option>
                                <option><?= $this->lang->line('monthly') ?></option>
                              </select>                            
                            </div>                       
                            <div class="col-sm-4">
                              <label for="comm_type_amount" class="col-form-label"><?= $this->lang->line('comm_type_amount') ?></label>
                              <input type="number" min="0" step="0.01" class="form-control" id="comm_type_amount" value="0" name="comm_type_amount" required >
                            </div>
                            <div class="col-sm-4">
                              <label for="comm_telefonist_amount" class="col-form-label"><?= $this->lang->line('comm_telefonist_amount') ?></label>
                              <input type="number" min="0" class="form-control" id="comm_telefonist_amount" value="0" name="comm_telefonist_amount" required >
                            </div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-4">
                              <label for="comm_currency" class="col-form-label"><?= $this->lang->line('comm_currency') ?> </label>
                              <select class="form-control comm-currency-select2" id="comm_currency" name="comm_currency" required>
                                <option>CAD</option>
                                <option>EUR</option>
                                <option>USD</option>
                                <option>XAF</option>
                              </select>
                            </div>                       
                            <div class="col-sm-4">
                              <label for="tax" class="col-form-label"><?= $this->lang->line('tax') ?></label>
                              <select class="form-control comm-tax-select2" id="tax" name="comm_tax" required>
                                <?php foreach($taxes ?? [] as $m_tax ): ?>
                                  <option value="<?= $m_tax['tax_id'] ?>"><?= $m_tax['tax_name'] ?></option>
                                <?php endforeach ?>
                              </select>
                            </div>
                            <div class="col-sm-4">
                              <label for="btnsubmit" class="col-form-label"></label>
                              <p></p>
                              <button type="submit" id="btnsubmit" class="btn btn-danger"><?= $this->lang->line('add') ?></button>
                            </div>
                          </div>
                        </form>
                        <table id="" class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th><?= $this->lang->line('comm_type') ?></th>
                              <th><?= $this->lang->line('comm_type_amount') ?></th>
                              <th><?= $this->lang->line('comm_telefonist_amount') ?></th>
                              <th><?= $this->lang->line('tax') ?></th>
                              <th><?= $this->lang->line('comm_currency') ?></th>
                              <th><?= $this->lang->line('action') ?></th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php foreach($settings ?? [] as $setting ): ?>
                              <tr data-toggle="tooltip" title="<?= $this->lang->line('author') . ' : ' . $setting['username']?>">
                                <td ><?= $setting['config_commission_type'] ?></td>
                                <td ><?= $setting['config_commission_amount'] ?></td>
                                <td><?= $setting['config_telefonist_amount'] ?></td>
                                <td><a href="<?= site_url('dashboard/tax/view/'.$setting['config_tax_id']) ?>"> <?= $setting['tax_name'] ?></a></td>
                                <td><?= $setting['config_currency'] ?></td>
                                <td class="text-right py-0 align-middle">
                                  <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url('dashboard/setting/view/'.$setting['config_id']) ?>" class="btn btn-success" data-toggle="tooltip" title="<?= $this->lang->line('update') ?>"><i class="fas fa-edit"></i></a>
                                  </div>
                                </td>
                              </tr>
                            <?php endforeach ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="tax_config">
                    <div class="row">
                      <div class="col-md-12">
                        <?= validation_errors() ?>
                        <?= form_open(site_url('dashboard/tax/add'), array('class'=>'form form-horizontal','role'=>'form'))?>
                          <div class="form-group row">
                            <div class="col-sm-5">
                              <label for="tax_name" class="col-form-label"><?= $this->lang->line('tax_name') ?> </label>
                              <input type="text" class="form-control" id="tax_name" name="tax_name" required>
                            </div>                       
                            <div class="col-sm-5">
                              <label for="tax_rate" class="col-form-label"><?= $this->lang->line('tax_rate') ?></label>
                              <input type="number" min="0" step="0.001" max="100" class="form-control" id="tax_rate" name="tax_rate" required >
                            </div>
                            <div class="col-sm-2">
                              <label for="btnsubmit" class="col-form-label"></label>
                              <p></p>
                              <button type="submit" id="btnsubmit" class="btn btn-danger"><?= $this->lang->line('add') ?></button>
                            </div>
                          </div>
                        </form>
                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th><?= $this->lang->line('tax_name') ?></th>
                              <th><?= $this->lang->line('tax_rate') ?></th>
                              <th><?= $this->lang->line('date_created') ?></th>
                              <th><?= $this->lang->line('action') ?></th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php foreach($taxes ?? [] as $m_tax ): ?>
                              <tr data-toggle="tooltip" title="<?= $this->lang->line('author') . ' : ' . $m_tax['username']?>">
                                <td ><a href="" ><?= $m_tax['tax_name'] ?></a></td>
                                <td><?= $m_tax['tax_rate'] ?></td>
                                <td><?= $m_tax['create_time'] ?></td>
                                <td class="text-right py-0 align-middle">
                                  <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url('dashboard/tax/view/'.$m_tax['tax_id']) ?>" class="btn btn-success" data-toggle="tooltip" title="<?= $this->lang->line('update') ?>"><i class="fas fa-edit"></i></a>
                                  </div>
                                </td>
                              </tr>
                            <?php endforeach ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="role_config">
                    <div class="row">
                      <div class="col-md-6">
                    <?= validation_errors() ?>
                    <?= form_open(site_url('dashboard/role/add'), array('class'=>'form form-horizontal','role'=>'form'))?>
                      <div class="form-group ">
                        <div class="col-sm-12">
                          <label for="role_name" class="col-form-label"><?= $this->lang->line('role_name') ?></label>
                          <input type="text" class="form-control" id="role_name" name="role_name" required >
                        </div>
                      </div>
                      <div class="form-group ">
                        <div class="col-sm-12">
                          <label for="permission" class="col-form-label"><?= $this->lang->line('permission') ?></label>
                          <select class="form-control permission-select2" id="permission" name="permission[]" multiple required>
                                <?php foreach($permissions ?? [] as $permission ): ?>
                                  <option value="<?= $permission['permission_id'] ?>"><?= $permission['permission_name'] ?></option>
                                <?php endforeach ?>
                          </select>                        
                        </div>
                      </div>
                      
                      <div class="form-group ">
                        <div class="col-sm-12">
                          <button type="submit" class="btn btn-danger float-right"><?= $this->lang->line('add') ?></button>
                        </div>
                      </div>
                    </form>
                      </div>
                      <div class="col-md-6">
                      <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th>Role</th>
                              <th><?= $this->lang->line('permissions') ?></th>
                              <th><?= $this->lang->line('action') ?></th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php foreach($roles ?? [] as $role_id => $role ): ?>
                              <tr data-toggle="tooltip" title="">
                                <td ><a href="#!" ><?= $role[0]['role_name'] ?></a></td>
                                <td>
                                  <?= array_walk($role, function($data, $key){
                                      echo $data['permission_name'] . ', ';
                                }) ?>
                                </td>
                                <td class="text-right py-0 align-middle">
                                  <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url('dashboard/role/delete/'.$role_id) ?>" class="btn btn-danger" data-toggle="tooltip" title="<?= $this->lang->line('delete') ?>"><i class="fas fa-trash"></i></a>
                                    <a href="<?= site_url('dashboard/role/view/'.$role_id) ?>" class="btn btn-success" data-toggle="tooltip" title="<?= $this->lang->line('update') ?>"><i class="fas fa-edit"></i></a>
                                  </div>
                                </td>
                              </tr>
                            <?php endforeach ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
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
