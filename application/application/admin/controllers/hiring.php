<?php

class Hiring extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('hiring_model');
        $this->load->helper('string');
        $this->controller = 'hiring';
        $this->uppercase = 'Team';
        $this->title = 'Team Management';
        $this->load->library('aws_sdk');
    }
  
    
     // viewTeam
    function viewTeam($iUserID = '') {
        $getRecords = $this->hiring_model->getMemberDataAll();
        $viewData   = array("title" => "view Team");
        $viewData['breadCrumbArr'] = array("team" => "Team Management");
       if ($getRecords != '')
           $viewData['record_set'] = $getRecords;
        else
           $viewData['record_set'] = '';

       $viewData['get_iUserID'] = $iUserID;

        $this->load->view('hiring/view_team', $viewData);
    }
  
    
      // addTeam
    function addTeam($iMemberID = '', $ed = '') {
       // $getRecords = $this->category_model->getCategoryDataAll();
        $viewData   = array("title" => "add Team");
         $viewData['ACTION_LABEL'] = (isset($iMemberID) && $iMemberID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iMemberID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->hiring_model->getMemberDataById($iMemberID);

            $viewData['getMemberData'] = $getData;
        }
        
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.teamadd') {
            //$check = $this->hiring_model->checkMemberNameAvailable($this->input->post('vMemberName'));
            $check = 1;
            if ($check) {
                $memberEdit = $this->hiring_model->addMember($_POST);
                if ($memberEdit != '') {
                    if (isset($_FILES) && !empty($_FILES) && $_FILES['vMemberImage']['error'] === 0) {

                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'weAreHiringTeam',
                                'id' => $memberEdit
                            ),
                            'requireThumb' => TRUE
                        );

                        $this->load->library('fileupload', $param);
                        $upload_files = $this->fileupload->upload($_FILES, 'vMemberImage');
                       
                        if (!empty($upload_files)) {
                            /*
                             * NEED TO UPDATE THE FILE NAME TO DATABASE...
                             */
                            foreach ($upload_files as $V) {
                                $this->hiring_model->updateMemberImage($memberEdit, $V);
                            }
                        }
                    }

                    $succ = array('0' => TEAM_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => JOB_NOT_EDITED);
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('hiring/viewTeam', 'refresh');
            } else {
                $err = array('0' => MEMBER_NAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
            }
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.teamedit') {
            //$check = $this->hiring_model->checkMemberNameAvailable($this->input->post('vMemberName'), $this->input->post('iMemberID'));
            $check = 1;
            if ($check) {
                $memberEdit = $this->hiring_model->editMember($_POST);
                $iMemberID = $this->input->post('iMemberID');
                $fileChange = FALSE;

                $param = array(
                    'fileType' => 'image',
                    'maxSize' => 20,
                    'uploadFor' => array(
                        'key' => 'weAreHiringTeam',
                        'id' => $iMemberID
                    ),
                    'requireThumb' => TRUE
                );

                $this->load->library('fileupload', $param);

//                if ($this->input->post('removepic') == '1') {
//
//                    $this->fileupload->removeFile();
//
//                    /*
//                     * UPDATE IMAGE TO NULL VALUE
//                     */
//                    $this->db->update('tbl_job_team_member', array('vMemberImage' => ''), array('iMemberID' => $iMemberID));
//                }

                if (isset($_FILES['vMemberImage']['name']) && !empty($_FILES['vMemberImage']['name'])) {

                    $this->fileupload->removeFile();
                    $upload_files = $this->fileupload->upload($_FILES, 'vMemberImage');

                    if (!empty($upload_files)) {
                        /*
                         * NEED TO UPDATE THE FILE NAME TO DATABASE...
                         */
                        foreach ($upload_files as $V) {
                            $this->hiring_model->updateMemberImage($iMemberID, $V);
                        }
                        $fileChange = TRUE;
                    }
                }

                if ($memberEdit != '' || $fileChange) {
                    $succ = array('0' => TEAM_EDITED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => TEAM_EDITED);
                    $this->session->set_userdata('SUCCESS', $err);
                }

                redirect('hiring/viewTeam', 'refresh');
            } else {
                $err = array('0' => MEMBER_NAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
            }
        }

        $this->load->view('hiring/add_team', $viewData);
    }
    
    
    
     function paginate() {
        $data = $this->hiring_model->get_paginationresult();
        echo json_encode($data);
    }
    
    
    
    
      // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iMemberID = '', $rm = '') {
        if ($iMemberID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->hiring_model->changeMemberStatus($iMemberID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("category", 'refresh');
    }
    
    
    
      // ***************************************************************
    function deleteAll() {
        $data = $_POST['rows'];

        $removeMember = $this->hiring_model->removeMember($_POST['rows']);
        if ($removeMember != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("category", "refresh");
    }
    
    /*     * ******************* End of the File ***************************** */
}
