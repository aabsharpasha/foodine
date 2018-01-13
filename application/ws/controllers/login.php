<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author OpenXcell Technolabs
 */
class Login extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('login_model');
        $this->load->model('user_model');
        $this->load->model('general_model');
    }

    /*
     * WHEN USER POST LOGIN TO
     * URL : http://your-site/ws/login/index/
     * WITH REQUIRE PARAMETER
     *      - EMAIL*
     *      - PASSWORD*
     */

    function index_post() {

        /*
         * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
         */
        if (checkselectedparams($this->post(), array('email', 'password', 'plateForm', 'deviceToken'))) {

            /*
             * TO CHECK WETHEAR USER IS LOGGED IN OR NOT..
             *  PARAMATER
             *      EMAIL-ID*
             *      PASSWORD*
             */

            $result = $this->login_model->checkAuthentication($this->post('email'), $this->post('password'), $this->post('plateForm'), $this->post('deviceToken'));
//print_r($result); exit;
            if (!empty($result)) {      
                $result['photoUrl'] = IMG_URL.$result['photoUrl'];
		$status = 'Active';
 		$resp = $this->_returnStatus($result, $status);

            } else {
                $resp = array(
                    'result' => FAIL,
                    'resultCode' => FAIL_STATUS
                );
            }
        }

        $this->response($resp, 200);
    }

    /*
     * YOU MAY GOT THE STATUS FROM THIS FUNCTION...
     *  actually line of code is increasing so, better option is that we have to define another function...
     */

    private function _returnStatus($result, $status = 'Active', $isVendor = FALSE) {
        try {
            switch ($status) {
                case 'Inactive' :
//                    $resp = array(
//                        'MESSAGE' => ACCOUNT_DEACTIVE,
//                        'STATUS' => FAIL_STATUS
//                    );
                    $resp = array(
                        'MESSAGE' => SUCCESS,
                        'STATUS' => SUCCESS_STATUS,
                        ($isVendor ? 'VENDORDATA' : 'USERDATA') => $result
                    );
                    break;

                case 'Active' :
                    $resp_main = array(
                        'result' => SUCCESS,
                        'resultCode' => SUCCESS_STATUS,

                    );
		    $resp = array_merge($resp_main, $result);
                    break;
            }

            return $resp;
        } catch (Exception $ex) {
            exit('Login Controll : Error in _returnFunction : ' . $ex);
        }
    }

    /*
     * WHEN RESTAURANT POST LOGIN TO [ VENDOR LOGIN ] 
     * URL : http://your-site/ws/login/vendor/
     * WITH REQUIRE PARAMETER
     *      - EMAIL*
     *      - PASSWORD*
     */

    function vendor_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            if (checkselectedparams($this->post(), array('email', 'password'))) {
                /*
                 * TO CHECK WETHEAR USER IS LOGGED IN OR NOT..
                 *  PARAMATER
                 *      EMAIL-ID*
                 *      PASSWORD*
                 */
                $deviceToken = $this->post('deviceToken');
                $plateForm = $this->post('plateForm');
                $result = $this->login_model->checkVendorAuthentication($this->post('email'), $this->post('password'), $deviceToken, $plateForm);

                if (!empty($result)) {
                    $status = $result['vendorStatus'];
                    $resp = $this->_returnStatus($result, $status, TRUE);
                } else {
                    $resp = array(
                        'MESSAGE' => LOGIN_INVALID,
                        'STATUS' => FAIL_STATUS
                    );
                }
            } else {
                $resp = array(
                    'MESSAGE' => INSUFF_DATA,
                    'STATUS' => FAIL_STATUS
                );
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Login Controller : Error in vendorLogin function - ' . $ex);
        }
    }

}
