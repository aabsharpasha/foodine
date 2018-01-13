<?php

class OffersPurchase_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_user_combo';
        $this->load->library('DatatablesHelper');
        $this->load->helper('string');
    }

    /*
     * GET ALL THE RECORDS FROM THE DEALS TABLE...
     */


    function getPaginationResult() {
        $where_condition = '';

        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $where_condition .= ' AND ts.iRestaurantID = "' . $iRestaurantID . '"';
        }
        $fields = array(
            "ts.vRestaurantName AS vRestaurantName",
            "ts.tAddress2 AS restaurantAddress",
            "ts.vPrimRestManagerPhone AS restaurantPhone",
            "ts.vPrimRestManagerEmail AS restaurantEmail",
            "CONCAT(tcso.vOfferTitle,': <BR> ',tcso.tOfferDetail) AS comboDetail",
            "tcso.tDiscountedPrice AS unitPrice",
            "tcso.dCommission AS unitCommission",
            "tuc.iComboSubOffersID AS iComboSubOffersID",
            "sum(tuc.qty) AS quantitySold",
            "sum(IF(tuc.eAvailedStatus='Availed', tuc.qty, 0)) AS quantityRedeemed",
            "sum(IF(tuc.eAvailedStatus='Not Availed', tuc.qty, 0)) AS quantityUnredeemed",
            "(sum(IF(tuc.eAvailedStatus='Availed', tuc.qty, 0)) * tcso.tDiscountedPrice) AS totalRedeemedCost",
            "(sum(IF(tuc.eAvailedStatus='Not Availed', tuc.qty, 0)) * tcso.tDiscountedPrice) AS totalUnredeemedCost",
            "(sum(IF(tuc.eAvailedStatus='Availed', tuc.qty, 0)) * tcso.tDiscountedPrice * tcso.dCommission / 100) AS totalCommission",
            "((sum(IF(tuc.eAvailedStatus='Availed', tuc.qty, 0)) * tcso.tDiscountedPrice) - (sum(IF(tuc.eAvailedStatus='Availed', tuc.qty, 0)) * tcso.tDiscountedPrice * tcso.dCommission / 100)) AS totalPayable",
        );
        $fieldsStr  = implode(", ", $fields);
        $tsJoin     = "LEFT JOIN tbl_restaurant AS ts ON ts.iRestaurantID=tuc.iRestaurantID";
        $tcsoJoin   = "LEFT JOIN tbl_combo_sub_offers AS tcso ON tcso.iComboSubOffersID=tuc.iComboSubOffersID";
        
        $query      = "SELECT $fieldsStr FROM tbl_user_combo AS tuc $tsJoin $tcsoJoin WHERE tuc.eBookingStatus='Paid' $where_condition GROUP BY tuc.iComboSubOffersID";
        $data       = $this->datatableshelper->query($query);
        return $data;
    }

    function getPaginationPurchaseDetailResult($iComboOffersID){
        $where_condition = '';
        $fields = array(
            "CONCAT(tu.vFirstName,' ',tu.vLastName) AS userName",
            "tu.vEmail AS userEmail",
            "tu.vMobileNo AS userPhone",
            "tuc.OrderID As orderId",
            "DATE_FORMAT(tuc.tModifiedAt , '%d %b %Y %h:%i %p') As redemptionDate",
            "tuc.eSaleThrough As saleThrough",
            "'Walk-in' As redemptionType",
        );
        $fieldsStr  = implode(", ", $fields);
        $tuJoin     = "LEFT JOIN tbl_user AS tu ON tu.iUserID=tuc.iUserID";
        
        $query      = "SELECT $fieldsStr FROM tbl_user_combo AS tuc $tuJoin WHERE tuc.eAvailedStatus='Availed' AND iComboSubOffersID='$iComboOffersID'";
        $data       = $this->datatableshelper->query($query);
        return $data;
        
    }
    
}

?>
