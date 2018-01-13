<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author OpenXcell Technolabs
 */
class User extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('user_model');
    }

    function verifyEmail_post() {
        /*
         * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
         */
        $allowParam = array('userId');
        $MESSAGE = INSUFF_DATA;
        $STATUS = FAIL_STATUS;

        if (checkselectedparams($this->post(), $allowParam)) {
            $userId = $this->post('userId');
            $data = $this->general_model->getUserBasicRecordById($userId);
            if (count($data) > 0) {
                $mailuserId = $this->user_model->verifyEmail($data);
                if ($mailuserId == $userId) {
                    $MESSAGE = 'Mail Sent successfully.';
                    $STATUS = SUCCESS_STATUS;
                }
            }
        }
        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS
        );
        $this->response($resp, 200);
    }

    function verifyMobile_post() {
        $allowParam = array('userMobile');
        $MESSAGE = INSUFF_DATA;
        $STATUS = FAIL_STATUS;

        if (checkselectedparams($this->post(), $allowParam)) {
            @$userMobile = $this->post('userMobile');
            if (checkselectedparams($this->post(), array('userOTP'))) {
                $status = $this->user_model->verifyOTP($this->post());
                if ($status == 1) {
                    $MESSAGE = 'OTP Verified successfully.';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    if ($status == 2) {
                        $MESSAGE = 'OTP Expired';
                    } else {
                        $MESSAGE = 'Invalid OTP';
                    }
                }
            } else {
                $mobileVerified = '';
                if (checkselectedparams($this->post(), array('userEmail'))) {
                    $userEmail = $this->post('userEmail');
                    $mobileVerified = $this->user_model->checkMobileVerified($userMobile, $userEmail);
                }
                if ($mobileVerified == 1) {
                    $MESSAGE = 'OTP Verified successfully.';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $rand = $this->user_model->saveOTP($userMobile);
                    if ($rand != '') {
                        $msg = 'Your HungerMafia verification code is ' . $rand;
                        $this->load->model('Sms_model', 'sms_m');
                        $this->sms_m->destmobileno = @$userMobile;
                        $this->sms_m->msg = $msg;
                        $data = $this->sms_m->Send();
                        $smsData = json_decode($data);
                        $status = $smsData->messages[0]->status;
                        if (isset($status) && $status == 0) {
                            $sdata = array(
                            'recieved' => date('Y-m-d H:i:s'),
                             'logdata'=>$data
                            );
                            $result = $this->db->update('tbl_otp_verification_sms_logs', $sdata, array('mobile' => $userMobile, 'verificationCode' => $rand));
                            $MESSAGE = 'OTP Sent successfully.';
                            $STATUS = SUCCESS_STATUS;
                        } else {
                            $MESSAGE = 'Some Error Occurred';
                        }
                    } else {
                        $MESSAGE = ACCOUNT_PHONE_ERROR;
                    }
                }
            }
        }
        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS
        );

        $this->response($resp, 200);
    }

    /*
     * TO GET THE CUISINE / MUSIC / INTEREST LIST
     */

    function getUserChoiceList_post() {
        try {
            @$userId = $this->post('userId');
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $USERCHOICEDATA = '';

            $MESSAGE = USERCHOICE_RECORD_FOUND;
            $STATUS = SUCCESS_STATUS;
            $USERCHOICEDATA['userCuisine'] = $this->user_model->getCIMUserArray('cuisine', @$userId);
            $USERCHOICEDATA['userInterest'] = $this->user_model->getCIMUserArray('interest', @$userId);
            $USERCHOICEDATA['userCategory'] = $this->user_model->getCIMUserArray('category', @$userId);
            $USERCHOICEDATA['userMusic'] = $this->user_model->getCIMUserArray('music', @$userId);
            $locRec = $this->user_model->getCIMUserArray('location');
            $USERCHOICEDATA['userLocation'] = $this->_locArry($locRec);

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$USERCHOICEDATA !== '') {
                $resp['USERCHOICEDATA'] = $USERCHOICEDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getUserChoiceList function - ' . $ex);
        }
    }


     function getDashboard_post() {
         $this->load->model('restaurant_model');
        try {
            @$userId = $this->post('userId');
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $USERCHOICEDATA = '';

            $MESSAGE = USERCHOICE_RECORD_FOUND;
            $STATUS = SUCCESS_STATUS;
            //$USERCHOICEDATA['userCuisine'] = $this->user_model->getCIMUserArray('cuisine', @$userId);
            //$USERCHOICEDATA['userInterest'] = $this->user_model->getCIMUserArray('interest', @$userId);
            //$USERCHOICEDATA['userCategory'] = $this->user_model->getCIMUserArray('category', @$userId);
            //$USERCHOICEDATA['userMusic'] = $this->user_model->getCIMUserArray('music', @$userId);

            $USERCHOICEDATA['introPics'] = $this->user_model->getBanners();
            $USERCHOICEDATA['featuredRestaurant'] =  $this->restaurant_model->getTrending();
            print_r($USERCHOICEDATA['featuredRestaurant']); exit;

            
            $locRec = $this->user_model->getCIMUserArray('location');
            $USERCHOICEDATA['userLocation'] = $this->_locArry($locRec);

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$USERCHOICEDATA !== '') {
                $resp['USERCHOICEDATA'] = $USERCHOICEDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getUserChoiceList function - ' . $ex);
        }
    }

    private function _locArry($locRec) {
        try {
            if (!empty($locRec)) {
                $returnArry = array();
                for ($i = 0; $i < count($locRec); $i++) {
                    $row = $locRec[$i];
                    $returnArry[$row['zoneName']]['zoneName'] = $row['zoneName'];
                    $returnArry[$row['zoneName']]['locations'][] = array(
                        'locName' => $row['locationName'],
                        'locId' => (int) $row['locationId']
                    );
                }

                $returnArry = array_values($returnArry);

                return $returnArry;
            }
            return array();
        } catch (Exception $ex) {
            
        }
    }

    /*
     * TO CREATE A NEW ACCOUNT
     * URL : http://your-site/ws/user/createAccount/
     * PARAM
     *      - userName              :   FIRST-NAME
     *      - userEmail             :   PRIMARY EMAIL ADDRESS
     *      - userMobile            :   VALID MOBILE NUMBER
     *      - userPassword          :   PASSWORD OF THE REGISTERED ACCOUNT 
     *      - userGender            :   GENDER OF THE USER [ M / F / NOT DEFINE ]
     *      - cuisineChoice         :   CUISINE CHOICE; IT WILL REFLECTS FROM THE MASTER RECORD
     *                                  array('1', '2', '3', ....)
     * 
     *      - userInterest          :   CUISINE CHOICE; IT WILL REFLECTS FROM THE MASTER RECORD
     *                                  array('1', '2', '3', ....)
     * 
     *      - userMusic             :   CUISINE CHOICE; IT WILL REFLECTS FROM THE MASTER RECORD
     *                                  array('1', '2', '3', ....)
     */

    function createAccount_post() {
        /*
         * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
         */

        $allowParam = array(
            'firstName', 'lastName',
            'email', 'mobileNumber', 'dateOfBirth', 'anniversary',
            'gender', 'password','registrationType',
            'deviceType', 'deviceToken', 'OTP'
        );


//mprd($allowParam);
//mprd($this->post());

        /*
         * TO SET DEFAULT VARIABLE VALUES...
         */

        $MESSAGE = INSUFF_DATA;
        $STATUS = FAIL_STATUS;
        $USERDATA = array();
//print_r($this->post());
//print_r($allowParam); exit;
        if (checkselectedparams($this->post(), $allowParam)) {
            /*
             * FIRST WE NEED TO CHECK THAT THIS EMAIL ADDRESS IS ALREADT EXISTS OR NOT...
             */
            $isExists = $this->user_model->isEmailExists($this->post('email'));
            if (!$isExists) {
                /*
                 * CALL A FUNCTION TO CREATE AN ACCOUNT.
                 */
                $userId = $this->user_model->createAccount($this->post());
                //echo $userId; exit;
                /* @var $accountResp type */
                if (@$userId !== '') {

                    $targetpath = BASEDIR . 'images/user/' . $userId;

//echo $targetpath;
                    if (!is_dir($targetpath)) {
                        mkdir($targetpath, 0777, TRUE);
                    }
                    if (!is_dir($targetpath . "/thumb/")) {
                        mkdir($targetpath . "/thumb/", 0777, TRUE);
                    }

                    $uploaded_files = array();

//print_r($_FILES);

                    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['name'] != '') {

                        $DEFAULT_PARAM = array(
                            'fType' => 'image',
                            'fLimit' => 20,
                            'fLoc' => array(
                                'key' => 'user',
                                'id' => $userId
                            ),
                            'fThumb' => TRUE,
                            'fCopyText' => FALSE
                        );

                        $this->load->library('fupload', $DEFAULT_PARAM);
                        $uploaded_files = $this->fupload->fUpload($_FILES, 'profilePic');

                        /*
                         * $param = array(
                          'fileType' => 'image',
                          'maxSize' => 20,
                          'uploadFor' => array(
                          'key' => 'user',
                          'id' => $userId
                          ),
                          'requireThumb' => TRUE
                          );

                          $this->load->library('fileupload', $param);
                          $uploadFiles = $this->fileupload->upload($_FILES, 'profilePic'); */
                    }

                    /*
                     * UPDATE A IMAGE TO THE DATABASE...
                     */
                    if (!empty($uploaded_files)) {
                        $this->user_model->updateProfilePic($uploaded_files[0], $userId);
                    }

                    $accountResp = $this->general_model->getUserBasicRecordById($userId);
                    
                    $MESSAGE = 'Success';
                    $STATUS = SUCCESS_STATUS;
                    $USERDATA = $accountResp;
                } else {
                    $MESSAGE = ACCOUNT_ERROR;
                }
            } else {
                $MESSAGE = ACCOUNT_EMAIL_ERROR;
            }
            
           
            
           
        }

        $response = array(
            'result' => $MESSAGE,
            'resultCode' => $STATUS,
            'firstName' => $USERDATA['userFirstName'], 
            'lastName' => $USERDATA['userLastName'],
            'email' => $USERDATA['userEmail'], 
            'mobileNumber' => $USERDATA['userMobile'], 
            'dateOfBirth' => $USERDATA['userDOB'], 
            'anniversary' => $USERDATA['userAnniversary'],
            'gender' => $USERDATA['userGender'],
        );
        $this->response($response, 200);
    }

    function uploadUserPic_post() {
        $result = 'Fail';
        $userId = $this->post('userId');
     
        $targetpath = './uploads/';

//echo $targetpath;
//        if (!is_dir($targetpath)) {
//            mkdir($targetpath, 0777, TRUE);
//        }
//        if (!is_dir($targetpath . "/thumb/")) {
//            mkdir($targetpath . "/thumb/", 0777, TRUE);
//        }
      //  print_r($_FILES['profilePic']); exit;
         if (isset($_FILES['profilePic']) && $_FILES['profilePic']['name'] != '') {

                        $config['upload_path']   = $targetpath; 
                        $config['allowed_types'] = 'gif|jpg|png'; 
                        $config['max_size']      = 100; 
                        $config['max_width']     = 1024; 
                        $config['max_height']    = 768;  
                        $this->load->library('upload', $config);
			//print_r($this->upload->do_upload('profilePic')); 
                        //print_r($this->upload->display_errors());
                        //exit;
                     
                         /*
                     * UPDATE A IMAGE TO THE DATABASE...
                     */
                    
                   
                    if ($this->upload->do_upload('profilePic')) {
                         $uploaded_files = $this->upload->data();
                        $this->user_model->updateProfilePic($uploaded_files['file_name'], $userId);
                        $result = 'Success';
                        $resultCode = SUCCESS_STATUS;
                    }
                    
                    
                  }
                  
                  $resp = array(
                        'result' => $result,
                        'resultCode' => $resultCode,
                 );
                  
                  $this->response($resp, 200);
    }
    
    function createWebAccount_post() {
        /*
         * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
         */

        $allowParam = array(
            'userFullName', 'userEmail', 'userPassword',
            'userMobile', 'userGender', 'userDob', 'userFirstName',
            'userLastName'
        );

        /*
         * TO SET DEFAULT VARIABLE VALUES...
         */

        $MESSAGE = INSUFF_DATA;
        $STATUS = FAIL_STATUS;
        $USERDATA = '';

        if (checkselectedparams($this->post(), $allowParam)) {
            /*
             * FIRST WE NEED TO CHECK THAT THIS EMAIL ADDRESS IS ALREADT EXISTS OR NOT...
             */
            $isExists = $this->user_model->isEmailExists($this->post('userEmail'));
            if (!$isExists) {
                /*
                 * CALL A FUNCTION TO CREATE AN ACCOUNT.
                 */
                $userId = $this->user_model->createWebAccount($this->post());
                /* @var $accountResp type */
                if (@$userId !== '') {
                    $accountResp = $this->general_model->getUserBasicRecordById($userId);
                    $accountResp['hMac'] = genratemac($userId);

                    $MESSAGE = ACCOUNT_SUCCESS_WEB;
                    $STATUS = SUCCESS_STATUS;
                    $USERDATA = $accountResp;
                } else {
                    $MESSAGE = ACCOUNT_ERROR;
                }
            } else {
                $MESSAGE = ACCOUNT_EMAIL_ERROR;
            }
        }

        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS
        );

        if (@$USERDATA !== '') {
            $resp['USERDATA'] = $USERDATA;
        }

        $this->response($resp, 200);
    }

    function userdetail_post() {
        /*
         * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
         */

        $allowParam = array(
            'userId'
        );

        /*
         * TO SET DEFAULT VARIABLE VALUES...
         */

        $MESSAGE = INSUFF_DATA;
        $STATUS = FAIL_STATUS;
        $USERDATA = '';

        if (checkselectedparams($this->post(), $allowParam)) {
            /*
             * FIRST WE NEED TO CHECK THAT THIS EMAIL ADDRESS IS ALREADT EXISTS OR NOT...
             */
            $userId = $this->post('userId');
            if (@$userId !== '') {
                /*
                 * CALL A FUNCTION TO FETCH USER RECORD
                 */
                $accountResp = $this->general_model->getUserBasicRecordById($userId);

                $MESSAGE = '';
                $STATUS = SUCCESS_STATUS;
                $USERDATA = $accountResp;
            } else {
                $MESSAGE = ACCOUNT_EMAIL_ERROR;
            }
        }

        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS
        );

        if (@$USERDATA !== '') {
            $resp['USERDATA'] = $USERDATA;
        }

        $this->response($resp, 200);
    }

    function createAppAccount_post() {
        /*
         * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
         */

        $allowParam = array(
            'userFirstName', 'userLastName', 'userEmail', 'userMobile',
            'userPassword', 'plateForm', 'deviceToken'
        );

        /*
         * TO SET DEFAULT VARIABLE VALUES...
         */

        $MESSAGE = INSUFF_DATA;
        $STATUS = FAIL_STATUS;
        $USERDATA = '';

        if (checkselectedparams($this->post(), $allowParam)) {
            /*
             * FIRST WE NEED TO CHECK THAT THIS EMAIL ADDRESS IS ALREADT EXISTS OR NOT...
             */
            $isExists = $this->user_model->isEmailExists($this->post('userEmail'));
            if (!$isExists) {
                /*
                 * CALL A FUNCTION TO CREATE AN ACCOUNT.
                 */
                $userId = $this->user_model->createAppAccount($this->post());
                /* @var $accountResp type */
                if (@$userId !== '') {

                    $targetpath = BASEDIR . 'images/user/' . $userId;

                    if (!is_dir($targetpath)) {
                        mkdir($targetpath, 0777, TRUE);
                    }
                    if (!is_dir($targetpath . "/thumb/")) {
                        mkdir($targetpath . "/thumb/", 0777, TRUE);
                    }

                    $uploaded_files = array();

                    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['name'] != '') {

                        $DEFAULT_PARAM = array(
                            'fType' => 'image',
                            'fLimit' => 20,
                            'fLoc' => array(
                                'key' => 'user',
                                'id' => $userId
                            ),
                            'fThumb' => TRUE,
                            'fCopyText' => FALSE
                        );

                        $this->load->library('fupload', $DEFAULT_PARAM);
                        $uploaded_files = $this->fupload->fUpload($_FILES, 'profilePic');
                    }

                    /*
                     * UPDATE A IMAGE TO THE DATABASE...
                     */
                    if (!empty($uploaded_files)) {
                        $this->user_model->updateProfilePic($uploaded_files[0], $userId);
                    }

                    $accountResp = $this->general_model->getUserBasicRecordById($userId);
                    $accountResp['hMac'] = genratemac($userId);


                    $MESSAGE = ACCOUNT_SUCCESS;
                    $STATUS = SUCCESS_STATUS;
                    $USERDATA = $accountResp;
                } else {
                    $MESSAGE = ACCOUNT_ERROR;
                }
            } else {
                $MESSAGE = ACCOUNT_EMAIL_ERROR;
            }
        }

        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS
        );

        if (@$USERDATA !== '') {
            $resp['USERDATA'] = $USERDATA;
        }

        $this->response($resp, 200);
    }

    /**
     * Create account when using Social login
     * and account doesn't exists
     *
     */
    function createWebSocialAccount_post() {

        // TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
        $allowParam = array(
            'userFullName', 'userEmail', 'userProviderId', 'userProvider'
        );

        /*
         * TO SET DEFAULT VARIABLE VALUES...
         */
        $MESSAGE = INSUFF_DATA;
        $STATUS = FAIL_STATUS;
        $USERDATA = $ACCOUNTSTATUS = '';

        if (checkselectedparams($this->post(), $allowParam)) {

            // FIRST WE NEED TO CHECK THAT THIS EMAIL ADDRESS IS ALREADY EXISTS OR NOT...
            $isExists = $this->user_model->isEmailExists($this->post('userEmail'));
            if (!$isExists) {

                // CALL A FUNCTION TO CREATE AN ACCOUNT.
                $userId = $this->user_model->createWebSocialAccount($this->post());
                /* @var $accountResp type */
                if (@$userId !== '') {
                    $accountResp = $this->general_model->getUserBasicRecordById($userId);
                    $accountResp['hMac'] = genratemac($userId);

                    $MESSAGE = ACCOUNT_SUCCESS_WEB;
                    $STATUS = SUCCESS_STATUS;
                    $USERDATA = $accountResp;
                    $ACCOUNTSTATUS = 'register';
                } else {
                    $MESSAGE = ACCOUNT_ERROR;
                }
            } else {
                // update providerid and return the user
                if ($this->post('userProvider') == 'facebook') {
                    $USERDATA = $this->user_model->updateFbAccount($this->post());
                } elseif ($this->post('userProvider') == 'google') {
                    $USERDATA = $this->user_model->updateGpAccount($this->post());
                } elseif ($this->post('userProvider') == 'twitter') {
                    $USERDATA = $this->user_model->updateTwitterAccount($this->post());
                }
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = ACCOUNT_UPDATED;
                $ACCOUNTSTATUS = 'login';
            }
        }

        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS
        );

        if (@$USERDATA !== '') {
            $resp['USERDATA'] = $USERDATA;
        }
        if (@$ACCOUNTSTATUS !== '') {
            $resp['ACCOUNTSTATUS'] = $ACCOUNTSTATUS;
        }

        $this->response($resp, 200);
    }

    function verifytoken_post() {

        $MESSAGE = INSUFF_DATA;
        $STATUS = FAIL_STATUS;
        $USERDATA = '';
        $token = $this->post('token');
        if (strlen($token)) {
            // verify token

            $this->load->model("link_model", "link_model");
            $userId = $this->link_model->verifySignupLink($token);
            if ($userId) {
                $MESSAGE = ACCOUNT_ACTIVATED;
                $STATUS = SUCCESS_STATUS;
            } else {
                $MESSAGE = ACCOUNT_ACTIVATION_ERROR;
            }
        }

        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS
        );

        $this->response($resp, 200);
    }

    function verifypasswordtoken_post() {
        $MESSAGE = INSUFF_DATA;
        $STATUS = FAIL_STATUS;
        $USERDATA = '';
        $LINKID = '';
        $token = $this->post('token');
        if (strlen($token)) {
            // verify token
            $this->load->model("link_model", "link_model");
            $linkId = $this->link_model->verifyForgotPwdLink($token);
            if ($linkId) {
                $MESSAGE = '';
                $STATUS = SUCCESS_STATUS;
                $LINKID = $linkId;
            } else {
                $MESSAGE = FORGOTPASS_LINK_ERROR;
            }
        }

        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS,
            'LINKID' => $LINKID
        );

        $this->response($resp, 200);
    }

    /*
     * TO CREATE A NEW ACCOUNT
     * URL : http://your-site/ws/user/createAccount/
     * PARAM
     *      - userName              :   FIRST-NAME
     *      - userEmail             :   PRIMARY EMAIL ADDRESS
     *      - fbUserId              :   FACEBOOK LOGIN ID
     *      - userCuisine           :   CUISINE CHOICE; IT WILL REFLECTS FROM THE MASTER RECORD
     *                                  array('1', '2', '3', ....)
     * 
     *      - userInterest          :   CUISINE CHOICE; IT WILL REFLECTS FROM THE MASTER RECORD
     *                                  array('1', '2', '3', ....)
     * 
     *      - userMusic             :   CUISINE CHOICE; IT WILL REFLECTS FROM THE MASTER RECORD
     *                                  array('1', '2', '3', ....)
     */

    function fbLogin_post() {
        /*
         * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
         */
        $allowParam = array(
            'userEmail', 'fbUserId',
            'userCuisine', 'userInterest', 'userMusic', 'userCategory',
            'userFirstName', 'userLastName', 'plateForm', 'deviceToken'
        );
//mprd($allowParam);
//mprd($this->post());

        /*
         * TO SET DEFAULT VARIABLE VALUES...
         */

        $MESSAGE = INSUFF_DATA;
        $STATUS = FAIL_STATUS;
        $USERDATA = '';

        if (checkselectedparams($this->post(), $allowParam)) {
            /*
             * FIRST WE NEED TO CHECK THAT THIS EMAIL ADDRESS IS ALREADT EXISTS OR NOT...
             */
            $isExists = $this->user_model->isEmailExists($this->post('userEmail'));
            if (!$isExists) {
                /*
                 * CALL A FUNCTION TO CREATE AN ACCOUNT.
                 */
                $accountResp = $this->user_model->fbLogin($this->post());

                /*
                 * @var $accountResp type 
                 */
                if (@$accountResp !== '') {
                    $MESSAGE = ACCOUNT_SUCCESS;
                    $STATUS = SUCCESS_STATUS;

                    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['name'] != '') {
                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'user',
                                'id' => $accountResp['userId']
                            ),
                            'requireThumb' => TRUE
                        );

                        $this->load->library('fileupload', $param);
                        $uploadFiles = $this->fileupload->upload($_FILES, 'profilePic');
                    }

                    /*
                     * UPDATE A IMAGE TO THE DATABASE...
                     */
                    if (!empty($uploadFiles)) {
                        $this->user_model->updateProfilePic($uploadFiles[0], $accountResp['userId']);
                    }
                    $accountResp = $this->general_model->getUserBasicRecordById($accountResp['userId']);
                    $accountResp['hMac'] = genratemac($accountResp['userId']);

                    $USERDATA = $accountResp;
                } else {
                    $MESSAGE = ACCOUNT_ERROR;
                }
            } else {
                /*
                 * CALL A FUNCTION TO UPDATE THE ACCOUNT
                 */
                $accountResp = $this->user_model->updateAccount($this->post());

                /*
                 * @var $accountResp type 
                 */
                if (@$accountResp !== '') {
                    $MESSAGE = ACCOUNT_UPDATED;
                    $STATUS = SUCCESS_STATUS;
                    $USERDATA = $accountResp;
                } else {
                    $MESSAGE = ACCOUNT_ERROR;
                }
            }
        }

        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS
        );

        if (@$USERDATA !== '') {
            $resp['USERDATA'] = $USERDATA;
        }

        $this->response($resp, 200);
    }

    /*
     * TO CHECK FACEBOOKID IS EXISTS...
     */

    function fbUserIdCheck_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $USERDATA = array();
            $ACCOUNTDATA = FALSE;
            if (checkselectedparams($this->post(), array('fbUserId'))) {
                /*
                 * TO CHECK THIS FACEBOOK USER ID ALREADY EXIST OR NOT...
                 */
                $USERDATA = $this->user_model->fbUserIdCheck($this->post('fbUserId'));
                $STATUS = SUCCESS_STATUS;
                if (!empty($USERDATA)) {
                    $ACCOUNTDATA = TRUE;
                    $MESSAGE = ACCOUNT_EXISTS;
                } else {
                    $MESSAGE = ACCOUNT_NOT_EXISTS;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'ACCOUNTDATA' => $ACCOUNTDATA
            );
            if (@$USERDATA !== '') {
                $resp['USERDATA'] = $USERDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in fbUserIdCheck function - ' . $ex);
        }
    }

    function socialiteCreate_post() {
        
    }

    /**
     * Check if social a/c exists or not for a user
     *
     * @throws Exception
     * @return response
     */
    function socialiteCheck_post() {

        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $USERDATA = array();
            $ACCOUNTDATA = FALSE;
            // print_r($this->post('deviceToken'));die;
            if (checkselectedparams($this->post(), array('providerId', 'provider'))) {
                /*
                 * TO CHECK THIS FACEBOOK USER ID ALREADY EXIST OR NOT...
                 */
                $providerId = $this->post('providerId');
                $provider = $this->post('provider');
                $platForm = 'web';
                if (!empty($this->post('plateForm'))) {
                    $platForm = $this->post('plateForm');
                }
                $deviceToken = '';
                if (!empty($this->post('deviceToken'))) {
                    $deviceToken = $this->post('deviceToken');
                }
                $profilePic = '';
                if (!empty($this->post('profilePic'))) {
                    $profilePic = $this->post('profilePic');
                }
                if (strtolower($provider) == 'google') {
                    // new method created to check google plus account
                    $USERDATA = $this->user_model->gpUserIdCheck($providerId, $platForm, $deviceToken);
                    $STATUS = SUCCESS_STATUS;
                } else if (strtolower($provider) == 'facebook') {
                    $USERDATA = $this->user_model->fbUserIdCheck($providerId, $platForm, $deviceToken, $profilePic);
                    $STATUS = SUCCESS_STATUS;
                } else if (strtolower($provider) == 'twitter') {
                    // new method created to check google plus account
                    $USERDATA = $this->user_model->twitUserIdCheck($providerId, $platForm, $deviceToken);
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $STATUS = FAIL_STATUS;
                }

                if (!empty($USERDATA)) {
                    $ACCOUNTDATA = TRUE;
                    $MESSAGE = ACCOUNT_EXISTS;
                } else {
                    $MESSAGE = ACCOUNT_NOT_EXISTS;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'ACCOUNTDATA' => $ACCOUNTDATA
            );
            if (@$USERDATA !== '') {
                $resp['USERDATA'] = $USERDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in fbUserIdCheck function - ' . $ex);
        }
    }

    /*
     * TO SEND A MAIL ON FORGOTPASSWORD
     * URL: http://your-site/ws/user/forgotPassword/
     * PARAM
     *      -
     */

    function forgotPassword_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            if (checkselectedparams($this->post(), array('userEmail'))) {
                $response = $this->user_model->forgotPassword($this->post('userEmail'));

                if ($response) {
                    $MESSAGE = FORGOTPASS_RESET_SUCCESS;
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = FORGOTPASS_RESET_ERROR;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in forgotPassword function - ' . $ex);
        }
    }

    /*
     * TO CHANGE THE PASSWORD...
     */

    function changePassword_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            if (checkselectedparams($this->post(), array('userId', 'oldPassword', 'newPassword', 'confirmPassword'))) {
                $msgResp = $this->user_model->changePassword($this->post('userId'), $this->post('oldPassword'), $this->post('newPassword'), $this->post('confirmPassword'));
//echo $msgResp; exit;
                switch ($msgResp) {
                    case 103 :
                        $MESSAGE = PASSWORD_CHANGE_FAIL;
                        break;

                    case 102 :
                        $MESSAGE = PASSWORD_NOT_CONFIRM;
                        break;

                    case 101 :
                        $MESSAGE = PASSWORD_WRONG;
                        break;

                    case 100 :
                        $MESSAGE = PASSWORD_INVALID;
                        break;

                    case 200 :
                        $MESSAGE = PASSWORD_CHANGE_SUCCESS;
                        $STATUS = SUCCESS_STATUS;
                        break;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'resultCode' => $STATUS
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in changePassword_post function - ' . $ex);
        }
    }

    function updateForgotPassword_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), array('linkId', 'userId', 'newPassword', 'confirmPassword'))) {
                $msgResp = $this->user_model->changeForgotPassword($this->post('linkId'), $this->post('userId'), $this->post('newPassword'), $this->post('confirmPassword'));
                switch ($msgResp) {
                    case 103 :
                        $MESSAGE = PASSWORD_CHANGE_FAIL;
                        break;

                    case 102 :
                        $MESSAGE = PASSWORD_NOT_CONFIRM;
                        break;

                    case 101 :
                        $MESSAGE = 'Invalid Link';
                        break;

                    case 100 :
                        $MESSAGE = PASSWORD_INVALID;
                        break;

                    case 200 :
                        $MESSAGE = PASSWORD_CHANGE_SUCCESS;
                        $STATUS = SUCCESS_STATUS;
                        break;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in changePassword_post function - ' . $ex);
        }
    }

    /*
     * TO EDIT ACCOUNT
     * URL : http://your-site/ws/user/editProfile/
     * PARAM
     *      - userFirstname         :   FIRST-NAME
     *      - userLastname          :   LAST-NAME
     *      - userEmail             :   PRIMARY EMAIL ADDRESS
     *      - userMobile            :   VALID MOBILE NUMBER
     *      - userPassword          :   PASSWORD OF THE REGISTERED ACCOUNT
     *      - userGender            :   GENDER OF THE USER [ M / F / NOT DEFINE ]
     *      - profilePic            :   PROFILE PIC OF THE USER
     *      - userCuisine           :   CUISINE CHOICE; IT WILL REFLECTS FROM THE MASTER RECORD
     *                                  array('1', '2', '3', ....)
     *
     *      - userInterest          :   CUISINE CHOICE; IT WILL REFLECTS FROM THE MASTER RECORD
     *                                  array('1', '2', '3', ....)
     *
     *      - userMusic             :   CUISINE CHOICE; IT WILL REFLECTS FROM THE MASTER RECORD
     *                                  array('1', '2', '3', ....)
     */

    function editProfile_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */

            /* 'userPassword', */
            $allowParam = array(
                'userEmail', 'userMobile', 'userBirthDate',
                'userGender', 'userCuisine', 'userCategory',
                'userInterest', 'userMusic', 'userFirstName',
                'userLastName', 'userId', 'userAbout'
            );

