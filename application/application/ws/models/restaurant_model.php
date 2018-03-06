<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of user_model
 * @author OpenXcell Technolabs
 */
class Restaurant_Model extends CI_Model {

    var $table;
    var $currHour, $glob;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_restaurant';

        date_default_timezone_set('Asia/Calcutta');
        $this->currHour = time();
        $this->glob = unserialize(RESTAURANT_OPEN_CLOSE_DAYS);
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
            throw new Exception('Error in _getAllLocations function - ' . $ex);
        }
        return 0;
    }

    /**
     * Action to get restaurant by city
     * @package Restaurant Model
     * @access Public
     * @author 3031@Foodine :
     * @return array
     */
    function getRestaurantListCity($city) {
        try {
            $sql = 'SELECT iRestaurantID AS restaurantId, vRestaurantName as restaurantName';
            $sql .= ' FROM `tbl_restaurant` ';
            $sql .= ' WHERE lower(vCityName) LIKE "%' . $city . '%" ';
            $sql .= ' ORDER BY vRestaurantName ';
            return $this->db->query($sql)->result_array();
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantListCity function - ' . $ex);
        }
    }

    /**
     * Fetch all Solr data (restaurants)
     * Using Procedure solrdata()
     *
     * @return restaurants array
     */
    function fetchRestaurantsForSolr() {
        try {
            //$this->db->query('UPDATE `tbl_restaurant` SET `iSolrFlag` =0 WHERE `iSolrFlag` =1');
            $sql = 'CALL solrdata()';

            $qryResult = $this->db->query($sql);
            $result = $qryResult->result_array();
            $qryResult->next_result();

            $restaurantData = [];
            $this->load->model("user_model", "user_model");
            foreach ($result as $data) {
                $minTime = $data['restaurantMinTime'];
                $maxTime = $data['restaurantMaxTime'];
                $timeSlot = $this->user_model->getRestaurantTimeSlot($minTime, $maxTime);
                $data['oct'] = $timeSlot['openCloseTime'];
                $data['ts'] = array($timeSlot['openSlot'], $timeSlot['closeSlot']);

                $restUserImages = explode("(#)", $data['rui']);
                $restMenuImages = explode("(#)", $data['rmi']);
                $restUserImagesPath = $restUserThumbImagesPath = array();
                foreach ($restUserImages as $rui) {
                    //$userImagePath = $userImageThumbPath = BASEURL . 'images/restaurantPhoto/';
                    //$userImagePath = empty($rui) ? '' : $userImagePath . $data['id'] . '/' . $rui;
                    //$userImageThumbPath = empty($rui) ? '' : $userImageThumbPath . $data['id'] . '/thumb/' . $rui;
                    $userImagePath = empty($rui) ? '' : $rui;
                    $userImageThumbPath = empty($rui) ? '' : $rui;
                    array_push($restUserImagesPath, $userImagePath);
                    array_push($restUserThumbImagesPath, $userImageThumbPath);
                }
                $data['rui'] = $restUserImagesPath;
                $data['ruti'] = $restUserThumbImagesPath;

                $restMenuImagesPath = $restMenuThumbImagesPath = array();
                foreach ($restMenuImages as $rui) {
                    //$menuImagePath = $menuImageThumbPath = BASEURL . 'images/restaurantMenu/';
                    //$menuImagePath = empty($rui) ? '' : $menuImagePath . $data['id'] . '/' . $rui;
                    $menuImagePath = empty($rui) ? '' : $rui;
                    $menuImageThumbPath = empty($rui) ? '' : $rui;
                    //$menuImageThumbPath = empty($rui) ? '' : $menuImageThumbPath . $data['id'] . '/thumb/' . $rui;
                    array_push($restMenuImagesPath, $menuImagePath);
                    array_push($restMenuThumbImagesPath, $menuImageThumbPath);
                }

                if ($data['cui'] != '') {
                    $data['cui'] = explode("(#)", $data['cui']);
                }
                if ($data['cuiId'] != '') {
                    $data['cuiId'] = explode("(#)", $data['cuiId']);
                }
                if ($data['cat'] != '') {
                    $data['cat'] = explode("(#)", $data['cat']);
                }
                if ($data['catId'] != '') {
                    $data['catId'] = explode("(#)", $data['catId']);
                }
                if ($data['fcl'] != '') {
                    $data['fcl'] = explode("(#)", $data['fcl']);
                }
                if ($data['fclId'] != '') {
                    $data['fclId'] = explode("(#)", $data['fclId']);
                }
                if ($data['ofr'] != '') {
                    $data['ofr'] = explode("(#)", $data['ofr']);
                }
                if ($data['ofrTypId'] != '') {
                    $data['ofrTypId'] = explode("(#)", $data['ofrTypId']);
                }

                $data['rmi'] = $restMenuImagesPath;
                $data['rmti'] = $restMenuThumbImagesPath;

                preg_match('!\d+!', $data['cf2'], $matches);     // because db stores - Rs. 2000 (inc. Rs.)
                $data['cf2'] = isset($matches[0]) ? $matches[0] : 0;

                unset($data['restaurantMinTime']);
                unset($data['restaurantMaxTime']);
                array_push($restaurantData, $data);
            }

            return $restaurantData;
        } catch (Exception $ex) {

            throw new Exception('Error in fetchRestaurantsForSolr function - ' . $ex);
        }
    }

    /**
     * Action to get deleted restaurant for solr listing
     * @package Restaurant Model
     * @access Public
     * @author 3031@Foodine :
     * @return array
     */
    function fetchDeletedRestaurantsForSolr() {
        $sql = "select tr.iRestaurantID from `tbl_restaurant` AS `tr` where iSolrFlag = 3";
        $result = $this->db->query($sql)->result_array();
        $restIds = array();
        foreach ($result as $results) {
            $restIds[] = $results['iRestaurantID'];
        }
        return $restIds;
    }

    /**
     * Action to set solr flag in db
     * @package Restaurant Model
     * @access Public
     * @author 3031@Foodine :
     * @return true/false
     */
    function setSolrFlag($restId, $solrFlag) {
        $sql = 'UPDATE `tbl_restaurant` SET `iSolrFlag` = ? WHERE `iRestaurantID` = ?';
        $params = array($solrFlag, $restId);

        $result = $this->db->query($sql, $params);
        return $result;
    }

    function getRestaurantInProximity($data) {
        $data['centerLat'] = $data['sourceLatitude'];
        $data['centerLong'] = $data['sourceLongitude'];
        $data['proximity'] = 20;
        try {
            $sql = 'SELECT `tr`.iRestaurantID as restaurantId, `tr`.`vRestaurantName` AS restaurantName, `tr`.vLat as lat, `tr`.vLog as lng, ';
            $sql .= ' 3956 *2 * ASIN( SQRT( POWER( SIN( ( ' . $data['centerLat'] . ' - ABS( vLat ) ) * PI( ) /180 /2 ) , 2 ) ';
            $sql .= ' + COS( ' . $data['centerLat'] . ' * PI( ) /180 ) * COS( ABS( vLat ) * PI( ) /180 )';
            $sql .= ' * POWER( SIN( ( ' . $data['centerLng'] . ' - vLog) * PI( ) /180 /2 ) , 2 ) )) AS distance, ';
            $sql .= ' `tr`.iPriceValue AS cost_for_2, CONCAT(`tl`.vLocationName, ", ", `tr`.vCityName) AS location,';
            $sql .= 'CONCAT("' . BASEURL . 'images/restaurantMobile/", IF(tr.vRestaurantMobLogo = \'\', "defaultdetail.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantMobLogo)) ) AS restaurantImage,';
            $sql .= ' GROUP_CONCAT(DISTINCT `tc`.vCategoryName SEPARATOR ", ") AS category,';
            $sql .= ' GROUP_CONCAT(DISTINCT `tcs`.vCuisineName SEPARATOR ", ") AS cuisine,';
            $sql .= ' GROUP_CONCAT(DISTINCT `tf`.vFacilityName SEPARATOR ", ") AS facility,';
            $sql .= ' COUNT(DISTINCT `td`.iDealID) AS offers,';
            $sql .= ' `tr`.iMinTime AS restaurantMinTime, `tr`.iMaxTime AS restaurantMaxTime, `tr`.tAddress as address, ';
            $sql .= ' AVG(`trr`.iRateValue) AS rating, `tr`.eAlcohol AS alcohol, `tr`.vDaysOpen as openDays, `tr`.tSpecialty as speciality ';
            $sql .= ' FROM `tbl_restaurant` AS `tr`';
            $sql .= ' LEFT JOIN `tbl_location` AS `tl` ON `tl`.`iLocationID` = `tr`.`iLocationID`';
            $sql .= ' LEFT JOIN `tbl_restaurant_category` AS `trc` ON `trc`.`iRestaurantID` = `tr`.`iRestaurantID`';
            $sql .= ' LEFT JOIN `tbl_category` AS `tc` ON `tc`.`iCategoryID` = `trc`.`iCategoryID`';
            $sql .= ' LEFT JOIN `tbl_restaurant_cuisine` AS `trcs` ON `trcs`.`iRestaurantID` = `tr`.`iRestaurantID`';
            $sql .= ' LEFT JOIN `tbl_cuisine` AS `tcs` ON `tcs`.`iCuisineID` = `trcs`.`iCuisineID`';
            $sql .= ' LEFT JOIN `tbl_restaurant_facility` AS `trf` ON `trf`.`iRestaurantID` = `tr`.`iRestaurantID`';
            $sql .= ' LEFT JOIN `tbl_facility` AS `tf` ON `tf`.`iFacilityID` = `trf`.`iFacilityID`';
            $sql .= ' LEFT JOIN `tbl_restaurant_review` AS `trr` ON `trr`.`iRestaurantID` = `tr`.`iRestaurantID`';
            $sql .= ' LEFT JOIN `tbl_deals` AS `td` ON `td`.`iRestaurantID` = `tr`.`iRestaurantID`';
            //$sql .= 'IFNULL((SELECT IF(iFavoriteID = \'null\',\'No\',\'Yes\') FROM tbl_user_restaurant_favorite AS turf WHERE turf.iUserID = \'' . $userId . '\' AND turf.iRestaurantID = \'' . $recordId . '\'),\'No\') AS isFavourite,';
            $sql .= ' WHERE `tr`.eStatus = "Active"';
            $sql .= ' AND `trr`.eStatus = "active"';
            $sql .= ' GROUP BY `tr`.`iRestaurantID`';
           // $sql .= ' HAVING distance < ' . $data['proximity'];

            $params = array($data['centerLat'], $data['centerLat'], $data['centerLng'], $data['proximity']);
            //return $sql;
            //CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage
            $result = $this->db->query($sql)->result_array();
           // print_r($result); exit;
            $restaurantData = [];
            $this->load->model("user_model", "user_model");
            foreach ($result as $restaurantLine) {
                $minTime = $restaurantLine['restaurantMinTime'];
                $maxTime = $restaurantLine['restaurantMaxTime'];
                $timeSlot = $this->user_model->getRestaurantTimeSlot($minTime, $maxTime);
                $data['timing'] = $timeSlot;
                
                //print_r($data); exit;
                $resRet['id'] = $restaurantLine['restaurantId'];
                $resRet['rating'] = $restaurantLine['rating'];
                
                $resRet['distance'] = $restaurantLine['distance'];
                $resRet['address'] = $restaurantLine['address'];
                $resRet['restaurantName'] = $restaurantLine['restaurantName'];
                $resRet['dishName'] = $restaurantLine['speciality'];
                $resRet['photoUrl'] = $restaurantLine['restaurantImage'];
                $deals = $this->_getRestaurantDeals($resRet['id'], '');
                
                $bookmark = $this->_getBookMark($data['userId'], $resRet['id']);
                //print_r($bookmark); exit;
                $resRet['bookmarked'] = $bookmark == 0 ? "false" : "true" ;
                $resRet['offers'] = array();
                foreach($deals as $line) {
                    $resRet['offers'][]= array('description' => $line['offerText'], 'offerCode' => $line['dealCode']);
                }
                  //$resRet['offers'] = $deals;
               // $data = $resRet;
                
                array_push($restaurantData, $resRet);
            }
            
            
            return $restaurantData;
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantInProximity function - ' . $ex);
        }
    }

    /**
     * Get Restaurant listing from Solr
     * @param $data
     * @return array
     * @throws Exception
     */
    function getRestaurantListing($data) {
        try {
            $search = $data['search'];
            $userLat = $data['latitude'];
            $userLong = $data['longitude'];
            
            $distanceType = 'KM';
                $multiplyer = 6371;
                if ($distanceType == 'MILES') {
                    $multiplyer = 3959;
                }
                
            
            $sql = 'SELECT `tr`.iRestaurantID as restaurantId, `tr`.`vRestaurantName` AS restaurantName, `tr`.vLat as lat, `tr`.vLog as lng, ';
            $sql .= ' `tr`.iPriceValue AS cost_for_2, CONCAT(`tl`.vLocationName, ", ", `tr`.vCityName) AS location,';
            $sql .= ' CONCAT("' . BASEURL . 'images/restaurant/", IF(`tr`.vRestaurantLogo = \'\', "default.jpeg", CONCAT(`tr`.iRestaurantID,\'/\',`tr`.vRestaurantLogo)) ) AS restaurantImage, ';
            $sql .= ' GROUP_CONCAT(DISTINCT `tc`.vCategoryName SEPARATOR ", ") AS category,';
            $sql .= ' GROUP_CONCAT(DISTINCT `tcs`.vCuisineName SEPARATOR ", ") AS cuisine,';
            $sql .= ' GROUP_CONCAT(DISTINCT `tf`.vFacilityName SEPARATOR ", ") AS facility,';
            $sql .= ' COUNT(DISTINCT `td`.iDealID) AS offers,';
            $sql .= 'IFNULL((SELECT IF(iFavoriteID = \'null\',\'No\',\'Yes\') FROM tbl_user_restaurant_favorite AS turf WHERE turf.iUserID = \'' . $userId . '\' AND turf.iRestaurantID = \'' . $recordId . '\'),\'No\') AS isFavourite,';
            if ($userLat !== false && $userLong !== false) {
                    $sql .= 'CONCAT(ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                            . ' * cos( radians( vLat) )'
                            . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                            . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) )," ' . $distanceType . '")'
                            . ' AS restaurantDistance,';

                }
            $sql .= ' `tr`.iMinTime AS restaurantMinTime, `tr`.iMaxTime AS restaurantMaxTime, `tr`.tAddress as address, ';
//            $sql .= ' AVG(`trr`.iRateValue) AS rating, `tr`.eAlcohol AS alcohol, `tr`.vDaysOpen as openDays, `tr`.tSpecialty as speciality ';
            $sql .= 'AVG(TRUNCATE((`trr`.iAmbience + `trr`.iPrice + `trr`.iFood + `trr`.iService ) /8, 2 ))  AS rating, ';
            $sql .= ' `tr`.eAlcohol AS alcohol, `tr`.vDaysOpen as openDays, `tr`.tSpecialty as speciality ';
            $sql .= ' FROM `tbl_restaurant` AS `tr`';
            $sql .= ' LEFT JOIN `tbl_location` AS `tl` ON `tl`.`iLocationID` = `tr`.`iLocationID`';
            $sql .= ' LEFT JOIN `tbl_restaurant_category` AS `trc` ON `trc`.`iRestaurantID` = `tr`.`iRestaurantID`';
            $sql .= ' LEFT JOIN `tbl_category` AS `tc` ON `tc`.`iCategoryID` = `trc`.`iCategoryID`';
            $sql .= ' LEFT JOIN `tbl_restaurant_cuisine` AS `trcs` ON `trcs`.`iRestaurantID` = `tr`.`iRestaurantID`';
            $sql .= ' LEFT JOIN `tbl_cuisine` AS `tcs` ON `tcs`.`iCuisineID` = `trcs`.`iCuisineID`';
            $sql .= ' LEFT JOIN `tbl_restaurant_facility` AS `trf` ON `trf`.`iRestaurantID` = `tr`.`iRestaurantID`';
            $sql .= ' LEFT JOIN `tbl_facility` AS `tf` ON `tf`.`iFacilityID` = `trf`.`iFacilityID`';
            $sql .= ' LEFT JOIN `tbl_restaurant_review` AS `trr` ON `trr`.`iRestaurantID` = `tr`.`iRestaurantID`';
            $sql .= ' LEFT JOIN `tbl_deals` AS `td` ON `td`.`iRestaurantID` = `tr`.`iRestaurantID`';
            $sql .= ' WHERE `tr`.eStatus = "Active" AND `tr`.`vRestaurantName` LIKE "%' . $search . '%"';
           // $sql .= ' AND `trr`.eStatus = "active"';
            $sql .= ' GROUP BY `tr`.`iRestaurantID`';
               if($data['debug']) {
               echo $sql; exit;
               }
            //$sql .= ' HAVING distance < ?';
            //$params = array($data['centerLat'], $data['centerLat'], $data['centerLng'], $data['proximity']);
            // add additional params when search filters, sorting is used
            //CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage
            $result = $this->db->query($sql)->result_array();
            $restaurantData = [];
            $this->load->model("user_model", "user_model");
            foreach ($result as $data) {
                $minTime = $data['restaurantMinTime'];
                $maxTime = $data['restaurantMaxTime'];
                $timeSlot = $this->user_model->getRestaurantTimeSlot($minTime, $maxTime);
                $data['timing'] = $timeSlot;
               //print_r($data); exit;
              $resRet['id'] = $data['restaurantId'];
              $resRet['rating'] = $data['rating'];
              $resRet['bookmarked'] = $data['isFavourite'] == 'no' ? "false" : "true" ;
              $resRet['distance'] = $data['restaurantDistance'];
              $resRet['address'] = $data['address'];
              $resRet['restaurantName'] = $data['restaurantName'];
              $resRet['dishName'] = $data['cuisine'];
              $resRet['photoUrl'] = $data['restaurantImage'];
              
              $resRet['offers'] = array();
              $arry['restaurantDeals'] = $this->_getRestaurantDeals($resRet['id'], '');
              foreach($arry['restaurantDeals'] as $line) {
                 // print_r($line);
                  $resRet['offers'][]= array('description' => $line['offerText'], 'offerCode' => $line['dealCode']);
              }
              //exit;
            //  $res_list[] = $resRet;
                
                
                array_push($restaurantData, $resRet);
            }

            return $restaurantData;
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantListing function - ' . $ex);
        }
    }

    private function _getAllLocations() {
        try {
            $fields[] = 'iLocationID AS locationId';
            $fields[] = 'vLocationName AS locationName';

            $tbl[] = 'tbl_location AS tl';

            $condition[] = 'eStatus IN(\'Active\')';

            $fields = 'SELECT ' . implode(',', $fields);
            $tbl = ' FROM ' . implode(',', $tbl);
            $condition = ' WHERE ' . implode(' AND ', $condition);

            $qry = $fields . $tbl . $condition;
            return $this->db->query($qry)->result_array();
        } catch (Exception $ex) {
            throw new Exception('Error in _getAllLocations function - ' . $ex);
        }
    }

    private function _setUserChoiceList($userId, $optionCuisine = '', $optionInterest = '', $optionMusic = '', $optionCategory = '') {
        try {
            $this->db->delete('tbl_user_cuisine', array('iUserID' => $userId));
            if ($optionCuisine != '') {
                $cuisineList = explode(',', $optionCuisine);

                for ($i = 0; $i < count($cuisineList); $i++) {
                    if ($cuisineList[$i] != '') {
                        $this->db->insert('tbl_user_cuisine', array('iUserID' => $userId, 'iCuisineID' => $cuisineList[$i]));
                    }
                }
            }
            $this->db->delete('tbl_user_interest', array('iUserID' => $userId));
            if ($optionInterest != '') {
                $interestList = explode(',', $optionInterest);

                for ($i = 0; $i < count($interestList); $i++) {
                    if ($interestList[$i] != '') {
                        $this->db->insert('tbl_user_interest', array('iUserID' => $userId, 'iInterestID' => $interestList[$i]));
                    }
                }
            }
            $this->db->delete('tbl_user_music', array('iUserID' => $userId));
            if ($optionMusic != '') {
                $musicList = explode(',', $optionMusic);

                for ($i = 0; $i < count($musicList); $i++) {
                    if ($musicList[$i] != '') {
                        $this->db->insert('tbl_user_music', array('iUserID' => $userId, 'iMusicID' => $musicList[$i]));
                    }
                }
            }

            $this->db->delete('tbl_user_category', array('iUserID' => $userId));
            if ($optionCategory != '') {
                $categoryList = explode(',', $optionCategory);
                //mprd($categoryList);
                //exit;
                for ($i = 0; $i < count($categoryList); $i++) {
                    if ($categoryList[$i] != '') {
                        $this->db->insert('tbl_user_category', array('iUserID' => $userId, 'iCategoryID' => $categoryList[$i], 'tCreatedAt' => date('Y-m-d H:i:s')));
                        //echo $this->db->last_query();
                    }
                }
                //exit;
            }
        } catch (Exception $ex) {
            throw new Exception('Resturant Model : Error in _setUserChoiceList function - ' . $ex);
        }
    }

    /**
     * This fetches all the details of a restaurant on the basis of id.
     * 
     * @modified by Garima <garima.chowrasia@Foodine :.com>
     * @modified-date 25-nov-2015
     * @param type $recordId
     * @param type $userLat
     * @param type $userLong
     * @param type $userId
     * @return string
     * @throws Exception
     */
    function getRestaurantDetail($recordId, $userLat = '', $userLong = '', $userId = '', $platform = '') {
       
        try {
            if ($recordId != '') {
                $distanceType = 'KM';
                $multiplyer = 6371;
                if ($distanceType == 'MILES') {
                    $multiplyer = 3959;
                }

                $tbl[] = 'tbl_restaurant AS tr';
                $defaultCostDesc = '"The cost for two is computed as follows: Average of 2 mid ranged Appetizers + 2 Mains + 2 Beverages + 1 Dessert. The actual cost you incur at a restaurant might change depending on your appetite, or with changes in restaurant menu prices."';
                $fields[] = 'tr.iRestaurantID AS restaurantId';
                $fields[] = 'tr.vRestaurantName AS restaurantName';
                $fields[] = ' IF((select id from tbl_banquet_map where iRestaurantID = tr.iRestaurantId),1,0) as isBanquet';
//                if ($platform == 'web') {
//                    $fields[] = 'CONCAT("' . BASEURL . 'images/restaurantListing/", IF(tr.vRestaurantLogo = \'\', "defaultdetail.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage';
//                } else {
//                    $fields[] = 'CONCAT("' . BASEURL . 'images/restaurantMobile/", IF(tr.vRestaurantMobLogo = \'\', "defaultdetail.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantMobLogo)) ) AS restaurantImage';
//                }
                $fields[] = 'CONCAT("' . BASEURL . 'images/restaurantMobile/", IF(tr.vRestaurantMobLogo = \'\', "defaultdetail.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantMobLogo)) ) AS restaurantImage';
                $fields[] = 'tr.vEmail AS restaurantEmail';
                $fields[] = 'tr.vLat AS restaurantLat';
                $fields[] = 'tr.vLog AS restaurantLong';
                $fields[] = 'tr.tSpecialty AS restaurantSpeciality';
                $fields[] = 'IFNULL((SELECT GROUP_CONCAT(trs.vSpecialtyName SEPARATOR ", ") FROM tbl_restaurant_specialty trs WHERE trs.eSpecialtyType IN(\'food\') AND trs.eStatus IN(\'active\') AND trs.iRestaurantID IN(' . $recordId . ')),"") restaurantFoodSpeciality';
                $fields[] = 'IFNULL((SELECT GROUP_CONCAT(trs.vSpecialtyName SEPARATOR ", ") FROM tbl_restaurant_specialty trs WHERE trs.eSpecialtyType IN(\'drink\') AND trs.eStatus IN(\'active\') AND trs.iRestaurantID IN(' . $recordId . ')),"") restaurantDrinkSpeciality';
                $fields[] = 'tr.tDescription AS restaurantDescription';
                //$fields[] = 'tr.tAddress AS restaurantAddress';
                $fields[] = 'tr.tAddress2 AS restaurantAddress';
                $fields[] = 'tr.vCityName AS restaurantCity';
                $fields[] = 'tr.vStateName AS restaurantStateName';
                $fields[] = 'tr.vCountryName AS restaurantCountryName';
                $fields[] = 'tr.vDaysOpen AS openDays';
                $fields[] = 'tr.vContactNo AS restaurantContact';
                $fields[] = 'tr.iMinTime AS restaurantMinTime';
                $fields[] = 'tr.iMaxTime AS restaurantMaxTime';
                $fields[] = 'tr.iMinPerson AS minPerson';
                $fields[] = 'tr.iMaxPerson AS maxPerson';
                $fields[] = 'tr.bookingAvailable AS bookingAvailable';
                $fields[] = 'tr.vMondayTheme AS mondayTheme';
                $fields[] = 'tr.vThuesdayTheme AS tuesdayTheme';
                $fields[] = 'tr.vWednesdayTheme AS wednesdayTheme';
                $fields[] = 'tr.vThursdayTheme AS thursdayTheme';
                $fields[] = 'tr.vFridayTheme AS fridayTheme';
                $fields[] = 'tr.vSaturdayTheme AS saturdayTheme';
                $fields[] = 'tr.vSundayTheme AS sundayTheme';
                $fields[] = 'tr.vPrimRestManagerPhone AS primRestManagerPhone';
                //$fields[] = 'tr.iMinPrice AS restaurantMinPrice';
                //$fields[] = 'tr.iMaxPrice AS restaurantMaxPrice';
                $fields[] = 'IF(tr.iPriceValue != "", tr.iPriceValue, "N/A") AS restaurantPriceValue';
                $fields[] = 'tr.eAlcohol AS restaurantAlcohol';
                $fields[] = 'tr.tDressCodeInfo AS restaurantDressCode';
                $fields[] = "IF(tr.tCostDescription IS NULL or tr.tCostDescription = ''," . $defaultCostDesc . ", tr.tCostDescription) as costDescription";
                $fields[] = 'IFNULL((SELECT COUNT(turf.iUserID) FROM tbl_user_restaurant_favorite turf WHERE turf.iRestaurantID = ' . $recordId . '),"") numberBookmark';
                $fields[] = 'IFNULL((SELECT COUNT(tuc.iUserID) FROM tbl_user_checkin tuc WHERE tuc.iRestaurantID = ' . $recordId . '),"") numberCheckIn';
                $fields[] = 'IFNULL((SELECT TRUNCATE(AVG((t1.iAmbience + t1.iPrice + t1.iFood + t1.iService ) /8 ), 2) FROM tbl_restaurant_review t1 JOIN (SELECT iUserID, MAX(tCreatedAt) tCreatedAt FROM tbl_restaurant_review where `iRestaurantID` = ' . $recordId . ' GROUP BY iUserID) t2 ON t1.iUserID = t2.iUserID AND t1.tCreatedAt = t2.tCreatedAt and `iRestaurantID` = ' . $recordId . ' AND eStatus = "active"),0) userRating';
                // $fields[] = 'IFNULL((SELECT TRUNCATE(AVG((trr.iAmbience + trr.iPrice + trr.iFood + trr.iService ) /8 ), 2) FROM tbl_restaurant_review trr WHERE trr.iRestaurantID = ' . $recordId . ' AND eStatus = "active"),"") userRating1';
                if (isset($userId) && $userId != '') {
                    $fields[] = 'IFNULL((SELECT trr.iAmbience FROM tbl_restaurant_review trr WHERE trr.iRestaurantID = ' . $recordId . ' AND trr.iUserID = ' . $userId . ' AND eStatus = "active" order by tCreatedAt DESC LIMIT 1),"0") ambience';
                    $fields[] = 'IFNULL((SELECT trr.iPrice FROM tbl_restaurant_review trr WHERE trr.iRestaurantID = ' . $recordId . ' AND trr.iUserID = ' . $userId . ' AND eStatus = "active" order by tCreatedAt DESC LIMIT 1),"0") price';
                    $fields[] = 'IFNULL((SELECT trr.iFood FROM tbl_restaurant_review trr WHERE trr.iRestaurantID = ' . $recordId . ' AND trr.iUserID = ' . $userId . ' AND eStatus = "active" order by tCreatedAt DESC LIMIT 1),"0") food';
                    $fields[] = 'IFNULL((SELECT trr.iService FROM tbl_restaurant_review trr WHERE trr.iRestaurantID = ' . $recordId . ' AND trr.iUserID = ' . $userId . ' AND eStatus = "active" order by tCreatedAt DESC LIMIT 1),"0") service';
                } else {
                    $fields[] = '0 AS ambience';
                    $fields[] = '0 AS price';
                    $fields[] = '0 AS food';
                    $fields[] = '0 AS service';
                }
                $fields[] = 'IFNULL((SELECT COUNT(trr.tReviewDetail) FROM tbl_restaurant_review trr WHERE trr.iRestaurantID = ' . $recordId . ' AND trr.tReviewDetail != \'\' AND trr.eStatus = "active"),"") numberReview';
                $fields[] = 'IFNULL((SELECT COUNT(tri.vPictureName) FROM tbl_restaurant_image tri WHERE tri.iRestaurantID = ' . $recordId . '),"") numberImage';
                $fields[] = 'IFNULL((SELECT TRUNCATE(AVG((trr.iAmbience + trr.iPrice + trr.iFood + trr.iService) / 8), 2) FROM tbl_restaurant_review trr inner join tbl_user tu on trr.iUserID = tu.iUserID WHERE tu.is_critic = 1 AND trr.iRestaurantID = ' . $recordId . ' AND trr.eStatus = "active"),"") criticRating';

                if ($userLat !== false && $userLong !== false) {
                    $fields[] = 'CONCAT(ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                            . ' * cos( radians( vLat) )'
                            . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                            . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) )," ' . $distanceType . '")'
                            . ' AS restaurantDistance';
                }
                if ($userId !== false) {
                    $fields[] = 'IFNULL((SELECT eLikeDisLikeValue FROM tbl_restaurant_like_dislike AS trld WHERE trld.iRestaurantID = tr.iRestaurantID AND trld.iUserID = "' . $userId . '"),"") AS likeDislikeValue';
                }

                $fields[] = 'IFNULL((SELECT ROUND(AVG(tur.iRateValue),1) FROM tbl_restaurant_review AS tur WHERE tur.iRestaurantID  = tr.iRestaurantID),0) AS restaurantRatting';
                $fields[] = 'IFNULL((SELECT GROUP_CONCAT(vCuisineName SEPARATOR \', \') FROM tbl_cuisine AS tc, tbl_restaurant_cuisine AS trc WHERE tc.iCuisineID = trc.iCuisineID AND trc.iRestaurantID = tr.iRestaurantID),"") AS totalCuisine';
                $fields[] = 'IFNULL((SELECT GROUP_CONCAT(vCuisineName SEPARATOR \', \') FROM tbl_cuisine AS tc, tbl_restaurant_minor_cuisine AS trmc WHERE tc.iCuisineID = trmc.iCuisineID AND trmc.iRestaurantID = tr.iRestaurantID),"") AS totalCuisineM';
                $fields[] = 'IFNULL((SELECT GROUP_CONCAT(tf.iFacilityID) FROM tbl_facility AS tf, tbl_restaurant_facility AS trf WHERE tf.iFacilityID = trf.iFacilityID AND trf.iRestaurantID = tr.iRestaurantID),"") AS totalFacility';
                if ($userId !== '') {
                    $fields[] = 'IFNULL((SELECT IF(iFavoriteID = \'null\',\'No\',\'Yes\') FROM tbl_user_restaurant_favorite AS turf WHERE turf.iUserID = \'' . $userId . '\' AND turf.iRestaurantID = \'' . $recordId . '\'),\'No\') AS isFavourite';
                }
                $condition[] = 'tr.eStatus = \'Active\'';
                $condition[] = 'tr.iRestaurantID = \'' . $recordId . '\'';

                $tbl = implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
          //      echo $qry; exit;
                $res = $this->db->query($qry);
                $row = $res->row_array();
