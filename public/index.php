<?php 
$urlRoot = sprintf("%s://%s", 
				$_SERVER['HTTPS']=="off" ? "http": "https",
				$_SERVER['HTTP_HOST']
			);
$datasources = [
	'All in One' 		=> 'airmap',
	'LASS' 				=> 'lass',
	'LASS 4U' 			=> 'lass-4u',
	'LASS MAPS' 		=> 'lass-maps',
	'Edimax Airbox' 	=> 'edimax-airbox',
	'Asus Airbox' 		=> 'asus-airbox',
	'EPA' 				=> 'epa',
	'G0V Independent' 	=> 'independent',
	'G0V ProbeCube' 	=> 'probecube',
];
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
	<body data-spy="scroll" data-target="#sidebar">
		<div id="navbar" class="row">
			<div class="col-xs-6 left-side">
				<img src="https://i.imgur.com/IWqy7yh.png" height="50px" alt="Image">
			</div>
			<div class="col-xs-6 right-side">
				<h3 style="margin-top: 12px;">空汙地圖資料來源管理</h3>
			</div>
		</div>

		<div class="row">
			<div class='col-md-2 visible-md visible-lg'>
				<div class='well well-sm' id='sidebar' data-spy="affix" data-offset-top="10">
					<ul class="nav nav-pills nav-stacked">
						<li><a href="#scrollspy-datasource">Datasource</a></li>
						<li><a href="#scrollspy-query-history">History</a></li>
						<li><a href="#scrollspy-query-lastest">Lastest</a></li>
						<li><a href="#scrollspy-admin">Admin</a></li>
					</ul>
				</div>
			</div>

			<div class='col-md-10'>
				<div class="panel panel-default">
					<div class="panel-heading" id="scrollspy-datasource">Datasource (JSON Format)</div>
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Site Type</th>
								<th>Valid JSON URL</th>
								<th>Expire JSON URL</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($datasources as $name => $filename): ?>
							<tr>
								<td><?php echo $name; ?></td>
								<td>
									<a target="json" href="<?php echo sprintf("%s/%s.json", $urlRoot, $filename);?>">
										<?php echo sprintf("%s/%s.json", $urlRoot, $filename);?>
									</a>
								</td>
								<td>
									<a target="json" href="<?php echo sprintf("%s/%s-expire.json", $urlRoot, $filename);?>">
										<?php echo sprintf("%s/%s-expire.json", $urlRoot, $filename);?>
									</a>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					
					<div class="panel-body">
						<span class="label label-primary">Optional Parameter</span>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Name</th>
									<th>Value</th>
									<th>Description</th>
									<th>Example</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>raw</td>
									<td>1</td>
									<td>response include RawData from original datasource.</td>
									<td>
										<a target="json" href="<?php echo sprintf("%s/%s.json?raw=1", $urlRoot, 'airmap');?>">
											<?php echo sprintf("%s/%s.json?raw=1", $urlRoot, 'airmap');?>
										</a>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading" id="scrollspy-query-history">Query History Records</div>
					
					<div class="panel-body">
						<table class="table">
							<tr>
								<th>Request URL</th>
								<td><?php echo sprintf("%s/query-history", $urlRoot);?></td>
							</tr>
							<tr>
								<th>Request Method</th>
								<td>GET</td>
							</tr>
						</table>
					
						<span class="label label-primary">Required Parameter</span>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Name</th>
									<th>Value Type</th>
									<th>Description</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>group</td>
									<td>string</td>
									<td>site group (ex: lass, lass-4u, ...etc).</td>
								</tr>
								<tr>
									<td>id</td>
									<td>string</td>
									<td>device unique key specified in datasource json.</td>
								</tr>
								<tr>
									<td>start</td>
									<td>string(datetime format) or number(timestamp)</td>
									<td>history start time.</td>
								</tr>
								<tr>
									<td>end</td>
									<td>string(datetime format) or number(timestamp)</td>
									<td>history end time.</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading" id="scrollspy-query-lastest">Query Lastest Record</div>
					
					<div class="panel-body">
						<table class="table">
							<tr>
								<th>Request URL</th>
								<td><?php echo sprintf("%s/query-lastest", $urlRoot);?></td>
							</tr>
							<tr>
								<th>Request Method</th>
								<td>GET</td>
							</tr>
						</table>
					
						<span class="label label-primary">Required Parameter</span>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Name</th>
									<th>Value Type</th>
									<th>Description</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>group</td>
									<td>string</td>
									<td>site group (ex: lass, lass-4u, ...etc).</td>
								</tr>
								<tr>
									<td>id</td>
									<td>string</td>
									<td>device unique key specified in datasource json.</td>
								</tr>
							</tbody>
						</table>

						<span class="label label-primary">Optional Parameter</span>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Name</th>
									<th>Value</th>
									<th>Description</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>raw</td>
									<td>1</td>
									<td>response include RawData from original datasource.</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div class="panel panel-danger">
					<div class="panel-heading" id="scrollspy-admin">Warning!! staff only</div>
					
					<div class="panel-body">
						<a href='/admin' class="btn btn-lg btn-danger">Admin</a>
					</div>
				</div>
			</div>
		</div>

		<div id="footer">
			Asper &copy; 2017 | <a href="https://github.com/Aspertw/datasource-airmap" target="github">Github</a>
		</div>

		<script>
			$("#sidebar .panel-heading").click(function(){
				var target = this.hash,
				$target = $(target);
				$('html, body').stop().animate({
					'scrollTop': $target.offset().top-60
				}, 900, 'swing');
				return false;
			});
		</script>	
	</body>
</html>