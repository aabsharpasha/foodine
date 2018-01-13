<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of link_model
 * link hashes expiry 24 hrs
 * @author Amit Malakar
 */
class Career_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_job_details';
    }

    function getTeamDetails() {
        try {
            $fields = array(
                'tjtm.iMemberID AS memberId',
                'tjtm.vMemberName AS memberName',
                'tjtm.vMemberTagline AS memberTag',
                'CONCAT("' . BASEURL . 'images/weAreHiringTeam/", IF(`tjtm`.vMemberImage = \'\', "default.png", CONCAT(`tjtm`.iMemberID,\'/thumb/\',`tjtm`.vMemberImage)) ) AS memberImage',
                'tjtm.eStatus AS status'
            );

            $tbl = array(
                'tbl_job_team_member AS tjtm',
            );
            $limit = '';
            $condition[] = 'tjtm.eStatus = "Active"';

            $fields = implode(',', $fields);
            $tbl = ' FROM ' . implode(',', $tbl);
            $condition = ' WHERE ' . implode(' AND ', $condition);

            $qry = 'SELECT ' . $fields . $tbl . $condition . $limit;

            $res = $this->db->query($qry);
            return $res->result_array();
        } catch (Exception $ex) {
            throw new Exception('Error in getTeamDetails function - ' . $ex);
        }
    }

    function getJobDetails($postValue = array()) {
        try {
            if (!empty($postValue)) {

                extract($postValue);
            }
            $fields = array(
                'tjd.iJobDetailID AS jobId',
                'tjd.vJobTitle AS jobTitle',
                'tlz.vZoneName AS jobLocation',
                'tjd.iMinExp AS experience',
                'tjd.iEmploymentType AS empType',
                'tjd.tJobDescription AS jobDesc',
                'tjd.eStatus AS status'
            );

            $tbl = array(
                'tbl_job_details AS tjd',
                'tbl_location_zone as tlz'
            );
            $limit = '';
            if (isset($jobId) && !empty($jobId)) {
                $condition[] = 'tjd.iJobDetailID IN (' . $jobId . ')';
                $limit = ' LIMIT 1';
            }
            $condition[] = 'tjd.vJobLOcation = tlz.iLocZoneID';
            $condition[] = 'tjd.eStatus = "Active"';

            $fields = implode(',', $fields);
            $tbl = ' FROM ' . implode(',', $tbl);
            $condition = ' WHERE ' . implode(' AND ', $condition);

            $qry = 'SELECT ' . $fields . $tbl . $condition . $limit;

            $res = $this->db->query($qry);
            if (isset($jobId) && !empty($jobId)) {
                return $res->row_array();
            }
            return $res->result_array();
        } catch (Exception $ex) {
            throw new Exception('Error in getJobDetails function - ' . $ex);
        }
    }

    public function postJob($postData) {
        extract($postData);
        $data = array(
            'iJobDetailID' => $jobId,
            'vApplicantName' => $name,
            'vApplicantEmail' => $email,
            'iApplicantPhone' => $number,
            'tApplicantCoverNote' => $covernote,
            'vApplicantResume' => '',
            'tCreatedAt' => date('Y-m-d H:i:s'),
        );
        $query = $this->db->insert('tbl_job_applications', $data);
        if ($this->db->affected_rows() > 0) {
            $iJobApplicationId = $this->db->insert_id();
            if ($iJobApplicationId) {
                $this->sendUserMail($email, $name);
                $this->sendAdminMail($postData);
            }
            return $iJobApplicationId;
        } else
            return '';
    }

    function sendUserMail($email,$name) {
        $param = array(
            '%MAILSUBJECT%' => 'HungerMafia : Request for hiring',
            '%BASEURL%' => BASEURL,
            '%NAME%' => @$name,
        );

        //send email
        $tmplt = DIR_VIEW . 'email/user-request-for-hiring.php';
        $subject = 'HungerMafia : Request for hiring';
        $to = $email;
        $this->load->model("smtpmail_model", "smtpmail_model");
        $data = $this->smtpmail_model->send($to, $subject, $tmplt, $param);
    }
    
    function sendAdminMail($postData) {
        extract($postData);
        $param = array(
            '%MAILSUBJECT%' => 'HungerMafia : Request for hiring',
            '%BASEURL%' => BASEURL,
            '%NAME%' => @$name,
            '%EMAIL%' => @$email,
            '%PHONE%' => @$number,
        );

        //send email
        $tmplt = DIR_VIEW . 'email/career-request-for-hiring.php';
        $subject = 'HungerMafia : Request for hiring';
        $to = @CAREER_EMAIL_ID;
        $this->load->model("smtpmail_model", "smtpmail_model");
        $data = $this->smtpmail_model->send($to, $subject, $tmplt, $param);
    }

    public function addResume($postData, $iJobApplicationId) {
        $date = date('Y-m-d H:i:s');
        foreach ($postData AS $type => $imageData) {
            switch ($type) {
                case "resume":
                    $query = 'UPDATE tbl_job_applications SET vApplicantResume = ' . "'$imageData'" . ' WHERE iApplicationID=' . $iJobApplicationId;
                    $this->db->query($query);
                    break;
                default:
                    break;
            }
        }
        return true;
    }

}
