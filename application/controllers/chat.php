<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Chat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('redis');
        $this->load->library('session');
        $this->load->model('mchat');
        $this->load->model('muser');
    }
    
    public function index() {
        // Nếu ko có User ID tức là chưa login, redirect về trang login
        // Nếu đã login thành công thì change trạng thái thành online
        $uid = $this->session->userdata('uid');
        if(empty($uid)) {
            redirect('user');
        }else{
            $this->muser->changeStatus($uid,1);
        }

        // Lấy ra danh sách tất cả các user
        $outputData['listOfUsers'] = $this->muser->getUsers();
        $this->load->view('chat',$outputData);
    }
    public function redisHash()
    {
        $uid = 1;
        // $conversation_id = $_SESSION['conversation_id'];
        // echo($_SESSION['conversation_id']);
        // $user = $this->mchat->chatHeartbeat($uid);
        // $this->redis->hmset("user:$uid", $user[0]);
        // $ruser = $this->redis->hgetall("conversation:$conversation_id");
        $ruser = $this->redis->hgetall("user:$uid");
        echo json_encode($ruser);
    }
    // Được gọi tới trong chat.js
    public function successChat(){
        // echo($_GET['action']); exit();
        if ($_GET['action'] == "chatheartbeat") { $this->chatHeartbeat(); } 
        if ($_GET['action'] == "sendchat") { $this->sendChat(); } 
        if ($_GET['action'] == "closechat") { $this->closeChat(); } 
        if ($_GET['action'] == "startchatsession") { $this->startChatSession(); } 

    }
    // Request từ ajax gửi đến function này sẽ bị treo cho đến khi function này thực hiện xong
    public function chatHeartbeat(){
        $_SESSION['id'] = 1;
        $id = 1;
        $query = $this->mchat->chatHeartbeat($id);
        $items = '';

        $chatBoxes = array();
        
        if ( !empty($query) ) {
            while ( $chat = $query) {
                
                unset($_SESSION['tsChatBoxes'][$chat['username']]);
                $_SESSION['openChatBoxes'][$chat['username']] = $chat['time'];
            } // end while line 69

            if (!empty($_SESSION['openChatBoxes'])) {
                foreach ($_SESSION['openChatBoxes'] as $chatbox => $time) {
                    if (!isset($_SESSION['tsChatBoxes'][$chatbox])) {
                        $now = time()-strtotime($time);
                        $time = date('g:iA M dS', strtotime($time));

                        $message = "Gửi lúc $time";
                        if ($now > 180) {
                            
                            if (!isset($_SESSION['chatHistory'][$chatbox])) {
                                $_SESSION['chatHistory'][$chatbox] = '';
                            }

                            
                            $_SESSION['tsChatBoxes'][$chatbox] = 1;

                        } 
                    } 
                } 
            } 

        }
        
        // Thực hiện update trạng thái từ 'chưa đọc' -> 'đã đọc' tin nhắn
        $this->mchat->updateConversation();

        if ( $items != '' ) {
            $items = substr($items, 0, -1);
        }
        
        header('Content-type: application/json');
        ?>
        {
            "items": [ 
                <?php echo $items;?> 
            ]
        }
        <?php

        exit(0);
    }

    public function chatBoxSession($chatbox) {
        
        $items = '';
        
        if (isset($_SESSION['chatHistory'][$chatbox])) {
            $items = $_SESSION['chatHistory'][$chatbox];
        }

        return $items;
    }

    public function startChatSession() {
        
        $items = '';

        if (!empty($_SESSION['openChatBoxes'])) {
            foreach ($_SESSION['openChatBoxes'] as $chatbox => $void) {
                $items .= $this->chatBoxSession($chatbox);
            }
        }

        if ($items != '') {
            $items = substr($items, 0, -1);
        }
        $result = json_encode($items);
        header('Content-Type: application/json');
        echo($result);
        
    }

    public function sendChat() {
        $from = $this->session->userdata('uid'); 
        $to = $this->input->post('to');
        $message = $this->input->post('message');        
        $messagesan = $this->sanitize($_POST['message']);
        $data = array(
            'id_user_from' => $from ,
            'id_to' => $to,
            'content' => $messagesan,
            'time' =>  date('Y-m-d H:i:s', time()),
            'type' =>0
            );
        $conversation_id = $this->mchat->insertConversation($data);
        $this->redis->hmset("conversation:$from",$data);
        echo $conversation_id;
        exit(0);
    }

    public function closeChat() {

        // $this->redis->del()        
        echo "1";
        exit(0);
    }

    // Filter chuỗi
    public function sanitize($text) {
        $text = htmlspecialchars($text, ENT_QUOTES);
        $text = str_replace("\n\r","\n",$text);
        $text = str_replace("\r\n","\n",$text);
        $text = str_replace("\n","<br>",$text);
        return $text;
    }

}