<?php

class Menu extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('menu_model');
        $this->load->helper('string');
        $this->controller = 'menu';
        $this->uppercase = 'Menu';
        $this->title = 'Restaurant Menu Images Management';
        $this->load->library('aws_sdk');
    }

    // INDEX
    function index($iRestaurantID = '') {

        if ($iRestaurantID == '')
            redirect('restaurant', 'refresh');

        $getRecords = $this->menu_model->getImageDataAllbyRes($iRestaurantID);
        $getRes = $this->menu_model->getRestaurantDataById($iRestaurantID);

        if ($getRes == '')
            redirect('restaurant', 'refresh');

        $viewData = array("title" => "Restaurant Menu Images Management");
        $viewData['breadCrumbArr'] = array("image" => "Restaurant Menu Images Management");
        if ($getRecords != '') {
            $viewData['record_set'] = $getRecords;
            $viewData['record_set_restaurant'] = $getRes;
        } else
            $viewData['record_set'] = '';

        $viewData['iRestaurantID'] = $iRestaurantID;

        $this->load->view('menu/menu_view', $viewData);
    }

    // ***************************************************************
    // ADD
    // ***************************************************************
    function add($iMenuPictureID = '', $ed = '') {
        $viewData['title'] = "Restaurant Menu Images Management";

        $viewData['ACTION_LABEL'] = (isset($iMenuPictureID) && $iMenuPictureID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iMenuPictureID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->menu_model->getImageDataById($iMenuPictureID);
            //$getCategory = $this->menu_model->getCategoryDataById($iMenuPictureID);

            $viewData['getImageData'] = $getData;
            //$viewData['getCategoryData'] = $getCategory;
        }
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.imageadd') {

            if ($this->menu_model->checkImageEmailAvailable($this->input->post('vEmail'))) {
                $imageAdd = $this->menu_model->addImage($_POST);

                if ($imageAdd != '') {

                    $targetpath = UPLOADS . "image/" . $imageAdd;

                    //echo $targetpath;

                    if (!mkdir($targetpath, 0777, true)) {
                        
                    }
                    if (!mkdir($targetpath . "/thumb", 0777, true)) {
                        
                    }

                    if (isset($_FILES['vImageLogo']) && $_FILES['vImageLogo']['name'] != '') {
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
                                //echo 'user/'.$userAdd.'/'.$keyImage[0];die;
                                $aws_object = $this->aws_sdk->saveObject(array(
                                    'Key' => 'user/' . $userAdd . '/' . $keyImage[0],
                                    'ContentType' => $file_type,
                                    'ACL' => 'public-read',
                                    'SourceFile' => $full_path,
                                ));
                                $imageedit = $this->menu_model->editImageData($images, $imageAdd);
                            }

                            $mypath = $targetpath . '/' . $images['vImageLogo'];
                            $targetpath_thumb = $targetpath . "/thumb/" . $images['vImageLogo'];
                            $this->make_thumb($mypath, $targetpath_thumb, 200);
                        } else {
                            //print_r($this->upload->display_errors());
                            //die();
                            //echo 'error';
                        }
                    }

                    $succ = array('0' => IMAGE_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => IMAGE_NOT_ADDED);

                    $this->session->set_userdata('ERROR', $err);
                }
            } else {
                $err = array('0' => IMAGE_EXISTS);

                $this->session->set_userdata('ERROR', $err);
            }
            redirect('image', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.imageedit') {
            if ($this->menu_model->checkImageEmailAvailable($this->input->post('vEmail'), $this->input->post('iMenuPictureID'))) {


                $imageEdit = $this->menu_model->editImage($_POST);

                $iMenuPictureID = $this->input->post('iMenuPictureID');

                $imagedata = $this->menu_model->getImageDataById($iMenuPictureID);

                $targetpath = UPLOADS . "image/" . $iMenuPictureID;

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
                            if (!empty($images)) {
                                $keyImage = array_reverse(explode('images/', $file_name));
                                //echo 'user/'.$userAdd.'/'.$keyImage[0];die;
                                $aws_object = $this->aws_sdk->saveObject(array(
                                    'Key' => 'user/' . $userAdd . '/' . $keyImage[0],
                                    'ContentType' => $file_type,
                                    'ACL' => 'public-read',
                                    'SourceFile' => $full_path,
                                ));
                                $imageedit = $this->menu_model->editImageData($images, $imageAdd);
                            }

                            $imageedit = $this->menu_model->editImageData($images, $iMenuPictureID);
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
                                    $imageedit = $this->menu_model->editImageData($images, $iMenuPictureID);
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
        
        $this->load->view('image/menu_add_view', $viewData);
    }

    // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iMenuPictureID = '', $rm = '') {
        if ($iMenuPictureID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->menu_model->changeImageStatus($iMenuPictureID);

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
        $this->load->view("menu/menu_view", $viewData);
    }

    // ***************************************************************
    // REMOVE
    // ***************************************************************
    function remove($iMenuPictureID = '', $rm = '') {
        if ($iMenuPictureID != '' && $rm != '' && $rm == 'y') {
            $removeImage = $this->menu_model->removeImage($iMenuPictureID);
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

        $removeImage = $this->menu_model->removeImage($_POST['rows']);
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

        $removeImage = $this->menu_model->deleteFollowerAll($_POST['rows']);
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

        $removeImage = $this->menu_model->deleteFollowingAll($_POST['rows']);
        if ($removeImage != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("image", "refresh");
    }

    function paginate($iRestaurantID = '') {
        $data = $this->menu_model->get_paginationresult($iRestaurantID);
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
        } else {
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
