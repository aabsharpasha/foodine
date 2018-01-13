<?php

class Banner_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_banner';
        $this->load->library('DatatablesHelper');
    }

    function get_paginationresult($type) {
        $where_condition = '';
        $bannerName = "CASE eType "
                . "WHEN 'featured' THEN (SELECT vRestaurantName FROM tbl_restaurant WHERE tbl_restaurant.iRestaurantID=tbl_banner.iTypeId) "
                . "WHEN 'event' THEN (SELECT iEventTitle FROM tbl_restaurant_event WHERE tbl_restaurant_event.iEventId=tbl_banner.iTypeId) "
                . "WHEN 'deals' THEN (SELECT vOfferText FROM tbl_deals WHERE tbl_deals.iDealID=tbl_banner.iTypeId) "
                . "WHEN 'combo' THEN (SELECT vOfferText FROM tbl_combo_offers WHERE tbl_combo_offers.iComboOffersID=tbl_banner.iTypeId) "
                . "ELSE NULL "
                . "END as bannerName";
        
        $data = $this->datatableshelper->query("SELECT iBannerId, iBannerId AS DT_RowId, vLabel, tText, DATE_FORMAT(tStartDate , '%d %b %Y, %h:%i %p') AS tStartDate, DATE_FORMAT(tEndDate , '%d %b %Y, %h:%i %p') AS tEndDate, eType, eStatus, dCreatedAt, dModifiedAt, $bannerName FROM tbl_banner WHERE estatus<>'Deleted' AND eType='$type'" . $where_condition);
        return $data;
    }

    function getRestaurantDataAll() {
        $query = "SELECT vRestaurantName AS name, iRestaurantID AS id FROM tbl_restaurant WHERE eStatus='Active'";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getEventDataAll($restId) {
        $query = "SELECT iEventTitle AS name, iEventId AS id FROM tbl_restaurant_event WHERE iRestaurantId = $restId AND eStatus='Active' AND  CURDATE() between dEventStartDate and dEventEndDate";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getDealsDataAll($restId) {
        $query = "SELECT vOfferText AS name, iDealID AS id FROM tbl_deals WHERE iRestaurantID = $restId AND eStatus='Active' AND CURDATE() between dtStartDate and dtExpiryDate";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getComboDataAll($restId) {
        $query = "SELECT vOfferText AS name, iComboOffersID AS id FROM tbl_combo_offers WHERE iRestaurantID = $restId AND eStatus='Active' AND CURDATE() between dtStartDate and dtExpiryDate";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function softDeleteBanner($id) {
        $adid = implode(',', $id);
        $this->db->query("UPDATE $this->table SET eStatus = 'Deleted' WHERE iBannerId in ($adid) ");
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function changeStatus($id) {
        $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iBannerId = $id");
        if ($this->db->affected_rows() > 0)
            return true;
        else
            return false;
    }

    function getBannerDataById($id, $bannerType) {
        $result = $this->db->get_where($this->table, array('iBannerId' => $id));

        if ($result->num_rows() > 0) {
            $returnData = $result->row_array();
            if ($bannerType == 'featured') {
                $restId = $returnData['iTypeId'];
            } else if ($bannerType == 'combo') {
                $query = "SELECT iRestaurantID AS id FROM tbl_combo_offers WHERE iComboOffersID=" . $returnData['iTypeId'];
                $result = $this->db->query($query);
                $data = $result->row_array();
                $restId = $data['id'];
            } else if ($bannerType == 'deals') {
                $query = "SELECT iRestaurantID AS id FROM tbl_deals WHERE iDealID=" . $returnData['iTypeId'];
                $result = $this->db->query($query);
                $data = $result->row_array();
                $restId = $data['id'];
            } else if ($bannerType == 'event') {
                $query = "SELECT iRestaurantId AS id FROM tbl_restaurant_event WHERE iEventId=" . $returnData['iTypeId'];
                $result = $this->db->query($query);
                $data = $result->row_array();
                $restId = $data['id'];
            }
            if ($restId != '') {
                $returnData['iRestaurantId'] = $restId;
            }
            return $returnData;
        } else {
            return '';
        }
    }

    function addBanner($data) {
        $data['tStartDate'] = date("Y-m-d ", strtotime($data['tStartDate'])) . $data['startHours'] . ":" . $data['startMinutes'] . ":00";
        $data['tEndDate'] = date("Y-m-d ", strtotime($data['tEndDate'])) . $data['endHours'] . ":" . $data['endMinutes'] . ":00";
        $data['dCreatedAt'] = date("Y-m-d H:i:s");
        $data['dModifiedAt'] = date("Y-m-d H:i:s");
        $data['eType'] = $data['type'];
        unset($data['action']);
        unset($data['startHours']);
        unset($data['startMinutes']);
        unset($data['endHours']);
        unset($data['endMinutes']);
        unset($data['type']);
        unset($data['iRestaurantId']);
//        print_r($data); exit;
        if ($data['tStartDate'] > $data['tEndDate']) {
            return '';
        }
        $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        } else {
            return '';
        }
    }

    function editBanner($data) {
        $iVoucherID = $data['iBannerId'];
        $data['tStartDate'] = date("Y-m-d ", strtotime($data['tStartDate'])) . $data['startHours'] . ":" . $data['startMinutes'] . ":00";
        $data['tEndDate'] = date("Y-m-d ", strtotime($data['tEndDate'])) . $data['endHours'] . ":" . $data['endMinutes'] . ":00";
        $data['dCreatedAt'] = date("Y-m-d H:i:s");
        $data['dModifiedAt'] = date("Y-m-d H:i:s");
        $data['eType'] = $data['type'];
        unset($data['iBannerId']);
        unset($data['action']);
        unset($data['startHours']);
        unset($data['startMinutes']);
        unset($data['endHours']);
        unset($data['endMinutes']);
        unset($data['type']);
        unset($data['iRestaurantId']);
//        print_r($data); exit;
        if ($data['tStartDate'] > $data['tEndDate']) {
            return '';
        }
        $this->db->update($this->table, $data, array('iBannerId' => $iVoucherID));
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    function updateBannerImage($id, $imageName = '') {
        try {
            if (@$imageName !== '') {
                $this->db->update($this->table, array('vBannerImage' => $imageName), array('iBannerId' => $id));
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            throw new Exception('Error in updateCuisineImage function - ' . $ex);
        }
    }

}
