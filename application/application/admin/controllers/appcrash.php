<?php

class AppCrash extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('appcrash_model');
        $this->load->helper('string');
        $this->controller = 'appcrash';
        $this->uppercase = 'Application Crash History';
        $this->title = 'Application Crash History';
    }

    /*
     * TO DISPLAY LIST OF DEALS TO THE TABLE FORMAT...
     */

    function index() {
        $viewData = array("title" => $this->title);
        $viewData['breadCrumbArr'] = array("booking" => $this->uppercase);

        $this->load->view('appcrash/crash_view', $viewData);
    }

    /*
     * TO LIST OUT THE LIST OF RECORDS...
     */

    function paginate() {
        $data = $this->appcrash_model->get_paginationresult();
        echo json_encode($data);
    }
    
    /*
     * TO LOAD RESTAURANT VIEW HTML
     */

    function viewDetail($recordId = '') {
        if ($recordId !== '') {
            $data = $this->appcrash_model->getCrashAppDataById($recordId);
            
            $this->load->view('appcrash/viewDetail', array('viewData' => $data));
        } return '';
    }

}

?>
