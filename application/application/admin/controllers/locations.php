<?php

class Locations extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('location_model');
        $this->load->helper('string');
        $this->controller = 'locations';
        $this->uppercase = 'Locations';
        $this->title = 'Locations Management';
    }

    /*
     * TO DISPLAY LIST OF DEALS TO THE TABLE FORMAT...
     */

    function index() {
        $getRecords = $this->location_model->getLocationDataAll();
        $viewData = array("title" => $this->title);
        $viewData['breadCrumbArr'] = array($this->controller => $this->title);
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $this->load->view($this->controller . '/location_view', $viewData);
    }

    /*
     * TO LIST OUT THE LIST OF RECORDS...
     */

    function paginate() {
        $data = $this->location_model->get_paginationresult();
        echo json_encode($data);
    }

    /*
     * DELETE EITHER ONE RECORD OR LIST OF RECORDS... 
     */

    function deleteAll() {
        $data = $_POST['rows'];
        $removeDeal = $this->location_model->removeLocation($_POST['rows']);
        if ($removeDeal != '') {
            echo '1';
        } else {
            echo '0';
        }
    }

    /*
     * TO CHANGE THE STATUS OF THE RECORD..
     */

    function status($iLocationID = '', $rm = '') {
        if ($iLocationID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->location_model->changeLocationStatus($iLocationID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

    /*
     * TO ADD / EDIT THE DEALS...
     */

    function add($iLocationID = '', $ed = '') {
        $viewData['title'] = $this->title;

        $viewData['ACTION_LABEL'] = (isset($iLocationID) && $iLocationID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iLocationID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->location_model->getLocationDataById($iLocationID);

            $viewData['getLocationData'] = $getData;
        }
        
        $viewData['getZone'] = $this->location_model->getZone();;

        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.locationadd') {
            $dealsAdd = $this->location_model->addLocation($_POST);
            if ($dealsAdd != '') {
                $succ = array('0' => LOCATION_ADDED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => LOCATION_NOT_ADDED);
                $this->session->set_userdata('ERROR', $err);
            }
            redirect($this->controller."/index", 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.locationedit') {
            $dealsAdd = $this->location_model->editLocation($_POST);
            if ($dealsAdd != '') {
                $succ = array('0' => LOCATION_EDITED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => LOCATION_NOT_EDITED);
                $this->session->set_userdata('ERROR', $err);
            }
            redirect($this->controller."/index", 'refresh');
        }

        $this->load->view($this->controller . '/location_add_view', $viewData);
    }

}

?>
