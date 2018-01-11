<!DOCTYPE html>
<html lang="en">
<head>
  <title>Notification</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="<?php echo base_url() ?>/public/js/config.js"></script>
  <script src="<?php echo base_url() ?>/public/js/post.js"></script>
</head>
<body>
<nav class="navbar navbar-inverse">
	<div class="container-fluid">
		<div class="navbar-header">
		  <a class="navbar-brand" href="<?php echo base_url() ?>">Live chat</a>
		</div>
		<ul class="nav navbar-nav navbar-right">
		  <li class="dropdown">
		  	<a href="#" class="dropdown-toggle" data-toggle='dropdown'>
		  		Notifications<span class="label label-pill label-danger count"></span>
		  	</a>
		  	<ul class="dropdown-menu"></ul>
		  </li>
		</ul>
	</div>
</nav>
<br>
<div class="container">	
	<form action="#" method="post" id="frmPost">
		<div class="form-group">
			<label for="title">Enter Title</label>
			<input type="text" name="title" class="form-control" id="txtTitle">
		</div>
		<div class="form-group">
			<label for="txtAreaPost">Enter Post</label>
			<textarea name="txtAreaPost" id="txtAreaPost" cols="30" rows="10" class="form-control" row="5"></textarea>
		</div>
		<div class="form-group">
			<input type="submit" id="btnPost" class="btn btn-info" name="btnPost" value="Post">
		</div>
	</form>
</div>

</body>
</html>
