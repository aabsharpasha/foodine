<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Welcome extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {

        $this->load->library('topdf');
        $replace = array(
            '%HEADER_LOGO%' => IMG_URL . 'icp-150.png',
            '%PICKUP_DATE%' => '25 Feb 2015 12:20 PM',
            '%CUSTOMER_SIGN%' => SIGN_URL . 'thumb/1424756707.png',
            '%CUSTOMER_NAME%' => 'Foodine',
            '%CUSTOMER_EMAIL%' => '545@openxcell.com1',
            '%CUSTOMER_LOCATION%' => 'Ahemdabad',
            '%CUSTOMER_ADDRESS%' => wordwrap('202-203, Baleshwar Avenue, S.G Highway, Ahmedabad, Gujarat, India.', 40, '<br/>'),
            '%WORK_ORDER_NUMBER%' => 156234896,
            '%PICKUP_TYPE%' => 'Bulk Pickup'
        );
        $this->topdf->create('pickupDetails.php', $replace);
        exit;

        $this->load->helper('url');
        $this->load->view('welcome_message');
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */