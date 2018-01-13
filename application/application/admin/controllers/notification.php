<?php
class Notification extends CI_Controller {

    var $viewData = array();

    function __construct() {

        parent::__construct();

        $this->controller = 'notification';
        $this->uppercase = 'Notification History';
        $this->title = 'Notification History';
        $this->load->library('aws_sdk');
        $this->load->model('notification_model');
    }

    function index() {
        $data = array(
            'title' => 'Notifications',
            'notification' => $this->admin_model->getNotifications(true)
        );
        $this->load->view('notification/list', $data);
    }

    /*
     * UPDATE THE NOTIFICATION STATUS
     */

    function update() {
        if ($_POST) {
            extract($_POST);
            $action = (int) $action;

            $tableName = $this->db->query('SELECT tblName FROM tbl_notification_activity WHERE iActivityID IN(' . $action . ')')->row_array()['tblName'];

            $keyValue = '';
            $activeStatus = $dectiveStatus = '';
            $msgVal = '';
            switch ($action) {
                case 1:
                    $keyValue = 'iRestSpecialtyID';
                    $activeStatus = 'active';
                    $dectiveStatus = 'reject';
                    $msgVal = 'specialty';
                    break;

                case 2:
                    $keyValue = 'iDealID';
                    $activeStatus = 'Active';
                    $dectiveStatus = 'Reject';
                    $msgVal = 'offers';
                    break;

                case 3:
                    $keyValue = 'iDealID';
                    $activeStatus = 'Active';
                    $dectiveStatus = 'Reject';
                    $msgVal = 'offers';
                    break;

                case 4:
                    $keyValue = 'iRestSpecialtyID';
                    $activeStatus = 'active';
                    $dectiveStatus = 'reject';
                    $msgVal = 'specialty';
                    break;
            }

            if ($action != 3 && $action != 4) {
                $this->db->update($tableName, array('eStatus' => ($type == 'yes' ? $activeStatus : $dectiveStatus)), array($keyValue => $id));
            } else {
                $this->db->delete($tableName, array($keyValue => $id));
            }

            $this->db->update('tbl_notification', array('takeAction' => $type), array('iNotificationID' => $target));

            $restId = $this->db->query('SELECT iRestaurantID FROM tbl_notification WHERE iNotificationID IN(' . $target . ')')->row_array()['iRestaurantID'];
            $record = $this->db->query('SELECT ePlatform, vDeviceToken FROM tbl_admin WHERE iRestaurantID IN(' . $restId . ')')->row_array();

            $this->load->library('pushnotify');

            $osType = $record['ePlatform'] == 'ios' ? 2 : 1;
            $deviceToken = $record['vDeviceToken'];


            $mesg = '';
            if ($target == 3) {
                $mesg = 'Your request has ';
                if ($type == 'yes') {
                    if ($action != 3 && $action != 4) {
                        $mesg .= 'been rejected ';
                    } else {
                        $mesg .= 'been deleted ';
                    }
                } else {
                    if ($action != 3 && $action != 4) {
                        $mesg .= 'not been rejected ';
                    } else {
                        $mesg .= 'not been deleted ';
                    }
                }
            } else {
                $mesg = 'Your request has been ';
                if ($type == 'yes') {
                    if ($action != 3 && $action != 4) {
                        $mesg .= 'accepted ';
                    } else {
                        $mesg .= 'deleted ';
                    }
                } else {
                    if ($action != 3 && $action != 4) {
                        $mesg .= 'rejected ';
                    } else {
                        $mesg .= 'not deleted ';
                    }
                }
            }
            $mesg .= 'in ' . $msgVal;

            if ($deviceToken != '') {
                $this->pushnotify->sendIt($osType, $deviceToken, $mesg, 2);
            }


            $resp = array(
                'STATUS' => 200,
                'MSG' => 'You have ' . ($type == 'yes' ? 'accept' : 'decline') . ' this request.',
            );

            echo json_encode($resp);
        }
    }


