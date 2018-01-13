<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author KelltonTech
 */
class Payumoney extends REST_Controller {

    function __construct() {
        parent::__construct();
    }

    function success_post() {
        $this->load->view('payumoney/success');
    }

    function failure_post() {
        $this->load->view('payumoney/fail');
    }

}
