<?php

class Referal extends CI_Controller {

    var $viewData = array();

    function __construct() {

        parent::__construct();

        $this->controller = 'referal';
        $this->uppercase = 'Reference Code History';
        $this->title = 'Reference Code History';

        $this->load->model('referal_model');
    }

    function index() {
        redirect(BASEURL);
    }

    function view() {
        try {
            $viewData = array("title" => "Reference Code History");
            $viewData['breadCrumbArr'] = array("referal" => "Reference Code History");

            $this->load->view('referal/view', $viewData);
        } catch (Exception $ex) {
            throw new Exception('Error in index function - ' . $ex);
        }
    }

    /*
     * TO GET THE NOTIFICATION LIST
     */

    function paginate() {
        $data = $this->referal_model->get_paginationresult();
        echo json_encode($data);
    }

    function add($ID = '', $ed = '') {
        try {
            $viewData['ACTION_LABEL'] = (isset($ID) && $ID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";
            $viewData['title'] = $viewData['ACTION_LABEL'] . " Refence Code";

            if ($ID != '' && $ed != '' && $ed == 'y') {
                $getData = $this->referal_model->getReferenceDataById($ID);
                $viewData['getNotificationData'] = $getData;
            }
            $ID = $this->input->post('iReferalID');




            if ($this->input->post('action') && $this->input->post('action') == 'backoffice.notificationadd') {
                $check = $this->referal_model->checkCodeAvailable($this->input->post('vReferalCode'));
                if (!$check) {
                    $this->referal_model->saveReferal($_POST);

                    $succ = array('0' => REFERAL_ADD);
                    $this->session->set_userdata('SUCCESS', $succ);

                    redirect(BASEURL . 'referal/view');
                } else {
                    $err = array('0' => REFERAL_EXISTS);
                    $this->session->set_userdata('ERROR', $err);
                }
            } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.notificationedit') {
                $check = $this->referal_model->checkCodeAvailable($this->input->post('vReferalCode'), $ID);

                if (!$check) {
                    $this->referal_model->saveReferal($_POST, $ID);

                    $succ = array('0' => REFERAL_EDIT);
                    $this->session->set_userdata('SUCCESS', $succ);

                    redirect(BASEURL . 'referal/view');
                } else {
                    $err = array('0' => REFERAL_EXISTS);
                    $this->session->set_userdata('ERROR', $err);
                }
            }

            $this->load->view('referal/add', $viewData);
        } catch (Exception $ex) {
            throw new Exception('Error in addhistory function - ' . $ex);
        }
    }

    function deleteAll() {
        $data = $_POST['rows'];

        $removeUser = $this->referal_model->removeReferal($_POST['rows']);
        if ($removeUser != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("user", "refresh");
    }
    
    /*
     * TO CHANGE THE STATUS OF THE RECORD..
     */

    function status($ID = '', $rm = '') {
        if ($ID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->referal_model->changeStatus($ID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

}
