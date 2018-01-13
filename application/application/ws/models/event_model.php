<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * event_model
 * @author Anurag Srivastava (anurag.srivastava@kelltontech.com)
 */
class Event_Model extends CI_Model {

    var $table;
    var $currHour, $glob;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_restaurant_event';
        date_default_timezone_set('Asia/Calcutta');
    }

    /*
     * Method to return array for all active events from restaurant events
     * @author Anurag Srivastava (anurag.srivastava@kelltontech.com)
     */

    function getEventList($postValue = array()) {
        try {
            extract($postValue);
            if (isset($userLat) && !empty($userLat) && isset($userLong) && !empty($userLong)) {
                $orderBy = ' ORDER BY distance ASC';
            }
            $condition[] = "tr.eStatus='Active'";
            $condition[] = "tse.eStatus='Active'";
            //If user selecting location from filter
            if (isset($localities) && $localities !== '') {
                $condition[] = 'tr.iLocationID IN(' . $localities . ')';
            }
            
            if (isset($zone) && !empty($zone)){
                    if ($zone == 10) {
                        $zoneId = '1,2,3,4,5,6,7,8,9';
                    } else if ($zone == 9) {
                        $zoneId = '1,2,3,4,5,9';
                    } else {
                        $zoneId = $zone;
                    }
                    $condition[] = "tl.iLocZoneID IN($zoneId)";
             }

            //If user selecting location from filter
            if (isset($restId) && $restId !== '') {
                $condition[] = 'tr.iRestaurantID IN(' . $restId . ')';
            }

            // if event date is specified in filter
            if (!empty($eventDate)) {
                $dayOfWeek = date('w', strtotime($eventDate)) + 1;
                $condition[] = 'tse.dEventStartDate <= \'' . $eventDate . '\'';
                $condition[] = 'tse.dEventEndDate >= \'' . $eventDate . '\'';
            }
            // if date preference is set
            if (!empty($datePreference)) {
                switch ($datePreference) {
                    case 'today' :
                        $todayDate = date('Y-m-d');
                        $dayOfWeek = date('w') + 1;
                        $condition[] = 'tse.dEventStartDate <= \'' . $todayDate . '\'';
                        $condition[] = 'tse.dEventEndDate >= \'' . $todayDate . '\'';
                        break;

                    case 'tomorrow' :
                        $tomorrowDate = date('Y-m-d', strtotime("tomorrow"));
                        $dayOfWeek = date('w', strtotime("tomorrow")) + 1;
                        $condition[] = 'tse.dEventStartDate <= \'' . $tomorrowDate . '\'';
                        $condition[] = 'tse.dEventEndDate >= \'' . $tomorrowDate . '\'';
                        break;

                    case 'weekend' :
                        $nextSatTimestamp = strtotime("next Saturday");
                        $nextSatDate = date('Y-m-d', $nextSatTimestamp);

                        $nextSunTimestamp = strtotime("next Sunday");
                        $nextSunDate = date('Y-m-d', $nextSunTimestamp);

                        $condition[] = '((tse.dEventStartDate <= \'' . $nextSatDate . '\' AND tse.dEventEndDate >= \'' . $nextSatDate . '\') OR (tse.dEventStartDate <= \'' . $nextSunDate . '\' AND tse.dEventEndDate >= \'' . $nextSunDate . '\'))';
                        break;
                    default :
                        $condition[] = 'CURDATE() <= tse.dEventEndDate';
                }
            }

            if (empty($datePreference) && empty($eventDate)) {
                $condition[] = 'CURDATE() <= tse.dEventEndDate';
            }


            // If order is not defined
            if (!isset($distorder) || empty($distorder))
                $distorder = 'ASC';

            if (!isset($dateorder) || empty($dateorder))
                $dateorder = 'ASC';

            switch ($filterSortBy) {
                case 'dist':
                    $orderBy = ' ORDER BY distance ' . $distorder;
                    break;

                case 'date':
                    $orderBy = ' ORDER BY dEventStartDate ' . $dateorder;
                    break;
                default :
                    $orderBy = ' ORDER BY dEventStartDate ' . $dateorder;
                    break;
            }

            // constructing condition
            if (!empty($condition)) {
                $condition = ' WHERE ' . implode(' AND ', $condition);
            } else {
                $condition = '';
            }
            $distanceType = 'km';
            $multiplyer = 6371 * 1000;
            $maxDistance = 160.934*1000;//offers in 100 miles
            if ($distanceType == 'miles') {
                $multiplyer = 3959;
            }


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
            $join = ' LEFT JOIN `tbl_location` AS `tl` ON `tl`.`iLocationID` = `tr`.`iLocationID`';

            // Query to fetch count of records
            $countSql = 'SELECT count(*) as count FROM `tbl_restaurant_event` AS tse '
                    . 'INNER JOIN `tbl_restaurant` AS tr ON tr.iRestaurantID = tse.iRestaurantId ' . $join
                    . $condition;
            $count = $this->db->query($countSql)->row_array();
            $totalPage = 1 + (floor($count['count'] / $otherPageRecord));
            // main query to fetch the data
            $sql = 'SELECT tse.iEventId as id,tl.vLocationName , tr.vCityName as restaurantCity, tse.iEventTitle as title ,
                tse.iEventDescription as description ,tr.vRestaurantName as 
                RestaurantName,tr.bookingAvailable as bookingAvailable, tr.iRestaurantID as 
                RestaurantId, `tse`.iEventImage, CONCAT("' . BASEURL . 'images/restaurant/", IF(`tr`.vRestaurantLogo = \'\', "default.png", CONCAT(`tr`.iRestaurantID,\'/\',`tr`.vRestaurantLogo)) ) AS restaurantImage,
                CONCAT("' . BASEURL . 'images/event/", IF(tse.iEventImage =\'\', "default.png", CONCAT(`tr`.iRestaurantID,\'/\',`tse`.iEventImage))) AS image, tse.iVenueofEvent as 
                VenueofEvent,DATE_FORMAT(tse.dEventStartDate,"' . '%b %d' . '") AS startDateofEvent1,tse.dEventStartDate AS startDateofEvent,tse.dEventEndDate AS endDateofEvent,DATE_FORMAT(tse.dEventEndDate,"' . '%b %d' . '") AS endDateofEvent1,tse.iDayofEvent 
                as DayofEvent,tse.vEventStartTime as startTimeofEvent,tse.vEventEndTime as endTimeofEvent, tse.URL as followUs ';
            if (isset($userLat) && !empty($userLat) && isset($userLong) && !empty($userLong)) {
                $sql .= ', (ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                        . ' * cos( radians( vLat) )'
                        . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                        . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) ))'
                        . ' AS distance , tr.iRestaurantID ';
                
                $condition .= ' AND (ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                        . ' * cos( radians( vLat) )'
                        . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                        . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) )) < '.$maxDistance;
            }
            $sql.= 'FROM `tbl_restaurant_event` AS tse ';
            $sql.= 'INNER JOIN `tbl_restaurant` AS tr ';
            $sql.= 'ON tr.iRestaurantID = tse.iRestaurantId ' . $join . $condition . ' ' . $orderBy . ' ' . $limit;
            $records = $this->db->query($sql)->result_array();

            $days = array(
                '1' => 'Sun',
                '2' => 'Mon',
                '3' => 'Tue',
                '4' => 'Wed',
                '5' => 'Thu',
                '6' => 'Fri',
                '7' => 'Sat'
            );

            $record = array();
            foreach ($records as $kk => $value) {
                $restAlias = strtolower(preg_replace("/\s+/", "-", trim($value['RestaurantName'])));
                $aliasName = str_replace("'", "", $restAlias) . '-' . $value['RestaurantId'];
                $value['RestaurantAlias'] = $aliasName;

                if ($value['startDateofEvent1'] && $value['endDateofEvent1']) {
                    if ($value['startDateofEvent1'] != $value['endDateofEvent1']) {
                        $value['DateofEvent'] = $value['startDateofEvent1'] . '-' . $value['endDateofEvent1'];
                    }else {
                       $value['DateofEvent'] = $value['startDateofEvent1']; 
                    }
                } else {
                    $value['DateofEvent'] = $value['startDateofEvent1'];
                }

                if ($value['startTimeofEvent'] && $value['endTimeofEvent']) {
                    $value['TimeofEvent'] = $value['startTimeofEvent'] . '-' . $value['endTimeofEvent'];
                } else {
                    $value['TimeofEvent'] = $value['startTimeofEvent'];
                }
                
                if($value['iEventImage'] == ''){
                    $value['image'] = $value['restaurantImage'];
                }
                unset($value['iEventImage'], $value['restaurantImage']);
                unset($value['startDateofEvent1'], $value['endDateofEvent1']);
                $dayArray = $dayArr = explode(',', $value['DayofEvent']);
                asort($dayArr);

                $day = '';
                $rangeFlag = false;
                while ($dayKey = array_shift($dayArr)) {
                    if (count($dayArr)) {
                        $day .= $days[$dayKey] . ",";
                    } else {
                        $day .= $days[$dayKey];
                    }
                }
                $records[$kk]['DayofEvent'] = $day;
                $value['DayofEvent'] = $day;

                $rec = array();
                if ($value['DayofEvent'] != '' && isset($datePreference) && !empty($datePreference)) {
                    if ($datePreference == 'today' || $datePreference == 'tomorrow') {
                        if (!in_array($dayOfWeek, $dayArray)) {
                            unset($records[$kk]);
                            $rec[] = $records[$kk];
                        } else {
                            $record[] = $value;
                        }
                    } else if ($datePreference == 'weekend') {
                        if (!in_array('1', $dayArray) && !in_array('7', $dayArray)) {
                            unset($records[$kk]);
                            $rec[] = $records[$kk];
                        } else {
                            $record[] = $value;
                        }
                    }
                } else {
                    $record[] = $value;
                }
            }
            $data['event'] = $record;
            $data['totalRecords'] = (int) $count['count'];
            $data['totalPage'] = $totalPage;

            $this->load->model('restaurant_model');
            $banner = $this->restaurant_model->getBanner("event");
            foreach ($banner as $k => $v) {
                $bannerdetail = $this->_getEventDetail($v['bannerId']);
                $banner[$k]['event'] = $bannerdetail;
            }
            $data['banner'] = $banner;
            return $data;
        } catch (Exception $ex) {
            return array();
        }
    }

    private function _getEventDetail($eventId) {
        try {

            $condition[] = 'tse.iEventId =' . $eventId;
            $join = ' LEFT JOIN `tbl_location` AS `tl` ON `tl`.`iLocationID` = `tr`.`iLocationID`';

            if (!empty($condition)) {
                $condition = ' WHERE ' . implode(' AND ', $condition);
            } else {
                $condition = '';
            }

            $sql = 'SELECT tse.iEventId as id,tl.vLocationName , tse.iEventTitle as title ,
                tse.iEventDescription as description ,tr.iRestaurantId as 
                iRestaurantID,tr.vRestaurantName as 
                RestaurantName,tr.bookingAvailable as bookingAvailable, tr.vCityName as restaurantCity, CONCAT("' . BASEURL . 'images/event/", IF(tse.iEventImage =\'\', "default.png", CONCAT(`tr`.iRestaurantID,\'/\',`tse`.iEventImage))) AS image, tse.iVenueofEvent as 
                VenueofEvent,DATE_FORMAT(tse.dEventStartDate,"' . '%b %d' . '") AS startDateofEvent,tse.dEventStartDate AS startDateofEvent1,DATE_FORMAT(tse.dEventEndDate,"' . '%b %d' . '") AS endDateofEvent,tse.dEventEndDate AS endDateofEvent1,tse.iDayofEvent 
                as DayofEvent,tse.vEventStartTime as startTimeofEvent,tse.vEventEndTime as endTimeofEvent, tse.URL as followUs ';
            $sql.= 'FROM `tbl_restaurant_event` AS tse ';
            $sql.= 'INNER JOIN `tbl_restaurant` AS tr ';
            $sql.= 'ON tr.iRestaurantID = tse.iRestaurantId ' . $join . $condition;
            $records = $this->db->query($sql)->row_array();
            //return $this->db->query($sql)->row_array();
            $days = array(
                '1' => 'Sun',
                '2' => 'Mon',
                '3' => 'Tue',
                '4' => 'Wed',
                '5' => 'Thu',
                '6' => 'Fri',
                '7' => 'Sat'
            );
            if (!empty($records['startDateofEvent']) && !empty($records['endDateofEvent'])) {
                $records['DateofEvent'] = $records['startDateofEvent'] . '-' . $records['endDateofEvent'];
            } else {
                $records['DateofEvent'] = $records['startDateofEvent'];
            }

            if ($records['startTimeofEvent'] && $records['endTimeofEvent']) {
                $records['TimeofEvent'] = $records['startTimeofEvent'] . '-' . $records['endTimeofEvent'];
            } else {
                $records['TimeofEvent'] = $records['startTimeofEvent'];
            }

            $dayArr = explode(',', $records['DayofEvent']);
            asort($dayArr);
            $day = '';
            $rangeFlag = false;
            while ($dayKey = array_shift($dayArr)) {
                if (count($dayArr)) {
                    $day .= $days[$dayKey] . ",";
                } else {
                    $day .= $days[$dayKey];
                }
            }
            $records['DayofEvent'] = $day;

            unset($records['startDateofEvent'], $records['endDateofEvent']);
            $records['startDateofEvent'] = $records['startDateofEvent1'];
            $records['endDateofEvent'] = $records['endDateofEvent1'];
            unset($records['startDateofEvent1'], $records['endDateofEvent1']);
            return $records;
        } catch (Exception $ex) {
            throw new Exception('Error in _getAllLocations function - ' . $ex);
        }
    }

    public function getEventDetail($eventId) {
        try {
            $bannerdetail = $this->_getEventDetail($eventId);
            return $bannerdetail;
        } catch (Exception $ex) {
            throw new Exception('Error in getEventDetail function - ' . $ex);
        }
    }

    /*
     * Method to return all filters which can be applied on restaurant. 
     * 
     * @return array data with location listing
     * @author Anurag Srivastava (anurag.srivastava@kelltontech.com)
     */

    public function getAllEventFilter($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
            }
            // Setting sort by manually
            $resp['sortBy'] = array('dist' => 'Distance', 'date' => 'Date');

            // Fetching localities
            $tbl = $fields = $join = '';
            $tbl .= '`tbl_restaurant` AS `tr`';
            $tbl .= ' ,`tbl_location` AS `tl`';
            $tbl .= ', `tbl_restaurant_event` AS `tre`';
            $condition[] = 'tre.iRestaurantID = tr.iRestaurantID';
            $condition[] = 'tl.iLocationID = tr.iLocationID';
            $condition[] = 'tr.eStatus IN(\'Active\')';
            $condition[] = 'tl.eStatus IN(\'Active\')';
            $condition[] = 'CURDATE() <= tre.dEventEndDate';

            $groubBy = ' GROUP BY tr.iLocationID';
            $fields .= ' `tr`.iLocationID as id, `tl`.`vLocationName` AS name';
            $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');
            $localitysql = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groubBy;
            $localityres = $this->db->query($localitysql);
            $allActiveLocalities = $localityres->result_array();
            $resp['filters']['locality'] = $allActiveLocalities;

            return $resp;

            //
        } catch (Exception $ex) {
            // If execution stop unexpectedly send blank array
            return array();
        }
    }

}
