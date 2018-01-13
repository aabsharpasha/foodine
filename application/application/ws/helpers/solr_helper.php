<?php

/**
 * Created by PhpStorm.
 * Project: hm_live
 * User: Amit Malakar
 * Date: 2/11/15
 * Time: 12:21 PM
 */
//include_once 'C:\xampp\htdocs\platform\vendor\autoload.php';
use Solarium\Client as Client;

function getSolrClient() {
    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);
    $config = array(
        'endpoint' => array(
            'localhost' => array(
                'host' => 'localhost', //192.168.12.239
                'port' => 8983,
                'path' => '/solr',
                'core' => 'restaurants',
            ),
        ),
    );
    $client = new Client($config);
    $client->setAdapter('Solarium\Core\Client\Adapter\Http');

    return $client;
}

function getSolrQuery($params) {
    $client = getSolrClient();
    $query = $client->createSelect();
    // $qryString = '(';
    $qryString = '*:*';
    $fl = '*';

    if (isset($params['search']) && !empty($params['search'])) {
        $params['search'] = trim($params['search']);
        $searchstr = str_replace(" ", "*", $params['search']);
        $qryString = '(';
        $qryString .= 'name:*' . $searchstr . '*';
        $qryString .= ' OR cat:*' . $params['search'] . '*';
        $qryString .= ' OR cui:*' . $params['search'] . '*';
        $qryString .= ' OR add:*' . $params['search'] . '*';
        $qryString .= ')';
    }
    if (isset($params['cat'])) {
        $categories = implode('","', $params['cat']);
        $qryString .= ' AND cat:("' . $categories . '")';
    }

    if (isset($params['qb'])) {
        $qb = implode('","', $params['qb']);
        $qryString .= ' AND cat:("' . $qb . '")';
    }

    if (isset($params['cui'])) {
        $cuisines = implode('","', $params['cui']);
        $qryString .= ' AND cui:("' . $cuisines . '")';
    }

    if (isset($params['fcl'])) {
        $facilities = implode('","', $params['fcl']);
        $qryString .= ' AND fcl:("' . $facilities . '")';
    }

    if (isset($params['ofr'])) {
        $facilities = implode('","', $params['ofr']);
        $qryString .= ' AND ofr:("' . $facilities . '")';
    }

    if (isset($params['bkng'])) {
        $bkng = implode('","', $params['bkng']);
        $qryString .= ' AND bkng:' . $bkng;
    }

    if (isset($params['dlvry'])) {
        $dlvry = implode('","', $params['dlvry']);
        $qryString .= ' AND dlvry:' . $dlvry;
    }

    if (isset($params['minprice']) && isset($params['maxprice'])) {
        $qryString .= ' AND cf2:[' . $params['minprice'] . ' TO ' . $params['maxprice'] . ']';
    }
    if (isset($params['zoneId']) && $params['zoneId'] != '') {
        $zoneId = $params['zoneId'];
        if ($zoneId == 9) {
            //$qryString .= ' AND zoneId:(1 OR 2 OR 3 OR 4 OR 5)';
        } else {
            $qryString .= ' AND zoneId:' . $zoneId;
        }
    }
    $helper = $query->getHelper();

    if (isset($params['lat']) && isset($params['lng'])) {

        $latitude = $params['lat'];
        $longitude = $params['lng'];
        $distance = 100;
        if (isset($params['distance'])) {
            $distance = $params['distance'];
        }

        $query->createFilterQuery('distance')->setQuery(
                $helper->geofilt(
                        'geo', doubleval($latitude), doubleval($longitude), doubleval($distance)
                )
        );

        $query->setQuery('{!func}' . $helper->geodist(
                        'geo', doubleval($latitude), doubleval($longitude)
        ));

        $query->addField('_distance_:' . $helper->geodist(
                        'geo', doubleval($latitude), doubleval($longitude)
                )
        );
    }

    /* PAGINATION */
    $zeroPageRecord = 10;
    $otherPageRecord = 10;
    if (!isset($params['pageId'])) {
        $params['pageId'] = 0;
    }
    if ($params['pageId'] == 0) {
        /* first page load records... */
        $perPageValue = $zeroPageRecord;
    } else {
        /* second to other page records... */
        $perPageValue = $otherPageRecord;
    }
    $pageId = (int) $params['pageId'];
    if ($pageId == 1) {
        $pageId = $zeroPageRecord * $pageId;
    } else if ($pageId > 1) {
        $pageId = (($zeroPageRecord) + ($perPageValue * ($pageId - 1)));
    }

    $qryString = '(' . $qryString . ')';
    $query->setQuery($qryString);
    $query->setStart($pageId);
    $query->setRows($perPageValue);
    if (isset($params['sort'])) {
        $query->addSort($params['sort'], 'asc');
    } else {
        $query->addSort('score', 'asc');
    }

    $resultset = $client->select($query);

    return $resultset;
}

