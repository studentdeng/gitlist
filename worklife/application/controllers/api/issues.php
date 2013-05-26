<?php

defined('BASEPATH') OR exit('No direct script access allowed');


// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Issues extends REST_Controller {

    public $rest_format = 'json';

    /**
     * 创建
     * 
     * @params string text
     * @params string project_id
     * @params string reply_to (optional)
     * 
     * @link http://localhost/worklife/index.php/api/issues/create
     */
    public function create_post() {
        
        $this->HTTPBaseAuth();
        $user = $this->getAuthUserArray();
        
        $inputParam = array('text', 'project_id');
        $paramValues = $this->posts($inputParam);
        $reply_to = $this->post('reply_to');
        
        $data = $paramValues;
        $data['type'] = 1; //issues
        $data['reply_to'] = $reply_to;
        $data['created'] = date('Y-m-d H:i:s');
        $data['userid'] = $user['id'];

        $this->load->model('Notice_model');
        $noticeId = $this->Notice_model->create($data);
        $notice = $this->Notice_model->get_by_id($noticeId);

        $this->response($notice);
    }

    /**
     * 更新issue 状态
     * @params string id
     * @params string state
     * 
     * @link http://localhost/worklife/index.php/api/issues/update_state
     */
    public function update_state_post()
    {
        $inputParam = array('state', 'id');
        $paramValues = $this->posts($inputParam);
        $data = array('state' => $paramValues['state']);
        
        $db = $this->load->database('default', TRUE);
        $db->where('id', $paramValues['id']);
        $db->update('notice', $data);
        $db->close();
        
        $this->load->model('Notice_model');
        $notice = $this->Notice_model->get_by_id($paramValues['id']);
        $this->response($notice);
    }
    
    public function index_get() {
        $inputParam = array('id');
        $paramValues = $this->gets($inputParam);

        $this->load->model('Notice_model');
        $notice = $this->Notice_model->get_by_id($paramValues['id']);
        $this->response($notice);
    }
    
    /**
     * bug 回复列表
     * 
     * @params string id     : issueId
     * 
     * @link http://localhost/worklife/index.php/api/issues/reply_list?id=23
     */
    public function reply_list_get() {
        $this->load->model('User_model');
        
        $inputParam = array('id');
        $paramValues = $this->gets($inputParam);

        $db = $this->load->database('default', TRUE);
        $db->where('reply_to', $paramValues['id']);
        $db->order_by('created', 'desc');
        $query = $db->get('notice');
        $db->close();
        $resultOriginal = $query->result_array();
        
        $result = array();
        foreach ($resultOriginal as $item) {
            //created2 用来客户端日期分组
            $item['created2'] = date('Y-m-d', strtotime($item['created']));
            $item['user'] = $this->User_model->getUser($item['userid']);
            $result[] = $item;
        }

        $this->response(array(
            'list' => $result
        ));
    }

    /**
     * 列表
     * @params string page
     * @params string count
     * @params string project_id
     * @params string state
     * 
     * @link http://localhost/worklife/index.php/api/issues/list?project_id=1&page=0&count=1&state=1
     */
    public function list_get() {
        
        $this->load->model('Notice_model');
        $this->load->model('User_model');

        $inputParam = array('project_id');
        $paramValues = $this->gets($inputParam);

        $page = $this->get('page');
        if (empty($page)) {
            $page = 0;
        }

        $count = $this->get('count');
        if (empty($count)) {
            $count = 20;
        }
        
        $state = $this->get('state');

        $db = $this->load->database('default', TRUE);
        $db->where('project_id', $paramValues['project_id']);
        if (!empty($state)) {
            $db->where('state', $state);
        }
        
        $db->where('reply_to', 0);
        $db->limit($count, $page * $count);
        $db->order_by('created', 'desc');
        $query = $db->get('notice');
        $db->close();
        $resultOriginal = $query->result_array();

        $db2 = $this->load->database('default', TRUE);
        $db->where('reply_to', 0);
        $db->where('project_id', $paramValues['project_id']);
        $sum = $db2->count_all("notice");
        $db2->close();

        $result = array();
        foreach ($resultOriginal as $item) {
            //created2 用来客户端日期分组
            $item['created2'] = date('Y-m-d', strtotime($item['created']));
            $item['user'] = $this->User_model->getUser($item['userid']);
            $result[] = $item;
        }

        $this->response(array(
            'list' => $result,
            'sum' => $sum
        ));
    }

}