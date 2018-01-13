<html>
    <head>
        <meta charset='utf-8'/>
    </head>
    <body>
        <h1>Basic Information</h1>
        <table border="1" cellspacing="5" cellpadding="5">
            <tr>
                <th align="left">Name</th>
                <td align="left"><?= $restData['vRestaurantName']; ?></td>
            </tr>
            <tr>
                <th align="left">Email</th>
                <td align="left"><?= $restData['vEmail']; ?></td>
            </tr>
            <tr>
                <th align="left">Secondary Email</th>
                <td align="left"><?= $restData['vEmailSecondary']; ?></td>
            </tr>
            <tr>
                <th align="left">Latitude</th>
                <td align="left"><?= $restData['vLat']; ?></td>
            </tr>
            <tr>
                <th align="left">Longitude</th>
                <td align="left"><?= $restData['vLog']; ?></td>
            </tr>
            <tr>
                <th align="left">Address Line 1</th>
                <td align="left"><?= $restData['tAddress']; ?></td>
            </tr>
            <tr>
                <th align="left">Address Line 2</th>
                <td align="left"><?= $restData['tAddress2']; ?></td>
            </tr>
            <tr>
                <th align="left">Location Name</th>
                <td align="left"><?= $restData['tAddress2']; ?></td>
            </tr>
            <tr>
                <th align="left">Specialty</th>
                <td align="left"><?= $restData['tSpecialty']; ?></td>
            </tr>
            <tr>
                <th align="left">City</th>
                <td align="left"><?= $restData['vCityName']; ?></td>
            </tr>
            <tr>
                <th align="left">State</th>
                <td align="left"><?= $restData['vStateName']; ?></td>
            </tr>
            <tr>
                <th align="left">Country</th>
                <td align="left"><?= $restData['vCountryName']; ?></td>
            </tr>
            <? /*<tr>
                <th align="left">Restaurant Image</th>
                <td align="left" style="height: 100px;">
                    <?php
                    if ($restData['vRestaurantLogo'] != '') {
                        $pathVal = DOC_ROOT . "/images/restaurant/" . $restData['iRestaurantID'] . "/thumb/" . $restData['vRestaurantLogo'];
                        ?>
                        <img src="<?= $pathVal; ?>" width="140"/>
                        <?php
                    } else {
                        $pathVal = DOC_ROOT . "/admin/img/no-image.png";
                        ?>
                        <img src="<?= $pathVal; ?>" width="140"/>
                    <?php } ?>
                </td>
            </tr> */?>
            <tr>
                <th align="left">Contact Number</th>
                <td align="left"><?= $restData['vContactNo']; ?></td>
            </tr>
            <tr>
                <th align="left">Description</th>
                <td align="left"><?= $restData['tDescription']; ?></td>
            </tr>
            <tr>
                <th align="left">Open Close Time</th>
                <?php
                $minTime = explode('-', $restData['iMinTime']);
                $maxTime = explode('-', $restData['iMaxTime']);
                $openCloseTime = '';
                if (isset($minTime[0]) && isset($minTime[1]) && isset($minTime[2])) {
                    $minMaradian = $minTime[2] == '1' ? 'AM' : 'PM';
                    $maxMaradian = $maxTime[2] == '1' ? 'AM' : 'PM';

                    $minhr = strlen($minTime[0]) == 1 ? '0' . $minTime[0] : $minTime[0];
                    $maxhr = strlen($maxTime[0]) == 1 ? '0' . $maxTime[0] : $maxTime[0];

                    $minmin = strlen($minTime[1]) == 1 ? '0' . $minTime[1] : $minTime[1];
                    $maxmin = strlen($maxTime[1]) == 1 ? '0' . $maxTime[1] : $maxTime[1];

                    $openCloseTime = $minhr . ':' . $minmin . ' ' . $minMaradian . ' to ' . $maxhr . ':' . $maxmin . ' ' . $maxMaradian;
                }
                ?>
                <td align="left"><?= $openCloseTime; ?></td>
            </tr>
            <tr>
                <th align="left">Open Days</th>
                <?php
                $openDays = unserialize(RESTAURANT_OPEN_CLOSE_DAYS);
                $openDaysVal = explode(',', $restData['vDaysOpen']);
                $echo = array();
                ?>
                <td>
                    <?php
                    foreach ($openDaysVal AS $v) {
                        if (in_array($v, $openDaysVal))
                            $echo[] = $openDays[$v];
                    }
                    echo implode(',', $echo);
                    ?>
                </td>
            </tr>
            <tr>
                <th align="left">Price</th>
                <td align="left"><?= $restData['iPriceValue']; ?></td>
            </tr>
            <tr>
                <th align="left">FB Link</th>
                <td align="left"><?= $restData['vFbLink']; ?></td>
            </tr>
            <tr>
                <th align="left">Instagram Link</th>
                <td align="left"><?= $restData['vInstagramLink']; ?></td>
            </tr>
        </table>

        <h2>Restaurant Other Information</h2>
        <table border="1" cellspacing="5" cellpadding="5">
            <tr>
                <th align="left">Category</th>
                <td align="left"><?= $catData; ?></td>
            </tr>
            <tr>
                <th align="left">Cuisine</th>
                <td align="left"><?= $cuisineData; ?></td>
            </tr>
            <tr>
                <th align="left">Music</th>
                <td align="left"><?= $musicData; ?></td>
            </tr>
            <tr>
                <th align="left">Total Deals</th>
                <td align="left"><?= isset($otherCountData['totalDeals']) ? $otherCountData['totalDeals'] : 0; ?></td>
            </tr>
            <tr>
                <th align="left">Total Review</th>
                <td align="left"><?= isset($otherCountData['totalReview']) ? $otherCountData['totalReview'] : 0; ?></td>
            </tr>
            <tr>
                <th align="left">Total Likes</th>
                <td align="left"><?= isset($otherCountData['totalLike']) ? $otherCountData['totalLike'] : 0; ?></td>
            </tr>
            <tr>
                <th align="left">Total Dislike</th>
                <td align="left"><?= isset($otherCountData['totalDislike']) ? $otherCountData['totalDislike'] : 0; ?></td>
            </tr>
        </table>

        <h2>Restaurant Booking Information</h2>
        <table border="1" cellspacing="5" cellpadding="5">
            <tr>
                <th align="left">Total Request</th>
                <td align="left"><?= isset($bookingData['total']) ? $bookingData['total'] : 0; ?></td>
            </tr>
            <tr>
                <th align="left">Accept</th>
                <td align="left"><?= isset($bookingData['accept']) ? $bookingData['accept'] : 0; ?></td>
            </tr>
            <tr>
                <th align="left">Pending</th>
                <td align="left"><?= isset($bookingData['pending']) ? $bookingData['pending'] : 0; ?></td>
            </tr>
            <tr>
                <th align="left">Reject</th>
                <td align="left"><?= isset($bookingData['reject']) ? $bookingData['reject'] : 0; ?></td>
            </tr>
        </table>
    </body>
</html>