/* Get Restaurant LIst Solr :: APP */

function getSolrRestaurantList($params) {
    $client = getSolrClient();
    $query = $client->createSelect();
    $qryString = '*:*';
    $fl = '*';
    $helper = $query->getHelper();
    $qryString .= ' AND cat:*';

    if (isset($params['search']) && !empty($params['search'])) {
        $params['search'] = trim($params['search']);
        $searchstr = str_replace(" ", "*", $params['search']);
        $qryString = '(';
        $qryString .= 'name:*' . $searchstr . '*';
        $qryString .= ' OR cat:*' . $params['search'] . '*';
        $qryString .= ' OR cui:*' . $params['search'] . '*';
        $qryString .= ' OR add:*' . $params['search'] . '*';
        $qryString .= ' OR add2:*' . $params['search'] . '*';
        $qryString .= ' OR loc:*' . $params['search'] . '*';
        $qryString .= ')';
    }

    if (isset($params['userLat'] ) && !empty($params['userLat']) && isset($params['userLong']) && !empty($params['userLong'])) {
        $latitude = $params['userLat'];
        $longitude = $params['userLong'];
        $distance = 100*1.60934;//100 miles
        if (isset($params['distance'])) {
            $distance = $params['distance'];
        }
        $query->createFilterQuery('distance')->setQuery(
                $helper->geofilt(
                        'geo', doubleval($latitude), doubleval($longitude), doubleval($distance)
                )
        );

        $query->setQuery('{!func}' . $helper->geodist(
                        'geo', doubleval($latitude), doubleval($longitude)
        ));

        $query->addField('distance:' . $helper->geodist(
                        'geo', doubleval($latitude), doubleval($longitude)
                )
        );
    }

    if (isset($params['currentLat']) && !empty($params['currentLat']) && isset($params['currentLong']) && !empty($params['currentLong'])) {
        $clatitude = $params['currentLat'];
        $clongitude = $params['currentLong'];
        $cdistance = 100000;//100 miles
        if (isset($params['distance'])) {
            $distance = $params['distance'];
        }

        $query->createFilterQuery('currentdistance')->setQuery(
                $helper->geofilt(
                        'geo', doubleval($clatitude), doubleval($clongitude), doubleval($cdistance)
                )
        );

        $query->setQuery('{!func}' . $helper->geodist(
                        'geo', doubleval($clatitude), doubleval($clongitude)
        ));

        $query->addField('currentdistance:' . $helper->geodist(
                        'geo', doubleval($clatitude), doubleval($clongitude)
                )
        );
    }


    $sarchTypeArr = array('feature', 'nearby', 'collection', 'handpicks');

    //START : SORTING
    $orderByField = 'score';
    $orderBy = 'ASC';
    if (isset($params['filterSortBy']) && in_array($params['searchType'], $sarchTypeArr)) {
        /*
         * SET SORT BY VALUE
         */
        // If order is not defined
        if (!isset($params['rateorder']) || empty($params['rateorder']))
            $params['rateorder'] = 'DESC';

        if (!isset($params['costorder']) || empty($params['costorder']))
            $params['costorder'] = 'ASC';

        switch ($params['filterSortBy']) {
            case 'rate':
                $orderByField = 'rtn';
                $orderBy = $params['rateorder'];
                break;
            case 'cost':
                $orderByField = 'cf2';
                $orderBy = $params['costorder'];
                break;
            case 'pop':
                $orderByField = 'rtn';
                $orderBy = 'DESC';
                break;
            case 'date':
                $orderByField = 'dt';
                $orderBy = 'DESC';
                break;
            case 'name':
                $orderByField = 'name';
                $orderBy = 'ASC';
                break;
            case 'dist':
                $orderByField = $helper->geodist('geo', doubleval($latitude), doubleval($longitude));
                $orderBy = 'ASC';
                break;
            default :
                $orderByField = $helper->geodist('geo', doubleval($latitude), doubleval($longitude));
                $orderBy = 'ASC';
                break;
        }
    } else if ($params['searchType'] == '' || $params['searchType'] == 'checkin' || $params['searchType'] == 'nearby' || $params['searchType'] == 'collection' || $params['searchType'] == 'feature') {
        //Default Sorting
        $orderByField = $helper->geodist('geo', doubleval($latitude), doubleval($longitude));
        $orderBy = 'ASC';
    }
    if ($params['searchType'] != 'recentviewed') {
        $query->addSort($orderByField, $orderBy);
    }
    //END : SORTING
    //START : FILTER
    // Adding Service Provided filter
    if (isset($params['serviceProvided']) && $params['serviceProvided'] !== '') {
        if ($params['serviceProvided'] == 'both') {
            $qryString = '(';
            $qryString .= 'dlvry:1';
            $qryString .= ' OR bkng:1';
            $qryString .= ')';
        } else if ($params['serviceProvided'] == 'booking') {
            $qryString .= ' AND bkng:1';
        } else if ($params['serviceProvided'] == 'order') {
            $qryString .= ' AND dlvry:1';
        }
    }

    if (isset($params['zoneId']) && $params['zoneId'] != '') {
        $zoneId = $params['zoneId'];
        if ($zoneId == 10) {
            $zoneId = '1,2,3,4,5,6,7,8,9';
        } else if ($zoneId == 9) {
            $zoneId = '1,2,3,4,5,9';
        } else {
            $zoneId = $zoneId;
        }
        $zoneId = str_replace(',', ' OR ', $zoneId);
        $qryString .= ' AND zoneId: (' . $zoneId.')' ;
    }
    // Adding price filter
    if (isset($params['minPrice']) && !empty($params['minPrice']) && isset($params['maxPrice']) && !empty($params['maxPrice'])) {
        $qryString .= ' AND cf2:[' . $params['minPrice'] . ' TO ' . $params['maxPrice'] . ']';
    } else if (!empty($params['minPrice']) && empty($params['maxPrice'])) {
        $qryString .= ' AND cf2:[' . $params['minPrice'] . ' TO *]';
    } else if (!empty($params['maxPrice']) && empty($params['minPrice'])) {
        $qryString .= ' AND cf2:[* TO ' . $params['maxPrice'] . ']';
    }

    if (in_array($params['searchType'], $sarchTypeArr)) {
        // Adding Cuisine filter
        if (isset($params['filterCuisine']) && $params['filterCuisine'] !== '') {
            $cuisine = str_replace(',', '","', $params['filterCuisine']);
            $qryString .= ' AND cuiId:("' . $cuisine . '")';
        }
        // Adding Offer filter
        if (isset($params['offertype']) && !empty($params['offertype'])) {
            $offrtype = str_replace(',', '","', $params['offertype']);
            $qryString .= ' AND ofrTypId:("' . $offrtype . '")';
        }
        // Adding Establishment filter
        if (isset($params['optionEstablishment']) && !empty($params['optionEstablishment'])) {
            //CHECK USERID CONDITION//
            $estblishment = str_replace(',', '","', $params['optionEstablishment']);
            $qryString .= ' AND catId:("' . $estblishment . '")';
        }
        // Adding Establishment filter
        if (isset($params['optionCategory']) && !empty($params['optionCategory'])) {
            //CHECK USERID CONDITION//
            $category = str_replace(',', '","', $params['optionCategory']);
            $qryString .= ' AND catId:("' . $category . '")';
        }
        // Adding Faciltiy filter
        if (isset($params['optionFacilities']) && !empty($params['optionFacilities'])) {
            //CHECK USERID CONDITION//
            $facilities = str_replace(',', '","', $params['optionFacilities']);
            $qryString .= ' AND fclId:("' . $facilities . '")';
        }
        //Adding Location Filter
        if (isset($params['filterLocation']) && $params['filterLocation'] !== '') {
            $locId = str_replace(',', ' OR ', $params['filterLocation']);
            $qryString .= ' AND locId:(' . $locId . ')';
        }
    }
    //END : FILTER
    switch ($params['searchType']) {
        case 'feature' :
            $qryString .= ' AND featrd:yes';
            break;
        case 'collection':
            if (isset($params['selectedCategory']) && !empty($params['selectedCategory'])) {
                $qryString .= ' AND catId:("' . $params['selectedCategory'] . '")';
            }

            if (isset($params['selectedCuisine']) && $params['selectedCuisine'] !== '') {
                $qryString .= ' AND cuiId:("' . $params['selectedCuisine'] . '")';
            }
            break;
        case 'recentviewed':

            $countId = count($params['restIds']);
            if (isset($params['restIds']) && $params['restIds'] !== '' && !empty($params['restIds'])) {
                $qryString .= ' AND id:(';
                foreach ($params['restIds'] as $k => $restId) {
                    if ($k > 0) {
                        $qryString .= ' OR ';
                    }
                    $qryString .= $restId .'^'. ($countId-$k);
                }
                $qryString .= ')';
            }
            break;
        case 'handpicks':
        case 'favourite':
            if (isset($params['restIds']) && $params['restIds'] !== '' && !empty($params['restIds'])) {
                $restId = implode(' OR ', $params['restIds']);
                $qryString .= ' AND id:(' . $restId . ')';
            }
            break;
    }
    /* PAGINATION */
    $zeroPageRecord = 9;
    $otherPageRecord = 10;
    if (isset($params['platform']) && $params['platform'] == 'web') {
        $zeroPageRecord = $otherPageRecord = 15;
    }
    if ($params['pageId'] == 0) {
        /* first page load records... */
        $perPageValue = $zeroPageRecord;
    } else {
        /* second to other page records... */
        $perPageValue = $otherPageRecord;
    }
    $pageId = (int) $params['pageId'];
    if ($pageId == 1) {
        $pageId = $zeroPageRecord * $pageId;
    } else if ($pageId > 1) {
        $pageId = (($zeroPageRecord) + ($perPageValue * ($pageId - 1)));
    }
    $qryString = '(' . $qryString . ')';
    $query->setQuery($qryString);
    $query->setStart($pageId);
    $query->setRows($perPageValue);

    $resultset = $client->select($query);

    return $resultset;
}

