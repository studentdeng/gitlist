<?php
class Notice_model extends CI_Model
{
    /**
     * 
     * @params array $param include (text, created, userid,project_id, reply_to, type)
     */
    public function create($param) {
        
        $db = $this->load->database('default', TRUE);
        $db->insert('notice', $param);
        $insertId = $db->insert_id();
        $db->close();
        
        return $insertId;
    }
    
    public function get_by_id($id)
    {
        $db = $this->load->database('default', TRUE);
        $db->where('id', $id);
        $query = $db->get('notice');
        $db->close();
        
        return $query->row_array();
    }
    
    public function fillNoticelistWithIds($ids) {
        $db = $this->load->database('default', TRUE);
        $db->where_in('id', $ids);
        $db->order_by('created','desc');
        $query = $db->get('notice');
        $db->close();
        
        return $query->result_array();
    }
}
