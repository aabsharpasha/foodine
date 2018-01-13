<?php

class Changepassword extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        $data = array('title' => 'Change Password');

        if ($this->input->post('vOldPassword') && $this->input->post('vPassword')) {
            $checkPassword = $this->admin_model->checkOldPassword();
            if ($checkPassword) {
                $updatePassword = $this->admin_model->changePassword();

                if ($updatePassword) {
                    $succ = array('0' => PASSWORD_CHANGED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => PASSWORD_CHANGED);
                    $this->session->set_userdata('SUCCESS', $err);
                }
            } else {
                $err = array('0' => OLD_PASSWORD_NOT_OK);
                $this->session->set_userdata('ERROR', $err);
            }
        }

        $this->load->view('changepassword/changepassword_view', $data);
    }

    public function checkpass() {
        if ($this->admin_model->checkOldPassword())
            echo "true";
        else
            echo "false";
    }

}

/*
| -------------------------------------------------------------------
|  END OF CLASS FILE
| -------------------------------------------------------------------
*/
