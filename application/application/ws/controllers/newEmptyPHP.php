

<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class PushNotificationShell extends AppShell {

	public $uses = array('AppNotificationUser', 'PushNotification', 'PushNotificationJob', 'NotificationGroup', 'AppUserToken');
	public $limit = PUSH_NOTIFICATION_CHUNK_LIMIT;
	public $currentRunningProcess = CURRENT_RUNNING_PROCESS_LIMIT;
	public $pendingStatus = '1';
	public $inprocessStatus = '2';
	public $completeStatus = '3';
	public $andiPlatform = 'Android';
	public $IOSPlatform = 'IOS';

	/**
	 * @method      main
	 * @desc        this function is main/default function being used for couple of functions
	 * @access      public
	 * @author      3031@kelltontech.com
	 * @return      void
	 */
	public function main() {
		$notificationArr = $this->__getNotification();
		//print_r($notificationArr);die;
		if (!empty($notificationArr)) {
			$this->__updateNotificationStatus($notificationArr['PushNotification']['id'], $this->inprocessStatus);
			$this->__createPushNotificationJob($notificationArr['PushNotification']['id'], $notificationArr['PushNotification']['platform'], $notificationArr['PushNotification']['group_id']);
		}

		$pendingJobData = $this->__getPendingJobs();
		if (isset($pendingJobData['PushNotificationJob']['notification_id']) && !empty($pendingJobData)) {
			$getGroupid = $data = $this->PushNotification->find('first', array('conditions' => array("PushNotification.id" => $pendingJobData['PushNotificationJob']['notification_id']), "fields" => array("PushNotification.group_id")));
		}
		////pr($getGroupid);die;
		while ($pendingJobData) {
			$jobId = $pendingJobData['PushNotificationJob']['id'];
			$notificationId = $pendingJobData['PushNotificationJob']['notification_id'];
			$limit = $pendingJobData['PushNotificationJob']['end_limit'] - $pendingJobData['PushNotificationJob']['start_limit'];
			$offset = $pendingJobData['PushNotificationJob']['start_limit'];
			$platform = $pendingJobData['PushNotificationJob']['platform'];
			$gid = $getGroupid['PushNotification']['group_id'];

			$runningJobs = $this->__getRunningJobs();

			if ($runningJobs < $this->currentRunningProcess) {
				$this->__updateJobs($jobId, $this->inprocessStatus);
				if ($platform == $this->andiPlatform) {
					shell_exec(APP . "Console/cake android_notification $jobId $notificationId $limit $offset $platform $gid >/dev/null 2>/dev/null &");
					//shell_exec(APP."Console/cake android_notification $jobId $notificationId $limit $offset $platform $gid");
					//exec(APP."Console\cake android_notification $jobId $notificationId $limit $offset $platform $gid> /dev/null 2>/dev/null &");
				} else if ($platform == $this->IOSPlatform) {
					shell_exec(APP . "Console/cake IOSNotification $jobId $notificationId $limit $offset $platform $gid >/dev/null 2>/dev/null &");
					//exec(APP."Console\cake IOSNotification $jobId $notificationId $limit $offset $platform $gid> /dev/null 2>/dev/null &");
				}
			} else {
				$this->_stop();
			}
			$pendingJobData = $this->__getPendingJobs();
		}
	}

//Console\cake IOSNotification 18 6 1000 0 IOS 2
	/**
	 * @method      __createPushNotificationJob
	 * @desc        this function is used to create the jobs as per device tokens
	 * @access      private
	 * @author      3031@kelltontech.com
	 * @return      void
	 */
	private function __createPushNotificationJob($notificationId, $platform, $gid) {
		if ($platform == $this->andiPlatform || $platform == $this->IOSPlatform) {
			$jobPlatform = array($platform);
		} else {
			$jobPlatform = array($this->andiPlatform, $this->IOSPlatform);
		}
		$this->__saveJobs($notificationId, $jobPlatform, $gid);
	}

	/**
	 * @method      __saveJobs
	 * @desc        this function is used to create the jobs as per device tokens
	 * @access      private
	 * @author      3031@kelltontech.com
	 * @return      void
	 */
	private function __saveJobs($notificationId, $jobPlatform, $gid) {
		foreach ($jobPlatform as $platform) {
			$deviceIdCount = $this->__getdeviceIdCount($platform, $gid);
			$limit = $this->limit;
			$max = ceil(($deviceIdCount / $limit));
			for ($i = 0; $i < $max; $i++) {
				$offset = $i * $limit;
				$this->__createJob($notificationId, $platform, $limit, $offset);
			}
		}
	}

	/**
	 * @method      __createJob
	 * @desc        this function is used to create the jobs as per device tokens
	 * @access      private
	 * @author      3031@kelltontech.com
	 * @return      void
	 */
	private function __createJob($notificationId, $platform, $limit, $offset) {
		if ($platform == $this->andiPlatform) {
			$device = "Andi";
		} else {
			$device = "IOS";
		}

		$getNotification = $this->PushNotification->find("first", array("conditions" => array("PushNotification.id" => $notificationId), "fields" => array("PushNotification.parameter")));

		if (isset($getNotification['PushNotification']['parameter']) && !empty($getNotification['PushNotification']['parameter'])) {
			$data = json_decode($getNotification['PushNotification']['parameter'], true);
			if (isset($data['img']) && !empty($data['img'])) {
				$imageName = array_reverse(explode("/", $data['img']));
				//pr($imageName);die;
				$imgHost = implode('/', array_reverse(array_slice($imageName, 4)));
				//http://devfms.findit.com.my/PushNotification/showImage?id=349&img=notify_1427363993.jpeg&plateform=andi&type=notification_image
				$imgPath = IMG_HOST . "PushNotification/showImage?id=" . $notificationId . "&img=" . $imageName[0] . "&plateform=" . $device . "&type=notification_image&language_code=en";
				$data['img'] = $imgPath;
				$notificationParams = json_encode($data);
			} else {
				$notificationParams = $getNotification['PushNotification']['parameter'];
			}
		} else {
			$notificationParams = "";
		}
		if (!empty($notificationParams)) {
			$jobData['notification_id'] = $notificationId;
			$jobData['start_limit'] = $offset;
			$jobData['end_limit'] = $offset + $limit;
			$jobData['platform'] = $platform;
			$jobData['created'] = time();
			$jobData['status'] = $this->pendingStatus;
			$jobData['parameter'] = $notificationParams;
			//print_r($jobData);
			$this->PushNotificationJob->create();
			$this->PushNotificationJob->save($jobData);
		}
	}

	/**
	 * @method      __getDeviceIdCount
	 * @desc        this function is used to find out the count of device id.
	 * @access      private
	 * @author      3031@kelltontech.com
	 * @return      count
	 */
	private function __getDeviceIdCount($platform = array(), $gid = null) {
		$notificationFilters = $this->___getGroupFilter($gid);
		if ($platform == $this->andiPlatform) {
			$platform = 'Andi';
			$deviceToken = 'device_token';
		} else {
			$platform = 'IOS';
			$deviceToken = 'device_token';
		}
		$conditions = array('AppUserToken.status' => 1, 'AppUserToken.platform' => $platform, "OR" => array('AppUserToken.' . $deviceToken . ' <>' => null, 'AppUserToken.' . $deviceToken . ' <>' => ''));
		if (isset($notificationFilters['city']) && !empty($notificationFilters['city']))
			$conditions[] = array('AppUserToken.city_id' => $notificationFilters['city']);
		/* else
		  $conditions[] = array('AppUserToken.city_id !='=> null); */
		$data = $this->AppUserToken->find('count', array('conditions' => $conditions));
		return $data;
	}

	/**
	 * @method      __getNotification
	 * @desc        this function is used to find out notifications.
	 * @access      private
	 * @author      3031@kelltontech.com
	 * @return      notification array
	 */
	private function __getNotification($platform = array()) {
		$time = date('Y-m-d H:i:s');
		$conditions = array('PushNotification.status' => $this->pendingStatus, 'PushNotification.schedule_time <=' => $time);
		$fields = array('PushNotification.id', 'PushNotification.platform', 'PushNotification.group_id');
		$pushNotificationData = $this->PushNotification->find('first', array('fields' => $fields, 'conditions' => $conditions, 'order' => 'PushNotification.id ASC'));
		return $pushNotificationData;
	}

	/**
	 * @method      ___getGroup
	 * @desc        this function is used to fetch group records
	 * @access      private
	 * @author      3055@kelltontech.com
	 * @return      void
	 */
	private function ___getGroupFilter($groupID = null) {
		$conditions = array('NotificationGroup.status' => $this->pendingStatus, 'NotificationGroup.id' => $groupID);
		$fields = array('NotificationGroup.group_condition');
		$groupNotification = $this->NotificationGroup->find('first', array('fields' => $fields, 'conditions' => $conditions));
		$results = json_decode($groupNotification['NotificationGroup']['group_condition'], true);
		$notificationParams = array();
		foreach ($results as $result) {
			if (!empty($result['conditions'])) {
				$notificationParams[$result['name']] = explode(',', $result['conditions']);
			} else {
				$notificationParams[$result['name']] = array();
			}
		}
		return $notificationParams;
	}

	/**
	 * @method      __updateNotificationStatus
	 * @desc        this function is used to update notification
	 * @access      private
	 * @author      3031@kelltontech.com
	 * @return     void
	 */
	private function __updateNotificationStatus($notificationId, $status) {
		$notificationData['id'] = $notificationId;
		$notificationData['status'] = $status;
		$this->PushNotification->save($notificationData);
	}

	/**
	 * @method      __getPendingJobs
	 * @desc        this function is used to find out pending notifications.
	 * @access      private
	 * @author      3031@kelltontech.com
	 * @return      pending jobs data
	 */
	private function __getPendingJobs() {
		$conditions = array('PushNotificationJob.status' => $this->pendingStatus);
		$fields = array('PushNotificationJob.*');
		$pushNotificationJobData = $this->PushNotificationJob->find('first', array('fields' => $fields, 'conditions' => $conditions, 'order' => 'PushNotificationJob.id ASC'));
		return $pushNotificationJobData;
	}

	/**
	 * @method      __getRunningJobs
	 * @desc        this function is used to find out running notifications.
	 * @access      private
	 * @author      3031@kelltontech.com
	 * @return      running jobs data
	 */
	private function __getRunningJobs() {
		$conditions = array('PushNotificationJob.status' => $this->inprocessStatus);
		$countRunningJobs = $this->PushNotificationJob->find('count', array('conditions' => $conditions, 'order' => 'PushNotificationJob.id ASC'));
		return $countRunningJobs;
	}

	/**
	 * @method      __updateJobs
	 * @desc        this function is used to update jobs
	 * @access      private
	 * @author      3031@kelltontech.com
	 * @return      void
	 */
	private function __updateJobs($jobId, $status) {
		$jobData['id'] = $jobId;
		$jobData['status'] = $status;
		$this->PushNotificationJob->save($jobData);
	}

}

?>