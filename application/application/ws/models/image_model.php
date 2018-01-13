<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of user_model
 * @author OpenXcell Technolabs
 */
class Image_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_restaurant_image';
        $this->load->library('DatatablesHelper');
    }

    /*
     * 
     */

    function restaurant_photo_paginate($iRestaurantID) {
        $qry = "SELECT vPictureName , eStatus ,  DATE_FORMAT(tCreatedAt , '%d %b %Y %h:%i %p') as tCreatedAt , iPictureID, iRestaurantID , iPictureID AS DT_RowId FROM tbl_restaurant_image as r where iRestaurantID = $iRestaurantID";
        $data = $this->datatableshelper->query($qry);
        return $data;
    }

    function restaurant_menu_paginate($iRestaurantID) {
        $qry = "SELECT eMenuType, vPictureName , eStatus ,  DATE_FORMAT(tCreatedAt , '%d %b %Y %h:%i %p') as tCreatedAt , iMenuPictureID, iRestaurantID , iMenuPictureID AS DT_RowId FROM tbl_restaurant_menu_image as r where iRestaurantID = $iRestaurantID";
        $data = $this->datatableshelper->query($qry);
        return $data;
    }

    /*
     * to add the restaurant photo
     */

    function add_restaurant_photo($postValues = array()) {

        if (!empty($postValues)) {

            $res_id = $postValues['iRestaurantID'];
            $images = $postValues['images'];

            for ($i = 0; $i < count($images); $i++) {
                $insArry = array(
                    'iRestaurantID' => $res_id,
                    'vPictureName' => $images[$i],
                    'eStatus' => 'Active',
                    'tCreatedAt' => date('Y-m-d H:i:s')
                );

                $this->db->insert($this->table, $insArry);
            }
        } else {
            return '';
        }
    }

    /*
     * to add the restaurant photo
     */

    function add_restaurant_menu($postValues = array()) {

        if (!empty($postValues)) {

            $res_id = $postValues['iRestaurantID'];
            $menu_type = $postValues['menu_type'];
            $images = $postValues['images'];

            for ($i = 0; $i < count($images); $i++) {
                $insArry = array(
                    'iRestaurantID' => $res_id,
                    'eMenuType' => $menu_type,
                    'vPictureName' => $images[$i],
                    'eStatus' => 'Active',
                    'tCreatedAt' => date('Y-m-d H:i:s')
                );

                $this->db->insert('tbl_restaurant_menu_image', $insArry);
            }
        } else {
            return '';
        }
    }

    /*
     * to edti the restaurant photo
     */

    function edit_restaurant_photo($postValues = array()) {

        if (!empty($postValues)) {

            $pic_id = $postValues['iPictureID'];
            $res_id = $postValues['iRestaurantID'];
            $images = $postValues['images'];

            for ($i = 0; $i < count($images); $i++) {

                $updtArry = array(
                    'iRestaurantID' => $res_id,
                    'vPictureName' => $images[$i]
                );

                $this->db->update($this->table, $updtArry, array('iPictureID' => $pic_id));
            }
        } else {
            return '';
        }
    }

    /*
     * to edti the restaurant photo
     */

    function edit_restaurant_menu($postValues = array()) {


        if (!empty($postValues)) {

            $mpic_id = $postValues['iMenuPictureID'];
            $m_type = $postValues['menu_type'];
            $res_id = $postValues['iRestaurantID'];
            $images = $postValues['images'];

            for ($i = 0; $i < count($images); $i++) {

                $updtArry = array(
                    'iRestaurantID' => $res_id,
                    'eMenuType' => $m_type,
                    'vPictureName' => $images[$i]
                );

                $this->db->update('tbl_restaurant_menu_image', $updtArry, array('iMenuPictureID' => $mpic_id));
            }
        } else {
            return '';
        }
    }

    /*
     * to remove the restaurant image 
     */

    function removeRestarantPhoto($rows) {
        $adid = implode(',', $rows);

        $res_data = array();

        $result = $this->db->query("SELECT iRestaurantID , iPictureID , vPictureName FROM tbl_restaurant_image WHERE iPictureID IN (" . $adid . ")");

        if ($this->db->affected_rows() > 0) {
            $this->load->helper("file");
            $row = $result->result_array();
            foreach ($row as $value) {

                $baseDIR = DOC_ROOT . "/images/restaurantPhoto/" . $value['iRestaurantID'] . "/";
                //exit;
                //if (is_dir($baseDIR)) {
                //$baseDIR . $value['vPictureName'];
                $this->unlinkimage($baseDIR . $value['vPictureName']);
                $this->unlinkimage($baseDIR . "thumb/" . $value['vPictureName']);
                //}
            }

            //mprd($adid);
            $query = $this->db->query("DELETE FROM $this->table WHERE iPictureID IN (" . $adid . ")");

            return $row;
        } else
            return '';
    }

    /*
     * to remove the restaurant image 
     */

    function removeRestarantMenu($rows) {
        $adid = implode(',', $rows);

        $res_data = array();

        $result = $this->db->query("SELECT iRestaurantID , iMenuPictureID , vPictureName FROM tbl_restaurant_menu_image WHERE iMenuPictureID IN (" . $adid . ")");

        if ($this->db->affected_rows() > 0) {
            $this->load->helper("file");
            $row = $result->result_array();
            foreach ($row as $value) {

                $baseDIR = DOC_ROOT . "/images/restaurantMenu/" . $value['iRestaurantID'] . "/";
                //exit;
                //if (is_dir($baseDIR)) {
                //$baseDIR . $value['vPictureName'];
                $this->unlinkimage($baseDIR . $value['vPictureName']);
                $this->unlinkimage($baseDIR . "thumb/" . $value['vPictureName']);
                //}
            }

            //mprd($adid);
            $query = $this->db->query("DELETE FROM tbl_restaurant_menu_image WHERE iMenuPictureID IN (" . $adid . ")");

            return $row;
        } else
            return '';
    }

    /*
     * to get the picture informations by id
     */

    function restaurantPhotoById($iPictureID) {
        if ($iPictureID !== '') {
            $res = $this->db->get_where($this->table, array('iPictureID' => $iPictureID));
            //echo $this->db->last_query();
            return $res->row_array();
        } else {
            return '';
        }
    }

    /*
     * to get the picture informations by id
     */

    function restaurantMenuById($iPictureID) {
        if ($iPictureID !== '') {
            $res = $this->db->get_where('tbl_restaurant_menu_image', array('iMenuPictureID' => $iPictureID));
            //echo $this->db->last_query();
            return $res->row_array();
        } else {
            return '';
        }
    }

    // **********************************************************************
    // Display List of Image
    // **********************************************************************
    function getImageDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getImageDataAllbyRes($iRestaurantID) {
        $this->db->from($this->table);
        $this->db->where('iRestaurantID', $iRestaurantID);

        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    // **********************************************************************
    // Display List of Image
    // **********************************************************************
    function getCategoryDataAll() {
        $this->db->from('tbl_category');
        $this->db->where('eStatus', 'Active');
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /*     * **********************************************************************
      Image Setting(Getting user details by admin_id(Selected or inserted)
     * ********************************************************************** */

    function getImageDataById($iPictureID) {
        $result = $this->db->get_where($this->table, array('iPictureID' => $iPictureID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    function getRestaurantDataById($iRestaurantID) {
        $result = $this->db->get_where('tbl_restaurant', array('iRestaurantID' => $iRestaurantID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*     * **********************************************************************
      Image Setting(Getting user details by admin_id(Selected or inserted)
     * ********************************************************************** */

    function getCategoryDataById($iPictureID) {
        $result = $this->db->get_where('tbl_restaurant_image_category', array('iPictureID' => $iPictureID));
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    // **********************************************************************
    // Image Status
    // **********************************************************************
    function changeImageStatus($iPictureID) {
        //$updateData = array('admin_status' => 'IF (admin_status = "Active", "Inactive","Active")');
        //$query = $this->db->update($this->admin_tbl,$updateData, array('admin_role ' => $admin_role));
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iPictureID = $iPictureID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function changeImageStatusMenu($iPictureID) {
        //$updateData = array('admin_status' => 'IF (admin_status = "Active", "Inactive","Active")');
        //$query = $this->db->update($this->admin_tbl,$updateData, array('admin_role ' => $admin_role));
        $query = $this->db->query("UPDATE tbl_restaurant_menu_image SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iMenuPictureID = $iPictureID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeImage($iPictureID) {
        $adid = implode(',', $iPictureID);

        $res_data = array();

        $result = $this->db->query("select iRestaurantID , iPictureID , vPictureName from tbl_restaurant_image where iPictureID in ($adid)");
        if ($result->num_rows() > 0)
            $res_data = $result->result_array();
        else
            $res_data = array();

        //mprd($adid);
        $query = $this->db->query("DELETE from $this->table WHERE iPictureID in ($adid) ");
        //$query = $this->db->delete($this->table, array('iPictureID' => $iPictureID));
        //mprd($this->db->last_query());
        if ($this->db->affected_rows() > 0) {
            $this->load->helper("file");
            if (!empty($res_data)) {
                foreach ($res_data as $value) {
                    $this->unlinkimage(UPLOADS . "restaurant/" . $value['iRestaurantID'] . "/image/" . $value['vPictureName']);
                    $this->unlinkimage(UPLOADS . "restaurant/" . $value['iRestaurantID'] . "/image/thumb/" . $value['vPictureName']);
                }
            }

            return $query;
        } else
            return '';
    }

    /*
     * Delete All Restaurant Photos
     */

    function deleteAllPhotos($iPictureID) {
        $adid = implode(',', $iPictureID);

        $res_data = array();

        $result = $this->db->query("select iRestaurantID , iPictureID , vPictureName from tbl_restaurant_image where iPictureID in ($adid)");
        if ($result->num_rows() > 0)
            $res_data = $result->result_array();
        else
            $res_data = array();

        //mprd($adid);
        $query = $this->db->query("DELETE from $this->table WHERE iPictureID IN ($adid) ");
        //$query = $this->db->delete($this->table, array('iPictureID' => $iPictureID));
        //mprd($this->db->last_query());
        if ($this->db->affected_rows() > 0) {
            $this->load->helper("file");
            if (!empty($res_data)) {
                foreach ($res_data as $value) {
                    $this->unlinkimage(UPLOADS . "restaurantPhoto/" . $value['iRestaurantID'] . "/image/" . $value['vPictureName']);
                    $this->unlinkimage(UPLOADS . "restaurantPhoto/" . $value['iRestaurantID'] . "/image/thumb/" . $value['vPictureName']);
                }
            }

            return $query;
        } else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function deleteFollowerAll($iPictureID) {
        $adid = implode(',', $iPictureID);
        //mprd($adid);
        //$uid = $this->input->post('uid');

        $query = $this->db->query("DELETE from tbl_restaurant_image_follow WHERE iFollowID in ($adid)");
        if ($this->db->affected_rows() > 0) {
            return $query;
        } else
            return '';
    }

    //http://localhost/blacklist/admin/image/restaurant_photo_paginate/1
    // **********************************************************************
    // remove admin
    // **********************************************************************
    function deleteFollowingAll($iPictureID) {
        $adid = implode(',', $iPictureID);
        //mprd($adid);
        //$uid = $this->input->post('uid');


        $query = $this->db->query("DELETE from tbl_restaurant_image_follow WHERE iFollowID in ($adid)");
        if ($this->db->affected_rows() > 0) {
            return $query;
        } else
            return '';
    }

    // **********************************************************************
    // add admin
    // **********************************************************************
    function addImage($postData) {
        extract($postData);
        $vPassword = md5($vPassword);

        $data = array(
            'vImageName' => $vImageName,
            'vEmail' => $vEmail,
            'eStatus' => 'Active',
            'vPassword' => $vPassword,
            'vCityName' => $vCityName,
            'vStateName' => $vStateName,
            'vCountryName' => $vCountryName,
            'tAddress' => $tAddress,
            'vContactNo' => $vContactNo,
            'tDescription' => $tDescription,
            'vFbLink' => $vFbLink,
            'vInstagramLink' => $vInstagramLink,
            'tCreatedAt' => date('Y-m-d H:i:s')
        );
        $query = $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0) {
            $iPictureID = $this->db->insert_id();
            if (isset($iCategoryID)) {
                if (!empty($iCategoryID)) {
                    foreach ($iCategoryID as $key => $value) {
                        $data1 = array('iCategoryID' => $value, 'iPictureID' => $iPictureID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                        $query = $this->db->insert('tbl_restaurant_image_category', $data1);
                    }
                }
            }
            return $iPictureID;
        } else
            return '';
    }

    // **********************************************************************
    // Edit admin
    // **********************************************************************
    function editImage($postData) {

        extract($postData);
        $data = array(
            'vImageName' => $vImageName,
            'vEmail' => $vEmail,
            'vPassword' => $vPassword,
            'vCityName' => $vCityName,
            'vStateName' => $vStateName,
            'vCountryName' => $vCountryName,
            'tAddress' => $tAddress,
            'vContactNo' => $vContactNo,
            'tDescription' => $tDescription,
            'vFbLink' => $vFbLink,
            'vInstagramLink' => $vInstagramLink
        );
        if (isset($vPassword) && $vPassword != "") {
            $data['vPassword'] = md5($vPassword);
        }

        $query = $this->db->update($this->table, $data, array('iPictureID' => $iPictureID));


        if (isset($iCategoryID)) {
            if (!empty($iCategoryID)) {
                $query = $this->db->query("DELETE from tbl_restaurant_image_category WHERE iPictureID = $iPictureID");
                foreach ($iCategoryID as $key => $value) {
                    $data1 = array('iCategoryID' => $value, 'iPictureID' => $iPictureID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                    $query = $this->db->insert('tbl_restaurant_image_category', $data1);
                }
            }
        }

        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function editImageData($data, $iPictureID = '') {
        if ($iPictureID != '') {
            $query = $this->db->update($this->table, $data, array('iPictureID' => $iPictureID));
            if ($this->db->affected_rows() > 0)
                return $query;
            else
                return '';
        }else {
            return '';
        }
    }

    ############################################################
    #Check EMail#
############################################################

    public function checkImageEmailAvailable($vEmail, $iPictureID = '') {
        if ($iPictureID != "") {
            $result = $this->db->query("SELECT `iPictureID`  FROM `tbl_restaurant_image` WHERE (`vEmail` = '" . $vEmail . "') AND  `iPictureID` != $iPictureID");
        } else {
            $this->db->where('vEmail', $vEmail);
            //$this->db->or_where('vImagename', $vEmail);
            $result = $this->db->get($this->table);
        }

        if ($result->num_rows() >= 1)
            return 0;
        else
            return 1;
    }

    function get_paginationresult($iRestaurantID) {
        $data = $this->datatableshelper->query("SELECT vPictureName , eStatus ,  DATE_FORMAT(tCreatedAt , '%d %b %Y %h:%i %p') as tCreatedAt , iPictureID, iRestaurantID , iPictureID AS DT_RowId FROM tbl_restaurant_image as r where iRestaurantID = $iRestaurantID");
        return $data;
    }

    function unlinkimage($path) {
        if (file_exists($path))
            unlink($path);
    }

}

?>
