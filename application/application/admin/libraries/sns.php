<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/aws.phar');

/**
 * Description of sns
 * @author Admin
 */
class SNS {

    var $SNS_KEY, $SNS_SECRET, $SNS_REGION;
    var $PLATEFORM_ARN, $PLATEFORM_TYPE, $PLATEFORM_APNS;
    var $SNS_OBJ;
    var $IS_LIVE;
    var $END_POINT_ARN, $TOPIC_ARN, $SUBSCRIPTION_ARN;

    /*
     * SET PLATE-FORM ios/android
     */

    function __construct($requestVal = array()) {
        /*
         * SET PLATEFORM TYPE/APPLICATION VALUE [ IS IT LIVE OR NOT ?? ]
         */
        if (!empty($requestVal)) {
            foreach ($requestVal as $KEY => $VAL) {
                $this->$KEY = $VAL;
            }
        }


        /*
         * SET PRIMARY VALUES..
         * STEP 1
         */
        $this->_setPrimaryValue();

        /*
         * SET OBEJCT VALUE
         * STEP 2
         */
        $this->_setObjectValue();
    }

    /*
     * SET PLATEFORM ARN
     * STEP 1.1
     */

    private function _setARN() {
        try {
            switch ($this->PLATEFORM_TYPE) {
                case 'ios' :
                    $this->PLATEFORM_ARN = $this->IS_LIVE ?
                            'arn:aws:sns:ap-southeast-1:224401661599:app/APNS/HungerMafia_iOS_live_user' :
                            'arn:aws:sns:ap-southeast-1:224401661599:app/APNS_SANDBOX/HungerMafia_iOS_dev_user';

                    $this->PLATEFORM_APNS = $this->IS_LIVE ?
                            'APNS' : 'APNS_SANDBOX';
                    break;

                case 'android' :
                    $this->PLATEFORM_ARN = 'arn:aws:sns:ap-southeast-1:224401661599:app/GCM/HungerMafia_android_user';
                    break;
            }
        } catch (Exception $ex) {
            throw new Exception('Erro rin _setARN function - ' . $ex);
        }
    }

    /*
     * SET PRIMARY VALUES...
     * STEP 1
     */

    private function _setPrimaryValue() {
        try {
            $this->SNS_KEY = 'AKIAJ6AHYZKKMWEWGN3Q';
            $this->SNS_SECRET = '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';
            $this->SNS_REGION = 'ap-southeast-1';

            /*
             * SET ARN VALUES
             * STEP 1.1
             */
            $this->_setARN();
        } catch (Exception $ex) {
            throw new Exception('Error in _setPrimaryValue function - ' . $ex);
        }
    }

    /*
     * SET OBJECT VALUE
     * STEP 2
     */

    private function _setObjectValue() {
        try {
            $this->SNS_OBJ = Aws\Sns\SnsClient::factory(array(
                        'key' => $this->SNS_KEY,
                        'secret' => $this->SNS_SECRET,
                        'region' => $this->SNS_REGION
            ));
        } catch (Exception $ex) {
            throw new Exception('Error in_setObjectValue function - ' . $ex);
        }
    }

    /*
     * CREATE PLATEFORM_END_POINT_ARN
     */

    function createPlatFormEndPointARN($deviceToken = '', $userId = '') {
        try {
            if ($deviceToken != '' && $userId != '') {
                $RESULT = $this->SNS_OBJ->createPlatformEndpoint(array(
                    'PlatformApplicationArn' => $this->PLATEFORM_ARN,
                    'Token' => $deviceToken,
                    'CustomUserData' => $userId
                ));
            }

            $this->END_POINT_ARN = $RESULT['EndpointArn'];

            return $this->END_POINT_ARN;
        } catch (Exception $ex) {
            throw new Exception('Error in createPlatformEndPointARN function - ' . $ex);
        }
    }

    /*
     * CREATE TOPIC
     */

