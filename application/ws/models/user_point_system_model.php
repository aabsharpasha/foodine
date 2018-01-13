<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of link_model
 * link hashes expiry 24 hrs
 * @author Amit Malakar
 */
class User_Point_System_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_user_point_system';
    }

}
