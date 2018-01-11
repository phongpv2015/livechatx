<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mpost extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * @param str $user
	 * @param str $pass
	 */
	public function insertPost($data){

	    $this->db->set($data)
            ->insert('post');
        return $this->db->insert_id();
	}

    /**
     * Lấy thông tin của bảng post
     */
    public function getPosts() {

	    $result = $this->db->select('*')
	    				->from('post')
         				->get();        
        return $result->result_array();
    }

    public function unseenNotificationPost()
    {
        $this->db->select('p.pid,p.title,p.body,p.authorId,u.username')
                ->from('post p')
                ->join('user u','p.authorId = u.id')
                ->order_by('p.pid','desc');
        $query = $this->db->get();                    
        return $query->result_array();
    }

    public function countUnseenPost()
    {
        $query = $this->db->select('*')
                    ->from('post')
                    ->where('status',0)
                    ->get();
        return $query->num_rows();
    }
    /**
     * Thay đổi trạng thái user thành online
     * @param int $status
     */
    public function changeStatus($id,$status){
        
        $this->db->set('status',$status);
        $this->db->where('id',$id);        
        $this->db->update('post');
    }
}