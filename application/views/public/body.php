<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div id="container">
	<h1>THIS IS THE BODY OF YOUR PAGE!</h1>

	<div id="body">
		<p>The page you are looking at is being generated dynamically by CodeIgniter.</p>

		<p>If you would like to edit this page you'll find the files located at:</p>
		<code>application/views/public/</code>

		<p>The corresponding controller for this page is found at:</p>
		<code>application/controllers/Home.php</code>

		<p>If you are exploring CodeIgniter for the very first time, you should start by reading the <a href="user_guide/">User Guide</a>.</p>
	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
</div>