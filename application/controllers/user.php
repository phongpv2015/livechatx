<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('muser');
		$this->load->library('redis');
		$this->load->library('session');
	}

	public function index()	{

		$this->load->view('user');

	}
	public function login()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$user = $this->muser->login($username,$password);
		if ($user) {
			$uid = $user['id'];
			$this->redis->hset("users:$uid",$user['id'],$user['username']);
			$this->session->set_userdata('uid',$user['id']);
			redirect('chat');
		}else{
			die('Tên đăng nhập hoặc mật khẩu không đúng. Quay lại và thử nhập lại !');
		}
	}
	public function logout(){
		$uid = $this->session->uid;
        $this->muser->logout($uid);
		$this->redis->hdel('users:$uid',$uid);
		$this->session->unset_userdata('uid');
		redirect('user');
	}

}

/* End of file user.php */
/* Location: ./application/controllers/user.php */