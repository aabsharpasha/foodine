<?php

class Permission_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
    }

    private function _getTableValue($type) {
        switch ($type) {
            case 'type':
                $tbl = array(
                    'name' => 'tbl_admin_type',
                    'key' => 'iAdminTypeID'
                );
                break;

            case 'user':
                $tbl = array(
                    'name' => 'tbl_admin',
                    'key' => 'iAdminID'
                );
                break;

            case 'module':
                $tbl = array(
                    'name' => 'tbl_page_module',
                    'key' => 'iPageModuleID'
                );
                break;

            case 'page':
                $tbl = array(
                    'name' => 'tbl_page',
                    'key' => 'iPageID'
                );

            case 'permission':
                $tbl = array(
                    'name' => 'tbl_page_permission',
                    'key' => 'iAdminTypeID'
                );
                break;
        } return $tbl;
    }

    function getPermissionRecordById($type, $target_id) {
        if ($target_id != '') {
            $tbl = $this->_getTableValue($type);
            return $this->db->query('SELECT * FROM ' . $tbl['name'] . ' WHERE ' . $tbl['key'] . ' IN(' . $target_id . ')')->row_array();
        } return array();
    }

    function remove_all($type = '', $ids = array()) {
        if ($type != '') {
            $ids = implode(',', $ids);
            $tbl = $this->_getTableValue($type);

            $res = $this->db->query('DELETE FROM ' . $tbl['name'] . ' WHERE ' . $tbl['key'] . ' IN(' . $ids . ')');
            if ($this->db->affected_rows() > 0) {
                return 1;
            } return 0;
        }
    }

    function changeStatus($type, $ids) {
        if ($type != '') {
            if (is_array($ids))
                $ids = implode(',', $ids);
            $tbl = $this->_getTableValue($type);

            $res = $this->db->query('UPDATE ' . $tbl['name'] . ' SET eStatus = IF (eStatus = "Active", "Inactive", "Active") WHERE ' . $tbl['key'] . ' IN(' . $ids . ')');
            if ($this->db->affected_rows() > 0) {
                return 1;
            } return 0;
        }
    }

    /* permission types @START */

    function get_paginate_types() {
        $qry = "SELECT "
                . "vAdminTitle, "
                . "eStatus, "
                . "isDeveloper, "
                . "iAdminTypeID, "
                . "iAdminTypeID AS DT_RowId "
                . "FROM tbl_admin_type AS tdt";

        $ADMINTYPE = $this->session->userdata('ADMINTYPE');
        if ($ADMINTYPE > 1) {
            $qry .= ' WHERE tdt.isDeveloper IN(\'no\') AND tdt.iAdminTypeID NOT IN (' . $ADMINTYPE . ')';
        }

        return $this->datatableshelper->query($qry);
    }

    function addType($post_value = array()) {
        if (!empty($post_value)) {
            extract($post_value);

            $ins = array(
                'vAdminTitle' => $vAdminTitle
            );
            $this->db->insert('tbl_admin_type', $ins);
            return $this->db->insert_id();
        } return -1;
    }

    function editType($post_value = array()) {
        if (!empty($post_value)) {
            extract($post_value);

            $updt = array(
                'vAdminTitle' => $vAdminTitle
            );
            $this->db->update('tbl_admin_type', $updt, array('iAdminTypeID' => $iAdminTypeID));
            return $iAdminTypeID;
        } return -1;
    }

    /* permission types @END */

    /* permission modules @START */

    function get_paginate_modules() {
        $qry = "SELECT "
                . "vModuleName, "
                . "vModuleIcon, "
                . "eStatus, "
                . "isDeveloper, "
                . "iPageModuleID, "
                . "iPageModuleID AS DT_RowId "
                . "FROM tbl_page_module AS tpd";

        return $this->datatableshelper->query($qry);
    }

    function addModule($post_value = array()) {
        if (!empty($post_value)) {
            extract($post_value);

            $ins = array(
                'vModuleName' => $vModuleName,
                'vModuleIcon' => $vModuleIcon
            );
            if (isset($isDeveloper) && $isDeveloper) {
                $ins['isDeveloper'] = $isDeveloper;
            }
            $this->db->insert('tbl_page_module', $ins);
            return $this->db->insert_id();
        } return -1;
    }

    function editModule($post_value = array()) {
        if (!empty($post_value)) {
            extract($post_value);

            $updt = array(
                'vModuleName' => $vModuleName,
                'vModuleIcon' => $vModuleIcon
            );
            if (isset($isDeveloper) && $isDeveloper) {
                $updt['isDeveloper'] = $isDeveloper;
            }
            $this->db->update('tbl_page_module', $updt, array('iPageModuleID' => $iPageModuleID));
            return $iPageModuleID;
        } return -1;
    }

    /* permission modules @END */

    /* permission pages @START */

    function get_paginate_page() {
        $qry = "SELECT "
                . "(SELECT vModuleName FROM tbl_page_module AS tpm WHERE tpm.iPageModuleID = tp.iPageModuleID) AS vModuleName, "
                . "vPageTitle, "
                . "vPageURL, "
                . "eStatus, "
                . "iPageID, "
                . "iPageID AS DT_RowId "
                . "FROM tbl_page AS tp";

        return $this->datatableshelper->query($qry);
    }

    function addPage($post_value = array()) {
        if (!empty($post_value)) {
            extract($post_value);

            $ins = array(
                'iPageModuleID' => $iPageModuleID,
                'vPageTitle' => $vPageTitle,
                'vPageURL' => $vPageURL
            );

            $this->db->insert('tbl_page', $ins);
            return $this->db->insert_id();
        } return -1;
    }

    function editPage($post_value = array()) {
        if (!empty($post_value)) {
            extract($post_value);

            $updt = array(
                'iPageModuleID' => $iPageModuleID,
                'vPageTitle' => $vPageTitle,
                'vPageURL' => $vPageURL
            );

            $this->db->update('tbl_page', $updt, array('iPageModuleID' => $iPageModuleID));
            return $iPageModuleID;
        } return -1;
    }

    /* permission pages @END */

    function editPermission($post_value = array()) {
        if (!empty($post_value)) {
            extract($post_value);
            //mprd(@$permission);
            if (isset($permission) && !empty($permission)) {
                $this->db->delete('tbl_page_permission', array('iAdminTypeID' => $iAdminTypeID));

                foreach ($permission as $page_id => $page_action) {
                    foreach ($page_action as $action_id => $action_value) {
                        $ins = array(
                            'iAdminTypeID' => $iAdminTypeID,
                            'iPageID' => $page_id,
                            'iPageActionID' => $action_id
                        );
                        $this->db->insert('tbl_page_permission', $ins);
                    }
                }
            }
        } return -1;
    }

    function get_paginate_user() {
        $qry = "SELECT "
                . "CONCAT(vFirstName,' ',vLastName) AS full_name, "
                . "vEmail, "
                . "(SELECT vAdminTitle FROM tbl_admin_type tat WHERE tat.iAdminTypeID = ta.iAdminTypeID ) AS admin_type, "
                . "vPassword, "
                . "eStatus, "
                . "iAdminID, "
                . "iAdminID AS DT_RowId "
                . "FROM tbl_admin AS ta "
                . "WHERE iAdminTypeID > '" . $this->session->userdata('ADMINTYPE') . "'";

        return $this->datatableshelper->query($qry);
    }

    function get_all_permission_type() {
        return $this->db->get_where('tbl_admin_type', array('isDeveloper' => 'no'))->result_array();
    }

    function get_all_restaurants() {
        return $this->db->get_where('tbl_restaurant', array('eStatus' => 'Active'))->result_array();
    }

    function addUser($post_value) {
        if (!empty($post_value)) {
            extract($post_value);
            /* check email is already exists or not */
            $has_email = (($this->db->get_where('tbl_admin', array('vEmail' => $vEmail))->num_rows()) > 0);
            $password = isset($vPassword) ? $vPassword : random_string('alnum', 8);

            if (!$has_email) {
                /* add record to admin table */
                $iAdminTypeID = (int) $iAdminTypeID;
                $ins = array(
                    'vFirstName' => $vFirstName,
                    'vLastName' => $vLastName,
                    'vEmail' => $vEmail,
                    'vPassword' => md5($password),
                    'iAdminTypeID' => $iAdminTypeID
                );
                if ($iAdminTypeID == 3) {
                    $ins['iRestaurantID'] = $iRestaurantID;
                }
                $this->db->insert('tbl_admin', $ins);

                $get_type_name = $this->db->get_where('tbl_admin_type', array('iAdminTypeID' => $iAdminTypeID))->row_array();
                $get_type_name = $get_type_name['vAdminTitle'];

                /* send mail to user with email and password */
                $this->load->library('maillib');
                $param = array(
                    '%MAILSUBJECT%' => 'HungerMafia : Welcome',
                    '%LOGO_IMAGE%' => DOMAIN_URL . '/images/hungermafia.png',
                    '%USER_TYPE%' => $get_type_name,
                    '%USERNAME%' => $vEmail,
                    '%PASSWORD%' => $password
                );
                //$tmplt = DIR_ADMIN_VIEW . $this->controller . '/email/new_restaurant.php';
                $tmplt = DIR_ADMIN_VIEW . 'permission/email/new_user.php';
                $subject = 'HungerMafia: Welcome';
                $to = $vEmail;
                //$this->smtpmail_model->send($to, $subject, $tmplt, $param);
                $this->maillib->sendMail($to, $subject, $tmplt, $param, false, array(), array(CC_EMAIL_ID));

                return 1;
            } return -2; // email already exists!!
        } return -1;
    }

    function editUser($post_value) {
        if (!empty($post_value)) {
            extract($post_value);
            /* check email is already exists or not */
            $has_email = (($this->db->get_where('tbl_admin', array('vEmail' => $vEmail, 'iAdminID !=' => $iAdminID))->num_rows()) > 0);
            //$password = random_string('alnum', 8);

            if (!$has_email) {
                /* add record to admin table */
                $iAdminTypeID = (int) $iAdminTypeID;
                $updt = array(
                    'vFirstName' => $vFirstName,
                    'vLastName' => $vLastName,
                    'vEmail' => $vEmail,
                    'iAdminTypeID' => $iAdminTypeID
                );
                if ($iAdminTypeID == 3) {
                    $ins['iRestaurantID'] = $iRestaurantID;
                }

                $this->db->update('tbl_admin', $updt, array('iAdminID' => $iAdminID));
                return 1;
            } return -2; // email already exists!!
        } return -1;
    }

}

?>