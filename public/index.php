<?php 
$urlRoot = sprintf("%s://%s", 
				$_SERVER['HTTPS']=="off" ? "http": "https",
				$_SERVER['HTTP_HOST']
			);
?>
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

		<link rel="stylesheet" href="css/admin.css">
		<link rel="stylesheet" href="css/login-modal.css">

		<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	</head>
	<body>
		<div id="navbar" class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<img src="https://i.imgur.com/IWqy7yh.png" class="img-responsive" alt="Image">
			</div>
		</div>

		<div class="row">
			<div class="col-sm-10 col-sm-offset-1">
				<div class="panel panel-default">
					<div class="panel-heading">Datasource (JSON Format)</div>
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Site Type</th>
								<th>JSON Resource</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>All in One</td>
								<td>
									<a href="<?php echo $urlRoot;?>/airmap.json">
										<?php echo $urlRoot;?>/airmap.json
									</a>
								</td>
							</tr>
							<tr>
								<td>LASS</td>
								<td>
									<a href="<?php echo $urlRoot;?>/lass.json">
										<?php echo $urlRoot;?>/lass.json
									</a>
								</td>
							</tr>
							<tr>
								<td>LASS 4U</td>
								<td>
									<a href="<?php echo $urlRoot;?>/lass4u.json">
										<?php echo $urlRoot;?>/lass4u.json
									</a>
								</td>
							</tr>
							<tr>
								<td>Edimax Airbox</td>
								<td>
									<a href="<?php echo $urlRoot;?>/edimax-airbox.json">
										<?php echo $urlRoot;?>/edimax-airbox.json
									</a>
								</td>
							</tr>
							<tr>
								<td>Asus Airbox</td>
								<td>
									<a href="<?php echo $urlRoot;?>/asus-airbox.json">
										<?php echo $urlRoot;?>/asus-airbox.json
									</a>
								</td>
							</tr>
							<tr>
								<td>EPA</td>
								<td>
									<a href="<?php echo $urlRoot;?>/epa.json">
										<?php echo $urlRoot;?>/epa.json
									</a>
								</td>
							</tr>
							<tr>
								<td>G0V Independent site</td>
								<td>
									<a href="<?php echo $urlRoot;?>/independent.json">
										<?php echo $urlRoot;?>/independent.json
									</a>
								</td>
							</tr>
							<tr>
								<td>G0V ProbeCube site</td>
								<td>
									<a href="<?php echo $urlRoot;?>/probecube.json">
										<?php echo $urlRoot;?>/probecube.json
									</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		
		<div class="row text-center">
			<div class="col-sm-4 col-sm-offset-4">
				<h2>Warning!! staff only</h2>
				<a href='/admin' class="btn btn-lg btn-danger btn-block">Admin</a>
			</div>
		</div>


		<hr />
		<div id="footer" style="font-size: 0.85em; text-align: center;">
			Asper &copy; 2017
		</div>		
	</body>
</html>