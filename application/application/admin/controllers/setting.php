<?php

class Setting extends CI_Controller {

    var $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('settings_model');
    }

    // function index()
    // {	
    // 	$viewData['title'] = 'Edit Settings';
    // 	$this->input->post('admin_fname');
    // 	if($this->input->post('admin_fname') && $this->input->post('admin_lname') && $this->input->post('admin_email'))
    // 	{
    // 		$editData = $this->admin_model->edit_admin_setting();
    // 		if($editData != '')
    // 		{
    // 			$succ = array('0' => SETTINGS_CHANGED);
    // 			$this->session->set_userdata('SUCCESS',$succ);
    // 		}else{
    // 			$err = array('0' => SETTINGS_NOT_CHANGED);
    // 			$this->session->set_userdata('ERROR',$err);
    // 		}
    // 	}
    // 	$tempVar = $this->admin_model->admin_setting();
    // 	if(count($tempVar) > 0)
    // 	{
    // 		$viewData['getSetting'] = $tempVar;
    // 	}else{
    // 		$err = array('0' => SETTING_NOT_FOUND);
    // 		$this->session->set_userdata('ERROR',$err);
    // 	}
    // 	$this->load->view('setting_view',$viewData);
    // }
    function index() {

        $getRecords = $this->settings_model->getalldata();

        $viewData = array("title" => "Setting Management");



        if (count($_POST) > 0) {
            $data = $this->settings_model->updatesetting();
            if ($data != '') {
                $succ = array('0' => SETTINGS_CHANGED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => SETTINGS_CHANGED);
                $this->session->set_userdata('SUCCESS', $err);
            }
        }
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $this->load->view('settings/setting_view', $viewData);
    }

}