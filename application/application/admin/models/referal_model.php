<?php

class Referal_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_referal';
        $this->load->library('DatatablesHelper');
    }

    /*
     * GET PAGINATION RECORDS TO DISPLAY THE TABLE...
     */

    function get_paginationresult() {
        $extra_condition = '';

        $table = $fields = $qry = '';
        $condition = array();

        $table = $this->table . ' AS tpn';

        $fields .= 'tpn.iReferalID AS referalId';
        $fields .= ', tpn.iReferalID AS DT_RowId';
        $fields .= ', tpn.vReferalName AS referalName';
        $fields .= ', tpn.vReferalCode AS referalCode';
        $fields .= ', tpn.eStatus AS status';
        $fields .= ', IFNULL((SELECT COUNT(*) FROM tbl_referal_counter AS trc WHERE trc.iReferalID IN(tpn.iReferalID)),0) AS referalCounter';
        $fields .= ', DATE_FORMAT(tpn.tCreatedAt , "%d %b %Y %h:%i %p") as createdDate';

        if (!empty($condition))
            $condition = " WHERE " . implode(' AND ', $condition) . $extra_condition;
        else
            $condition = '';

        $orderBy = ''; // ORDER BY tpn.tCreatedAt DESC ';

        $qry .= "SELECT " . $fields . " FROM " . $table . $condition . $orderBy;

        $data = $this->datatableshelper->query($qry);

        return $data;
    }

    /*
     * DELETE THE DEALS EITHER ONE OR MORE...
     */

    function removeBooking($iBookingID) {
        $adid = implode(',', $iBookingID);

        $query = $this->db->query("DELETE from $this->table WHERE iBookingID IN ($adid) ");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    /*
     * TO CHANGE THE STATUS OF THE SELECTED RECORD...
     */

    function changeBookingStatus($iBookingID) {
        $qry = 'UPDATE ' . $this->table . ' SET eBookingStatus = IF(eBookingStatus = "Pending", "Accept", IF(eBookingStatus = "Accept", "Reject", "Accept") ) WHERE iTableBookID = "' . $iBookingID . '"';
        $query = $this->db->query($qry);
    }

    /*
     * TO CHECK THAT PUSH NOTIFICATION TITLE IS ALREADY EXITS OR NOT??
     */

    function checkCodeAvailable($title = '', $id = '') {
        try {
            if ($title != '') {
                if ($id != '')
                    $this->db->where_not_in('iReferalID', array($id));
                return $this->db->get_where('tbl_referal', array('vReferalCode' => $title))->num_rows() > 0 ? TRUE : FALSE;
            } return FALSE;
        } catch (Exception $ex) {
            throw new Exception('Error incheckNotificationNameAvailable function - ' . $ex);
        }
    }

    function saveReferal($request = array(), $id = '') {
        try {
            if (!empty($request)) {
                extract($request);
                if ($id != '') {
                    /*
                     * edit the record
                     */
                    $rec = array(
                        'vReferalName' => $vReferalName,
                        'vReferalCode' => $vReferalCode
                    );

                    $this->db->update($this->table, $rec, array('iReferalID' => $id));
                } else {
                    /*
                     * save the record
                     */
                    $rec = array(
                        'vReferalName' => $vReferalName,
                        'vReferalCode' => $vReferalCode,
                        'tCreatedAt' => date('Y-m-d H:i', time())
                    );
                    $this->db->insert($this->table, $rec);
                }
            }
        } catch (Exception $ex) {
            throw new Exception('Error in saveReferal function - ' . $ex);
        }
    }

    /*
     * GET THE RECORD BY ID
     */

    function getReferenceDataById($ID) {
        $result = $this->db->get_where($this->table, array('iReferalID' => $ID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*
     * DELETE THE DEALS EITHER ONE OR MORE...
     */

    function removeReferal($ID) {
        $adid = implode(',', $ID);

        $query = $this->db->query("DELETE FROM $this->table WHERE iReferalID IN ($adid) ");
        if ($this->db->affected_rows() > 0) {
            return $query;
        } else {
            return '';
        }
    }

    /*
     * TO CHANGE THE STATUS OF THE SELECTED RECORD...
     */

    function changeStatus($ID) {
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'active', 'deactive','active') WHERE iReferalID = '" . $ID . "'");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

}

?>
