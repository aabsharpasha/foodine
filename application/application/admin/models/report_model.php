<?php

class Report_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_report_user';
        $this->load->library('DatatablesHelper');
    }

    // **********************************************************************
    // Display List of Report
    // **********************************************************************
    function getReportDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /*     * **********************************************************************
      Report Setting(Getting report details by admin_id(Selected or inserted)
     * ********************************************************************** */

    function getReportDataById($iBlockID) {
        $result = $this->db->get_where($this->table, array('iBlockID' => $iBlockID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    // **********************************************************************
    // Report Status
    // **********************************************************************
    function changeReportStatus($iBlockID) {
        //$updateData = array('admin_status' => 'IF (admin_status = "Active", "Inactive","Active")');
        //$query = $this->db->update($this->admin_tbl,$updateData, array('admin_role ' => $admin_role));
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iBlockID = $iBlockID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeReport($iBlockID) {
        $adid = implode(',', $iBlockID);
        //mprd($adid);
        $query = $this->db->query("DELETE from $this->table WHERE iBlockID in ($adid) ");
        //$query = $this->db->delete($this->table, array('iBlockID' => $iBlockID));
        //mprd($this->db->last_query());
        if ($this->db->affected_rows() > 0) {
            return $query;
        } else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeComment() {

        /*        $iCommentID = $this->input->post('iCommentID');
          if($iCommentID != ''){
          $query=$this->db->query("DELETE from tbl_comment WHERE iCommentID  = $iCommentID ");
          if ($this->db->affected_rows() > 0)
          {
          return '1';
          }
          else
          return '';
          }
          else */
        return '';
    }

    public function checkReportNameAvailable($vReportName, $iBlockID = '') {
        if ($iBlockID != "") {
            $result = $this->db->query("SELECT `iBlockID`  FROM `tbl_report_user` WHERE (`vReportName` = '" . $vReportName . "') AND  `iBlockID` != $iBlockID");
        } else {
            $this->db->where('vReportName', $vReportName);
            $result = $this->db->get($this->table);
        }

        if ($result->num_rows() >= 1)
            return 0;
        else
            return 1;
    }

    // **********************************************************************
    // add admin
    // **********************************************************************
    function addReport($reportData) {
        extract($reportData);

        $data = array(
            'vReportName' => $vReportName,
            'eStatus' => 'Active',
            'tCreatedAt' => date('Y-m-d H:i:s')
        );
        $query = $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0)
            return $this->db->insert_id();
        else
            return '';
    }

    // **********************************************************************
    // Edit admin
    // **********************************************************************
    function editReport($reportData) {

        extract($reportData);
        $data = array(
            'vReportName' => $vReportName
        );

        $query = $this->db->update($this->table, $data, array('iBlockID' => $iBlockID));
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function editReportData($data, $iBlockID = '') {
        if ($iBlockID != '') {
            $query = $this->db->update($this->table, $data, array('iBlockID' => $iBlockID));
            if ($this->db->affected_rows() > 0)
                return $query;
            else
                return '';
        }else {
            return '';
        }
    }

    function get_paginationresult() {

        $data = $this->datatableshelper->query("SELECT iUserID  , iBlockedUserID  , vReasonType , tCreatedAt , '0' as count_report , iBlockID , iBlockID AS DT_RowId FROM tbl_report_user as r");

        return $data;
    }

    function getCommentList() {
        /*        $iBlockID = $this->input->post('iBlockID');
          if($iBlockID != ''){
          $str = "SELECT iCommentID , CONCAT(vFirstName, ' ', vLastName) as vName   , vReportName as vReport ,  vCommentText , u.iUserID  , u.vProfilePicture , cm.iBlockID , DATE_FORMAT(cm.tCreatedAt,'%d %b %Y %h:%i %p') as tCreatedAt  from tbl_comment cm , tbl_report_user p , tbl_user u , tbl_report_user as s where s.iBlockID = p.iBlockID and u.iUserID = cm.iUserID and p.iBlockID = cm.iBlockID and cm.iBlockID = $iBlockID ";
          $result = $this->db->query($str);
          if ($result->num_rows() > 0)
          return $result->result_array();
          else
          return 'nocomment';
          }else
          return '';
         */
        return '';
    }

    function get_averageRatingResult($dtdb = TRUE) {
        $extra_condition = '';

        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $extra_condition .= ' AND tb.iRestaurantID = "' . $iRestaurantID . '"';
        }

        $table = $fields = $qry = '';
        $condition = array();

        $table = 'tbl_restaurant AS tr LEFT JOIN `tbl_location` AS `tl` ON `tr`.`iLocationID` = `tl`.`iLocationID` LEFT JOIN `tbl_restaurant_review` AS `trr` ON `trr`.`iRestaurantID` = `tr`.`iRestaurantID` AND  `trr`.eStatus = "active"';

        $fields .= 'vRestaurantName';

        $fields .= ', tAddress2 as address';
        $fields .= ', tl.vLocationName AS location';

        $fields .= ', IFNULL(TRUNCATE(AVG((`trr`.iAmbience) /2), 2 ),"0.00")  AS ambience';
        $fields .= ', IFNULL(TRUNCATE(AVG((`trr`.iPrice) /2), 2 ),"0.00")  AS price';
        $fields .= ', IFNULL(TRUNCATE(AVG((`trr`.iFood) /2), 2 ),"0.00")  AS food';
        $fields .= ', IFNULL(TRUNCATE(AVG((`trr`.iService ) /2), 2 ),"0.00")  AS service';
        $fields .= ', IFNULL(TRUNCATE(AVG((`trr`.iAmbience + `trr`.iPrice + `trr`.iFood + `trr`.iService ) /8), 2 ),"0.00")  AS rating';
        $condition[] = '`tr`.eStatus = "Active"';

        $groupBy = ' GROUP BY `tr`.`iRestaurantID`';

        $condition = implode(' AND ', $condition) . $extra_condition;

        $qry .= "SELECT " . $fields . " FROM " . $table . " WHERE " . $condition . $groupBy;

        $data = $dtdb ? $this->datatableshelper->query($qry) : $this->db->query($qry)->result_array();

        return $data;
    }

    function get_bestRatingResult($dtdb = TRUE) {
        $extra_condition = '';

        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $extra_condition .= ' AND tb.iRestaurantID = "' . $iRestaurantID . '"';
        }

        $table = $fields = $qry = '';
        $condition = array();

        $table = 'tbl_restaurant AS tr LEFT JOIN `tbl_location` AS `tl` ON `tr`.`iLocationID` = `tl`.`iLocationID` LEFT JOIN tbl_restaurant_review AS `trr` ON `trr`.`iRestaurantID` = `tr`.`iRestaurantID` AND  `trr`.eStatus = "active"';

        $fields .= 'vRestaurantName';

        $fields .= ', tAddress2 as address';
        $fields .= ', tl.vLocationName AS location';

        $fields .= ', IFNULL(TRUNCATE((SELECT max(`trr`.iAmbience)/2),1),"0")  AS ambience';
        $fields .= ', IFNULL(TRUNCATE((SELECT max(`trr`.iPrice)/2),1),"0")  AS price';
        $fields .= ', IFNULL(TRUNCATE((SELECT max(`trr`.iFood)/2),1),"0")  AS food';
        $fields .= ', IFNULL(TRUNCATE((SELECT max(`trr`.iService)/2),1),"0") AS service';

        $fields .= ', IFNULL(TRUNCATE((SELECT MAX(`trr`.iAmbience + `trr`.iPrice + `trr`.iFood + `trr`.iService )/8),2),"0") AS rating';
        $condition[] = '`tr`.eStatus = "Active"';

        $groupBy = ' GROUP BY `tr`.`iRestaurantID`';

        $condition = implode(' AND ', $condition) . $extra_condition;

        $qry .= "SELECT " . $fields . " FROM " . $table . " WHERE " . $condition . $groupBy;

        $data = $dtdb ? $this->datatableshelper->query($qry) : $this->db->query($qry)->result_array();

        return $data;
    }

    function get_worstRatingResult($dtdb = TRUE) {
        $extra_condition = '';

        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $extra_condition .= ' AND tb.iRestaurantID = "' . $iRestaurantID . '"';
        }

        $table = $fields = $qry = '';
        $condition = array();

        $table = 'tbl_restaurant AS tr LEFT JOIN `tbl_location` AS `tl` ON `tr`.`iLocationID` = `tl`.`iLocationID` LEFT JOIN tbl_restaurant_review AS `trr` ON `trr`.`iRestaurantID` = `tr`.`iRestaurantID` AND  `trr`.eStatus = "active"';

        $fields .= 'vRestaurantName';

        $fields .= ', tAddress2 as address';
        $fields .= ', tl.vLocationName AS location';

        $fields .= ', IFNULL(TRUNCATE((SELECT MIN(`trr`.iAmbience)/2),1),"0")  AS ambience';
        $fields .= ', IFNULL(TRUNCATE((SELECT MIN(`trr`.iPrice)/2),1),"0")  AS price';
        $fields .= ', IFNULL(TRUNCATE((SELECT MIN(`trr`.iFood)/2),1),"0")  AS food';
        $fields .= ', IFNULL(TRUNCATE((SELECT MIN(`trr`.iService)/2),1),"0") AS service';

        $fields .= ', IFNULL(TRUNCATE((SELECT MIN(`trr`.iAmbience + `trr`.iPrice + `trr`.iFood + `trr`.iService )/8),2),"0") AS rating';
        $condition[] = '`tr`.eStatus = "Active"';

        $groupBy = ' GROUP BY `tr`.`iRestaurantID`';

        $condition = implode(' AND ', $condition) . $extra_condition;

        $qry .= "SELECT " . $fields . " FROM " . $table . " WHERE " . $condition . $groupBy;

        $data = $dtdb ? $this->datatableshelper->query($qry) : $this->db->query($qry)->result_array();

        return $data;
    }

    function get_bookmarkedResult($dtdb = TRUE) {

        $extra_condition = '';

        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $extra_condition .= ' AND tb.iRestaurantID = "' . $iRestaurantID . '"';
        }

        $table = $fields = $qry = '';
        $condition = array();

        $table = '`tbl_user_restaurant_favorite` AS `turf`';
        $table .= ' LEFT JOIN `tbl_user` AS `tu` ON `turf`.`iUserID` = `tu`.`iUserID`';
        $table .= ' LEFT JOIN `tbl_restaurant` AS `tr` ON `turf`.`iRestaurantID` = `tr`.`iRestaurantID`';
        $table .= ' LEFT JOIN `tbl_location` AS `tl` ON `tl`.`iLocationID` = `tr`.`iLocationID`';
        $table .= ' LEFT JOIN `tbl_restaurant_category` AS `trc` ON `trc`.`iRestaurantID` = `tr`.`iRestaurantID`';
        $table .= ' LEFT JOIN `tbl_category` AS `tc` ON `tc`.`iCategoryID` = `trc`.`iCategoryID`';
        $table .= ' LEFT JOIN `tbl_restaurant_cuisine` AS `trcs` ON `trcs`.`iRestaurantID` = `tr`.`iRestaurantID`';
        $table .= ' LEFT JOIN `tbl_cuisine` AS `tcs` ON `tcs`.`iCuisineID` = `trcs`.`iCuisineID`';

        $fields .= '`tr`.`vRestaurantName` AS restaurantName';
        $fields .= ', CONCAT(tu.vFirstName," ",tu.vLastName) as customerName';
        $fields .= ', GROUP_CONCAT(DISTINCT `tc`.vCategoryName SEPARATOR ", ") AS category';
        $fields .= ', GROUP_CONCAT(DISTINCT `tcs`.vCuisineName SEPARATOR ", ") AS cuisine';
        $fields .= ', `tr`.`tAddress2` as address';

        $condition[] = '`tr`.eStatus = "Active"';

        $groupBy = ' GROUP BY `tr`.`iRestaurantID`';

        $condition = implode(' AND ', $condition) . $extra_condition;

        $qry .= "SELECT " . $fields . " FROM " . $table . " WHERE " . $condition . $groupBy;

        $data = $dtdb ? $this->datatableshelper->query($qry) : $this->db->query($qry)->result_array();

        return $data;
    }
    
    function get_suggestedResult($dtdb = TRUE) {

        $extra_condition = '';

        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $extra_condition .= ' AND tb.iRestaurantID = "' . $iRestaurantID . '"';
        }

        $table = $fields = $qry = '';
        $condition = array();

        $table = '`tbl_suggested_restaurant` AS `turf`';
        $table .= ' LEFT JOIN `tbl_user` AS `tu` ON `turf`.`user_id` = `tu`.`iUserID`';
        //$table .= ' LEFT JOIN `tbl_restaurant` AS `tr` ON `turf`.`iRestaurantID` = `tr`.`iRestaurantID`';
        //$table .= ' LEFT JOIN `tbl_location` AS `tl` ON `tl`.`iLocationID` = `tr`.`iLocationID`';
        //$table .= ' LEFT JOIN `tbl_restaurant_category` AS `trc` ON `trc`.`iRestaurantID` = `tr`.`iRestaurantID`';
        //$table .= ' LEFT JOIN `tbl_category` AS `tc` ON `tc`.`iCategoryID` = `trc`.`iCategoryID`';
        //$table .= ' LEFT JOIN `tbl_restaurant_cuisine` AS `trcs` ON `trcs`.`iRestaurantID` = `tr`.`iRestaurantID`';
        //$table .= ' LEFT JOIN `tbl_cuisine` AS `tcs` ON `tcs`.`iCuisineID` = `trcs`.`iCuisineID`';

        $fields .= 'turf.restaurant_name AS restaurantName,';
        $fields .= 'turf.res_address as address';
       // $fields .= '`turf`.`restaurant_name` AS restaurantName';
        $fields .= ', CONCAT(tu.vFirstName," ",tu.vLastName) as customerName';
        //$fields .= ', GROUP_CONCAT(DISTINCT `tc`.vCategoryName SEPARATOR ", ") AS category';
        //$fields .= ', GROUP_CONCAT(DISTINCT `tcs`.vCuisineName SEPARATOR ", ") AS cuisine';
        //$fields .= ', `tr`.`tAddress2` as address';

        //$condition[] = '`tr`.eStatus = "Active"';

        //$groupBy = ' GROUP BY `tr`.`iRestaurantID`';

        $condition = implode(' AND ', $condition) . $extra_condition;

        $qry .= "SELECT " . $fields . " FROM " . $table; //" WHERE " . $condition . $groupBy;
//echo $qry; exit;
       // $dtdb = 0;
        $data = $dtdb ? $this->datatableshelper->query($qry) : $this->db->query($qry)->result_array();

        return $data;
    }

    function getRestaurants(){
        $query    = "SELECT iRestaurantID,vRestaurantName FROM tbl_restaurant WHERE eStatus <>'pending' ORDER BY vRestaurantName ASC";
        $result   = $this->db->query($query);
        return $result->result_array();
    }
    
    function getOfferTypes(){
        $query    = "SELECT offerTypeId, offerTypeName FROM tbl_offer_type WHERE status<>'Deleted'";
        $result   = $this->db->query($query);
        return $result->result_array();
    }

    function getSearchedText(){
        $query    = "SELECT DISTINCT(vSearchText) AS vSearchText FROM tbl_search WHERE eSearchType='Indirect' ORDER BY vSearchText ASC";
        $result   = $this->db->query($query);
        return $result->result_array();
    }
    
    function getCategories(){
        $query    = "SELECT iCategoryID, vCategoryName FROM tbl_category WHERE eStatus <> 'Deleted' ORDER BY vCategoryName ASC";
        $result   = $this->db->query($query);
        return $result->result_array();
    }
    
    function paginateMostSuccessfullDeal($requestData){
        $where_condition = array();
        if(!empty($requestData["iRestaurantID"])){
            $where_condition[]  = "td.iRestaurantID=".$requestData["iRestaurantID"];
        }
        if(!empty($requestData["iOfferType"])){
            $where_condition[]  = "td.iOfferType=".$requestData["iOfferType"];
        }
        if( !empty($requestData["validityStartDate"]) && !empty($requestData["validityEndDate"]) ){
            $where_condition[]  = "(DATE(td.dtStartDate) >='".date('Y-m-d', strtotime($requestData["validityStartDate"]))."'"
                    ." AND DATE(td.dtStartDate) <='".date('Y-m-d',strtotime($requestData["validityEndDate"]))."'"
                    ." OR "
                    ."DATE(td.dtExpiryDate) >='".date('Y-m-d',strtotime($requestData["validityStartDate"]))."'"
                    ." AND DATE(td.dtExpiryDate) <='".date('Y-m-d',strtotime($requestData["validityEndDate"]))."'"
                    ." OR "
                    ."DATE(td.dtStartDate) <'".date('Y-m-d',strtotime($requestData["validityStartDate"]))."'"
                    ." AND DATE(td.dtExpiryDate) >'".date('Y-m-d',strtotime($requestData["validityEndDate"]))."')";
        }
    
        $fields = array(
            "td.iDealID AS iDealID",
            "td.iDealID AS DT_RowId",
            "(SELECT vRestaurantName FROM tbl_restaurant AS ts WHERE ts.iRestaurantID=td.iRestaurantID) AS restaurantName",
            "td.vOfferText AS vOfferText"
        );
        
        if( !empty($requestData["redemptionStartDate"]) && !empty($requestData["redemptionEndDate"]) ){
            
            $query    = "SELECT iDealID, count(iDealID) AS recCount FROM tbl_deals_code WHERE DATE(dtAvailedDate) >='".date('Y-m-d',strtotime($requestData["redemptionStartDate"]))."' AND DATE(dtAvailedDate) <='".date('Y-m-d',strtotime($requestData["redemptionEndDate"]))."' AND `iTableBookID` IS NULL GROUP BY iDealID HAVING recCount>2";
            $result   = $this->db->query($query)->result_array();
            $deals  = array();
            foreach($result AS $row){
               $deals[] = $row["iDealID"];
            }
            if(!empty($deals)){
                $deals = implode(", ",$deals);
                $where_condition[]  = "td.iDealID IN($deals)";
            }else{
                $where_condition[]  = "td.iDealID IN('')";
            }
            $fields[]   = "(SELECT COUNT(*) FROM tbl_deals_code AS tdc WHERE tdc.iDealID=td.iDealID AND tdc.`iTableBookID` IS NULL AND tdc.eStatus='availed' AND DATE(tdc.dtAvailedDate) >='".date('Y-m-d',strtotime($requestData["redemptionStartDate"]))."' AND DATE(tdc.dtAvailedDate) <='".date('Y-m-d',strtotime($requestData["redemptionEndDate"]))."') AS useCount";
        }else{
            $fields[]   = "(SELECT COUNT(*) FROM tbl_deals_code AS tdc WHERE tdc.iDealID=td.iDealID AND tdc.`iTableBookID` IS NULL AND tdc.eStatus='availed') AS useCount";
        }
        $fields[]   = "(SELECT offerTypeName FROM tbl_offer_type AS tot WHERE tot.offerTypeId=td.iOfferType) AS offerType";
        $fields[]   = "DATE_FORMAT(td.dtExpiryDate ,'%d %b %Y %h:%i %p') AS dtExpiryDate";
        $fields[]   = "DATE_FORMAT(td.dtStartDate ,'%d %b %Y %h:%i %p') AS dtStartDate";
        $fields[]   = "td.eStatus AS eStatus";
        $fields[]   = "DATE_FORMAT(td.tCreatedAt ,'%d %b %Y %h:%i %p') AS tCreatedAt";
        
        $conditionStr   = implode(" AND ", $where_condition);
        if($conditionStr){
            $conditionStr   = " WHERE ".$conditionStr;
        }
        
        $fieldsStr  = implode(", ",$fields);
        
        $data = $this->datatableshelper->query("SELECT $fieldsStr  FROM tbl_deals AS td " . $conditionStr." HAVING useCount > 2");
        return $data;
    }
    
    function paginateWorstPerformingDeal($requestData){
        $where_condition = array();
        if(!empty($requestData["iRestaurantID"])){
            $where_condition[]  = "td.iRestaurantID=".$requestData["iRestaurantID"];
        }
        if(!empty($requestData["iOfferType"])){
            $where_condition[]  = "td.iOfferType=".$requestData["iOfferType"];
        }
        if( !empty($requestData["validityStartDate"]) && !empty($requestData["validityEndDate"]) ){
            $where_condition[]  = "(DATE(td.dtStartDate) >='".date('Y-m-d', strtotime($requestData["validityStartDate"]))."'"
                    ." AND DATE(td.dtStartDate) <='".date('Y-m-d',strtotime($requestData["validityEndDate"]))."'"
                    ." OR "
                    ."DATE(td.dtExpiryDate) >='".date('Y-m-d',strtotime($requestData["validityStartDate"]))."'"
                    ." AND DATE(td.dtExpiryDate) <='".date('Y-m-d',strtotime($requestData["validityEndDate"]))."'"
                    ." OR "
                    ."DATE(td.dtStartDate) <'".date('Y-m-d',strtotime($requestData["validityStartDate"]))."'"
                    ." AND DATE(td.dtExpiryDate) >'".date('Y-m-d',strtotime($requestData["validityEndDate"]))."')";
        }
    
        $fields = array(
            "td.iDealID AS iDealID",
            "td.iDealID AS DT_RowId",
            "(SELECT vRestaurantName FROM tbl_restaurant AS ts WHERE ts.iRestaurantID=td.iRestaurantID) AS restaurantName",
            "td.vOfferText AS vOfferText"
        );
        
        if( !empty($requestData["redemptionStartDate"]) && !empty($requestData["redemptionEndDate"]) ){
            
            $query    = "SELECT iDealID, count(iDealID) AS recCount FROM tbl_deals_code WHERE DATE(dtAvailedDate) >='".date('Y-m-d',strtotime($requestData["redemptionStartDate"]))."' AND DATE(dtAvailedDate) <='".date('Y-m-d',strtotime($requestData["redemptionEndDate"]))."' AND `iTableBookID` IS NULL GROUP BY iDealID HAVING recCount <= 2";
            $result   = $this->db->query($query)->result_array();
            $deals  = array();
            foreach($result AS $row){
               $deals[] = $row["iDealID"];
            }
            if(!empty($deals)){
                $deals = implode(", ",$deals);
                $where_condition[]  = "td.iDealID IN($deals)";
            }else{
                $where_condition[]  = "td.iDealID IN('')";
            }
            $fields[]   = "(SELECT COUNT(*) FROM tbl_deals_code AS tdc WHERE tdc.iDealID=td.iDealID AND tdc.eStatus='availed' AND tdc.`iTableBookID` IS NULL AND DATE(tdc.dtAvailedDate) >='".date('Y-m-d',strtotime($requestData["redemptionStartDate"]))."' AND DATE(tdc.dtAvailedDate) <='".date('Y-m-d',strtotime($requestData["redemptionEndDate"]))."') AS useCount";
        }else{
            $fields[]   = "(SELECT COUNT(*) FROM tbl_deals_code AS tdc WHERE tdc.iDealID=td.iDealID AND tdc.`iTableBookID` IS NULL AND tdc.eStatus='availed') AS useCount";
        }
        
        $fields[]   = "(SELECT offerTypeName FROM tbl_offer_type AS tot WHERE tot.offerTypeId=td.iOfferType) AS offerType";
        $fields[]   = "DATE_FORMAT(td.dtExpiryDate ,'%d %b %Y %h:%i %p') AS dtExpiryDate";
        $fields[]   = "DATE_FORMAT(td.dtStartDate ,'%d %b %Y %h:%i %p') AS dtStartDate";
        $fields[]   = "td.eStatus AS eStatus";
        $fields[]   = "DATE_FORMAT(td.tCreatedAt ,'%d %b %Y %h:%i %p') AS tCreatedAt";
        
        $conditionStr   = implode(" AND ", $where_condition);
        if($conditionStr){
            $conditionStr   = " WHERE ".$conditionStr;
        }
        
        $fieldsStr  = implode(", ",$fields);
//        exit("SELECT $fieldsStr  FROM tbl_deals AS td " . $conditionStr." HAVING useCount <= 2");
        $data = $this->datatableshelper->query("SELECT $fieldsStr  FROM tbl_deals AS td " . $conditionStr." HAVING useCount <= 2");
        return $data;
    }
    
    function paginateCheckIn($requestData){
        $where_condition = array();
        if(!empty($requestData["iRestaurantID"])){
            $where_condition[]  = "tuc.iRestaurantID=".$requestData["iRestaurantID"];
        }
        if( !empty($requestData["startDate"]) && !empty($requestData["endDate"]) ){
            
             $where_condition[] = "DATE(tuc.tCreatedAt) >='".date('Y-m-d',strtotime($requestData["startDate"]))."' AND DATE(tuc.tCreatedAt) <='".date('Y-m-d',strtotime($requestData["endDate"]))."'";
        }
    
        $fields = array(
            "DATE_FORMAT(tuc.tCreatedAt ,'%d %b %Y %h:%i %p') AS tCreatedAt",
            "tuc.iCheckInID AS iCheckInID",
            "tuc.iCheckInID AS DT_RowId",
            "(SELECT ts.vRestaurantName FROM tbl_restaurant AS ts WHERE ts.iRestaurantID=tuc.iRestaurantID) AS restaurantName",
            "count(tuc.iCheckInID) AS totalCheckIns",
            "SUM(IF((Select count(ttb.iTableBookID) FROM tbl_table_book AS ttb WHERE ttb.eBookingStatus='Accept' AND ttb.iUserID=tuc.iUserID AND ttb.iRestaurantID=tuc.iRestaurantID AND DATE(ttb.tDateTime)=DATE(tuc.tCreatedAt)), 1, 0)) AS bookingCheckIns"
        );

        $conditionStr   = implode(" AND ", $where_condition);
        if($conditionStr){
            $conditionStr   = " WHERE ".$conditionStr;
        }
        $fieldsStr  = implode(", ",$fields);
        $data = $this->datatableshelper->query("SELECT $fieldsStr  FROM tbl_user_checkin AS tuc " . $conditionStr." GROUP BY tuc.tCreatedAt,tuc.iRestaurantID");
        return $data;
    }
    
    function paginateDirectSearch($requestData){
        $where_condition = array("eSearchType='Direct'");
        if(!empty($requestData["searchText"])){
            $where_condition[]  = "ts.vSearchText= '".$requestData["searchText"]."'";
        }
        if( !empty($requestData["startDate"]) && !empty($requestData["endDate"]) ){
            
             $where_condition[] = "DATE(ts.tCreatedAt) >='".date('Y-m-d',strtotime($requestData["startDate"]))."' AND DATE(ts.tCreatedAt) <='".date('Y-m-d',strtotime($requestData["endDate"]))."'";
        }
    
        $fields = array(
            "DATE_FORMAT(ts.tCreatedAt ,'%d %b %Y %h:%i %p') AS tCreatedAt",
            "ts.iSearchId AS DT_RowId",
            "ts.vSearchText AS vSearchText",
            "count(ts.vSearchText) AS searchCount"
        );

        $conditionStr   = implode(" AND ", $where_condition);
        if($conditionStr){
            $conditionStr   = " WHERE ".$conditionStr;
        }
        $fieldsStr  = implode(", ",$fields);
//        echo "SELECT $fieldsStr  FROM tbl_search AS ts " . $conditionStr." GROUP BY ts.vSearchText ";exit;
        $data = $this->datatableshelper->query("SELECT $fieldsStr  FROM tbl_search AS ts " . $conditionStr." GROUP BY ts.vSearchText ");
        return $data;
    }
    
    function paginateIndirectSearch($requestData){
        $where_condition = array("eSearchType='Indirect'");
        if(!empty($requestData["searchText"])){
            $where_condition[]  = "ts.vSearchText= '".$requestData["searchText"]."'";
        }
        if( !empty($requestData["startDate"]) && !empty($requestData["endDate"]) ){
            
             $where_condition[] = "DATE(ts.tCreatedAt) >='".date('Y-m-d',strtotime($requestData["startDate"]))."' AND DATE(ts.tCreatedAt) <='".date('Y-m-d',strtotime($requestData["endDate"]))."'";
        }
    
        $fields = array(
            "DATE_FORMAT(ts.tCreatedAt ,'%d %b %Y %h:%i %p') AS tCreatedAt",
            "ts.iSearchId AS DT_RowId",
            "ts.vSearchText AS vSearchText",
            "count(ts.vSearchText) AS searchCount"
        );

        $conditionStr   = implode(" AND ", $where_condition);
        if($conditionStr){
            $conditionStr   = " WHERE ".$conditionStr;
        }
        $fieldsStr  = implode(", ",$fields);
//        echo "SELECT $fieldsStr  FROM tbl_search AS ts " . $conditionStr." GROUP BY ts.vSearchText ";exit;
        $data = $this->datatableshelper->query("SELECT $fieldsStr  FROM tbl_search AS ts " . $conditionStr." GROUP BY ts.vSearchText ");
        return $data;
    }
    
    function paginateRankInLocality($requestData){
        $where_condition = array();
        if(!empty($requestData["iRestaurantID"])){
            $where_condition[]  = "ts.iRestaurantID=".$requestData["iRestaurantID"];
        }
        if(!empty($requestData["iCategoryID"])){
            $where_condition[]  = "trc.iCategoryID=".$requestData["iCategoryID"];
        }
        $clickWhere     = '';
        $bookingWhere   = '';
        if( !empty($requestData["startDate"]) && !empty($requestData["endDate"]) ){
            $clickWhere     = "AND DATE(turv.tCreatedAt) >='".date('Y-m-d', strtotime($requestData["startDate"]))."'"
                    . " AND DATE(turv.tCreatedAt) <='".date('Y-m-d',strtotime($requestData["endDate"]))."'";
            $bookingWhere   = "AND DATE(ttb.tCreatedAt) >='".date('Y-m-d', strtotime($requestData["startDate"]))."'"
                    . " AND DATE(ttb.tCreatedAt) <='".date('Y-m-d',strtotime($requestData["endDate"]))."'";
        }
        $fields = array(
            "ts.iRestaurantID AS iRestaurantID",
            "ts.iRestaurantID AS DT_RowId",
            "ts.vRestaurantName AS vRestaurantName",
            "(SELECT SUM(turv.iCount) FROM tbl_user_restaurant_viewed AS turv WHERE turv.iRestaurantID=ts.iRestaurantID $clickWhere) AS clicks",
            "GROUP_CONCAT((SELECT tc.vCategoryName FROM tbl_category AS tc WHERE tc.iCategoryID=trc.iCategoryID)) AS iCategoryID",
            "(SELECT tl.vLocationName FROM tbl_location AS tl WHERE tl.iLocationID=ts.iLocationID) AS vLocationName",
            "(SELECT COUNT(ttb.iTableBookID) FROM tbl_table_book AS ttb WHERE ttb.iRestaurantID=ts.iRestaurantID $bookingWhere) AS tableBooking"
        );

        $trcJoin    = "INNER JOIN tbl_restaurant_category AS trc ON trc.iRestaurantID=ts.iRestaurantID";
        $conditionStr   = implode(" AND ", $where_condition);
        if($conditionStr){
            $conditionStr   = " WHERE ".$conditionStr;
        }

        $fieldsStr  = implode(", ",$fields);
        $data = $this->datatableshelper->query("SELECT $fieldsStr  FROM tbl_restaurant AS ts $trcJoin " . $conditionStr." GROUP BY ts.iRestaurantID");
    
        return $data;
        
    }
}

?>
