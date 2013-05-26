<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Project extends REST_Controller {

    public $rest_format = 'json';
    
    /**
     * 创建
     * 
     * @params name
     * @params description
     * @params source_path (optinal)
     * 
     * @link http://localhost/worklife/index.php/api/project/create
     * @return error name exists
     */
    public function create_post() {
        $inputParam = array('name', 'description');
        $paramValues = $this->posts($inputParam);
        
        $this->checkName($paramValues['name']);

        $data = $paramValues;
        $data['source_path'] = $this->post('source_path');
        $data['created'] = date('Y-m-d H:i:s');
        $db = $this->load->database('default', TRUE);
        $db->insert('project', $data);
        $db->close();
        
        $this->responseSuccess();
    }
    
    /**
     * list
     * 
     * @link http://localhost/worklife/index.php/api/project/list
     */
    public function list_get() {
        
        $db = $this->load->database('default', TRUE);
        $query = $db->get('project');
        $db->close();
        
        $this->responseArray($query->result_array());
    }
    
    /**
     * 根据id 获取 project
     * 
     * @param string id
     * @return error id not found
     */
    public function index_get() {
        $id = $this->get('id');
        $source_path = $this->get('source_path');
        
        if (empty($id) && empty($source_path))
        {
            $this->responseError(400, 'source_path or id cannot be all empty');
        }
        
        $db = $this->load->database('default', TRUE);
        if (!empty($id))
        {
            $db->where('id', $id);
        }
        else if (!empty($source_path))
        {
            $db->where('source_path', $source_path);
        }
        
        $query = $db->get('project');
        $db->close();
        
        if ($query->num_rows() == 0)
        {
            $this->responseError(404, "not found path: $source_path");
        }

        $this->response($query->row_array());
    }
    
    private function checkName($name)
    {
        $db = $this->load->database('default', TRUE);
        $db->where('name', $name);
        $query = $db->get('project');
        $db->close();
        
        if ($query->num_rows() != 0)
        {
            $this->responseError(400, 'name exists');
        }
    }

}