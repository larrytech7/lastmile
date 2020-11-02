<?php
defined('BASEPATH') or exit('No direct script access allowed');
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
	<strong>Copyright &copy; <?= date('Y') ?> <a href="https://sevengps.net" target="_blank">Seven GPS</a>. </strong> All rights
	reserved.
</footer>
</div>
<!-- ./wrapper -->
<div class="modal modal-primary fade" id="adduser">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line('add_user') ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Input addon -->
				<?= form_open(site_url('dashboard/user_add'), array('class' => 'form form_vertical', 'role' => 'form')) ?>
				<div class="box box-info">
					<div class="box-header with-border"></div>
					<div class="box-body">
						<div class="form-group">
							<label for="reseller_name" class="col-form-label"><?= $this->lang->line('name') ?> </label>
							<input type="text" class="form-control" id="reseller_name" name="name" placeholder="<?= $this->lang->line('name') ?>" required>
						</div>
						<div class="form-group">
							<label for="reseller_username"><?= $this->lang->line('username') ?></label>
							<input type="text" class="form-control" id="reseller_username" name="username" placeholder="<?= $this->lang->line('username') ?>">
						</div>
						<div class="form-group">
							<label for="email"><?= $this->lang->line('email') ?></label>
							<input type="email" class="form-control" id="email" name="email" placeholder="<?= $this->lang->line('email') ?>" required>
						</div>
						<div class="form-group">
							<label for="phone"><?= $this->lang->line('phone_number') ?></label>
							<input type="phone" class="form-control" id="phone" name="phone_number" placeholder="<?= $this->lang->line('phone_number') ?>" required>
						</div>
						<div class="form-group">
							<label for="address"><?= $this->lang->line('address') ?></label>
							<input type="text" class="form-control" id="address" name="address" placeholder="<?= $this->lang->line('address') ?>">
						</div>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-outline btn-primary"><?= $this->lang->line('save') ?> <i class="fa fa-check"></i></button>
			</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<div class="modal modal-primary fade" id="addcampaign">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line('add_campaign') ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Input addon -->
				<?= form_open(site_url('dashboard/campaign_add'), array('class' => 'form form_vertical', 'role' => 'form')) ?>
				<div class="box box-info">
					<div class="box-header with-border"></div>
					<div class="box-body">
						<div class="form-group">
							<label for="client"><?= $this->lang->line('client') ?>s</label>
							<select type="text" class="form-control campaign-client-select2" id="client" name="campaign_client[]" multiple required>
								<?php foreach ($clients ?? [] as $client) : ?>
									<option value="<?= $client['client_id'] ?>"><?= $client['first_name'] . ' ' . $client['last_name'] . ' ( ' . $client['client_category'] . ')' ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<div class="form-group">
							<label for="campaign_tax"><?= $this->lang->line('campaign_type') ?></label>
							<select type="text" class="form-control campaign_type-select2" id="campaign_tax" name="campaign_tax" required>
								<?php foreach ($this->session->tax_commissions ?? [] as $tax_commission) : ?>
									<option value="<?= $tax_commission->config_id ?>"><?= $tax_commission->config_commission_type ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label for="campaign_start_date" class="col-form-label"><?= $this->lang->line('period') . '(' . $this->lang->line('select_period') . ')' ?> </label>
									<input type="text" class="form-control" id="campaign_period" name="campaign_period" required>
								</div>
							</div>
						</div>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-outline btn-primary"><?= $this->lang->line('save') ?> <i class="fa fa-check"></i></button>
			</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<div class="modal modal-primary fade" id="addfranchise">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line('add_franchise') ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Input addon -->
				<?= form_open(site_url('dashboard/franchise_add'), array('class' => 'form form_vertical', 'role' => 'form')) ?>
				<div class="box box-info">
					<div class="box-header with-border"></div>
					<div class="box-body">
						<div class="form-group">
							<label for="reseller_name" class="col-form-label"><?= $this->lang->line('name') ?> </label>
							<input type="text" class="form-control" id="reseller_name" name="name" placeholder="<?= $this->lang->line('name') ?>" required>
						</div>
						<div class="form-group">
							<label for="reseller_username"><?= $this->lang->line('username') ?></label>
							<input type="text" class="form-control" id="reseller_username" name="username" placeholder="<?= $this->lang->line('username') ?>">
						</div>
						<div class="form-group">
							<label for="email"><?= $this->lang->line('email') ?></label>
							<input type="email" class="form-control" id="email" name="email" placeholder="<?= $this->lang->line('email') ?>" required>
						</div>
						<div class="form-group">
							<label for="phone"><?= $this->lang->line('phone_number') ?></label>
							<input type="phone" class="form-control" id="phone" name="phone_number" placeholder="<?= $this->lang->line('phone_number') ?>" required>
						</div>
						<div class="form-group">
							<label for="address"><?= $this->lang->line('address') ?></label>
							<input type="text" class="form-control" id="address" name="address" placeholder="<?= $this->lang->line('address') ?>">
						</div>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-outline btn-primary"><?= $this->lang->line('save') ?> <i class="fa fa-check"></i></button>
			</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<div class="modal modal-primary fade" id="addclient">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line('add_client') ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Input addon -->
				<?= form_open(site_url('dashboard/client_add'), array('class' => 'form form_vertical', 'role' => 'form')) ?>
				<div class="box box-info">
					<div class="box-header with-border"></div>
					<div class="box-body">
						<div class="form-group">
							<label for="category" class="col-form-label"><?= $this->lang->line('client_category') ?> </label>
							<select class="form-control client-select2 select2-danger" data-dropdown-css-class="select2-danger" id="category" name="category"  style="width: 100%;">
								<option>Conseiller financier</option>
								<option>Courtier immobilier</option>
								<option>Agence de placement</option>
							</select>
						</div>
						<div class="form-group">
							<label for="f_name" class="col-form-label"><?= $this->lang->line('first_name') ?> </label>
							<input type="text" class="form-control" id="f_name" name="first_name" placeholder="<?= $this->lang->line('first_name') ?>" required>
						</div>
						<div class="form-group">
							<label for="last_name"><?= $this->lang->line('last_name') ?></label>
							<input type="text" class="form-control" id="last_name" name="last_name" placeholder="<?= $this->lang->line('last_name') ?>">
						</div>
						<div class="form-group">
							<label for="email"><?= $this->lang->line('email') ?></label>
							<input type="email" class="form-control" id="email" name="email" placeholder="<?= $this->lang->line('email') ?>" required>
						</div>
						<div class="form-group">
							<label for="phone"><?= $this->lang->line('phone_number') ?></label>
							<input type="phone" class="form-control" id="phone" name="phone_number" placeholder="<?= $this->lang->line('phone_number') ?>" required>
						</div>
						<div class="form-group">
							<label for="occupation"><?= $this->lang->line('occupation') ?></label>
							<input type="text" class="form-control" id="occupation" name="occupation" placeholder="<?= $this->lang->line('occupation') ?>">
						</div>
						<div class="form-group">
							<label for="remark"><?= $this->lang->line('remark') ?></label>
							<textarea type="text" class="form-control" id="remark" name="remark" rows="3" placeholder="<?= $this->lang->line('remark') ?>"></textarea>
						</div>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-outline btn-primary"><?= $this->lang->line('save') ?> <i class="fa fa-check"></i></button>
			</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<div class="modal modal-primary fade" id="addMessage">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line('add_message') ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Input addon -->
				<?= form_open(site_url('dashboard/message_add'), array('class' => 'form form_vertical', 'role' => 'form')) ?>
				<div class="box box-info">
					<div class="box-header with-border"></div>
					<div class="box-body">
						<div class="form-group">
							<label for="message_recipients" class="col-form-label"><?= $this->lang->line('message_recipients') ?> </label>
							<select class="form-control message_recipients client-select2 select2-danger" id="message_recipients" name="message_recipients[]" data-dropdown-css-class="select2-danger" style="width: 100%;" multiple>
								<?php foreach ($clients as $client) : ?>
									<option value=<?= $client['client_id'] ?>><?= $client['first_name'] . " " . $client['last_name'] ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<div class="form-group">
							<label for="message_subject" class="col-form-label"><?= $this->lang->line('message_subject') ?> </label>
							<input type="text" class="form-control" id="message_subject" name="message_subject" placeholder="<?= $this->lang->line('message_subject') . ' Du Message' ?>" required>
						</div>

						<div class="form-group">
							<label for="message_content"><?= $this->lang->line('message') ?></label>
							<textarea type="text" class="form-control" id="message_content" name="message_content" rows="3" placeholder="<?= 'Votre ' . $this->lang->line('message') ?>"></textarea>
						</div>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-outline btn-primary"><?= $this->lang->line('save') ?> <i class="fa fa-check"></i></button>
			</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<!-- Add new rdv-->
