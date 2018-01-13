<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author Foodine :
 */
class Payumoney extends REST_Controller {

    function __construct() {
        parent::__construct();
    }

    function success_post() {
        $this->load->view('payumoney/success');
    }

    function failure_post() {
        $this->load->view('payumoney/fail');
    }
    
    function hash_post() {
        $post = $this->post();
        extract($post);
       
        $this->load->model('general_model');
        $this->load->model('restaurant_model');
        $userInfo = $this->general_model->getUserBasicRecordById($userId);
        //print_r($userInfo); exit;
        $firstname = $userInfo['userFirstName'];
        $email = $userInfo['userEmail'];
        $key = 'RBeBf7KP';
        $salt = 'uavAJbZKIX';
        $txnid = substr('foodinemob'.strtotime(date('Y-m-d')),0,20);
        
        $resDetail = $this->restaurant_model->getRestaurantDataById($restaurantId);
        $productInfo = str_replace("'",'',$resDetail['vRestaurantName']);
        $posted['key'] = $key;
        $posted['txnid'] = $txnid;
        $posted['amount'] = $amount;
        $posted['productinfo'] = $productInfo;
        $posted['firstname'] = $firstname;
        $posted['email'] = $email;
        //print_r($posted); exit;
                //key|txnid|amount|productinfo|firstname|email
       //print_r($productInfo); 
//        echo $key."<br />";
//        echo $txnid."<br />";
//        echo $amount."<br />";
//        echo $productInfo."<br />";
//        echo $firstname."<br />";
//        echo $email."<br />";
//        echo $salt."<br />";
       
        //print_r($resDetail); exit;
        $MESSAGE = SUCCESS;
        $STATUS = SUCCESS_STATUS;
        $hash = '';
        

//print_r(hash_algos()); exit;
   //echo $key.'|'.$txnid.'|'.$amount.'|'.$productInfo.'|'.$firstname.'|'.$email.'|||||||||||'.$salt; exit;
        if(empty($firstname)) {
             $MESSAGE = 'Update name in your profile first and try.';
             $STATUS = FAIL_STATUS;
        } else if(empty($email)) {
             $MESSAGE = INSUFF_DATA;
             $STATUS = 'Kindly update your email in your profile first.';
        } else {
            //$hash = hash('sha512', $string);
        }
        
        
     $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";

    //$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
    $hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';	
    foreach($hashVarsSeq as $hash_var) {
      $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
      $hash_string .= '|';
    }

    $hash_string .= $salt;
    //echo $hash_string; exit;
    $hash = strtolower(hash('sha512', $hash_string));
    foreach($hash as $line) {
                $hex = dechex($line);
                if (str_len($hex)== 1)
                        $hexstring .= '0';
                    $hexstring .= $hex;
    }
        if($hash) {
            $resp['result'] = 'Success';
            $resp['resultCode'] = 200;
            $resp['amount'] = $amount;
            $resp['merchantKey'] = $key;
            $resp['transactionId'] = $txnid;
            $resp['productInfo'] = $productInfo;
            $resp['successUrl'] = BASEURL.'payumoney/success';
            $resp['failUrl'] = BASEURL.'payumoney/fail';
            $resp['hashKey'] = $hash;
            $resp['firstName'] = $firstname;
            $resp['email'] = $email;
            $resp['mobileNumber'] = $userInfo['userMobile'];
        } else {
            $resp['result'] = $MESSAGE;
            $resp['resultCode'] = $STATUS;
        }

        $this->response($resp, 200);
    }

}
