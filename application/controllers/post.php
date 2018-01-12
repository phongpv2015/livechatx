<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Post
*/
include '/application/third_party/vendor/autoload.php';
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
class Post extends CI_Controller  implements MessageComponentInterface 
{
	protected $clients;
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model('mpost');
		$this->load->model('muser');
		$this->clients = new \SplObjectStorage;
	}
	public function index(){
		$uid = $this->session->userdata('uid');
        if(empty($uid)) {
            redirect('user');
        }else{
            $this->muser->changeStatus($uid,1);
        }
        $outputData['view'] = 'post';
        $outputData['js'] = 'post.js';
        $outputData['css'] = 'post.css';
        $this->load->view('layout/index',$outputData);
	}

	public function onOpen(ConnectionInterface $conn) {
	    // Store the new connection to send messages to later
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }
    public function onMessage(ConnectionInterface $from, $msg) {
    	 $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
    	// The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    	echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
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
    // $server = IoServer::factory(
    //     new HttpServer(
    //         new WsServer(
    //             new Post()
    //         )
    //     ),
    //     8080
    // );

    // $server->run();