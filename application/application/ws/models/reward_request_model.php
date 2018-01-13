<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of link_model
 * link hashes expiry 24 hrs
 * @author Amit Malakar
 */
class Reward_Request_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_reward_request';
    }
    
    // function to get NON-REJECTED user rewards list
    function getUserRewardList($userId){
        try {
            $rewards = array();
            if (!empty($userId)) {
                $sql = "SELECT count(iRewardID) AS reward_count, iRewardID FROM tbl_reward_request WHERE iUserID = " . $userId . " AND eStatus IN ('Pending','Accept') GROUP BY iRewardID ";
                $rewardsArray = $this->db->query($sql)->result_array();
                if (is_array($rewardsArray)) {
                    foreach ($rewardsArray AS $reward) {
                        $rewards[$reward['iRewardID']]["id"]    = $reward['iRewardID'];
                        $rewards[$reward['iRewardID']]["count"] = $reward['reward_count'];
                    }
                }
            }
            return $rewards;
        } catch (Exception $ex) {
            throw new Exception('Error in getUserRewardList function - ' . $ex);
        }
    }
    
    // function to get NON-REJECTED user rewards in detail
    function getUserRewards($userId, $rewardTitle='', $points=''){
        try {
            $rewards = array();
            $conditions = array(
                "trr.iUserID = " . $userId,
                "trr.eStatus IN ('Pending','Accept')");
            if( !empty($rewardTitle) ){
                if(is_array($rewardTitle)){
                    $conditions[]   = "tr.vRewardTitle IN ('".implode("', '",$rewardTitle)."')";
                }else{
                    $conditions[]   = "tr.vRewardTitle='".$rewardTitle."'";
                }
            }
            if(isset($points["min"]) && isset($points["max"]) ){
                $points  = array($points); 
            }
            if(isset($points[0]["min"]) && isset($points[0]["max"]) ){
                $tmpConditions  = [];
                foreach($points As $pointFilter){
                    if( is_numeric($pointFilter["min"]) && is_numeric($pointFilter["max"]) ){
                        $tmpConditions[] = " ( tr.iRewardPoint >='".$pointFilter["min"]."' AND tr.iRewardPoint <='".$pointFilter["max"]."' ) ";
                    }
                }
                $conditions[]   = " ( ".implode(" OR ", $tmpConditions)." ) ";
            }
            $conditionsStr  = implode(" AND ", $conditions);
            
            if (!empty($userId)) {
                $sql = "SELECT trr.iRewardRequestID as rewardRequestId, trr.iRewardID as rewardId, tr.vRewardTitle AS rewardTitle, tr.tRewardDesc AS rewardDescription, tr.iRewardPoint AS pointsRequired, tr.iRewardVoucher AS voucherValue, IF( tr.vRewardImage = '' , '', CONCAT('" . IMG_URL . "reward/', tr.vRewardImage) ) AS rewardImage, tr.dtRewardValidUpto AS validUpto  FROM tbl_reward_request AS trr LEFT JOIN tbl_reward as tr ON tr.iRewardID = trr.iRewardID WHERE $conditionsStr";
                
                $rewards = $this->db->query($sql)->result_array();
            }
            return $rewards;
        } catch (Exception $ex) {
            throw new Exception('Error in getUserRewards function - ' . $ex);
        }
    }
    
    function addRequest($userId,$rewardId){
        try {
            if (!empty($userId)&&!empty($rewardId)) {
                if(!is_array($rewardId)){
                    $rewardId =array($rewardId);
                }
                foreach($rewardId AS $rId){
                    $data = array(
                        'iRewardID'     => $rId,
                        'iUserID'       => $userId,
                        "eStatus"       =>'Accept',
                        'tCreatedAt'    => date('Y:m:d H:i:s'),
                        'tModifiedAt'   => date('Y:m:d H:i:s')
                    );

                    $this->db->insert('tbl_reward_request', $data);
                }
            }
            return true;
        } catch (Exception $ex) {
            throw new Exception('Error in addRequest function - ' . $ex);
        }
    }
}
