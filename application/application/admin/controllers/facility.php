<?php

class Facility extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('facility_model');
        $this->load->helper('string');
        $this->controller = 'facility';
        $this->uppercase = 'Facility';
        $this->title = 'Facility Management';
    }

    // INDEX
    function index($iUserID = '') {
        $getRecords = $this->facility_model->getFacilityDataAll();
        $viewData = array("title" => "Facility Management");
        $viewData['breadCrumbArr'] = array("facility" => "Facility Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $viewData['get_iUserID'] = $iUserID;

        $this->load->view('facility/facility_view', $viewData);
    }

    // ***************************************************************
    // ADD
    // ***************************************************************
    function add($iFacilityID = '', $ed = '') {
        $viewData['title'] = "Facility Management";

        $viewData['ACTION_LABEL'] = (isset($iFacilityID) && $iFacilityID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iFacilityID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->facility_model->getFacilityDataById($iFacilityID);

            $viewData['getFacilityData'] = $getData;
        }
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.facilityadd') {
            $check = $this->facility_model->checkFacilityNameAvailable($this->input->post('vFacilityName'));
            if ($check) {
                $facilityEdit = $this->facility_model->addFacility($_POST);
                if ($facilityEdit != '') {
                    $succ = array('0' => FACILITY_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => FACILITY_NOT_ADDED);
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('facility/index', 'refresh');
            } else {
                $err = array('0' => FACILITY_FACILITYNAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
            }
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.facilityedit') {
            $check = $this->facility_model->checkFacilityNameAvailable($this->input->post('vFacilityName'), $this->input->post('iFacilityID'));

            if ($check) {
                $facilityEdit = $this->facility_model->editFacility($_POST);
                $iFacilityID = $this->input->post('iFacilityID');
                if ($facilityEdit != '') {
                    $succ = array('0' => FACILITY_EDITED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => FACILITY_EDITED);
                    $this->session->set_userdata('SUCCESS', $err);
                }

                redirect('facility/index', 'refresh');
            } else {
                $err = array('0' => FACILITY_FACILITYNAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
            }
        }

        $this->load->view('facility/facility_add_view', $viewData);
    }

    // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iFacilityID = '', $rm = '') {
        if ($iFacilityID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->facility_model->changeFacilityStatus($iFacilityID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("facility", 'refresh');
    }

    // ***************************************************************
    // Order history
    // ***************************************************************

    function get_ordered_facility($iOutletID) {
        $viewData = array(
            "title" => $this->title,
            'page' => 'facility',
            'outlet_id' => $iOutletID
        );
        $this->load->view("facility/facility_view", $viewData);
    }

    // ***************************************************************
    // REMOVE
    // ***************************************************************
    function remove($iFacilityID = '', $rm = '') {
        if ($iFacilityID != '' && $rm != '' && $rm == 'y') {
            $removeFacility = $this->facility_model->removeFacility($iFacilityID);
            if ($removeFacility != '') {
                $succ = array('0' => FACILITY_DELETED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => FACILITY_NOT_DELETED);
                $this->session->set_userdata('ERROR', $err);
            }
        }
        redirect("facility/index", 'refresh');
    }

    function deleteComment() {
        $removeComment = $this->facility_model->removeComment();
        echo $removeComment;
    }

    // ***************************************************************
    function deleteAll() {
        $data = $_POST['rows'];

        $removeFacility = $this->facility_model->removeFacility($_POST['rows']);
        if ($removeFacility != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("facility", "refresh");
    }

    function paginate() {
        $data = $this->facility_model->get_paginationresult();
        echo json_encode($data);
    }

    function getCommentList() {
        $data = $this->facility_model->getCommentList();
        echo json_encode($data);
    }

    public function make_thumb($mypath, $targetpath, $myratio) {
        $list = list($width, $height) = getimagesize($mypath);
        $ratio = $myratio / min($width, $height);

        $w = $width * $ratio;
        $h = $height * $ratio;

        $config_manip = array(
            'image_library' => 'gd2',
            'source_image' => $mypath,
            'new_image' => $targetpath,
            'maintain_ratio' => TRUE,
            'create_thumb' => FALSE,
            'width' => $w,
            'height' => $h
        );

        $this->load->library('image_lib', $config_manip);
        $this->image_lib->clear();
        $this->image_lib->initialize($config_manip);

        if (!$this->image_lib->resize()) {
            echo $this->image_lib->display_errors();
        }
    }

    /*
     * TO LOAD RESTAURANT VIEW HTML
     */

    function viewRestaurantDetail($recordId = '') {
        if ($recordId !== '') {
            $data = $this->facility_model->getRestaurantDataByFclId($recordId);
            $this->load->view('category/view_restaurants', array('viewData' => $data));
        } return '';
    }

    /*     * ******************* End of the File ***************************** */
}
