<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/aws.phar');

/**
 * Description of SESLib
 * @author chin2
 */
class SESLib {

    private $ACCESS_KEY, $SECRET_KEY, $REGION, $BASEURL;
    private $CLIENT, $CLIENT_ARR;
    private $MSG_BODY;

    function __construct() {
        /*
         * INITIALIZE CONFIGURATION
         */
        $this->_initConfig();
    }

    private function _initConfig() {
        try {
            /*
             * TO SET THE PRIMARY VALUE
             */
            $this->_setPrimaryValue();
            /*
             * TO CREATE CLIENT OBJECT 
             *  THROUGH WICH YOU CAN SEND A MAIL
             */
            $this->_clientObject();
        } catch (Exception $ex) {
            throw new Exception('Error in _initConfig function - ' . $ex);
        }
    }

    /*
     * TO SET THE PRIMARY REQUIRE VALUE
     */

    private function _setPrimaryValue() {
        try {
            $this->ACCESS_KEY = 'AKIAIY5T575P67UI6CFQ';
            $this->SECRET_KEY = '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';
            $this->REGION = 'eu-west-1';
            $this->BASEURL = 'https://email.eu-west-1.amazonaws.com';
        } catch (Exception $ex) {
            throw new Exception('Error in _setDefaultValue function - ' . $ex);
        }
    }

    /*
     * TO CREATE THE CLIENT OBJECT
     *  THROUGH WICH YOU CAN SEND A MAIL
     */

    private function _clientObject() {
        try {
            $this->_clientObjectValue();

            $this->CLIENT = Aws\Ses\SesClient::factory($this->CLIENT_ARR);
        } catch (Exception $ex) {
            throw new Exception('Error in _clientObject function - ' . $ex);
        }
    }

    /*
     * TO SET THE CLIETN OBEJCT ARRAY VALUE
     */

    private function _clientObjectValue() {
        try {
            $this->CLIENT_ARR = array(
                'key' => $this->ACCESS_KEY,
                'secret' => $this->SECRET_KEY,
                'region' => $this->REGION,
                'base_url' => $this->BASEURL,
            );
        } catch (Exception $ex) {
            throw new Exception('Error in _clientObjectValue function - ' . $ex);
        }
    }

    /*
     * SEND MAIL LIBRARY 
     */

    function sendMail() {
        try {
            
        } catch (Exception $ex) {
            throw new Exception('Error in sendMail function - ' . $ex);
        }
    }

    private function _messageBody($RECEIVER = array(), $SUBJECT = '', $HTML = '') {
        try {
            $this->MSG_BODY = array(
                'Source' => '',
                'Destination' => array(
                    'ToAddresses' => $RECEIVER
                ),
                'Message' => array(
                    'Subject' => array(
                        'Data' => $SUBJECT,
                        'Charset' => 'UTF-8'
                    ),
                    'Body' => array(
                        'Html' => array(
                            'Data' => $HTML,
                            'Charset' => 'UTF-8'
                        )
                    )
                )
            );

            $RESP = $this->CLIENT->sendEmail($this->MSG_BODY);

            return $RESP->get('MessageId');
        } catch (Exception $ex) {
            throw new Exception('Error in _messageBody function - ' . $ex);
        }
    }

}
