<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Seo_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_seo_meta_tags';
    }

    function getTagsPagination() {
        $where_condition = '';
        $fields = array(
            "tr.vRestaurantName AS vRestaurantName",
            "IF(tsmt.vPageTitle IS NULL, tr.vRestaurantName, tsmt.vPageTitle) AS vPageTitle",
            "IF(tsmt.vMetaTitle IS NULL, tr.vRestaurantName, tsmt.vMetaTitle) AS vMetaTitle",
            "IF(tsmt.vMetaDescription IS NULL, tr.vRestaurantName, tsmt.vMetaDescription) AS vMetaDescription",
            "IF(tsmt.vMetaKeywords IS NULL, tr.vRestaurantName, tsmt.vMetaKeywords) AS vMetaKeywords",
            "tr.iRestaurantID AS iRestaurantID",
            "tr.iRestaurantID AS DT_RowId"
        );

        $fieldsStr = implode(", ", $fields);
        $tsmtJoin = "LEFT JOIN tbl_seo_meta_tags AS tsmt ON tr.iRestaurantID=tsmt.iRestaurantID";
        $data = $this->datatableshelper->query("SELECT $fieldsStr  FROM tbl_restaurant AS tr  $tsmtJoin WHERE tr.eStatus<>'Deleted'" . $where_condition);
        return $data;
    }

    function getTagsData($iRestaurantID) {
        $fields = array(
            "tr.vRestaurantName AS vRestaurantName",
            "IF(tsmt.vPageTitle IS NULL, tr.vRestaurantName, tsmt.vPageTitle) AS vPageTitle",
            "IF(tsmt.vMetaTitle IS NULL, tr.vRestaurantName, tsmt.vMetaTitle) AS vMetaTitle",
            "IF(tsmt.vMetaDescription IS NULL, tr.vRestaurantName, tsmt.vMetaDescription) AS vMetaDescription",
            "IF(tsmt.vMetaKeywords IS NULL, tr.vRestaurantName, tsmt.vMetaKeywords) AS vMetaKeywords",
            "tr.iRestaurantID AS iRestaurantID"
        );

        $fieldsStr = implode(", ", $fields);
        $tsmtJoin = "LEFT JOIN tbl_seo_meta_tags AS tsmt ON tr.iRestaurantID=tsmt.iRestaurantID";
        $data = $this->db->query("SELECT $fieldsStr  FROM tbl_restaurant AS tr  $tsmtJoin WHERE tr.iRestaurantID='$iRestaurantID'");
        return $data->row_array();
    }

    function updateMetaTags($data) {

        $record = $this->db->query("SELECT iMetaTagId  FROM tbl_seo_meta_tags WHERE iRestaurantID='{$data['iRestaurantID']}'")->row_array();
        unset($data['action']);
        unset($data['senditnow']);
        $data["tModifiedAt"] = date("Y-m-d H:i:s");
        if (!empty($record["iMetaTagId"])) {
            $this->db->update("tbl_seo_meta_tags", $data, array('iMetaTagId' => $record["iMetaTagId"]));
        } else {
            $data["tCreatedAt"] = date("Y-m-d H:i:s");
            $this->db->insert("tbl_seo_meta_tags", $data);
        }
        return true;
    }

    function getStaticTagsPagination() {
        $fields = array(
            "tssmt.vPageName AS vPageName",
            "tssmt.vPageTitle AS vPageTitle",
            "tssmt.vMetaTitle AS vMetaTitle",
            "tssmt.vMetaDescription AS vMetaDescription",
            "tssmt.vMetaKeywords AS vMetaKeywords",
            "tssmt.iMetaTagId AS iMetaTagId",
            "tssmt.iMetaTagId AS DT_RowId"
        );

        $fieldsStr = implode(", ", $fields);
        $data = $this->datatableshelper->query("SELECT $fieldsStr  FROM tbl_seo_static_meta_tags AS tssmt");
        return $data;
    }

    function getStaticTagsData($iMetaTagId) {
        $data = $this->db->query("SELECT *  FROM tbl_seo_static_meta_tags WHERE iMetaTagId='$iMetaTagId'");
        return $data->row_array();
    }

    function updateStaticMetaTags($data) {

        unset($data['action']);
        unset($data['senditnow']);
        $data["tModifiedAt"] = date("Y-m-d H:i:s");
        $this->db->update("tbl_seo_static_meta_tags", $data, array('iMetaTagId' => $data["iMetaTagId"]));
        return true;
    }

    function getMetaTags($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                if ($page != '') {
                    if ($page == 'PDP') {
                        if (isset($restaurantId) && !empty($restaurantId)) {
                            $conditions = array('iRestaurantID' => $restaurantId);
                        }else {
                            return array();
                        } 
                    } else {
                        $this->table = 'tbl_seo_static_meta_tags';
                        $conditions = array('vPageName' => $page);
                    }
                    $res = $this->db->select()->get_where($this->table, $conditions);
                    if ($res->num_rows() > 0) {
                        return $res->row_array();
                    } else {
                        if ($page == 'PDP' && !empty($restaurantId)) {
                            return $this->_getRestaurantData($restaurantId);
                        } return array();
                    }
                } return array();
            } return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getActiveCuisine function - ' . $ex);
        }
    }

    private function _getRestaurantData($restaurantID) {
        $field = array(
            'tr.vRestaurantName AS vPageTitle',
            'tr.vRestaurantName AS vMetaTitle',
        );
        $tbl = 'tbl_restaurant AS tr';
        $condition = array("tr.iRestaurantID = $restaurantID");
        $condition = ' WHERE ' . implode(' AND ', $condition);
        $fields = implode(',', $field);
        $sql = 'SELECT ' . $fields . ' FROM ' . $tbl . $condition;
        $res = $this->db->query($sql);
        return $res->row_array();
    }

}

?>
