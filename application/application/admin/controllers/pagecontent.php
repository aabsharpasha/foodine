<?php

class Pagecontent extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('pagecontent_model');
    }

    function index() {
        $getRecords = $this->pagecontent_model->getPageContentDataAll();
        $viewData = array("title" => "Page Content Management");
        $viewData['breadCrumbArr'] = array("pagecontent" => "Page Content Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $this->load->view('pagecontent/pagecontent_view', $viewData);
    }

    // ***************************************************************
    // ADD
    // ***************************************************************
    function add($iPageID = '', $ed = '') {
        $viewData['title'] = "PageContent Management";

        $viewData['breadCrumbArr'] = array(
            "PageContent" => "PageContent Management",
            "action" => (isset($iPageID) && $iPageID != '' && $ed != '' && $ed == 'y') ? "Edit Page Content" : "Add Page Content"
        );

        $viewData['ACTION_LABEL'] = (isset($iPageID) && $iPageID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iPageID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->pagecontent_model->getPageContentDataById($iPageID);
            $viewData['getPageContentData'] = $getData;
        }


        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.pagecontentedit') {

            $categoryEdit = $this->pagecontent_model->editPageContent($_POST);
            if ($categoryEdit != '') {
                $succ = array('0' => PAGECONTENT_EDITED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => PAGECONTENT_EDITED);
                $this->session->set_userdata('SUCCESS', $err);
            }

            redirect('pagecontent/add/' . $this->input->post('iPageID') . '/y');
        }

        $this->load->view('pagecontent/pagecontent_add_view', $viewData);
    }

    // ***************************************************************

    function deleteAll() {
        $data = $_POST;

        //mprd($data);
        foreach ($data as $k => $v) {
            if ($v == 'on') {
                $removePageContent = $this->pagecontent_model->removePageContent($k);
                //echo $this->db->last_query();exit;
            }
        }
        if ($removePageContent != '') {
            $succ = array('0' => PAGECONTENT_DELETED);
            $this->session->set_userdata('SUCCESS', $succ);
        } else {
            $err = array('0' => PAGECONTENT_DELETED);
            $this->session->set_userdata('SUCCESS', $err);
        }

        redirect("category", "refresh");
    }

    function paginate() {


        //    $data=$this->datatableshelper->query("SELECT `iPageID`,concat(vFirstName,' ',vLastName) as name,`vPageContentName`,`vEmail`, `vProfilePicture`,`eType` FROM tbl_category");

        $data = $this->pagecontent_model->get_paginationresult();
        //  print_r($data);
        //print_r($data);
        echo json_encode($data);
    }

}

