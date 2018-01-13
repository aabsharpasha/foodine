<div class="container margin-top-5">
    <div class="row padding0 margin0auto">
        <div class="col-lg-12 padding0 margin0auto pos-relative text-justify">
            <div class="row padding0 margin0auto">
                <div class="col-lg-12 padding0 margin0auto">
                    <h1 class="padding0 margin0auto"><?= $viewData['vOfferText']; ?></h1>
                    <h4 class="">Restaurant Name : <?= $viewData['vRestaurantName']; ?></h4>
                    
                </div>
<!--                <div class="col-lg-12 padding0 margin0auto">
                    Terms and Conditions: <label><?//= $viewData['tTermsOfUse']; ?></label>
                </div>-->
                <?php if(!empty($viewData['vDaysAllow'])) {
                $openDays = unserialize(RESTAURANT_OPEN_CLOSE_DAYS);
                $openDaysVal = explode(',', $viewData['vDaysAllow']);
                //mprd($openDays);
                $echo = array();
                ?>
                <div class="col-lg-12 padding0 margin0auto">
                    Days Allow: <?php
                    foreach ($openDaysVal AS $v) {
                        if (in_array($v, $openDaysVal))
                            $echo[] = $openDays[$v];
                    }
                    echo '<label>' . implode(', ', $echo) . '</label>';
                    ?>
                </div>
                <?php }?>

<!--                <div class="col-lg-12 padding0 margin0auto">
                    Specific Deal: <label><?//= ucfirst($viewData['eSpecific']); ?></label>
                </div>-->

                <div class="col-lg-12 padding0 margin0auto">
                    Deal Date: <label><?= $viewData['dtStartDate'] . ' to ' . $viewData['dtExpiryDate']; ?></label>
                </div>
            </div>
        </div>
    </div>
</div>