<?php

class User_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_user';
        $this->load->library('DatatablesHelper');
    }

    // **********************************************************************
    // Display List of User
    // **********************************************************************
    function getUserDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getFavUserDataAll() {
        $this->db->from('tbl_user_restaurant_favorite');
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getUserPointDataAll() {
        $this->db->from('tbl_user_restaurant_favorite');
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getCuisineDataAll() {
        //$this->db->from('tbl_cuisine');
        $result = $this->db->get_where('tbl_cuisine', array('eStatus' => 'Active'));
        //$result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getFacilityDataAll() {
        //$this->db->from('tbl_cuisine');
        $result = $this->db->get_where('tbl_facility', array('eStatus' => 'Active'));
        //$result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getMusicDataAll() {
        //$this->db->from('tbl_cuisine');
        $result = $this->db->get_where('tbl_music', array('eStatus' => 'Active'));
        //$result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getCuisineDataById($iUserID) {
        $result = $this->db->get_where('tbl_user_cuisine', array('iUserID' => $iUserID));
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getFacilityDataById($iUserID) {
        $result = $this->db->get_where('tbl_user_interest', array('iUserID' => $iUserID));
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getMusicDataById($iUserID) {
        $result = $this->db->get_where('tbl_user_music', array('iUserID' => $iUserID));
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /*     * **********************************************************************
      User Setting(Getting user details by admin_id(Selected or inserted)
     * ********************************************************************** */

    function getUserDataById($iUserID) {
        $result = $this->db->get_where($this->table, array('iUserID' => $iUserID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    // **********************************************************************
    // User Status
    // **********************************************************************
    function changeUserStatus($iUserID) {
        //$updateData = array('admin_status' => 'IF (admin_status = "Active", "Inactive","Active")');
        //$query = $this->db->update($this->admin_tbl,$updateData, array('admin_role ' => $admin_role));
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iUserID = $iUserID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeUser($iUserID) {
        $adid = implode(',', $iUserID);
        //mprd($adid);

        /*
         * GET ALL RECORD FOR REQUESTED USERS
         */
        $rec = $this->db->query('SELECT * FROM ' . $this->table . ' WHERE iUserID IN(' . $adid . ')')->result_array();

        if (!empty($rec)) {
            foreach ($rec as $key => $val) {
                $subscribeARN = $val['vSubscribeARN'];
                $endPointARN = $val['vEndPointARN'];
                $plateForm = $val['ePlatform'];

                /*
                 * SUBSCRIBE USER TO SNS
                 */
                $DEFAULT_ARR = array(
                    'IS_LIVE' => IS_NOTIFICATION_LIVE,
                    'PLATEFORM_TYPE' => $plateForm
                );

                $this->load->library('sns', $DEFAULT_ARR);
                $this->sns->unSubScribe($subscribeARN);
                $this->sns->deleteEndPointARN($endPointARN);
            }
        }

        $query = $this->db->query("DELETE from $this->table WHERE iUserID in ($adid) ");
        //$query = $this->db->delete($this->table, array('iUserID' => $iUserID));
        //mprd($this->db->last_query());
        if ($this->db->affected_rows() > 0) {
            $this->load->helper("file");
            foreach ($iUserID as $value) {
                if (is_dir(UPLOADS . "user/" . $value)) {
                    delete_files(UPLOADS . "user/" . $value . "/", TRUE);
                    rmdir(UPLOADS . "user/" . $value);
                }
            }
            return $query;
        } else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeUserfavorite($iFavoriteID) {
        $adid = implode(',', $iFavoriteID);
        //mprd($adid);
        $query = $this->db->query("DELETE from tbl_user_restaurant_favorite WHERE iFavoriteID in ($adid) ");
        //$query = $this->db->delete($this->table, array('iUserID' => $iUserID));
        //mprd($this->db->last_query());
        if ($this->db->affected_rows() > 0) {
            return $query;
        } else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function deleteFollowerAll($iUserID) {
        $adid = implode(',', $iUserID);
        //mprd($adid);
        //$uid = $this->input->post('uid');

        $query = $this->db->query("DELETE from tbl_user_follow WHERE iFollowID in ($adid)");
        if ($this->db->affected_rows() > 0) {
            return $query;
        } else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function deleteFollowingAll($iUserID) {
        $adid = implode(',', $iUserID);
        //mprd($adid);
        //$uid = $this->input->post('uid');


        $query = $this->db->query("DELETE from tbl_user_follow WHERE iFollowID in ($adid)");
        if ($this->db->affected_rows() > 0) {
            return $query;
        } else
            return '';
    }

    // **********************************************************************
    // add admin
    // **********************************************************************
    function addUser($postData) {
        extract($postData);
        $vPassword = md5($vPassword);

        $data = array('vFullName' => $vFullName,
            'vEmail' => $vEmail,
            'eStatus' => 'Active',
            'vPassword' => $vPassword,
            'vMobileNo' => $vMobileNo,
            'eGender' => $eGender,
            'tCreatedAt' => date('Y-m-d H:i:s')
        );
        $query = $this->db->insert($this->table, $data);

        if ($this->db->affected_rows() > 0) {
            $iUserID = $this->db->insert_id();

            if (isset($iCuisineID)) {
                if (!empty($iCuisineID)) {
                    foreach ($iCuisineID as $key => $value) {
                        $data1 = array('iCuisineID' => $value, 'iUserID' => $iUserID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                        $query = $this->db->insert('tbl_user_cuisine', $data1);
                    }
                }
            }

            if (isset($iFacilityID)) {
                if (!empty($iFacilityID)) {
                    foreach ($iFacilityID as $key => $value) {
                        $data1 = array('iInterestID' => $value, 'iUserID' => $iUserID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                        $query = $this->db->insert('tbl_user_interest', $data1);
                    }
                }
            }

            if (isset($iMusicID)) {
                if (!empty($iMusicID)) {
                    foreach ($iMusicID as $key => $value) {
                        $data1 = array('iMusicID' => $value, 'iUserID' => $iUserID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                        $query = $this->db->insert('tbl_user_music', $data1);
                    }
                }
            }

            return $iUserID;
        } else
            return '';
    }

    // **********************************************************************
    // Edit admin
    // **********************************************************************
    function editUser($postData) {

        extract($postData);
        $data = array('vFullName' => $vFullName,
            'vEmail' => $vEmail,
            'vMobileNo' => $vMobileNo,
            'eGender' => $eGender
        );
        if (isset($vPassword) && $vPassword != "") {
            $data['vPassword'] = md5($vPassword);
        }

        $query = $this->db->update($this->table, $data, array('iUserID' => $iUserID));

        $aff = $this->db->affected_rows();

        if (isset($iCuisineID)) {
            if (!empty($iCuisineID)) {
                $query = $this->db->query("DELETE from tbl_user_cuisine WHERE iUserID = $iUserID");
                foreach ($iCuisineID as $key => $value) {
                    $data1 = array('iCuisineID' => $value, 'iUserID' => $iUserID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                    $query = $this->db->insert('tbl_user_cuisine', $data1);
                }
            }
        }

        if (isset($iFacilityID)) {
            if (!empty($iFacilityID)) {
                $query = $this->db->query("DELETE from tbl_user_interest WHERE iUserID = $iUserID");
                foreach ($iFacilityID as $key => $value) {
                    $data1 = array('iInterestID' => $value, 'iUserID' => $iUserID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                    $query = $this->db->insert('tbl_user_interest', $data1);
                }
            }
        }

        if (isset($iMusicID)) {
            if (!empty($iMusicID)) {
                $query = $this->db->query("DELETE from tbl_user_music WHERE iUserID = $iUserID");
                foreach ($iMusicID as $key => $value) {
                    $data1 = array('iMusicID' => $value, 'iUserID' => $iUserID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                    $query = $this->db->insert('tbl_user_music', $data1);
                }
            }
        }

        if ($aff > 0)
            return $query;
        else
            return '';
    }

    function editUserData($data, $iUserID = '') {
        if ($iUserID != '') {
            $query = $this->db->update($this->table, $data, array('iUserID' => $iUserID));
            if ($this->db->affected_rows() > 0)
                return $query;
            else
                return '';
        }else {
            return '';
        }
    }

    ############################################################
    #Check EMail#
############################################################

    public function checkUserEmailAvailable($vEmail, $iUserID = '') {
        if ($iUserID != "") {
            $result = $this->db->query("SELECT `iUserID`  FROM `tbl_user` WHERE (`vEmail` = '" . $vEmail . "') AND  `iUserID` != $iUserID");
        } else {
            $this->db->where('vEmail', $vEmail);
            //$this->db->or_where('vUsername', $vEmail);
            $result = $this->db->get($this->table);
        }

        if ($result->num_rows() >= 1)
            return 0;
        else
            return 1;
    }

    ############################################################
    #Check User Name #
    ############################################################

    public function checkUserNameAvailable($vUserName, $iUserID = '') {
        if ($iUserID != "") {
            $result = $this->db->query("SELECT `iUserID`  FROM `tbl_user` WHERE (`vUserName` = '" . $vUserName . "') AND  `iUserID` != $iUserID");
        } else {
            $this->db->where('vUserName', $vUserName);
            //$this->db->or_where('vUsername', $vUserName);
            $result = $this->db->get($this->table);
        }

        if ($result->num_rows() >= 1)
            return 0;
        else
            return 1;
    }

    ############################################################
    #Check Mobile No #
    ############################################################

    public function checkMobileNoAvailable($vMobileNo, $iUserID = '') {

        if ($vMobileNo == '') {
            return 1;
        } else {

            if ($iUserID != "") {
                $result = $this->db->query("SELECT `iUserID`  FROM `tbl_user` WHERE (`vMobileNo` = '" . $vMobileNo . "') AND  `iUserID` != $iUserID");
            } else {
                $this->db->where('vMobileNo', $vMobileNo);
                //$this->db->or_where('vUsername', $vMobileNo);
                $result = $this->db->get($this->table);
            }

            if ($result->num_rows() >= 1)
                return 0;
            else
                return 1;
        }
    }

    function get_paginationresult() {
        $data = $this->datatableshelper->query("SELECT "
                . 'IF( vFullName = "", CONCAT(u.vFirstName, " ", u.vLastName), vFullName) AS vName'
                . ", vEmail"
                . ", vMobileNo"
                . ", vProfilePicture"
                . ", eSubscriptionType, "
                . "tCreatedAt, "
                . "DATE_FORMAT(tCreatedAt, '%m/%d/%Y') as createdAt, "
                . "(SELECT COUNT(*) from tbl_user_restaurant_favorite as uf where uf.iUserID = u.iUserID) as total_favorite , "
                . "(SELECT SUM(trp.iRewardPoint) FROM tbl_user_collect AS tuc, tbl_reward_point AS trp WHERE tuc.iRewardPointId = trp.iRewardPointID AND tuc.iUserID = u.iUserID) AS total_points, "
                . "DATE_FORMAT(tCreatedAt, '%d %b %Y %h:%i %p') as create_time, "
                . "eStatus, ePlatform, iUserID, "
                . "iUserID AS DT_RowId FROM tbl_user u");

        // echo $this->db->last_query(); exit;
        return $data;
    }

    function get_paginate_favorite($iUserID = '') {
        $data = $this->datatableshelper->query("SELECT vRestaurantName , r.vEmail as vEmail , tAddress , vRestaurantLogo ,  (SELECT count(*) from tbl_restaurant_image as rp where rp.iRestaurantID = r.iRestaurantID ) as total_picture  , (SELECT count(*) from tbl_restaurant_menu_image as rp1 where rp1.iRestaurantID = r.iRestaurantID ) as total_menu , r.eStatus as eStatus , vCityName , vStateName , vCountryName , r.iRestaurantID as iRestaurantID , iFavoriteID, iFavoriteID AS DT_RowId FROM tbl_restaurant as r , tbl_user_restaurant_favorite as uf , tbl_user as u where uf.iRestaurantID = r.iRestaurantID and u.iUserID = uf.iUserID and uf.iUserID = $iUserID");
        return $data;
    }

    function get_paginate_user_point($iUserID = '') {
        $data = $this->datatableshelper->query("SELECT iRewardPoint, trp.vRewardWay, tu.vFullName FROM tbl_user_collect AS tuc, tbl_reward_point AS trp, tbl_user AS tu WHERE tuc.iRewardPointId = trp.iRewardPointID AND tu.iUserID = tuc.iUserID AND tuc.iUserID = '" . $iUserID . "'");
        return $data;
    }

    function unlinkprofileimages($img) {
        unlink("../images/user/original/" . $img);
        unlink("../images/user/thumb/" . $img);
    }

}

?>
