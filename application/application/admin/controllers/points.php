<?php

class Points extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('points_model');
        $this->load->helper('string');
        $this->controller = 'points';
        $this->uppercase = 'Points';
        $this->title = 'Points Management';
    }

    /*
     * TO DISPLAY LIST OF DEALS TO THE TABLE FORMAT...
     */

    function index() {
        $getRecords = $this->points_model->getPointsDataAll();
        $viewData = array("title" => "Points Management");
        $viewData['breadCrumbArr'] = array("reward" => "Points Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';


        $this->load->view('points/points_view', $viewData);
    }

    /*
     * TO LIST OUT THE LIST OF RECORDS...
     */

    function paginate() {
        $data = $this->points_model->get_paginationresult();
        echo json_encode($data);
    }

    /*
     * ADD POINTS VALUE
     */

    function add($iUserPointSystemID = '', $ed = '') {
        $viewData['title'] = "Points Management";


        $viewData['ACTION_LABEL'] = (isset($iUserPointSystemID) && $iUserPointSystemID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iUserPointSystemID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->points_model->getPointsDataById($iUserPointSystemID);

            $viewData['getPointsData'] = $getData;
        }
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.pointsadd') {
            
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.pointsedit') {
            
            $check = $this->points_model->checkPointNameAvailable($this->input->post('vType'), $this->input->post('iUserPointSystemID'));

            if ($check) {
                $musicEdit = $this->points_model->editPoints($_POST);
                $iUserPointSystemID = $this->input->post('iUserPointSystemID');
                if ($musicEdit != '') {
                    $succ = array('0' => POINTS_EDITED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => POINTS_EDITED);
                    $this->session->set_userdata('SUCCESS', $err);
                }

                redirect('points/index', 'refresh');
            } else {
                $err = array('0' => POINTS_NAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
                redirect('points/index', 'refresh');
            }
        }

        $this->load->view('points/points_add_view', $viewData);
    }

}

?>
