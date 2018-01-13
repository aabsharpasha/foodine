<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of restaurant
 * @author Foodine : Solutions Ltd
 */
class Restaurant extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('restaurant_model');
    }

    /**
     * Action to get Restaurant listing from Solr
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post
     * @return array
     */
    function getRestaurantList_post() {
        try {
            //MANDATORY PARAMETERS [searchType = nearby, feature, category, choice]
            $allowParam = array(
                'latitude', 'longitude',
                'searchType', /*'pageId'*/
            );
            //TO SET DEFAULT VALUES
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $RESTAURANTDATA = '';
            $RESTAURANTTOTALDATA = 0;
            $RESTAURANTTOTALPAGE = 0;
           // print_r($this->post()); exit;
            //CHECK MANDATORY PARAMETERS
            
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->getRestaurantListFoodine($this->post());
                $userId = $this->post('userId');
                $this->load->model('user_model');
                if($userId) {
                   // echo 'hi'; exit;
                    $userId = $this->post('userId');
                    $sort = $this->user_model->getCIMUserArray('sort', @$userId);
                    $price_sort = $sort[0]['selected'];
                    $rating_sort = $sort[1]['selected'];
                   //print_r($price_sort."--".$rating_sort); exit;
                }
              //  echo $rateorder; exit;
                if ($rating_sort) {
                  

                    usort($resp['data'], function($postA, $postB) {
                        
                          if (!isset($rateorder) || empty($rateorder)) {
                       // echo 'here'; exit;
                        $rateorder = 'asc';
                    }

                    $orderBy = $rateorder;
                        if ($postA["rating"] == $postB["rating"]) {
                            return 0;
                        }
                       // echo $orderBy; exit;
                        if ($orderBy == 'ASC' || $orderBy == 'asc') {
                            return ($postA["rating"] < $postB["rating"]) ? 1 : -1;
                        } else {
                          // echo 'hello'; exit;
                            return ($postA["rating"] > $postB["rating"]) ? 1 : -1;
                        }
                    });
                }
                
                if (!empty($resp['data'])) {
                    $MESSAGE = RESTAURANT_FOUND;
                    $STATUS = SUCCESS_STATUS;
                    $RESTAURANTDATA = $resp['data'];
                    $RESTAURANTTOTALDATA = $resp['totalRecord'];
                    $RESTAURANTTOTALPAGE = $resp['totalPage'];
                   // $BANNER = isset($resp['banner']) ? $resp['banner'] : '';
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                    $STATUS = FAIL_STATUS;
                }
            }
            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );

            if (@$RESTAURANTDATA !== '') {
                $resp['totalRestaurants'] = $RESTAURANTTOTALDATA;
                $resp['totalPage'] = $RESTAURANTTOTALPAGE;
                $resp['restaurants'] = $RESTAURANTDATA;
            }
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantWebList_post function - ' . $ex);
        }
    }

    /**
     * Action to get Restaurant listing from Solr  :: Web
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post
     * @return array
     */
    function getRestaurantListingSolr_post() {
        try {
            //MANDATORY PARAMETERS [searchType = nearby, feature, category, choice]
            $allowParam = array(
                'searchType', 'pageId'
            );
            //TO SET DEFAULT VALUES
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $RESTAURANTDATA = '';
            $RESTAURANTTOTALDATA = 0;
            $RESTAURANTTOTALPAGE = 0;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->getRestaurantList($this->post());
                if (!empty($resp['data'])) {
                    $MESSAGE = RESTAURANT_FOUND;
                    $STATUS = SUCCESS_STATUS;
                    $RESTAURANTDATA = $resp['data'];
                    $RESTAURANTTOTALDATA = $resp['totalRecord'];
                    $RESTAURANTTOTALPAGE = $resp['totalPage'];
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                    $STATUS = FAIL_STATUS;
                }
            }
            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );

            if (@$RESTAURANTDATA !== '') {
                $resp['totalPoint'] = 0;
                $resp['RESTAURANTTOTALDATA'] = $RESTAURANTTOTALDATA;
                $resp['RESTAURANTTOTALPAGE'] = $RESTAURANTTOTALPAGE;
                $resp['RESTAURANTDATA'] = $RESTAURANTDATA;
            }
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantWebList_post function - ' . $ex);
        }
    }

    /**
     * Action to indexed data in solr
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @return array
     */
    function solrAdd_post() {
        try {
            $this->load->model('restaurant_model');
            // fetch all restaurants ready to be indexed in solr
            $restaurants = $this->restaurant_model->fetchRestaurantsForSolr();
            // solr index all restaurants
            $this->load->helper('solr_helper');
            $result = addSolrDocs($restaurants);
            $this->response($result, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in solrAdd_post function - ' . $ex);
        }
    }

    /**
     * Action to delete data from solr
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post <int> Restaurant Id
     * @return array
     */
    function solrDelete_post() {
        try {
            //LOAD SOLR HELPER
            $this->load->helper('solr_helper');
            // fetch all restaurants ready to be delete from solr
            $restaurantIds = $this->restaurant_model->fetchDeletedRestaurantsForSolr();
            // COMMA SEPRATED RESTAURANT IDS
            $result = deleteSolrDocs($restaurantIds);
            $this->response($result, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in solrDelete_post function - ' . $ex);
        }
    }

    /**
     * Action to get the list of restaurants
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $city <string>
     * @return void
     */
    function getRestaurantWebList_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array('city');
            //TO SET DEFAULT VALUES
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $RESTAURANTDATA = '';
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $MESSAGE = NO_RECORD_FOUND;
                $STATUS = FAIL_STATUS;
                //SEARCH RESTAURANT BY CITY
                $res = $this->restaurant_model->getRestaurantListCity($this->post('city'));
                if (!empty($res)) {
                    $MESSAGE = RESTAURANT_FOUND;
                    $STATUS = SUCCESS_STATUS;
                    $RESTAURANTDATA = $res;
                }
            }
            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );

            if (@$RESTAURANTDATA !== '') {
                $resp['RESTAURANTDATA'] = $RESTAURANTDATA;
            }
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantWebList_post function - ' . $ex);
        }
    }

    /**
     * Action to get Restaurant list between source & destination
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $centerLat<float>, $centerLng<float>, $proximity<int>
     * @return array
     */
    function getRestaurantSDS_post() { //Obsolete
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'sourceLatitude', 'sourceLongitude'
            );
            //TO SET DEFAULT VALUES
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $RESTAURANTDATA = '';
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $MESSAGE = NO_RECORD_FOUND;
                $STATUS = FAIL_STATUS;
                //TO GET RESULTS BY DISTANCE
                $res = $this->restaurant_model->getRestaurantInProximity($this->post());
                //$this->response($res, 200);
                if (!empty($res)) {
                    $MESSAGE = RESTAURANT_FOUND;
                    $STATUS = SUCCESS_STATUS;
                    $RESTAURANTDATA = $res;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'restaurants' => $res,
            );

