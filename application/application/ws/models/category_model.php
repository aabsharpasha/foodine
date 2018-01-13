<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of category_model
 * @author OpenXcell Technolabs
 */
class Category_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_category';
    }


    /**
     * To return only Active category/interest
     *
     * @return string
     * @throws Exception
     */
    function activeInterest() {
        try {
            $res = $this->db->select()->get_where($this->table, array('eStatus' => 'Active'));
            $this->db->select('iCategoryID, vCategoryName, vCategoryImage')
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