/**
 * Index Solr documents
 * @param $restaurants
 * @return \Solarium\QueryType\Update\Result
 * @throws Exception
 */
function addSolrDocs($restaurants) {
    try {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        // create a client instance
        $client = getSolrClient();

        $update = $client->createUpdate();
        $docs = array();
        foreach ($restaurants as $rest) {
            $newDoc = $update->createDocument();
            $newDoc->id = $rest['id'];
            $newDoc->name = $rest['name'];
            $newDoc->featrd = $rest['featrd'];
            $newDoc->cf2 = $rest['cf2'];
            $newDoc->loc = $rest['loc'];
            $newDoc->locId = $rest['locId'];
            $newDoc->city = $rest['city'];
            $newDoc->zoneId = $rest['zoneId'];
            $newDoc->state = $rest['state'];
            $newDoc->add = $rest['add'];
            $newDoc->add2 = $rest['add2'];
            $newDoc->rtn = $rest['rtn'];
            $newDoc->totRevw = $rest['totRevw'];
            $newDoc->dt = $rest['dt'];
            $newDoc->geo = $rest['geo'];
            $newDoc->ri = $rest['ri'];
            $newDoc->rti = $rest['rti'];
            $newDoc->oct = $rest['oct'];
            $newDoc->ts = $rest['ts'];
            $newDoc->bkng = $rest['bkng'];
            $newDoc->dlvry = $rest['dlvry'];
            // multivalued fields >>>
            $newDoc->rui = $rest['rui'];
            $newDoc->ruti = $rest['ruti'];
            $newDoc->rmi = $rest['rmi'];
            $newDoc->rmti = $rest['rmti'];
            $newDoc->cui = $rest['cui'];
            $newDoc->cuiId = $rest['cuiId'];
            $newDoc->cat = $rest['cat'];
            $newDoc->catId = $rest['catId'];
            $newDoc->fcl = $rest['fcl'];
            $newDoc->fclId = $rest['fclId'];
            $newDoc->ofr = $rest['ofr'];
            $newDoc->ofrTypId = $rest['ofrTypId'];
            // <<< multivalued fields

            $docs[$rest['id']] = $newDoc;
            // update database set iSolrValue
            $CI = & get_instance();
            $CI->load->model('restaurant_model', 'restaurant_model');
            $CI->restaurant_model->setSolrFlag($rest['id'], 1); // 1 = updated solr
        }
        // add the documents and a commit command to the update query
        if (!empty($docs)) {
            $update->addDocuments($docs);
            $update->addCommit();
            $result = $client->update($update);

            return $result;
        }
        //echo '<b>Update query executed</b><br/>';
        //echo 'Query status: ' . $result->getStatus(). '<br/>';
        //echo 'Query time: ' . $result->getQueryTime();
    } catch (Exception $ex) {
        throw new Exception('Error in addSolrDocs function - ' . $ex);
    }
}

