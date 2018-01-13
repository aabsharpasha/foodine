<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of logout
 * @author Chintan
 */
class Logout extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('login_model');
    }

    /*
     * WHEN USER LOGS OUT FROM THE SYSTEM...
     */

    function index_post() {
        if (checkselectedparams($this->post(), array('uid'))) {
            $result = $this->login_model->userLogOut($this->post('uid'));
            if ($result == 1) {
                $resp = array(
                    'MESSAGE' => LOGOUT_SUCCESS,
                    'STATUS' => SUCCESS_STATUS
                );
                $this->response($resp, 200);
            } else {
                $resp = array(
                    'MESSAGE' => LOGOUT_ERROR,
                    'STATUS' => FAIL_STATUS
                );
                $this->response($resp, 200);
            }
        } else {
            $resp = array(
                'MESSAGE' => INSUFF_DATA,
                'STATUS' => FAIL_STATUS
            );
            $this->response($resp, 200);
        }
    }

    /*
     * WHEN VENDOR LOGS OUT FROM THE SYSTEM...
     */

    function vendor_post() {
        if (checkselectedparams($this->post(), array('vid'))) {
            $result = $this->login_model->userLogOut($this->post('vid'));
            if ($result == 1) {
                $resp = array(
                    'MESSAGE' => LOGOUT_SUCCESS,
                    'STATUS' => SUCCESS_STATUS
                );
                $this->response($resp, 200);
            } else {
                $resp = array(
                    'MESSAGE' => LOGOUT_ERROR,
                    'STATUS' => FAIL_STATUS
                );
                $this->response($resp, 200);
            }
        } else {
            $resp = array(
                'MESSAGE' => INSUFF_DATA,
                'STATUS' => FAIL_STATUS
            );
            $this->response($resp, 200);
        }
    }

}

?>
