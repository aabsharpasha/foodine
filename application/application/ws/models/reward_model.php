<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of link_model
 * link hashes expiry 24 hrs
 * @author Amit Malakar
 */
class Reward_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_reward';
    }

    function getRecommendedRewards($rewardTitle='', $points=''){
        try{
            $conditions = [ "eRecommended='1'",
                            "eStatus='Active'", 
                            "dtRewardValidUpto >='".date('Y-m-d H:i:s')."'"
                ];
            if( !empty($rewardTitle) ){
                if(is_array($rewardTitle)){
                    $conditions[]   = "vRewardTitle IN ('".implode("', '",$rewardTitle)."')";
                }else{
                    $conditions[]   = "vRewardTitle='".$rewardTitle."'";
                }
            }
            if(isset($points["min"]) && isset($points["max"]) ){
                $points  = array($points); 
            }
            if(isset($points[0]["min"]) && isset($points[0]["max"]) ){
                $tmpConditions  = [];
                foreach($points As $pointFilter){
                    if( is_numeric($pointFilter["min"]) && is_numeric($pointFilter["max"]) ){
                        $tmpConditions[] = " ( iRewardPoint >='".$pointFilter["min"]."' AND iRewardPoint <='".$pointFilter["max"]."' ) ";
                    }
                }
                $conditions[]   = " ( ".implode(" OR ", $tmpConditions)." ) ";
            }
            $conditionsStr  = implode(" AND ", $conditions);
            
            $fields = [ "iRewardID AS rewardId", 
                        "vRewardTitle AS rewardTitle", 
                        "tRewardDesc AS rewardDescription", 
                        "iRewardPoint AS pointsRequired", 
                        "iRewardVoucher AS voucherValue", 
                        'CONCAT("' . BASEURL . 'images/reward/", IF(vRewardImage =\'\', "default.png", CONCAT(iRewardID,\'/\',vRewardImage))) AS rewardImage',
                        "dtRewardValidUpto AS validUpto"
                ];
            $fieldsStr  = implode(", ", $fields);
            
            $sql = "SELECT $fieldsStr FROM tbl_reward WHERE $conditionsStr";
            $rewardsArray = $this->db->query($sql)->result_array();
            return $rewardsArray;
        } catch (Exception $ex) {
            throw new Exception('Error in getRecommendedRewards function - ' . $ex);
        }
    }

    function getAllActiveRewards($rewardTitle='', $points=''){
        try{
            $conditions = [ "eStatus='Active'", 
                            "dtRewardValidUpto >='".date('Y-m-d H:i:s')."'"
                ];
            if( !empty($rewardTitle) ){
                if(is_array($rewardTitle)){
                    $conditions[]   = "vRewardTitle IN ('".implode("', '",$rewardTitle)."')";
                }else{
                    $conditions[]   = "vRewardTitle='".$rewardTitle."'";
                }
            }
            if(isset($points["min"]) && isset($points["max"]) ){
                $points  = array($points); 
            }
            if(isset($points[0]["min"]) && isset($points[0]["max"]) ){
                $tmpConditions  = [];
                foreach($points As $pointFilter){
                    if( is_numeric($pointFilter["min"]) && is_numeric($pointFilter["max"]) ){
                        $tmpConditions[] = " ( iRewardPoint >='".$pointFilter["min"]."' AND iRewardPoint <='".$pointFilter["max"]."' ) ";
                    }
                }
                $conditions[]   = " ( ".implode(" OR ", $tmpConditions)." ) ";
            }
            $conditionsStr  = implode(" AND ", $conditions);
            
            $fields = [ "iRewardID AS rewardId", 
                        "vRewardTitle AS rewardTitle", 
                        "tRewardDesc AS rewardDescription", 
                        "iRewardPoint AS pointsRequired", 
                        "iRewardVoucher AS voucherValue", 
                        'CONCAT("' . BASEURL . 'images/reward/", IF(vRewardImage =\'\', "default.png", CONCAT(iRewardID,\'/\',vRewardImage))) AS rewardImage',
                        "dtRewardValidUpto AS validUpto"
                ];
            $fieldsStr  = implode(", ", $fields);
            
            $sql = "SELECT $fieldsStr FROM tbl_reward WHERE $conditionsStr";
            $rewardsArray = $this->db->query($sql)->result_array();
            return $rewardsArray;
        } catch (Exception $ex) {
            throw new Exception('Error in getRecommendedRewards function - ' . $ex);
        }
    }
    
    function getRedeemedPoints($userId){
        try {
            $points = 0;
            if (!empty($userId)) {
                //points spend on rewards
                $this->load->model('reward_request_model');
                $rewardList = $this->reward_request_model->getUserRewardList( $userId );
                if($rewardList){
                    $rewardIds  = implode(", ", array_keys($rewardList));
                    $sql = "SELECT iRewardID, iRewardPoint FROM tbl_reward WHERE iRewardID IN ( $rewardIds ) GROUP BY iRewardID ";
                    $rewardsArray = $this->db->query($sql)->result_array();
                    if (is_array($rewardsArray)) {
                        foreach ($rewardsArray AS $reward) {
                            $points +=  $reward["iRewardPoint"] * $rewardList[$reward["iRewardID"]]["count"];
                        }
                    }
                }
                //points spend on combo purchase
                $sql = "SELECT pointsUsed FROM tbl_order WHERE pointsUsed > 0 AND user_id = " . $userId;
                $comboArray = $this->db->query($sql)->result_array();
                if (is_array($comboArray)) {
                    foreach ($comboArray AS $reward) {
                        $points +=  $reward["pointsUsed"];
                    }
                }
            }
            return $points;
        } catch (Exception $ex) {
            throw new Exception('Error in getRedeemedPoints function - ' . $ex);
        }
    }
    
    function getFilters(){
        try {
            $slab   = 500;
            $sql = "SELECT DISTINCT(vRewardTitle) AS vendorName FROM tbl_reward WHERE eStatus = 'Active' AND dtRewardValidUpto > '".date('Y-m-d H:i:s')."'";
            $vendors    = $this->db->query($sql)->result_array();
            $vendorList = array();
            foreach($vendors AS $vendor){
                $vendorList[]   = $vendor["vendorName"];
            }

            $sql1 = "SELECT min(iRewardPoint) AS startPoint, max(iRewardPoint) AS endPoint FROM tbl_reward WHERE eStatus = 'Active' AND dtRewardValidUpto > '".date('Y-m-d H:i:s')."'";
            $pointLimits    = $this->db->query($sql1)->row_array();
            $pointFilter    = array();
            $i              = floor($pointLimits["startPoint"]/$slab);
            $endPoint       = floor(($pointLimits["endPoint"]%$slab==0)?($pointLimits["endPoint"]/$slab)-1:$pointLimits["endPoint"]/$slab);
            while($i<=$endPoint){
                $pointFilter[]   = array("min"=>$i*$slab,"max"=>($i+1)*$slab);
                $i++;
            }
            return array('points'=>$pointFilter, 'rewardTitles'=>$vendorList);
        } catch (Exception $ex) {
            throw new Exception('Error in getRedeemedPoints function - ' . $ex);
        }
    }

}
