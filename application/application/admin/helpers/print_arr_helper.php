<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('_print')) {

    function _print($arry = array(), $exit = 0) {
        echo '<pre>' . print_r($d, true) . '</pre>';
        if ($exit)
            exit();
    }

}

if (!function_exists('mpr')) {

    function mpr($d, $echo = TRUE) {
        if ($echo) {
            echo '<pre>' . print_r($d, true) . '</pre>';
        } else {
            return '<pre>' . print_r($d, true) . '</pre>';
        }
    }

}

if (!function_exists('mprd')) {

    function mprd($d) {
        mpr($d);
        die;
    }

}

if (!function_exists('mvr')) {

    function mvr($d) {
        echo '<pre>' . var_dump($d, true) . '</pre>';
    }

}

if (!function_exists('mvrd')) {

    function mvrd($d) {
        mvr($d);
        die;
    }

}
if (!function_exists('getPagePermission')) {

    function getPagePermission($adminTypeId = '') {
        if ($adminTypeId != '') {
            $ci = & get_instance();
            $ADMINTYPE = $ci->session->userdata('ADMINTYPE');

            $tbl = $fields = $condition = array();

            $tbl[] = 'tbl_page AS tp';
            $tbl[] = 'tbl_page_module AS tpa';

            $fields[] = 'tp.iPageID AS pageId';
            $fields[] = 'tp.vPageTitle AS pageTitle';
            $fields[] = 'tpa.vModuleName AS moduleName';

            $condition[] = 'tp.iPageModuleID IN(tpa.iPageModuleID)';
            if ($ADMINTYPE != 1) {
                $condition[] = 'tpa.isDeveloper IN(\'no\')';
                $condition[] = 'tp.eStatus IN(\'active\')';
            }

            $tbl = ' FROM ' . implode(',', $tbl);
            $fields = 'SELECT ' . implode(',', $fields);
            $condition = ' WHERE ' . implode(' AND ', $condition);

            $qry = $fields . $tbl . $condition;

            $pageRec = $ci->db->query($qry)->result_array();

            $returnRec = array();
            for ($i = 0; $i < count($pageRec); $i++) {
                $returnRec[$i]['title'] = $pageRec[$i]['pageTitle'];
                $returnRec[$i]['module'] = $pageRec[$i]['moduleName'];
                $returnRec[$i]['id'] = $pageRec[$i]['pageId'];
                $returnRec[$i]['action'] = getPageActions($adminTypeId, $pageRec[$i]['pageId']);
            }
            //_print_r($returnRec,TRUE);

            return $returnRec;
        }
    }

}
if (!function_exists('getPageActions')) {

    function getPageActions($adminTypeId = '', $pageId = '') {
        if ($adminTypeId != '' && $pageId != '') {
            $ci = & get_instance();

            $field[] = 'tpa.iPageActionID AS actionId';
            $field[] = 'tpa.vActionName AS actionName';
            $field[] = 'IF((SELECT COUNT(*) FROM tbl_page_permission AS tpp WHERE tpp.iAdminTypeID IN(' . $adminTypeId . ') AND tpp.iPageID IN(' . $pageId . ') AND tpp.iPageActionID IN(tpa.iPageActionID)) = 0, 0,1) AS actionPermission';

            $tbl[] = 'tbl_page_action AS tpa';

            $field = 'SELECT ' . implode(',', $field);
            $tbl = ' FROM ' . implode(',', $tbl);

            $qry = $field . $tbl;
            $row = $ci->db->query($qry)->result_array();

            for ($i = 0; $i < count($row); $i++) {
                $row[$i]['actionPermission'] = $row[$i]['actionPermission'] == '1' ? TRUE : FALSE;
            }

            return $row;
        } return array();
    }

}
if (!function_exists('getNotifications')) {

    /*
     * GET ALL USER NOTIFICATIONS
     */

    function getNotifications($getAll = FALSE, $notifyId = 0) {
        try {
            $ci = & get_instance();
            $tbl = array(
                'tbl_table_book AS tn',
                'tbl_restaurant AS tr',
                //'tbl_notification_activity AS tna'
            );

            $fields = array(
//                'tna.tblName AS tableName',
//                'tna.tblString AS tableString',
                'tn.iRestaurantID AS restaurantId',
                'tr.vRestaurantName AS restaurantName',
                'CONCAT("' . DOMAIN_URL . '/images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.png", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage',
                'tn.iTableBookID AS recordId',
                //'tn.iActivityID AS activityId',
                'tn.tCreatedAt AS notifyDate',
                'tn.iTableBookID AS notifyId',
                //'tn.takeAction AS notifyAction'
            );

            $condition[] = 'tn.iRestaurantID IN(tr.iRestaurantID)';
           // $condition[] = 'tn.iActivityID IN(tna.iActivityID)';
            $condition[] = 'tn.iTableBookID > ' . $notifyId;
//            if (!$getAll)
//                $condition[] = 'tn.hasRead IN(\'no\')';

            $tbl = ' FROM ' . implode(',', $tbl);
            $fields = ' SELECT ' . implode(',', $fields);
            $condition = ' WHERE ' . implode(' AND ', $condition);
            $orderBy = ' ORDER BY tn.tCreatedAt DESC ';

            $qry = $fields . $tbl . $condition . $orderBy;
            $row = $ci->db->query($qry)->result_array();

           foreach ($row as $k => $v) {
                $keyValue = '';
                $fieldValue = '';
                $fromValue = '';
//                if ((int) $v['activityId'] == 1 || (int) $v['activityId'] == 4) {
//                    $keyValue = 'iRestSpecialtyID';
//                    $fieldValue = 'vSpecialtyName';
//                    $fromValue = 'from specialty';
//                } else {
//                    $keyValue = 'iDealID';
//                    $fieldValue = 'vOfferText';
//                    $fromValue = 'from deals';
//                }
//               $foundRec = $this->db->get_where($v['tableName'], array($keyValue => $v['recordId']))->row_array();
//                if (isset($foundRec[$fieldValue])) {
//                    $row[$k]['recordField'] = isset($foundRec[$fieldValue]) ? $foundRec[$fieldValue] : '';
//                    $row[$k]['fromValue'] = $fromValue;
//                    $row[$k]['notifyDate'] = $thisget_timeago(strtotime($v['notifyDate']));
//                } else {
//                    unset($row[$k]);
//                }
                    $row[$k]['recordField'] = isset($foundRec[$fieldValue]) ? $foundRec[$fieldValue] : '';
                   // $row[$k]['fromValue'] = $fromValue;
                    $row[$k]['notifyDate'] = get_timeago(strtotime($v['notifyDate']));
            }
          
            return $row;
        } catch (Exception $ex) {
            throw new Exception('Error in getNotifications function - ' . $ex);
        }
    }

}
if (!function_exists('get_timeago')) {

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
                return 'about ' . $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
            }
        }
    }

}
if (!function_exists('page_permission')) {

    function page_permission($admin_type) {
        if ($admin_type != '') {
            $ci = & get_instance();
            /* select all modules */
            if ($admin_type == 1) {
                $condition = array('vModuleName !=' => '');
            } else {
                $condition = array(
                    'isDeveloper' => 'no',
                    'eStatus' => 'Active'
                );
            }
            $modules = $ci->db->order_by('vModuleName','asc')->get_where('tbl_page_module', $condition)->result_array();
            //mprd($modules);

            $return_arr = array();
            foreach ($modules as $mod_val) {
                /* select all pages */
                $pages = $ci->db->get_where('tbl_page', array('iPageModuleID' => $mod_val['iPageModuleID']))->result_array();

                if (!empty($pages)) {
                    $return_arr[$mod_val['iPageModuleID']]['module'] = array(
                        'name' => $mod_val['vModuleName'],
                        'icon' => $mod_val['vModuleIcon']
                    );
                    $return_arr[$mod_val['iPageModuleID']]['pages'] = array();
                    foreach ($pages as $page_val) {
                        /* to get all page permission */
                        $permission = $ci->db->get_where('tbl_page_permission', array('iAdminTypeID' => $admin_type, 'iPageID' => $page_val['iPageID']))->result_array();
                        $page_permission = array();
                        foreach ($permission as $permission_val) {
                            $page_permission[] = $permission_val['iPageActionID'];
                        }
                        /* get page permission */
                        $return_arr[$mod_val['iPageModuleID']]['pages'][$page_val['iPageID']] = array(
                            'name' => $page_val['vPageTitle'],
                            'url' => $page_val['vPageURL'],
                            'permission' => $page_permission
                        );
                        if ($admin_type > 1 && in_array(6, $page_permission)) {
                            unset($return_arr[$mod_val['iPageModuleID']]['pages'][$page_val['iPageID']]);
                        } else {
                            //unset($return_arr[$mod_val['iPageModuleID']]['pages'][$page_val['iPageID']]);
                        }
                    }
                }
            } return $return_arr;
        } return array();
    }

}

if (!function_exists('get_page_permission')) {

    function get_page_permission($admin_type = 0, $page_id = 0) {
        if ($page_id != 0) {
            $ci = & get_instance();
            if ($admin_type == 1) {
                return array(1, 2, 3, 4, 5, 7);
            } else {
                $rec = $ci->db->get_where('tbl_page_permission', array('iAdminTypeID' => $admin_type, 'iPageID' => $page_id))->result_array();
                $permission = array();
                foreach ($rec as $rec_key => $rec_val) {
                    $permission[] = $rec_val['iPageActionID'];
                } return $permission;
            }
        } return array();
    }

}
?>