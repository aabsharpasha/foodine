<?php

class Restaurant extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('restaurant_model');
        $this->load->model('music_model');
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
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $this->load->view('restaurant/restaurant_view', $viewData);
    }

    // ***************************************************************
    // ADD
    // ***************************************************************
    function add($iRestaurantID = '', $ed = '') {
        $viewData['title'] = "Restaurant Management";

        $viewData['ACTION_LABEL'] = (isset($iRestaurantID) && $iRestaurantID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iRestaurantID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->restaurant_model->getRestaurantDataById($iRestaurantID);
            $getCategory = $this->restaurant_model->getCategoryDataById($iRestaurantID);
            $getCuisine = $this->restaurant_model->getCuisineDataById($iRestaurantID);
            $getFacility = $this->restaurant_model->getFacilityDataById($iRestaurantID);
            $getMusic = $this->restaurant_model->getMusicDataById($iRestaurantID);

            $viewData['getRestaurantData'] = $getData;
            $viewData['getCategoryData'] = $getCategory;
            $viewData['getCuisineData'] = $getCuisine;
            $viewData['getFacilityData'] = $getFacility;
            $viewData['getMusicData'] = $getMusic;
        }
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.restaurantadd') {

            if ($this->restaurant_model->checkRestaurantEmailAvailable($this->input->post('vEmail'))) {
                $restaurantAdd = $this->restaurant_model->addRestaurant($_POST);

                if ($restaurantAdd != '') {

                    $targetpath = UPLOADS . "restaurant/" . $restaurantAdd;

                    //echo $targetpath;

                        if (!is_dir($targetpath)) {
                            if (!mkdir($targetpath, 0777, true)) {
                                
                            }
                        }
                        if (!is_dir($targetpath . "/thumb")) {
                            if (!mkdir($targetpath . "/thumb", 0777, true)) {
                                
                            }
                        }

                    if (isset($_FILES['vRestaurantLogo']) && $_FILES['vRestaurantLogo']['name'] != '') {
                        $config['upload_path'] = $targetpath;
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';
                        $config['max_size'] = 1024 * 20;
                        $config['encrypt_name'] = TRUE;

                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);

                        if ($this->upload->do_upload('vRestaurantLogo')) {
                            extract($this->upload->data('vRestaurantLogo'));
                            $images['vRestaurantLogo'] = $file_name;

                            if (!empty($images))
                                $restaurantedit = $this->restaurant_model->editRestaurantData($images, $restaurantAdd);

                            $mypath = $targetpath . '/' . $images['vRestaurantLogo'];
                            $targetpath_thumb = $targetpath . "/thumb/" . $images['vRestaurantLogo'];
                            $this->make_thumb($mypath, $targetpath_thumb, 200);
                        }
                        else {
                            //print_r($this->upload->display_errors());
                            //die();
                            //echo 'error';
                        }
                    }

                    $succ = array('0' => RESTAURANT_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => RESTAURANT_NOT_ADDED);

                    $this->session->set_userdata('SUCCESS', $err);
                }
            } else {
                $err = array('0' => RESTAURANT_EXISTS);

                $this->session->set_userdata('ERROR', $err);
            }
            redirect('restaurant', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.restaurantedit') {
            if ($this->restaurant_model->checkRestaurantEmailAvailable($this->input->post('vEmail'), $this->input->post('iRestaurantID'))) {


                $restaurantEdit = $this->restaurant_model->editRestaurant($_POST);

                $iRestaurantID = $this->input->post('iRestaurantID');

                $restaurantdata = $this->restaurant_model->getRestaurantDataById($iRestaurantID);

                $targetpath = UPLOADS . "restaurant/" . $iRestaurantID;

                if (!is_dir($targetpath)) {
                    if (!mkdir($targetpath, 0777, true)) {
                        
                    }
                }
                if (!is_dir($targetpath . "/thumb")) {
                    if (!mkdir($targetpath . "/thumb", 0777, true)) {
                        
                    }
                }

                if (isset($_FILES['vRestaurantLogo']) && $_FILES['vRestaurantLogo']['name'] != '') {

                    if ($restaurantdata != '') {
                        if ($restaurantdata['vRestaurantLogo'] != "") {
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

                    if ($this->upload->do_upload('vRestaurantLogo')) {

                        extract($this->upload->data('vRestaurantLogo'));
                        $images['vRestaurantLogo'] = $file_name;

                        if (!empty($images))
                            $restaurantedit = $this->restaurant_model->editRestaurantData($images, $iRestaurantID);

                        $mypath = $targetpath . '/' . $images['vRestaurantLogo'];
                        $targetpath_thumb = $targetpath . "/thumb/" . $images['vRestaurantLogo'];
                        $this->make_thumb($mypath, $targetpath_thumb, 200);
                    }
                    else {
                        //print_r($this->upload->display_errors());
                        //die();
                        //echo 'error';
                    }
                } else {

                    $removepic = $this->input->post('removepic');
                    if ($removepic == '1') {
                        if ($restaurantdata != '') {
                            if ($restaurantdata['vRestaurantLogo'] != "") {
                                $this->load->helper("file");
                                delete_files($targetpath . "/");
                                $images['vRestaurantLogo'] = '';
                                if (!empty($images))
                                    $restaurantedit = $this->restaurant_model->editRestaurantData($images, $iRestaurantID);
                            }
                        }
                    }
                }


                if ($restaurantEdit != '') {
                    $succ = array('0' => RESTAURANT_EDITED);

                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => RESTAURANT_EDITED);

                    $this->session->set_userdata('ERROR', $err);
                }
            } else {
                $err = array('0' => RESTAURANT_EXISTS);

                $this->session->set_userdata('ERROR', $err);
            }

            redirect('restaurant', 'refresh');
        }
        $this->load->view('restaurant/restaurant_add_view', $viewData);
    }

    // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iRestaurantID = '', $rm = '') {
        if ($iRestaurantID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->restaurant_model->changeRestaurantStatus($iRestaurantID);

            if ($changeStatus != '') {
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
        redirect("restaurant", 'refresh');
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
            redirect('restaurant', 'refresh');
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
            redirect('restaurant', 'refresh');
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

    /*     * ******************* End of the File ***************************** */
}
