<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of user_model
 * @author OpenXcell Technolabs
 */
class User_Points_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_user_points';
    }

    function getTotalPoints($userId) {
        try {
            $totalPoints = 0;
            if (!empty($userId)) {
                $sql = "SELECT iUserPointSystemID, iPoints FROM tbl_user_point_system";
                $pointSystemRes = $this->db->query($sql)->result_array();
                $pointSystem = array();
                foreach ($pointSystemRes AS $val) {
                    $pointSystem[$val["iUserPointSystemID"]] = $val["iPoints"];
                }

                $sql = "SELECT count(iUserPointSystemID) AS count, iUserPointSystemID FROM tbl_user_points WHERE iUserID = " . $userId . " AND eStatus='Active' GROUP BY iUserPointSystemID ";
                $pointsArray = $this->db->query($sql)->result_array();
                if (is_array($pointsArray)) {
                    foreach ($pointsArray AS $points) {
                        $totalPoints += $pointSystem[$points["iUserPointSystemID"]] * $points["count"];
                    }
                }
            }
            return $totalPoints;
        } catch (Exception $ex) {
            throw new Exception('Error in getTotalPoints function - ' . $ex);
        }
    }

    function getUserPoints($userId)
    {
        try {
            if ($userId !== '') {
                $sql = 'SELECT vType AS type, iPoints AS points, tCreatedAt AS createdAt ';
                $sql .= ' FROM `tbl_user_points` AS `tup`';
                $sql .= ' LEFT JOIN `tbl_user_point_system` AS `tups` ON `tups`.`iUserPointSystemID` = `tup`.`iUserPointSystemID`';
                $sql .= ' WHERE `tup`.`eStatus`="Active" AND `tup`.`iUserID`="'.$userId.'"';

                $res = $this->db->query($sql)->result_array();
                if (count($res))
                    return $res;
                else
                    return FALSE;
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            exit('User Model : Error in getUserPoints function - ' . $ex);
        }
    }

    function moveUserPointOnMerge($fromUserId, $toUserId) {
        // update table change user id
        try {
            if ($fromUserId !== '' && $toUserId !== '') {
                $this->db->update($this->table, array('iUserID' => $toUserId), array('iUserID' => $fromUserId, 'eStatus' => 'Active'));
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            exit('User Model : Error in addUserPointOnMerge function - ' . $ex);
        }

    }

    function deleteUserPoints($userId) {

    }
    
    function addUserPoints($userId,$pointSystemId){
            
        $insert = array(
            'iUserPointSystemID'    => $pointSystemId,
            'iUserID'               => $userId,
            'eStatus'               => "Active",
            'tCreatedAt'            => date('Y:m:d H:i:s'),
        );
        $this->db->insert('tbl_user_points', $insert);
        
    }
    
    function getPointsHistory( $userId ){
        try {
            $pointsHistory  = [];
            $rewardsHistory = [];
            $comboHistory   = [];
            $result         = [];
            if (!empty($userId)) {
                //earned points
                $sql = "SELECT iUserPointSystemID, iPoints, vType FROM tbl_user_point_system";
                $pointSystemRes = $this->db->query($sql)->result_array();
                $pointSystem = array();
                foreach ($pointSystemRes AS $val) {
                    $pointSystem[$val["iUserPointSystemID"]] = array("points"=>$val["iPoints"],"type"=>$val["vType"]);
                }

                $sql = "SELECT iUserPointSystemID, DATE(tCreatedAt) AS tCreatedAt FROM tbl_user_points WHERE iUserID = " . $userId . " AND eStatus='Active'";
                $pointsArray = $this->db->query($sql)->result_array();
                if (is_array($pointsArray)) {
                    foreach ($pointsArray AS $points) {
                        $pointsHistory[] = array(
                                'title'     => $pointSystem[$points["iUserPointSystemID"]]["type"],
                                'date'      => $points["tCreatedAt"],
                                'points'    => $pointSystem[$points["iUserPointSystemID"]]["points"],
                                'year'      => date("Y",  strtotime($points["tCreatedAt"])),
                                'type'      => "Earned"  
                        );
                    }
                }
                //rewards purchased
                $sql = "SELECT trr.iRewardID, DATE(trr.tCreatedAt) AS createdAt, tr.vRewardTitle AS rewardTitle, tr.iRewardPoint AS pointsSpend FROM tbl_reward_request AS trr LEFT JOIN tbl_reward AS tr ON trr.iRewardID=tr.iRewardID WHERE trr.iUserID = " . $userId . " AND trr.eStatus IN ('Pending','Accept')";
                $rewardsArray = $this->db->query($sql)->result_array();
                if (is_array($rewardsArray)) {
                    foreach ($rewardsArray AS $reward) {
                        $rewardsHistory[]    =array(
                                'title'         => "Rewards Earned",
                                'date'          => $reward["createdAt"],
                                'points'        => $reward["pointsSpend"],
//                                'restaurantName'=> $reward["rewardTitle"],
                                'year'          => date("Y",  strtotime($points["tCreatedAt"])),
                                'type'          => "Redeemed"  
                        );
                    }
                }
                //combo purchased
                $sql = "SELECT DATE(createdAt) AS createdAt, pointsUsed AS pointsSpend FROM tbl_order WHERE pointsUsed > 0 AND user_id = " . $userId;
                $comboArray = $this->db->query($sql)->result_array();
                if (is_array($comboArray)) {
                    foreach ($comboArray AS $reward) {
                        $comboHistory[]    = array(
                                'title'         => "Combo Purchased",
                                'date'          => $reward["createdAt"],
                                'points'        => $reward["pointsSpend"],
                                'year'          => date("Y",  strtotime($points["tCreatedAt"])),
                                'type'          => "Redeemed"  
                        );
                    }
                }
                $result  = array_merge($pointsHistory,$rewardsHistory,$comboHistory);
                usort($result, function($rowA, $rowB) {
                    if ($rowA["date"] == $rowB["date"]) {
                        return 0;
                    }
                    return ($rowA["date"] < $rowB["date"]) ? 1 : -1;
                });
            }
            return $result;
        } catch (Exception $ex) {
            throw new Exception('Error in getPointsHistory function - ' . $ex);
        }
    }

}