<div class="modal modal-primary fade" id="addRdv">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line('add_rdv') ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Input addon -->
				<?= form_open(site_url('dashboard/rdv_add'), array('class' => 'form form_vertical', 'role' => 'form')) ?>
				<div class="box box-info">
					<div class="box-header with-border"></div>
					<div class="box-body">
						<div class="form-group">
							<label for="rdv_client" class="col-form-label"><?= $this->lang->line('name') ?> </label>
							<select class="form-control client-select2 select2-danger" id="rdv_client" name="rdv_client_id" data-dropdown-css-class="select2-danger" style="width: 100%;">
								<?php foreach ($clients as $client) : ?>
									<option value=<?= $client['client_id'] ?>><?= $client['first_name'] . " " . $client['last_name'] ?></option>
								<?php endforeach ?>
							</select>
						</div>

						<div class="form-group">
							<label from="rdv_date" class="col-form-label"><?= $this->lang->line('rdv_date') ?></label>
							<div class="input-group">
								<input class="form-control" type="date" name="rdv_date" value="2020-08-19" id="example-date-input">
							</div>
							<!-- /.input group -->
						</div>

						<div class="bootstrap-timepicker">
							<div class="form-group">
								<label for="rdv_time" class="col-form-label"><?= $this->lang->line('rdv_time') ?></label>
								<div class="input-group date" id="timepicker" data-target-input="nearest">
									<input class="form-control" name="rdv_time" type="time" value="12:00:00" id="example-time-input">
								</div>
								<!-- /.input group -->
							</div>
							<!-- /.form group -->
						</div>

					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-outline btn-primary"><?= $this->lang->line('save') ?> <i class="fa fa-check"></i></button>
			</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>


