<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/phpmailer/PHPMailerAutoload.php');
require(APPPATH . '/libraries/aws.phar');

/**
 * Description of maillib
 * @author Admin
 */
class MailLib extends PHPMailer {

    var $default;
    var $mailObj;

    public function __construct() {
        parent::__construct();

        $this->mailObj = new PHPMailer();

        $this->_constantConfig();
        $this->_initConfig();
    }

    private function _constantConfig() {
        try {
            define('MAIL_FROM_EMAIL', 'info@hungermafia.com');
            define('MAIL_FROM_NAME', 'Hungermafia Team');
            define('MAIL_SUBJECT', 'HungerMafia');
            define('AMAZON_ACCESS_KEY', 'AKIAII5B5BIEMELG4AYA');
            define('AMAZON_SECRET_KEY', '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');
            define('AMAZON_REGION', 'us-east-1');
        } catch (Exception $ex) {
            throw new Exception('Erorr in _constantConfig function - ' . $ex);
        }
    }

    /*
     * FIRST WE HAVE TO INITAIALIZE THE BASIC VARIBALES 
     * THAT WE USING IN THE SEND MAIL...
     */

    private function _initConfig() {
        try {
            $this->mailObj->Host = MAIL_HOST; //"mail.icpcorp.com";
            $this->mailObj->SMTPDebug = 2;
            $this->mailObj->SMTPAuth = true;
            $this->mailObj->Port = MAIL_PORT;
            /*
             * THE MAIL WHICH IS SEND FROM THE USER EMAIL ADDRESS WITH HIS / HER NAME
             */
            $this->mailObj->From = MAIL_FROM_EMAIL;
            $this->mailObj->FromName = MAIL_FROM_NAME;

            /*
             * TO ADD THE REPLY TO EMAIL ADDRESS 
             *  IF AVAILABLE THEN AND THEN YOU HAVE TO ADD
             */
            //$this->mailObj->ReplyTo = MAIL_FROM_EMAIL;

            /*
             * SET DEFAULT WORD WRAP VALUE THAT HAVE TO SET 
             */
            $this->mailObj->WordWrap = 50;

            /*
             * SET IS HTML CONTENT EITHER IT WILL TRUE OR FALSE
             */
            $this->mailObj->isHTML(TRUE);
        } catch (Exception $ex) {
            throw new Exception('Error in _init function - ' . $ex);
        }
    }

    /*
     * TO SEND A MAIL...
     * PARAM
     *      - RECIEVER*         - to which user do you want T O  S E N D  the mail...
     *      - SUBJECT*          - the  S U B J E C T  of the content mail...
     *      - TEMPLATE*         - the absolute  D I R E C T O R Y  P A T H  of the template file...
     *      - PARAM             - the array with key and values
     *                              e.g. 
     *                              array(
     *                                  0 => array('%PARAM_WORD_REPLACE%','PARAM_WORD_VALUE')
     *                                  ...
     *                              )
     *      - ATTACHMENT        - the array with the values of  A B S O L U T E  path
     *                              e.g.
     *                              array(
     *                                  'D:/xampp/htdocs/project/folder/file.attachment'
     *                                  ....
     *                              )
     *
     *       - CC               - the array with the values of senders
     *                              e.g.
     *                              array(
     *                                  'example@example.com'
     *                                  ....
     *                              )
     */

    function sendMail($RECIEVER = array(), $SUBJECT = MAIL_SUBJECT, $TEMPLATE = '', $PARAM = array(), $isHTML = FALSE, $ATTACHMENT = array(), $CC = array()) {
        try {

            /*
             * TO USER'S EMAIL ARRAY....
             */

            if (is_string($RECIEVER)) {
                $RECIEVER = explode(',', $RECIEVER);
            }


            /*
             * TO ADD RECIEVER TO WHOM WE WANT TO SEND A MAIL
             */

            foreach ($RECIEVER as $k => $v)
                if ($v !== '')
                    $this->mailObj->addAddress($v, '');

            /*
             * DEFAULT SUBJECT NAME THAT I HAVE TO SET
             */

            $this->mailObj->Subject = $SUBJECT;

            /*
             * TO LOAD THE MESSAGE...
             *  USING EMAIL TEMPLATING...
             */

            $this->mailObj->Body = !$isHTML ? $this->_template($TEMPLATE, $PARAM) : $TEMPLATE;

            /*
             * ADD ATTACHMENT TO THE MAIL BODY
             */

            if (!empty($ATTACHMENT)) {
                foreach ($ATTACHMENT as $v) {
                    $this->mailObj->addAttachment($v);
                }
            }

            /*
             * SET THE CC
             */

            if (!empty($CC)) {
                foreach ($CC as $v) {
                    if ($v !== '')
                        $this->mailObj->addCC($v, '');
                }
            }

            /*
             * TO SEND A MAIL...
             */

            /*
              SES CODE  START
              [ Edited By : Girish Solanki ]
              [ Modified On : 28 September 2015 (Monday) - 12:31  ]
             */
            //use Aws\Ses\SesClient;
            $client = Aws\Ses\SesClient::factory(array(
                        'key' => AMAZON_ACCESS_KEY,
                        'secret' => AMAZON_SECRET_KEY,
                        'region' => AMAZON_REGION
            ));

            $msg = array();
            //$msg['Source'] = "Hungermafia <info@hungermafia.com>";
            $msg['Source'] = MAIL_FROM_NAME . ' <' . MAIL_FROM_EMAIL . '>';
            $msg['Destination']['ToAddresses'] = $RECIEVER;
            if (!empty($CC))
                $msg['Destination']['CcAddresses'] = $CC;
            //$msg['Destination']['ToAddresses'][] = 'girish.solanki@openxcelltechnolabs.com';
            $msg['Message']['Subject']['Data'] = $SUBJECT;
            $msg['Message']['Subject']['Charset'] = "UTF-8";
            $msg['Message']['Body']['Html']['Data'] = $this->mailObj->Body;
            $msg['Message']['Body']['Html']['Charset'] = "UTF-8";
            try {
                $result = $client->sendEmail($msg);
                $msg_id = $result->get('MessageId');
                //echo("MessageId: $msg_id");
                //print_r($result);
                return 1;
            } catch (Exception $e) {
                //echo($e->getMessage());
                return 0;
                //exit;
            }
            /*
              SES CODE  END
              [ Edited By : Girish Solanki ]
              [ Modified On : 28 September 2015 (Monday) - 12:31  ]
             */

            //mprd($this->mailObj);
            //$this->mailObj->CharSet = "iso-8859-1rn";
            /*            if ($this->mailObj->send()) {
              return 1;
              } else {
              //return 0;
              echo $this->mailObj->ErrorInfo;
              exit;
              } */
        } catch (Exception $ex) {
            throw new Exception('Error in send function - ' . $ex);
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
            exit('MailLib Library : Error in _template function - ' . $ex);
        }
    }

}
