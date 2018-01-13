<?php

class Restaurant extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('restaurant_model');
        $this->load->model('music_model');
        $this->load->model('offers_model');
        $this->load->helper('string');
        $this->controller = 'restaurant';
        $this->uppercase = 'Restaurant';
        $this->title = 'Restaurant Management';
    }

    // INDEX
    function index() {
        $getRecords = $this->restaurant_model->getRestaurantDataAll();
        $viewData = array("title" => "Restaurant Management");
        $viewData['breadCrumbArr'] = array("restaurant" => "Restaurant Management");
        if ($getRecords != '') {
            $viewData['record_set'] = $getRecords;
        } else {
            $viewData['record_set'] = '';
        }

        $this->load->view('restaurant/restaurant_view', $viewData);
    }

    /*
     * to load the email template...
     */

    function template() {
        echo file_get_contents(DIR_ADMIN_VIEW . $this->controller . '/email/new_restaurant.php');
    }

    // ***************************************************************
    // ADD
    // ***************************************************************
    function add($iRestaurantID = '', $ed = '') {
        ini_set('memory_limit', '256M'); 
        $viewData['title'] = "Restaurant Management";

        if (isset($_POST['tSpecialty'])) {
            $_POST['tSpecialty'] = str_replace('[', '', $_POST['tSpecialty']);
            $_POST['tSpecialty'] = str_replace(']', '', $_POST['tSpecialty']);
            $_POST['tSpecialty'] = str_replace('"', '', $_POST['tSpecialty']);
        }

        if (isset($_POST['tDrinkSpecialty'])) {
            $_POST['tDrinkSpecialty'] = str_replace('[', '', $_POST['tDrinkSpecialty']);
            $_POST['tDrinkSpecialty'] = str_replace(']', '', $_POST['tDrinkSpecialty']);
            $_POST['tDrinkSpecialty'] = str_replace('"', '', $_POST['tDrinkSpecialty']);
        }


        $viewData['ACTION_LABEL'] = (isset($iRestaurantID) && $iRestaurantID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";
//echo $iRestaurantID; exit;
        if ($iRestaurantID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->restaurant_model->getRestaurantDataById($iRestaurantID);
//print_r($getData); exit;
            $getLocData = $this->restaurant_model->getStateCityDataByLocId($getData['iLocationID']);
            if(!empty($getLocData)){
                $getData['iLocZoneID'] = $getLocData['iLocZoneID'];
                $getData['iStateID'] = $getLocData['iStateID'];
            }
            $getCategory = $this->restaurant_model->getCategoryDataById($iRestaurantID);
            $getCuisine = $this->restaurant_model->getCuisineDataById($iRestaurantID);
            $getCollection = $this->restaurant_model->getCollectionDataById($iRestaurantID);
            $getMinorCuisine = $this->restaurant_model->getMinorCuisineDataById($iRestaurantID);
            $getFacility = $this->restaurant_model->getFacilityDataById($iRestaurantID);
            $getMusic = $this->restaurant_model->getMusicDataById($iRestaurantID);
//            mprd($getMinorCuisine);
            $viewData['getRestaurantData'] = $getData;
            $viewData['getCategoryData'] = $getCategory;
            $viewData['getCuisineData'] = $getCuisine;
            $viewData['getCollectionData'] = $getCollection;
            $viewData['getMinorCuisineData'] = $getMinorCuisine;
            $viewData['getFacilityData'] = $getFacility;
            $viewData['getMusicData'] = $getMusic;
            $viewData['getLocations'] = $this->restaurant_model->getLocation($getData['iLocZoneID']);
            $viewData['getCities'] = $this->restaurant_model->getCities($getData['iStateID']);
        }
        $viewData['getStates'] = $this->restaurant_model->getStates();
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.restaurantadd') {

//            if ($this->restaurant_model->checkRestaurantEmailAvailable($this->input->post('vEmail'))) {
          // print_r($_POST);die;
            $restaurantAdd = $this->restaurant_model->addRestaurant($_POST);

            if ($restaurantAdd != '') {
                foreach ($_FILES as $key => $value) {
                    if ($key == 'vRestaurantLogo') {
                        $targetpath = UPLOADS . "restaurant/" . $restaurantAdd;
                  //      echo "hello".$targetpath; exit;
                        $DEFAULT_PARAM = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'restaurant',
                                'id' => $restaurantAdd
                            ),
                            'requireThumb' => TRUE,
                        );
                    } else if ($key == 'vRestaurantMobLogo') {
                        $targetpath = UPLOADS . "restaurantMobile/" . $restaurantAdd;
                        $DEFAULT_PARAM = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'restaurantMobile',
                                'id' => $restaurantAdd
                            ),
                            'requireThumb' => TRUE,
                        );
                    } else if ($key == 'vRestaurantListing') {
                        $targetpath = UPLOADS . "restaurantListing/" . $restaurantAdd;
                        $DEFAULT_PARAM = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'restaurantListing',
                                'id' => $restaurantAdd
                            ),
                            'requireThumb' => TRUE,
                        );
                    } else if ($key == 'vTableLayout') {
                        $targetpath = UPLOADS . "vTableLayout/" . $restaurantAdd;
                        $DEFAULT_PARAM = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'vTableLayout',
                                'id' => $restaurantAdd
                            ),
                            'requireThumb' => TRUE,
                        );
                    }
                    $this->load->library('fileupload', $DEFAULT_PARAM);
                    $this->fileupload->setConfig($DEFAULT_PARAM);
                    if ((isset($_POST['removepic']) && $_POST['removepic'] == '1' ) || (isset($_POST['removepicM']) && $_POST['removepicM'] == '1' )) {
                        $this->fileupload->removeFile();

                        $images[$key] = '';
                        $this->restaurant_model->editRestaurantData($images, $restaurantAdd);
                    } else {
                        if (isset($value['error']) && $value['error'] == 0) {
                            $uploaded_files = $this->fileupload->upload($_FILES, $key);
                            if (!empty($uploaded_files)) {
                                foreach ($uploaded_files as $v) {
                                    $images[$key] = $v;
                                    $this->restaurant_model->editRestaurantData($images, $restaurantAdd);                                    
                                }
                            }
                        }
                    }
                }

                /*
                 * SEND MAIL FUNCTIONALITY...
                 */
                //$this->load->model('smtpmail_model');
                $this->load->library('maillib');
                $param = array(
                    '%MAILSUBJECT%' => 'Foodine : Welcome',
                    '%FONT1%' => DOMAIN_URL . '/images/fonts/HarabaraMais.otf',
                    '%FONT2%' => DOMAIN_URL . '/images/fonts/NexaLight.otf',
                    '%LOGO_IMAGE%' => DOMAIN_URL . '/images/hungermafia.png',
                    '%BACK_IMAGE%' => DOMAIN_URL . '/images/email/new_restaurant.jpg',
                    '%FT1%' => DOMAIN_URL . '/images/email/fb.png',
                    '%FT2%' => DOMAIN_URL . '/images/email/twitter.png',
                    '%FT3%' => DOMAIN_URL . '/images/email/linkedin.png',
                    '%FT4%' => DOMAIN_URL . '/images/email/youtube.png',
                    '%RESTAURANT_NAME%' => $this->input->post('vRestaurantName'),
                    '%CMS_LOGIN_LINK%' => BASEURL . 'login',
                    '%USERNAME%' => $this->input->post('vEmail'),
                    '%PASSWORD%' => $this->input->post('vPassword')
                );
                //$tmplt = DIR_ADMIN_VIEW . $this->controller . '/email/new_restaurant.php';
                $tmplt = DIR_ADMIN_VIEW . 'template/new_res.php';
                $subject = 'Foodine : Welcome';
                $to = $this->input->post('vEmail');
                //$this->smtpmail_model->send($to, $subject, $tmplt, $param);
                //$this->load->model("smtpmail_model", "smtpmail_model");
                //$this->smtpmail_model->send($to, $subject, $tmplt, $param, array(CC_EMAIL_ID));
                //$this->maillib->sendMail($to, $subject, $tmplt, $param, false, array(), array(CC_EMAIL_ID));

                $succ = array('0' => RESTAURANT_ADDED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => RESTAURANT_NOT_ADDED);

                $this->session->set_userdata('ERROR', $err);
            }
