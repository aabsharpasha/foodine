<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of pickup_model
 * @author OpenXcell Technolabs
 */
class FileUpload_Model extends CI_Model {

    var $config;
    var $defaultpath, $fileType, $maxSize, $allowEncrypt;

    function __construct() {
        parent::__construct();
        
        $this->fileType = $fileType;
        //$this->_configure($fileType);
        $this->load->library('upload');
    }

    private function _configure1($uploadType) {

        $this->_settings($uploadType);

        $this->config['upload_path'] = $this->defaultpath;
        $this->config['allowed_types'] = $this->fileType;
        $this->config['max_size'] = $this->maxSize;
        $this->config['encrypt_name'] = $this->allowEncrypt;
    }

    private function _settings1($uploadType) {

        switch ($uploadType) {
            case 'image' :
                $this->fileType = 'gif|jpg|png|jpeg';   // image file allow only...
                break;

            default :
                $this->fileType = 'gif|jpg|png|jpeg';   // default we upload image only...
                break;
        }

        $this->_setValue();
    }

    private function _setValue1() {
        $this->defaultpath = UPLOADS;
        $this->maxSize = 1024 * 20; // 20MB 
        $this->allowEncrypt = TRUE; // to encrypt the file name...
    }

    private function _changePath1($uploadFor) {
        $uploadForKey = $uploadFor['key'];
        switch ($uploadFor) {
            case 'restaurant' :
                $this->defaultpath .= 'restaurant/';
                break;

            default :
                $this->defaultpath .= 'restaurant/';
                break;
        }
        $uploadForId = $uploadFor['id'];

        $this->_createDIR($uploadForId);
    }

    private function _createDIR1($id = '') {
        /*
         * FOR TARGET PATH....
         */
        !is_dir($this->defaultpath . '/' . $id) ? mkdir($this->defaultpath, 0777, TRUE) : '';

        /*
         * FOR THUMBNAIL PATH...
         */
        !is_dir($this->defaultpath . '/' . $id . ($id !== '' ? '/' : '') . 'thumb/') ? mkdir($this->defaultpath . '/' . $id . ($id !== '' ? '/' : '') . 'thumb/', 0777, TRUE) : '';
    }

    function upload1($fileArry = array(), $uploadFor = array('key' => 'restaurant', 'id' => 1)) {


        //mprd($fileArry);

        $this->_changePath($uploadFor);

        $this->upload->initialize($this->config);


        mprd($this->upload);




        if ($this->upload->do_upload('vRestaurantLogo')) {
            
        }
    }

    
    
    /*
     * TO CREATE AN DIRECTORY OF THE TARGETED FOLDER...
     */
    
    private function _createDIR(){
        try {
            
        } catch(Exception $ex) {
            exit('FileUpload Model : Error in _createDIR function - ' . $ex);
        }
    }

}