//                mprd($row);
                if (!empty($row)) {
                    foreach ($row as $key => $val) {
                        if ($key == 'restaurantPriceValue') {
                            if ($val['restaurantPriceValue'] != 'N/A') {
                                $row['restaurantPriceValue'] = 'Rs. ' . $val;
                            }
                        }
                        if (in_array($key, array('totalFacility'))) { //'totalCuisine', 
                            $row[$key] = $val != '' ? explode(',', $val) : array();
                        }
                        if ($key == 'totalCuisineM' && $val != '') {
                            $row['totalCuisine'] .= ', ' . $val;
                        }
                    }
                }
                unset($row['totalCuisineM']);

                if (!empty($row)) {
                    $restAlias = strtolower(preg_replace("/\s+/", "-", trim($row['restaurantName'])));
                    $aliasName = str_replace("'", "", $restAlias) . '-' . $row['restaurantId'];
                    $row['webLink'] = WEBSITEURL . 'listing/detail/' . $aliasName;
                }
                //mprd($row);
                $allFacilities = $this->_getAllFacilities();
                $totalFacility = $row['totalFacility'];
                $facilityArry = array();
                $oldfacilityArry = array();
                if (!empty($allFacilities)) {
                    for ($i = 0; $i < count($allFacilities); $i++) {
                        $value = $allFacilities[$i];
                        $sel = 'no';
                        if (in_array($value['facilityId'], $totalFacility)) {
                            $sel = 'yes';
                            $oldfacilityArry[] = $value['facilityName'];
                        }
                        $facilityArry[$i] = array(
                            'name' => $value['facilityName'],
                            'mobileImage' => $value['mobileImage'],
                            'webImage' => $value['blackImage'],
                            'status' => $sel
                        );
                    }
                }

                //mprd($facilityArry);
                $yesArr = $noArr = array();

                for ($i = 0; $i < count($facilityArry); $i++) {
                    if ($facilityArry[$i]['status'] == 'yes')
                        $yesArr[] = $facilityArry[$i];
                    else
                        $noArr[] = $facilityArry[$i];
                }

                //$row['totalFacilityArry'] = array_merge($yesArr, $noArr);
                $row['totalFacilityArry'] = $yesArr;
                $row['totalFacility'] = $oldfacilityArry;

                if (isset($row['restaurantAlcohol']) && $row['restaurantAlcohol'] == 'yes') {
                    $row['isAlcohol'] = 'With Alcohol';
                } else {
                    $row['isAlcohol'] = 'Without Alcohol';
                }
                unset($row['restaurantAlcohol']);

                if (isset($row['restaurantDressCode']) && $row['restaurantDressCode'] == '2') {
                    $row['dressCode'] = 'Business Attire';
                } else if (isset($row['restaurantDressCode']) && $row['restaurantDressCode'] == '1') {
                    $row['dressCode'] = 'Smart Casual';
                } else if (isset($row['restaurantDressCode']) && $row['restaurantDressCode'] == '0') {
                    $row['dressCode'] = 'Casual';
                }
                unset($row['restaurantDressCode']);
                if (isset($row['userRating'])) {
                    $row['userRating'] = round($row['userRating'], 2);
                }
                $arry['info'] = $row;
                $arry['info']['restaurantCheckIn'] = 'no';
                $arry['info']['restaurantSpeciality'] = array(
                    'food' => $row['restaurantFoodSpeciality'],
                    'drink' => $row['restaurantDrinkSpeciality']
                );
                unset($arry['info']['restaurantFoodSpeciality']);
                unset($arry['info']['restaurantDrinkSpeciality']);
                //$arry['bookingInfo']['allowDates'] = $this->_getMonthsDates();
                //$arry['bookingInfo']['allowSlots'] = array();

                /*
                 * TO CHECK USER IS ALAREADY CHECKIN OR NOT?
                 */
                if ($userId !== '' && $recordId != '') {
                    //echo 'SELECT iCheckInID FROM tbl_user_checkin WHERE iRestaurantID = "' . $recordId . '" AND iUserID = "' . $userId . '" AND tCreatedAt = DATE(NOW())';
                    //exit;
                    $isCheckIn = $this->db->query('SELECT iCheckInID FROM tbl_user_checkin WHERE iRestaurantID = "' . $recordId . '" AND iUserID = "' . $userId . '" AND tCreatedAt = DATE(NOW())');
                    if ($isCheckIn->num_rows() > 0) {
                        $arry['info']['restaurantCheckIn'] = 'yes';
                    }
                }

                $arry['info']['isFavourite'] = 'no';
                if ($userId === '') {
                    $arry['info']['likeDislikeValue'] = '';
                }
                if ($userId !== '') {
                    $isFav = $this->db->get_where('tbl_user_restaurant_favorite', array('iRestaurantID' => $recordId, 'iUserID' => $userId));

                    if ($isFav->num_rows() > 0)
                        $arry['info']['isFavourite'] = 'yes';
                }
                if ($arry['info']['restaurantContact'] !== '') {
                    $contacts = explode(',', $arry['info']['restaurantContact']);
                    $arry['info']['restaurantContact'] = $contacts;
                } else {
                    $arry['info']['restaurantContact'] = array();
                }

                /*
                 * SET MINIMUM TIME / MAXIMUM TIME
                 */
                $arry['info']['openCloseTime'] = '';
                if (isset($row['restaurantMinTime']) && $row['restaurantMinTime'] !== '') {
                    if (isset($row['restaurantMaxTime']) && $row['restaurantMaxTime'] !== '') {
                        $minTime = explode('-', $row['restaurantMinTime']);
                        $maxTime = explode('-', $row['restaurantMaxTime']);

                        if (isset($minTime[0]) && isset($minTime[1]) && isset($minTime[2])) {
                            $minMaradian = $minTime[2] == '1' ? 'AM' : 'PM';
                            $maxMaradian = $maxTime[2] == '1' ? 'AM' : 'PM';

                            $minhr = strlen($minTime[0]) == 1 ? '0' . $minTime[0] : $minTime[0];
                            $maxhr = strlen($maxTime[0]) == 1 ? '0' . $maxTime[0] : $maxTime[0];

                            $minmin = strlen($minTime[1]) == 1 ? '0' . $minTime[1] : $minTime[1];
                            $maxmin = strlen($maxTime[1]) == 1 ? '0' . $maxTime[1] : $maxTime[1];

                            $openCloseTime = $minhr . ':' . $minmin . ' ' . $minMaradian . ' to ' . $maxhr . ':' . $maxmin . ' ' . $maxMaradian;
                            $openCloseTimeValue = $minhr . ':' . $minmin . ':' . $minMaradian . ' to ' . $maxhr . ':' . $maxmin . ':' . $maxMaradian;
                            $arry['info']['openCloseTime'] = $openCloseTime;
                            $arry['info']['openCloseTimeValue'] = $openCloseTimeValue;

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
                            $arry['info']['openSlot'] = $openSlot;
                            $arry['info']['closeSlot'] = $closeSlot;
                            $arry['info']['currentSlot'] = $currentSlot;


                            if ($closeTimeNW == '00:00' || $closeTimeNW == '23:45') {
                                $closeSlot = 96;
                            } else if ($closeTimeNW > '00:00' && $maxMaradian == 'AM') {
                                $currentTimeAMPM = date('A', $serverTime);
                                $closeSlot = $closeSlot + 96;
                            } else {
                                $closeSlot = $closeSlot;
                            }

//                            $totalSlots = array();
//                            if ($openSlot > $closeSlot) {
//                                $tmp1 = range($openSlot, 48);
//                                $tmp2 = range(1, $closeSlot);
//                                $totalSlots = array_merge($tmp1, $tmp2);
//                            } else {
//                                $totalSlots = range($openCloseTime, $closeSlot);
//                            }
//                            
//                            $openSlotTendToSTAMP = strtotime($openSlotTendTo);
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
//                            if (in_array($currentSlot, $totalSlots)) {
//                                $isOpenNow = 'Open';
//                            }
//                            if ($currentSlot == $openSlot) {
//                                if ($openSlotTendToSTAMP > $serverTime) {
//                                    $isOpenNow = 'Open';
//                                } else {
//                                    $isOpenNow = 'Closed';
//                                }
//                            } else if ($openSlot >= $closeSlot) {
//                                $closeTimestamp = strtotime($maxhr . ':' . $maxmin . ' ' . $maxMaradian);
//                                if ($closeSlot < 10 && $maxMaradian == 'AM') {
//                                    $closeTimestamp = strtotime('+1 day' . $maxhr . ':' . $maxmin . ' ' . $maxMaradian);
//                                }
//                                if ($closeTimestamp >= $serverTime && $openSlotTendToSTAMP < $serverTime) {
//                                    $isOpenNow = 'Open';
//                                } else {
//                                    $isOpenNow = 'Closed';
//                                }
//                            } else {
//                                if ($closeTimeNW >= $currentTimeNW /* && $minMaradian != $maxMaradian */) {
//                                    $isOpenNow = 'Open';
//                                } else {
//                                    $isOpenNow = 'Closed';
//                                }
//                            }
                            $arry['info']['isOpenNow'] = $isOpenNow;
                        }
                    }
                }
                unset($row['restaurantMinTime']);
                unset($row['restaurantMaxTime']);
                //$glob = unserialize(RESTAURANT_OPEN_CLOSE_DAYS);
                //mprd($arry['info']);
                if (isset($arry['info']['openDays']) && $arry['info']['openDays'] !== '') {
                    $openDays = explode(',', $arry['info']['openDays']);
                    //mprd($openDays);
                    $days = array();
                    $weekDays = array();
                    for ($i = 0; $i < count($openDays); $i++) {
                        $days[$i]['time'] = $arry['info']['openCloseTime'];
                        $days[$i]['name'] = $this->glob[$openDays[$i]];
                        $weekDays[] = strtolower($this->glob[$openDays[$i]]);
                    }
                    if ($isOpenNow == 'Open' && in_array(strtolower(date("D")), $weekDays)) {
                        $isOpenNow = 'Open';
                    } else {
                        $isOpenNow = 'Closed';
                    }
                    $arry['info']['restaurantOpenDays'] = $days;
                    $arry['info']['isOpenNow'] = $isOpenNow;
                    unset($arry['info']['openDays']);
                }

                /*
                 * SET MINIMUM / MAXIMUM COST
                 */
                //$arry['info']['averageCost'] = $row['restaurantPriceValue'];
                /* if (isset($row['restaurantMinPrice']) && $row['restaurantMinPrice'] !== '') {
                  if (isset($row['restaurantMaxPrice']) && $row['restaurantMaxPrice'] !== '') {
                  $minPrice = (int) $row['restaurantMinPrice'];
                  $maxPrice = (int) $row['restaurantMaxPrice'];

                  $averagePrice = ($minPrice + $maxPrice) / 2;
                  $arry['info']['averageCost'] = $averagePrice;

                  unset($row['restaurantMinPrice']);
                  unset($row['restaurantMaxPrice']);
                  }
                  } */

                $arry['info']['images'] = $this->_getRestaurantImage($recordId, $row['restaurantImage']);
                $arry['info']['category'] = $this->_getRestaurantCategory($recordId);
                $arry['restaurantComments'] = $this->_getRestaurantComments($recordId, $userId);
                $arry['restaurantEvents'] = $this->_getRestaurantEvents($recordId);
                $arry['restaurantMenu'] = $this->_getRestaurantMenu($recordId);
                //print_r($arry['restaurantMenu']); exit;
                //$arry['restaurantDeals'] = $this->_getRestaurantDeals($recordId, $userId);
                $arry['restaurantDeals'] = $this->_getRestaurantDeals($recordId, '');
                //print_r($arry['restaurantDeals']); exit;
                $arry['restaurantCombos'] = $this->_getRestaurantComboOffers($recordId);
                $arry['restaurantOrderMenu'] = $this->_getRestaurantOrderMenu($recordId);
                $arry['currentTime'] = date('h:i:A', time());
                if (isset($userId) && $userId != '') {
                    $this->_updateRecentView($recordId, $userId);
                } else {
                    $this->_updateRecentView($recordId);
                }

                return $arry;
            }

            return '';
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantDetail function - ' . $ex);
        }
    }

    private function _updateRecentView($recordId = '', $userId = '') {
        try {
            if ($userId == '') {
                $userId = 0;
            }
            $res = $this->db->get_where('tbl_user_restaurant_viewed', array('iUserID' => $userId, 'iRestaurantID' => $recordId, 'DATE(tCreatedAt)' => date('Y-m-d')));
            $dataArr = $res->result_array();

            if ($res->num_rows() > 0) {
                $icount = $dataArr[0]['iCount'];
                $updateCount = $icount + 1;
                $this->db->update('tbl_user_restaurant_viewed', array('iCount' => $updateCount, 'tModifiedAt' => date('Y-m-d H:i:s')), array('iUserID' => $userId, 'iRestaurantID' => $recordId, 'DATE(tCreatedAt)' => date('Y-m-d')));
                return 1;
            } else {
                $data = array(
                    'iRestaurantID' => $recordId,
                    'iUserID' => $userId,
                    'iCount' => 1,
                    'tCreatedAt' => date('Y-m-d H:i:s'),
                    'tModifiedAt' => date('Y-m-d H:i:s')
                );
                $this->db->insert('tbl_user_restaurant_viewed', $data);
                return 1;
            } return 0;
        } catch (Exception $ex) {
            throw new Exception('Error in _updateRecentView function - ' . $ex);
        }
    }

    private function _getMonthsDates() {
        try {
            $currDate = time();
            $lastDate = strtotime('+1 month', $currDate);

            $date = array();
            while ($currDate <= $lastDate) {
                $tmp['year'] = (int) date('Y', $currDate);
                $tmp['month'] = (int) date('m', $currDate);
                $tmp['date'] = (int) date('d', $currDate);
                $tmp['day'] = date('D', $currDate);

                $date[] = $tmp;
                $currDate = strtotime('+1 day', $currDate);
            }
            return $date;
        } catch (Exception $ex) {
            throw new Exception('Error in _getMonthsDates function - ' . $ex);
        }
    }

    private function _getAllFacilities() {
        try {
            $fields = 'CONCAT("' . BASEURL . 'images/facilityicon/m/", IF(iconimage = \'\', "default.jpeg", CONCAT(iconimage)) ) AS mobileImage';
            $fields .= ' ,CONCAT("' . BASEURL . 'images/facilityicon/w/", IF(iconimage = \'\', "default.jpeg", CONCAT(iconimage)) ) AS whiteImage';
            $fields .= ' ,CONCAT("' . BASEURL . 'images/facilityicon/b/", IF(iconimage = \'\', "default.jpeg", CONCAT(iconimage)) ) AS blackImage';
            $qry = 'SELECT iFacilityID AS facilityId,' . $fields . ', vFacilityName AS facilityName FROM tbl_facility WHERE eStatus = "Active"';
            $res = $this->db->query($qry);
            return $res->result_array();
        } catch (Exception $ex) {
            throw new Exception('Error in _getAllFacilities function - ' . $ex);
        }
    }

    /**
     * 
     * @param type $recordId
     * @return type
     * @throws Exception
     */
    private function _getRestaurantCategory($recordId) {
        try {
            if ($recordId !== '') {
                $tbl[] = 'tbl_category AS tc';
                $tbl[] = 'tbl_restaurant_category AS trc';

                //$fields[] = 'trc.iCategoryID AS categoryId';
                $fields[] = 'tc.vCategoryName AS categoryName';

                $condition[] = 'tc.eStatus = \'Active\'';
                $condition[] = 'trc.iCategoryID = tc.iCategoryID';
                $condition[] = 'trc.iRestaurantID = "' . $recordId . '"';

                $tbl = implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
                $res = $this->db->query($qry);
                $row = $res->result_array();
                $returnArry = array();
                foreach ($row as $key => $val) {
                    $returnArry[] = $val['categoryName'];
                }

                return implode(', ', $returnArry);
            }

            return array();
        } catch (Exception $ex) {
            throw new Exception('Error in _getRestaurantCategory function - ' . $ex);
        }
    }

    /**
     * This fetches all the restaurant ongoing events from currrent date onwards.
     * 
     * @author Garima <garima.chowrasia@Foodine :.com>
     * @param type $recordId
     * @return type
     * @throws Exception
     */
    private function _getRestaurantEvents($recordId) {
        try {
            if ($recordId !== '') {

                $tbl[] = 'tbl_restaurant_event AS tre';
                $tbl[] = 'tbl_restaurant AS tr';
                $fields[] = 'tre.iEventId AS eventId';
                $fields[] = 'tr.vRestaurantName AS restaurantName';
                $fields[] = 'tr.iRestaurantID AS restaurantId';
                $fields[] = 'IF(tre.iEventImage =\'\',CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ), CONCAT("' . BASEURL . 'images/event/", IF(tre.iEventImage =\'\', "default.jpeg", CONCAT(`tre`.iRestaurantID,\'/thumb/\',tre.iEventImage)))) AS eventImage';
                //$fields[] = 'CONCAT("' . BASEURL . 'images/event/", IF(tre.iEventImage =\'\', "default.jpeg", CONCAT(`tre`.iRestaurantID,\'/\',`tre`.iEventImage))) AS eventImage';
                $fields[] = 'tre.iEventTitle AS eventTitle';
                $fields[] = 'tre.iEventDescription AS eventDescription';
                //$fields[] = 'DATE_FORMAT(tre.iDateofEvent,"' . '%d %b' . '") AS dateofEvent';
                $fields[] = 'DATE_FORMAT(tre.dEventStartDate,"' . '%d %b' . '") AS startDateofEvent1';
                $fields[] = 'DATE_FORMAT(tre.dEventEndDate,"' . '%d %b' . '") AS endDateofEvent1';
                $fields[] = 'tre.dEventStartDate AS startDateofEvent';
                $fields[] = 'tre.dEventEndDate AS endDateofEvent';
                $fields[] = 'tre.iDayofEvent AS dayofEvent';
                //$fields[] = 'tre.iTimeofEvent AS timeofEvent';
                $fields[] = 'tre.vEventStartTime as startTimeofEvent';
                $fields[] = 'tre.vEventEndTime as endTimeofEvent';
                $fields[] = 'tre.iVenueofEvent AS venueofEvent';
                $fields[] = 'tre.URL AS urlEvent';
                $condition[] = 'tre.iRestaurantID = tr.iRestaurantID';
                //$condition[] = 'tre.iDateofEvent >=' . date('Y-m-d');
                $condition[] = 'tre.iRestaurantID = "' . $recordId . '"';
                $condition[] = 'tre.eStatus IN(\'Active\')';
                $condition[] = 'CURDATE() <= tre.dEventEndDate';

                $tbl = implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = implode(' AND ', $condition);

                $groupBy = ' GROUP BY tre.iEventId';
                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition . $groupBy;
                $res = $this->db->query($qry);
                $row = $res->result_array();
                $days = array(
                    '1' => 'Sun',
                    '2' => 'Mon',
                    '3' => 'Tue',
                    '4' => 'Wed',
                    '5' => 'Thu',
                    '6' => 'Fri',
                    '7' => 'Sat'
                );


                foreach ($row as $kk => $value) {
                    if ($value['startDateofEvent1'] && $value['endDateofEvent1']) {
                        if ($value['startDateofEvent1'] != $value['endDateofEvent1']) {
                            $row[$kk]['dateofEvent'] = $value['startDateofEvent1'] . '-' . $value['endDateofEvent1'];
                        } else {
                            $row[$kk]['dateofEvent'] = $value['startDateofEvent1'];
                        }
                    } else {
                        $row[$kk]['dateofEvent'] = $value['startDateofEvent1'];
                    }

                    if ($value['startTimeofEvent'] && $value['endTimeofEvent']) {
                        $row[$kk]['timeofEvent'] = $value['startTimeofEvent'] . '-' . $value['endTimeofEvent'];
                    } else {
                        $row[$kk]['timeofEvent'] = $value['startTimeofEvent'];
                    }
                    unset($row[$kk]['startDateofEvent1'], $row[$kk]['endDateofEvent1'], $row[$kk]['startTimeofEvent'], $row[$kk]['endTimeofEvent']);


                    $dayArr = explode(',', $value['dayofEvent']);
                    asort($dayArr);
                    $day = '';
                    $rangeFlag = false;
                    while ($dayKey = array_shift($dayArr)) {
                        if (count($dayArr)) {
                            $day .= $days[$dayKey] . ",";
//                            if ($dayKey + 1 == $dayArr[0]) {
//                                if (!$rangeFlag) {
//                                    $day .= $days[$dayKey] . "-";
//                                    $rangeFlag = true;
//                                }
//                            } else {
//                                if ($rangeFlag) {
//                                    $rangeFlag = false;
//                                }
//                                $day .= $days[$dayKey] . ",";
//                            }
                        } else {
                            $day .= $days[$dayKey];
                        }
                    }
                    $row[$kk]['dayofEvent'] = $day;
                }

                return $row;
            }

            return array();
        } catch (Exception $ex) {
            throw new Exception('Error in _getRestaurantCategory function - ' . $ex);
        }
    }

    /**
     * This will fetches all the reviews given by all the users for a restaurant.
     * 
     * @author Garima <garima.chowrasia@Foodine :.com>
     * @param type $recordId
     * @return type
     * @throws Exception
     */
    private function _getRestaurantComments($recordId, $userId = null) {
        try {
            if (!empty($recordId)) {
                //extract($postValues);
                //$sql = 'select trr.iReviewID as reviewId, trr.iUserID as userId, trr.iRestaurantID AS restaurant_id, trr.iRateValue AS rate_value, trr.iAmbience AS ambience, trr.iPrice AS price, trr.iFood AS food, trr.iService AS service, trr.tReviewDetail AS review, trr.tCreatedAt as created_at, CONCAT(tu.vFirstName," ",tu.vLastName) AS name, IF(tu.vProfilePicture = "" , CONCAT("' . USER_IMG . 'dM.png"), CONCAT("' . USER_IMG . '", tu.iUserID, "/thumb/", tu.vProfilePicture)) AS profile_pic, tr.vRestaurantName AS restaurant_name from tbl_restaurant_review as trr LEFT JOIN tbl_user as tu ON tu.iUserID=trr.iUserID  LEFT JOIN tbl_restaurant as tr ON tr.iRestaurantID=trr.iRestaurantID where trr.iUserID IN (' . $userId . ' )';
                $sql = 'SELECT trr.iReviewID AS reviewId, trr.iRestaurantID AS restaurantId, tr.vRestaurantName AS restaurantName,CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage, ';
                $sql .= 'tu.iUserID AS userId,';
                $sql .= 'CONCAT(tu.vFirstName," ",tu.vLastName) AS userName,';
                $sql .= 'tu.vFullName AS userName1,';
                $sql .= 'IF(tu.vProfilePicture = "" , CONCAT("' . USER_IMG . 'default-image.png"), CONCAT("' . USER_IMG . '", tu.iUserID, "/thumb/", tu.vProfilePicture))  AS profilePic,';
                $sql .= ' TRUNCATE((trr.iAmbience + trr.iPrice + trr.iFood + trr.iService) /8, 2 ) AS rating,';
                $sql .= 'trr.iAmbience as ambience, trr.iPrice as price, trr.iFood as food, trr.iService as service,';
                $sql .= ' trr.tReviewDetail AS review, trr.tModifiedAt AS dateTime';
                $sql .= ' FROM  `tbl_restaurant_review` AS  `trr`';
                $sql .= ' LEFT JOIN  `tbl_user` AS  `tu`  ON `trr`.`iUserID` =  `tu`.`iUserID`';
                $sql .= ' LEFT JOIN  `tbl_restaurant` AS  `tr` ON  `trr`.`iRestaurantID` =  `tr`.`iRestaurantID`';
                $sql .= ' WHERE trr.iRestaurantID = ' . $recordId . ' AND trr.eStatus = "active" order by trr.tModifiedAt desc';

                //return  $recordId;die;

                $reviews = $this->db->query($sql)->result_array();
                $reviewArr = $updateReviewArr = array();
                foreach ($reviews AS $key => $review) {
                    $updateReviewArr = $review;
                    $updateReviewArr['elapsedTime'] = $this->getTimeElapsed(strtotime($review['dateTime']));
                    $reviewInfo = $this->_getRestaurantCommentsFav($review['reviewId']);
                    $updateReviewArr['numFavourite'] = isset($reviewInfo['numberFav']) ? $reviewInfo['numberFav'] : '0';
                    $updateReviewArr['numFavouriteID'] = isset($reviewInfo['userArray']) ? $reviewInfo['userArray'] : '0';
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
                            $updateReviewArr["reviewImages"][] = REVIEW_IMG . $review["reviewId"] . "/thumb/" . $img['review_image'];
                        }
                    }
                    if (empty($reviewImages) && empty($review['review'])) {
                        unset($reviews[$key]);
                        continue;
                    }
                    $reviewArr[] = $updateReviewArr;
                }
                //print_r($reviewArr);die;
                return $reviewArr;
            } return false;
        } catch (Exception $ex) {
            throw new Exception('Error in getExpertReviews function - ' . $ex);
        }
    }

    function getRestaurantReviews($restaurantId) {
        try {
            if ($recordId !== '') {
                $tbl[] = 'tbl_user AS tu';
                //$tbl[] = 'tbl_restaurant AS tr';
                $tbl[] = 'tbl_restaurant_review AS trv';

                //$fields[] = 'tu.iUserID AS userId';
                $fields[] = 'CONCAT(tu.vFirstName," ",tu.vLastName) AS userName';
                //$fields[] = 'tu.vFullName AS userName1';
                //$fields[] = 'IF(tu.vProfilePicture = "" , CONCAT("' . USER_IMG . 'default-image.png"), CONCAT("' . USER_IMG . '", tu.iUserID, "/thumb/", tu.vProfilePicture))  AS profilePic';
                //$fields[] = 'trv.iReviewID AS commentId';
                $fields[] = 'trv.tReviewDetail AS comment';
                $fields[] = 'trv.iRateValue AS rating';
                $fields[] = 'trv.tModifiedAt AS dateTime';


                $condition[] = 'trv.eStatus = \'Active\'';
                //$condition[] = 'trv.iRestaurantID = tr.iRestaurantID';
                $condition[] = 'trv.iUserID = tu.iUserID';
                $condition[] = 'trv.iRestaurantID = \'' . $restaurantId . '\'';
                $condition[] = 'trv.tReviewDetail != \'\'';
                $condition[] = 'trv.eStatus = \'active\'';


                $tbl = implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
                //echo $qry; exit;
                $res = $this->db->query($qry);
                $row = $res->result_array();

                foreach($row as $line) {
                    $line['dateTime'] = strtotime($line['dateTime']) * 1000;
                    $return[] = $line;
                }

                
                //print_r($row); exit;

                return $return;
            }
            return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantComments function - ' . $ex);
        } 
    }
    private function _getRestaurantComments1($recordId) {
        try {
            if ($recordId !== '') {
                $tbl[] = 'tbl_user AS tu';
                $tbl[] = 'tbl_restaurant AS tr';
                $tbl[] = 'tbl_restaurant_review AS trv';

                $fields[] = 'tu.iUserID AS userId';
                $fields[] = 'CONCAT(tu.vFirstName," ",tu.vLastName) AS userName';
                $fields[] = 'tu.vFullName AS userName1';
                $fields[] = 'IF(tu.vProfilePicture = "" , CONCAT("' . USER_IMG . 'default-image.png"), CONCAT("' . USER_IMG . '", tu.iUserID, "/thumb/", tu.vProfilePicture))  AS profilePic';
                $fields[] = 'trv.iReviewID AS commentId';
                $fields[] = 'trv.tReviewDetail AS commentDesc';
                $fields[] = 'trv.iRateValue AS rating';
                $fields[] = 'IFNULL(trv.tModifiedAt, "") AS commentTime';


                $condition[] = 'trv.eStatus = \'Active\'';
                $condition[] = 'trv.iRestaurantID = tr.iRestaurantID';
                $condition[] = 'trv.iUserID = tu.iUserID';
                $condition[] = 'trv.iRestaurantID = \'' . $recordId . '\'';
                $condition[] = 'trv.tReviewDetail != \'\'';
                $condition[] = 'trv.eStatus = \'active\'';


                $tbl = implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
                $res = $this->db->query($qry);
                $row = $res->result_array();
                foreach ($row as $key => $value) {
                    $reviewInfo = $this->_getRestaurantCommentsFav($value['commentId']);
                    $row[$key]['numFavourite'] = isset($reviewInfo['numberFav']) ? $reviewInfo['numberFav'] : '0';
                    $row[$key]['numFavouriteID'] = isset($reviewInfo['userArray']) ? $reviewInfo['userArray'] : '0';
                    $row[$key]['numcomment'] = $this->_getRestaurantReviewComments($value['commentId']);
                    $row[$key]['elapsedTime'] = $this->getTimeElapsed(strtotime($value['commentTime']));
                }
                return $row;
            }
            return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantComments function - ' . $ex);
        }
    }

    /**
     * This fetches all the favourite comment of any review for a restaurant.
     * 
     * @author Garima <garima.chowrasia@Foodine :.com>
     * @param type $recordId
     * @return type
     * @throws Exception
     */
    private function _getRestaurantCommentsFav($recordId) {
        try {
            if ($recordId !== '') {
                $tbl[] = 'tbl_restaurant_review_user_fav AS trruf';

                $fields[] = 'COUNT(trruf.iUserID) AS numberFav';
                $fields[] = 'GROUP_CONCAT(trruf.iUserID) AS userArray';


                $condition[] = 'trruf.iReviewID = \'' . $recordId . '\'';


                $tbl = implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = implode(' AND ', $condition);
                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
                $res = $this->db->query($qry);
                $row = $res->row_array();
                return $row;
            }
            return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantCommentsFav function - ' . $ex);
        }
    }

    /**
     * This will fetch all the review comments of a restaurant review.
     * 
     * @author Garima <garima.chowrasia@Foodine :.com>
     * @param type $recordId
     * @return type
     * @throws Exception
     */
    private function _getRestaurantReviewComments($recordId) {
        try {
            if ($recordId !== '') {
                $tbl[] = 'tbl_restaurant_review_user_comment AS trruc';

                $fields[] = 'COUNT(trruc.iUserID) AS numberComment';


                $condition[] = 'trruc.iReviewID = \'' . $recordId . '\'';


                $tbl = implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = implode(' AND ', $condition);
                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
                $res = $this->db->query($qry);
                $row = $res->row_array();
                return isset($row['numberComment']) ? $row['numberComment'] : '0';
            }
            return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantReviewComments function - ' . $ex);
        }
    }

    /**
     * This function fetches all the menu images of a resturant.
     * 
     * @author Garima <garima.chowrasia@Foodine :.com>
     * @param type $recordId
     * @return type
     * @throws Exception
     */
    private function _getRestaurantMenu($recordId) {
        try {
            if ($recordId !== '') {
                $tbl[] = 'tbl_restaurant AS tr';
                $tbl[] = 'tbl_restaurant_menu_image AS trmi';

                $fields[] = 'trmi.iMenuPictureID AS menuId';
                $fields[] = 'trmi.eMenutype AS menuType';
                $fields[] = 'IF(trmi.vPictureName = "" , CONCAT("' . MENU_IMG . 'default.png"), CONCAT("' . MENU_IMG . '", trmi.iRestaurantID, "/thumb/", trmi.vPictureName))  AS menuImage';
                $fields[] = 'IF(trmi.vPictureName = "" , CONCAT("' . MENU_IMG . 'default.png"), CONCAT("' . MENU_IMG . '", trmi.iRestaurantID, "/", trmi.vPictureName))  AS menuFullImage';

                $condition[] = 'trmi.eStatus = \'Active\'';
                $condition[] = 'trmi.iRestaurantID = tr.iRestaurantID';
                $condition[] = 'trmi.iRestaurantID = \'' . $recordId . '\'';

                $tbl = implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
                $res = $this->db->query($qry);

                $row = $res->result_array();

                $arry = array();
                $arry['food'] = array();
                $arry['bar'] = array();
                foreach ($row as $key => $val) {
                    $arry[$val['menuType']][] = $val;
                }

                return $arry;
            }
            return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantMenu function - ' . $ex);
        }
    }

    /**
     * This function fetch all the deals for a restaturant.
     * 
     * @author Garima <garima.chowrasia@Foodine :.com>
     * @param type $recordId
     * @return type
     * @throws Exception
     */
    private function _getRestaurantDeals($recordId, $userId) {
       // echo $recordId; exit;
        try {
            if ($recordId !== '') {
                $tbl[] = 'tbl_restaurant AS tr';
                $tbl[] = 'tbl_deals AS td';

                $fields[] = 'tr.vRestaurantName AS restaurantName';
                $fields[] = 'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ) AS restaurantImage';
                $fields[] = 'td.iDealID AS offerId';
                $fields[] = 'td.vOfferText AS offerText';
                $fields[] = 'td.tOfferDetail AS offerDetail';
                $fields[] = 'td.tTermsOfUse AS offerTerms';
                $fields[] = 'td.vDaysAllow AS daysAllow';
                $fields[] = 'td.vDealCode AS dealCode';
                $fields[] = 'DATE_FORMAT(td.dtStartDate,"' . MYSQL_DATE_FORMAT . '") AS offerStartDate';
                $fields[] = 'DATE_FORMAT(td.dtExpiryDate,"' . MYSQL_DATE_FORMAT . '") AS offerEndDate';
                $fields[] = 'DATE_FORMAT(td.dtExpiryDate,"%h:%i %p") AS exipryTime';

                $condition[] = 'td.eStatus = \'Active\'';
                $condition[] = 'td.iRestaurantID = tr.iRestaurantID';
                $condition[] = 'CURDATE() between td.dtStartDate and td.dtExpiryDate';
                $condition[] = 'td.iRestaurantID = \'' . $recordId . '\'';
                $currenttime = date('Y-m-d H:i:s');
                if (isset($userId) && $userId !== '' && $userId > 0) {
                    //$tbl[] = 'tbl_deals_code AS tdc';
                    //$fields[] = 'IFNULL(tdc.vOfferCode, "") AS offerCode';
                    $condition1[] = 'td.iDealID  = tdc.iDealID';
                    $condition1[] = 'tdc.iUserId  = ' . $userId;
                    $condition1[] = "tdc.eStatus  = 'availed'";
                    $condition1[] = 'tdc.dtExpiryDate >=' . "'" . $currenttime . "'";
                    $condition1[] = 'iTableBookID IS NULL';

                    $conditionCode = implode(' AND ', $condition1);

                    $fields[] = 'IFNULL((SELECT tdc.vOfferCode FROM tbl_deals_code AS tdc WHERE ' . $conditionCode . '), "") AS offerCode ';
                } else {
                    $fields[] = '"" AS offerCode';
                }

                $tbl = implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
                //echo $qry;
                $res = $this->db->query($qry);

                $row = $res->result_array();

                foreach ($row as $key => $val) {
                    $days = array();
                    if ($val['daysAllow'] != '') {
                        $daysAllow = explode(',', $val['daysAllow']);
                        //mprd($openDays);

                        for ($i = 0; $i < count($daysAllow); $i++) {
                            $days[] = $this->glob[$daysAllow[$i]];
                        }
                    }
                    $row[$key]['daysAllowValue'] = $days;
                    $details = strip_tags($val['offerDetail']);
                    $detail = htmlentities($details);
                    $detailDesc = html_entity_decode($detail);

                    $row[$key]['offerDetail'] = $detailDesc;
                }

                return $row;
            }
            return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantMenu function - ' . $ex);
        }
    }

    private function _getRestaurantOrderMenu($recorId) {
        $fields = array(
            'tm.iItemId as itemId',
            'tm.iRestaurantID as restaurantId',
            'tr.vRestaurantName as restaurantName',
            'tm.vItemName as itemName',
            'tm.tItemDesc as itemDesc',
            'tm.dItemPrice as itemPrice',
            'tm.iMealTypeId as mealTypeId',
            //'tm.labelName',
            'CONCAT("' . BASEURL . 'images/menu/", IF(tm.vItemImage = \'\', "default.jpeg", CONCAT(tm.iRestaurantID,\'/\',tm.vItemImage)) ) AS itemImage',
        );

//        $fields = array(
//            'tm.itemId',
//            'tm.restaurantId',
//            'tr.vRestaurantName as restaurantName',
//            'tm.itemName',
//            'tm.itemDesc',
//            'tm.itemPrice',
//            'tm.labelName',
//            'tm.itemDesc',
//            'tm.mealTypeId',
//            'tm.labelName',
//            'CONCAT("' . BASEURL . 'images/menu/", IF(tm.itemImage = \'\', "default.jpeg", CONCAT(tm.restaurantId,\'/\',tm.itemImage)) ) AS itemImage',
//        );

        $tbl = array(
            'tbl_menu_item AS tm',
            'tbl_restaurant AS tr',
            'tbl_meal_type AS tmt',
        );
        //$tbl[] = 'tbl_deals AS td';
//        $condition = array(
//            'tm.restaurantId IN(' . $recorId . ')',
//            'tm.status = "Active"',
//            'tm.restaurantId = tr.iRestaurantID',
//            'tm.mealTypeId = tmt.mealTypeId'
//        );

        $condition = array(
            'tm.iRestaurantID IN(' . $recorId . ')',
            'tm.eStatus = "Active"',
            'tm.iRestaurantID = tr.iRestaurantID',
            'tm.iMealTypeId = tmt.iMealTypeId'
        );


        //$condition[] = 'td.iRestaurantID = tr.iRestaurantID';
        //$condition[] = 'CURDATE() between td.dtStartDate and td.dtExpiryDate';

        $fields = implode(',', $fields);
        $tbl = ' FROM ' . implode(',', $tbl);
        $condition = ' WHERE ' . implode(' AND ', $condition);
        //$orderBy = ' ORDER BY tm.labelName ASC';
        $limit = $orderBy = '';

        $qry = 'SELECT ' . $fields . $tbl . $condition . $orderBy . $limit;

        $res = $this->db->query($qry);
        return $res->result_array();
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
            $arry[] = BASEURL . 'uploads/restaurantPhoto/' . $recorId . '/thumb/' . $row[$i]['imageName'];
        }

        //Published photos
        $fields = $condition = $tbl = array();
        $fields[] = 'CONCAT("' . BASEURL . 'images/publish/", IF(tpi.vImageName = \'\', "default.jpeg", CONCAT(tp.iPublishID,\'/thumb/\',tpi.vImageName)) ) AS image';

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
        $fields[] = 'CONCAT("' . BASEURL . 'images/reviewImages/", IF(tpi.iReviewImage = \'\', "default.jpeg", CONCAT(tp.iReviewID,\'/thumb/\',tpi.iReviewImage)) ) AS reviewImage';

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

    /**
     * New restaurant images function, as _getRestaurantImage is private
     * @param $recorId
     * @param $restImage
     * @return array
     */
    function getRestaurantUserImages($recorId, $restImage, $restThumbImage) {
        $qry = 'SELECT vPictureName AS imageName FROM tbl_restaurant_image WHERE iRestaurantID = \'' . $recorId . '\'';
        $res = $this->db->query($qry);
        //mprd($res->result_array());
        $row = $res->result_array();
        $arry = array();
        $arry[] = $restImage;
        $thumbArry[] = $restThumbImage;

        for ($i = 0; $i < count($row); $i++) {
            $arry[] = BASEURL . 'images/restaurantPhoto/' . $recorId . '/' . $row[$i]['imageName'];
            $thumbArry[] = BASEURL . 'images/restaurantPhoto/' . $recorId . '/thumb/' . $row[$i]['imageName'];
        }

        if (empty($arry)) {
            $arry[] = $thumbArry[] = BASEURL . 'images/restaurantPhoto/default.png';
        }
        $data = array(
            'images' => $arry,
            'thumbImages' => $thumbArry
        );
        return $data;
    }

    function setRestaurantLikeDisLike($userId = '', $restaurantId = '', $saveValue = '') {
        try {
            if ($userId !== '' && $restaurantId !== '' && $saveValue !== '') {
                /*
                 * TO CHECK USER IS ALREADY LIKED OR NOT...
                 */
                $hasRec = $this->db->query('SELECT iLikeDislikeID,eLikeDislikeValue FROM tbl_restaurant_like_dislike WHERE iRestaurantID = "' . $restaurantId . '" AND iUserID = "' . $userId . '"');
                //echo 'SELECT iLikeDislikeID,eLikeDislikeValue FROM tbl_restaurant_like_dislike WHERE iRestaurantID = "' . $restaurantId . '" AND iUserID = "' . $userId . '"';

                if ($hasRec->num_rows() <= 0) {
                    /*
                     * INSERT THE RECORD TO THAT TABLE
                     */
                    $data = array(
                        'iRestaurantID' => $restaurantId,
                        'iUserID' => $userId,
                        'eLikeDislikeValue' => $saveValue
                    );
                    $this->db->insert('tbl_restaurant_like_dislike', $data);

                    return $this->db->insert_id();
                } else {
                    $rec = $hasRec->row_array();
                    if ($rec['eLikeDislikeValue'] == $saveValue) {
                        return -2;
                    }
                    $data = array(
                        'eLikeDislikeValue' => $saveValue
                    );

                    $this->db->update('tbl_restaurant_like_dislike', $data, array('iLikeDislikeID' => $rec['iLikeDislikeID']));

                    return 0;
                }
            }
            return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in setRestaurantLikeDisLike function - ' . $ex);
        }
    }

    function setRestaurantFavourite($userId = '', $restaurantId = '', $setFavourite = 'yes') {
        try {
            if ($userId !== '' && $restaurantId !== '') {
                /*
                 * TO CHECK USER IS ALREADY LIKED OR NOT...
                 */
                $hasRec = $this->db->query('SELECT iFavoriteID FROM tbl_user_restaurant_favorite WHERE iRestaurantID = "' . $restaurantId . '" AND iUserID = "' . $userId . '"');

                if ($hasRec->num_rows() <= 0 && $setFavourite == 'yes') {
                    /*
                     * INSERT THE RECORD TO THAT TABLE
                     */
                    $data = array(
                        'iRestaurantID' => $restaurantId,
                        'iUserID' => $userId
                    );
                    $this->db->insert('tbl_user_restaurant_favorite', $data);

                    return $this->db->insert_id();
                } else if ($setFavourite == 'no') {
                    $this->db->delete('tbl_user_restaurant_favorite', array('iUserID' => $userId, 'iRestaurantID' => $restaurantId));

                    return -2;
                }
                return 0;
            }
            return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in setRestaurantLikeDisLike function - ' . $ex);
        }
    }

    function getRewardList($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                if ($userId !== '') {

                    $field[] = 'tr.iRewardID AS rewardId';
                    $field[] = 'tr.vRewardTitle AS title';
                    $field[] = 'tr.tRewardDesc AS rewardDesc';
                    //$field[] = 'DATE_FORMAT(tr.dtRewardValidUpto,"' . MYSQL_DATE_FORMAT . '") AS rewardValidDate';
                    $field[] = 'tr.tRewardDesc AS description';
                    $field[] = 'tr.iRewardPoint AS requiredPoints';
                    //$field[] = 'tr.iRewardVoucher AS rewardVoucher';
                    $field[] = 'CONCAT("' . BASEURL . 'images/reward/", IF(tr.vRewardImage = "", "default.jpeg", CONCAT(tr.iRewardID,"/",tr.vRewardImage))) AS photoUrl';

                   // $field[] = 'IF( (SELECT COUNT(trr.iRewardRequestID) FROM tbl_reward_request trr WHERE trr.iUserID = "' . $userId . '" AND trr.iRewardID = tr.iRewardID) = 0,"No","Yes") AS rewardRequest';
                    //$field[] = 'IF( (SELECT COUNT(trr.iRewardRequestID) FROM tbl_reward_request trr WHERE trr.iUserID = "' . $userId . '" AND trr.iRewardID = tr.iRewardID) = 0,"",(SELECT DISTINCT eStatus FROM tbl_reward_request trr WHERE trr.iUserID = "' . $userId . '" AND trr.iRewardID IN(tr.iRewardID) )) AS rewardStatus';

                    //$field[] = 'tr.tRewardDesc AS rewardDesc';
                    //>= DATE(NOW())
                    //$res = $this->db->get_where('tbl_reward', array('eStatus' => 'Active'));

                    $condition[] = 'eStatus = "Active"';
                    $condition[] = 'tr.dtRewardValidUpto >= DATE(NOW())';

                    if (isset($recordId) && $recordId != '') {
                        $condition[] = 'tr.iRewardID IN(' . $recordId . ')';
                    }
                    if (isset($rewardId) && $rewardId !== '') {
                        $condition[] = 'iRewardID = "' . $rewardId . '"';
                    }

                    $res = $this->db->query('SELECT ' . implode(',', $field) . ' FROM tbl_reward tr WHERE ' . implode(' AND ', $condition));

                    if (isset($recordId) && $recordId != '') {
                        $row = $res->row_array();
                        foreach ($row as $key => $val) {
                            if ($key === 'rewardValidDate') {
                                $dates = explode(' ', $val);
                                //mprd($dates);
                                foreach ($dates as $k => $v)
                                    $dates[$k] = rtrim($v, ',');
                                $row[$key] = $dates;
                            }
                        }
                    } else {
                        $row = $res->result_array();

                        for ($i = 0; $i < count($row); $i++) {
                            foreach ($row[$i] as $key => $val) {
                                if ($key === 'rewardValidDate') {
                                    $dates = explode(' ', $val);
                                    //mprd($dates);
                                    foreach ($dates as $k => $v)
                                        $dates[$k] = rtrim($v, ',');
                                    $row[$i][$key] = $dates;
                                }
                            }
                        }
                    }

                    return $row;
                } return '';
            } return '';
        } catch (Exception $ex) {
            throw new Exception('Error in getRewardList function - ' . $ex);
        }
    }

    function getRewardDetail($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                if ($userId !== '' && $rewardId !== '') {
                    $field[] = 'iRewardID AS rewardId';
                    $field[] = 'vRewardTitle AS rewardTitle';
                    $field[] = 'iRewardPoint AS rewardPoint';
                    $field[] = 'iRewardVoucher AS rewardVoucher';
                    $field[] = 'CONCAT("' . BASEURL . 'images/reward/", IF(vRewardImage = "", "default.jpeg", CONCAT(iRewardID,"/",vRewardImage))) AS rewardImage';

                    $res = $this->db->query('SELECT ' . implode(',', $field) . ' FROM tbl_reward WHERE eStatus = "Active" AND ');
                    //echo $this->db->last_query();
                    return $res->row_array();
                } return '';
            } return '';
        } catch (Exception $ex) {
            throw new Exception('Error in getRewardDetail function - ' . $ex);
        }
    }

    /*
     * ADD RESTAURANT RATE VALUE
     */

    function addRate($userId = '') {
        try {
            if ($userId != '') {
                $res = $this->db->get_where('tbl_user', array('iUserID' => $userId, 'eGivenRate' => 'yes'));
                if ($res->num_rows() <= 0) {
                    $this->db->update('tbl_user', array('eGivenRate' => 'yes'), array('iUserID' => $userId));
                    $this->general_model->addUserPointValue($userId, 4);
                    return 1;
                } return 0;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in addRate function - ' . $ex);
        }
    }

    /*
     * USE REWARD 
     */

    function useReward($postValue = array()) {
        try {

            if (!empty($postValue)) {
                extract($postValue);

                if ($userId != '' && $rewardId != '') {
                    /*
                     * TO CHECK THAT REWARD REQUEST ALREADY SEND OR NOT
                     */
                    $res = $this->db->get_where('tbl_reward_request', array('iRewardID' => $rewardId, 'iUserID' => $userId));
                    if ($res->num_rows() <= 0) {

                        /*
                         * TO CHECK THAT USER HAS ENOUGH POINTS OR NOT
                         */
                        $USERPOINTS = $this->general_model->getRedeemAmount($userId);
                        $qry = 'SELECT iRewardID '
                                . 'FROM tbl_reward '
                                . 'WHERE iRewardID IN(' . $rewardId . ') '
                                . 'AND iRewardPoint <= ' . $USERPOINTS;

                        $pointRes = $this->db->query($qry);
                        if ($pointRes->num_rows() > 0) {
                            $ins = array(
                                'iRewardID' => $rewardId,
                                'iUserID' => $userId,
                                'tCreatedAt' => date('Y-m-d H:i:s'),
                            );
                            $this->db->insert('tbl_reward_request', $ins);

                            //$this->general_model->addUserPointValue($userId, 2);

                            return $this->db->insert_id();
                        } return -4;
                    } return -3;
                } return -2;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in useReward function - ' . $ex);
        }
    }

    /**
     * Book restaurant table via WEB
     * @param array $postValue
     * @return int
     * @throws Exception
     */
    function bookWebTableValidate($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                // check if same rest., same slot and same time
                $qry = 'SELECT iTableBookID FROM tbl_table_book'
                        . ' WHERE iRestaurantID IN(' . $restaurantId . ') '
                        . ' AND iSlotID IN(' . $slotId . ') '
                        . ' AND eBookingStatus != "Cart"'
                        . ' AND tDateTime = \'' . date('Y-m-d', strtotime($bookDate)) . '\' ';

                $res = $this->db->query($qry);
                if ($res->num_rows() <= 0 || $res->num_rows() > 0) {
                    // check if max 5 accepted bookings for same rest. same day
                    $qry2 = 'SELECT iTableBookID FROM tbl_table_book'
                            . ' WHERE iRestaurantID IN(' . $restaurantId . ') '
                            . ' AND tDateTime = \'' . date('Y-m-d', strtotime($bookDate)) . '\' '
                            . ' AND eBookingStatus = "Accept"';

                    $res2 = $this->db->query($qry2);
                    if ($res2->num_rows < 5 || $res2->num_rows >= 5) {
                        // restaurant cut off time check
                        $slots = $this->getRestaurantSlots($restaurantId);
                        //return $slots;
                        // AS CLOSE TIME IF 12 AM SHOULD ONLY SELECT 11:30PM TO 12:00 AM SLOT WHICH IS 48
                        //if (($slots['openSlot'] >= $slotId && $slotId <= $slots['closeSlot']) ? true : false) {
                        if (($slotId >= $slots['openSlot'] && $slotId <= $slots['closeSlot']) ? true : false) {
                            return -5; //Record can be saved
                        } return -4;
                    } return -3;
                } return -2;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in bookWebTable function - ' . $ex);
        }
    }

    /**
      /**
     * Book restaurant table via WEB
     * @param array $postValue
     * @return int
     * @throws Exception
     */
    function bookWebTable($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                // check if same rest., same slot and same time
                $qry = 'SELECT iTableBookID FROM tbl_table_book'
                        . ' WHERE iRestaurantID IN(' . $restaurantId . ') '
                        . ' AND eBookingStatus != "Cart"'
                        . ' AND iSlotID IN(' . $slotId . ') '
                        . ' AND tDateTime = \'' . date('Y-m-d', strtotime($bookDate)) . '\' ';

                $res = $this->db->query($qry);

                if ($res->num_rows() <= 0 || $res->num_rows() > 0) {
                    // check if max 5 accepted bookings for same rest. same day
                    $qry2 = 'SELECT iTableBookID FROM tbl_table_book'
                            . ' WHERE iRestaurantID IN(' . $restaurantId . ') '
                            . ' AND tDateTime = \'' . date('Y-m-d', strtotime($bookDate)) . '\' '
                            . ' AND eBookingStatus = "Accept"';

                    $res2 = $this->db->query($qry2);

                    if ($res2->num_rows < 5 || $res2->num_rows >= 5) {
                        // restaurant cut off time check
                        $slots = $this->getRestaurantSlots($restaurantId);
                        //return $slots;
                        // if (($slotId >= $slots['openSlot'] && $slotId <= $slots['closeSlot']) ? true : false) {
                        // AS CLOSE TIME IF 12 AM SHOULD ONLY SELECT 11:30PM TO 12:00 AM SLOT WHICH IS 48
                        if (($slotId >= $slots['openSlot'] && $slotId <= $slots['closeSlot']) ? true : false) {

                            $ins = array(
                                'iUserID' => $userId,
                                'iRestaurantID' => $restaurantId,
                                'iSlotID' => $slotId,
                                'iWaitTime' => 0,
                                'iPersonTotal' => $totalPerson,
                                'iMealType' => $mealType,
                                'iDealID' => $selOffer,
                                'vUserRequest' => $userRequest,
                                'vUserName' => $userBookingName,
                                'vMobileNo' => $userMobile,
                                'tDateTime' => date('Y-m-d', strtotime($bookDate)),
                                'tCreatedAt' => date('Y-m-d'),
                                'ePlatform' => 'web',
                            );

                            $qry3 = 'SELECT iTableBookID FROM tbl_table_book'
                                    . ' WHERE iRestaurantID IN(' . $restaurantId . ') '
                                    . ' AND iUserID = \'' . $userId . '\' '
                                    . ' AND iSlotID = \'' . $slotId . '\' '
                                    . ' AND eBookingStatus = "Cart"';
                            $res3 = $this->db->query($qry3);
                            $tableId = $res3->result_array();
                            if ($res3->num_rows > 0) {
                                $ins['eBookingStatus'] = 'Pending';
                                $ins['tUpdatedAt'] = date('Y-m-d');

                                $data = $this->db->update('tbl_table_book', $ins, array('iRestaurantID' => $restaurantId,
                                    'iUserID' => $userId, 'iSlotID' => $slotId, 'eBookingStatus' => 'Cart'));
                                $insId = isset($tableId['0']['iTableBookID']) ? $tableId['0']['iTableBookID'] : 0;
                            } else {
                                if (isset($tableBookId) && !empty($tableBookId)) {
                                    $ins['eBookingStatus'] = 'Pending';
                                    $ins['tUpdatedAt'] = date('Y-m-d');

                                    $this->db->update('tbl_table_book', $ins, array('iTableBookID' => $tableBookId));
                                    $insId = $tableBookId;
                                } else {
                                    $this->db->insert('tbl_table_book', $ins);

                                    $insId = $this->db->insert_id();

                                    if ($insId) {
                                        $this->db->query('UPDATE tbl_table_book SET unique_code = CONCAT("HMB",LPAD(iTableBookID, 5, "0")) WHERE iTableBookID = ' . $insId);
                                        //$this->db->update('tbl_table_book', array('unique_code' => "CONCAT('HMB',LPAD(iTableBookID, 5, '0'))"), array('iTableBookID' => $insId));
                                    }
                                }
                            }


                            $this->general_model->addUserPointValueWeb($userId, 2, $insId);

                            /*
                             * HERE PUSH NOTIFICATION WILL SEND IT TO VENDOR APP
                             */
                            $record = $this->db->query('SELECT ePlatform, vDeviceToken FROM tbl_admin WHERE iRestaurantID IN(' . $restaurantId . ')')->row_array();

                            $this->load->library('pushnotify');

                            $osType = $record['ePlatform'] == 'ios' ? 2 : 1;
                            $deviceToken = $record['vDeviceToken'];

                            $mesg = 'You have a new booking request.';

                            if ($deviceToken != '') {
                                $this->pushnotify->sendIt($osType, $deviceToken, $mesg, 2);
                            }
                            return $insId;
                        } return -4;
                    } return -3;
                } return -2;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in bookWebTable function - ' . $ex);
        }
    }

    /*
     * TO GET THE BOOKED TABLE LIST
     */

    function bookTableList($postValue) {
        try {
            $currentDate = date('Y-m-d');
            $currentTime = date('H:i:s');
            if (!empty($postValue)) {
                extract($postValue);
                $type = $postValue['searchType'];
                if ($userId != '') {
                    $fields = array(
                        'ttb.vMobileNo AS userMobile',
                        'ttb.iMealType AS mealType',
                        'ttb.iDealID AS selOffer',
                        'ttb.vUserRequest AS userRequest',
                        'ttb.iTableBookID AS bookedId',
                        'ttb.unique_code AS bookingId',
                        //'ttb.iSlotID AS slotId',
                        'ttb.vUserName AS bookingName',
                        'ttb.iWaitTime AS waitingTime',
                        'CASE WHEN ttb.eBookingStatus = "Cancel" THEN "Cancelled" ELSE ttb.eBookingStatus END AS bookingStatus',
                        'ttb.iPersonTotal AS totalPerson',
                        'ttb.tDateTime AS bookedDate',
                        //'DATE_FORMAT(sm.tstartFrom,"%h:%i %p") AS slotTime',
//                    'sm.tstartFrom AS slotTime',
                        'tr.iRestaurantID AS id',
                        'tr.vRestaurantName AS restaurantName',
                        'tr.tAddress2 AS restaurantAddress',
                        'CONCAT("' . BASEURL . 'images/restaurantMobile/", IF(tr.vRestaurantMobLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantMobLogo)) ) AS restaurantImage',
//                        'tbd.vOfferText AS offer',
                        'IFNULL((SELECT tbd.vOfferText FROM tbl_deals tbd WHERE ttb.iDealID = tbd.iDealID),0) AS offer',
                        'IF((SELECT count(tbd.vOfferText) FROM tbl_deals tbd WHERE ttb.iDealID = tbd.iDealID)=0,"0 Offer","1 Offer") AS offerCount',
//"IF(appliedOfferCode = '',0,1) as offerCount",
                    );



                    $tbl = array(
                        'tbl_restaurant AS tr',
                        'tbl_table_book AS ttb',
                        //'slot_master AS sm',
                        'tbl_deals AS tbd'
                    );

                    $condition = array(
                        'tr.iRestaurantID IN(ttb.iRestaurantID)',
                        'ttb.iUserID IN(' . $userId . ')',
                        //'sm.iSlotID = ttb.iSlotID',
//                        'ttb.iDealID = tbd.iDealID',
                    );
                    $condition[] = 'ttb.isDeleted != "yes"';
                    if (isset($type) && !empty($type)) {
                        if ($type == 'upcoming') {
                            $condition[] = 'ttb.tDateTime >= CURDATE()';
                            $condition[] = 'ttb.eBookingStatus != "Cancel"';
                        }
                        if ($type == 'history') {
                            $condition[] = '((ttb.tDateTime < CURDATE() AND ttb.eBookingStatus != "Cancel") OR (ttb.eBookingStatus = "Cancel"))';
                            //$condition[] = '((ttb.tDateTime <= CURDATE() AND ttb.eBookingStatus != "Cancel") OR (ttb.eBookingStatus = "Cancel"))';
                        }
                    }

                    if ($bookingId != '') {
                        $condition[] = 'ttb.iTableBookID IN (' . $bookingId . ')';
                    }

                    $fields = implode(',', $fields);
                    $tbl = ' FROM ' . implode(',', $tbl);
                    $condition = ' WHERE ' . implode(' AND ', $condition);

                    $orderby = ' ORDER BY ttb.iTableBookID DESC';
                    $groupby = ' GROUP BY ttb.iTableBookID';

                    $qry = 'SELECT ' . $fields . $tbl . $condition . $groupby . $orderby;
                    //echo $qry; exit;
                    if ($bookingId != '') {
                        $row = $this->db->query($qry)->row_array();
                    } else {
                        $row = $this->db->query($qry)->result_array();
                    }

                    $breakFast = range(25, 48); //range(13, 24);
                    $lunch = range(49, 72); //range(25, 36);
                    $dinner1 = range(73, 96); //range(37, 48);
                    $dinner2 = range(1, 24);
                    $dinner = array_merge($dinner1, $dinner2);

                    if ($bookingId != '') {
                        foreach ($row as $k => $v) {
//                            $row['offerCount'] = '1 Offer';
                            if (in_array($k, array('bookedId', 'slotId', 'waitingTime', 'totalPerson'))) {
                                $v = (int) $v;
                                $row[$k] = $v;
                                if ($k === 'slotId') {
                                    if (in_array($v, $breakFast)) {
                                        $row['timeType'] = 'Breakfast';
                                    } else if (in_array($v, $lunch)) {
                                        $row['timeType'] = 'Lunch';
                                    } else {
                                        $row['timeType'] = 'Dinner';
                                    }
                                }
                            }
                        }
                    } else {
                        for ($i = 0; $i < count($row); $i++) {
                            foreach ($row[$i] as $k => $v) {
//                                $row[$i]['offerCount'] = '1 Offer';
                                if (in_array($k, array('bookedId', 'slotId', 'waitingTime', 'totalPerson'))) {
                                    $v = (int) $v;
                                    $row[$i][$k] = $v;
                                    if ($k === 'slotId') {
                                        if (in_array($v, $breakFast)) {
                                            $row[$i]['timeType'] = 'Breakfast';
                                        } else if (in_array($v, $lunch)) {
                                            $row[$i]['timeType'] = 'Lunch';
                                        } else {
                                            $row[$i]['timeType'] = 'Dinner';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (isset($type) && !empty($type)) {
                        $newArr = array();
                        $result = $row;
                        foreach ($result as $key => $value) {
                            $bookingDate = strtotime($value['bookedDate']);
//                            if ($currentDate == $bookingDate) {
//                                if (trim($type) == 'upcoming') {
//                                    if (strtotime($currentTime) < strtotime($value['slotTime']))
//                                        $newArr[] = $value;
//                                    //$row[$key] = $value;
//                                }
//                                if (trim($type) == 'history') {
//                                    if ((strtotime($currentTime) >= strtotime($value['slotTime'])) || $value['bookingStatus'] == 'Cancelled')
//                                        $newArr[] = $value;
//                                    //$row[$key] = $value;
//                                }
//                            }else {
                            $bookingId = $value['bookedId'];
                            $dealId = $value['selOffer'];
                            if (trim($type) == 'upcoming' && !empty($userId) && !empty($bookingId) && !empty($dealId)) {
                                $value['offerCode'] = $this->_getOfferCode($userId, $bookingId, $dealId);
                            }

                            if (trim($type) == 'history') {
                                $value['offerCode'] = "";
                            }
                            $value['bookedDate'] = strtotime($value['bookedDate']);
                            $newArr[] = $value;
                            
                            //print_r($value); exit;
                            //}
                        }
                        
                        $row = $newArr;
                    }
                    return $row;
                }
            }return array();
        } catch (Exception $ex) {
            throw new Exception('Error in bookTableList function - ' . $ex);
        }
    }

    private function _getOfferCode($userId, $bookingId, $dealId) {
        $currentDate = date('Y-m-d');
        if (!empty($userId) && !empty($bookingId) && !empty($dealId)) {
            $condition[] = 'tdc.iDealID  = ' . $dealId;
            $condition[] = 'tdc.iTableBookID  = ' . $bookingId;
            $condition[] = 'tdc.iUserId  = ' . $userId;
            $condition[] = "tdc.eStatus  = 'availed'";
            $condition[] = 'tdc.dtExpiryDate >=' . "'" . $currentDate . "'";
            $fields = array('tdc.vOfferCode');
            // $fields[] = 'IFNULL((SELECT tdc.vOfferCode FROM tbl_deals_code AS tdc WHERE ' . $conditionCode . '), "") AS offerCode ';

            $tbl = 'tbl_deals_code AS tdc';
            $fields = implode(',', $fields);
            $condition = implode(' AND ', $condition);

            $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition . 'limit 1';
            $res = $this->db->query($qry);

            $row = $res->row_array();
            if (isset($row['vOfferCode'])) {
                return $row['vOfferCode'];
            } return '';
        } return '';
    }

    /*
     * TO CANCEL THE RESERVATION
     */

    function cancelReservation($userId = '', $bookedId = '') {
        try {
            if ($userId != '' && $bookedId != '') {
                $this->db->update('tbl_table_book', array('eBookingStatus' => 'Cancel'), array('iTableBookID' => $bookedId, 'iUserID' => $userId));
                if ($this->db->affected_rows() > 0) {
                    //send push notification
                    $sql = "SELECT tr.vRestaurantName AS restaurantName,ttb.vUserName as vUserName,ttb.vMobileNo as vMobileNo, ttb.tDateTime AS bookDate FROM tbl_table_book AS ttb LEFT JOIN tbl_restaurant AS tr ON ttb.iRestaurantID=tr.iRestaurantID WHERE iTableBookID='$bookedId'";
                    $res = $this->db->query($sql)->row_array();
                    $bookingName = isset($res['vUserName']) ? $res['vUserName'] : '';
                    $bookingMobile = isset($res['vMobileNo']) ? $res['vMobileNo'] : '';

                    $this->load->library('pushnotification', "live");

                    //send sms
                    $user = $this->db->query('SELECT vMobileNo, vFirstName, vLastName FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array();
                    $mobile_msg = "Dear {$bookingName}, \nYour reservation at {$res["restaurantName"]} has been cancelled. If you did not take this action, please call us at 1800118200.\nCheers,\nHungerMafia";
                    $this->load->model('Sms_model', 'sms_m');
                    $this->sms_m->destmobileno = $bookingMobile; //$user['vMobileNo'];
                    $this->sms_m->msg = $mobile_msg;
                    $this->sms_m->Send();

                    //send push
                    $message = "Your table booking request cancelled successfully.";
                    $pushData = array("bookingId" => $bookedId,
                        "restaurantName" => $res["restaurantName"],
                        "time" => $this->getTimeElapsed(strtotime($res["bookDate"])),
                        "notificationType" => 4
                    );

                    $this->load->model('usernotifications_model');
                    $this->usernotifications_model->sendNotification($userId, $message, $pushData);
                    /*
                     * REMOVE RECORD FROM THE USER PONT COLLECTION TABLE.
                     */
                    //Send Mail
                    $this->load->model('tablebooking_model');
                    $this->tablebooking_model->sendBookingEmail('cancel', $bookedId, $userId);

                    $this->db->delete('tbl_user_collect', array('iRecordID' => $bookedId, 'iUserID' => $userId, 'iRewardPointID' => 3));

                    return 1;
                } return -2;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in cancelReservation function - ' . $ex);
        }
    }

    /*
     * GET RESTAURANT SLOT VALUES
     */

    function getRestaurantSlots($restId = '') {
        try {
            if ($restId != '') {



                $row = $this->db->query('SELECT iMinTime AS restaurantMinTime, iMaxTime AS restaurantMaxTime FROM tbl_restaurant WHERE iRestaurantID IN(' . $restId . ')')->row_array();

                $minTime = explode('-', $row['restaurantMinTime']);
                $maxTime = explode('-', $row['restaurantMaxTime']);

                if (isset($minTime[0]) && isset($minTime[1]) && isset($minTime[2])) {
                    $minMaradian = $minTime[2] == '1' ? 'AM' : 'PM';
                    $maxMaradian = $maxTime[2] == '1' ? 'AM' : 'PM';

                    $minhr = strlen($minTime[0]) == 1 ? '0' . $minTime[0] : $minTime[0];
                    $maxhr = strlen($maxTime[0]) == 1 ? '0' . $maxTime[0] : $maxTime[0];

                    $minmin = strlen($minTime[1]) == 1 ? '0' . $minTime[1] : $minTime[1];
                    $maxmin = strlen($maxTime[1]) == 1 ? '0' . $maxTime[1] : $maxTime[1];

                    $openCloseTime = $minhr . ':' . $minmin . ' ' . $minMaradian . ' to ' . $maxhr . ':' . $maxmin . ' ' . $maxMaradian;
                    $openCloseTimeValue = $minhr . ':' . $minmin . ':' . $minMaradian . ' to ' . $maxhr . ':' . $maxmin . ':' . $maxMaradian;
                    $arry['info']['openCloseTime'] = $openCloseTime;
                    $arry['info']['openCloseTimeValue'] = $openCloseTimeValue;

                    $openTimeNW = date('H:i', strtotime($minhr . ':' . $minmin . ' ' . $minMaradian));
                    $closeTimeNW = date('H:i', strtotime($maxhr . ':' . $maxmin . ' ' . $maxMaradian));


                    //$openSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $openTimeNW . '\' AND tendTo > \'' . $openTimeNW . '\'')->row_array();
                    $openSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $openTimeNW . '\' order by `iSlotID` desc')->row_array();
                    //echo $this->db->last_query();
                    $openSlot = (int) @$openSlot['iSlotID'];

                    //$closeSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $closeTimeNW . '\' AND tendTo > \'' . $closeTimeNW . '\'')->row_array();
                    $closeSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $closeTimeNW . '\'  order by `iSlotID` desc')->row_array();
                    //echo $this->db->last_query();
                    // AS CLOSE TIME IF 12 AM SHOULD ONLY SELECT 11:45PM TO 12:00 AM SLOT WHICH IS 96
                    if ($closeTimeNW == '00:00' || $closeTimeNW == '23:45') {
                        $closeSlot = 96;
                    } else if ($closeTimeNW > '00:00' && $maxMaradian == 'AM') {
                        $closeSlot = (int) @$closeSlot['iSlotID'] + 96;
                    } else {
                        $closeSlot = (int) @$closeSlot['iSlotID'];
                    }

                    //$closeSlot = (int) @$closeSlot['iSlotID'];

                    $currentTimeNW = date('H:i', time());
                    //$currentSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $currentTimeNW . '\' AND tendTo > \'' . $currentTimeNW . '\'')->row_array();
                    $currentSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $currentTimeNW . '\' order by `iSlotID` desc')->row_array();
                    $currentSlot = (int) @$currentSlot['iSlotID'];

                    return array(
                        'openSlot' => $openSlot,
                        'closeSlot' => $closeSlot,
                        'currentSlot' => $currentSlot
                    );
                } return array();
            } return array();
        } catch (Exception $ex) {
            
        }
    }

    /*
     * USER POST A REVIEW 
     */

    function postReview($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                $hasrec = $this->db->get_where('tbl_restaurant_review', array('iRestaurantID' => $restaurantId, 'iUserID' => $userId, 'eStatus' => 'active'))->num_rows();
                if ($hasrec > 0) {
                    /*
                     * UPDATE THE RECORD WITH THE OLDER ONE REVIEW AND RATE VALUE...
                     */
                   
                    if($reviewRate && $reviewComment) {
                         $UPDT = array(
                        'iRateValue' => $reviewRate,
                        'tReviewDetail' => $reviewComment
                        );
                    } else if($reviewComment) {
                        $UPDT['tReviewDetail'] = $reviewComment;
                    } else
                    {
                        $UPDT['iRateValue'] = $reviewRate;
                    }
                    
                    $this->db->update('tbl_restaurant_review', $UPDT, array('iRestaurantID' => $restaurantId, 'iUserID' => $userId));
                } else {
                    /*
                     * INSERT THE RECORD WITH REVIEW COMMENT AND RATE VALUE...
                     */

                    $INS = array(
                        'iUserID' => $userId,
                        'iRestaurantID' => $restaurantId,
                        'tCreatedAt' => date('Y:m:d H:i:s'),
                    );
                    
                    if($reviewRate && $reviewComment) {
                         $INS = array(
                        'iRateValue' => $reviewRate,
                        'tReviewDetail' => $reviewComment
                        );
                    } else if($reviewComment) {
                        $INS['tReviewDetail'] = $reviewComment;
                    } else
                    {
                        $INS['iRateValue'] = $reviewRate;
                    }

                    $this->db->insert('tbl_restaurant_review', $INS);
                }

                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in postReview functi0on - ' . $ex);
        }
    }

     function postBanquetEnquiry($postValue) {

        try {
            if (!empty($postValue)) {
                extract($postValue);
                    $data = array(
                            'restaurant_id' => $postValue['restaurantId'], 
                            'name' => $postValue['name'],
                            'email' => $postValue['email'],
                            'mobile' => $postValue['mobileNumber'],
                            'user_id' => $postValue['userId'],
                            );
                    $this->db->insert('tbl_banquet_enquiry', $data);
                // $bookingDate1 = date("Y-m-d",$dateTime/1000);
                // $bookingTime = date("h:i A", $dateTime/1000);
                    $bookingName = $postValue['name'];
                    $mobileNumber = $postValue['mobileNumber'];
                    $email = $postValue['email'];
                $mobile_msg = "Your details has been sent to the banquet manager. They will get back to you shortly\nThank you\nTeam Foodine";
                //echo 'hi';
                $this->load->model('Sms_model', 'sms_m');
                //echo 'hello'; exit;
                $this->sms_m->destmobileno = $mobileNumber;
                $this->sms_m->msg = $mobile_msg;
                 $smsData =  $this->sms_m->Send();
                 $smsData = json_decode($smsData);


                    $res_detail = $this->getRestaurantInfo($postValue['restaurantId']);
                   // print_r($res_detail); exit;
                    $restaurantMobile = $res_detail->restaurantContact;
                    //$restaurantMobile = '9555687555';

                    $rest_msg = "Greetings! Enquiry Alert\nCustomer Name: {$bookingName}\nMobile no: {$mobileNumber}\nEmail: {$email}\nThanking you! Team Foodine";
                                
                                $rest['mobile'] = $restaurantMobile;
                                $rest['msg'] = $rest_msg;
                                $data1 = $this->sms_m->Send1($rest);
                                $smsData = json_decode($data1);


                return 1;
            } 
        } catch (Exception $ex) {
            throw new Exception('Error in postReview functi0on - ' . $ex);
        }
    }

    /**
     * 
     * @param type $postValue
     * @return int
     * @throws Exception
     */
    function postWebReview($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                if (isset($reviewId) && !empty($reviewId)) {

                    $sql = "SELECT * FROM tbl_restaurant_review WHERE iReviewID='$reviewId' AND iUserID='$userId' AND eStatus='active'";
                    $record = $this->db->query($sql)->row_array();
                    /*
                     * UPDATE THE RECORD WITH THE OLDER ONE REVIEW AND RATE VALUE...
                     */
                    $UPDT = array(
                        'iAmbience' => isset($ambience) ? $ambience : $record['iAmbience'],
                        'iPrice' => isset($price) ? $price : $record['iPrice'],
                        'iFood' => isset($food) ? $food : $record['iFood'],
                        'iService' => isset($service) ? $service : $record['iService'],
                        'tReviewDetail' => isset($review) ? $review : $record['tReviewDetail'],
                        'tModifiedAt' => date('Y:m:d H:i:s')
                    );
                    $this->db->update('tbl_restaurant_review', $UPDT, array('iReviewID' => $reviewId, 'iUserID' => $userId));
                } else {
                    /*
                     * INSERT THE RECORD WITH REVIEW COMMENT AND RATE VALUE...
                     */

                    $INS = array(
                        'iUserID' => $userId,
                        'iRestaurantID' => $restaurantId,
                        'iAmbience' => isset($ambience) ? $ambience : '',
                        'iPrice' => isset($price) ? $price : '',
                        'iFood' => isset($food) ? $food : '',
                        'iService' => isset($service) ? $service : '',
                        'tReviewDetail' => isset($review) ? $review : '',
                        'tCreatedAt' => date('Y:m:d H:i:s'),
                        'tModifiedAt' => date('Y:m:d H:i:s')
                    );

                    $this->db->insert('tbl_restaurant_review', $INS);
                    $sql = "SELECT iReviewID FROM tbl_restaurant_review WHERE iRestaurantID='$restaurantId' AND iUserID='$userId' AND eStatus='active' order by iReviewID DESC limit 1";
                    $res = $this->db->query($sql)->row_array();
                    $reviewId = $res['iReviewID'];
                    //to add user points
                    $this->load->model('user_points_model');
                    if (!empty($ambience) || !empty($price) || !empty($food) || !empty($service)) {
                        $this->user_points_model->addUserPoints($userId, 3);
                    }
                    if (!empty($review)) {
                        $this->user_points_model->addUserPoints($userId, 4);
                    }
                }
                $this->db->update('tbl_restaurant', array('iSolrFlag' => '0'), array('iRestaurantID' => $restaurantId));
                return $reviewId;
            }

            return false;
        } catch (Exception $ex) {
            throw new Exception('Error in postReview functi0on - ' . $ex);
        }
    }

    function postWebReviewImages($postValue, $reviewID, $reviewImages) {
        extract($postValue);

        $sql = "DELETE FROM tbl_restaurant_review_images where iReviewID = '" . $reviewID . "'";
        $this->db->query($sql);
        foreach ($reviewImages AS $image) {
            $INS = array(
                'iReviewID' => $reviewID,
                'iRestaurantID' => $restaurantId,
                'iReviewImage' => $image,
                'tCreatedAt' => date('Y:m:d H:i:s'),
                'tModifiedAt' => date('Y:m:d H:i:s')
            );
            $this->db->insert('tbl_restaurant_review_images', $INS);
        }
        return true;
    }

    /*
     * SEARCH RESTAURANT LIST
     */

    function searchRestaurantAutofill($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $distFields = '1';
                if (isset($userLat) && isset($userLong)) {
                    $distanceType = 'km';
                    $multiplyer = 6371 * 1000;
                    if ($distanceType == 'miles') {
                        $multiplyer = 3959;
                    }
                    $fields = $tbl = $condition = array();
                    $distFields = ' (ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                            . ' * cos( radians( vLat) )'
                            . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                            . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) ))'
                            . ' AS distance';
                }
                $searchTextStr .= str_replace(' ', "%' OR '%", $searchText);

                $qry1 = "select DISTINCT tr.iRestaurantID AS id,tr.vRestaurantName AS name, tr.vCityName AS city, tco.vLocationName AS location FROM tbl_restaurant AS tr, tbl_location AS tco WHERE tr.eStatus IN('Active') AND ( ( tr.vRestaurantName LIKE '%$searchTextStr%' )) and `tco`.`iLocationID` = `tr`.`iLocationID` ORDER BY
                CASE
                  WHEN tr.vRestaurantName LIKE '$searchTextStr%' THEN 1
                  WHEN tr.vRestaurantName LIKE '%$searchTextStr' THEN 3
                  ELSE 2
                END";
                $qry2 = "select DISTINCT tc.iCuisineID AS id,tc.vCuisineName AS name FROM tbl_cuisine AS tc WHERE tc.eStatus IN('Active') AND ( ( tc.vCuisineName LIKE '%$searchTextStr%' )) ORDER BY
                CASE
                  WHEN tc.vCuisineName LIKE '$searchTextStr%' THEN 1
                  WHEN tc.vCuisineName LIKE '%$searchTextStr' THEN 3
                  ELSE 2
                END";
                $qry3 = "select DISTINCT tct.iCategoryID AS id,tct.vCategoryName AS name FROM tbl_category AS tct WHERE tct.eStatus IN('Active') AND (( tct.vCategoryName LIKE '%$searchTextStr%' ) ) ORDER BY
                CASE
                  WHEN tct.vCategoryName LIKE '$searchTextStr%' THEN 1
                  WHEN tct.vCategoryName LIKE '%$searchTextStr' THEN 3
                  ELSE 2
                END";

                $row1 = $this->db->query($qry1)->result_array();
                foreach ($row1 as $k => $row11) {
                    $row1[$k]['type'] = 'OUTLET';
                }
                $row2 = $this->db->query($qry2)->result_array();
                foreach ($row2 as $l => $row21) {
                    $row2[$l]['type'] = 'CUISINE';
                    $row2[$l]['city'] = '';
                    $row2[$l]['location'] = '';
                }
                $row3 = $this->db->query($qry3)->result_array();
                foreach ($row3 as $m => $row31) {
                    $row3[$m]['type'] = 'CATEGORY';
                    $row3[$m]['city'] = '';
                    $row3[$m]['location'] = '';
                }
                $row = array_merge($row1, $row2, $row3);
                return $row;
            } return array();
        } catch (Exception $ex) {
            throw new Exception('Error in searchRestaurant function - ' . $ex);
        }
    }

    function searchRestaurant($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $distFields = '1';
                if (isset($userLat) && isset($userLong) && !empty($userLat) && !empty($userLong)) {
                    $distanceType = 'km';
                    $multiplyer = 6371 * 1000;
                    if ($distanceType == 'miles') {
                        $multiplyer = 3959;
                    }

                    $fields = $tbl = $condition = array();

                    $distFields = ' (ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                            . ' * cos( radians( vLat) )'
                            . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                            . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) ))'
                            . ' AS distance';
                }
                $fields = array(
                    'DISTINCT tr.iRestaurantID AS restaurantId',
                    'tr.vRestaurantName AS restaurantName',
                    'tr.tAddress AS restaurantAddress',
                    'tr.vLat AS restaurantLatitude',
                    'tr.vLog AS restaurantLongitude',
                    $distFields,
                    'tc.vCuisineName AS cuisineName',
                    'tct.vCategoryName AS categoryName',
                    'IFNULL(DATE_FORMAT(tr.tCreatedAt,\'' . MYSQL_DATE_FORMAT . '\'), "") AS createdDate',
                    'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage',
                    'IFNULL((SELECT AVG(tur.iRateValue) FROM tbl_restaurant_review AS tur WHERE tur.iRestaurantID IN(tr.iRestaurantID) and tur.eStatus = "active"),0) AS ratting',
                    'IFNULL((SELECT COUNT(*) FROM tbl_deals AS td WHERE td.iRestaurantID IN(tr.iRestaurantID)),0) AS totalOffers',
                    'IFNULL((SELECT COUNT(*) FROM tbl_restaurant_review AS trr WHERE trr.iRestaurantID IN(tr.iRestaurantID) and trr.eStatus = "active"),"") AS totalReview',
                        //'IFNULL((SELECT COUNT(*) FROM tbl_restaurant_review AS trr WHERE trr.iRestaurantID IN(tr.iRestaurantID)),"") AS totalReview',
                );

                $join_type = 'JOIN';
                $from = ' FROM tbl_restaurant AS tr ';
                $join[] = array(
                    $join_type => 'tbl_restaurant_cuisine AS trc',
                    'ON' => 'trc.iRestaurantID IN(tr.iRestaurantID)'
                );
                $join[] = array(
                    $join_type => 'tbl_cuisine AS tc',
                    'ON' => 'tc.iCuisineID IN(trc.iCuisineID)'
                );

                $join[] = array(
                    $join_type => 'tbl_restaurant_category AS trct',
                    'ON' => 'trct.iRestaurantID IN(tr.iRestaurantID)'
                );
                $join[] = array(
                    $join_type => 'tbl_category AS tct',
                    'ON' => 'tct.iCategoryID IN(trct.iCategoryID)'
                );

                $join_str = '';
                for ($i = 0; $i < count($join); $i++) {
                    foreach ($join[$i] AS $key => $val) {
                        $join_str .= ' ' . $key . ' ' . $val . ' ';
                    }
                }
                $where = array();


                /*
                 * $tbl = array(
                  'tbl_restaurant AS tr',
                  //'tbl_restaurant_cuisine AS trc',
                  //'tbl_cuisine AS c',
                  //'tbl_restaurant_category AS trct',
                  //'tbl_category AS ct'
                  );
                  $condition[] = 'tr.eStatus IN(\'Active\')';
                  //$condition[] = 'tr.iRestaurantID IN(trc.iRestaurantID)';
                  //$condition[] = 'tr.iRestaurantID IN(trct.iRestaurantID)';
                  //$condition[] = 'ct.iCategoryID IN(trct.iCategoryID)';
                  //$condition[] = 'c.iCuisineID IN(trc.iCuisineID)';
                 * */

                /*
                 * SEARCH LOGIC 
                 */

                $searchQry = array();
                if (isset($searchText) && $searchText != '') {

                    $searchText = explode(' ', @trim($searchText));

                    $searchOn = array(
                        'tr.vRestaurantName',
                        'tr.tAddress',
                        'tc.vCuisineName',
                        'tct.vCategoryName'
                    );

                    foreach ($searchOn AS $v) {
                        $tmp = array();
                        foreach ($searchText as $key => $val) {
                            $tmp[] = $v . ' LIKE \'%' . $val . '%\'';
                        }
                        $searchQry[] = '( ' . implode(' OR ', $tmp) . ' )';
                    }
                    $where[] = $condition[] = $searchQry = '( ' . implode(') OR (', $searchQry) . ' )';
                } else {
                    $searchQry = '';
                }

                $tbl = ' FROM ' . implode(',', $tbl);
                $fields = ' SELECT ' . implode(',', $fields);
                $condition = ' WHERE ' . implode(' AND ', $condition);
                $where = ' AND ' . implode(' AND ', $where);
                $where .= ' WHERE tr.eStatus IN(\'Active\')';


                //$qry = $fields . $tbl . $condition;
                //exit;

                $qry = $fields . $from . $join_str . $where . 'GROUP BY restaurantId';

