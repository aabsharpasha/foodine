<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author KelltonTech
 */
class Paytm extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('paytm_model');
    }

    function generateChecksum_post() {
        try {
            header("Pragma: no-cache");
            header("Cache-Control: no-cache");
            header("Expires: 0");
            //Here checksum string will return by getChecksumFromArray() function.
            $checkSum = $this->paytm_model->getChecksumFromArray($this->post(),PAYTM_MERCHANT_KEY);
            echo json_encode(array("CHECKSUMHASH" => $checkSum, "ORDER_ID" => $this->post("ORDER_ID"), "payt_STATUS" => "1"));
            die;
        } catch (Exception $ex) {
            throw new Exception('Error in generateChecksum function - ' . $ex);
        }
    }

    function verifyChecksum_post() {
        try {
            header("Pragma: no-cache");
            header("Cache-Control: no-cache");
            header("Expires: 0");
            $paramList = $this->post();
            $return_array = $this->post();
            $paytmChecksum = !empty($this->post("CHECKSUMHASH")) ? $this->post("CHECKSUMHASH") : ""; //Sent by Paytm pg
            $isValidChecksum = $this->paytm_model->verifychecksum_e($paramList, PAYTM_MERCHANT_KEY, $paytmChecksum); //will return TRUE or FALSE string.

            $return_array["IS_CHECKSUM_VALID"] = $isValidChecksum ? "Y" : "N";
            unset($return_array["CHECKSUMHASH"]);

            $encoded_json = htmlentities(json_encode($return_array));
//            echo $encoded_json;

            echo "<html>"
                . "<head>"
                    .'<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-I">'
                    .'<title>Paytm</title>'
                    .'<script type="text/javascript">'
                            .' function response(){ '
                                    ."return document.getElementById('response').value;"
                            .' }'
                    .'</script>'
            .'</head>'
            .'<body>'
              .'Redirect back to the app<br>'
              .'<form name="frm" method="post">'
                .'<input type="hidden" id="response" name="responseField" value="'. $encoded_json .'">'
              .'</form>'
            .'</body>'
            .'</html>';
            die;
        } catch (Exception $ex) {
            throw new Exception('Error in verifyChecksum function - ' . $ex);
        }
    }

}
