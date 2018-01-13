<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

class ReferenceCode extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('referencecode_model');
    }

    function availReferenceCode_post() {
        try {
            $allowParam = array('userId', 'deviceToken', 'refrenceCode');
            $message = INSUFF_DATA;
            $isLinked = 0;
            $status = FAIL_STATUS;
            if (checkselectedparams($this->post(), $allowParam)) {
                try {
                    $res = $this->referencecode_model->availCode($this->post('userId'), $this->post('deviceToken'), $this->post('refrenceCode'));
                    if ($res['status']) {
                        $message = 'Reference code availed successfully.';
                        $status = SUCCESS_STATUS;
                        $isLinked = $res['isLinked'];
                    }
                } catch (Exception $ex) {
                    $message = $ex->getMessage();
                }
            }
            $resp = array(
                'MESSAGE' => $message,
                'STATUS' => $status,
                'isVoucherLinked' => $isLinked
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getReviews function - ' . $ex);
        }
    }

    function availVoucher_post() {
        try {
            $allowParam = array('userId', 'voucherCode', 'grossamount');
            $message = INSUFF_DATA;
            $status = FAIL_STATUS;
            $data = [];
            if (checkselectedparams($this->post(), $allowParam)) {
                try {
                    $data = $this->referencecode_model->availVoucher($this->post('userId'), $this->post('voucherCode'), $this->post('grossamount'));
                    $message = 'Voucher availed successfully.';
                    $status = SUCCESS_STATUS;
                } catch (Exception $ex) {
                    $message = $ex->getMessage();
                }
            }
            $resp = array(
                'MESSAGE' => $message,
                'STATUS' => $status
            );
            if (!empty($data)) {
                $resp['DATA'] = $data;
            }
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getReviews function - ' . $ex);
        }
    }

    function removeAvailedVoucher_post() {
        try {
            $allowParam = array('userId', 'voucherCode');
            $message = INSUFF_DATA;
            $status = FAIL_STATUS;
            if (checkselectedparams($this->post(), $allowParam)) {
                try {
                    $data = $this->referencecode_model->removeAvailedVoucher($this->post('userId'), $this->post('voucherCode'));
                    $message = 'Voucher removed successfully.';
                    $status = SUCCESS_STATUS;
                } catch (Exception $ex) {
                    $message = $ex->getMessage();
                }
            }
            $resp = array(
                'MESSAGE' => $message,
                'STATUS' => $status
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in removeAvailedVoucher function - ' . $ex);
        }
    }

    function getMyVouchers_post() {
        try {
            $allowParam = array('userId');
            $message = INSUFF_DATA;
            $status = FAIL_STATUS;
            $data = array();
            if (checkselectedparams($this->post(), $allowParam)) {
                $availedVouchers = $this->referencecode_model->getAvailedVouchers($this->post('userId'));
                $unAvailedVouchers = $this->referencecode_model->getUnavailedVouchers($this->post('userId'));
                $message = '';
                $status = SUCCESS_STATUS;
                $data["availedVouchers"] = $availedVouchers;
                $data["unAvailedVouchers"] = $unAvailedVouchers;
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

}
