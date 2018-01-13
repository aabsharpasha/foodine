<?php

class Pagecontent_model extends CI_Model {

    var $pageContent_tbl;

    public function __construct() {

        parent::__construct();

        $this->load->helper('cookie');
        $this->table = 'tbl_pagecontent';
        $this->load->library('DatatablesHelper');
    }

    // **********************************************************************
    // Display List of SubCategory 
    // **********************************************************************
    function getPageContentDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    // **********************************************************************
    // add SubCategory
    // **********************************************************************
    function addSubCategory($postData) {
        extract($postData);

        $data = array('vSubCategoryName' => $vSubCategoryName,
            'iCategoryID' => $iCategoryID,
            'eStatus' => 'Active',
            'tCreatedAt' => date('Y-m-d H:i:s')
        );

        $query = $this->db->insert($this->table, $data);

        if ($this->db->affected_rows() > 0)
            return $this->db->insert_id();
        else
            return '';
    }

    /*     * **********************************************************************
      page Content Data
     * ********************************************************************** */

    function getPageContentDataById($iPageID) {
        $result = $this->db->get_where($this->table, array('iPageID' => $iPageID));

        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    // **********************************************************************
    // Edit SubCategory
    // **********************************************************************
    function editPageContent($postData) {
        extract($postData);

        $data = array('vPageTitle' => $vPageTitle,
            'tContent' => $tContent
        );


        $query = $this->db->update($this->table, $data, array('iPageID' => $iPageID));
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function get_paginationresult() {
        $data = $this->datatableshelper->query("SELECT  `vPageTitle` ,  case when length(`tContent`) > 150 then concat(substr(`tContent`, 1, 150), '...') else `tContent` end as `tContent` , DATE_FORMAT(tModifiedAt , '%d %b %Y %h:%i %p') as tModifiedAt, `iPageID` FROM `tbl_pagecontent`");
        return $data;
    }

}

?>