<?php

class Banner extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('restaurant_model');
        $this->load->model('banner_model');
//        $this->load->model('offers_model');
        $this->load->helper('string');
        $this->controller = 'banner';
        $this->uppercase = 'Banner';
        $this->title = 'Banner Management';
    }

    // INDEX
    function indexold($iUserID = '') {
        $getRecords = $this->banner_model->getRestaurantDataAll();
        $getEvents = $this->banner_model->getEventDataAll();
        $getDeals = $this->banner_model->getDealsDataAll();
        $getCombo = $this->banner_model->getComboDataAll();
        $viewData = array("title" => "Banner Management");
        $viewData['breadCrumbArr'] = array("banner" => "Banner Management");
        $viewData['data'] = empty($getRecords) ? '' : $getRecords;
        $viewData['eventData'] = empty($getEvents) ? '' : $getEvents;
        $viewData['dealData'] = empty($getDeals) ? '' : $getDeals;
        $viewData['comboData'] = empty($getCombo) ? '' : $getCombo;
//        mprd($getEvents);
        $typeArray = ['featured', 'event', 'deals', 'combo'];
        foreach ($typeArray as $value) {
            $viewData[$value] = $this->banner_model->getBanner($value);
        }
        $viewData['get_iUserID'] = $iUserID;

        $this->load->view('banner/banner_view', $viewData);
    }

    function saveBanner() {
        extract($_POST);
        $this->banner_model->saveBanner($id, $restId, $type);
    }

    function index($bannerType = '') {
        $viewData = array("title" => "Banner Management");
        if (!empty($getRecords)) {
            $viewData['record_set'] = $getRecords;
        } else {
            $viewData['record_set'] = '';
        }
        $viewData['breadCrumbArr'] = array("banner" => "Banner Management");
        switch ($bannerType) {
            case "featured":
                $this->load->view('banner/featured_banner_view', $viewData);
                break;
            case "combo":
                $this->load->view('banner/combo_banner_view', $viewData);
                break;
            case "deals":
                $this->load->view('banner/deals_banner_view', $viewData);
                break;
            case "event":
                $this->load->view('banner/event_banner_view', $viewData);
                break;
            default:
                $this->load->view('banner/featured_banner_view', $viewData);
        }
    }

    function paginate($type) {
        $data = $this->banner_model->get_paginationresult($type);
        echo json_encode($data);
    }

    function add($bannerType = '', $id = '') {
        if (empty($bannerType)) {
            $bannerType = $this->input->post('type');
        }
        if ($this->input->post('action') && $this->input->post('action') == 'add') {
            $add = $this->banner_model->addBanner($_POST);
            if ($add) {
                 if (isset($_FILES) && !empty($_FILES) && $bannerType == 'featured') {
                    // echo 'hi'; exit;

                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'banner',
                                'id' => $add
                            ),
                            'requireThumb' => TRUE
                        );

                        $this->load->library('fileupload', $param);
                        $upload_files = $this->fileupload->upload($_FILES, 'vCuisineImage');

                        if (!empty($upload_files)) {
                            /*
                             * NEED TO UPDATE THE FILE NAME TO DATABASE...
                             */
                            foreach ($upload_files as $V) {
                                $this->banner_model->updateBannerImage($add, $V);
                            }
                        }
                        //print_r($upload_files); exit;
                    }
                $succ = array('0' => "Banner successfully added.");
                $this->session->set_userdata('SUCCESS', $succ);
                redirect('banner/index/' . $bannerType, 'refresh');
            } else {
                $err = array('0' => "Failed to add banner.");
                $this->session->set_userdata('ERROR', $err);
            }
        }
        if ($this->input->post('action') && $this->input->post('action') == 'edit') {
            $add = $this->banner_model->editBanner($_POST);
            $iCuisineID = $this->input->post('iBannerId');
                $fileChange = FALSE;
                //mprd($_FILES);
                $param = array(
                    'fileType' => 'image',
                    'maxSize' => 20,
                    'uploadFor' => array(
                        'key' => 'banner',
                        'id' => $iCuisineID
                    ),
                    'requireThumb' => TRUE
                );

                $this->load->library('fileupload', $param);

//                if ($this->input->post('removepic') == '1') {
//
//                    $this->fileupload->removeFile();
//                    $removeImg = $this->input->post('vBannerImage');
//                    if (!empty($removeImg)) {
//                        $ImagePath = 'banner/' . $iCuisineID . '/' . $removeImg;
//                        $ImageThumbPath = 'banner/' . $iCuisineID . '/thumb/' . $removeImg;
//                        //$this->aws_sdk->deleteImage($ImagePath);
//                        //$this->aws_sdk->deleteImage($ImageThumbPath);
//                    }
//                    /*
//                     * UPDATE IMAGE TO NULL VALUE
//                     */
//                    $this->db->update('tbl_banner', array('vBannerImage' => ''), array('iBannerId' => $iCuisineID));
//                }

                if (isset($_FILES) && !empty($_FILES) && $_FILES['vCuisineImage']['error'] === 0) {
                    if (isset($_FILES['vCuisineImage']['name']) && !empty($_FILES['vCuisineImage']['name'])) {

                        $param = array(
                            'fileType' => 'image',
                            '$parammaxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'banner',
                                'id' => $iCuisineID
                            ),
                            'requireThumb' => TRUE
                        );

                        $this->load->library('fileupload', $param);
                        $this->fileupload->removeFile();
                        $upload_files = $this->fileupload->upload($_FILES, 'vCuisineImage');

                        if (!empty($upload_files)) {

                            /*
                             * NEED TO UPDATE THE FILE NAME TO DATABASE...
                             */

                            foreach ($upload_files as $V) {
                                $this->banner_model->updateBannerImage($iCuisineID, $V);
                            }
                            $fileChange = TRUE;
                        }
                        $oldImg = $this->input->post('vCuisineUrl');
                        if (!empty($oldImg)) {
                            $ImagePath = 'banner/' . $iCuisineID . '/' . $oldImg;
                            $ImageThumbPath = 'banner/' . $iCuisineID . '/thumb/' . $oldImg;
                            //$this->aws_sdk->deleteImage($ImagePath);
                            //$this->aws_sdk->deleteImage($ImageThumbPath);
                        }
                    }
                }
            if ($add) {
                $succ = array('0' => "Banner successfully updated.");
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => "Failed to update banner.");
                $this->session->set_userdata('ERROR', $err);
            }
            redirect('banner/index/' . $bannerType, 'refresh');
        }
        $viewData['title'] = "Banner Management";
        if (empty($bannerType)) {
            $bannerType = $this->input->post('type');
        }
        switch ($bannerType) {
            case "featured":
                $bannerArray = $this->banner_model->getRestaurantDataAll();
                break;
            case "combo":
                $bannerArray = $this->banner_model->getRestaurantDataAll();
                //$bannerArray = $this->banner_model->getComboDataAll();
                break;
            case "deals":
                $bannerArray = $this->banner_model->getRestaurantDataAll();
                break;
            case "event":
                $bannerArray = $this->banner_model->getRestaurantDataAll();
                //$bannerArray = $this->banner_model->getEventDataAll();
                break;
            default:
                $bannerArray = $this->banner_model->getRestaurantDataAll();
                $bannerType = "featured";
                break;
        }
        $viewData['ACTION_LABEL'] = ( $id != '') ? "Edit" : "Add";
        $viewData['bannerType'] = $bannerType;
        $viewData['bannerArray'] = $bannerArray;

        if ($id != '') {
            $bannerData = array();
            $getData = $this->banner_model->getBannerDataById($id, $bannerType);
            switch ($bannerType) {
                case "combo":
                    $bannerData = $this->banner_model->getComboDataAll($getData['iRestaurantId']);
                    break;
                case "deals":
                    $bannerData = $this->banner_model->getDealsDataAll($getData['iRestaurantId']);
                    break;
                case "event":
                    $bannerData = $this->banner_model->getEventDataAll($getData['iRestaurantId']);
                    break;
            }
            $viewData['bannerData'] = $getData;
            $viewData['bannerDataArray'] = $bannerData;
        }

        $this->load->view('banner/banner_add_view', $viewData);
    }

    function deleteAll() {
        $data = $_POST['rows'];
        if ($this->banner_model->softDeleteBanner($data)) {
            echo '1';
        } else {
            echo '0';
        }
    }

    function status($id = '', $rm = '') {
        if ($id != '' && $rm != '' && $rm == 'y') {
            if ($this->banner_model->changeStatus($id)) {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

    function getBanner() {
        $bannerType = $_POST['bannerType'];
        $restId = $_POST['restId'];
        switch ($bannerType) {
            case "combo":
                $bannerArray = $this->banner_model->getComboDataAll($restId);
                break;
            case "deals":
                $bannerArray = $this->banner_model->getDealsDataAll($restId);
                break;
            case "event":
                $bannerArray = $this->banner_model->getEventDataAll($restId);
                break;
        }
        echo json_encode($bannerArray);
        exit;
    }

}
