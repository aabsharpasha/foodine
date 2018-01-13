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

  function Send(){
      $this->api_url .= "?api_key=".$this->api_key."&api_secret=".$this->api_secret."&from=".$this->api_from;
      $this->api_url .= "&text=".urlencode($this->msg)."&to=91".$this->destmobileno;
      $stream_options = array(
            'http' => array(
               'method'  => 'GET',
            ),
       );

    $context  = stream_context_create($stream_options);
    $response = file_get_contents($this->api_url, null, $context);

    return $response;
 }
}