     function sendPushNotification($iPushNotifyID) {
        $qry = 'SELECT * FROM tbl_push_notification'
                . ' WHERE eSent = \'0\' '
                . 'AND iPushNotifyID = '.$iPushNotifyID.' AND eStatus=\'Active\'';
        //echo  $qry; exit;
        $record = $this->db->query($qry)->result_array();
        $record = $record[0];
        $this->load->library('pushnotification', "live");
        
            $androidArr = $iosArr = array();
                
            $message['message']      = $record['vNotifyText'];
            $message['title']        = $record['vNotifyTitle'];
            $linked_id      = "";
            $restaurant_id  = "";
        
            $pushData = $message;
            
                
                //Android Device Token
                //$andiDeviceTokenQry = "SELECT vDeviceToken AS deviceToken, iUserID FROM tbl_user WHERE vDeviceToken != '' and iUserID IN (6132,6145,6133,6134,6135,7077,7121,6129)";
                 $andiDeviceTokenQry = "SELECT vDeviceToken AS deviceToken, iUserID FROM tbl_user WHERE vDeviceToken != ''";    
                $andiDeviceToken = $this->db->query($andiDeviceTokenQry)->result_array();
     //       print_r($andiDeviceToken); exit;
                foreach($andiDeviceToken as $andiDeviceToken) {
                     $pushData['userid'] = $andiDeviceToken['iUserID'];
                        $androidArr = $andiDeviceToken['deviceToken'];
                        $insert = array(
                            'vMessage'      => $message['message'],
                            'iUserID'       => $andiDeviceToken['iUserID'],
                            'tData'         => json_encode($pushData),
                            'tCreatedAt'    => date('Y:m:d H:i:s'),
                            'vNotifyTitle' => $message['title']
                        );

                        $this->db->insert('tbl_user_notifications', $insert);
                         // if(count($androidArr)==1000){
                         //         $this->pushnotification->sendIt(1, $androidArr, $message, $pushData);
                         //        $androidArr=array();
                         //  }
                    $this->pushnotification->sendIt(1, $androidArr, $message, $pushData);
                    $this->db->query("UPDATE tbl_push_notification SET eSent='1' WHERE iPushNotifyID='".$record['iPushNotifyID']."'");
                }

                return true;

    }



    /*
     * UPDATE THE NOTIFICATION STATUS
     */

    function status($innner = FALSE) {

        $this->db->query('UPDATE tbl_notification SET hasRead = \'yes\' WHERE true');

        $resp = array(
            'STATUS' => 200,
            'MSG' => 'You have updated notification list.',
        );
        if (!$innner)
            echo json_encode($resp);
    }

    /*
     * GET UPDATED LIST
     */

    function newlist() {

        $row = $this->admin_model->getNotifications(false, $_POST['id']);
        //$this->status(true);
        if (count($row) > 0) {
            $resp = array(
                'STATUS' => 200,
                'MSG' => 'Record found successfully',
                'RECORD' => $row
            );
        } else {
            $resp = array(
                'STATUS' => 101,
                'MSG' => 'NO Record found.'
            );
        }

        echo json_encode($resp);
    }

    /*
     * TO GET THE PUSH NOTIFICATION HISTORY
     */

    function history() {
        try {
            $viewData = array("title" => "Notification History");
            $viewData['breadCrumbArr'] = array("notification" => "Notification History");

            $this->load->view('notification/view', $viewData);
        } catch (Exception $ex) {
            throw new Exception('Error in history function history - ' . $ex);
        }
    }

    /*
     * TO GET THE NOTIFICATION LIST
     */

    function paginate() {
        $data = $this->notification_model->get_paginationresult();
        echo json_encode($data);
    }