//                return $qry;
                $row = $this->db->query($qry)->result_array();

                for ($i = 0; $i < count($row); $i++) {
                    foreach ($row[$i] as $key => $val) {
                        if ($key == 'distance') {
                            $distance = $val / 1000;
                            $row[$i][$key] = number_format($distance, 2) . ' KM';
                        } elseif ($key == 'totalPoint') {
                            if ($totalPoints === 0)
                                $totalPoints = (int) $val;
                            unset($row[$i][$key]);
                        }
                    }
                }

                return $row;
            } return array();
        } catch (Exception $ex) {
            throw new Exception('Error in searchRestaurant function - ' . $ex);
        }
    }

    /**
     * Action to get time slots
     * @package Restaurant Model
     * @access Public
     * @author 3031@Foodine :
     * @return array
     */
    function getTimeSlots() {
        $row = array();
        try {
            $query = 'Select iSlotId, DATE_FORMAT(tstartFrom,"' . '%h:%i %p' . '") AS tstartFrom from slot_master';
            $row = $this->db->query($query)->result_array();
            return $row;
        } catch (Exception $ex) {
            throw new Exception('Error in getTimeSlots function - ' . $ex);
        }
    }

    /**
     * 
     * @param type $postValue
     * @return type
     * @throws Exception
     */
    function getUserBookingCart($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                $qry = 'SELECT iTableBookID FROM tbl_table_book'
                        . ' WHERE iRestaurantID IN(' . $restaurantId . ') '
                        . ' AND iSlotID IN(' . $slotId . ') '
                        . ' AND tDateTime >= \'' . date('Y-m-d', strtotime($bookDate)) . '\' ';

                $res = $this->db->query($qry);

                if ($res->num_rows() <= 0 || $res->num_rows() > 0) {
                    $ins = array(
                        'iRestaurantID' => $restaurantId,
                        'iUserID' => $userId,
                        'iSlotID' => $slotId,
                        'iWaitTime' => 0,
                        'iPersonTotal' => $totalPerson,
                        'tDateTime' => date('Y-m-d', strtotime($bookDate)),
                        'tCreatedAt' => date('Y-m-d', strtotime($bookDate))
                    );

                    $this->db->insert('tbl_table_book', $ins);

                    $insId = $this->db->insert_id();

                    $this->general_model->addUserPointValue($userId, 3, $insId);


                    /*
                     * HERE PUSH NOTIFICATION WILL SEND IT TO VENDOR APP
                     */
                    $record = $this->db->query('SELECT ePlatform, vDeviceToken FROM tbl_admin WHERE iRestaurantID IN(' . $restaurantId . ')')->row_array();

                    $this->load->library('pushnotify');

                    $osType = $record['ePlatform'] == 'ios' ? 2 : 1;
                    $deviceToken = $record['vDeviceToken'];

                    $mesg = 'You have a new booking request.';

                    if ($deviceToken != '') {
                        $this->pushnotify->sendIt($osType, $deviceToken, $mesg, 2);
                    }

                    return $insId;
                } return -2;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in bookTable function - ' . $ex);
        }
    }

    /**
     * 
     * @return type
     * @throws Exception
     */
    public function getRestaurantUserRatings() {
        try {
            $fields[] = 'iLocationID AS locationId';
            $fields[] = 'vLocationName AS locationName';

            $tbl[] = 'tbl_location AS tl';

            $condition[] = 'eStatus IN(\'Active\')';

            $fields = 'SELECT ' . implode(',', $fields);
            $tbl = ' FROM ' . implode(',', $tbl);
            $condition = ' WHERE ' . implode(' AND ', $condition);

            $qry = $fields . $tbl . $condition;
            return $this->db->query($qry)->result_array();
        } catch (Exception $ex) {
            throw new Exception('Error in _getAllLocations function - ' . $ex);
        }
    }

    /**
     * This will save the Favourite Book mark information of a user.
     * 
     * @author Garima <garima.chowrasia@Foodine :.com>
     * @param type $postValue
     * @return int
     * @throws Exception
     */
    public function postBookmark($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                $hasrec = $this->db->get_where('tbl_user_restaurant_favorite', array('iRestaurantID' => $restaurantId, 'iUserID' => $userId))->num_rows();
                if ($hasrec > 0) {
                    $this->db->delete('tbl_user_restaurant_favorite', array('iRestaurantID' => $restaurantId, 'iUserID' => $userId));
                    return 0;
                } else {
                    /*
                     * INSERT THE RECORD WITH REVIEW COMMENT AND RATE VALUE...
                     */

                    $INS = array(
                        'iUserID' => $userId,
                        'iRestaurantID' => $restaurantId,
                        'tCreatedAt' => date('Y:m:d H:i:s'),
                    );

                    $this->db->insert('tbl_user_restaurant_favorite', $INS);
                    return 1;
                }

                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in postBookmark functi0on - ' . $ex);
        }
    }
    
    public function suggestRestaurant($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                    $INS = array(
                        'user_id' => $userId,
                        'restaurant_name' => $restaurantName,
                        'res_address' => $address,
                        'city' => $state,
                        'state' => $city,
                    );

                    $this->db->insert('tbl_suggested_restaurant', $INS);
                    return 1;
                

                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in suggest functi0on - ' . $ex);
        }
    }

    /**
     * This function will marked any review as favourite.
     * 
     * @author Garima <garima.chowrasia@Foodine :.com>
     * @param type $postValue
     * @return int
     * @throws Exception
     */
    public function postReviewFavourite($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $hasrec = $this->db->get_where('tbl_restaurant_review_user_fav', array('iReviewID' => $reviewId, 'iUserID' => $userId))->num_rows();
                if ($hasrec > 0) {
                    $this->db->delete('tbl_restaurant_review_user_fav', array('iReviewID' => $reviewId, 'iUserID' => $userId));
                } else {
                    /*
                     * INSERT THE RECORD WITH REVIEW COMMENT AND RATE VALUE...
                     */

                    $INS = array(
                        'iUserID' => $userId,
                        'iReviewID' => $reviewId,
                    );

                    $this->db->insert('tbl_restaurant_review_user_fav', $INS);
                }

                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in postReviewFavourite function - ' . $ex);
        }
    }

    /**
     * This function is used to get the elapsed time in string.
     * 
     * @author Garima <garima.chowrasia@Foodine :.com>
     * @param type $time
     * @return type
     */
    public function getTimeElapsed($time) {

        $time = time() - $time; // to get the time since that moment
        $time = ($time < 1) ? 1 : $time;
        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit)
                continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
        }
    }

    /**
     * This function is used to save comment on review for a restaurant.
     * 
     * @author Garima <garima.chowrasia@Foodine :.com> 
     * @param type $postValue
     * @return int
     * @throws Exception
     */
    public function postReviewComments($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                //code is commented coz unsure whether to isert new record or update existing.
//                $hasrec = $this->db->get_where('tbl_restaurant_review_user_comment', array('iReviewID' => $reviewId, 'iUserID' => $userId))->num_rows();
//                if ($hasrec > 0) {
//                $data = array(
//                        'iUserID' => $userId,
//                        'iReviewID' => $reviewId,
//                        'iCommentText' => $comment,
//                    );
//                    $this->db->update('tbl_restaurant_review_user_comment', $data, array('iReviewID' => $reviewId, 'iUserID' => $userId));
//                } else {
                /*
                 * INSERT THE RECORD WITH REVIEW COMMENT AND RATE VALUE...
                 */

                $INS = array(
                    'iUserID' => $userId,
                    'iReviewID' => $reviewId,
                    'iCommentText' => $comment,
                    'created_at' => date('Y:m:d H:i:s'),
                );

                $this->db->insert('tbl_restaurant_review_user_comment', $INS);

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

//                }

                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in postReviewComments function - ' . $ex);
        }
    }

    /**
     * This function save restaurant closure information.
     * 
     * @author Garima <garima.chowrasia@Foodine :.com>
     * @param type $postValue
     * @return int
     * @throws Exception
     */
    public function postRestClosure($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $hasrec = $this->db->get_where('tbl_restaurant_closure', array('iRestaurantID' => $restaurantId, 'iUserID' => $userId))->num_rows();
                if ($hasrec > 0) {
                    $data = array(
                        'iUserID' => $userId,
                        'iRestaurantID' => $restaurantId,
                        'is_closed' => '1',
                    );
                    $this->db->update('tbl_restaurant_closure', $data, array('iRestaurantID' => $restaurantId, 'iUserID' => $userId));
                } else {
                    /*
                     * INSERT THE RECORD WITH REVIEW COMMENT AND RATE VALUE...
                     */

                    $INS = array(
                        'iUserID' => $userId,
                        'iRestaurantID' => $restaurantId,
                        'is_closed' => '1',
                        'iCreatedAt' => date('Y:m:d H:i:s'),
                    );

                    $this->db->insert('tbl_restaurant_closure', $INS);
                }

                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in postReviewComments function - ' . $ex);
        }
    }

    /**
     * This function save the changes/remarks in restaurant details.
     * 
     * @author Garima <garima.chowrasia@Foodine :.com> 
     * @param type $postValue
     * @return int
     * @throws Exception
     */
    public function postReportError($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                /*
                 * INSERT THE RECORD WITH REVIEW COMMENT AND RATE VALUE...
                 */

                $INS = array(
                    'iUserID' => $userId,
                    'iRestaurantID' => $restaurantId,
                    'iEmail' => $email,
                    'iRemarks' => $remarks,
                    'iCreatedAt' => date('Y:m:d H:i:s'),
                );

                $this->db->insert('tbl_restaurant_edit_request', $INS);

                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in postReportError function - ' . $ex);
        }
    }

    /**
     * This function save report error information of a resturant.
     * 
     * @author Garima <garima.chowrasia@Foodine :.com> 
     * @param type $postValue
     * @return int
     * @throws Exception
     */
    public function postReportTypeError($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                /*
                 * INSERT THE RECORD WITH REVIEW COMMENT AND RATE VALUE...
                 */

                $INS = array(
                    'iUserID' => $userId,
                    'iRestaurantID' => $restaurantId,
                    'isWrongPhone' => $isWrongPhone,
                    'isWrongAddress' => $isWrongAddress,
                    'isWrongMenu' => $isWrongMenu,
                    'isWrongOthers' => $isWrongOthers,
                    'iRemarks' => $iRemarks,
                    'iCreatedAt' => date('Y:m:d H:i:s'),
                );

                $this->db->insert('tbl_restaurant_report_error', $INS);

                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in postReportTypeError function - ' . $ex);
        }
    }

    /**
     * Action to get time slots
     * @package Restaurant Model
     * @access Public
     * @author 3031@Foodine :
     * @return array
     */
    function getLinkDetails($postValue) {
        $data = array();
        try {
            $data['quicklinks'] = $this->getQuickLinks();
            $data['categories'] = $this->getCategories();
            $data['handpicks'] = $this->getHandPicks($postValue);
            $data['trending'] = $this->getTrending();

            foreach ($data['categories'] AS $k => $v) {
                $data['categories'][$k]['categoryAlias'] = preg_replace("/\s+/", "-", $v['categoryName']);
            }

            foreach ($data['quicklinks'] AS $kq => $vq) {
                $data['quicklinks'][$kq]['linkAlias'] = preg_replace("/\s+/", "-", $vq['linkName']);
            }
            return $data;
        } catch (Exception $ex) {
            throw new Exception('Error in getTimeSlots function - ' . $ex);
        }
    }

    function getQuickLinks() {
        $tbl = $fields = '';
        $key = 'Quick-Links';
        $cache = $this->cache->memcached->get($key);
        if ($cache) {
            $row = $this->cache->memcached->get($key);
        } else {
            $tbl .= 'tbl_quicklinks AS tq';

            $condition[] = 'tq.eStatus IN(\'Active\')';

            $fields .= 'tq.iQuicklInkID AS linkId';
            $fields .= ', tq.vQuicklinkName AS linkName';
            $fields .= ', CONCAT("' . BASEURL . 'images/quicklinks/", IF(tq.vQuicklinkImage =\'\', "default.jpeg", CONCAT(tq.iQuicklInkID,\'/\',"thumb",\'/\',tq.vQuicklinkImage))) AS linkImage';

            $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');

            $orderBy = ' ORDER BY tq.iOrder ASC ';
            $limit = ' ';
            $groupBy = '';

            $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $condition . $groupBy . $orderBy . $limit;

            $res = $this->db->query($qry);

            $row = $res->result_array();
            $this->cache->memcached->save($key, $row);
        }
        return $row;
    }

    function getCategories() {
        $tbl = $fields = '';
        $key = 'AllCategories';
        $cache = $this->cache->memcached->get($key);
        if ($cache) {
            $row = $this->cache->memcached->get($key);
        } else {
            $tbl .= 'tbl_category AS tc';

            $condition[] = 'tc.eStatus IN(\'Active\')';

            $fields .= 'tc.iCategoryID AS categoryId';
            $fields .= ', tc.vCategoryName AS categoryName';
            $fields .= ', CONCAT("' . BASEURL . 'images/category/", IF(tc.vCategoryImage =\'\', "default.jpeg", CONCAT(tc.iCategoryID,\'/\',"thumb",\'/\',tc.vCategoryImage))) AS categoryImage';

            $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');

            $orderBy = ' ORDER BY tc.iOrder ASC ';
            $limit = ' ';
            $groupBy = '';

            $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $condition . $groupBy . $orderBy . $limit;
            //echo $qry; exit;
            $res = $this->db->query($qry);

            $row = $res->result_array();
            $this->cache->memcached->save($key, $row);
        }

        return $row;
    }

    function getHandPicks($postValue) {
        $postValue['searchType'] = 'handpicks';
        //$restIds = $this->_solrGetList($postValue);

        $tbl = $fields = $join = '';
        $tbl .= '`tbl_handpicks` as `handpicks`';

        $condition[] = 'tr.eStatus IN(\'Active\')';
        if (!empty($restIds)) {
            $restIds = implode(',', $restIds);
            $condition[] = 'tr.iRestaurantID IN(' . $restIds . ')';
        }
        $fields .= 'CONCAT("' . BASEURL . 'images/restaurantMobile/", IF(tr.vRestaurantMobLogo = \'\', "defaultdetail.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantMobLogo)) ) AS photoUrl,';
        $fields .= ' `tr`.iRestaurantID as id, `tr`.`vRestaurantName` AS restaurantName, `tr`.tAddress as address,';
       // $fields .= ' CONCAT("' . BASEURL . 'images/restaurant/", IF(`tr`.vRestaurantLogo = \'\', "default.jpeg", CONCAT(`tr`.iRestaurantID,\'/\',"thumb",\'/\',`tr`.vRestaurantLogo)) ) AS photoUrl, ';
        $fields .= ' GROUP_CONCAT(DISTINCT `tcs`.vCuisineName SEPARATOR ", ") AS cuisineType';
        $join .= ' LEFT JOIN `tbl_restaurant` AS tr ON `handpicks`.`iRestaurantID` = `tr`.`iRestaurantID`';
        $join .= ' LEFT JOIN `tbl_restaurant_cuisine` AS `trcs` ON `trcs`.`iRestaurantID` = `tr`.`iRestaurantID`';
        $join .= ' LEFT JOIN `tbl_cuisine` AS `tcs` ON `tcs`.`iCuisineID` = `trcs`.`iCuisineID`';
        //$join .= ' LEFT JOIN `tbl_location` AS `tl` ON `tl`.`iLocationID` = `tr`.`iLocationID`';
       
//        $filt_cond = $this->getFilterCondition($postValue['userId']);
//        if($filt_cond) {
//            $condition[] = $filt_cond;
//        }
        
        //print_r($condition); exit;
        $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');

        $orderBy = ' ORDER BY `tr`.`iRestaurantID` ASC ';
        $limit = ' LIMIT 5';
        $groupBy = ' GROUP BY `tr`.`iRestaurantID`';

        $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groupBy . $orderBy . $limit1;
//echo $qry; exit;
        $res = $this->db->query($qry);

        $row = $res->result_array();

        foreach ($row as $k => $rowData) {
            $restAlias = strtolower(preg_replace("/\s+/", "-", trim($rowData['restaurantName'])));
           // $row[$k]['aliasname'] = str_replace("'", "", $restAlias) . '-' . $rowData['restaurantId'];
        }
        return $row;
    }



    function getBanquetPick($postValue) {
        $postValue['searchType'] = 'handpicks';
        //$restIds = $this->_solrGetList($postValue);

        $tbl = $fields = $join = '';
        $tbl .= '`tbl_banquet_map` as `handpicks`';

        $condition[] = 'tr.eStatus IN(\'Active\')';
        if (!empty($restIds)) {
            $restIds = implode(',', $restIds);
            $condition[] = 'tr.iRestaurantID IN(' . $restIds . ')';
        }
        $fields .= 'CONCAT("' . BASEURL . 'images/restaurantMobile/", IF(tr.vRestaurantMobLogo = \'\', "defaultdetail.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantMobLogo)) ) AS photoUrl,';
        $fields .= ' `tr`.iRestaurantID as id, `tr`.`vRestaurantName` AS restaurantName, `tr`.tAddress as address,';
       // $fields .= ' CONCAT("' . BASEURL . 'images/restaurant/", IF(`tr`.vRestaurantLogo = \'\', "default.jpeg", CONCAT(`tr`.iRestaurantID,\'/\',"thumb",\'/\',`tr`.vRestaurantLogo)) ) AS photoUrl, ';
        $fields .= ' GROUP_CONCAT(DISTINCT `tcs`.vCuisineName SEPARATOR ", ") AS cuisineType';
        $join .= ' LEFT JOIN `tbl_restaurant` AS tr ON `handpicks`.`iRestaurantID` = `tr`.`iRestaurantID`';
        $join .= ' LEFT JOIN `tbl_restaurant_cuisine` AS `trcs` ON `trcs`.`iRestaurantID` = `tr`.`iRestaurantID`';
        $join .= ' LEFT JOIN `tbl_cuisine` AS `tcs` ON `tcs`.`iCuisineID` = `trcs`.`iCuisineID`';
        //$join .= ' LEFT JOIN `tbl_location` AS `tl` ON `tl`.`iLocationID` = `tr`.`iLocationID`';
       
//        $filt_cond = $this->getFilterCondition($postValue['userId']);
//        if($filt_cond) {
//            $condition[] = $filt_cond;
//        }
        
        //print_r($condition); exit;
        $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');

        $orderBy = ' ORDER BY `tr`.`iRestaurantID` ASC ';
        $limit = ' LIMIT 5';
        $groupBy = ' GROUP BY `tr`.`iRestaurantID`';

        $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groupBy . $orderBy . $limit1;
//echo $qry; exit;
        $res = $this->db->query($qry);

        $row = $res->result_array();

        foreach ($row as $k => $rowData) {
            $restAlias = strtolower(preg_replace("/\s+/", "-", trim($rowData['restaurantName'])));
           // $row[$k]['aliasname'] = str_replace("'", "", $restAlias) . '-' . $rowData['restaurantId'];
        }
        return $row;
    }

    function getFastFilling($postValue) {
        $postValue['searchType'] = 'fastfilling';
        //$restIds = $this->_solrGetList($postValue);

        $tbl = $fields = $join = '';
        $tbl .= '`tbl_fastfilling` as `handpicks`';

        $condition[] = 'tr.eStatus IN(\'Active\')';
        if (!empty($restIds)) {
            $restIds = implode(',', $restIds);
            $condition[] = 'tr.iRestaurantID IN(' . $restIds . ')';
        }

        $fields .= ' `tr`.iRestaurantID as id, `tr`.`vRestaurantName` AS restaurantName, `tr`.tAddress as address,';
        $fields .= ' CONCAT("' . BASEURL . 'images/restaurantMobile/", IF(`tr`.vRestaurantMobLogo = \'\', "default.jpeg", CONCAT(`tr`.iRestaurantID,\'/\',"thumb",\'/\',`tr`.vRestaurantMobLogo)) ) AS photoUrl, ';
        $fields .= ' GROUP_CONCAT(DISTINCT `tcs`.vCuisineName SEPARATOR ", ") AS cuisineType';
        $join .= ' LEFT JOIN `tbl_restaurant` AS tr ON `handpicks`.`iRestaurantID` = `tr`.`iRestaurantID`';
        $join .= ' LEFT JOIN `tbl_restaurant_cuisine` AS `trcs` ON `trcs`.`iRestaurantID` = `tr`.`iRestaurantID`';
        $join .= ' LEFT JOIN `tbl_cuisine` AS `tcs` ON `tcs`.`iCuisineID` = `trcs`.`iCuisineID`';
        //$join .= ' LEFT JOIN `tbl_location` AS `tl` ON `tl`.`iLocationID` = `tr`.`iLocationID`';
       

        $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');

        $orderBy = ' ORDER BY `tr`.`iRestaurantID` ASC ';
        $limit = ' LIMIT 5';
        $groupBy = ' GROUP BY `tr`.`iRestaurantID`';

        $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groupBy . $orderBy . $limit1;
//echo $qry; exit;
        $res = $this->db->query($qry);

        $row = $res->result_array();
        //echo 'hello'; exit;
      //  var_dump($postValue['userId']); exit;
        if($postValue['userId'])
        $cond = $this->getFilterCondition($postValue['userId']);
        //print_r($cond); exit;
        foreach ($row as $k => $rowData) {
          //  print_r($rowData); exit;
            if(is_array($cond)) {
                    if(!in_array($rowData['id'], $cond)) {
                        continue;
                    } else {
                        $final_arr[] = $rowData;
                    }
               } else {
                   $final_arr = $row;
               }
            $restAlias = strtolower(preg_replace("/\s+/", "-", trim($rowData['restaurantName'])));
           // $row[$k]['aliasname'] = str_replace("'", "", $restAlias) . '-' . $rowData['restaurantId'];
        }
        return $final_arr;
    }
    
    function getPopularLocation($postValue) {
        $postValue['searchType'] = 'popularLocations';
        //$restIds = $this->_solrGetList($postValue);

        $tbl = $fields = $join = '';
        $tbl .= '`tbl_popularlocation` as `handpicks`';

        $condition[] = 'tr.eStatus IN(\'Active\')';
        if (!empty($restIds)) {
            $restIds = implode(',', $restIds);
            $condition[] = 'tr.iRestaurantID IN(' . $restIds . ')';
        }
        
//        $filt_cond = $this->getFilterCondition($postValue['userId']);
//        if($filt_cond) {
//            $condition[] = $filt_cond;
//        }

        $fields .= ' `tr`.iRestaurantID as id, `tr`.`vRestaurantName` AS restaurantName, `tr`.tAddress as address,';
        $fields .= ' CONCAT("' . BASEURL . 'images/restaurantMobile/", IF(`tr`.vRestaurantMobLogo = \'\', "default.jpeg", CONCAT(`tr`.iRestaurantID,\'/\',"thumb",\'/\',`tr`.vRestaurantMobLogo)) ) AS photoUrl,';
        $fields .= ' GROUP_CONCAT(DISTINCT `tcs`.vCuisineName SEPARATOR ", ") AS cuisineType';
        $join .= ' LEFT JOIN `tbl_restaurant` AS tr ON `handpicks`.`iRestaurantID` = `tr`.`iRestaurantID`';
        $join .= ' LEFT JOIN `tbl_restaurant_cuisine` AS `trcs` ON `trcs`.`iRestaurantID` = `tr`.`iRestaurantID`';
        $join .= ' LEFT JOIN `tbl_cuisine` AS `tcs` ON `tcs`.`iCuisineID` = `trcs`.`iCuisineID`';
        //$join .= ' LEFT JOIN `tbl_location` AS `tl` ON `tl`.`iLocationID` = `tr`.`iLocationID`';
       

        $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');

        $orderBy = ' ORDER BY `tr`.`iRestaurantID` ASC ';
        $limit = ' LIMIT 5';
        $groupBy = ' GROUP BY `tr`.`iRestaurantID`';

        $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groupBy . $orderBy . $limit1;
//echo $qry; exit;
        $res = $this->db->query($qry);

        $row = $res->result_array();
        $cond = $this->getFilterCondition($postValue['userId']);
        foreach ($row as $k => $rowData) {
              
            if(is_array($cond)) {
                    if(!in_array($rowData['id'], $cond)) {
                        continue;
                    } else {
                        $final_arr[] = $rowData;
                    }
               } else {
                   $final_arr = $row;
               }
            $restAlias = strtolower(preg_replace("/\s+/", "-", trim($rowData['restaurantName'])));
           // $row[$k]['aliasname'] = str_replace("'", "", $restAlias) . '-' . $rowData['restaurantId'];
        }
        return $final_arr;
    }
    
    function getSelectedIdList($key, $source) {
        if(count($source)) {
            foreach($source as $line) {
                if($line['selected'] == 'true') {
                    $ret_arr[] = $line[$key];
                }
            }
            
            return $ret_arr;
        }
        
        return false;
    }
    
    function getRestaurantHaving($list, $tbl_name, $cond) {
        $query = 'SELECT tr.`iRestaurantID` AS Id FROM '.$tbl_name.' as tr WHERE '.$cond.' IN('.implode($list,",").') group by irestaurantid';
       // echo $query; exit;
        $res = $this->db->query($query);
        
        if ($res->num_rows() > 0) {
            foreach ($res->result_array() as $rowData) {
              $in_cond[] = $rowData['Id'];      
            }
            
            return $in_cond;
        }
        
        return array();
    }
    
    function  getFilterCondition($userId) {
       if($userId) {
        $this->load->model('user_model');
        $cuisine = $this->user_model->getCIMUserArray('cuisine', @$userId);
        $interest = $this->user_model->getCIMUserArray('interest', @$userId);
        //print_r($interest); exit;
        $cuisine_arr = $this->getSelectedIdList('id', $cuisine);
        $interest_arr = $this->getSelectedIdList('id', $interest);
        //var_dump($interest_arr); exit;
        if(count($cuisine_arr)) {
             $cuisine_in = $this->getRestaurantHaving($cuisine_arr,'tbl_restaurant_cuisine','icuisineid');
        }
       if(count($interest_arr)) {
             $interest_in = $this->getRestaurantHaving($interest_arr,'tbl_restaurant_facility','ifacilityid');
       }
       //var_dump($cuisine_in); exit;0
       //var_dump($interest_in); exit;
        $join_cond = '';
        $cond = '';
            if(count($cuisine_in) || count($interest_in)) {
                if(count($interest_in) && count($cuisine_in)) {
                    $final_filter = array_merge($cuisine_in, $interest_in);
                } else if(count($interest_in)) {
                    $final_filter = $interest_in;
                } else if(count($cuisine_in)){
                    $final_filter = $cuisine_in;
                } else {
                    $final_filter = '';
                }
            //   var_dump($final_filter); exit;
                if($final_filter) {
                  //  $final_filter = implode(array_unique($final_filter),",");
                 //   $cond.=' tr.irestaurantid IN('.trim($final_filter, ",").')';
                     $final_filter = array_unique($final_filter);
                } else {
                    return false;
                }

            }
        } 
        else
        {
            return false;
        }
        return  $final_filter;
    }
    
    function getFeaturedRestaurant($userId, $userLat, $userLong, $limit = '100') {
        $cond = $this->getFilterCondition($userId);
        
        
        $query = 'SELECT tr.`iRestaurantID` AS Id, tr.vRestaurantName, IF(tr.eFeatured=\'yes\',\'yes\',"") AS eFeatured FROM tbl_restaurant AS tr '.$join_cond.' WHERE tr.eStatus = \'Active\' and tr.eFeatured = "yes" ORDER BY eFeatured DESC, tr.vRestaurantName ASC limit '.$limit;
        $res = $this->db->query($query);
        //var_dump($cond); exit;
        if ($res->num_rows() > 0) {
            foreach ($res->result_array() as $rowData) {
                
               if(is_array($cond)) {
                    if(!in_array($rowData['Id'], $cond)) {
                        continue;
                    }
               }
              $resDetail = $this->getRestaurantDetail($rowData['Id'], $userLat, $userLong, $userId, 'web');
             
              $resRet['id'] = $resDetail['info']['restaurantId'];
              $resRet['rating'] = $resDetail['info']['restaurantRatting'];
              $resRet['bookmarked'] = $resDetail['info']['isFavourite'] == 'no' ? "false" : "true" ;
              $resRet['distance'] = $resDetail['info']['restaurantDistance'];
              $resRet['address'] = $resDetail['info']['restaurantAddress'];
              $resRet['restaurantName'] = $resDetail['info']['restaurantName'];
              $resRet['dishName'] = $resDetail['info']['restaurantSpeciality']['food'];
              $resRet['photoUrl'] = $resDetail['info']['restaurantImage'];
              $resRet['type'] = $resDetail['info']['isBanquet'] ? "banquet": '';
              
              $resRet['offers'] = array();
              foreach($resDetail['restaurantDeals'] as $line) {
                 // print_r($line);
                  $resRet['offers'][]= array('description' => $line['offerText'], 'offerCode' => $line['dealCode']);
              }
              //exit;
              $res_list[] = $resRet;
                // print_r($resDetail); exit;
            }
        } else {
            return array();
        }
        
        return $res_list;
    }
    
    function getTrending() {
        $tbl = $fields = $join = '';
        //$tbl .= '`tbl_restaurant_trending` AS `trd`';
        $tbl .= '`tbl_banner` AS `trd`';
       // getRestaurantDetail
        $condition[] = 'trd.eType IN(\'featured\')';
        $condition[] = 'tr.eStatus IN(\'Active\')';
        $condition[] = 'trd.tStartDate <= NOW()';
        $condition[] = 'trd.tEndDate >= NOW()';

        $fields .= ' `tr`.iRestaurantID as restaurantId, `tr`.`vRestaurantName` AS restaurantName,';
        $fields .= ' `tr`.tAddress as address, TRUNCATE(AVG(`trr`.iRateValue), 1) AS rating,7.5 as distance,true as bookmarked,';
        $fields .= ' CONCAT("' . BASEURL . 'images/restaurant/", IF(`tr`.vRestaurantLogo = \'\', "default.jpeg", CONCAT(`tr`.iRestaurantID,\'/\',`tr`.vRestaurantLogo)) ) AS restaurantImage ';
        
        
        
        $join .= ' LEFT JOIN `tbl_restaurant` AS `tr` ON `tr`.`iRestaurantID` = `trd`.`iTypeId`';
        $join .= ' LEFT JOIN `tbl_user_ratting` AS `trr` ON `trr`.`iRestaurantID` = `trd`.`iTypeId`';
        
        

        $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');

        $orderBy = ' ORDER BY count DESC ';
        //$limit = ' LIMIT 6';
        // $groupBy = ' GROUP BY `trd`.`iRestaurantID`';
        $groupBy = ' ';

        $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition;

        $res = $this->db->query($qry);

        $row = $res->result_array();

        foreach ($row as $k => $rowData) {
            $restAlias = strtolower(preg_replace("/\s+/", "-", trim($rowData['restaurantName'])));
            $row[$k]['aliasname'] = str_replace("'", "", $restAlias) . '-' . $rowData['restaurantId'];
            ;
        }

        return $row;
    }

    public function escapeSpecialCharString($str = '') {
        $indexLimit = array('\\', "'", '"', '%', '&', '$', '?', '(', ')', '[', ']', '{', '}',
            '^', '#', '!', '/', ';', '.', '<', '>', ' ', ':'
        );
        $totalRowsToIndex = array('\\\\', "\'", '\"', '%25', '%26', '\$', '\?', '\(', '\)', '\[',
            '\]', '\{', '\}', '\^', '\#', '\!', '\/', '\;', '\.', '\<', '\>', '*', '\:'
        );
        $string = str_replace($indexLimit, $totalRowsToIndex, urldecode(trim($str)));
        return $string;
    }

    public function getAllCategories() {
        $data = array();
        try {
            $data['categories'] = $this->getCategories();
            $data['quicklinks'] = $this->getQuickLinks();
            foreach ($data['categories'] AS $k => $v) {
                $data['categories'][$k]['categoryAlias'] = preg_replace("/\s+/", "-", $v['categoryName']);
            }
            foreach ($data['quicklinks'] AS $kq => $vq) {
                $data['quicklinks'][$kq]['linkAlias'] = preg_replace("/\s+/", "-", $vq['linkName']);
            }
            return $data;
        } catch (Exception $ex) {
            throw new Exception('Error in getAllCategories function - ' . $ex);
        }
    }

    /*
     * Method to return all locations from location master table (tbl_location). 
     * 
     * @return array data with location listing
     * @author Anurag Srivastava (anurag.srivastava@Foodine :.com)
     */

    public function getAllLocations($postValues) {
        try {
            if (!empty($postValues)) {
                extract($postValues);
                $tbl = $fields = '';
                $tbl .= '`tbl_restaurant` as `tr`, `tbl_location` AS `tl`, `tbl_location_zone` AS `tlz`,`tbl_state` AS `ts`';
                $fields .= '`tl`.iLocationID  as id,  `tl`.`vLocationName` AS locationName, `tlz`.vZoneName as cityName, `ts`.vStateName as stateName, `tr`.vLat as latitude, `tr`.vLog as longitude';
                $condition = ' WHERE `tl`.iLocationID = `tr`.iLocationID AND `tl`.iLocZoneID = `tlz`.iLocZoneID AND `tlz`.iStateID = `ts`.iStateID and tr.eStatus="Active" and (lower(`tl`.`vLocationName`) LIKE "%' . strtolower($searchText) . '%" OR lower(`tlz`.`vZoneName`) LIKE "%' . strtolower($searchText) . '%")';
                $groupby = ' Group By  `tr`.iLocationID,`tl`.vLocationName';
                $orderBy = ' Order By  `tlz`.vZoneName';
                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $condition . $groupby . $orderBy;
                $res = $this->db->query($qry);
                $row = $res->result_array();
                if (isset($onlyLocation) && trim($onlyLocation) == 1)
                    return $row;

                $qry1 = 'SELECT DISTINCT `tlz`.vZoneName as city from tbl_location_zone as tlz where lower(`tlz`.`vZoneName`) LIKE "%' . strtolower($searchText) . '%" and tlz.eStatus="Active"';
                $res1 = $this->db->query($qry1);
                $row1 = $res1->result_array();
                $cityArray = array();
                foreach ($row1 as $k => $v) {
                    $cityArray[] = $v['city'];
                }
                $i = 0;
                foreach ($row as $key => $val) {
                    if (!in_array($val['cityName'], $cityArr) && in_array($val['cityName'], $cityArray)) {
                        $cityArr[] = $val['cityName'];
                        $cityArray = array();
                        $cityArray['id'] = 0;
                        $cityArray['cityName'] = $val['cityName'];
                        $cityArray['locationName'] = '';
                        $cityArray['stateName'] = $val['stateName'];
                        $cityArray['latitude'] = $val['latitude'];
                        $cityArray['longitude'] = $val['longitude'];
                        $row1[$i++] = $cityArray;
                    }
                    if ($val['cityName'] != '') {
                        $row1[$i++] = $val;
                    }
                }
                return $row1;
            }
            return false;
        } catch (Exception $ex) {
            throw new Exception('Error in getExpertReviews function - ' . $ex);
        }
    }

    /*
     * Method to return all filters which can be applied on restaurant. 
     * 
     * @return array data with location listing
     * @author Anurag Srivastava (anurag.srivastava@Foodine :.com)
     */

    public function getAllRestaurantFilter($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
            }

            // Setting sort by manually
            $resp['sortBy'] = array('dist' => 'Distance', 'rate' => 'Rating', 'cost' => 'Cost', 'pop' => 'Popularity');

            // Setting service provided filter manually
            //$resp['filters']['serviceProvided'] = array('booking' => 'Booking Available', 'order' => 'Order Online');
            $resp['filters']['serviceProvided'] = array('booking' => 'Booking Available');

            // Setting price filter manually
            $maxpriceqry = 'SELECT max(iMaxPrice) as maxPrice FROM `tbl_restaurant`';
            $res = $this->db->query($maxpriceqry);
            $maxpriceres = $res->row_array();
            $maxPrice = (int) $maxpriceres['maxPrice'];
            $resp['filters']['price'] = array('minPrice' => 0, 'maxPrice' => $maxPrice);

            // Fetching all active cuisines
            $key1 = 'All-Active-Cuisines';
            $cache1 = $this->cache->memcached->get($key1);
            if ($cache1) {
                $resp['filters']['cuisine'] = $this->cache->memcached->get($key1);
            } else {
                $sql = 'select iCuisineID as id,vCuisineName as name from tbl_cuisine where eStatus = "Active"';
                $res = $this->db->query($sql);
                $allActiveCuisines = $res->result_array();
                $resp['filters']['cuisine'] = $allActiveCuisines;
                $this->cache->memcached->save($key1, $allActiveCuisines);
            }

            // Fetching offer type filter
            $key2 = 'All-Active-Offer-Types';
            $cache2 = $this->cache->memcached->get($key2);
            if ($cache2) {
                $resp['filters']['offerType'] = $this->cache->memcached->get($key2);
            } else {
                $offersql = "SELECT offerTypeId as id, offerTypeName as name FROM tbl_offer_type WHERE status = 'Active' ";
                $offerres = $this->db->query($offersql);
                $allActiveOffers = $offerres->result_array();
                $resp['filters']['offerType'] = $allActiveOffers;
                $this->cache->memcached->save($key2, $allActiveOffers);
            }

            // Fetching establishment type filter
            $key3 = 'All-Active-Quick-Links';
            $cache3 = $this->cache->memcached->get($key3);
            if ($cache3) {
                $resp['filters']['establishment'] = $this->cache->memcached->get($key3);
            } else {
                $sql = 'select iQuicklinkID as id,vQuicklinkName as name from tbl_quicklinks where eStatus = "Active"';
                $res = $this->db->query($sql);
                $allActiveEstablishments = $res->result_array();
                $resp['filters']['establishment'] = $allActiveEstablishments;
                $this->cache->memcached->save($key3, $allActiveEstablishments);
            }

            // Fetching facilities type filter
            $key4 = 'All-Active-Facilities';
            $cache4 = $this->cache->memcached->get($key4);
            if ($cache4) {
                $resp['filters']['facilities'] = $this->cache->memcached->get($key4);
            } else {
                $sql = 'select iFacilityID as id,vFacilityName as name from tbl_facility where eStatus = "Active"';
                $res = $this->db->query($sql);
                $allActiveFacilities = $res->result_array();
                $resp['filters']['facilities'] = $allActiveFacilities;
                $this->cache->memcached->save($key4, $allActiveFacilities);
            }


            // Fetching localities
            $tbl = $fields = $join = '';
            $tbl .= '`tbl_restaurant` AS `tr`';
            $tbl .= ' ,`tbl_location` AS `tl`';
            $condition[] = 'tl.iLocationID = tr.iLocationID';
            $condition[] = 'tr.eStatus IN(\'Active\')';
            $condition[] = 'tl.eStatus IN(\'Active\')';
            if (isset($zoneId) && !empty($zoneId)) {
                if (isset($zoneId) && !empty($zoneId)) {
                    if ($zoneId == 10) {
                        $zoneId = '1,2,3,4,5,6,7,8,9';
                    } else if ($zoneId == 9) {
                        $zoneId = '1,2,3,4,5,9';
                    } else {
                        $zoneId = $zoneId;
                    }
                    $condition[] = "tl.iLocZoneID IN($zoneId)";
                }
            }
            $groubBy = ' GROUP BY tr.iLocationID';
            $orderBy = ' ORDER BY tl.vLocationName ASC';
            $fields .= ' `tr`.iLocationID as id, `tl`.`vLocationName` AS name';
            $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');
            $localitysql = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groubBy . $orderBy;
            $localityres = $this->db->query($localitysql);
            $allActiveLocalities = $localityres->result_array();

            $resp['filters']['locality'] = $allActiveLocalities;


            // Fetching categories filter

            $key5 = 'All-Active-Categories';
            $cache5 = $this->cache->memcached->get($key5);
            if ($cache5) {
                $resp['filters']['categories'] = $this->cache->memcached->get($key5);
            } else {
                $sql = 'select iCategoryID as id,vCategoryName as name from tbl_category where eStatus = "Active"';
                $res = $this->db->query($sql);
                $allActiveCategories = $res->result_array();
                $resp['filters']['categories'] = $allActiveCategories;
                $this->cache->memcached->save($key5, $allActiveCategories);
            }
            return $resp;

            //
        } catch (Exception $ex) {
            // If execution stop unexpectedly send blank array
            return array();
        }
    }

    function getRestaurantTimeSlots($restaurantID) {
        try {
            if (!empty($restaurantID)) {
                $tbl = $fields = $join = '';
                $tbl .= '`tbl_restaurant` AS `tr`';
                $condition[] = 'tr.iRestaurantID =' . $restaurantID;
                $fields .= ' `tr`.iMinTime as minTime, `tr`.`iMaxTime` AS maxTime';
                $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');
                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition;
                $res = $this->db->query($qry);
                $row = $res->row_array();
                return $row;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in postReportError function - ' . $ex);
        }
    }

    private function _getRestaurantComboOffers($restaurantID) {
        try {
            $orderBy = '';
            $result = array();
            $tbl = $fields = $join = $groupBy = '';
            $tbl .= '`tbl_combo_offers` AS `tco`';
            //$tbl .= ', tbl_location AS tc';
            if (!empty($restaurantID)) {
                $condition[] = 'tco.iRestaurantID =' . $restaurantID;
            }

            $condition[] = 'tco.eStatus = "Active"';
            $condition[] = 'CURDATE() between tco.dtStartDate and tco.dtExpiryDate';
            $condition[] = 'tr.eStatus IN(\'Active\')';

            $fields .= 'tco.iComboOffersID, tco.iRestaurantID, tr.vRestaurantName as  restaurantName, tr.vCityName as city, tco.vOfferText, tcso.tActualPrice, tcso.tDiscountedPrice, tco.vDaysAllow, tco.dtStartDate, tco.dtExpiryDate';
            //$fields .= ', CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage =\'\', "default.jpeg", CONCAT(`tco`.iRestaurantID,\'/\',`tco`.vOfferImage))) AS vOfferImage';
            $fields .= ', IF(tco.vOfferImage =\'\',CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ), CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage =\'\', "default.jpeg", CONCAT(`tco`.iRestaurantID,\'/thumb/\',`tco`.vOfferImage)))) AS vOfferImage';
            $fields .= ', tc.vLocationName AS location';
            $join .= ' INNER JOIN `tbl_combo_sub_offers` AS `tcso` ON `tcso`.`iComboOffersID` = `tco`.`iComboOffersID`';
            $join .= ' INNER JOIN `tbl_restaurant` as `tr` on  `tco`.`iRestaurantID` = `tr`.`iRestaurantID`';
            $join .= ' INNER JOIN `tbl_location` as `tc` on  `tc`.`iLocationID` = `tr`.`iLocationID`';
            $groupBy = ' GROUP BY tco.iComboOffersID ';
            $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');

            $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groupBy . $orderBy;

            $res = $this->db->query($qry);
            $row = $res->result_array();

            $fields .= ', tcso.vOfferTitle as subOfferTitle, tcso.tOfferDetail as subOfferDetail, tcso.iComboSubOffersID as subOfferId ';
            $sqry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $orderBy;
            $sres = $this->db->query($sqry);
            $srow = $sres->result_array();

            $dayArray = array(
                '1' => 'Sunday',
                '2' => 'Monday',
                '3' => 'Tuesday',
                '4' => 'Wednesday',
                '5' => 'Thursday',
                '6' => 'Friday',
                '7' => 'Saturday'
            );
            $currentDay = array_search(date('l'), $dayArray);

            foreach ($row as $key => $value) {
                $days = explode(',', $value['vDaysAllow']);
                $perDiscount = round(((($row[$key]['tActualPrice'] - $row[$key]['tDiscountedPrice']) * 100) / $row[$key]['tActualPrice']), 0);
                $row[$key]['perDiscount'] = $perDiscount;
                if (in_array($currentDay, $days)) {
                    $result['today'][] = $row[$key];
                }
            }
            $result['all'] = $row;
            $result['allsuboffer'] = $srow;
            if (!empty($restaurantID)) {
                $result['resID'] = $restaurantID;
                $query = 'SELECT vRestaurantName from tbl_restaurant where iRestaurantID =' . $restaurantID;
                $res = $this->db->query($query);
                $row = $res->row_array();
                $result['restaurantName'] = isset($row['vRestaurantName']) ? $row['vRestaurantName'] : '';
            }

            return $result;
            return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in postReportError function - ' . $ex);
        }
    }

    function getRestaurantComboOffers($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
            }
            $userLat = $latitude;
            $userLong = $longitude;
            
            $distanceType = 'km';
            $multiplyer = 6371 * 1000;
            $maxDistance = 160.934 * 1000; //offers in 100 miles
            if ($distanceType == 'miles') {
                $multiplyer = 3959;
            }
            $orderBy = '';
            $result = array();
            $tbl = $fields = $join = $groupBy = '';
            $tbl .= '`tbl_combo_offers` AS `tco`';
            if (!empty($restaurantId)) {
                $condition[] = 'tco.iRestaurantID =' . $restaurantId;
            }

            $condition[] = 'tco.eStatus = "Active"';
            $condition[] = 'CURDATE() between tco.dtStartDate and tco.dtExpiryDate';
            $condition[] = 'tr.eStatus IN(\'Active\')';


            if (!empty($minPrice) && !empty($maxPrice)) {
                $condition[] = 'tcso.tDiscountedPrice >= ' . $minPrice . ' AND tcso.tDiscountedPrice <= ' . $maxPrice;
            } else if (!empty($maxPrice) && empty($minPrice)) {
                $condition[] = 'tcso.tDiscountedPrice <= ' . $maxPrice;
            } else if (!empty($minPrice) && empty($maxPrice)) {
                $condition[] = 'tcso.tDiscountedPrice >= ' . $minPrice;
            }

            if (isset($person) && $person !== '') {
                $condition[] = 'tcso.people = ' . $person;
            }

            if (isset($userLat) && !empty($userLat) && isset($userLong) && !empty($userLong)) {
                $orderBy = ' ORDER BY distance ASC';
            }

            if (isset($filterLocation) && $filterLocation !== '') {
                $condition[] = 'tr.iLocationID IN(' . $filterLocation . ')';
            }

            if (isset($filterSortBy)) {
                /*
                 * SET SORT BY VALUE
                 */
                // If order is not defined
                if (!isset($costorder) || empty($costorder))
                    $costorder = 'DESC';

                switch ($filterSortBy) {
                    case 'cost':
                        $orderBy = ' ORDER BY tcso.tDiscountedPrice ' . $costorder;
                        break;

                    default :
                        if (isset($userLat) && !empty($userLat) && isset($userLong) && !empty($userLong)) {
                            $orderBy = ' ORDER BY distance ASC';
                        } else {
                            $orderBy = ' ORDER BY tcso.tDiscountedPrice ASC';
                        }
                        break;
                }
            }

            $fields .= 'tco.iRestaurantID as id, tr.vRestaurantName as  restaurantName, tco.vOfferText as description, CONCAT(tr.vCityName,\', \',tc.vLocationName) as address,  tcso.tActualPrice as restaurantPriceValue, tcso.tDiscountedPrice discountedPrice';
            //$fields .= ', DATE_FORMAT(tco.dtExpiryDate,"%d-%m-%Y") AS dtExpiryDate';
            //$fields .= ', CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage =\'\', "default.jpeg", CONCAT(`tco`.iRestaurantID,\'/\',`tco`.vOfferImage))) AS vOfferImage';
            $fields .= ', IF(tco.vOfferImage =\'\',CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ), CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage =\'\', "default.jpeg", CONCAT(`tco`.iRestaurantID,\'/thumb/\',`tco`.vOfferImage)))) AS photoUrl';
            //$fields .= ', tc.vLocationName AS location, DATE_FORMAT(tco.dtExpiryDate,"%h:%i %p") AS exipryTime';
            $fields .= ', (SELECT count(*) from tbl_user_combo AS tuc where iComboOffersID = tco.iComboOffersID) AS broughtCount';
           // $fields .= ', tr.vLat AS restaurantLatitude';
           // $fields .= ', tr.vLog AS restaurantLongitude';
            if (isset($userLat) && !empty($userLat) && isset($userLong) && !empty($userLong)) {
                $fields .= ', (ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                        . ' * cos( radians( vLat) )'
                        . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                        . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) ))'
                        . ' AS distance';
                $condition[] = '(ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                        . ' * cos( radians( vLat) )'
                        . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                        . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) )) < ' . $maxDistance;
            }

            if (isset($zone) && !empty($zone)) {
                if ($zone == 10) {
                    $zoneId = '1,2,3,4,5,6,7,8,9';
                } else if ($zone == 9) {
                    $zoneId = '1,2,3,4,5,9';
                } else {
                    $zoneId = $zone;
                }
                $condition[] = "tc.iLocZoneID IN($zoneId)";
            }

            $join .= ' INNER JOIN `tbl_combo_sub_offers` AS `tcso` ON `tcso`.`iComboOffersID` = `tco`.`iComboOffersID`';
            $join .= ' INNER JOIN `tbl_restaurant` as `tr` on  `tco`.`iRestaurantID` = `tr`.`iRestaurantID`';
            $join .= ' INNER JOIN `tbl_location` as `tc` on  `tc`.`iLocationID` = `tr`.`iLocationID`';
            $groupBy = ' GROUP BY tco.iComboOffersID ';

            $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');


            if (isset($pageId)) {

                /* PAGINATION */
                $zeroPageRecord = 10;
                $otherPageRecord = 11;
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
            }

            $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groupBy . $orderBy . $limit;
            $res = $this->db->query($qry);
            $row = $res->result_array();
            $countBy = 'tco.iComboOffersID';
            $qry1 = 'SELECT COUNT(DISTINCT(' . $countBy . ')) AS totalRows FROM ' . $tbl . $join . $condition;

            $countRes = $this->db->query($qry1);
            $countRec = $countRes->row_array();
            $countRec = (int) $countRec['totalRows'];

            $totalPage = 0;
            if (isset($pageId)) {
                $totalPage = 1 + (floor($countRec / $otherPageRecord));
            }

            $dayArray = array(
                '1' => 'Sunday',
                '2' => 'Monday',
                '3' => 'Tuesday',
                '4' => 'Wednesday',
                '5' => 'Thursday',
                '6' => 'Friday',
                '7' => 'Saturday'
            );
            $currentDay = array_search(date('l'), $dayArray);
            foreach ($row as $key => $value) {
                $days = explode(',', $value['vDaysAllow']);
                $perDiscount = round(((($row[$key]['tActualPrice'] - $row[$key]['tDiscountedPrice']) * 100) / $row[$key]['tActualPrice']), 0);
//                $row[$key]['bought'] = $value['totalBuy'];
                $row[$key]['discountPercent'] = $perDiscount;
                if (in_array($currentDay, $days)) {
                    //$result['today'][] = $row[$key];
                }
               // print_r($value);
                if(!array_key_exists($value['Id'], $main_arr)) {
                    unset($value['distance']);
                    $value['rating'] = $this->_getRating($value['Id']);
                    $main_arr[$value['Id']] = $value; 
                    $main_arr[$value['Id']]['offers'][] = array('description'=>$value['description']);
                } else {
                    $main_arr[$value['Id']]['offers'][] = array('description'=>$value['description']);
                }
            }
           // print_r($main_arr); exit;
            $result = array_values($main_arr);
            if (!empty($restaurantId)) {
                $result['resID'] = $restaurantId;
                $query = 'SELECT vRestaurantName from tbl_restaurant where iRestaurantID =' . $restaurantId;
                $res = $this->db->query($query);
                $row = $res->row_array();
                $result['restaurantName'] = isset($row['vRestaurantName']) ? $row['vRestaurantName'] : '';
            }

          //  $result['banner'] = $this->getBanner("combo");
