<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author OpenXcell Technolabs
 */
class Offer extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('offer_model');
    }

    function getOffer_post() {

        try {

            $allowParam = array(
                'restId'
            );
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $CARTDATA = '';

            if (checkselectedparams($this->post(), $allowParam)) {


                /*
                 * SEARCH FROM THE MODEL TO GET THE RESULT...
                 */
                $MESSAGE = NO_RECORD_FOUND;
                $STATUS = FAIL_STATUS;

                $res = $this->offer_model->getOfferDetail($this->post());


                if (!empty($res)) {
                    $MESSAGE = '';
                    $STATUS = SUCCESS_STATUS;
                    $OFFERDATA = $res;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$OFFERDATA != '') {
                $resp['OFFERDATA'] = $OFFERDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getOfferByRestId_post function - ' . $ex);
        }
    }

    //With Filter
    function allOffers_post() {

        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $CARTDATA = '';
            /*
             * SEARCH FROM THE MODEL TO GET THE RESULT...
             */
            $MESSAGE = NO_RECORD_FOUND;
            $STATUS = FAIL_STATUS;

            $res = $this->offer_model->getAllOffer($this->post());


            if (!empty($res)) {
                $MESSAGE = '';
                $STATUS = SUCCESS_STATUS;
                $OFFERDATA = $res;
            }


            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$OFFERDATA != '') {
                $resp['OFFERDATA'] = $OFFERDATA;
                $this->load->model('restaurant_model');
                $resp['BANNER'] = $this->restaurant_model->getBanner("deals");
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getOfferByRestId_post function - ' . $ex);
        }
    }

    /*
     * Method to return json for all available filters on event. 
     * 
     * @return json data with event filter
     * @author Anurag Srivastava (anurag.srivastava@Foodine :.com)
     */

    function getOfferFilter_post() {
        try {

            // Default value
            $DATA = array();
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            $resp = $this->offer_model->getAllOfferFilter($this->post());
            if (!empty($resp)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = '';
                $DATA = $resp;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            // setting the response
            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'DATA' => $DATA
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            // If execution stop unexpectedly 
            $resp = array(
                'FILTERS' => $ex->getMessage(),
                'STATUS' => FAIL_STATUS,
                'DATA' => ''
            );
            $this->response($resp, 200);
        }
    }
    
     function getComboFilter_post() {
        try {

            // Default value
            $DATA = array();
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            $resp = $this->offer_model->getAllComboFilter($this->post());
            if (!empty($resp)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = '';
                $DATA = $resp;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            // setting the response
            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'DATA' => $DATA
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            // If execution stop unexpectedly 
            $resp = array(
                'FILTERS' => $ex->getMessage(),
                'STATUS' => FAIL_STATUS,
                'DATA' => ''
            );
            $this->response($resp, 200);
        }
    }

    function getCode_post() {
        $allowParam = array('userId', 'offerId');
        $MESSAGE = INSUFF_DATA;
        $STATUS = FAIL_STATUS;
        $CODE = '';
        if (checkselectedparams($this->post(), $allowParam)) {
            if (checkselectedparams($this->post(), array('offerCode'))) {
                $resArr = $this->offer_model->verifyCode($this->post());
                $MESSAGE = $resArr['msg'];
                if ($resArr['status'] == 1 || $resArr['status'] == 2) {
                    $STATUS = SUCCESS_STATUS;
                    $CODE = $resArr['offercode'];
                }
            } else {
                $arr = $this->offer_model->saveCode($this->post());
                if (isset($arr['rand']) && $arr['rand'] != '') {
                    $msg = 'Your HungerMafia verification code is ' . $arr['rand'];
                    //$msg = 'Code is ' . $arr['rand'] . ' for Hungermafia deal';
                    $this->load->model('Sms_model', 'sms_m');
                    $this->sms_m->destmobileno = @$arr['mobile'];
                    $this->sms_m->msg = $msg;
                    $data = $this->sms_m->Send();
                    $smsData = json_decode($data);
                    $status = $smsData->messages[0]->status;
                    if (isset($status) && $status == 0) {
                        $MESSAGE = 'Code Sent successfully.';
                        $STATUS = SUCCESS_STATUS;
                    } else {
                        $MESSAGE = 'Some Error Occurred';
                    }
                }
            }
        }
        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS,
            'CODE' => $CODE
        );


        $this->response($resp, 200);
    }

    function sendEmail_post() {
        try {
            /*
             * SEND MAIL FUNCTIONALITY...
             */
            $comboData = $this->post('comboData');
            $price = $this->post('price');
            $orderId = $this->post('orderId');
//            $this->load->library('maillib');
            $this->load->model("smtpmail_model", "smtpmail_model");
            $str = '<div class="order-table">';
            if (isset($comboData)) {
                foreach ($comboData as $i => $data) {
                    $str .= '<div class="confirm-discount">';
                    $str .= '<table><tr>'
                            . '<th>Combo: ' . $data['offerText'] . '</th><th>Quantity</th><th> Original Price</th>'
                            . '<th> Discounted Price</th></tr><tr><td>' . $data['tOfferDetail'] . '</td><td>' . $data['qty'] . '</td>
                        <td>' . $data['tActualPrice'] . '</td>
                        <td>' . $data['tDiscountedPrice'] . '</td>
                        </tr></table></div>';
                }
            }

            $str .= '</div>';
            if (isset($price['total'])) {
                $str .= '<div class="total-items order-table clearfix">'
                        . '<span class="cols-2">Order Total</span>'
                        . '<span class="cols-1">&#8377; ' . $price['total'] . '</span></div>';
            }

            $param = array(
                '%MAILSUBJECT%' => 'Foodine : Order Details',
                '%LOGO_IMAGE%' => BASEURL . '/images/hungermafia.png',
                '%ORDER_ID%' => $orderId,
                '%DATA%' => $str,
            );
            $tmplt = DIR_VIEW . 'email/combo_offer.php';
            $subject = 'Foodine : Order Details';
            $to = $this->post('vEmail');
            $data = $this->smtpmail_model->send($to, $subject, $tmplt, $param);
//            $data = $this->maillib->sendMail($to, $subject, $tmplt, $param);
            $this->response($data, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getOfferByRestId_post function - ' . $ex);
        }
    }

    function saveComboDetails_post() {
        try {

            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'userId', 'comboData'
            );
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DETAILS = array();

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->offer_model->savecomboCartDetails($this->post());

                if ($resp) {
                    $DETAILS = $resp;
                    $MESSAGE = 'Details Updated SuccessFully';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'DETAILS' => $DETAILS
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in saveComboDetails_post function - ' . $ex);
        }
    }

    /*
     * Method to return json for all available offer/combo/event/deals on home page. 
     * @return json data 
     * @added 16May,2016
     */

    function getOfferList_post() {
        try {
            // Default value
            $DATA = array();
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $this->load->model('restaurant_model');
            $deals = $this->restaurant_model->getBanner("deals",$this->post());
            $featured = $this->restaurant_model->getBanner("featured",$this->post());
            $combo = $this->restaurant_model->getBanner("combo",$this->post());
            $event = $this->restaurant_model->getBanner("event",$this->post());
            $offerList['deals'] = $deals;
            $offerList['featured'] = $featured;
            $offerList['combo'] = $combo;
            $offerList['event'] = $event;
            if (!empty($offerList)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = '';
                $DATA = $offerList;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            // setting the response
            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'DATA' => $DATA
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            // If execution stop unexpectedly 
            $resp = array(
                'FILTERS' => $ex->getMessage(),
                'STATUS' => FAIL_STATUS,
                'DATA' => ''
            );
            $this->response($resp, 200);
        }
    }
    
    function wowDeals_post() {
        try {
            // Default value
            $DATA = array();
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $this->load->model('restaurant_model');
            
            $offerList['deals'] = $deals;
            $offerList['featured'] = $featured;
            $offerList['combo'] = $combo;
            $offerList['event'] = $event;
            if (!empty($offerList)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = '';
                $DATA = $offerList;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }
            
            $deals[] = array(
                "id" => 'restro1',
                "name" => "Combo Offer",
                "description" => "Start Saving",
                "photoUrl" => BASEURL.'images/banner/images.jpeg'
            );
            
            $deals[] = array(
                "id" => 'restro2',
                "name" => "Discounted Offer",
                "description" => "To avail discount",
                "photoUrl" => BASEURL.'images/banner/images4.jpeg'
            );
            // setting the response
            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'deals' => $deals
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            // If execution stop unexpectedly 
            $resp = array(
                'FILTERS' => $ex->getMessage(),
                'STATUS' => FAIL_STATUS,
                'DATA' => ''
            );
            $this->response($resp, 200);
        }
    }

}
