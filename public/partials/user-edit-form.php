<div class="modal" id="modifyUserModal" tabindex="-1" role="dialog" aria-labelledby="modifyUserModal">
	<div class="modal-dialog" role="document">
		<div class="loginmodal-container">
			<h1 class="modal-title"></h1><br>
			<form method="post" action="">
				<input type="text" class="form-control" name="user" placeholder="Username">
				<input type="password" class="form-control" name="pass" placeholder="Password">
				
				<label class="radio-inline" title="user can only view config">
					<input type="radio" name="power" value="viewer"> Viewer
				</label>

				<label class="radio-inline" title="user can edit config, but can't delete">
					<input type="radio" name="power" value="operator"> Operator
				</label>

				<label class="radio-inline" title="user can edit and delete config">
					<input type="radio" name="power" value="admin"> Admin
				</label>

				<hr/>

				<input type="submit" name="login" class="login loginmodal-submit" value="Submit">
			</form>
		</div>
	</div>
</div>