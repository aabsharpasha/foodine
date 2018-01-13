<?php

class Event_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_restaurant_event';
        $this->load->library('DatatablesHelper');
    }
    
    
    function getRestarantId($iOffersID){
        if ($iComboOffersID != '') {
            $result = $this->db->query('select iRestaurantID from tbl_restaurant_event where iEventId= '.$iOffersID);
            if ($result->num_rows() > 0)                
                return $result->result_array();
            else
                return '';
        }
    }

    /*
     * TO UPDATE THE CUISINE IMAGE
     */

    function updateEventImage($id, $imageName = '') {
        try {
            if (@$imageName !== '') {
                $this->db->update($this->table, array('iEventImage' => $imageName), array('iEventId' => $id));
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            throw new Exception('Error in updateEventImage function - ' . $ex);
        }
    }

    // **********************************************************************
    // Display List of Event
    // **********************************************************************
    function getEventDataAll() {
        $result = $this->db->get_where($this->table, array('eStatus' => 'Active'));
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /*     * **********************************************************************
      Event Setting(Getting event details by admin_id(Selected or inserted)
     * ********************************************************************** */

    function getEventDataById($iEventID) {
        $result = $this->db->get_where($this->table, array('iEventId' => $iEventID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    // **********************************************************************
    // Event Status
    // **********************************************************************
    function changeEventStatus($iEventID) {
        //$updateData = array('admin_status' => 'IF (admin_status = "Active", "Inactive","Active")');
        //$query = $this->db->update($this->admin_tbl,$updateData, array('admin_role ' => $admin_role));
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iEventId = $iEventID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeEvent($iEventID) {
        $adid = implode(',', $iEventID);
        //mprd($adid);
        $query = $this->db->query("DELETE from $this->table WHERE iEventId in ($adid) ");
        //$query = $this->db->delete($this->table, array('iEventID' => $iEventID));
        //mprd($this->db->last_query());
        if ($this->db->affected_rows() > 0) {

            /*
             * REMOVE DIRECTORY
             */
            $ids = $iEventID;
            if (!empty($ids)) {
                $basePath = DOC_ROOT . '/images/event/';
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
        } else
            return '';
    }

    // **********************************************************************
    // add admin
    // **********************************************************************
    function addEvent($eventData) {
        extract($eventData);
        if (isset($iDayofEvent) && $iDayofEvent != '') {
            $vDaysAllow = implode(',', $iDayofEvent);
        } else {
            $vDaysAllow = '';
        }
        if ($iMinTimeMaradian == 1) {
            $minMaradian = 'AM';
        } else {
            $minMaradian = 'PM';
        }
        if ($iMaxTimeMaradian == 1) {
            $maxMaradian = 'AM';
        } else {
            $maxMaradian = 'PM';
        }
        $eventStartTime = $iMinTime . ':' . $iMinMin . ' ' . $minMaradian;
        $eventEndTime = $iMaxTime . ':' . $iMaxMin . ' ' . $maxMaradian;
        $data = array(
            'iRestaurantId' => $iRestaurantID,
            'iEventTitle' => $iEventTitle,
            'iEventDescription' => $iEventDescription,
            'dEventStartDate' => date('Y-m-d', strtotime($dEventStartDate)),
            'dEventEndDate' => isset($dEventEndDate) ? date('Y-m-d', strtotime($dEventEndDate)) : '',
            'vEventStartTime' => $eventStartTime,
            'vEventEndTime' => $eventEndTime,
            'iDayofEvent' => $vDaysAllow,
            'iVenueofEvent' => $iVenueofEvent,
            'URL' => $URL,
            'eStatus' => 'Active',
            'created_at' => date('Y-m-d H:i:s')
        );
        $query = $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0)
            return $this->db->insert_id();
        else
            return '';
    }

    // **********************************************************************
    // Edit admin
    // **********************************************************************
    function editEvent($eventData) {

        extract($eventData);
        if (isset($iDayofEvent) && $iDayofEvent != '') {
            $vDaysAllow = implode(',', $iDayofEvent);
        } else {
            $vDaysAllow = '';
        }

        if ($iMinTimeMaradian == 1) {
            $minMaradian = 'AM';
        } else {
            $minMaradian = 'PM';
        }
        if ($iMaxTimeMaradian == 1) {
            $maxMaradian = 'AM';
        } else {
            $maxMaradian = 'PM';
        }
        $eventStartTime = $iMinTime . ':' . $iMinMin . ' ' . $minMaradian;
        $eventEndTime = $iMaxTime . ':' . $iMaxMin . ' ' . $maxMaradian;

        $data = array(
            'iRestaurantId' => $iRestaurantID,
//            'iEventImage' => $iEventImage,
            'iEventTitle' => $iEventTitle,
            'iEventDescription' => $iEventDescription,
            'dEventStartDate' => date('Y-m-d', strtotime($dEventStartDate)),
            'dEventEndDate' => isset($dEventEndDate) ? date('Y-m-d', strtotime($dEventEndDate)) : '',
            'vEventStartTime' => $eventStartTime,
            'vEventEndTime' => $eventEndTime,
            'iDayofEvent' => $vDaysAllow,
            'iVenueofEvent' => $iVenueofEvent,
            'URL' => $URL,
        );
        $query = $this->db->update($this->table, $data, array('iEventId' => $iEventID));
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function editEventData($data, $iEventID = '') {
        if ($iEventID != '') {
            $query = $this->db->update($this->table, $data, array('iEventId' => $iEventID));
            if ($this->db->affected_rows() > 0)
                return $query;
            else
                return '';
        }else {
            return '';
        }
    }

    function get_paginationresult() {
        $where_condition = '';
        $data = $this->datatableshelper->query("SELECT tre.iEventId AS iEventID ,tre.iEventTitle as iEventTitle, tre.iEventImage as iEventImage, tre.iEventDescription as iEventDescription, DATE_FORMAT(tre.dEventStartDate , '%d %b %Y') as dEventStartDate, DATE_FORMAT(tre.dEventEndDate , '%d %b %Y') as dEventEndDate, "
                . "tre.vEventStartTime as vEventStartTime, tre.iDayofEvent as iDayofEvent, tre.iVenueofEvent as iVenueofEvent, tre.URL as URL, tre.eStatus as eStatus, tr.vRestaurantName as vRestaurantName,tre.iRestaurantId as iRestaurantId, "
                . "DATE_FORMAT(tre.created_at , '%d %b %Y %h:%i %p') as tCreatedAt , "
                . "tre.iEventId AS DT_RowId FROM tbl_restaurant_event AS tre, tbl_restaurant AS tr WHERE tre.iRestaurantId = tr.iRestaurantID" . $where_condition);

        return $data;
    }

}

?>
