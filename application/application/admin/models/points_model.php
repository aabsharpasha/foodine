<?php

class Points_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_user_point_system';
        $this->load->library('DatatablesHelper');
    }

    /*
     * GET ALL THE RECORDS FROM THE DEALS TABLE...
     */

    function getPointsDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /*
     * GET THE RECORD BY ID
     */

    function getPointsDataById($iTargetID) {
        $result = $this->db->get_where($this->table, array('iUserPointSystemID' => $iTargetID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*
     * GET PAGINATION RECORDS TO DISPLAY THE TABLE...
     */

    function get_paginationresult($requestId = '') {

        $table = $fields = $qry = '';
        $condition = array();

        $table = 'tbl_user_point_system AS trp';

        $fields .= 'iUserPointSystemID';
        $fields .= ', iUserPointSystemID AS DT_RowId ';
        $fields .= ', vType';
        $fields .= ', iPoints';

        if ($requestId !== '') {
            $condition[] = 'trp.iUserPointSystemID = \'' . $requestId . '\'';
        }

        if (!empty($condition)) {
            $condition = " WHERE " . implode(' AND ', $condition);
        } else {
            $condition = NULL;
        }

        $qry .= "SELECT " . $fields . " FROM " . $table . $condition;

        $data = $this->datatableshelper->query($qry);

        return $data;
    }

    function checkPointNameAvailable($name = '', $id = '') {
        if ($name != '' && $id != '') {
            $this->db->where_not_in('iUserPointSystemID', $id);
            $res = $this->db->get_where('tbl_user_point_system', array('vType' => $name));

            return $res->num_rows() == 0 ? TRUE : FALSE;
        }
    }

    function editPoints($postValue) {
        if (!empty($postValue)) {
            $updt = array(
                'vType' => $postValue['vType'],
                'iPoints' => $postValue['iPoints']
            );

            $this->db->update('tbl_user_point_system', $updt, array('iUserPointSystemID' => $postValue['iUserPointSystemID']));
        }
    }

}

?>