<!-- Add new rdv-->
<div class="modal modal-primary fade" id="addDocument">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line('add_document') ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Input addon -->
				<?= form_open_multipart('dashboard/document_add', array('class' => 'form form_vertical', 'role' => 'form')) ?>
				<div class="box box-info">
					<div class="box-header with-border"></div>
					<div class="box-body">
						<div class="form-group">
							<label for="document_name" class="col-form-label"><?= $this->lang->line('document_name') ?> </label>
							<div class="custom-file">
								<input type="file" name="document_name" class="custom-file-input" id="document_name" accept=".pdf,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
								<label class="custom-file-label" for="customFile">Choisir un document</label>
							</div>
						</div>

						<div class="form-group">
							<label from="document_type" class="col-form-label"><?= $this->lang->line('documents_type') ?></label>
							<input type="text" class="form-control" id="document_type" name="document_type" placeholder="<?= $this->lang->line('documents_type') ?>">
							<!-- /.input group -->
						</div>

					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-outline btn-primary"><?= $this->lang->line('save') ?> <i class="fa fa-check"></i></button>
			</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<!-- jQuery -->
<script src="<?= base_url('resources/') ?>plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?= base_url('resources/') ?>plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
	$.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?= base_url('resources/') ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="<?= base_url('resources/') ?>plugins/select2/js/select2.full.min.js"></script>
<!-- daterangepicker -->
<script src="<?= base_url('resources/') ?>plugins/moment/moment.min.js"></script>
<script src="<?= base_url('resources/') ?>plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= base_url('resources/') ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?= base_url('resources/') ?>plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?= base_url('resources/') ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url('resources/') ?>plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="<?= base_url('resources/') ?>plugins/toastr/toastr.min.js"></script>
<!-- DataTables -->
<script src="<?= base_url('resources/') ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url('resources/') ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url('resources/') ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('resources/') ?>dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?= base_url('resources/') ?>dist/js/demo.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>


<script>
	$(".custom-file-input").on("change", function() {
		var fileName = $(this).val().split("\\").pop();
		$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
	});
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#calenda').fullCalendar({
			editable: true,
			header: {
				left: 'prev, next, today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			
		});
	});
