<?php

class Reward extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('reward_model');
        $this->load->helper('string');
        $this->controller = 'reward';
        $this->uppercase = 'Reward';
        $this->title = 'Reward Management';
    }

    /*
     * TO DISPLAY LIST OF DEALS TO THE TABLE FORMAT...
     */

    function index() {
        $getRecords = $this->reward_model->getRewardDataAll();
        $viewData = array("title" => "Reward Management");
        $viewData['breadCrumbArr'] = array("reward" => "Reward Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';


        $this->load->view('reward/reward_view', $viewData);
    }

    /*
     * TO LIST OUT THE LIST OF RECORDS...
     */

    function paginate() {
        $data = $this->reward_model->get_paginationresult();
        echo json_encode($data);
    }

    /*
     * DELETE EITHER ONE RECORD OR LIST OF RECORDS... 
     */

    function deleteAll() {
        $data = $_POST['rows'];
        $removeDeal = $this->reward_model->removeReward($_POST['rows']);
        if ($removeDeal != '') {
            echo '1';
        } else {
            echo '0';
        }
    }

    /*
     * TO CHANGE THE STATUS OF THE RECORD..
     */

    function status($iRewardID = '', $rm = '') {
        if ($iRewardID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->reward_model->changeRewardStatus($iRewardID);

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

    function add($iRewardID = '', $ed = '') {
        $viewData['title'] = "Reward Management";

        $viewData['ACTION_LABEL'] = (isset($iRewardID) && $iRewardID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iRewardID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->reward_model->getRewardDataById($iRewardID);

            $viewData['getRewardData'] = $getData;
        }

        /*
         * GET RESTAURANT LIST
         */
        $viewData['getRestaurantData'] = $this->reward_model->getRestaurantList();

        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.rewardadd') {
            $rewardAdd = $this->reward_model->addReward($_POST);
            if ($rewardAdd != '') {
                if (isset($_FILES) && !empty($_FILES)) {
                    $param = array(
                        'fileType' => 'image',
                        'maxSize' => 20,
                        'uploadFor' => array(
                            'key' => 'reward',
                            'id' => $rewardAdd
                        ),
                        'requireThumb' => TRUE
                    );

                    $this->load->library('fileupload', $param);
                    $upload_files = $this->fileupload->upload($_FILES, 'vRewardImage');

                    foreach ($upload_files as $v) {
                        $images['vRewardImage'] = $v;
                        $this->reward_model->editRewardData($images, $rewardAdd);
                    }
                }
                $succ = array('0' => REWARD_ADDED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => REWARD_NOT_ADDED);
                $this->session->set_userdata('ERROR', $err);
            }
            redirect('reward/index', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.rewardedit') {
            $rewardEdit = $this->reward_model->editReward($_POST);
            //var_dump($dealsAdd);
            
            
            if ($this->input->post('iRewardID') != '') {
                $iRewardId = $this->input->post('iRewardID');

                $rewardData = $this->reward_model->getRewardDataById($_POST['iRewardID']);
                $targetpath = UPLOADS . "reward/" . $_POST['iRewardID'] . '/';
                $param = array(
                    'fileType' => 'image',
                    'maxSize' => 20,
                    'uploadFor' => array(
                        'key' => 'reward',
                        'id' => $iRewardId
                    ),
                    'requireThumb' => TRUE
                );
                $this->load->library('fileupload', $param);

                if ($this->input->post('removepic') == '1') {

                    $this->fileupload->removeFile();

                    /*
                     * UPDATE IMAGE TO NULL VALUE
                     */
                    $this->db->update('tbl_reward', array('vRewardImage' => ''), array('iRewardID' => $iRewardId));
                }

                if (isset($_FILES) && !empty($_FILES)) {

                    $this->fileupload->removeFile();
                    $upload_files = $this->fileupload->upload($_FILES, 'vRewardImage');
                    foreach ($upload_files as $v) {
                        $images['vRewardImage'] = $v;
                        $this->reward_model->editRewardData($images, $iRewardId);
                    }

                    //print_r($upload_files);
                }

                $succ = array('0' => REWARD_EDITED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => REWARD_NOT_EDITED);
                $this->session->set_userdata('ERROR', $err);
            }
            //exit;
            redirect('reward/index', 'refresh');
        }

        $this->load->view('reward/reward_add_view', $viewData);
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

}

?>
