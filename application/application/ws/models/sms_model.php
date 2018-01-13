<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Sms_model extends CI_Model {

  var $api_url = NEX_SMS_APIURL; //SMS api url
  var $api_key = NEX_SMS_KEY; //SMS response required
  var $api_secret = NEX_SMS_SECRET; //SMS response required
  var $api_from = NEX_SMS_FROM; //SMS response required
  
  function __construct() {
    parent::__construct();
  }

  function Send() {
    ini_set('allow_url_open', 'On');
      $api_key = NEX_SMS_KEY;
      $contacts = $this->destmobileno;
      $from = NEX_SMS_FROM;
      $sms_text = urlencode($this->msg);

      //Submit to server

     $api_url = "http://37.48.104.215/app/smsapi/index.php?key=".$api_key."&routeid=29&type=text&contacts=91".$contacts."&senderid=".$from."&msg=".$sms_text;

     $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $api_url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);    

//Submit to server
//echo $api_url;
//$response = file_get_contents($api_url);
//print_r($response); exit;
return $output; 


      //http://37.48.104.214/api/mt/SendSMS?user=andron&password=andron2345&senderid=FOODIN&channel=Trans&DCS=0&flashsms=0&number=919555687555&text=test
      //http://37.48.104.214/api/mt/SendSMS?user=andron&password=andron2345&senderid=FOODIN&&channel=Trans&DCS=0&flashsms=0&text=Your+Foodine+verification+code+is+3066&to=919555687555bool(false)
      ini_set('allow_url_open', 'On');
      $this->api_url .= "?user=".$this->api_key."&password=".$this->api_secret."&senderid=".$this->api_from;
      $this->api_url .= "&channel=Trans&DCS=0&flashsms=0&text=".urlencode($this->msg)."&number=91".$this->destmobileno;
//      $stream_options = array(
//            'http' => array(
//               'method'  => 'GET',
//            ),
//       );
//
//    $context  = stream_context_create($stream_options);
//    $response = file_get_contents($this->api_url);
//var_dump(ini_get('allow_url_open'));
//
//print_r($response); exit;
//echo $this->api_url;
      
$url = $this->api_url;
$ch = curl_init();
//echo $url;
curl_setopt ($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
$contents = curl_exec($ch);
if (curl_errno($ch)) {
  echo curl_error($ch);
  echo "\n<br />";
 // echo 'hello'; exit;
  $contents = '';
} else {
   // echo 'done';
  curl_close($ch);
}

if (!is_string($contents) || !strlen($contents)) {
echo "Failed to get contents.";
$contents = '';
}

//var_dump($contents);
      
    return $contents;





 }
 
 function Send1($rest){
     ini_set('allow_url_open', 'On');
   $api_key = NEX_SMS_KEY;
      $contacts = $rest['mobile'];
      $from = NEX_SMS_FROM;
      $sms_text = urlencode($rest['msg']);

      //Submit to server

   // ini_set('allow_url_open', 'On');

$api_url = "http://37.48.104.215/app/smsapi/index.php?key=".$api_key."&routeid=29&type=text&contacts=91".$contacts."&senderid=".$from."&msg=".$sms_text;
//echo $api_url; exit;
//Submit to server

  $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $api_url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);    

//Submit to server
//echo $api_url;
//$response = file_get_contents($api_url);
//print_r($response); exit;
return $output; 



      //http://37.48.104.214/api/mt/SendSMS?user=andron&password=andron2345&senderid=FOODIN&channel=Trans&DCS=0&flashsms=0&number=919555687555&text=test
      //http://37.48.104.214/api/mt/SendSMS?user=andron&password=andron2345&senderid=FOODIN&&channel=Trans&DCS=0&flashsms=0&text=Your+Foodine+verification+code+is+3066&to=919555687555bool(false)
   //   ini_set('allow_url_open', 'On');
     $url= NEX_SMS_APIURL; 
     $url .= "?user=".$this->api_key."&password=".$this->api_secret."&senderid=".$this->api_from;
      $url .= "&channel=Trans&DCS=0&flashsms=0&text=".urlencode($rest['msg'])."&number=91".$rest['mobile'];
    
$ch = curl_init();
//echo $url;
curl_setopt ($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
$contents = curl_exec($ch);
if (curl_errno($ch)) {
  echo curl_error($ch);
  echo "\n<br />";
 // echo 'hello'; exit;
  $contents = '';
} else {
   // echo 'done';
  curl_close($ch);
}

if (!is_string($contents) || !strlen($contents)) {
echo "Failed to get contents.";
$contents = '';
}

//var_dump($contents);
      
    return $contents;
 }
}
