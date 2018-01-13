<?php

class User extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('user_model');
        $this->load->helper('string');
        $this->controller = 'user';
        $this->uppercase = 'User';
        $this->title = 'User Management';
        $this->load->library('aws_sdk');
    }

    // INDEX
    function index() {
        $getRecords = $this->user_model->getUserDataAll();
        $viewData = array("title" => "User Management");
        $viewData['breadCrumbArr'] = array("user" => "User Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $this->load->view('user/user_view', $viewData);
    }

    // INDEX
    function fav_restaurant($iUserID = '') {

        if ($iUserID == '') {
            redirect('user/index', 'refresh');
        } else {
            $getRecords = $this->user_model->getFavUserDataAll();
            $getData = $this->user_model->getUserDataById($iUserID);

            if ($getData == '')
                redirect('user/index', 'refresh');

            $viewData = array("title" => "User Management");
            $viewData['breadCrumbArr'] = array("user" => "User Management");

            $viewData['getUserData'] = $getData;

            if ($getRecords != '')
                $viewData['record_set'] = $getRecords;
            else
                $viewData['record_set'] = '';

            $this->load->view('user/user_favorite_view', $viewData);
        }
    }

    function user_point($iUserID = '') {
        if ($iUserID == '') {
            redirect('user/index', 'refresh');
        } else {
            $getRecords = $this->user_model->getUserPointDataAll();
            $getData = $this->user_model->getUserDataById($iUserID);

            if ($getData == '')
                redirect('user/index', 'refresh');

            $viewData = array("title" => "User Management");
            $viewData['breadCrumbArr'] = array("user" => "User Management");

            $viewData['getUserData'] = $getData;

            if ($getRecords != '')
                $viewData['record_set'] = $getRecords;
            else
                $viewData['record_set'] = '';

            $this->load->view('user/user_point_view', $viewData);
        }
    }

    // ***************************************************************
    // ADD
    // ***************************************************************
    function add($iUserID = '', $ed = '') {
        $viewData['title'] = "User Management";

        $viewData['ACTION_LABEL'] = (isset($iUserID) && $iUserID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iUserID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->user_model->getUserDataById($iUserID);
            $getCuisine = $this->user_model->getCuisineDataById($iUserID);
            $getFacility = $this->user_model->getFacilityDataById($iUserID);
            $getMusic = $this->user_model->getMusicDataById($iUserID);

            $viewData['getUserData'] = $getData;
            $viewData['getCuisineData'] = $getCuisine;
            $viewData['getFacilityData'] = $getFacility;
            $viewData['getMusicData'] = $getMusic;
        }
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.useradd') {

            if ($this->user_model->checkUserEmailAvailable($this->input->post('vEmail'))) {

                /* if ($this->user_model->checkUserNameAvailable($this->input->post('vUserName'))) { */

                if ($this->user_model->checkMobileNoAvailable($this->input->post('vMobileNo'))) {

                    $userAdd = $this->user_model->addUser($_POST);

                    if ($userAdd != '') {

                        $targetpath = UPLOADS . "user/" . $userAdd;

                        //echo $targetpath;

                        if (!mkdir($targetpath, 0777, true)) {
                            
                        }
                        if (!mkdir($targetpath . "/thumb", 0777, true)) {
                            
                        }

                        if (isset($_FILES['vProfilePicture']) && $_FILES['vProfilePicture']['name'] != '') {
                            $config['upload_path'] = $targetpath;
                            $config['allowed_types'] = 'gif|jpg|png|jpeg';
                            $config['max_size'] = 1024 * 20;
                            $config['encrypt_name'] = TRUE;

                            $this->load->library('upload', $config);
                            $this->upload->initialize($config);

                            if ($this->upload->do_upload('vProfilePicture')) {

                                extract($this->upload->data('vProfilePicture'));


                                $images['vProfilePicture'] = $file_name;

                                if (!empty($images)) {
                                    //$file = $this->CI->upload->data('image_file');
                                    $keyImage = array_reverse(explode('images/', $file_name));
                                    //echo 'user/'.$userAdd.'/'.$keyImage[0];die;
                                    $aws_object = $this->aws_sdk->saveObject(array(
                                        'Key' => 'user/' . $userAdd . '/' . $keyImage[0],
                                        'ContentType' => $file_type,
                                        'ACL' => 'public-read',
                                        'SourceFile' => $full_path,
                                    ));

                                    $useredit = $this->user_model->editUserData($images, $userAdd);
                                }

                                $mypath = $targetpath . '/' . $images['vProfilePicture'];
                                $targetpath_thumb = $targetpath . "/thumb/" . $images['vProfilePicture'];
                                $this->make_thumb($mypath, $targetpath_thumb, 200);
                                chmod($mypath,'0777');chmod($targetpath_thumb,'0777');
                                @unlink($mypath);@unlink($targetpath_thumb);
                            } else {
                                //print_r($this->upload->display_errors());
                                //die();
                                //echo 'error';
                            }
                        }

                        /*$this->load->library('maillib');
                        $param = array(
                            '%MAILSUBJECT%' => 'HungerMafia : Welcome',
                            '%FONT1%' => DOMAIN_URL . '/images/fonts/HarabaraMais.otf',
                            '%FONT2%' => DOMAIN_URL . '/images/fonts/NexaLight.otf',
                            '%LOGO_IMAGE%' => DOMAIN_URL . '/images/hungermafia.png',
                            '%BACK_IMAGE%' => DOMAIN_URL . '/images/email/new_user.jpg',
                            '%FT1%' => DOMAIN_URL . '/images/email/fb.png',
                            '%FT2%' => DOMAIN_URL . '/images/email/twitter.png',
                            '%FT3%' => DOMAIN_URL . '/images/email/linkedin.png',
                            '%FT4%' => DOMAIN_URL . '/images/email/youtube.png',
                            '%USER_NAME%' => $this->input->post('vFullName'),
                            //'%CMS_LOGIN_LINK%' => BASEURL . 'login',
                            '%USERNAME%' => $this->input->post('vEmail'),
                            '%PASSWORD%' => $this->input->post('vPassword')
                        );
                        //$tmplt = DIR_ADMIN_VIEW . $this->controller . '/email/new_restaurant.php';
                        $tmplt = DIR_ADMIN_VIEW . 'template/new_user.php';
                        $subject = 'HungerMafia : Welcome';
                        $to = $this->input->post('vEmail');
                        //$this->smtpmail_model->send($to, $subject, $tmplt, $param);
                        $this->maillib->sendMail($to, $subject, $tmplt, $param, false, array(), array(CC_EMAIL_ID));
*/

                        $succ = array('0' => USER_ADDED);
                        $this->session->set_userdata('SUCCESS', $succ);
                    } else {
                        $err = array('0' => USER_NOT_ADDED);

                        $this->session->set_userdata('ERROR', $err);
                    }
                } else {
                    $err = array('0' => USER_MOBILENO_EXISTS);

                    $this->session->set_userdata('ERROR', $err);
                }
                /* } else {
                  $err = array('0' => USER_USERNAME_EXISTS);

                  $this->session->set_userdata('ERROR', $err);
                  } */
            } else {
                $err = array('0' => USER_EXISTS);

                $this->session->set_userdata('ERROR', $err);
            }
            redirect('user/index', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.useredit') {
            if ($this->user_model->checkUserEmailAvailable($this->input->post('vEmail'), $this->input->post('iUserID'))) {

                /* if ($this->user_model->checkUserNameAvailable($this->input->post('vUserName'), $this->input->post('iUserID'))) { */

                if ($this->user_model->checkMobileNoAvailable($this->input->post('vMobileNo'), $this->input->post('iUserID'))) {

                    $userEdit = $this->user_model->editUser($_POST);

                    $iUserID = $this->input->post('iUserID');

                    $userdata = $this->user_model->getUserDataById($iUserID);

                    $targetpath = UPLOADS . "user/" . $iUserID;

                    if (!is_dir($targetpath)) {
                        if (!mkdir($targetpath, 0777, true)) {
                            
                        }
                    }
                    if (!is_dir($targetpath . "/thumb")) {
                        if (!mkdir($targetpath . "/thumb", 0777, true)) {
                            
                        }
                    }

                    if (isset($_FILES['vProfilePicture']) && $_FILES['vProfilePicture']['name'] != '') {

                        if ($userdata != '') {
                            if ($userdata['vProfilePicture'] != "") {
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

                        if ($this->upload->do_upload('vProfilePicture')) {

                            extract($this->upload->data('vProfilePicture'));
                            $images['vProfilePicture'] = $file_name;

                            if (!empty($images)){
                                $useredit = $this->user_model->editUserData($images, $iUserID);
                                 $keyImage = array_reverse(explode('images/', $file_name));
                                //echo 'user/'.$userAdd.'/'.$keyImage[0];die;
                                $aws_object = $this->aws_sdk->saveObject(array(
                                    'Key' => 'user/' . $userAdd . '/' . $keyImage[0],
                                    'ContentType' => $file_type,
                                    'ACL' => 'public-read',
                                    'SourceFile' => $full_path,
                                ));
                            }

                            $mypath = $targetpath . '/' . $images['vProfilePicture'];
                            $targetpath_thumb = $targetpath . "/thumb/" . $images['vProfilePicture'];
                            $this->make_thumb($mypath, $targetpath_thumb, 200);
                            
                            chmod($mypath,'0777');chmod($targetpath_thumb,'0777');
                            @unlink($mypath);@unlink($targetpath_thumb);
                        }
                        else {
                            //print_r($this->upload->display_errors());
                            //die();
                            //echo 'error';
                        }
                    } else {

                        $removepic = $this->input->post('removepic');
                        if ($removepic == '1') {
                            if ($userdata != '') {
                                if ($userdata['vProfilePicture'] != "") {
                                    $this->load->helper("file");
                                    delete_files($targetpath . "/");
                                    $images['vProfilePicture'] = '';
                                    if (!empty($images))
                                        $useredit = $this->user_model->editUserData($images, $iUserID);
                                }
                            }
                        }
                    }


                    if ($userEdit != '') {
                        $succ = array('0' => USER_EDITED);

                        $this->session->set_userdata('SUCCESS', $succ);
                    } else {
                        $err = array('0' => USER_EDITED);

                        $this->session->set_userdata('SUCCESS', $err);
                    }
                } else {
                    $err = array('0' => USER_MOBILENO_EXISTS);

                    $this->session->set_userdata('ERROR', $err);
                }
                /* } else {
                  $err = array('0' => USER_USERNAME_EXISTS);

                  $this->session->set_userdata('ERROR', $err);
                  } */
            } else {
                $err = array('0' => USER_EXISTS);

                $this->session->set_userdata('ERROR', $err);
            }

            redirect('user/index', 'refresh');
        }
        $this->load->view('user/user_add_view', $viewData);
    }

    // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iUserID = '', $rm = '') {
        if ($iUserID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->user_model->changeUserStatus($iUserID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("user", 'refresh');
    }

    // ***************************************************************
    // Order history
    // ***************************************************************

    function get_ordered_user($iOutletID) {
        $viewData = array(
            "title" => $this->title,
            'page' => 'post',
            'outlet_id' => $iOutletID
        );
        $this->load->view("user/user_view", $viewData);
    }

    // ***************************************************************
    // REMOVE
    // ***************************************************************
    function remove($iUserID = '', $rm = '') {
        if ($iUserID != '' && $rm != '' && $rm == 'y') {
            $removeUser = $this->user_model->removeUser($iUserID);
            if ($removeUser != '') {
                $succ = array('0' => USER_DELETED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => USER_NOT_DELETED);
                $this->session->set_userdata('ERROR', $err);
            }
        }
        redirect("user/index", 'refresh');
    }

    // ***************************************************************
    function deleteAll() {
        $data = $_POST['rows'];

        $removeUser = $this->user_model->removeUser($_POST['rows']);
        if ($removeUser != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("user", "refresh");
    }

    function deleteAll_favorite() {
        $data = $_POST['rows'];

        $removeUser = $this->user_model->removeUserfavorite($_POST['rows']);
        if ($removeUser != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("user", "refresh");
    }

    // ***************************************************************
    function deleteFollowerAll() {
        $data = $_POST['rows'];

        $removeUser = $this->user_model->deleteFollowerAll($_POST['rows']);
        if ($removeUser != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("user", "refresh");
    }

    // ***************************************************************
    function deleteFollowingAll() {
        $data = $_POST['rows'];

        $removeUser = $this->user_model->deleteFollowingAll($_POST['rows']);
        if ($removeUser != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("user", "refresh");
    }

    function paginate() {
        $data = $this->user_model->get_paginationresult();
        echo json_encode($data);
    }

    function paginate_favorite($iUserID = '') {
        $data = $this->user_model->get_paginate_favorite($iUserID);
        echo json_encode($data);
    }

    function paginate_follower($iUserID = '') {
        $data = $this->user_model->get_paginate_follower($iUserID);
        echo json_encode($data);
    }

    function paginate_user_point($iUserID = '') {
        $data = $this->user_model->get_paginate_user_point($iUserID);
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

    /*
     * TO LOAD RESTAURANT VIEW HTML
     */

    function viewDetail($recordId = '') {
        if ($recordId !== '') {
            $data = $this->user_model->getUserDataById($recordId);

            $this->load->view('user/viewDetail', array('viewData' => $data));
        } return '';
    }

    function testmail() {
        $this->load->library('maillib');
        $param = array(
            '%MAILSUBJECT%' => 'HungerMafia : Welcome',
            '%FONT1%' => DOMAIN_URL . '/images/fonts/HarabaraMais.otf',
            '%FONT2%' => DOMAIN_URL . '/images/fonts/NexaLight.otf',
            '%LOGO_IMAGE%' => DOMAIN_URL . '/images/hungermafia.png',
            '%BACK_IMAGE%' => DOMAIN_URL . '/images/email/new_user.jpg',
            '%FT1%' => DOMAIN_URL . '/images/email/fb.png',
            '%FT2%' => DOMAIN_URL . '/images/email/twitter.png',
            '%FT3%' => DOMAIN_URL . '/images/email/linkedin.png',
            '%FT4%' => DOMAIN_URL . '/images/email/youtube.png',
            '%USER_NAME%' => $this->input->post('vFullName'),
            //'%CMS_LOGIN_LINK%' => BASEURL . 'login',
            '%USERNAME%' => $this->input->post('vEmail'),
            '%PASSWORD%' => $this->input->post('vPassword')
        );
        //$tmplt = DIR_ADMIN_VIEW . $this->controller . '/email/new_restaurant.php';
        $tmplt = DIR_ADMIN_VIEW . 'template/new_user.php';
        $subject = 'HungerMafia : Welcome';
        $to = 'mayur.chhatrala@openxcell.com';
        //$this->smtpmail_model->send($to, $subject, $tmplt, $param);
        $this->maillib->sendMail($to, $subject, $tmplt, $param, false, array(), array(CC_EMAIL_ID));
    }

    function sendBirthdayAnniversaryEmail() {
        
    }

    /*     * ******************* End of the File ***************************** */
}
