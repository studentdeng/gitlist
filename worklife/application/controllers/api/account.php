<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';


class Account extends REST_Controller
{
    public $rest_format = 'json';

    /**
     * 注册接口
     * 
     * @params username
     * @params password
     * @params nickname
     * @params avatar
     * 
     * @return success user
     * @return failed  username exist
     */
    public function register_post() 
    {
        $this->sessionOut();
        
        $param = array(
            'username', 
            'password',
            'nickname',
            'avatar',
            'email');
        
        $paramValues = $this->posts($param);
        
        $db = $this->load->database('default', TRUE);
        $db->where('username', $paramValues['username']);
        $query = $db->get('user');
        $db->close();
        
        if ($query->num_rows() > 0)
        {
            $this->responseError(400, 'username exist');
        }
        
        $db3 = $this->load->database('default', TRUE);
        $db3->insert('user', $paramValues);
        $db3->close();

        @session_start();

        $_SESSION['login'] = TRUE;
        $_SESSION['username'] = $paramValues['username'];
        $_SESSION['password'] = $paramValues['password'];

        $this->sessionAuth();
        $user = $this->getAuthUserArray();
        $this->response($user);
    }
    
    /**
     * 登录
     * 
     * @params HTTP Basic Auth
     */
    public function login_post()
    {
        $this->sessionOut();
        $this->sessionAuth();
        $user = $this->getAuthUserArray();
        $this->response($user);
    }
    
    /**
     * 登出
     */
    public function loginout_post()
    {
        $this->sessionOut();
        $this->responseSuccess();
    }
    
    /**
     * 用户个人信息
     */
    public function show_get()
    {
        $this->sessionAuth();
        $user = $this->getAuthUserArray();
        $this->response($user);
    }
}