/**
 * Delete Solr documents by id
 * @param $restaurants id array
 * @return \Solarium\QueryType\Update\Result
 * @throws Exception
 */
function deleteSolrDocs($restaurantIds) {
    try {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $client = getSolrClient();
        $update = $client->createUpdate();
        foreach ($restaurantIds as $restId) {
            $update->addDeleteById($restId);
            $CI = & get_instance();
            $CI->load->model('restaurant_model', 'restaurant_model');
            $CI->restaurant_model->setSolrFlag($restId, 4); // 1 = updated solr
        }
        $update->addCommit();
        // this executes the query and returns the result
        $result = $client->update($update);
        return $result;
    } catch (Exception $ex) {
        throw new Exception('Error in deleteSolrDocs function - ' . $ex);
    }
}

// ================================================================================================================


function addSolrDocument($quotes) {
    try {
        // create a client instance
        $client = getSolrClient();

        $update = $client->createUpdate();
        $docs = array();
        foreach ($quotes as $quote) {
            $newDoc = $update->createDocument();
            $newDoc->id = 'quote_' . $quote->quote_id;
            $newDoc->entity_type = 'quote';

            $newDoc->origin_zip = $quote->zip_code_from;
            $newDoc->origin_state = $quote->state_from;
            $newDoc->origin_city = $quote->city_from;
            if ($quote->origin_lat != '') {
                $newDoc->origin_latlan = $quote->origin_lat . ',' . $quote->origin_lng;
            }
            $newDoc->origin_email = $quote->origin_email;
            $newDoc->origin_business_name = $quote->origin_business_name;

            $newDoc->destination_zip = $quote->zip_code_to;
            $newDoc->destination_state = $quote->state_to;
            $newDoc->destination_city = $quote->city_to;
            if ($quote->des_lat != '') {
                $newDoc->destination_latlan = $quote->des_lat . ',' . $quote->des_lng;
            }
            $newDoc->destination_email = $quote->des_email;
            $newDoc->destination_business_name = $quote->des_business_name;

            $newDoc->pickup_date = gmdate('Y-m-d\TH:i:s\Z', strtotime($quote->readyfor_pcikup_date));
            //        $newDoc->created_date = gmdate('Y-m-d\TH:i:s\Z', strtotime($quote->created_at));
            $newDoc->price = $quote->expected_cost;
            $newDoc->description = $quote->special_instruction;
            $newDoc->truck_load_type = $quote->truck_load_type;
            $newDoc->load_id = $quote->Load_id;
            $newDoc->quote_confirm = $quote->quote_confirm;
            $newDoc->quote_status = ($quote->quote_status) ? $quote->quote_status : 'null';
            $newDoc->trailer_type = $quote->trailer_type;
            //        $newDoc->trailer_type_options = $doc->trailer_type;

            $docs[$quote->quote_id] = $newDoc;
        }
        // add the documents and a commit command to the update query
        if (!empty($docs)) {
            $update->addDocuments($docs);
            $update->addCommit(1);
            $result = $client->update($update);
        }
        //echo '<b>Update query executed</b><br/>';
        //echo 'Query status: ' . $result->getStatus(). '<br/>';
        //echo 'Query time: ' . $result->getQueryTime();
    } catch (Exception $ex) {
        
    }
}

