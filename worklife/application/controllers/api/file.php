<?php defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class File extends REST_Controller
{
    public $rest_format = 'json';
    
    function upload_post()
    {
        $name = time();
        $config_upload = $this->config->item('custom_attachments');
        $config_upload['file_name'] = "img_".$name;
        $config_upload['upload_path'] = APPPATH.'files/images/';

        $this->load->library('upload', $config_upload);

        if (!$this->upload->do_upload("image"))
        {
            $this->responseError(400, $this->upload->display_errors());
        }
        
        $data = $this->upload->data();
        $picture = $this->config->item('base_url').'application/files/images/'.$data['file_name'];
        
        $this->response(array('url' => $picture));
    }
}