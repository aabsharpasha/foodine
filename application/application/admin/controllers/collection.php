<?php

class Collection extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('collection_model');
        $this->load->helper('string');
        $this->controller = 'collection';
        $this->uppercase = 'Collection';
        $this->title = 'Collection Management';
        $this->load->library('aws_sdk');
    }

    // INDEX
    function index($iUserID = '') {
        $getRecords = $this->collection_model->getCollectionDataAll();
        $viewData = array("title" => "Collection Management");
        $viewData['breadCrumbArr'] = array("collection" => "Collection Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $viewData['get_iUserID'] = $iUserID;

        $this->load->view('collection/collection_view', $viewData);
    }

    // ***************************************************************
    // ADD
    // ***************************************************************
    function add($iCollectionID = '', $ed = '') {
        $viewData['title'] = "Collection Management";

        $viewData['ACTION_LABEL'] = (isset($iCollectionID) && $iCollectionID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iCollectionID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->collection_model->getCollectionDataById($iCollectionID);

            $viewData['getCollectionData'] = $getData;
        }
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.collectionadd') {
            $check = $this->collection_model->checkCollectionNameAvailable($this->input->post('vCollectionName'));
            if ($check) {
                $collectionEdit = $this->collection_model->addCollection($_POST);
                if ($collectionEdit != '') {

                    if (isset($_FILES) && !empty($_FILES)) {

                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'cuisine',
                                'id' => $collectionEdit
                            ),
                            'requireThumb' => TRUE
                        );

                        $this->load->library('fileupload', $param);
                        $upload_files = $this->fileupload->upload($_FILES, 'vCollectionImage');

                        if (!empty($upload_files)) {
                            /*
                             * NEED TO UPDATE THE FILE NAME TO DATABASE...
                             */
                            foreach ($upload_files as $V) {
                                $this->collection_model->updateCollectionImage($collectionEdit, $V);
                            }
                        }
                    }

                    $succ = array('0' => CUISINE_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => CUISINE_NOT_ADDED);
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('collection/index', 'refresh');
            } else {
                $err = array('0' => CUISINE_CUISINENAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
            }
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.collectionedit') {
            $check = $this->collection_model->checkCollectionNameAvailable($this->input->post('vCollectionName'), $this->input->post('iCollectionID'));

            if ($check) {
                $collectionEdit = $this->collection_model->editCollection($_POST);
                $iCollectionID = $this->input->post('iCollectionID');
                $fileChange = FALSE;
                //mprd($_FILES);
                $param = array(
                    'fileType' => 'image',
                    'maxSize' => 20,
                    'uploadFor' => array(
                        'key' => 'cuisine',
                        'id' => $iCollectionID
                    ),
                    'requireThumb' => TRUE
                );

                $this->load->library('fileupload', $param);

                if ($this->input->post('removepic') == '1') {

                    $this->fileupload->removeFile();
                    $removeImg = $this->input->post('vCollectionUrl');
                    if (!empty($removeImg)) {
                        $ImagePath = 'collection/' . $iCollectionID . '/' . $removeImg;
                        $ImageThumbPath = 'collection/' . $iCollectionID . '/thumb/' . $removeImg;
                        //$this->aws_sdk->deleteImage($ImagePath);
                        //$this->aws_sdk->deleteImage($ImageThumbPath);
                    }
                    /*
                     * UPDATE IMAGE TO NULL VALUE
                     */
                    $this->db->update('tbl_collection', array('vCollectionImage' => ''), array('iCollectionID' => $iCollectionID));
                }

                if (isset($_FILES) && !empty($_FILES) && $_FILES['vCollectionImage']['error'] === 0) {
                    if (isset($_FILES['vCollectionImage']['name']) && !empty($_FILES['vCollectionImage']['name'])) {

                        $param = array(
                            'fileType' => 'image',
                            '$parammaxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'collection',
                                'id' => $iCollectionID
                            ),
                            'requireThumb' => TRUE
                        );

                        $this->load->library('fileupload', $param);
                        $this->fileupload->removeFile();
                        $upload_files = $this->fileupload->upload($_FILES, 'vCollectionImage');

                        if (!empty($upload_files)) {

                            /*
                             * NEED TO UPDATE THE FILE NAME TO DATABASE...
                             */

                            foreach ($upload_files as $V) {
                                $this->collection_model->updateCollectionImage($iCollectionID, $V);
                            }
                            $fileChange = TRUE;
                        }
                        $oldImg = $this->input->post('vCollectionUrl');
                        if (!empty($oldImg)) {
                            $ImagePath = 'collection/' . $iCollectionID . '/' . $oldImg;
                            $ImageThumbPath = 'collection/' . $iCollectionID . '/thumb/' . $oldImg;
                            //$this->aws_sdk->deleteImage($ImagePath);
                            //$this->aws_sdk->deleteImage($ImageThumbPath);
                        }
                    }
                }

                if ($collectionEdit != '' || $fileChange) {
                    $succ = array('0' => CUISINE_EDITED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => CUISINE_EDITED);
                    $this->session->set_userdata('SUCCESS', $err);
                }

                redirect('collection/index', 'refresh');
            } else {
                $err = array('0' => CUISINE_CUISINENAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
            }
        }

        $this->load->view('collection/collection_add_view', $viewData);
    }

    // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iCollectionID = '', $rm = '') {
        if ($iCollectionID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->collection_model->changeCollectionStatus($iCollectionID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("collection", 'refresh');
    }

    // ***************************************************************
    // Order history
    // ***************************************************************

    function get_ordered_collection($iOutletID) {
        $viewData = array(
            "title" => $this->title,
            'page' => 'collection',
            'outlet_id' => $iOutletID
        );
        $this->load->view("collection/collection_view", $viewData);
    }

    // ***************************************************************
    // REMOVE
    // ***************************************************************
    function remove($iCollectionID = '', $rm = '') {
        if ($iCollectionID != '' && $rm != '' && $rm == 'y') {
            $removeCollection = $this->collection_model->removeCollection($iCollectionID);
            if ($removeCollection != '') {
                $succ = array('0' => CUISINE_DELETED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => CUISINE_NOT_DELETED);
                $this->session->set_userdata('ERROR', $err);
            }
        }
        redirect("collection/index", 'refresh');
    }

    function deleteComment() {
        $removeComment = $this->collection_model->removeComment();
        echo $removeComment;
    }

    // ***************************************************************
    function deleteAll() {
        $data = $_POST['rows'];        

        $removeCollection = $this->collection_model->removeCollection($_POST['rows']);
        if ($removeCollection != '') {
            //$res=$this->aws_sdk->deleteDirectory('collection/'.$data[0]);
            echo '1';
        } else {
            echo '0';
        }
        //redirect("collection", "refresh");
    }

    function paginate() {
        $data = $this->collection_model->get_paginationresult();
        echo json_encode($data);
    }

    function getCommentList() {
        $data = $this->collection_model->getCommentList();
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
            $data = $this->collection_model->getRestaurantDataByCuiId($recordId);
            $this->load->view('category/view_restaurants', array('viewData' => $data));
        } return '';
    }

    /*     * ******************* End of the File ***************************** */
}
