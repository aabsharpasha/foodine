<?php

class Testing extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
    }

    /*
     * TO DISPLAY LIST OF DEALS TO THE TABLE FORMAT...
     */

    function index() {
        $this->load->library('maillib');
        $param = array(
            '%MAILSUBJECT%' => 'Blacklist : Welcome',
            '%LOGO_IMAGE%' => DOMAIN_URL . '/images/blacklist.jpg',
            '%RESTAURANT_NAME%' => 'Testing Restaurnat Name',
            '%CMS_LOGIN_LINK%' => BASEURL . 'login',
            '%USERNAME%' => 'test@test.com',
            '%PASSWORD%' => base64_encode('testing')
        );
        $tmplt = DIR_ADMIN_VIEW . 'restaurant/email/new_restaurant.php';
        $subject = 'Blacklist : Welcome';
        $to = 'chintan.openxcell@gmail.com';
        //$this->smtpmail_model->send($to, $subject, $tmplt, $param);
        $this->maillib->sendMail($to, $subject, $tmplt, $param);
    }

    function notify() {
        error_reporting(0);
        $this->load->library('pushnotify');
        $platform = 1;
        $token = 'APA91bGj-ukCD1agSZhCvZzAfPYLm_N__UeD6jktqVLYos-TYJKJaRTAN4_kdGjOVWcQsEiEM1gD7DRfqBM7Sw00C4s6-NchpT61smMZt7veDkJ_5H5uEl9zmuStU3uNrU-xtxtjnP1p';
        $msg = 'You table request has been rejected.';
        $extra = array(
            'type' => 'table',
            'id' => 1
        );
        $return = $this->pushnotify->sendIt($platform, $token, $msg, 1, $extra);
        echo $return;
    }

    function email($type = 1) {
        switch ($type) {
            case 1:
                $this->load->view('template/new_res');
                $tmplt = DIR_ADMIN_VIEW . 'template/new_res.php';
                $backImg = 'new_restaurant.jpg';
                break;

            case 2:
                $this->load->view('template/new_user');
                $tmplt = DIR_ADMIN_VIEW . 'template/new_user.php';
                $backImg = 'new_user.jpg';
                break;
        }


        $this->load->library('maillib');
        $param = array(
            '%MAILSUBJECT%' => 'HungerMafia : Welcome',
            '%FONT1%' => DOMAIN_URL . '/images/fonts/HarabaraMais.otf',
            '%FONT2%' => DOMAIN_URL . '/images/fonts/NexaLight.otf',
            '%LOGO_IMAGE%' => DOMAIN_URL . '/images/hungermafia.png',
            '%BACK_IMAGE%' => DOMAIN_URL . '/images/email/' . $backImg,
            '%FT1%' => DOMAIN_URL . '/images/email/fb.png',
            '%FT2%' => DOMAIN_URL . '/images/email/twitter.png',
            '%FT3%' => DOMAIN_URL . '/images/email/linkedin.png',
            '%FT4%' => DOMAIN_URL . '/images/email/youtube.png',
            '%RESTAURANT_NAME%' => 'Khana Khajana',
            '%CMS_LOGIN_LINK%' => BASEURL . 'login',
            '%USERNAME%' => 'chintan.goswami@openxcetechnolabs.com',
            '%PASSWORD%' => '123456'
        );

        $subject = 'HungerMafia : Welcome';
        $to = 'chintan.goswami@openxcelltechnolabs.com';
        //$this->smtpmail_model->send($to, $subject, $tmplt, $param);
        $this->maillib->sendMail($to, $subject, $tmplt, $param);
    }

    function database_err() {
        $this->db->trans_start();
        $this->db->query('AN SQL QUERY...');
        $this->db->query('ANOTHER QUERY...');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo 'Error found !!';
            // generate an error... or use the log_message() function to log your error
        }
    }

    function compress_image() {

        $default_path = UPLOADS . 'restaurant/';

        echo '<pre/>';
        $dirs = array_filter(glob($default_path . '*', GLOB_ONLYDIR), 'is_dir');
        //print_r($dirs);

        foreach ($dirs as $key => $val) {
            $val = end(explode('/', $val));
            $new_default_path = $default_path . $val;
            $files = scandir($new_default_path);

            //echo 'TOTAL FILES ' . PHP_EOL;
            //print_r($files);
            foreach ($files as $file) {
                $file_src = $new_default_path . '/' . $file;
                if (is_file($file_src)) {
                    echo '--------------------------------------' . PHP_EOL;
                    echo $file_src . PHP_EOL;
                    echo 'BEFORE - ' . (filesize($file_src) / 1024) . ' KB' . PHP_EOL;
                    $fInfo = getimagesize($file_src);
                    switch ($fInfo['mime']) {
                        case 'image/jpeg':
                            $IMAGE = imagecreatefromjpeg($file_src);
                            break;

                        case 'image/png':
                            $IMAGE = imagecreatefrompng($file_src);
                            break;

                        case 'image/gif':
                            $IMAGE = imagecreatefromgif($file_src);
                            break;
                    }

                    /*
                     * COMPRESS AND SAVE FILE TO JPEG
                     */
                    imagejpeg($IMAGE, $file_src, 70);
                    echo 'AFTER - ' . (filesize($file_src) / 1024) . ' KB' . PHP_EOL;
                }
            }
            echo '========================================' . PHP_EOL;
        }
    }

    function test_sms() {
        try {

            $this->load->library('smslane');
            for ($i = 0; $i < 100; $i++) {
                $text = 'Your reservation request has been declined due to unavailability at TGB' . ($i + 1) . '.';
                $this->smslane->send(array('918460694787','917878727176'), $text);
            }
        } catch (Exception $ex) {
            throw new Exception(__CLASS__ . '::' . __FUNCTION__ . '::' . $ex);
        }
    }

}

?>
