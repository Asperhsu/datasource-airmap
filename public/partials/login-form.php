
<div class="login-form">
	<div class="modal-dialog">
		<div class="loginmodal-container">
			<h1>Login to Your Account</h1><br>
			<form method="post" action="/user?op=login">
				<input type="text" name="user" placeholder="Username">
				<input type="password" name="pass" placeholder="Password">
				<input type="submit" name="login" class="login loginmodal-submit" value="Login">
			</form>

			<div class="login-help">
				<?php echo isset($_SESSION['loginErrorMsg']) ? $_SESSION['loginErrorMsg'] : ''; ?>
			</div>
		</div>
	</div>
</div>