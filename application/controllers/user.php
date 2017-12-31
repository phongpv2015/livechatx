<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('muser');
		session_start();
	}

	public function index()	{

		$this->load->view('user');

	}
	public function login()
	{

		if ( isset($_POST['username']) && isset($_POST['password']) ) {
			$user = $_POST['username'];
			$pass = $_POST['password'];
			$user = $this->muser->login($user,$pass);
			if ($user) {
				$_SESSION['username'] = $user['username'];
				$_SESSION['password'] = $user['password'];
				$_SESSION['id'] = $user['id'];
				redirect('chat');
			}else{
				die('Tên đăng nhập hoặc mật khẩu không đúng. Quay lại và thử nhập lại !');
			}
		}
	}
	public function logout(){
        $this->muser->logout($_SESSION['id']);
		session_destroy();
		redirect('user');
	}

}

/* End of file user.php */
/* Location: ./application/controllers/user.php */