    function addhistory($HistoryID = '', $ed = '') {
        try {
            $viewData['title'] = "Send Push Notification";

            $viewData['ACTION_LABEL'] = (isset($HistoryID) && $HistoryID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

            if ($HistoryID != '' && $ed != '' && $ed == 'y') {
                $getData = $this->notification_model->getNotificationDataById($HistoryID);
                $viewData['getNotificationData'] = $getData;
            }

            if ($this->input->post('action') && $this->input->post('action') == 'backoffice.notificationadd') {
                $check = $this->notification_model->checkNotificationNameAvailable($this->input->post('vNotifyTitle'));
                if ($check) {
                    $notificationSend = $this->notification_model->sendNotification($_POST);
                    if ($notificationSend == 1) {
                        $succ = array('0' => NOTIFICATION_ADDED);
                        $this->session->set_userdata('SUCCESS', $succ);
                    } else {
                        $err = array('0' => NOTIFICATION_NOT_ADDED);
                        $this->session->set_userdata('ERROR', $err);
                    }
                    redirect('notification/history', 'refresh');
                } else {
                    $err = array('0' => NOTIFICATION_NAME_EXISTS);
                    $this->session->set_userdata('ERROR', $err);
                }
            }

            $this->load->view('notification/history_add', $viewData);
        } catch (Exception $ex) {
            throw new Exception('Error in addhistory function - ' . $ex);
        }
    }

    function addNotification($HistoryID = '', $ed = '') {
        try {
            $viewData['title'] = "Send Push Notification";

            $viewData['ACTION_LABEL'] = (isset($HistoryID) && $HistoryID != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

            if ($HistoryID != '' && $ed != '' && $ed == 'y') {
                $getData = $this->notification_model->getNotificationById($HistoryID);
                $viewData['getNotificationData'] = $getData;
            }

            if ($this->input->post('action') && $this->input->post('action') == 'backoffice.notificationadd') {
                unset($_POST['vNotifyUrl']);
                $id = $this->notification_model->addNotification($_POST);
                if ($id) {
                    $this->sendPushNotification($id);
                    // if (isset($_FILES) && !empty($_FILES)) {
                    //     $param = array(
                    //         'fileType' => 'image',
                    //         'maxSize' => 20,
                    //         'uploadFor' => array(
                    //             'key' => 'notification',
                    //             'id' => $id
                    //         ),
                    //         'requireThumb' => TRUE
                    //     );
                    //     $this->load->library('fileupload', $param);
                    //     $upload_files = $this->fileupload->upload($_FILES, 'vImage');
                    //     if (!empty($upload_files)) {
                    //         foreach ($upload_files as $V) {
                    //             $this->notification_model->updateNotificationImage($id, $V);
                    //         }
                    //     }
                    // }
                    $succ = array('0' => "Notification added successfully.");
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => "Notification not added, please try again.");
                    $this->session->set_userdata('ERROR', $err);
                }
     //           redirect('notification/history', 'refresh');
                $url = 'notification/history';
echo '<script>
window.location.href = "'.base_url().'index.php?/'.$url.'";
</script>';
            } elseif ($this->input->post('action') && $this->input->post('action') == 'backoffice.notificationedit') {
                redirect('notification/history', 'refresh');
                $notifyImgUrl = $_POST['vNotifyUrl'];
                unset($_POST['vNotifyUrl']);
                if ($this->notification_model->editNotification($_POST)) {
                    // $param = array(
                    //     'fileType' => 'image',
                    //     'maxSize' => 20,
                    //     'uploadFor' => array(
                    //         'key' => 'notification',
                    //         'id' => $this->input->post('iPushNotifyID')
                    //     ),
                    //     'requireThumb' => TRUE
                    // );

                    // $this->load->library('fileupload', $param);

                    // if ($this->input->post('removepic') == '1') {
                    //     $this->fileupload->removeFile();
                    //     if (!empty($notifyImgUrl)) {
                    //         $ImagePath = 'pushNotification/' . $this->input->post('iPushNotifyID') . '/' . $notifyImgUrl;
                    //         $ImageThumbPath = 'pushNotification/' . $this->input->post('iPushNotifyID') . '/thumb/' . $notifyImgUrl;
                    //         //$this->aws_sdk->deleteImage($ImagePath);
                    //         //$this->aws_sdk->deleteImage($ImageThumbPath);
                    //     }
                    //     $this->notification_model->updateNotificationImage($this->input->post('iPushNotifyID'), '');
                    // }
                    // if (isset($_FILES) && !empty($_FILES) && $_FILES['vImage']['error'] === 0) {
                    //     $this->load->library('fileupload', $param);
                    //     $this->fileupload->removeFile();
                    //     $upload_files = $this->fileupload->upload($_FILES, 'vImage');
                    //     if (!empty($upload_files)) {
                    //         foreach ($upload_files as $V) {
                    //             $this->notification_model->updateNotificationImage($this->input->post('iPushNotifyID'), $V);
                    //         }
                    //         if (!empty($notifyImgUrl)) {
                    //             $ImagePath = 'pushNotification/' . $this->input->post('iPushNotifyID') . '/' . $notifyImgUrl;
                    //             $ImageThumbPath = 'pushNotification/' . $this->input->post('iPushNotifyID') . '/thumb/' . $notifyImgUrl;
                    //             //$this->aws_sdk->deleteImage($ImagePath);
                    //             //$this->aws_sdk->deleteImage($ImageThumbPath);
                    //         }
                    //     }
                    // }
                    $succ = array('0' => "Notification updated successfully.");
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => "Unable to update notification, please try again.");
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('notification/history', 'refresh');
            }

            $viewData['locationData'] = $this->notification_model->getLocations();

            $viewData['restaurantData'] = $this->notification_model->getRestaurants();

            $viewData['featuredRestaurantData'] = $this->notification_model->getFeaturedRestaurants();

            $viewData['eventData'] = $this->notification_model->getEvents();

            $viewData['comboData'] = $this->notification_model->getCombo();

            $viewData['offerData'] = $this->notification_model->getOffers();
            $this->load->view('notification/notification_add', $viewData);
        } catch (Exception $ex) {
            throw new Exception('Error in addhistory function - ' . $ex);
        }
    }

    function notificationStatus($id = '', $rm = '') {
        if ($id != '' && $rm != '' && $rm == 'y') {
            if ($this->notification_model->changeStatus($id)) {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

    function notificationDeleteAll() {
        $data = $_POST['rows'];
        if ($this->notification_model->softDelete($data)) {
            $res = $this->aws_sdk->deleteDirectory('cuisine/' . $data[0]);
            echo '1';
        } else {
            echo '0';
        }
    }

}
