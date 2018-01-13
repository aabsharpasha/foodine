<?php

class OffersPurchase extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('offerspurchase_model');
        $this->load->helper('string');
        $this->controller = 'offerspurchase';
        $this->uppercase = 'Offers Purchase';
        $this->title = 'Offers Purchase Management';
    }

    /*
     * TO DISPLAY LIST OF DEALS TO THE TABLE FORMAT...
     */

    function index() {
        $viewData = array("title" => "Offers Purchase Management");
        $viewData['breadCrumbArr'] = array("deals" => "Offers Purchase Management");
        $this->load->view('offerspurchase/view', $viewData);
    }

    function paginate() {
        $data = $this->offerspurchase_model->getPaginationResult();
        echo json_encode($data);
    }
    
    function detail($iComboOffersID) {
        $viewData = array("title" => "Offers Purchase Detail",'iComboOffersID'=>$iComboOffersID);
        $viewData['breadCrumbArr'] = array("deals" => "Offers Purchase Detail");
        $this->load->view('offerspurchase/detail_view', $viewData);
    }
    
    function purchaseDetailPaginate($iComboOffersID) {
        $data = $this->offerspurchase_model->getPaginationPurchaseDetailResult($iComboOffersID);
        echo json_encode($data);
    }

}

?>
