<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author OpenXcell Technolabs
 */
class Test extends REST_Controller {

    function __construct() {
        parent::__construct();
    }

    function index_post() {
        $this->load->library('pushnotify');
        $platform = 1;
        $token = 'APA91bHiSlo0SM7s5IaGaFNrq6eYb9xBa_Uvc8G6bb20iyb0uXfXWDKUoFlsjIfGc7tpzfidl_JpdMU5xC3kEZh7ZvAkyKZMYnWa_JqQli3iiVUCvS4taA3IwSK9j_8Cc8_J6sdXq6BW8xN3ncmFzgkR7h-jwxervg';
        $msg = 'This is testing';
        $this->pushnotify->sendIt($platform, $token, $msg);
        exit;
    }

}
