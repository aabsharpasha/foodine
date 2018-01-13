<?php

class Report extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('report_model');
        $this->load->helper('string');
        $this->controller = 'report';
        $this->uppercase = 'Report';
        $this->title = 'Report Management';
    }

    // INDEX
    function index($iUserID = '') {
        $getRecords = $this->report_model->getReportDataAll();
        $viewData = array("title" => "Report Management");
        $viewData['breadCrumbArr'] = array("report" => "Report Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $viewData['get_iUserID'] = $iUserID;

        $this->load->view('report/report_view', $viewData);
    }

    // ***************************************************************
    // ADD
    // ***************************************************************
    function add($iReportID = '', $ed = '') {
        redirect('report', 'refresh');

        $viewData['title'] = "Report Management";

        $viewData['ACTION_LABEL'] = (isset($iReportID) && $iReportID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iReportID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->report_model->getReportDataById($iReportID);

            $viewData['getReportData'] = $getData;
        }
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.reportadd') {
            $check = $this->report_model->checkReportNameAvailable($this->input->post('vReportName'));
            if ($check) {
                $reportEdit = $this->report_model->addReport($_POST);
                if ($reportEdit != '') {
                    $succ = array('0' => REPORT_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => REPORT_NOT_ADDED);
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('report', 'refresh');
            } else {
                $err = array('0' => REPORT_REPORTNAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
            }
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.reportedit') {
            $check = $this->report_model->checkReportNameAvailable($this->input->post('vReportName'), $this->input->post('iReportID'));

            if ($check) {
                $reportEdit = $this->report_model->editReport($_POST);
                $iReportID = $this->input->post('iReportID');
                if ($reportEdit != '') {
                    $succ = array('0' => REPORT_EDITED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => REPORT_EDITED);
                    $this->session->set_userdata('SUCCESS', $err);
                }

                redirect('report', 'refresh');
            } else {
                $err = array('0' => REPORT_REPORTNAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
            }
        }

        $this->load->view('report/report_add_view', $viewData);
    }

    // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iReportID = '', $rm = '') {
        if ($iReportID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->report_model->changeReportStatus($iReportID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("report", 'refresh');
    }

    // ***************************************************************
    // Order history
    // ***************************************************************
    // ***************************************************************
    // REMOVE
    // ***************************************************************
    function remove($iReportID = '', $rm = '') {
        if ($iReportID != '' && $rm != '' && $rm == 'y') {
            $removeReport = $this->report_model->removeReport($iReportID);
            if ($removeReport != '') {
                $succ = array('0' => REPORT_DELETED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => REPORT_NOT_DELETED);
                $this->session->set_userdata('ERROR', $err);
            }
        }
        redirect("report", 'refresh');
    }

    function deleteComment() {
        $removeComment = $this->report_model->removeComment();
        echo $removeComment;
    }

    // ***************************************************************
    function deleteAll() {
        $data = $_POST['rows'];

        $removeReport = $this->report_model->removeReport($_POST['rows']);
        if ($removeReport != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("report", "refresh");
    }

    function paginate() {
        $data = $this->report_model->get_paginationresult();
        echo json_encode($data);
    }

    function getCommentList() {
        $data = $this->report_model->getCommentList();
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
    public function mostSuccessfullDealReport(){
        if(isset($_POST['iRestaurantID'])){
            $viewData = array("data" => $_POST);
            $this->load->view('report/most_successfull_deal_paginate', $viewData);
        }else{
            $viewData = array("title" => "Most Successfull Deals");
            $viewData["restaurantList"] = $this->report_model->getRestaurants();
            $viewData["offersTypeList"]     = $this->report_model->getOfferTypes();
            $this->load->view('report/most_successfull_deal', $viewData);
        }
    }

    function paginate_mostSuccessfullDeal() {
        $data = $this->report_model->paginateMostSuccessfullDeal($_GET);
        echo json_encode($data);
    }
    
    function averageUserRating() {
        $viewData = array("title" => "Average User Rating");
        $viewData['breadCrumbArr'] = array("report" => "Average User Rating");
        
        $this->load->view('report/average_user_rating', $viewData);
    }
    
    function paginate_averageRating() {
        $data = $this->report_model->get_averageRatingResult();
        echo json_encode($data);
    }
    
    function bestRating() {
        $viewData = array("title" => "Best Rating");
        $viewData['breadCrumbArr'] = array("report" => "Best Rating");
        
        $this->load->view('report/best_rating', $viewData);
    }
    function paginate_bestRating() {
        $data = $this->report_model->get_bestRatingResult();
        echo json_encode($data);
    }
    
    function worstRating() {
        $viewData = array("title" => "Worst Rating");
        $viewData['breadCrumbArr'] = array("report" => "Worst Rating");
        
        $this->load->view('report/worst_rating', $viewData);
    }
    function paginate_worstRating() {
        $data = $this->report_model->get_worstRatingResult();
        echo json_encode($data);
    }
    
    function bookmarked() {
        $viewData = array("title" => "Bookmarked");
        $viewData['breadCrumbArr'] = array("report" => "Bookmarked");
        
        $this->load->view('report/bookmarked', $viewData);
    }
    function paginate_bookmarked() {
        $data = $this->report_model->get_bookmarkedResult();
        echo json_encode($data);
    }
    
    function suggestedRestReport() {
        $viewData = array("title" => "Suggested Restaurant");
        $viewData['breadCrumbArr'] = array("report" => "Suggested Restaurant");
        
        $this->load->view('report/suggested', $viewData);
    }
    function paginate_suggested() {
        $data = $this->report_model->get_suggestedResult();
        echo json_encode($data);
    }
    
    public function worstPerformingDealReport(){
        if(isset($_POST['iRestaurantID'])){
            $viewData = array("data" => $_POST);
            $this->load->view('report/worst_performing_deal_paginate', $viewData);
        }else{
            $viewData = array("title" => "Most Successfull Deals");
            $viewData["restaurantList"] = $this->report_model->getRestaurants();
            $viewData["offersTypeList"]     = $this->report_model->getOfferTypes();
            $this->load->view('report/worst_performing_deal', $viewData);
        }
        
    }

    function paginate_worstPerformingDeal() {
        $data = $this->report_model->paginateWorstPerformingDeal($_GET);
        echo json_encode($data);
    }

    public function checkInReport() {
        if(isset($_POST['iRestaurantID'])){
            $viewData = array("data" => $_POST);
            $this->load->view('report/checkin_paginate', $viewData);
        }else{
            $viewData = array("title" => "Check In");
            $viewData["restaurantList"] = $this->report_model->getRestaurants();
            $this->load->view('report/checkin', $viewData);
        }
        
    }

    function paginate_checkIn() {
        $data = $this->report_model->paginateCheckIn($_GET);
        echo json_encode($data);
    }

    public function directSearchReport(){
        if(isset($_POST['searchText'])){
            $viewData = array("data" => $_POST);
            $this->load->view('report/direct_search_paginate', $viewData);
        }else{
            $viewData = array("title" => "Direct Search");
            $viewData["restaurantList"] = $this->report_model->getRestaurants();
            $this->load->view('report/direct_search', $viewData);
        }
        
    }

    function paginate_directSearch() {
        $data = $this->report_model->paginateDirectSearch($_GET);
        echo json_encode($data);
    }
 
    public function indirectSearchReport(){
        if(isset($_POST['searchText'])){
            $viewData = array("data" => $_POST);
            $this->load->view('report/indirect_search_paginate', $viewData);
        }else{
            $viewData = array("title" => "Direct Search");
            $viewData["searchTextList"] = $this->report_model->getSearchedText();
            $this->load->view('report/indirect_search', $viewData);
        }
        
    }

    function paginate_indirectSearch() {
        $data = $this->report_model->paginateIndirectSearch($_GET);
        echo json_encode($data);
    }

    public function rankInLocalityReport(){
        if(isset($_POST['iRestaurantID'])){
            $viewData = array("data" => $_POST);
            $this->load->view('report/rank_in_locality_paginate', $viewData);
        }else{
            $viewData = array("title" => "Direct Search");
            $viewData["restaurantList"] = $this->report_model->getRestaurants();
            $viewData["categoryList"]   = $this->report_model->getCategories();
            $this->load->view('report/rank_in_locality', $viewData);
        }
        
    }

    function paginate_rankInLocalityReport() {
        $data = $this->report_model->paginateRankInLocality($_GET);
        echo json_encode($data);
    }
    /*     * ******************* End of the File ***************************** */
}
