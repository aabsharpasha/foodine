<?php
$headerData = $this->headerlib->data();
if (isset($getDealsData) && $getDealsData != '')
    extract($getDealsData);
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$ADMIN_iRestaurantID = $this->session->userdata('iRestaurantID');


//print_r($getFacilityData);

$form_attr = array(
    'name' => 'deals-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);
$vOfferTitleField = array(
    'name' => 'subOffer[0][vOfferTitle]',
    'id' => 'vOfferTitle_0',
    "required" => "required",
    'placeholder' => 'Please enter offer title',
    "data-errortext" => "This is offer's text!",
    'value' => (isset($vOfferTitle) && $vOfferTitle != '') ? $vOfferTitle : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$vOfferTextField = array(
    'name' => 'vOfferText',
    'id' => 'vOfferText',
    "required" => "required",
    'placeholder' => 'Please enter offer title',
    "data-errortext" => "This is offer's text!",
    'value' => (isset($vOfferText) && $vOfferText != '') ? $vOfferText : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$peopleField = array(
    'name' => 'subOffer[0][people]',
    'id' => 'people_0',
    "required" => "required",
    'placeholder' => 'Please enter number of people',
    "data-errortext" => "This is number of people!",
    'value' => (isset($people) && $people != '') ? $people : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$tActualPriceField = array(
    'name' => 'tActualPrice',
    'id' => 'tActualPrice',
    "required" => "required",
    'placeholder' => 'Please enter actual price',
    "data-errortext" => "This is offer's text!",
    'value' => (isset($tActualPrice) && $tActualPrice != '') ? $vtActualPrice : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$tDiscountedPriceField = array(
    'name' => 'tDiscountedPrice',
    'id' => 'tDiscountedPrice',
    "required" => "required",
    'placeholder' => 'Please enter discounted price',
    "data-errortext" => "This is offer's text!",
    'value' => (isset($tDiscountedPrice) && $tDiscountedPrice != '') ? $tDiscountedPrice : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$tSOActualPriceField = array(
    'name' => 'subOffer[0][tActualPrice]',
    'id' => 'tActualPrice_0',
    "required" => "required",
    'placeholder' => 'Please enter actual price',
    "data-errortext" => "This is offer's text!",
    'value' => (isset($tActualPrice) && $tActualPrice != '') ? $vtActualPrice : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$dSOCommissionField = array(
    'name' => 'subOffer[0][dCommission]',
    'id' => 'dCommission_0',
    "required" => "required",
    'placeholder' => 'Please enter commission',
    "data-errortext" => "This is Commission Percentage Amount!",
    'value' => (isset($dCommission) && $dCommission != '') ? $dCommission : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$tSODiscountedPriceField = array(
    'name' => 'subOffer[0][tDiscountedPrice]',
    'id' => 'tDiscountedPrice_0',
    "required" => "required",
    'placeholder' => 'Please enter discounted price',
    "data-errortext" => "This is offer's text!",
    'value' => (isset($tDiscountedPrice) && $tDiscountedPrice != '') ? $tDiscountedPrice : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$dtStartDateField = array(
    'name' => 'dtStartDate',
    'id' => 'dtStartDate',
    "required" => "required",
    'placeholder' => 'Please select start date',
    "data-errortext" => "This is start date text!",
    'readonly' => 'readonly',
    'value' => (isset($dtStartDate) && $dtStartDate != '') ? date('m/d/Y', strtotime($dtStartDate)) : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$vDealCode_text = array(
    'name' => 'vDealCode',
    'id' => 'vDealCode',
    'placeholder' => 'Please enter deal code',
    "data-errortext" => "This is start date text!",
    'value' => (isset($vDealCode) && $vDealCode != '') ? $vDealCode : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$dtExpiryDateField = array(
    'name' => 'dtExpiryDate',
    'id' => 'dtExpiryDate',
    "required" => "required",
    'readonly' => 'readonly',
    'placeholder' => 'Please select expiry date',
    "data-errortext" => "This is expiry date text!",
    'value' => (isset($dtExpiryDate) && $dtExpiryDate != '') ? date('m/d/Y', strtotime($dtExpiryDate)) : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$activestatus = array(
    'name' => 'eStatus',
    'value' => 'Active',
    'checked' => (isset($eStatus) && $eStatus == 'Active') ? 'TRUE' : 'FALSE'
);

$inactivestatus = array(
    'name' => 'eStatus',
    'value' => 'Inactive',
    'checked' => (isset($eStatus) && $eStatus == 'Inactive') ? 'TRUE' : 'FALSE'
);

// Setting Hidden action attributes for Add/Edit functionality.
$hiddeneditattr = array(
    "action" => "backoffice.offersedit"
);

$hiddenaddattr = array(
    "action" => "backoffice.offersadd"
);

$deal_id = array(
    "iComboOffersID" => (isset($iComboOffersID) && $iComboOffersID != '') ? $iComboOffersID : ''
);

$submit_attr = array(
    'class' => 'submit btn btn-sm btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Offer",
    'type' => 'submit'
);
$cancel_attr = array(
    'class' => 'btn btn-inverse ',
    'value' => "Reset",
    'type' => 'reset'
);

$pic = (isset($vOfferImage) && $vOfferImage != '') ? $vOfferImage : '';
$uid = (isset($iComboOffersID) && $iComboOffersID != '') ? $iComboOffersID : '';
$restid = (isset($iRestaurantID) && $iRestaurantID != '') ? $iRestaurantID : '';
?>
<!doctype html>
<html lang="en-us">
    <head>
        <title><?= $title ?></title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets_form']; ?>

        <style type="text/css">
            .genderlabel{
                display:inline-block;
                margin-right: 10px;
            } 
            .genderlabel .radio{
                padding-top: 5px;
            }
            .close-sub-offer{
                display:block;
                float:right;
                width:30px;
                height:29px;
                background:url(http://www.htmlgoodies.com/img/registrationwelcome/close_icon.png) no-repeat center center;
            }            
        </style>

    </head>
    <body>
        <?php $this->load->view('include/header_view') ?>
        <section id="page">
            <!-- SIDEBAR -->
            <?php $this->load->view('include/sidebar_view') ?>
            <!-- /SIDEBAR -->
            <div id="main-content">
                <div class="container">
                    <div class="row">
                        <div id="content" class="col-lg-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="page-header">
                                        <ul class="breadcrumb">
                                            <li>
                                                <i class="fa fa-tachometer"></i>
                                                <a href="<?= BASEURL ?>">Home</a>
                                            </li>
                                            <li>Combo Offers</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Combo Offer</h3>
                                        </div>
                                        <div class="description">Add/Edit Offer</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Combo"; ?></h4>
                                            <div class="tools hidden-xs">

                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>

                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("offers/add", $form_attr);
                                            if (isset($iComboOffersID) && $iComboOffersID != '') {
                                                echo form_hidden($deal_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <?php if ($ADMINTYPE != 3) { ?>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label" for="iRestaurantID">Restaurant <span class="required">*</span></label>
                                                    <div class="col-sm-9">
                                                        <select name="iRestaurantID" id="iRestaurantID" class="maxwidth500 col-lg-12" required="required">
                                                            <option value="" > - Select Restaurant - </option>
                                                            <?php
                                                            foreach ($getRestaurantData as $key => $value) {
                                                                if (isset($iRestaurantID) && $value['iRestaurantID'] == $iRestaurantID) {
                                                                    echo '<option value="' . $value['iRestaurantID'] . '" selected="selected">' . $value['vRestaurantName'] . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $value['iRestaurantID'] . '">' . $value['vRestaurantName'] . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <input type="hidden" name="iRestaurantID" value="<?= $ADMIN_iRestaurantID; ?>">
                                            <?php } ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="vOfferText">Offer Title <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?= form_input($vOfferTextField); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Offer Image</label>
                                                <div class="col-sm-9">

                                                    <?php
                                                    if ($pic != '' && $restid != '' && $uid != '') {
                                                        $pic_str = '<img src="' . IMGURL . '/combo/' . $restid . '/thumb/' . $pic . '">';
                                                        $sel_text = 'fileinput-new';
                                                        $change_text = 'fileinput-exists';
                                                        $change_div = 'fileinput-exists';
                                                    } else {
                                                        $sel_text = 'fileinput-new';
                                                        $change_text = 'fileinput-exists';
                                                        $pic_str = '';
                                                        $change_div = 'fileinput-new';
                                                    }
                                                    ?>

                                                    <div class="fileinput <?php echo $change_div; ?> " data-provides="fileinput">
                                                        <div class="fileinput-preview thumbnail" style="width: 200px; height: 150px;">
                                                            <?php echo $pic_str; ?>
                                                        </div>
                                                        <input type="hidden" name="vOfferUrl" value="<?php echo $pic; ?>"/>
                                                        <div>
                                                            <span class="btn btn-default btn-file">
                                                                <span class="<?php echo $sel_text ?> select_pic" data-trigger="fileinput"  >Select image</span>
                                                                <span class="<?php echo $change_text ?> select_pic" data-trigger="fileinput">Change</span>
                                                                <input type="file" 
                                                                       name="vOfferImage" 
                                                                       accept="image/jpg, image/JPG,image/JPEG, image/jpeg, image/png, image/PNG" 
                                                                       id="vOfferImage" />
                                                            </span>
                                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput" id="removebtn">Remove</a>
                                                        </div>
                                                        <input type="hidden" id="removepic" name="removepic" value="0" />
                                                    </div>

                                                </div>
                                            </div>

                                            <!--                                            <div class="form-group">
                                                                                            <label class="col-sm-3 control-label" for="tOfferDetail" required="required">Offer Detail </label>
                                                                                            <div class="col-sm-9">
                                                                                                <textarea class="maxwidth500 form-control" id="tOfferDetail" name="tOfferDetail" placeholder="Please enter offer details"><?= isset($tOfferDetail) && $tOfferDetail != '' ? $tOfferDetail : ''; ?></textarea>
                                                                                            </div>
                                                                                        </div>-->
                                            <!--                                            <div class="form-group">
                                                                                            <label class="col-sm-3 control-label" for="tActualPrice">Actual Price <span class="required">*</span></label>
                                                                                            <div class="col-sm-9">
                                                                                                <?//= form_input($tActualPriceField); ?>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="form-group">
                                                                                            <label class="col-sm-3 control-label" for="tDiscountedPrice">Discounted Price <span class="required">*</span></label>
                                                                                            <div class="col-sm-9">
                                                                                                <?//= form_input($tDiscountedPriceField); ?>
                                                                                            </div>
                                                                                        </dTerms Of Useiv>-->
                                            <!--                                            <div class="form-group">
                                                                                            <label class="col-sm-3 control-label" for="tTermsOfUse" required="required">Terms Of Use <span class="required">*</span></label>
                                                                                            <div class="col-sm-9">
                                                                                                <textarea class="maxwidth500 form-control" id="tTermsOfUse" name="tTermsOfUse" placeholder="Please enter terms of use"><?= isset($tTermsOfUse) && $tTermsOfUse != '' ? $tTermsOfUse : ''; ?></textarea>
                                                                                            </div>
                                                                                        </div>
                                            
                                                                                        <div class="form-group">
                                                                                            <label class="col-sm-3 control-label" for="vDealCode">Deal Code<span class="required">*</span></label>
                                                                                            <div class="col-sm-9">
                                                                                                <?//= form_input($vDealCode_text); ?>
                                                                                            </div>
                                                                                        </div>-->

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="dtStartDate">Start Date <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?= form_input($dtStartDateField); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="dtExpiryDate">Expiry Date <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?= form_input($dtExpiryDateField); ?>
                                                </div>
                                            </div>

                                            <?php
                                            $vDaysAllowValue = array();
                                            if (isset($vDaysAllow)) {
                                                $vDaysAllowValue = explode(',', $vDaysAllow);
                                            }
                                            ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="vDaysAllow">Days Allow</label>
                                                <div class="col-sm-9">
                                                    <select name="vDaysAllow[]" id="vDaysAllow" multiple="multiple">
                                                        <option value="1" <?= in_array('1', $vDaysAllowValue) ? 'selected="selected"' : NULL; ?>>Sunday</option>
                                                        <option value="2" <?= in_array('2', $vDaysAllowValue) ? 'selected="selected"' : NULL; ?>>Monday</option>
                                                        <option value="3" <?= in_array('3', $vDaysAllowValue) ? 'selected="selected"' : NULL; ?>>Tuesday</option>
                                                        <option value="4" <?= in_array('4', $vDaysAllowValue) ? 'selected="selected"' : NULL; ?>>Wednesday</option>
                                                        <option value="5" <?= in_array('5', $vDaysAllowValue) ? 'selected="selected"' : NULL; ?>>Thursday</option>
                                                        <option value="6" <?= in_array('6', $vDaysAllowValue) ? 'selected="selected"' : NULL; ?>>Friday</option>
                                                        <option value="7" <?= in_array('7', $vDaysAllowValue) ? 'selected="selected"' : NULL; ?>>Saturday</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!--                                            <div class="form-group">
                                                                                            <label class="col-sm-3 control-label">Specific Deal</label>
                                                                                            <div class="col-sm-9">
                                                                                                <input type="checkbox" 
                                                                                                       id="eSpecific" 
                                                                                                       name="eSpecific" 
                                                                                                       value="1" 
                                                                                                       <?//= isset($eSpecific) && $eSpecific == 'yes' ? 'checked="checked"' : ''; ?>/>
                                                                                                <label class="font-12 error">Check to set as a specific deal</label>
                                                                                            </div>
                                                                                        </div>-->

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tTermsOfUse" required="required">Terms Of Use </label>
                                                <div class="col-sm-9">
                                                    <!-- CKE -->  
                                                    <div class="box border primary">
                                                        <div class="box-title">
                                                            <h4><i class="fa fa-pencil-square"></i>Content Editor</h4>
                                                            <div class="tools hidden-xs">
                                                                <a href="javascript:;" class="collapse">
                                                                    <i class="fa fa-chevron-up"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="box-body">
                                                            <textarea class="ckeditor" id="tTermsOfUse" name="tTermsOfUse" placeholder="Please enter terms of use"><?= isset($tTermsOfUse) && $tTermsOfUse != '' ? $tTermsOfUse : ''; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <!-- /CKE --> 
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="page-header">
                                                        <div class="clearfix">
                                                            <h3 class="content-suboffer">Combo Sub Offer</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if (isset($getSubOffers) && $getSubOffers != '') { ?>
                                                <?php foreach ($getSubOffers as $key => $value) { ?>
                                                    <div class="subOffer" data-attr="<?php echo $key; ?>">
                                                        <?php if($key){ ?>
                                                        <a class="close-sub-offer" href="javascript:removeSubOffer(<?php echo $key; ?>);"></a><br>
                                                        <?php } ?>
                                                        <div class="form-group">
                                                            <input type="hidden" name="subOffer[<?php echo $key; ?>][iComboSubOffersID]" value="<?= $value['iComboSubOffersID']; ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-3 control-label" for="vOfferTitle">Offer Title <span class="required">*</span></label>
                                                            <div class="col-sm-9">
                                                                <?=
                                                                form_input(array('name' => 'subOffer[' . $key . '][vOfferTitle]',
                                                                    'id' => 'vOfferTitle_' . $key,
                                                                    "required" => "required",
                                                                    'placeholder' => 'Please enter offer title',
                                                                    "data-errortext" => "This is offer's text!",
                                                                    'value' => (isset($value['vOfferTitle']) && $value['vOfferTitle'] != '') ? $value['vOfferTitle'] : '',
                                                                    'type' => 'text',
                                                                    'class' => 'form-control maxwidth500'));
                                                                ?>
                                                            </div>
                                                        </div>


                                                        <div class="form-group">
                                                            <label class="col-sm-3 control-label" for="tOfferDetail" required="required">Offer Detail </label>
                                                            <div class="col-sm-9">
                                                                <!-- CKE -->  
                                                                <div class="box border primary">
                                                                    <div class="box-title">
                                                                        <h4><i class="fa fa-pencil-square"></i>Content Editor</h4>
                                                                        <div class="tools hidden-xs">
                                                                            <a href="javascript:;" class="collapse">
                                                                                <i class="fa fa-chevron-up"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="box-body">
                                                                        <textarea class="ckeditor" id="tOfferDetail_"<?php echo $key; ?> name="subOffer[<?php echo $key; ?>][tOfferDetail]" placeholder="Please enter offer details"><?= isset($value['tOfferDetail']) && $value['tOfferDetail'] != '' ? $value['tOfferDetail'] : ''; ?></textarea>
                                                                    </div>
                                                                </div>
                                                                <!-- /CKE --> 
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-3 control-label" for="tActualPrice">Actual Price <span class="required">*</span></label>
                                                            <div class="col-sm-9">
                                                                <?=
                                                                form_input(array(
                                                                    'name' => 'subOffer[' . $key . '][tActualPrice]',
                                                                    'id' => 'tActualPrice_' . $key,
                                                                    "required" => "required",
                                                                    'placeholder' => 'Please enter actual price',
                                                                    "data-errortext" => "This is offer's text!",
                                                                    'value' => (isset($value['tActualPrice']) && $value['tActualPrice'] != '') ? $value['tActualPrice'] : '',
                                                                    'type' => 'text',
                                                                    'class' => 'form-control maxwidth500'
                                                                ));
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-3 control-label" for="tDiscountedPrice">Discounted Price <span class="required">*</span></label>
                                                            <div class="col-sm-9">
                                                                <?=
                                                                form_input(array(
                                                                    'name' => 'subOffer[' . $key . '][tDiscountedPrice]',
                                                                    'id' => 'tDiscountedPrice_' . $key,
                                                                    "required" => "required",
                                                                    'placeholder' => 'Please enter discounted price',
                                                                    "data-errortext" => "This is offer's text!",
                                                                    'value' => (isset($value['tDiscountedPrice']) && $value['tDiscountedPrice'] != '') ? $value['tDiscountedPrice'] : '',
                                                                    'type' => 'text',
                                                                    'class' => 'form-control maxwidth500'
                                                                ));
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-3 control-label" for="people">No of People <span class="required">*</span></label>
                                                            <div class="col-sm-9">
                                                                <?=
                                                                form_input(array(
                                                                    'name' => 'subOffer[' . $key . '][people]',
                                                                    'id' => 'people_' . $key,
                                                                    "required" => "required",
                                                                    'placeholder' => 'Please enter number of people',
                                                                    "data-errortext" => "This is number of people!",
                                                                    'value' => (isset($value['people']) && $value['people'] != '') ? $value['people'] : '',
                                                                    'type' => 'text',
                                                                    'class' => 'form-control maxwidth500'));
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-3 control-label" for="dCommission">Commission (in percentage)<span class="required">*</span></label>
                                                            <div class="col-sm-9">
                                                                <?=
                                                                form_input(array(
                                                                    'name' => 'subOffer[' . $key . '][dCommission]',
                                                                    'id' => 'dCommission_' . $key,
                                                                    "required" => "required",
                                                                    'placeholder' => 'Please enter discounted price',
                                                                    "data-errortext" => "This is offer's text!",
                                                                    'value' => (isset($value['dCommission']) && $value['dCommission'] != '') ? $value['dCommission'] : '',
                                                                    'type' => 'text',
                                                                    'class' => 'form-control maxwidth500'
                                                                ));
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <?php } ?>
                                            <?php } else { ?>
                                                <div class="subOffer" data-attr="0">
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label" for="vOfferTitle">Offer Title <span class="required">*</span></label>
                                                        <div class="col-sm-9">
                                                            <?= form_input($vOfferTitleField); ?>
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label" for="tOfferDetail" required="required">Offer Detail </label>
                                                        <div class="col-sm-9">
                                                            <!-- CKE -->  
                                                            <div class="box border primary">
                                                                <div class="box-title">
                                                                    <h4><i class="fa fa-pencil-square"></i>Content Editor</h4>
                                                                    <div class="tools hidden-xs">
                                                                        <a href="javascript:;" class="collapse">
                                                                            <i class="fa fa-chevron-up"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="box-body">
                                                                    <textarea class="ckeditor" id="tOfferDetail_0" name="subOffer[0][tOfferDetail]" placeholder="Please enter offer details"><?= isset($tOfferDetail) && $tOfferDetail != '' ? $tOfferDetail : ''; ?></textarea>
                                                                </div>
                                                            </div>
                                                            <!-- /CKE --> 
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label" for="tActualPrice">Actual Price <span class="required">*</span></label>
                                                        <div class="col-sm-9">
                                                            <?= form_input($tSOActualPriceField); ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label" for="tDiscountedPrice">Discounted Price <span class="required">*</span></label>
                                                        <div class="col-sm-9">
                                                            <?= form_input($tSODiscountedPriceField); ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label" for="people">No of People <span class="required">*</span></label>
                                                        <div class="col-sm-9">
                                                            <?= form_input($peopleField); ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label" for="dCommission">Commission <span class="required">*</span></label>
                                                        <div class="col-sm-9">
                                                            <?= form_input($dSOCommissionField); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <a class="btn btn-sm btn-grey" id="addSuboffer">+Sub Offer</a>
                                                    <?php echo form_input($submit_attr); ?>
                                                    <a class="btn btn-sm btn-grey" href="<?= BASEURL .''. $this->controller; ?>">Cancel</a>
                                                </div>
                                            </div>
                                            <?php echo form_close(); ?>
                                        </div>
                                    </div>
                                    <!-- /BOX -->
                                </div>
                            </div>
                            <?php $this->load->view('include/footer_view') ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?= $headerData['javascript_form']; ?>
        <script type="text/javascript" src="<?= base_url() ?>js/ckeditor/ckeditor.js"></script>
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.css" type="text/css" media="screen" />
        <script src="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript" charset="utf-8"></script>
        
        <script>
            var _minDay, _maxDay;
            _minDay = _maxDay = -1;
            var $datepicker1 = $("#dtStartDate");
            var $datepicker2 = $("#dtExpiryDate");
            
            function removeSubOffer(id){
                $("div[data-attr="+id+"]").remove(); 
                $('#validateForm').find('.subOffer').last().css('border-bottom', 'none');
            };
            
            jQuery(document).ready(function() {
                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                var iComboOffersID = '<?= isset($iComboOffersID) ? $iComboOffersID : 0; ?>';
                var lastDiv = $('#validateForm').find('.subOffer').last().css('border-bottom', 'none');
                $('#addSuboffer').click(function() {
                    var lastDiv = $('#validateForm').find('.subOffer').last();
                    var curId = parseInt(lastDiv.attr('data-attr'));
                    var id = curId + 1;
                    console.log(id);
                    var newDiv = '<div data-id="' + id + '" class="subOffer" data-attr="' + id + '" style="border-bottom: medium none;"><a class="close-sub-offer" href="javascript:removeSubOffer('+id+');"></a><br>\n\
                                <div class="form-group"><label for="vOfferTitle" class="col-sm-3 control-label">Offer Title \n\
                                <span class="required">*</span></label><div class="col-sm-9">\n\
                                <input type="text" class="form-control maxwidth500" data-errortext="This is offer\'s text!" \n\
                                placeholder="Please enter offer title" required="required" id="vOfferTitle_' + id + '" value="" \n\
                                name="subOffer[' + id + '][vOfferTitle]"></div></div><div class="form-group"><label required="required" for="tOfferDetail" \n\
                                class="col-sm-3 control-label">Offer Detail </label><div class="col-sm-9"><div class="box border primary"><div class="box-title">\n\
                                <h4><i class="fa fa-pencil-square"></i>Content Editor</h4><div class="tools hidden-xs"><a href="javascript:;" class="collapse">\n\
                                <i class="fa fa-chevron-up"></i></a></div></div><div class="box-body"><textarea placeholder="Please enter offer details" name="subOffer[' + id + '][tOfferDetail]" id="tOfferDetail_' + id + '" \n\
                                class="ckeditor"></textarea></div></div></div></div><div class="form-group"><label for="tActualPrice" \n\
                                class="col-sm-3 control-label">Actual Price <span class="required">*</span></label>\n\
                                <div class="col-sm-9"><input type="text" class="form-control maxwidth500" \n\
                                data-errortext="This is offer\'s text!" placeholder="Please enter actual price" required="required" \n\
                                id="tActualPrice_' + id + '" value="" name="subOffer[' + id + '][tActualPrice]"></div></div>\n\
                                <div class="form-group"><label for="tDiscountedPrice" class="col-sm-3 control-label">Discounted Price \n\
                                <span class="required">*</span></label><div class="col-sm-9">\n\
                                <input type="text" class="form-control maxwidth500" data-errortext="This is offer\'s text!" \n\
                                placeholder="Please enter discounted price" required="required" id="tDiscountedPrice_' + id + '" value="" \n\
                                name="subOffer[' + id + '][tDiscountedPrice]"></div></div><div class="form-group"><label for="people" \n\
                                class="col-sm-3 control-label">No of People <span class="required">*</span></label><div \n\
                                class="col-sm-9"><input type="text" class="form-control maxwidth500" data-errortext="This is number of people!" \n\
                                placeholder="Please enter number of people" required="required" id="subOffer[' + id + '][people]" \n\
                                value="" name="subOffer[' + id + '][people]"></div></div><div class="form-group"><label for="dCommission" class="col-sm-3 control-label">Commission \n\<span class="required">*</span></label><div class="col-sm-9">\n\<input type="text" class="form-control maxwidth500" data-errortext="This is offer\'s text!" \n\placeholder="Please enter commission" required="required" id="dCommission_' + id + '" value="" \n name="subOffer[' + id + '][dCommission]"></div></div>';
                    lastDiv.after(newDiv);
                    var lastDiv = $('#validateForm').find('.subOffer').last().css('border-bottom', 'none');
                    lastDiv.css('border-top', '1px #000 solid');
                    CKEDITOR.replace( "tOfferDetail_" + id  );
                });
                $("#validateForm").validate({
                    rules: {
                        iRestaurantID: {
                            required: true
                        },
//                        vDealCode: {
//                            required: true,
//                            remote: {
//                                url: BASEURL + '<?//= $this->controller; ?>/checkDealCode',
//                                type: 'post',
//                                data: {
//                                    iDealID: iDealID
//                                }
//                            }
//                        },
                        vOfferText: {
                            required: true
                        },
//                        tTermsOfUse: {
//                            required: true
//                        },
                        tActualPrice: {
                            required: true,
                            number: true
                        },
                        people: {
                            required: true,
                            number: true
                        },
                        tDiscountedPrice: {
                            required: true,
                            number: true
                        },
                        dtStartDate: {
                            required: true
                        },
                        dtExpiryDate: {
                            required: true
                        }
                    },
                    messages: {
                        iRestaurantID: "Please select restaurant",
//                        vDealCode: {
//                            required: 'Please enter deal code.',
//                            remote: 'Entered deal code is already exists.'
//                        },
                        vOfferText: 'Please enter offer text',
                        tTermsOfUse: 'Please enter terms of use',
                        tActualPrice: {required: 'Please enter actual price',
                            number: 'Please enter a valid amount'},
                        tDiscountedPrice: {required: 'Please enter discounted price',
                            number: 'Please enter a valid amount'},
                        people: {required: 'Please enter number of people',
                            number: 'Please enter a valid number'},
                        dtStartDate: 'Please select start date',
                        dtExpiryDate: 'Please select expiry date'
                    }
                });

                // $('.fileinput').fileinput()
                $('.uniform').uniform();
                $('#iRestaurantID').select2();

                $('#vDaysAllow').multiselect({
                    includeSelectAllOption: true,
                    selectAllText: 'All Days!',
                    selectAllValue: 'all'
                });

                $datepicker1.datepicker({
                    minDate: 0,
                    changeMonth: true,
                    changeYear: true,
                    onClose: function(selectedDate) {
                        $("#dtExpiryDate").datepicker("option", "minDate", selectedDate);
                    },
                    onSelect: function(dateText, inst) {
                        var date = $(this).datepicker('getDate');
                        _minDay = date.getDay();
                        dayShowHide();
                    }
                });
                $datepicker2.datepicker({
                    changeMonth: true,
                    changeYear: true,
                    onSelect: function(dateText, inst) {
                        var date = $(this).datepicker('getDate');
                        _maxDay = date.getDay();
                        dayShowHide();
                    }
                });

                var _URL = window.URL || window.webkitURL;
                $("#vOfferImage").change(function(e) {
                    var $file = $(this);
                    var fileExt = ($file.val()).split('.').pop().toUpperCase();

                    var image, file;
                    if (file = this.files[0]) {
                        image = new Image();
                        image.onload = function() {
                            //alert(this.width + ' ' + this.height);
                            if (fileExt == 'JPG' || fileExt == 'JPEG' || fileExt == 'PNG') {

                            } else {
                                alert('Please upload valid image type.');
                                //$file.replaceWith($file.val('').clone(true));
                                $('#removebtn').trigger('click');
                            }
                            // alert("The image width is " + this.width + " and image height is " + this.height);
                        };
                        image.src = _URL.createObjectURL(file);
                    }
                });
                document.getElementById('removepic').value = 0;

                $(document).on('click', '#removebtn', function(event) {
                    $("#removepic").val('1');
                });
                $(document).on('click', '.select_pic', function(event) {
                    $("#removepic").val('0');
                });

                $('.uniform').uniform();
            });

            function dayShowHide() {
                /*
                 * RESET DROPDOWN
                 */
                $('.multiselect.dropdown-toggle.btn.btn-default').attr('title', '').html('None selected <b class="caret"></b>');
                $('ul.multiselect-container.dropdown-menu li').each(function() {
                    var $li = $(this);
                    if (!$li.hasClass('multiselect-item')) {
                        var $chk = $li.find('input[type="checkbox"]');
                        $chk.attr({checked: false}).removeAttr('disabled');
                        $li.removeClass('active').show();

                    }
                });

                if (_minDay != -1 && _maxDay != -1) {
                    var disallow = [];
                    var fromDate = $datepicker1.datepicker('getDate');
                    var toDate = $datepicker2.datepicker('getDate');
                    // date difference in millisec
                    var diff = new Date(toDate - fromDate);
                    // date difference in days
                    var days = diff / 1000 / 60 / 60 / 24;
                    if (days < 6) {
                        for (var i = _minDay; i <= 6; i++) {
                            if (i >= _minDay)
                                disallow.push(i);
                        }
                        if (_maxDay > _minDay) {
                            for (var i = _minDay; i <= _maxDay; i++) {
                                if (i <= _maxDay)
                                    disallow.push(i);
                            }
                        } else {
                            for (var i = 0; i <= _maxDay; i++) {
                                if (i <= _maxDay)
                                    disallow.push(i);
                            }
                        }
                        var uniq = unique(disallow);
                        uniq.sort();
                        //console.log(uniq);
                        var return_arr = [];
                        for (var i = 0; i < 7; i++) {
                            if (!(uniq.indexOf(i) >= 0))
                                return_arr.push(i);
                        }
                        disallow = return_arr;
                    } else {
                        disallow = [];
                    }

                    //alert('in')

                    /*
                     * GOING TO HIDE DROP DOWN LIST ~LI~
                     */
                    var inc = 0;
                    $('ul.multiselect-container.dropdown-menu li').each(function() {
                        var $li = $(this);
                        if (!$li.hasClass('multiselect-item')) {
                            //console.log(inc + ' >> ' + disallow.indexOf(inc));
                            if (disallow.indexOf(inc) >= 0) {
                                var $chk = $li.find('input[type="checkbox"]');
                                $chk.attr({checked: false, disabled: 'disabled'});
                                //console.log($li);
                                $li.hide();
                            }
                            inc++;
                        }
                    });
                }
            }

            function unique(array) {
                return array.filter(function(el, index, arr) {
                    return index == arr.indexOf(el);
                });
            }
        </script>
    </body>
</html>