function deleteQuoteSolrDocument($quotes) {
    try {
        $client = getSolrClient();
        $update = $client->createUpdate();

        foreach ($quotes as $quote) {
            $update->addDeleteById('quote_' . $quote->quote_id);
        }
        $update->addCommit();
        // this executes the query and returns the result
        $result = $client->update($update);
    } catch (Exception $ex) {
        
    }
}

function getSolrDocument($lat, $lan, $distance = 100, $results = 20) {
    $client = getSolrClient();
    $query = $client->createSelect();
    $helper = $query->getHelper();

    $query->setRows(20);
    $query->addSort('score', 'asc');

    //convert to miles
    $distance1 = $distance * 1.604;
    $query->createFilterQuery('distance')->setQuery(
            $helper->geofilt(
                    'origin_latlan', doubleval($lat), doubleval($lan), doubleval($distance1)
            )
    );

    $query->setQuery('{!func}' . $helper->geodist(
                    'origin_latlan', doubleval($lat), doubleval($lan)
    ));

    $query->addField('_distance_:' . $helper->geodist(
                    'origin_latlan', doubleval($lat), doubleval($lan)
            )
    );

    $resultset = $client->select($query);
    print_r($resultset);
}

function addAllQuotesSolr() {
    $CI = &get_instance();
    $CI->load->model('cc_models/cc_quote_m');
    $quotes = $CI->cc_quote_m->getAllQuotes();
    addQuoteSolrDocument($quotes);
}

