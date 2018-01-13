<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require(APPPATH . '/libraries/REST_Controller.php');

class Page extends REST_Controller {

    public function __construct() {
        parent::__construct();
        //Do your magic here
        $this->load->model('page_model');
    }

    protected $methods = array(
        'register_post' => array('level' => 10, 'limit' => 10),
        'login_post' => array('level' => 10, 'limit' => 10),
        'sport_get' => array('level' => 10),
        'language_get' => array('level' => 10)
    );

    //*************************************************************************************	
    //get coupon information (Developer : rahul)
    //**************************************************************************************
    public function getPageContent_post() {

        $result = $this->page_model->getPageContent($this->post());

        if (!empty($result)) {
            $row = array("MESSAGE" => "", "STATUS" => "SUCCESS", "UserData" => $result);
            $this->response($row, 200);
        } else {
            $row = array("MESSAGE" => "Something wrong", "STATUS" => 'FAIL');
            $this->response($row, 401);
        }
    }

}

?>
