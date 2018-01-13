<?php

class Image extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('image_model');
        $this->load->helper('string');
        $this->controller = 'image';
        $this->uppercase = 'Image';
        $this->title = 'Restaurant Images Management';
        $this->load->library('aws_sdk');
    }

    /*
     * to load the view part
     */

    function restaurant_image($iResturantID = '') {
        if ($iResturantID != '') {
            /*
             * to get the restaurant image information
             */
            $this->load->model('restaurant_model');
            $getRecords = $this->restaurant_model->getRestaurantDataById($iResturantID);

            $viewData = array("title" => "Restaurant Photos");
            $viewData['breadCrumbArr'] = array("restaurant" => "Restaurant Photos");
            if ($getRecords != '') {
                $viewData['record_set'] = $getRecords;
            } else {
                $viewData['record_set'] = '';
            }

            $viewData['iRestaurantID'] = $iResturantID;

            $this->load->view('image/restaurant_photo_view', $viewData);
        } else {
            redirect('restaurant', 'refresh');
        }
    }

    /*
     * to load the view part
     */

    function restaurant_menu($iResturantID = '') {
        if ($iResturantID != '') {
            /*
             * to get the restaurant image information
             */
            $this->load->model('restaurant_model');
            $getRecords = $this->restaurant_model->getRestaurantDataById($iResturantID);

            $viewData = array("title" => "Restaurant Menu Image");
            $viewData['breadCrumbArr'] = array("restaurant" => "Restaurant Menu Image");
            if ($getRecords != '') {
                $viewData['record_set'] = $getRecords;
            } else {
                $viewData['record_set'] = '';
            }

            $viewData['iRestaurantID'] = $iResturantID;

            $this->load->view('image/restaurant_menu_view', $viewData);
        } else {
            redirect('restaurant', 'refresh');
        }
    }

    /*
     * to add new restaurant image 
     */

    function restaurant_image_add($iResturantID = '', $iPictureID = '', $ed = '') {
        if ($iResturantID != '') {
            $viewData['title'] = "Add / Edit Restaurant Photo";
            $viewData['iRestaurantID'] = $iResturantID;
            $viewData['iPictureID'] = $iPictureID;

            $viewData['ACTION_LABEL'] = (isset($iPictureID) && $iPictureID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

            if ($iPictureID != '' && $ed != '' && $ed == 'y') {
                $getData = $this->image_model->restaurantPhotoById($iPictureID);
                //$getCategory = $this->image_model->getCategoryDataById($iPictureID);
                //mprd($getData);
                $viewData['getPictureData'] = $getData;
                //$viewData['getCategoryData'] = $getCategory;
            }


            /*
             * to add new restaurant photo...
             */

            if ($this->input->post('action') && $this->input->post('action') == 'backoffice.imageadd') {

                if ($this->input->post('iRestaurantID') !== '') {

                    if (isset($_FILES) && !empty($_FILES)) {

                        //mprd($_FILES);
                        /* $param = array(
                          'fileType' => 'image',
                          'maxSize' => 20,
                          'uploadFor' => array(
                          'key' => 'restaurantPhoto',
                          'id' => $iResturantID
                          ),
                          'requireThumb' => TRUE
                          );
                          $this->load->library('fileupload', $param);
                          $uploadedFiles = $this->fileupload->upload($_FILES, 'res_image');
                         */
                        $DEFAULT_PARAM = array(
                            'fType' => 'image', // file format
                            'fLimit' => 20, // maximum upload file limit 
                            'fLoc' => array(
                                'key' => 'restaurantPhoto', // user     - user
                                'id' => $iResturantID // user-id  - 1
                            ),
                            'fThumb' => TRUE, // only for image format
                            'fCopyText' => FALSE // only for image format
                        );
                        $this->load->library('fupload', $DEFAULT_PARAM);
                        $uploadedFiles = $this->fupload->fUpload($_FILES, 'res_image');
//print_r($uploadedFiles); exit;
                        $submitArry = array(
                            'iRestaurantID' => $iResturantID,
                            'images' => $uploadedFiles
                        );
                        $this->image_model->add_restaurant_photo($submitArry);
                    }

                    $succ = array('0' => IMAGE_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);

                    redirect('image/restaurant_image/' . $iResturantID, 'refresh');
                } else {
                    $err = array('0' => 'Restaurant Value not defined..!!');

                    $this->session->set_userdata('ERROR', $err);
                }
            } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.imageedit') {

                if ($this->input->post('iRestaurantID') !== '') {

                    if (isset($_FILES) && !empty($_FILES)) {

                        //mprd($_FILES);
                        /* $param = array(
                          'fileType' => 'image',
                          'maxSize' => 20,
                          'uploadFor' => array(
                          'key' => 'restaurantPhoto',
                          'id' => $iResturantID
                          ),
                          'requireThumb' => TRUE
                          );


                          $this->load->library('fileupload', $param);
                          $uploadedFiles = $this->fileupload->upload($_FILES, 'res_image');
                         */
                        $DEFAULT_PARAM = array(
                            'fType' => 'image', // file format
                            'fLimit' => 20, // maximum upload file limit 
                            'fLoc' => array(
                                'key' => 'restaurantPhoto', // user     - user
                                'id' => $iResturantID // user-id  - 1
                            ),
                            'fThumb' => TRUE, // only for image format
                            'fCopyText' => FALSE // only for image format
                        );
                        $this->load->library('fupload', $DEFAULT_PARAM);
                        $uploadedFiles = $this->fupload->fUpload($_FILES, 'res_image');


                        $submitArry = array(
                            'iPictureID' => $this->input->post('iPictureID'),
                            'iRestaurantID' => $iResturantID,
                            'images' => $uploadedFiles
                        );
                        $this->image_model->edit_restaurant_photo($submitArry);
                    }
                    // exit;

                    $succ = array('0' => IMAGE_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);

                    redirect('image/restaurant_image/' . $iResturantID, 'refresh');
                } else {
                    $err = array('0' => 'Restaurant Value not defined..!!');

                    $this->session->set_userdata('ERROR', $err);
                }
            }

            $this->load->view('image/photo_add_view', $viewData);
        } else {
            redirect('restaurant', 'refresh');
        }
    }

    /*
     * to add new restaurant image 
     */

    function restaurant_menu_add($iResturantID = '', $iPictureID = '', $ed = '') {
        if ($iResturantID != '') {
            $viewData['title'] = "Add / Edit Restaurant Menu Photo";
            $viewData['iRestaurantID'] = $iResturantID;
            $viewData['iMenuPictureID'] = $iPictureID;

            $viewData['ACTION_LABEL'] = (isset($iPictureID) && $iPictureID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

            if ($iPictureID != '' && $ed != '' && $ed == 'y') {
                $getData = $this->image_model->restaurantMenuById($iPictureID);
                //$getCategory = $this->image_model->getCategoryDataById($iPictureID);
                //mprd($getData);
                $viewData['getPictureData'] = $getData;
                //$viewData['getCategoryData'] = $getCategory;
            }


            /*
             * to add new restaurant photo...
             */

            if ($this->input->post('action') && $this->input->post('action') == 'backoffice.imageadd') {

                if ($this->input->post('iRestaurantID') !== '') {

                    $uploadedFiles = array();
                    if (isset($_FILES) && !empty($_FILES)) {

                        $DEFAULT_PARAM = array(
                            'fType' => 'image', // file format
                            'fLimit' => 20, // maximum upload file limit 
                            'fLoc' => array(
                                'key' => 'restaurantMenu', // user     - user
                                'id' => $iResturantID // user-id  - 1
                            ),
                            'fThumb' => TRUE, // only for image format
                            'fCopyText' => FALSE // only for image format
                        );
                        $this->load->library('fupload', $DEFAULT_PARAM);
                        $uploadedFiles = $this->fupload->fUpload($_FILES, 'res_image');

                        //mprd($_FILES);
                        /* $param = array(
                          'fileType' => 'image',
                          'maxSize' => 20,
                          'uploadFor' => array(
                          'key' => 'restaurantMenu',
                          'id' => $iResturantID
                          ),
                          'requireThumb' => TRUE
                          );
                          $this->load->library('fileupload', $param);
                          $uploadedFiles = $this->fileupload->upload($_FILES, 'res_image'); */
                    }

              //      mprd($uploadedFiles);

                    $submitArry = array(
                        'menu_type' => $this->input->post('menu_type'),
                        'iRestaurantID' => $iResturantID,
                        'images' => $uploadedFiles
                    );
                    $this->image_model->add_restaurant_menu($submitArry);

                    $succ = array('0' => IMAGE_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);


                    redirect('image/restaurant_menu/' . $iResturantID, 'refresh');
                } else {
                    $err = array('0' => 'Restaurant Value not defined..!!');

                    $this->session->set_userdata('ERROR', $err);
                }
            } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.imageedit') {

                if ($this->input->post('iRestaurantID') !== '') {
                    $uploadedFiles = array();

                    //echo 'in';
                    if (isset($_FILES['res_image']) && !empty($_FILES['res_image'])) {

                        $DEFAULT_PARAM = array(
                            'fType' => 'image', // file format
                            'fLimit' => 20, // maximum upload file limit 
                            'fLoc' => array(
                                'key' => 'restaurantMenu', // user     - user
                                'id' => $iResturantID // user-id  - 1
                            ),
                            'fThumb' => TRUE, // only for image format
                            'fCopyText' => FALSE // only for image format
                        );
                        $this->load->library('fupload', $DEFAULT_PARAM);
                        $uploadedFiles = $this->fupload->fUpload($_FILES, 'res_image');

//                        //mprd($_FILES);
//                        $param = array(
//                            'fileType' => 'image',
//                            'maxSize' => 20,
//                            'uploadFor' => array(
//                                'key' => 'restaurantMenu',
//                                'id' => $iResturantID
//                            ),
//                            'requireThumb' => TRUE
//                        );
//
//
//                        $this->load->library('fileupload', $param);
//                        $uploadedFiles = $this->fileupload->upload($_FILES, 'res_image');
                    }
                    //mprd($this->input->post());
                    $submitArry = array(
                        'iMenuPictureID' => $this->input->post('iMenuPictureID'),
                        'menu_type' => $this->input->post('menu_type'),
                        'iRestaurantID' => $iResturantID,
                        'images' => $uploadedFiles
                    );
                    $deleteMenuPhoto = empty($uploadedFiles) ? FALSE : TRUE;
                    $this->image_model->edit_restaurant_menu($submitArry, $deleteMenuPhoto);

                    //exit;
                    $succ = array('0' => IMAGE_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);

                    redirect('image/restaurant_menu/' . $iResturantID, 'refresh');
                } else {
                    $err = array('0' => 'Restaurant Value not defined..!!');

                    $this->session->set_userdata('ERROR', $err);
                }
            }

            $this->load->view('image/menu_add_view', $viewData);
        } else {
            redirect('restaurant', 'refresh');
        }
    }

    /*
     * to delete the restaurant image...
     */

    function deleteRestaurantImage() {
        $data = $_POST['rows'];

        $removeImage = $this->image_model->removeRestarantPhoto($_POST['rows']);
        if ($removeImage != '') {
            echo '1';
        } else {
            echo '0';
        }
    }

    function deleteRestaurantMenu() {
        $data = $_POST['rows'];

        $removeImage = $this->image_model->removeRestarantMenu($_POST['rows']);
        if ($removeImage != '') {
            echo '1';
        } else {
            echo '0';
        }
    }

    function restaurant_photo_paginate($iRestaurantID) {
        $data = $this->image_model->restaurant_photo_paginate($iRestaurantID);
        echo json_encode($data);
    }

    function restaurant_menu_paginate($iRestaurantID) {
        $data = $this->image_model->restaurant_menu_paginate($iRestaurantID);
        echo json_encode($data);
    }

    // INDEX
    function index($iRestaurantID = '') {

        if ($iRestaurantID == '')
            redirect('restaurant', 'refresh');

        $getRecords = $this->image_model->getImageDataAllbyRes($iRestaurantID);
        $getRes = $this->image_model->getRestaurantDataById($iRestaurantID);

        if ($getRes == '')
            redirect('restaurant', 'refresh');

        $viewData = array("title" => "Restaurant Images Management");
        $viewData['breadCrumbArr'] = array("image" => "Restaurant Images Management");
        if ($getRecords != '') {
            $viewData['record_set'] = $getRecords;
            $viewData['record_set_restaurant'] = $getRes;
        } else
            $viewData['record_set'] = '';

        $viewData['iRestaurantID'] = $iRestaurantID;

        $this->load->view('image/image_view', $viewData);
    }

    // ***************************************************************
    // ADD
    // ***************************************************************
    function add($iPictureID = '', $ed = '') {
        $viewData['title'] = "Restaurant Images Management";

        $viewData['ACTION_LABEL'] = (isset($iPictureID) && $iPictureID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iPictureID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->image_model->getImageDataById($iPictureID);
            //$getCategory = $this->image_model->getCategoryDataById($iPictureID);

            $viewData['getImageData'] = $getData;
            $viewData['getCategoryData'] = $getCategory;
        }

        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.imageadd') {

            //mprd($this->input->post());
            if ($this->input->post('iRestaurantID') != '') {
                if (isset($_FILES) && !empty($_FILES)) {

                    $param = array(
                        'fileType' => 'image',
                        '$parammaxSize' => 20,
                        'uploadFor' => array(
                            'key' => 'restaurantPhoto',
                            'id' => $this->input->post('iRestaurantID')
                        ),
                        'fThumb' => TRUE, // only for image format
                        'fCopyText' => FALSE // only for image format
                    );
                    $this->load->library('fupload', $DEFAULT_PARAM);
                    $uploadedFiles = $this->fupload->fUpload($_FILES, 'res_image');
                }

                $viewData['iResturantID'] = $this->input->post('iRestaurantID');

                $succ = array('0' => IMAGE_ADDED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => IMAGE_NOT_ADDED);

                $this->session->set_userdata('ERROR', $err);
            }
            exit;
            redirect('image', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.imageedit') {
            if ($this->image_model->checkImageEmailAvailable($this->input->post('vEmail'), $this->input->post('iPictureID'))) {


                $imageEdit = $this->image_model->editImage($_POST);

                $iPictureID = $this->input->post('iPictureID');

                $imagedata = $this->image_model->getImageDataById($iPictureID);

                $targetpath = UPLOADS . "image/" . $iPictureID;

                if (!is_dir($targetpath)) {
                    if (!mkdir($targetpath, 0777, true)) {
                        
                    }
                }
                if (!is_dir($targetpath . "/thumb")) {
                    if (!mkdir($targetpath . "/thumb", 0777, true)) {
                        
                    }
                }

                if (isset($_FILES['vImageLogo']) && $_FILES['vImageLogo']['name'] != '') {

                    if ($imagedata != '') {
                        if ($imagedata['vImageLogo'] != "") {
                            $this->load->helper("file");
                            delete_files($targetpath . "/");
                        }
                    }

                    $config['upload_path'] = $targetpath;
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = 1024 * 20;
                    $config['encrypt_name'] = TRUE;

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('vImageLogo')) {

                        extract($this->upload->data('vImageLogo'));
                        $images['vImageLogo'] = $file_name;

                        if (!empty($images)) {
                            $keyImage = array_reverse(explode('images/', $file_name));
                            $aws_object = $this->aws_sdk->saveObject(array(
                                'Key' => 'user/' . $userAdd . '/' . $keyImage[0],
                                'ContentType' => $file_type,
                                'ACL' => 'public-read',
                                'SourceFile' => $full_path,
                            ));
                            $imageedit = $this->image_model->editImageData($images, $iPictureID);
                        }
                        $mypath = $targetpath . '/' . $images['vImageLogo'];
                        $targetpath_thumb = $targetpath . "/thumb/" . $images['vImageLogo'];
                        $this->make_thumb($mypath, $targetpath_thumb, 200);
                        chmod($mypath,'0777');chmod($targetpath_thumb,'0777');
                        @unlink($mypath);@unlink($targetpath_thumb);
                    } else {
                        //print_r($this->upload->display_errors());
                        //die();
                        //echo 'error';
                    }
                } else {

                    $removepic = $this->input->post('removepic');
                    if ($removepic == '1') {
                        if ($imagedata != '') {
                            if ($imagedata['vImageLogo'] != "") {
                                $this->load->helper("file");
                                delete_files($targetpath . "/");
                                $images['vImageLogo'] = '';
                                if (!empty($images))
                                    $imageedit = $this->image_model->editImageData($images, $iPictureID);
                            }
                        }
                    }
                }


                if ($imageEdit != '') {
                    $succ = array('0' => IMAGE_EDITED);

                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => IMAGE_EDITED);

                    $this->session->set_userdata('SUCCESS', $err);
                }
            } else {
                $err = array('0' => IMAGE_EXISTS);

                $this->session->set_userdata('ERROR', $err);
            }

            redirect('image', 'refresh');
        }
        /* echo '<pre>';
          print_r($viewData); */
        $this->load->view('image/photo_add_view', $viewData);
    }

    // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iPictureID = '', $rm = '') {
        if ($iPictureID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->image_model->changeImageStatus($iPictureID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("image", 'refresh');
    }

    function menu_status($iPictureID = '', $rm = '') {
        if ($iPictureID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->image_model->changeImageStatusMenu($iPictureID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("image", 'refresh');
    }

    // ***************************************************************
    // Order history
    // ***************************************************************

    function get_ordered_image($iOutletID) {
        $viewData = array(
            "title" => $this->title,
            'page' => 'post',
            'outlet_id' => $iOutletID
        );
        $this->load->view("image/image_view", $viewData);
    }

    // ***************************************************************
    // REMOVE
    // ***************************************************************
    function remove($iPictureID = '', $rm = '') {
        if ($iPictureID != '' && $rm != '' && $rm == 'y') {
            $removeImage = $this->image_model->removeImage($iPictureID);
            if ($removeImage != '') {
                $succ = array('0' => IMAGE_DELETED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => IMAGE_NOT_DELETED);
                $this->session->set_userdata('ERROR', $err);
            }
        }
        redirect("image", 'refresh');
    }

    // ***************************************************************
    function deleteAll() {
        $data = $_POST['rows'];

        $removeImage = $this->image_model->removeImage($_POST['rows']);
        if ($removeImage != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("image", "refresh");
    }

    // ***************************************************************
    function deleteFollowerAll() {
        $data = $_POST['rows'];

        $removeImage = $this->image_model->deleteFollowerAll($_POST['rows']);
        if ($removeImage != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("image", "refresh");
    }

    // ***************************************************************
    function deleteFollowingAll() {
        $data = $_POST['rows'];

        $removeImage = $this->image_model->deleteFollowingAll($_POST['rows']);
        if ($removeImage != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("image", "refresh");
    }

    function paginate($iRestaurantID = '') {
        $data = $this->image_model->get_paginationresult($iRestaurantID);
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
        }else {
            $awsTargetPath=array_reverse(explode('images/',$targetpath));
            $aws_object = $this->aws_sdk->saveObject(array(
                'Key' => $awsTargetPath[0],
                'ACL' => 'public-read',
                'SourceFile' => $mypath,
            ));
        }
    }

    /*     * ******************* End of the File ***************************** */
}
