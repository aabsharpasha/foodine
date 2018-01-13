<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class UserReferenceCode_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_user_reference_codes';
    }
    
    function availCodeWithoutVoucher($userId,$deviceToken,$refrenceCodeId ){
        if (!empty($userId) && !empty($deviceToken) && !empty($refrenceCodeId) ) {
            $conditions = array(
                "iUserID='".$userId."'",
                "vDeviceToken='".$deviceToken."'"
                //"iReferenceCodeID='".$refrenceCodeId."'"
            );
            $conditionsStr  = implode(' AND ',$conditions);
            $sql        = "SELECT count(vDeviceToken) AS device_count FROM tbl_user_reference_codes WHERE $conditionsStr" ;
            $countRes   = $this->db->query($sql)->row_array();
            // check if code is availed by current device
            if(!empty($countRes['device_count'])){
                throw new Exception('Reference code already used!');
            }
            
            $data = array(
                'iReferenceCodeID'  => $refrenceCodeId,
                'iUserID'           => $userId,
                "vDeviceToken"      => $deviceToken,
                'tCreatedAt'        => date('Y:m:d H:i:s'),
                'tModifiedAt'       => date('Y:m:d H:i:s')
            );
            $this->db->insert('tbl_user_reference_codes', $data);
            return true;
        }
        return false;
    }
    
    function availCodeWithVoucher($userId,$deviceToken,$refrenceCodeId,$voucherId ){
        if (!empty($userId) && !empty($deviceToken) && !empty($refrenceCodeId) &&!empty($voucherId)) {

            //check if voucher exists
            $voucherSql = "SELECT eOneTimeUsable, eUserSpecific FROM tbl_vouchers WHERE iVoucherID='$voucherId' AND eStatus='Active' ";
            $voucherRes = $this->db->query($voucherSql)->row_array();
            if(empty($voucherRes)){
                throw new Exception('This code is invalid !');
            }
            //only one user can use multiple times
            if(strtolower($voucherRes["eOneTimeUsable"])=="no" && strtolower($voucherRes["eUserSpecific"])=="yes"){
                $voucherSql = "SELECT iUserID FROM tbl_availed_vouchers WHERE iVoucherID='$voucherId' AND iUserID<>'$userId' LIMIT 1";
                $availedRes = $this->db->query($voucherSql)->row_array();
                if(!empty($availedRes["iUserID"])){
                    throw new Exception('This code is invalid !');
                }
            }
            //only one user can use it once only
            if(strtolower($voucherRes["eOneTimeUsable"])=="yes" && strtolower($voucherRes["eUserSpecific"])=="yes"){
                $voucherSql = "SELECT count(iVoucherID) AS useCount FROM tbl_availed_vouchers WHERE iVoucherID='$voucherId'";
                $availedRes = $this->db->query($voucherSql)->row_array();
                if(!empty($availedRes["useCount"])){
                    throw new Exception('This code is invalid !');
                }
            }
            //any user can use it once only
            if(strtolower($voucherRes["eOneTimeUsable"])=="yes" && strtolower($voucherRes["eUserSpecific"])=="no"){
                $voucherSql = "SELECT count(iVoucherID) AS useCount FROM tbl_availed_vouchers WHERE iVoucherID='$voucherId' AND iUserID='$userId'";
                $availedRes = $this->db->query($voucherSql)->row_array();
                if(!empty($availedRes["useCount"])){
                    throw new Exception('You have already availed this code !');
                }
            }
            //any user can use multiple times
//            if(strtolower($voucherRes["eOneTimeUsable"])=="no" && strtolower($voucherRes["eUserSpecific"])=="no"){
//            }
            
            // check if reference code is availed by current user
            $conditions = array( "iReferenceCodeID='".$refrenceCodeId."'", "iUserID='".$userId."'" );
            $conditionsStr  = implode(' AND ',$conditions);
            $sql            = "SELECT count(iUserID) AS user_count FROM tbl_user_reference_codes WHERE $conditionsStr" ;
            $countRes       = $this->db->query($sql)->row_array();
            if(!empty($countRes['user_count'])){
                throw new Exception('This code is already availed !');
            }
            
            $data = array(
                'iReferenceCodeID'   => $refrenceCodeId,
                'iUserID'           => $userId,
                "vDeviceToken"      => $deviceToken,
                'tCreatedAt'        => date('Y:m:d H:i:s'),
                'tModifiedAt'       => date('Y:m:d H:i:s')
            );

            $this->db->insert('tbl_user_reference_codes', $data);
            return true;
        }
        return false;
    }
    
}
