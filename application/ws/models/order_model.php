<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of link_model
 * link hashes expiry 24 hrs
 * @author Amit Malakar
 */
class Order_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        //$this->table = 'tbl_booking';
    }

    /*
     * GET RESTAURANT SLOT VALUES
     */

    function getMenuList($restaurantId = '') {

        try {
            if ($restaurantId != '') {

                $fields = array(
                    'tm.iItemId as itemId',
                    'tm.iRestaurantID as restaurantId',
                    'tr.vRestaurantName as restaurantName',
                    'tm.vItemName as itemName',
                    'tm.tItemDesc as itemDesc',
                    'tm.dItemPrice as itemPrice',
                    'tm.iItemCategoryId as labelName',
                    'tm.iMealTypeId as mealTypeId',
                    'CONCAT("' . BASEURL . 'images/menu/", IF(tm.vItemImage = \'\', "default.png", CONCAT(tm.iRestaurantID,\'/\',tm.vItemImage)) ) AS itemImage',
                );
                //$fields[] = 'td.vOfferText AS offerText';

                $tbl = array(
                    'tbl_menu_item AS tm',
                    'tbl_restaurant AS tr',
                    'tbl_meal_type AS tmt',
                );
                //$tbl[] = 'tbl_deals AS td';

                $condition = array(
                    'tm.iRestaurantID IN(' . $restaurantId . ')',
                    'tm.eStatus = "Active"',
                    'tm.iRestaurantID = tr.iRestaurantID',
                    'tm.iMealTypeId = tmt.iMealTypeId'
                );
                //$condition[] = 'td.iRestaurantID = tr.iRestaurantID';
                //$condition[] = 'CURDATE() between td.dtStartDate and td.dtExpiryDate';

                $fields = implode(',', $fields);
                $tbl = ' FROM ' . implode(',', $tbl);
                $condition = ' WHERE ' . implode(' AND ', $condition);
                $orderBy = ' ORDER BY tm.iItemCategoryId ASC';
                $limit = '';

                $qry = 'SELECT ' . $fields . $tbl . $condition . $orderBy . $limit;

                $res = $this->db->query($qry);
                return $res->result_array();
            }return '';
        } catch (Exception $ex) {
            throw new Exception('Error in getMenuList function - ' . $ex);
        }
    }

    public function saveOrderDetail($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $INS = array(
                    'restaurantId' => $restaurantId,
                    'restaurantName' => $restaurantName,
                    'itemId' => $itemId,
                    'itemName' => $itemName,
                    'itemPrice' => $itemPrice,
                    'qty' => $qty,
                    'itemTotalPrice' => $itemTotalPrice,
                    'userId' => $userId,
                    'userName' => $userName,
                    'userMobile' => $userMobile,
                    'createdAt' => date('Y:m:d H:i:s')
                );
                $this->db->insert('tbl_user_online_order', $INS);
                $insId = $this->db->insert_id();
                return $insId;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in savecomboCartDetail function - ' . $ex);
        }
    }

}