//
//            $result['totalRecord'] = $countRec;
//            $result['totalPage'] = $totalPage;
            return $result;
            return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in postReportError function - ' . $ex);
        }
    }

    private function _getComboMenuImage($comboId) {
        $qry = 'SELECT vPictureName AS imageName FROM tbl_offer_menu_image WHERE iComboOffersID = \'' . $comboId . '\' and eStatus = "Active"';
        $res = $this->db->query($qry);
        $row = $res->result_array();
        $arry = array();
        //Restaurant Photos
        for ($i = 0; $i < count($row); $i++) {
            $arry[] = BASEURL . 'images/comboOfferMenu/' . $comboId . '/' . $row[$i]['imageName'];
        }
        return $arry;
    }

    function getRestaurantSubComboOffers($postValue) {

        try {
            if (!empty($postValue)) {
                extract($postValue);
                $result = array();
                $tbl = $fields = $join = $groupBy = '';
                $tbl .= '`tbl_combo_offers` AS `tco`';
                //$condition[] = 'tco.iRestaurantID =' . $restaurantId;
                $condition[] = 'CURDATE() between tco.dtStartDate and tco.dtExpiryDate';
                $condition[] = 'tco.eStatus = "Active"';
                $condition[] = 'tco.iComboOffersID =' . $comboOfferID;
                $fields .= 'tco.iComboOffersID, tco.iRestaurantID, tr.vRestaurantName as  restaurantName, tco.vOfferText,tco.tTermsOfUse, tcso.tActualPrice, tcso.tDiscountedPrice, tco.vDaysAllow, tco.dtStartDate, tco.dtExpiryDate';
                $fields .= ',tcso.iComboSubOffersID, tcso.vOfferTitle, tcso.tOfferDetail';
                //$fields .= ', CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage =\'\', "default.jpeg", CONCAT(`tco`.iRestaurantID,\'/\',`tco`.vOfferImage))) AS vOfferImage';
                $fields .= ', IF(tco.vOfferImage =\'\',CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ), CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage =\'\', "default.jpeg", CONCAT(`tco`.iRestaurantID,\'/thumb/\',`tco`.vOfferImage)))) AS vOfferImage';
                $join .= ' INNER JOIN `tbl_combo_sub_offers` AS `tcso` ON `tcso`.`iComboOffersID` = `tco`.`iComboOffersID`';
                $join .= ' INNER JOIN `tbl_restaurant` as `tr` on  `tco`.`iRestaurantID` = `tr`.`iRestaurantID`';
                $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');
                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groupBy;

                $res = $this->db->query($qry);
                $row = $res->result_array();
                $dayArray = array(
                    '1' => 'Sunday',
                    '2' => 'Monday',
                    '3' => 'Tuesday',
                    '4' => 'Wednesday',
                    '5' => 'Thursday',
                    '6' => 'Friday',
                    '7' => 'Saturday'
                );

                foreach ($row as $key => $value) {
                    if ($key == '0') {
                        $result['iComboOffersID'] = $value['iComboOffersID'];
                        $result['iRestaurantID'] = $value['iRestaurantID'];
                        $result['restaurantName'] = $value['restaurantName'];
                        $result['vOfferText'] = $value['vOfferText'];
                        $result['tTermsOfUse'] = $value['tTermsOfUse'];
                        $result['vOfferImage'] = $value['vOfferImage'];
                        $result['iMinTime'] = $value['iMinTime'];
                        $result['iMaxTime'] = $value['iMaxTime'];
                        $result['dtExpiryDate'] = date('d/m/Y', strtotime($value['dtExpiryDate']));
                        $result['images'] = $this->_getComboMenuImage($value['iComboOffersID']);
                        $days = explode(',', $value['vDaysAllow']);
                        foreach ($days as $key => $value) {
                            $days[$key] = $dayArray[$value];
                        }
                        $result['vDaysAllow'] = implode(', ', $days);
                    } else {
                        break;
                    }
                }
                foreach ($row as $k => $v) {
                    $perDiscount = round(((($v['tActualPrice'] - $v['tDiscountedPrice']) * 100) / $v['tActualPrice']), 0);
                    $row[$k]['perDiscount'] = $perDiscount;
                }
                $result['data'] = $row;

                if (!empty($restaurantId)) {
                    $result['resID'] = $restaurantId;
                    $query = 'SELECT vRestaurantName from tbl_restaurant where iRestaurantID =' . $restaurantId;
                    $res = $this->db->query($query);
                    $row = $res->row_array();
                    $result['restaurantName'] = isset($row['vRestaurantName']) ? $row['vRestaurantName'] : '';
                }
                return $result;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in postReportError function - ' . $ex);
        }
    }

    function getSubComboOfferDetail($subOfferId) {
        try {
            if (!empty($subOfferId)) {
                $tbl = $fields = $join = $groupBy = '';
                $tbl .= '`tbl_combo_sub_offers` AS `tcso`';
                $condition[] = 'tcso.iComboSubOffersID = ' . $subOfferId;
                $fields[] = 'tcso.*';
                $fields[] = 'DATE_FORMAT(tco.dtExpiryDate,"%d/%m/%Y") AS dtExpiryDate';
                //$fields[] = 'CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage =\'\', "default.jpeg", CONCAT(`tco`.iRestaurantID,\'/\',`tco`.vOfferImage))) AS offerImage';
                $fields[] = 'IF(tco.vOfferImage =\'\',CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ), CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage =\'\', "default.jpeg", CONCAT(`tco`.iRestaurantID,\'/thumb/\',`tco`.vOfferImage)))) AS offerImage';
                $fields[] = 'tco.vOfferText AS offerText';
                $fields[] = 'tco.iRestaurantID AS restId';
                $fields[] = 'tco.vDaysAllow AS vDaysAllow';
                $fields[] = 'tco.tTermsOfUse AS tTermsOfUse';
                $fields = implode(',', $fields);
                $join .= ' LEFT JOIN `tbl_combo_offers` AS `tco` ON `tcso`.`iComboOffersID` = `tco`.`iComboOffersID`';
                $join .= ' LEFT JOIN `tbl_restaurant` AS `tr` ON `tr`.`iRestaurantID` = `tco`.`iRestaurantID`';
                $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');
                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groupBy;
                $res = $this->db->query($qry);
                $row = $res->row_array();
                $dayArray = array(
                    '1' => 'Sunday',
                    '2' => 'Monday',
                    '3' => 'Tuesday',
                    '4' => 'Wednesday',
                    '5' => 'Thursday',
                    '6' => 'Friday',
                    '7' => 'Saturday'
                );

                $days = explode(',', $row['vDaysAllow']);
                foreach ($days as $key => $value) {
                    $days[$key] = $dayArray[$value];
                }
                $row['vDaysAllow'] = implode(', ', $days);
                $perDiscount = round(((($row['tActualPrice'] - $row['tDiscountedPrice']) * 100) / $row['tActualPrice']), 0);
                $row['perDiscount'] = $perDiscount;
                $row['images'] = $this->_getComboMenuImage($row['iComboOffersID']);
                return $row;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in postReportError function - ' . $ex);
        }
    }

    public function savecomboCartDetail($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                /*
                 * INSERT THE RECORD WITH REVIEW COMMENT AND RATE VALUE...
                 */
                $INS = array(
                    'iRestaurantID' => $iRestaurantID,
                    'iUserID' => $iUserID,
                    'iComboSubOffersID' => $iComboSubOffersID,
                    'iComboOffersID' => $iComboOffersID,
                    'tDiscountedPrice' => $tDiscountedPrice,
                    'qty' => $qty,
                    'dtExpiryDate' => $dtExpiryDate,
                    'vDaysAllow' => $vDaysAllow,
                    'iTotal' => $iTotal,
                    'vUserName' => $vUserName,
                    'vMobileNo' => $vMobileNo,
                    'eBookingStatus' => 'cart',
                    'tCreatedAt' => date('Y:m:d H:i:s')
                );
                $this->db->insert('tbl_user_combo', $INS);
                return $this->db->insert_id();
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in savecomboCartDetail function - ' . $ex);
        }
    }

    function sponseredRestaurant($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
            }
            $tbl[] = 'tbl_restaurant AS tr';
            $tbl[] = 'tbl_restaurant_sponsered AS tds';
            $tbl[] = 'tbl_location AS tl';
            $fields[] = 'tr.iRestaurantID AS restaurantId';
            $fields[] = 'tr.vRestaurantName AS restaurantName';
            $fields[] = 'tr.vStateName AS state';
            $fields[] = 'tr.vCityName AS city';
            $fields[] = 'tl.vLocationName AS locality';
            $fields[] = 'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage';
            $fields[] = 'tr.tAddress as address';
            $fields[] = 'DATE_FORMAT(tds.dtStartDate,"' . MYSQL_DATE_FORMAT . '") AS sponserStartDate';
            $fields[] = 'DATE_FORMAT(tds.dtExpiryDate,"' . MYSQL_DATE_FORMAT . '") AS sponserEndDate';

            $condition[] = 'tds.eStatus = \'Active\'';
            $condition[] = 'tr.eStatus = \'Active\'';
            $condition[] = 'tds.iRestaurantID = tr.iRestaurantID';
            $condition[] = 'tl.iLocationID = tr.iLocationID';

            if (isset($zone) && !empty($zone)) {
                if ($zone == 10) {
                    $zoneId = '1,2,3,4,5,6,7,8,9';
                } else if ($zone == 9) {
                    $zoneId = '1,2,3,4,5,9';
                } else {
                    $zoneId = $zone;
                }
                $condition[] = "tl.iLocZoneID IN($zoneId)";
            }

            if (!empty($keyword)) {
                $keyword = "'" . $keyword . "'";
                $condition[] = 'FIND_IN_SET (' . $keyword . ',tds.iSponserKeywords)';
            }
            $condition[] = 'CURDATE() between tds.dtStartDate and tds.dtExpiryDate';

            $tbl = implode(',', $tbl);
            $fields = implode(',', $fields);
            $condition = implode(' AND ', $condition);

            $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
            $res = $this->db->query($qry);
            $row = $res->result_array();
            foreach ($row as $k => $rowData) {
                $restAlias = strtolower(preg_replace("/\s+/", "-", trim($rowData['restaurantName'])));
                $row[$k]['restaurantAlias'] = str_replace("'", "", $restAlias) . '-' . $rowData['restaurantId'];
            }
            return $row;
        } catch (Exception $ex) {
            throw new Exception('Error in sponseredRestaurant function - ' . $ex);
        }
    }

    public function removecomboCartDetail($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $where = array('iRestaurantID' => $iRestaurantID, 'iUserID' => $iUserID,
                    'iComboSubOffersID' => $iComboSubOffersID, 'iComboOffersID' => $iComboOffersID);
                $dtExpiryDate = date('d/m/Y', strtotime($dtExpiryDate));
                $hasrec = $this->db->get_where('tbl_user_combo', $where)->num_rows();
                if ($hasrec > 0) {
                    $this->db->delete('tbl_user_combo', $where);
//                    $this->db->update('tbl_user_combo', $INS, $where);
                }
                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in savecomboCartDetail function - ' . $ex);
        }
    }

    function _generateRandomString($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public function _getCartCalculations($data) {
        $price = [];
        $totalPrice = 0;
        foreach ($data as $value) {
            $totalPrice += ($value['discountedPrice'] * $value['quantity']);
        }
        $price['total'] = number_format($totalPrice, 2, '.', '');
        $price['appliedTax'] = number_format((($totalPrice * 10) / 100), 2, '.', '');
        $price['swachTax'] = number_format((($totalPrice * 0.5) / 100), 2, '.', '');
        return $price;
    }

    public function updateComboCart($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $update = array('iRestaurantID' => $iRestaurantID, 'iUserID' => $iUserID,
                    'iComboSubOffersID' => $iComboSubOffersID, 'iComboOffersID' => $iComboOffersID, 'OrderID' => $OrderID, 'eBookingStatus' => $eBookingStatus);
                $where = array('iUserComboID' => $tblComboId);
                $hasrec = $this->db->get_where('tbl_user_combo', $where)->num_rows();
                if ($hasrec > 0) {
                    $this->db->update('tbl_user_combo', $update, $where);
                }
                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in updateComboCart function - ' . $ex);
        }
    }

    public function saveComboPaymentDetails($postValue) {
        try {
            if (!empty($postValue)) {
                //comboData in request needed

                $item_id = [];
                $comboData = [];
                $cdata = array();
                $cdata[] = $postValue["comboData"];
                //foreach ($cdata as $k => $value) {
                foreach ($postValue["comboData"] as $value) {
                    $comboQuery = 'SELECT tcso.iComboOffersID, tcso.vOfferTitle, tcso.tOfferDetail, tcso.tActualPrice, tcso.tDiscountedPrice, tco.iRestaurantID FROM tbl_combo_sub_offers AS tcso LEFT JOIN tbl_combo_offers AS tco ON tcso.iComboOffersID=tco.iComboOffersID WHERE tcso.iComboSubOffersID=' . $value['subComboOfferId'];
                    $comboRes = $this->db->query($comboQuery)->row_array();
                    $data = [
                        'iRestaurantID' => $comboRes['iRestaurantID'],
                        'iUserID' => $postValue['userId'],
                        'iComboSubOffersID' => $value['subComboOfferId'],
                        'iComboOffersID' => $comboRes['iComboOffersID'],
                        'eBookingStatus' => 'Paid',
                        'tblComboId' => $value['userComboId'],
                        'quantity' => $value['quantity'],
                        'discountedPrice' => $comboRes['tDiscountedPrice'],
                        'offerText' => $comboRes['vOfferTitle'],
                        'offerDetail' => $comboRes['tOfferDetail'],
                        'actualPrice' => $comboRes['tActualPrice']
                    ];
                    $item_id[] = $value['subComboOfferId'];
                    $comboData[] = $data;
                }

                if (empty($postValue['pointsUsed'])) {
                    $postValue['pointsUsed'] = 0;
                }

//                $postValue["rawData"]["testComboData"] = $postValue["comboData"]; // for testing combo data
                $INS = array(
                    'pointsUsed' => $postValue['pointsUsed'],
                    'user_id' => $postValue['userId'],
                    'amount' => $postValue['amount'],
                    'status' => $postValue['status'],
                    'item_id' => json_encode($item_id),
                    'rawData' => json_encode($postValue["rawData"]),
                    'payuid' => $postValue['paymentId'],
                    'createdAt' => date('Y:m:d H:i:s')
                );

                // create order
                if (!empty($postValue['paymentId'])) {
                    $where = array('payuid' => $postValue['paymentId']);
                    $hasrec = $this->db->get_where('tbl_order', $where)->num_rows();
                    if ($hasrec > 0) {
                        $this->db->update('tbl_order', $INS, $where);
                    } else {
                        $this->db->insert('tbl_order', $INS);
                        $where = array("order_id" => $this->db->insert_id());
                    }
                } else {
                    $this->db->insert('tbl_order', $INS);
                    $where = array("order_id" => $this->db->insert_id());
                }
                $rec = $this->db->get_where('tbl_order', $where)->row_array();
                // update orderID
                $orderID = "HMC" . sprintf("%05d", $rec['order_id']);
                $this->db->update('tbl_order', array("orderID" => $orderID), $where);

                //update combo cart status
                foreach ($comboData as $data) {
                    $data["OrderID"] = $orderID;
                    $this->updateComboCart($data);
                }

                $price = $this->_getCartCalculations($comboData);

                //prepare email template
                $str = '<div class="order-table">';
                foreach ($comboData as $data) {
                    $str .= '<div class="confirm-discount">'
                            . '<table>'
                            . '<tr>'
                            . '<th>Combo: ' . $data['offerText'] . '</th>'
                            . '<th>Quantity</th>'
                            . '<th> Original Price</th>'
                            . '<th> Discounted Price</th>'
                            . '</tr>'
                            . '<tr>'
                            . '<td>' . $data['offerDetail'] . '</td>'
                            . '<td>' . $data['quantity'] . '</td>'
                            . '<td>' . $data['actualPrice'] . '</td>'
                            . '<td>' . $data['discountedPrice'] . '</td>'
                            . '</tr>'
                            . '</table>'
                            . '</div>';
                }

                $str .= '</div>';
                if (isset($price['total'])) {
                    $str .= '<div class="total-items order-table clearfix">'
                            . '<span class="cols-2">Order Total</span>'
                            . '<span class="cols-1">&#8377; ' . $price['total'] . '</span></div>';
                }

                $param = array(
                    '%MAILSUBJECT%' => 'Foodine : Order Details',
                    '%LOGO_IMAGE%' => BASEURL . '/images/hungermafia.png',
                    '%ORDER_ID%' => $orderID,
                    '%DATA%' => $str,
                );
                //get user email id
                $emailQuery = 'SELECT vEmail FROM tbl_user WHERE iUserID=' . $postValue['userId'];
                $res = $this->db->query($emailQuery)->row_array();

                //send email
                $tmplt = DIR_VIEW . 'email/combo_offer.php';
                $subject = 'Foodine : Order Details';
                $to = $res['vEmail'];
                $this->load->model("smtpmail_model", "smtpmail_model");
                $data = $this->smtpmail_model->send($to, $subject, $tmplt, $param);

                return $orderID;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in updateComboCart function - ' . $ex);
        }
    }

    public function savePaymentDetails($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                if (empty($orderID)) {
                    $orderID = $this->_generateRandomString();
                }

                if (!empty($payuid) && empty($paymentId)) {
                    $paymentId = $payuid;
                    $item_id = json_decode($item_id);
                }
                if (is_array($item_id)) {
                    $item_id = implode(",", $item_id);
                }

                if (empty($pointsUsed)) {
                    $pointsUsed = 0;
                }

                $INS = array(
                    'orderID' => $orderID,
                    'pointsUsed' => $pointsUsed,
                    'user_id' => $iUserID,
                    'amount' => $amount,
                    'status' => $status,
                    'item_id' => $item_id,
                    'rawData' => $rawData,
                    'payuid' => $paymentId,
                    'createdAt' => date('Y:m:d H:i:s')
                );

                $where = array('payuid' => $paymentId);
                $hasrec = $this->db->get_where('tbl_order', $where)->num_rows();
                if ($hasrec > 0) {
                    $this->db->update('tbl_order', $INS, $where);
                } else {
                    $this->db->insert('tbl_order', $INS);
                }

                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in updateComboCart function - ' . $ex);
        }
    }

    function getRestaurantAlias() {
        try {
            $tbl = 'tbl_restaurant AS tr';
            $fields[] = 'tr.vRestaurantName AS restaurantName';
            $fields[] = 'tr.iRestaurantID AS restaurantID';
            $condition = 'tr.eStatus IN(\'Active\')';
            $fields = implode(',', $fields);

            $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
            $res = $this->db->query($qry);
            $row = $res->result_array();
            $arr = array();
            foreach ($row as $i => $rowData) {
                $companyAlias = strtolower(preg_replace("/\s+/", "-", trim($rowData['restaurantName'])));
                $companyAlias = str_replace('#', '', $companyAlias);
                $restAlias = str_replace("'", "", $companyAlias);
                $restAlias = $restAlias . '-' . $rowData['restaurantID'];
                $arr[$restAlias] = $rowData['restaurantID'];
            }
            return $arr;
        } catch (Exception $ex) {
            throw new Exception('Error in sponseredRestaurant function - ' . $ex);
        }
    }

    public function addPartner($postData) {
//        return $postData;
        extract($postData);
        $data = array(
            'vRestaurantName' => $vRestaurantName,
            'vParentCompanyName' => $vParentCompanyName,
            'vEmail' => $vEmail,
            'vEmailSecondary' => $vEmailSecondary,
            'vheadManagerName' => $vheadManagerName,
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
            'vThirdRestManagerEmail' => $vThirdRestManagerEmail,
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
//            'iMinPrice' => $iMinPrice,
//            'iMaxPrice' => $iMaxPrice,
            'iPriceValue' => $iPriceValue,
            'tCostDescription' => $tCostDescription,
            'eAlcohol' => $eAlcohol,
            'eFeatured' => $eFeatured,
            'vContactNo' => $vContactNo,
            'tDescription' => $tDescription,
            'tWaitStaff' => $tWaitStaff,
            'tNoManagers' => $tNoManagers,
            'tFoodSpeciality' => $tFoodSpeciality,
            'tDrinkSpeciality' => $tDrinkSpeciality,
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
            'serviceTaxApplied' => $serviceTaxApplied,
            'tableReservationAllowed' => $tableReservationAllowed,
            'tableAllocatedFreq' => $tableAllocatedFreq,
            'peoplePerTable' => $peoplePerTable,
            'bookingDescription' => $bookingDescription,
            'timeOfReservation' => $timeOfReservation,
            'iMinPerson' => $iMinPerson,
            'iMaxPerson' => $iMaxPerson,
            'eStatus' => 'pending',
        );
//        return $data;
        $query = $this->db->insert('tbl_restaurant', $data);
//        return $query;
        if ($this->db->affected_rows() > 0) {
            $iRestaurantID = $this->db->insert_id();
            $adminRec = array(
                'iAdminTypeID' => 2,
                'iRestaurantID' => $iRestaurantID,
                'vFirstName' => $vRestaurantName,
                'vEmail' => '',
                'vPassword' => '',
                'ePlatform' => 'web',
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
                        $data1 = array('iMusicID' => $key, 'iRestaurantID' => $iRestaurantID, 'iDaysPlayed' => $value, 'tCreatedAt' => date('Y-m-d H:i:s'));
                        $query = $this->db->insert('tbl_restaurant_music', $data1);
                    }
                }
            }
            if (isset($iEventID)) {
                if (!empty($iEventID)) {
                    foreach ($iEventID as $key => $value) {
                        $value['iDayofEvent'] = implode(',', $value['iDayofEvent']);
//                         return $value;
                        $data1 = array('iEventTitle' => $value['iEventTitle'],
                            'iEventDescription' => $value['iEventDescription'],
                            'dEventEndDate' => $value['iDateofEvent'],
                            'vEventStartTime' => $value['iTimeOfEvent'],
                            'iDayofEvent' => $value['iDayofEvent'],
                            'iVenueofEvent' => $value['iVenueofEvent'],
                            'URL' => $value['URL'],
                            'iRestaurantId' => $iRestaurantID,
                            'created_at' => date('Y-m-d H:i:s'));
                        $query = $this->db->insert('tbl_restaurant_event', $data1);
                    }
                }
            }

            if (isset($offer)) {
                if (!empty($offer)) {
                    foreach ($offer as $key => $value) {
                        $value['offerDays'] = implode(',', $value['offerDays']);
                        $data1 = array('vOfferText' => $value['offerName'],
                            'vDaysAllow' => $value['offerDays'],
                            'iRestaurantID' => $iRestaurantID,
                            'dtStartDate' => date("Y-m-d", strtotime($value['startDateOffer'])),
                            'dtExpiryDate' => date("Y-m-d", strtotime($value['endDateOffer'])),
                            'tCreatedAt' => date('Y-m-d H:i:s'));
                        $query = $this->db->insert('tbl_deals', $data1);
                    }
                }
            }
            if (isset($combo)) {
                if (!empty($combo)) {
//                    foreach ($combo as $key => $value) {
                    $combo['comboDays'] = implode(',', $combo['comboDays']);
                    $data1 = array('vOfferText' => $combo['comboOfferName'],
                        'vDaysAllow' => $combo['comboDays'],
                        'iRestaurantID' => $iRestaurantID,
                        'dtStartDate' => date("Y-m-d", strtotime($combo['startDateCombo'])),
                        'dtExpiryDate' => date("Y-m-d", strtotime($combo['endDateCombo'])),
                        'tCreatedAt' => date('Y-m-d H:i:s'));
                    $query = $this->db->insert('tbl_combo_offers', $data1);
                    if ($this->db->affected_rows() > 0) {
                        $comboID = $this->db->insert_id();
                        $subOffer = $combo['subOffer'];
                        foreach ($subOffer as $key => $valueSub) {
                            $data1 = array('iComboOffersID' => $comboID,
                                'vOfferTitle' => $valueSub['subOfferTitle'],
                                'tOfferDetail' => $valueSub['subOfferDesc'],
                                'tActualPrice' => $valueSub['subOfferOPrice'],
                                'tDiscountedPrice' => $valueSub['subOfferDPrice'],
                                'tCreatedAt' => date('Y-m-d H:i:s'));
                            $query = $this->db->insert('tbl_combo_sub_offers', $data1);
                        }
                    }
                }
//                }
            }

            return $iRestaurantID;
        } else
            return '';
    }

    public function addPartnerImages($postData, $iRestaurantID) {
        $date = date('Y-m-d H:i:s');
        foreach ($postData AS $type => $imageData) {
            switch ($type) {
                case "bannerImage":
                    $query = 'UPDATE tbl_restaurant SET vRestaurantLogo = ' . "'$imageData'" . ' WHERE iRestaurantID=' . $iRestaurantID;
                    $this->db->query($query);

                    break;
                case "eventImage":
                    $query = 'UPDATE tbl_restaurant_event SET iEventImage = ' . "'$imageData'" . ' WHERE iRestaurantID=' . $iRestaurantID;
                    $this->db->query($query);

                    break;
                case "eventImage":
                    foreach ($imageData AS $image) {
                        $data = array(
                            'iRestaurantID' => $iRestaurantID,
                            'vPictureName' => $image,
                            'eStatus' => 'Active',
                            'tCreatedAt' => $date,
                            'tModifiedAt' => $date);
                        $this->db->insert('tbl_restaurant_image', $data);
                    }
                    break;
                case "restaurentListImages":
                    foreach ($imageData AS $image) {
                        $data = array(
                            'iRestaurantID' => $iRestaurantID,
                            'vPictureName' => $image,
                            'eStatus' => 'Active',
                            'tCreatedAt' => $date,
                            'tModifiedAt' => $date);
                        $this->db->insert('tbl_restaurant_image', $data);
                    }
                    break;
                case "barImages":
                    foreach ($imageData AS $image) {
                        $data = array(
                            'iRestaurantID' => $iRestaurantID,
                            'vPictureName' => $image,
                            'eMenuType' => 'bar',
                            'eStatus' => 'Active',
                            'tCreatedAt' => $date,
                            'tModifiedAt' => $date);
                        $this->db->insert('tbl_restaurant_menu_image', $data);
                    }
                    break;
                case "foodImages":
                    foreach ($imageData AS $image) {
                        $data = array(
                            'iRestaurantID' => $iRestaurantID,
                            'vPictureName' => $image,
                            'eMenuType' => 'food',
                            'eStatus' => 'Active',
                            'tCreatedAt' => $date,
                            'tModifiedAt' => $date);
                        $this->db->insert('tbl_restaurant_menu_image', $data);
                    }
                    break;

                default:
                    break;
            }
        }
        return true;
    }

    public function getLocations() {
        $res = $this->db->get_where('tbl_location', array('eStatus' => 'Active'));
        return $res->result_array();
    }

    public function getRestaurantByLocation($locationID) {
        try {
            $tbl = $fields = '';
            $tbl .= '`tbl_restaurant` AS `tr`';
            $fields .= ' `tr`.iRestaurantID as id, `tr`.`vRestaurantName` AS restaurantName, CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage';
            $condition = " WHERE `tr`.`iLocationID` IN ('$locationID') AND `tr`.eStatus = 'Active'";
            $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $condition;
            $res = $this->db->query($qry);
            $row = $res->result_array();
            return $row;
        } catch (Exception $ex) {
            // If execution stop unexpectedly send blank array
            return array();
        }
    }

    function allRestaurant() {
        try {
            $qry = 'select DISTINCT tr.iRestaurantID AS id, CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS image, tr.vRestaurantName AS name, tr.vCityName AS city, tco.vLocationName AS location FROM tbl_restaurant AS tr, tbl_location AS tco WHERE tr.eStatus IN("Active") AND `tco`.`iLocationID` = `tr`.`iLocationID`';
            $row = $this->db->query($qry)->result_array();
            foreach ($row as $k => $row1) {
                $row[$k]['address'] = $row1['location'] . ', ' . $row1['city'];
            }
            return $row;
        } catch (Exception $ex) {
            throw new Exception('Error in allRestaurant function - ' . $ex);
        }
    }

    function updatePrice() {
        try {
            $qry = "select DISTINCT tr.iRestaurantID AS id,tr.iPriceValue AS cf2 FROM tbl_restaurant AS tr";
            $row = $this->db->query($qry)->result_array();
            foreach ($row as $k => $row1) {
                //$maxPrice = explode(' ', $row1['cf2']);
                $maxPrice = preg_replace('/[^0-9]/', "", $row1['cf2']);
                $this->db->update('tbl_restaurant', array('iMaxPrice' => $maxPrice), array('iRestaurantID' => $row1['id']));
                //$this->db->update('tbl_restaurant', array('iMinPrice' => $maxPrice[1]), array('iRestaurantID' => $row1['id']));
            }
            return $row;
        } catch (Exception $ex) {
            throw new Exception('Error in updatePrice function - ' . $ex);
        }
    }

    /**
     * 
     * @param type $postValue
     * @return int
     * @throws Exception
     */
