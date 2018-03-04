<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of pickup_model
 * @author OpenXcell Technolabs
 */
class SMTPMail_Model extends CI_Model {

    var $config;

    function __construct() {
        parent::__construct();

        $this->_configure();
        $this->load->library('Email');
    }

    /*
     * TO SEND A MAIL...
     * PARAM
     *      - sendTo*           - to which user do you want to send the mail...
     *      - mailTamplate*     - the absolute directory path of the template file...
     *      - mailParam         - the array with key and values
     *                              e.g. 
     *                              array(
     *                                  0 => array('%PARAM_WORD_REPLACE%','PARAM_WORD_VALUE')
     *                                  ...
     *                              )
     *      - mailAttachment    - the array with the values of absolute path
     *                              e.g.
     *                              array(
     *                                  'D:/xampp/htdocs/project/folder/file.attachment'
     *                                  ....
     *                              )
     *
     *       - mailCC           - the array with the values of senders
     *                              e.g.
     *                              array(
     *                                  'example@example.com'
     *                                  ....
     *                              )
     */

    function send($sendTo, $mailSubject = EMAIL_SUBJECT, $mailTemplate = '', $mailParam = array(), $mailAttachment = array(), $mailCC = array()) {

        // if($mailSubject != 'Foodine : Forgot Password') {
        //     return 1;
        // }

        try {

            /*
             * TO USER'S EMAIL ARRAY....
             */
            if (is_string($sendTo)) {
                $sendTo = explode(',', $sendTo);
            }

            $this->email->initialize($this->config);

            $this->email->from(FROM_EMAIL_ID, FROM_EMAIL_NAME);
            $this->email->to($sendTo);
            $this->email->subject($mailSubject);

            /*
             * TO LOAD THE MESSAGE...
             *  USING EMAIL TEMPLATING...
             */
            $mailMsg = $this->_template($mailTemplate, $mailParam);
            $this->email->message($mailMsg);


            /*
             * ADD ATTACHMENT TO THE MAIL BODY
             */
            if (!empty($mailAttachment)) {
                foreach ($mailAttachment as $v) {
                    $this->email->attach($v);
                }
            }

            /*
             * SET THE CC
             */
            if (!empty($mailCC)) {
                foreach ($mailCC as $v) {
                    $this->email->cc($v);
                }
            }

           // mprd($this->email);

            if ($this->email->send()) {
                return 1;
            } else {
                show_error($this->email->print_debugger());
            }
        } catch (Exception $ex) {
            exit('SMTPMail Model : Error in send function - ' . $ex);
        }
    }

    /*
     * DEFAULT EMAIL - CONFIGURATION...
     */

    private function _configure() {
        try {
            $this->config['protocol'] = MAIL_PROTOCOL; // mail, sendmail, or smtp    The mail sending protocol.
            $this->config['smtp_host'] = MAIL_HOST; //'ssl://smtp.googlemail.com'; // SMTP Server Address.
            $this->config['smtp_user'] = MAIL_USERNAME; //'chintan.openxcell@gmail.com'; // SMTP Username.
            $this->config['smtp_pass'] = MAIL_PASSWORD; //'chintan.goswami@01'; // SMTP Password.
            $this->config['smtp_port'] = MAIL_PORT; ///'465'; // SMTP Port.
            $this->config['smtp_timeout'] = MAIL_TIMEOUT; //'500'; // SMTP Timeout (in seconds).
            $this->config['wordwrap'] = TRUE; // TRUE or FALSE (boolean)    Enable word-wrap.
            $this->config['wrapchars'] = 76; // Character count to wrap at.
            $this->config['mailtype'] = MAIL_MAIL_TYPE; //'html'; // text or html Type of mail. If you send HTML email you must send it as a complete web page. Make sure you don't have any relative links or relative image paths otherwise they will not work.
            $this->config['charset'] = MAIL_CHARSET; //'utf-8'; // Character set (utf-8, iso-8859-1, etc.).
            $this->config['validate'] = MAIL_VALIDATE_EMAIL_ID; // TRUE or FALSE (boolean)    Whether to validate the email address.
            $this->config['priority'] = MAIL_EMAIL_PRIORITY; // 1, 2, 3, 4, 5    Email Priority. 1 = highest. 5 = lowest. 3 = normal.
            $this->config['crlf'] = "\r\n"; // "\r\n" or "\n" or "\r" Newline character. (Use "\r\n" to comply with RFC 822).
            $this->config['newline'] = "\r\n"; // "\r\n" or "\n" or "\r"    Newline character. (Use "\r\n" to comply with RFC 822).
            $this->config['bcc_batch_mode'] = MAIL_BCC_BATCH_MODE; // TRUE or FALSE (boolean)    Enable BCC Batch Mode.
            $this->config['bcc_batch_size'] = MAIL_BCC_BATCH_SIZE; //200; // Number of emails in each BCC batch.
        } catch (Exception $ex) {
            exit('SMTPMail Model : Error in _configure function - ' . $ex);
        }
    }

    /*
     * TO LOAD THE MAIL TEMPLATE...
     * REQUIRE PARAM...
     *      - mailTemplate          - the absolute path of the mail 
     *      - mailParam         - the array with key and values
     *                              e.g. 
     *                              array(
     *                                  0 => array('%PARAM_WORD_REPLACE%','PARAM_WORD_VALUE')
     *                                  ...
     *                              )
     */

    private function _template($mailTemplate, $mailParam = array()) {
        try {
            /*
             * TO LOAD THE HTML CONTENT FROM THE TEMPLATE FILE...
             */
            $mailMsg = file_get_contents($mailTemplate, TRUE);

            /*
             * REPLACE EACH AND EVERY PARAM WHICH WE ARE GOING TO REPLACE...
             */
            foreach ($mailParam as $key => $val) {
                $mailMsg = str_replace($key, $val, $mailMsg);
            }
            return $mailMsg;
        } catch (Exception $ex) {
            exit('SMTPMail Model : Error in _template function - ' . $ex);
        }
    }

}
