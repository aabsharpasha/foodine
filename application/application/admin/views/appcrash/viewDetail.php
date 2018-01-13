<div class="container margin-top-5">
    <div class="row padding0 margin0auto">
        <div class="col-lg-12 padding0 margin0auto pos-relative text-justify">
            <div class="row padding0 margin0auto">
                <div class="col-lg-12 padding0 margin0auto">

                    <div class="col-lg-4 padding0 margin0auto">
                        <div class="col-lg-5 padding0 margin0auto">
                            <h5 class="padding0 margin0auto"><strong>Activity Name:- </strong></h5>
                        </div>
                        <div class="col-lg-7 padding0 margin0auto">
                            <h5 class="padding0 margin0auto"><?= $viewData['activityName']; ?></h5>
                        </div>
                    </div>
                    <div class="col-lg-4 padding0 margin0auto">
                        <div class="col-lg-5 padding0 margin0auto">
                            <h5 class="padding0 margin0auto"><strong>Version:- </strong></h5>
                        </div>
                        <div class="col-lg-5 padding0 margin0auto">
                            <h5 class="padding0 margin0auto"> <?= $viewData['appVersion']; ?></h5>
                        </div>
                    </div>
                    <div class="col-lg-4 padding0 margin0auto">
                        <div class="col-lg-5 padding0 margin0auto">
                            <h5 class="padding0 margin0auto"><strong>Network Type:- </strong></h5>
                        </div>
                        <div class="col-lg-5 padding0 margin0auto">
                            <h5 class="padding0 margin0auto"> <?= $viewData['netType']; ?></h5>
                        </div>
                    </div>
                </div>
                <div class="margin-top-5">&nbsp;</div>

                <div class="col-lg-12 padding0 margin0auto">

                    <div class="col-lg-4 padding0 margin0auto">
                        <div class="col-lg-5 padding0 margin0auto">
                            <h5 class="padding0 margin0auto"><strong>Device SDK:- </strong></h5>
                        </div>
                        <div class="col-lg-7 padding0 margin0auto">
                            <h5 class="padding0 margin0auto"><?= $viewData['deviceSDK']; ?></h5>
                        </div>
                    </div>
                    <div class="col-lg-4 padding0 margin0auto">
                        <div class="col-lg-5 padding0 margin0auto">
                            <h5 class="padding0 margin0auto"><strong>Device RAM:- </strong></h5>
                        </div>
                        <div class="col-lg-5 padding0 margin0auto">
                            <h5 class="padding0 margin0auto"> <?= $viewData['deviceRAM']; ?></h5>
                        </div>
                    </div>
                    <div class="col-lg-4 padding0 margin0auto">
                        <div class="col-lg-5 padding0 margin0auto">
                            <h5 class="padding0 margin0auto"><strong>Created At:- </strong></h5>
                        </div>
                        <div class="col-lg-5 padding0 margin0auto">
                            <h5 class="padding0 margin0auto"> <?= $viewData['tCreatedAt']; ?></h5>
                        </div>
                    </div>
                </div>
                <div class="margin-top-5">&nbsp;</div>
                <div class="col-lg-12 padding0 margin0auto">
                    <h4 class="padding0 margin0auto">Detailed Report</h4>
                    <div class="margin-top-10">
                        <pre><?= $viewData['errorValue']; ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>