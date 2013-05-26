<?php
class User_model extends CI_Model
{
    function findUserWithUsername($username)
    {
        $DB_store = $this->load->database('default', TRUE);
        
        $sql = "SELECT * FROM user WHERE username = ?";
        $query = $DB_store->query($sql, array($username));
        $DB_store->close();
        
        if ($query->num_rows() === 0)
        {
            $this->responseError(404, 'user not found');
            return FALSE;
        }
        
        return $query->row_array();
    }
    
    function findUserWithUserId($userId)
    {
        $DB_store = $this->load->database('default', TRUE);
        
        $sql = "SELECT * FROM user WHERE id = ?";
        $query = $DB_store->query($sql, array($userId));
        $DB_store->close();
        
        if ($query->num_rows() === 0)
        {
            $this->responseError(404, 'user not found');
            return FALSE;
        }
        
        return $query->row_array();
    }
    
    public function getUser($userid)
    {
        $db = $this->load->database('default', TRUE);
        $db->where('id', $userid);
        $query = $db->get('user');
        $db->close();
        
        return $query->row_array();
    }
}
