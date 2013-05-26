<?php
class File_model extends CI_Model
{
    public function addToProject($filePath, $type, $notice_id)
    {
        $data = array(
            'file_path'    => $filePath,
            'notice_id'    => $notice_id,
            'type'         => $type
        );
        
        $db = $this->load->database('default', TRUE);
        $db->insert('file', $data);
        $db->close();
        
        return TRUE;
    }
}
