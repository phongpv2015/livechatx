<!DOCTYPE html>
<html lang="en">
<head>
  <title>Notification</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="<?php echo base_url() ?>/public/js/config.js"></script>  
  <script type="text/javascript">
        var uid=<?php echo $this->session->userdata('uid');?>
  </script>
  <script src="<?php echo base_url() ?>/public/js/post.js"></script>
  <script src="<?php echo base_url('/public/js/'.$js) ?>"></script>  
  <link type="text/css" rel="stylesheet" media="all" href="<?php echo base_url('/public/css/'.$css) ?>" />
</head>
<body>
<?php 
	$this->load->view('layout/nav');
	$this->load->view($view);
?>
</body>
</html>