    function createTopic($topicName = '') {
        try {
            if ($topicName != '') {
                $RESULT = $this->SNS_OBJ->createTopic(array(
                    'Name' => $topicName
                ));

                $this->TOPIC_ARN = $RESULT['TopicArn'];

                return $this->TOPIC_ARN;
            }
        } catch (Exception $ex) {
            throw new Exception('Error in createTopic function - ' . $ex);
        }
    }

    /*
     * TO SUBSCRIBE THE USER
     */

    function subscribeUser($endPointARN = '', $topicARN = '') {
        try {
            $RESULT = $this->SNS_OBJ->subscribe(array(
                'TopicArn' => $topicARN != '' ? $topicARN : $this->TOPIC_ARN,
                'Protocol' => 'application',
                'Endpoint' => $endPointARN != '' ? $endPointARN : $this->END_POINT_ARN
            ));

            $this->SUBSCRIPTION_ARN = $RESULT['SubscriptionArn'];

            return $this->SUBSCRIPTION_ARN;
        } catch (Exception $ex) {
            throw new Exception('Error in subscribeUser function - ' . $ex);
        }
    }

    /*
     * TO UNSUBSCRIBE THE USER
     */

    function unSubscribeUser($SUBSCRIPTION_ARN) {
        try {
            if ($SUBSCRIPTION_ARN != '') {
                $this->SNS_OBJ->unsubscribe(array(
                    'SubscriptionArn' => $SUBSCRIPTION_ARN,
                ));
            }
        } catch (Exception $ex) {
            throw new Exception('Error in unSubscribeUser function - ' . $ex);
        }
    }

    /*
     * BROADCAST THE MESSAGE...
     */

    function broadCast($MSG = '', $topicARN = '', $endPointARN = '', $TARGET = 'all') {
        try {
            IF ($MSG != '') {

                $PUBLISH_ARR = array(
                    'MessageStructure' => 'json',
                    'Message' => json_encode(array(
                        'default' => '',
                        $this->PLATEFORM_APNS => json_encode(array(
                            'aps' => array(
                                'alert' => $MSG,
                                'sound' => 'default',
                                'badge' => 1
                            )
                        )),
                        'GCM' => json_encode(array(
                            'data' => array(
                                'message' => $MSG,
                                'xdata' => array(
                                    'type' => 'broadcast',
                                    'id' => 1
                                )
                            ),
                        ))
                    ))
                );
                if ($TARGET == 'all')
                    $PUBLISH_ARR['TopicArn'] = $topicARN != '' ? $topicARN : $this->TOPIC_ARN;
                else
                    $PUBLISH_ARR['TargetArn'] = $endPointARN != '' ? $endPointARN : $this->END_POINT_ARN;

                //mprd($PUBLISH_ARR);

                $this->SNS_OBJ->publish($PUBLISH_ARR);
            }
        } catch (Exception $ex) {
            throw new Exception('Error in broadCast function - ' . $ex);
        }
    }

    /*
     * UNSUBSCRIBE THE USER
     */

    function unSubScribe($subScribeARN = '') {
        try {
            if ($subScribeARN != '') {
                $this->SNS_OBJ->unsubscribe(array(
                    'SubscriptionArn' => $subScribeARN,
                ));
            }
        } catch (Exception $ex) {
            throw new Exception('Error in unSubScribe function - ' . $ex);
        }
    }

    /*
     * DELETE END POINT ARN
     */

    function deleteEndPointARN($endPointARN = '') {
        try {
            if ($endPointARN != '') {
                $this->SNS_OBJ->deleteEndpoint(array(
                    'EndpointArn' => $endPointARN,
                ));
            }
        } catch (Exception $ex) {
            throw new Exception('Error in deleteEndPointARN  function - ' . $ex);
        }
    }

    /*
     * DELETE TOPIC
     */

    function deleteTopic($topicARN = '') {
        try {
            if ($topicARN != '') {
                $this->SNS_OBJ->deleteTopic(array(
                    'TopicArn' => $topicARN,
                ));
            }
        } catch (Exception $ex) {
            throw new Exception('Error in deleteTopic function - ' . $ex);
        }
    }

}
