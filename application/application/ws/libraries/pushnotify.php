<?php

/**
 * Description of pushnotify
 * @author Admin
 */
class pushNotify {

    protected $TOKEN_VALUE, $MSG, $EXTRA_FIELD;
    protected $NOTIFICATION_MODE;
    protected $iOS_PEM_FILE, $iOS_SOCKET_URL;
    protected $ADR_API_KEY, $ADR_SOCKET_URL;
    protected $ERR_LOG;
    protected $APP_TYPE;

    function __construct($mode = 'live') {
        $this->NOTIFICATION_MODE = $mode;
    }

    /*
     * SEND A PUSH NOTIFICATION 
     * PARAMS
     *      deviceType  :   1   -   For ANDROID
     *                      2   -   For iOS
     * 
     *      tokenValue  :   DEVICE UNIQUE TOKEN VALUE, THAT WILL SEND FROM THE
     *                      APPLICATION SIDE...
     * 
     *      msg         :   PUSH NOTIFICATION MESSAGE...     
     */

    public function sendIt($deviceType = 1, $tokenValue = '', $msg = '', $app = 1, $extraField = array(), $BADGECOUNT = 0) {
        try {
            $this->TOKEN_VALUE = $tokenValue;
            $this->MSG = $msg;
            $this->APP_TYPE = $app;
            $this->EXTRA_FIELD = $extraField;

            $this->_preLoadValue();

            switch ($deviceType) {
                case 1:
                    return $this->_pushAndroid();
                    break;

                case 2:
                    return $this->_pushIOS($BADGECOUNT);
                    break;
            }
        } catch (Exception $ex) {
            exit('Error in sendIt function - ' . $ex);
        }
    }

    /*
     * PRE LOAD THE VALUES...
     */

    private function _preLoadValue() {
        try {
            $this->ADR_API_KEY = 'AIzaSyAyzVfSxWoKTbjQbv1ZFrzCEEml_qBP3Q8';
            $this->ADR_SOCKET_URL = 'https://android.googleapis.com/gcm/send';

            switch ($this->NOTIFICATION_MODE) {
                case 'sandbox':
                    $this->iOS_PEM_FILE = $this->APP_TYPE == 2 ? DIR_LIB . 'apns-dev-vendor.pem' : DIR_LIB . 'ck.pem';
                    $this->iOS_SOCKET_URL = 'ssl://gateway.sandbox.push.apple.com:2195';
                    break;

                case 'live':
                    $this->iOS_PEM_FILE = $this->APP_TYPE == 2 ? DIR_LIB . 'apns-live-vendor.pem' : DIR_LIB . 'apns-live.pem';
                    $this->iOS_SOCKET_URL = 'ssl://gateway.push.apple.com:2195';
                    break;
            }
        } catch (Exception $ex) {
            exit('Error in _preLoadValue function - ' . $ex);
        }
    }

    /*
     * PUSH NOTIFICATION FOR iOS
     */

    private function _pushIOS($BADGECOUNT) {
        try {
            /*
             * CREATE CONTEXT STREAM
             */
            $CONTEXT_STREAM = stream_context_create();

            /*
             * SET STREAM CONTEXT OPTION
             */
            stream_context_set_option($CONTEXT_STREAM, 'ssl', 'local_cert', $this->iOS_PEM_FILE);

            /*
             * OPEN APNS SERVER
             */
            $APNS_SERVER = stream_socket_client($this->iOS_SOCKET_URL, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $CONTEXT_STREAM);

            $this->ERR_LOG = !$APNS_SERVER ? ' FAIL TO CONNECT ' . $err . ' ' . $errstr : '';

            if ($this->ERR_LOG != '') {
                echo $this->ERR_LOG;
                exit;
            }

            $ALERT_BODY = array();
            if (!empty($this->EXTRA_FIELD)) {
                $ALERT_BODY = array(
                    'body' => $this->MSG,
                    'xdata' => $this->EXTRA_FIELD
                );
            } else {
                $ALERT_BODY = array(
                    'body' => $this->MSG
                );
            }


            $BODY = array(
                'aps' => array(
                    'alert' => $ALERT_BODY,
                    'badge' => (int) $BADGECOUNT,
                    'sound' => 'default'
                )
            );
            
            //mprd($BODY);

            /*
             * ENCODE PAYLOAD AS JSON
             */
            $PAYLOAD = json_encode($BODY);

            /*
             * BUILD THE BINARY NOTIFICATION
             */
            $this->MSG = chr(0) . pack('n', 32) . pack('H*', $this->TOKEN_VALUE) . pack('n', strlen($PAYLOAD)) . $PAYLOAD;

            /*
             * SEND IT TO THE SERVER
             */
            $OUTPUT = fwrite($APNS_SERVER, $this->MSG, strlen($this->MSG));

            /*
             * CLOSE THE FILE CONNECTION
             */
            //fclose($OUTPUT);
        } catch (Exception $ex) {
            exit('Error in _pushIOS function - ' . $ex);
        }
    }

    /*
     * PUSH NOTIFICATION FOR ANDROID
     */

    private function _pushAndroid() {
        try {
            /*
             * CREATE THE NOTIFICATION BODY / HEADER
             */
            $receiver = array();
            if (is_string($this->TOKEN_VALUE)) {
                $receiver[] = $this->TOKEN_VALUE;
            } else {
                $receiver = $this->TOKEN_VALUE;
            }

            $MESSAGE_BODY = array();
            if (!empty($this->EXTRA_FIELD)) {
                $MESSAGE_BODY = array(
                    'message' => $this->MSG,
                    'xdata' => $this->EXTRA_FIELD
                );
            } else {
                $MESSAGE_BODY = array(
                    'message' => $this->MSG
                );
            }

            $BODY = array(
                'registration_ids' => $receiver,
                'data' => $MESSAGE_BODY
            );

            $HEADER = array(
                'Authorization: key=' . $this->ADR_API_KEY,
                'Content-Type: application/json'
            );

            /*
             * OPEN CONNECTION
             * SET THE URL, NUMBER OF POST VARIABLES, POST DATA
             */
            $CURL = curl_init();
            curl_setopt($CURL, CURLOPT_URL, $this->ADR_SOCKET_URL);
            curl_setopt($CURL, CURLOPT_POST, TRUE);
            curl_setopt($CURL, CURLOPT_HTTPHEADER, $HEADER);
            curl_setopt($CURL, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($CURL, CURLOPT_POSTFIELDS, json_encode($BODY));

            /*
             * EXECUTE CURL
             */
            $result = curl_exec($CURL);
            //var_dump($result);

            /*
             * CLOSE THE CONNECTION
             */
            curl_close($CURL);

            return $result;

            //echo $result;
        } catch (Exception $ex) {
            exit('Error in _pushAndroid function - ' . $ex);
        }
    }

}
