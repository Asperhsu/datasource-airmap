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
		<div role="tabpanel" class="tab-pane active" id="config-independent">
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
						<td class="text-center"><?php echo $config['Channel_id']; ?></td>
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
		<div role="tabpanel" class="tab-pane" id="config-probecube">
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
						<td class="text-center"><?php echo $config['Channel_id']; ?></td>
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
				var checkedValue = value ? "true" : "false";
				$input.removeProp('checked')
					  .filter("[value='" + checkedValue + "']")
					  	.prop('checked', true);
				return;
			}
		}

		$("#config-independent a[data-action]").click(function(){
			var action = $(this).data('action');
			var $modal = $("#editConfigIndependentModal");
			$modal.find("input[name=mode]").val(action);
			$modal.find("form:first").attr('action', '/config?op=' + action);

			if(action == 'add'){
				var $input = $modal.find("input:not([type=hidden])");
				fillField($input, '');
			}

			if(action == "edit"){
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
			}

			if(action == "delete"){
				var key = "" + $(this).data('key');
				if(!key.length){ return false; }

				if( !confirm("Are you sure?") ){ return false; }

				$.post("/config?op=delete", {configType: 'independent', key: key}, function(res){
					if(res == 'true'){ location.reload(); }
				});
				return false;				
			}
		});

		$("#config-probecube a[data-action]").click(function(){
			var action = $(this).data('action');
			var $modal = $("#editConfigProbecubeModal");
			$modal.find("input[name=mode]").val(action);
			$modal.find("form:first").attr('action', '/config?op=' + action);

			if(action == 'add'){
				var $input = $modal.find("input:not([type=hidden])");
				fillField($input, '');
			}

			if(action == "edit"){
				var config = $(this).parents('tr').data('config');

				$modal.find("td[data-field]").each(function(){
					var field = $(this).data('field');
					var $input = $(this).find("input");
					var value = config[field];

					if(field == "Channel_id"){
						$modal.find("input[name=key]").val(value);
					}

					fillField($input, value);
				});
			}

			if(action == "delete"){
				var key = "" + $(this).data('key');
				if(!key.length){ return false; }

				if( !confirm("Are you sure?") ){ return false; }

				$.post("/config?op=delete", {configType: 'probecube', key: key}, function(res){
					if(res == 'true'){ location.reload(); }
				});
				return false;				
			}
		});

		
	</script>

</div>