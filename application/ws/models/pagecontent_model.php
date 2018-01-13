<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of login_model
 * @author OpenXcell Technolabs
 */
class PageContent_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_pagecontent';
    }

    /*
     * TO LOAD THE PAGE...
     * PARAM
     *      - PAGE-ID*
     */

    function loadPage($pageId = '') {
        try {
            $res = $this->db->get_where($this->table, array('iPageID' => $pageId));
            $row = $res->row_array();
            return array(
                'pageId' => $row['iPageID'],
                'pageTitle' => $row['vPageTitle'],
                'pageContent' => $row['tContent']
            );
        } catch (Exception $ex) {
            exit('PageContent Mode : Error in loadPages function - ' . $ex);
        }
    }

    /**
     * 
     * @param type $subject
     * @param type $message
     * @return int|string
     * @throws Exception
     */
    function feedback($subject = '', $message = '') {
        try {
            if (!empty($subject) && !empty($message)) {
                $ins = array(
                    'Subject' => $subject,
                    'Message' => $message
                );
                $this->db->insert('hm_contactform', $ins);
                return 1;
            } return '';
        } catch (Exception $ex) {
            throw new Exception('Error in feedback function - ' . $ex);
        }
    }

    /**
     * 
     * @param type $name
     * @param type $number
     * @param type $email
     * @param type $message
     * @return int|string
     * @throws Exception
     */
    function contact($name = '', $number = '', $email = '', $message = '') {
        try {
            if (!empty($number) && !empty($message) && !empty($name) && !empty($email)) {
                $ins = array(
                    'Message' => $message,
                    'Phone' => $number,
                    'Email' => $email,
                    'Name' => $name
                );
                $this->db->insert('hm_contactform', $ins);
                return 1;
            } return '';
        } catch (Exception $ex) {
            throw new Exception('Error in contact function - ' . $ex);
        }
    }

}
