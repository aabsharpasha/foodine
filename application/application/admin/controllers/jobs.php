<?php

class Jobs extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('jobs_model');
        $this->load->helper('string');
        $this->controller = 'jobs';
        $this->uppercase = 'Jobs';
        $this->title = 'Jobs Management';
    }
  
    
     // viewTeam
    function viewJobs($iJobDetailID = '') {
        $getRecords = $this->jobs_model->getJobsDataAll();
        $locations = $this->jobs_model->getAllLocation();
        $viewData   = array("Locations" => $locations);
        $viewData   = array("title" => "view Jobs");
        $viewData['breadCrumbArr'] = array("team" => "Jobs Management");
       if ($getRecords != '')
           $viewData['record_set'] = $getRecords;
        else
           $viewData['record_set'] = '';

       $viewData['get_iUserID'] = $iJobDetailID;

        $this->load->view('jobs/view_jobs', $viewData);
    }
  
    
    //paginate
     function paginate() {
        $data = $this->jobs_model->get_paginationresult();
        echo json_encode($data);
    }
    
    
      // addTeam
    function addJobs($iJobDetailID = '', $ed = '') {
        $locations = $this->jobs_model->getAllLocation();
        $viewData   = array("title" => "add Job");
        $viewData   = array("Locations" => $locations);
         $viewData['ACTION_LABEL'] = (isset($iJobDetailID) && $iJobDetailID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iJobDetailID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->jobs_model->getJobDataById($iJobDetailID);
            $viewData['getJobData'] = $getData;
        }
        
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.jobadd') {
            $jobEdit = $this->jobs_model->addJob($_POST);
                if ($jobEdit != '') {
                    $succ = array('0' => JOB_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => JOB_NOT_ADDED);
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('jobs/viewJobs', 'refresh');
            
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.jobedit') {
           
                $jobEdit = $this->jobs_model->editJob($_POST);
                $iJobDetailID = $this->input->post('iJobDetailID');
               
                if ($jobEdit != '') {
                    $succ = array('0' => JOB_EDITED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => JOB_NOT_EDITED);
                    $this->session->set_userdata('SUCCESS', $err);
                }

                redirect('jobs/viewJobs', 'refresh');
           
        }

        $this->load->view('jobs/add_jobs', $viewData);
    }
    
    
    
    
    
    
      // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iJobDetailID = '', $rm = '') {
        if ($iJobDetailID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->jobs_model->changeJobStatus($iJobDetailID);

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

        $removeMember = $this->jobs_model->removeJob($_POST['rows']);
        if ($removeMember != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("category", "refresh");
    }
    
    /*     * ******************* End of the File ***************************** */
}
