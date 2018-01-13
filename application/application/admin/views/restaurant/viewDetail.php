<div class="container margin-top-5">
    <div class="row padding0 margin0auto">
        <div class="col-lg-4 padding0 margin0auto pos-relative">
            <div class="row padding0 margin0auto">
                <div class="col-lg-12 padding0 margin0auto">
                    <div class="row padding0 margin0auto">
                        <div class="row padding0 margin0auto">
                            <?php if ($viewData['vRestaurantLogo'] != '') { ?>
                                <img src="<?= DOMAIN_URL; ?>/images/restaurant/<?= $viewData['iRestaurantID'] . '/thumb/' . $viewData['vRestaurantLogo']; ?>" />
                            <?php } else { ?>
                                <img src="<?= BASEURL; ?>/img/no-image.png" alt="Default Image" title="Default Image"/>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 padding0 margin0auto">
                    <div class="padding-top-10 text-justify">
                        <div class="row padding0 margin0auto">
                            <div class="col-lg-12 padding0 margin0auto">
                                Primary Email: <label><?= $viewData['vEmail']; ?></label>
                            </div>
                            <div class="col-lg-12 padding0 margin0auto">
                                Secondary Email: <label><?= $viewData['vEmailSecondary'] != '' ? $viewData['vEmailSecondary'] : 'N/A'; ?></label>
                            </div>
                            <div class="col-lg-12 padding0 margin0auto">
                                <label><?= $viewData['vContactNo'] != '' ? $viewData['vContactNo'] : ''; ?></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 padding0 margin0auto">
            <div class="padding-left-15 text-justify">
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        <h1 class="padding0 margin0auto"><?= $viewData['vRestaurantName']; ?></h1>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        <label>Primary Address:&nbsp;<?= $viewData['tAddress']; ?> - </label>
                        <label>Secondary Address:&nbsp;<?= $viewData['tAddress2'] != '' ? $viewData['tAddress2'] : '---'; ?> - </label>
                        <label><?= $viewData['vCityName'] . ', ' . $viewData['vStateName'] . ', ' . $viewData['vCountryName']; ?></label>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        <?= $viewData['tDescription']; ?>
                    </div>
                </div>
                <div class="padding-top-10"></div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Specialty: <label><?= $viewData['tSpecialty'] != '' ? $viewData['tSpecialty'] : 'N/A'; ?></label>
                    </div>
                </div>
                <?php
                $minTime = explode('-', $viewData['iMinTime']);
                $maxTime = explode('-', $viewData['iMaxTime']);
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
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Open-Close Time: <label><?= $openCloseTime; ?></label>
                    </div>
                </div>

                <?php
                $openDays = unserialize(RESTAURANT_OPEN_CLOSE_DAYS);
                $openDaysVal = explode(',', $viewData['vDaysOpen']);
                $echo = array();
                ?>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Open Days: <?php
                        foreach ($openDaysVal AS $v) {
                            if (in_array($v, $openDaysVal))
                                $echo[] = $openDays[$v] . ' ';
                        }
                        echo '<label>' . implode(', ', $echo) . '</label>';
                        ?>
                    </div>
                </div>

                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Price: <label><?= $viewData['iPriceValue'] . ' (' . ($viewData['eAlcohol'] == 'yes' ? 'With Alchohol' : 'Without Alcohol'); ?>) </label>
                    </div>
                </div>



            </div>
        </div>
    </div>
</div>