<?php

class Dashboard extends CI_Controller {

    var $viewData = array();

    function __construct() {

        parent::__construct();

        $this->load->model('dashboard_model');
    }

    function logoff() {
        $this->session->set_userdata();
        $this->session->sess_destroy();
        redirect('login', 'refresh');
    }

    function index() {
        $data = array(
            'title' => 'Dashboard',
            'notification' => getNotifications()
        );
        $this->load->view('dashboard/dashboard_view', $data);
    }

    function trends() {
        $data = $this->dashboard_model->toptrends();
        echo json_encode($data);
    }
    
    function session() {
        mprd($this->session());
    }

}
