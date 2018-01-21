<?php

class Notification_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_push_notification';
        $this->load->library('DatatablesHelper');
    }

    /*
     * GET PAGINATION RECORDS TO DISPLAY THE TABLE...
     */

    function get_paginationresult() {
        $extra_condition = '';

        $fields = array(
            "IF(tpn.eCriteria='Age',CONCAT('Age Between ',tpn.iMinAge,' to ',tpn.iMaxAge), IF(tpn.eCriteria='Location', CONCAT('Location: ',(SELECT tl.vLocationName FROM tbl_location AS tl WHERE tl.iLocationID=tpn.iLocationID)), tpn.eCriteria)) AS eCriteria",
            "tpn.vNotifyText AS vNotifyText",
            "tpn.eLink AS eLink",
            "tpn.vImage AS vImage",
            "tScheduleDate",
            "DATE_FORMAT(tpn.tCreatedAt ,'%d %b %Y %h:%i %p') AS scheduleDate",
            "tpn.eStatus AS eStatus",
            "DATE_FORMAT(tCreatedAt ,'%d %b %Y %h:%i %p') AS tCreatedAt",
            "tpn.iPushNotifyID AS iPushNotifyID",
            "tpn.iPushNotifyID AS DT_RowId",
        );

        $fieldStr   = implode(", ",$fields);
        $qry = "SELECT $fieldStr FROM $this->table AS tpn WHERE eStatus <>'Deleted'";

        $data = $this->datatableshelper->query($qry);

        return $data;
    }

    /*
     * DELETE THE DEALS EITHER ONE OR MORE...
     */

    function removeBooking($iBookingID) {
        $adid = implode(',', $iBookingID);

        $query = $this->db->query("DELETE from $this->table WHERE iBookingID IN ($adid) ");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    /*
     * TO CHANGE THE STATUS OF THE SELECTED RECORD...
     */

    function changeBookingStatus($iBookingID) {
        $qry = 'UPDATE ' . $this->table . ' SET eBookingStatus = IF(eBookingStatus = "Pending", "Accept", IF(eBookingStatus = "Accept", "Reject", "Accept") ) WHERE iTableBookID = "' . $iBookingID . '"';
        $query = $this->db->query($qry);
    }

    /*
     * TO CHECK THAT PUSH NOTIFICATION TITLE IS ALREADY EXITS OR NOT??
     */

    function checkNotificationNameAvailable($title = '') {
        try {
            if ($title != '') {
                return $this->db->get_where('tbl_push_notification', array('vNotifyTitle' => $title))->num_rows() > 0 ? FALSE : TRUE;
            } return FALSE;
        } catch (Exception $ex) {
            throw new Exception('Error incheckNotificationNameAvailable function - ' . $ex);
        }
    }

    /*
     * SEND NOTIFICATION TO THE END USERS
     */

    function sendNotification($requestValue = array()) {
        try {
            if (!empty($requestValue)) {
                extract($requestValue);

                /*
                 * ADD RECORD IN THE DATABASE
                 */
                $scheduleDate = date('Y-m-d', strtotime($scheduleDate));
                $scheduleTime = date('H:i:s', strtotime($scheduleTime));

                $INS = array(
                    'vNotifyTitle' => $vNotifyTitle,
                    'vNotifyText' => $vNotifyText,
                    'eNotifyType' => isset($scheduleJob) ? $scheduleJob : 'simple',
                    'tScheduleDate' => date('Y-m-d H:i:s', strtotime($scheduleDate . ' ' . $scheduleTime)),
                    'tCreatedAt' => date('Y-m-d H:i:s')
                );

                $this->db->insert('tbl_push_notification', $INS);

                if (!isset($scheduleJob)) {
                    /*
                     * BRAODCAST USER TO SNS
                     * iOS
                     */
                    $DEFAULT_ARR = array(
                        'IS_LIVE' => IS_NOTIFICATION_LIVE,
                        'PLATEFORM_TYPE' => 'ios'
                    );

                    $this->load->library('sns', $DEFAULT_ARR);
                    $this->sns->broadCast($vNotifyText, TOPIC_ARN);
                }

                return 1;
            } return -2;
        } catch (Exception $ex) {
            throw new Exception('Error in sendNotification function - ' . $ex);
        }
    }
    
    function addNotification($data){
        $vLinkData   = array();
        if(!empty($data["eLink"])){
            switch ($data["eLink"]) {
                case "Restaurant":
                    if(!empty($data["linkedRestaurant"])){
                        $vLinkData["linkedRestaurant"]  = $data["linkedRestaurant"];
                    }
                    break;
                case "Featured":
//                    if(!empty($data["linkedFeaturedRestaurant"])){
//                        $vLinkData["linkedFeaturedRestaurant"]  = $data["linkedFeaturedRestaurant"];
//                    }
                    break;
                case "Events":
                    if(!empty($data["linkedEvent"])){
                        $vLinkData["linkedRestaurant"]  = $data["linkedRestaurant"];
                        $vLinkData["linkedEvent"]  = $data["linkedEvent"];
                    }
                    break;
                case "EventsListing":
                    $data["eLink"]  = "Events";
                    break;
                case "Combo":
                    if(!empty($data["linkedCombo"])){
                        $vLinkData["linkedRestaurant"]  = $data["linkedRestaurant"];
                        $vLinkData["linkedCombo"]  = $data["linkedCombo"];
                    }
                    break;
                case "ComboListing":
                    $data["eLink"]  = "Combo";
                    break;
                case "Offer":
                    if(!empty($data["linkedOffer"])){
                        $vLinkData["linkedRestaurant"]  = $data["linkedRestaurant"];
                        $vLinkData["linkedOffer"]  = $data["linkedOffer"];
                    }
                    break;
                case "OfferListing":
                    $data["eLink"]  = "Offer";
                    break;

                default:
                    break;
            }
        }
        if(!empty($vLinkData)){
            $data["vLinkData"]  = json_encode($vLinkData);
        }
        $scheduleDate           = date('Y-m-d', strtotime($data['scheduleDate']));
        $scheduleTime           = date('H:i:s', strtotime($data['scheduleTime']));
        $data['tScheduleDate']  = $scheduleDate." ".$scheduleTime;
        $data['tCreatedAt']     = date("Y-m-d H:i:s");
        $data['tModifiedAt']    = date("Y-m-d H:i:s");
        $data['vNotifyText']    = $data['vNotifyText'];
        unset($data['scheduleDate']);
        unset($data['scheduleTime']);
        unset($data['senditnow']);
        unset($data['linkedRestaurant']);
        unset($data['linkedFeaturedRestaurant']);
        unset($data['linkedEvent']);
        unset($data['linkedCombo']);
        unset($data['linkedOffer']);
        unset($data['action']);
        
        $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }else{
            return false;
        }
    }
    
    function editNotification($data){
        $id         = $data['iPushNotifyID'];
        $vLinkData  = array();
        if(!empty($data["eLink"])){
            switch ($data["eLink"]) {
                case "Restaurant":
                    if(!empty($data["linkedRestaurant"])){
                        $vLinkData["linkedRestaurant"]  = $data["linkedRestaurant"];
                    }
                    break;
                case "Featured":
//                    if(!empty($data["linkedFeaturedRestaurant"])){
//                        $vLinkData["linkedFeaturedRestaurant"]  = $data["linkedFeaturedRestaurant"];
//                    }
                    break;
                case "Events":
                    if(!empty($data["linkedEvent"])){
                        $vLinkData["linkedRestaurant"]  = $data["linkedRestaurant"];
                        $vLinkData["linkedEvent"]  = $data["linkedEvent"];
                    }
                    break;
                case "EventsListing":
                    $data["eLink"]  = "Events";
                    break;
                case "Combo":
                    if(!empty($data["linkedCombo"])){
                        $vLinkData["linkedRestaurant"]  = $data["linkedRestaurant"];
                        $vLinkData["linkedCombo"]  = $data["linkedCombo"];
                    }
                    break;
                case "ComboListing":
                    $data["eLink"]  = "Combo";
                    break;
                case "Offer":
                    if(!empty($data["linkedOffer"])){
                        $vLinkData["linkedRestaurant"]  = $data["linkedRestaurant"];
                        $vLinkData["linkedOffer"]  = $data["linkedOffer"];
                    }
                    break;
                case "OfferListing":
                    $data["eLink"]  = "Offer";
                    break;

                default:
                    break;
            }
        }
        if(!empty($vLinkData)){
            $data["vLinkData"]  = json_encode($vLinkData);
        }
        $scheduleDate           = date('Y-m-d', strtotime($data['scheduleDate']));
        $scheduleTime           = date('H:i:s', strtotime($data['scheduleTime']));
        $data['tScheduleDate']  = $scheduleDate." ".$scheduleTime;
        $data['tModifiedAt']    = date("Y-m-d H:i:s");
        unset($data['iPushNotifyID']);
        unset($data['scheduleDate']);
        unset($data['scheduleTime']);
        unset($data['senditnow']);
        unset($data['linkedRestaurant']);
        unset($data['linkedFeaturedRestaurant']);
        unset($data['linkedEvent']);
        unset($data['linkedCombo']);
        unset($data['linkedOffer']);
        unset($data['action']);
        
        $this->db->update($this->table, $data, array('iPushNotifyID' => $id));
        if ($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
        
    }
    
    function updateNotificationImage($id, $image){
        $this->db->query("UPDATE $this->table SET vImage='$image' WHERE iPushNotifyID IN ($id) ");
    }

    function getNotificationById($id){
        $result = $this->db->get_where($this->table, array('iPushNotifyID' => $id));
        if ($result->num_rows() > 0){
            $data   = $result->row_array();
            $data["scheduleDate"]   = date('j F, Y', strtotime($data["tScheduleDate"]));
            $data["scheduleTime"]   = date('g:i A', strtotime($data["tScheduleDate"]));
            if(!empty($data["vLinkData"])){ 
                $vLinkData   = (Array)json_decode($data["vLinkData"]);
                if(!empty($vLinkData["linkedRestaurant"])){
                    $data["linkedRestaurant"]   = $vLinkData["linkedRestaurant"];
                }
                if(!empty($vLinkData["linkedFeaturedRestaurant"])){
                    $data["linkedFeaturedRestaurant"]   = $vLinkData["linkedFeaturedRestaurant"];
                }
                if(!empty($vLinkData["linkedEvent"])){
                    $data["linkedEvent"]    = $vLinkData["linkedEvent"];
                }
                if(!empty($vLinkData["linkedCombo"])){
                    $data["linkedCombo"]    = $vLinkData["linkedCombo"];
                }
                if(!empty($vLinkData["linkedOffer"])){
                    $data["linkedOffer"]    = $vLinkData["linkedOffer"];
                }
            }
            
            return $data;
        }else{
            return '';
        }
    }
    
    function changeStatus($id) {
        $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iPushNotifyID = $id");
        if ($this->db->affected_rows() > 0)
            return true;
        else
            return false;
    }
    
    
    function softDelete($id) {
        $adid = implode(',', $id);
        $this->db->query("UPDATE $this->table SET eStatus = 'Deleted' WHERE iPushNotifyID in ($adid) ");
        if ($this->db->affected_rows() > 0) {
            return true;
        } else{
            return false;
        }
    }
    
    function getLocations(){
        $query    = "SELECT iLocationID,vLocationName FROM tbl_location WHERE eStatus='Active'";
        $result   = $this->db->query($query);
        return $result->result_array();
    }
    
    function getRestaurants(){
        $query    = "SELECT iRestaurantID,vRestaurantName FROM tbl_restaurant WHERE eStatus='Active' ORDER BY vRestaurantName ASC";
        $result   = $this->db->query($query);
        return $result->result_array();
    }
    
    function getFeaturedRestaurants(){
        $query    = "SELECT iRestaurantID,vRestaurantName FROM tbl_restaurant WHERE eStatus='Active' AND eFeatured='yes' ORDER BY vRestaurantName ASC";
        $result   = $this->db->query($query);
        return $result->result_array();
    }
    
    function getEvents(){
        $query    = "SELECT iEventId,iEventTitle,iRestaurantId FROM tbl_restaurant_event WHERE eStatus='Active'";
        $result   = $this->db->query($query);
        return $result->result_array();
    }
    
    function getCombo(){
        $query    = "SELECT iComboOffersID,vOfferText,iRestaurantID FROM tbl_combo_offers WHERE eStatus='Active'";
        $result   = $this->db->query($query);
        return $result->result_array();
    }
    
    function getOffers(){
        $query    = "SELECT iDealID,vOfferText,iRestaurantID FROM tbl_deals WHERE eStatus='Active'";
        $result   = $this->db->query($query);
        return $result->result_array();
    }
}

?>
