<?php

/**
 * Description of pickup_model
 * @author OpenXcell Technolabs
 */
class FileUpload {

    var $config, $thumbConfig;
    var $defaultpath, $fileType, $maxSize, $requireThumb;

    function __construct($fileType = array()) {
        $this->CI = &get_instance();
        $this->CI->load->library('upload');
        $this->CI->load->library('aws_sdk');
        $this->_initialize($fileType);
    }

    /*
     * PREPARE FUNCTION WHICH UPLOADS THE FILE(S)...
     * PARAM
     *      - FILE_ARRY     :   file array that we are uploading...
     *      - FILE_ARRY_KEY :   file array key for using we preparing a array...
     */

    function upload($fileArry = array(), $fileArryKey = '') {
        try {
            $uploadedFiles = array();
            $fileArry = $this->_prepareArry($fileArry[$fileArryKey]);

            //mprd($fileArry);
//print_r($this->config);
            $this->CI->upload->initialize($this->config);

            //mprd($this->CI->upload);



            for ($i = 0; $i < count($fileArry); $i++) {               
                $_FILES['image_file'] = $fileArry[$i];
                if ($this->CI->upload->do_upload('image_file')) {
                    extract($this->CI->upload->data('image_file'));
                    $file = $this->CI->upload->data('image_file');                   
                    $keyImage = array_reverse(explode('images/', $file['full_path']));
//                    $aws_object = $this->CI->aws_sdk->saveObject(array(
//                        'Key' => 'images/'.$keyImage[0],
//                        'ContentType' => $file['file_type'],
//                        'ACL' => 'public-read',
//                        'SourceFile' => $file['full_path'],
//                    ));
                    //echo $file_name;die;
                    $uploadedFiles[] = $file_name;
                // if($keyImage[0] == 'vTableLayout')
                //   print_r($this->CI->upload->data());
                } else {
                    //echo 'in';
                    echo $this->CI->upload->display_errors(); exit;
                }
            }

            /*
             * COMPRESS THE IMAGE AND CONVERT THOSE IMAGES TO THE JPEG FORMAT
             */
            $this->_2JPG($uploadedFiles);

            /*
             * COPYRIGHT TEXT WILL BE HERE....
             * WATER-MARK
             */


            /*
             * TO CREATE A THUMBNAIL...
             */
            if ($this->requireThumb) {
                $this->_createThumbNail($uploadedFiles);
            }
            
            return $uploadedFiles;
        } catch (Exception $ex) {
            exit('FileUpload Model : Error in upload function - ' . $ex);
        }
    }

    /*
     * CONVERT ANY IMAGE TO JPG
     *  - MAIN ADVANTAGE OF THIS FUNCTION IS THAT TO COMPRESS THE IMAGE
     *    BUT PARRALEL WE HAVE TO MAINTAIN THE IMAGE QUALITY
     */

    private function _2JPG($uploadedFiles) {
        try {
            $CompressQuality = 25;
            foreach ($uploadedFiles as $V) {
                $SRC = $this->defaultpath . $V;
                $Info = getimagesize($SRC);

                //print_r($Info);

                switch ($Info['mime']) {
                    case 'image/jpeg':
                        $IMAGE = imagecreatefromjpeg($SRC);
                        break;

                    case 'image/png':
                        $IMAGE = imagecreatefrompng($SRC);
                        break;

                    case 'image/gif':
                        $IMAGE = imagecreatefromgif($SRC);
                        break;
                }


                /*
                 * COMPRESS AND SAVE FILE TO JPEG
                 */
                imagejpeg($IMAGE, $SRC, $CompressQuality);
            }
        } catch (Exception $ex) {
            throw new Exception('FUpload Library : Error in _fToJPG function - ' . $ex);
        }
    }

    /*
     * TO REMOVE FILES
     */

    function removeFile() {
        $this->CI->load->helper("file");
        delete_files($this->defaultpath . "/");
    }

    /*
     * CREATE THUMBNAIL...
     */

    private function _createThumbNail($fileArry, $ratio = 200) {
        $this->_thumbConfig();
        for ($i = 0; $i < count($fileArry); $i++) {
            $actualPath = $this->defaultpath . $fileArry[$i];
            $thumbPath = $this->defaultpath . 'thumb/' . $fileArry[$i];

            $img_list = list($img_w, $img_h) = getimagesize($actualPath);
            $img_ratio = $ratio / min($img_w, $img_h);

            $new_size = array(
                'width' => $img_w * $img_ratio,
                'height' => $img_h * $img_ratio
            );

            $this->thumbConfig['source_image'] = $actualPath;
            $this->thumbConfig['new_image'] = $thumbPath;
            $this->thumbConfig['width'] = $new_size['width'];
            $this->thumbConfig['height'] = $new_size['height'];

            $this->CI->load->library('image_lib', $this->thumbConfig);
            $this->CI->image_lib->clear();
            $this->CI->image_lib->initialize($this->thumbConfig);
            if ($this->CI->image_lib->resize()) {
                $thumbFile = $this->CI->image_lib;
              //  print_r($thumbFile->new_image);
                $keyImage = array_reverse(explode('images/', $thumbFile->new_image));
             /*   $aws_object = $this->CI->aws_sdk->saveObject(array(
                    'Key' => 'images/'.$keyImage[0],
                    'ContentType' => $thumbFile->mime_type,
                    'ACL' => 'public-read',
                    'SourceFile' => $thumbFile->new_image,
                )); */
                chmod($thumbFile->new_image, 0777);
                chmod($thumbFile->full_src_path, 0777);
                //@unlink($thumbFile->new_image);
                //@unlink($thumbFile->full_src_path);
            }else{
                $this->fUploadError[] = $this->CI->image_lib->display_errors();
                print_r($this->CI->image_lib->display_errors()); exit;
            }
        }
    }

