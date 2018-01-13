<?php

class Sponsors extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('restaurant_model');
        $this->load->model('sponsers_model');
        $this->load->helper('string');
        $this->controller = 'sponsors';
        $this->uppercase = 'Sponsors';
        $this->title = 'Sponsors Management';
    }

    /*
     * TO DISPLAY LIST OF SPONSORS TO THE TABLE FORMAT...
     */

    function index() {
        $viewData = array();
        $getRecords = $this->sponsers_model->getSponsersDataAll();
        $viewData = array("title" => "Sponsers Management");
        $viewData['breadCrumbArr'] = array("deals" => "Sponsors Management");
       
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';
        $this->load->view('sponsors/sponsers_view', $viewData);
    }
    


    /*
     * TO LIST OUT THE LIST OF RECORDS...add
     */

    function paginate() {
        $data = $this->sponsers_model->get_paginationresult();
        echo json_encode($data);
    }
    
    function subpaginate($iComboSponsersID) {
        $data = $this->sponsers_model->get_paginationresultsuboffers($iComboOffersID);
        echo json_encode($data);
    }

    /*
     * TO LIST OUT THE LIST OF RECORDS...
     */

    function likePaginate($iDealID = '') {
        $data = $this->sponsers_model->get_like_paginationresult($iDealID);
        echo json_encode($data);
    }

    /*
     * DELETE EITHER ONE RECORD OR LIST OF RECORDS... 
     */

    function deleteAll() {
        $data = $_POST['rows'];
        $removeDeal =  '1';
        $removeDeal = $this->sponsers_model->removeSponsers($_POST['rows']);
        if ($removeDeal != '') {
            echo '1';
        } else {
            echo '1';
        }
    }

    /*
     * TO CHANGE THE STATUS OF THE RECORD..
     */

    function status($iComboOffersID = '', $rm = '') {
        if ($iComboOffersID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->sponsers_model->changeSponsersStatus($iComboOffersID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

    /*
     * TO ADD / EDIT THE SPONSORS...
     */

    function add($iSponserId = '', $ed = '') {
        $viewData['title'] = "Sponsors Management";

        $viewData['ACTION_LABEL'] = (isset($iSponserId) && $iSponserId != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iSponserId != '' && $ed != '' && $ed == 'y') {
            $getData = $this->sponsers_model->getSponsersDataById($iSponserId);

            $viewData['getSponsersData'] = $getData;
        }

        /*
         * GET RESTAURANT LIST
         */
        $viewData['getRestaurantData'] = $this->sponsers_model->getRestaurantList();

        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.sponsersadd') {
            $dealsAdd = $this->sponsers_model->addSponser($_POST);
            if ($dealsAdd != '') {
                $succ = array('0' => SPONSORS_ADDED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => SPONSORS_NOT_ADDED);
                $this->session->set_userdata('ERROR', $err);
            }
            redirect('sponsors', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.sponsersedit') {
            $dealsAdd = $this->sponsers_model->editSponser($_POST);
            if ($this->input->post('iSponserId') != '') {
                $dealsAdd = $this->input->post('iSponserId');
                $succ = array('0' => SPONSORS_EDITED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => SPONSORS_NOT_EDITED);
                $this->session->set_userdata('ERROR', $err);
            }
            redirect('sponsors', 'refresh');
        }

        $this->load->view('sponsors/sponsers_add_view', $viewData);
    }

  
   /*
     * TO LOAD RESTAURANT VIEW HTML
     */

    function viewDetail($recordId = '') {
        if ($recordId !== '') {
            $data = $this->sponsers_model->getSponsersDataViewById($recordId);
            $this->load->view('sponsors/viewDetail', array('viewData' => $data));
        } return '';
    }

}

?>
