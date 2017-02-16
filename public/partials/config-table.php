<?php
	require("bootstrap.php");
	use Asper\Datasource\Independent;
	use Asper\Datasource\ProbeCube;
	use Asper\User;

	$configIndependent = (new Independent())->loadConfig();
	$configProbeCube = (new ProbeCube())->loadConfig();
	$configUser = (new User())->loadConfig();
?>
<div>	
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active">
			<a href="#config-independent" aria-controls="config-independent" role="tab" data-toggle="tab">Independent</a>
		</li>
		<li role="presentation">
			<a href="#config-probecube" aria-controls="config-probecube" role="tab" data-toggle="tab">Probecube</a>
		</li>
		<li role="presentation">
			<a href="#config-user" aria-controls="config-user" role="tab" data-toggle="tab">User</a>
		</li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" data-config-type="independent" id="config-independent">
			<?php if( permissionAccept(['operator', 'admin']) ): ?>
			<p class="text-right">
				<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#editConfigIndependentModal" data-action="add">Add New Site</a>
			</p>
			<?php endif; ?>

			<table class="table table-striped">
				<thead>
					<tr>
						<th>No.</th>
						<th>Thingspeak Channel ID</th>
						<th>Site Name</th>
						<th>Maker</th>
						<th>Active</th>
						<th>Update at</th>
						<th>Func</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($configIndependent as $index => $config):?>
					<tr data-config='<?php echo json_encode($config);?>'>
						<td class="text-center"><?php echo $index+1; ?></td>
						<td class="text-center">
							<a target="thingspeak"
								href="https://thingspeak.com/channels/<?php echo $config['Channel_id']; ?>">
								<?php echo $config['Channel_id']; ?>
							</a>
						</td>
						<td><?php echo $config['name']; ?></td>
						<td><?php echo $config['Maker']; ?></td>
						<td class="text-center">
							<?php if($config['active']): ?>
								<span class="glyphicon glyphicon-ok"></span>
							<?php else: ?>
								<span class="glyphicon glyphicon-remove"></span>
							<?php endif; ?>
						</td>
						<td class="text-center"><?php echo $config['update_at']; ?></td>
						<td class="text-center">
							<a href="#" class="btn btn-sm btn-success" 
							   data-action="fetchChannelInfo" data-key="<?php echo $config['Channel_id']; ?>">
							   Fetch Channel Info
							</a>

							<?php if( permissionAccept(['operator', 'admin']) ): ?>
							<a href="#" class="btn btn-sm btn-warning" 
							   data-toggle="modal" data-target="#editConfigIndependentModal" data-action="edit">
							   Edit
							</a>
							<?php endif; ?>

							<?php if( permissionAccept('admin') ): ?>
							<a href="#" class="btn btn-sm btn-danger" 
							   data-action="delete" data-key="<?php echo $config['Channel_id']; ?>">
							   Delete
							</a>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div role="tabpanel" class="tab-pane" data-config-type="probecube" id="config-probecube">
			<?php if( permissionAccept(['operator', 'admin']) ): ?>
			<p class="text-right">
				<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#editConfigProbecubeModal" data-action="add">Add New Site</a>
			</p>
			<?php endif; ?>

			<table class="table table-striped">
				<thead>
					<tr>
						<th>No.</th>
						<th>Channel ID</th>
						<th>Maker</th>
						<th>Active</th>
						<th>Update at</th>
						<th>Func</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($configProbeCube as $index => $config):?>
					<tr data-config='<?php echo json_encode($config);?>'>
						<td class="text-center"><?php echo $index+1; ?></td>
						<td class="text-center">
							<a target="thingspeak"
								href="https://thingspeak.com/channels/<?php echo $config['Channel_id']; ?>">
								<?php echo $config['Channel_id']; ?>
							</a>
						</td>
						<td><?php echo $config['maker']; ?></td>
						<td class="text-center">
							<?php if($config['active']): ?>
								<span class="glyphicon glyphicon-ok"></span>
							<?php else: ?>
								<span class="glyphicon glyphicon-remove"></span>
							<?php endif; ?>
						</td>
						<td><?php echo $config['update_at']; ?></td>
						<td class="text-center">
							<a href="#" class="btn btn-sm btn-success" 
							   data-action="fetchChannelInfo" data-key="<?php echo $config['Channel_id']; ?>">
							   Fetch Channel Info
							</a>

							<?php if( permissionAccept(['operator', 'admin']) ): ?>
							<a href="#" class="btn btn-sm btn-warning" 
								data-toggle="modal" data-target="#editConfigProbecubeModal" data-action="edit">
								Edit
							</a>
							<?php endif; ?>

							<?php if( permissionAccept('admin') ): ?>
							<a href="#" class="btn btn-sm btn-danger"
								data-action="delete" data-key="<?php echo $config['Channel_id']; ?>">
								Delete
							</a>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div role="tabpanel" class="tab-pane" id="config-user">
			<?php include("user-config-tab.php"); ?>
		</div>

		<pre id="jsonDisplay" style="display: none;"></pre>
	</div>

	<?php include("independent-edit-modal.php"); ?>
	<?php include("probecube-edit-modal.php"); ?>

	<script>
		function fillField($input, value){
			if( $input.is(":text") ){
				$input.val(value);
				return;
			}

			if( $input.is(":radio") ){
				var checkedValue = value ? "on" : "off";
				$input.removeProp('checked')
					  .filter("[value='" + checkedValue + "']")
					  	.prop('checked', true);
				return;
			}
		}

		$(".tab-pane a[data-action]").click(function(){
			var action = $(this).data('action');
			var configType = $(this).parents('.tab-pane').data('config-type');

			var $modal;
			if(configType == "independent"){ $modal = $("#editConfigIndependentModal"); }
			if(configType == "probecube"){ $modal = $("#editConfigProbecubeModal"); }

			$modal.find("input[name=mode]").val(action);
			$modal.find("form:first").attr('action', '/config?op=' + action);

			switch(action){
				case 'add':
					$modal.find("td[data-field]").each(function(){
						var $input = $(this).find("input");
						fillField($input, '');
					});
					break;
				case 'edit':
					var config = $(this).parents('tr').data('config');

					$modal.find("td[data-field]").each(function(){
						var field = $(this).data('field');
						var $input = $(this).find("input");
						var value = config[field];

						if(field == "Channel_id"){
							$modal.find("input[name=key]").val(value);
						}

						if(field == "Option"){
							for(var name in config[field]){
								$(this).find("td[data-field-mapping=" + value[name] + "] input")
									   .val(name);
							}
							return;
						}

						fillField($input, value);
					});
					break;
				case 'delete':
					var key = "" + $(this).data('key');
					if(!key.length){ return false; }

					if( !confirm("Are you sure?") ){ return false; }

					$.post("/config?op=delete", {configType: configType, key: key}, function(res){
						if(res == 'true'){ location.reload(); }
					});
					return false;
					break;
				case 'fetchChannelInfo':
					var id = $(this).data('key');
					if(!id){ return false; }

					var $tr = $(this).parents('tr');
					var showJson = function(json){
						$tr.after([
							'<tr>',
								'<td></td>',
								'<td colspan="6">',
								'<pre style="display: none;">',
									'<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>',
									JSON.stringify(json, null, '\t'),
								'</pre>',
								'</td>',
							'</tr>'
						].join(''));
						
						$tr.next('tr').find("pre").slideDown();
					}

					var urlTemplate = "https://api.thingspeak.com/channels/{{id}}/feeds.json?results=1";
					var url = urlTemplate.replace('{{id}}', id);
					$.getJSON(url, function(data){
						showJson(data);
					}).fail(function(jqxhr, textStatus, error) {
						showJson(jqxhr);
					});
					return false;
					break;
			}
		});	

		$(".tab-pane").on("click", ".close", function(){
			$(this).parents('tr').find("pre").slideUp(function(){
				$(this).parents('tr').remove();
			});
		});
	</script>

</div>