<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div id="container">
	<h1>SEVENPAY MOCK PAYMENTS API</h1>

	<div id="body">

		<p>
			<a href="<?= site_url('home/momo') ?>">MTN CAMEROON MOBILE MONEY API</a> | <a href="<?= site_url('home/orangemo')?>">ORANGEMONEY API</a> | 
			<a href="<?= site_url('home/eupay') ?>">EXPRESS UNION MOBILE API</a> | <a href="<?= site_url('login/linkedin')?>">UBA VISA API</a> | 
		</p>

	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
</div>