//            } else {
//                $err = array('0' => RESTAURANT_EXISTS);
//
//                $this->session->set_userdata('ERROR', $err);
//            }
            redirect('restaurant/index', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.restaurantedit') {
//            if ($this->restaurant_model->checkRestaurantEmailAvailable($this->input->post('vEmail'), $this->input->post('iRestaurantID'))) {
            $oldWebImg = $_POST['vRestImgW'];
            $oldMobileImg = $_POST['vRestImgM'];
            $oldListingImg = $_POST['vRestImgL'];
//            echo '<pre>';
//            print_r($_POST);
//            print_r($_FILES);
//            die;
            unset($_POST['vRestImgW'], $_POST['vRestImgM'], $_POST['vRestImgL']);

            $restaurantEdit = $this->restaurant_model->editRestaurant($_POST);

            $iRestaurantID = $this->input->post('iRestaurantID');
//mprd($_FILES);
            $restaurantdata = $this->restaurant_model->getRestaurantDataById($iRestaurantID);
            //print_r($_FILES); exit;
            foreach ($_FILES as $key => $value) {
                if ($key == 'vRestaurantLogo') {
                    $targetpath = UPLOADS . "restaurant/" . $iRestaurantID;
                   // echo $targetpath;

                    $DEFAULT_PARAM = array(
                        'fileType' => 'image',
                        'maxSize' => 20,
                        'uploadFor' => array(
                            'key' => 'restaurant',
                            'id' => $iRestaurantID
                        ),
                        'requireThumb' => TRUE,
                    );
                } else if ($key == 'vRestaurantMobLogo') {
                    $targetpath = UPLOADS . "restaurantMobile/" . $iRestaurantID;
                    $DEFAULT_PARAM = array(
                        'fileType' => 'image',
                        'maxSize' => 20,
                        'uploadFor' => array(
                            'key' => 'restaurantMobile',
                            'id' => $iRestaurantID
                        ),
                        'requireThumb' => TRUE,
                    );
                } else if ($key == 'vRestaurantListing') {
                    $targetpath = UPLOADS . "restaurantListing/" . $iRestaurantID;
                 //   echo $targetpath;
                    $DEFAULT_PARAM = array(
                        'fileType' => 'image',
                        'maxSize' => 20,
                        'uploadFor' => array(
                            'key' => 'restaurantListing',
                            'id' => $iRestaurantID
                        ),
                        'requireThumb' => TRUE,
                    );
                 } else if ($key == 'vTableLayout') {
                        $targetpath = UPLOADS . "vTableLayout/" . $iRestaurantID;
                        $DEFAULT_PARAM = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'vTableLayout',
                                'id' => $iRestaurantID
                            ),
                            'requireThumb' => TRUE,
                        );
                }
                $this->load->library('fileupload', $DEFAULT_PARAM);
                $this->fileupload->setConfig($DEFAULT_PARAM);

                if (isset($value['error']) && $value['error'] == 0) {
                    $uploaded_files = $this->fileupload->upload($_FILES, $key);
                    if (!empty($uploaded_files)) {
                        foreach ($uploaded_files as $v) {
                            $images[$key] = $v;
                            $this->restaurant_model->editRestaurantData($images, $iRestaurantID);                            
                        }
                    }

                    // REMOVE OLD AWS IMAGE
                   /* if (isset($_FILES['vRestaurantLogo']['name']) && !empty($_FILES['vRestaurantLogo']['name'])) {
                        if (!empty($oldWebImg)) {
                            $ImagePath = 'restaurant/' . $iRestaurantID . '/' . $oldWebImg;
                            $ImageThumbPath = 'restaurant/' . $iRestaurantID . '/thumb/' . $oldWebImg;
                            //$this->aws_sdk->deleteImage($ImagePath);
                            //$this->aws_sdk->deleteImage($ImageThumbPath);
                        }
                    } else if (isset($_FILES['vRestaurantMobLogo']['name']) && !empty($_FILES['vRestaurantMobLogo']['name'])) {
                        if (!empty($oldMobileImg)) {
                            $ImagePath = 'restaurantMobile/' . $iRestaurantID . '/' . $oldMobileImg;
                            $ImageThumbPath = 'restaurantMobile/' . $iRestaurantID . '/thumb/' . $oldMobileImg;
                            //$this->aws_sdk->deleteImage($ImagePath);
                            //$this->aws_sdk->deleteImage($ImageThumbPath);
                        }
                    } else if (isset($_FILES['vRestaurantListing']['name']) && !empty($_FILES['vRestaurantListing']['name'])) {
                        if (!empty($oldListingImg)) {
                            $ImagePath = 'restaurantListing/' . $iRestaurantID . '/' . $oldListingImg;
                            $ImageThumbPath = 'restaurantListing/' . $iRestaurantID . '/thumb/' . $oldListingImg;
                            //$this->aws_sdk->deleteImage($ImagePath);
                            //$this->aws_sdk->deleteImage($ImageThumbPath);
                        }
                    } */
                }
            }
               // $this->load->library('fileupload');
                //$this->fileupload->setConfig($DEFAULT_PARAM);
            // Remove Pic //
            if (isset($_POST['removepic']) && $_POST['removepic'] == '1') {
                if (!empty($oldWebImg)) {
                    //$this->fileupload->removeFile();
                    $ImagePath = 'restaurant/' . $iRestaurantID . '/' . $oldWebImg;
                    $ImageThumbPath = 'restaurant/' . $iRestaurantID . '/thumb/' . $oldWebImg;
                    //$this->aws_sdk->deleteImage($ImagePath);
                    //$this->aws_sdk->deleteImage($ImageThumbPath);
                }
                $images['vRestaurantLogo'] = '';
                $this->restaurant_model->editRestaurantData(array("vRestaurantLogo" => null), $iRestaurantID);
            }
            
            if (isset($_POST['removepicM']) && $_POST['removepicM'] == '1') {
                if (!empty($oldMobileImg)) {
                  //  $this->fileupload->removeFile();
                    $ImagePath = 'restaurantMobile/' . $iRestaurantID . '/' . $oldMobileImg;
                    $ImageThumbPath = 'restaurantMobile/' . $iRestaurantID . '/thumb/' . $oldMobileImg;
                    //$this->aws_sdk->deleteImage($ImagePath);
                    //$this->aws_sdk->deleteImage($ImageThumbPath);
                }
                $images['vRestaurantMobLogo'] = '';
                $this->restaurant_model->editRestaurantData($images, $iRestaurantID);
            }

            if (isset($_POST['removepicL']) && $_POST['removepicL'] == '1') {
                if (!empty($oldListingImg)) {
                   // $this->fileupload->removeFile();
                    $ImagePath = 'restaurantListing/' . $iRestaurantID . '/' . $oldListingImg;
                    $ImageThumbPath = 'restaurantListing/' . $iRestaurantID . '/thumb/' . $oldListingImg;
                  /*  //$this->aws_sdk->deleteImage($ImagePath);
                    //$this->aws_sdk->deleteImage($ImageThumbPath); */
                }
                $images['vRestaurantListing'] = '';
                $this->restaurant_model->editRestaurantData($images, $iRestaurantID);
            }

            if (@$this->input->post('vPassword') != "") {
                $this->load->library('maillib');
                $param = array(
                    '%MAILSUBJECT%' => 'Foodine: Updated Password',
                    '%FONT1%' => DOMAIN_URL . '/images/fonts/HarabaraMais.otf',
                    '%FONT2%' => DOMAIN_URL . '/images/fonts/NexaLight.otf',
                    '%LOGO_IMAGE%' => DOMAIN_URL . '/images/hungermafia.png',
                    '%BACK_IMAGE%' => DOMAIN_URL . '/images/email/new_restaurant.jpg',
                    '%FT1%' => DOMAIN_URL . '/images/email/fb.png',
                    '%FT2%' => DOMAIN_URL . '/images/email/twitter.png',
                    '%FT3%' => DOMAIN_URL . '/images/email/linkedin.png',
                    '%FT4%' => DOMAIN_URL . '/images/email/youtube.png',
                    '%RESTAURANT_NAME%' => $this->input->post('vRestaurantName'),
                    '%CMS_LOGIN_LINK%' => BASEURL . 'login',
                    '%USERNAME%' => $this->input->post('vEmail'),
                    '%PASSWORD%' => $this->input->post('vPassword')
                );
                //$tmplt = DIR_ADMIN_VIEW . $this->controller . '/email/new_restaurant.php';
                $tmplt = DIR_ADMIN_VIEW . 'template/new_res.php';
                $subject = 'Foodine: Updated Password';
                $to = $this->input->post('vEmail');
                //$this->smtpmail_model->send($to, $subject, $tmplt, $param);
                //$this->load->model("smtpmail_model", "smtpmail_model");
               // $this->smtpmail_model->send($to, $subject, $tmplt, $param, array(CC_EMAIL_ID));
                //$this->maillib->sendMail($to, $subject, $tmplt, $param, false, array(), array(CC_EMAIL_ID));
            }

            if ($restaurantEdit != '') {
                $succ = array('0' => RESTAURANT_EDITED);

                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => RESTAURANT_EDITED);

                $this->session->set_userdata('SUCCESS', $err);
            }
