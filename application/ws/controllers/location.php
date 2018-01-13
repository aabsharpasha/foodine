<?php

class Locations extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('location_model');
        $this->load->helper('string');
        $this->controller = 'location';
        $this->uppercase = 'Location';
        $this->title = 'Locations Management';
    }

    /*
     * TO DISPLAY LIST OF DEALS TO THE TABLE FORMAT...
     */

    function getLocationZone_post() {
        try {
            $MESSAGE = NO_RECORD_FOUND;
            $STATUS = FAIL_STATUS;
            $res = $this->location_model->getZone();
            if (!empty($res)) {
                $MESSAGE = '';
                $STATUS = SUCCESS_STATUS;
                $LOCATION = $res;
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$BOOKINGDATA != '') {
                $resp['LOCATION'] = $LOCATION;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getLocationZone_post function - ' . $ex);
        }
    }

}

?>
