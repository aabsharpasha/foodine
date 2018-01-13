<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of login_model
 * @author OpenXcell Technolabs
 */
class AppCrash_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_app_crash_report';
    }

    /*
     * CHECK WHETHER USERNAME AND PASSWORD ARE CORRECT
     * OR NOT...
     */

    function saveCrash($postValue) {
        try {
            extract($postValue);
            
            $ins = array(
                'eOsType' => $osType,
                'errorValue' => $reportValue,
                'activityName' => $activityName,
                'appVersion' => $appVersion,
                'netType' => $netType,
                'deviceSDK' => $deviceSDK,
                'deviceRAM' => $deviceRAM,
                'tCreatedAt' => date('Y-m-d H:i:s')
            );

            $this->db->insert($this->table, $ins);

            return $this->db->insert_id();
        } catch (Exception $ex) {
            throw new Exception('AppCrash Model : Error in saveCrash function - ' . $ex);
        }
    }

}

?>