//            if (@$RESTAURANTDATA !== '') {
//                $resp['RESTAURANTDATA'] = $RESTAURANTDATA;
//            }
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantWebList_post function - ' . $ex);
        }
    }

    /**
     * Action to get Restaurant list
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $search <string>
     * @return array
     */
    function getRestaurantListing_post() { //Obsolete
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'search'
            );
            //TO SET DEFAULT VALUES
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $RESTAURANTDATA = '';
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $MESSAGE = NO_RECORD_FOUND;
                $STATUS = FAIL_STATUS;
                //TO GET RESULTS BY DISTANCE
                $res = $this->restaurant_model->getRestaurantListing($this->post());
                if (!empty($res)) {
                    $MESSAGE = RESTAURANT_FOUND;
                    $STATUS = SUCCESS_STATUS;
                    $RESTAURANTDATA = $res;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );

            if (@$RESTAURANTDATA !== '') {
                $resp['restaurants'] = $RESTAURANTDATA;
            }
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantWebList_post function - ' . $ex);
        }
    }

    /**
     * Action to get restaurant detail
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return array
     */
    function getRestaurantDetail_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array('restaurantId');
            // TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $RESTAURANTDETAIL = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $MESSAGE = NO_RECORD_FOUND;
                $STATUS = FAIL_STATUS;
               
                //GET DETAIL
              $resDetail = $this->restaurant_model->getRestaurantDetail($this->post('restaurantId'), $this->post('longitude'), $this->post('latitude'), $this->post('userId'), $this->post('platform'));
              $resRet['id'] = $resDetail['info']['restaurantId'];
              $resRet['rating'] = $resDetail['info']['restaurantRatting'];
              $resRet['bookmarked'] = $resDetail['info']['isFavourite'] == 'yes' ? "true" : "false" ;
              $resRet['distance'] = $resDetail['info']['restaurantDistance'];
              $resRet['address'] = $resDetail['info']['restaurantAddress'];
              $resRet['restaurantName'] = $resDetail['info']['restaurantName'];
              $resRet['dishName'] = $resDetail['info']['restaurantSpeciality']['food'];
              $resRet['isOpen'] = $resDetail['info']['isOpenNow'] == 'Closed' ? false : true;
              $resRet['restaurantPriceValue'] = trim(str_replace('Rs. ','',$resDetail['info']['restaurantPriceValue']));
              $resRet['latitude'] = $resDetail['info']['restaurantLat'];
              $resRet['longitude'] = $resDetail['info']['restaurantLong'];
              $resRet['contactNumber'] = array_pop($resDetail['info']['restaurantContact']);
              //$resRet['contact_number1'] = $resDetail['info']['restaurantContact'];
              $resRet['photoUrl'] = $resDetail['info']['restaurantImage'];
              
             $resRet['todayOpenTiming'] = 'Today :'.$resDetail['info']['restaurantOpenDays'][date('w', strtotime($date))+1]['time'];
              
              $resRet['offers'] = array();
              foreach($resDetail['restaurantDeals'] as $line) {
                 // print_r($line);
                  $resRet['offers'][]= array('description' => $line['offerText'], 'offerCode' => $line['dealCode']);
              }
              
              if (isset($resDetail['info']['restaurantId']) && !empty($resDetail['info']['restaurantId'])) {
                    $MESSAGE = RESTAURANT_FOUND;
                    $STATUS = SUCCESS_STATUS;
                    $RESTAURANTDETAIL = $resRet;
              }
              
               $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
                 );
            if (@$RESTAURANTDETAIL !== '') {
                $resp['restaurant'] = $RESTAURANTDETAIL;
            }
              $resp['cuisine'] = $resDetail['info']['totalCuisine'];
              $resp['mustEat'] = $resDetail['info']['restaurantSpeciality']['food'];
               $resp['dressCode'] = $resDetail['info']['dressCode'];
              //$resp['costOf2PeopleTip'] = $resDetail['info']['costDescription'];
              $resp['costOf2PeopleTip'] = $resDetail['info']['costDescription'];
              
              $resp['costOf2People'] = array($resDetail['info']['isAlcohol'].' '.$resDetail['info']['restaurantPriceValue'], '');
              $resp['restaurantPriceValue'] = $resDetail['info']['restaurantPriceValue'];
              $resp['openingHours'] = array();
              foreach($resDetail['info']['restaurantOpenDays'] as $openLine) {
                  $resp['openingHours'][] = $openLine['name'].": ".$openLine['time'];
              } 
              
               $resp['barMenu'] = array();
               //print_r($resDetail['restaurantMenu']); exit;
              foreach($resDetail['restaurantMenu']['bar'] as $bar) {
                 // print_r($food); exit;
                  $resp['barMenu'][] = $bar['menuImage'];
              } 
              
               $resp['foodMenu'] = array();
              foreach($resDetail['restaurantMenu']['food'] as $food) {
                  $resp['foodMenu'][] = $food['menuImage'];
              } 
              
               $resp['restaurantPhoto'] = $resDetail['info']['images'];
              
              $resp['facilities'] = array();
              foreach($resDetail['info']['totalFacility'] as $facility) {
                  switch($facility) {
                      case 'Wi-Fi Available':
                          $iconName = 'f_wifi';
                          break;
                       case 'Air Conditioned':
                          $iconName = 'f_air_conditioned';
                          break;
                       case 'Serves Non-Veg':
                          $iconName = 'f_non_veg';
                          break;
                      case 'Serves Veg':
                          $iconName = 'f_veg';
                          break;
                       case 'Cards Accepted':
                          $iconName = 'f_card_accepted';
                          break;
                      case 'Cash Accepted':
                          $iconName = 'f_cash_accepted';
                          break;
                       case 'Seating Available':
                          $iconName = 'f_seat_available';
                          break;
                       case 'Serves Alcohol':
                          $iconName = 'f_serves_alcohol';
                          break;
                      default:
                         // echo $facility;
                          $fac_name_arr = explode(' ',$facility);
                          //var_dump($fac_name_arr); exit;
                          $iconName = 'f_'.strtolower(implode('_', $fac_name_arr));
                          //$iconName = '';
                          break;
                          
                  }
                  $resp['facilities'][]= array('name' => $facility, 'iconName' => $iconName);
              }
              //exit;
            }
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantDetail_post function - ' . $ex);
        }
    }

    /**
     * Action to set like/dislike
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $restaurantId <int>, $userId <int>
     * @return status
     */
    function setRestaurantLikeDisLike_post() { //Obsolete
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId', 'restaurantId', 'saveValue'
            );
            // TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //SET LIKE/DISLIKE DATA OF RESTAURANT
                $response = $this->restaurant_model->setRestaurantLikeDisLike($this->post('userId'), $this->post('restaurantId'), $this->post('saveValue'));
                switch ($response) {
                    case -2 :
                        if ($this->post('saveValue') == 'like') {
                            $MESSAGE = RESTAURANT_LIKED_BEFORE;
                        } else {
                            $MESSAGE = RESTAURANT_DISLIKED_BEFORE;
                        }
                        break;
                    case -1 :
                        $MESSAGE = NO_RECORD_FOUND;
                        break;
                    case 0 :
                        if ($this->post('saveValue') == 'like') {
                            $MESSAGE = RESTAURANT_LIKED;
                        } else {
                            $MESSAGE = RESTAURANT_DISLIKED;
                        }
                        $STATUS = SUCCESS_STATUS;
                        break;
                    default :
                        if ($this->post('saveValue') == 'like') {
                            $MESSAGE = RESTAURANT_LIKED;
                        } else {
                            $MESSAGE = RESTAURANT_DISLIKED;
                        }
                        $STATUS = SUCCESS_STATUS;
                        break;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in setRestaurantLikeDisLike_post function - ' . $ex);
        }
    }

    /**
     * Action to set bookmark of restaurant
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $restaurantId <int>, $userId <int>
     * @return status
     */
    function setRestaurantFavourite_post() { //Obsolete
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId', 'restaurantId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //SET RESTAURANT AS FAVOURITE/BOOKMARK
                $response = $this->restaurant_model->setRestaurantFavourite($this->post('userId'), $this->post('restaurantId'), $this->post('setFavourite'));
                switch ($response) {
                    case -2 :
                        $MESSAGE = 'You have successfully set as unfavourite restaurant.';
                        $STATUS = SUCCESS_STATUS;
                        break;

                    case -1 :
                        $MESSAGE = NO_RECORD_FOUND;
                        break;

                    case 0 :
                        $MESSAGE = RESTAURANT_FAV_SET;
                        break;

                    default :
                        $MESSAGE = RESTAURANT_FAV;
                        $STATUS = SUCCESS_STATUS;
                        break;
                }
            }
            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in setRestaurantFavourite_post function - ' . $ex);
        }
    }

    /**
     * Action to get reward list
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $userId <int>
     * @return array
     */
    function getRewardList_post() { //Obsolete
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $REWARDLIST = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //SET REWARDS
                $response = $this->restaurant_model->getRewardList($this->post());
                if (!empty($response)) {
                    $MESSAGE = REWARD_FOUND;
                    $STATUS = SUCCESS_STATUS;
                    $REWARDLIST = $response;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }
            $this->load->model('user_model');
            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );
             $this->load->model('user_points_model');
             $this->load->model('reward_model');
                $totalPoints        = $this->user_points_model->getTotalPoints($this->post('userId'));
                $redeemedPoints     = $this->reward_model->getRedeemedPoints($this->post('userId'));
                $data["availablePoints"] = ($totalPoints - $redeemedPoints) > 0 ? $totalPoints - $redeemedPoints : 0;
            $resp['rewardPoints'] = $data["availablePoints"];
            
            if (!empty($REWARDLIST)) {
                $resp['rewards'] = $REWARDLIST;
               
            }
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getRewardList_post function - ' . $ex);
        }
    }

    /**
     * Action to get reward detail
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $recordId <int>, $userId <int>
     * @return array
     */
    function getRewardDetail_post() { //Obsolete
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId', 'recordId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $REWARDLIST = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $response = $this->restaurant_model->getRewardList($this->post());
                if (!empty($response)) {
                    $MESSAGE = REWARD_FOUND;
                    $STATUS = SUCCESS_STATUS;
                    $REWARDLIST = $response;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );

            if (!empty($REWARDLIST)) {
                $resp['REWARDLIST'] = $REWARDLIST;
            }
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getRewardList_post function - ' . $ex);
        }
    }

    /**
     * Action to rate the application (ONE USER CAN RATE TO ONE RESTAURANT ONLY)
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $userId <int> 
     * @return void
     */
    function rateUs_post() { //Obsolete
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $REWARDLIST = array();
            $TOTALPOINT = 0;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //ADD RATING
                $resp = $this->restaurant_model->addRate($this->post('userId'));
                switch ($resp) {
                    case 0 :
                        $MESSAGE = RATEUS_ALREADY;
                        break;

                    default :
                        $MESSAGE = RATEUS_SUCC;
                        $STATUS = SUCCESS_STATUS;
                        $TOTALPOINT = '';
                        break;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'totalPoint' => $this->general_model->getRedeemAmount($this->post('userId'))
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in rateUs_post function - ' . $ex);
        }
    }

    /**
     * Action to redeem the reward
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $userId <int>, $rewardId <int>
     * @return void
     */
    function useReward_post() { //Obsolete
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId', 'rewardId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $REWARDLIST = array();
            $TOTALPOINT = 0;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->useReward($this->post());
                switch ($resp) {
                    case -2 :
                        $MESSAGE = REWARD_REQUEST_NILL;
                        break;

                    case -3:
                        $MESSAGE = REWARD_REQUEST_ALREADY;
                        break;

                    case -4:
                        $MESSAGE = REWARD_REQUEST_INSUFF;
                        break;

                    default:
                        $MESSAGE = REWARD_REQUEST_SUCC;
                        $STATUS = SUCCESS_STATUS;
                        break;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'totalPoint' => $this->general_model->getRedeemAmount($this->post('userId'))
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in useReward function - ' . $ex);
        }
    }

    /**
     * Action to book table
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post
     * @return array
     */
    function bookTable_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId', 'restaurantId',
                'slotId', 'bookDate', 'totalPerson'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->bookTable($this->post());
                switch ($resp) {
                    case -1:
                        $MESSAGE = NO_RECORD_FOUND;
                        break;

                    case -2:
                        $MESSAGE = TABLE_BOOKl_WARN;
                        break;

                    default:
                        $MESSAGE = TABLE_BOOK_SUCC;
                        $STATUS = SUCCESS_STATUS;
                        break;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'bookingId' => $resp,
                //'totalPoint' => $this->general_model->getRedeemAmount($this->post('userId'))
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in bookTable_post function - ' . $ex);
        }
    }

    /**
     * Action to book table
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post
     * @return array
     */
    function bookWebTable_post() {
        try {
            //MANDATORY PARAMETERS
//            $allowParam = array(
//                'userId', 'restaurantId', 'slotId', 'bookDate', 'totalPerson',
//                'mealType', 'userBookingName', 'userMobile', 'userEmail', 'userFullName'
//            );
            
              $allowParam = array(
                'userId', 'restaurantId', 'bookTime', 'bookDate', 'totalPerson',
                'bookingName', 'mobileNumber'
            );
              
            // TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {

                $resp = $this->restaurant_model->bookWebTable($this->post());
                switch ($resp) {
                    case -1:
                        $MESSAGE = NO_RECORD_FOUND;
                        break;

                    case -2:
                        $MESSAGE = TABLE_BOOK_WARN;
                        break;

                    case -3:
                        $MESSAGE = TABLE_BOOK_LIMIT;
                        break;

                    case -4:
                        $MESSAGE = TABLE_BOOK_CLOSED;
                        break;

                    default:
                        $MESSAGE = TABLE_BOOK_SUCC;
                        $STATUS = SUCCESS_STATUS;
                        break;
                }

                // SEND MAIL TO USER WITH PENDING STATUS
                extract($this->post());
                $subject = 'You just booked a table, check status.';
                $this->load->model("smtpmail_model", "smtpmail_model");
                $param = array(
                    '%MAILSUBJECT%' => 'Foodine : ' . $subject,
                    '%LOGO_IMAGE%' => BASEURL . '/images/hungermafia.png',
                    '%USER_NAME%' => @$userFullName,
                    '%STATUS%' => 'Pending',
                    '%RESTAURANT%' => @$restaurantName,
                    '%PERSON%' => @$totalPerson,
                );

                $tmplt = DIR_VIEW . 'email/table_book_status.php';
                $subject = 'Foodine : ' . $subject;
                $to = @$userEmail;
                $this->smtpmail_model->send($to, $subject, $tmplt, $param);
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'bookingId' => $resp,
                'totalPoint' => $this->general_model->getRedeemAmountWeb($this->post('userId')),
            );


            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in bookRestaurant function - ' . $ex);
        }
    }

    /**
     * Action to validate table booking
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post
     * @return array
     */
    function bookWebTableValidate_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId', 'restaurantId', 'slotId', 'date', 'peopleCount',
                'mealType', 'bookingName', 'mobileNumber'
            );

            // TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->bookWebTableValidate($this->post());
                switch ($resp) {
                    case -1:
                        $MESSAGE = NO_RECORD_FOUND;
                        break;

                    case -2:
                        $MESSAGE = TABLE_BOOK_WARN;
                        break;

                    case -3:
                        $MESSAGE = TABLE_BOOK_LIMIT;
                        break;

                    case -4:
                        $MESSAGE = TABLE_BOOK_CLOSED;
                        break;

                    default:
                        $MESSAGE = TABLE_BOOK_SUCC;
                        $STATUS = SUCCESS_STATUS;
                        break;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'bookingId' => $resp,
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in bookWebTableValidate_post function - ' . $ex);
        }
    }

    /**
     * Action to get time slots
     * @package Restaurant Controller
     * @access Public
     * @author 3031@Foodine :
     * @return array
     */
    function timeslots_post() {
        $MESSAGE = '';
        $STATUS = FAIL_STATUS;
        $data = array();
        try {
            $data = $this->restaurant_model->getTimeSlots();
            $STATUS = SUCCESS_STATUS;
            //RESPONSE
        } catch (Exception $ex) {
            throw new Exception('Error in timeslots_post function - ' . $ex);
        }
        $resp = array(
            'result' => $MESSAGE,
            'resultCode' => $STATUS,
            'DATA' => $data,
        );
        $this->response($resp, 200);
    }

    /**
     * Action to get all booking list
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return array
     */
    function bookTableList_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId'
            );
            // TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $BOOKINGLIST = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //GET LIST OF BOOKING
                $resp = $this->restaurant_model->bookTableList($this->post());

                if (!empty($resp)) {
                    $BOOKINGLIST = $resp;
                    $MESSAGE = BOOK_TABLE_FOUND;
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'bookings' => $BOOKINGLIST
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in bookTableList_post function - ' . $ex);
        }
    }

    /**
     * Action to get booking detail
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $bookingId <int>
     * @return array
     */
    function bookTableDetail_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId', 'bookingId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $BOOKINGLIST = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //GET BOOKING DETAIL
                $resp = $this->restaurant_model->bookTableList($this->post());
                if (!empty($resp)) {
                    $BOOKINGLIST = $resp;
                    $MESSAGE = BOOK_TABLE_FOUND;
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'BOOKINGLIST' => $BOOKINGLIST
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in bookTableDetail_post function - ' . $ex);
        }
    }

    /**
     * Action to cancel the booking
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $bookedId <int>
     * @return void
     */
    function cancelReservation_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId', 'bookedId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //CANCEL BOOKING
                $resp = $this->restaurant_model->cancelReservation($this->post('userId'), $this->post('bookedId'));
                switch ($resp) {
                    case 1 :
                        $STATUS = SUCCESS_STATUS;
                        $MESSAGE = BOOK_TABLE_CANCEL;
                        break;

                    case -2:
                        $MESSAGE = BOOK_TABLE_CANCEL_ERR;
                        break;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'totalPoint' => $this->general_model->getRedeemAmount($this->post('userId'))
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in cancelReservation_post function - ' . $ex);
        }
    }

    /**
     * Action to get restaurant slots
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $restaurantId <int>
     * @return void
     */
    function getRestaurantSlots_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'restaurantId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $SLOTVALUES = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //GET RESTAURANT SLOTS
                $resp = $this->restaurant_model->getRestaurantSlots($this->post('restaurantId'));
                if (!empty($resp)) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = SLOTS_FOUND;
                    $SLOTVALUES = $resp;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'SLOTVALUES' => $SLOTVALUES
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantSlots_post function - ' . $ex);
        }
    }

    /**
     * Action to fetch all user reviews for restaurants
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return array
     */
    function getUserReviews_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array('userId');
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $this->load->model("user_model", "user_model");
                $resp = $this->user_model->getUserReviews($this->post('userId'));
                if (!empty($resp)) {
                    $MESSAGE = REVIEW_FOUND_SUCC;
                    $STATUS = SUCCESS_STATUS;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'REVIEWDATA' => $resp,
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postReview_post function - ' . $ex);
        }
    }
