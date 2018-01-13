<?php

class Menu_Item_Category_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_menu_item_category';
        $this->load->library('DatatablesHelper');
    }

    function getMenuItemCategoryPagination() {
        $fields = array(
            "tmic.vName AS vName",
            "tmic.tCreatedAt",
            "DATE_FORMAT(tmic.tCreatedAt ,'%d %b %Y %h:%i %p') AS createdAt",
            "tmic.iItemCategoryId AS DT_RowId",
            "tmic.iItemCategoryId AS iItemCategoryId",
            "tmic.eStatus AS eStatus"
        );
        $fieldsStr  = implode(", ",$fields);
        $data = $this->datatableshelper->query("SELECT $fieldsStr  FROM tbl_menu_item_category AS tmic WHERE tmic.estatus <> 'Deleted'");
        return $data;
    }
    
    function changeStatus($id) {
        $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iItemCategoryId = $id");
        if ($this->db->affected_rows() > 0)
            return true;
        else
            return false;
    }
    
    
    function softDelete($id) {
        $adid = implode(',', $id);
        $this->db->query("UPDATE $this->table SET eStatus = 'Deleted' WHERE iItemCategoryId in ($adid) ");
        if ($this->db->affected_rows() > 0) {
            return true;
        } else{
            return false;
        }
    }

    function getMenuItemCategoryById($id) {
        $result = $this->db->get_where($this->table, array('iItemCategoryId' => $id));
        if ($result->num_rows() > 0){
            $data   = $result->row_array();
            return $data;
        }else{
            return '';
        }
    }
    
    function addMenuItemCategory($data) {
        $data['tCreatedAt']     = date("Y-m-d H:i:s");
        $data['tModifiedAt']    = date("Y-m-d H:i:s");
        unset($data['senditnow']);
        unset($data['action']);
        
        $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }else{
            return '';
        }
    }
    
    function editMenuItemCategory($data) {
        $iItemCategoryId        = $data['iItemCategoryId'];
        $data['tModifiedAt']    = date("Y-m-d H:i:s");
        unset($data['iItemCategoryId']);
        unset($data['senditnow']);
        unset($data['action']);
        
        $this->db->update($this->table, $data, array('iItemCategoryId' => $iItemCategoryId));
        if ($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
}

?>
