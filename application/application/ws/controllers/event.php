<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author OpenXcell Technolabs
 */
class Event extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('event_model');
    }

    /*
     * Method to return json for all active events 
     * 
     * @return array data with event listing
     * @author Anurag Srivastava (anurag.srivastava@kelltontech.com)
     */

    function getAllEvents_post() {
        try {
            // SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $EVENTTOTALDATA = 0;
            $EVENTTOTALPAGE = 0;
            $EVENTS = array();
            $BANNER = array();
            // Fetching all the events
            $resp = $this->event_model->getEventList($this->post());
            //print_r($resp['totalRecords']); exit;
            // If response is not empty
            if ($resp['totalRecords'] > 0) {
                $EVENTS = $resp['event'];
                $EVENTTOTALDATA = $resp['totalRecords'];
                $EVENTTOTALPAGE = $resp['totalPage'];
                $BANNER = $resp['banner'];
                $MESSAGE = 'Events found';
                $STATUS = SUCCESS_STATUS;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            // Preparing the response data
            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'EVENTS' => $EVENTS,
                'TOTALRECORDS' => $EVENTTOTALDATA,
                'TOTALPAGE' => $EVENTTOTALPAGE,
                'BANNER' => $BANNER
            );

            // Displaying the response
            $this->response($resp, 200);
        }
        // If any unexpected error occurs
        catch (Exception $ex) {
            // Preparing the response data
            $resp = array(
                'MESSAGE' => $ex->getMessage(),
                'STATUS' => FAIL_STATUS,
                'EVENTS' => ''
            );
            // Displaying the response
            $this->response($resp, 200);
        }
    }

    /*
     * Method to return json for all available filters on event. 
     * 
     * @return json data with event filter
     * @author Anurag Srivastava (anurag.srivastava@kelltontech.com)
     */

    function getEventFilter_post() {
        try {

            // Default value
            $DATA = array();
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            $resp = $this->event_model->getAllEventFilter($this->post());
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

    function getEventDetail_post() {
        try {
            // Default value
            $DATA = array('eventId');
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            $resp = $this->event_model->getEventDetail($this->post('eventId'));
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
            throw new Exception('Error in getEventDetail function - ' . $ex);
        }
    }

}
