<?php require("bootstrap.php"); ?>
<!DOCTYPE html>
<html lang="">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<meta property="og:title" content="g0v台灣空汙地圖資料來源管理">
		<meta property="og:description" content="Taiwan Air Pollution Map">
		<meta property="og:type" content="website">
		<meta property="og:url" content="http://datasource.airmap.asper.tw/">
		<meta property="og:image" content="https://i.imgur.com/AuINEkK.png">

		<title data-lang="pageTitle">g0v台灣空汙地圖資料來源管理</title>
		<link rel='shortcut icon' type='image/x-icon' href='https://i.imgur.com/Gro4juQ.png' />

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

		<link rel="stylesheet" href="css/login-modal.css">
		<link rel="stylesheet" href="css/admin.css">

		<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	</head>
	<body>
		<?php !isset($_SESSION['loginUser']) && include("partials/login-form.php"); ?>

		<?php if(isset($_SESSION['loginUser'])) :?>
			<?php include("partials/alert.php"); ?>
			<?php include("partials/navbar.php"); ?>
			<?php include("partials/config-table.php"); ?>
			<?php include("partials/user-edit-form.php"); ?>
		<?php endif; ?>

		<hr />
		<div id="footer" style="font-size: 0.85em; text-align: center;">
			Asper &copy; 2017
		</div>

		<!-- <script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-55384149-4', 'auto');
			ga('send', 'pageview');
		</script> -->
	</body>
</html>