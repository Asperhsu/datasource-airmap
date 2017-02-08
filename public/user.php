<?php
require("bootstrap.php");

use Asper\User;
$User = new User();

switch($_GET['op']){
	case 'login':
		$result = $User->login($_POST['user'], $_POST['pass']);
		if($result){
			$_SESSION['loginUser'] = $_POST['user'];
			$_SESSION['loginPermission'] = $User->getPermission($_SESSION['loginUser']);
		}else{
			$_SESSION['loginErrorMsg'] = "UserName or Password not correct";
		}
		header("location: /admin");
		break;
	case 'logout':
		unset($_SESSION['loginUser'], $_SESSION['loginPermission'], $_SESSION['loginErrorMsg']);
		header("location: /admin");
		break;
	case 'addUser':
		if( !permissionAccept('admin') ){
			header("HTTP/1.0 550 Permission Denied");
		}

		$user =  strlen($_POST['user'])  ? $_POST['user']  : null;
		$pass =  strlen($_POST['pass'])  ? $_POST['pass']  : null;
		$power = strlen($_POST['power']) ? $_POST['power'] : null;
		$result = $User->addUser($user, $pass, $power);

		showAlert(
			$result ? 'success' : 'danger', 
			$result ? 'Add User Success' : 'Add User Failed'
		);

		header("location: /admin");
		break;
	case 'chgpasswd':
		$pass =  strlen($_POST['pass'])  ? $_POST['pass']  : null;
		$User->edit($_SESSION['loginUser'], $pass);

		unset($_SESSION['loginUser'], $_SESSION['loginErrorMsg']);
		header("location: /admin");
		break;
	case 'edit':
		if( !permissionAccept('admin') ){
			header("HTTP/1.0 550 Permission Denied");
		}

		$username = $_POST['user'];
		$pass =  strlen($_POST['pass'])  ? $_POST['pass']  : null;
		$power = strlen($_POST['power']) ? $_POST['power'] : null;
		$result = $User->edit($username, $pass, $power);

		showAlert(
			$result ? 'success' : 'danger', 
			sprintf("Edit User: %s %s", $username, $result ? 'Success' : 'Failed')
		);
		header("location: /admin");
		break;
	case 'delete':
		if( !permissionAccept('admin') ){
			header("HTTP/1.0 550 Permission Denied");
		}

		echo $User->delete($_POST['user']) ? 'true' : 'false';
		break;
}