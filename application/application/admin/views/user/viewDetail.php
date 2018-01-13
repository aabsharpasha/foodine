<div class="container margin-top-5">
    <div class="row padding0 margin0auto">
        <div class="col-lg-3 padding0 margin0auto pos-relative">
            <div class="row padding0 margin0auto">
                <div class="col-lg-12 padding0 margin0auto">
                    <div class="row padding0 margin0auto">
                        <?php if ($viewData['vProfilePicture'] != '') { ?>
                            <img src="<?= IMGURL; ?>/user/<?= $viewData['iUserID'] . '/thumb/' . $viewData['vProfilePicture']; ?>" />
                        <?php } else { ?>
                            <img src="<?= BASEURL; ?>/img/no-image.png" alt="Default Image" title="Default Image"/>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9 padding0 margin0auto">
            <div class="padding-left-15 text-justify">
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        <h1 class="padding0 margin0auto">
                            <?php if ($viewData['vFirstName'] . ' ' . $viewData['vLastName'] == '') { ?>
                                <?= $viewData['vFirstName'] . ' ' . $viewData['vLastName']; ?>
                            <?php } else if ($viewData['vFullName'] !== '') { ?>
                                <?= $viewData['vFullName']; ?>
                            <?php } else {
                                echo '___';
                            } ?>
                        </h1>
                    </div>
                </div>
                <?php /*
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Gender: <label><?= $viewData['eGender']; ?></label>

                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Date Of Birth: <label><?= $viewData['dtDOB']; ?></label>
                    </div>
                </div> */ ?>

                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Status: <label><?= $viewData['eStatus']; ?></label>
                    </div>
                </div>

                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Primary Email: <label><?= $viewData['vEmail']; ?></label>
                    </div>
                </div>

                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Contact No: <label><?= $viewData['vMobileNo'] != '' ? $viewData['vMobileNo'] : 'N/A'; ?></label>
                    </div>
                </div>
                
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Registered On: <label><?= $viewData['tCreatedAt'] != '' ? date('d M Y g:i A',strtotime($viewData['tCreatedAt'])) : 'N/A'; ?></label>
                    </div>
                </div>
                <div class="padding-top-10"></div>
            </div>
        </div>
    </div>
</div>