<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Post
*/
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
class Post extends CI_Controller implements MessageComponentInterface 
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model('mpost');
		$this->load->model('muser');
	}
	public function index()
	{
		 $uid = $this->session->userdata('uid');
        if(empty($uid)) {
            redirect('user');
        }else{
            $this->muser->changeStatus($uid,1);
        }
        $this->load->view('post');
	}
	public function unseenNotificationPost()
	{
		$output = '';
		$result = $this->mpost->unseenNotificationPost();
		if($result){
			foreach ($result as $row) {
				$output .= '
					<li>
						<a href="#">
							<strong>'.$row['title'].'</strong><br>
							<small><em>'.$row['body'].'</em></small>
						</a>
					</li>		
				';
			}
		}else{
			$output .= '
				<li><a href="#" class="text-bold text-italic">No Notification Found</a></li>
			';
		}
		$count = $this->mpost->countUnseenPost();
		$data = array(
			'notification' =>$output,
			'unseen_notification' => $count
		);

		echo json_encode($data);
	}
	public function insertPost()
	{
		$title = $this->input->post('title');
		$body = $this->input->post('body');
		$pid = $this->session->userdata('uid');

		$data = array(
			'title' => $title,
			'body' => $body,
			'authorId' => $pid,
			'status'=>0
		);
		$insert_id = $this->mpost->insertPost($data);
		echo($insert_id);
	}
}