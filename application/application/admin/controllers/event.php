<?php

class Event extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('event_model');
        $this->load->model('offers_model');
        $this->load->helper('string');
        $this->controller = 'event';
        $this->uppercase = 'Event';
        $this->title = 'Event Management';
        $this->load->library('aws_sdk');
    }

    // INDEX
    function index($iUserID = '') {
        $getRecords = $this->event_model->getEventDataAll();
        $viewData = array("title" => "Event Management");
        $viewData['breadCrumbArr'] = array("event" => "Event Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $viewData['get_iUserID'] = $iUserID;

        $this->load->view('event/event_view', $viewData);
    }

    // ***************************************************************
    // ADD
    // ***************************************************************
    function add($iEventID = '', $ed = '') {
        $viewData['title'] = "Event Management";

        $viewData['ACTION_LABEL'] = (isset($iEventID) && $iEventID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iEventID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->event_model->getEventDataById($iEventID);
            $viewData['getEventData'] = $getData;
        }

        $viewData['getRestaurantData'] = $this->offers_model->getRestaurantList();
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.eventadd') {
            $eventEdit = $this->event_model->addEvent($_POST);
            if ($eventEdit != '') {

                if (isset($_FILES) && !empty($_FILES)) {

                    $param = array(
                        'fileType' => 'image',
                        'maxSize' => 20,
                        'uploadFor' => array(
                            'key' => 'event',
                            'id' => $_POST['iRestaurantID']
                        ),
                        'requireThumb' => TRUE
                    );
                    $this->load->library('fileupload', $param);
                    if (isset($_FILES) && !empty($_FILES) && $_FILES['iEventImage']['error'] === 0) {
                        //$this->fileupload->removeFile();
                        $upload_files = $this->fileupload->upload($_FILES, 'iEventImage');

                        if (!empty($upload_files)) {

                            /*
                             * NEED TO UPDATE THE FILE NAME TO DATABASE...
                             */

                            foreach ($upload_files as $V) {
                                $this->event_model->updateEventImage($eventEdit, $V);
                            }
                            $fileChange = TRUE;
                        }
                    }
                }

                $succ = array('0' => EVENT_ADDED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => EVENT_NOT_ADDED);
                $this->session->set_userdata('ERROR', $err);
            }
            redirect('event/index', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.eventedit') {
            $eventEdit = $this->event_model->editEvent($_POST);
            $iEventID = $this->input->post('iEventID');
            $fileChange = FALSE;
//            mprd($iEventID);

            $param = array(
                'fileType' => 'image',
                'maxSize' => 20,
                'uploadFor' => array(
                    'key' => 'event',
                    'id' => $_POST['iRestaurantID']
                ),
                'requireThumb' => TRUE
            );

            $this->load->library('fileupload', $param);

            if ($this->input->post('removepic') == '1') {
                $removeImg = $this->input->post('iEventUrl');
                if (!empty($removeImg)) {
                    $ImagePath = 'event/' . $_POST['iRestaurantID'] . '/' . $removeImg;
                    $ImageThumbPath = 'event/' . $_POST['iRestaurantID'] . '/thumb/' . $removeImg;
                    //$this->aws_sdk->deleteImage($ImagePath);
                    //$this->aws_sdk->deleteImage($ImageThumbPath);
                }
                // $this->fileupload->removeFile();

                /*
                 * UPDATE IMAGE TO NULL VALUE
                 */
                $this->db->update('tbl_restaurant_event', array('iEventImage' => ''), array('iEventId' => $iEventID));
            }

            if (isset($_FILES) && !empty($_FILES) && $_FILES['iEventImage']['error'] === 0) {

                $param = array(
                    'fileType' => 'image',
                    '$parammaxSize' => 20,
                    'uploadFor' => array(
                        'key' => 'event',
                        'id' => $_POST['iRestaurantID']
                    ),
                    'requireThumb' => TRUE
                );

                $this->load->library('fileupload', $param);
                //$this->fileupload->removeFile();
                $upload_files = $this->fileupload->upload($_FILES, 'iEventImage');

                if (!empty($upload_files)) {

                    /*
                     * NEED TO UPDATE THE FILE NAME TO DATABASE...
                     */

                    foreach ($upload_files as $V) {
                        $this->event_model->updateEventImage($iEventID, $V);
                    }
                    $oldImg = $this->input->post('iEventUrl');
                    if (!empty($oldImg)) {
                        $ImagePath = 'event/' . $_POST['iRestaurantID'] . '/' . $oldImg;
                        $ImageThumbPath = 'event/' . $_POST['iRestaurantID'] . '/thumb/' . $oldImg;
                        //$this->aws_sdk->deleteImage($ImagePath);
                        //$this->aws_sdk->deleteImage($ImageThumbPath);
                    }
                    $fileChange = TRUE;
                }
            }
            if ($eventEdit != '' || $fileChange) {
                $succ = array('0' => EVENT_ADDED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => EVENT_EDITED);
                $this->session->set_userdata('SUCCESS', $err);
            }

            redirect('event/index', 'refresh');
        }

        $this->load->view('event/event_add_view', $viewData);
    }

    // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iEventID = '', $rm = '') {
        if ($iEventID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->event_model->changeEventStatus($iEventID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("event", 'refresh');
    }

    // ***************************************************************
    // Order history
    // ***************************************************************

    function get_ordered_event($iOutletID) {
        $viewData = array(
            "title" => $this->title,
            'page' => 'event',
            'outlet_id' => $iOutletID
        );
        $this->load->view("event/event_view", $viewData);
    }

    // ***************************************************************
    // REMOVE
    // ***************************************************************
    function remove($iEventID = '', $rm = '') {
        if ($iEventID != '' && $rm != '' && $rm == 'y') {
            $removeEvent = $this->event_model->removeEvent($iEventID);
            if ($removeEvent != '') {
                $succ = array('0' => EVENT_DELETED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => EVENT_NOT_DELETED);
                $this->session->set_userdata('ERROR', $err);
            }
        }
        redirect("event/index", 'refresh');
    }

    // ***************************************************************
    function deleteAll() {
        $data = $_POST['rows'];
        $restId=$this->event_model->getRestarantId($_POST['rows'][0]);
        $removeEvent = $this->event_model->removeEvent($_POST['rows']);
        //print_r($removeEvent);die;
        if ($removeEvent != '') {
            //$res=$this->aws_sdk->deleteDirectory('event/'.$restId[0]['iRestaurantID']);
            echo '1';
        } else {
            echo '0';
        }
        //redirect("event", "refresh");
    }

    function paginate() {
        $data = $this->event_model->get_paginationresult();
        echo json_encode($data);
    }

    function getCommentList() {
        $data = $this->event_model->getCommentList();
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

    /*     * ******************* End of the File ***************************** */
}
