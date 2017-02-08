<?php if( permissionAccept('admin') ): ?>
<p class="text-right">
	<a href="#" class="btn btn-primary" 
		data-toggle="modal" data-target="#modifyUserModal" data-action="add">
		Add New User
	</a>
	<a href="#" class="btn btn-success" 
		data-toggle="modal" data-target="#modifyUserModal" data-action="chgpasswd">
		Change My Password
	</a>
</p>
<?php endif; ?>

<table class="table table-striped">
	<thead>
		<tr>
			<th>No.</th>
			<th>UserName</th>
			<th>Permission</th>
			<th>Func</th>
		</tr>
	</thead>
	<tbody>
		<?php $i = 0; foreach($configUser as $name => $config): ?>
		<tr>
			<td class="text-center"><?php echo $i+1; ?></td>
			<td class="text-center"><?php echo $name; ?></td>
			<td class="text-center"><?php echo ucfirst($config['permission']); ?></td>
			<td class="text-center">
				<?php if($_SESSION['loginUser'] != $name && $_SESSION['loginPermission'] == 'admin'): ?>
					<a href="#" class="btn btn-sm btn-warning" 
					   data-action="edit" 
					   data-toggle="modal" data-target="#modifyUserModal"
					   data-username="<?php echo $name;?>" data-permission="<?php echo $config['permission'];?>">
					   Edit
					</a>

					<?php if($name != 'admin'): ?>
					<a href="#" class="btn btn-sm btn-danger" 
					   data-action="delete" data-username="<?php echo $name;?>">
					   Delete
					</a>
					<?php endif;?>
				<?php endif;?>
			</td>
		</tr>
		<?php $i++; endforeach; ?>
	</tbody>
</table>

<script>
	function setUserModalField(userOptions){
		var defaultOptions = {
			username: 			'<?php echo $_SESSION['loginUser']; ?>',
			usernameDisable: 	true,
			permission: 		'<?php echo $_SESSION['loginPermission']; ?>',
			permissionDisable: 	true,
			formAction: 		'',
			title: 				'',
		};

		var option = $.extend({}, defaultOptions, userOptions || {});

		$("#modifyUserModal")
			.find("form:first").attr('action', '/user?op=' + option.formAction).end()
			.find(".modal-title").text(option.title).end()
			.find("input[name=user]").val(option.username).attr('readonly', option.usernameDisable).end()
			.find("input[name=pass]").val('').end()
			.find("input[name=power]")
				.removeProp('checked').prop('disabled', option.permissionDisable)
				.filter("[value='" + option.permission + "']").prop('checked', true)
				.end().end();
	}

	$('#config-user a[data-action]').click(function(e){
		var action = $(this).data('action');

		if(action == "add"){
			setUserModalField({ 
				username: 			"",
				usernameDisable: 	false,
				permission: 		"viewer",
				permissionDisable: 	false,
				formAction: 		"addUser",
				title: 				"Add User",
			});
		}

		if(action == "edit"){
			setUserModalField({ 
				username: 	$(this).data('username'),
				permission: $(this).data('permission'),
				permissionDisable: 	false,
				formAction: "edit",
				title: 		"Change Password",
			});
		}

		if(action == "chgpasswd"){
			setUserModalField({
				formAction: "chgpasswd",
				title: 		"Change My Password",
			});
		}

		if(action == "delete"){
			var username = $(this).data('username');
			if(!username.length){ return false; }

			if( !confirm("Are you sure?") ){ return false; }

			$.post("/user?op=delete", {user: username}, function(res){
				if(res == 'true'){ location.reload(); }
			});				
		}
	});
</script>