//    function postUpdateReview($postValue) {
//        try {
//            if (!empty($postValue)) {
//                extract($postValue);
//
//                $hasrec = $this->db->get_where('tbl_restaurant_review', array('iReviewID' => $reviewId, 'iRestaurantID' => $restaurantId, 'iUserID' => $userId))->num_rows();
//                if ($hasrec > 0) {
//                    /*
//                     * UPDATE THE RECORD WITH THE OLDER ONE REVIEW AND RATE VALUE...
//                     */
//                    $UPDT = array(
//                        'iAmbience' => isset($ambience) ? $ambience : $hasrec['iAmbience'],
//                        'iPrice' => isset($price) ? $price : $hasrec['iPrice'],
//                        'iFood' => isset($food) ? $food : $hasrec['iFood'],
//                        'iService' => isset($service) ? $service : $hasrec['iService'],
//                        'tReviewDetail' => isset($review) ? $review : $hasrec['tReviewDetail'],
//                    );
//                    $this->db->update('tbl_restaurant_review', $UPDT, array('iReviewID' => $reviewId));
//                    $this->db->update('tbl_restaurant', array('iSolrFlag' => '0'), array('iRestaurantID' => $restaurantId));
//                    return $reviewId;
//                }return false;
//            }
//            return false;
//        } catch (Exception $ex) {
//            throw new Exception('Error in postReview functi0on - ' . $ex);
//        }
//    }

    /**
     * 
     * @param type $postValue
     * @return int
     * @throws Exception
     */
    function deleteReview($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                $hasrec = $this->db->get_where('tbl_restaurant_review', array('iReviewID' => $reviewId, 'iUserID' => $userId))->num_rows();
                if ($hasrec > 0) {
                    /*
                     * DELETE THE REVIEW AND REVIEW COMMENTS
                     */
                    $this->db->update('tbl_restaurant_review', array('eStatus' => 'deleted'), array('iReviewID' => $reviewId));
                    $this->db->update('tbl_restaurant', array('iSolrFlag' => '0'), array('iRestaurantID' => $restaurantId));
                    return $reviewId;
                }return false;
            }
            return false;
        } catch (Exception $ex) {
            throw new Exception('Error in deleteReview functi0on - ' . $ex);
        }
    }

    function verifyPasscode($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                $hasrec = $this->db->get_where('tbl_restaurant', array('vPasscode' => $passcode, 'iRestaurantID' => $restaurantId, 'eStatus' => 'Active'))->num_rows();
                if ($hasrec > 0) {
                    /*
                     * Mark as availed
                     */
                    $this->db->update('tbl_user_combo', array('eAvailedStatus' => 'Availed'), array('iRestaurantID' => $restaurantId, 'iUserID' => $userId, 'iComboSubOffersID' => $comboSubOfferId, 'eBookingStatus' => 'Paid'));
                    return true;
                }return false;
            }
            return false;
        } catch (Exception $ex) {
            throw new Exception('Error in deleteReview functi0on - ' . $ex);
        }
    }

    public function getAllOffers($restaurantId) {
        return $this->_getAllOffers($restaurantId);
    }

    private function _getAllOffers($restaurantId) {
        $fields = $join = $groupBy = $orderBy = $limit = '';
        $tbl[] = 'tbl_restaurant AS tr';
        $tbl[] = 'tbl_deals AS td';
        $tbl[] = '`tbl_location` AS `tl`';
        $fields[] = 'tr.iRestaurantId AS restaurantId';
        $fields[] = 'tr.vRestaurantName AS restaurantName';
        $fields[] = 'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage';
        $fields[] = 'CONCAT("' . BASEURL . 'images/deal/", IF(td.vDealImage =\'\', "default.jpeg", CONCAT(`td`.iDealID,\'/\',`td`.vDealImage))) AS offerImage';
        $fields[] = 'td.iDealID AS offerId';
        $fields[] = '"" AS offerCode';
        $fields[] = 'tl.vLocationName AS vLocationName';
        $fields[] = 'tr.vCityName AS vCityName';
        $fields[] = 'td.vOfferText AS offerText';
        $fields[] = 'td.tOfferDetail AS offerDetail';
        $fields[] = 'td.tTermsOfUse AS offerTerms';
        $fields[] = 'td.vDaysAllow AS daysAllow';
        $fields[] = 'td.vDealCode AS dealCode';
        $fields[] = 'DATE_FORMAT(td.dtStartDate,"' . MYSQL_DATE_FORMAT . '") AS offerStartDate';
        $fields[] = 'DATE_FORMAT(td.dtExpiryDate,"' . '%d-%m-%Y' . '") AS offerEndDate';
        'DATE_FORMAT(td.dtExpiryDate,"' . MYSQL_DATE_FORMAT . '") AS offerEndDate';

        $fields[] = '"discount" AS offerType';

        $condition[] = 'td.eStatus = \'Active\'';
        $condition[] = 'td.iRestaurantID = tr.iRestaurantID';
        $condition[] = 'tl.iLocationID = tr.iLocationID';
        $condition[] = 'tl.eStatus IN(\'Active\')';
        $condition[] = 'tr.eStatus IN(\'Active\')';
        $condition[] = 'CURDATE() between td.dtStartDate and td.dtExpiryDate';
        $condition[] = 'td.iRestaurantID = \'' . $restaurantId . '\'';

        $tbl = implode(',', $tbl);
        $fields = implode(',', $fields);
        $condition = implode(' AND ', $condition);

        $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition . $limit;
        $res = $this->db->query($qry);
        $row = $res->result_array();
        $combo = $this->_getComboOffer($restaurantId);
        return array_merge($row, $combo);
    }

    private function _getComboOffer($restaurantId) {
        $orderBy = '';
        $tbl = $fields = $join = $groupBy = '';
        $tbl .= '`tbl_combo_offers` AS `tco`';
        if (!empty($restaurantId)) {
            $condition[] = 'tco.iRestaurantID =' . $restaurantId;
        }

        $condition[] = 'tco.eStatus = "Active"';
        $condition[] = 'CURDATE() between tco.dtStartDate and tco.dtExpiryDate';
        $condition[] = 'tr.eStatus IN(\'Active\')';

        $fields .= 'tco.iComboOffersID, tco.iRestaurantID, tr.vRestaurantName as  restaurantName, tr.vCityName as city, tco.vOfferText, tcso.tActualPrice, tcso.tDiscountedPrice, tco.vDaysAllow, tco.dtStartDate, tco.dtExpiryDate';
        $fields .= ', IF(tco.vOfferImage =\'\',CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ), CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage =\'\', "default.jpeg", CONCAT(`tco`.iRestaurantID,\'/thumb/\',`tco`.vOfferImage)))) AS vOfferImage';
        //$fields .= ', CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage =\'\', "default.jpeg", CONCAT(`tco`.iRestaurantID,\'/\',`tco`.vOfferImage))) AS vOfferImage';
        $fields .= ', tc.vLocationName AS location';

        $fields .= ',"combo" AS offerType';
        $fields .= ',tco.iComboOffersID AS offerId';
        $fields .= ',tco.vOfferText AS offerText';
        $join .= ' INNER JOIN `tbl_combo_sub_offers` AS `tcso` ON `tcso`.`iComboOffersID` = `tco`.`iComboOffersID`';
        $join .= ' INNER JOIN `tbl_restaurant` as `tr` on  `tco`.`iRestaurantID` = `tr`.`iRestaurantID`';
        $join .= ' INNER JOIN `tbl_location` as `tc` on  `tc`.`iLocationID` = `tr`.`iLocationID`';
        $groupBy = ' GROUP BY tco.iComboOffersID ';
        $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');

        $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groupBy . $orderBy;

        $res = $this->db->query($qry);
        $row = $res->result_array();
        foreach ($row as $key => $value) {
            $perDiscount = round(((($row[$key]['tActualPrice'] - $row[$key]['tDiscountedPrice']) * 100) / $row[$key]['tActualPrice']), 0);
            $row[$key]['perDiscount'] = $perDiscount;
        }
        return $row;
    }

    public function getBanner($type, $postValue=array()) {
        
        try {
            if (!empty($postValue)) {
                extract($postValue);
            }
            if ($type == 'feature')
                $orderBy = '';
            $tbl = $fields = $join = $groupBy = '';
            if($type != 'deals') {
            $tbl[] = '`tbl_banner` AS `tb`';
            $condition[] = 'tb.eType IN(\'' . $type . '\')';
            $condition[] = 'tb.tStartDate <= NOW()';
            $condition[] = 'tb.tEndDate >= NOW()';
            $condition[] = 'tb.eStatus IN(\'Active\')';
            }
           // $fields[] = 'tb.iTypeId AS bannerId';
           // $fields[] = 'tb.eType AS eType';
           // $fields[] = 'tb.vLabel AS bannerLabel';
           // $fields[] = 'tb.tText AS bannerText';

            switch ($type) {
                case 'featured':
                    $fields[] = 'tr.vRestaurantName AS bannerName';
                    $fields[] = 'tr.iRestaurantId AS iRestaurantID';
                    $fields[] = 'tr.iRestaurantId AS id';
                    $fields[] = 'tr.vRestaurantName as restaurantName';
                    $fields[] = 'tr.vCityName AS restaurantCity';
                    $fields[] = 'tr.bookingAvailable as bookingAvailable';
                    $fields[] = 'tl.vLocationName as restaurantLocality';
                    $fields[] = 'CONCAT("' . BASEURL . 'images/banner/",tb.iBannerId,"/",tb.vBannerImage) AS bannerImage';
                    $fields[] = 'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ) AS restaurantImage';
                    $condition[] = "tr.eStatus = 'Active'";
                    $join .= " LEFT JOIN `tbl_restaurant` AS `tr` ON `tb`.`iTypeId` = `tr`.`iRestaurantID`";
                    $join .= " LEFT JOIN tbl_location as tl on tr.iLocationID = tl.iLocationID";
                    break;
                case 'event':
                    $todayDate = date('Y-m-d');
                    $fields[] = 'tre.iEventId AS id';
                    $fields[] = 'tre.iEventTitle AS eventName';
                    $fields[] = 'tre.iVenueofEvent as address';
                    $fields[] = 'tr.vRestaurantName AS restaurantName';
                    $fields[] = 'tre.iDayofEvent AS dayOrDate';
                    $fields[] = 'CONCAT(tre.vEventStartTime,"-",tre.vEventEndTime) AS timing';
                    $fields[] = 'tr.iRestaurantId AS id';
                   // $fields[] = 'tr.vCityName AS restaurantCity';
                   // $fields[] = 'tr.bookingAvailable as bookingAvailable';
                    
                    $fields[] = 'IF(tre.iEventImage =\'\',CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ), CONCAT("' . BASEURL . 'images/event/", IF(tre.iEventImage =\'\', "default.jpeg", CONCAT(`tre`.iRestaurantID,\'/thumb/\',`tre`.iEventImage)))) AS photoUrl';
                    $fields[] = 'tre.iEventDescription AS eventDetail';
                   // $condition[] = "tr.eStatus = 'Active'";
                    $condition[] = "tre.eStatus = 'Active'";
                    $condition[] = 'tre.dEventEndDate >= \'' . $todayDate . '\'';
                    $join .= " LEFT JOIN `tbl_restaurant_event` AS `tre` ON `tb`.`iTypeId` = `tre`.`iEventId`";
                    $join .= " LEFT JOIN `tbl_restaurant` AS `tr` ON `tre`.`iRestaurantId` = `tr`.`iRestaurantID`";
                    $join .= " LEFT JOIN tbl_location as tl on tr.iLocationID = tl.iLocationID";
                    break;
                case 'deals':
                    $fields[] = 'td.vOfferText AS bannerName';
                    $fields[] = 'td.vOfferText AS offerText';
                    $fields[] = 'tr.iRestaurantId AS iRestaurantID';
                    $fields[] = 'tr.vRestaurantName AS restaurantName';
                    $fields[] = 'tr.vCityName AS restaurantCity';
                    $fields[] = 'tr.vStateName AS restaurantState';
                    $fields[] = 'tr.bookingAvailable as bookingAvailable';
                    $fields[] = 'IF(td.vDealImage =\'\',CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ), CONCAT("' . BASEURL . 'images/deal/", IF(td.vDealImage =\'\', "default.jpeg", CONCAT(`td`.iRestaurantID,\'/thumb/\',`td`.vDealImage)))) AS bannerImage';
                    $fields[] = 'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ) AS restaurantImage';
                    $condition[] = "tr.eStatus = 'Active'";
                    $condition[] = "td.eStatus = 'Active'";
                    $condition[] = 'CURDATE() between td.dtStartDate and td.dtExpiryDate';
                    $join .= "`tbl_deals` AS `td`";
                    $join .= "LEFT JOIN `tbl_restaurant` AS `tr` ON `td`.`iRestaurantID` = `tr`.`iRestaurantID`";
                    $join .= " LEFT JOIN tbl_location as tl on tr.iLocationID = tl.iLocationID";
                    break;
                case 'combo':
                    $todayDate = date('Y-m-d');
                    $fields[] = 'tco.vOfferText AS bannerName';
                    $fields[] = 'tr.iRestaurantId AS iRestaurantID';
                    $fields[] = 'tr.vRestaurantName AS restaurantName';
                    $fields[] = 'tr.vCityName AS restaurantCity';
                    $fields[] = 'tr.vStateName AS restaurantState';
                    $fields[] = 'tr.bookingAvailable as bookingAvailable';
                    $fields[] = 'IF(tco.vOfferImage =\'\',CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ), CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage =\'\', "default.jpeg", CONCAT(`tco`.iRestaurantID,\'/thumb/\',`tco`.vOfferImage)))) AS bannerImage';
                    $fields[] = 'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ) AS restaurantImage';
                    $condition[] = "tr.eStatus = 'Active'";
                    $condition[] = "tco.eStatus = 'Active'";
                    $condition[] = 'tco.dtExpiryDate >= \'' . $todayDate . '\'';
                    $join .= " LEFT JOIN `tbl_combo_offers` AS `tco` ON `tb`.`iTypeId` = `tco`.`iComboOffersID`";
                    $join .= " LEFT JOIN `tbl_restaurant` AS `tr` ON `tco`.`iRestaurantID` = `tr`.`iRestaurantID`";
                    $join .= " LEFT JOIN tbl_location as tl on tr.iLocationID = tl.iLocationID";
                    break;
                default:
                    break;
            }

            if (isset($plateform) && $plateform == 'web' && isset($zoneId) && !empty($zoneId)) {
                if (isset($zoneId) && !empty($zoneId)) {
                    if ($zoneId == 10) {
                        $zoneId = '1,2,3,4,5,6,7,8,9';
                    } else if ($zoneId == 9) {
                        $zoneId = '1,2,3,4,5,9';
                    } else {
                        $zoneId = $zoneId;
                    }
                    $condition[] = "tl.iLocZoneID IN($zoneId)";
                }
            }
            $tbl = implode(',', $tbl);
            $fields = implode(',', $fields);
            $condition = implode(' AND ', $condition);
            $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . ' WHERE ' . $condition . ' Limit 12';
            //echo $qry; exit;
            $res = $this->db->query($qry);
            $row = $res->result_array();
            $result = [];
            switch ($type) {
                case 'featured':
                    foreach ($row as $key => $value) {
                        $result[$key] = $value;
                        $result[$key]['totalOffer'] = count($this->getAllOffers($value['bannerId'])) - count($this->_getComboOffer($value['bannerId']));
                    }
                    break;
                case 'event':
                    foreach ($row as $key => $value) {
                        $result[$key] = $value;
                    }
                    break;
                case 'deals':
                    foreach ($row as $key => $value) {
                        $result[$key] = $value;
                    }

                    break;
                case 'combo':
                    foreach ($row as $key => $value) {
                        $result[$key] = $value;
                        //Rest detail
                        $tbl = $fields = $condition = array();
                        $tbl[] = 'tbl_combo_offers AS tco';
                        $tbl[] = 'tbl_combo_sub_offers AS tcso'; //18may
                        $fields[] = 'tcso.tDiscountedPrice AS actualPrice'; //18may
                        $condition[] = 'tco.iComboOffersID = tcso.iComboOffersID'; //18may
                        $condition[] = 'tco.iComboOffersID = \'' . $value['bannerId'] . '\'';
                        $orderBy = ' ORDER BY tDiscountedPrice ASC';
                        $tbl = implode(',', $tbl);
                        $fields = implode(',', $fields);
                        $condition = implode(' AND ', $condition);

                        $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition . $orderBy;
                        $res = $this->db->query($qry);
                        $row = $res->row_array();
                        $result[$key]['actualPrice'] = $row['actualPrice'];
                        $restAlias = strtolower(preg_replace("/\s+/", "-", trim($value['restaurantName'])));
                        $result[$key]['aliasname'] = str_replace("'", "", $restAlias) . '-' . $value['iRestaurantID'];
                    }

                    break;

                default:
                    $result = [];
                    break;
            }

            return $result;
        } catch (Exception $ex) {
            throw new Exception('Error in getBanner function - ' . $ex);
        }
    }

    public function getBannersadas($type) {
        try {
            if ($type == 'feature')
                $type = 'featured';
            $orderBy = '';
            $tbl = $fields = $join = $groupBy = '';
            $tbl[] = '`tbl_banner` AS `tb`';
            $condition[] = 'tb.eType IN(\'' . $type . '\')';
            $condition[] = 'tb.tStartDate <= NOW()';
            $condition[] = 'tb.tEndDate >= NOW()';
            $condition[] = 'tb.eStatus IN(\'Active\')';
            $fields[] = 'tb.iTypeId AS id';
            $fields[] = 'tb.eType AS eType';
            $fields[] = 'tb.vLabel AS label';
            $fields[] = 'tb.tText AS text';
            $tbl = implode(',', $tbl);
            $fields = implode(',', $fields);
            $condition = implode(' AND ', $condition);

            $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
            $res = $this->db->query($qry);
            $row = $res->result_array();
            $result = [];
            switch ($type) {
                case 'featured':
                    foreach ($row as $key => $value) {
                        $result[$key]['bannerId'] = $value['id'];
                        $query = 'SELECT CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ) AS bannerImage, tr.vRestaurantName as restaurantName FROM tbl_restaurant AS tr where tr.iRestaurantID = ' . $value['id'];
                        $rest = $this->db->query($query);
                        $rowData = $rest->row_array();
                        $result[$key]['bannerImage'] = isset($rowData['bannerImage']) ? $rowData['bannerImage'] : '';
                        $result[$key]['bannerName'] = isset($rowData['restaurantName']) ? $rowData['restaurantName'] : '';
                        $result[$key]['bannerLabel'] = $value['label'];
                        $result[$key]['bannerText'] = $value['text'];
                        $result[$key]['iRestaurantID'] = $value['id'];
                        $result[$key]['RestaurantName'] = isset($rowData['restaurantName']) ? $rowData['restaurantName'] : '';
                        $result[$key]['RestaurantImage'] = isset($rowData['bannerImage']) ? $rowData['bannerImage'] : '';
                    }
                    break;
                case 'event':
                    foreach ($row as $key => $value) {
                        $result[$key]['bannerId'] = $value['id'];
                        $query = 'SELECT CONCAT("' . BASEURL . 'images/event/", IF(tre.iEventImage =\'\', "default.jpeg", CONCAT(`tre`.iRestaurantID,\'/thumb/\',`tre`.iEventImage))) AS bannerImage, tre.iEventTitle AS bannerName FROM tbl_restaurant_event AS tre where tre.iEventId = ' . $value['id'];
                        $rest = $this->db->query($query);
                        $rowData = $rest->row_array();
                        $result[$key]['bannerImage'] = isset($rowData['bannerImage']) ? $rowData['bannerImage'] : '';
                        $result[$key]['bannerName'] = $rowData['bannerName'];
                        $result[$key]['bannerLabel'] = $value['label'];
                        $result[$key]['bannerText'] = $value['text'];
                    }
                    break;
                case 'deals':
                    foreach ($row as $key => $value) {
                        $result[$key]['bannerId'] = $value['id'];
                        $query = 'SELECT CONCAT("' . BASEURL . 'images/deal/", IF(td.vDealImage =\'\', "default.jpeg", CONCAT(`td`.iDealID,\'/thumb/\',`td`.vDealImage))) AS bannerImage, td.vOfferText AS bannerName FROM tbl_deals AS td where td.iDealID = ' . $value['id'];
                        $rest = $this->db->query($query);
                        $rowData = $rest->row_array();

                        $result[$key]['bannerImage'] = isset($rowData['bannerImage']) ? $rowData['bannerImage'] : '';
                        $result[$key]['bannerName'] = $rowData['bannerName'];
                        $result[$key]['bannerLabel'] = $value['label'];
                        $result[$key]['bannerText'] = $value['text'];
                        //Rest detail
                        $tbl = $fields = $condition = array();
                        $tbl[] = 'tbl_restaurant AS tr';
                        $tbl[] = 'tbl_deals AS td';
                        $fields[] = 'tr.iRestaurantId AS restaurantId';
                        $fields[] = 'tr.vRestaurantName AS restaurantName';
                        $fields[] = 'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ) AS restaurantImage';
                        $condition[] = 'td.iRestaurantID = tr.iRestaurantID';
                        $condition[] = 'td.iDealID = \'' . $value['id'] . '\'';

                        $tbl = implode(',', $tbl);
                        $fields = implode(',', $fields);
                        $condition = implode(' AND ', $condition);

                        $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
                        $res = $this->db->query($qry);
                        $row = $res->row_array();

                        $result[$key]['iRestaurantID'] = $row['restaurantId'];
                        $result[$key]['RestaurantName'] = $row['restaurantName'];
                        $result[$key]['RestaurantImage'] = $row['restaurantImage'];
                    }

                    break;
                case 'combo':
                    foreach ($row as $key => $value) {
                        $result[$key]['bannerId'] = $value['id'];
                        $query = 'SELECT CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage =\'\', "default.jpeg", CONCAT(`tco`.iRestaurantID,\'/\',`tco`.vOfferImage))) AS bannerImage, tco.vOfferText AS bannerName FROM tbl_combo_offers AS tco where tco.iComboOffersID = ' . $value['id'];
                        $rest = $this->db->query($query);
                        $rowData = $rest->row_array();
                        $result[$key]['bannerImage'] = isset($rowData['bannerImage']) ? $rowData['bannerImage'] : '';
                        $result[$key]['bannerName'] = $rowData['bannerName'];
                        $result[$key]['bannerLabel'] = $value['label'];
                        $result[$key]['bannerText'] = $value['text'];

                        //Rest detail
                        $tbl = $fields = $condition = array();
                        $tbl[] = 'tbl_restaurant AS tr';
                        $tbl[] = 'tbl_combo_offers AS tco';
                        $fields[] = 'tr.iRestaurantId AS restaurantId';
                        $fields[] = 'tr.vRestaurantName AS restaurantName';
                        $fields[] = 'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage';
                        $condition[] = 'tco.iRestaurantID = tr.iRestaurantID';
                        $condition[] = 'tco.iComboOffersID = \'' . $value['id'] . '\'';

                        $tbl = implode(',', $tbl);
                        $fields = implode(',', $fields);
                        $condition = implode(' AND ', $condition);

                        $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
                        $res = $this->db->query($qry);
                        $row = $res->row_array();

                        $result[$key]['iRestaurantID'] = $row['restaurantId'];
                        $result[$key]['RestaurantName'] = $row['restaurantName'];
                        $result[$key]['RestaurantImage'] = $row['restaurantImage'];
                    }

                    break;

                default:
                    $result = [];
                    break;
            }

            return $result;
        } catch (Exception $ex) {
            throw new Exception('Error in getBanner function - ' . $ex);
        }
    }

    function getRestaurantDataById($iRestaurantID) {
        $result = $this->db->get_where($this->table, array('iRestaurantID' => $iRestaurantID));
        if ($result->num_rows() > 0) {
            $row = $result->row_array();
            return $row;
        } else {
            return '';
        }
    }

    function getReviewDetails($recordId) {
        try {
            $updateReviewArr = array();
            if (!empty($recordId)) {
                $sql .= ' SELECT trr.iAmbience as ambience, trr.iPrice as price, trr.iFood as food, trr.iService as service, trr.tReviewDetail AS review, trr.tModifiedAt AS dateTime FROM  `tbl_restaurant_review` AS  `trr` WHERE trr.iReviewID = ' . $recordId;
                $review = $this->db->query($sql)->row_array();
                $updateReviewArr = $review;
                $updateReviewArr['elapsedTime'] = $this->getTimeElapsed(strtotime($review['dateTime']));
                $updateReviewArr['reviewId'] = $recordId;

                $updateReviewArr["reviewImages"] = array();
                $sql = "select tbl_restaurant_review_images.iReviewImage as review_image from tbl_restaurant_review_images WHERE tbl_restaurant_review_images.iReviewID='" . $review['reviewId'] . "'";
                $reviewImages = $this->db->query($sql)->result_array();
                if ($reviewImages) {
                    foreach ($reviewImages AS $img) {
                        $updateReviewArr["reviewImages"][] = REVIEW_IMG . $review["restaurant_id"] . "/" . $img['review_image'];
                    }
                }
            }
            return $updateReviewArr;
        } catch (Exception $ex) {
            throw new Exception('Error in getReviewDetails function - ' . $ex);
        }
    }

    /**
     * Action to get restaurant list
     * @package Restaurant Model
     * @access Public
     * @author 3031@Foodine :
     * @return array
     */
    function getRestaurantList($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $banner = [];
                $this->load->helper('solr_helper');
                $restIds = array();
                switch ($searchType) {
                    case 'feature':
                        $res = getSolrRestaurantList($postValue);
                        //Get Banner
                        $banner = $this->getBanner($searchType);
                        break;
                    case 'nearby':
                        $res = getSolrRestaurantList($postValue);
                        break;
                    case 'collection':
                        $res = getSolrRestaurantList($postValue);
                        break;
                    case 'handpicks':
                        $restIds = $this->_solrGetList($postValue);
                        if (!empty($restIds)) {
                            $postValue['restIds'] = $restIds;
                            $res = getSolrRestaurantList($postValue);
                        } else {
                            $returnArr['data'] = array();
                            return $returnArr;
                        }
                        break;
                    case 'category' :
                    case 'cuisine' :
                        $data = $this->_solrGetList($postValue);
                        $returnArr['data'] = $data;
                        $returnArr['totalRecord'] = count($data);
                        $returnArr['totalPage'] = 1;
                        return $returnArr;
                        break;
                    case 'recentviewed':
                    case 'favourite' :
                        if (!empty($userId)) {
                            $restIds = $this->_solrGetList($postValue);
                        }
                        if (!empty($restIds)) {
                            $postValue['restIds'] = $restIds;
                            $res = getSolrRestaurantList($postValue);
                        } else {
                            $returnArr['data'] = array();
                            return $returnArr;
                        }
                        break;
                }
                return $res;
                $response = $res->getResponse()->getBody();
                $data = json_decode($response, true);
                $otherPageRecord = 11;
                $countRec = $data['response']['numFound'];
                $solrdata = $data['response']['docs'];
                $totalPage = 1 + (floor($countRec / $otherPageRecord));
                //Format Array
                if (isset($postValue['platform']) && !empty($postValue['platform']) && $postValue['platform'] == 'web') {
                    $return = $this->_makeWebList($solrdata, $postValue);
                } else {
                    $return = $this->_makeList($solrdata, $postValue);
                }

                if (isset($filterSortBy) && $filterSortBy == 'rate') {
                    if (!isset($rateorder) || empty($rateorder))
                        $rateorder = 'DESC';

                    $orderBy = $rateorder;

                    usort($return, function($postA, $postB) {
                        if ($postA["restaurantRating"] == $postB["restaurantRating"]) {
                            return 0;
                        }
                        if ($orderBy == 'ASC' || $orderBy == 'asc') {
                            return ($postA["restaurantRating"] < $postB["restaurantRating"]) ? 1 : -1;
                        } else {
                            return ($postA["restaurantRating"] > $postB["restaurantRating"]) ? 1 : -1;
                        }
                    });
                }
                $returnArr['data'] = $return;
                $returnArr['totalRecord'] = $countRec;
                $returnArr['totalPage'] = $totalPage;
                $returnArr['banner'] = $banner;
                return $returnArr;
            }
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantList function - ' . $ex);
        }
    }
    
    function getRestaurantListFoodine($postValue = array()) {
        
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $banner = [];
               // $this->load->helper('solr_helper');
                $res = array();
                switch ($searchType) {
                    case 'nearby':
                        $res = $this->_solrGetList($postValue);
                       // print_r($res);
                        $resp = $this->getRestaurantListByType($res, $postValue);
                        break;
                    case 'featured':
                        $userLat = $latitude;
                        $userLong = $longitude;
                        $resp = $this->getFeaturedRestaurant($userId, $userLat, $userLong);
                        $res['totalCount'] = 20;
                        break;
                    case 'cuisine' :
                        $data = $this->_solrGetList($postValue);
                        $returnArr['data'] = $data['data'];
                        $returnArr['totalRecord'] = count($data['data']);
                        $returnArr['totalPage'] = 1;
                        return $returnArr;
                        break;
                    case 'collection' :
                        $data = $this->_solrGetList($postValue);
                        $returnArr['data'] = $data['data'];
                        $returnArr['totalRecord'] = count($data['data']);
                        $returnArr['totalPage'] = 1;
                        return $returnArr;
                        break;
                    case 'cuisinerest':
                        $res = $this->_solrGetList($postValue);
                       //print_r($res); exit;
                        $resp = $this->getRestaurantListByType($res, $postValue);
                        break;
                    case 'collectionrest':
                        $res = $this->_solrGetList($postValue);
                       //print_r($res); exit;
                        $resp = $this->getRestaurantListByType($res, $postValue);
                    break;
                    case 'popular':
                        $userLat = $latitude;
                        $userLong = $longitude;
                        $resp = $this->getPopularLocation($postValue);
                        $res['totalCount'] = 20;
                        break;
                    case 'fastfilling':
                        $userLat = $latitude;
                        $userLong = $longitude;
                        $resp = $this->getHandPicks($postValue);
                        $res['totalCount'] = 20;
                        break;
                    case 'recentviewed':
                        $restIds = $this->_solrGetList($postValue);
                        $resp = $this->getRestaurantListByType($restIds, $postValue);
                        $res['totalCount'] = 20;
                        break;

                }
               
                $otherPageRecord = 10;
                $countRec = $res['totalCount'];
               // $solrdata = $data['response']['docs'];
                $totalPage = 1 + (floor($countRec / $otherPageRecord));
                //Format Array
                
                $returnArr['data'] = $resp;
                $returnArr['totalRecord'] = $res['totalCount'];
                $returnArr['totalPage'] = $totalPage;
                
                return $returnArr;
             
            }
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantList function - ' . $ex);
        }
    }

    /**
     * Action to make Array
     * @package Restaurant Model
     * @access Public
     * @author 3031@Foodine :
     * @return array
     */
    private function _makeList($solrdata = array(), $postValue = array()) {
        $return = array();
        foreach ($solrdata as $k => $solrdata) {
            $dist = round($solrdata['distance'], 2) . ' KM';
            if (isset($currentLat) && !empty($currentLat) && isset($currentLong) && !empty($currentLong)) {
                $dist = round($solrdata['currentdistance'], 2) . ' KM';
            }
            $return[$k]['restaurantName'] = $solrdata['name'];
            $return[$k]['minPrice'] = "" . $solrdata['cf2'] . "";
            $return[$k]['maxPrice'] = "" . $solrdata['cf2'] . "";
            $return[$k]['restaurantId'] = $solrdata['id'];
            $return[$k]['bookingAvailable'] = "" . $solrdata['bkng'] . "";
            $return[$k]['deliveryAvailable'] = "" . $solrdata['dlvry'] . "";
            $return[$k]['restaurantAddress'] = $solrdata['add2'];
            $return[$k]['location'] = $solrdata['loc'];
            $return[$k]['restaurantLocation'] = $solrdata['loc'];
            $return[$k]['isBookmark'] = "0";
            if (isset($postValue['userId']) && !empty($postValue['userId'])) {
                $userId = $postValue['userId'];
                $restId = $solrdata['id'];
                $bookmark = $this->_getBookMark($userId, $restId);
                $return[$k]['isBookmark'] = $bookmark;
            }
            $return[$k]['restaurantCity'] = $solrdata['city'];
            $return[$k]['restaurantLatitude'] = "" . $solrdata['geo_0_coordinate'] . "";
            $return[$k]['restaurantLongitude'] = "" . $solrdata['geo_1_coordinate'] . "";
            $return[$k]['locationId'] = "" . $solrdata['locId'] . "";
            $return[$k]['createdDate'] = date('M d, Y', strtotime($solrdata['dt']));
            $return[$k]['restaurantImage'] = BASEURL . 'images/restaurant/' . $solrdata['id'] . '/thumb/' . $solrdata['rti'];
            $return[$k]['totalCuisine'] = $solrdata['cui'];
            $return[$k]['cuisineName'] = implode(',', $solrdata['cui']);
            $return[$k]['categoryName'] = implode(',', $solrdata['cat']);
            $return[$k]['ratting'] = "" . $solrdata['rtn'] . "";
            $return[$k]['restaurantRating'] = $this->_getRating($solrdata['id']);
            $return[$k]['totalReview'] = "" . $solrdata['totRevw'] . "";
            $return[$k]['distance'] = $dist;
            $return[$k]['iRestaurantID'] = $solrdata['id'];
            $offer = $this->_getAllOffers($solrdata['id']);
            $return[$k]['offer'] = $offer; //$solrdata['ofr'];
            $return[$k]['totalOffers'] = count($offer);
        }
        return $return;
    }

    /**
     * Action to make Array
     * @package Restaurant Model
     * @access Public
     * @author 3031@Foodine :
     * @return array
     */
    private function _makeWebList($solrdata = array(), $postValue = array()) {
        $return = array();
        foreach ($solrdata as $k => $solrdata) {
            //unset($solrdata['featrd'], $solrdata['geo_0_coordinate'], $solrdata['geo_1_coordinate']);
            unset($solrdata['locId']);
            unset($solrdata['zoneId'], $solrdata['add2']);

            unset($solrdata['totRevw'], $solrdata['dt'], $solrdata['geo']);
            unset($solrdata['cuiId'], $solrdata['catId']);

            unset($solrdata['fclId'], $solrdata['ofr'], $solrdata['ofrTypId']);
            unset($solrdata['_version_'], $solrdata['score'], $solrdata['add2']);
            $return[$k] = $solrdata;
            $return[$k]['rtn'] = $this->_getRating($solrdata['id']);
            $return[$k]['isBookmark'] = "0";
            if (isset($postValue['userId']) && !empty($postValue['userId'])) {
                $userId = $postValue['userId'];
                $restId = $solrdata['id'];
                $bookmark = $this->_getBookMark($userId, $restId);
                $return[$k]['isBookmark'] = $bookmark;
            }
            $this->load->model("offer_model", "offer_model");
            $offer = $this->offer_model->getOfferDetail(array('restId' => $solrdata['id']));
            //$offer = $this->_getAllOffers($solrdata['id']);
            $return[$k]['offers'] = $offer;
            $return[$k]['countoffers'] = count($offer);
            //$BASEURL = "https://www.hungermafia.com/application/";
            if ($solrdata['rti'] != 'default.jpeg') {
                $return[$k]['ri'] = BASEURL . 'images/restaurant/' . $solrdata['id'] . '/thumb/' . $solrdata['rti'];
            } else {
                $return[$k]['ri'] = BASEURLSITE . 'images/restaurantMobile/default.jpeg';
            }

            $userImagePath = $userImageThumbPath = BASEURL . 'images/restaurantPhoto/';
            $menuImagePath = $menuImageThumbPath = BASEURL . 'images/restaurantMenu/';
            $arrrui = array();
            foreach ($return[$k]['rui'] as $imgrui) {
                if ($imgrui != '') {
                    $arrrui[] = $userImagePath . $solrdata['id'] . '/' . $imgrui;
                }
            }
            $imgruistr = implode(',', $arrrui);
            $return[$k]['ruistr'] = $imgruistr;

            $arrruti = array();
            foreach ($return[$k]['ruti'] as $imgruti) {
                if ($imgruti != '') {
                    $arrruti[] = $userImagePath . $solrdata['id'] . '/thumb/' . $imgruti;
                }
            }
            $imgrutistr = implode(',', $arrruti);
            $return[$k]['rutistr'] = $imgrutistr;

            $arrrmi = array();
            foreach ($return[$k]['rmi'] as $imgrmi) {
                if ($imgrmi != '') {
                    $arrrmi[] = $menuImagePath . $solrdata['id'] . '/' . $imgrmi;
                }
            }
            $imgrmistr = implode(',', $arrrmi);
            $return[$k]['rmistr'] = $imgrmistr;

            $arrrmti = array();
            foreach ($return[$k]['rmti'] as $imgrmti) {
                if ($imgrmti != '') {
                    $arrrmti[] = $menuImagePath . $solrdata['id'] . '/thumb/' . $imgrmti;
                }
            }
            $imgrmtistr = implode(',', $arrrmti);
            $return[$k]['rmtistr'] = $imgrmtistr;

            $companyAlias = strtolower(preg_replace("/\s+/", "-", trim($solrdata['name'])));
            $return[$k]['aliasname'] = str_replace("'", "", $companyAlias) . '-' . $solrdata['id'];
            $return[$k]['oct'] = "" . $solrdata['oct'] . "";

            $facilities = array('Seating Available' => 'Seat_available.png', 'Serves Alcohol' => 'Serves-Alcohol.png', 'Serves Non-Veg' => 'serves_nonveg.png', 'Wi-Fi Available' => 'Wifi.png', 'Wi-Fi Avaliable' => 'Wifi.png', 'Air Conditioned' => 'Air-Conditioned.png', 'Cash Accepted' => 'Cash-Accepted.png', 'Cards Accepted' => 'Cards-Accepted.png', 'Home Delivery' => 'home_delivery.png', 'Outdoor Seating' => 'outdoor_seating.png', 'Vegetarian' => 'pure_veg.png', 'Pure Vegetarian' => 'pure_veg.png', 'Smoking Area' => 'smoking_area.png', 'Valet Parking' => 'valet.png', 'Free Parking' => 'free-parking.png', 'Private Parking' => 'private-parking.png', 'Wheelchair Accessible' => 'wheel-chair.png', 'Live Sports Screening' => 'live-sport.png', 'Live Music' => 'live-music.png');
            $fldata = array();
            foreach ($solrdata['fcl'] as $fc => $fl) {
                $fldata[$fc]['fac'] = BASEURL . 'images/facilityicon/w/' . $facilities[$fl];
                //$fldata[$fc]['fac'] = 'https://www.hungermafia.com/application/images/facilityicon/w/' . $facilities[$fl];
                $fldata[$fc]['fcname'] = $fl;
            }
            $return[$k]['fl'] = $fldata;
            unset($return[$k]['fcl'], $solrdata['rti']);
        }
        return $return;
    }

    /**
     * Action to get restids from DB
     * @package Restaurant Model
     * @access Public
     * @author 3031@Foodine :
     * @return array
     */
    private function _solrGetList($postValue = array()) {
        $distanceType = 'km';
        $multiplyer = 6371 * 1000;
        $maxDistance = 160.934 * 1000; //offers in 100 miles
        $userLat = $postValue['latitude'];
        $userLong = $postValue['longitude'];
        $this->load->model('user_model');
        if($postValue['userId']) {
            $userId = $postValue['userId'];
            $sort = $this->user_model->getCIMUserArray('sort', @$userId);
            $price_sort = $sort[0]['selected'];
            $rating_sort = $sort[1]['selected'];
            //print_r($price_sort."--".$rating_sort); exit;
        }
        
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $tbl = $fields = $join = $limit = '';
                $condition = array();

                if ($searchType != 'category' && $searchType != 'cuisine' &&  $searchType != 'cuisinerest' && $searchType != 'collection' &&  $searchType != 'collectionrest') {
                    $tbl = 'tbl_restaurant AS tr';
                    $fields .= 'tr.iRestaurantID AS restaurantId';
                }
$fields .= ', IF((select id from tbl_banquet_map where iRestaurantID = tr.iRestaurantId),1,0) as isBanquet';
                switch ($searchType) {
                    case 'recentviewed':
                        if (isset($userId) && $userId != '') {
                            $tbl .= ', tbl_user_restaurant_viewed AS turv';
                            $condition[] = 'tr.iRestaurantID = turv.iRestaurantID';
                            $condition[] = 'turv.iUserID = "' . $userId . '"';
                            $orderBy = ' ORDER BY turv.tModifiedAt DESC ';
                            $limit = ' limit 20';
                        }
                        break;
                    case 'category' :
                        $tbl .= 'tbl_category AS tc';
                        $tbl .= ' left join tbl_restaurant_category AS trc ON trc.iCategoryID = tc.iCategoryID';
                        $tbl .= ' left join tbl_restaurant AS tr ON tr.iRestaurantId= trc.iRestaurantId';
                        if (isset($plateform) && $plateform == 'web' && isset($zoneId) && !empty($zoneId)) {
                            $tbl .= ' left join tbl_location AS tl ON tl.iLocationID= tr.iLocationID';
                            if (isset($zoneId) && !empty($zoneId)) {
                                if ($zoneId == 10) {
                                    $zoneId = '1,2,3,4,5,6,7,8,9';
                                } else if ($zoneId == 9) {
                                    $zoneId = '1,2,3,4,5,9';
                                } else {
                                    $zoneId = $zoneId;
                                }
                                $condition[] = "tl.iLocZoneID IN($zoneId)";
                            }
                        }
                        $condition[] = 'tr.eStatus IN(\'Active\')';
                        $condition[] = 'tc.eStatus IN(\'Active\')';
                        if (isset($userLat) && !empty($userLat) && isset($userLong) && !empty($userLong)) {
                            $condition[] = '(ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                                    . ' * cos( radians( vLat) )'
                                    . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                                    . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) )) < ' . $maxDistance;
                        }
                        $fields .= 'tc.iCategoryID AS categoryId';
                        $fields .= ', tc.vCategoryName AS categoryName';
                        $fields .= ', CONCAT("' . BASEURL . 'images/category/", IF(tc.vCategoryImage =\'\', "default.jpeg", CONCAT(tc.iCategoryID,\'/thumb/\',tc.vCategoryImage))) AS categoryImage';
                        $fields .= ',count(trc.iCategoryID) as restaurantCount';
                        $groupBy = ' GROUP BY tc.iCategoryID ';
                        $orderBy = ' ORDER BY tc.iOrder ASC ';

                        if (isset($pageId) && $pageId >= 0 && isset($plateform) && $plateform == 'web') {

                            /* PAGINATION */
                            $zeroPageRecord = 6;
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
                        }

                        break;
                    case 'cuisine' :
                        $tbl .= 'tbl_cuisine AS tc';
                        $tbl .= ' left join tbl_restaurant_cuisine AS trc ON trc.iCuisineID = tc.iCuisineID';
                        $tbl .= ' left join tbl_restaurant AS tr ON tr.iRestaurantId= trc.iRestaurantId';
                        $condition[] = 'tr.eStatus IN(\'Active\')';
                        $condition[] = 'tc.eStatus IN(\'Active\')';
                        if (isset($userLat) && !empty($userLat) && isset($userLong) && !empty($userLong)) {
                            $condition[] = '(ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                                    . ' * cos( radians( vLat) )'
                                    . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                                    . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) )) < ' . $maxDistance;
                        }
                        $fields .= 'tc.iCuisineID AS id';
                        //$fields .= ', tr.iRestaurantId AS id';
                        $fields .= ', tc.vCuisineName AS cuisineType';
                        $fields .= ', CONCAT("' . BASEURL . 'images/cuisine/", IF(tc.vCuisineImage =\'\', "default.jpeg", CONCAT(tc.iCuisineID,\'/thumb/\',tc.vCuisineImage))) AS photoUrl';
                        $fields .= ',count(trc.iCuisineID) as restaurantCount';
                        $groupBy = ' GROUP BY tc.iCuisineID ';
                        if($sort_price)
                                $orderBy = 'ORDER BY tr.iPriceValue';
