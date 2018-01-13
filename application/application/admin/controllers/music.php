<?php

class Music extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('Datatables.php');
        $this->load->library('DatatablesHelper');
        $this->load->model('music_model');
        $this->load->helper('string');
        $this->controller = 'music';
        $this->uppercase = 'Music';
        $this->title = 'Music Management';
    }

    // INDEX
    function index($iUserID = '') {
        $getRecords = $this->music_model->getMusicDataAll();
        $viewData = array("title" => "Music Management");
        $viewData['breadCrumbArr'] = array("music" => "Music Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';

        $viewData['get_iUserID'] = $iUserID;

        $this->load->view('music/music_view', $viewData);
    }

    // ***************************************************************
    // ADD
    // ***************************************************************
    function add($iMusicID = '', $ed = '') {
        $viewData['title'] = "Music Management";

        $viewData['ACTION_LABEL'] = (isset($iMusicID) && $iMusicID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iMusicID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->music_model->getMusicDataById($iMusicID);

            $viewData['getMusicData'] = $getData;
        }
        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.musicadd') {
            $check = $this->music_model->checkMusicNameAvailable($this->input->post('vMusicName'));
            if ($check) {
                $musicEdit = $this->music_model->addMusic($_POST);
                if ($musicEdit != '') {
                    $succ = array('0' => MUSIC_ADDED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => MUSIC_NOT_ADDED);
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('music/index', 'refresh');
            } else {
                $err = array('0' => MUSIC_MUSICNAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
            }
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.musicedit') {
            $check = $this->music_model->checkMusicNameAvailable($this->input->post('vMusicName'), $this->input->post('iMusicID'));

            if ($check) {
                $musicEdit = $this->music_model->editMusic($_POST);
                $iMusicID = $this->input->post('iMusicID');
                if ($musicEdit != '') {
                    $succ = array('0' => MUSIC_EDITED);
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => MUSIC_EDITED);
                    $this->session->set_userdata('SUCCESS', $err);
                }

                redirect('music/index', 'refresh');
            } else {
                $err = array('0' => MUSIC_MUSICNAME_EXISTS);
                $this->session->set_userdata('ERROR', $err);
            }
        }

        $this->load->view('music/music_add_view', $viewData);
    }

    // ***************************************************************
    // STATUS
    // ***************************************************************
    function status($iMusicID = '', $rm = '') {
        if ($iMusicID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->music_model->changeMusicStatus($iMusicID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("music", 'refresh');
    }

    // ***************************************************************
    // Order history
    // ***************************************************************

    function get_ordered_music($iOutletID) {
        $viewData = array(
            "title" => $this->title,
            'page' => 'music',
            'outlet_id' => $iOutletID
        );
        $this->load->view("music/music_view", $viewData);
    }

    // ***************************************************************
    // REMOVE
    // ***************************************************************
    function remove($iMusicID = '', $rm = '') {
        if ($iMusicID != '' && $rm != '' && $rm == 'y') {
            $removeMusic = $this->music_model->removeMusic($iMusicID);
            if ($removeMusic != '') {
                $succ = array('0' => MUSIC_DELETED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => MUSIC_NOT_DELETED);
                $this->session->set_userdata('ERROR', $err);
            }
        }
        redirect("music/index", 'refresh');
    }

    function deleteComment() {
        $removeComment = $this->music_model->removeComment();
        echo $removeComment;
    }

    // ***************************************************************
    function deleteAll() {
        $data = $_POST['rows'];

        $removeMusic = $this->music_model->removeMusic($_POST['rows']);
        if ($removeMusic != '') {
            echo '1';
        } else {
            echo '0';
        }
        //redirect("music", "refresh");
    }

    function paginate() {
        $data = $this->music_model->get_paginationresult();
        echo json_encode($data);
    }

    function getCommentList() {
        $data = $this->music_model->getCommentList();
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
