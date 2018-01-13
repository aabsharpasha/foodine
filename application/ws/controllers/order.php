<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author OpenXcell Technolabs
 */
class Order extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('order_model');
    }

    function getMenuList_post() {

        try {

            $allowParam = array(
                'restaurantId'
            );
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $MENUDATA = '';

            if (checkselectedparams($this->post(), $allowParam)) {

                /*
                 * SEARCH FROM THE MODEL TO GET THE RESULT...
                 */
                $MESSAGE = NO_RECORD_FOUND;
                $STATUS = FAIL_STATUS;

                $menuData = $this->order_model->getMenuList($this->post('restaurantId'));
                if (!empty($menuData)) {
                    $MESSAGE = RECORD_FOUND;
                    $STATUS = SUCCESS_STATUS;
                    $MENUDATA = $menuData;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$MENUDATA != '') {
                $resp['MENUDATA'] = $MENUDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getMenuList_post function - ' . $ex);
        }
    }

    function updateOrderCart_post() {
        try {

            $allowParam = array(
                'restaurantId', 'restaurantName', 'itemId', 'itemName', 
                'itemPrice', 'qty', 'itemTotalPrice', 'userId', 'userName',
                'userMobile'
            );
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $ID = '';

            if (checkselectedparams($this->post(), $allowParam)) {

                /*
                 * SEARCH FROM THE MODEL TO GET THE RESULT...
                 */
                $MESSAGE = NO_RECORD_FOUND;
                $STATUS = FAIL_STATUS;

                $userOrderID = $this->order_model->saveOrderDetail($this->post());
                if (!empty($userOrderID)) {
                    $MESSAGE = 'Record Updated Successfully';
                    $STATUS = SUCCESS_STATUS;
                    $ID = $userOrderID;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );
            
             if (@$ID != '') {
                $resp['ID'] = $ID;
            }
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in saveOrderDetail_post function - ' . $ex);
        }
    }

}