//                        if($userId) {
//                            $fil_cond = $this->getFilterCondition($userId);
//                            if($fil_cond)
//                            $condition[] = $fil_cond;
//                        }
                        
                        break;
                    case 'collection' :
                    $tbl .= 'tbl_collection AS tc';
                    $tbl .= ' left join tbl_restaurant_collection AS trc ON trc.iCollectionID = tc.iCollectionID';
                    $tbl .= ' left join tbl_restaurant AS tr ON tr.iRestaurantId= trc.iRestaurantId';
                    $condition[] = 'tr.eStatus IN(\'Active\')';
                    $condition[] = 'tc.eStatus IN(\'Active\')';
                    if (isset($userLat) && !empty($userLat) && isset($userLong) && !empty($userLong)) {
                        $condition[] = '(ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                                . ' * cos( radians( vLat) )'
                                . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                                . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) )) < ' . $maxDistance;
                    }
                    $fields .= 'tc.iCollectionID AS id';
                    //$fields .= ', tr.iRestaurantId AS id';
                    $fields .= ', tc.vCollectionName AS cuisineType';
                    $fields .= ', CONCAT("' . BASEURL . 'images/cuisine/", IF(tc.vCollectionImage =\'\', "default.jpeg", CONCAT(tc.iCollectionID,\'/thumb/\',tc.vCollectionImage))) AS photoUrl';
                    $fields .= ',count(trc.iCollectionID) as restaurantCount';
                    $groupBy = ' GROUP BY tc.iCollectionID ';
                    if($sort_price)
                            $orderBy = 'ORDER BY tr.iPriceValue';
