<?php

/*
 * TO SEND SMS TO END-USERS
 * SMSLane Library 
 * FOR MORE INFORMATION PLEASE VISIT
 * http://www.smslane.com/
 */

class SmsLane {

    var $_data;
    var $_username, $_password, $_smsid, $_flag;
    var $_request_url, $_own_url;

    function __construct() {
        $this->_setValues();
    }

    function send($mobile_number = array(), $sms_text = '') {
        try {
            if (!empty($mobile_number) && $sms_text != '') {
                $mobile_number = implode(',', $mobile_number);

                /* set data object value */
                $this->_setDataObject($mobile_number, $sms_text);

                /* call function to send SMS to end user */
                $resp = $this->_sendSMSRequest();
                if (!empty($resp)) {
                    return 1;
                } return -2; // request not yet send to the smslane server.
            } return -1; // insufficient data to send the sms.
        } catch (Exception $ex) {
            throw new Exception(__CLASS__ . '::' . __FUNCTION__ . '::' . $ex);
        }
    }

    private function _setValues() {
        try {
            $this->_username = SMSLANE_USERNAME;
            $this->_password = SMSLANE_PASSWORD;
            $this->_smsid = 'TRASMS';//SMSLANE_SMSID;
            $this->_flag = SMSLANE_SMSFLAG;
            $this->_request_url = SMSLANE_POST_URL;
            $this->_own_url = SMSLANE_OWN_URL;
        } catch (Exception $ex) {
            throw new Exception(__CLASS__ . '::' . __FUNCTION__ . '::' . $ex);
        }
    }

    private function _setDataObject($mobile_number, $sms_text) {
        try {
            $this->_data = array(
                'user' => $this->_username,
                'password' => $this->_password,
                'msisdn' => $mobile_number,
                'sid' => $this->_smsid,
                'msg' => $sms_text,
                'fl' => $this->_flag,
                'gwid' => 2
            );
        } catch (Exception $ex) {
            throw new Exception(__CLASS__ . '::' . __FUNCTION__ . '::' . $ex);
        }
    }

    private function _sendSMSRequest() {
        // convert variables array to string:
        $_send_data_packet = array();
        while (list($n, $v) = each($this->_data)) {
            $_send_data_packet[] = "$n=$v";
        }
        $_send_data_packet = implode('&', $_send_data_packet);

        // parse the given URL
        $_url = parse_url($this->_request_url);
        if ($_url['scheme'] != 'http') {
            /* Only HTTP request are supported! */
            return array();
        } else {
            // extract host and path:
            $host = $_url['host'];
            $path = $_url['path'];

            // open a socket connection on port 80
            $fp = fsockopen($host, 80);

            // send the request headers:
            fputs($fp, "POST $path HTTP/1.1\r\n");
            fputs($fp, "Host: $host\r\n");
            fputs($fp, "Referer: " . $this->_own_url . "\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: " . strlen($_send_data_packet) . "\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $_send_data_packet);

            $_result = '';
            while (!feof($fp)) {
                // receive the results of the request
                $_result .= fgets($fp, 128);
            }
            // close the socket connection:
            fclose($fp);

            // split the result header from the content
            $_result = explode("\r\n\r\n", $_result, 2);
            $header = isset($_result[0]) ? $_result[0] : '';
            $content = isset($_result[1]) ? $_result[1] : '';

            // return as array:
            return array($header, $content);
        }
    }

}
