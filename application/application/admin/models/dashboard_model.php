<?php

class Dashboard_model extends CI_Model {

    var $table;
    var $content;
    var $admin;
    

    public function __construct() {
        parent::__construct();
        $this->load->helper('cookie');

        
    }

    // ***************************************************************
    // Count Number of users
    // ***************************************************************
    function getNumberOfUsers() {
        $counterResult = $this->db->query("SELECT * FROM tbl_user");
        return $counterResult->num_rows();
    }

    /*    function getNumberOfPost() {
      $counterResult=$this->db->query("SELECT * FROM tbl_post");
      return $counterResult->num_rows();
      } */

    function getNumberOfCategories() {
        $counterResult = $this->db->query("SELECT * FROM tbl_category");
        return $counterResult->num_rows();
    }

    function getNumberOfCuisine() {
        $counterResult = $this->db->query("SELECT * FROM tbl_cuisine");
        return $counterResult->num_rows();
    }

    function getNumberOfFacility() {
        $counterResult = $this->db->query("SELECT * FROM tbl_facility");
        return $counterResult->num_rows();
    }

    function getNumberOfMusic() {
        $counterResult = $this->db->query("SELECT * FROM tbl_music");
        return $counterResult->num_rows();
    }

    function getNumberOfRestaurant() {
        $counterResult = $this->db->query("SELECT * FROM tbl_restaurant where estatus != 'Deleted'");
        return $counterResult->num_rows();
    }

    function getNumberOfDeals() {
        $where_condition = '';
        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $where_condition .= ' WHERE iRestaurantID = "' . $iRestaurantID . '"';
        }

        $counterResult = $this->db->query("SELECT * FROM tbl_deals " . $where_condition);
        return $counterResult->num_rows();
    }

    // ***************************************************************
    // Count Number of users Add on Today
    // ***************************************************************
}

?>