//            } else {
//                $err = array('0' => RESTAURANT_EXISTS);
//
//                $this->session->set_userdata('ERROR', $err);
//            }
            //exit;
        //   echo 'Get the way to main';
       redirect('restaurant/index', 'refresh');
        }
        $this->load->view('restaurant/restaurant_add_view', $viewData);
    }

//    function fileupload() {
//        if (isset($_FILES) && !empty($_FILES)) {
//
//            $param = array(
//                'fileType' => 'image',
//                '$parammaxSize' => 20,
//                'uploadFor' => array(
//                    'key' => 'restaurant',
//                    'id' => 99
//                ),
//                'requireThumb' => FALSE
//            );
//            $this->load->library('fileupload', $param);
//            $this->fileupload->upload($_FILES, 'image_file');
//        }
//
//        $this->load->view('restaurant/test/file_upload.php');
//    }
    // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iRestaurantID = '', $rm = '') {
        if ($iRestaurantID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->restaurant_model->changeRestaurantStatus($iRestaurantID);

            if ($changeStatus != '') {
                $this->restaurant_model->updateSolrFlag($iRestaurantID);
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("restaurant", 'refresh');
    }

    // ***************************************************************
    // Order history
    // ***************************************************************

    function get_ordered_restaurant($iOutletID) {
        $viewData = array(
            "title" => $this->title,
            'page' => 'post',
            'outlet_id' => $iOutletID
        );
        $this->load->view("restaurant/restaurant_view", $viewData);
    }

    // ***************************************************************
    // REMOVE
    // ***************************************************************
    function remove($iRestaurantID = '', $rm = '') {
        if ($iRestaurantID != '' && $rm != '' && $rm == 'y') {
            $removeRestaurant = $this->restaurant_model->removeRestaurant($iRestaurantID);
            if ($removeRestaurant != '') {
                $succ = array('0' => RESTAURANT_DELETED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => RESTAURANT_NOT_DELETED);
                $this->session->set_userdata('ERROR', $err);
            }
        }
        redirect("restaurant/index", 'refresh');
    }

    // ***************************************************************
    function deleteAll() {
        $data = $_POST['rows'];

        $removeRestaurant = $this->restaurant_model->removeRestaurant($_POST['rows']);
        if ($removeRestaurant != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("restaurant", "refresh");
    }

    function deleteFeatured() {
        $data = $_POST['rows'];

        $remove = $this->restaurant_model->removeFeatured($_POST['rows']);
        if ($remove != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("restaurant", "refresh");
    }

    // ***************************************************************
    function deleteFollowerAll() {
        $data = $_POST['rows'];

        $removeRestaurant = $this->restaurant_model->deleteFollowerAll($_POST['rows']);
        if ($removeRestaurant != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("restaurant", "refresh");
    }

    // ***************************************************************
    function deleteFollowingAll() {
        $data = $_POST['rows'];

        $removeRestaurant = $this->restaurant_model->deleteFollowingAll($_POST['rows']);
        if ($removeRestaurant != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("restaurant", "refresh");
    }

    function paginate() {
        $data = $this->restaurant_model->get_paginationresult();
        echo json_encode($data);
    }

    public function make_thumb($mypath, $targetpath, $myratio) {
        $list = list($width, $height) = getimagesize($mypath);
        $ratio = $myratio / min($width, $height);

        $w = $width * $ratio;
        $h = $height * $ratio;

        $config_manip = array(
            'image_library' => 'gd2',
            'source_image' => $mypath,
            'new_image' => $targetpath,
            'maintain_ratio' => TRUE,
            'create_thumb' => FALSE,
            'width' => $w,
            'height' => $h
        );

        $this->load->library('image_lib', $config_manip);
        $this->image_lib->clear();
        $this->image_lib->initialize($config_manip);

        if (!$this->image_lib->resize()) {
            echo $this->image_lib->display_errors();
        }
    }

    // INDEX
    function checkin($iRestaurantID = '') {

        if ($iRestaurantID == '') {
            redirect('restaurant/index', 'refresh');
        }
        $getRecords = $this->restaurant_model->getRestaurantCheckInDataAll($iRestaurantID);
        $viewData = array("title" => "Restaurant Management");
        $viewData['breadCrumbArr'] = array("restaurant" => "Restaurant Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $viewData['iRestaurantID'] = $iRestaurantID;

        $this->load->view('restaurant/restaurant_checkin_view', $viewData);
    }

    function paginate_checkin($iRestaurantID) {
        $data = $this->restaurant_model->getRestaurantCheckInDataAll($iRestaurantID);
        echo json_encode($data);
    }

    // INDEX
    function ratting($iRestaurantID = '') {

        if ($iRestaurantID == '') {
            redirect('restaurant/index', 'refresh');
        }
        $getRecords = $this->restaurant_model->getRestaurantRattingDataAll($iRestaurantID);
        $viewData = array("title" => "Restaurant Management");
        $viewData['breadCrumbArr'] = array("restaurant" => "Restaurant Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $viewData['iRestaurantID'] = $iRestaurantID;

        $this->load->view('restaurant/restaurant_ratting_view', $viewData);
    }

    function paginate_ratting($iRestaurantID) {
        $data = $this->restaurant_model->getRestaurantRattingDataAll($iRestaurantID);
        echo json_encode($data);
    }

    /*
     * TO LOAD RESTAURANT VIEW HTML
     */

    function viewDetail($recordId = '') {
        if ($recordId !== '') {
            $data = $this->restaurant_model->getRestaurantDataById($recordId);

            $this->load->view('restaurant/viewDetail', array('viewData' => $data));
        } return '';
    }

    function featured($action = '', $target = '') {

        redirect('dashboard', 'refresh');

        if ($action == '' && $target == '') {
            $viewData = array("title" => "Featured Restaurant");
            $viewData['breadCrumbArr'] = array("restaurant" => "Featured Restaurant");

            $this->load->view('restaurant/featured_view', $viewData);


            /*
             */
        } else if (in_array($action, array('add', 'edit'))) {
            $viewData = array("title" => "Featured Restaurant");
            $viewData['breadCrumbArr'] = array("restaurant" => "Featured Restaurant");
            $viewData['ACTION_LABEL'] = $target == 'add' ? 'Add' : 'Edit';

            $viewData['listCat'] = $this->restaurant_model->listCategory();
            $viewData['listRest'] = $this->restaurant_model->listRest();
            if ($target !== '') {
                $record = $this->restaurant_model->get_featuredPaginationResult($target);
                $viewData['record'] = $record['aaData'][0];
            }

            $this->load->view('restaurant/featured_add_view', $viewData);
            /*
             */
        } else if ($action == 'submit') {
            if ($_POST) {
                extract($_POST);

                //mprd($_POST);

                if ($action == 'backoffice.featuredadd') {
                    /*
                     * to ghet the record to check that the availabel record should be less than three 
                     * because, fot each of the category we have to add maximum three restaurants
                     */
                    $_3Res = $this->db->get_where('tbl_featured_restaurant', array('iCategoryID' => $iCategoryID));
                    if ($_3Res->num_rows() < 3) {

                        $hasRec = $this->db->get_where('tbl_featured_restaurant', array('iCategoryID' => $iCategoryID, 'iRestaurantID' => $iRestaurantID));

                        if ($hasRec->num_rows() > 0) {
                            $err = array('0' => 'You have already added this restaurant for this category.');
                            $this->session->set_userdata('ERROR', $err);
                        } else {
                            $insdata = array(
                                'iCategoryID' => $iCategoryID,
                                'iRestaurantID' => $iRestaurantID,
                                'tCreatedAt' => date('Y-m-d H:i:s')
                            );
                            $this->db->insert('tbl_featured_restaurant', $insdata);
                            $msg = array('0' => 'Your record has been inserted successfully.');
                            $this->session->set_userdata('SUCCESS', $msg);
                        }
                    } else {
                        $err = array('0' => 'You can add maximum three restaurant for this category.');
                        $this->session->set_userdata('ERROR', $err);
                    }
                } else if ($action == 'backoffice.featurededit') {
                    $hasRec = $this->db->get_where('tbl_featured_restaurant', array('iCategoryID' => $iCategoryID, 'iRestaurantID' => $iRestaurantID));
                    if ($hasRec->num_rows() > 0) {
                        $err = array('0' => 'You have already added this restaurant for this category.');
                        $this->session->set_userdata('ERROR', $err);
                    } else {
                        $updtdata = array(
                            'iCategoryID' => $iCategoryID,
                            'iRestaurantID' => $iRestaurantID
                        );
                        $this->db->update('tbl_featured_restaurant', $updtdata, array('iFeaturedID' => $iFeaturedID));

                        $msg = array('0' => 'Your record has been updated successfully.');
                        $this->session->set_userdata('SUCCESS', $msg);
                    }
                }
            }

            redirect('restaurant/featured', 'refresh');
        }
    }

    function ajaxRest() {
        $html = '';
        $status = 200;
        if ($_POST) {
            extract($_POST);

            $qry = 'SELECT tr.vRestaurantName AS vRestaurantName, tr.iRestaurantID AS iRestaurantID FROM tbl_restaurant AS tr, tbl_restaurant_category AS trc WHERE trc.iCategoryID IN(' . $val . ') AND trc.iRestaurantID IN(tr.iRestaurantID) ';
            $res = $this->db->query($qry);
            if ($res->num_rows() > 0) {
                $rec = $res->result_array();
                for ($i = 0; $i < count($rec); $i++) {
                    $html .= '<option value="' . $rec[$i]['iRestaurantID'] . '">' . $rec[$i]['vRestaurantName'] . '</option>';
                }
            }
        } else {
            $status = 100;
        }

        echo json_encode(array('status' => $status, 'html' => $html));
    }

    function report($rest_id) {
        if ($rest_id != '') {
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header("Content-Disposition: attachment;filename=restaurant-report-" . time() . ".xls");
            header("Content-Transfer-Encoding: binary ");

            $viewData['restData'] = $this->restaurant_model->getRestaurantDataById($rest_id);
            $viewData['catData'] = $this->restaurant_model->getRestaurantCategory($rest_id);
            $viewData['cuisineData'] = $this->restaurant_model->getRestaurantCuisine($rest_id);
            $viewData['musicData'] = $this->restaurant_model->getRestaurantMusic($rest_id);
            $viewData['facilityData'] = $this->restaurant_model->getRestaurantFacility($rest_id);
            $viewData['otherCountData'] = $this->restaurant_model->getRestaurantOtherCount($rest_id);


            $viewData['bookingData'] = $this->restaurant_model->getRestaurantBooking($rest_id);

            $this->load->view('restaurant/report_view', $viewData);
        }
    }

    function review($iRestaurantID = '') {
//        if ($rest_id == '') {
//            redirect('restaurant', 'refresh');
//        }
//        $getRecords = $this->restaurant_model->getRestaurantReviewDataAll($rest_id);
//        $viewData = array("title" => "Restaurant Review");
//        $viewData['breadCrumbArr'] = array("restaurant" => "Restaurant Review");
//        if ($getRecords != '')
//            $viewData['record_set'] = $getRecords;
//        else
//            $viewData['record_set'] = '';
//
//        $viewData['iRestaurantID'] = $rest_id;
//
//        $this->load->view('restaurant/restaurant_review_view', $viewData);

        if ($iRestaurantID == '') {
            redirect('restaurant/index', 'refresh');
        }
        $getRecords = $this->restaurant_model->getRestaurantRattingDataAll($iRestaurantID);
        $viewData = array("title" => "Restaurant Management");
        $viewData['breadCrumbArr'] = array("restaurant" => "Restaurant Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $viewData['iRestaurantID'] = $iRestaurantID;

        $this->load->view('restaurant/restaurant_ratting_view', $viewData);
    }

    function paginate_review($rest_id = '') {
        $getRecords = $this->restaurant_model->getRestaurantReviewDataAll($rest_id);
        echo json_encode($getRecords);
    }

    function featuredPaginate() {
        $data = $this->restaurant_model->get_featuredPaginationResult();
        echo json_encode($data);
    }

    function resendMail() {
        extract($_POST);
        $rec = $this->db->get_where('tbl_restaurant', array('iRestaurantID' => $target_id))->row_array();

        $passwordNew = random_string();
        $this->db->update('tbl_admin', array('vPassword' => md5($passwordNew)), array('vEmail' => $rec['vEmail']));

        $this->load->library('maillib');
        $param = array(
            '%MAILSUBJECT%' => 'Foodine : Welcome',
            '%FONT1%' => DOMAIN_URL . '/images/fonts/HarabaraMais.otf',
            '%FONT2%' => DOMAIN_URL . '/images/fonts/NexaLight.otf',
            '%LOGO_IMAGE%' => DOMAIN_URL . '/images/hungermafia.png',
            '%BACK_IMAGE%' => DOMAIN_URL . '/images/email/new_restaurant.jpg',
            '%FT1%' => DOMAIN_URL . '/images/email/fb.png',
            '%FT2%' => DOMAIN_URL . '/images/email/twitter.png',
            '%FT3%' => DOMAIN_URL . '/images/email/linkedin.png',
            '%FT4%' => DOMAIN_URL . '/images/email/youtube.png',
            '%RESTAURANT_NAME%' => $rec['vRestaurantName'],
            '%CMS_LOGIN_LINK%' => BASEURL . 'login',
            '%USERNAME%' => $rec['vEmail'],
            '%PASSWORD%' => $passwordNew
        );
        //$tmplt = DIR_ADMIN_VIEW . $this->controller . '/email/new_restaurant.php';
        $tmplt = DIR_ADMIN_VIEW . 'template/new_res.php';
        $subject = 'Foodine : Welcome';
        $to = $rec['vEmail'];
        //$this->smtpmail_model->send($to, $subject, $tmplt, $param);
        $this->load->model("smtpmail_model", "smtpmail_model");
        $this->smtpmail_model->send($to, $subject, $tmplt, $param, array(CC_EMAIL_ID));
        //$this->maillib->sendMail($to, $subject, $tmplt, $param, false, array(), array(CC_EMAIL_ID));

        echo json_encode(array('STATUS' => 200, 'MSG' => 'Mail has been sent'));
    }

    function featuredRest() {
        $viewData['title'] = "Featured Restaurant Management";
        $viewData['restaurantFeatured'] = $this->restaurant_model->getFeaturedRestaurant();
        $this->load->view('restaurant/featuredRest_view', $viewData);
    }

    function saveFeaturedRestaurants() {
        $this->restaurant_model->saveFeatured($_POST["restaurants"]);
        redirect('restaurant/featuredRest', 'refresh');
    }

    function saveFeatured() {
        extract($_POST);
        $this->restaurant_model->saveFeaturedRestaurant($id, $featured);
    }

    function handpickRest() {
        $viewData['title'] = "HandPick Restaurant Management";
        $viewData['restaurantPick'] = $this->restaurant_model->getPickRestaurant();
        $this->load->view('restaurant/pickRest_view', $viewData);
    }

    function saveHandpicksRestaurants() {
        $this->restaurant_model->saveHandpicks($_POST["restaurants"]);
        redirect('restaurant/handpickRest', 'refresh');
    }


    function banquetRest() {
        $viewData['title'] = "Banquet Restaurant Management";
        $viewData['restaurantPick'] = $this->restaurant_model->getBanquetRestaurant();
        $this->load->view('restaurant/banquetRest_view', $viewData);
    }

    function saveBanquetRestaurants() {
        $this->restaurant_model->saveBanquet($_POST["restaurants"]);
        redirect('restaurant/banquetRest', 'refresh');
    }
    
    function fastfillingRest() {
        $viewData['title'] = "HandPick Restaurant Management";
        $viewData['restaurantPick'] = $this->restaurant_model->getfastfillingRestaurant();
        $this->load->view('restaurant/fastfilling_view', $viewData);
    }

    function saveFastfillingRestaurants() {
        $this->restaurant_model->savefastfilling($_POST["restaurants"]);
        redirect('restaurant/fastfillingRest', 'refresh');
    }
    
    
    function popularLocationRest() {
        $viewData['title'] = "HandPick Restaurant Management";
        $viewData['restaurantPick'] = $this->restaurant_model->getPopularLocationRestaurant();
        $this->load->view('restaurant/popularLocation_view', $viewData);
    }

    function savePopularLocationRestaurants() {
        $this->restaurant_model->savePopularLocation($_POST["restaurants"]);
        redirect('restaurant/popularLocationRest', 'refresh');
    }
    
    

    function savePick() {
        extract($_POST);
        $this->restaurant_model->savePickRestaurant($id, $featured);
    }

    public function reportedByUser() {
        $viewData = array("title" => "Restaurant Reported By User");
        $this->load->view('restaurant/restaurant_reported_by_user', $viewData);
    }

    function paginate_restaurantReportedByUser() {
        $data = $this->restaurant_model->paginateReportedByUser();
        echo json_encode($data);
    }

    function reportedByUserStatus($iReportErrorId) {
        if ($iReportErrorId != '') {
            $changeStatus = $this->restaurant_model->changeReportedByUserStatus($iReportErrorId);
            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

    public function closureReportedByUser() {
        $viewData = array("title" => "Restaurant Closure Reported By User");
        $this->load->view('restaurant/closure_reported_by_user', $viewData);
    }

    function paginate_closureReportedByUser() {
        $data = $this->restaurant_model->paginateClosureReported();
        echo json_encode($data);
    }

    function closureReportedByUserStatus($iReportClosureId) {
        if ($iReportClosureId != '') {
            $changeStatus = $this->restaurant_model->changeClosureReportedStatus($iReportClosureId);
            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

    public function infoChangeReportedByUser() {
        $viewData = array("title" => "Information Change Reported By User");
        $this->load->view('restaurant/infochange_reported_by_user', $viewData);
    }

    function paginate_infoChangeReportedByUser() {
        $data = $this->restaurant_model->paginateInfoChangeReported();
        echo json_encode($data);
    }

    function infoChangeReportedByUserStatus($iEditReqID) {
        if ($iEditReqID != '') {
            $changeStatus = $this->restaurant_model->changeInfoChangeReportedStatus($iEditReqID);
            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
    }
    
    function getCity() {
        $stateId = $_POST['stateId'];
        $citiesArray = $this->restaurant_model->getCities($stateId);
        echo json_encode($citiesArray);
        exit;
    }
    
     function getLocation() {
        $cityId = $_POST['cityId'];
        $locArray = $this->restaurant_model->getLocation($cityId);
        echo json_encode($locArray);
        exit;
    }

    /*     * ******************* End of the File ***************************** */
}
