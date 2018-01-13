<?php

class Category extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');        
        $this->load->library('DatatablesHelper');
        $this->load->model('category_model');
        $this->load->helper('string');
        $this->controller = 'category';
        $this->uppercase = 'Category';
        $this->title = 'Category Management';
        $this->load->library('aws_sdk');
    }

    // INDEX
    function index($iUserID = '') {
        $getRecords = $this->category_model->getCategoryDataAll();
        $viewData = array("title" => "Category Management");
        $viewData['breadCrumbArr'] = array("category" => "Category Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $viewData['get_iUserID'] = $iUserID;

        $this->load->view('category/category_view', $viewData);
    }

    // ***************************************************************
    // ADD
    // ***************************************************************
    function add($iCategoryID = '', $ed = '') {
        $viewData['title'] = "Category Management";
        
        $viewData['ACTION_LABEL'] = (isset($iCategoryID) && $iCategoryID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iCategoryID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->category_model->getCategoryDataById($iCategoryID);

            $viewData['getCategoryData'] = $getData;
        }

        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.categoryadd') {

            $check = $this->category_model->checkCategoryNameAvailable($this->input->post('vCategoryName'));
            if ($check) {
                $categoryEdit = $this->category_model->addCategory($_POST);
                if ($categoryEdit != '') {
                    if (isset($_FILES) && !empty($_FILES) && $_FILES['vCategoryImage']['error'] === 0) {

                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'category',
                                'id' => $categoryEdit
                            ),
                            'requireThumb' => TRUE
                        );

                        $this->load->library('fileupload', $param);
                        $upload_files = $this->fileupload->upload($_FILES, 'vCategoryImage');
                        if (!empty($upload_files)) {
                            /*
                             * NEED TO UPDATE THE FILE NAME TO DATABASE...
                             */
                            foreach ($upload_files as $V) {
                                $this->category_model->updateCategoryImage($categoryEdit, $V);
                            }
                        }
                    }

                    $succ = array('0' => CATEGORY_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => CATEGORY_NOT_ADDED);
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('category/index', 'refresh');
            } else {
                $err = array('0' => CATEGORY_CATEGORYNAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
            }
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.categoryedit') {
            $pic = $this->input->post('vCategoryUrl');
            //echo IMGURL . '/category/' . $this->input->post('iCategoryID') . '/' . $pic ;die;
            $check = $this->category_model->checkCategoryNameAvailable($this->input->post('vCategoryName'), $this->input->post('iCategoryID'));

            if ($check) {
                $categoryEdit = $this->category_model->editCategory($_POST);
                $iCategoryID = $this->input->post('iCategoryID');
                $fileChange = FALSE;

                $param = array(
                    'fileType' => 'image',
                    'maxSize' => 20,
                    'uploadFor' => array(
                        'key' => 'category',
                        'id' => $iCategoryID
                    ),
                    'requireThumb' => TRUE
                );

                $this->load->library('fileupload', $param);

                if ($this->input->post('removepic') == '1') {

                    $this->fileupload->removeFile();
                    // Remove Old Image from AWS //
                    $pic = $this->input->post('vCategoryUrl');
                    if (!empty($pic)) {
                        $ImagePath = 'category/' . $iCategoryID . '/' . $pic;
                        $ImageThumbPath = 'category/' . $iCategoryID . '/thumb/' . $pic;
                        //$this->aws_sdk->deleteImage($ImagePath);
                        //$this->aws_sdk->deleteImage($ImageThumbPath);
                    }
                    /*
                     * UPDATE IMAGE TO NULL VALUE
                     */
                    $this->db->update('tbl_category', array('vCategoryImage' => ''), array('iCategoryID' => $iCategoryID));
                }

                if (isset($_FILES['vCategoryImage']['name']) && !empty($_FILES['vCategoryImage']['name'])) {
                    //if (isset($_FILES) && !empty($_FILES)) {

                    $this->fileupload->removeFile();
                    $upload_files = $this->fileupload->upload($_FILES, 'vCategoryImage');

                    if (!empty($upload_files)) {
                        /*
                         * NEED TO UPDATE THE FILE NAME TO DATABASE...
                         */
                        foreach ($upload_files as $V) {
                            $this->category_model->updateCategoryImage($iCategoryID, $V);
                        }         

                        $fileChange = TRUE;
                    }
                    
                    //Remove old file from bucket //
                    $oldImg = $this->input->post('vCategoryUrl');
                    if (!empty($oldImg)) {
                        $ImagePath = 'category/' . $iCategoryID . '/' . $oldImg;
                        $ImageThumbPath = 'category/' . $iCategoryID . '/thumb/' . $oldImg;
                        //$this->aws_sdk->deleteImage($ImagePath);
                        //$this->aws_sdk->deleteImage($ImageThumbPath);
                    }
                }

                if ($categoryEdit != '' || $fileChange) {
                    $succ = array('0' => CATEGORY_EDITED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => CATEGORY_EDITED);
                    $this->session->set_userdata('SUCCESS', $err);
                }

                redirect('category/index', 'refresh');
            } else {
                $err = array('0' => CATEGORY_CATEGORYNAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
            }
        }

        $this->load->view('category/category_add_view', $viewData);
    }

    // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iCategoryID = '', $rm = '') {
        if ($iCategoryID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->category_model->changeCategoryStatus($iCategoryID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("category", 'refresh');
    }

    // ***************************************************************
    // Order history
    // ***************************************************************

    function get_ordered_category($iOutletID) {
        $viewData = array(
            "title" => $this->title,
            'page' => 'category',
            'outlet_id' => $iOutletID
        );
        $this->load->view("category/category_view", $viewData);
    }

    // ***************************************************************
    // REMOVE
    // ***************************************************************
    function remove($iCategoryID = '', $rm = '') {
        if ($iCategoryID != '' && $rm != '' && $rm == 'y') {
            $removeCategory = $this->category_model->removeCategory($iCategoryID);
            if ($removeCategory != '') {
                $succ = array('0' => CATEGORY_DELETED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => CATEGORY_NOT_DELETED);
                $this->session->set_userdata('ERROR', $err);
            }
        }
        redirect("category/index", 'refresh');
    }

    function deleteComment() {
        $removeComment = $this->category_model->removeComment();
        echo $removeComment;
    }

    // ***************************************************************
    function deleteAll() {
        $data = $_POST['rows'];        
        $removeCategory = $this->category_model->removeCategory($_POST['rows']);
        if ($removeCategory != '') {
           //$res=$this->aws_sdk->deleteDirectory('category/'.$data[0]);
           echo '1';
        } else {
           echo '0';
        }
        exit;
        //redirect("category", "refresh");
    }

    function paginate() {
        $data = $this->category_model->get_paginationresult();
        echo json_encode($data);
    }

    function getCommentList() {
        $data = $this->category_model->getCommentList();
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

    function reorder() {
        try {
            $viewdata['reorderdata'] = $this->category_model->getCategoryOrderData();

            $this->load->view('category/category_order', $viewdata);
        } catch (Exception $ex) {
            throw new Exception('Error in reorder function - ' . $ex);
        }
    }

    function saveorder() {
        try {
            $catOrder = $_POST['ordercat'];

            $this->category_model->saveOrder($catOrder);

            $succ = array('0' => 'Category Order saved successfully.');
            $this->session->set_userdata('SUCCESS', $succ);

            redirect('category/reorder', 'refresh');
        } catch (Exception $ex) {
            throw new Exception('Error in saveorder function - ' . $ex);
        }
    }

    function mapCategoryRestaurants() {
        $viewData['title'] = "Category Management";

        if ($this->input->post('iCategoryID')) {
            $map = $this->category_model->mapCategoryRestaurants($_POST);
            if ($map === true) {
                $succ = array('0' => "Mapping saved sucessfully");
                $this->session->set_userdata('SUCCESS', $succ);
                redirect('category/index', 'refresh');
            } else {
                if (is_string($map)) {
                    $err = array('0' => $map);
                } else {
                    $err = array('0' => "Mapping not saved, please try again.");
                }
                $this->session->set_userdata('ERROR', $err);
                $viewData['iCategoryID'] = $this->input->post('iCategoryID');
            }
        }

        $viewData['categoryList'] = $this->category_model->getCategoryList();

        $viewData['restaurantList'] = $this->category_model->getRestaurantList();

        $this->load->view('category/map_category_restaurants', $viewData);
    }

    function getMappedRestaurants($category = '') {
        $viewData['restaurantList'] = $this->category_model->getRestaurantList();
        if ($category) {
            $viewData['restaurants'] = $this->category_model->getMappedRestaurants($category);
        }
        $this->load->view('category/mapped_restaurants', $viewData);
    }

    /*
     * TO LOAD RESTAURANT VIEW HTML
     */

    function viewRestaurantDetail($recordId = '') {
        if ($recordId !== '') {
            $data = $this->category_model->getRestaurantDataByCatId($recordId);
            $this->load->view('category/view_restaurants', array('viewData' => $data));
        } return '';
    }

    /*     * ******************* End of the File ***************************** */
}
