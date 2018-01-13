<?php

class Login extends CI_Controller {

    var $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('admin_model');
        $this->load->helper('cookie');
    }

    

    function index() {


        if (isset($_POST) && count($_POST) > 0) {

            $res = $this->admin_model->login_check();
            

            if ($res == true) {

                if (isset($_POST['rememberme'])) {
                    $cookie = array(
                        'name' => 'remember',
                        'value' => base64_encode($_POST['vEmail'] . '#######!!!!!!!*******' . $_POST['vPassword']),
                        'expire' => '86500'
                    );
                } else {

                    delete_cookie('remember');
                }
                if (isset($cookie)) {
                    $this->input->set_cookie($cookie);
                }


                redirect('dashboard');
            }
        } else {

            if (@get_cookie('remember')) {
                $rem = base64_decode(get_cookie('remember'));
                $rem = explode('#######!!!!!!!*******', $rem);
                $this->viewData['vEmail'] = $rem[0];
                $this->viewData['vPassword'] = $rem[1];
            }

            $this->load->view('login/login_view', $this->viewData);
        }
    }

    // **********************************************************************
    // Account Activation
    // **********************************************************************

    function activate($eAccessToken) {

        $this->load->library('encrypt');

        $token = $this->encrypt->decode($eAccessToken);



        // CHECK IF URL IS NOT WRONG

        if ($token != '') {

            $res = $this->admin_model->activateAccount($token);

            if ($res) {

                $succ = array('0' => ACTIVE_ACCOUNT);

                $this->session->set_userdata('SUCCESS', $succ);
            } else {

                redirect('invitation');

                exit;
            }
        }

        redirect('login');
    }
    
    /*
     * TO CHECK EMAIL IS ALREADY EXISTS OR NOT...
     */

    function checkEmail() {
        if ($this->input->post('vEmail') !== '') {
            /*
             * TO CHECKL WITH THE DATABASE...
             */
            echo $this->admin_model->checkEmailExists($this->input->post('vEmail'));
        } else {
            echo 'false';
        }
    }

}
