<?php

class Offers extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('offers_model');
        $this->load->helper('string');
        $this->controller = 'offers';
        $this->uppercase = 'Offers';
        $this->title = 'Offers Management';
    }

    /*
     * TO DISPLAY LIST OF DEALS TO THE TABLE FORMAT...
     */

    function index() {
        $getRecords = $this->offers_model->getDealsDataAll();
        $viewData = array("title" => "Offers Management");
        $viewData['breadCrumbArr'] = array("deals" => "Offers Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';
        
        $this->load->view('offers/deals_view', $viewData);
    }
    
    function suboffers($iComboOffersID) {
        $getRecords = $this->offers_model->getSubOffersDataAll($iComboOffersID);
        //print_r($getRecords);die;
        $viewData = array("title" => "SubOffers Management",'iComboOffersID'=>$iComboOffersID);
        $viewData['breadCrumbArr'] = array("deals" => "SubOffers Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';
        //print_r($viewData);
        $this->load->view('offers/subdeals_view', $viewData);
    }

    /*
     * TO LIST OUT THE LIST OF RECORDS...add
     */

    function paginate() {
        $data = $this->offers_model->get_paginationresult();
        echo json_encode($data);
    }
    
    function subpaginate($iComboOffersID) {
        $data = $this->offers_model->get_paginationresultsuboffers($iComboOffersID);
        echo json_encode($data);
    }

    /*
     * TO LIST OUT THE LIST OF RECORDS...
     */

    function likePaginate($iDealID = '') {
        $data = $this->offers_model->get_like_paginationresult($iDealID);
        echo json_encode($data);
    }

    /*
     * DELETE EITHER ONE RECORD OR LIST OF RECORDS... 
     */

    function deleteAll() {
        $data = $_POST['rows'];
        $removeDeal =  '1';
        //$removeDeal = $this->offers_model->removeDeals($_POST['rows']);
        if ($removeDeal != '') {
            echo '1';
        } else {
            echo '0';
        }
    }

    /*
     * TO CHANGE THE STATUS OF THE RECORD..
     */

    function status($iComboOffersID = '', $rm = '') {
        if ($iComboOffersID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->offers_model->changeDealsStatus($iComboOffersID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

    /*
     * TO ADD / EDIT THE DEALS...
     */

    function add($iComboOffersID = '', $ed = '') {
        $viewData['title'] = "Offers Management";

        $viewData['ACTION_LABEL'] = (isset($iComboOffersID) && $iComboOffersID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iComboOffersID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->offers_model->getDealsDataById($iComboOffersID);

            $viewData['getDealsData'] = $getData;
        }

        /*
         * GET RESTAURANT LIST
         */
        $viewData['getRestaurantData'] = $this->offers_model->getRestaurantList();

        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.offersadd') {
            //print_r($_POST);die;
            $dealsAdd = $this->offers_model->addDeals($_POST);
            if ($dealsAdd != '') {

                $param = array(
                    'fileType' => 'image',
                    'maxSize' => 20,
                    'uploadFor' => array(
                        'key' => 'combo',
                        'id' => $_POST['iRestaurantID']
                    ),
                    'requireThumb' => TRUE
                );
                //print_r($param);die;
                $this->load->library('fileupload', $param);
                
                if (isset($_FILES) && !empty($_FILES) && $_FILES['vOfferImage']['error'] === 0) {

                    $this->fileupload->removeFile();
                    $upload_files = $this->fileupload->upload($_FILES, 'vOfferImage');

                   // print_r($upload_files);die;
                    if (!empty($upload_files)) {

                        /*
                         * NEED TO UPDATE THE FILE NAME TO DATABASE...
                         */

                        foreach ($upload_files as $V) {
                            $this->db->update('tbl_combo_offers', array('vOfferImage' => $V), array('iComboOffersID' => $dealsAdd));
                        }
                        $fileChange = TRUE;
                    }
                }

                $this->load->library('fileupload', $param);
                $succ = array('0' => DEALS_ADDED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => DEALS_NOT_ADDED);
                $this->session->set_userdata('ERROR', $err);
            }
            redirect('offers', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.offersedit') {
            
            $dealsAdd = $this->offers_model->editDeals($_POST);
            if ($this->input->post('iComboOffersID') != '') {
                $dealsAdd = $this->input->post('iComboOffersID');

                $param = array(
                    'fileType' => 'image',
                    'maxSize' => 20,
                    'uploadFor' => array(
                        'key' => 'combo',
                        'id' => $_POST['iRestaurantID']
                    ),
                    'requireThumb' => TRUE
                );

                $this->load->library('fileupload', $param);


                if ($this->input->post('removepic') == '1') {

                    $this->fileupload->removeFile();

                    /*
                     * UPDATE IMAGE TO NULL VALUE
                     */
                    $this->db->update('tbl_combo_offers', array('vOfferImage' => ''), array('iComboOffersID' => $dealsAdd));
                }

                if (isset($_FILES) && !empty($_FILES) && $_FILES['vOfferImage']['error'] === 0) {


                    $this->fileupload->removeFile();
                    $upload_files = $this->fileupload->upload($_FILES, 'vOfferImage');

                    if (!empty($upload_files)) {

                        /*
                         * NEED TO UPDATE THE FILE NAME TO DATABASE...
                         */

                        foreach ($upload_files as $V) {
                            $this->db->update('tbl_combo_offers', array('vOfferImage' => $V), array('iComboOffersID' => $dealsAdd));
                        }
                        $fileChange = TRUE;
                    }
                }

                $succ = array('0' => DEALS_EDITED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => DEALS_NOT_EDITED);
                $this->session->set_userdata('ERROR', $err);
            }
            redirect('offers', 'refresh');
        }

        $this->load->view('offers/deals_add_view', $viewData);
    }

    function subofferadd($iOfferID = '', $iDealID = '', $ed = '') {
        $viewData['title'] = "Sub Offers Management";

        $viewData['ACTION_LABEL'] = (isset($iDealID) && $iDealID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iDealID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->offers_model->getSubDealsDataById($iDealID);
            $viewData['iComboSubOffersID'] = $iDealID;
            $viewData['getDealsData'] = $getData;
            
            //print_r($viewData);
        }
        $viewData['iComboOffersID']  = $iOfferID;
        /*
         * GET RESTAURANT LIST
         */
        //$viewData['getRestaurantData'] = $this->offers_model->getRestaurantList();
        
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.offersadd') {
             $iOfferID = $_POST['iComboOffersID'];
            //print_r($_POST);die;
            $dealsAdd = $this->offers_model->addSubDeals($_POST);
            if ($dealsAdd != '') {
                $succ = array('0' => DEALS_ADDED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => DEALS_NOT_ADDED);
                $this->session->set_userdata('ERROR', $err);
            }
            redirect('offers/suboffers/'.$iOfferID, 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.offersedit') {
            $iOfferID = $_POST['iComboOffersID'];
            $dealsAdd = $this->offers_model->editSubDeals($_POST);
            //print_r($dealsAdd);die;
            if ($this->input->post('iDealID') != '') {
                $dealsAdd = $this->input->post('iDealID');

                $succ = array('0' => DEALS_EDITED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => DEALS_NOT_EDITED);
                $this->session->set_userdata('ERROR', $err);
            }
            //echo $iOfferID;die;
            redirect('offers/suboffers/'.$iOfferID, 'refresh');
        }

        $this->load->view('offers/suboffer_add_view', $viewData);
    }

    /*
     * TO CHECK THE DEAL CODE IS ALREADY EXISTS OR NOT...
     */

    function checkDealCode() {
        if (@$this->input->post('vDealCode') !== '') {
            /*
             * TO CHECKL WITH THE DATABASE...
             */
            echo $this->offers_model->checkDealCodeExists($this->input->post('vDealCode'), $this->input->post('iDealID'));
        } else {
            echo 'true';
        }
    }

    /*
     * TO SHOW TOTAL LIKES..
     */

    function likes($iDealID = '') {
        //echo $iDealID;
        if ($iDealID != '') {
            $getData = $this->offers_model->getDealsDataById($iDealID);
            $viewData['ACTION_LABEL'] = "View";
            $viewData['getDealsData'] = $getData;
            $viewData['title'] = $getData['vOfferText'] . " Deal";
            $viewData['iDealID'] = $iDealID;

            $this->load->view('deals/deals_like_view', $viewData);
        } else {
            
        }
    }

    /*
     * TO LOAD RESTAURANT VIEW HTML
     */

    function viewDetail($recordId = '') {
        if ($recordId !== '') {
            $data = $this->offers_model->getDealsDataViewById($recordId);
            $this->load->view('offers/viewDetail', array('viewData' => $data));
        } return '';
    }
    
    function viewOfferDetail($recordId = '') {
        if ($recordId !== '') {
            $data = $this->offers_model->getSubDealsDataViewById($recordId);
            

            $this->load->view('offers/viewDetailSubOffer', array('viewData' => $data));
        } return '';
    }

}

?>