    private function _thumbConfig() {
        $this->thumbConfig['image_library'] = 'gd2';
        $this->thumbConfig['maintain_ratio'] = TRUE;
        $this->thumbConfig['create_thumb'] = FALSE;
    }

    /*
     * PREPARE ARRAY TO POST TO THE UPLOAD CLASS...
     */

    private function _prepareArry($arry) {
        $returnArry = array();

        $isMultiple = gettype($arry['name']) === 'string' ? FALSE : TRUE;
        if ($isMultiple) {
            $arryLen = count($arry['name']);
            for ($i = 0; $i < $arryLen; $i++) {
                $returnArry[$i]['name'] = $arry['name'][$i];
                $returnArry[$i]['type'] = $arry['type'][$i];
                $returnArry[$i]['tmp_name'] = $arry['tmp_name'][$i];
                $returnArry[$i]['error'] = $arry['error'][$i];
                $returnArry[$i]['size'] = $arry['size'][$i];
            }
        } else {
            $returnArry[0]['name'] = $arry['name'];
            $returnArry[0]['type'] = $arry['type'];
            $returnArry[0]['tmp_name'] = $arry['tmp_name'];
            $returnArry[0]['error'] = $arry['error'];
            $returnArry[0]['size'] = $arry['size'];
        }

        return $returnArry;
    }
    /*
     * TO SET THE VALUE OF REQUESTED VARIABLE...
     */

    function setConfig($arry) {

        /*
         * TO CREATE THE DYNAMIC VARIABLE...
         */

        foreach ($arry as $key => $val) {
            $this->$key = $val;
        }

        /*
         * INITIALIZE CONFIGURATION....
         */
        $this->_initConfig();
    }

    /*
     * TO SET THE VALUE OF REQUESTED VARIABLE...
     */

    private function _initialize($arry) {

        /*
         * TO CREATE THE DYNAMIC VARIABLE...
         */

        foreach ($arry as $key => $val) {
            $this->$key = $val;
        }

        /*
         * INITIALIZE CONFIGURATION....
         */
        $this->_initConfig();
    }

    /*
     * TO INITIALIZE CONFIGURATION...
     */

    private function _initConfig() {

        /*
         * TO SET TARGETED PATH...
         */
        $this->_setTargetedPath();

        /*
         * TO SET THE FILE TYPE...
         */
        $this->_setFileUploadType();

        /*
         * TO SET THE CONFIGURATION VALUE...
         */
        $this->config['upload_path'] = $this->defaultpath;
        $this->config['allowed_types'] = $this->uploadType;
        $this->config['max_size'] = 1024 * (int) $this->maxSize;
        $this->config['encrypt_name'] = TRUE;
    }

    /*
     * TO SET THE DEFAULT TARGETED PATH...
     */

    private function _setTargetedPath() {
        extract($this->uploadFor);
//echo $key;
        $this->defaultpath = UPLOADS;
        switch ($key) {
            case 'banner' :
                $this->defaultpath .= 'banner/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;
            case 'restaurant' :
                $this->defaultpath .= 'restaurant/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;

            case 'vTableLayout' :
                $this->defaultpath .= 'vTableLayout/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;
            case 'restaurantPhoto' :
                $this->defaultpath .= 'restaurantPhoto/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;

            case 'restaurantMenu' :
                $this->defaultpath .= 'restaurantMenu/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;

            case 'cuisine' :
                $this->defaultpath .= 'cuisine/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;

            case 'category' :
                $this->defaultpath .= 'category/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;

            case 'reward' :
                $this->defaultpath .= 'reward/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;

            case 'deal' :
                $this->defaultpath .= 'deal/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;
            case 'combo' :
                $this->defaultpath .= 'combo/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;
            case 'event' :
                $this->defaultpath .= 'event/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;
            case 'restaurantMobile' :
                $this->defaultpath .= 'restaurantMobile/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;
            case 'restaurantListing' :
                $this->defaultpath .= 'restaurantListing/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;

            case 'notification' :
                $this->defaultpath .= 'pushNotification/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;

            case 'orderMenuItem' :
                $this->defaultpath .= 'orderMenuItem/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;

            case 'weAreHiringTeam' :
                $this->defaultpath .= 'weAreHiringTeam/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;

            default :
                $this->defaultpath .= 'restaurant/';
                $this->_createDIR();

                $this->defaultpath .= $id . '/';
                $this->_createDIR(TRUE);
                break;
        }
    }

    /*
     * TO CREATE THE DIRECTORY...
     */

    private function _createDIR($thumb = FALSE) {

        !is_dir($this->defaultpath) ? mkdir($this->defaultpath, 0777, TRUE) : '';

        if ($thumb) {
            !is_dir($this->defaultpath . 'thumb/') ? mkdir($this->defaultpath . 'thumb/', 0777, TRUE) : '';
        }
    }

    /*
     * TO SET THE FILE UPLOAD TYPE...
     */

    private function _setFileUploadType() {
        switch ($this->fileType) {
            case 'image' :
                $this->uploadType = 'gif|jpg|png|jpeg';   // image file allow only...
                break;

            case 'text' :
                $this->uploadType = 'txt';   // text file allow only...
                break;

            case 'video' :
                $this->uploadType = 'mp4|wmv|mkv|3gp|avi';   // video file allow only...
                break;

            default :
                $this->uploadType = 'gif|jpg|png|jpeg';   // image file allow only...
                break;
        }
    }

}
