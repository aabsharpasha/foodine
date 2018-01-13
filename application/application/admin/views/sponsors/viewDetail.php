<div class="container margin-top-5">
    <div class="row padding0 margin0auto">
        <div class="col-lg-12 padding0 margin0auto pos-relative text-justify">
            <div class="row padding0 margin0auto">
                <div class="col-lg-12 padding0 margin0auto">
                    <h4 class="">Restaurant Name : <?= $viewData['vRestaurantName']; ?></h4>
                </div>
              <div class="col-lg-12 padding0 margin0auto">
                    Keyword(s): <label><?= $viewData['iSponserKeywords']; ?></label>
                </div>

                <div class="col-lg-12 padding0 margin0auto">
                    Deal Date: <label><?= $viewData['dtStartDate'] . ' to ' . $viewData['dtExpiryDate']; ?></label>
                </div>
            </div>
        </div>
    </div>
</div>