<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of cuisine_model
 * @author OpenXcell Technolabs
 */
class Cuisine_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_cuisine';
    }


    /**
     * To return only Active cuisines
     *
     * @return string
     * @throws Exception
     */
    function activeCuisine() {
        try {
            $res = $this->db->select()->get_where($this->table, array('eStatus' => 'Active'));
            $this->db->select('iCuisineID, vCuisineName, vCuisineImage')
                ->from($this->table)
                ->where('eStatus = "Active"');
            $res = $this->db->get();

            if ($res->num_rows() > 0) {
                return $res->result_array();
            }
        } catch (Exception $ex) {
            throw new Exception('Error in getActiveCuisine function - ' . $ex);
        }
    }
}
