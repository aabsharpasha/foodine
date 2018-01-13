<?php

class Restaurant_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_restaurant';
        $this->load->library('DatatablesHelper');
    }

    function getLocations() {
        $res = $this->db->get_where('tbl_location', array('eStatus' => 'Active'));

        return $res->result_array();
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
        $fields .= ', DATE_FORMAT(tuc.tCreatedAt , "%d %b %Y %h:%i %p") as tCheckInDateTime';
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

    function getRestaurantReviewDataAll($iRestaurantID) {
        $table = $fields = $qry = '';
        $condition = array();

        $table = 'tbl_restaurant_review AS trr, tbl_restaurant AS tr, tbl_user AS tu';

        $fields .= 'trr.iReviewID AS iReviewID';
        $fields .= ', trr.iReviewID AS DT_RowId ';
        $fields .= ', DATE_FORMAT(trr.tCreatedAt , "%d %b %Y %h:%i %p") as tCreatedAt';
        $fields .= ', trr.tReviewDetail AS tReviewDetail';
        $fields .= ', tr.vRestaurantName AS vRestaurantName';
        $fields .= ', tu.vFullName AS vFullName';

        $condition[] = 'trr.iRestaurantID IN(tr.iRestaurantID)';
        $condition[] = 'tu.iUserID = trr.iUserID';
        $condition[] = 'trr.iRestaurantID IN(' . $iRestaurantID . ')';

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
        //$this->db->where('eStatus', 'Active');
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
        //$this->db->where('eStatus', 'Active');
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getCollectionDataAll() {
        $this->db->from('tbl_collection');
        //$this->db->where('eStatus', 'Active');
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
        //$this->db->where('eStatus', 'Active');
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
        //$this->db->where('eStatus', 'Active');
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
        if ($result->num_rows() > 0) {
            $row = $result->row_array();
            $res_spec = $this->db->get_where('tbl_restaurant_specialty', array('iRestaurantID' => $iRestaurantID, 'eStatus' => 'active'));
            $specialty = $drinkSpecialty = [];
            $row_spec = $res_spec->result_array();

            // print_r($row_spec);
            foreach ($row_spec AS $k => $v) {
                if ($v['eSpecialtyType'] == 'drink') {
                    $drinkSpecialty[] = $v['vSpecialtyName'];
                } else {
                    $specialty[] = $v['vSpecialtyName'];
                }
            }
            $row['tSpecialty'] = implode(',', $specialty);
            $row['tDrinkSpecialty'] = implode(',', $drinkSpecialty);
            return $row;
        } else {
            return '';
        }
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

    function getMinorCuisineDataById($iRestaurantID) {
        $result = $this->db->get_where('tbl_restaurant_minor_cuisine', array('iRestaurantID' => $iRestaurantID));
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

     function getCollectionDataById($iRestaurantID) {
        $result = $this->db->get_where('tbl_restaurant_collection', array('iRestaurantID' => $iRestaurantID));
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

    function getDealsDataById($iRestaurantID) {
        $result = $this->db->get_where('tbl_restaurant_deals_offers', array('iRestaurantID' => $iRestaurantID));
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getEventDataById($iRestaurantID) {
        $result = $this->db->get_where('tbl_restaurant_event', array('iRestaurantID' => $iRestaurantID));
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
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active'), iSolrFlag = 0 WHERE iRestaurantID = $iRestaurantID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function updateSolrFlag($iRestaurantID) {
        $query = $this->db->query("Update " . $this->table . " SET iSolrFlag = 3 WHERE eStatus = 'Inactive' and iRestaurantID = $iRestaurantID and iSolrFlag <> 4");
        $query = $this->db->query("Update " . $this->table . " SET iSolrFlag = 0 WHERE eStatus = 'Active' and iRestaurantID = $iRestaurantID");
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeRestaurant($iRestaurantID) {
        $adid = implode(',', $iRestaurantID);
        //mprd($adid);
        //$query = $this->db->query("DELETE from " . $this->table . " WHERE iRestaurantID IN ($adid) ");
        $query = $this->db->query("Update " . $this->table . " SET `eStatus` = 'Deleted', `iSolrFlag` = '3' WHERE iRestaurantID IN ($adid) ");
        $query = $this->db->query("DELETE from tbl_admin WHERE iRestaurantID IN ($adid) ");
        //$query = $this->db->delete($this->table, array('iRestaurantID' => $iRestaurantID));
        //mprd($this->db->last_query());
        if ($this->db->affected_rows() > 0) {
            $this->load->helper("file");
            foreach ($iRestaurantID as $value) {
                if (is_dir(UPLOADS . "user/" . $value . "/")) {
                    delete_files(UPLOADS . "user/" . $value . "/", TRUE);
                    rmdir(UPLOADS . "user/" . $value);
                }
            }
            return 1;
        } else {
            return '';
        }
    }

    function removeFeatured($recordId) {
        $adid = implode(',', $recordId);
        $query = $this->db->query("DELETE FROM tbl_featured_restaurant WHERE iFeaturedID IN ($adid)");
        if ($this->db->affected_rows() > 0) {
            return $query;
        } else {
            return '';
        }
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
        } else
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
        } else
            return '';
    }

    function manageSpecialty($iRestaurantID = '', $specialty = '', $drinkSpecialty = '') {
        if ($iRestaurantID != '') {
            $specialtyArr = $specialtyDrinkArr = array();
            $this->db->delete('tbl_restaurant_specialty', array('iRestaurantID' => $iRestaurantID));
            if ($specialty != '') {
                $specialtyArr = explode(',', $specialty);
            }
            if ($drinkSpecialty != '') {
                $specialtyDrinkArr = explode(',', $drinkSpecialty);
            }
            foreach ($specialtyArr AS $v) {
                $v = trim($v);
                if ($v != '') {
                    $this->db->insert('tbl_restaurant_specialty', array('vSpecialtyName' => $v, 'iRestaurantID' => $iRestaurantID, 'eSpecialtyType' => 'food', 'eStatus' => 'active'));
                }
            }

            foreach ($specialtyDrinkArr AS $vd) {
                $vd = trim($vd);
                if ($vd != '') {
                    $this->db->insert('tbl_restaurant_specialty', array('vSpecialtyName' => $vd, 'iRestaurantID' => $iRestaurantID, 'eSpecialtyType' => 'drink', 'eStatus' => 'active'));
                }
            }
        }
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
        } else {
            $vContactNo = '';
        }

        $iMinTime = $iMinTime . '-' . $iMinMin . '-' . $iMinTimeMaradian;
        $iMaxTime = $iMaxTime . '-' . $iMaxMin . '-' . $iMaxTimeMaradian;

        $iMinBookTime = $iMinBookTime != '' && $iMinBookMin != '' ? ($iMinBookTime . '-' . $iMinBookMin . '-' . $iMinBookTimeMaradian) : '';
        $iMaxBookTime = $iMaxBookTime != '' && $iMaxBookMin != '' ? ($iMaxBookTime . '-' . $iMaxBookMin . '-' . $iMaxBookTimeMaradian) : '';

        $iMinBookTime2 = $iMinBookTime2 != '' && $iMinBookMin2 != '' ? ($iMinBookTime2 . '-' . $iMinBookMin2 . '-' . $iMinBookTimeMaradian2) : '';
        $iMaxBookTime2 = $iMaxBookTime2 != '' && $iMaxBookMin2 != '' ? ($iMaxBookTime2 . '-' . $iMaxBookMin2 . '-' . $iMaxBookTimeMaradian2) : '';

        $eFeatured = isset($eFeatured) ? 'yes' : 'no';
        $eAlcohol = isset($eAlcohol) ? $eAlcohol : 'no';
        $tAddress2 = isset($tAddress2) ? $tAddress2 : '';


        //print_r($postData); exit;
        $data = array(
            'vRestaurantName' => $vRestaurantName,
            'vPasscode' => $vPasscode,
            //'vParentCompanyName' => $vParentCompanyName,
            'vEmail' => $vEmail,
            'vEmailSecondary' => $vEmailSecondary,
            /* 'vheadManagerName' => $vheadManagerName,
              'vheadManagerPhone' => $vheadManagerPhone,
              'vheadManagerEmail' => $vheadManagerEmail,
              'vPrimRestManagerName' => $vPrimRestManagerName,
              'vPrimRestManagerPhone' => $vPrimRestManagerPhone,
              'vPrimRestManagerEmail' => $vPrimRestManagerEmail,
              'vSecRestManagerName' => $vSecRestManagerName,
              'vSecRestManagerPhone' => $vSecRestManagerPhone,
              'vSecRestManagerEmail' => $vSecRestManagerEmail,
              'vThirdRestManagerName' => $vThirdRestManagerName,
              'vThirdRestManagerPhone' => $vThirdRestManagerPhone,
              'vThirdRestManagerEmail' => $vThirdRestManagerEmail, */
            'eStatus' => 'Active',
            'vPassword' => $vPassword,
            'vCityName' => $vCityName,
            'vStateName' => $vStateName,
            'vCountryName' => $vCountryName,
            'tAddress' => $tAddress,
            'tAddress2' => $tAddress2,
            'iLocationID' => $iLocationID,
            'tSpecialty' => $tSpecialty,
            'vDaysOpen' => $vDaysOpen,
            'iMinTime' => $iMinTime,
            'iMaxTime' => $iMaxTime,
            'iMinBookTime' => $iMinBookTime,
            'iMaxBookTime' => $iMaxBookTime,
            'iMinBookTime2' => $iMinBookTime2,
            'iMaxBookTime2' => $iMaxBookTime2,
            'iMinPerson' => $min_person,
            'iMaxPerson' => $max_person,
//            'iMinPrice' => $iMinPrice,
//            'iMaxPrice' => $iMaxPrice,
            'iPriceValue' => $iPriceValue,
            //'tCostDescription' => $tCostDescription,
            'eAlcohol' => $eAlcohol,
            'eFeatured' => $eFeatured,
            'allow_book' => (isset($allow_book) ? 'yes' : 'no'),
            'sms_contact' => $sms_contact,
            'vContactNo' => $vContactNo,
            'vTableList' => $vTableList,
//            'mngr1_name' => @$mngr1_name,
//            'mngr2_name' => @$mngr2_name,
//            'mngr3_name' => @$mngr3_name,
//            'mngr1_contact' => @$mngr1_contact,
//            'mngr2_contact' => @$mngr2_contact,
//            'mngr3_contact' => @$mngr3_contact,
//            'mngr1_email' => @$mngr1_email,
//            'mngr2_email' => @$mngr2_email,
//            'mngr3_email' => @$mngr3_email,
            'tDescription' => $tDescription,
            /* 'tWaitStaff' => $tWaitStaff,
              'tNoManagers' => $tNoManagers,
              'tFoodSpeciality' => $tFoodSpeciality,
              'tDrinkSpeciality' => $tDrinkSpeciality, */
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
            'vLog' => $vLog,
            'tCreatedAt' => date('Y-m-d H:i:s'),
            'bookingAvailable' => (isset($allow_book) ? '1' : '0'),
                /* 'serviceTaxApplied' => $serviceTaxApplied,
                  'tableReservationAllowed' => $tableReservationAllowed,
                  'tableAllocatedFreq' => $tableAllocatedFreq,
                  'peoplePerTable' => $peoplePerTable,
                  'bookingDescription' => $bookingDescription,
                  'timeOfReservation' => $timeOfReservation,
                  'iMinPerson' => $iMinPerson,
                  'iMaxPerson' => $iMaxPerson */
        );
        $query = $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0) {
            $iRestaurantID = $this->db->insert_id();

            $this->manageSpecialty($iRestaurantID, $tSpecialty, $tDrinkSpecialty);

            /*
             * INSERT RECORD INTO THE ADMIN TABLE...
             */
            $adminRec = array(
                'iAdminTypeID' => 3,
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

             if (isset($iCollectionID)) {
                if (!empty($iCollectionID)) {
                    foreach ($iCollectionID as $key => $value) {
                        $data1 = array('iCollectionID' => $value, 'iRestaurantID' => $iRestaurantID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                        $query = $this->db->insert('tbl_restaurant_collection', $data1);
                    }
                }
            }

            if (isset($iCuisineIDM)) {
                if (!empty($iCuisineIDM)) {
                    foreach ($iCuisineIDM as $key => $value) {
                        $data1 = array('iCuisineID' => $value, 'iRestaurantID' => $iRestaurantID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                        $query = $this->db->insert('tbl_restaurant_minor_cuisine', $data1);
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

            /*
             * SEND NOTIFICATION TO THE USERS
             */
            $this->load->library('pushnotify');
            /*
             * get type and device toekn from the user table
             */
            $this->db->where('vDeviceToken IS NOT NULL');
            $this->db->where('eStatus IN(\'Active\')');
            $rowUser = $this->db->get('tbl_user')->result_array();
            if (!empty($rowUser)) {
                $msg = 'New Restaurant is going to start now!!';
                for ($i = 0; $i < count($rowUser); $i++) {
                    $platForm = $rowUser[$i]['ePlatform'] == 'ios' ? 2 : 1;
                    $deviceToken = $rowUser[$i]['vDeviceToken'];
                    //$this->pushnotify->sendIt($platForm, $deviceToken, $msg);
                }
            }

            /* if (isset($iDealsID)) {
              if (!empty($iDealsID)) {
              foreach ($iDealsID as $key => $value) {
              //                        $data1 = array('iCuisineID' => $value, 'iRestaurantId' => $iRestaurantID, 'tCreatedAt' => date('Y-m-d H:i:s'));
              //                        $query = $this->db->insert('tbl_restaurant_deals_offers', $data1);
              }
              }
              } */

            return $iRestaurantID;
        } else
            return '';
    }

    // **********************************************************************
    // Edit admin
    // **********************************************************************
    function editRestaurant($postData) {
        //print_r($postData); 
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
        } else {
            $vContactNo = '';
        }

        $iMinTime = $iMinTime . '-' . $iMinMin . '-' . $iMinTimeMaradian;
        $iMaxTime = $iMaxTime . '-' . $iMaxMin . '-' . $iMaxTimeMaradian;

        $iMinBookTime = $iMinBookTime != '' && $iMinBookMin != '' ? ($iMinBookTime . '-' . $iMinBookMin . '-' . $iMinBookTimeMaradian) : '';
        $iMaxBookTime = $iMaxBookTime != '' && $iMaxBookMin != '' ? ($iMaxBookTime . '-' . $iMaxBookMin . '-' . $iMaxBookTimeMaradian) : '';

        $iMinBookTime2 = $iMinBookTime2 != '' && $iMinBookMin2 != '' ? ($iMinBookTime2 . '-' . $iMinBookMin2 . '-' . $iMinBookTimeMaradian2) : '';
        $iMaxBookTime2 = $iMaxBookTime2 != '' && $iMaxBookMin2 != '' ? ($iMaxBookTime2 . '-' . $iMaxBookMin2 . '-' . $iMaxBookTimeMaradian2) : '';

        $eFeatured = isset($eFeatured) ? 'yes' : 'no';
        $eAlcohol = isset($eAlcohol) ? $eAlcohol : 'no';
        $tAddress2 = isset($tAddress2) ? $tAddress2 : '';

        // $vContactNo = implode(', ', $vContactNo);
        //$vDaysOpen = implode(',', $vDaysOpen);




        $data = array(
            'vRestaurantName' => $vRestaurantName,
            'vPasscode' => $vPasscode,
            //'vParentCompanyName' => $vParentCompanyName,
            'vEmail' => $vEmail,
            'vEmailSecondary' => $vEmailSecondary,
            /* 'vheadManagerName' => $vheadManagerName,
              'vheadManagerPhone' => $vheadManagerPhone,
              'vheadManagerEmail' => $vheadManagerEmail, */
              'vPrimRestManagerName' => @$mngr_name,
              'vPrimRestManagerPhone' => @$mngr_contact ? @$mngr_contact : 0,
              'vPrimRestManagerEmail' => @$mngr_email,
              'vSecRestManagerName' => $mngr1_name,
              'vSecRestManagerPhone' => $mngr1_contact ? $mngr1_contact : 0,
              'vSecRestManagerEmail' => $mngr1_email,
              'vThirdRestManagerName' => $mngr2_name,
              'vThirdRestManagerPhone' => $mngr2_contact ? $mngr2_contact : 0,
              'vThirdRestManagerEmail' => $mngr2_email,
            'eStatus' => 'Active',
            'vCityName' => $vCityName,
            'vStateName' => $vStateName,
            'vCountryName' => $vCountryName,
            'tAddress' => $tAddress,
            'tAddress2' => $tAddress2,
            'iLocationID' => $iLocationID,
            //'tSpecialty' => $tSpecialty,
            'vDaysOpen' => $vDaysOpen,
            'iMinTime' => $iMinTime,
            'iMaxTime' => $iMaxTime,
            'iMinBookTime' => $iMinBookTime,
            'iMaxBookTime' => $iMaxBookTime,
            'iMinBookTime2' => $iMinBookTime2,
            'iMaxBookTime2' => $iMaxBookTime2,
            'iMinPerson' => $min_person,
            'iMaxPerson' => $max_person,
         /*  'iMinPrice' => $iMinPrice,
            'iMaxPrice' => $iMaxPrice, */
            'iPriceValue' => $iPriceValue,
            //'tCostDescription' => $tCostDescription,
            'eAlcohol' => $eAlcohol,
            'eFeatured' => $eFeatured,
            'allow_book' => (isset($allow_book) ? 'yes' : 'no'),
            'sms_contact' => $sms_contact,
            'vContactNo' => $vContactNo,
            'vTableList' => $vTableList,
           /* 'mngr1_name' => @$mngr_name,
            'mngr2_name' => @$mngr2_name,
            'mngr3_name' => @$mngr3_name,
            'mngr1_contact' => @$mngr_contact,
            'mngr2_contact' => @$mngr2_contact,
            'mngr3_contact' => @$mngr3_contact,
            'mngr1_email' => @$mngr_email,
            'mngr2_email' => @$mngr2_email,
            'mngr3_email' => @$mngr3_email, */
            'tDescription' => $tDescription,
            'bookingAvailable' => (isset($allow_book) ? '1' : '0'),
            /* 'tWaitStaff' => $tWaitStaff,
              'tNoManagers' => $tNoManagers,
              'tFoodSpeciality' => $tFoodSpeciality,
              'tDrinkSpeciality' => $tDrinkSpeciality, */
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
            'vLog' => $vLog,
            'iSolrFlag' => '0',
                /* 'serviceTaxApplied' => $serviceTaxApplied,
                  'tableReservationAllowed' => $tableReservationAllowed,
                  'tableAllocatedFreq' => $tableAllocatedFreq,
                  'peoplePerTable' => $peoplePerTable,
                  'bookingDescription' => $bookingDescription,
                  'timeOfReservation' => $timeOfReservation,
                  'iMinPerson' => $iMinPerson,
                  'iMaxPerson' => $iMaxPerson */
        );

        $this->manageSpecialty($iRestaurantID, $tSpecialty, $tDrinkSpecialty);

        $data1 = array();

        if (isset($vPassword) && $vPassword != "") {
            $data['vPassword'] = md5($vPassword);
            $data1['vPassword'] = md5($vPassword);
        }

        $query2 = $this->db->update('tbl_admin', array('vEmail' => $vEmail, 'vFirstName' => $vRestaurantName), array('iRestaurantID' => $iRestaurantID));
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

         if (isset($iCollectionID)) {
            if (!empty($iCollectionID)) {
                $query = $this->db->query("DELETE from tbl_restaurant_collection WHERE iRestaurantID = $iRestaurantID");
                foreach ($iCollectionID as $key => $value) {
                    $data1 = array('iCollectionID' => $value, 'iRestaurantID' => $iRestaurantID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                    $query = $this->db->insert('tbl_restaurant_collection', $data1);
                }
            }
        }

        if (isset($iCuisineIDM)) {
            if (!empty($iCuisineIDM)) {
                $query = $this->db->query("DELETE from tbl_restaurant_minor_cuisine WHERE iRestaurantID = $iRestaurantID");
                foreach ($iCuisineIDM as $key => $value) {
                    $data1 = array('iCuisineID' => $value, 'iRestaurantID' => $iRestaurantID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                    $query = $this->db->insert('tbl_restaurant_minor_cuisine', $data1);
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
        if ($where_condition != '') {
            $where_condition .= " AND eStatus <> 'Deleted'";
        } else {
            $where_condition .= " WHERE eStatus <> 'Deleted'";
        }
        $qry = "SELECT "
                . "vRestaurantName, "
                . "vEmail, "
                . "vContactNo, "
                . "tAddress2 AS tAddress, "
                . "vRestaurantLogo, "
                . "(SELECT COUNT(*) FROM tbl_user_checkin AS ci WHERE ci.iRestaurantID IN(r.iRestaurantID) ) AS total_checkin,  "
                . "(SELECT COUNT(*) FROM tbl_restaurant_image AS rp WHERE rp.iRestaurantID IN(r.iRestaurantID) ) AS total_picture, "
                //. "(SELECT COUNT(*) FROM tbl_restaurant_review AS trr WHERE trr.iRestaurantID IN(r.iRestaurantID) ) AS total_review, "
                . "(SELECT COUNT(*) FROM tbl_restaurant_like_dislike AS trld WHERE trld.iRestaurantID IN(r.iRestaurantID) AND trld.eLikeDislikeValue IN('like')) AS total_like, "
                . "(SELECT COUNT(*) FROM tbl_restaurant_like_dislike AS trld WHERE trld.iRestaurantID IN(r.iRestaurantID) AND trld.eLikeDislikeValue IN('dislike')) AS total_dislike, "
                . "(SELECT COUNT(*) FROM tbl_restaurant_menu_image AS rp1 WHERE rp1.iRestaurantID IN(r.iRestaurantID) ) AS total_menu, "
                . "(SELECT TRUNCATE(AVG(tur.iRateValue), 2) FROM `tbl_user_ratting` AS tur WHERE tur.iRestaurantID IN(r.iRestaurantID) AND tur.iRateValue <> 0 GROUP BY tur.iRestaurantID) AS total_rate, "
                . "(SELECT COUNT(tur.iRateValue) FROM `tbl_user_ratting` AS tur WHERE tur.iRestaurantID IN(r.iRestaurantID) GROUP BY tur.iRestaurantID) AS total_review, "
                . "eStatus, "
                . "vCityName, "
                . "vStateName, "
                . "vCountryName, "
                . "iRestaurantID, "
                . "iRestaurantID AS DT_RowId "
                . "FROM tbl_restaurant as r " . $where_condition;

        $data = $this->datatableshelper->query($qry);
        return $data;
    }

    function get_featuredPaginationResult($target = '') {
        $iRestaurantID = $this->session->userdata('iRestaurantID');
        $where_condition = '';
        if ($iRestaurantID != 0) {
            $where_condition .= ' WHERE iRestaurantID = "' . $iRestaurantID . '"';
        }

        $tbl = array(
            'tbl_featured_restaurant AS tfr',
            'tbl_restaurant AS tr',
            'tbl_category AS tc'
        );

        $condition[] = 'tfr.iRestaurantID IN(tr.iRestaurantID)';
        $condition[] = 'tfr.iCategoryID IN(tc.iCategoryID)';
        if ($target != '') {
            $condition[] = 'tfr.iFeaturedID IN(' . $target . ')';
        }

        $fields[] = 'tr.vRestaurantName AS vRestaurantName';
        $fields[] = 'tc.vCategoryName AS vCategoryName';
        $fields[] = 'tfr.iCategoryID AS iCategoryID';
        $fields[] = 'tfr.iRestaurantID AS iRestaurantID';
        $fields[] = 'tfr.iFeaturedID AS iFeaturedID';
        $fields[] = 'tfr.iFeaturedID AS DT_RowId ';

        $tbl = implode(', ', $tbl);
        $fields = implode(', ', $fields);
        $condition = ' WHERE ' . implode(' AND ', $condition);

        $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $condition;

        $data = $this->datatableshelper->query($qry);

        return $data;
    }

    function listCategory() {
        $qry = 'SELECT iCategoryID, vCategoryName FROM tbl_category WHERE eStatus IN(\'Active\')';
        $res = $this->db->query($qry);

        if ($res->num_rows() > 0) {
            return $res->result_array();
        } else {
            return array();
        }
    }

    function listRest() {
        $qry = 'SELECT iRestaurantID, vRestaurantName FROM tbl_restaurant WHERE eStatus IN(\'Active\')';
        $res = $this->db->query($qry);

        if ($res->num_rows() > 0) {
            return $res->result_array();
        } else {
            return array();
        }
    }

    function getRestaurantCategory($rest_id) {
        if ($rest_id != '') {
            $qry = 'SELECT vCategoryName FROM tbl_category AS tc, tbl_restaurant_category AS trc WHERE trc.iCategoryID IN(tc.iCategoryID) AND trc.iRestaurantID IN(' . $rest_id . ')';
            $res = $this->db->query($qry);
            if ($res->num_rows() > 0) {
                $rec = $res->result_array();
                $cat = array();
                for ($i = 0; $i < count($rec); $i++) {
                    $cat[] = $rec[$i]['vCategoryName'];
                } return implode(',', $cat);
            } return '';
        } return '';
    }

    function getRestaurantCuisine($rest_id) {
        if ($rest_id != '') {
            $qry = 'SELECT vCuisineName FROM tbl_cuisine AS tc, tbl_restaurant_cuisine AS trc WHERE trc.iCuisineID IN(tc.iCuisineID) AND trc.iRestaurantID IN(' . $rest_id . ')';
            $res = $this->db->query($qry);
            if ($res->num_rows() > 0) {
                $rec = $res->result_array();
                $cat = array();
                for ($i = 0; $i < count($rec); $i++) {
                    $cat[] = $rec[$i]['vCuisineName'];
                } return implode(',', $cat);
            } return '';
        } return '';
    }

    function getRestaurantMusic($rest_id) {
        if ($rest_id != '') {
            $qry = 'SELECT vMusicName FROM tbl_music AS tc, tbl_restaurant_music AS trc WHERE trc.iMusicID IN(tc.iMusicID) AND trc.iRestaurantID IN(' . $rest_id . ')';
            $res = $this->db->query($qry);
            if ($res->num_rows() > 0) {
                $rec = $res->result_array();
                $cat = array();
                for ($i = 0; $i < count($rec); $i++) {
                    $cat[] = $rec[$i]['vMusicName'];
                } return implode(',', $cat);
            } return '';
        } return '';
    }

    function getRestaurantFacility($rest_id) {
        if ($rest_id != '') {
            $qry = 'SELECT vFacilityName FROM tbl_facility AS tc, tbl_restaurant_facility AS trc WHERE trc.iFacilityID IN(tc.iFacilityID) AND trc.iRestaurantID IN(' . $rest_id . ')';
            $res = $this->db->query($qry);
            if ($res->num_rows() > 0) {
                $rec = $res->result_array();
                $cat = array();
                for ($i = 0; $i < count($rec); $i++) {
                    $cat[] = $rec[$i]['vFacilityName'];
                } return implode(',', $cat);
            } return '';
        } return '';
    }

    function getRestaurantBooking($rest_id) {
        $fields[] = "COUNT(*) AS total";
        $fields[] = "(SELECT COUNT(*) FROM tbl_booking AS tb1 WHERE tb1.eBookingStatus IN('Pending') AND tb1.iRestaurantID IN(tb.iRestaurantID)) AS pending";
        $fields[] = "(SELECT COUNT(*) FROM tbl_booking AS tb2 WHERE tb2.eBookingStatus IN('Accept') AND tb2.iRestaurantID IN(tb.iRestaurantID)) AS accept";
        $fields[] = "(SELECT COUNT(*) FROM tbl_booking AS tb3 WHERE tb3.eBookingStatus IN('Reject') AND tb3.iRestaurantID IN(tb.iRestaurantID)) AS reject";

        $qry = "SELECT " . implode(',', $fields) . " FROM tbl_booking AS tb WHERE tb.iRestaurantID IN(" . $rest_id . ") GROUP BY tb.iRestaurantID";

        $res = $this->db->query($qry);
        if ($res->num_rows() > 0) {
            return $res->row_array();
        } return '';
    }

    function getRestaurantOtherCount($rest_id) {
        $fields[] = "(SELECT COUNT(*) FROM tbl_restaurant_review AS trv WHERE trv.iRestaurantID IN(tr.iRestaurantID)) AS totalReview";
        $fields[] = "(SELECT COUNT(*) FROM tbl_deals AS td WHERE td.iRestaurantID IN(tr.iRestaurantID)) AS totalDeals";
        $fields[] = "(SELECT COUNT(*) FROM tbl_restaurant_like_dislike AS trld WHERE trld.eLikeDislikeValue IN('like') AND trld.iRestaurantID IN(tr.iRestaurantID)) AS totalLike";
        $fields[] = "(SELECT COUNT(*) FROM tbl_restaurant_like_dislike AS trld WHERE trld.eLikeDislikeValue IN('dislike') AND trld.iRestaurantID IN(tr.iRestaurantID)) AS totalDislike";

        $qry = "SELECT " . implode(',', $fields) . " FROM tbl_restaurant AS tr WHERE tr.iRestaurantID IN(" . $rest_id . ") GROUP BY tr.iRestaurantID";

        $res = $this->db->query($qry);
        if ($res->num_rows() > 0) {
            return $res->row_array();
        } return '';
    }

    function unlinkprofileimages($img) {
        unlink("../images/user/original/" . $img);
        unlink("../images/user/thumb/" . $img);
    }

    function getFeaturedRestaurant() {
        $query = 'SELECT tr.`iRestaurantID` AS Id, tr.vRestaurantName, IF(tr.eFeatured=\'yes\',\'yes\',"") AS eFeatured FROM tbl_restaurant AS tr WHERE tr.eStatus = \'Active\' ORDER BY eFeatured DESC, tr.vRestaurantName ASC';
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result_array();
        } else {
            return array();
        }
    }

    function saveFeaturedRestaurant($id, $featured) {
        if ($featured == 'true') {
            $this->db->update('tbl_restaurant', array('eFeatured' => 'yes', 'iSolrFlag' => '0'), array('iRestaurantID' => $id));
            return 1;
        } else {
            $this->db->update('tbl_restaurant', array('eFeatured' => 'no', 'iSolrFlag' => '0'), array('iRestaurantID' => $id));
            return 1;
        }
    }

    function saveFeatured($restaurants) {
        $this->db->query("UPDATE tbl_restaurant SET eFeatured='no', iSolrFlag='0' WHERE eFeatured='yes'");
        foreach ($restaurants AS $restaurant) {
            $this->db->update('tbl_restaurant', array('eFeatured' => 'yes', 'iSolrFlag' => '0'), array('iRestaurantID' => $restaurant));
        }
    }

    function savePickRestaurant($id, $featured) {
        if ($featured == 'true') {
            $this->db->insert('tbl_handpicks', array('iRestaurantID' => $id, 'createdAt' => date('Y-m-d H:i:s')));
            return $this->db->insert_id();
        } else {
            $this->db->delete('tbl_handpicks', array('iRestaurantID' => $id));
            return 0;
        }
    }

    function saveHandpicks($restaurants) {
        $this->db->query('DELETE FROM tbl_handpicks');
        foreach ($restaurants AS $restaurant) {
            $this->db->insert('tbl_handpicks', array('iRestaurantID' => $restaurant, 'createdAt' => date('Y-m-d H:i:s')));
        }
    }

    function getPickRestaurant() {
        $query = 'SELECT tr.`iRestaurantID` AS Id, tr.vRestaurantName , tr.eStatus as status, IFNULL((SELECT th.`id` from tbl_handpicks AS th where th.`iRestaurantID` = tr.`iRestaurantID`),0) AS handpick FROM tbl_restaurant AS tr left join tbl_handpicks AS thr on thr.`iRestaurantID` = tr.`iRestaurantID`where tr.eStatus = \'Active\' ORDER BY handpick DESC, tr.vRestaurantName';
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result_array();
        } else {
            return array();
        }
    }

    function saveBanquet($restaurants) {
        $this->db->query('DELETE FROM tbl_banquet_map');
        foreach ($restaurants AS $restaurant) {
            $this->db->insert('tbl_banquet_map', array('iRestaurantID' => $restaurant, 'createdAt' => date('Y-m-d H:i:s')));
        }
    }

    function getBanquetRestaurant() {
        $query = 'SELECT tr.`iRestaurantID` AS Id, tr.vRestaurantName , tr.eStatus as status, IFNULL((SELECT th.`id` from tbl_banquet_map AS th where th.`iRestaurantID` = tr.`iRestaurantID`),0) AS handpick FROM tbl_restaurant AS tr left join tbl_banquet_map AS thr on thr.`iRestaurantID` = tr.`iRestaurantID`where tr.eStatus = \'Active\' ORDER BY handpick DESC, tr.vRestaurantName';
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result_array();
        } else {
            return array();
        }
    }
    
    
     function savefastfilling($restaurants) {
        $this->db->query('DELETE FROM tbl_fastfilling');
        foreach ($restaurants AS $restaurant) {
            $this->db->insert('tbl_fastfilling', array('iRestaurantID' => $restaurant, 'createdAt' => date('Y-m-d H:i:s')));
        }
    }

    function getfastfillingRestaurant() {
        $query = 'SELECT tr.`iRestaurantID` AS Id, tr.vRestaurantName , tr.eStatus as status, IFNULL((SELECT th.`id` from tbl_fastfilling AS th where th.`iRestaurantID` = tr.`iRestaurantID`),0) AS handpick FROM tbl_restaurant AS tr left join tbl_handpicks AS thr on thr.`iRestaurantID` = tr.`iRestaurantID`where tr.eStatus = \'Active\' ORDER BY handpick DESC, tr.vRestaurantName';
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result_array();
        } else {
            return array();
        }
    }
    
    function savePopularLocation($restaurants) {
        $this->db->query('DELETE FROM tbl_popularlocation');
        foreach ($restaurants AS $restaurant) {
            $this->db->insert('tbl_popularlocation', array('iRestaurantID' => $restaurant, 'createdAt' => date('Y-m-d H:i:s')));
        }
    }

    function getPopularLocationRestaurant() {
        $query = 'SELECT tr.`iRestaurantID` AS Id, tr.vRestaurantName , tr.eStatus as status, IFNULL((SELECT th.`id` from tbl_popularlocation AS th where th.`iRestaurantID` = tr.`iRestaurantID`),0) AS handpick FROM tbl_restaurant AS tr left join tbl_handpicks AS thr on thr.`iRestaurantID` = tr.`iRestaurantID`where tr.eStatus = \'Active\' ORDER BY handpick DESC, tr.vRestaurantName';
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result_array();
        } else {
            return array();
        }
    }

    function paginateReportedByUser() {
        $fields = array(
            "trre.iReportErrorId AS DT_RowId",
            "trre.iReportErrorId AS iReportErrorId",
            "(SELECT ts.vRestaurantName FROM tbl_restaurant AS ts WHERE ts.iRestaurantID=trre.iRestaurantID) AS restaurantName",
            "(SELECT CONCAT(tu.vFirstName, ' ',tu.vLastName) FROM tbl_user AS tu WHERE tu.iUserID=trre.iUserID) AS userName",
            "IF(trre.isWrongPhone=0,'Yes','No') AS isWrongPhone",
            "IF(trre.isWrongAddress=0,'Yes','No') AS isWrongAddress",
            "IF(trre.isWrongOthers=0,'Yes','No') AS isWrongOthers",
            "IF(trre.isWrongMenu=0,'Yes','No') AS isWrongMenu",
            "trre.iRemarks AS iRemarks",
            "trre.is_accepted AS is_accepted",
            "DATE_FORMAT(trre.iCreatedAt ,'%d %b %Y %h:%i %p') AS tCreatedAt",
        );

        $fieldStr = implode(", ", $fields);
        $qry = 'SELECT ' . $fieldStr . ' FROM tbl_restaurant_report_error AS trre ';
        $data = $this->datatableshelper->query($qry);
        return $data;
    }

    function changeReportedByUserStatus($id) {
        $qry = 'UPDATE tbl_restaurant_report_error SET is_accepted = IF(is_accepted = "0", "1", "0" ) WHERE iReportErrorId = "' . $id . '"';
        $this->db->query($qry);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return '';
        }
    }

    function paginateClosureReported() {
        $fields = array(
            "trc.iReportClosureId AS DT_RowId",
            "trc.iReportClosureId AS iReportClosureId",
            "(SELECT ts.vRestaurantName FROM tbl_restaurant AS ts WHERE ts.iRestaurantID=trc.iRestaurantID) AS restaurantName",
            "(SELECT CONCAT(tu.vFirstName, ' ',tu.vLastName) FROM tbl_user AS tu WHERE tu.iUserID=trc.iUserID) AS userName",
            "trc.is_accepted AS is_accepted",
            "DATE_FORMAT(trc.iCreatedAt ,'%d %b %Y %h:%i %p') AS tCreatedAt",
        );

        $fieldStr = implode(", ", $fields);
        $qry = 'SELECT ' . $fieldStr . ' FROM tbl_restaurant_closure AS trc ';
        $data = $this->datatableshelper->query($qry);
        return $data;
    }

    function changeClosureReportedStatus($id) {
        $qry = 'UPDATE tbl_restaurant_closure SET is_accepted = IF(is_accepted = "0", "1", "0" ) WHERE iReportClosureId = "' . $id . '"';
        $this->db->query($qry);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return '';
        }
    }

    function paginateInfoChangeReported() {
        $fields = array(
            "trer.iEditReqID AS DT_RowId",
            "trer.iEditReqID AS iEditReqID",
            "(SELECT ts.vRestaurantName FROM tbl_restaurant AS ts WHERE ts.iRestaurantID=trer.iRestaurantID) AS restaurantName",
            "(SELECT CONCAT(tu.vFirstName, ' ',tu.vLastName) FROM tbl_user AS tu WHERE tu.iUserID=trer.iUserID) AS userName",
            "trer.iRemarks AS iRemarks",
            "trer.is_accepted AS is_accepted",
            "DATE_FORMAT(trer.iCreatedAt ,'%d %b %Y %h:%i %p') AS tCreatedAt",
        );

        $fieldStr = implode(", ", $fields);
        $qry = 'SELECT ' . $fieldStr . ' FROM tbl_restaurant_edit_request AS trer ';
        $data = $this->datatableshelper->query($qry);
        return $data;
    }

    function changeInfoChangeReportedStatus($id) {
        $qry = 'UPDATE tbl_restaurant_edit_request SET is_accepted = IF(is_accepted = "0", "1", "0" ) WHERE iEditReqID = "' . $id . '"';
        $this->db->query($qry);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return '';
        }
    }

    function getStates() {
        $res = $this->db->get_where('tbl_state', array('eStatus' => 'Active'));
        return $res->result_array();
    }

    function getCities($stateId = '') {
        if (isset($stateId) && !empty($stateId)) {
            $query = "SELECT * FROM tbl_location_zone WHERE iStateID = $stateId AND eStatus='Active'";
        } else {
            $query = "SELECT * FROM tbl_location_zone WHERE eStatus='Active'";
        }
        $result = $this->db->query($query);
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getLocation($cityId) {
        $query = "SELECT * FROM tbl_location WHERE iLocZoneID = $cityId AND eStatus='Active'";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }
    
    function getStateCityDataByLocId($locId) {
        $result = $this->db->get_where('tbl_location', array('iLocationID' => $locId));
        $returnArr = array();
        if ($result->num_rows() > 0) {
            $locData = $result->row_array();
            $reslt = $this->db->get_where('tbl_location_zone', array('iLocZoneID' => $locData['iLocZoneID']));
            $returnArr['iLocZoneID'] = $locData['iLocZoneID'];
            if ($reslt->num_rows() > 0) {
                $stateData = $reslt->row_array();
                $returnArr['iStateID'] = $stateData['iStateID'];
                return $returnArr;
            }
            return $returnArr;
        }else{
            return $returnArr;
        }
    }

}

?>
