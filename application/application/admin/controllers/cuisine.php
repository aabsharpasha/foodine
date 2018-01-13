<?php

class Cuisine extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('cuisine_model');
        $this->load->helper('string');
        $this->controller = 'cuisine';
        $this->uppercase = 'Cuisine';
        $this->title = 'Cuisine Management';
        $this->load->library('aws_sdk');
    }

    // INDEX
    function index($iUserID = '') {
        $getRecords = $this->cuisine_model->getCuisineDataAll();
        $viewData = array("title" => "Cuisine Management");
        $viewData['breadCrumbArr'] = array("cuisine" => "Cuisine Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $viewData['get_iUserID'] = $iUserID;

        $this->load->view('cuisine/cuisine_view', $viewData);
    }

    // ***************************************************************
    // ADD
    // ***************************************************************
    function add($iCuisineID = '', $ed = '') {
        $viewData['title'] = "Cuisine Management";

        $viewData['ACTION_LABEL'] = (isset($iCuisineID) && $iCuisineID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iCuisineID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->cuisine_model->getCuisineDataById($iCuisineID);

            $viewData['getCuisineData'] = $getData;
        }
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.cuisineadd') {
            $check = $this->cuisine_model->checkCuisineNameAvailable($this->input->post('vCuisineName'));
            if ($check) {
                $cuisineEdit = $this->cuisine_model->addCuisine($_POST);
                if ($cuisineEdit != '') {

                    if (isset($_FILES) && !empty($_FILES)) {

                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'cuisine',
                                'id' => $cuisineEdit
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
                                $this->cuisine_model->updateCuisineImage($cuisineEdit, $V);
                            }
                        }
                    }

                    $succ = array('0' => CUISINE_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => CUISINE_NOT_ADDED);
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('cuisine/index', 'refresh');
            } else {
                $err = array('0' => CUISINE_CUISINENAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
            }
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.cuisineedit') {
            $check = $this->cuisine_model->checkCuisineNameAvailable($this->input->post('vCuisineName'), $this->input->post('iCuisineID'));

            if ($check) {
                $cuisineEdit = $this->cuisine_model->editCuisine($_POST);
                $iCuisineID = $this->input->post('iCuisineID');
                $fileChange = FALSE;
                //mprd($_FILES);
                $param = array(
                    'fileType' => 'image',
                    'maxSize' => 20,
                    'uploadFor' => array(
                        'key' => 'cuisine',
                        'id' => $iCuisineID
                    ),
                    'requireThumb' => TRUE
                );

                $this->load->library('fileupload', $param);

                if ($this->input->post('removepic') == '1') {

                    $this->fileupload->removeFile();
                    $removeImg = $this->input->post('vCuisineUrl');
                    if (!empty($removeImg)) {
                        $ImagePath = 'cuisine/' . $iCuisineID . '/' . $removeImg;
                        $ImageThumbPath = 'cuisine/' . $iCuisineID . '/thumb/' . $removeImg;
                        //$this->aws_sdk->deleteImage($ImagePath);
                        //$this->aws_sdk->deleteImage($ImageThumbPath);
                    }
                    /*
                     * UPDATE IMAGE TO NULL VALUE
                     */
                    $this->db->update('tbl_cuisine', array('vCuisineImage' => ''), array('iCuisineID' => $iCuisineID));
                }

                if (isset($_FILES) && !empty($_FILES) && $_FILES['vCuisineImage']['error'] === 0) {
                    if (isset($_FILES['vCuisineImage']['name']) && !empty($_FILES['vCuisineImage']['name'])) {

                        $param = array(
                            'fileType' => 'image',
                            '$parammaxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'cuisine',
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
                                $this->cuisine_model->updateCuisineImage($iCuisineID, $V);
                            }
                            $fileChange = TRUE;
                        }
                        $oldImg = $this->input->post('vCuisineUrl');
                        if (!empty($oldImg)) {
                            $ImagePath = 'cuisine/' . $iCuisineID . '/' . $oldImg;
                            $ImageThumbPath = 'cuisine/' . $iCuisineID . '/thumb/' . $oldImg;
                            //$this->aws_sdk->deleteImage($ImagePath);
                            //$this->aws_sdk->deleteImage($ImageThumbPath);
                        }
                    }
                }

                if ($cuisineEdit != '' || $fileChange) {
                    $succ = array('0' => CUISINE_EDITED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => CUISINE_EDITED);
                    $this->session->set_userdata('SUCCESS', $err);
                }

                redirect('cuisine/index', 'refresh');
            } else {
                $err = array('0' => CUISINE_CUISINENAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
            }
        }

        $this->load->view('cuisine/cuisine_add_view', $viewData);
    }

    // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iCuisineID = '', $rm = '') {
        if ($iCuisineID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->cuisine_model->changeCuisineStatus($iCuisineID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("cuisine", 'refresh');
    }

    // ***************************************************************
    // Order history
    // ***************************************************************

    function get_ordered_cuisine($iOutletID) {
        $viewData = array(
            "title" => $this->title,
            'page' => 'cuisine',
            'outlet_id' => $iOutletID
        );
        $this->load->view("cuisine/cuisine_view", $viewData);
    }

    // ***************************************************************
    // REMOVE
    // ***************************************************************
    function remove($iCuisineID = '', $rm = '') {
        if ($iCuisineID != '' && $rm != '' && $rm == 'y') {
            $removeCuisine = $this->cuisine_model->removeCuisine($iCuisineID);
            if ($removeCuisine != '') {
                $succ = array('0' => CUISINE_DELETED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => CUISINE_NOT_DELETED);
                $this->session->set_userdata('ERROR', $err);
            }
        }
        redirect("cuisine/index", 'refresh');
    }

    function deleteComment() {
        $removeComment = $this->cuisine_model->removeComment();
        echo $removeComment;
    }

    // ***************************************************************
    function deleteAll() {
        $data = $_POST['rows'];        

        $removeCuisine = $this->cuisine_model->removeCuisine($_POST['rows']);
        if ($removeCuisine != '') {
            //$res=$this->aws_sdk->deleteDirectory('cuisine/'.$data[0]);
            echo '1';
        } else {
            echo '0';
        }
        //redirect("cuisine", "refresh");
    }

    function paginate() {
        $data = $this->cuisine_model->get_paginationresult();
        echo json_encode($data);
    }

    function getCommentList() {
        $data = $this->cuisine_model->getCommentList();
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

    /*
     * TO LOAD RESTAURANT VIEW HTML
     */

    function viewRestaurantDetail($recordId = '') {
        if ($recordId !== '') {
            $data = $this->cuisine_model->getRestaurantDataByCuiId($recordId);
            $this->load->view('category/view_restaurants', array('viewData' => $data));
        } return '';
    }

    /*     * ******************* End of the File ***************************** */
}
