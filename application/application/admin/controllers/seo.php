<?php

class Seo extends CI_Controller {

    var $viewData = array();

    function __construct() {

        parent::__construct();

        $this->controller = 'seo';
        $this->uppercase = 'SEO';
        $this->title = 'SEO';

        $this->load->model('seo_model');
    }

    function metaTags() {
        $viewData   = array("title" => "SEO");
        $this->load->view('seo/view_meta_tags', $viewData);
    }

    function paginateMetaTags() {
        $data = $this->seo_model->getTagsPagination();
//        print_r($data);exit;
        echo json_encode($data);
    }

    function addMetaTags($iRestaurantID=''){
        try{
            $viewData['title'] = "Edit SEO Meta Tags";
            if ($this->input->post('action')) {
                if( $this->seo_model->updateMetaTags($_POST) ){
                    $succ = array('0' => "Meta Tags updated successfully!");
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => "Unable to update Meta Tags!");
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('seo/metaTags', 'refresh');
            }

            $getData = $this->seo_model->getTagsData($iRestaurantID);
            $viewData['tagData'] = $getData;
            $this->load->view('seo/add_meta_tags', $viewData);
        }catch(Exception $ex){
            throw new Exception('Error in seo addMetaTags function - ' . $ex);
        }
    }

    function staticMetaTags() {
        $viewData   = array("title" => "SEO");
        $this->load->view('seo/view_static_meta_tags', $viewData);
    }

    function paginateStaticMetaTags() {
        $data = $this->seo_model->getStaticTagsPagination();
        echo json_encode($data);
    }

    function addStaticMetaTags($iMetaTagId=''){
        try{
            $viewData['title'] = "Edit SEO Meta Tags";
            if ($this->input->post('action')) {
                if( $this->seo_model->updateStaticMetaTags($_POST) ){
                    $succ = array('0' => "Meta Tags updated successfully!");
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => "Unable to update Meta Tags!");
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('seo/staticMetaTags', 'refresh');
            }

            $getData = $this->seo_model->getStaticTagsData($iMetaTagId);
            $viewData['tagData'] = $getData;
            $this->load->view('seo/add_static_meta_tags', $viewData);
        }catch(Exception $ex){
            throw new Exception('Error in seo addStaticMetaTags function - ' . $ex);
        }
    }

}
