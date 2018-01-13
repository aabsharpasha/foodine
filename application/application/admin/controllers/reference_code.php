<?php

class Reference_Code extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('reference_code_model');
        $this->load->helper('string');
        $this->controller = 'reference_code';
        $this->uppercase = 'Reference Code';
        $this->title = 'Reference Code Management';
    }

    // INDEX
    function index($iUserID = '') {
//        $getRecords = $this->voucher_model->getVoucherDataAll();
        $viewData = array("title" => "Reference Code Management");
        $viewData['breadCrumbArr'] = array("reference_code" => "Reference Code Management");
//        if ($getRecords != ''){
//            $viewData['record_set'] = $getRecords;
//        }else{
//            $viewData['record_set'] = '';
//        }
        $viewData['get_iUserID'] = $iUserID;
        $this->load->view('reference_code/reference_code_view', $viewData);
    }

    function paginate() {
        $data = $this->reference_code_model->get_paginationresult();
        echo json_encode($data);
    }
    
    function add($id = '', $ed = '') {
        $viewData['title'] = "Reference Code Management";

        $viewData['ACTION_LABEL'] = (isset($id) && $id != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($id != '' && $ed != '' && $ed == 'y') {
            $getData = $this->reference_code_model->getReferenceCodeDataById($id);
            $viewData['getReferenceCodeData'] = $getData;
        }

        $viewData['voucherData'] = $this->reference_code_model->getVoucherList();
     
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.referencecodeadd') {
            $add = $this->reference_code_model->addReferenceCode($_POST);
            if ($add) {
                $succ = array('0' => "Reference Code successfully added.");
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => EVENT_NOT_ADDED);
                $this->session->set_userdata('ERROR', $err);
            }
            redirect('reference_code/index', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.referencecodeedit') {
            $edit = $this->reference_code_model->editReferenceCode($_POST);
            if ($edit) {
                $err = array('0' => "Reference Code successfully edited.");
                $this->session->set_userdata('SUCCESS', $err);
            }

            redirect('reference_code/index', 'refresh');
        }

        $this->load->view('reference_code/reference_code_add_view', $viewData);
    }
    
    function deleteAll() {
        $data = $_POST['rows'];
        if ( $this->reference_code_model->softDelete($data) ) {
            echo '1';
        } else {
            echo '0';
        }
    }
    
    function status($id = '', $rm = '') {
        if ($id != '' && $rm != '' && $rm == 'y') {
            if ($this->reference_code_model->changeStatus($id)) {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

}

?>
