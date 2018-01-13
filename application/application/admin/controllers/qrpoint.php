<?php

class QRPoint extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('qrpoint_model');
        $this->load->helper('string');
        $this->controller = 'qrpoint';
        $this->uppercase = 'QRPoint';
        $this->title = 'QRPoint Management';
    }

    /*
     * TO DISPLAY LIST OF DEALS TO THE TABLE FORMAT...
     */

    function index() {
        $getRecords = $this->qrpoint_model->getQRPointDataAll();
        $viewData = array("title" => "QRPoint Management");
        $viewData['breadCrumbArr'] = array("deals" => "QRPoint Management");
        if ($getRecords != '') {
            $viewData['record_set'] = $getRecords;
        } else {
            $viewData['record_set'] = '';
        }

        $this->load->view('qrpoint/qrpoint_view', $viewData);
    }

    /*
     * TO LIST OUT THE LIST OF RECORDS...
     */

    function paginate() {
        $data = $this->qrpoint_model->get_paginationresult();
        echo json_encode($data);
    }

    /*
     * DELETE EITHER ONE RECORD OR LIST OF RECORDS... 
     */

    function deleteAll() {
        $data = $_POST['rows'];
        $removeDeal = $this->qrpoint_model->removeQRPoint($_POST['rows']);
        if ($removeDeal != '') {
            echo '1';
        } else {
            echo '0';
        }
    }

    /*
     * TO CHANGE THE STATUS OF THE RECORD..
     */

    function status($iDealID = '', $rm = '') {
        if ($iDealID != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->qrpoint_model->changeQRPointStatus($iDealID);

            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

    /*
     * TO ADD / EDIT THE QRCODE...
     */

    function add($iDealID = '', $ed = '') {
        $viewData['title'] = "QRPoint Management";

        $viewData['ACTION_LABEL'] = (isset($iDealID) && $iDealID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

        if ($iDealID != '' && $ed != '' && $ed == 'y') {
            $getData = $this->qrpoint_model->getQRPointDataById($iDealID);

            $viewData['getQRPointData'] = $getData;
        }

        /*
         * GET RESTAURANT LIST
         */
        $viewData['getRestaurantData'] = $this->qrpoint_model->getRestaurantList();

        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.qrcodeadd') {
            $qrcodeAdd = $this->qrpoint_model->addQRPoint($_POST);
            if ($qrcodeAdd != '') {

                /*
                 * GENERATE QRCODE... 
                 */

                $QRPointPath = UPLOADS . "qrcode/" . $qrcodeAdd . '/';

                if (!is_dir($QRPointPath)) {
                    if (!mkdir($QRPointPath, 0777, true)) {
                        
                    }
                }

                $this->load->library('ciqrcode');
                $this->load->library('Encrypt');
                $encryptionFormat = $this->encrypt->encode($qrcodeAdd, ENCRYPTION_KEY);
                $QRImage = md5('QRPoint' . $qrcodeAdd) . '.png';

                $params['cacheable'] = true; //boolean, the default is true
                $params['cachedir'] = ''; //string, the default is application/cache/
                $params['errorlog'] = ''; //string, the default is application/logs/
                $params['data'] = $encryptionFormat;
                $params['level'] = 'H';
                $params['size'] = 5;
                $params['savename'] = $QRPointPath . $QRImage;
                $this->ciqrcode->generate($params);

                /*
                 * NEED TO BE UPDATE THE RECORD...
                 */
                $updt['vQRCodeImage'] = $QRImage;
                $this->qrpoint_model->editQRCodeData($updt, $qrcodeAdd);

                /*
                 * SEND MAIL
                 */
                $restaurantData = $this->qrpoint_model->getRestaurantById($_POST['iRestaurantID'])[0];

                $emailMsg = '<p>QR Code has been generated for your restaurant and the price in between ' . $_POST['iMinBillAmount'] . ' (minimum) to ' . $_POST['iMaxBillAmount'] . ' (maximum)</p>';
                $emailMsg .= '<p>Please find the attachment. We have attached the update QR Code.</p>';
                $attachment = $QRPointPath . $QRImage;
                $this->sendQRCodeMail($restaurantData['vEmail'], $restaurantData['vRestaurantName'], $emailMsg, $attachment);

                $succ = array('0' => QRCODE_ADDED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => QRCODE_NOT_ADDED);
                $this->session->set_userdata('ERROR', $err);
            }
            //exit;
            redirect('qrpoint', 'refresh');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.qrcodeedit') {
            $qrcodeEdit = $this->qrpoint_model->editQRPoint($_POST);
            if ($qrcodeEdit != '') {

                /*
                 * GENERATE QRCODE... 
                 */

                $QRPointPath = UPLOADS . "qrcode/" . $_POST['iQRCodeID'] . '/';

                if (!is_dir($QRPointPath)) {
                    if (!mkdir($QRPointPath, 0777, true)) {
                        
                    }
                }

                /*
                 * DELETE OLD FILES
                 */
                $this->load->helper("file");
                delete_files($QRPointPath . "/");

                $this->load->library('ciqrcode');
                $this->load->library('Encrypt');
                $encryptionFormat = $this->encrypt->encode($_POST['iQRCodeID'], ENCRYPTION_KEY);
                $QRImage = md5('QRPoint' . $_POST['iQRCodeID']) . '.png';

                $params['cacheable'] = true; //boolean, the default is true
                $params['cachedir'] = ''; //string, the default is application/cache/
                $params['errorlog'] = ''; //string, the default is application/logs/
                $params['data'] = $encryptionFormat;
                $params['level'] = 'H';
                $params['size'] = 5;
                $params['savename'] = $QRPointPath . $QRImage;
                $this->ciqrcode->generate($params);

                /*
                 * NEED TO BE UPDATE THE RECORD...
                 */
                $updt['vQRCodeImage'] = $QRImage;
                $this->qrpoint_model->editQRCodeData($updt, $_POST['iQRCodeID']);

                /*
                 * SEND MAIL
                 */
                $restaurantData = $this->qrpoint_model->getRestaurantById($_POST['iRestaurantID'])[0];

                $emailMsg = '<p>QR Code has been updated for your restaurant and the price in between ' . $_POST['iMinBillAmount'] . ' (minimum) to ' . $_POST['iMaxBillAmount'] . ' (maximum)</p>';
                $emailMsg .= '<p>Please find the attachment. We have attached the update QR Code.</p>';
                $attachment = $QRPointPath . $QRImage;
                $this->sendQRCodeMail($restaurantData['vEmail'], $restaurantData['vRestaurantName'], $emailMsg, $attachment);
                //exit;
                $succ = array('0' => QRCODE_EDITED);
                $this->session->set_userdata('SUCCESS', $succ);
            } else {
                $err = array('0' => QRCODE_NOT_EDITED);
                $this->session->set_userdata('ERROR', $err);
            }
            redirect('qrpoint', 'refresh');
        }

        $this->load->view('qrpoint/qrpoint_add_view', $viewData);
    }

    private function sendQRCodeMail($userEmail = NULL, $userName = NULL, $emailMsg = NULL, $attachment = NULL) {
        try {
            if ($userEmail != NULL) {

                $config = $this->email_configuration();

                $this->load->library('Email');
                $this->email->initialize($config);

                $this->email->from(FROM_EMAIL_ID, FROM_EMAIL_NAME);
                $this->email->to(array($userEmail));
                //$this->email->cc('another@another-example.com');
                //$this->email->bcc('them@their-example.com');

                $this->email->subject('Require QR Code');
                //$emailMsg = $this->email_template($userEmail, $userName);
                $this->email->message($emailMsg);

                if ($attachment != NULL)
                    $this->email->attach($attachment);

                // - 07944820079
                if ($this->email->send()) {
                    return 1;
                } else {
                    return 0;
                    //show_error($this->email->print_debugger());
                }
            }
        } catch (Exception $ex) {
            exit('Error in sendRegisterMail function - ' . $ex);
        }
    }

    private function email_configuration() {
        $config['protocol'] = MAIL_PROTOCOL; // mail, sendmail, or smtp    The mail sending protocol.
        $config['smtp_host'] = MAIL_HOST; //'ssl://smtp.googlemail.com'; // SMTP Server Address.
        $config['smtp_user'] = MAIL_USERNAME; //'chintan.openxcell@gmail.com'; // SMTP Username.
        $config['smtp_pass'] = MAIL_PASSWORD; //'chintan.goswami@01'; // SMTP Password.
        $config['smtp_port'] = MAIL_PORT; ///'465'; // SMTP Port.
        $config['smtp_timeout'] = MAIL_TIMEOUT; //'500'; // SMTP Timeout (in seconds).
        $config['wordwrap'] = TRUE; // TRUE or FALSE (boolean)    Enable word-wrap.
        $config['wrapchars'] = 76; // Character count to wrap at.
        $config['mailtype'] = MAIL_MAIL_TYPE; //'html'; // text or html Type of mail. If you send HTML email you must send it as a complete web page. Make sure you don't have any relative links or relative image paths otherwise they will not work.
        $config['charset'] = MAIL_CHARSET; //'utf-8'; // Character set (utf-8, iso-8859-1, etc.).
        $config['validate'] = MAIL_VALIDATE_EMAIL_ID; // TRUE or FALSE (boolean)    Whether to validate the email address.
        $config['priority'] = MAIL_EMAIL_PRIORITY; // 1, 2, 3, 4, 5    Email Priority. 1 = highest. 5 = lowest. 3 = normal.
        $config['crlf'] = "\r\n"; // "\r\n" or "\n" or "\r" Newline character. (Use "\r\n" to comply with RFC 822).
        $config['newline'] = "\r\n"; // "\r\n" or "\n" or "\r"    Newline character. (Use "\r\n" to comply with RFC 822).
        $config['bcc_batch_mode'] = MAIL_BCC_BATCH_MODE; // TRUE or FALSE (boolean)    Enable BCC Batch Mode.
        $config['bcc_batch_size'] = MAIL_BCC_BATCH_SIZE; //200; // Number of emails in each BCC batch.

        return $config;
    }

}

?>
