<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of login_model
 * @author OpenXcell Technolabs
 */
class Login_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_user';
        date_default_timezone_set('Asia/Calcutta');
    }

    /*
     * CHECK WHETHER USERNAME AND PASSWORD ARE CORRECT
     * OR NOT...
     */

    function checkAuthentication($email, $password, $plateForm = '', $deviceToken = '') {
        $res = $this->db->get_where($this->table, array('vEmail' => $email, 'vPassword' => md5($password)));
//print_r($res); exit;
        if ($res->num_rows() > 0) {
            $row = $res->row_array();
            //For old users
           
            /*
             * UPDATE WITH THE PLATEFORM AND DEVICETOKEN
             */
           //$this->db->update($this->table, array('vDeviceToken' => ''), array('vDeviceToken' => $deviceToken));
           if($deviceToken) {
            $this->db->update($this->table, array('ePlatform' => $plateForm, 'vDeviceToken' => $deviceToken), array('iUserID' => $row['iUserID']));
           }

            $data = $this->general_model->getUserBasicRecordById($row['iUserID']);
//print_r($data); exit;
            //$data['hMac'] = genratemac($row['iUserID']);

          
            return $data;
            //}
        } else {

            return '';
        }
    }

    function checkWebAuthentication($email, $password, $plateForm) {
        $res = $this->db->get_where($this->table, array('vEmail' => $email, 'vPassword' => md5($password)));

        if ($res->num_rows() > 0) {
            $row = $res->row_array();
            
             //For old users
            if (empty($row['vReferralCode']) || $row['vReferralCode'] == '') {
                $this->_addReferralCode($row['iUserID'], $row['vFirstName'], 'web');
            }
            
            $this->db->update($this->table, array('ePlatform' => $plateForm), array('iUserID' => $row['iUserID']));

//            if ($row['eStatus'] == 'Inactive') {
//                return array(
//                    'status' => 'Inactive'
//                );
//            } else {
            return array(
                //'status' => 'Active',
                'status' => $row['eStatus'],
                'data' => $this->general_model->getUserBasicRecordById($row['iUserID'])
            );
            // }
        } else {
            return '';
        }
    }

    /*
     * WHEN USER IS LOGGING OUT FROM THE SYSTEM...
     */

    function userLogOut($sessionId = '') {
        try {
            delete_key();
            return 1;
        } catch (Exception $ex) {
            return 0;
        }
    }

    /*
     * CHECK WHETHER USERNAME AND PASSWORD ARE CORRECT
     * OR NOT...
     */

    function checkVendorAuthentication($email, $password, $deviceToken = '', $plateForm = '') {

        $res = $this->db->get_where('tbl_admin', array('vEmail' => $email, 'vPassword' => md5($password), 'eAdminType' => 2));

        if ($res->num_rows() > 0) {
            $row = $res->row_array();

            if ($row['eStatus'] == 'Inactive') {
                return array(
                    'status' => 'Inactive'
                );
            } else {

                /*
                 * UPDATE WITH THE PLATEFORM AND DEVICETOKEN
                 */
                if ($deviceToken != '' && $plateForm != '') {
                    $this->db->update('tbl_admin', array('vDeviceToken' => ""), array('vDeviceToken' => $deviceToken));
                    $this->db->update('tbl_admin', array('ePlatform' => $plateForm, 'vDeviceToken' => $deviceToken), array('iRestaurantID' => $row['iRestaurantID']));
                    //echo $this->db->last_query();
                }

                $data = $this->general_model->getVendorBasicRecordById($row['iRestaurantID']);
                $data['vendorStatus'] = $row['eStatus'];
                $data['hMac'] = genratemac($row['iRestaurantID'], TRUE);

                return $data;
            }
        } else {
            return '';
        }
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

}

?>
