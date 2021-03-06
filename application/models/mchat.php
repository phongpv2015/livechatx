<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mchat extends CI_Model {
     
    function __construct() {
        parent::__construct();
    }

    /**
     * Trung tâm xử lý dữ liệu chat
     */
    public function chatHeartbeat($id) {
        // Lấy ra lịch sử tin nhắn đến ( chưa đọc ) của user
        $sql = "  SELECT  `user`.`username`, 
                            `conversation`.`time` , 
                            `conversation`.`content` 
                    FROM    `conversation` 
                    LEFT JOIN `user` 
                    ON        `user`.`id` = `conversation`.`id_user_from` 
                    WHERE     `conversation`.`id_to` = '".$id."' 
                    AND       `conversation`.`type`  = '0' 
                    ORDER BY  `conversation`.`id` ASC 
                ;";
        $query = $this->db->query($sql);
        return $query->result_array();

    } // end function

    public function chatWith($uid,$chatUserId,$offset = 0)
    {
        $sql = "  SELECT  `user`.`username` as chatWithUser, 
                            `conversation`.`id_user_from` as `from`, 
                            `conversation`.`id_to` as `to`, 
                            `conversation`.`time` , 
                            `conversation`.`type` , 
                            `conversation`.`content` 
                    FROM    `conversation` 
                    LEFT JOIN `user` 
                    ON        `user`.`id` = `conversation`.`id_user_from` 
                    WHERE     `conversation`.`id_to` = '".$uid."' 
                    AND     `conversation`.`id_user_from` = '".$chatUserId."' 
                    OR      `conversation`.`id_to` = '".$chatUserId."'
                    AND     `conversation`.`id_user_from` = '".$uid."' 
                    ORDER BY  `conversation`.`id` ASC 
                    LIMIT $offset, 50
                ;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /**
     * Thực hiện update trạng thái từ 'chưa đọc' -> 'đã đọc' tin nhắn
     * '0' : chưa đọc ; '1' : đã đọc
     */
    public function updateConversation($id_to){

        $this->db->set('type',1);        

        $this->db->where('id_to',$id_to);        
        $this->db->where('type',0);        
        $this->db->update('conversation');
    }

    // Lưu dữ liệu chat vào database
    public function insertConversation($data){

        $this->db->set($data);        
        $this->db->insert('conversation');
        return $this->db->insert_id();
    }


}