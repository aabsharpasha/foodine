<div class="container margin-top-5">
    <div class="row padding0 margin0auto">
        <div class="col-lg-4 padding0 margin0auto pos-relative">
            <div class="row padding0 margin0auto">

                <div class="col-lg-12 padding0 margin0auto"  style="height: 500px; width:940px; overflow: auto;">
                    <div class="padding-top-10 text-justify">
                        <div class="row padding0 margin0auto">
                            <?php $i= 1; ?>
                            <?php foreach($viewData as $record) { ?>
                            <div class="col-lg-12 padding0 margin0auto">
                               <label><?= $i++; ?>) </label> <?= $record; ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>