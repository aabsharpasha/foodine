<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of user_model
 * @author OpenXcell Technolabs
 */
class General_Model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /*
     * TO SAVE USER NOTIFICATION
     */

    function saveUserNotification($userId = '', $msg = '', $targetId = 0, $recordId = 0, $type = 'table') {
        try {
            if ($userId != '' && $targetId != 0 && $recordId != 0) {
                $ins = array(
                    'iUserID' => $userId,
                    'iTargetID' => $targetId,
                    'iRecordID' => $recordId,
                    'vNotificationText' => $msg,
                    'tCreatedAt' => date('Y-m-d H:i:s', time())
                );
                $this->db->insert('tbl_user_notification', $ins);

                $this->db->query('UPDATE tbl_user SET iNotifyCount = iNotifyCount + 1 WHERE iUserID IN(' . $userId . ')');
            }
        } catch (Exception $ex) {
            throw new Exception('Error in saveUserNotification function - ' . $ex);
        }
    }

    /*
     * TO GET THE USER RECORD BY ID
     */

    function getUserBasicRecordById($userId = '') {
        try {
            if ($userId !== '') {

                $tbl = 'tbl_user AS tu';

                $condition = array(
                    'tu.iUserID = ' . $userId
                );

                $fields = 'tu.iUserID AS userId';
                $fields .= ', tu.eStatus AS userStatus';
                //$fields .= ', tu.isNotify AS userNotify';
                $fields .= ', tu.vReferralCode AS referralCode';
                //$fields .= ', CONCAT(tu.vFirstName,\' \',tu.vLastName) AS userName';
                //$fields .= ', tu.vFirstName AS userFirstName';
                //$fields .= ', tu.vLastName AS userLastName';
                //$fields .= ', tu.vFullName AS userFullName';
                //$fields .= ', tu.vAbout AS userAbout';
                $fields .= ',tu.dtDOB AS userDOB';
                $fields .= ', tu.dtAnniversary AS userAnniversary';
                $fields .= ', tu.vFirstName AS userFirstName';
                $fields .= ', tu.vLastName AS userLastName';
                $fields .= ', tu.vProfilePicture  AS photoUrl';
                $fields .= ', tu.vEmail AS userEmail';
                //$fields .= ', (IFNULL((SELECT SUM(trp.iRewardPoint) FROM tbl_user_collect AS tuc, tbl_reward_point AS trp WHERE trp.iRewardPointID IN(tuc.iRewardPointID) AND tuc.iUserID IN(tu.iUserID) AND tuc.iUserID IN(' . $userId . ')),0) - IFNULL((SELECT SUM(tr.iRewardPoint) FROM tbl_user_redeem AS tur, tbl_reward AS tr WHERE tr.iRewardID IN(tur.iRewardID) AND tur.iUserID IN(' . $userId . ') ),0) ) AS totalPoint';
                $fields .= ', tu.vMobileNo AS userMobile';
                $fields .= ', tu.eGender AS userGender';
                //$fields .= ', tu.eGivenRate AS userRateGiven';
                //$fields .= ', tu.tModifiedAt AS userModifiedAt';
                //$fields .= ', (SELECT GROUP_CONCAT(tc.iCuisineID) FROM tbl_cuisine AS tc, tbl_user_cuisine AS tuc WHERE tc.iCuisineID IN(tuc.iCuisineID) AND tuc.iUserID IN(' . $userId . ')) AS cuisineName';
                //$fields .= ', (SELECT GROUP_CONCAT(tf.iFacilityID) FROM tbl_facility AS tf, tbl_user_interest AS tui WHERE tf.iFacilityID IN(tui.iInterestID) AND tui.iUserID IN(' . $userId . ')) AS interestName';
                //$fields .= ', (SELECT GROUP_CONCAT(tm.iMusicID) FROM tbl_music AS tm, tbl_user_music AS tum WHERE tm.iMusicID IN(tum.iMusicID) AND tum.iUserID IN(' . $userId . ')) AS musicName';
                //$fields .= ', (SELECT GROUP_CONCAT(tct.iCategoryID) FROM tbl_category AS tct, tbl_user_category AS tuct WHERE tct.iCategoryID IN(tuct.iCategoryID) AND tuct.iUserID IN(' . $userId . ')) AS categoryName';
                //$fields .= ', IFNULL((SELECT SUM(trp.iRewardPoint) FROM tbl_reward_point AS trp, tbl_user_collect AS tup WHERE trp.iRewardPointID = tup.iRewardPointID AND tup.iUserID = tu.iUserID), 0) AS totalPoint';

                $sql = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . implode(' AND ', $condition);

                $res = $this->db->query($sql);
                $rec = $res->row_array();
                
                $this->load->model("user_model", "user_model");
                $this->load->model("general_model", "general_model");
                 
//                $countArrRes = $this->user_model->getFollowersAndFollowing($userId);
//                $rec['followers'] = $countArrRes['followers'];
//                $rec['following'] = $countArrRes['following'];
//
//                $rec['userReviews'] = $this->user_model->getUserReviewsCount($userId);
//                $rec['userRatings'] = $this->user_model->getUserRatingsCount($userId);
//                $totalUserPoint     = $this->user_model->getTotalPoints($userId);
//                $rec['userPoints']  = $totalUserPoint;
//                $levelDetails               = $this->user_model->getUserLevel($totalUserPoint);
//                $rec['userLevel']           = $levelDetails["name"];
//                $rec['userLevelImage']      = $levelDetails["image"];
//                $rec['userLevelPercentage'] = $levelDetails["percentage"];
//                $badges =  $this->user_model->getUserBadges($userId);
//                $rec['badgesCount'] = empty($badges) ? 0 : count($badges);
//                $rec['photosCount'] = $this->user_model->getUserPhotosCount($userId);
//                $rec['expertAreaCount'] = $this->user_model->getExpertAreasCount($userId);
//                $rec['cuisineName'] = $this->getUserChoiceRecord('cuisine', $rec['cuisineName']);
//                $rec['interestName'] = $this->getUserChoiceRecord('interest', $rec['interestName']);
//                $rec['musicName'] = $this->getUserChoiceRecord('music', $rec['musicName']);
//                $rec['categoryName'] = $this->getUserChoiceRecord('category', $rec['categoryName']);

                $query = 'SELECT iUserPointSystemID,iPoints FROM tbl_user_point_system WHERE iUserPointSystemID IN (8,10)';
                $result = $this->db->query($query);
                $result = $result->result_array();
//                foreach($result AS $row){
//                    if($row["iUserPointSystemID"]==8){
//                        $rec['signupPoints']    = $row["iPoints"];
//                    }
//                    if($row["iUserPointSystemID"]==10){
//                        $rec['referralPoints']    = $row["iPoints"];
//                    }
//                }
                
                return $rec;
            } else {
                return '';
            }
        } catch (Exception $ex) {
            throw new Exception('Error in getUserRecordById function - ' . $ex);
        }
    }

    /*
     * TO GET THE USER CHOICE RECORD ARRAY
     */

    private function getUserChoiceRecord($type = 'cuisine', $id) {
        try {
            if ($id != '') {
                switch ($type) {
                    case 'cuisine' :
                        $tbl = 'tbl_cuisine';
                        $fields = 'vCuisineName AS name, iCuisineID AS id';
                        $condition = 'iCuisineID IN(' . $id . ')';
                        break;

                    case 'interest':
                        $tbl = 'tbl_facility';
                        $fields = 'vFacilityName AS name, iFacilityID AS id';
                        $condition = 'iFacilityID IN(' . $id . ')';
                        break;

                    case 'category':
                        $tbl = 'tbl_category';
                        $fields = 'vCategoryName AS name, iCategoryID AS id';
                        $condition = 'iCategoryID IN(' . $id . ')';
                        break;

                    case 'music':
                        $tbl = 'tbl_music';
                        $fields = 'vMusicName AS name, iMusicID AS id';
                        $condition = 'iMusicID IN(' . $id . ')';
                        break;
                }

                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
                $res = $this->db->query($qry);

                return $res->result_array();
            }
            return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getUserChoiceRecord function - ' . $ex);
        }
    }

    /*
     * TO GET THE REDEEEM AMOUNT 
     */

    function getRedeemAmount($userId = 0) {
        try {
            if ($userId > 0) {
                $qry = 'SELECT (SUM(trp.iRewardPoint) - IFNULL((SELECT SUM(tr.iRewardPoint) FROM tbl_user_redeem AS tur, tbl_reward AS tr WHERE tr.iRewardID IN(tur.iRewardID) AND tur.iUserID IN(' . $userId . ') ),0)) AS rewardPoint '
                        . 'FROM tbl_reward_point AS trp, '
                        . 'tbl_user_collect AS tup '
                        . 'WHERE trp.iRewardPointID = tup.iRewardPointID '
                        . 'AND tup.iUserID = "' . $userId . '"';
                //exit;
                $res = $this->db->query($qry);
                $row = $res->row_array();

                return ($row['rewardPoint'] == NULL ? 0 : $row['rewardPoint']);
            }
        } catch (Exception $ex) {
            throw new Exception('Error in getRedeemAmount function - ' . $ex);
        }
    }

    /*
     * TO GET THE REDEEEM AMOUNT WEB
     * tbl_user_point_system is updated points
     */

    function getRedeemAmountWeb($userId = 0) {
        try {
            if ($userId > 0) {
                $qry = 'SELECT (SUM(tups.iPoints) - IFNULL((SELECT SUM(tr.iRewardPoint) FROM tbl_user_redeem AS tur, tbl_reward AS tr WHERE tr.iRewardID IN(tur.iRewardID) AND tur.iUserID IN(' . $userId . ') ),0)) AS rewardPoint '
                        . 'FROM tbl_user_point_system AS tups, '
                        . 'tbl_user_collect AS tup '
                        . 'WHERE tups.iUserPointSystemID = tup.iRewardPointID '
                        . 'AND tup.iUserID = "' . $userId . '"';
                //exit;
                $res = $this->db->query($qry);
                $row = $res->row_array();

                return ($row['rewardPoint'] == NULL ? 0 : $row['rewardPoint']);
            }
        } catch (Exception $ex) {
            throw new Exception('Error in getRedeemAmount function - ' . $ex);
        }
    }

    /*
     * GET VENDOR BASIC INFORMATION
     */

    function getVendorBasicRecordById($restId = 0) {
        try {
            if ($restId != 0) {
                $tbl = $fields = $conditions = array();

                $tbl[] = 'tbl_restaurant AS tr';

                $fields[] = 'tr.iRestaurantID as vendorId';
                $fields[] = 'tr.vRestaurantName as vendorName';
                $fields[] = 'tr.vEmail as vendorEmail';
                $fields[] = 'tr.tAddress as vendorAddress';
                $fields[] = 'IF(tr.vRestaurantLogo = "" , CONCAT("' . IMG_URL . 'restaurant/default.png"), CONCAT("' . IMG_URL . 'restaurant/", tr.iRestaurantID, "/thumb/", tr.vRestaurantLogo))  AS vendorImage';
                $fields[] = '(SELECT COUNT(tb.iTableBookID) FROM tbl_table_book AS tb WHERE tb.iRestaurantID IN(tr.iRestaurantID) AND eBookingStatus IN(\'Pending\')) AS totalBookingRequest';
                $fields[] = '(SELECT COUNT(td.iDealID) FROM tbl_deals AS td WHERE td.iRestaurantID IN(tr.iRestaurantID)) AS totalDeals';
                $fields[] = "(SELECT COUNT(trr.iReviewID) FROM tbl_restaurant_review AS trr WHERE trr.iRestaurantID IN(tr.iRestaurantID) and trr.eStatus = 'active') AS totalReviews";

                $conditions[] = 'tr.iRestaurantID IN(' . $restId . ')';

                $tbl = ' FROM ' . implode(',', $tbl);
                $fields = implode(',', $fields);
                $conditions = ' WHERE ' . implode(' AND ', $conditions);

                $qry = 'SELECT ' . $fields . $tbl . $conditions;

                $res = $this->db->query($qry);

                if ($res->num_rows() > 0) {
                    $row = $res->row_array();
                    foreach ($row as $key => $val) {
                        if ($key == 'vendorId' || $key == 'totalDeals' || $key == 'totalReviews' || $key == 'totalBookingRequest') {
                            $row[$key] = (int) $val;
                        }
                    }
                    return $row;
                } return '';
            } return '';
        } catch (Exception $ex) {
            throw new Exception('General Model : Error in getVendorBasicRecordById function - ' . $ex);
        }
    }

    /*
     * ADD POINTS ENTRY TO THE DATABASE
     *  USER WILL GET POINTS WHILE HE / SHE 
     *      - 1. CHECK-IN
     *      - 2. USE DEAL
     *      - 3. BOOK TABLE
     *      - 4. RATE US
     *      - 5. SHARE ON FB
     *      - 6. INVITE FRIENDS
     */

    function addUserPointValue($userId = '', $pointsFor = 0, $recordId = 0) {
        try {
            if ($userId != '' && $pointsFor != 0) {
                if ($pointsFor == 4) {
                    /*
                     * NEED TO CHECK THAT THE USER HAS ALREADY RATE THE APPLICATION OR NOT
                     */
                    $res = $this->db->get_where('tbl_user_collect', array('iUserID' => $userId, 'iRewardPointID' => $pointsFor));
                    if ($res->num_rows() > 0) {
                        return 0;
                    }
                }
                /*
                 * MAKE A NEW ENTRY FOR THE USER POINT WHICH HE/SHE COLLECTED
                 */
                $this->db->insert('tbl_user_collect', array('iUserID' => $userId, 'iRewardPointID' => $pointsFor, 'iRecordID' => $recordId, 'tCreatedAt' => date('Y-m-d H:i:s')));
                return $this->db->insert_id();
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in addUserPointValue function - ' . $ex);
        }
    }

    /**
     * WEB - Add user point value
     * USER WILL GET POINTS WHILE HE / SHE
     *      - 1. CHECK-IN
     *      - 2. BOOK TABLE
     *      - 3. RATE
     *      - 4. REVIEW
     *      - 5. SHARE
     *      - 6. INVITE FRIENDS
     *      - 7. VERIFIED REGISTRATION
     */
    function addUserPointValueWeb($userId = '', $pointsFor = 0, $recordId = 0) {
        try {
            if ($userId != '' && $pointsFor != 0) {
                if ($pointsFor == 3) {
                    // NEED TO CHECK THAT THE USER HAS ALREADY RATE THE APPLICATION OR NOT
                    $res = $this->db->get_where('tbl_user_collect', array('iUserID' => $userId, 'iUserPointSystemID' => $pointsFor));
                    if ($res->num_rows() > 0) {
                        return 0;
                    }
                }
                // MAKE A NEW ENTRY FOR THE USER POINT WHICH HE/SHE COLLECTED
                $this->db->insert('tbl_user_collect', array('iUserID' => $userId, 'iRewardPointID' => $pointsFor, 'iRecordID' => $recordId, 'tCreatedAt' => date('Y-m-d H:i:s')));
                return $this->db->insert_id();
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in addUserPointValueWeb function - ' . $ex);
        }
    }

    function get_timeago($ptime) {
        $estimate_time = time() - $ptime;

        if ($estimate_time < 1) {
            return 'less than 1 second ago';
        }

        $condition = array(
            12 * 30 * 24 * 60 * 60 => 'year',
            30 * 24 * 60 * 60 => 'month',
            24 * 60 * 60 => 'day',
            60 * 60 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($condition as $secs => $str) {
            $d = $estimate_time / $secs;

            if ($d >= 1) {
                $r = round($d);
                //'about ' 
                return $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
            }
        }
    }

    /*
     * Function to get user level on the basis of points
     * 
     * @return the level data 
     * @author Anurag Srivastava (anurag.srivastava@Foodine :.com)
     */

    function getLevelByPoints($points = 0) {
        try {
            // parse integer in case some other type of data is coming
            $points = (int) $points;
            $qry = "SELECT vLevelName FROM tbl_level WHERE iLevelPoints <= $points order by iLevelID desc limit 1";
            $res = $this->db->query($qry);
            $row = $res->row_array();
            return $row['vLevelName'];
        } catch (Exception $ex) {
            throw new Exception('Error in getLevelByPoints function - ' . $ex->getMessage());
        }
    }
    
    function isReferalUsed($userId) {
        $query1 = "Select count(iUserID) AS iUserID FROM tbl_user_points WHERE iUserID='$userId' AND iUserPointSystemID='9'";
            $res1 = $this->db->query($query1)->row_array();
            if (empty($res1['iUserID'])) {
                return "false";
            }
            return "true";
    }
    
    

}
