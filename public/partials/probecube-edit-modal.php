<!-- Modal -->
<div class="modal fade" id="editConfigProbecubeModal" tabindex="-1" role="dialog" aria-labelledby="editConfigProbecubeModal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form method="post" action="">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Add/Edit ProbeCube Config</h4>
				</div>

				<div class="modal-body">
					<table class="table table-striped">
						<tr>
							<th>Thingspeak Channel ID</th>
							<td data-field="Channel_id">
								<input type="text" name="Channel_id" class="form-control" required/>
							</td>
						</tr>
						<tr>
							<th>Maker</th>
							<td data-field="maker">
								<input type="text" name="maker" class="form-control" required/>
							</td>
						</tr>
						<tr>
							<th>Active</th>
							<td data-field="active">
								<label class="radio-inline">
									<input type="radio" name="active" value="on" />
									<span class="glyphicon glyphicon-ok"></span> Enable
								</label>
								<label class="radio-inline">
									<input type="radio" name="active" value="off" />
									<span class="glyphicon glyphicon-remove"></span> Disable
								</label>
							</td>
						</tr>
					</table>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>

				<input type="hidden" name="configType" value="probecube">
				<input type="hidden" name="mode" value="">
				<input type="hidden" name="key" value="">
			</form>
		</div>
	</div>
</div>