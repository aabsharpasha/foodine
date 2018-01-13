<?php

class Application extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('application_model');
        $this->load->helper('string');
        $this->controller = 'application';
        $this->uppercase = 'Job Application';
        $this->title = 'Jobs Application';
    }
  
    
     // viewTeam
    function view($iApplicationID = '') {
        $getRecords = $this->application_model->getApplicationDataAll();
       
        $viewData   = array("title" => "view Job Applications");
        $viewData['breadCrumbArr'] = array("team" => "Job Applications");
       if ($getRecords != '')
           $viewData['record_set'] = $getRecords;
        else
           $viewData['record_set'] = '';

             $viewData['iApplicationID'] = $iApplicationID;

        $this->load->view('application/view', $viewData);
    }
  
    
    //paginate
     function paginate() {
        $data = $this->application_model->get_paginationresult();
        echo json_encode($data);
    }
    
    
    
      // ***************************************************************
    function deleteAll() {
        $data = $_POST['rows'];

        $removeMember = $this->application_model->removeApplication($_POST['rows']);
        if ($removeMember != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("category", "refresh");
    }
    
    /*     * ******************* End of the File ***************************** */
}
