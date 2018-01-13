<?php

class Notification {

    function __construct() {
        
    }

    function sendNotification() {
        $message = "";
        $badge = "";
        $sound = "";
        $deviceToken = "7fc51f9d0fc012b185ce979c620db6ce775216631e9feb2c3cb972d98bc75cac";
        $message = 'jalsa kar';
        $badge = 1;
        $sound = "default";
        $body = array();
        $body['aps'] = array('alert' => $message, 'data' => "THOR");
        if ($badge)
            $body['aps']['badge'] = $badge;
        if ($sound)
            $body['aps']['sound'] = $sound;
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'sslv3', 'local_cert', '../apns-dev.pem');
        //stream_context_set_option($ctx, 'ssl', 'local_cert', 'apns-dev.pem');
        $fp = stream_socket_client('sslv2://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp) {
            $data['msgs'] = "Failed to connect $err $errstr \n";
            return;
        } else {
            //print "Connection OK\n";
            $payload = json_encode($body);
            $msg = chr(0) . pack("n", 32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n", strlen($payload)) . $payload;
            //print "sending message :" . $payload . "\n";
            $data['msgs'] = "Notification sent succesfully";
            $return = fwrite($fp, $msg);
            //print $return;
            fclose($fp);
        }
        print_r(json_encode($data));
    }

}

?>