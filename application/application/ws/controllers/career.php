<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author OpenXcell Technolabs
 */
class Career extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('career_model');
    }

    function getTeamDetails_get() {
        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = NO_RECORD_FOUND;
            $STATUS = FAIL_STATUS;
            /*
             * SEARCH FROM THE MODEL TO GET THE RESULT...
             */
            $res = $this->career_model->getTeamDetails($this->post());

            if (!empty($res)) {
                $MESSAGE = 'Details Found';
                $STATUS = SUCCESS_STATUS;
                $TEAMDATA = $res;
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$TEAMDATA != '') {
                $resp['TEAMDATA'] = $TEAMDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getTeamDetails_post function - ' . $ex);
        }
    }

    function getJobDetails_post() {
        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = NO_RECORD_FOUND;
            $STATUS = FAIL_STATUS;
            /*
             * SEARCH FROM THE MODEL TO GET THE RESULT...
             */
            $res = $this->career_model->getJobDetails($this->post());

            if (!empty($res)) {
                $MESSAGE = 'Details Found';
                $STATUS = SUCCESS_STATUS;
                $JOBDATA = $res;
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$JOBDATA != '') {
                $resp['JOBDATA'] = $JOBDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getJobDetails_post function - ' . $ex);
        }
    }

    /**
     * Action to add new outlet for the parent company in temp outlets.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@kelltontech
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    public function postJob_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array('name', 'number', 'email', 'jobId');
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DETAILS = array();

            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $data = $this->post();
                $jobApplicationId = $this->career_model->postJob($data);
                $images = array();
                if ($jobApplicationId) {
                    if (!empty($_FILES['resume']) && !empty($_FILES['resume']['name'])) {
                        $param = array(
                            'fileType' => 'text',
                            'uploadFor' => array(
                                'key' => 'resume',
//                                'id' => $jobApplicationId
                            ),
                        );

                        $this->load->library('fileupload', $param);
                        //$this->fileupload->removeFile();
                        $uploadFiles = $this->fileupload->upload($_FILES, 'resume');
                        $images['resume'] = $uploadFiles[0];
                    }
                    if ($images) {
                        $this->career_model->addResume($images, $jobApplicationId);
                    }

                    $DETAILS = $jobApplicationId;
                    $MESSAGE = 'Details Updated SuccessFully';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'APPLICATIONID' => $DETAILS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in addPartner_post function - ' . $ex);
        }
    }

}
