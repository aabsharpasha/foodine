<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of user_model
 * @author OpenXcell Technolabs
 */
class User_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_user';
    }

    /*
     * TO CHECK THAT THIS EMAIL ADDRESS ALEADY EXISTS OR NOT...
     */

    function isEmailExists($emailId = '', $userId = '') {
        try {
            if ($userId != '') {
                $this->db->where_not_in('iUserID', $userId);
            }
            if ($emailId !== '') {
                $res = $this->db->get_where($this->table, array('vEmail' => $emailId));
                /*
                 * echo $this->db->last_query();
                 * echo ' >> ';
                 * echo $res->num_rows();
                 * exit;
                 */

                if ($userId != '') {
                    if ($res->num_rows() == 0)
                        return TRUE;
                    else
                        return FALSE;
                } else {
                    if ($res->num_rows() > 0)
                        return TRUE;
                    else
                        return FALSE;
                }
                //return $userId != '' && $res->num_rows() == 0 ? TRUE : ($res->num_rows() > 0 ? TRUE : FALSE);
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            exit('User Model : Error in isEmailExists function - ' . $ex);
        }
    }

    function getUserIdFromEmail($emailId) {
        try {
            if ($emailId !== '') {
                $res = $this->db->select('iUserID')->get_where($this->table, array('vEmail' => $emailId))->row_array();
                if (count($res))
                    return $res['iUserID'];
                else
                    return FALSE;
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            exit('User Model : Error in getUserIdFromEmail function - ' . $ex);
        }
    }

    /*
     * NEED TO ADD CUISINE RECORDS
     */

    private function _addUserCIMValues($userId, $type = 'cuisine', $record = array(), $isEdit = FALSE) {
        try {
            $KEY = $TBL = '';
            switch ($type) {
                case 'cuisine' :
                    $KEY = 'iCuisineID';
                    $TBL = 'tbl_user_cuisine';
                    break;

                case 'interest' :
                    $KEY = 'iInterestID';
                    $TBL = 'tbl_user_interest';
                    break;

                case 'music' :
                    $KEY = 'iMusicID';
                    $TBL = 'tbl_user_music';
                    break;

                case 'category' :
                    $KEY = 'iCategoryID';
                    $TBL = 'tbl_user_category';
                    break;
                case 'sort' :
                    $KEY = '';
                    $TBL = 'foodine_user_sort';
                    break;
            }
            if ($isEdit) {
                /*
                 * IF USER WANTS TO EDIT THEN DELETE ALL THE OLD REORDS..
                 */
                if($type == 'sort') 
                    $this->db->delete($TBL, array('userId' => $userId));
                else
                    $this->db->delete($TBL, array('iUserID' => $userId));
                    
                
            }
          // print_r($record); 
            if($type == 'interest') {
                //echo 'hello'; exit;
            }
            
            if (!empty($record)) {
                $data = array(
                    'iUserID' => $userId,
                    'tCreatedAt' => date('Y-m-d H:i:s')
                );
                if($type == 'sort') {
                     foreach ($record as $v) {
                         $data = array('userId' => $userId, 'sortName' => $v['title'], 'sortOrder' => 'desc', 'selected' => ($v['selected']));
                        $this->db->insert($TBL, $data);
                        unset($data[$KEY]);
                     }
                } else {
                    foreach ($record as $v) {
//                        if($type == 'interest') {
//                            print_r($v); exit;
//                        }
//var_dump($v['selected']); exit;
                        if($v['selected']) {
                            $data[$KEY] = $v['id'];

                            if ($v['id'] !== '')
                                $this->db->insert($TBL, $data);
                        }
                        unset($data[$KEY]);
                    }
                }
            }
        } catch (Exception $ex) {
            throw new Exception('Error in _addUserCIMValues function - ' . $ex);
        }
    }

    /*
     * TO CREATE AN ACCOUNT
     */

    function createAccount($postValues) {
        try {
            $returnValue = '';
            if (!empty($postValues)) {
                extract($postValues);
                $referralCode = strtoupper(substr($firstName, 0, 3)) . rand(100, 999);
                /*
                 * PREPARE THE INSERT DATA ARRAY...
                 */
                $data = array(
                    //'vFullName' => @$userName,
                    'vFirstName' => @$firstName,
                    'vLastName' => @$lastName,
                    'vEmail' => @$email,
                    'vMobileNo' => @$mobileNumber,
                    'vPassword' => md5(@$password),
                    'eGender' => @$gender,
                    'tCreatedAt' => date('Y-m-d H:i:s'),
                    'dtDOB' => $dateOfBirth,
                    'dtAnniversary' => $anniversary,
                    'ePlatform' => @$deviceType,
                    'vDeviceToken' => @$deviceToken,
                    'registrationType' => @$registrationType,
                    'vReferralCode' => $referralCode
                );
                $this->db->insert($this->table, $data);
                $userId = $this->db->insert_id();
               
            }

            return $userId;
        } catch (Exception $ex) {
            exit('User Model : Error in createAccount function - ' . $ex);
        }
    }

    /*
     * TO CREATE WEB ACCOUNT
     */

    function createWebAccount($postValues) {
        try {
            $returnValue = '';
            if (!empty($postValues)) {
                extract($postValues);

                /*
                 * PREPARE THE INSERT DATA ARRAY...
                 */
                $referralCode = strtoupper(substr($userFirstName, 0, 3)) . rand(100, 999);
                $data = array(
                    'vFullName' => @$userFullName,
                    'vFirstName' => $userFirstName,
                    'vLastName' => $userLastName,
                    'vEmail' => @$userEmail,
                    'vMobileNo' => @$userMobile,
                    'vPassword' => md5(@$userPassword),
                    'eGender' => @$userGender,
                    'dtDOB' => @$userDob,
                    'tCreatedAt' => date('Y-m-d H:i:s'),
                    'eStatus' => ACCOUNT_STATUS_INACTIVE,
                    'vReferralCode' => $referralCode
                );
                $this->db->insert($this->table, $data);
                $userId = $this->db->insert_id();
                if ($this->db->affected_rows() > 0) {
                    //add user points
                    $this->load->model('user_points_model');
//                    $this->user_points_model->addUserPoints($userId, 8);

                    $inviterQuery = "Select iUserID FROM tbl_user_invite WHERE vInviteEmail='$userEmail' ORDER BY iUserInviteID LIMIT 1";
                    $inviterRes = $this->db->query($inviterQuery)->row_array();
                    if (!empty($inviterRes['iUserID'])) {
                        $this->user_points_model->addUserPoints($inviterRes['iUserID'], 6);
                    }
                    // generate 24 hr exipry signup verification link
                    $link = sha1(uniqid(rand(), TRUE));
                    $linkData = [
                        'userid' => $userId,
                        'hash' => $link,
                        'datecreated' => date('Y-m-d H:i:s'),
                    ];

                    $this->load->model("link_model", "link_model");
                    if ($this->link_model->createSingupLink($linkData)) {
                        /*
                         * SEND A MAIL...
                         */
                        $subject = 'Verify your account';
                        $this->load->model("smtpmail_model", "smtpmail_model");
                        $param = array(
                            '%MAILSUBJECT%' => 'Foodine : ' . $subject,
                            '%LOGO_IMAGE%' => BASEURL . 'images/Foodine.png',
                            '%USER_NAME%' => @$userFullName,
                            '%LINK%' => WEB_USER_ACTIVATION . $link
                        );

                        $tmplt = DIR_VIEW . 'email/signup_verify.php';
                        $subject = 'Foodine : ' . $subject;
                        $to = @$userEmail;
                        $this->smtpmail_model->send($to, $subject, $tmplt, $param);
                    }
                    $returnValue = $userId;
                }
            }

            return $returnValue;
        } catch (Exception $ex) {
            exit('User Model : Error in createAccount function - ' . $ex);
        }
    }

    /*
     * TO CREATE WEB ACCOUNT
     */

    function createAppAccount($postValues) {
        try {
            $returnValue = '';
            if (!empty($postValues)) {
                extract($postValues);
                $userFullName = $userFirstName . ' ' . $userLastName;
                /*
                 * PREPARE THE INSERT DATA ARRAY...
                 */
                $referralCode = strtoupper(substr($userFirstName, 0, 3)) . rand(100, 999);
                if (isset($userDob) && $userDob != '') {
                    $userDob = date('Y-m-d', strtotime(@$userDob));
                } else {
                    $userDob = '';
                }
                $data = array(
                    'vFullName' => @$userFullName,
                    'vFirstName' => @$userFirstName,
                    'vLastName' => @$userLastName,
                    'vEmail' => @$userEmail,
                    'eGender' => isset($userGender) ? $userGender : '',
                    'vMobileNo' => @$userMobile,
                    'vPassword' => md5(@$userPassword),
                    'dtDOB' => @$userDob,
                    'tCreatedAt' => date('Y-m-d H:i:s'),
                    'eStatus' => ACCOUNT_STATUS_INACTIVE,
                    'ePlatform' => @$plateForm,
                    'vDeviceToken' => @$deviceToken,
                    'vReferralCode' => $referralCode
                );
                $this->db->insert($this->table, $data);
                $userId = $this->db->insert_id();
                if ($this->db->affected_rows() > 0) {
                    //add user points
                    $this->load->model('user_points_model');
                    $this->user_points_model->addUserPoints($userId, 8);

                    $inviterQuery = "Select iUserID FROM tbl_user_invite WHERE vInviteEmail='$userEmail' ORDER BY iUserInviteID LIMIT 1";
                    $inviterRes = $this->db->query($inviterQuery)->row_array();
                    if (!empty($inviterRes['iUserID'])) {
                        $this->user_points_model->addUserPoints($inviterRes['iUserID'], 6);
                    }
                    // generate 24 hr exipry signup verification link
                    $link = sha1(uniqid(rand(), TRUE));
                    $linkData = [
                        'userid' => $userId,
                        'hash' => $link,
                        'datecreated' => date('Y-m-d H:i:s'),
                    ];

                    $this->load->model("link_model", "link_model");
                    if ($this->link_model->createSingupLink($linkData)) {
                        /*
                         * SEND A MAIL...
                         */
                        $subject = 'Verify your account';
                        $this->load->model("smtpmail_model", "smtpmail_model");
                        $param = array(
                            '%MAILSUBJECT%' => 'Foodine : ' . $subject,
                            '%LOGO_IMAGE%' => BASEURL . 'images/Foodine.png',
                            '%USER_NAME%' => @$userFullName,
                            '%LINK%' => WEB_USER_ACTIVATION . $link
                        );

                        $tmplt = DIR_VIEW . 'email/signup_verify.php';
                        $subject = 'Foodine : ' . $subject;
                        $to = @$userEmail;
                        $this->smtpmail_model->send($to, $subject, $tmplt, $param);
                    }
                    $returnValue = $userId;
                }
            }

            return $returnValue;
        } catch (Exception $ex) {
            exit('User Model : Error in createAccount function - ' . $ex);
        }
    }

    function verifyEmail($postValues) {
        try {
            $returnValue = '';
            if (!empty($postValues)) {
                extract($postValues);
                $userFullName = $userFirstName . ' ' . $userLastName;
                if ($userId) {
                    // generate 24 hr exipry signup verification link
                    $link = sha1(uniqid(rand(), TRUE));
                    $linkData = [
                        'userid' => $userId,
                        'hash' => $link,
                        'datecreated' => date('Y-m-d H:i:s'),
                    ];

                    $this->load->model("link_model", "link_model");
                    if ($this->link_model->createSingupLink($linkData)) {
                        /*
                         * SEND A MAIL...
                         */
                        $subject = 'Verify your account';
                        $this->load->model("smtpmail_model", "smtpmail_model");
                        $param = array(
                            '%MAILSUBJECT%' => 'Foodine : ' . $subject,
                            '%LOGO_IMAGE%' => BASEURL . '/images/Foodine.png',
                            '%USER_NAME%' => @$userFullName,
                            '%LINK%' => WEB_USER_ACTIVATION . $link
                        );

                        $tmplt = DIR_VIEW . 'email/signup_verify.php';
                        $subject = 'Foodine : ' . $subject;
                        $to = @$userEmail;
                        $this->smtpmail_model->send($to, $subject, $tmplt, $param);
                    }
                    $returnValue = $userId;
                }
            }

            return $returnValue;
        } catch (Exception $ex) {
            exit('User Model : Error in verifyEmail function - ' . $ex);
        }
    }

    function createWebSocialAccount($postValues) {
        try {
            $returnValue = '';
            if (!empty($postValues)) {
                extract($postValues);
                $random = random_string('alnum', 12);
                // PREPARE THE INSERT DATA ARRAY...
                $userFirstName = $userLastName = '';
                if ($userFullName) {
                    $nameArr = explode(' ', $userFullName);
                    if (isset($nameArr[0])) {
                        $userFirstName = $nameArr[0];
                    }
                    if (isset($nameArr[1])) {
                        $userLastName = $nameArr[1];
                    }
                }
                $referralCode = strtoupper(substr($userFirstName, 0, 3)) . rand(100, 999);

                $data = array(
                    'vFullName' => @$userFullName,
                    'vFirstName' => @$userFirstName,
                    'vLastname' => @$userLastName,
                    'vEmail' => @$userEmail,
                    'vPassword' => md5($random),
                    'tCreatedAt' => date('Y-m-d H:i:s'),
                    'eStatus' => ACCOUNT_STATUS_ACTIVE,
                    'ePlatform' => isset($plateForm) ? @$plateForm : 'web',
                    'vDeviceToken' => isset($deviceToken) ? @$deviceToken : '',
                    'vReferralCode' => $referralCode
                );
                if ($userProvider == 'facebook') {
                    $data['vFBUserID'] = $userProviderId;
                }
                if ($userProvider == 'google') {
                    $data['vGPUserID'] = $userProviderId;
                }
                if ($userProvider == 'twitter') {
                    $data['vTwitUserID'] = $userProviderId;
                }

                if (!empty(@$userMobileNo)) {
                    $data['vMobileNo'] = @$userMobileNo;
                }
                if (!empty(@$userGender)) {
                    $data['eGender'] = @$userGender;
                }
                $this->db->insert($this->table, $data);

                $userId = $this->db->insert_id();
                if (isset($profilePic) && $profilePic != '') {
                    $rand = random_string('numeric', 4);
                    $content = file_get_contents(base64_decode($profilePic));
                    $uploadFiles = array();
                    //$targetpath = BASEDIR . 'images/user/' . $userId;
                    $targetpath = UPLOADS . 'user/' . $userId;
                    if (!is_dir($targetpath)) {
                        mkdir($targetpath, 0777, TRUE);
                    }
                    if (!is_dir($targetpath . "/thumb/")) {
                        mkdir($targetpath . "/thumb/", 0777, TRUE);
                    }
                    $imgName = $userProvider . '-' . $userId . '-' . $rand . '.jpg';
                    if (file_put_contents($targetpath . '/' . $imgName, $content)) {
                        file_put_contents($targetpath . '/thumb/' . $imgName, $content);
                        $this->updateProfilePic($imgName, $userId);
                    }
                }

                if ($this->db->affected_rows() > 0) {
                    //add user points
                    $this->load->model('user_points_model');
                    if (isset($platform) && strtolower($platform) != 'web') {
                        $this->user_points_model->addUserPoints($userId, 8);
                    }

                    $inviterQuery = "Select iUserID FROM tbl_user_invite WHERE vInviteEmail='$userEmail' ORDER BY iUserInviteID LIMIT 1";
                    $inviterRes = $this->db->query($inviterQuery)->row_array();
                    if (!empty($inviterRes['iUserID'])) {
                        $this->user_points_model->addUserPoints($inviterRes['iUserID'], 6);
                    }
                    // mail password to user
                    /*
                     * SEND A MAIL...
                     */
                    $this->load->model('smtpmail_model');
                    $subject = 'Your Account created successfully.';
                    $param = array(
                        '%MAILSUBJECT%' => 'Foodine : ' . $subject,
                        '%LOGO_IMAGE%' => UPLOADS . '/Foodine.png',
                        '%USER_NAME%' => @$userFullName,
                        '%NEW_PASSWORD%' => trim($random),
                    );
                    $tmplt = DIR_VIEW . 'email/social_signup_password.php';
                    $subject = 'Foodine : ' . $subject;
                    $to = $userEmail;
                    $this->smtpmail_model->send($to, $subject, $tmplt, $param);

                    $returnValue = $userId;
                }
            }

            return $returnValue;
        } catch (Exception $ex) {
            exit('User Model : Error in createWebSocialAccount function - ' . $ex);
        }
    }

    /*
     * FACEBOOK LOGIN...
     */

    function fbLogin($postValues) {
        try {
            $returnValue = '';
            if (!empty($postValues)) {
                extract($postValues);

                if (@$userGender != '') {
                    $userGender = ucfirst($userGender);
                }
                $referralCode = strtoupper(substr($firstName, 0, 3)) . rand(100, 999);
                /*
                 * PREPARE THE INSERT DATA ARRAY...
                 */
                $data = array(
                    //'vFullName' => @$userName,
                    'vFirstName' => @$firstName,
                    'vLastname' => @$userLastName,
                    'vEmail' => @$email,
                    'vFBUserID' => @$fbUserId,
                    'eGender' => @$userGender,
                    'tCreatedAt' => date('Y-m-d H:i:s'),
                    'ePlatform' => @$plateForm,
                    'vDeviceToken' => @$deviceToken,
                    'vReferralCode' => $referralCode
                );
                $this->db->insert($this->table, $data);
                $userId = $this->db->insert_id();
                if ($this->db->affected_rows() > 0) {
                    $userCuisine = $userCuisine != '' ? explode(',', $userCuisine) : array();
                    $userInterest = $userInterest != '' ? explode(',', $userInterest) : array();
                    $userMusic = $userMusic != '' ? explode(',', $userMusic) : array();
                    $userCategory = $userCategory != '' ? explode(',', $userCategory) : array();

                  
                    $returnValue = $this->general_model->getUserBasicRecordById($userId);
                    $returnValue['hMac'] = genratemac($userId);
                }


                /*
                 * SUBSCRIBE USER TO SNS
                 */
                if ($deviceToken != '' && @$userId != '') {
                    $this->db->update($this->table, array('ePlatform' => $plateForm, 'vDeviceToken' => $deviceToken), array('iUserID' => $userId));
                    $DEFAULT_ARR = array(
                        'IS_LIVE' => IS_NOTIFICATION_LIVE,
                        'PLATEFORM_TYPE' => @$plateForm
                    );

//                    $this->load->library('sns', $DEFAULT_ARR);
//                    $endPointARN = $this->sns->createPlatFormEndPointARN(@$deviceToken, @$userId);
//                    $topicARN = $this->sns->createTopic('broadcast_message');
//                    $subscribeARN = $this->sns->subscribeUser($endPointARN, $topicARN);

                    /*
                     * UPDATE TO THE DATABASE..
                     */
                    $this->db->update($this->table, array('vSubscribeARN' => $subscribeARN, 'vEndPointARN' => $endPointARN), array('iUserID' => $row['iUserID']));
                }
            }

            return $returnValue;
        } catch (Exception $ex) {
            exit('User Model : Error in fbLogin function - ' . $ex);
        }
    }

    /*
     * TO CHECK FACEBOOK ID IS ALREADY EXISTS OR NOT...
     */

    function fbUserIdCheck($fbUserId, $platForm, $deviceToken, $profilePic) {
        try {
            if ($fbUserId != '') {
                $res = $this->db->get_where($this->table, array('vFBUserID' => $fbUserId));
                if ($res->num_rows() > 0) {
                    $rec = $res->row_array();

                    //Update DEviceToken and platform when social login
                    $this->db->update($this->table, array('ePlatform' => @$platForm, 'vDeviceToken' => $deviceToken), array('iUserID' => $rec['iUserID']));

                    if (isset($profilePic) && $profilePic != '' && $rec['vProfilePicture'] == '') {
                        $rand = random_string('numeric', 4);
                        $userId = $rec['iUserID'];
                        $content = file_get_contents(base64_decode($profilePic));
                        $uploadFiles = array();
                        //$targetpath = BASEDIR . 'images/user/' . $userId;
                        $targetpath = UPLOADS . 'user/' . $userId;
                        if (!is_dir($targetpath)) {
                            mkdir($targetpath, 0777, TRUE);
                        }
                        if (!is_dir($targetpath . "/thumb/")) {
                            mkdir($targetpath . "/thumb/", 0777, TRUE);
                        }
                        $userProvider = 'facebook';
                        $imgName = $userProvider . '-' . $userId . '-' . $rand . '.jpg';
                        if (file_put_contents($targetpath . '/' . $imgName, $content)) {
                            file_put_contents($targetpath . '/thumb/' . $imgName, $content);
                            $this->updateProfilePic($imgName, $userId);
                        }
                    }
                    //For old users :: add refferal code
                    if (empty($rec['vReferralCode']) || $rec['vReferralCode'] == '') {
                        $this->_addReferralCode($rec['iUserID'], $rec['vFirstName'], $platForm);
                    }

                    $returnValue = $this->general_model->getUserBasicRecordById($rec['iUserID']);
                    $returnValue['hMac'] = genratemac($rec['iUserID']);

                    return $returnValue;
                } else {
                    return '';
                }
            } else {
                return '';
            }
        } catch (Exception $ex) {
            throw new Exception('Error in fbUserIdCheck function - ' . $ex);
        }
    }

    /*
     * TO CHECK Twitter ID IS ALREADY EXISTS OR NOT...
     */

    function twitUserIdCheck($twitUserId, $platForm, $deviceToken) {
        try {
            if ($twitUserId != '') {
                $res = $this->db->get_where($this->table, array('vTwitUserID' => $twitUserId));
                if ($res->num_rows() > 0) {
                    $rec = $res->row_array();

                    //Update DEviceToken and platform when social login
                    $this->db->update($this->table, array('ePlatform' => @$platForm, 'vDeviceToken' => $deviceToken), array('iUserID' => $rec['iUserID']));

                    //For old users :: add refferal code
                    if (empty($rec['vReferralCode']) || $rec['vReferralCode'] == '') {
                        $this->_addReferralCode($rec['iUserID'], $rec['vFirstName'], $platForm);
                    }
                    $returnValue = $this->general_model->getUserBasicRecordById($rec['iUserID']);
                    $returnValue['hMac'] = genratemac($rec['iUserID']);

                    return $returnValue;
                } else {
                    return '';
                }
            } else {
                return '';
            }
        } catch (Exception $ex) {
            throw new Exception('Error in twitUserIdCheck function - ' . $ex);
        }
    }

    /**
     * To check if Google Plus id exists or not
     *
     * @param $gpUserId
     * @return string
     * @throws Exception
     */
    function gpUserIdCheck($gpUserId, $platForm, $deviceToken) {
        try {
            if ($gpUserId != '') {
                $res = $this->db->get_where($this->table, array('vGPUserID' => $gpUserId));
                if ($res->num_rows() > 0) {
                    $rec = $res->row_array();

                    //Update DEviceToken and platform when social login
                    $this->db->update($this->table, array('ePlatform' => @$platForm, 'vDeviceToken' => $deviceToken), array('iUserID' => $rec['iUserID']));

                    //For old users :: refferal code
                    if (empty($rec['vReferralCode']) || $rec['vReferralCode'] == '') {
                        $this->_addReferralCode($rec['iUserID'], $rec['vFirstName'], $platForm);
                    }
                    $returnValue = $this->general_model->getUserBasicRecordById($rec['iUserID']);
                    $returnValue['hMac'] = genratemac($rec['iUserID']);

                    return $returnValue;
                } else {
                    return '';
                }
            } else {
                return '';
            }
        } catch (Exception $ex) {
            throw new Exception('Error in gpUserIdCheck function - ' . $ex);
        }
    }

    /*
     * TO UPDATE THE USER PROFILE PICTURE
     */

    function updateProfilePic($profilePic, $userId) {
        try {
            if ($profilePic !== '' && $userId !== '') {
                $this->db->update($this->table, array('vProfilePicture' => $profilePic), array('iUserID' => $userId));
            }
        } catch (Exception $ex) {
            
        }
    }

    /*
     * TO UPDATE THE USER ACCOUNT...
     */

    function updateAccount($postValues) {
        try {
            $returnValue = '';
            if (!empty($postValues)) {
                extract($postValues);

                /*
                 * PREPARE THE INSERT DATA ARRAY...
                 */
                $data = array(
                    'vFullName' => @$userName,
                    'vFBUserID' => @$fbUserId,
                    'vProfilePicture' => $photoUrl,
                    'ePlatform' => $plateForm, 
                    'vDeviceToken' => $deviceToken
                );
                $this->db->update($this->table, $data, array('vEmail' => $email));
                //if ($this->db->affected_rows() > 0) {

                $userRes = $this->db->get_where($this->table, array('vEmail' => $email));
                $userRec = $userRes->row_array();

                $userId = $userRec['iUserID'];

                $userCuisine = $userCuisine != '' ? explode(',', $userCuisine) : array();
                $userInterest = $userInterest != '' ? explode(',', $userInterest) : array();
                $userMusic = $userMusic != '' ? explode(',', $userMusic) : array();
                $userCategory = $userCategory != '' ? explode(',', $userCategory) : array();

                /*
                 * NEED TO ADD CUISINE RECORDS...
                 *
                $this->_addUserCIMValues($userId, 'cuisine', $userCuisine);
                /*
                 * NEED TO ADD INTEREST RECORDS...
                 *
                $this->_addUserCIMValues($userId, 'interest', $userInterest);
                /*
                 * NEED TO ADD MUSIC RECORDS...
                 *
                $this->_addUserCIMValues($userId, 'music', $userMusic);

                /*
                 * NEED TO ADD CATEGORY RECORDS...
                 *
                $this->_addUserCIMValues($userId, 'category', $userCategory);
                */
                $returnValue = $this->general_model->getUserBasicRecordById($userId);
               // $returnValue['hMac'] = genratemac($userId);
              
            }

            return $returnValue;
        } catch (Exception $ex) {
            exit('User Model : Error in updateAccount function - ' . $ex);
        }
    }

    /**
     * Update facebook id when user email found for social login
     * @param $postValues
     * @return string
     */
    function updateFbAccount($postValues) {
        try {
            $returnValue = '';
            if (!empty($postValues)) {
                extract($postValues);

                /*
                 * PREPARE THE INSERT DATA ARRAY...
                 */
                $data = array(
                    'vFullName' => @$userFullName,
                    'vFBUserID' => @$userProviderId
                );
                $this->db->update($this->table, $data, array('vEmail' => $userEmail));
                //if ($this->db->affected_rows() > 0) {
                if (!isset($platform)) {
                    $platform = '';
                }
                $this->_updateRefferalCode($userEmail, $userFullName, $platform);
                $userRes = $this->db->get_where($this->table, array('vEmail' => $userEmail));
                $userRec = $userRes->row_array();

                $userId = $userRec['iUserID'];

                if (isset($profilePic) && $profilePic != '' && $userRec['vProfilePicture'] == '') {
                    $rand = random_string('numeric', 4);
                    $content = file_get_contents(base64_decode($profilePic));
                    $uploadFiles = array();
                    //$targetpath = BASEDIR . 'images/user/' . $userId;
                    $targetpath = UPLOADS . 'user/' . $userId;
                    if (!is_dir($targetpath)) {
                        mkdir($targetpath, 0777, TRUE);
                    }
                    if (!is_dir($targetpath . "/thumb/")) {
                        mkdir($targetpath . "/thumb/", 0777, TRUE);
                    }
                    $imgName = $userProvider . '-' . $userId . '-' . $rand . '.jpg';
                    if (file_put_contents($targetpath . '/' . $imgName, $content)) {
                        file_put_contents($targetpath . '/thumb/' . $imgName, $content);
                        $this->updateProfilePic($imgName, $userId);
                    }
                }

                $returnValue = $this->general_model->getUserBasicRecordById($userId);
                $returnValue['hMac'] = genratemac($userId);
            }

            return $returnValue;
        } catch (Exception $ex) {
            exit('User Model : Error in updateFbAccount function - ' . $ex);
        }
    }

    /**
     * Update google plus id when user email found for social login
     * @param $postValues
     * @return string
     */
    function updateGpAccount($postValues) {
        try {
            $returnValue = '';
            if (!empty($postValues)) {
                extract($postValues);

                /*
                 * PREPARE THE INSERT DATA ARRAY...
                 */
                $data = array(
                    'vFullName' => @$userFullName,
                    'vGPUserID' => @$userProviderId
                );
                $this->db->update($this->table, $data, array('vEmail' => $userEmail));
                //if ($this->db->affected_rows() > 0) {

                if (!isset($platform)) {
                    $platform = '';
                }
                $this->_updateRefferalCode($userEmail, $userFullName, $platform);

                $userRes = $this->db->get_where($this->table, array('vEmail' => $userEmail));
                $userRec = $userRes->row_array();

                $userId = $userRec['iUserID'];

                $returnValue = $this->general_model->getUserBasicRecordById($userId);
                $returnValue['hMac'] = genratemac($userId);
            }

            return $returnValue;
        } catch (Exception $ex) {
            exit('User Model : Error in updateGpAccount function - ' . $ex);
        }
    }

    /**
     * Update google plus id when user email found for social login
     * @param $postValues
     * @return string
     */
    function updateTwitterAccount($postValues) {
        try {
            $returnValue = '';
            if (!empty($postValues)) {
                extract($postValues);

                /*
                 * PREPARE THE INSERT DATA ARRAY...
                 */

                $data = array(
                    'vFullName' => @$userFullName,
                    'vTwitUserID' => @$userProviderId
                );
                $this->db->update($this->table, $data, array('vEmail' => $userEmail));

                //if ($this->db->affected_rows() > 0) {
                if (!isset($platform)) {
                    $platform = '';
                }
                $this->_updateRefferalCode($userEmail, $userFullName, $platform);

                $userRes = $this->db->get_where($this->table, array('vEmail' => $userEmail));
                $userRec = $userRes->row_array();

                $userId = $userRec['iUserID'];

                $returnValue = $this->general_model->getUserBasicRecordById($userId);
                $returnValue['hMac'] = genratemac($userId);
            }

            return $returnValue;
        } catch (Exception $ex) {
            exit('User Model : Error in updateGpAccount function - ' . $ex);
        }
    }

    private function _updateRefferalCode($userEmail = '', $userFullName = '', $platform = '') {
        if ($userEmail != '') {
            $res = $this->db->get_where($this->table, array('vEmail' => $userEmail));
            if ($res->num_rows() > 0) {
                $rec = $res->row_array();

                if ($rec['vFirstName'] == '') {
                    $userFirstName = $userLastName = '';
                    if ($userFullName) {
                        $nameArr = explode(' ', $userFullName);
                        if (isset($nameArr[0])) {
                            $userFirstName = $nameArr[0];
                        }
                        if (isset($nameArr[1])) {
                            $userLastName = $nameArr[1];
                        }
                    }
                    $udata = array(
                        'vFirstName' => @$userFirstName,
                        'vLastName' => @$userLastName
                    );
                    $rec['vFirstName'] = @$userFirstName;
                    $this->db->update($this->table, $udata, array('vEmail' => $userEmail));
                }

                //For old users :: add refferal code
                if (empty($rec['vReferralCode']) || $rec['vReferralCode'] == '') {
                    $this->_addReferralCode($rec['iUserID'], $rec['vFirstName'], $platform);
                }
            }
        }
        return true;
    }

    /*
     * TO GET LOGGED IN USER CUISINE / INTEREST / MUSIC VALUE
     */

    public function getCIMUserArray($type = 'cuisine', $userId = '') {
        $tbl = '';
        switch ($type) {
            case 'cuisine' :
                $tbl = 'tbl_cuisine AS tc';

                $fields = array(
                    'tc.iCuisineID AS id',
                    'tc.vCuisineName AS title',
                   /* 'CONCAT("' . BASEURL . 'images/cuisine/", IF(tc.vCuisineImage = \'\', "default.jpg", CONCAT(tc.iCuisineID,\'/\',tc.vCuisineImage)) ) AS cuisineImage'*/
                );
                if ($userId !== '') {
                    $fields[] = 'IF((SELECT COUNT(*) FROM tbl_user_cuisine AS tuc WHERE tuc.iCuisineID = tc.iCuisineID AND tuc.iUserID = "' . $userId . '") = "0", "false", "true") AS selected';
                } else {
                    $fields[] = '"false" AS selected';
                }

                break;

            case 'interest' :
                $tbl = 'tbl_facility AS tf';

                $fields = array(
                    'tf.iFacilityID AS id',
                    'tf.vFacilityName AS title'
                );
                if ($userId !== '') {
                    $fields[] = 'IF((SELECT COUNT(*) FROM tbl_user_interest AS tui WHERE tui.iInterestID = tf.iFacilityID AND tui.iUserID = "' . $userId . '") = "0", "false", "true") AS selected';
                } else {
                    $fields[] = '"no" AS selected';
                }
                break;

            case 'category' :
                $tbl = 'tbl_category AS tc';

                $fields = array(
                    'tc.iCategoryID AS categoryId',
                    'tc.vCategoryName AS categoryName'
                );
                if ($userId != '') {
                    $fields[] = 'IF((SELECT COUNT(*) FROM tbl_user_category AS tuc WHERE tuc.iCategoryID IN(tc.iCategoryID) AND tuc.iUserID IN(' . $userId . ')) = "0", "no", "yes") AS selected';
                } else {
                    $fields[] = '"no" AS selected';
                }
                break;
            case 'priceInfo' :
                $tbl = 'tbl_user AS tc';

                $fields = array(
                    'tc.selectMinPrice AS selectedMin',
                    'tc.selectMaxPrice AS selectedMax',
                );
               $condition[] = 'iUserId = '.$userId;
                break;

            case 'music' :
                $tbl = 'tbl_music AS tm';

                $fields = array(
                    'tm.iMusicID AS musicId',
                    'tm.vMusicName AS musicName'
                );

                if ($userId !== '') {
                    $fields[] = 'IF((SELECT COUNT(*) FROM tbl_user_music AS tum WHERE tum.iMusicID = tm.iMusicID AND tum.iUserID = "' . $userId . '") = "0", "false", "true") AS selected';
                } else {
                    $fields[] = '"false" AS selected';
                }
                break;

            case 'location' :
                $tbl = 'tbl_location AS tl';
                $tbl .= ', tbl_location_zone AS tlz';

                $fields = array(
                    'tl.iLocationID AS locationId',
                    'tl.vLocationName AS locationName',
                    'tlz.vZoneName AS zoneName'
                );
                $condition[] = 'tl.iLocZoneID = tlz.iLocZoneID';
                $condition[] = 'tl.eStatus = \'Active\'';
                break;
             case 'sort' :
                //pasha
                 //$oldRes = $this->db->get_where($this->table, array('vPassword' => md5($oldPassword), 'iUserID' => $userId));
                $result = $this->db->get_where('foodine_user_sort',array('userId'=> $userId));
                 
                $rows = $result->result_array();
                $price_selected = false;
                $rating_selected = false;
                 foreach($rows as $line) {
                     if(strtolower($line['sortName']) == 'price') {
                        $price_selected = $line['selected'];
                     }
                     
                     if(strtolower($line['sortName']) == 'rating') {
                         $rating_selected = $line['selected'];
                     }
                 }
                $returnArr[] = array('title' => 'price', 'id' => '1', 'selected' => $price_selected);
                $returnArr[] = array('title' => 'rating', 'id' => '2', 'selected' => $rating_selected);
                return $returnArr;
        }
        //$tbl .= ', tbl_user AS tu';
        if ($type !== 'location' && $type !== 'priceInfo')
            $condition[] = 'eStatus = \'Active\'';

        $qry = 'SELECT ' . implode(',', $fields) . ' FROM ' . $tbl . ' WHERE ' . implode(' AND ', $condition);

        $res = $this->db->query($qry);
        $row = $res->result_array();
       //mprd($row);

        $yesArr = $noArr = array();

        $returnArr = $row;
        if ($type !== 'location') {
            for ($i = 0; $i < count($row); $i++) {
                if ($row[$i]['selected'] == 'yes')
                    $yesArr[] = $row[$i];
                else
                    $noArr[] = $row[$i];
            }
            $returnArr = array_merge($yesArr, $noArr);
        }
         
        

        if($type == 'priceInfo') {
        	//echo $qry;
              // print_r($returnArr); exit;
            $returnArr['selectedMin'] = $returnArr[0]['selectedMin'];
            $returnArr['selectedMax'] = $returnArr[0]['selectedMax'];
        }
        
                
        return $returnArr;
    }

    public function getBanners() {
       $record = $this->restaurant_model->getBanner('featured');
        foreach($record as $line) {
            
            $ret[] = $line['bannerImage'];
        }
        return $ret;
    
    }

     public function getBannersWithRetaurantId() {
       $record = $this->restaurant_model->getBanner('featured');
        foreach($record as $line) {
            $banner = array('photoUrl' => $line['bannerImage'], 'restaurantId' => $line['iRestaurantID']); 
            $ret[] = $banner;
        }
        return $ret;
    
    }

    /*
     * TO SEND A MAIL NOTIFICATION WHILE FORGOT PASSWORD
     */

    function forgotPassword($userEmail) {
        try {
            if ($userEmail != '') {
                if ($this->isEmailExists($userEmail)) {
                    //$random = random_string('alnum', 12);
                    //$this->db->update($this->table, array('vPassword' => md5($random)), array('vEmail' => $userEmail));
                    $res = $this->db->get_where($this->table, array('vEmail' => $userEmail));
                    $record = $res->row_array();
                    $userId = $record['iUserID'];



                    $link = sha1(uniqid(rand(), TRUE));
                    //$userLink = base64_encode($userId) . '-' . base64_encode($record['vEmail']) . '/' . $link . '#forgotpassword';
                    $linkData = [
                        'userid' => $userId,
                        'hash' => $link,
                        'datecreated' => date('Y-m-d H:i:s'),
                    ];

                    $this->load->model("link_model", "link_model");
                    if ($this->link_model->createForgotPwdLink($linkData)) {
                        /*
                         * SEND A MAIL...
                         */
                        $this->load->model('smtpmail_model');
                        $param = array(
                            '%MAILSUBJECT%' => 'Foodine : Forgot Password',
                            '%LOGO_IMAGE%' => UPLOADS . '/images/Foodine.jpg',
                            '%USER_NAME%' => $record['vFullName'],
                            '%LINK%' => WEB_USER_FORGOT_PASSWORD . $link
                                //'%NEW_PASSWORD%' => $random
                        );
                        $tmplt = DIR_VIEW . 'email/forgot_password.php';
                        $subject = 'Foodine : Forgot Password';
                        $to = $userEmail;
                        $this->smtpmail_model->send($to, $subject, $tmplt, $param);

                        return TRUE;
                    }return FALSE;
                }
                return FALSE;
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            throw new Exception('Error in forgotPassword function - ' . $ex);
        }
    }

    /*
     * TO CHANGE THE USER PASSWORD
     */

    function changePassword($userId, $oldPassword, $newPassword, $confirmPassword) {
        try {
            if ($userId !== '' && $oldPassword !== '' && $newPassword !== '' && $confirmPassword !== '') {
                /*
                 * NEED TO CHECK OLD PASSWORD IS CORRECT OR NOT...
                 */
                $oldRes = $this->db->get_where($this->table, array('vPassword' => md5($oldPassword), 'iUserID' => $userId));
                //echo $oldRes->last_query();

                if ($oldRes->num_rows() > 0) {
                        $newRes = $this->db->update($this->table, array('vPassword' => md5($newPassword)), array('iUserID' => $userId));
                        if ($this->db->affected_rows() > 0) {
                            return 200;
                        } else {
                            return 103;
                        }
                    
                } else {
                    return 101;
                }
            } else {
                return 100;
            }
        } catch (Exception $ex) {
            throw new Exception('Error in changePassword function - ' . $ex);
        }
    }
    

    /*
     * TO CHANGE THE USER PASSWORD
     */

    function changeForgotPassword($linkId, $userId, $newPassword, $confirmPassword) {
        try {
            if ($linkId !== '' && $userId !== '' && $newPassword !== '' && $confirmPassword !== '') {
                /*
                 * NEED TO CHECK OLD PASSWORD IS CORRECT OR NOT...
                 */
                $oldRes = $this->db->get_where($this->table, array('iUserID' => $userId));
                //echo $oldRes->last_query();

                if ($oldRes->num_rows() > 0) {
                    if ($newPassword === $confirmPassword) {
                        $newRes = $this->db->update($this->table, array('vPassword' => md5($newPassword)), array('iUserID' => $userId));
                        if ($newRes == true) {
                            // update the link as expired
                            $this->db->update('link', array('expired' => 1), array('id' => $linkId));
                            return 200;
                        } else {
                            return 103;
                        }
                    } else {
                        return 102;
                    }
                } else {
                    return 101;
                }
            } else {
                return 100;
            }
        } catch (Exception $ex) {
            throw new Exception('Error in changeforgotPassword function - ' . $ex);
        }
    }

    function userMergeLinkGenerate($data) {
        try {
            // check both exists in db
            $primaryEmail = $data['primaryEmail'];
            $secondaryEmail = $data['secondaryEmail'];
            $userId = $data['userId'];
            $fullName = $data['fullName'];
            $email = $data['email'];

            $toUserId = $this->getUserIdFromEmail($primaryEmail);
            $fromUserId = $this->getUserIdFromEmail($secondaryEmail);

            if ($toUserId && $fromUserId) {
                $link = sha1(uniqid(rand(), TRUE));
                $mergeData = [
                    'iUserID' => $userId,
                    'iToUserID' => $toUserId,
                    'iFromUserID' => $fromUserId,
                    'vHash' => $link,
                    'tCreatedAt' => date('Y-m-d H:i:s'),
                ];

                $this->load->model("merge_model", "merge_model");
                if ($this->merge_model->createMergeLink($mergeData)) {
                    //SEND A MAIL...
                    $subject = 'Verification mail to merge account';
                    $this->load->model("smtpmail_model", "smtpmail_model");
                    $param = array(
                        '%MAILSUBJECT%' => 'Foodine : ' . $subject,
                        '%LOGO_IMAGE%' => BASEURL . '/images/Foodine.png',
                        '%USER_NAME%' => @$fullName,
                        '%LINK%' => WEB_USER_MERGE . $link
                    );

                    $tmplt = DIR_VIEW . 'email/merge_verify.php';
                    $subject = 'Foodine : ' . $subject;
                    $to = @$email;
                    $this->smtpmail_model->send($to, $subject, $tmplt, $param);
                }
                return '1';
            }
            return '0';
        } catch (Exception $ex) {
            exit('User Model : Error in activateUser function - ' . $ex);
        }
    }

    function userMergeAccount($token) {
        try {
            // check both exists in db

            if ($token !== '') {
                $this->load->model("merge_model", "merge_model");
                $res = $this->merge_model->verifyMergeLink($token);
                return $res;
            } else {
                return FALSE;
            }

            if ($toUserId && $fromUserId) {
                $link = sha1(uniqid(rand(), TRUE));
                $mergeData = [
                    'iUserID' => $userId,
                    'iToUserID' => $toUserId,
                    'iFromUserID' => $fromUserId,
                    'vHash' => $link,
                    'tCreatedAt' => date('Y-m-d H:i:s'),
                ];

                $this->load->model("merge_model", "merge_model");
                if ($this->merge_model->createMergeLink($mergeData)) {
                    //SEND A MAIL...
                    $subject = 'Verification mail to merge your account';
                    $this->load->model("smtpmail_model", "smtpmail_model");
                    $param = array(
                        '%MAILSUBJECT%' => 'Foodine : ' . $subject,
                        '%LOGO_IMAGE%' => BASEURL . '/images/Foodine.png',
                        '%USER_NAME%' => @$fullName,
                        '%LINK%' => WEB_USER_MERGE . $link
                    );

                    $tmplt = DIR_VIEW . 'email/merge_verify.php';
                    $subject = 'Foodine : ' . $subject;
                    $to = @$email;
                    $this->smtpmail_model->send($to, $subject, $tmplt, $param);
                }
                return '1';
            }
            return '0';
        } catch (Exception $ex) {
            exit('User Model : Error in activateUser function - ' . $ex);
        }
    }

    function activateUser($userId) {
        try {
            $this->db->update($this->table, array('eStatus' => 'Active'), array('iUserID' => $userId));
            //add user points
            $query = "Select count(iUserID) AS iUserID FROM tbl_user_points WHERE iUserID='$userId' AND iUserPointSystemID='7'";
            $res = $this->db->query($query)->row_array();
            if (empty($res['iUserID'])) {
                $this->load->model('user_points_model');
                $this->user_points_model->addUserPoints($userId, 7);
            }
            return true;
        } catch (Exception $ex) {
            exit('User Model : Error in activateUser function - ' . $ex);
        }
    }

    function deactivateUser($userId) {
        try {
            $this->db->update($this->table, array('eStatus' => 'Inactive'), array('iUserID' => $userId));
            return true;
        } catch (Exception $ex) {
            exit('User Model : Error in deactivateUser function - ' . $ex);
        }
    }

    /*
     * TO CREATE AN ACCOUNT
     */

    function editAccount($postValues) {
        try {
            $returnValue = '';
            if (!empty($postValues)) {
                extract($postValues);

                /*
                 * PREPARE THE INSERT DATA ARRAY...
                 */
                $data = array(
                    'vFullName' => (@$firstName . ' ' . @$lastName),
                    'vFirstName' => @$firstName,
                    'vLastName' => @$lastName,
                    //'vEmail' => @$userEmail,
                    'vAbout' => @$userAbout,
                    'dtDOB' => @$dateOfBirth,
                    'dtAnniversary' => $anniversary,
                    'vMobileNo' => @$mobileNumber,
                    'eGender' => @$gender,
                   
                );
                $this->db->update($this->table, $data, array('iUserID' => @$userId));

//                $userCuisine = $userCuisine != '' ? explode(',', $userCuisine) : array();
//                $userInterest = $userInterest != '' ? explode(',', $userInterest) : array();
//                $userMusic = $userMusic != '' ? explode(',', $userMusic) : array();
//                $userCategory = $userCategory != '' ? explode(',', $userCategory) : array();
//
//                /*
//                 * NEED TO UPDATE CUISINE RECORDS...
//                 */
//                $this->_addUserCIMValues($userId, 'cuisine', $userCuisine, TRUE);
//
//                /*
//                 * NEED TO UPDATE MUSIC RECORDS...
//                 */
//                $this->_addUserCIMValues($userId, 'music', $userMusic, TRUE);
//
//                /*
//                 * NEED TO UPDATE INTEREST RECORDS...
//                 */
//                $this->_addUserCIMValues($userId, 'interest', $userInterest, TRUE);
//
//                /*
//                 * NEED TO ADD CATEGORY RECORDS...
//                 */
//                $this->_addUserCIMValues($userId, 'category', $userCategory);

                $returnValue = $userId;
            }

            return $returnValue;
        } catch (Exception $ex) {
            //exit('User Model : Error in createAccount function - ' . $ex);
            return '';
        }
    }

    /*
     * TO EDIT AN ACCOUNT (WEB)
     */

    function editWebAccount($postValues) {
        try {
            $returnValue = '';
            if (!empty($postValues)) {
                extract($postValues);

                /*
                 * PREPARE THE INSERT DATA ARRAY...
                 */
                //$userBirthDate = $userAnniversary = '';
                if ($userBirthDate != '') {
                    $userBirthDate = date('Y-m-d', strtotime(@$userBirthDate));
                }
                if ($userAnniversary != '') {
                    $userAnniversary = date('Y-m-d', strtotime(@$userAnniversary));
                }
                $data = array(
                    'vFirstName' => @$userFirstName,
                    'vLastName' => @$userLastName,
                    'vFullName' => @$userFirstName . ' ' . @$userLastName,
                    'vMobileNo' => @$userMobile,
                    'eGender' => @$userGender,
                    'dtDOB' => @$userBirthDate,
                    'dtAnniversary' => @$userAnniversary,
                );

                $this->db->update($this->table, $data, array('iUserID' => @$userId));

                $userCuisine = @$userCuisine != '' ? explode(',', @$userCuisine) : array();
                $userInterest = @$userInterest != '' ? explode(',', @$userInterest) : array();
                $userMusic = @$userMusic != '' ? explode(',', @$userMusic) : array();
                //$userCategory = $userCategory != '' ? explode(',', $userCategory) : array();

                /*
                 * NEED TO UPDATE CUISINE RECORDS...
                 */
                $this->_addUserCIMValues($userId, 'cuisine', $userCuisine, TRUE);
                /*
                 * NEED TO UPDATE INTEREST RECORDS...
                 */
                $this->_addUserCIMValues($userId, 'interest', $userInterest, TRUE);
                /*
                 * NEED TO UPDATE MUSIC RECORDS...
                 */
                $this->_addUserCIMValues($userId, 'music', $userMusic, TRUE);
                /*
                 * NEED TO ADD CATEGORY RECORDS...
                 */
                //$this->_addUserCIMValues($userId, 'category', $userCategory);
                //return $userMusic;
                $returnValue = $userId;
            }

            return $returnValue;
        } catch (Exception $ex) {
            exit('User Model : Error in createAccount function - ' . $ex);
        }
    }

    function redeemReward($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                $res = $this->db->get_where('tbl_reward_request', array('iRewardID' => $rewardId, 'iUserID' => $userId));
                if ($res->num_rows() > 0) {
                    return 0;
                } else {
                    $data = array(
                        'iRewardID' => $rewardId,
                        'iUserID' => $userId
                    );
                    $this->db->insert('tbl_reward_request', $data);

                    $insId = $this->db->insert_id();
                    if ($insId !== '') {
                        /*
                         * SEND MAIL TO THE USER WHO IS REQUESTING FOR THE REDEEM POINTS
                         */
                        $USERINFO = $this->general_model->getUserBasicRecordById($userId);
                        if (isset($USERINFO['userEmail']) && $USERINFO['userEmail'] !== '') {
                            $this->load->model('smtpmail_model');

                            $to = $USERINFO['userEmail'];
                            $cc = array();
                            $subject = 'Foodine : Redeem Reward Request Response';
                            $template = DIR_VIEW . '/mail_template/reward_request.php';
                            $param = $attach = array();

//                            $this->smtpmail_model->send($to, $subject, $template, $param, $attach, $cc);
                        }
                    }

                    return $insId;
                }
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in redeemReward function - ' . $ex);
        }
    }

    /*
     * TO CHECKIN USER
     */

    function uesrCheckIn($postVal) {
        try {
            if (!empty($postVal)) {
                extract($postVal);
                $minDistance = 10; //IN KM
                $id = $restaurantId;
                /*
                 * TO CEHCK THAT CHECK IN RECORD IS AVAILABEL OR NOT??
                 */
                //$fields = 'tuc.iCheckInID';
                $fields = 'iRestaurantID';
                if (!empty($userLat) && !empty($userLong)) {
                    $fields = ' ROUND(( ' . (6371) . ' * acos( cos( radians( ' . $userLat . ' ) )'
                            . ' * cos( radians( tr.vLat) )'
                            . ' * cos( radians( tr.vLog ) - radians( ' . $userLong . ' ) )'
                            . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( tr.vLat ) ) ) ),2)'
                            . ' AS distance';
                }
                $tbl[] = 'tbl_restaurant AS tr';
                $condition[] = 'tr.iRestaurantID IN(' . $id . ')';

                $tbl = ' FROM ' . implode(',', $tbl);
                $condition = ' WHERE ' . implode(' AND ', $condition);

                //$having = ' HAVING distance < ' . $minDistance;
                $having = '';

                $qry = 'SELECT ' . $fields . $tbl . $condition . $having;

                $isCheckin = $this->db->query($qry);

                if ($isCheckin->num_rows() > 0) {

                    $hasRestRec = $this->db->query('SELECT iCheckInID FROM tbl_user_checkin WHERE iRestaurantID = "' . $id . '" AND iUserID = "' . $userId . '" AND tCreatedAt = DATE(NOW()) ');

                    if ($hasRestRec->num_rows() <= 0) {
                        /*
                         * USER ALLOW TO ADD CHECK IN ENTRY...
                         */
                        $checkInRow = $isCheckin->row_array();

                        $data = array(
                            'iUserID' => $userId,
                            'iRestaurantID' => $id,
                            'vDistance' => isset($checkInRow['distance']) ? $checkInRow['distance'] : 0,
                            'vFriendID' => isset($friendId) ? $friendId : '',
                            'tMessage' => isset($message) ? $message : '',
                            'tCreatedAt' => date('Y-m-d H:i:s')
                        );

                        $this->db->insert('tbl_user_checkin', $data);
                        $checkinId = $this->db->insert_id();
                        $this->load->model('user_points_model');
                        // new method to add user points
                        $this->user_points_model->addUserPoints($userId, 1);
                        //$bookingQuery = "Select count(iTableBookID) As bookCount FROM tbl_table_book WHERE eBookingStatus='Accept' AND iUserID='$userId' AND iRestaurantID='$id' AND DATE(tDateTime)=DATE(NOW())";
                        //$bookingRes = $this->db->query($bookingQuery)->row_array();
                        //if (!empty($bookingRes['bookCount'])) {
                        //$this->user_points_model->addUserPoints($userId, 2);
                        //}
                        // old method to add user points
                        //$this->general_model->addUserPointValue($userId, 1);

                        /*
                         * TO GIVE USER POINTS FOR TABLE BOOKING
                         * TO CHECK THAT USER HAVE BOOKED THE TABLE OR NOT
                         * FOR THE SAME DATE AND TIME...
                         */
                        $QRY = 'SELECT ttb.iTableBookID AS tableBookId '
                                . 'FROM tbl_table_book AS ttb, slot_master AS sm '
                                . 'WHERE ttb.iRestaurantID IN(' . $id . ') '
                                . 'AND ttb.iUserID IN(' . $userId . ') '
                                . 'AND ttb.eBookingStatus IN(\'Pending\') '
                                . 'AND ttb.iSlotID IN(sm.iSlotID) '
                                . 'AND (\'' . date('H:i:s', time()) . '\' BETWEEN sm.tstartFrom AND sm.tendTo ) '
                                . 'AND tDateTime = \'' . date('Y-m-d', time()) . '\'';

                        $hasrec = $this->db->query($QRY)->row_array();
                        if (!empty($hasrec) && @$hasrec['tableBookId'] != '') {
                            $this->general_model->addUserPointValue($userId, 3, @$hasrec['tableBookId']);
                        }

                        /*
                         * HERE PUSH NOTIFICATION WILL SEND IT TO VENDOR APP
                         */
                        $fullName = $this->db->query('SELECT CONCAT(vFirstName,\' \',vLastName) AS fullName FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array()['fullName'];
                        $record = $this->db->query('SELECT ePlatform, vDeviceToken FROM tbl_admin WHERE iRestaurantID IN(' . $id . ')')->row_array();

                        //$restaurantName = $this->db->query('SELECT vRestaurantName FROM tbl_restaurant WHERE iRestaurantID IN(' . $vId . ')')->row_array()['vRestaurantName'];
                        $this->load->library('pushnotify');

                        $osType = $record['ePlatform'] == 'ios' ? 2 : 1;
                        $deviceToken = $record['vDeviceToken'];

                        $pushMSG = $fullName . ' is going to check-in.';

                        if ($deviceToken != '') {
                            $this->pushnotify->sendIt($osType, $deviceToken, $pushMSG, 2);
                        }

                        //return $this->db->insert_id();
                        return $checkinId;
                    } else {
                        return -3;
                    }
                } else {
                    return -2;
                }
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in uesrCheckIn function - ' . $ex);
        }
    }

    public function addImages($postData, $iCheckInID, $restId) {
        $date = date('Y-m-d H:i:s');
        foreach ($postData AS $type => $imageData) {
            switch ($type) {
                case "checkInImages":
                    foreach ($imageData AS $image) {
                        $data = array(
                            'iCheckInID' => $iCheckInID,
                            'vImageName' => $image,
                            'eStatus' => 'Active',
                            'tCreatedAt' => $date,
                            'tModifiedAt' => $date);
                        $this->db->insert('tbl_user_checkin_images', $data);
                    }
                    break;
                case "publishImages":
                    foreach ($imageData AS $image) {
                        $data = array(
                            'iPublishID' => $iCheckInID,
                            'vImageName' => $image,
                            'eStatus' => 'Active',
                            'tCreatedAt' => $date,
                            'tModifiedAt' => $date);
                        $this->db->insert('tbl_user_publish_images', $data);
                    }
                    break;
                case "reviewImages":
                    foreach ($imageData AS $image) {
                        $data = array(
                            'iReviewID' => $iCheckInID,
                            'iRestaurantID' => $restId,
                            'iReviewImage' => $image,
                            'eStatus' => 'Active',
                            'tCreatedAt' => $date,
                            'tModifiedAt' => $date);
                        $this->db->insert('tbl_restaurant_review_images', $data);
                    }
                    break;
                default:
                    break;
            }
        }
        return true;
    }

    /*
     * GET USER CHECK IN LIST
     */

    function getCheckInList($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                $fields[] = 'tr.iRestaurantID AS id';
                $fields[] = 'tr.vRestaurantName AS restaurantName';
                $fields[] = 'tr.tAddress AS restaurantAddr';
                $fields[] = 'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.png", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage';
                $fields[] = 'tuc.iCheckInID AS checkInId';
                $fields[] = 'CONCAT(tuc.vDistance," KM") AS distance';
                $fields[] = 'DATE_FORMAT(tuc.tCreatedAt,"' . MYSQL_DATE_FORMAT . '") AS checkInDate';

                $tbl[] = 'tbl_restaurant AS tr';
                $tbl[] = 'tbl_user_checkin AS tuc';

                $condition[] = 'tr.iRestaurantID IN(tuc.iRestaurantID)';
                $condition[] = 'tuc.iUserID IN(' . $userId . ')';

                $fields = implode(',', $fields);
                $tbl = ' FROM ' . implode(',', $tbl);
                $condition = ' WHERE ' . implode(' AND ', $condition);

                /* PAGINATION */
                $zeroPageRecord = 5;
                $otherPageRecord = 6;
                if ($pageId == 0) {
                    /* first page load records... */
                    $perPageValue = $zeroPageRecord;
                } else {
                    /* second to other page records... */
                    $perPageValue = $otherPageRecord;
                }
                $pageId = (int) $pageId;
                if ($pageId == 1) {
                    $pageId = $zeroPageRecord * $pageId;
                } else if ($pageId > 1) {
                    $pageId = (($zeroPageRecord) + ($perPageValue * ($pageId - 1)));
                }

                $limit = ' LIMIT ' . $pageId . ',' . $perPageValue;
                //$limit = '';
                //echo time() . PHP_EOL;
                $countqry = 'SELECT COUNT(DISTINCT(tuc.iCheckInID)) AS totalRows ' . $tbl . $condition;
                //echo $qry; exit;
                $countRes = $this->db->query($countqry);
                $countRec = $countRes->row_array();
                $countRec = (int) $countRec['totalRows'];

                $totalPage = 1 + (floor($countRec / $otherPageRecord));

                $qry = 'SELECT ' . $fields . $tbl . $condition . $limit;

                $res = $this->db->query($qry);
                if ($res->num_rows() > 0) {
                    return array(
                        'foundRec' => $res->result_array(),
                        'totalRecord' => $countRec,
                        'totalPage' => $totalPage
                    );
                } return array(
                    'foundRec' => array(),
                    'totalRecord' => 0,
                    'totalPage' => 0
                );
            }
        } catch (Exception $ex) {
            throw new Exception('Error in getCheckInList function - ' . $ex);
        }
    }

    /*
     * TO GET ALL THE REVIEWS
     */

    function getAllReviews($restId) {
        try {
            if ($restId != '') {
                $tbl = $fields = $condition = array();

                $tbl[] = 'tbl_user_ratting AS tur';
                $tbl[] = 'tbl_user AS tu';
                $tbl[] = 'tbl_restaurant AS tr';

                $fields[] = 'tr.iRestaurantID AS vendorId';
                $fields[] = 'CONCAT(tu.vFirstName," ",tu.vLastName) AS userName';
                $fields[] = 'IF(tu.vProfilePicture = "" , CONCAT("' . USER_IMG . 'dM.png"), CONCAT("' . USER_IMG . '", tu.iUserID, "/thumb/", tu.vProfilePicture)) AS userProfile';
                $fields[] = 'tur.iRateValue AS rateValue';
                $fields[] = 'tur.tRateComment AS reviewComment';
                $fields[] = 'IFNULL(DATE_FORMAT(tur.tCreatedAt,\'' . MYSQL_DATE_FORMAT2 . '\'), "") AS reviewDate';

                $condition[] = 'tur.iRestaurantID IN(tr.iRestaurantID)';
                $condition[] = 'tur.iUserID IN(tu.iUserID)';
                //$condition[] = 'tur.eStatus IN(\'Active\')';
                $condition[] = 'tr.iRestaurantID IN(' . $restId . ')';

                $tbl = ' FROM ' . implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = ' WHERE ' . implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . $tbl . $condition;

                $res = $this->db->query($qry);

                if ($res->num_rows() > 0) {
                    return $res->result_array();
                } return array();
            } return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getAllReviews function - ' . $ex);
        }
    }

    /**
     * @param array $userId
     * @return int
     * @throws Exception
     */
    function getUserReviews($userId) {
        try {
            $sql = 'SELECT trr.iReviewID AS reviewId, trr.iRestaurantID AS id, tr.vRestaurantName AS restaurantName,';
            $sql .= ' TRUNCATE((trr.iAmbience + trr.iPrice + trr.iFood + trr.iService) /8, 2 ) AS rating,';
            $sql .= ' tr.eStatus AS restStatus, trr.tReviewDetail AS ratingReview, trr.tModifiedAt AS modifiedAt,';
            $sql .= ' CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.png", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage,';
            $sql .= ' CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.png", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ) AS restaurantThumbImage';
            $sql .= ' FROM  `tbl_restaurant_review` AS  `trr`';
            $sql .= ' LEFT JOIN  `tbl_restaurant` AS  `tr` ON  `trr`.`iRestaurantID` =  `tr`.`iRestaurantID`';
            $sql .= ' WHERE iUserID = ' . $userId . ' AND trr.eStatus = "active"';
            $sql .= ' ORDER BY trr.tModifiedAt desc';
            $res = $this->db->query($sql);
            if ($res->num_rows() > 0) {
                $this->load->model("restaurant_model", "restaurant_model");
                $result = $res->result_array();
                for ($i = 0; $i < count($result); $i++) {
                    $data = $this->restaurant_model->getRestaurantUserImages($result[$i]['id'], $result[$i]['restaurantImage'], $result[$i]['restaurantThumbImage']);
                    $result[$i]['images'] = $data['images'];
                    $result[$i]['thumbImage'] = $data['thumbImages'];
                }
                return $result;
            }
            return array();
        } catch (Exception $ex) {
            throw new Exception('Error in userReviews function - ' . $ex);
        }
    }

    /*
     * TO INVITE THE FRIENDS TO USE THIS APPLICATION...
     */

    function inviteFriends($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                if ($userId != '' && !empty($inviteFriends)) {
                    for ($i = 0; $i < count($inviteFriends); $i++) {

                        /*
                         * TO CHECK THAT USER HAD ALREADY INVITE THE FRIENDS OR NOT??
                         */
                        $hasRec = $this->db->get_where('tbl_user_invite', array('vInviteEmail' => $inviteFriends[$i], 'iUserID' => $userId));
                        if ($hasRec->num_rows() == 0) {

                            /*
                             * IF USER NOT INVITED ANY FRIEND THEN ADD RECORD TO THE DATABASE.
                             */
                            $insVal = array(
                                'iUserID' => $userId,
                                'vInviteEmail' => $inviteFriends[$i]
                            );
                            $this->db->insert('tbl_user_invite', $insVal);

                            return $this->db->insert_id();
                        }
                    }
                } return -2;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in inviteFriends function - ' . $ex);
        }
    }

    /*
     * TO CHECK THAT USER IS SUBSCRIBE OR NOT
     */

    function userSubscription($postValues = array()) {
        try {
            if (!empty($postValues)) {
                extract($postValues);
                $timePeriod = (int) $timePeriod;

                $row = end($this->db->get_where('tbl_user_subscription', array('iUserID' => $userId))->result_array());

                if (empty($row)) {
                    /*
                     * IF USER SCUBSCRIBE TO THE APP AT THE FIRST TIME 
                     * MAKE A FRESH ENTRY
                     */
                    $ins = array(
                        'iUserID' => $userId,
                        'iTimePeriod' => $timePeriod,
                        'tCreatedAt' => date('Y:m:d H:i:s', time())
                    );

                    $this->db->insert('tbl_user_subscription', $ins);

                    return 1;
                } else {
                    /*
                     * IF USER SUBSCRIBED ALREADY THAN CHECK THAT USER HAD ALREADY SUBSCRIBED 
                     * OR NEED TO CHECK THAT USER SUBSCRIPTION TIME FINISHED OR NOT
                     * IF FINISHED THAN MAKE ANOTHER ENTRY 
                     */
                    $oldTime = $row['tCreatedAt'];
                    $newTime = date('Y:m:d H:i:s');

                    $oldTime_stamp = strtotime($oldTime);
                    $newTime_stamp = strtotime($newTime);

                    $DIFF = $newTime_stamp - $oldTime_stamp;

                    /*
                     * DIFFERENCE IN MONTHS 
                     */

                    $totalSeconds = 0;
                    switch ($timePeriod) {
                        case 3:
                            $totalSeconds = 7776000;
                            break;

                        case 6:
                            $totalSeconds = 15552000;
                            break;

                        case 12:
                            $totalSeconds = 31104000;
                            break;
                    }
                    if ($DIFF >= $totalSeconds) {
                        /*
                         * MAKE A NEW ENTRY 
                         */
                        $ins = array(
                            'iUserID' => $userId,
                            'iTimePeriod' => $timePeriod,
                            'tCreatedAt' => date('Y:m:d H:i:s', time()),
                        );
                        $this->db->insert('tbl_user_subscription', $ins);
                        return 1;
                    } return -2;
                }
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in userSubscription function - ' . $ex);
        }
    }

    // Time format is UNIX timestamp or
    // PHP strtotime compatible strings
    private function dateDiff($time1, $time2, $precision = 6) {
        // If not numeric then convert texts to unix timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }

        // If time1 is bigger than time2
        // Then swap time1 and time2
        if ($time1 > $time2) {
            $ttime = $time1;
            $time1 = $time2;
            $time2 = $ttime;
        }

        // Set up intervals and diffs arrays
        $intervals = array('year', 'month', 'day', 'hour', 'minute', 'second');
        $diffs = array();

        // Loop thru all intervals
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }

            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }

        $count = 0;
        $times = array();
        // Loop thru all diffs
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval 
            // if value is bigger than 0
            if ($value > 0) {
                // Add s if value is not 1
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }

        // Return string with times
        return implode(", ", $times);
    }

    /*
     * CHANGE USER NOTIFICATION SETTINGS
     */

    function changeNotifySetting($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                $this->db->update('tbl_user', array('isNotify' => $switchValue), array('iUserID' => $userId));

                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in changeNotifySetting function - ' . $ex);
        }
    }

    /*
     * TO GET THE USER NOTIFICATION LIST
     */

    function notificationList($userId = '') {
        try {
            if ($userId != '') {

                $fields[] = 'iUserNotifyID AS notifyId';
                $fields[] = 'vNotificationText AS notifyText';
                $fields[] = 'iTargetID AS targetId';
                $fields[] = 'iRecordID AS recordId';
                $fields[] = 'eType AS targetType';
                $fields[] = 'DATE_FORMAT(tCreatedAt,"' . MYSQL_DATE_FORMAT2 . '") AS notifyDate';

                $fields = implode(',', $fields);

                $qry = 'SELECT ' . $fields . ' FROM tbl_user_notification WHERE iUserID IN(' . $userId . ') ORDER BY iUserNotifyID DESC';
                $row = $this->db->query($qry)->result_array();


                // mprd($row);
                $return = array();
                for ($i = 0; $i < count($row); $i++) {
                    $tmp = array();
                    if ($row[$i]['targetType'] == 'table') {
                        $inRow = $this->db->get_where('tbl_restaurant', array('iRestaurantID' => $row[$i]['targetId']))->row_array();
                        $tmp = array(
                            'listName' => $inRow['vRestaurantName'],
                            'listId' => $inRow['iRestaurantID'],
                            'listRequest' => '',
                            'listImage' => IMG_URL . 'restaurant/' . ($inRow['vRestaurantLogo'] != '' ? $inRow['iRestaurantID'] . '/' . $inRow['vRestaurantLogo'] : 'default.png'),
                        );
                    } else {
                        $inRow = $this->db->get_where('tbl_reward', array('iRewardID' => $row[$i]['targetId']))->row_array();
                        $tmp = array(//rewardRequest
                            'listName' => $inRow['vRewardTitle'],
                            'listId' => $inRow['iRewardID'],
                            'listRequest' => ($this->db->get_where('tbl_reward_request', array('iUserID' => $userId, 'iRewardID' => $inRow['iRewardID']))->num_rows() > 0) ? 'Yes' : 'No',
                            'listImage' => IMG_URL . 'reward/' . ($inRow['vRewardImage'] != '' ? $inRow['iRewardID'] . '/' . $inRow['vRewardImage'] : 'default.png'),
                        );
                    }

                    $notifyType = $row[$i]['targetType'];
                    $return[$i] = array(
                        'notifyId' => (int) $row[$i]['notifyId'],
                        'notifyText' => $row[$i]['notifyText'],
                        'notifyDate' => $this->general_model->get_timeago(strtotime($row[$i]['notifyDate'])),
                        'targetId' => $notifyType == 'table' ? ((int) $row[$i]['recordId']) : ((int) $tmp['listId']),
                        'targetType' => $row[$i]['targetType'],
                        'listName' => $tmp['listName'],
                        'listId' => $notifyType == 'table' ? ((int) $tmp['listId']) : ((int) $row[$i]['recordId']),
                        'listImage' => $tmp['listImage'],
                        'listRequest' => $tmp['listRequest']
                    );
                }
                return $return;
            } return array();
        } catch (Exception $ex) {
            throw new Exception('Error in notificationList function - ' . $ex);
        }
    }

    /*
     * CLEAR USER NOTIFICATION 
     */

    function clearNotification($userId = '') {
        try {
            if ($userId != '') {
                $this->db->delete('tbl_user_notification', array('iUserID' => $userId));
                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in clearNotification function - ' . $ex);
        }
    }

    /*
     * RESET USER BADGE COUNT
     */

    function resetUserBadge($userId = '') {
        try {
            if ($userId != '') {
                $this->db->update('tbl_user', array('iNotifyCount' => 1), array('iUserID' => $userId));
                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in resetUserBadge function - ' . $ex);
        }
    }

    function userBookmarkList($userId = '', $latitude, $longitude) {
        try {
            if ($userId != '') {
                $sql = 'SELECT `turf`.`iFavoriteID` AS bookmarkId, `turf`.iRestaurantID as restaurantId, `tr`.`vRestaurantName` AS restaurantName, ';
                $sql .= ' `tr`.iPriceValue AS cost_for_2, CONCAT(`tl`.vLocationName, ", ", `tr`.vCityName) AS location,';
                $sql .= ' `tr`.bookingAvailable AS bookingAvailable,';
                //$sql .= ' CONCAT("' . BASEURL . 'images/restaurant/", IF(`tr`.vRestaurantLogo = \'\', "default.png", CONCAT(`tr`.iRestaurantID,\'/\',`tr`.vRestaurantLogo)) ) AS restaurantImage, ';
                $sql .= ' CONCAT("' . BASEURL . 'images/restaurantMobile/", IF(tr.vRestaurantMobLogo = \'\', "defaultdetail.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantMobLogo)) ) AS restaurantImage,';
                $sql .= ' GROUP_CONCAT(DISTINCT `tc`.vCategoryName SEPARATOR ", ") AS category,';
                $sql .= ' GROUP_CONCAT(DISTINCT `tcs`.vCuisineName SEPARATOR ", ") AS cuisine,';
                $sql .= ' GROUP_CONCAT(DISTINCT `tf`.vFacilityName SEPARATOR ", ") AS facility,';
                $sql .= ' GROUP_CONCAT(DISTINCT CONCAT("' . BASEURL . 'images/facilityicon/b/", IF(tf.iconimage = \'\', "default.jpeg", CONCAT(tf.iconimage)) ) SEPARATOR ", ") AS facilities,';
                $sql .= ' IFNULL((SELECT COUNT(*) FROM tbl_deals AS td WHERE td.iRestaurantID IN(tr.iRestaurantID)),0) AS totalOffers,';
                $sql .= ' `tr`.iMinTime AS restaurantMinTime, `tr`.iMaxTime AS restaurantMaxTime, ';
                //$sql .= ' IFNULL((SELECT AVG(TRUNCATE((trr.iAmbience + trr.iPrice + trr.iFood + trr.iService ) /8, 2 )) FROM tbl_restaurant_review trr WHERE trr.iRestaurantID = tr.iRestaurantID and trr.eStatus="active"),"0") rating, ';
                $sql .= ' IFNULL((SELECT TRUNCATE(AVG((trr.iAmbience + trr.iPrice + trr.iFood + trr.iService) / 8), 2) FROM tbl_restaurant_review trr inner join tbl_user tu on trr.iUserID = tu.iUserID WHERE tu.is_critic = 1 AND trr.iRestaurantID = tr.iRestaurantID AND trr.eStatus = "active"),"0") criticRating, ';
                //$sql .= ' IFNULL((SELECT AVG(TRUNCATE((trr.iAmbience + trr.iPrice + trr.iFood + trr.iService) / 8, 2)) FROM tbl_restaurant_review trr inner join tbl_user tu on trr.iUserID = tu.iUserID WHERE tu.is_critic = 1 AND trr.iRestaurantID = tr.iRestaurantID AND trr.eStatus="active"),"0") criticRating, ';
                $sql .= ' `tr`.eAlcohol AS alcohol, `tr`.vDaysOpen as openDays ';

                $sql .= ' FROM `tbl_user_restaurant_favorite` AS `turf`';
                $sql .= ' LEFT JOIN `tbl_restaurant` AS `tr` ON `turf`.`iRestaurantID` = `tr`.`iRestaurantID`';
                $sql .= ' LEFT JOIN `tbl_location` AS `tl` ON `tl`.`iLocationID` = `tr`.`iLocationID`';
                $sql .= ' LEFT JOIN `tbl_restaurant_category` AS `trc` ON `trc`.`iRestaurantID` = `tr`.`iRestaurantID`';
                $sql .= ' LEFT JOIN `tbl_category` AS `tc` ON `tc`.`iCategoryID` = `trc`.`iCategoryID`';
                $sql .= ' LEFT JOIN `tbl_restaurant_cuisine` AS `trcs` ON `trcs`.`iRestaurantID` = `tr`.`iRestaurantID`';
                $sql .= ' LEFT JOIN `tbl_cuisine` AS `tcs` ON `tcs`.`iCuisineID` = `trcs`.`iCuisineID`';
                $sql .= ' LEFT JOIN `tbl_restaurant_facility` AS `trf` ON `trf`.`iRestaurantID` = `tr`.`iRestaurantID`';
                $sql .= ' LEFT JOIN `tbl_facility` AS `tf` ON `tf`.`iFacilityID` = `trf`.`iFacilityID`';

                $sql .= ' LEFT JOIN `tbl_restaurant_review` AS `trr` ON `trr`.`iRestaurantID` = `tr`.`iRestaurantID`';
                $sql .= ' WHERE `tr`.eStatus = "Active" AND `turf`.iUserID = ' . $userId;
                //$sql .= ' AND `trr`.eStatus = "active"';
                $sql .= ' GROUP BY `tr`.`iRestaurantID`';

                //CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.png", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage
                $result = $this->db->query($sql)->result_array();
                $restaurantData['data'] = [];
                foreach ($result as $data) {
                    if (!empty($data['cost_for_2'])) {
                        $costArr = explode('For', $data['cost_for_2']);
                        $costArr = explode('for', $costArr[0]);
                        unset($data['cost_for_2']);
                        $data['cost_for_2'] = trim($costArr[0]);
                    }
                    $minTime = $data['restaurantMinTime'];
                    $maxTime = $data['restaurantMaxTime'];
                    $timeSlot = $this->getRestaurantTimeSlot($minTime, $maxTime);
                    $data['timing'] = $timeSlot;
                    $this->load->model('restaurant_model');
                    $offer = $this->restaurant_model->getAllOffers($data['id']);
                    $data['offer'] = $offer;
                    $data['totalOffers'] = count($offer);
                    array_push($restaurantData['data'], $data);
                }

                $restArr = array();
                $post1['latitude'] = $latitude;
                $post1['longitude'] = $longitude;
            //    print_r($restaurantData); exit;
              $restArr =  $this->restaurant_model->getRestaurantListByType($restaurantData, $post1);
//                foreach ($restaurantData as $restData) {
//                    $restData['rating'] = $this->_getRating($restData['id']);
//                    $restAlias = strtolower(preg_replace("/\s+/", "-", trim($restData['restaurantName'])));
//                    $restData['aliasname'] = str_replace("'", "", $restAlias) . '-' . $restData['id'];
//                    $restData['images'] = $this->_getRestaurantImage($restData['id'], $restData['restaurantImage']);
//                    //$restData['criticrating'] = $this->getCriticRating($restData['id']);
//                    $restArr[] = $restData;
//                }
                return $restArr;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in getUserBookmarkList function - ' . $ex);
        }
    }

    private function _getRestaurantImage($recorId, $restImage) {
        $qry = 'SELECT vPictureName AS imageName FROM tbl_restaurant_image WHERE iRestaurantID = \'' . $recorId . '\' order by tModifiedAt desc';
        $res = $this->db->query($qry);
        //mprd($res->result_array());
        $row = $res->result_array();
        $arry = $arry1 = $arry2 = $finalarry = array();
        //$arry[] = $restImage;
        //Restaurant Photos
        for ($i = 0; $i < count($row); $i++) {
            $arry[] = BASEURL . 'images/restaurantPhoto/' . $recorId . '/thumb/' . $row[$i]['imageName'];
        }

        //Published photos
        $fields = $condition = $tbl = array();
        $fields[] = 'CONCAT("' . BASEURL . 'images/publish/", IF(tpi.vImageName = \'\', "default.png", CONCAT(tp.iPublishID,\'/thumb/\',tpi.vImageName)) ) AS image';

        $tbl[] = 'tbl_user_publish AS tp';
        $tbl[] = 'tbl_user_publish_images AS tpi';
        $tbl[] = 'tbl_restaurant AS tr';

        $condition[] = 'tr.iRestaurantID = tp.iRestaurantID';
        $condition[] = 'tpi.iPublishID = tp.iPublishID';
        $condition[] = 'tr.iRestaurantID =' . $recorId;
        $condition[] = 'tp.eStatus IN(\'Active\')';
        $condition[] = 'tpi.eStatus IN(\'Active\')';

        $fields = 'SELECT ' . implode(',', $fields);
        $tbl = ' FROM ' . implode(',', $tbl);
        $condition = ' WHERE ' . implode(' AND ', $condition);
        $orderbypublish = ' ORDER BY tpi.tModifiedAt desc';

        $qry = $fields . $tbl . $condition . $orderbypublish;
        $publishedImages = $this->db->query($qry)->result_array();


        foreach ($publishedImages as $publishImages) {
            $arry1[] = $publishImages['image'];
        }

        //Review Photos
        $fields = $condition = $tbl = array();
        $fields[] = 'CONCAT("' . BASEURL . 'images/reviewImages/", IF(tpi.iReviewImage = \'\', "default.png", CONCAT(tp.iReviewID,\'/thumb/\',tpi.iReviewImage)) ) AS reviewImage';

        $tbl[] = 'tbl_restaurant_review AS tp';
        $tbl[] = 'tbl_restaurant_review_images AS tpi';
        $tbl[] = 'tbl_restaurant AS tr';

        $condition[] = 'tr.iRestaurantID = tp.iRestaurantID';
        $condition[] = 'tpi.iReviewID = tp.iReviewID';
        $condition[] = 'tp.iRestaurantID =' . $recorId;
        $condition[] = 'tr.eStatus IN(\'Active\')';
        $condition[] = 'tp.eStatus IN(\'active\')';
        $condition[] = 'tpi.eStatus IN(\'Active\')';

        $fields = 'SELECT ' . implode(',', $fields);
        $tbl = ' FROM ' . implode(',', $tbl);
        $condition = ' WHERE ' . implode(' AND ', $condition);
        $orderbyreview = ' ORDER BY tpi.tModifiedAt desc';

        $qry = $fields . $tbl . $condition . $orderbyreview;
        $reviewDataImages = $this->db->query($qry)->result_array();

        foreach ($reviewDataImages as $reviewImages) {
            $arry2[] = $reviewImages['reviewImage'];
        }
        $finalarry = array_merge($arry, $arry1, $arry2);
        if (empty($finalarry)) {
            //$finalarry[] = BASEURL . 'images/restaurantPhoto/default.png';
        }
        return $finalarry;
    }

    function getRestaurantTimeSlot($minTime, $maxTime) {

        if ($minTime !== '' && $maxTime !== '') {
            $minTime = explode('-', $minTime);
            $maxTime = explode('-', $maxTime);
            $arry = [];
            if (isset($minTime[0]) && isset($minTime[1]) && isset($minTime[2])) {
                $minMaradian = $minTime[2] == '1' ? 'AM' : 'PM';
                $maxMaradian = $maxTime[2] == '1' ? 'AM' : 'PM';

                $minhr = strlen($minTime[0]) == 1 ? '0' . $minTime[0] : $minTime[0];
                $maxhr = strlen($maxTime[0]) == 1 ? '0' . $maxTime[0] : $maxTime[0];

                $minmin = strlen($minTime[1]) == 1 ? '0' . $minTime[1] : $minTime[1];
                $maxmin = strlen($maxTime[1]) == 1 ? '0' . $maxTime[1] : $maxTime[1];

                $openCloseTime = $minhr . ':' . $minmin . ' ' . $minMaradian . ' to ' . $maxhr . ':' . $maxmin . ' ' . $maxMaradian;
                $openCloseTimeValue = $minhr . ':' . $minmin . ':' . $minMaradian . ' to ' . $maxhr . ':' . $maxmin . ':' . $maxMaradian;
                $arry['openCloseTime'] = $openCloseTime;
                $arry['openCloseTimeValue'] = $openCloseTimeValue;

                $openTimeNW = date('H:i', strtotime($minhr . ':' . $minmin . ' ' . $minMaradian));
                $closeTimeNW = date('H:i', strtotime($maxhr . ':' . $maxmin . ' ' . $maxMaradian));

                $openSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $openTimeNW . '\' order by `iSlotID` desc')->row_array();
                $openSlotTendTo = $this->db->query('SELECT tendTo FROM slot_master WHERE iSlotID IN(' . @$openSlot['iSlotID'] . ')')->row_array()['tendTo'];

                //echo $this->db->last_query();
                $openSlot = (int) @$openSlot['iSlotID'];

                $closeSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $closeTimeNW . '\'  order by `iSlotID` desc')->row_array();
                $closeSlot = (int) @$closeSlot['iSlotID'];

                $serverTime = time();
                $currentTimeNW = date('H:i', $serverTime);
                $currentSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $currentTimeNW . '\' order by `iSlotID` desc')->row_array();
                $currentSlot = (int) @$currentSlot['iSlotID'];

                $arry['openSlot'] = $openSlot;
                $arry['closeSlot'] = $closeSlot;
                $arry['currentSlot'] = $currentSlot;

//                $totalSlots = array();
//                if ($openSlot > $closeSlot) {
//                    $tmp1 = range($openSlot, 96);
//                    $tmp2 = range(1, $closeSlot);
//                    $totalSlots = array_merge($tmp1, $tmp2);
//                } else {
//                    $totalSlots = range($openCloseTime, $closeSlot);
//                }
                if ($closeTimeNW == '00:00' || $closeTimeNW == '23:45') {
                    $closeSlot = 96;
                } else if ($closeTimeNW > '00:00' && $maxMaradian == 'AM') {
                    $currentTimeAMPM = date('A', $serverTime);
                    $closeSlot = $closeSlot + 96;
                } else {
                    $closeSlot = $closeSlot;
                }

                //$arry['info']['totalSlot'] = $totalSlots;
                $isOpenNow = 'Closed';
                if ($currentSlot >= $openSlot && $currentSlot <= $closeSlot) {
                    $isOpenNow = 'Open';
                } else {
                    $currentTimeAMPM = date('A', $serverTime);
                    if ($currentTimeAMPM == 'AM') {
                        $currentSlot = $currentSlot + 97;
                    }
                    if ($currentSlot >= $openSlot && $currentSlot <= $closeSlot) {
                        $isOpenNow = 'Open';
                    }
                }

//                $isOpenNow = 'Closed';
//                if (in_array($currentSlot, $totalSlots)) {
//                    $isOpenNow = 'Open';
//                }
//
//                if ($currentSlot == $openSlot) {
//                    $openSlotTendToSTAMP = strtotime($openSlotTendTo);
//
//                    if ($openSlotTendToSTAMP > $serverTime) {
//                        $isOpenNow = 'Open';
//                    } else {
//                        $isOpenNow = 'Closed';
//                    }
//                }

                $arry['isOpenNow'] = $isOpenNow;
                return $arry;
            }
        }
    }

    function userList($data) {
        try {
            $limit = 5;
            $fields[] = 'tbl_user.vFullName AS name, tbl_user.iUserID AS id';
            $fields[] = 'IF(tbl_user.vProfilePicture = "" , CONCAT("' . USER_IMG . 'dM.png"), CONCAT("' . USER_IMG . '", tbl_user.iUserID, "/thumb/", tbl_user.vProfilePicture)) AS userProfile';
            $fields = implode(',', $fields);
            $sql = 'SELECT ' . $fields;
            $sql .= ' FROM `tbl_user` ';

            $con = '';
            if (!empty($data['name'])) { //add the parameter check here
                $con = ' WHERE ';
            }
            if (!empty($data['name'])) {
                $con .= ' vFullName LIKE "%' . $data['name'] . '%" ';
            }
            $sql .= $con;
            $sql .= ' ORDER BY iUserID desc';
            $sql .= ' LIMIT ' . $limit;

            return $this->db->query($sql)->result_array();
        } catch (Exception $ex) {
            throw new Exception('Error in userList function - ' . $ex);
        }
    }

    // function to fetch follower and following count
    function getFollowersAndFollowing($userId = '') {
        try {
            $count = array();
            if ($userId != '') {
                $sql = "SELECT count(1) as followerCount from tbl_followers "
                        . "where iUserID = " . $userId . " and eFollow = 'yes'";
                $countRec = $this->db->query($sql)->row_array();
                ;
                $followerCount = (int) $countRec['followerCount'];

                $sql1 = "SELECT count(1) as followingCount from tbl_followers "
                        . "where iFollowerUserID = " . $userId . " and eFollow = 'yes'";
                $countRec1 = $this->db->query($sql1)->row_array();
                $followingCount = (int) $countRec1['followingCount'];

                $count['followers'] = $followerCount;
                $count['following'] = $followingCount;
            }
            return $count;
        } catch (Exception $ex) {
            throw new Exception('Error in getFollowers function - ' . $ex);
        }
    }

    function getTotalPoints($userId) {
        
        try {
            $totalPoints = 0;
            if (!empty($userId)) {
                $sql = "SELECT iUserPointSystemID, iPoints FROM tbl_user_point_system";
                $pointSystemRes = $this->db->query($sql)->result_array();
                $pointSystem = array();
                foreach ($pointSystemRes AS $val) {
                    $pointSystem[$val["iUserPointSystemID"]] = $val["iPoints"];
                }
               // print_r($pointSystem); exit;

                $sql = "SELECT count(iUserPointSystemID) AS count, iUserPointSystemID FROM tbl_user_points WHERE iUserID = " . $userId . " GROUP BY iUserPointSystemID ";
                $pointsArray = $this->db->query($sql)->result_array();
              //  print_r($pointsArray); exit;
                $totalPoints = 0;
                if (is_array($pointsArray)) {
                    foreach ($pointsArray AS $points) {
                        $totalPoints += $pointSystem[$points["iUserPointSystemID"]] * $points["count"];
                    }
                }
            }
            return $totalPoints;
        } catch (Exception $ex) {
            throw new Exception('Error in getTotalPoints function - ' . $ex);
        }
    }

    // function to get pointwise yuser levels
    function getUserLevel($points) {
        try {
            $level = '';
            $sql = "SELECT vLevelName,iLevelNumber, IF( vLevelImage = '' , '', CONCAT('" . IMG_URL . "levels/', vLevelImage) ) AS image FROM tbl_level WHERE iLevelPoints <=" . $points . " order by iLevelPoints desc limit 1";
            $result = $this->db->query($sql)->row_array();
            $level['name'] = $result['vLevelName'];
            $level['level'] = $result['iLevelNumber'];

            $level['image'] = $result['image'];
            $sql = "SELECT max(iLevelPoints) AS maxPoints FROM tbl_level";
            $result = $this->db->query($sql)->row_array();
            $level['percentage'] = floor(($points / $result["maxPoints"]) * 100);
            return $level;
        } catch (Exception $ex) {
            throw new Exception('Error in userList function - ' . $ex);
        }
    }

    // function to get pointwise user levels
    function getUserLevelList() {
        try {
            $level = '';
            $sql = "SELECT vLevelName,min(iLevelPoints) as iLevelPoints FROM tbl_level group by vLevelName";
            $result = $this->db->query($sql)->result_array();
            $levelArray = [];
            foreach ($result AS $row) {
                $levelArray[$row["iLevelPoints"]] = $row["vLevelName"];
            }
            return $levelArray;
        } catch (Exception $ex) {
            throw new Exception('Error in userList function - ' . $ex);
        }
    }

    function getAllLevelsList() {
        try {
            $level = '';
            $sql = "SELECT iLevelNumber as levelNo,vLevelName as levelName,iLevelPoints as points, IF( vLevelImage = '' , '', CONCAT('" . IMG_URL . "levels/', vLevelImage) ) AS image FROM tbl_level order by iLevelPoints asc";
            $result = $this->db->query($sql)->result_array();
            $levelArray = [];
            $resultArr = array();
            foreach ($result AS $row) {
                $resultArr[$row["levelName"]][] = $row;
            }
            return $resultArr;
        } catch (Exception $ex) {
            throw new Exception('Error in getAllLevelsList function - ' . $ex);
        }
    }

    // function to get user review count
    function getUserReviewsCount($userId = '') {
        try {
            $reviewCount = 0;
            if ($userId != '') {
                $sql = "SELECT count(*) as reviewcount FROM "
                        . "`tbl_restaurant_review` WHERE `iUserID` "
                        . "= " . $userId . " AND tReviewDetail IS NOT NULL AND tReviewDetail <> '' AND eStatus = 'active'";
                $countRec = $this->db->query($sql)->row_array();

                $reviewCount = (int) $countRec['reviewcount'];
            }
            return $reviewCount;
        } catch (Exception $ex) {
            throw new Exception('Error in getUserReviewsCount function - ' . $ex);
        }
    }

    // function to get user rating count
    function getUserRatingsCount($userId = '') {
        try {
            $reviewCount = 0;
            if ($userId != '') {
                $sql = "SELECT count(DISTINCT iRestaurantID) as reviewcount FROM "
                        . "`tbl_restaurant_review` WHERE `iUserID` "
                        . "= " . $userId . " AND iAmbience > 0 AND iPrice > 0 AND iFood > 0 AND iService > 0 AND eStatus = 'active'";
                $countRec = $this->db->query($sql)->row_array();

                $reviewCount = (int) $countRec['reviewcount'];
            }
            return $reviewCount;
        } catch (Exception $ex) {
            throw new Exception('Error in getUserReviewsCount function - ' . $ex);
        }
    }

    // function to get user review count
    function getUserPhotosCount($userId = '') {
        try {
            $photoCount = 0;
            if ($userId != '') {
                $sql = "SELECT count(*) AS photoCount FROM tbl_restaurant_review INNER JOIN tbl_restaurant_review_images ON tbl_restaurant_review.iReviewID = tbl_restaurant_review_images.iReviewID WHERE tbl_restaurant_review.iUserId= " . $userId . " and tbl_restaurant_review.eStatus = 'active'";
                $countRec = $this->db->query($sql)->row_array();

                $sql = "SELECT count(*) AS photoCount FROM tbl_user_publish as tp INNER JOIN tbl_user_publish_images AS tpi ON tp.iPublishID = tpi.iPublishID WHERE tp.iUserID= " . $userId . " and tp.eStatus IN('Active') and tpi.eStatus IN('Active')";
                $countRec1 = $this->db->query($sql)->row_array();

                $photoCount = (int) $countRec['photoCount'] + (int) $countRec1['photoCount'];
            }
            return $photoCount;
        } catch (Exception $ex) {
            throw new Exception('Error in getUserPhotosCount function - ' . $ex);
        }
    }

    function checkFollowers($userId = '', $followerUserID = '') {
        $sql = 'select count(*) as count from tbl_followers where iUserID '
                . '= ' . $userId . ' and iFollowerUserID = ' . $followerUserID;
        $countRec = $this->db->query($sql)->row_array();
        if ($countRec['count'] > 0)
            return false;
        else
            return true;
    }

    // function to add follower of the user.
    function addFollowers($userId = '', $followerUserID = '') {
        $data = array(
            'iUserID' => $userId,
            'iFollowerUserID' => $followerUserID,
            'tCreatedAt' => date('Y:m:d H:i:s', time()),
        );

        $status = $this->db->insert('tbl_followers', $data);

        if ($status) {
            $sql1 = "SELECT count(1) as followingCount from tbl_followers "
                    . "where iFollowerUserID = " . $followerUserID . " and eFollow = 'yes'";
            $countRec1 = $this->db->query($sql1)->row_array();
            $followingCount = (int) $countRec1['followingCount'];

            //send push notification
            $this->load->model('usernotifications_model');
            $sql = "SELECT CONCAT(vFullName,' ', vLastName) AS name FROM tbl_user WHERE iUserID='$followerUserID'";
            $userName = $this->db->query($sql)->row_array();
            $message = $userName["name"] . " starts following you.";
            $pushData = array(
                "time" => "1 second",
                "notificationType" => 3
            );
            $this->usernotifications_model->sendNotification($userId, $message, $pushData);

            return $followingCount;
        }

        return $status;
    }

    // function to add follower of the user.
    function reviewLikeUnlike($userId, $reviewId, $action) {

        $sql = "SELECT count(1) as isLiked from tbl_restaurant_review_likes where iReviewID = '" . $reviewId . "' and iUserID = '" . $userId . "'";
        $countRec = $this->db->query($sql)->row_array();
        $isLiked = (int) $countRec['isLiked'];
        if (strtoupper(trim($action)) == "LIKE") {
            if (!$isLiked) {
                $data = array(
                    'iReviewID' => $reviewId,
                    'iUserID' => $userId,
                    'dtCreatedAt' => date('Y:m:d H:i:s')
                );
                $status = $this->db->insert('tbl_restaurant_review_likes', $data);

                //send push notification
                $this->load->model('usernotifications_model');
                $sql = "SELECT CONCAT(vFullName,' ', vLastName) AS name FROM tbl_user WHERE iUserID='$userId'";
                $userName = $this->db->query($sql)->row_array();
                $message = $userName["name"] . " liked your review.";
                $pushData = array(
                    "time" => "1 second",
                    "notificationType" => 7
                );
                $sql = "SELECT iUserID FROM tbl_restaurant_review WHERE iReviewID='$reviewId'";
                $user = $this->db->query($sql)->row_array();
                $this->usernotifications_model->sendNotification($user['iUserID'], $message, $pushData);
            }
        } else {
            if ($isLiked) {
                $sql1 = "DELETE FROM tbl_restaurant_review_likes where iReviewID = '" . $reviewId . "' and iUserID = '" . $userId . "'";
                $this->db->query($sql1);
            }
        }
        $sql1 = "SELECT count(1) as likeCount from tbl_restaurant_review_likes where iReviewID = " . $reviewId;
        $countRec1 = $this->db->query($sql1)->row_array();
        return (int) $countRec1['likeCount'];
    }

    // function to add follower of the user.
    function reviewAddComment($userId, $reviewId, $comment) {
        $data = array(
            'iReviewID' => $reviewId,
            'iUserID' => $userId,
            'tCommentText' => $comment,
            'dtCreatedAt' => date('Y:m:d H:i:s'),
            'dtModifiedAt' => date('Y:m:d H:i:s')
        );
        $status = $this->db->insert('tbl_restaurant_review_user_comment', $data);

        //send push notification
        $this->load->model('usernotifications_model');
        $sql = "SELECT CONCAT(vFullName,' ', vLastName) AS name FROM tbl_user WHERE iUserID='$userId'";
        $userName = $this->db->query($sql)->row_array();
        $message = $userName["name"] . " replied on your review.";
        $pushData = array(
            "time" => "1 second",
            "notificationType" => 1
        );
        $sql = "SELECT iUserID FROM tbl_restaurant_review WHERE iReviewID='$reviewId'";
        $user = $this->db->query($sql)->row_array();
        $this->usernotifications_model->sendNotification($user['iUserID'], $message, $pushData);

        $sql1 = "SELECT count(1) as commentCount from tbl_restaurant_review_user_comment where iReviewID = " . $reviewId;
        $countRec1 = $this->db->query($sql1)->row_array();
        return (int) $countRec1['commentCount'];
    }

    // fetching all available critic to be followed by the user
    function whoToFollowList($followerUserID) {
        $sql = 'SELECT 	iUserID as userID, CONCAT(vFirstName," ",vLastName) as '
                . 'name, IF(tbl_user.vProfilePicture = "" , '
                . 'CONCAT("' . USER_IMG . 'dM.png"), CONCAT("' . USER_IMG . '", '
                . 'tbl_user.iUserID, "/thumb/", tbl_user.vProfilePicture)) AS '
                . 'userProfile  FROM `tbl_user` WHERE  is_critic = 1 AND iUserID '
                . 'NOT IN (select iUserID from tbl_followers where '
                . 'iFollowerUserID = ' . $followerUserID . ' )';
        $res = $this->db->query($sql)->result_array();
        return $res;
    }

    function getReviewComments($reviewID, $limit = '') {
        $limitConditions = '';
        if ($limit) {
            $limitConditions = " LIMIT $limit";
        }
        $profilePic = ', IF(tbl_user.vProfilePicture = "" , CONCAT("' . USER_IMG . 'dM.png"), CONCAT("' . USER_IMG . '", tbl_user.iUserID, "/thumb/", tbl_user.vProfilePicture)) AS profile_pic';
        $sql = "select tbl_restaurant_review_user_comment.iCommentID as comment_id,DATE_FORMAT(tbl_restaurant_review_user_comment.dtModifiedAt, '%b %d, %Y, %h:%i %p') as dateTime, tbl_restaurant_review_user_comment.iReviewID AS review_id, tbl_restaurant_review_user_comment.iUserID AS user_id, tbl_restaurant_review_user_comment.tCommentText AS comment, CONCAT(tbl_user.vFirstName,' ',tbl_user.vLastName) AS name $profilePic from tbl_restaurant_review_user_comment INNER JOIN tbl_user ON tbl_user.iUserID=tbl_restaurant_review_user_comment.iUserID WHERE tbl_restaurant_review_user_comment.iReviewID='" . $reviewID . "' ORDER BY tbl_restaurant_review_user_comment.dtCreatedAt DESC" . $limitConditions;
        $reviewComments = $this->db->query($sql)->result_array();
        return $reviewComments;
    }

    function getUserFeeds($followerUserID) {
        $sql = 'select tbl_restaurant_review.iReviewID as review_id, tbl_restaurant_review.iUserID as user_id, tbl_restaurant_review.iRestaurantID AS restaurant_id, tbl_restaurant_review.iRateValue AS rate_value, tbl_restaurant_review.iAmbience AS ambience, tbl_restaurant_review.iPrice AS price, tbl_restaurant_review.iFood AS food, tbl_restaurant_review.iService AS service, tbl_restaurant_review.tReviewDetail AS review, tbl_restaurant_review.tCreatedAt as created_at, CONCAT(tbl_user.vFirstName," ",tbl_user.vLastName) AS name, IF(tbl_user.vProfilePicture = "" , CONCAT("' . USER_IMG . 'dM.png"), CONCAT("' . USER_IMG . '", tbl_user.iUserID, "/thumb/", tbl_user.vProfilePicture)) AS profile_pic, tbl_restaurant.vRestaurantName AS restaurant_name from tbl_restaurant_review LEFT JOIN tbl_user ON tbl_user.iUserID=tbl_restaurant_review.iUserID  LEFT JOIN tbl_restaurant ON tbl_restaurant.iRestaurantID=tbl_restaurant_review.iRestaurantID where tbl_restaurant_review.eStatus = "active" and tbl_restaurant_review.iUserID IN (select tbl_followers.iUserID from tbl_followers where tbl_followers.iFollowerUserID = ' . $followerUserID . ' ) ORDER BY tbl_restaurant_review.tModifiedAt DESC';
        $reviews = $this->db->query($sql)->result_array();
        foreach ($reviews AS $key => $review) {
            $reviews[$key]["review_images"] = array();
            $sql = "select tbl_restaurant_review_images.iReviewImage as review_image from tbl_restaurant_review_images WHERE tbl_restaurant_review_images.iReviewID='" . $review['review_id'] . "'";
            $reviewImages = $this->db->query($sql)->result_array();
            if ($reviewImages) {
                foreach ($reviewImages AS $img) {
                    $reviews[$key]["review_images"][] = IMG_URL . "reviewImages/" . $review["review_id"] . "/" . $img['review_image'];
                }
            }
            if (empty($reviewImages) && empty($review['review'])) {
                unset($reviews[$key]);
                continue;
            }

            $createdAt = new DateTime($review['created_at']);
            $now = new DateTime();
            $diff = $createdAt->diff($now);
            if ($diff->y == 0) {
                if ($diff->m == 0) {
                    if ($diff->d == 0) {
                        if ($diff->h == 0) {
                            if ($diff->i == 0) {
                                $reviews[$key]['created_at'] = $diff->s . " seconds ago";
                            } else {
                                $reviews[$key]['created_at'] = $diff->i . " minutes ago";
                            }
                        } else {
                            $reviews[$key]['created_at'] = $diff->h . " hours ago";
                        }
                    } else {
                        $reviews[$key]['created_at'] = $diff->d . " days ago";
                    }
                } else {
                    $reviews[$key]['created_at'] = $diff->m . " months ago";
                }
            } else {
                $reviews[$key]['created_at'] = $diff->y . " years ago";
            }
            $sql = "select count(*) AS likeCount from tbl_restaurant_review_likes WHERE tbl_restaurant_review_likes.iReviewID='" . $review['review_id'] . "'";
            $res = $this->db->query($sql)->row_array();
            $reviews[$key]["reviewLikeCount"] = $res["likeCount"];

            $sql = "SELECT count(*) as isLiked from tbl_restaurant_review_likes WHERE tbl_restaurant_review_likes.iReviewID='" . $review['review_id'] . "' and tbl_restaurant_review_likes.iUserID = '" . $followerUserID . "'";
            $countRec = $this->db->query($sql)->row_array();
            $reviews[$key]["isLiked"] = (int) $countRec['isLiked'];

            $sql = "select count(*) AS commentCount from tbl_restaurant_review_user_comment WHERE tbl_restaurant_review_user_comment.iReviewID='" . $review['review_id'] . "'";
            $res = $this->db->query($sql)->row_array();
            $reviews[$key]["reviewCommentCount"] = $res["commentCount"];

            $reviews[$key]["reviewComments"] = $this->getReviewComments($review['review_id'], 3);
        }
        return $reviews;
    }

    function getOthersActivities($userID) {
        $result = array();
        $sql = 'select tbl_restaurant_review_likes.iReviewID as review_id, tbl_restaurant_review_likes.iUserID as user_id, tbl_restaurant_review_likes.dtCreatedAt AS created_at, CONCAT(tbl_user.vFirstName," ",tbl_user.vLastName) AS name, IF(tbl_user.vProfilePicture = "" , CONCAT("' . USER_IMG . 'dM.png"), CONCAT("' . USER_IMG . '", tbl_user.iUserID, "/thumb/", tbl_user.vProfilePicture)) AS profile_pic, tbl_restaurant_review.iRestaurantID as restaurent_id from tbl_restaurant_review_likes LEFT JOIN tbl_user ON tbl_user.iUserID=tbl_restaurant_review_likes.iUserID LEFT JOIN tbl_restaurant_review ON tbl_restaurant_review.iReviewID=tbl_restaurant_review_likes.iReviewID  where tbl_restaurant_review_likes.iUserID IN (select tbl_followers.iUserID from tbl_followers where tbl_followers.iFollowerUserID = ' . $userID . ' ) and tbl_restaurant_review.eStatus = "active" ORDER BY dtCreatedAt DESC LIMIT 5';
        $likes = $this->db->query($sql)->result_array();
        foreach ($likes AS $key => $like) {
            $like["type"] = "like";
            $like["review_images"] = array();
            $sql = "select tbl_restaurant_review_images.iReviewImage as review_image from tbl_restaurant_review_images WHERE tbl_restaurant_review_images.iReviewID='" . $like['review_id'] . "'";
            $reviewImages = $this->db->query($sql)->result_array();
            if ($reviewImages) {
                foreach ($reviewImages AS $img) {
                    $like["review_images"][] = IMG_URL . "reviewImages/" . $like["review_id"] . "/" . $img['review_image'];
                }
            }

            $like["restaurant_name"] = '';
            $sql = "select tbl_restaurant.vRestaurantName AS restaurant_name from tbl_restaurant WHERE tbl_restaurant.iRestaurantID='" . $like["restaurent_id"] . "'";
            $restaurentName = $this->db->query($sql)->row_array();
            if ($restaurentName) {
                $like["restaurant_name"] = $restaurentName["restaurant_name"];
            }
            $result[] = $like;
        }

        $sql = 'select tbl_restaurant_review_user_comment.iReviewID as review_id, tbl_restaurant_review_user_comment.iUserID as user_id, tbl_restaurant_review_user_comment.dtCreatedAt AS created_at, tbl_restaurant_review_user_comment.tCommentText AS comment, CONCAT(tbl_user.vFirstName," ",tbl_user.vLastName) AS name, IF(tbl_user.vProfilePicture = "" , CONCAT("' . USER_IMG . 'dM.png"), CONCAT("' . USER_IMG . '", tbl_user.iUserID, "/thumb/", tbl_user.vProfilePicture)) AS profile_pic, tbl_restaurant_review.iRestaurantID as restaurent_id from tbl_restaurant_review_user_comment LEFT JOIN tbl_user ON tbl_user.iUserID=tbl_restaurant_review_user_comment.iUserID LEFT JOIN tbl_restaurant_review ON tbl_restaurant_review.iReviewID=tbl_restaurant_review_user_comment.iReviewID  where tbl_restaurant_review_user_comment.iUserID IN (select tbl_followers.iUserID from tbl_followers where tbl_followers.iFollowerUserID = ' . $userID . ' ) and tbl_restaurant_review.eStatus = "active" ORDER BY dtCreatedAt DESC LIMIT 5';
        $comments = $this->db->query($sql)->result_array();
        foreach ($comments AS $key => $comment) {
            $comment["type"] = "comment";
            $comment["review_images"] = array();
            $sql = "select tbl_restaurant_review_images.iReviewImage as review_image from tbl_restaurant_review_images WHERE tbl_restaurant_review_images.iReviewID='" . $comment['review_id'] . "'";
            $reviewImages = $this->db->query($sql)->result_array();
            if ($reviewImages) {
                foreach ($reviewImages AS $img) {
                    $comment["review_images"][] = IMG_URL . "reviewImages/" . $comment["review_id"] . "/" . $img['review_image'];
                }
            }

            $comment["restaurant_name"] = '';
            $sql = "select tbl_restaurant.vRestaurantName AS restaurant_name from tbl_restaurant WHERE tbl_restaurant.iRestaurantID='" . $comment["restaurent_id"] . "'";
            $restaurentName = $this->db->query($sql)->row_array();
            if ($restaurentName) {
                $comment["restaurant_name"] = $restaurentName["restaurant_name"];
            }
            $result[] = $comment;
        }
        usort($result, function($postA, $postB) {
            if ($postA["created_at"] == $postB["created_at"]) {
                return 0;
            }
            return ($postA["created_at"] < $postB["created_at"]) ? 1 : -1;
        });

        return array_slice($result, 0, 5, true);
    }

    function getSelfActivities($userID) {
        $result = array();

        $sql = 'select tbl_restaurant_review_likes.iReviewID as review_id, tbl_restaurant_review_likes.iUserID as user_id, tbl_restaurant_review_likes.dtCreatedAt AS created_at, CONCAT(tbl_user.vFirstName," ",tbl_user.vLastName) AS name, IF(tbl_user.vProfilePicture = "" , CONCAT("' . USER_IMG . 'dM.png"), CONCAT("' . USER_IMG . '", tbl_user.iUserID, "/thumb/", tbl_user.vProfilePicture)) AS profile_pic, tbl_restaurant_review.iRestaurantID as restaurent_id, tbl_followers.iFollowerUserID AS is_following from tbl_restaurant_review_likes LEFT JOIN tbl_user ON tbl_user.iUserID=tbl_restaurant_review_likes.iUserID LEFT JOIN tbl_restaurant_review ON tbl_restaurant_review.iReviewID=tbl_restaurant_review_likes.iReviewID LEFT JOIN tbl_followers ON tbl_followers.iUserID=tbl_restaurant_review_likes.iUserID AND tbl_followers.iFollowerUserID=' . $userID . ' where tbl_restaurant_review_likes.iReviewID IN (select tbl_restaurant_review.iReviewID from tbl_restaurant_review where tbl_restaurant_review.iUserID = ' . $userID . ' ) AND tbl_restaurant_review_likes.iUserID <>' . $userID . ' ORDER BY dtCreatedAt DESC LIMIT 5';
        $likes = $this->db->query($sql)->result_array();
        foreach ($likes AS $key => $like) {
            $like["type"] = "like";
            $like["review_images"] = array();
            $sql = "select tbl_restaurant_review_images.iReviewImage as review_image from tbl_restaurant_review_images WHERE tbl_restaurant_review_images.iReviewID='" . $like['review_id'] . "'";
            $reviewImages = $this->db->query($sql)->result_array();
            if ($reviewImages) {
                foreach ($reviewImages AS $img) {
                    $like["review_images"][] = IMG_URL . "reviewImages/" . $like["review_id"] . "/" . $img['review_image'];
                }
            }

            $like["restaurant_name"] = '';
            $sql = "select tbl_restaurant.vRestaurantName AS restaurant_name from tbl_restaurant WHERE tbl_restaurant.iRestaurantID='" . $like["restaurent_id"] . "'";
            $restaurentName = $this->db->query($sql)->row_array();
            if ($restaurentName) {
                $like["restaurant_name"] = $restaurentName["restaurant_name"];
            }

            $like["is_following"] = empty($like["is_following"]) ? false : true;

            $result[] = $like;
        }

        $sql = 'select tbl_restaurant_review_user_comment.iReviewID as review_id, tbl_restaurant_review_user_comment.iUserID as user_id, tbl_restaurant_review_user_comment.dtCreatedAt AS created_at, tbl_restaurant_review_user_comment.tCommentText AS comment, CONCAT(tbl_user.vFirstName," ",tbl_user.vLastName) AS name, IF(tbl_user.vProfilePicture = "" , CONCAT("' . USER_IMG . 'dM.png"), CONCAT("' . USER_IMG . '", tbl_user.iUserID, "/thumb/", tbl_user.vProfilePicture)) AS profile_pic, tbl_restaurant_review.iRestaurantID as restaurent_id, tbl_followers.iFollowerUserID AS is_following from tbl_restaurant_review_user_comment LEFT JOIN tbl_user ON tbl_user.iUserID=tbl_restaurant_review_user_comment.iUserID LEFT JOIN tbl_restaurant_review ON tbl_restaurant_review.iReviewID=tbl_restaurant_review_user_comment.iReviewID LEFT JOIN tbl_followers ON tbl_followers.iUserID=tbl_restaurant_review_user_comment.iUserID AND tbl_followers.iFollowerUserID=' . $userID . ' where tbl_restaurant_review_user_comment.iReviewID IN (select tbl_restaurant_review.iReviewID from tbl_restaurant_review where tbl_restaurant_review.iUserID = ' . $userID . ' ) AND tbl_restaurant_review_user_comment.iUserID <>' . $userID . ' ORDER BY dtCreatedAt DESC LIMIT 5';
        $comments = $this->db->query($sql)->result_array();
        foreach ($comments AS $key => $comment) {
            $comment["type"] = "comment";
            $comment["review_images"] = array();
            $sql = "select tbl_restaurant_review_images.iReviewImage as review_image from tbl_restaurant_review_images WHERE tbl_restaurant_review_images.iReviewID='" . $comment['review_id'] . "'";
            $reviewImages = $this->db->query($sql)->result_array();
            if ($reviewImages) {
                foreach ($reviewImages AS $img) {
                    $comment["review_images"][] = IMG_URL . "reviewImages/" . $comment["review_id"] . "/" . $img['review_image'];
                }
            }

            $comment["restaurant_name"] = '';
            $sql = "select tbl_restaurant.vRestaurantName AS restaurant_name from tbl_restaurant WHERE tbl_restaurant.iRestaurantID='" . $comment["restaurent_id"] . "'";
            $restaurentName = $this->db->query($sql)->row_array();
            if ($restaurentName) {
                $comment["restaurant_name"] = $restaurentName["restaurant_name"];
            }

            $comment["is_following"] = empty($comment["is_following"]) ? false : true;

            $result[] = $comment;
        }
        usort($result, function($postA, $postB) {
            if ($postA["created_at"] == $postB["created_at"]) {
                return 0;
            }
            return ($postA["created_at"] < $postB["created_at"]) ? 1 : -1;
        });

        return array_slice($result, 0, 5, true);
        ;
    }

    function searchUser($userId, $searchText) {
        $sql = 'SELECT 	iUserID as userID, CONCAT(vFirstName," ",vLastName) as '
                . 'name, IF(tbl_user.vProfilePicture = "" , '
                . 'CONCAT("' . USER_IMG . 'dM.png"), CONCAT("' . USER_IMG . '", '
                . 'tbl_user.iUserID, "/thumb/", tbl_user.vProfilePicture)) AS '
                . 'userProfile  FROM `tbl_user` WHERE  iUserID '
                . 'NOT IN (select iUserID from tbl_followers where '
                . 'iFollowerUserID = ' . $userId . ' ) AND (iUserID LIKE \'' . $searchText . '%\' OR vFirstName LIKE \'' . $searchText . '%\' OR vLastName LIKE \'' . $searchText . '%\' OR CONCAT(vFirstName," ",vLastName) LIKE \'' . $searchText . '%\' ) AND iUserID<>' . $userId;

        $res = $this->db->query($sql)->result_array();
        return $res;
    }

    function reportReview($userId, $reviewId, $reportType) {

        $data = array(
            'iReviewID' => $reviewId,
            'iUserID' => $userId,
            'eReportType' => $reportType,
            'dtCreatedAt' => date('Y:m:d H:i:s'),
            'dtModifiedAt' => date('Y:m:d H:i:s')
        );
        $this->db->insert('tbl_restaurant_review_report', $data);

        $sql1 = "SELECT CONCAT(vFirstName, ' ',vLastName) as name from tbl_user where iUserID = " . $userId;
        $rec = $this->db->query($sql1)->row_array();

        //send email
        $subject = 'Review Report';
        $this->load->model("smtpmail_model", "smtpmail_model");
        $param = array(
            '%MAILSUBJECT%' => 'Foodine : ' . $subject,
            '%LOGO_IMAGE%' => IMG_URL . 'foodine.png',
            '%REPORT_TYPE%' => $reportType,
            '%USER%' => $rec['name'],
            '%REVIEW_ID%' => $reviewId
        );

        $tmplt = DIR_VIEW . 'email/report_review.php';
        $subject = 'Foodine : ' . $subject;
        $to = "support@Foodine.com";
        $this->smtpmail_model->send($to, $subject, $tmplt, $param);
        return true;
    }

    function saveOTP($mobile) {

        $sqlM = 'select count(*) as count from tbl_user where vMobileNo '
                . '= ' . $mobile;
        $countRecMobile = $this->db->query($sqlM)->row_array();
        if ($countRecMobile['count'] > 0) {
            return '';
        }
        $rand = random_string('numeric', 4);

        $sql = 'select count(*) as count, sendOtpCount from tbl_otp_verification where mobile '
                . '= ' . $mobile;
        $countRec = $this->db->query($sql)->row_array();
        if ($countRec['count'] > 0) {
            $otpCount = $countRec['sendOtpCount'] + 1;
            $data = array(
                'verificationCode' => $rand,
                'sendOtpCount' => $otpCount,
                'status' => 'Active',
                'modified' => date('Y-m-d H:i:s')
            );
            $result = $this->db->update('tbl_otp_verification', $data, array('mobile' => $mobile));
        } else {
            $data = array(
                'mobile' => $mobile,
                'verificationCode' => $rand,
                'sendOtpCount' => 1,
                'status' => 'Active',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            );
            $result = $this->db->insert('tbl_otp_verification', $data);
        }
        $sdata = array(
            'mobile' => $mobile,
            'verificationCode' => $rand,
            'sent' => date('Y-m-d H:i:s'),
            'recieved' => date('Y-m-d H:i:s')
        );
        $res = $this->db->insert('tbl_otp_verification_sms_logs', $sdata);
        if ($result == 1) {
            return $rand;
        }
        return '';
    }

    function verifyOTP($data) {
        $time = strtotime("-10 minutes");
        $sql = 'select count(*) as count,status , modified from tbl_otp_verification where mobile ' . '= ' . $data['userMobile'] . " and verificationCode = " . $data['userOTP'];
        $countRec = $this->db->query($sql)->row_array();
        if ($countRec['count'] > 0) {
            if ($countRec['status'] == 'Active') {
                $lastmodified = strtotime($countRec['modified']);
                if ($lastmodified >= $time) {
                    $result = $this->db->update('tbl_otp_verification', array('status' => 'Expired', 'verificationStatus' => '1'), array('mobile' => $data['userMobile']));
                    return 1;
                } else {
                    $result = $this->db->update('tbl_otp_verification', array('status' => 'Expired'), array('mobile' => $data['userMobile']));
                    return 2; //OTP Expired
                }
            } else {
                return 2; //OTP Expired
            }
        }
        return false;
    }

    function checkMobileVerified($mobile, $email) {
        $res = $this->db->select('iUserID')->get_where($this->table, array('vEmail' => $email, 'vMobileNo' => $mobile))->row_array();

        if (count($res) > 0) {
            return 1;
        }
        return '';
    }

    function getVouchers($postValues) {
        try {

            if (!empty($postValues)) {
                extract($postValues);
                if ($userId != '') {
                    $tbl = $fields = $condition = $conditionOR = array();

                    $tbl[] = 'tbl_vouchers AS tv';

                    $fields[] = 'tv.title AS title';
                    $fields[] = 'tv.code AS code';
                    $fields[] = 'DATE_FORMAT(tv.startDate,"%d/%m/%Y") AS startDate';
                    $fields[] = 'DATE_FORMAT(tv.endDate,"%d/%m/%Y") AS endDate';
                    $fields[] = 'DATE_FORMAT(tv.endDate,"%b %d, %Y, %h:%i %p") AS validityDate';
                    $fields[] = 'tv.valueType AS valueType';
                    $fields[] = 'tv.value AS value';
                    $fields[] = 'tv.description AS description';
                    $fields[] = 'tv.itemPrice AS itemPrice';
                    $fields[] = 'tv.userSpecific AS userSpecific';
                    $fields[] = 'tv.userId AS userId';
                    $fields[] = 'tv.status AS status';
                    $fields[] = 'tv.voucherUseId AS voucherUseId';
                    $fields[] = 'tvu.description AS vUseDescription';

                    if (isset($type) && !empty($type)) {

                        if ($type == 'availed') {
                            $condition[] = 'tv.eAvailedStatus IN(\'Availed\')';
                        }
                        if ($type == 'notavailed') {
                            $condition[] = 'tv.eAvailedStatus IN(\'Not Availed\')';
                        }
                    }
                    $condition[] = 'tv.status IN(\'Active\')';
                    $condition[] = 'tv.userId IN(' . $userId . ')';
                    $conditionOR = 'tv.userSpecific IN(\'No\')';


                    $join[] = array(
                        $join_type => 'JOIN tbl_voucher_use AS tvu',
                        'ON' => 'tvu.id IN(tv.voucherUseId)'
                    );

                    $join_str = '';
                    for ($i = 0; $i < count($join); $i++) {
                        foreach ($join[$i] AS $key => $val) {
                            $join_str .= ' ' . $key . ' ' . $val . ' ';
                        }
                    }

                    $tbl = ' FROM ' . implode(',', $tbl);
                    $fields = implode(',', $fields);
                    $condition = ' WHERE ' . implode(' AND ', $condition) . ' OR ' . $conditionOR;

                    $qry = 'SELECT ' . $fields . $tbl . $join_str . $condition;
                    $res = $this->db->query($qry);

                    if ($res->num_rows() > 0) {
                        return $res->result_array();
                    } return array();
                }return array();
            }return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getVouchers function - ' . $ex);
        }
    }

    function getPurchase($userId) {
        try {
            if ($userId != '') {
                $currentDate = date('Y-m-d');
                $tbl = $fields = $condition = $conditionOR = array();

                $tbl[] = 'tbl_user_combo AS tuc';

                $fields[] = 'tuc.iRestaurantID AS id';
                $fields[] = 'tuc.OrderID AS orderId';
                $fields[] = 'tcso.vOfferTitle AS title';
                $fields[] = 'tcso.tOfferDetail AS detail';
                $fields[] = 'tcso.tActualPrice AS actualPrice';
                $fields[] = 'tuc.tDiscountedPrice AS discountedPrice';
                $fields[] = 'tr.vRestaurantName AS restaurantName';
                $fields[] = 'date(tuc.dtExpiryDate) AS comboExpiryDate';
                $fields[] = 'tuc.iTotal AS totalPrice';
                $fields[] = 'tuc.qty AS quantity';
                $fields[] = 'tuc.vDaysAllow AS comboDays';
                $fields[] = 'DATE_FORMAT(tuc.tCreatedAt,"%b %d, %Y") AS purchasedDate';
                //$fields[] = 'tuc.eBookingStatus AS eBookingStatus';
                $fields[] = 'tuc.iComboSubOffersID AS comboSubOffersId';
                $fields[] = 'tuc.iUserComboID AS comboId';
                $fields[] = 'tuc.iComboOffersID AS comboOfferId';
                $fields[] = 'tco.tTermsOfUse AS comboTermsUse';
                $fields[] = 'CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage =\'\', "default.png", CONCAT(`tco`.iRestaurantID,\'/\',`tco`.vOfferImage))) AS comboImage';
                $fields[] = 'tco.vOfferText AS comboText';
                $fields[] = 'tuc.eAvailedStatus AS availedStatus';
                $fields[] = 'tuc.bookingStatus AS bookingStatus';
                $condition[] = 'tuc.iUserID IN(' . $userId . ')';
                $condition[] = 'tuc.eBookingStatus IN(\'Paid\')';

                $join[] = array(
                    $join_type => 'JOIN tbl_restaurant AS tr',
                    'ON' => 'tuc.iRestaurantID IN(tr.iRestaurantID)'
                );
                $join[] = array(
                    $join_type => 'JOIN tbl_combo_offers AS tco',
                    'ON' => 'tuc.iComboOffersID IN(tco.iComboOffersID)'
                );
                $join[] = array(
                    $join_type => 'JOIN tbl_combo_sub_offers AS tcso',
                    'ON' => 'tuc.iComboSubOffersID IN(tcso.iComboSubOffersID)'
                );

                $join_str = '';
                for ($i = 0; $i < count($join); $i++) {
                    foreach ($join[$i] AS $key => $val) {
                        $join_str .= ' ' . $key . ' ' . $val . ' ';
                    }
                }

                $tbl = ' FROM ' . implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = ' WHERE ' . implode(' AND ', $condition);
                $orderBy = ' ORDER BY tuc.tCreatedAt desc';
                $qry = 'SELECT ' . $fields . $tbl . $join_str . $condition . $orderBy;

                $res = $this->db->query($qry);


                if ($res->num_rows() > 0) {
                    $result = $res->result_array();
                    foreach ($result as $key => $value) {
                        $perDiscount = round(((($value['actualPrice'] - $value['discountedPrice']) * 100) / $value['actualPrice']), 0);
                        $result[$key]['perDiscount'] = $perDiscount . '%';
                        if ($value['comboExpiryDate'] < $currentDate) {
                            $result[$key]['availedStatus'] = 'EXPIRED';
                        } else {
                            $result[$key]['availedStatus'] = strtoupper($value['availedStatus']);
                        }
                        $result[$key]['comboID'] = 'HMCOMBO' . $value['comboOfferId'];
                        $restAlias = strtolower(preg_replace("/\s+/", "-", trim($value['restaurantName'])));
                        $result[$key]['restaurantAlias'] = str_replace("'", "", $restAlias) . '-' . $value['id'];
                    }
                    return $result;
                } return array();
            } return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getVouchers function - ' . $ex);
        }
    }

    function friendList() {
        try {
            $qry = "select tu.iUserID AS id, tu.vFirstName AS fname, tu.vLastName AS lname FROM tbl_user AS tu WHERE tu.eStatus IN('Active') and tu.vFirstName != '' order by tu.vFirstName asc";
            $row = $this->db->query($qry)->result_array();
            foreach ($row as $k => $row1) {
                $row[$k]['fullname'] = $row1['fname'] . ' ' . $row1['lname'];
            }
            return $row;
        } catch (Exception $ex) {
            throw new Exception('Error in allUser function - ' . $ex);
        }
    }

    function getUserBadges($userId) {

        $reservationsql = "SELECT count(*) AS booking_count FROM tbl_table_book "
                . "WHERE iUserID= " . $userId . " and eBookingStatus='Accept'";
        $reservationres = $this->db->query($reservationsql)->row_array();
        $reservations = (int) $reservationres["booking_count"];

        $paymentsql = "SELECT count(*) AS payments_count FROM tbl_order "
                . "WHERE user_id= " . $userId . " and status='success'";
        $paymentres = $this->db->query($paymentsql)->row_array();
        $payments = (int) $paymentres["payments_count"];

        $reviews = $this->getUserReviewsCount($userId);

        $offersql = "SELECT count(*) as offers_count FROM tbl_user_combo "
                . "WHERE iUserID= $userId AND eBookingStatus='Paid'";
        $offerres = $this->db->query($offersql)->row_array();
        $offers = (int) $offerres['offers_count'];
//        $badgessql = "SELECT id, name, IF( image = '' , '', CONCAT('" . IMG_URL . "badges/', image) ) AS image,reservations_required AS reservationsRequired, offers_required AS offersRequired, payments_required AS paymentsRequired, reviews_required AS reviewsRequired FROM tbl_badges WHERE "
//                . "reservations_required <= " . $reservations . " AND "
//                . "offers_required <=" . $offers . " AND "
//                . "payments_required <=" . $payments . " AND "
//                . "reviews_required <=" . $reviews;


        $badgessql = "SELECT id, name, IF( image = '' , '', CONCAT('" . IMG_URL . "badges/', image) ) AS image,reservations_required AS reservationsRequired, offers_required AS offersRequired, payments_required AS paymentsRequired, reviews_required AS reviewsRequired FROM tbl_badges";
        $badges = $this->db->query($badgessql)->result_array();

        if (!empty($badges)) {
            foreach ($badges as $key => $value) {
                $badges[$key]['status'] = 'inactive';
                if ($value['reservationsRequired'] <= $reservations && $value['offersRequired'] <= $offers && $value['paymentsRequired'] <= $payments && $value['reviewsRequired'] <= $reviews) {
                    $badges[$key]['status'] = 'active';
                }
                $detail = array();
                if (!empty($value['reservationsRequired'])) {
                    $count = $value['reservationsRequired'];
                    $detail[] = ' Successful reservations';
                }
                if (!empty($value['offersRequired'])) {
                    $count = $value['offersRequired'];
                    $detail[] = ' Offers Used';
                }
                if (!empty($value['paymentsRequired'])) {
                    $count = $value['paymentsRequired'];
                    $detail[] = ' Successful payments';
                }
                if (!empty($value['reviewsRequired'])) {
                    $count = $value['reviewsRequired'];
                    $detail[] = ' Reviews';
                }
                $detail = implode(",", $detail);
                $badges[$key]['badgeContent'] = $count . $detail;
                $badges[$key]['datedOn'] = date('d-m-Y');
            }

            usort($badges, function($rowA, $rowB) {
                if ($rowA["status"] == $rowB["status"]) {
                    return 0;
                }
                return ($rowA["status"] < $rowB["status"]) ? -1 : 1;
            });
        }

        return $badges;
    }

    function _prepareReviewRatingData($userId, $reviews) {
        $reviewArr = $updateReviewArr = array();
        foreach ($reviews AS $review) {
            $updateReviewArr = $review;
            $sql = "SELECT count(*) as isLiked from tbl_restaurant_review_likes WHERE tbl_restaurant_review_likes.iReviewID='" . $review['reviewId'] . "' and tbl_restaurant_review_likes.iUserID = '" . $userId . "'";
            $countRec = $this->db->query($sql)->row_array();
            $updateReviewArr["isLiked"] = (int) $countRec['isLiked'];

            $sql = "select count(*) AS likeCount from tbl_restaurant_review_likes WHERE tbl_restaurant_review_likes.iReviewID='" . $review['reviewId'] . "'";
            $res = $this->db->query($sql)->row_array();
            $updateReviewArr["reviewLikeCount"] = $res["likeCount"];

            $sql = "select count(*) AS commentCount from tbl_restaurant_review_user_comment WHERE tbl_restaurant_review_user_comment.iReviewID='" . $review['reviewId'] . "'";
            $res = $this->db->query($sql)->row_array();
            $updateReviewArr["reviewCommentCount"] = $res["commentCount"];
            $this->load->model("user_model", "user_model");
            $updateReviewArr["reviewComments"] = $this->user_model->getReviewComments($review['reviewId']);

            $updateReviewArr["reviewImages"] = array();
            $sql = "select tbl_restaurant_review_images.iReviewImage as review_image from tbl_restaurant_review_images WHERE tbl_restaurant_review_images.iReviewID='" . $review['reviewId'] . "'";
            $reviewImages = $this->db->query($sql)->result_array();
            if ($reviewImages) {
                foreach ($reviewImages AS $img) {
                    $updateReviewArr["reviewImages"][] = REVIEW_IMG . $review["reviewId"] . "/" . $img['review_image'];
                }
            }
            $reviewArr[] = $updateReviewArr;
        }
        return $reviewArr;
    }

    function getExpertReviews($postValues) {
        try {
            if (!empty($postValues)) {
                extract($postValues);

                $sql = 'SELECT trr.iRestaurantID AS restaurantId, tr.vRestaurantName AS restaurantName, ';
                //$sql .= 'trr.iAmbience as ambience, trr.iPrice as price, trr.iFood as food, trr.iService as service,';
                //$sql .= ' TRUNCATE((trr.iAmbience + trr.iPrice + trr.iFood + trr.iService) /8, 2 ) AS rating,';
                $sql .= ' trr.tReviewDetail AS reviewComment, DATE_FORMAT(trr.tModifiedAt, "%b %d, %Y, %h:%i %p")AS dateTime,';

                $sql .= ' TRUNCATE(AVG((trr2.iAmbience + trr2.iPrice + trr2.iFood + trr2.iService ) /8 ), 2) reviewRate';

                $sql .= ' FROM  `tbl_restaurant_review` AS  `trr`';
                $sql .= ' LEFT JOIN  `tbl_restaurant` AS  `tr` ON  `trr`.`iRestaurantID` =  `tr`.`iRestaurantID`';
                $sql .= ' LEFT JOIN  tbl_restaurant_review AS  trr2 ON  trr.iRestaurantID =  trr2.iRestaurantID';
                $sql .= ' WHERE trr.iUserID = ' . $userId . ' AND trr.tReviewDetail IS NOT NULL AND trr.tReviewDetail <> "" AND trr.eStatus = "active"';
                if (isset($locId) && !empty($locId)) {
                    $sql .= ' AND tr.iLocationID = ' . $locId;
                }
                $sql .= ' GROUP BY trr.iReviewID ORDER BY trr.tModifiedAt DESC';

                $reviews = $this->db->query($sql)->result_array();
                return $reviews;
            } return false;
        } catch (Exception $ex) {
            throw new Exception('Error in getExpertReviews function - ' . $ex);
        }
    }

    function getRatings($postValues) {
        try {
            if (!empty($postValues)) {
                extract($postValues);
                    
                $sql .= 'SELECT max(iReviewID) AS id FROM  tbl_restaurant_review WHERE iUserID = ' . $userId . ' AND eStatus = "active" GROUP BY iRestaurantID';
                //$sql .= 'SELECT max(iReviewID) AS id FROM  tbl_restaurant_review WHERE iUserID = ' . $userId . ' AND iAmbience > 0 AND iPrice > 0 AND iFood > 0 AND iService > 0 AND eStatus = "active" GROUP BY iRestaurantID';
                $idArray = array();
                $res = $this->db->query($sql)->result_array();
                foreach ($res as $row) {
                    $idArray[] = $row["id"];
                }
                $idStr = implode(",", $idArray);

                $sql = 'SELECT trr.iRestaurantID AS restaurantId, tr.vRestaurantName AS restaurantName, ';
               // $sql .= 'trr.iAmbience as ambience, trr.iPrice as price, trr.iFood as food, trr.iService as service,';
                $sql .= ' iRateValue AS reviewRate,';
                $sql .= ' trr.tReviewDetail AS reviewComment, trr.tCreatedAt AS dateTime,';

                $sql .= ' (select TRUNCATE(AVG((trr3.iAmbience + trr3.iPrice + trr3.iFood + trr3.iService ) /8 ), 2)  from tbl_restaurant_review AS trr3 WHERE trr3.iRestaurantID=trr.iRestaurantID) restaurantRating';
//                $sql .= ' TRUNCATE(AVG((trr2.iAmbience + trr2.iPrice + trr2.iFood + trr2.iService ) /8 ), 2) restaurantRating';

                $sql .= ' FROM  `tbl_restaurant_review` AS  `trr`';
                $sql .= ' LEFT JOIN  `tbl_restaurant` AS  `tr` ON  `trr`.`iRestaurantID` =  `tr`.`iRestaurantID`';
//                $sql .= ' LEFT JOIN  tbl_restaurant_review AS  trr2 ON  trr.iRestaurantID =  trr2.iRestaurantID';
                if($idStr)
                    $sql .= ' WHERE trr.iReviewID IN (' . $idStr . ')';
                else
                    $sql .= ' WHERE trr.iReviewID IN (0)';
                if (isset($locId) && !empty($locId)) {
                    $sql .= ' AND tr.iLocationID = ' . $locId;
                }
                
                
                $sql .= 'ORDER BY trr.tCreatedAt DESC,trr.tModifiedAt DESC';
//print_r($sql);exit;
                $reviews = $this->db->query($sql)->result_array();
                return $reviews;
            } return false;
        } catch (Exception $ex) {
            throw new Exception('Error in getExpertReviews function - ' . $ex);
        }
    }

    /*
     * TO CHECKIN USER
     */

    function uesrPublish($postVal) {
        try {
            if (!empty($postVal)) {
                extract($postVal);
                $data = array(
                    'iUserID' => $userId,
                    'iRestaurantID' => $id,
                    'vFriendID' => isset($friendId) ? $friendId : '',
                    'tMessage' => isset($message) ? $message : '',
                    'tCreatedAt' => date('Y-m-d H:i:s')
                );

                $this->db->insert('tbl_user_publish', $data);
                $publishId = $this->db->insert_id();
                return $publishId;
            }
        } catch (Exception $ex) {
            throw new Exception('Error in uesrPublish function - ' . $ex);
        }
    }

    function getExpertAreasCount($userId) {
        try {
            $expertAreaCount = 0;
            if (!empty($userId)) {
                if ($userId) {
                    $tbl = $fields = $conditions = array();

                    $tbl[] = 'tbl_restaurant_review AS trr';
                    $tbl[] = 'tbl_restaurant AS tr';
                    $tbl[] = 'tbl_location AS tl';
                    $fields[] = 'tl.vLocationName as locationName';
                    $fields[] = 'tr.iLocationID as locationId';
                    $fields[] = "IFNULL((SELECT COUNT(*) FROM tbl_restaurant_review_images AS tri WHERE tri.iReviewID IN(trr.iReviewID)),'') AS totalImages";
                    $conditions[] = 'tr.iRestaurantID = trr.iRestaurantID';
                    $conditions[] = 'tr.iLocationID = tl.iLocationID';
                    $conditions[] = 'trr.iUserID IN(' . $userId . ')';
                    $conditions[] = "trr.eStatus IN('active')";
                    $tbl = ' FROM ' . implode(',', $tbl);
                    $fields = implode(',', $fields);
                    $conditions = ' WHERE ' . implode(' AND ', $conditions);
                    $qry = 'SELECT ' . $fields . $tbl . $conditions;

                    $responseArr = $this->db->query($qry)->result_array();
                    $updateArr = $location = array();
                    foreach ($responseArr as $k => $reviewArr) {
                        if (!in_array($reviewArr['locationId'], $location)) {
                            $location[] = $reviewArr['locationId'];
                            $updateArr[$reviewArr['locationId']]['image'] = '';
                            $updateArr[$reviewArr['locationId']]['id'] = $reviewArr['locationId'];
                            $updateArr[$reviewArr['locationId']]['location'] = $reviewArr['locationName'];
                            $updateArr[$reviewArr['locationId']]['images'] = $reviewArr['totalImages'];
                            $updateArr[$reviewArr['locationId']]['review'] = 1;
                        } else {
                            $updateArr[$reviewArr['locationId']]['image'] = '';
                            $updateArr[$reviewArr['locationId']]['id'] = $reviewArr['locationId'];
                            $updateArr[$reviewArr['locationId']]['location'] = $reviewArr['locationName'];
                            $updateArr[$reviewArr['locationId']]['images'] = $updateArr[$reviewArr['locationId']]['images'] + $reviewArr['totalImages'];
                            $updateArr[$reviewArr['locationId']]['review'] = $updateArr[$reviewArr['locationId']]['review'] + 1;
                        }
                    }
                    $returnArr = array();
                    foreach ($updateArr as $arr) {
                        if ($arr['images'] >= 40 && $arr['review'] >= 8) {
                            unset($arr['images'], $arr['review']);
                            $returnArr[] = $arr;
                        }
                    } $expertAreaCount = count($returnArr);
                }
            }
            return $expertAreaCount;
        } catch (Exception $ex) {
            throw new Exception('Error in getExpertAreasCount function - ' . $ex);
        }
    }

    function getExpertAreas($postVal) {
        try {
            if (!empty($postVal)) {
                extract($postVal);
                if ($userId) {
                    $tbl = $fields = $conditions = array();

                    $tbl[] = 'tbl_restaurant_review AS trr';
                    $tbl[] = 'tbl_restaurant AS tr';
                    $tbl[] = 'tbl_location AS tl';
                    $fields[] = 'tl.vLocationName as locationName';
                    $fields[] = 'tr.iLocationID as locationId';
                    $fields[] = "IFNULL((SELECT COUNT(*) FROM tbl_restaurant_review_images AS tri WHERE tri.iReviewID IN(trr.iReviewID)),'') AS totalImages";
                    $conditions[] = 'tr.iRestaurantID = trr.iRestaurantID';
                    $conditions[] = 'tr.iLocationID = tl.iLocationID';
                    $conditions[] = 'trr.iUserID IN(' . $userId . ')';
                    $conditions[] = "trr.eStatus IN('active')";
                    $tbl = ' FROM ' . implode(',', $tbl);
                    $fields = implode(',', $fields);
                    $conditions = ' WHERE ' . implode(' AND ', $conditions);
                    $qry = 'SELECT ' . $fields . $tbl . $conditions;

                    $responseArr = $this->db->query($qry)->result_array();
                    $updateArr = $location = array();
                    foreach ($responseArr as $k => $reviewArr) {
                        if (!in_array($reviewArr['locationId'], $location)) {
                            $location[] = $reviewArr['locationId'];
                            $updateArr[$reviewArr['locationId']]['image'] = '';
                            $updateArr[$reviewArr['locationId']]['id'] = $reviewArr['locationId'];
                            $updateArr[$reviewArr['locationId']]['location'] = $reviewArr['locationName'];
                            $updateArr[$reviewArr['locationId']]['images'] = $reviewArr['totalImages'];
                            $updateArr[$reviewArr['locationId']]['review'] = 1;
                        } else {
                            $updateArr[$reviewArr['locationId']]['image'] = '';
                            $updateArr[$reviewArr['locationId']]['id'] = $reviewArr['locationId'];
                            $updateArr[$reviewArr['locationId']]['location'] = $reviewArr['locationName'];
                            $updateArr[$reviewArr['locationId']]['images'] = $updateArr[$reviewArr['locationId']]['images'] + $reviewArr['totalImages'];
                            $updateArr[$reviewArr['locationId']]['review'] = $updateArr[$reviewArr['locationId']]['review'] + 1;
                        }
                    }
                    $returnArr = array();
                    foreach ($updateArr as $arr) {
                        if ($arr['images'] >= 40 && $arr['review'] >= 8) {
                            unset($arr['images'], $arr['review']);
                            $returnArr[] = $arr;
                        }
                    } return $returnArr;
                } return false;
            }
        } catch (Exception $ex) {
            throw new Exception('Error in expertAreas function - ' . $ex);
        }
    }

    /**
     * 
     * @param type $postValue
     * @return int
     * @throws Exception
     */
    function postReview($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $INS = array(
                    'iUserID' => $userId,
                    'iRestaurantID' => $id,
                    'iAmbience' => isset($ambience) ? $ambience : '',
                    'iPrice' => isset($price) ? $price : '',
                    'iFood' => isset($food) ? $food : '',
                    'iService' => isset($service) ? $service : '',
                    'tReviewDetail' => isset($message) ? $message : '',
                    'vFriendID' => isset($friendId) ? $friendId : '',
                    'tCreatedAt' => date('Y:m:d H:i:s'),
                    'tModifiedAt' => date('Y:m:d H:i:s')
                );

                $this->db->insert('tbl_restaurant_review', $INS);
                $this->db->update('tbl_restaurant', array('iSolrFlag' => '0'), array('iRestaurantID' => $id));

                $sql = "SELECT iReviewID FROM tbl_restaurant_review WHERE iRestaurantID='$id' AND iUserID='$userId' AND eStatus='active' order by iReviewID desc";
                $res = $this->db->query($sql)->row_array();
                //to add user points
                $this->load->model('user_points_model');
                if (!empty($ambience) || !empty($price) || !empty($food) || !empty($service)) {
                    $this->user_points_model->addUserPoints($userId, 3);
                }
                if (!empty($message)) {
                    $this->user_points_model->addUserPoints($userId, 4);
                }
                $data['iReviewID'] = $res['iReviewID'];

                //$sql = "SELECT TRUNCATE(AVG((iAmbience + iPrice + iFood + iService ) /8 ), 2) AS ratings FROM tbl_restaurant_review WHERE iRestaurantID = '$id' AND eStatus = 'active'";
                $sql = "SELECT TRUNCATE(AVG((t1.iAmbience + t1.iPrice + t1.iFood + t1.iService ) /8 ), 2) AS ratings FROM tbl_restaurant_review t1 JOIN (SELECT iUserID, MAX(tCreatedAt) tCreatedAt FROM tbl_restaurant_review where `iRestaurantID` = '$id' GROUP BY iUserID) t2 ON t1.iUserID = t2.iUserID AND t1.tCreatedAt = t2.tCreatedAt and `iRestaurantID` = '$id' AND eStatus = 'active'";
                $countRec = $this->db->query($sql)->row_array();
                $data['rating'] = empty($countRec['ratings']) ? 0 : $countRec['ratings'];
                return $data;
            }
            return false;
        } catch (Exception $ex) {
            throw new Exception('Error in postReview function - ' . $ex);
        }
    }

    public function getUserPublishedPhoto($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $fields[] = 'tp.iPublishID AS publishId';
                $fields[] = 'tp.tMessage AS publishMessage';
                $fields[] = 'DATE_FORMAT(tpi.tModifiedAt,"%b %d, %Y, %h:%i %p") AS publishDateTime';
                $fields[] = 'CONCAT("' . BASEURL . 'images/publish/", IF(tpi.vImageName = \'\', "default.png", CONCAT(tp.iPublishID,\'/\',tpi.vImageName)) ) AS publishImage';
                $fields[] = 'tr.iRestaurantID AS id';
                $fields[] = 'tr.vRestaurantName AS restaurantName';
                $fields[] = 'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.png", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage';

                $tbl[] = 'tbl_user_publish AS tp';
                $tbl[] = 'tbl_user_publish_images AS tpi';
                $tbl[] = 'tbl_restaurant AS tr';

                $condition[] = 'tr.iRestaurantID = tp.iRestaurantID';
                $condition[] = 'tpi.iPublishID = tp.iPublishID';
                $condition[] = 'tp.iUserID =' . $userId;
                $condition[] = 'tr.eStatus IN(\'Active\')';
                $condition[] = 'tp.eStatus IN(\'Active\')';
                $condition[] = 'tpi.eStatus IN(\'Active\')';

                $fields = 'SELECT ' . implode(',', $fields);
                $tbl = ' FROM ' . implode(',', $tbl);
                $orderBy = ' ORDER BY tpi.tModifiedAt desc';
                $condition = ' WHERE ' . implode(' AND ', $condition);

                $qry = $fields . $tbl . $condition . $orderBy;

                $publishedData = $this->db->query($qry)->result_array();
                $reviewData = $this->reviewImages($userId);
                $result = array_merge($publishedData, $reviewData);
                usort($result, function($postA, $postB) {
                    if (strtotime($postA["publishDateTime"]) == strtotime($postB["publishDateTime"])) {
                        return 0;
                    }
                    return (strtotime($postA["publishDateTime"]) < strtotime($postB["publishDateTime"])) ? 1 : -1;
                });

                return $result;
            }return false;
        } catch (Exception $ex) {
            throw new Exception('Error in _getAllLocations function - ' . $ex);
        }
    }

    function reviewImages($userId) {

        try {
            if (!empty($userId)) {
                $fields[] = 'tp.iReviewID AS publishId';
                $fields[] = 'tp.tReviewDetail AS publishMessage';
                $fields[] = 'DATE_FORMAT(tpi.tModifiedAt,"%b %d, %Y, %h:%i %p") AS publishDateTime';
                $fields[] = 'CONCAT("' . BASEURL . 'images/reviewImages/", IF(tpi.iReviewImage = \'\', "default.png", CONCAT(tp.iReviewID,\'/\',tpi.iReviewImage)) ) AS publishImage';
                $fields[] = 'tr.iRestaurantID AS id';
                $fields[] = 'tr.vRestaurantName AS restaurantName';
                $fields[] = 'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.png", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage';

                $tbl[] = 'tbl_restaurant_review AS tp';
                $tbl[] = 'tbl_restaurant_review_images AS tpi';
                $tbl[] = 'tbl_restaurant AS tr';

                $condition[] = 'tr.iRestaurantID = tp.iRestaurantID';
                $condition[] = 'tpi.iReviewID = tp.iReviewID';
                $condition[] = 'tp.iUserID =' . $userId;
                $condition[] = 'tr.eStatus IN(\'Active\')';
                $condition[] = 'tp.eStatus IN(\'active\')';
                $condition[] = 'tpi.eStatus IN(\'Active\')';

                $fields = 'SELECT ' . implode(',', $fields);
                $tbl = ' FROM ' . implode(',', $tbl);
                $orderBy = ' ORDER BY tpi.tModifiedAt desc';
                $condition = ' WHERE ' . implode(' AND ', $condition);

                $qry = $fields . $tbl . $condition . $orderBy;

                return $this->db->query($qry)->result_array();
            }return false;
        } catch (Exception $ex) {
            throw new Exception('Error in _getAllLocations function - ' . $ex);
        }
    }

    function updateDeviceToken($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $this->db->update($this->table, array('vDeviceToken' => $deviceToken), array('iUserID' => $userId));
                return true;
            }return false;
        } catch (Exception $ex) {
            throw new Exception('Error in updateDeviceToken function - ' . $ex);
        }
    }

    function verifyUserReferralCode($userId, $referralCode) {
        $query = "Select iUserID FROM tbl_user WHERE vReferralCode='$referralCode'";
        $res = $this->db->query($query)->row_array();
        if($res['iUserID'] == $userId) {
            return false;
        }
        if (!empty($res["iUserID"])) {
            $query1 = "Select count(iUserID) AS iUserID FROM tbl_user_points WHERE iUserID='$userId' AND iUserPointSystemID='9'";
            $res1 = $this->db->query($query1)->row_array();
            if (empty($res1['iUserID'])) {
                //add user points
                $this->load->model('user_points_model');
                $this->user_points_model->addUserPoints($userId, 9);
                $this->user_points_model->addUserPoints($res["iUserID"], 10);
                return true;
            }
        }
        return false;
    }

    private function _addReferralCode($userId, $userFirstName, $platform = '') {
        $referralCode = strtoupper(substr($userFirstName, 0, 3)) . rand(100, 999);
        if ($userId != '') {
            $this->db->update($this->table, array('vReferralCode' => $referralCode), array('iUserID' => $userId));
            $this->load->model("user_points_model", "user_points_model");
            if ($platform != 'web') {
                $this->user_points_model->addUserPoints($userId, 8);
            }
        }

        return true;
    }

    /**
     * Action to get rating of restaurant
     * @package Restaurant Model
     * @access Public
     * @author 3031@Foodine :
     * @return rating
     */
    private function _getRating($restId) {
        try {
            if ($restId) {
                $qry = 'SELECT TRUNCATE(AVG((t1.iAmbience + t1.iPrice + t1.iFood + t1.iService ) /8 ), 2) as rating FROM tbl_restaurant_review t1 JOIN (SELECT iUserID, MAX(tCreatedAt) tCreatedAt FROM tbl_restaurant_review where `iRestaurantID` = ' . $restId . ' GROUP BY iUserID) t2 ON t1.iUserID = t2.iUserID AND t1.tCreatedAt = t2.tCreatedAt and `iRestaurantID` = ' . $restId . ' AND eStatus = "active"';
                $result = $this->db->query($qry)->row_array();
                if ($result['rating'] == '') {
                    return 0;
                }
                return $result['rating'];
            }return 0;
        } catch (Exception $ex) {
            throw new Exception('Error in _getRating function - ' . $ex);
        }
        return 0;
    }

    /**
     * Action to get rating of restaurant
     * @package Restaurant Model
     * @access Public
     * @author 3031@Foodine :
     * @return rating
     */
    function getCriticRating($restId) {
        try {
            if ($restId) {
                $qry = 'SELECT TRUNCATE(AVG((t1.iAmbience + t1.iPrice + t1.iFood + t1.iService ) /8 ), 2) as rating,t1.iUserID FROM tbl_restaurant_review t1 JOIN (SELECT iUserID, MAX(tCreatedAt) tCreatedAt FROM tbl_restaurant_review where `iRestaurantID` = ' . $restId . ' GROUP BY iUserID) t2 ON t1.iUserID = t2.iUserID AND t1.tCreatedAt = t2.tCreatedAt and `iRestaurantID` = ' . $restId . ' AND eStatus = "active" inner join tbl_user tu on t1.iUserID = tu.iUserID WHERE tu.is_critic = 1';
                $result = $this->db->query($qry)->row_array();
                if ($result['rating'] == '') {
                    return 0;
                }
                return $result['rating'];
            }return 0;
        } catch (Exception $ex) {
            throw new Exception('Error in getCriticRating function - ' . $ex);
        }
        return 0;
    }
    
    function updateUserChoice($userId, $cuisine, $interest, $priceInfo, $sort) {
        try {
            if ($userId !== '' && $cuisine !== '' && $interest !== '' && $priceInfo !== '') {
                $newRes = $this->db->update($this->table, array('selectMinPrice' => $priceInfo['selectedMin'], 'selectMaxPrice' => $priceInfo['selectedMax']), array('iUserID' => $userId));
                $cus_res = $this->_addUserCIMValues($userId, 'cuisine', $cuisine, true);
                $ins_res= $this->_addUserCIMValues($userId, 'interest', $interest, true);
                $ins_res= $this->_addUserCIMValues($userId, 'sort', $sort, true);
//                print_r($newRes);
//                print_r($cus_res);
//                print_r($ins_res); exit;
                if ($this->db->affected_rows() > 0) {
                    return 200;
                } else {
                    return 103;
                }
            } else {
                return 100;
            }
        } catch (Exception $ex) {
            throw new Exception('Error in changePassword function - ' . $ex);
        }
    }

}