//mprd($allowParam);
//mprd($this->post());

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $USERDATA = '';

            if (checkselectedparams($this->post(), $allowParam)) {
                /*
                 * CALL A FUNCTION TO CREATE AN ACCOUNT.
                 */
                $userId = $this->user_model->editAccount($this->post());
                /* @var $accountResp type */
                if (@$userId !== '') {

                    $targetpath = BASEDIR . 'images/user/' . $userId;

//echo $targetpath;
                    if (!is_dir($targetpath)) {
                        mkdir($targetpath, 0777, TRUE);
                    }
                    if (!is_dir($targetpath . "/thumb/")) {
                        mkdir($targetpath . "/thumb/", 0777, TRUE);
                    }

                    $uploadFiles = array();

//print_r($_FILES);

                    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['name'] != '') {
                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'user',
                                'id' => $userId
                            ),
                            'requireThumb' => TRUE
                        );

                        $this->load->library('fileupload', $param);
                        $this->fileupload->removeFile();
                        $uploadFiles = $this->fileupload->upload($_FILES, 'profilePic');
                    }

                    /*
                     * UPDATE A IMAGE TO THE DATABASE...
                     */
                    if (!empty($uploadFiles)) {
                        $this->user_model->updateProfilePic($uploadFiles[0], $userId);
                    }

                    $accountResp = $this->general_model->getUserBasicRecordById($userId);
                    $accountResp['hMac'] = genratemac($userId);


                    $MESSAGE = ACCOUNT_UPDATED;
                    $STATUS = SUCCESS_STATUS;
                    $USERDATA = $accountResp;
                } else {
                    $MESSAGE = ACCOUNT_ERROR;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$USERDATA !== '') {
                $resp['USERDATA'] = $USERDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in editProfile_post function - ' . $ex);
        }
    }

    /*
     * TO EDIT ACCOUNT - WEB REQUEST
     * URL : http://your-site/ws/user/editWebProfile/
     * PARAM
     *      - userFirstName         :   First name
     *      - userLastName          :   Last name
     *      - userEmail             :   PRIMARY EMAIL ADDRESS
     *      - userMobile            :   VALID MOBILE NUMBER
     *      - userGender            :   GENDER OF THE USER [ M / F / NOT DEFINE ]
     *      - userBirthDate         :   DATE OF BIRTH
     *      - userAnniversary       :   DATE OF ANNIVERSARY
     *      - userCuisine           :   CUISINE CHOICE; IT WILL REFLECTS FROM THE MASTER RECORD
     *                                  array('1', '2', '3', ....)
     *
     *      - userInterest          :   CUISINE CHOICE; IT WILL REFLECTS FROM THE MASTER RECORD
     *                                  array('1', '2', '3', ....)
     *
     *      - userMusic             :   CUISINE CHOICE; IT WILL REFLECTS FROM THE MASTER RECORD
     *                                  array('1', '2', '3', ....)
     *      - profilePic            :   PROFILE PIC OF THE USER
     */

    function editWebProfile_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */

            /* 'userPassword', */
            $allowParam = array(
                'userId', 'userFirstName', 'userLastName', 'userEmail', 'userMobile', 'userBirthDate'
                    // , 'userGender'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $USERDATA = '';

            if (checkselectedparams($this->post(), $allowParam)) {
                /*
                 * CALL A FUNCTION TO CREATE AN ACCOUNT.
                 */
                $userId = $this->user_model->editWebAccount($this->post());

                /* @var $accountResp type */
                if (@$userId !== '') {

                    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['name'] != '') {
                        $uploadFiles = array();
                        $targetpath = BASEDIR . 'images/user/' . $userId;

                        if (!is_dir($targetpath)) {
                            mkdir($targetpath, 0777, TRUE);
                        }
                        if (!is_dir($targetpath . "/thumb/")) {
                            mkdir($targetpath . "/thumb/", 0777, TRUE);
                        }
                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'user',
                                'id' => $userId
                            ),
                            'requireThumb' => TRUE
                        );

                        $this->load->library('fileupload', $param);
                        $this->fileupload->removeFile();
                        $uploadFiles = $this->fileupload->upload($_FILES, 'profilePic');
                    }
                    /*
                     * UPDATE A IMAGE TO THE DATABASE...
                     */
                    if (!empty($uploadFiles)) {
                        $this->user_model->updateProfilePic($uploadFiles[0], $userId);
                    }

                    $accountResp = $this->general_model->getUserBasicRecordById($userId);
                    $accountResp['hMac'] = genratemac($userId);


                    $MESSAGE = ACCOUNT_UPDATED;
                    $STATUS = SUCCESS_STATUS;
                    $USERDATA = $accountResp;
                } else {
                    $MESSAGE = ACCOUNT_ERROR;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$USERDATA !== '') {
                $resp['USERDATA'] = $USERDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in editProfile_post function - ' . $ex);
        }
    }

    function getUserBookmarks_post() {
        try {
            $allowParam = array('userId');
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $RESTAURANTDATA = '';

            if (checkselectedparams($this->post(), $allowParam)) {
                /*
                 * SEARCH FROM THE MODEL TO GET THE RESULT...
                 */
                $MESSAGE = NO_RECORD_FOUND;
                $STATUS = FAIL_STATUS;

                $returnRec = $this->user_model->userBookmarkList($this->post('userId'));
                if (!empty($returnRec)) {
                    $MESSAGE = RESTAURANT_FOUND;
                    $STATUS = SUCCESS_STATUS;
                    $RESTAURANTDATA = $returnRec;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$RESTAURANTDATA !== '') {
                $resp['RESTAURANTDATA'] = $RESTAURANTDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getUserBookmarks function - ' . $ex);
        }
    }

    function mergeAccountVerify_post() {
        try {
            $allowParam = array('userId', 'fullName', 'email', 'primaryEmail', 'secondaryEmail');
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $MERGEVERIFY = '';

            if (checkselectedparams($this->post(), $allowParam)) {
                /*
                 * SEARCH FROM THE MODEL TO GET THE RESULT...
                 */
                $MESSAGE = NO_RECORD_FOUND;
                $STATUS = FAIL_STATUS;

                $returnRec = $this->user_model->userMergeLinkGenerate($this->post());

                if ($returnRec === '1') {
                    $MESSAGE = MERGE_VERIFY_EMAIL;
                    $STATUS = SUCCESS_STATUS;
                    $MERGEVERIFY = $returnRec;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'MERGEVERIFY' => $MERGEVERIFY,
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in mergeAccountVerify function - ' . $ex);
        }
    }

    function mergeAccount_post() {
        try {
            $allowParam = array('token');
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $MERGEVERIFY = false;

            if (checkselectedparams($this->post(), $allowParam)) {
                /*
                 * SEARCH FROM THE MODEL TO GET THE RESULT...
                 */
                $MESSAGE = NO_RECORD_FOUND;
                $STATUS = FAIL_STATUS;

                $returnRec = $this->user_model->userMergeAccount($this->post('token'));
                if ($returnRec) {
                    $MESSAGE = MERGE_SUCCESSFUL;
                    $STATUS = SUCCESS_STATUS;
                    $MERGEVERIFY = $returnRec;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'MERGEVERIFY' => $MERGEVERIFY,
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in mergeAccountVerify function - ' . $ex);
        }
    }

    function redeemReward_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */

            $allowParam = array(
                'userId', 'rewardId'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $REDEEMAMOUNT = 0;

            if (checkselectedparams($this->post(), $allowParam)) {
                $response = $this->user_model->redeemReward($this->post());
                switch ($response) {
                    case -1 :
                        $MESSAGE = REWARD_RQST_FAIL;
                        break;

                    case 0 :
                        $MESSAGE = REWARD_RQST_OLD;
                        break;

                    default :
                        $MESSAGE = REWARD_RQST;
                        $STATUS = SUCCESS_STATUS;
                        $REDEEMAMOUNT = $this->general_model->getRedeemAmount($this->post('userId'));
                        break;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'REDEEMAMOUNT' => $REDEEMAMOUNT
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in redeemReward_post function - ' . $ex);
        }
    }

    /*
     * TO CHECK USER CHECKIN FUNCTINALITY
     */

    function userCheckIn_post() {
        try {

            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */

            $allowParam = array(
                'userId', 'restaurantId'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $REDEEMAMOUNT = 0;

            if (checkselectedparams($this->post(), $allowParam)) {
                $response = $this->user_model->uesrCheckIn($this->post());
                if ($response != '-3' && $response != '-2' && $response != '-1') {
                    $checkinId = $response;
                    if (isset($_FILES['images']) && !empty($_FILES['images'])) {
                        $_FILES['checkInImages'] = $_FILES['images'];
                        unset($_FILES['images']);
                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'checkin',
                                'id' => $checkinId
                            ),
                            'requireThumb' => TRUE
                        );
                        $this->load->library('fileupload', $param);
                        $this->fileupload->setConfig($param);
                        $this->fileupload->removeFile();
                        $uploadFiles = $this->fileupload->upload($_FILES, 'checkInImages');
                        $images['checkInImages'] = $uploadFiles;
                    }
                    if ($images) {
                        $this->user_model->addImages($images, $checkinId);
                    }
                }
                switch ($response) {
                    case -3 :
                        $MESSAGE = CHECKIN_ALREADY;
                        $REDEEMAMOUNT = $this->general_model->getRedeemAmount($this->post('userId'));
                        break;

                    case -2 :
                        $MESSAGE = CHECKIN_COVERAGE_FAIL;
                        break;

                    default :
                        $MESSAGE = CHECKIN_SUCC;
                        $STATUS = SUCCESS_STATUS;
                        $REDEEMAMOUNT = $this->general_model->getRedeemAmount($this->post('userId'));
                        break;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'totalPoint' => $REDEEMAMOUNT
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in userCheckIn function - ' . $ex);
        }
    }

    /*
     * TO GET ALL CHECK IN LIST
     */

    function getCheckInList_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */

            $allowParam = array(
                'userId'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $CHECKINRECORD = array();
            $TOTALPAGE = $TOTALRECORD = 0;

            if (checkselectedparams($this->post(), $allowParam)) {
                $CHECKINRECORD = $this->user_model->getCheckInList($this->post());

                if ($CHECKINRECORD['totalRecord'] != 0) {

                    $MESSAGE = CHECKIN_REC_SUCC;
                    $STATUS = SUCCESS_STATUS;

                    $TOTALPAGE = $CHECKINRECORD['totalPage'];
                    $TOTALRECORD = $CHECKINRECORD['totalRecord'];
                    $CHECKINRECORD = $CHECKINRECORD['foundRec'];
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'TOTALPAGE' => $TOTALPAGE,
                'TOTALRECORD' => $TOTALRECORD,
                'CHECKINRECORD' => $CHECKINRECORD
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getCheckInList function - ' . $ex);
        }
    }

    function updateUserChoice_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //if (checkselectedparams($this->post(), array('cuisine', 'oldPassword', 'newPassword', 'confirmPassword'))) {
            //}

            $cuisine = $this->post('cuisine');
            $interest = $this->post('interest');
            $music = $this->post('music');

            //$resp = $this->user_model->updateUserChoice();

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('User Controller : Error in updateUserChoice_post function - ' . $ex);
        }
    }

    /*
     * FOR THE VENDOR
     * TO GET LIST OF REVIEWS...
     */

    function getAllReviews_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'vId'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $REVIEWDATA = array();

            if (checkselectedparams($this->post(), $allowParam)) {
                $REVIEWDATA = $this->user_model->getAllReviews($this->post('vId'));
                if (!empty($REVIEWDATA)) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = REVIEW_FOUND_SUCC;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'RESULT' => array(
                    'MESSAGE' => $MESSAGE,
                    'STATUS' => $STATUS,
                    'REVIEWDATA' => $REVIEWDATA,
                )
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getAllReviews_post function - ' . $ex);
        }
    }

    /*
     * TO INVITE THE FRIENDS
     */

    function inviteFriends_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'userId', 'inviteFriends'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->user_model->inviteFriends();
                switch ($resp) {
                    case -2:
                        $MESSAGE = INVITE_ERR;
                        break;

                    default:
                        $MESSAGE = INVITE_SUCC;
                        $STATUS = SUCCESS_STATUS;
                        break;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'totalPoint' => $this->general_model->getRedeemAmount($this->post('userId'))
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in inviteFriends function - ' . $ex);
        }
    }

    /*
     * USER SUBSCRIPTION
     */

    function userSubscription_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'userId', 'timePeriod'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->user_model->userSubscription($this->post());

                switch ($resp) {
                    case -2:
                        $MESSAGE = SUBSCRIBED_BEFORE;
                        break;

                    default:
                        $MESSAGE = SUBSCRIBED_DONE;
                        $STATUS = SUCCESS_STATUS;
                        break;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in userSubscription function - ' . $ex);
        }
    }

    /*
     * TO CHANGE THE USER NOTIFICATION BOOLEAN
     */

    function changeNotifySetting_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'userId', 'switchValue'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->user_model->changeNotifySetting($this->post());

                if ($resp != -1) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = NOTIFY_CHANGE;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in changeNotifySetting_post function - ' . $ex);
        }
    }

    /*
     * USER NOTIFICATION LIST
     */

    function notificationList_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'userId'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $NOTIFICATIONLIST = array();

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->user_model->notificationList($this->post('userId'));

                if (!empty($resp)) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = NOTIFY_FOUND;
                    $NOTIFICATIONLIST = $resp;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'NOTIFICATIONLIST' => $NOTIFICATIONLIST
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in notificationList_post function - ' . $ex);
        }
    }

    /*
     * CLEAR ALL NOTIFICATION LIST
     */

    function clearNotification_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'userId'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->user_model->clearNotification($this->post('userId'));

                if ($resp != -1) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = NOTIFY_CLR_SUCC;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in clearNotification_post function - ' . $ex);
        }
    }

    /*
     * RESET USER BADGE COUNT
     */

    function resetUserBadge_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'userId'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->user_model->resetUserBadge($this->post('userId'));

                if ($resp != -1) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = BADGE_CLR_SUCC;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in resetUserBadge function - ' . $ex);
        }
    }

    /**
     * Return all the choice list from db
     * music, interests, cuisines
     *
     * @throws Exception
     */
    function allChoiceList_get() {

        try {

            $CHOICEDATA = array();
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            $this->load->model("cuisine_model", "cuisine_model");
            $CHOICEDATA['cuisine'] = $this->cuisine_model->activeCuisine();

            $this->load->model("music_model", "music_model");
            $CHOICEDATA['music'] = $this->music_model->activeMusic();

            $this->load->model("category_model", "category_model");
            $CHOICEDATA['interest'] = $this->category_model->activeInterest();

            if (@$CHOICEDATA !== '') {
                $resp = [
                    'CHOICEDATA' => $CHOICEDATA,
                    'MESSAGE' => CHOICES_FOUND,
                    'STATUS' => SUCCESS_STATUS
                ];
            } else {
                $resp = array(
                    'MESSAGE' => $MESSAGE,
                    'STATUS' => $STATUS
                );
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in activeCuisines function - ' . $ex);
        }
    }

    function getUsersList_post() {
        try {
            $MESSAGE = '';
            $STATUS = SUCCESS_STATUS;
            $USERDATA = $this->user_model->userList($this->post());

            if (empty($USERDATA)) {
                $MESSAGE = NO_RECORD_FOUND;
            }
            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$USERDATA !== '') {
                $resp['USER'] = $USERDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getUsersList function - ' . $ex);
        }
    }

    function getFollowersAndFollowing_post() {
        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $allowParam = array(
                'userId'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            if (checkselectedparams($this->post(), $allowParam)) {
                $countArrRes = $this->user_model->getFollowersAndFollowing($this->post('userId'));
                if ($countArrRes != -1) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = '';
                }
            }
            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'FOLLOWDATA' => $countArrRes
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getUsersList function - ' . $ex);
        }
    }

    /*
     * TO GET THE CUISINE / MUSIC / INTEREST LIST
     */

    function getCusineDetails_post() {
        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $USERCHOICEDATA = '';

            $MESSAGE = USERCHOICE_RECORD_FOUND;
            $STATUS = SUCCESS_STATUS;
            $USERCHOICEDATA['userCuisine'] = $this->user_model->getCIMUserArray('cuisine');
            $USERCHOICEDATA['userInterest'] = $this->user_model->getCIMUserArray('interest');
            $USERCHOICEDATA['userCategory'] = $this->user_model->getCIMUserArray('category');
            $USERCHOICEDATA['userMusic'] = $this->user_model->getCIMUserArray('music');
            $locRec = $this->user_model->getCIMUserArray('location');
            $USERCHOICEDATA['userLocation'] = $this->_locArry($locRec);

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if ($USERCHOICEDATA !== '') {
                $resp['USERCHOICEDATA'] = $USERCHOICEDATA;
            }
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getUserChoiceList function - ' . $ex);
        }
    }

    function getReviewsCount_post() {
        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $allowParam = array(
                'userId'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            if (checkselectedparams($this->post(), $allowParam)) {
                $countArrRes = $this->user_model->getUserReviewsCount($this->post('userId'));
                if ($countArrRes != -1) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = '';
                }
            }
            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'REVIEWDATA' => $countArrRes
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getReviews function - ' . $ex);
        }
    }

    function getRatingCount_post() {
        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $allowParam = array(
                'userId'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            if (checkselectedparams($this->post(), $allowParam)) {
                $countArrRes = $this->user_model->getUserRatingsCount($this->post('userId'));
                if ($countArrRes != -1) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = '';
                }
            }
            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'RATINGDATA' => $countArrRes
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getReviews function - ' . $ex);
        }
    }

    function getPhotosCount_post() {
        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $allowParam = array(
                'userId'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            if (checkselectedparams($this->post(), $allowParam)) {
                $countArrRes = $this->user_model->getUserPhotosCount($this->post('userId'));
                if ($countArrRes != -1) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = '';
                }
            }
            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'PHOTODATA' => $countArrRes
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getReviews function - ' . $ex);
        }
    }

    // get users total active points
    function getLevel_post() {
        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $allowParam = array(
                'userId'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            if (checkselectedparams($this->post(), $allowParam)) {
                $totalPoints = $this->user_model->getTotalPoints($this->post('userId'));
                $level = $this->user_model->getUserLevel($totalPoints);
                $levelList = $this->user_model->getUserLevelList();
                $alllevelList = $this->user_model->getAllLevelsList();

                if ($level != -1) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = '';
                }
            }
            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'LEVELDATA' => array("level" => $level['name'], "levelNo" => 'Level ' . $level['level'], "levelImage" => $level['image'], "points" => $totalPoints, 'allLevel' => $alllevelList, "levelList" => array_keys($levelList))
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getReviews function - ' . $ex);
        }
    }

    function getUserFeed_post() {
        try {
            $allowParam = array(
                'userId'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $userFeeds = [];
            if (checkselectedparams($this->post(), $allowParam)) {
                $userFeeds = $this->user_model->getUserFeeds($this->post('userId'));
                if ($userFeeds !== false) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = '';
                } else {
                    $STATUS = FAIL_STATUS;
                    $MESSAGE = 'Unable to get feeds';
                }
            }
        } catch (Exception $ex) {
            $STATUS = FAIL_STATUS;
            $MESSAGE = $ex->getMessage();
        }

        // Sending the response
        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS,
            'DATA' => $userFeeds
        );
        $this->response($resp, 200);
    }

    function getFeedActivities_post() {
        try {
            $allowParam = array(
                'userId'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $othersFeeds = [];
            $selfFeeds = [];
            if (checkselectedparams($this->post(), $allowParam)) {
                $othersFeeds = $this->user_model->getOthersActivities($this->post('userId'));
                $selfFeeds = $this->user_model->getSelfActivities($this->post('userId'));
                if ($othersFeeds !== false && $selfFeeds !== false) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = '';
                } else {
                    $STATUS = FAIL_STATUS;
                    $MESSAGE = 'Unable to get feeds';
                }
            }
        } catch (Exception $ex) {
            $STATUS = FAIL_STATUS;
            $MESSAGE = $ex->getMessage();
        }

        // Sending the response
        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS,
            'DATA' => ["selfFeeds" => $selfFeeds, "othersFeeds" => $othersFeeds]
        );
        $this->response($resp, 200);
    }

    function reviewLikeUnlike_post() {
        try {
            $allowParam = array(
                'userId', 'reviewId', 'action'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $likeCount = 0;
            if (checkselectedparams($this->post(), $allowParam)) {
                $likeCount = $this->user_model->reviewLikeUnlike($this->post('userId'), $this->post('reviewId'), $this->post('action'));
                if ($likeCount !== FALSE) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = 'Successfully ' . $this->post('action') . 'd the review';
                } else {
                    $STATUS = FAIL_STATUS;
                    $MESSAGE = 'Unable to ' . $this->post('action') . ' the review';
                }
            }
        } catch (Exception $ex) {
            $STATUS = FAIL_STATUS;
            $MESSAGE = $ex->getMessage();
        }

        // Sending the response
        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS,
            'LIKECOUNT' => $likeCount
        );
        $this->response($resp, 200);
    }

    function reviewAddComment_post() {
        try {
            $allowParam = array(
                'userId', 'reviewId', 'comment'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $commentCount = 0;
            if (checkselectedparams($this->post(), $allowParam)) {
                $commentCount = $this->user_model->reviewAddComment($this->post('userId'), $this->post('reviewId'), $this->post('comment'));
                if ($commentCount !== FALSE) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = 'Successfully commented on review';
                } else {
                    $STATUS = FAIL_STATUS;
                    $MESSAGE = 'Unable to comment on the review';
                }
            }
        } catch (Exception $ex) {
            $STATUS = FAIL_STATUS;
            $MESSAGE = $ex->getMessage();
        }

        // Sending the response
        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS,
            'COMMENTCOUNT' => $commentCount
        );
        $this->response($resp, 200);
    }

    function getReviewComments_post() {
        try {
            $allowParam = array(
                'userId', 'reviewId'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $comments = [];
            if (checkselectedparams($this->post(), $allowParam)) {
                $comments = $this->user_model->getReviewComments($this->post('reviewId'));
                if ($comments !== FALSE) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = 'Successfully commented on review';
                } else {
                    $STATUS = FAIL_STATUS;
                    $MESSAGE = 'Unable to comment on the review';
                }
            }
        } catch (Exception $ex) {
            $STATUS = FAIL_STATUS;
            $MESSAGE = $ex->getMessage();
        }

        // Sending the response
        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS,
            'COMMENTS' => $comments
        );
        $this->response($resp, 200);
    }

    function searchUser_post() {
        try {
            $allowParam = array(
                'userId', 'searchText'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $users = [];
            if (checkselectedparams($this->post(), $allowParam)) {
                $users = $this->user_model->searchUser($this->post('userId'), $this->post('searchText'));
                if ($users !== FALSE) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = '';
                } else {
                    $STATUS = FAIL_STATUS;
                    $MESSAGE = 'No user found.';
                }
            }
        } catch (Exception $ex) {
            $STATUS = FAIL_STATUS;
            $MESSAGE = $ex->getMessage();
        }

        // Sending the response
        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS,
            'DATA' => $users
        );
        $this->response($resp, 200);
    }

    function reportReview_post() {
        try {
            $allowParam = array(
                'userId', 'reviewId', 'reportType'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            if (checkselectedparams($this->post(), $allowParam)) {
                $status = $this->user_model->reportReview($this->post('userId'), $this->post('reviewId'), $this->post('reportType'));
                if ($status !== FALSE) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = '';
                } else {
                    $STATUS = FAIL_STATUS;
                    $MESSAGE = 'Unable to send report';
                }
            }
        } catch (Exception $ex) {
            $STATUS = FAIL_STATUS;
            $MESSAGE = $ex->getMessage();
        }

        // Sending the response
        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS,
        );
        $this->response($resp, 200);
    }

    function addFollowers_post() {
        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $allowParam = array(
                'userId', 'followerUserID'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $followCount = 0;
            if (checkselectedparams($this->post(), $allowParam)) {
                $check = $this->user_model->checkFollowers($this->post('userId')
                        , $this->post('followerUserID'));
                if ($check) {
                    $followCount = $this->user_model->addFollowers($this->post('userId')
                            , $this->post('followerUserID'));
                    if ($followCount) {
                        $status = true;
                    }
                } else {
                    $status = false;
                }
                if ($status) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = 'Successfully followed the user';
                } else {
                    $STATUS = FAIL_STATUS;
                    $MESSAGE = 'Unable to follow the user';
                }
            }
        } catch (Exception $ex) {
            $STATUS = FAIL_STATUS;
            $MESSAGE = $ex->getMessage();
        }

        // Sending the response
        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS,
            'FOLLOWCOUNT' => $followCount
        );
        $this->response($resp, 200);
    }

    function whoToFollowList_post() {
        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $allowParam = array(
                'followerUserID'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            if (checkselectedparams($this->post(), $allowParam)) {
                $followerList = $this->user_model->whoToFollowList($this->post('followerUserID'));
            }
            if (count($followerList) > 0) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = 'Successfully fetched the followers';
            } else {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = 'No users to follow';
            }
        } catch (Exception $ex) {
            $STATUS = FAIL_STATUS;
            $MESSAGE = $ex->getMessage();
            $followerList = '';
        }

        // Sending the response
        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS,
            'DATA' => $followerList
        );
        $this->response($resp, 200);
    }

    function getVouchers_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            if (checkselectedparams($this->post(), array('userId'))) {
                $this->load->model('referencecode_model');
                if ($this->post("type") == 'availed') {
                    $response = $this->referencecode_model->getAvailedVouchers($this->post('userId'));
                } elseif ($this->post("type") == 'notavailed') {
                    $response = $this->referencecode_model->getUnavailedVouchers($this->post('userId'));
                } else {
                    $response1 = $this->referencecode_model->getAvailedVouchers($this->post('userId'));
                    $response2 = $this->referencecode_model->getUnavailedVouchers($this->post('userId'));
                    $response = array_merge($response1, $response2);
                }

                if ($response) {
                    $MESSAGE = 'FOUND SUCCESSFULLY';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = 'No Record Found';
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'DATA' => $response
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getVouchers function - ' . $ex);
        }
    }

    function getPurchase_post() {
        try {
            /* myvo
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            if (checkselectedparams($this->post(), array('userId'))) {
                $response = $this->user_model->getPurchase($this->post('userId'));

                if ($response) {
                    $MESSAGE = 'FOUND SUCCESSFULLY';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = 'No Record Found';
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'DATA' => $response
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getPurchase function - ' . $ex);
        }
    }

    function allfriendList_post() {
        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $USERLIST = array();

            $resp = $this->user_model->friendList();
            if (!empty($resp)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = 'User(s) Found';
                $USERLIST = $resp;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'USERLIST' => $USERLIST
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in alluser_get function - ' . $ex);
        }
    }

    function getUserBadges_post() {
        try {
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $response = '';

            if (checkselectedparams($this->post(), array('userId'))) {
                $response = $this->user_model->getUserBadges($this->post('userId'));
                if ($response) {
                    $MESSAGE = 'FOUND SUCCESSFULLY';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = 'No Recod Found';
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'DATA' => $response
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getUserBadges function - ' . $ex);
        }
    }

    function getUserReviewAndComment_post() {
        try {
            $allowParam = array(
                'userId'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $userFeeds = [];
            if (checkselectedparams($this->post(), $allowParam)) {
                $userFeeds = $this->user_model->getExpertReviews($this->post());
                if ($userFeeds !== false) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = '';
                } else {
                    $STATUS = FAIL_STATUS;
                    $MESSAGE = 'No Record Found';
                }
            }
        } catch (Exception $ex) {
            $STATUS = FAIL_STATUS;
            $MESSAGE = $ex->getMessage();
        }

        // Sending the response
        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS,
            'DATA' => $userFeeds
        );
        $this->response($resp, 200);
    }

    function getUserRatings_post() {
        try {
            $allowParam = array(
                'userId'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $userFeeds = [];
            if (checkselectedparams($this->post(), $allowParam)) {
                $userFeeds = $this->user_model->getRatings($this->post());
                if ($userFeeds !== false) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = '';
                } else {
                    $STATUS = FAIL_STATUS;
                    $MESSAGE = 'No Record Found';
                }
            }
        } catch (Exception $ex) {
            $STATUS = FAIL_STATUS;
            $MESSAGE = $ex->getMessage();
        }

        // Sending the response
        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS,
            'DATA' => $userFeeds
        );
        $this->response($resp, 200);
    }

    /*
     * TO CHECK USER CHECKIN FUNCTINALITY
     */

    function userPublish_post() {
        try {

            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */

            $allowParam = array(
                'userId', 'restaurantId'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $REDEEMAMOUNT = 0;

            if (checkselectedparams($this->post(), $allowParam)) {
                $response = $this->user_model->uesrPublish($this->post());
                if (!empty($response)) {
                    $publishId = $response;
                    if (isset($_FILES['images']) && !empty($_FILES['images'])) {
                        $_FILES['publishImages'] = $_FILES['images'];
                        unset($_FILES['images']);
                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'publish',
                                'id' => $publishId
                            ),
                            'requireThumb' => TRUE
                        );
                        $this->load->library('fileupload', $param);
                        $this->fileupload->setConfig($param);
                        $this->fileupload->removeFile();
                        $uploadFiles = $this->fileupload->upload($_FILES, 'publishImages');
                        $images['publishImages'] = $uploadFiles;
                    }
                    if ($images) {
                        $this->user_model->addImages($images, $publishId);
                    }
                    $MESSAGE = 'Published Successfully';
                    $STATUS = SUCCESS_STATUS;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in userPublish function - ' . $ex);
        }
    }

    /*
     * TO CHECK USER CHECKIN FUNCTINALITY
     */

    function writeReview_post() {
        try {

            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */

            $allowParam = array(
                'userId', 'restaurantId'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $RATING = 0;

            if (checkselectedparams($this->post(), $allowParam)) {
                $review = $this->user_model->postReview($this->post());
                if ($review['iReviewID']) {
                    if (isset($_FILES['images']) && !empty($_FILES['images'])) {
                        $_FILES['reviewImages'] = $_FILES['images'];
                        unset($_FILES['images']);
                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'reviewImages',
                                'id' => $review['iReviewID'],
                            ),
                            'requireThumb' => TRUE,
                        );

                        $this->load->library('fileupload', $param);
                        $this->fileupload->setConfig($param);
                        $this->fileupload->removeFile();
                        $uploadFiles = $this->fileupload->upload($_FILES, 'reviewImages');
                        $images['reviewImages'] = $uploadFiles;
                        //$this->restaurant_model->postWebReviewImages($this->post(), $review, $uploadFiles);
                    }
                    if ($images) {
                        $this->user_model->addImages($images, $review["iReviewID"], $this->post('restaurantId'));
                    }
                    if (($this->post('message')) && $this->post('message') != '') {
                        $MESSAGE = REVIEW_RECORD_SAVE;
                    } else {
                        $MESSAGE = RATING_RECORD_SAVE;
                    }
                    $STATUS = SUCCESS_STATUS;
                    $RATING = isset($review['rating']) ? $review['rating'] : 0;
                } else {
                    $MESSAGE = "Unable to post review. Please try again !";
                }
            }
            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'rating' => $RATING,
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in userPublish function - ' . $ex);
        }
    }

    function expertAreas_post() {
        try {

            $allowParam = array(
                'userId'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $REDEEMAMOUNT = 0;
            $response = array();
            if (checkselectedparams($this->post(), $allowParam)) {
                $response = $this->user_model->getExpertAreas($this->post());
                if (!empty($response)) {
                    $MESSAGE = '';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $STATUS = FAIL_STATUS;
                    $MESSAGE = 'No Record Found';
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'LOCATION' => $response
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in userPublish function - ' . $ex);
        }
    }

    function getUserPublishedPhoto_post() {

        try {
            $allowParam = array(
                'userId'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $response = '';

            if (checkselectedparams($this->post(), $allowParam)) {
                $response = $this->user_model->getUserPublishedPhoto($this->post());
                if ($response) {
                    $MESSAGE = 'FOUND SUCCESSFULLY';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = 'No Recod Found';
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'DATA' => $response
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getUserBadges function - ' . $ex);
        }
    }

    function updateDeviceToken_post() {

        try {
            $allowParam = array(
                'userId', 'deviceToken'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $response = '';

            if (checkselectedparams($this->post(), $allowParam)) {
                $response = $this->user_model->updateDeviceToken($this->post());
                if ($response) {
                    $MESSAGE = 'UPDATED SUCCESSFULLY';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = 'No Recod Found';
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in updateDeviceToken function - ' . $ex);
        }
    }

    function verifyUserReferralCode_post() {

        try {
            $allowParam = array(
                'userId', 'referralCode'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $response = '';

            if (checkselectedparams($this->post(), $allowParam)) {
                $response = $this->user_model->verifyUserReferralCode($this->post('userId'), $this->post('referralCode'));
                if ($response) {
                    $MESSAGE = 'Referral code applied successfully';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = 'Invalid referral code';
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in updateDeviceToken function - ' . $ex);
        }
    }

}