</script>
<script>
	$(function() {
		$('#franchises').DataTable({
			"paging": true,
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": false,
			"responsive": true,
			"columnDefs": [{
				"targets": [5],
				"visible": true,
				"searchable": false
			}],
			"language": {
				"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
			},
			dom: 'lBfrtip',
			buttons: [
				'excel', 'pdf'
			],
			"lengthMenu": [
				[10, 25, 50, -1],
				[10, 25, 50, "All"]
			]
		});
		$('#campaigns').DataTable({
			"paging": true,
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": false,
			"responsive": true,
			"columnDefs": [{
				"targets": [6],
				"visible": true,
				"searchable": false
			}],
			"language": {
				"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
			},
			dom: 'lBfrtip',
			buttons: [
				'excel', 'pdf'
			],
			"lengthMenu": [
				[10, 25, 50, -1],
				[10, 25, 50, "All"]
			]
		});
		$('#commissions').DataTable({
			"paging": true,
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": false,
			"responsive": true,
			"columnDefs": [{
				"targets": [6],
				"visible": true,
				"searchable": false
			}],
			"language": {
				"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
			},
			dom: 'lBfrtip',
			buttons: [
				'excel', 'pdf'
			],
			"lengthMenu": [
				[10, 25, 50, -1],
				[10, 25, 50, "All"]
			]
		});

		$('.client-select2').select2({
			placeholder: '<?= $this->lang->line('client_category') ?>',
			tags: true,
			allowClear: true
		})
		$('.comm-type-select2').select2({
			placeholder: '<?= $this->lang->line('comm_type') ?>'
		})
		$('.comm-currency-select2').select2({
			placeholder: '<?= $this->lang->line('comm_currency') ?>'
		})
		$('.permission-select2').select2({
			placeholder: '<?= $this->lang->line('permission') ?>',
			tags: true,
			allowClear: true
		})
		$('.comm-tax-select2').select2({
			placeholder: '<?= $this->lang->line('tax') ?>',
			tags: false
		})
		//Date range picker
		$('#reservation').daterangepicker()
		//Date range picker with time picker
		$('#campaign_period').daterangepicker({
			timePicker: true,
			timePickerIncrement: 5,
			timePicker24Hour: true,
			startDate: moment().startOf('hour'),
			timePickerSeconds: true,
			showDropdowns: true,
			minDate: moment(),
			locale: {
				format: 'YYYY-MM-DD HH:mm:ss'
			}
		})
		$('#campaign_period_edit').daterangepicker({
			timePicker: true,
			timePickerIncrement: 5,
			timePicker24Hour: true,
			startDate: '<?= $campaign['start_date'] ?? '' ?> <?= $campaign['start_time'] ?? '' ?>',
			timePickerSeconds: true,
			endDate: '<?= $campaign['end_date'] ?? '' ?> <?= $campaign['end_time'] ?? '' ?>',
			showDropdowns: true,
			minDate: moment(),
			locale: {
				format: 'YYYY-MM-DD HH:mm:ss'
			}
		})

		$('#documents').DataTable({
			"paging": true,
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": false,
			"responsive": true,
			"columnDefs": [{
				"targets": [5],
				"visible": true,
				"searchable": false
			}],
			"language": {
				"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
			},
			dom: 'lBfrtip',
			buttons: [
				'excel', 'pdf'
			],
			"lengthMenu": [
				[10, 25, 50, -1],
				[10, 25, 50, "All"]
			]
		});
		$('#commissions').DataTable({
			"paging": true,
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": false,
			"responsive": true,
			"columnDefs": [{
				"targets": [6],
				"visible": true,
				"searchable": false
			}],
			"language": {
				"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
			},
			dom: 'lBfrtip',
			buttons: [
				'excel', 'pdf'
			],
			"lengthMenu": [
				[10, 25, 50, -1],
				[10, 25, 50, "All"]
			]
		});

		$('.campaign-client-select2').select2({
			placeholder: '<?= $this->lang->line('client') ?>',
			tags: true
		})

		<?php if (($this->session->flashdata('success'))) : ?>
			toastr.success('<?= $this->session->flashdata('success') ?>.', '', {
				closeButton: true,
				progressBar: true
			})
		<?php endif; ?>
		<?php if (($this->session->flashdata('error'))) : ?>
			var message = (`<?= ($this->session->flashdata('error')) ?>`);
			toastr.error(message, '', {
				closeButton: true,
				progressBar: true
			});
		<?php endif; ?>
		<?php if (($this->session->flashdata('info'))) : ?>
			var message = (`<?= ($this->session->flashdata('info')) ?>.`)
			toastr.info(message, '', {
				closeButton: true,
				progressBar: true
			});
		<?php endif; ?>
		<?php if (($this->session->flashdata('warning'))) : ?>
			toastr.warning(`<?= $this->session->flashdata('warning') ?>.`, '', {
				closeButton: true,
				progressBar: true
			});
		<?php endif; ?>
	});
</script>
</body>

</html>
