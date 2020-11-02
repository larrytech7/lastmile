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
                    <a class="nav-link active"  href="#tax_config" data-toggle="pill"><?= $this->lang->line('tax') ?></a>
                  </li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="tax_config">
                    <div class="row">
                      <div class="col-md-12">
                        <?= validation_errors() ?>
                        <?= form_open(site_url('dashboard/tax/edit/'.$tax['tax_id']), array('class'=>'form form-horizontal','role'=>'form'))?>
                          <div class="form-group row">
                            <div class="col-sm-5">
                              <label for="tax_name" class="col-form-label"><?= $this->lang->line('tax_name') ?> </label>
                              <input type="text" class="form-control" id="tax_name" name="tax_name" value="<?= $tax['tax_name']?>" required>
                              <input type="hidden" name="tax_id" value="<?= $tax['tax_id']?>" required>
                            </div>                       
                            <div class="col-sm-5">
                              <label for="tax_rate" class="col-form-label"><?= $this->lang->line('tax_rate') ?></label>
                              <input type="number" min="0" step="0.001" max="100" class="form-control" id="tax_rate" name="tax_rate" value="<?= $tax['tax_rate'] ?>" required >
                            </div>
                            <div class="col-sm-2">
                              <label for="btnsubmit" class="col-form-label"></label>
                              <p></p>
                              <button type="submit" id="btnsubmit" class="btn btn-danger"><?= $this->lang->line('update') ?></button>
                            </div>
                          </div>
                        </form>
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
