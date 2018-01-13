<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author OpenXcell Technolabs
 */
class Booking extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('tablebooking_model');
    }

    function getCartDetails_post() {
        try {

            $allowParam = array(
                'userId'
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

                $res = $this->tablebooking_model->getBookingCartDetails($this->post('userId'));

                if (!empty($res)) {
                    $MESSAGE = RESTAURANT_FOUND;
                    $STATUS = SUCCESS_STATUS;
                    $CARTDATA = $res;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$CARTDATA != '') {
                $resp['CARTDATA'] = $CARTDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getCartDetails_post function - ' . $ex);
        }
    }

    /*
     * TO BOOK RESTAURANT TABLE FOR WEB...
     */

    function saveBookTableCart_post() {
        try {
            // TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
//            $allowParam = array(
//                'userId', 'restaurantId', 'slotId', 'bookDate', 'totalPerson',
//                'mealType', 'userBookingName', 'userMobile', 'userEmail', 'userFullName'
//            );
            $allowParam = array(
                'userId', 'restaurantId', 'bookTime', 'bookDate', 'totalPerson',
                'userBookingName', 'userMobile'
            );

            // TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->tablebooking_model->saveBookTableInCart($this->post());
                switch ($resp) {
                    case -1:
                        $MESSAGE = NO_RECORD_FOUND;
                        break;

                    case -2:
                        $MESSAGE = TABLE_BOOK_WARN;
                        break;

                    case -3:
                        $MESSAGE = TABLE_BOOK_LIMIT;
                        break;

                    case -4:
                        $MESSAGE = TABLE_BOOK_CLOSED;
                        break;

                    default:
                        $MESSAGE = TABLE_BOOK_SUCC;
                        $STATUS = SUCCESS_STATUS;
                        break;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'bookingId' => isset($resp['bookingId']) ? $resp['bookingId'] : '',
                'assignedSlot' => isset($resp['assignedSlot']) ? $resp['assignedSlot'] : '',
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in bookRestaurant function - ' . $ex);
        }
    }

    /*
     * TO DELETE CART
     */

    function deleteCartBooking_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */

            $allowParam = array(
                'userId', 'bookedId'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->tablebooking_model->deleteBookingCart($this->post('userId'), $this->post('bookedId'));
                switch ($resp) {
                    case 1 :
                        $STATUS = SUCCESS_STATUS;
                        $MESSAGE = BOOK_TABLE_CART_CANCEL;
                        break;

                    case -2:
                        $MESSAGE = BOOK_TABLE_CART_CANCEL_ERR;
                        break;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in deleteCartBooking_post function - ' . $ex);
        }
    }

    function getLastBooking_post() {
        try {

            $allowParam = array(
                'userId'
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

                $res = $this->tablebooking_model->getLastBookingDetails($this->post('userId'));

                if (!empty($res)) {
                    $MESSAGE = '';
                    $STATUS = SUCCESS_STATUS;
                    $BOOKINGDATA = $res;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$BOOKINGDATA != '') {
                $resp['BOOKINGDATA'] = $BOOKINGDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getCartDetails_post function - ' . $ex);
        }
    }

    function getComboCartDetails_post() {
        try {

            $allowParam = array(
                'userId'
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

                $res = $this->tablebooking_model->getComboCartDetails($this->post('userId'));

                if (!empty($res)) {
                    $MESSAGE = RESTAURANT_FOUND;
                    $STATUS = SUCCESS_STATUS;
                    $CARTDATA = $res;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$CARTDATA != '') {
                $resp['CARTDATA'] = $CARTDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getComboCartDetails_post function - ' . $ex);
        }
    }

    function getTableBooking_post() {
        try {

            $allowParam = array(
                'restaurantId'
            );
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DATA = '';

            if (checkselectedparams($this->post(), $allowParam)) {

                /*
                 * SEARCH FROM THE MODEL TO GET THE RESULT...
                 */
                $MESSAGE = NO_RECORD_FOUND;
                $STATUS = FAIL_STATUS;

                $res = $this->tablebooking_model->getTableBooking($this->post('restaurantId'));

                if (!empty($res)) {
                    $MESSAGE = RESTAURANT_FOUND;
                    $STATUS = SUCCESS_STATUS;
                    $DATA = $res;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$DATA != '') {
                $resp['DATA'] = $DATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getTableBooking_post function - ' . $ex);
        }
    }

    /*
     * TO BOOK RESTAURANT TABLE FOR WEB...
     */

    function bookTable_post() {
        try {
            $this->load->model("restaurant_model", "restaurant_model");


            // TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
            $allowParam = array(
                'userId', 'restaurantId', 'bookTime', 'bookDate', 'totalPerson',
                'userBookingName', 'userMobile'
            );

            // TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            if (checkselectedparams($this->post(), $allowParam)) {

                $resp = $this->tablebooking_model->bookTable($this->post());
                switch ($resp) {
                    case -1:
                        $MESSAGE = NO_RECORD_FOUND;
                        break;

                    case -2:
                        $MESSAGE = TABLE_BOOK_WARN;
                        break;

                    case -3:
                        $MESSAGE = TABLE_BOOK_LIMIT;
                        break;

                    case -4:
                        $MESSAGE = TABLE_BOOK_CLOSED;
                        break;

                    default:
                        $MESSAGE = TABLE_BOOK_SUCC;
                        $STATUS = SUCCESS_STATUS;
                        break;
                }
            }

            $response = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'bookingId' => !empty($resp['uniqueCode']) ? $resp['uniqueCode'] : $resp['bookingId'],
                'offerCode' => isset($resp['offerCode']) ? $resp['offerCode'] : '',
                'assignedSlot' => isset($resp['assignedSlot']) ? $resp['assignedSlot'] : '',
                'totalPoint' => $this->general_model->getRedeemAmountWeb($this->post('userId')),
            );

            $this->response($response, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in bookRestaurant function - ' . $ex);
        }
    }

    /**
     * 
     */
    function partyBook_post() {
        try {
            $allowedParams = array('userId', 'name', 'location1', 'location2', 'rest1', 'rest2', 'priceMin', 'priceMax',
                'timeSlot', 'numberPeople', 'date');

            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            if (checkselectedparams($this->post(), $allowedParams)) {
                /*
                 * TO CALL THE FUNCTION FROM THE PAGE-CONTENT-MODEL
                 */
                $result = $this->tablebooking_model->partyBook($this->post());
                if (!empty($result)) {
                    $userData = $this->general_model->getUserBasicRecordById($this->post('userId'));
                    $emailData = [
                        'data' => $this->post(), 'vEmail' => $userData['userEmail'], 'vFirstName' => $userData['userFirstName']
                    ];
                    $this->_sendEmailParty($emailData);
                    $resp = array(
                        'MESSAGE' => 'We have recieved your Party request. Our team will get back to you shortly.',
                        'STATUS' => SUCCESS_STATUS,
                    );
                } else {
                    $resp = array(
                        'MESSAGE' => 'Record not found.!',
                        'STATUS' => FAIL_STATUS
                    );
                }
            } else {
                $resp = array(
                    'MESSAGE' => INSUFF_DATA,
                    'STATUS' => FAIL_STATUS
                );
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            exit('PageContent Controller : Error in partyBook_post function - ' . $ex);
        }
    }

    private function _sendEmailParty($emailData) {
        try {
            /*
             * SEND MAIL FUNCTIONALITY...
             */

            $this->load->model("smtpmail_model", "smtpmail_model");
            $param = array(
                '%MAILSUBJECT%' => 'HungerMafia : Book Party',
                '%BASEURL%' => BASEURL,
                '%USERNAME%' => $emailData['vFirstName']
            );
            $tmplt = DIR_VIEW . 'email/book_party.php';
            $subject = 'HungerMafia : Book Party';
            $to = $emailData['vEmail'];
            $data = $this->smtpmail_model->send($to, $subject, $tmplt, $param);
            return true;
        } catch (Exception $ex) {
            throw new Exception('Error in send emailParty function - ' . $ex);
        }
    }

    /**
     * AUTO SEARCH
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@kelltontech
     * @param type $searchText <string> 
     * @return void
     */
    function getRestaurantForBooking_post() {
        try {
            //MANDATORY PARAMETERS
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $RESTAURANTLIST = array();
            //CHECK MANDATORY PARAMETERS
            $resp = $this->tablebooking_model->searchRestaurantForBooking($this->post());
            if (!empty($resp)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = RESTAURANT_FOUND;
                $RESTAURANTLIST = $resp;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'RESTAURANTLIST' => $RESTAURANTLIST
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantForBooking_post function - ' . $ex);
        }
    }
    
     function getBookingDetail_post() {
        try {

            $allowParam = array(
                'userId','bookingId'
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

                $res = $this->tablebooking_model->getBookingDetail($this->post());

                if (!empty($res)) {
                    $MESSAGE = '';
                    $STATUS = SUCCESS_STATUS;
                    $BOOKINGDATA = $res;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$BOOKINGDATA != '') {
                $resp['BOOKINGDATA'] = $BOOKINGDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getCartDetails_post function - ' . $ex);
        }
    }

}