//                        if($userId) {
//                            $fil_cond = $this->getFilterCondition($userId);
//                            if($fil_cond)
//                            $condition[] = $fil_cond;
//                        }
                    
                    break;
                    case 'cuisinerest' :
                        
                        $tbl .= 'tbl_cuisine AS tc';
                        $tbl .= ' left join tbl_restaurant_cuisine AS trc ON trc.iCuisineID = tc.iCuisineID';
                        $tbl .= ' left join tbl_restaurant AS tr ON tr.iRestaurantId= trc.iRestaurantId';
                        $condition[] = 'tr.eStatus IN(\'Active\')';
                        $condition[] = 'tc.eStatus IN(\'Active\')';
                        $condition[] = 'trc.iCuisineID = '.$restaurantId;
                        if (isset($userLat) && !empty($userLat) && isset($userLong) && !empty($userLong)) {
                            $condition[] = '(ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                                    . ' * cos( radians( vLat) )'
                                    . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                                    . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) )) < ' . $maxDistance;
                        }
                        $fields .= 'tc.iCuisineID AS categoryId';
                        $fields .= ', tr.iRestaurantId AS id';
                        $fields .= ', tr.iRestaurantId AS restaurantId';
                        $fields .= ', tc.vCuisineName AS categoryName';
                        $fields .= ', CONCAT("' . BASEURL . 'images/cuisine/", IF(tc.vCuisineImage =\'\', "default.jpeg", CONCAT(tc.iCuisineID,\'/thumb/\',tc.vCuisineImage))) AS categoryImage';
                       // $fields .= ',count(trc.iCuisineID) as restaurantCount';
                       // $groupBy = ' GROUP BY tc.iCuisineID ';
                        $orderBy = ' ';
                        break;
                        case 'collectionrest' :
                        
                        $tbl .= 'tbl_collection AS tc';
                        $tbl .= ' left join tbl_restaurant_collection AS trc ON trc.iCollectionID = tc.iCollectionID';
                        $tbl .= ' left join tbl_restaurant AS tr ON tr.iRestaurantId= trc.iRestaurantId';
                        $condition[] = 'tr.eStatus IN(\'Active\')';
                        $condition[] = 'tc.eStatus IN(\'Active\')';
                        $condition[] = 'trc.iCollectionID = '.$restaurantId;
                        if (isset($userLat) && !empty($userLat) && isset($userLong) && !empty($userLong)) {
                            $condition[] = '(ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                                    . ' * cos( radians( vLat) )'
                                    . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                                    . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) )) < ' . $maxDistance;
                        }
                        $fields .= 'tc.iCollectionID AS categoryId';
                        $fields .= ', tr.iRestaurantId AS id';
                        $fields .= ', tr.iRestaurantId AS restaurantId';
                        $fields .= ', tc.vCollectionName AS categoryName';
                        $fields .= ', CONCAT("' . BASEURL . 'images/cuisine/", IF(tc.vCollectionImage =\'\', "default.jpeg", CONCAT(tc.iCollectionID,\'/thumb/\',tc.vCollectionImage))) AS categoryImage';
                       // $fields .= ',count(trc.iCuisineID) as restaurantCount';
                       // $groupBy = ' GROUP BY tc.iCuisineID ';
                        $orderBy = ' ';
                        break;
                    case 'favourite' :
                        $tbl .= ', tbl_user_restaurant_favorite AS turf';
                        $condition[] = 'tr.iRestaurantID = turf.iRestaurantID';
                        $condition[] = 'turf.iUserID = "' . $userId . '"';
                        if($sort_price)
                                $orderBy = 'ORDER BY tr.iPriceValue';
                        if($sort_rating)
                                $orderBy = 'ORDER BY tr.iPriceValue';
                        
                        break;
                    case 'handpicks':
                        if (isset($userId) && $userId != '') {
                            $tbl .= ', tbl_user_restaurant_viewed AS turv';
                            $condition[] = 'tr.iRestaurantID = turv.iRestaurantID';
                            $condition[] = 'turv.iUserID = "' . $userId . '"';
                            $orderBy = ' ORDER BY turv.tModifiedAt DESC ';
                            if($sort_price)
                                $orderBy = 'ORDER BY tr.iPriceValue';
                        } else {
                            $tbl .= ', tbl_handpicks AS thp';
                            $condition[] = 'tr.iRestaurantID = thp.iRestaurantID';
                            $orderBy = ' ORDER BY thp.id DESC ';
                        }

                        break;
                    case 'nearby':
