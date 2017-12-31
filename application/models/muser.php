<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Muser extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * @param str $user
	 * @param str $pass
	 */
	public function login($user, $pass){

        $condition = array('username' => $user, 'password' => $pass);
	    
	    $get = $this->db->select('id , username, status')
	            ->where($condition)
         		->get('user');
        $result = $get->num_rows();

        if ($result == 1){
            return $get->row_array();
        }else{
            return false;
        }
	}

    /**
     * Lấy thông tin của user
     */
    public function getUsers() {

	    $result = $this->db->select('id , username, status')
	    				->from('user')
         				->get();        
        return $result->result_array();
    }


    /**
     * Thay đổi trạng thái user thành online
     * @param int $status
     */
    public function changeStatus($id,$status){
        
        $this->db->set('status',$status);
        $this->db->where('id',$id);        
        $this->db->update('user');
    }

    /**
     * Thay đổi trạng thái user thành offline
     * Sau đó mới unset cookie và destroy session
     */
    public function logout($id) {

        $data = array( 'status' => 0 ); // offline
        $this->db->set($data)
		        ->where('id',$id)
		        ->update('user');
    }

}

/* End of file muser.php */
/* Location: ./application/models/muser.php */