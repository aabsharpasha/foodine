<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of vendor
 * @author OpenXcell Technolabs
 */
class Vendor extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('vendor_model');
    }

    /*
     * TO LIST OUT THE LIST OF OFFERS
     * URL : http://your-site/ws/vendor/offers/
     * WITH REQUIRE PARAMETER
     *      - VID*
     */

    function offers_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'vId'
            );
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $OFFERDATA = array();

            if (checkselectedparams($this->post(), $allowParam)) {
                $OFFERDATA = $this->vendor_model->getAllOffers($this->post('vId'));
                if (!empty($OFFERDATA)) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = OFFER_FOUND_SUCC;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'RESULT' => array(
                    'MESSAGE' => $MESSAGE,
                    'STATUS' => $STATUS,
                    'OFFERDATA' => $OFFERDATA
                )
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in offers_post function - ' . $ex);
        }
    }

    /*
     * DELETE OFFER
     * URL : http://your-site/ws/vendor/deleteOffer/
     * WITH REQUIRE PARAMETER
     *      - VID*
     */

    function deleteOffer_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'vId', 'offerId', 'needDelete'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->vendor_model->deleteOffer($this->post('vId'), $this->post('offerId'), $this->post('needDelete'));
                if ($resp) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = OFFER_DEL_SUCC;
                } else {
                    $MESSAGE = OFFER_DEL_ERR;
                }
            }

            $resp = array(
                'RESULT' => array(
                    'MESSAGE' => $MESSAGE,
                    'STATUS' => $STATUS
                )
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in deleteOffer function - ' . $ex);
        }
    }

    /*
     * DELETE OFFER
     * URL : http://your-site/ws/vendor/addOffer/
     * WITH REQUIRE PARAMETER
     *      - VID*
     */

    function addOffer_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'vId', 'offerText', 'offerDetail',
                'offerTerms', 'offerCode', 'offerStart',
                'offerEnd'
            );
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $OFFERID = 0;

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->vendor_model->addOffer($this->post());
                switch ($resp) {
                    case -2 :
                        $MESSAGE = OFEER_ID_EXISTS;
                        break;

                    case 0 :
                        $MESSAGE = OFEER_ADD_ERR;
                        break;

                    default :
                        $OFFERID = $resp;
                        $STATUS = SUCCESS_STATUS;
                        $MESSAGE = OFEER_ADD_SUCC;
                        break;
                }
            }

            $resp = array(
                'RESULT' => array(
                    'MESSAGE' => $MESSAGE,
                    'STATUS' => $STATUS,
                    'OFFERID' => $OFFERID
                )
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in addOffer_post function - ' . $ex);
        }
    }

    /*
     * SEND REQUEST FOR CHANGE
     */

    function changeForRequest_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'vId', 'requestText'
            );
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->vendor_model->changeForRequest($this->post());
                switch ($resp) {
                    case 0 :
                        $MESSAGE = REQUEST_CHANGE_ERR;
                        break;

                    case 1 :
                        $STATUS = SUCCESS_STATUS;
                        $MESSAGE = REQUEST_CHANGE_SUCC;
                        break;
                }
            }

            $resp = array(
                'RESULT' => array(
                    'MESSAGE' => $MESSAGE,
                    'STATUS' => $STATUS
                )
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in changeForRequest_post function - ' . $ex);
        }
    }

    /*
     * TO LIST OUT THE SPECIALTY OF THE EXISTING RESTAURANT
     */

    function getSpecialty_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'vId'
            );
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $SPCIALTYLIST = array();

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->vendor_model->getSpecialty($this->post('vId'));
                if (!empty($resp)) {
                    $SPCIALTYLIST = $resp;
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = SPECIALTY_FOUND_SUCC;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'RESULT' => array(
                    'MESSAGE' => $MESSAGE,
                    'STATUS' => $STATUS,
                    'SPCIALTYLIST' => $SPCIALTYLIST
                )
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getSpecialty_postfunction - ' . $ex);
        }
    }

    /*
     * TO ADD NEW SPECIALTY
     */

    function addSpecialty_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'vId', 'specialtyName', 'specialtyType'
            );
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->vendor_model->addSpecialty($this->post());
                switch ($resp) {
                    case -2:
                        $MESSAGE = SPECIALTY_ADD_ERR;
                        break;

                    default:
                        $MESSAGE = SPECIALTY_ADD_SUCC;
                        $STATUS = SUCCESS_STATUS;
                        break;
                }
            }

            $resp = array(
                'RESULT' => array(
                    'MESSAGE' => $MESSAGE,
                    'STATUS' => $STATUS
                )
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in addSpecialty_post function - ' . $ex);
        }
    }

    /*
     * TO DELETE THE SPECIALTY
     */

    function deleteSpecialty_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */
            $allowParam = array(
                'vId', 'specialtyId', 'needDelete'
            );
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->vendor_model->deleteSpecialty($this->post('specialtyId'), $this->post('needDelete'));
                switch ($resp) {
                    case 1:
                        $MESSAGE = SPECIALTY_DEL_SUCC;
                        $STATUS = SUCCESS_STATUS;
                        break;
                }
            }

            $resp = array(
                'RESULT' => array(
                    'MESSAGE' => $MESSAGE,
                    'STATUS' => $STATUS
                )
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in deleteSpecialty function - ' . $ex);
        }
    }

    /*
     * TO LIST OUT NUMBER OF TABLE REQUESTS
     */

    function tableRequestList_post() {
        try {

            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */

            $allowParam = array(
                'vId'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $REQUESTLIST = array();

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->vendor_model->tableRequestList($this->post('vId'));
                if ($resp != -1) {
                    $REQUESTLIST = $resp;
                    $MESSAGE = TABLE_REQUEST_FOUND;
                    $STATUS = SUCCESS_STATUS;
                }
            }

            $resp = array(
                'RESULT' => array(
                    'MESSAGE' => $MESSAGE,
                    'STATUS' => $STATUS,
                    'REQUESTLIST' => $REQUESTLIST
                )
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in tableRequest_post function - ' . $ex);
        }
    }

    /*
     * TO UPDATE THE REQUEST 
     */

    function updateTableRequest_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */

            //'requestTime'
            $allowParam = array(
                'bookingId',
                'vId', 'requestStatus'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->vendor_model->updateTableRequest($this->post());
                if ($resp != -1) {
                    $MESSAGE = TABLE_REQUEST_UPDT;
                    $STATUS = SUCCESS_STATUS;
                }
            }

            $resp = array(
                'RESULT' => array(
                    'MESSAGE' => $MESSAGE,
                    'STATUS' => $STATUS
                )
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in updateTableRequest_post function - ' . $ex);
        }
    }
    
    
    /*
     * TO UPDATE THE REQUEST 
     */

    function allowedPerson_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */

            //'requestTime'
            $allowParam = array(
                'vId'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $PERSONDATA = array();

            if (checkselectedparams($this->post(), $allowParam)) {
                $PERSONDATA = $this->vendor_model->getMinMaxPerson($this->post('vId'));
                $this->response($PERSONDATA, 200);
                if (!empty($PERSONDATA)) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = PERSON_FOUND_SUCC;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'RESULT' => array(
                    'MESSAGE' => $MESSAGE,
                    'STATUS' => $STATUS,
                    'PERSONDATA' => $PERSONDATA
                )
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in updateTableRequest_post function - ' . $ex);
        }
    }

}
