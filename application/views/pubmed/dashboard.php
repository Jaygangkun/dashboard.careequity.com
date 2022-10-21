<div id="dashboard_pubmed">
	<div class="report-add-area">
		<div class="container">
			<h3 class="area-title">Pubmed Search Parameters</h3>
			<div class="report-input-row">
				<div class="report-input-col">
					<div class="report-input-col-row report-input-col-row--title">
						<div class="report-input-col-row-50">
							<input type="text" class="report-input" id="title" placeholder="+ Enter report title">
						</div>

						<div class="report-input-col-row-50 report-input-col-row--conditions">
							<select class="report-input" id="field">
								<?php
								if (isset($fields)) {
									foreach ($fields as $field) {
								?>
										<option value="<?php echo $field['value'] ?>"><?php echo $field['text'] ?></option>
								<?php
									}
								}
								?>
							</select>
							<!--	<input type="text" class="report-input" id="conditions" placeholder="+ Enter condition or disease"> -->
						</div>
					</div>
					<div class="report-input-col-row">
						<div class="report-input-col-row-50 report-input-col-row--country">
							<div class="report-input-col-row-50">
								<input type="text" class="report-input" id="term" placeholder="+ Search term">
							</div>
							<div class="report-input-col-row-50">
								<select class="report-input" id="plus">
									<?php
									if (isset($plues)) {
										foreach ($plues as $plus) {
									?>
											<option value="<?php echo $plus['value'] ?>"><?php echo $plus['text'] ?></option>
									<?php
										}
									}
									?>
								</select>
							</div>
						</div>
						<div class="report-input-col-row-50">
							<input type="text" class="report-input" id="parameter" placeholder="+ Additional search parameter">
						</div>
					</div>
				</div>
				<div class="report-btn-col">
					<span class="btn-main" id="report_add_btn">+ Add</span>
				</div>
			</div>
		</div>
	</div>
	<div class="report-list-area">
		<div class="container">
			<div class="report-list-head">
				<div class="report-list-head-sort">
					<span class="sort-btn" id="sort_btn1" sort="ASC">
						<i class="material-icons">sort</i>Sort by:
					</span>
					<select class="" id="sort">
						<option value="az">Sort AZ</option>
						<option value="newold">Sort New to Old</option>
						<option value="oldnew">Sort Old to New</option>
					</select>
				</div>
				<div class="report-list-head-search">
					<div class="search-input-wrap">
						<input type="text" placeholder="Search" id="report_search_input">
						<i class="search-input-icon material-icons">search</i>
					</div>
				</div>
			</div>
			<div class="report-list-body" id="report_list">
				<?php
				if (isset($reports)) {
					foreach ($reports as $report) {

						$data = array();
						$data['report'] = $report;


						$this->view('pubmed/template/report-template', $data);
					}
				}
				?>
			</div>

			<!-- Trigger the modal with a button -->
			<button type="button" class="btn btn-info btn-lg show_modal_btn" data-toggle="modal" data-target="#dashboard_pubmed #myModal">Open Modal</button>

			<!-- Modal -->
			<div id="myModal" class="modal fade" role="dialog">
				<div class="modal-dialog">

					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Modal Header</h4>
						</div>
						<div class="modal-body">
							<div class="row graph-songs_wrap">
								<div class="col s-12 m-6">
									<p class="showing_line_title">Number of Reports per Week</p>
									<div class="graph-songs"></div>
								</div>

							</div>

							<div class="row graph-songs_total_wrap">
								<div class="col s-12 m-6">
									<p class="showing_line_title">Number of Reports per Week</p>
									<div class="graph-songs_total"></div>
								</div>

							</div>

							<div class="showing_line_bottom_wrap">
								<div>Older</div>
								<div>Last week</div>
							</div>

							<div class="change_display_method_wrap">
								<div class="change_display_cumulative active">Total Reports</div>
								<div class="change_display_daily">Daily</div>
							</div>


							<div class="date_picker_wrap">
								<div class="modal_header">Select custom date range</div>

								<div>
									<?php $attributes = 'id="start_date_pubmed" placeholder="Select Start Date"';
									echo form_input('start_date', set_value('start_date'), $attributes); ?>
										- 
									<?php $attributes = 'id="last_date_pubmed" placeholder="Select Last Date"';
									echo form_input('last_date', set_value('last_date'), $attributes); ?>
								</div>
								
							</div>

							<div class="set_third_days_btn">Last 30 Days</div>

							<div class="report_date-download-btn">
								<div class="modal_header">DOWNLOAD CSV</div>
								<div class="report-list-download-btn__icon-wrap"><i class="material-icons report_date-downlaod">file_download</i></div>
							</div>
							
						</div>

					</div>

				</div>
			</div>


			<!-- Trigger the modal with a button -->
			<button type="button" class="btn btn-info btn-lg show_popup_btn" data-toggle="modal" data-target="#dashboard_pubmed #myPopup">Open Modal</button>

			<!-- Modal -->
			<div id="myPopup" class="modal fade" role="dialog">
				<div class="modal-dialog">

					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="popup-title">Modal Header</h4>
						</div>
						<div class="modal-body">
							<div class="popup_updates_count">update</div>

							<table class="popup-table">
								<thead>
									<tr>
										<th style="width: 2%"></th>
										<th style="width: 15%">ID</th>
										<th style="width: 25%">Title</th>
										<th style="width: 45%">Description</th>
										<th style="width: 8%">Date</th>
										<th style="width: 5%"></th>
									</tr>
								</thead>

								<tbody class="popup_body">

								</tbody>

							</table>
						</div>

					</div>

				</div>
			</div>



		</div>




	</div>
</div>
<script src="<?= base_url() ?>assets/js/pubmed/app.js?v=<?php echo time() ?>"></script>
