<?php

class Restaurant_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_restaurant';
        $this->load->library('DatatablesHelper');
    }

    // **********************************************************************
    // Display List of Restaurant
    // **********************************************************************
    function getRestaurantDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getRestaurantCheckInDataAll($iRestaurantID) {
        $table = $fields = $qry = '';
        $condition = array();

        $table = 'tbl_user_checkin AS tuc, tbl_user AS tu, tbl_restaurant AS tr';

        $fields .= 'iCheckInID';
        $fields .= ', iCheckInID AS DT_RowId ';
        $fields .= ', DATE_FORMAT(tuc.tCheckInDateTime , "%d %b %Y %h:%i %p") as tCheckInDateTime';
        $fields .= ', vRestaurantName';
        $fields .= ', vFullName';

        $condition[] = 'tuc.iRestaurantID = tr.iRestaurantID';
        $condition[] = 'tuc.iUserID = tu.iUserID';
        $condition[] = 'tuc.iRestaurantID = "' . $iRestaurantID . '"';

        if (!empty($condition)) {
            $condition = " WHERE " . implode(' AND ', $condition);
        } else {
            $condition = NULL;
        }

        $qry .= "SELECT " . $fields . " FROM " . $table . $condition;

        $data = $this->datatableshelper->query($qry);

        return $data;
    }

    function getRestaurantRattingDataAll($iRestaurantID) {
        $table = $fields = $qry = '';
        $condition = array();

        $table = '`tbl_user_ratting` AS tur, `tbl_restaurant` AS r, `tbl_user` AS tu';

        $fields .= 'iRattingReviewID';
        $fields .= ', iRattingReviewID AS DT_RowId ';
        $fields .= ', DATE_FORMAT(tur.tCreatedAt , "%d %b %Y %h:%i %p") as tCreatedAt';
        $fields .= ', iRateValue';
        $fields .= ', vRestaurantName';
        $fields .= ', vFullName';
        $fields .= ', tRateComment';

        $condition[] = 'tur.iRestaurantID = r.iRestaurantID';
        $condition[] = 'tur.iRateValue <> 0';
        $condition[] = 'tu.iUserID = tur.iUserID';
        $condition[] = 'tur.iRestaurantID = "' . $iRestaurantID . '"';

        if (!empty($condition)) {
            $condition = " WHERE " . implode(' AND ', $condition);
        } else {
            $condition = NULL;
        }

        $qry .= "SELECT " . $fields . " FROM " . $table . $condition;

        $data = $this->datatableshelper->query($qry);

        return $data;
    }

    // **********************************************************************
    // Display List of Restaurant
    // **********************************************************************
    function getCategoryDataAll() {
        $this->db->from('tbl_category');
        $this->db->where('eStatus', 'Active');
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    // **********************************************************************
    // Display List of Restaurant
    // **********************************************************************
    function getCuisineDataAll() {
        $this->db->from('tbl_cuisine');
        $this->db->where('eStatus', 'Active');
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    // **********************************************************************
    // Display List of Restaurant
    // **********************************************************************
    function getFacilityDataAll() {
        $this->db->from('tbl_facility');
        $this->db->where('eStatus', 'Active');
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /*
     * DISPLAY LIST OF MUSIC...
     */

    function getMusicDataAll() {
        $this->db->from('tbl_music');
        $this->db->where('eStatus', 'Active');
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /*     * **********************************************************************
      Restaurant Setting(Getting user details by admin_id(Selected or inserted)
     * ********************************************************************** */

    function getRestaurantDataById($iRestaurantID) {
        $result = $this->db->get_where($this->table, array('iRestaurantID' => $iRestaurantID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*
     * Restaurant Setting(Getting user details by admin_id(Selected or inserted)
     */

    function getCategoryDataById($iRestaurantID) {
        $result = $this->db->get_where('tbl_restaurant_category', array('iRestaurantID' => $iRestaurantID));
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getCuisineDataById($iRestaurantID) {
        $result = $this->db->get_where('tbl_restaurant_cuisine', array('iRestaurantID' => $iRestaurantID));
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getFacilityDataById($iRestaurantID) {
        $result = $this->db->get_where('tbl_restaurant_facility', array('iRestaurantID' => $iRestaurantID));
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getMusicDataById($iRestaurantID) {
        $result = $this->db->get_where('tbl_restaurant_music', array('iRestaurantID' => $iRestaurantID));
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    // **********************************************************************
    // Restaurant Status
    // **********************************************************************
    function changeRestaurantStatus($iRestaurantID) {
        //$updateData = array('admin_status' => 'IF (admin_status = "Active", "Inactive","Active")');
        //$query = $this->db->update($this->admin_tbl,$updateData, array('admin_role ' => $admin_role));
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iRestaurantID = $iRestaurantID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeRestaurant($iRestaurantID) {
        $adid = implode(',', $iRestaurantID);
        //mprd($adid);
        $query = $this->db->query("DELETE from tbl_admin WHERE iRestaurantID IN ($adid) ");
        $query = $this->db->query("DELETE from " . $this->table . " WHERE iRestaurantID IN ($adid) ");
        //$query = $this->db->delete($this->table, array('iRestaurantID' => $iRestaurantID));
        //mprd($this->db->last_query());
        if ($this->db->affected_rows() > 0) {
            $this->load->helper("file");
            foreach ($iRestaurantID as $value) {
                // delete_files(UPLOADS."user/".$value."/", TRUE);
                // rmdir(UPLOADS."user/".$value);
            }
            return $query;
        }
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function deleteFollowerAll($iRestaurantID) {
        $adid = implode(',', $iRestaurantID);
        //mprd($adid);
        //$uid = $this->input->post('uid');

        $query = $this->db->query("DELETE from tbl_restaurant_follow WHERE iFollowID in ($adid)");
        if ($this->db->affected_rows() > 0) {
            return $query;
        }
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function deleteFollowingAll($iRestaurantID) {
        $adid = implode(',', $iRestaurantID);
        //mprd($adid);
        //$uid = $this->input->post('uid');


        $query = $this->db->query("DELETE from tbl_restaurant_follow WHERE iFollowID in ($adid)");
        if ($this->db->affected_rows() > 0) {
            return $query;
        }
        else
            return '';
    }

    // **********************************************************************
    // add admin
    // **********************************************************************
    function addRestaurant($postData) {
        extract($postData);
        $vPassword = md5($vPassword);

        $vContactNo = array();
        if (isset($vContactNo1) && $vContactNo1 != '') {
            $vContactNo[] = $vContactNo1;
        }
        if (isset($vContactNo2) && $vContactNo2 != '') {
            $vContactNo[] = $vContactNo2;
        }
        if (isset($vContactNo3) && $vContactNo3 != '') {
            $vContactNo[] = $vContactNo3;
        }
        if (isset($vContactNo4) && $vContactNo4 != '') {
            $vContactNo[] = $vContactNo4;
        }


        if (isset($vDaysOpen) && $vDaysOpen != '') {
            $vDaysOpen = implode(',', $vDaysOpen);
        } else {
            $vDaysOpen = '';
        }

        if (!empty($vContactNo)) {
            $vContactNo = implode(', ', $vContactNo);   
        }else{
            $vContactNo = '';
        }




        $data = array(
            'vRestaurantName' => $vRestaurantName,
            'vEmail' => $vEmail,
            'vEmailSecondary' => $vEmailSecondary,
            'eStatus' => 'Active',
            'vPassword' => $vPassword,
            'vCityName' => $vCityName,
            'vStateName' => $vStateName,
            'vCountryName' => $vCountryName,
            'tAddress' => $tAddress,
            'tSpecialty' => $tSpecialty,
            'vDaysOpen' => $vDaysOpen,
            'iMinTime' => $iMinTime,
            'iMaxTime' => $iMaxTime,
            'iMinPrice' => $iMinPrice,
            'iMaxPrice' => $iMaxPrice,
            'vContactNo' => $vContactNo,
            'tDescription' => $tDescription,
            'vFbLink' => $vFbLink,
            'vInstagramLink' => $vInstagramLink,
            'vMondayTheme' => $vMondayTheme,
            'vThuesdayTheme' => $vThuesdayTheme,
            'vWednesdayTheme' => $vWednesdayTheme,
            'vThursdayTheme' => $vThursdayTheme,
            'vFridayTheme' => $vFridayTheme,
            'vSaturdayTheme' => $vSaturdayTheme,
            'vSundayTheme' => $vSundayTheme,
            'tCreatedAt' => date('Y-m-d H:i:s')
        );
        $query = $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0) {
            $iRestaurantID = $this->db->insert_id();

            /*
             * INSERT RECORD INTO THE ADMIN TABLE...
             */
            $adminRec = array(
                'eAdminType' => 2,
                'iRestaurantID' => $iRestaurantID,
                'vFirstName' => $vRestaurantName,
                'vEmail' => $vEmail,
                'vPassword' => $vPassword
            );
            $this->db->insert('tbl_admin', $adminRec);


            if (isset($iCategoryID)) {
                if (!empty($iCategoryID)) {
                    foreach ($iCategoryID as $key => $value) {
                        $data1 = array('iCategoryID' => $value, 'iRestaurantID' => $iRestaurantID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                        $query = $this->db->insert('tbl_restaurant_category', $data1);
                    }
                }
            }

            if (isset($iCuisineID)) {
                if (!empty($iCuisineID)) {
                    foreach ($iCuisineID as $key => $value) {
                        $data1 = array('iCuisineID' => $value, 'iRestaurantID' => $iRestaurantID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                        $query = $this->db->insert('tbl_restaurant_cuisine', $data1);
                    }
                }
            }

            if (isset($iFacilityID)) {
                if (!empty($iFacilityID)) {
                    foreach ($iFacilityID as $key => $value) {
                        $data1 = array('iFacilityID' => $value, 'iRestaurantID' => $iRestaurantID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                        $query = $this->db->insert('tbl_restaurant_facility', $data1);
                    }
                }
            }

            if (isset($iMusicID)) {
                if (!empty($iMusicID)) {
                    foreach ($iMusicID as $key => $value) {
                        $data1 = array('iMusicID' => $value, 'iRestaurantID' => $iRestaurantID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                        $query = $this->db->insert('tbl_restaurant_music', $data1);
                    }
                }
            }

            return $iRestaurantID;
        }
        else
            return '';
    }

    // **********************************************************************
    // Edit admin
    // **********************************************************************
    function editRestaurant($postData) {

        extract($postData);

        $vContactNo = array();
        if (isset($vContactNo1) && $vContactNo1 != '') {
            $vContactNo[] = $vContactNo1;
        }
        if (isset($vContactNo2) && $vContactNo2 != '') {
            $vContactNo[] = $vContactNo2;
        }
        if (isset($vContactNo3) && $vContactNo3 != '') {
            $vContactNo[] = $vContactNo3;
        }
        if (isset($vContactNo4) && $vContactNo4 != '') {
            $vContactNo[] = $vContactNo4;
        }

        if (isset($vDaysOpen) && $vDaysOpen != '') {
            $vDaysOpen = implode(',', $vDaysOpen);
        } else {
            $vDaysOpen = '';
        }



        if (!empty($vContactNo)) {
            $vContactNo = implode(', ', $vContactNo);   
        }else{
            $vContactNo = '';
        }


       // $vContactNo = implode(', ', $vContactNo);

        //$vDaysOpen = implode(',', $vDaysOpen);

        $data = array(
            'vRestaurantName' => $vRestaurantName,
            'vEmail' => $vEmail,
            'vEmailSecondary' => $vEmailSecondary,
            'eStatus' => 'Active',
            'vCityName' => $vCityName,
            'vStateName' => $vStateName,
            'vCountryName' => $vCountryName,
            'tAddress' => $tAddress,
            'tSpecialty' => $tSpecialty,
            'vDaysOpen' => $vDaysOpen,
            'iMinTime' => $iMinTime,
            'iMaxTime' => $iMaxTime,
            'iMinPrice' => $iMinPrice,
            'iMaxPrice' => $iMaxPrice,
            'vContactNo' => $vContactNo,
            'tDescription' => $tDescription,
            'vFbLink' => $vFbLink,
            'vInstagramLink' => $vInstagramLink,
            'vMondayTheme' => $vMondayTheme,
            'vThuesdayTheme' => $vThuesdayTheme,
            'vWednesdayTheme' => $vWednesdayTheme,
            'vThursdayTheme' => $vThursdayTheme,
            'vFridayTheme' => $vFridayTheme,
            'vSaturdayTheme' => $vSaturdayTheme,
            'vSundayTheme' => $vSundayTheme,
            'vLat' => $vLat,
            'vLog' => $vLog
        );

        $data1 = array();

        if (isset($vPassword) && $vPassword != "") {
            $data['vPassword'] = md5($vPassword);
            $data1['vPassword'] = md5($vPassword);
        }
        if (!empty($data1))
            $query1 = $this->db->update('tbl_admin', $data1, array('iRestaurantID' => $iRestaurantID));

        $query = $this->db->update($this->table, $data, array('iRestaurantID' => $iRestaurantID));

        $aff = $this->db->affected_rows();

        if (isset($iCategoryID)) {
            if (!empty($iCategoryID)) {
                $query = $this->db->query("DELETE from tbl_restaurant_category WHERE iRestaurantID = $iRestaurantID");
                foreach ($iCategoryID as $key => $value) {
                    $data1 = array('iCategoryID' => $value, 'iRestaurantID' => $iRestaurantID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                    $query = $this->db->insert('tbl_restaurant_category', $data1);
                }
            }
        }


        if (isset($iCuisineID)) {
            if (!empty($iCuisineID)) {
                $query = $this->db->query("DELETE from tbl_restaurant_cuisine WHERE iRestaurantID = $iRestaurantID");
                foreach ($iCuisineID as $key => $value) {
                    $data1 = array('iCuisineID' => $value, 'iRestaurantID' => $iRestaurantID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                    $query = $this->db->insert('tbl_restaurant_cuisine', $data1);
                }
            }
        }

        if (isset($iFacilityID)) {
            if (!empty($iFacilityID)) {
                $query = $this->db->query("DELETE from tbl_restaurant_facility WHERE iRestaurantID = $iRestaurantID");
                foreach ($iFacilityID as $key => $value) {
                    $data1 = array('iFacilityID' => $value, 'iRestaurantID' => $iRestaurantID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                    $query = $this->db->insert('tbl_restaurant_facility', $data1);
                }
            }
        }

        if (isset($iMusicID)) {
            if (!empty($iMusicID)) {
                $query = $this->db->query("DELETE from tbl_restaurant_music WHERE iRestaurantID = $iRestaurantID");
                foreach ($iMusicID as $key => $value) {
                    $data1 = array('iMusicID' => $value, 'iRestaurantID' => $iRestaurantID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                    $query = $this->db->insert('tbl_restaurant_music', $data1);
                }
            }
        }

        if ($aff > 0)
            return $query;
        else
            return '';
    }

    function editRestaurantData($data, $iRestaurantID = '') {
        if ($iRestaurantID != '') {
            $query = $this->db->update($this->table, $data, array('iRestaurantID' => $iRestaurantID));
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

    public function checkRestaurantEmailAvailable($vEmail, $iRestaurantID = '') {
        if ($iRestaurantID != "") {
            $result = $this->db->query("SELECT `iRestaurantID`  FROM `tbl_restaurant` WHERE (`vEmail` = '" . $vEmail . "') AND  `iRestaurantID` != $iRestaurantID");
        } else {
            $this->db->where('vEmail', $vEmail);
            //$this->db->or_where('vRestaurantname', $vEmail);
            $result = $this->db->get($this->table);
        }

        if ($result->num_rows() >= 1)
            return 0;
        else
            return 1;
    }

    function get_paginationresult() {
        $iRestaurantID = $this->session->userdata('iRestaurantID');
        $where_condition = '';
        if ($iRestaurantID != 0) {
            $where_condition .= ' WHERE iRestaurantID = "' . $iRestaurantID . '"';
        }

        $data = $this->datatableshelper->query("SELECT vRestaurantName , vEmail, vContactNo , tAddress , vRestaurantLogo,  (SELECT count(*) from tbl_user_checkin as ci where ci.iRestaurantID = r.iRestaurantID ) as total_checkin ,  (SELECT count(*) from tbl_restaurant_image as rp where rp.iRestaurantID = r.iRestaurantID ) as total_picture  , (SELECT count(*) from tbl_restaurant_menu_image as rp1 where rp1.iRestaurantID = r.iRestaurantID ) as total_menu , (SELECT TRUNCATE(AVG(tur.iRateValue), 2) FROM `tbl_user_ratting` AS tur WHERE tur.iRestaurantID = r.iRestaurantID AND tur.iRateValue <> 0 GROUP BY tur.iRestaurantID) AS total_rate, eStatus, vCityName , vStateName , vCountryName , iRestaurantID, iRestaurantID AS DT_RowId FROM tbl_restaurant as r " . $where_condition);
        return $data;
    }

    function unlinkprofileimages($img) {
        unlink("../images/user/original/" . $img);
        unlink("../images/user/thumb/" . $img);
    }

}

?>
