<?php

class Deals_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_deals';
        $this->load->library('DatatablesHelper');
        $this->load->helper('string');
    }

    /*
     * GET ALL THE RECORDS FROM THE DEALS TABLE...
     */

    function getDealsDataAll() {
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

    function getDealsDataById($iDealID) {
        $result = $this->db->get_where($this->table, array('iDealID' => $iDealID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*
     * GET THE RECORD BY ID
     */

    function getDealsDataViewById($iDealID) {
        //$result = $this->db->get_where($this->table . ' td, tbl_restaurant AS tr', array('td.iDealID' => $iDealID, 'td.iRestaurantID = '));

        $fields[] = 'tr.vRestaurantName';
        $fields[] = 'td.iDealID';
        $fields[] = 'td.vOfferText';
        $fields[] = 'td.tOfferDetail';
        $fields[] = 'td.tTermsOfUse';
        $fields[] = 'td.vDaysAllow';
        //$fields[] = 'td.eSpecific';
        $fields[] = 'DATE_FORMAT(td.dtStartDate , \'%d %b %Y %h:%i %p\') AS dtStartDate';
        $fields[] = 'DATE_FORMAT(td.dtExpiryDate , \'%d %b %Y %h:%i %p\') AS dtExpiryDate';
        $fields[] = 'td.vDealCode';

        $qry = 'SELECT ' . implode(',', $fields)
                . ' FROM tbl_deals td, tbl_restaurant tr '
                . 'WHERE td.iDealID = "' . $iDealID . '" '
                . 'AND tr.iRestaurantID = td.iRestaurantID';
        $result = $this->db->query($qry);
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*
     * GET PAGINATION RECORDS TO DISPLAY THE TABLE...
     */

    function get_paginationresult() {
        $where_condition = '';
        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $where_condition .= ' AND td.iRestaurantID = "' . $iRestaurantID . '"';
        }
        $qry = "SELECT "
                . "td.vOfferText as vOfferText,"
                . "vDealImage,  "
                . "vRestaurantName, "
                . "tr.tAddress2 as tAddress, "
                . "vDealCode, "
                . "td.dtStartDate as dtStartDate1, "
                . "td.dtExpiryDate as dtExpiryDate1, "
                . "(SELECT COUNT(tdl.iDealLikeID) "
                . "FROM tbl_deals_like AS tdl "
                . "WHERE td.iRestaurantID = tdl.iRestaurantID "
                . "AND td.iDealID = tdl.iDealID) AS total_like, "
                . "DATE_FORMAT(td.dtStartDate, '%d %b %Y') as dtStartDate, "
                . "DATE_FORMAT(td.dtExpiryDate, '%d %b %Y') as dtExpiryDate, "
                . "td.eStatus AS eStatus, "
                . "td.iDealID as iDealID, "
                . "iDealID AS DT_RowId "
                . "FROM tbl_deals AS td, "
                . "tbl_restaurant AS tr "
                . "WHERE td.iRestaurantID = tr.iRestaurantID "
                . $where_condition;

        $data = $this->datatableshelper->query($qry);
        return $data;
    }
    
    function changeAvailedStatus($id){

        $qry = 'UPDATE tbl_deals_code SET eStatus = IF(eStatus = "availed", "unavailed", "availed" ) WHERE iCodeId = "'.$id.'"';
        $this->db->query($qry);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return '';
        }
    }
    /*
     * GET PAGINATION RECORDS TO DISPLAY THE TABLE...
     */

    function get_like_paginationresult($iDealID) {
        $where_condition = '';
        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $where_condition .= ' AND tdl.iRestaurantID = "' . $iRestaurantID . '"';
        }

        $qry = 'SELECT vOffertext, vRestaurantName, vFullName, DATE_FORMAT(tdl.tCreatedAt , "%d %b %Y %h:%i %p") as tCreatedAt FROM tbl_deals_like AS tdl, tbl_deals AS td, tbl_restaurant AS tr, tbl_user AS tu WHERE tdl.iUserID = tu.iUserID AND tdl.iRestaurantID = tr.iRestaurantID AND td.iDealID = tdl.iDealID AND tdl.iDealID = "' . $iDealID . '"' . $where_condition . ' ';

        //echo $qry;
        $data = $this->datatableshelper->query($qry);
        return $data;
    }

    /*
     * DELETE THE DEALS EITHER ONE OR MORE...
     */

    function removeDeals($iDealID) {
        $adid = implode(',', $iDealID);

        $query = $this->db->query("DELETE from $this->table WHERE iDealID IN ($adid) ");
        if ($this->db->affected_rows() > 0) {
            /*
             * REMOVE DIRECTORY
             */
            $ids = $iDealID;
            if (!empty($ids)) {
                $basePath = DOC_ROOT . '/images/deal/';
                for ($i = 0; $i < count($ids); $i++) {
                    $subFold = $basePath . $ids[$i] . '/';
                    if (is_dir($subFold)) {

                        $subThumb = $subFold . 'thumb/';
                        $thumbFile = glob($subThumb . '*');
                        foreach ($thumbFile as $file) {
                            if (is_file($file))
                                unlink($file);
                        }
                        rmdir($subThumb);

                        $files = glob($subFold . '*');
                        foreach ($files as $f) {
                            if (is_file($f))
                                unlink($f);
                        }
                        rmdir($subFold);
                    }
                }
            }

            return $query;
        }
        else {
            return '';
        }
    }

    /*
     * TO CHANGE THE STATUS OF THE SELECTED RECORD...
     */

    function changeDealsStatus($iDealID) {
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iDealID = '" . $iDealID . "'");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    /*
     * GET THE LIST OF RESTAURANTS...
     */

    function getRestaurantList() {
        $where_condition = '';
        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $where_condition .= ' AND iRestaurantID = "' . $iRestaurantID . '"';
        }

        $data = $this->datatableshelper->query("SELECT * FROM tbl_restaurant WHERE eStatus = 'Active' " . $where_condition);

        return $data['aaData'];
    }

    /*
     * TO INSERT THE DEAL RECORD
     */

    function addDeals($fieldRecord) {
        extract($fieldRecord);

        if (isset($vDaysAllow) && $vDaysAllow != '') {
            $vDaysAllow = implode(',', $vDaysAllow);
        } else {
            $vDaysAllow = '';
        }

        //$eSpecific = isset($eSpecific) ? 'yes' : 'no';


        $data = array(
            'iRestaurantID' => $iRestaurantID,
            'vOfferText' => $vOfferText,
            'vDealCode' => $vDealCode,
            'tOfferDetail' => $tOfferDetail,
            'tTermsOfUse' => $tTermsOfUse,
            'vDaysAllow' => $vDaysAllow,
            //'eSpecific' => $eSpecific,
            'iOfferType' => $iOfferType,
            'dtStartDate' => date('Y-m-d H:i:s', strtotime($dtStartDate)),
            'dtExpiryDate' => date('Y-m-d H:i:s', strtotime($dtExpiryDate)),
            'eStatus' => 'Active',
            'tCreatedAt' => date('Y-m-d H:i:s')
        );

        //$data['vDealCode'] = $this->checkDealCode();

        $query = $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0){
            $dealId = $this->db->insert_id();
//            $this->db->query("UPDATE tbl_deals SET vDealCode=CONCAT('HMD',LPAD(iDealID, 5, '0'))  WHERE iDealID= '$dealId'");
            $this->updateSolr($iRestaurantID);
            return $dealId;
        }
        else
            return '';
    }

    /**
     * [checkDealCode check deal code is exits or not]
     * @return [type] [description]
     */
    function checkDealCode() {

        $vDealCode = random_string('alnum', 10);
        $result = $this->db->get_where('tbl_deals', array('vDealCode' => $vDealCode));
        if ($result->num_rows() > 0)
            $this->checkDealCode();
        else
            return $vDealCode;
    }

    /*
     * TO EDIT THE RECORDS...
     */

    function editDeals($fieldRecord) {
        extract($fieldRecord);

        if (isset($vDaysAllow) && $vDaysAllow != '') {
            $vDaysAllow = implode(',', $vDaysAllow);
        } else {
            $vDaysAllow = '';
        }

        //$eSpecific = isset($eSpecific) ? 'yes' : 'no';

        $data = array(
            'iRestaurantID' => $iRestaurantID,
            'vDealCode' => $vDealCode,
            'vOfferText' => $vOfferText,
            'tOfferDetail' => $tOfferDetail,
            'iOfferType' => $iOfferType,
            'tTermsOfUse' => $tTermsOfUse,
            'vDaysAllow' => $vDaysAllow,
            //'eSpecific' => $eSpecific,
            'dtStartDate' => date('Y-m-d H:i:s', strtotime($dtStartDate)),
            'dtExpiryDate' => date('Y-m-d H:i:s', strtotime($dtExpiryDate))
        );

        if ($vDealCode == '') {
            $data['vDealCode'] = $this->checkDealCode();
        }

        $query = $this->db->update($this->table, $data, array('iDealID' => $iDealID));
        if ($this->db->affected_rows() > 0){
            $this->updateSolr($iRestaurantID);
            return $query;
        }
        else
            return '';
    }

    /*
     * TO CHECK THAT DEAL CODE IS ALREADY EXISTS OR NOT...
     */

    function checkDealCodeExists($dealCode, $dealID = '') {

        if ($dealID != '')
            $this->db->or_where_not_in('iDealID', $dealID);

        $rec = $this->db->get_where($this->table, array('vDealCode' => $dealCode));
        if ($rec->num_rows() > 0) {
            /*
             * IF THE PIN-ID ALREADY EXIST...
             */
            return 'false';
        } else {
            /*
             * IF THE PIN-ID IS NOT AVAILABLE...
             */
            return 'true';
        }
    }
    
    function updateSolr($restaurantId){
        $query = $this->db->query("UPDATE tbl_restaurant SET iSolrFlag = 0 WHERE iRestaurantID = $restaurantId");
    }
    
    /*
     * GET THE LIST OF OFFER TYPES...
     */

    function getOfferTypeList() {
        $data = $this->datatableshelper->query("SELECT * FROM tbl_offer_type WHERE status = 'Active' " );
        return $data['aaData'];
    }

}

?>
