<!-- Modal -->
<div class="modal fade" id="editConfigIndependentModal" tabindex="-1" role="dialog" aria-labelledby="editConfigIndependentModal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form method="post" action="">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Add/Edit Independent Config</h4>
				</div>

				<div class="modal-body">
					<table class="table table-striped">
						<tr>
							<th>Thingspeak Channel ID</th>
							<td data-field="Channel_id">
								<input type="text" name="Channel_id" class="form-control"/>
							</td>
						</tr>
						<tr>
							<th>Site Name</th>
							<td data-field="name">
								<input type="text" name="name" class="form-control"/>
							</td>
						</tr>
						<tr>
							<th>Maker</th>
							<td data-field="Maker">
								<input type="text" name="Maker" class="form-control"/>
							</td>
						</tr>
						<tr>
							<th>Active</th>
							<td data-field="active">
								<label class="radio-inline">
									<input type="radio" name="active" value="true" />
									<span class="glyphicon glyphicon-ok"></span> Enable
								</label>
								<label class="radio-inline">
									<input type="radio" name="active" value="false" />
									<span class="glyphicon glyphicon-remove"></span> Disable
								</label>
							</td>
						</tr>
						<tr>
							<th>Field Mapping</th>
							<td data-field="Option">
								<table class="table" style="margin-bottom: 0">
									<tr>
										<th>Temperature</th>
										<td data-field-mapping="Temperature">
											<input type="text" name="option_Temperature" class="form-control"/>
										</td>
									</tr>
									<tr>
										<th>Humidity</th>
										<td data-field-mapping="Humidity">
											<input type="text" name="option_Humidity" class="form-control"/>
										</td>
									</tr>
									<tr>
										<th>PM 2.5</th>
										<td data-field-mapping="Dust2_5">
											<input type="text" name="option_Dust2_5" class="form-control"/>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>

				<input type="hidden" name="configType" value="independent">
				<input type="hidden" name="mode" value="">
				<input type="hidden" name="key" value="">
			</form>
		</div>
	</div>
</div>