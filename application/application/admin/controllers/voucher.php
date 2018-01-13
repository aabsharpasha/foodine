<?php

class Voucher extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('voucher_model');
        $this->load->helper('string');
        $this->controller = 'voucher';
        $this->uppercase = 'Voucher';
        $this->title = 'Voucher Management';
    }

    // INDEX
    function index($iUserID = '') {
        $getRecords = $this->voucher_model->getVoucherDataAll();
        $viewData = array("title" => "Voucher Management");
        $viewData['breadCrumbArr'] = array("voucher" => "Voucher Management");
        if ($getRecords != ''){
            $viewData['record_set'] = $getRecords;
        }else{
            $viewData['record_set'] = '';
        }
        $viewData['get_iUserID'] = $iUserID;
//print_R($viewData);exit;
        $this->load->view('voucher/voucher_view', $viewData);
    }

    function paginate() {
        $data = $this->voucher_model->get_paginationresult();
        echo json_encode($data);
    }
    
    function add($id = '', $ed = '') {
        $viewData['title'] = "Voucher Management";

        $viewData['ACTION_LABEL'] = (isset($id) && $id != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($id != '' && $ed != '' && $ed == 'y') {
            $getData = $this->voucher_model->getVoucherDataById($id);
            $viewData['getVoucherData'] = $getData;
        }

        $viewData['voucherUseData'] = $this->voucher_model->getVoucherUseList();
     
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.voucheradd') {
            $add = $this->voucher_model->addVoucher($_POST);
            if ($add) {
                $succ = array('0' => "Voucher successfully added.");
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => EVENT_NOT_ADDED);
                $this->session->set_userdata('ERROR', $err);
            }
            redirect('voucher/index', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.voucheredit') {
            $edit = $this->voucher_model->editVoucher($_POST);
            if ($edit) {
                $err = array('0' => "Voucher details are successfully edited.");
                $this->session->set_userdata('SUCCESS', $err);
            }

            redirect('voucher/index', 'refresh');
        }

        $this->load->view('voucher/voucher_add_view', $viewData);
    }
    
    function deleteAll() {
        $data = $_POST['rows'];
        if ( $this->voucher_model->softDeleteVoucher($data) ) {
            echo '1';
        } else {
            echo '0';
        }
    }
    
    function status($id = '', $rm = '') {
        if ($id != '' && $rm != '' && $rm == 'y') {
            if ($this->voucher_model->changeStatus($id)) {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

}

?>
