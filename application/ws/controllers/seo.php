<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

class Seo extends REST_Controller {

    var $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('seo_model');
    }

    function getMetaTags_post() {
        try {
            //MANDATORY PARAMETERS [searchType = nearby, feature, category, choice]
            $allowParam = array('page');
            //TO SET DEFAULT VALUES
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $MESSAGE = NO_RECORD_FOUND;
                $STATUS = FAIL_STATUS;
                $DATA = array();
                //SEARCH FROM THE MODEL TO GET THE RESULT...
                $DATA = $this->seo_model->getMetaTags($this->post());
                
                if (count($DATA) > 0) {
                    unset($DATA['iMetaTagId'],$DATA['tCreatedAt'],$DATA['tModifiedAt']);
                    $MESSAGE = RESTAURANT_FOUND;
                    $STATUS = SUCCESS_STATUS;
                }
            }
            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'DATA' => $DATA
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in allrestaurant_get function - ' . $ex);
        }
    }

}
