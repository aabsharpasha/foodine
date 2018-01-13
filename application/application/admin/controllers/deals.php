<?php

class Deals extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('deals_model');
        $this->load->helper('string');
        $this->controller = 'deals';
        $this->uppercase = 'Deals';
        $this->title = 'Deals Management';
        $this->load->library('aws_sdk');
    }

    /*
     * TO DISPLAY LIST OF DEALS TO THE TABLE FORMAT...
     */

    function index() {
        $getRecords = $this->deals_model->getDealsDataAll();
        $viewData = array("title" => "Deals Management");
        $viewData['breadCrumbArr'] = array("deals" => "Deals Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';


        $this->load->view('deals/deals_view', $viewData);
    }

    /*
     * TO LIST OUT THE LIST OF RECORDS...
     */

    function paginate() {
        $data = $this->deals_model->get_paginationresult();
        echo json_encode($data);
    }


    function likePaginate($iDealID = '') {
        $data = $this->deals_model->get_like_paginationresult($iDealID);
        echo json_encode($data);
    }

    /*
     * DELETE EITHER ONE RECORD OR LIST OF RECORDS... 
     */

    function deleteAll() {
        $data = $_POST['rows'];
        $removeDeal = $this->deals_model->removeDeals($_POST['rows']);
        if ($removeDeal != '') {
            //$res=$this->aws_sdk->deleteDirectory('deal/'.$data[0]);
            echo '1';
        } else {
            echo '0';
        }
    }

    /*
     * TO CHANGE THE STATUS OF THE RECORD..
     */

    function status($iDealID = '', $rm = '') {
        if ($iDealID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->deals_model->changeDealsStatus($iDealID);

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

    function add($iDealID = '', $ed = '') {
        $viewData['title'] = "Deals Management";

        $viewData['ACTION_LABEL'] = (isset($iDealID) && $iDealID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iDealID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->deals_model->getDealsDataById($iDealID);

            $viewData['getDealsData'] = $getData;
        }

        /*
         * GET RESTAURANT LIST
         */
        $viewData['getRestaurantData'] = $this->deals_model->getRestaurantList();
        
         /*
         * GET OFFER TYPE LIST
         */
        $viewData['getOfferTypeData'] = $this->deals_model->getOfferTypeList();

        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.dealsadd') {
            $dealsAdd = $this->deals_model->addDeals($_POST);
            if ($dealsAdd != '') {
                
                $param = array(
                    'fileType' => 'image',
                    'maxSize' => 20,
                    'uploadFor' => array(
                        'key' => 'deal',
                        'id' => $dealsAdd
                    ),
                    'requireThumb' => TRUE
                );
                $this->load->library('fileupload', $param);
                
                if (isset($_FILES) && !empty($_FILES) && $_FILES['vDealImage']['error'] === 0) {

                    $this->fileupload->removeFile();
                    $upload_files = $this->fileupload->upload($_FILES, 'vDealImage');

                    if (!empty($upload_files)) {

                        /*
                         * NEED TO UPDATE THE FILE NAME TO DATABASE...
                         */

                        foreach ($upload_files as $V) {
                             $this->db->update('tbl_deals', array('vDealImage' => $V), array('iDealID' => $dealsAdd));
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
            redirect('deals/index', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.dealsedit') {
            $dealsAdd = $this->deals_model->editDeals($_POST);
            if ($this->input->post('iDealID') != '') {
                $dealsAdd = $this->input->post('iDealID');
                
                $param = array(
                    'fileType' => 'image',
                    'maxSize' => 20,
                    'uploadFor' => array(
                        'key' => 'deal',
                        'id' => $dealsAdd
                    ),
                    'requireThumb' => TRUE
                );

                $this->load->library('fileupload', $param);
                

                if ($this->input->post('removepic') == '1') {

                    $this->fileupload->removeFile();
                    $removeImg = $this->input->post('vOfferUrl');
                    if (!empty($removeImg)) {
                        $ImagePath = 'deal/' . $dealsAdd . '/' . $removeImg;
                        $ImageThumbPath = 'deal/' . $dealsAdd . '/thumb/' . $removeImg;
                        //$this->aws_sdk->deleteImage($ImagePath);
                        //$this->aws_sdk->deleteImage($ImageThumbPath);
                    }
                    /*
                     * UPDATE IMAGE TO NULL VALUE
                     */
                    $this->db->update('tbl_deals', array('vDealImage' => ''), array('iDealID' => $dealsAdd));
                }
                
                if (isset($_FILES) && !empty($_FILES) && $_FILES['vDealImage']['error'] === 0) {

                    
                    $this->fileupload->removeFile();
                    $upload_files = $this->fileupload->upload($_FILES, 'vDealImage');

                    if (!empty($upload_files)) {

                        /*
                         * NEED TO UPDATE THE FILE NAME TO DATABASE...
                         */

                        foreach ($upload_files as $V) {
                             $this->db->update('tbl_deals', array('vDealImage' => $V), array('iDealID' => $dealsAdd));
                        }
                        $fileChange = TRUE;
                        $oldImg = $this->input->post('vOfferUrl');
                        if (!empty($oldImg)) {
                            $ImagePath = 'deal/' . $dealsAdd . '/' . $oldImg;
                            $ImageThumbPath = 'deal/' . $dealsAdd . '/thumb/' . $oldImg;
                            //$this->aws_sdk->deleteImage($ImagePath);
                            //$this->aws_sdk->deleteImage($ImageThumbPath);
                        }
                    }
                }
                
                $succ = array('0' => DEALS_EDITED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => DEALS_NOT_EDITED);
                $this->session->set_userdata('ERROR', $err);
            }
            redirect('deals/index', 'refresh');
        }

        $this->load->view('deals/deals_add_view', $viewData);
    }

    /*
     * TO CHECK THE DEAL CODE IS ALREADY EXISTS OR NOT...
     */

    function checkDealCode() {
        if (@$this->input->post('vDealCode') !== '') {
            /*
             * TO CHECKL WITH THE DATABASE...
             */
            echo $this->deals_model->checkDealCodeExists($this->input->post('vDealCode'), $this->input->post('iDealID'));
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
            $getData = $this->deals_model->getDealsDataById($iDealID);
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
            $data = $this->deals_model->getDealsDataViewById($recordId);
            
            $this->load->view('deals/viewDetail', array('viewData' => $data));
        } return '';
    }
    
    function visitedStatus($id = ''){
        if ($id != '') {
            $changeStatus = $this->deals_model->changeAvailedStatus($id);
            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        
    }

}

?>