//banquetEnquiry
    function banquetEnquiry_post() {

        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'restaurantId', 'userId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {

                //POST REVIEW
                $resp = $this->restaurant_model->postBanquetEnquiry($this->post());

                if ($resp == 1) {
                    $MESSAGE = 'Your enquiry has been recieved, will get in touch shortly.';
                    $STATUS = SUCCESS_STATUS;
                }
            }
            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in banquetEnquiry_post function - ' . $ex);
        }
    }

    /**
     * Action to post user review
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function postReview_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'restaurantId', 'userId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //POST REVIEW
                $resp = $this->restaurant_model->postReview($this->post());
                if ($resp == 1) {
                    $MESSAGE = 'Success';
                    $STATUS = SUCCESS_STATUS;
                }
            }
            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postReview_post function - ' . $ex);
        }
    }

    /**
     * Action to post review on the basis of different params
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post
     * @return void
     */
    function postWebReview_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'restaurantId', 'userId', 'ambience', 'price', 'food', 'service'
            );
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->postWebReview($this->post());
                if ($resp) {

                    if (!empty($_FILES['restaurentReviewImages']) && !empty($_FILES['restaurentReviewImages']['name'])) {
                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'reviewImages',
                                'id' => $resp,
                            ),
                            'requireThumb' => TRUE,
                        );

                        $this->load->library('fileupload', $param);
                        $this->fileupload->removeFile();
                        $uploadFiles = $this->fileupload->upload($_FILES, 'restaurentReviewImages');
                        $resp = $this->restaurant_model->postWebReviewImages($this->post(), $resp, $uploadFiles);
                    }

                    $MESSAGE = REVIEW_RECORD_SAVE;
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = 'Something wrong!';
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postReview_post function - ' . $ex);
        }
    }

    /**
     * Action to search restaurant
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $searchText <string> 
     * @return array
     */
    function searchRestaurant_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'searchText'
            );
            // TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $RESTAURANTLIST = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //SEARCH RESTAURANT
                $resp = $this->restaurant_model->searchRestaurant($this->post());
                if (!empty($resp)) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = RESTAURANT_FOUND;
                    $RESTAURANTLIST = $resp;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'RESTAURANTLIST' => $RESTAURANTLIST
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in searchRestaurant_post function - ' . $ex);
        }
    }

    /**
     * AUTO SEARCH
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $searchText <string> 
     * @return void
     */
    function autosearch_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'searchText'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $RESTAURANTLIST = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->searchRestaurantAutofill($this->post());
                if (!empty($resp)) {
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = RESTAURANT_FOUND;
                    $RESTAURANTLIST = $resp;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'RESTAURANTLIST' => $RESTAURANTLIST
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in searchRestaurant_post function - ' . $ex);
        }
    }

    /**
     * Action to get all restaurant list
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type get
     * @return array
     */
    function allrestaurant_get() {
        try {
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $RESTAURANTLIST = array();
            //GET RESTAURANT LIST
            $resp = $this->restaurant_model->allrestaurant();
            if (!empty($resp)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = RESTAURANT_FOUND;
                $RESTAURANTLIST = $resp;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'RESTAURANTLIST' => $RESTAURANTLIST
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in allrestaurant_get function - ' . $ex);
        }
    }

    /**
     * This will save the book mark information.
     * @throws Exception
     */

    /**
     * Action to add new outlet for the parent company in temp outlets.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function postBookmark_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId',
                'restaurantId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $BOOKMARK = 0;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //POST BOOKMARK
                $resp = $this->restaurant_model->postBookmark($this->post());
                if ($resp == 1 || $resp == 0) {
                    $MESSAGE = BOOKMARK_RECORD_SAVE;
                    $STATUS = SUCCESS_STATUS;
                    $BOOKMARK = $resp;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'bookmark' => $BOOKMARK
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postBookmark_post function - ' . $ex);
        }
    }

    /**
     * Action to post review
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function postWebPartialReview_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'restaurantId', 'userId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DATA = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //POST REVIEW
                $review = $this->restaurant_model->postWebReview($this->post());
                if ($review) {
                    if (isset($_FILES['images']) && !empty($_FILES['images'])) {
                        $_FILES['restaurentReviewImages'] = $_FILES['images'];
                    }

                    if (!empty($_FILES['restaurentReviewImages']) && !empty($_FILES['restaurentReviewImages']['name'])) {
                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'reviewImages',
                                'id' => $review,
                            ),
                            'requireThumb' => TRUE,
                        );

                        $this->load->library('fileupload', $param);
                        $this->fileupload->removeFile();
                        $uploadFiles = $this->fileupload->upload($_FILES, 'restaurentReviewImages');
                        $this->restaurant_model->postWebReviewImages($this->post(), $review, $uploadFiles);
                    }
                    $DATA = $this->restaurant_model->getReviewDetails($review);
                    if (($this->post('review')) && $this->post('review') != '') {
                        $MESSAGE = REVIEW_RECORD_SAVE;
                    } else {
                        $MESSAGE = RATING_RECORD_SAVE;
                    }
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = "Unable to post review. Please try again !";
                }
            }

            $resp = array(
                'DATA' => $DATA,
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postWebPartialReview_post function - ' . $ex);
        }
    }

    /**
     * Action to review as favourite
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post
     * @return status
     */
    function postReviewFavourite_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId',
                'restaurantId',
                'reviewId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //POST REVIEW AS FAVOURITE
                $resp = $this->restaurant_model->postReviewFavourite($this->post());
                if ($resp == 1) {
                    $MESSAGE = REVIEWED_FAVOURITE;
                    $STATUS = SUCCESS_STATUS;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postReviewFavourite_post function - ' . $ex);
        }
    }

    /**
     * Action to post comment on review
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post
     * @return void
     */
    function postReviewComment_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId',
                'reviewId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->postReviewComments($this->post());
                if ($resp == 1) {
                    $MESSAGE = REVIEWED_COMMENT;
                    $STATUS = SUCCESS_STATUS;
                }
            }
            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postReviewComment_post function - ' . $ex);
        }
    }

    /**
     * Action to save the restaurant closure request.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function postRestClosure_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId',
                'restaurantId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->postRestClosure($this->post());
                if ($resp == 1) {
                    $MESSAGE = REVIEWED_COMMENT;
                    $STATUS = SUCCESS_STATUS;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postRestClosure_post function - ' . $ex);
        }
    }

    /**
     * Action to save the edit request for restaturant information.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return status
     */
    function postReportError_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId',
                'restaurantId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->postReportError($this->post());
                if ($resp == 1) {
                    $MESSAGE = REMARKS_POSTED;
                    $STATUS = SUCCESS_STATUS;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postReportError_post function - ' . $ex);
        }
    }

    /**
     * Action to save the error in existing restaurant information.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return status
     */
    function postReportTypeError_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId',
                'restaurantId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->postReportTypeError($this->post());
                if ($resp == 1) {
                    $MESSAGE = ERROR_POSTED;
                    $STATUS = SUCCESS_STATUS;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postReportTypeError_post function - ' . $ex);
        }
    }

    /**
     * Action to get all home links for website
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function getHomeLinks_post() {
        try {
            $this->load->driver('cache');
            //MANDATORY PARAMETERS
            $allowParam = array(
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $QUICKLINK = $CATEGORIES = $HANDPICKS = $TRENDING = array();
            //$resp = $this->restaurant_model->getLinkDetails($this->post());
            $resp = array('homelinks');
            if (!empty($resp)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = RESTAURANT_FOUND;
                $DATA = $resp;
                $this->load->model("location_model", "location_model");
                $respLoc = $this->location_model->getZone();
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DATA' => $DATA
            );
            if (!empty($respLoc)) {
                $resp['ZONE'] = $respLoc;
            }
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getHomeLinks_post function - ' . $ex);
        }
    }

    /**
     * Action to get all categories
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post
     * @return array
     */
    function getAllCategories_post() {
        try {
            $this->load->driver('cache');
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //GET CATEGORIES
            $resp = $this->restaurant_model->getAllCategories();
            if (!empty($resp)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = '';
                $DATA = $resp;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DATA' => $DATA
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getHomeLinks_post function - ' . $ex);
        }
    }

    /**
     * Action to get restaurant time slots
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $restaurantID <int> 
     * @return void
     */
    function restaurantTimeSlots_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'restaurantID'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $TIMESLOT = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->getRestaurantTimeSlots($this->post('restaurantID'));
                if (!empty($resp)) {
                    $TIMESLOT = $resp;
                    $MESSAGE = 'Time slots found';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'TIMESLOT' => $TIMESLOT
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in restaurantTimeSlots_post function - ' . $ex);
        }
    }

    /**
     * Action to get all sponsored restaurants
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post
     * @return array
     */
    function sponseredRestaurant_post() {
        try {
            $STATUS = FAIL_STATUS;
            //GET RESTAURANTS
            $resp = $this->restaurant_model->sponseredRestaurant($this->post());
            if (!empty($resp)) {
                $DATA = $resp;
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = '';
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DATA' => $DATA
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in sponseredRestaurant_post function - ' . $ex);
        }
    }

    /**
     * Action to get restaurant combo offers
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post
     * @return void
     */
    function restaurantComboOffers_post() {
        
        try {
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DETAILS = array();

            $resp = $this->restaurant_model->getRestaurantComboOffers($this->post());
            if (!empty($resp)) {
                $DETAILS = $resp;
                $MESSAGE = 'Record found';
                if (count($resp) > 0) {
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }
            
            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'restaurants' => $DETAILS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in restaurantComboOffers_post function - ' . $ex);
        }
    }
    
    function restaurantDiscountOffers_post() {
        
        try {
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DETAILS = array();

            $resp = $this->restaurant_model->restaurantDiscountOffers($this->post());
            if (!empty($resp)) {
                $DETAILS = $resp;
                $MESSAGE = 'Record found';
                if (count($resp) > 0) {
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }
            
            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'restaurants' => $DETAILS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in restaurantComboOffers_post function - ' . $ex);
        }
    }

    /**
     * Action to get variants/subcombo for combos
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $comboOfferID <int>
     * @return void
     */
    function restaurantSubComboOffers_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'comboOfferID'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DETAILS = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->getRestaurantSubComboOffers($this->post());
                if (!empty($resp)) {
                    $DETAILS = $resp;
                    $MESSAGE = 'Record found';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DETAILS' => $DETAILS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in restaurantSubComboOffers_post function - ' . $ex);
        }
    }

    /**
     * Action to get subcombo detail
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post
     * @return void
     */
    function getSubComboOfferDetail_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'subComboId'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DETAILS = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //GET DETAIL
                $resp = $this->restaurant_model->getSubComboOfferDetail($this->post('subComboId'));
                if (!empty($resp)) {
                    $DETAILS = $resp;
                    $MESSAGE = 'Record found';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DETAILS' => $DETAILS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getSubComboOfferDetail_post function - ' . $ex);
        }
    }

    /**
     * Action to get cart detail of combo
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post
     * @return void
     */
    function comboCartDetail_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'iRestaurantID', 'iUserID', 'iComboSubOffersID', 'iComboOffersID',
                'tDiscountedPrice', 'qty', 'dtExpiryDate', 'vDaysAllow', 'iTotal', 'vUserName',
                'vMobileNo'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DETAILS = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->savecomboCartDetail($this->post());
                if (!empty($resp)) {
                    $DETAILS = $resp;
                    $MESSAGE = 'Details Updated SuccessFully';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DETAILS' => $DETAILS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in comboCartDetail_post function - ' . $ex);
        }
    }

    /**
     * Action to remove combo from cart
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type post
     * @return void
     */
    function removecomboCartDetail_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'iRestaurantID', 'iUserID', 'iComboSubOffersID', 'iComboOffersID'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DETAILS = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //REMOVE COMBO
                $resp = $this->restaurant_model->removecomboCartDetail($this->post());
                if (!empty($resp)) {
                    $DETAILS = $resp;
                    $MESSAGE = 'Details Updated SuccessFully';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DETAILS' => $DETAILS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in removecomboCartDetail_post function - ' . $ex);
        }
    }

    /**
     * Action to update combo cart
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function updateComboCart_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'iRestaurantID', 'iUserID', 'iComboSubOffersID', 'iComboOffersID', 'eBookingStatus'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DETAILS = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->updateComboCart($this->post());
                if (!empty($resp)) {
                    $DETAILS = $resp;
                    $MESSAGE = 'Details Updated SuccessFully';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DETAILS' => $DETAILS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in updateComboCart_post function - ' . $ex);
        }
    }

    //save combo payment
    function saveComboPaymentDetails_post() {
        try {
            //TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
            $allowParam = array(
//                'pointsUsed'      //(optional)
                'paymentId',
                'userId',
                'amount',
                'status', //success/failed/..
                'paymentGateway', //paytm/payu
                'rawData',
                'comboData'         //[{'subComboOfferId':"",'userComboId':"",'quantity':""},...]
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DETAILS = array();

            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->saveComboPaymentDetails($this->post());
                if (!empty($resp)) {
                    $DETAILS = $resp;
                    $MESSAGE = 'Details Updated SuccessFully';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DETAILS' => $DETAILS
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in savePaymentDetails_post function - ' . $ex);
        }
    }

    /**
     * Action to add new outlet for the parent company in temp outlets.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function savePaymentDetails_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'iUserID',
                'amount',
                'status',
                'item_id',
                'rawData'
            );
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DETAILS = array();
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $resp = $this->restaurant_model->savePaymentDetails($this->post());
                if (!empty($resp)) {
                    $DETAILS = $resp;
                    $MESSAGE = 'Details Updated SuccessFully';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DETAILS' => $DETAILS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in savePaymentDetails_post function - ' . $ex);
        }
    }

    /**
     * Action to add new outlet for the parent company in temp outlets.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function getRestaurantAlias_post() {
        try {
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DETAILS = array();
            $resp = $this->restaurant_model->getRestaurantAlias();
            if (!empty($resp)) {
                $ALIAS = $resp;
                $MESSAGE = '';
                $STATUS = SUCCESS_STATUS;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'ALIAS' => $ALIAS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantAlias_post function - ' . $ex);
        }
    }

    /*
     * Method to return json for all available filters on restaurant. 
     * 
     * @return json data with restaurant filter
     * @author Anurag Srivastava (anurag.srivastava@Foodine :.com)
     */

    /**
     * Action to add new outlet for the parent company in temp outlets.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function getRestaurantFilter_post() {
        try {
            $this->load->driver('cache');
            // Default value
            $DATA = array();
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            $resp = $this->restaurant_model->getAllRestaurantFilter($this->post());
            if (!empty($resp)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = '';
                $DATA = $resp;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            // setting the response
            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DATA' => $DATA
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            // If execution stop unexpectedly 
            $resp = array(
                'FILTERS' => $ex->getMessage(),
                'resultCode' => FAIL_STATUS,
                'DATA' => ''
            );
            //RESPONSE
            $this->response($resp, 200);
        }
    }

    /*
     * Method to return json for all locations from location master table. 
     * 
     * @return json data with location listing
     * @author Anurag Srivastava (anurag.srivastava@Foodine :.com)
     */

    /**
     * Action to add new outlet for the parent company in temp outlets.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function getLocations_post() {
        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $searchText = $this->post('searchText');
            if (!isset($searchText) || empty($searchText))
                $searchText = '';
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            $resp = $this->restaurant_model->getAllLocations($this->post());
            if (!empty($resp)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = '';
                $DATA = $resp;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DATA' => $DATA
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            // If execution stop unexpectedly 
            $resp = array(
                'result' => $ex->getMessage(),
                'resultCode' => FAIL_STATUS,
                'DATA' => ''
            );
            //RESPONSE
            $this->response($resp, 200);
        }
    }

    /**
     * Action to add new outlet for the parent company in temp outlets.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function getRestaurantByLocation_post() {
        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $locationID = $this->post('locationID');
            if (!isset($locationID) || empty($locationID))
                $locationID = '';
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            $resp = $this->restaurant_model->getRestaurantByLocation($locationID);
            if (!empty($resp)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = '';
                $DATA = $resp;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }
            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DATA' => $DATA
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            // If execution stop unexpectedly 
            $resp = array(
                'result' => $ex->getMessage(),
                'resultCode' => FAIL_STATUS,
                'DATA' => ''
            );
            //RESPONSE
            $this->response($resp, 200);
        }
    }

    /**
     * Action to add new outlet for the parent company in temp outlets.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function updatePrice_get() {
        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $RESTAURANTLIST = array();
            $resp = $this->restaurant_model->updatePrice();
            if (!empty($resp)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = RESTAURANT_FOUND;
                $RESTAURANTLIST = $resp;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'RESTAURANTLIST' => $RESTAURANTLIST
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in allrestaurant_get function - ' . $ex);
        }
    }

    /**
     * Action to add new outlet for the parent company in temp outlets.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    public function addPartner_post() {
        try {
//MANDATORY PARAMETERS
            $allowParam = array('vDaysOpen', 'iMinTime', 'iMaxTime', 'vRestaurantName', 'vParentCompanyName', 'tAddress', 'vContactNo', 'vheadManagerName', 'vheadManagerEmail', 'vheadManagerPhone', 'vPrimManagerRole', 'tWaitStaff', 'tNoManagers');
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $DETAILS = array();

            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $data = $this->post();

                $restaurentId = $this->restaurant_model->addPartner($data);

                $images = array();
                if ($restaurentId) {

                    if (!empty($_FILES['bannerImage']) && !empty($_FILES['bannerImage']['name'])) {
                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'restaurant',
                                'id' => $restaurentId
                            ),
                            'requireThumb' => TRUE
                        );

                        $this->load->library('fileupload', $param);
                        $this->fileupload->removeFile();
                        $uploadFiles = $this->fileupload->upload($_FILES, 'bannerImage');
                        $images['bannerImage'] = $uploadFiles[0];
                    }

                    if (!empty($_FILES['eventImage']) && !empty($_FILES['eventImage']['name'])) {
                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'event',
                                'id' => $restaurentId
                            ),
                            'requireThumb' => TRUE
                        );
                        $this->load->library('fileupload', $param);
                        $this->fileupload->setConfig($param);
                        $this->fileupload->removeFile();
                        $uploadFiles = $this->fileupload->upload($_FILES, 'eventImage');
                        $images['eventImage'] = $uploadFiles[0];
                    }
                    if (!empty($_FILES['restaurentListImages'])) {
                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'restaurantPhoto',
                                'id' => $restaurentId
                            ),
                            'requireThumb' => TRUE
                        );
                        $this->load->library('fileupload', $param);
                        $this->fileupload->setConfig($param);
                        $this->fileupload->removeFile();
                        $uploadFiles = $this->fileupload->upload($_FILES, 'restaurentListImages');
                        $images['restaurentListImages'] = $uploadFiles;
                    }
                    if (!empty($_FILES['barImages'])) {
                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'restaurantMenu',
                                'id' => $restaurentId
                            ),
                            'requireThumb' => TRUE
                        );
                        $this->load->library('fileupload', $param);
                        $this->fileupload->setConfig($param);
                        $this->fileupload->removeFile();
                        $uploadFiles = $this->fileupload->upload($_FILES, 'barImages');
                        $images['barImages'] = $uploadFiles;
                    } if (!empty($_FILES['foodImages'])) {
                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'restaurantMenu',
                                'id' => $restaurentId
                            ),
                            'requireThumb' => TRUE
                        );
                        $this->load->library('fileupload', $param);
                        $this->fileupload->setConfig($param);
                        if (empty($_FILES['barImages'])) {
                            $this->fileupload->removeFile();
                        }
                        $uploadFiles = $this->fileupload->upload($_FILES, 'foodImages');
                        $images['foodImages'] = $uploadFiles;
                    } if ($images) {
                        $this->restaurant_model->addPartnerImages($images, $restaurentId);
                    }

                    $this->load->model("smtpmail_model", "smtpmail_model");
//                    $this->load->library('maillib');

                    $param = array(
                        '%MAILSUBJECT%' => 'Foodine : Welcome',
                        '%LOGO_IMAGE%' => BASEURL . 'images/hungermafia.png',
                        '%RESTAURANT_NAME%' => $this->input->post('vRestaurantName'),
//                        '%CMS_LOGIN_LINK%' => BASEURL . 'login',
//                        '%USERNAME%' => $this->input->post('vheadManagerEmail'),
//                        '%PASSWORD%' => $this->input->post('vPassword')
                    );
                    $tmplt = DIR_VIEW . 'email/new_restaurant.php';
                    $subject = 'Foodine : Welcome';
                    $to = $this->input->post('vheadManagerEmail');
//                    $this->maillib->sendMail($to, $subject, $tmplt, $param);
                    $data = $this->smtpmail_model->send($to, $subject, $tmplt, $param);
                    $DETAILS = $restaurentId;
//                    $this->response($data, 200);
                    $MESSAGE = 'Details Updated SuccessFully';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = NO_RECORD_FOUND;
                }
            }


            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DETAILS' => $DETAILS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in addPartner_post function - ' . $ex);
        }
    }

    /**
     * Action to add new outlet for the parent company in temp outlets.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    public function getLocation_post() {

        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            $resp = $this->restaurant_model->getLocations();
            if (!empty($resp)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = 'Locations fetched.';
                $DATA = $resp;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'DATA' => $DATA
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            // If execution stop unexpectedly 
            $resp = array(
                'result' => $ex->getMessage(),
                'resultCode' => FAIL_STATUS,
                'DATA' => ''
            );
            //RESPONSE
            $this->response($resp, 200);
        }
    }

    /**
     * Action to add new outlet for the parent company in temp outlets.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function deleteReview_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array('userId', 'reviewId');

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {

                $resp = $this->restaurant_model->deleteReview($this->post());
                if ($resp) {
                    $MESSAGE = REVIEW_RECORD_DELETE;
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = 'Something wrong!';
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postWebPartialReview_post function - ' . $ex);
        }
    }

    /**
     * Action to add new outlet for the parent company in temp outlets.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function verifyPasscode_post() {
        //MANDATORY PARAMETERS
        $allowParam = array('userId', 'restaurantId', 'comboSubOfferId', 'passcode');
        $MESSAGE = INSUFF_DATA;
        $STATUS = FAIL_STATUS;
        //CHECK MANDATORY PARAMETERS
        if (checkselectedparams($this->post(), $allowParam)) {
            $status = $this->restaurant_model->verifyPasscode($this->post());
            if ($status == true) {
                $MESSAGE = 'Successfully availed.';
                $STATUS = SUCCESS_STATUS;
            } else {
                $MESSAGE = 'Invalid Code. Please try again!';
            }
        }
        $resp = array(
            'result' => $MESSAGE,
            'resultCode' => $STATUS
        );
        //RESPONSE
        $this->response($resp, 200);
    }

    /**
     * Action to add new outlet for the parent company in temp outlets.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    public function getBanner_post() {
        //echo 'hello'; exit;

        try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            $resp = $this->restaurant_model->getBanner($this->post('searchType'), $this->post());
            if (!empty($resp)) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = 'Banner found successfully.';
                $DATA = $resp;
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'eventList' => $resp
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            // If execution stop unexpectedly 
            $resp = array(
                'result' => $ex->getMessage(),
                'resultCode' => FAIL_STATUS,
                'DATA' => ''
            );
            //RESPONSE
            $this->response($resp, 200);
        }
    }
    
    public function getRecommended_post()  {
            try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            $resp['data']=  $this->restaurant_model->getRecommended($this->post());
            $userId = $this->post('userId');
                $this->load->model('user_model');
                if($userId) {
                   // echo 'hi'; exit;
                  //  $userId = $this->post('userId');
                    $sort = $this->user_model->getCIMUserArray('sort', @$userId);
                    $price_sort = $sort[0]['selected'];
                    $rating_sort = $sort[1]['selected'];
                   //print_r($price_sort."--".$rating_sort); exit;
                }
              //  echo $rateorder; exit;
                if ($rating_sort) {
                  

                    usort($resp['data'], function($postA, $postB) {
                        
                          if (!isset($rateorder) || empty($rateorder)) {
                       // echo 'here'; exit;
                        $rateorder = 'asc';
                    }

                    $orderBy = $rateorder;
                        if ($postA["rating"] == $postB["rating"]) {
                            return 0;
                        }
                       // echo $orderBy; exit;
                        if ($orderBy == 'ASC' || $orderBy == 'asc') {
                            return ($postA["rating"] < $postB["rating"]) ? 1 : -1;
                        } else {
                          // echo 'hello'; exit;
                            return ($postA["rating"] > $postB["rating"]) ? 1 : -1;
                        }
                    });
                }
            
            if (!empty($resp['data'])) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = 'Recommended';
                $DATA = $resp['data'];
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'restaurants' => $resp['data']
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            // If execution stop unexpectedly 
            $resp = array(
                'result' => $ex->getMessage(),
                'resultCode' => FAIL_STATUS,
                'DATA' => ''
            );
            //RESPONSE
            $this->response($resp, 200);
        }
        
    }


    public function getBanquet_post()  {
            try {
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            $resp['data']=  $this->restaurant_model->getBanquet($this->post());
            $userId = $this->post('userId');
                $this->load->model('user_model');
                if($userId) {
                   // echo 'hi'; exit;
                  //  $userId = $this->post('userId');
                    $sort = $this->user_model->getCIMUserArray('sort', @$userId);
                    $price_sort = $sort[0]['selected'];
                    $rating_sort = $sort[1]['selected'];
                   //print_r($price_sort."--".$rating_sort); exit;
                }
              //  echo $rateorder; exit;
                if ($rating_sort) {
                  

                    usort($resp['data'], function($postA, $postB) {
                        
                          if (!isset($rateorder) || empty($rateorder)) {
                       // echo 'here'; exit;
                        $rateorder = 'asc';
                    }

                    $orderBy = $rateorder;
                        if ($postA["rating"] == $postB["rating"]) {
                            return 0;
                        }
                       // echo $orderBy; exit;
                        if ($orderBy == 'ASC' || $orderBy == 'asc') {
                            return ($postA["rating"] < $postB["rating"]) ? 1 : -1;
                        } else {
                          // echo 'hello'; exit;
                            return ($postA["rating"] > $postB["rating"]) ? 1 : -1;
                        }
                    });
                }
            
            if (!empty($resp['data'])) {
                $STATUS = SUCCESS_STATUS;
                $MESSAGE = 'Banquet';
                $DATA = $resp['data'];
            } else {
                $MESSAGE = NO_RECORD_FOUND;
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'restaurants' => $resp['data']
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            // If execution stop unexpectedly 
            $resp = array(
                'result' => $ex->getMessage(),
                'resultCode' => FAIL_STATUS,
                'DATA' => ''
            );
            //RESPONSE
            $this->response($resp, 200);
        }
        
    }




    
    /**
     * Action to add new outlet for the parent company in temp outlets.
     * @access  Public
     * @package Restaurant Controller
     * @author 3031@Foodine :
     * @param type $parentCompanyId <int> Parent Company Id
     * @return void
     */
    function suggestRestaurant_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId',
                'restaurantName',
                'address',
                'city',
                'state'
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $BOOKMARK = 0;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //POST BOOKMARK
                $resp = $this->restaurant_model->suggestRestaurant($this->post());
                if ($resp == 1 || $resp == 0) {
                    $MESSAGE = 'Thanks for your suggestion.';
                    $STATUS = SUCCESS_STATUS;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postBookmark_post function - ' . $ex);
        }
    }
    
    function getRestaurantTableList_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array(
                'userId',
                'restaurantId',
                'dateTime',
            );
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $BOOKMARK = 0;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                //POST BOOKMARK
                $resp_arr = $this->restaurant_model->getRestaurantTableList($this->post());
                if ($resp_arr['layoutUrl']) {
                    $MESSAGE = 'Success';
                    $STATUS = SUCCESS_STATUS;
                } else {
                      $MESSAGE = NO_RECORD_FOUND;
                      $STATUS = FAIL_STATUS;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'layoutUrl' => $resp_arr['layoutUrl'],
                'bookedTables' => $resp_arr['bookedTables'],
                'availableTables' => $resp_arr['availableTables'],
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postBookmark_post function - ' . $ex);
        }
    }

      function reviews_post() {
        try {
            //MANDATORY PARAMETERS
            $allowParam = array('restaurantId');
            //TO SET DEFAULT VARIABLE VALUES...
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            //CHECK MANDATORY PARAMETERS
            if (checkselectedparams($this->post(), $allowParam)) {
                $this->load->model("restaurant_model");
                $resp = $this->restaurant_model->getRestaurantReviews($this->post('restaurantId'));
                if (!empty($resp)) {
                    $MESSAGE = REVIEW_FOUND_SUCC;
                    $STATUS = SUCCESS_STATUS;
                }
            }

            $resp = array(
                'result' => $MESSAGE,
                'resultCode' => $STATUS,
                'reviews' => $resp,
            );
            //RESPONSE
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postReview_post function - ' . $ex);
        }
    }

}