//                        if($userId) {
//                            $fil_cond = $this->getFilterCondition($userId);
//                            if($fil_cond)
//                            $condition[] = $fil_cond;
//                        }
                        $condition[] = '(ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                                . ' * cos( radians( vLat) )'
                                    . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                                . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) )) < ' . $maxDistance;
                    
                       /* PAGINATION */
                            $zeroPageRecord = 10;
                            $otherPageRecord = 10;
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
                           $orderBy = ' ORDER BY (ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                            . ' * cos( radians( vLat) )'
                            . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                            . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) ))';
                        

                        break;
                }
                if($searchType == 'nearby') 
                $condition[] = 'iRestaurantId IS NOT NULL';
                
                $condition[] = "tr.eStatus != 'Deleted'";
                
                
               // print_r($condition); exit;
                $conditionn = ' WHERE ' . implode(' AND ', $condition);
                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $conditionn . $groupBy . $orderBy . $limit;
                
                
                
                //echo $qry; exit;
                $res = $this->db->query($qry);
                $arr = $row = $res->result_array();
               // $countRec = count($arr);
                
                
               //
            $countBy = 'tr.iRestaurantId';
            $qry1 = 'SELECT COUNT(DISTINCT(' . $countBy . ')) AS totalRows FROM ' . $tbl . $join . $conditionn;
//echo $qry1; exit;
            $countRes = $this->db->query($qry1);
            $countRec = $countRes->row_array();
            $countRec = (int) $countRec['totalRows'];
                //
             
            
                return array('data' => $arr, 'totalCount' => $countRec);
            }
            return '';
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantList function - ' . $ex);
        }
    }

    /**
     * Action to get user bookmark
     * @package Restaurant Model
     * @access Public
     * @author 3031@Foodine :
     * @return array
     */
    private function _getBookMark($userId, $restId) {
        $bookmark = "0";
        if (!empty($userId) && !empty($restId)) {
            $isFav = $this->db->get_where('tbl_user_restaurant_favorite', array('iRestaurantID' => $restId, 'iUserID' => $userId));
            if ($isFav->num_rows() > 0)
                $bookmark = "1";
        }
        return $bookmark;
    }
    
    function getCuisineTypeRestaurantCount() {
    //    CONCAT(`tr`.iRestaurantID,\'/\',`tr`.vRestaurantLogo))
    
        $sql = 'select  trc.iCuisineId as id,CONCAT("'. BASEURL .'images/cuisine/",trc.iCuisineID,"/",vCuisineImage) as photoUrl, vCuisineName cuisineType, count(trc.iCuisineID) as restaurantCount from  tbl_restaurant_cuisine trc left join tbl_cuisine tc on trc.iCuisineID = tc.iCuisineID  where eStatus = "Active" group by trc.iCuisineID limit 10';
      //echo $sql; exit;
        $res = $this->db->query($sql);
        $allActiveCuisines = $res->result_array();
        
        
        return $allActiveCuisines;
    }

    function getCollectionTypeRestaurantCount() {
    //    CONCAT(`tr`.iRestaurantID,\'/\',`tr`.vRestaurantLogo))
    
        $sql = 'select  trc.iCollectionId as id,CONCAT("'. BASEURL .'images/cuisine/",trc.iCollectionId,"/",vCollectionImage) as photoUrl, vCollectionName cuisineType, count(trc.iCollectionId) as restaurantCount from  tbl_restaurant_collection trc left join tbl_collection tc on trc.iCollectionId = tc.iCollectionId  where eStatus = "Active" group by trc.iCollectionId limit 10';
      //echo $sql; exit;
        $res = $this->db->query($sql);
        $allActiveCuisines = $res->result_array();
        
        
        return $allActiveCuisines;
    }
    
    function getRestaurantListByType($res, $params) {
     
      // print_r($res); exit;
       $userLat = $params['latitude'];
       $userLong = $params['longitude'];
       //print_r($params); exit;
       $cond = $this->getFilterCondition($params['userId']);
        if (count($res['data'])) {
            foreach ($res['data'] as $rowData) {
               // print_r($rowData); exit;
                if(is_array($cond)) {
                    if(!in_array($rowData['restaurantId'], $cond)) {
                        continue;
                    } 
                }
              $resDetail = $this->getRestaurantDetail($rowData['restaurantId'], $userLat, $userLong, $userId, 'web');
             //print_r($resDetail); exit;
              $resRet['id'] = $resDetail['info']['restaurantId'];
              $resRet['rating'] = $resDetail['info']['restaurantRatting'];
              $resRet['bookmarked'] = $resDetail['info']['isFavourite'] == 'yes' ? "true" : "false" ;
              $resRet['distance'] = $resDetail['info']['restaurantDistance'];
              $resRet['address'] = $resDetail['info']['restaurantAddress'];
              $resRet['restaurantName'] = $resDetail['info']['restaurantName'];
              $resRet['dishName'] = $resDetail['info']['restaurantSpeciality']['food'];
              $resRet['photoUrl'] = $resDetail['info']['restaurantImage'];
              $resRet['isOpen'] = $resDetail['info']['isOpenNow'] == 'Open' ? true : false ;
              $resRet['type'] = $resDetail['info']['isBanquet']  ? 'banquet' : '' ;
              $resRet['offers'] = array();
              foreach($resDetail['restaurantDeals'] as $line) {
                 // print_r($line);
                  $resRet['offers'][]= array('description' => $line['offerText'], 'offerCode' => $line['dealCode']);
              }
              //exit;
              
                $res_list[] = $resRet;
              
                // print_r($resDetail); exit;
            }
        } else {
            return array();
        }
    
       return $res_list;
    }
    function getRecommended($postValues) {
        $userLat = $postValues['latitude'];
        $userLong = $postValues['longitude'];
        $userId = $postValues['userId'];
                       //pasha 

       // print_r($postValues); exit;
       // echo $userLat; exit;
        $resp =  $this->getHandPicks($postValues);
        $cond = $this->getFilterCondition($userId);
              
        
        if (count($resp) > 0) {
            foreach ($resp as $rowData) {
                 if(is_array($cond)) {
                    if(!in_array($rowData['id'], $cond)) {
                        continue;
                    }
                  }
                //pasha
             //  print_r($rowData); exit;
              $resDetail = $this->getRestaurantDetail($rowData['id'], $userLat, $userLong, $userId, 'web');
             // print_r($resDetail); exit;
              $resRet['id'] = $resDetail['info']['restaurantId'];
              $resRet['rating'] = $resDetail['info']['restaurantRatting'];
              $resRet['bookmarked'] = $resDetail['info']['isFavourite'] == 'yes' ? "true" : "false" ;
              $resRet['distance'] = $resDetail['info']['restaurantDistance'];
              $resRet['address'] = $resDetail['info']['restaurantAddress'];
              $resRet['restaurantName'] = $resDetail['info']['restaurantName'];
              $resRet['dishName'] = $resDetail['info']['restaurantSpeciality']['food'];
              $resRet['photoUrl'] = $resDetail['info']['restaurantImage'];
              $resRet['offers'] = array();
              $resRet['type'] = $resDetail['info']['isBanquet']  ? 'banquet' : '' ;
              foreach($resDetail['restaurantDeals'] as $line) {
                 // print_r($line);
                  $resRet['offers'][]= array('description' => $line['offerText'], 'offerCode' => $line['dealCode']);
              }
              //exit;
              $res_list[] = $resRet;
                // print_r($resDetail); exit;
            }
        } else {
            return array();
        }
        
        return $res_list;
    }

     function getBanquet($postValues) {
        $userLat = $postValues['latitude'];
        $userLong = $postValues['longitude'];
        $userId = $postValues['userId'];
                       //pasha 

       // print_r($postValues); exit;
       // echo $userLat; exit;
        $resp =  $this->getBanquetPick($postValues);
        $cond = $this->getFilterCondition($userId);
              
        
        if (count($resp) > 0) {
            foreach ($resp as $rowData) {
                 if(is_array($cond)) {
                    if(!in_array($rowData['id'], $cond)) {
                        continue;
                    }
                  }
                //pasha
             //  print_r($rowData); exit;
              $resDetail = $this->getRestaurantDetail($rowData['id'], $userLat, $userLong, $userId, 'web');
             // print_r($resDetail); exit;
              $resRet['id'] = $resDetail['info']['restaurantId'];
              $resRet['rating'] = $resDetail['info']['restaurantRatting'];
              $resRet['bookmarked'] = $resDetail['info']['isFavourite'] == 'yes' ? "true" : "false" ;
              $resRet['distance'] = $resDetail['info']['restaurantDistance'];
              $resRet['address'] = $resDetail['info']['restaurantAddress'];
              $resRet['restaurantName'] = $resDetail['info']['restaurantName'];
              $resRet['dishName'] = $resDetail['info']['restaurantSpeciality']['food'];
              $resRet['photoUrl'] = $resDetail['info']['restaurantImage'];
              $resRet['offers'] = array();
              $resRet['type'] = $resDetail['info']['isBanquet']  ? 'banquet' : '' ;
              foreach($resDetail['restaurantDeals'] as $line) {
                 // print_r($line);
                  $resRet['offers'][]= array('description' => $line['offerText'], 'offerCode' => $line['dealCode']);
              }
              //exit;
              $res_list[] = $resRet;
                // print_r($resDetail); exit;
            }
        } else {
            return array();
        }
        
        return $res_list;
    }
    
    function getRestaurantTableList($post) {
       
        try {
           
                $tbl[] = 'tbl_restaurant AS tr';
                $fields[] = 'tr.iRestaurantID AS restaurantId';
                $fields[] = 'tr.vRestaurantName AS restaurantName';
                $fields[] = 'tr.vTableList AS tableList';
//                if ($platform == 'web') {
//                    $fields[] = 'CONCAT("' . BASEURL . 'images/restaurantListing/", IF(tr.vRestaurantLogo = \'\', "defaultdetail.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage';
//                } else {
//                    $fields[] = 'CONCAT("' . BASEURL . 'images/restaurantMobile/", IF(tr.vRestaurantMobLogo = \'\', "defaultdetail.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantMobLogo)) ) AS restaurantImage';
//                }
                //$fields[] = 'CONCAT("' . BASEURL . 'images/vTableLayout/'.$post[restaurantId].'/", IF(tr.vTableLayout = \'\', "defaultdetail.jpeg", tr.vTableLayout) ) AS layoutUrl';
                $fields[] = 'CONCAT("' . BASEURL . 'images/vTableLayout/", IF(tr.vTableLayout = \'\', "defaultdetail.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vTableLayout)) ) AS layoutUrl';
                $condition[] = 'tr.eStatus = \'Active\'';
                $condition[] = 'tr.iRestaurantID = \'' . $post['restaurantId'] . '\'';

                $tbl = implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
                $dateTime = $post['dateTime']/1000;
          //      echo $qry; exit;
                $res = $this->db->query($qry);
                $row = $res->row_array();
                //mprd($row); exit;
               if(!empty($row['tableList']))
                $tables = explode(',', $row['tableList']);
              // print_r($tables); exit;
                $emptyTables = array();
                $filledTables = array();
                if(count($tables)) {
                    foreach($tables as $line) {
                        $qry1 = "SELECT tableId, tDateTime from tbl_table_book where tableId = '".trim($line)."' and tableId != ''";
                        $res1 = $this->db->query($qry1);
                        $row1 = $res1->row_array();
                     // print_r($row1); exit;
                        if(count($row1) > 0) {
                           // echo date("Y-m-d H:i:s", $dateTime); echo '<br />';
                           // echo date("Y-m-d H:i:s",strtotime("+2 hours ", strtotime($row1['tDateTime'])));

                            if($dateTime >= strtotime("+2 hours ", strtotime($row1['tDateTime']))) {
                                $emptyTables[] = array('tableNumber' => $line, 'tableId' => $line, 'isAvailable' => "true");
                            } else {
                                $filledTables[] = array('tableNumber' => $line, 'tableId' => $line, 'isAvailable' => "false");
                            }
                        } else {
                            $emptyTables[] = array('tableNumber' => $line, 'tableId' => $line, 'isAvailable' => "true");
                        }

                    }
                }
                $result['bookedTables'] = $filledTables;
                $result['availableTables'] = $emptyTables;
                $result['layoutUrl'] = ($row['layoutUrl'] ? $row['layoutUrl'] : '');
                //mprd($result);
              
                return $result;
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantDetail function - ' . $ex);
        }
    }


    function getRestaurantInfo($recordId) {
       
        try {
                $tbl[] = 'tbl_restaurant AS tr';
                $fields[] = 'tr.iRestaurantID AS restaurantId';
                $fields[] = 'tr.vRestaurantName AS restaurantName';
//              $fields[] = 'CONCAT("' . BASEURL . 'images/restaurantMobile/", IF(tr.vRestaurantMobLogo = \'\', "defaultdetail.jpeg", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantMobLogo)) ) AS restaurantImage';
                $fields[] = 'tr.vEmail AS restaurantEmail';
                $fields[] = 'tr.vLat AS restaurantLat';
                $fields[] = 'tr.vLog AS restaurantLong';
                //$fields[] = 'tr.tAddress AS restaurantAddress';
                $fields[] = 'tr.tAddress2 AS restaurantAddress';
                $fields[] = 'tr.vContactNo AS restaurantContact';
                
                //$condition[] = 'tr.eStatus = \'Active\'';
                $condition[] = 'tr.iRestaurantID = \'' . $recordId . '\'';

                $tbl = implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
                //echo $qry; exit;
                $res = $this->db->query($qry);
                $row = $res->row();
//                mprd($row);
                
                return $row;
            

            return '';
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantInfo function - ' . $ex);
        }
    }

}
