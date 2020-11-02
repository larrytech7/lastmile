<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * @date 15/08/2020
 * Contributors : 
 */
?>

<footer class="text-center">
    <div class=" hidden-xs">
        <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; <?= date('Y')?> <a href="https://sevengps.net" target="_blank">Seven GPS</a>. </strong> All rights
    reserved.
</footer>
</div>
<!-- ./wrapper -->
<!-- jQuery -->
<script src="<?= base_url('resources/')?>plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?= base_url('resources/')?>plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?= base_url('resources/')?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Toastr -->
<script src="<?= base_url('resources/')?>plugins/toastr/toastr.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('resources/')?>dist/js/adminlte.js"></script>
<script>
    <?php if(($this->session->flashdata('success'))) : ?>
        toastr.success('<?= $this->session->flashdata('success') ?>.', '', {closeButton : true, progressBar : true, position : 'bottomRight'})
    <?php endif; ?>
    <?php if(($this->session->flashdata('error'))) : ?>
        toastr.error('<?= $this->session->flashdata('error') ?>.', '', {closeButton : true, progressBar : true})
    <?php endif; ?>
    <?php if(($this->session->flashdata('info'))) : ?>
        toastr.info('<?= $this->session->flashdata('info') ?>.', '', {closeButton : true, progressBar : true})
    <?php endif; ?>
    <?php if(($this->session->flashdata('warning'))) : ?>
        toastr.warning('<?= $this->session->flashdata('warning') ?>.', '', {closeButton : true, progressBar : true})
    <?php endif; ?>
</script>
</body>
</html>