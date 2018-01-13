<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author OpenXcell Technolabs
 */
class Reward extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('reward_model');
    }

    function getRewards_post() {
        try {
            $allowParam = array('userId');
            $message = INSUFF_DATA;
            $status = FAIL_STATUS;
            $data = array();
            if (checkselectedparams($this->post(), $allowParam)) {
                $this->load->model('user_points_model');
                $totalPoints = $this->user_points_model->getTotalPoints($this->post('userId'));
                $redeemedPoints = $this->reward_model->getRedeemedPoints($this->post('userId'));
                $rewardTitle = '';
                $points = '';
                if (!empty($this->post('filterRewardTitle'))) {
                    $rewardTitle = $this->post('filterRewardTitle');
                }
                if (!empty($this->post('filterPoints'))) {
                    $points = $this->post('filterPoints');
                }
                $recommendedRewards = $this->reward_model->getRecommendedRewards($rewardTitle, $points);
                $allRewards = $this->reward_model->getAllActiveRewards($rewardTitle, $points);
                $data["availablePoints"]    = ($totalPoints - $redeemedPoints) > 0 ? $totalPoints - $redeemedPoints : 0;
                $data["recommendedRewards"] = $recommendedRewards;
                $data["allRewards"]         = $allRewards;
                $message = '';
                $status = SUCCESS_STATUS;
            }
            $resp = array(
                'MESSAGE' => $message,
                'STATUS' => $status,
                'DATA' => $data
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getReviews function - ' . $ex);
        }
    }

    function getMyRewards_post() {
        try {
            $allowParam = array('userId');
            $message = INSUFF_DATA;
            $status = FAIL_STATUS;
            $data = array();
            if (checkselectedparams($this->post(), $allowParam)) {
                $this->load->model('user_points_model');
                $totalPoints = $this->user_points_model->getTotalPoints($this->post('userId'));
                $redeemedPoints = $this->reward_model->getRedeemedPoints($this->post('userId'));
                $this->load->model('reward_request_model');
                $rewardTitle = '';
                $points = '';
                if (!empty($this->post('filterRewardTitle'))) {
                    $rewardTitle = $this->post('filterRewardTitle');
                }
                if (!empty($this->post('filterPoints'))) {
                    $points = $this->post('filterPoints');
                }
                $rewards = $this->reward_request_model->getUserRewards($this->post('userId'),$rewardTitle,$points);
                $data["availablePoints"] = ($totalPoints - $redeemedPoints) > 0 ? $totalPoints - $redeemedPoints : 0;
                $data["myRewards"] = $rewards;
                $message = '';
                $status = SUCCESS_STATUS;
            }
            $resp = array(
                'MESSAGE' => $message,
                'STATUS' => $status,
                'DATA' => $data
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getReviews function - ' . $ex);
        }
    }

    function getFilters_post() {
        try {
            $message = INSUFF_DATA;
            $status = FAIL_STATUS;
            $data = array();
            $filters = $this->reward_model->getFilters();
            $data["filters"] = $filters;
            $message = '';
            $status = SUCCESS_STATUS;
            $resp = array(
                'MESSAGE' => $message,
                'STATUS' => $status,
                'DATA' => $data
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getReviews function - ' . $ex);
        }
    }

    function addRewardRequest_post() {
        try {
            $allowParam = array('userId', 'rewardId');
            $message = INSUFF_DATA;
            $status = FAIL_STATUS;
            if (checkselectedparams($this->post(), $allowParam)) {
                $this->load->model('reward_request_model');
                $rewards = $this->reward_request_model->addRequest($this->post('userId'), $this->post('rewardId'));
                if ($rewards) {
                    $message = 'Reward redeemed successfully!';
                    $status = SUCCESS_STATUS;
                } else {
                    $message = 'Unable to redeem reward';
                }
            }
            $resp = array(
                'MESSAGE' => $message,
                'STATUS' => $status
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getReviews function - ' . $ex);
        }
    }

    function getPointsHistory_post() {
        try {
            $allowParam = array('userId');
            $message = INSUFF_DATA;
            $status = FAIL_STATUS;
            $data = array();
            if (checkselectedparams($this->post(), $allowParam)) {
                $this->load->model('user_points_model');
                $this->load->model('user_model');
                $totalPoints = $this->user_points_model->getTotalPoints($this->post('userId'));
                $redeemedPoints = $this->reward_model->getRedeemedPoints($this->post('userId'));
                $history = $this->user_points_model->getPointsHistory($this->post('userId'));
                $level = $this->user_model->getUserLevel($totalPoints);
                $data["level"] = $level['name'];
                $data["levelImage"] = $level['image'];
                $data["availablePoints"] = ($totalPoints - $redeemedPoints) > 0 ? $totalPoints - $redeemedPoints : 0;
                $data["totalPoints"] = $totalPoints;
                $data["redeemedPoints"] = $redeemedPoints;
                $data["history"] = $history;
                $message = '';
                $status = SUCCESS_STATUS;
            }
            $resp = array(
                'MESSAGE' => $message,
                'STATUS' => $status,
                'DATA' => $data
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getReviews function - ' . $ex);
        }
    }

    function getAvailablePoints_post() {
        try {
            $allowParam = array('userId');
            $message    = INSUFF_DATA;
            $status     = FAIL_STATUS;
            $data       = array();
            if (checkselectedparams($this->post(), $allowParam)) {
                $this->load->model('user_points_model');
                $totalPoints        = $this->user_points_model->getTotalPoints($this->post('userId'));
                $redeemedPoints     = $this->reward_model->getRedeemedPoints($this->post('userId'));
                $data["availablePoints"] = ($totalPoints - $redeemedPoints) > 0 ? $totalPoints - $redeemedPoints : 0;
                $message    = '';
                $status     = SUCCESS_STATUS;
            }
            $resp = array(
                'MESSAGE' => $message,
                'STATUS' => $status,
                'DATA' => $data
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getAvailablePoints_post function - ' . $ex);
        }
    }

}
