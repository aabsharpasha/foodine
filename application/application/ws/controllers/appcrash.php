<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author OpenXcell Technolabs
 */
class AppCrash extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('appcrash_model');
    }

    /*
     * CALL FUNCTION TO STORE CRASH REPORT VALUE...
     * URL : http://your-site/ws/appcrash/index/
     * WITH REQUIRE PARAMETER
     *      - osType*
     *      - reportValue*
     */

    function index_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            if (checkselectedparams($this->post(), array('osType', 'reportValue'))) {
                /*
                 * TO SAVE VALUE TO THE DATABASE...
                 */
                $ins_id = $this->appcrash_model->saveCrash($this->post());
                if ($ins_id !== '') {
                    $resp = array(
                        'MESSAGE' => 'Crash Report has been saved',
                        'STATUS' => SUCCESS_STATUS
                    );
                } else {
                    $resp = array(
                        'MESSAGE' => 'Error in crash report save operation',
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
            throw new Exception('AppCrash Controller : Error in index_post function - ' . $ex);
        }
    }

}
