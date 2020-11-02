<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * @date 15/08/2020
 * Contributors : 
 */

?>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
  	<a href="<?= site_url()?>"><img src="<?= base_url('resources/dist/img/logo.png')?>" style="height: 80%; width:80%" /></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
	<p class="login-box-msg"><?= $this->lang->line('sign_in')?></p>

      <form action="<?= site_url('login/login')?>" method="post">
        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="<?= $this->lang->line('email')?>" name="email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="<?= $this->lang->line('password')?>" name="password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
			          <?= $this->lang->line('remember_me')?>
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-6">
            <button type="submit" class="btn btn-primary btn-block"><?= $this->lang->line('btn_sign_in')?></button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <p class="mb-1">
        <a href="<?= site_url('login/forgot_password')?>"><?= $this->lang->line('forgot_password')?> ?</a>
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>