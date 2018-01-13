<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ReferenceCode_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_reference_codes';
    }

    function availCode($userId, $deviceToken, $refrenceCode) {
        $conditions = array(
            "eStatus='Active'",
            "vReferenceCode='" . $refrenceCode . "'"
        );
        $conditionsStr = implode(' AND ', $conditions);
        $fields = array(
            "iReferenceCodeID",
            "eHasVoucher",
            "iVoucherID",
            "tStartDate",
            "tEndDate"
        );
        $fieldsStr = implode(', ', $fields);
        $sql = "SELECT $fieldsStr FROM tbl_reference_codes WHERE $conditionsStr";
        $res = $this->db->query($sql)->row_array();
        // check if code exists
        if (empty($res)) {
            throw new Exception("Invalid reference code!");
        }
        // check dates validity of code 
        if ($res["tStartDate"] > date('Y-m-d H:i:s') || $res["tEndDate"] < date('Y-m-d H:i:s')) {
            throw new Exception("This refrence code has been expired!");
        }
        $this->load->model('userreferencecode_model');
        if ($res["eHasVoucher"] == "yes" && !empty($res["iVoucherID"])) {
            $data['status'] = $this->userreferencecode_model->availCodeWithVoucher($userId, $deviceToken, $res["iReferenceCodeID"], $res["iVoucherID"]);
            $data['isLinked'] = 1;
            return $data;
        } else {
            $data['status'] = $this->userreferencecode_model->availCodeWithoutVoucher($userId, $deviceToken, $res["iReferenceCodeID"]);
            $data['isLinked'] = 0;
            return $data;
        }
        
    }

    function getAvailedVouchers($userId) {
        $sql = "SELECT iVoucherID FROM tbl_availed_vouchers WHERE iUserID='$userId' order by tCreatedAt desc";
        $res = $this->db->query($sql)->result_array();
        $data = array();
        if (!empty($res)) {
            $vouchers = array();
            foreach ($res AS $row) {
                $vouchers[] = $row["iVoucherID"];
            }
            $fields = array(
                "vTitle AS title",
                "vCode AS code",
                "DATE_FORMAT(tStartDate,'%d/%m/%Y') AS startDate",
                "DATE_FORMAT(tEndDate,'%d/%m/%Y') AS endDate",
                "DATE_FORMAT(tEndDate,'%d-%m-%Y, %h:%i %p') AS validityDate",
                "eValueType AS valueType",
                "dValue AS value",
                "tv.vDescription AS description",
                "dMinOrderValue AS itemPrice",
                "eUserSpecific AS userSpecific",
                "eStatus AS status",
                "iVoucherID AS voucherId",
                "tv.iVoucherUseId AS voucherUseId",
                "tvu.vDescription AS vUseDescription"
            );
            $vouchersStr = implode("', '", $vouchers);
            $fieldsStr = implode(", ", $fields);
            $sql = "SELECT $fieldsStr FROM tbl_vouchers AS tv LEFT JOIN tbl_voucher_use AS tvu ON tvu.iVoucherUseId=tv.iVoucherUseId WHERE iVoucherID IN ('$vouchersStr') order by FIELD(iVoucherID, '$vouchersStr')";
            $voucherRes = $this->db->query($sql)->result_array();
            if (!empty($voucherRes)) {
                $data = $voucherRes;
            }
        }
        return $data;
    }

    function getUnavailedVouchers($userId) {
        $conditions = array(
            "trc.eHasVoucher='yes'",
            "turc.iUserID=$userId",
            "trc.iVoucherID NOT IN ( SELECT iVoucherID FROM tbl_availed_vouchers WHERE iUserID='$userId')"
        );
        $conditionsStr = implode(' AND ', $conditions);
        $joinStr = "INNER JOIN tbl_user_reference_codes AS turc ON trc.iReferenceCodeID = turc.iReferenceCodeID";
        $sql = "SELECT trc.iVoucherID AS voucher_id FROM tbl_reference_codes AS trc $joinStr WHERE $conditionsStr order by turc.tCreatedAt desc";
        $res = $this->db->query($sql)->result_array();
        $data = array();
        if (!empty($res)) {
            $vouchers = array();
            foreach ($res AS $row) {
                $vouchers[] = $row["voucher_id"];
            }
            $vouchersStr = implode("', '", $vouchers);
        }

        $fields = array(
            "vTitle AS title",
            "vCode AS code",
            "DATE_FORMAT(tStartDate,'%d/%m/%Y') AS startDate",
            "DATE_FORMAT(tEndDate,'%d/%m/%Y') AS endDate",
            "DATE_FORMAT(tEndDate,'%d-%m-%Y, %h:%i %p') AS validityDate",
            "eValueType AS valueType",
            "dValue AS value",
            "tv.vDescription AS description",
            "dMinOrderValue AS itemPrice",
            "eUserSpecific AS userSpecific",
            "eStatus AS status",
            "iVoucherID AS voucherId",
            "tv.iVoucherUseId AS voucherUseId",
            "tvu.vDescription AS vUseDescription"
        );
        $fieldsStr = implode(", ", $fields);

        // check here what default  vouchers should be shown to user
        $condition = "(eOneTimeUsable='Yes' AND eUserSpecific='No' AND ePublic='Yes' AND iVoucherID NOT IN ( SELECT iVoucherID FROM tbl_availed_vouchers WHERE iUserID='$userId') ) OR (eOneTimeUsable='No' AND eUserSpecific='No' AND ePublic='Yes')";
        $condition .= ' AND CURDATE() between tStartDate and tEndDate';
        $voucherRes = array();
        if (!empty($vouchersStr)) {
            //include vouchers that are given to user through reference codes
            $condition1 = "iVoucherID IN ('$vouchersStr')";
            $condition1 .= ' AND CURDATE() between tStartDate and tEndDate';
            $condition .= " AND " . "iVoucherID NOT IN ('$vouchersStr')";
            $voucherRes = $this->db->query("SELECT $fieldsStr FROM tbl_vouchers AS tv LEFT JOIN tbl_voucher_use AS tvu ON tvu.iVoucherUseId=tv.iVoucherUseId WHERE ( $condition1 ) AND eStatus='Active' order by FIELD(iVoucherID, '$vouchersStr')")->result_array();
        }
        
        $voucherRes1 = $this->db->query("SELECT $fieldsStr FROM tbl_vouchers AS tv LEFT JOIN tbl_voucher_use AS tvu ON tvu.iVoucherUseId=tv.iVoucherUseId WHERE ( $condition ) AND eStatus='Active' ORDER BY tv.tCreatedAt desc")->result_array();
        if(!empty($voucherRes)) {
            $voucherResult = array_merge($voucherRes,$voucherRes1);
        }else {
            $voucherResult = $voucherRes1;
        }
        if (!empty($voucherResult)) {
            $data = $voucherResult;
        }
        return $data;
    }

    function availVoucher($userId, $voucherCode, $amount) {

        $fields = array(
            "vTitle AS title",
            "vCode AS code",
            "DATE_FORMAT(tStartDate,'%d/%m/%Y') AS startDate",
            "DATE_FORMAT(tEndDate,'%d/%m/%Y') AS endDate",
            "DATE_FORMAT(tEndDate,'%d-%m-%Y, %h:%i %p') AS validityDate",
            "eValueType AS valueType",
            "dValue AS value",
            "tv.vDescription AS description",
            "dMinOrderValue AS itemPrice",
            "eUserSpecific AS userSpecific",
            "eStatus AS status",
            "iVoucherID AS voucherId",
            "tv.iVoucherUseId AS voucherUseId",
            "tvu.vDescription AS vUseDescription",
            "eUserSpecific",
            "eOneTimeUsable",
            "tStartDate",
            "tEndDate"
        );
        $fieldsStr = implode(", ", $fields);
        $voucherRes = $this->db->query("SELECT $fieldsStr FROM tbl_vouchers AS tv LEFT JOIN tbl_voucher_use AS tvu ON tvu.iVoucherUseId=tv.iVoucherUseId WHERE vCode='$voucherCode'")->row_array();

        if (empty($voucherRes)) {
            throw new Exception("Invalid voucher !");
        }

        if ($voucherRes["tStartDate"] > date('Y-m-d H:i:s') || $voucherRes["tEndDate"] < date('Y-m-d H:i:s')) {
            throw new Exception("This voucher has been expired!");
        }

        if ($voucherRes["itemPrice"] != '' && $voucherRes["itemPrice"] > $amount) {
            throw new Exception("This voucher is valid for min amount " . $voucherRes["itemPrice"]);
        }

        //only one user can use multiple times
        if (strtolower($voucherRes["eOneTimeUsable"]) == "no" && strtolower($voucherRes["eUserSpecific"]) == "yes") {
            $voucherSql = "SELECT iUserID FROM tbl_availed_vouchers WHERE iVoucherID='" . $voucherRes["voucherId"] . "' AND iUserID<>'$userId' LIMIT 1";
            $availedRes = $this->db->query($voucherSql)->row_array();
            if (!empty($availedRes["iUserID"])) {
                throw new Exception('This voucher is invalid !');
            }
        }
        //only one user can use it once only
        if (strtolower($voucherRes["eOneTimeUsable"]) == "yes" && strtolower($voucherRes["eUserSpecific"]) == "yes") {
            $voucherSql = "SELECT count(iVoucherID) AS useCount FROM tbl_availed_vouchers WHERE iVoucherID='" . $voucherRes["voucherId"] . "'";
            $availedRes = $this->db->query($voucherSql)->row_array();
            if (!empty($availedRes["useCount"])) {
                throw new Exception('This voucher is invalid !');
            }
        }
        //any user can use it once only
        if (strtolower($voucherRes["eOneTimeUsable"]) == "yes" && strtolower($voucherRes["eUserSpecific"]) == "no") {
            $voucherSql = "SELECT count(iVoucherID) AS useCount FROM tbl_availed_vouchers WHERE iVoucherID='" . $voucherRes["voucherId"] . "' AND iUserID='$userId'";
            $availedRes = $this->db->query($voucherSql)->row_array();
            if (!empty($availedRes["useCount"])) {
                throw new Exception('You have already availed this voucher !');
            }
        }
        //any user can use multiple times
//            if(strtolower($voucherRes["eOneTimeUsable"])=="no" && strtolower($voucherRes["eUserSpecific"])=="no"){
//            }

        $insert = array(
            'iVoucherID' => $voucherRes["voucherId"],
            'iUserID' => $userId,
            'tCreatedAt' => date('Y:m:d H:i:s'),
            'tModifiedAt' => date('Y:m:d H:i:s')
        );

        $this->db->insert('tbl_availed_vouchers', $insert);
        unset($voucherRes["eOneTimeUsable"]);
        unset($voucherRes["tStartDate"]);
        unset($voucherRes["tEndDate"]);
        return $voucherRes;
    }

    function removeAvailedVoucher($userId, $voucherCode) {
        $fields = array(
            "iVoucherID AS voucherId",
        );
        $fieldsStr = implode(", ", $fields);
        $voucherRes = $this->db->query("SELECT $fieldsStr FROM tbl_vouchers AS tv WHERE vCode='$voucherCode'")->row_array();

        if (empty($voucherRes)) {
            throw new Exception("Invalid voucher !");
        }

        $voucherId = $voucherRes["voucherId"];
        $userVoucherRes = $this->db->query("SELECT iUserVoucherID FROM tbl_availed_vouchers AS tav WHERE iVoucherID='$voucherId' and iUserID='$userId'")->row_array();
        if (empty($userVoucherRes)) {
            throw new Exception("Voucher not added yet!");
        }
        $this->db->delete('tbl_availed_vouchers', array('iUserID' => $userId, 'iVoucherID' => $voucherId));
        return true;
    }

}
