<?php

class Promotional_Email extends CI_Controller {

    var $viewData = array();

    function __construct() {

        parent::__construct();

        $this->controller = 'promotional_email';
        $this->uppercase = 'Promotional Email';
        $this->title = 'Promotional Email';

        $this->load->model('promotional_email_model');
    }

    function index() {
        $viewData   = array("title" => "Promotional Email");
        $viewData['breadCrumbArr']  = array("booking" => "Promotional Email");
        $this->load->view('promotional_email/view', $viewData);
    }

    function paginate() {
        $data = $this->promotional_email_model->get_paginationresult();
        echo json_encode($data);
    }

    function status($id = '', $rm = '') {
        if ($id != '' && $rm != '' && $rm == 'y') {
            if ($this->promotional_email_model->changeStatus($id)) {
                echo '1';
            } else {
                echo '0';
            }
        }
    }
    
    function deleteAll() {
        $data = $_POST['rows'];
        if ( $this->promotional_email_model->softDelete($data) ) {
            echo '1';
        } else {
            echo '0';
        }
    }
    
    function add($HistoryID = '', $ed = '') {
        try {
            $viewData['title'] = "Send Promotional Email";

            $viewData['ACTION_LABEL'] = (isset($HistoryID) && $HistoryID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

            if ($HistoryID != '' && $ed != '' && $ed == 'y') {
                $getData = $this->promotional_email_model->getEmailDataById($HistoryID);
                $viewData['getEmailData'] = $getData;
            }
            $viewData['userList'] = $this->promotional_email_model->getUserList($HistoryID);

            if ($this->input->post('action') && $this->input->post('action') == 'backoffice.add') {
                $scheduleDate   = date('Y-m-d', strtotime($this->input->post('scheduleDate')));
                $scheduleTime   = date('H:i:s', strtotime($this->input->post('scheduleTime')));
                if($this->promotional_email_model->ifEmailExistsAtTime($scheduleDate." ".$scheduleTime) ){
                    $err = array('0' => "Email at $scheduleDate $scheduleTime already exists!");
                    $this->session->set_userdata('ERROR', $err);
                }elseif( $this->promotional_email_model->addEmail($_POST) ){
                    $succ = array('0' => "Promotional email added successfully!");
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => "Unable to add promotional email!");
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('promotional_email/index', 'refresh');
            }elseif ($this->input->post('action') && $this->input->post('action') == 'backoffice.edit') {
                $scheduleDate   = date('Y-m-d', strtotime($this->input->post('scheduleDate')));
                $scheduleTime   = date('H:i:s', strtotime($this->input->post('scheduleTime')));
                if($this->promotional_email_model->ifEmailExistsAtTime($scheduleDate." ".$scheduleTime,$this->input->post('iPromotionalEmailId')) ){
                    $err = array('0' => "Email at $scheduleDate $scheduleTime already exists!");
                    $this->session->set_userdata('ERROR', $err);
                }elseif( $this->promotional_email_model->editEmail($this->input->post()) ){
                    $succ = array('0' => "Promotional email updated successfully!");
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => "Unable to update promotional email!");
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('promotional_email/index', 'refresh');
            }

            $this->load->view('promotional_email/add', $viewData);
        } catch (Exception $ex) {
            throw new Exception('Error in promotional email add function - ' . $ex);
        }
    }

    

}
