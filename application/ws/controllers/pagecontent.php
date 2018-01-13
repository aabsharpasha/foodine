<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author OpenXcell Technolabs
 */
class PageContent extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('pagecontent_model');
    }

    /*
     * TO LOAD ALL THE STATIC PAGES...
     * PARAM
     *      - PAGE-ID*   :   PAGE ID 
     */

    function index_post() {
//        $this->response($this->post(), 200);
        try {

            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            if (checkselectedparams($this->post(), array('pageId'))) {
                /*
                 * TO CALL THE FUNCTION FROM THE PAGE-CONTENT-MODEL
                 */
                $result = $this->pagecontent_model->loadPage($this->post('pageId'));
                if (!empty($result)) {
                    $resp = array(
                        'MESSAGE' => 'Record founded successfully.',
                        'STATUS' => SUCCESS_STATUS,
                        'PAGECONTENT' => $result
                    );
                } else {
                    $resp = array(
                        'MESSAGE' => 'Record not found.!',
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
            exit('PageContent Controller : Error in PageContent function - ' . $ex);
        }
    }
    
    
    function feedback_post() {
//        $this->response($this->post(), 200);
        try {

            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            if (checkselectedparams($this->post(), array('subject', 'message'))) {
                /*
                 * TO CALL THE FUNCTION FROM THE PAGE-CONTENT-MODEL
                 */
                $result = $this->pagecontent_model->feedback($this->post('subject'), $this->post('message'));
                if (!empty($result)) {
                    $resp = array(
                        'MESSAGE' => 'Feedback saved successfully.',
                        'STATUS' => SUCCESS_STATUS,
//                        'PAGECONTENT' => $result
                    );
                } else {
                    $resp = array(
                        'MESSAGE' => 'Record not found.!',
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
            exit('PageContent Controller : Error in feedback_post function - ' . $ex);
        }
    }
    
    function contact_post() {
//        $this->response($this->post(), 200);
        try {

            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            if (checkselectedparams($this->post(), array('name', 'number','email','message'))) {
                /*
                 * TO CALL THE FUNCTION FROM THE PAGE-CONTENT-MODEL
                 */
                $result = $this->pagecontent_model->contact($this->post('name'), $this->post('number'),$this->post('email'), $this->post('message'));
                if (!empty($result)) {
                    $resp = array(
                        'MESSAGE' => 'Feedback saved successfully.',
                        'STATUS' => SUCCESS_STATUS,
//                        'PAGECONTENT' => $result
                    );
                } else {
                    $resp = array(
                        'MESSAGE' => 'Record not found.!',
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
            exit('PageContent Controller : Error in feedback_post function - ' . $ex);
        }
    }
    

    
    function sendEmailContact_post() {
        try {
            /*
             * SEND MAIL FUNCTIONALITY...
             */
            $this->load->model("smtpmail_model", "smtpmail_model");
            $str = $this->post('name').', <br />Thanks for contacting us. Our representative will get in touch with you soon.';
            $param = array(
                '%MAILSUBJECT%' => 'HungerMafia : Thanks for contacting us.',
                '%LOGO_IMAGE%' => BASEURL . '/images/hungermafia.png',
                '%DATA%' => $str,
            );
            $tmplt = DIR_VIEW . 'email/contact.php';
            $subject = 'HungerMafia : Thanks for contacting us.';
            $to = $this->post('email');
            $data = $this->smtpmail_model->send($to, $subject, $tmplt, $param);
//            $data = $this->maillib->sendMail($to, $subject, $tmplt, $param);
            $this->response($data, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in send sendEmailContact function - ' . $ex);
        }
    }
    
    function sendEmailFeedback_post() {
        try {
            /*
             * SEND MAIL FUNCTIONALITY...
             */
//            $this->load->library('maillib');
            $this->load->model("smtpmail_model", "smtpmail_model");
            $str = 'Thanks for your feedback. Our representative will get in touch with you soon.';

            $param = array(
                '%MAILSUBJECT%' => 'HungerMafia : Thanks for your feedback.',
                '%LOGO_IMAGE%' => BASEURL . 'images/hungermafia.png',
                '%DATA%' => $str,
            );
            $tmplt = DIR_VIEW . '/email/contact.php';
            $subject = 'HungerMafia : Thanks for your feedback.';
            $to = $this->post('email');
//            $data = $this->maillib->sendMail($to, $subject, $tmplt, $param);
            $data = $this->smtpmail_model->send($to, $subject, $tmplt, $param);
            $this->response($data, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in send sendEmailFeedback function - ' . $ex);
        }
    }
}
