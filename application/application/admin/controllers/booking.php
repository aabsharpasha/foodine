<?php

class Booking extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('booking_model');
        $this->load->helper('string');
        $this->controller = 'booking';
        $this->uppercase = 'Restaurant Booking';
        $this->title = 'Booking Management';
    }

    /*
     * TO DISPLAY LIST OF DEALS TO THE TABLE FORMAT...
     */

    function index() {
        //$getRecords = $this->booking_model->getBookingDataAll();
        $viewData = array("title" => "Booking Management");
        $viewData['breadCrumbArr'] = array("booking" => "Booking Management");
        /* if ($getRecords != '')
          $viewData['record_set'] = $getRecords;
          else
          $viewData['record_set'] = ''; */

        $viewData['bookingRec'] = $this->booking_model->get_paginationresult(FALSE);


        $this->load->view('booking/booking_view', $viewData);
    }

    function history() {
        $viewData = array("title" => "Booking History");
        $viewData['breadCrumbArr'] = array("booking" => "Booking History");
        
        $this->load->view('booking/booking_history', $viewData);
    }

    function partyRequests() {
        $viewData   = array("title" => "Party Requests");
        $viewData['breadCrumbArr']  = array("booking" => "Booking Management");
        $this->load->view('booking/party_request_view', $viewData);
    }

    function banquetEnquiry() {
        $viewData   = array("title" => "Banquet Enquiry");
        $viewData['breadCrumbArr']  = array("booking" => "Booking Management");
        $this->load->view('booking/banquet_request_view', $viewData);
    }
    
    /*
     * TO LIST OUT THE LIST OF RECORDS...
     */
    function paginate() {
        $data = $this->booking_model->get_paginationresult();
        echo json_encode($data);
    }

    function paginate_history() {
        $data = $this->booking_model->get_paginationhistoryresult();
        echo json_encode($data);
    }

    function paginate_partyRequests() {
        $data = $this->booking_model->getPaginatePartyRequests();
        echo json_encode($data);
    }

    function paginate_banquetEnquiry() {
        $data = $this->booking_model->getPaginateBanquetRequests();
        echo json_encode($data);
    }

    function viewPartyDetail($recordId=''){
        if ($recordId !== '') {
            $data = $this->booking_model->getPartyRequestById($recordId);

            $this->load->view('booking/viewDetail', array('viewData' => $data));
        } return '';
    }
    
    function changePartyBookingStatus($status='',$id=''){
        $data    = '';
        if ($status !== '' && $id !== '' ) {
            $data = $this->booking_model->changePartyBookingStatus($status,$id);
        }
        echo $data;
        die;
    }

    /*
     * DELETE EITHER ONE RECORD OR LIST OF RECORDS... 
     */

    function deleteAll() {
        if (isset($_POST['rows'])) {
            $data = $_POST['rows'];
            $removeDeal = $this->booking_model->removeBooking($_POST['rows']);
            if ($removeDeal != '') {
                echo '1';
            } else {
                echo '0';
            }
        } else {
            echo '0';
        }
    }

    function deleteAllPartyBooking(){
        if (isset($_POST['rows'])) {
            $data = $_POST['rows'];
            $removeDeal = $this->booking_model->removePartyBooking($_POST['rows']);
            if ($removeDeal != '') {
                echo '1';
            } else {
                echo '0';
            }
        } else {
            echo '0';
        }
    }
    
    /*
     * TO CHANGE THE STATUS OF THE RECORD..
     */

    function status($iDealID = '', $rm = '') {
        if ($iDealID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->booking_model->changeBookingStatus($iDealID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
    }
    
    function visitedStatus($iBookingID = ''){
        if ($iBookingID != '') {
            $changeStatus = $this->booking_model->changeBookingVisitedStatus($iBookingID);
            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        
    }

    function waitstatus($iBookingID = '', $rm = '') {
        if ($iBookingID != '' && $rm != '' && $rm == 'y') {

            $changeStatus = $this->booking_model->waitStatus($iBookingID, $_POST['waitTime']);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

    function report() {
        $viewData = array("title" => "Deals Management");
        $viewData['breadCrumbArr'] = array("deals" => "Deals Management");
        $this->load->view('booking/deals_viewreport', $viewData);
    }

    /*
     * TO LIST OUT THE LIST OF RECORDS...
     */

    function reportPaginate() {
        $data = $this->booking_model->get_reportpaginationresult();
        echo json_encode($data);
    }
    
    function changeAvailedStatus($id = ''){
        if ($id != '') {
            $changeStatus = $this->booking_model->changeAvailedStatus($id);
            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        
    }

    function offerPurchaseHistory() {
        $viewData = array("title" => "Offers Management");
        $viewData['breadCrumbArr'] = array("deals" => "Offers Management");
        $this->load->view('offers/history_view', $viewData);
    }
    
    function historypaginate() {
        $this->load->model('offers_model');
        $data = $this->offers_model->get_paginationresulthistory();
        echo json_encode($data);
    }

    function comboStatus($iUserComboID = '', $rm = '') {
        if ($iUserComboID != '' && $rm != '' && $rm == 'y') {
            $this->load->model('offers_model');
            $changeStatus = $this->offers_model->changeUserComboStatus($iUserComboID);
            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

}

?>
