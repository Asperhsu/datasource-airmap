<div id="navbar" class="row well well-sm" style="height:50px">
	<div class="col-sm-2">
		<img src="https://i.imgur.com/IWqy7yh.png" class="img-responsive" alt="Image">
	</div>
	<div class="col-sm-10 text-right">
		<button class="btn">
			<span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION['loginUser']; ?>
		</button>

		<a href="/user?op=logout" class="btn btn-warning" title='Logout'>
			<span class="glyphicon glyphicon-off"></span>
		</a>
	</div>
</div>

<script>
	$("#navbar .btn[data-action=edit]").click(function(){
		var username = '<?php echo $_SESSION['loginUser']; ?>';
		var permission = '<?php echo $_SESSION['loginPermission']; ?>';
		$("#modifyUserModal")
			.find("form:first").attr('action', '/user?op=edit').end()
			.find(".modal-title").text("Change Password").end()
			.find("input[name=user]")
				.val(username)
				.attr('disabled', true)
				.end()
			.find("input[name=pass]").val('').end()
			.find("input[name=power]").removeProp('checked')
				.filter("[value='" + permission + "']")
				.prop('checked', true);
	});
</script>