function deleteAllQuotesSolr() {
    $CI = &get_instance();
    $CI->load->model('cc_models/cc_quote_m');
    $quotes = $CI->cc_quote_m->getAllQuotes();
    deleteQuoteSolrDocument($quotes);
}

function getQuotesSolrSearch($criteria = '') {
    $criteria->truck->trailer_type = 'Vans';
    $criteria->truck->options = '';

    $criteria->price->min = '1';
    $criteria->price->max = '*';

    $criteria->weight->min = '1';
    $criteria->weight->max = '*';

    $criteria->origin->city = 'Palo Alto';
    $criteria->origin->state = 'CA';
    $criteria->origin->zip = '94301';
    $criteria->origin->lat = '39.8392358';
    $criteria->origin->long = '-104.990251';

    $criteria->destination->city = 'New York';
    $criteria->destination->state = 'NY';
    $criteria->destination->zip = '10001';
    $criteria->destination->lat = '40.75368539999999';
    $criteria->destination->long = '-73.9991637';

    $criteria->pickup_date = '2015-06-20';
    $criteria->pickup_time = '16:00:00';

    $criteria->limit = 20;
    $criteria->offset = 0;

    $CI = &get_instance();
    $CI->load->model('cc_models/cc_quote_m');
    $client = getSolrClient();
    $query = $client->createSelect();
    $helper = $query->getHelper();

    $query->createFilterQuery('quote_status')->setQuery('quote_status:(unbooked OR unpriced OR quoted OR confirmed OR null)');
    $query->createFilterQuery('quote_confirm')->setQuery('quote_confirm:Yes');
    $query->createFilterQuery('truck_load_type')->setQuery('truck_load_type:truckload');
    $query->createFilterQuery('load_id')->setQuery('load_id:0');

    $query->createFilterQuery('trailer')->setQuery('trailer_type:' . $criteria->truck->trailer_type);
//    $query->createFilterQuery('valid_price')->setQuery("price:[" . $criteria->price->min . " TO " . $criteria->price->max . "]");
//    $query->createFilterQuery('valid_weight')->setQuery("weight:[" . $criteria->weight->min . " TO " . $criteria->weight->max . "]"); // TODO :: Provide schema for weight field

    $query->setRows($criteria->limit);
    $query->setStart($criteria->offset);
    $query->addSort('score', 'desc');

//    $distance1 = 2000 * 1.604;
//    $query->createFilterQuery('distance')->setQuery( // This filter is to restrict the result with in certain distance
//            $helper->geofilt(
//                    'origin_latlan', doubleval($criteria->origin->lat), doubleval($criteria->origin->long), doubleval($distance1)
//            )
//    );
//
    // This function will calculate the score based on days and location diffrence. The score generated will be used to sort the relavent result first.
    $query->setQuery('{!func}sum(recip(abs(ms(' . $criteria->pickup_date . 'T' . $criteria->pickup_time . 'Z, pickup_date)),3.16e-11,0.01,0.01), recip(' . $helper->geodist(
                    'origin_latlan', doubleval($criteria->origin->lat), doubleval($criteria->origin->long)
            ) . ', 1, 50, 50))');


//    $query->setQuery('{!func}recip(' . $helper->geodist(
//                    'origin_latlan', doubleval($criteria->origin->lat), doubleval($criteria->origin->long)
//            ) . ', 1, 50, 50)');
    // This will show the distance between the search criteria coordinates and quote origin coordinates.
    $query->addField('_distance_:' . $helper->geodist(
                    'origin_latlan', doubleval($criteria->origin->lat), doubleval($criteria->origin->long)
            )
    );

    // This will show the number of days between the search criteria date and pickup date of quote
    $query->addField('_time_difference_:div(abs(ms(' . $criteria->pickup_date . 'T' . $criteria->pickup_time . 'Z, pickup_date)),product(3600000, 24))');

    // This will show the score calculated based on the days difference. just for debugging purpose
    $query->addField('_time_difference_score_:recip(abs(ms(' . $criteria->pickup_date . 'T' . $criteria->pickup_time . 'Z, pickup_date)),3.16e-11,0.01,0.01)');

    // This will show the score calculated based on the difference between the locations.
    $query->addField('_distance_score_:recip(' . $helper->geodist(
                    'origin_latlan', doubleval($criteria->origin->lat), doubleval($criteria->origin->long)
            ) . ', 1, 50, 50)');


    $resultset = $client->select($query);
//    echo $resultset->getNumFound();

    $quotes = array();
    foreach ($resultset as $document) {
        foreach ($document AS $field => $value) {
            if ($field == 'id') {
                $id = str_replace('quote_', '', $value);
                $quote = $CI->cc_quote_m->getQuoteById($id);
                if ($quote) {
                    $quotes[] = $quote;
                }
                break;
            }
        }
    }

//    echo '<pre>'; print_r($quotes); exit;
    return $quotes;
}
