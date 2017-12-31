<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Đăng nhập Demo live chat</title>
	<link type="text/css" rel="stylesheet" media="all" href="<?php echo base_url() ?>/public/css/login.css" />
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-4"></div>
			<div class="col-sm-8">
				<div class="login-page">
				  <div class="form">
				    <form class="register-form">
				      <input type="text" placeholder="name"/>
				      <input type="password" placeholder="password"/>
				      <input type="text" placeholder="email address"/>
				      <button>create</button>
				      <p class="message">Already registered? <a href="#">Sign In</a></p>
				    </form>
				    <form class="login-form" action="http://www.livechatx.com/user/login" method="post" accept-charset="utf-8">
						<input type="text" name="username" placeholder="Tên đăng nhập">
						<input type="password" name="password" placeholder="Mật khẩu">
						<input type="submit" value="Đăng nhập">
				      	<p class="message">Not registered? <a href="#">Create an account</a></p>
				    </form>
				  </div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>