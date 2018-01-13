<?php

/**
 * Description of pushnotify
 * @author Admin
 */
class pushNotification {

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

    public function sendIt($deviceType = 1, $tokenValue = '', $msg = '', $extraField = array(), $app = 1, $BADGECOUNT = 0, $title = '') {
        try {
            $this->TOKEN_VALUE = $tokenValue;
            $this->MSG = $msg['message'];
            $this->TITLE = $msg['title'];
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
             $this->ADR_API_KEY = 'AAAAmLyj9HY:APA91bHNCSEmKkKycUlHM9tgSTt302yVmfPes-8YMKp3ZeUmZPNZVmzehtmaiJueP7RWbaJJ8Iy37YKXH0AhOC6dKXB5UdtCF_B7KVdV5JFO8jfbweVuvCv6EVP9XfGfIod6mnakhtlU';
             $this->ADR_SOCKET_URL = 'https://fcm.googleapis.com/fcm/send';

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
        $MESSAGE_BODY = array();
    
        $MESSAGE_BODY = array(
            'message' => $this->MSG
        );
   // print_r($this->EXTRA_FIELD[userid]); exit;

$url = $this->ADR_SOCKET_URL;
$token = $this->TOKEN_VALUE;
//print_r($token); exit;
//var_dump($token); exit;
$serverKey = $this->ADR_API_KEY;
$title = $this->TITLE;
$body = $this->MSG;
$notification = array('title' =>$title , 'text' => $body, 'sound' => 'default', 'badge' => '1');
$arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
$json = json_encode($arrayToSend);
$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'Authorization: key='. $serverKey;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST,

"POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//Send the request
//echo $json;
$response = curl_exec($ch);
//echo $this->EXTRA_FIELD[userid];
//print_r($response);

//Close request
// if ($response === FALSE) {
    
// die('FCM Send Error: ' . curl_error($ch));
// }

curl_close($ch);
return;
return  $response;

            //echo $result;
        } catch (Exception $ex) {
            exit('Error in _pushAndroid function - ' . $ex);
        }
    }

}
