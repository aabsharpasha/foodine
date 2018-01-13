<?php

class Admin extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('admin_model');
        $this->controller = 'admin';
    }

    // INDEX
    function index() {
        $getRecords = $this->admin_model->getAdminDataAll();

        $viewData = array("title" => "Admin Management");

        $viewData['breadCrumbArr'] = array("Admin" => "Admin Management");

        if ($getRecords != '') {
            $viewData['record_set'] = $getRecords;
        } else {
            $viewData['record_set'] = '';
        }

        $this->load->view('admin/admin_view', $viewData);
    }

    // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iAdminID = '', $rm = '') {
        if ($iAdminID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->admin_model->changeAdminStatus($iAdminID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        //redirect("admin", 'refresh');
    }

    // ***************************************************************
    // REMOVE
    // ***************************************************************
    function remove($iAdminID = '', $rm = '') {
        if ($iAdminID != '' && $rm != '' && $rm == 'y') {
            $removeUser = $this->admin_model->removeAdmin($iAdminID);

            if ($removeUser != '') {
                $succ = array('0' => ADMIN_DELETED);

                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => ADMIN_DELETED);

                $this->session->set_userdata('SUCCESS', $err);
            }
        }
        redirect("admin", 'refresh');
    }

    // ***************************************************************
    // ADD
    // ***************************************************************
    function add($iAdminID = '', $ed = '') {
        $viewData['title'] = "Admin Management";

        $viewData['ACTION_LABEL'] = (isset($iAdminID) && $iAdminID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iAdminID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->admin_model->getAdminDataById($iAdminID);

            $viewData['getUserData'] = $getData;
        }
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.adminadd') {
            if ($this->admin_model->checkAdminEmailAvailable($this->input->post('vEmail'))) {
                $adminAdd = $this->admin_model->addAdmin($_POST);

                if ($adminAdd != '') {
                    $succ = array('0' => ADMIN_ADDED);

                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => ADMIN_ADDED);

                    $this->session->set_userdata('SUCCESS', $err);
                }
            } else {
                $err = array('0' => ADMIN_EXISTS);

                $this->session->set_userdata('ERROR', $err);
            }
            redirect('dashboard', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.adminedit') {
            if ($this->admin_model->checkAdminEmailAvailable($this->input->post('vEmail'), $this->input->post('iAdminID'))) {
                $adminEdit = $this->admin_model->editAdmin($_POST);

                if ($adminEdit != '') {
                    $succ = array('0' => ADMIN_EDITED);

                    //$this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => ADMIN_EDITED);

                    //$this->session->set_userdata('SUCCESS', $err);
                }
            } else {
                $err = array('0' => ADMIN_EXISTS);

                //$this->session->set_userdata('ERROR', $err);
            }

            redirect('dashboard', 'refresh');
        }
        $this->load->view('admin/admin_add_view', $viewData);
    }

    // ***************************************************************
    function deleteAll() {
        $data = $_POST['rows'];

        $removeUser = $this->admin_model->removeAdmin($_POST['rows']);

        if ($removeUser != '') {
            echo '1';
        } else {
            echo '0';
        }
    }

    function paginate() {
        $addid = $this->session->userdata('ADMINID');
        $data = $this->datatableshelper->query("SELECT 
                                               concat(vFirstName,' ',vLastName) as name,
                                               vEmail,
                                               eStatus,                                                    
                                               iAdminID,
                                               iAdminID AS DT_RowId 
                                               FROM tbl_admin 
                                               WHERE iAdminID != $addid
           ");
        echo json_encode($data);
    }

}