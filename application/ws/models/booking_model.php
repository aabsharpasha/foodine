<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of link_model
 * link hashes expiry 24 hrs
 * @author Amit Malakar
 */
class Booking_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_booking';
    }

    function initiateBooking($data) {
        try {
            return TRUE;
        } catch (Exception $ex) {
            exit('Link Model : Error in initiateBooking function - ' . $ex);
        }
    }

    function confirmedBooking($userId) {
        try {
            return TRUE;
        } catch (Exception $ex) {
            exit('Link Model : Error in confirmedBooking function - ' . $ex);
        }
    }

    function pendingBooking($userId) {
        try {
            return TRUE;
        } catch (Exception $ex) {
            exit('Link Model : Error in pendingBooking function - ' . $ex);
        }
    }

    function declinedBooking($userId) {
        try {
            return TRUE;
        } catch (Exception $ex) {
            exit('Link Model : Error in declinedBooking function - ' . $ex);
        }
    }

   

}
