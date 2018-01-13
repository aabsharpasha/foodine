<?php

class Offers_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_combo_offers';
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

    function getSubOffersDataAll($iComboOffersID = null) {
        if ($iComboOffersID != '') {
            $result = $this->db->get_where('tbl_combo_sub_offers', array('iComboOffersID' => $iComboOffersID));
            if ($result->num_rows() > 0)
                return $result->result_array();
            else
                return '';
        } return '';
    }

    /*
     * GET THE RECORD BY ID
     */

    function getDealsDataById($iComboOffersID) {
        $result = $this->db->get_where($this->table, array('iComboOffersID' => $iComboOffersID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }
    
    function getSubDealsDataById($iComboSubOffersID) {
       
        $result = $this->db->get_where('tbl_combo_sub_offers', array('iComboSubOffersID' => $iComboSubOffersID));
         //return $result->num_rows();
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*
     * GET THE RECORD BY ID
     */

    function getDealsDataViewById($iComboOffersID) {
        //$result = $this->db->get_where($this->table . ' td, tbl_restaurant AS tr', array('td.iDealID' => $iDealID, 'td.iRestaurantID = '));

        $fields[] = 'tr.vRestaurantName';
        $fields[] = 'tco.iRestaurantID';
        $fields[] = 'tr.iRestaurantID as id';
        $fields[] = 'tco.iComboOffersID';
        $fields[] = 'tco.vOfferText';
        $fields[] = 'tco.vDaysAllow';
        $fields[] = 'DATE_FORMAT(tco.dtStartDate , \'%d %b %Y %h:%i %p\') AS dtStartDate';
        $fields[] = 'DATE_FORMAT(tco.dtExpiryDate , \'%d %b %Y %h:%i %p\') AS dtExpiryDate';

        $qry = 'SELECT ' . implode(',', $fields) . ' FROM tbl_combo_offers tco, tbl_restaurant tr WHERE iComboOffersID = ' . $iComboOffersID . ' AND tr.iRestaurantID = tco.iRestaurantID';
        $result = $this->db->query($qry);
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    function getSubDealsDataViewById($iComboSubOffersID) {
        //$result = $this->db->get_where($this->table . ' td, tbl_restaurant AS tr', array('td.iDealID' => $iDealID, 'td.iRestaurantID = '));

        //$fields[] = 'tr.vRestaurantName';
        //$fields[] = 'tco.iComboOffersID';
        $fields[] = 'tco.vOfferText';
        $fields[] = 'tco.vDaysAllow';
        $fields[] = 'DATE_FORMAT(tco.dtStartDate , \'%d %b %Y %h:%i %p\') AS dtStartDate';
        $fields[] = 'DATE_FORMAT(tco.dtExpiryDate , \'%d %b %Y %h:%i %p\') AS dtExpiryDate';
        $fields[] = 'tcso.vOfferTitle';
        $fields[] = 'tcso.tOfferDetail';
        $fields[] = 'tcso.tActualPrice';
        $fields[] = 'tcso.tDiscountedPrice';

        $qry = 'SELECT ' . implode(',', $fields) . ' FROM tbl_combo_sub_offers tcso, tbl_combo_offers tco WHERE iComboSubOffersID = ' . $iComboSubOffersID . ' AND tco.iComboOffersID = tcso.iComboOffersID';
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
//        if ($iRestaurantID != 0) {
//            $where_condition .= ' AND td.iRestaurantID = "' . $iRestaurantID . '"';
//        }

        //$data = $this->datatableshelper->query("SELECT td.vOfferText as vOfferText,vDealImage,  vRestaurantName,vDealCode, td.dtStartDate as dtStartDate1  , td.dtExpiryDate as dtExpiryDate1 , (SELECT COUNT(tdl.iDealLikeID) FROM tbl_deals_like AS tdl WHERE td.iRestaurantID = tdl.iRestaurantID AND td.iDealID = tdl.iDealID) AS total_like ,  DATE_FORMAT(td.dtStartDate , '%d %b %Y') as dtStartDate, DATE_FORMAT(td.dtExpiryDate , '%d %b %Y') as dtExpiryDate, td.eStatus  as eStatus , td.iDealID as iDealID , iDealID AS DT_RowId FROM tbl_deals AS td, tbl_restaurant AS tr WHERE td.iRestaurantID = tr.iRestaurantID " . $where_condition);
        $data = $this->datatableshelper->query("SELECT tco.vOfferText as vOfferText, vOfferImage,  vRestaurantName, vDaysAllow,tr.iRestaurantID as iRestaurantID, tco.dtStartDate as dtStartDate1  , tco.dtExpiryDate as dtExpiryDate1 ,  DATE_FORMAT(tco.dtStartDate , '%d %b %Y') as dtStartDate, DATE_FORMAT(tco.dtExpiryDate , '%d %b %Y') as dtExpiryDate, tco.eStatus  as eStatus , tco.iComboOffersID as iComboOffersID , iComboOffersID AS DT_RowId FROM tbl_combo_offers AS tco, tbl_restaurant AS tr WHERE tco.iRestaurantID = tr.iRestaurantID " . $where_condition);
        return $data;
    }

    function get_paginationresultsuboffers($iComboOffersID) {
        $where_condition = '';
        if($iComboOffersID) {
        $where_condition .= ' AND tcso.iComboOffersID = "' . $iComboOffersID . '"';
        }
        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            // $where_condition .= ' AND td.iRestaurantID = "' . $iRestaurantID . '"';
        }

        //$data = $this->datatableshelper->query("SELECT td.vOfferText as vOfferText,vDealImage,  vRestaurantName,vDealCode, td.dtStartDate as dtStartDate1  , td.dtExpiryDate as dtExpiryDate1 , (SELECT COUNT(tdl.iDealLikeID) FROM tbl_deals_like AS tdl WHERE td.iRestaurantID = tdl.iRestaurantID AND td.iDealID = tdl.iDealID) AS total_like ,  DATE_FORMAT(td.dtStartDate , '%d %b %Y') as dtStartDate, DATE_FORMAT(td.dtExpiryDate , '%d %b %Y') as dtExpiryDate, td.eStatus  as eStatus , td.iDealID as iDealID , iDealID AS DT_RowId FROM tbl_deals AS td, tbl_restaurant AS tr WHERE td.iRestaurantID = tr.iRestaurantID " . $where_condition);
        $data = $this->datatableshelper->query("SELECT tcso.vOfferTitle as vOfferTitle, tOfferDetail,  tActualPrice, tDiscountedPrice,tco.iComboOffersID as iComboOffersID,iComboSubOffersID, iComboSubOffersID AS DT_RowId,tco.vOfferText as vOfferText FROM tbl_combo_sub_offers AS tcso, tbl_combo_offers AS tco WHERE tcso.iComboOffersID = tco.iComboOffersID " . $where_condition);
        return $data;
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

        $query = $this->db->query("DELETE from $this->table WHERE iComboOffersID IN ($adid) ");
        if ($this->db->affected_rows() > 0) {
            /*
             * REMOVE DIRECTORY
             */
            $ids = $iDealID;
            if (!empty($ids)) {
                $basePath = DOC_ROOT . '/images/offer/';
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

    function changeDealsStatus($iComboOffersID) {
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iComboOffersID = '" . $iComboOffersID . "'");
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

        $eSpecific = isset($eSpecific) ? 'yes' : 'no';


        $data = array(
            'iRestaurantID' => $iRestaurantID,
            'vOfferText' => $vOfferText,
            //'vDealCode' => $vDealCode,
            //'tOfferDetail' => $tOfferDetail,
            //'tTermsOfUse' => $tTermsOfUse,
            'vDaysAllow' => $vDaysAllow,
            //'eSpecific' => $eSpecific,
            'dtStartDate' => date('Y-m-d H:i:s', strtotime($dtStartDate)),
            'dtExpiryDate' => date('Y-m-d H:i:s', strtotime($dtExpiryDate)),
            'eStatus' => 'Active',
            'tCreatedAt' => date('Y-m-d H:i:s')
        );

        //$data['vDealCode'] = $this->checkDealCode();

        $query = $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0)
            return $this->db->insert_id();
        else
            return '';
    }

    function addSubDeals($fieldRecord) {
        extract($fieldRecord);

        if (isset($vDaysAllow) && $vDaysAllow != '') {
            $vDaysAllow = implode(',', $vDaysAllow);
        } else {
            $vDaysAllow = '';
        }

        //$eSpecific = isset($eSpecific) ? 'yes' : 'no';


        $data = array(
            'iComboOffersID' => $iComboOffersID,
            'vOfferTitle' => $vOfferTitle,
            'tOfferDetail' => $tOfferDetail,
            'tActualPrice' => $tActualPrice,
            'tDiscountedPrice' => $tDiscountedPrice,
            'tCreatedAt' => date('Y-m-d H:i:s')
        );
        $query = $this->db->insert('tbl_combo_sub_offers', $data);
        if ($this->db->affected_rows() > 0)
            return $this->db->insert_id();
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
            //'vDealCode' => $vDealCode,
            'vOfferText' => $vOfferText,
            //'tOfferDetail' => $tOfferDetail,
            //'tTermsOfUse' => $tTermsOfUse,
            'vDaysAllow' => $vDaysAllow,
            //'eSpecific' => $eSpecific,
            'dtStartDate' => date('Y-m-d H:i:s', strtotime($dtStartDate)),
            'dtExpiryDate' => date('Y-m-d H:i:s', strtotime($dtExpiryDate))
        );

//        if ($vDealCode == '') {
//            $data['vDealCode'] = $this->checkDealCode();
//        }

        $query = $this->db->update($this->table, $data, array('iComboOffersID' => $iComboOffersID));
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }
    
      function editSubDeals($fieldRecord) {
        extract($fieldRecord);

        $data = array(
            'vOfferTitle' => $vOfferTitle,
            'tOfferDetail' => $tOfferDetail,
            'tActualPrice' => $tActualPrice,
            'tDiscountedPrice' => $tDiscountedPrice,
            'tModifiedAt' => date('Y-m-d H:i:s')
        );

        $query = $this->db->update('tbl_combo_sub_offers', $data, array('iComboSubOffersID' => $iDealID));
        if ($this->db->affected_rows() > 0)
            return $query;
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

}

?>
