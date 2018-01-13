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
        $this->load->library('aws_sdk');
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
        $viewData = array("title" => "SubOffers Management", 'iComboOffersID' => $iComboOffersID);
        $viewData['breadCrumbArr'] = array("deals" => "SubOffers Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';
        //print_r($viewData);
        $this->load->view('offers/subdeals_view', $viewData);
    }

    function offerPurchaseHistory() {
        $viewData = array("title" => "Offers Management");
        $viewData['breadCrumbArr'] = array("deals" => "Offers Management");
        $this->load->view('offers/history_view', $viewData);
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

    function historypaginate() {
        $data = $this->offers_model->get_paginationresulthistory();
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
        $restId=$this->offers_model->getRestarantId($_POST['rows'][0]);
        ////$res=$this->aws_sdk->deleteDirectory('combo/'.$restId[0]['iRestaurantID']);die;
        $removeDeal = '1';
        $removeDeal = $this->offers_model->removeDeals($_POST['rows']);
        if ($removeDeal != '') {
           // //$res=$this->aws_sdk->deleteDirectory('combo/'.$restId[0]['iRestaurantID']);
            echo '1';
        } else {
            echo '1';
        }
    }

    /*
     * DELETE EITHER ONE RECORD OR LIST OF RECORDS... 
     */

    function deleteAllSubDeals() {
        $data = $_POST['rows'];
        $removeDeal = '1';
        $removeDeal = $this->offers_model->removeSubDeals($_POST['rows']);
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
            $getSubOffers = $this->offers_model->getSubOffersDataAll($iComboOffersID);

            $viewData['getDealsData'] = $getData;
            $viewData['getSubOffers'] = $getSubOffers;
        }

        /*
         * GET RESTAURANT LIST
         */
        $viewData['getRestaurantData'] = $this->offers_model->getRestaurantList();

        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.offersadd') {
            $dealsAdd = $this->offers_model->addDeals($_POST);
            if ($dealsAdd != '') {
                foreach ($_POST['subOffer'] as $key => $value) {
                    $value['iComboOffersID'] = $dealsAdd;
                    $this->offers_model->addSubDeals($value);
                }
//                $result = $this->db->get_where('tbl_restaurant', array('iRestaurantID' => $_POST['iRestaurantID']));
//                $res = $result->row_array();
//                $data['to'] = $res['vEmail'];
//                $data['vRestaurantName'] = $res['vRestaurantName'];
//                $rest = $this->sendComboEmail($data);

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
            redirect('offers/index', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.offersedit') {
            $dealsAdd = $this->offers_model->editDeals($_POST);

            if ($this->input->post('iComboOffersID') != '') {
                $dealsAdd = $this->input->post('iComboOffersID');
                $getSubOffers = $this->offers_model->getSubOffersDataAll($this->input->post('iComboOffersID'));
                $subOffers = array();
                foreach ($getSubOffers AS $subOffer) {
                    $subOffers[$subOffer["iComboSubOffersID"]] = $subOffer["iComboSubOffersID"];
                }

                foreach ($_POST['subOffer'] as $key => $value) {
                    $value['iComboOffersID'] = $dealsAdd;
                    if (isset($value['iComboSubOffersID']) && $value['iComboSubOffersID'] != 0) {
                        unset($subOffers[$value['iComboSubOffersID']]);
                        $value['iDealID'] = $value['iComboSubOffersID'];
                        $this->offers_model->editSubDeals($value);
                    } else {
                        $this->offers_model->addSubDeals($value);
                    }
                }
                if (!empty($subOffers)) {
                    $this->offers_model->removeSubDeals($subOffers);
                }

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
                    $rempic = $this->input->post('vOfferUrl');                    
                    if (!empty($rempic)) {
                        $ImagePath = 'combo/' .$_POST['iRestaurantID'] . '/' . $rempic;
                        $ImageThumbPath = 'combo/' . $_POST['iRestaurantID'] . '/thumb/' . $rempic;
//                        //$this->aws_sdk->deleteImage($ImagePath);
//                        //$this->aws_sdk->deleteImage($ImageThumbPath);
                    }
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
                    $pic = $this->input->post('vOfferUrl');                    
                    if (!empty($pic)) {
                        $ImagePath = 'combo/' .$_POST['iRestaurantID'] . '/' . $pic;
                        $ImageThumbPath = 'combo/' . $_POST['iRestaurantID'] . '/thumb/' . $pic;
//                        //$this->aws_sdk->deleteImage($ImagePath);
//                        //$this->aws_sdk->deleteImage($ImageThumbPath);
                    }
                }

                $succ = array('0' => DEALS_EDITED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => DEALS_NOT_EDITED);
                $this->session->set_userdata('ERROR', $err);
            }
            redirect('offers/index', 'refresh');
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
        $viewData['iComboOffersID'] = $iOfferID;
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
            redirect('offers/suboffers/' . $iOfferID, 'refresh');
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
            redirect('offers/suboffers/' . $iOfferID, 'refresh');
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

    function viewPurchaseHistory($recordId = '') {
        if ($recordId !== '') {
            $data = $this->offers_model->getComboPurchaseRecords($recordId);
            $this->load->view('offers/viewPurchaseHistory', array('viewData' => $data));
        } return '';
    }

    function viewRedeemHistory($recordId = '') {
        if ($recordId !== '') {
            $data = $this->offers_model->getComboRedeemRecords($recordId);
            $this->load->view('offers/viewRedeemHistory', array('viewData' => $data));
        } return '';
    }

    function viewOfferDetail($recordId = '') {
        if ($recordId !== '') {
            $data = $this->offers_model->getSubDealsDataViewById($recordId);


            $this->load->view('offers/viewDetailSubOffer', array('viewData' => $data));
        } return '';
    }

    public function sendComboEmail($data) {
        $subject = 'Combo Offer Added.';
        $this->load->model("smtpmail_model", "smtpmail_model");
        $param = array(
            '%MAILSUBJECT%' => 'HungerMafia : ' . $subject,
            '%LOGO_IMAGE%' => BASEURL . 'images/hungermafia.png',
            '%REST_NAME%' => $data['vRestaurantName'],
        );

        $tmplt = DIR_ADMIN_VIEW . 'template/combo_offer.php';
//        mprd($tmplt);
        $subject = 'HungerMafia : ' . $subject;
        $to = $data['to'];
        $this->smtpmail_model->send($to, $subject, $tmplt, $param);
        return 1;
    }

    /*
     * TO CHANGE THE STATUS OF THE RECORD..
     */

    function comboStatus($iUserComboID = '', $rm = '') {
        if ($iUserComboID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->offers_model->changeUserComboStatus($iUserComboID);
            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

    function offer_menu($iComboOffersID = '') {
        if ($iComboOffersID != '') {
            $this->load->model('offers_model');
            $getRecords = $this->offers_model->getDealsDataById($iComboOffersID);

            $viewData = array("title" => "Offer Menu Image");
            $viewData['breadCrumbArr'] = array("restaurant" => "Offer Menu Image");
            if ($getRecords != '') {
                $viewData['record_set'] = $getRecords;
            } else {
                $viewData['record_set'] = '';
            }

            $viewData['iComboOffersID'] = $iComboOffersID;

            $this->load->view('offers/offer_menu_view', $viewData);
        } else {
            redirect('restaurant', 'refresh');
        }
    }

    function offer_menu_add($iComboOffersID = '', $iPictureID = '', $ed = '') {
        if ($iComboOffersID != '') {
            $viewData['title'] = "Add / Edit Combo Offer Menu Photo";
            $viewData['iComboOffersID'] = $iComboOffersID;
            $viewData['iMenuPictureID'] = $iPictureID;

            $viewData['ACTION_LABEL'] = (isset($iPictureID) && $iPictureID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

            if ($iPictureID != '' && $ed != '' && $ed == 'y') {
                $getData = $this->offers_model->offerMenuById($iPictureID);
                $viewData['getPictureData'] = $getData;
            }

            if ($this->input->post('action') && $this->input->post('action') == 'backoffice.imageadd') {

                if ($this->input->post('iComboOffersID') !== '') {

                    $uploadedFiles = array();
                    if (isset($_FILES['res_image']) && !empty($_FILES['res_image'])) {

                        $DEFAULT_PARAM = array(
                            'fType' => 'image', // file format
                            'fLimit' => 20, // maximum upload file limit 
                            'fLoc' => array(
                                'key' => 'comboOfferMenu', // user     - user
                                'id' => $iComboOffersID // user-id  - 1
                            ),
                            'fThumb' => TRUE, // only for image format
                            'fCopyText' => FALSE // only for image format
                        );
                        $this->load->library('fupload', $DEFAULT_PARAM);
                        $uploadedFiles = $this->fupload->fUpload($_FILES, 'res_image');
                    }

                    $submitArry = array(
                        'menu_type' => $this->input->post('menu_type'),
                        'iComboOffersID' => $iComboOffersID,
                        'images' => $uploadedFiles
                    );
                    $this->offers_model->add_offer_menu($submitArry);

                    $succ = array('0' => IMAGE_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);

                    redirect('offers/offer_menu/' . $iComboOffersID, 'refresh');
                } else {
                    $err = array('0' => 'Combo Offer Value not defined..!!');

                    $this->session->set_userdata('ERROR', $err);
                }
            } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.imageedit') {

                if ($this->input->post('iComboOffersID') !== '') {
                    $uploadedFiles = array();
                    if (isset($_FILES['res_image']) && !empty($_FILES['res_image'])) {

                        $DEFAULT_PARAM = array(
                            'fType' => 'image', // file format
                            'fLimit' => 20, // maximum upload file limit 
                            'fLoc' => array(
                                'key' => 'comboOfferMenu', // user     - user
                                'id' => $iComboOffersID // user-id  - 1
                            ),
                            'fThumb' => TRUE, // only for image format
                            'fCopyText' => FALSE // only for image format
                        );
                        $this->load->library('fupload', $DEFAULT_PARAM);
                        $uploadedFiles = $this->fupload->fUpload($_FILES, 'res_image');
                    }
                    $submitArry = array(
                        'iMenuPictureID' => $this->input->post('iMenuPictureID'),
                        'menu_type' => $this->input->post('menu_type'),
                        'iComboOffersID' => $iComboOffersID,
                        'images' => $uploadedFiles
                    );
                    $deleteMenuPhoto = empty($uploadedFiles) ? FALSE : TRUE;
                    $this->offers_model->edit_offer_menu($submitArry, $deleteMenuPhoto);
                    $succ = array('0' => IMAGE_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);

                    redirect('offers/offer_menu/' . $iComboOffersID, 'refresh');
                } else {
                    $err = array('0' => 'Combo Offer Value not defined..!!');
                    $this->session->set_userdata('ERROR', $err);
                }
            }

            $this->load->view('offers/offer_menu_add_view', $viewData);
        } else {
            redirect('offers/index', 'refresh');
        }
    }

    function deleteOfferMenu() {
        $data = $_POST['rows'];
        $removeImage = $this->offers_model->removeOfferMenu($_POST['rows']);
        if ($removeImage != '') {
            echo '1';
        } else {
            echo '0';
        }
    }

    function offer_menu_paginate($iComboOffersID) {
        $data = $this->offers_model->offer_menu_paginate($iComboOffersID);
        echo json_encode($data);
    }

    function offerMenuStatus($iPictureID = '', $rm = '') {
        if ($iPictureID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->offers_model->changeImageStatusOfferMenu($iPictureID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("image", 'refresh');
    }

}

?>
