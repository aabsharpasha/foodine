<?php

class Menu_Item_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_menu_item';
        $this->load->library('DatatablesHelper');
    }

    function getMenuItemsPagination() {
        $fields = array(
            "tr.vRestaurantName AS vRestaurantName",
            "tmi.vItemName AS vItemName",
            "tmi.tItemDesc AS tItemDesc",
            "tmi.dItemPrice AS dItemPrice",
            "tmi.vItemImage AS vItemImage",
            "tmic.vName AS itemCategory",
            "tmt.vName AS mealType",
            "DATE_FORMAT(tmi.tCreatedAt ,'%d %b %Y %h:%i %p') AS tCreatedAt",
            "DATE_FORMAT(tmi.tModifiedAt ,'%d %b %Y %h:%i %p') AS tModifiedAt",
            "tmi.iItemId AS DT_RowId",
            "tmi.iItemId AS iItemId",
            "tmi.eStatus AS eStatus"
        );
        $trJoin     = "LEFT JOIN tbl_restaurant AS tr ON tr.iRestaurantID=tmi.iRestaurantID";
        $tmicJoin   = "LEFT JOIN tbl_menu_item_category AS tmic ON tmic.iItemCategoryId=tmi.iItemCategoryId";
        $tmtJoin    = "LEFT JOIN tbl_meal_type AS tmt ON tmt.iMealTypeId=tmi.iMealTypeId";
        $fieldsStr  = implode(", ",$fields);
        $data = $this->datatableshelper->query("SELECT $fieldsStr  FROM tbl_menu_item AS tmi $trJoin $tmicJoin $tmtJoin WHERE tmi.estatus <> 'Deleted'");
        return $data;
    }
    
    function changeStatus($id) {
        $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iItemId = $id");
        if ($this->db->affected_rows() > 0)
            return true;
        else
            return false;
    }
    
    
    function softDelete($id) {
        $adid = implode(',', $id);
        $this->db->query("UPDATE $this->table SET eStatus = 'Deleted' WHERE iItemId in ($adid) ");
        if ($this->db->affected_rows() > 0) {
            return true;
        } else{
            return false;
        }
    }

    function getMenuItemById($id) {
        $result = $this->db->get_where($this->table, array('iItemId' => $id));
        if ($result->num_rows() > 0){
            $data   = $result->row_array();
            return $data;
        }else{
            return '';
        }
    }
    
    function getRestaurants(){
        $query    = "SELECT iRestaurantID,vRestaurantName FROM tbl_restaurant WHERE eStatus='Active' ORDER BY vRestaurantName ASC";
        $result   = $this->db->query($query);
        return $result->result_array();
    }
    
    function getMenuItemCategories(){
        $query    = "SELECT iItemCategoryId,vName FROM tbl_menu_item_category WHERE eStatus='Active' ORDER BY vName ASC";
        $result   = $this->db->query($query);
        return $result->result_array();
    }
    
    function getMealTypes(){
        $query    = "SELECT iMealTypeId,vName FROM tbl_meal_type ORDER BY vName ASC";
        $result   = $this->db->query($query);
        return $result->result_array();
    }
    
    function addMenuItem($data) {
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
    
    function editMenuItem($data) {
        $iItemId                = $data['iItemId'];
        $data['tModifiedAt']    = date("Y-m-d H:i:s");
        unset($data['iItemId']);
        unset($data['senditnow']);
        unset($data['action']);
        
        $this->db->update($this->table, $data, array('iItemId' => $iItemId));
        if ($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    function updateMenuItemImage($id, $image){
        $this->db->query("UPDATE $this->table SET vItemImage='$image' WHERE iItemId IN ($id) ");
    }
    
}

?>
