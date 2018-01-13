<?php
$headerData = $this->headerlib->data();
if (isset($getRestaurantData) && $getRestaurantData != '')
    extract($getRestaurantData);


//print_r($getRestaurantData);

$form_attr = array(
    'name' => 'restaurant-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);

$email = array(
    'name' => 'vEmail',
    'id' => 'vEmail',
    "required" => "required",
    'placeholder' => 'Please provide email',
    "data-errortext" => "This is restaurant's email address!",
    'value' => (isset($vEmail) && $vEmail != '') ? $vEmail : '',
    'type' => 'email',
    'class' => 'form-control maxwidth500'
);
if ((isset($vEmail) && $vEmail != '')) {
    $email['readonly'] = 'readonly';
}

$secondaryEmail = array(
    'name' => 'vEmailSecondary',
    'id' => 'vEmailSecondary',
    'placeholder' => 'Please provide secondary email',
    "data-errortext" => "This is restaurant's secondary email address!",
    'value' => (isset($vEmailSecondary) && $vEmailSecondary != '') ? $vEmailSecondary : '',
    'type' => 'email',
    'class' => 'form-control maxwidth500'
);

$password = array(
    'name' => 'vPassword',
    'id' => 'vPassword',
    "required" => "required",
    'minlength' => '5',
    'placeholder' => 'Please provide Password',
    "data-errortext" => "This is restaurant's password!",
    'value' => '',
    'type' => 'password',
    'class' => 'form-control maxwidth500'
);
$confirm_password = array(
    'name' => 'confirm_password',
    'id' => 'confirm_password',
    "required" => "required",
    'placeholder' => 'Please Confirm Password',
    "data-errortext" => "This is restaurant's password!",
    'value' => '',
    'type' => 'password',
    'minlength' => '5',
    'equalTo' => "#password",
    'class' => 'form-control maxwidth500'
);

$restaurantname = array(
    'name' => 'vRestaurantName',
    'id' => 'vRestaurantName',
    'placeholder' => 'Please provide Resturant name',
    'value' => (isset($vRestaurantName) && $vRestaurantName != '') ? $vRestaurantName : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$cityname =
        array(
            'name' => 'vCityName',
            'id' => 'vCityName',
            'placeholder' => 'Please provide City name',
            'value' => (isset($vCityName) && $vCityName != '') ? $vCityName : '',
            'type' => 'text',
            'class' => 'form-control maxwidth500'
);
$state = array(
    'name' => 'vStateName',
    'id' => 'vStateName',
    'placeholder' => 'Please provide State name',
    'value' => (isset($vStateName) && $vStateName != '') ? $vStateName : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$country = array(
    'name' => 'vCountryName',
    'id' => 'vCountryName',
    'placeholder' => 'Please provide Country name',
    'value' => (isset($vCountryName) && $vCountryName != '') ? $vCountryName : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$contact_number1 = $contact_number2 = $contact_number3 = $contact_number4 = NULL;
if (isset($vContactNo) && $vContactNo != '') {
    $vContactNo = explode(',', $vContactNo);

    if (isset($vContactNo[0]))
        $contact_number1 = $vContactNo[0];
    if (isset($vContactNo[1]))
        $contact_number2 = $vContactNo[1];
    if (isset($vContactNo[2]))
        $contact_number3 = $vContactNo[2];
    if (isset($vContactNo[3]))
        $contact_number4 = $vContactNo[3];
}
$contactno1 = array(
    'name' => 'vContactNo1',
    'id' => 'vContactNo1',
    'placeholder' => 'Please provide Contact no-1',
    'value' => $contact_number1,
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$contactno2 = array(
    'name' => 'vContactNo2',
    'id' => 'vContactNo2',
    'placeholder' => 'Please provide Contact no-2',
    'value' => $contact_number2,
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$contactno3 = array(
    'name' => 'vContactNo3',
    'id' => 'vContactNo3',
    'placeholder' => 'Please provide Contact no-3',
    'value' => $contact_number3,
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$contactno4 = array(
    'name' => 'vContactNo4',
    'id' => 'vContactNo4',
    'placeholder' => 'Please provide Contact no-4',
    'value' => $contact_number4,
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$FbLink = array(
    'name' => 'vFbLink',
    'id' => 'vFbLink',
    'placeholder' => 'Please provide Facebook Link',
    'value' => (isset($vFbLink) && $vFbLink != '') ? $vFbLink : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);


$instaLink = array(
    'name' => 'vInstagramLink',
    'id' => 'vInstagramLink',
    'placeholder' => 'Please provide Instagram Link',
    'value' => (isset($vInstagramLink) && $vInstagramLink != '') ? $vInstagramLink : '',
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
    "action" => "backoffice.restaurantedit"
);
$hiddenaddattr = array(
    "action" => "backoffice.restaurantadd"
);
$restaurant_id = array(
    "iRestaurantID" => (isset($iRestaurantID) && $iRestaurantID != '') ? $iRestaurantID : ''
);
$submit_attr = array(
    'class' => 'submit btn btn-primary marginright20',
    'value' => "$ACTION_LABEL Restaurant",
    'type' => 'submit'
);
$cancel_attr = array(
    'class' => 'btn btn-inverse ',
    'value' => "Reset",
    'type' => 'reset'
);

$website = array(
    'name' => 'vWebsite',
    'id' => 'vWebsite',
    'placeholder' => 'Please provide Website name',
    'value' => (isset($vWebsite) && $vWebsite != '') ? $vWebsite : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);


$monday_theme = array(
    'name' => 'vMondayTheme',
    'id' => 'vMondayTheme',
    'placeholder' => 'Please provide Monday theme',
    'value' => (isset($vMondayTheme) && $vMondayTheme != '') ? $vMondayTheme : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$thuesday_theme = array(
    'name' => 'vThuesdayTheme',
    'id' => 'vThuesdayTheme',
    'placeholder' => 'Please provide Thuesday theme',
    'value' => (isset($vThuesdayTheme) && $vThuesdayTheme != '') ? $vThuesdayTheme : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$wednesday_theme = array(
    'name' => 'vWednesdayTheme',
    'id' => 'vWednesdayTheme',
    'placeholder' => 'Please provide Wednesday theme',
    'value' => (isset($vWednesdayTheme) && $vWednesdayTheme != '') ? $vWednesdayTheme : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$thursday_theme = array(
    'name' => 'vThursdayTheme',
    'id' => 'vThursdayTheme',
    'placeholder' => 'Please provide Thursday theme',
    'value' => (isset($vThursdayTheme) && $vThursdayTheme != '') ? $vThursdayTheme : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$friday_theme = array(
    'name' => 'vFridayTheme',
    'id' => 'vFridayTheme',
    'placeholder' => 'Please provide Friday theme',
    'value' => (isset($vFridayTheme) && $vFridayTheme != '') ? $vFridayTheme : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$saturday_theme = array(
    'name' => 'vSaturdayTheme',
    'id' => 'vSaturdayTheme',
    'placeholder' => 'Please provide Saturday theme',
    'value' => (isset($vSaturdayTheme) && $vSaturdayTheme != '') ? $vSaturdayTheme : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$sunday_theme = array(
    'name' => 'vSundayTheme',
    'id' => 'vSundayTheme',
    'placeholder' => 'Please provide Sunday theme',
    'value' => (isset($vSundayTheme) && $vSundayTheme != '') ? $vSundayTheme : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);


$pic = (isset($vRestaurantLogo) && $vRestaurantLogo != '') ? $vRestaurantLogo : '';
$uid = (isset($iRestaurantID) && $iRestaurantID != '') ? $iRestaurantID : '';
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
            .dropdown-menu li a{
                line-height: 22px;
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
                                                <i class="fa fa-home"></i>
                                                <a href="<?= BASEURL ?>">Home</a>
                                            </li>
                                            <li>Profile</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Profile</h3>
                                        </div>
                                        <div class="description">Name, Email , Password and more</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-bars"></i><?php echo $ACTION_LABEL . " Restaurant"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <!--                           <a href="#box-config" data-toggle="modal" class="config">
                                                  <i class="fa fa-cog"></i>
                                                </a> -->

                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                                <!--  <a href="javascript:void(0);" class="remove">
                                                   <i class="fa fa-times"></i>
                                                 </a> -->
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("restaurant/add", $form_attr);
                                            if (isset($iRestaurantID) && $iRestaurantID != '') {
                                                echo form_hidden($restaurant_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Restaurant Category<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="iCategoryID[]" id="iCategoryID" class="maxwidth500 col-lg-12" multiple>
                                                        <?php
                                                        $category = $this->restaurant_model->getCategoryDataAll();
                                                        $cat_array = array();
                                                        foreach ($getCategoryData as $key1 => $cat_value) {
                                                            array_push($cat_array, $cat_value['iCategoryID']);
                                                        }
//print_r($cat_value);
                                                        foreach ($category as $key => $value) {
                                                            //print_r($getCategoryData);
                                                            if (!empty($getCategoryData)) {

                                                                if (in_array($value['iCategoryID'], $cat_array)) {
                                                                    echo '<option value="' . $value['iCategoryID'] . '" selected="selected">' . $value['vCategoryName'] . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $value['iCategoryID'] . '">' . $value['vCategoryName'] . '</option>';
                                                                }
                                                            } else {
                                                                echo '<option value="' . $value['iCategoryID'] . '">' . $value['vCategoryName'] . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>

                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Cuisine Category<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="iCuisineID[]" id="iCuisineID" class="maxwidth500 col-lg-12" multiple>
                                                        <?php
                                                        $cuisine = $this->restaurant_model->getCuisineDataAll();
                                                        $cui_array = array();
                                                        foreach ($getCuisineData as $key1 => $cat_value) {
                                                            array_push($cui_array, $cat_value['iCuisineID']);
                                                        }
//print_r($cat_value);
                                                        foreach ($cuisine as $key => $value) {
                                                            //print_r($getCategoryData);
                                                            if (!empty($getCuisineData)) {

                                                                if (in_array($value['iCuisineID'], $cui_array)) {
                                                                    echo '<option value="' . $value['iCuisineID'] . '" selected="selected">' . $value['vCuisineName'] . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $value['iCuisineID'] . '">' . $value['vCuisineName'] . '</option>';
                                                                }
                                                            } else {
                                                                echo '<option value="' . $value['iCuisineID'] . '">' . $value['vCuisineName'] . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>

                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Restaurant Facility</label>
                                                <div class="col-sm-9">
                                                    <select name="iFacilityID[]" id="iFacilityID" class="maxwidth500 col-lg-12" multiple>
                                                        <?php
                                                        $facility = $this->restaurant_model->getFacilityDataAll();
                                                        $fac_array = array();
                                                        foreach ($getFacilityData as $key1 => $fac_value) {
                                                            array_push($fac_array, $fac_value['iFacilityID']);
                                                        }
//print_r($cat_value);
                                                        foreach ($facility as $key => $value) {
                                                            //print_r($getCategoryData);
                                                            if (!empty($getFacilityData)) {

                                                                if (in_array($value['iFacilityID'], $fac_array)) {
                                                                    echo '<option value="' . $value['iFacilityID'] . '" selected="selected">' . $value['vFacilityName'] . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $value['iFacilityID'] . '">' . $value['vFacilityName'] . '</option>';
                                                                }
                                                            } else {
                                                                echo '<option value="' . $value['iFacilityID'] . '">' . $value['vFacilityName'] . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>

                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Restaurant Music</label>
                                                <div class="col-sm-9">
                                                    <select name="iMusicID[]" id="iMusicID" class="maxwidth500 col-lg-12" multiple>
                                                        <?php
                                                        $musics = $this->restaurant_model->getMusicDataAll();
                                                        $music_array = array();
                                                        foreach ($getMusicData as $key1 => $mus_value) {
                                                            array_push($music_array, $mus_value['iMusicID']);
                                                        }
//print_r($cat_value);
                                                        foreach ($musics as $key => $value) {
                                                            //print_r($getCategoryData);
                                                            if (!empty($getMusicData)) {

                                                                if (in_array($value['iMusicID'], $music_array)) {
                                                                    echo '<option value="' . $value['iMusicID'] . '" selected="selected">' . $value['vMusicName'] . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $value['iMusicID'] . '">' . $value['vMusicName'] . '</option>';
                                                                }
                                                            } else {
                                                                echo '<option value="' . $value['iMusicID'] . '">' . $value['vMusicName'] . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>

                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Restaurant Name<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($restaurantname); ?>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Email<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($email); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Secondary Email</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($secondaryEmail); ?>
                                                </div>
                                            </div>

                                            <?php if (@$getRestaurantData != ''): ?>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label">Change Password</label>
                                                    <div class="col-sm-9 center maxwidth500">
                                                        <button id="passchange" class="btn btn-light-grey">Change Password</button>
                                                    </div>
                                                </div>
                                            <?php endif ?>
                                            <div class="form-group <?= (@$getRestaurantData != '') ? 'passtohide' : '' ?>">
                                                <label class="col-sm-3 control-label">Password<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($password); ?>
                                                </div>
                                            </div>
                                            <div class="form-group <?= (@$getRestaurantData != '') ? 'passtohide' : '' ?>">
                                                <label class="col-sm-3 control-label"> Confirm Password<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($confirm_password); ?>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Map </label>
                                                <div class="col-sm-6">

                                                    <div id="mapCanvas" style="display:block; height: 300px"></div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Area Details</label>
                                                <div class="col-sm-9" id="infoPanel">
                                                    <b>Current Address position:</b>
                                                     <div id="info"></div>
                                                      <input type="hidden" name="vLat" id="lat" value="<?= (isset($vLat) && $vLat != '') ? $vLat : '23.0300' ?>">
                                                      <input type="hidden" name="vLog" id="log" value="<?= (isset($vLog) && $vLog != '') ? $vLog : '72.4000' ?>">

                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Address</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control maxwidth500"  id="address" name="tAddress"><?= (isset($tAddress) && $tAddress != '') ? $tAddress : '' ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">City Name</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($cityname); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">State Name</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($state); ?>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Country Name</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($country); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Restaurant Logo</label>
                                                <div class="col-sm-9">


                                                    <?php
                                                    if ($pic != '' && $uid != '') {
                                                        $pic_str = '<img src="' . DOMAIN_URL . '/images/restaurant/' . $uid . '/thumb/' . $pic . '">';
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
                                                        <div>
                                                            <span class="btn btn-default btn-file">
                                                                <span class="<?php echo $sel_text ?> select_pic" data-trigger="fileinput"  >Select image</span>
                                                                <span class="<?php echo $change_text ?> select_pic" data-trigger="fileinput">Change</span>
                                                                <input type="file" name="vRestaurantLogo"></span>
                                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput" id="removebtn">Remove</a>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tSpecialty">Specialty</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control maxwidth500" id="tSpecialty" name="tSpecialty"><?= (isset($tSpecialty) && $tSpecialty != '') ? $tSpecialty : '' ?></textarea>
                                                </div>
                                            </div>

                                            <?php
                                            $vDaysOpenValue = array();
                                            if (isset($vDaysOpen)) {
                                                $vDaysOpenValue = explode(',', $vDaysOpen);
                                            }
                                            ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="vDaysOpen">Days Open</label>
                                                <div class="col-sm-9">
                                                    <select name="vDaysOpen[]" id="vDaysOpen" multiple="multiple">
                                                        <option value="1" <?= in_array('1', $vDaysOpenValue) ? 'selected="selected"' : NULL; ?>>Monday</option>
                                                        <option value="2" <?= in_array('2', $vDaysOpenValue) ? 'selected="selected"' : NULL; ?>>Tuesday</option>
                                                        <option value="3" <?= in_array('3', $vDaysOpenValue) ? 'selected="selected"' : NULL; ?>>Wednesday</option>
                                                        <option value="4" <?= in_array('4', $vDaysOpenValue) ? 'selected="selected"' : NULL; ?>>Thursday</option>
                                                        <option value="5" <?= in_array('5', $vDaysOpenValue) ? 'selected="selected"' : NULL; ?>>Friday</option>
                                                        <option value="6" <?= in_array('6', $vDaysOpenValue) ? 'selected="selected"' : NULL; ?>>Saturday</option>
                                                        <option value="7" <?= in_array('7', $vDaysOpenValue) ? 'selected="selected"' : NULL; ?>>Sunday</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Open & Closing Time</label>
                                                <div class="col-sm-9">
                                                    <div class="row">
                                                        <div class="col-sm-9">
                                                            <div id="time-range"></div>
                                                        </div>

                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-9">
                                                            <label for="time-value">Time range:</label>
                                                            <input type="text" id="time-value" readonly style="border:0; color:#f6931f; font-weight:bold;">
                                                            <input type="hidden" id="iMinTime" name="iMinTime" value="<?= (isset($iMinTime) && $iMinTime != '') ? $iMinTime : '6'; ?>" readonly style="border:0; color:#f6931f; font-weight:bold;">
                                                            <input type="hidden" id="iMaxTime" name="iMaxTime" value="<?= (isset($iMaxTime) && $iMaxTime != '') ? $iMaxTime : '18'; ?>" readonly style="border:0; color:#f6931f; font-weight:bold;">
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Price</label>
                                                <div class="col-sm-9">
                                                    <div class="row">
                                                        <div class="col-sm-9">
                                                            <div id="slider-range"></div>
                                                        </div>

                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-9">
                                                            <label for="slider-value">Price range:</label>
                                                            <input type="text" id="slider-value" readonly style="border:0; color:#f6931f; font-weight:bold;">
                                                            <input type="hidden" id="iMinPrice" name="iMinPrice" value="<?= (isset($iMinPrice) && $iMinPrice != '') ? $iMinPrice : '0'; ?>" readonly style="border:0; color:#f6931f; font-weight:bold;">
                                                            <input type="hidden" id="iMaxPrice" name="iMaxPrice" value="<?= (isset($iMaxPrice) && $iMaxPrice != '') ? $iMaxPrice : '2000'; ?>" readonly style="border:0; color:#f6931f; font-weight:bold;">
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Contact No 1</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($contactno1); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Contact No 2</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($contactno2); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Contact No 3</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($contactno3); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Contact No 4</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($contactno4); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Description</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control maxwidth500" name="tDescription"><?= (isset($tDescription) && $tDescription != '') ? $tDescription : '' ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Facebook Link</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($FbLink); ?>
                                                </div>
                                            </div>


                                            <div class="form-group margintop10">
                                                <label class="col-sm-3 control-label">Instagram Link</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($instaLink); ?>
                                                </div>
                                            </div>



                                            <div class="form-group">

                                                <div class="col-sm-12">

                                                        <div class="box border lite">
                                                            <div class="box-title">
                                                                <h4><i class=""></i>Restaurant Theme</h4>
                                                                <div class="tools">
<!--                                                                     <a class="config" data-toggle="modal" href="#box-config">
    <i class="fa fa-cog"></i>
</a>

<a class="collapse" href="javascript:;">
    <i class="fa fa-chevron-up"></i>
</a> -->

                                                                </div>
                                                            </div>
                                                            <div class="box-body center" style="display: block;">
                                                                <p>

                                                                    <div class="form-group">
                                                                        <label class="col-sm-3 control-label">Monday</label>
                                                                        <div class="col-sm-9">
                                                                             <?php echo form_input($monday_theme); ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="col-sm-3 control-label">Thuesday</label>
                                                                        <div class="col-sm-9">
                                                                             <?php echo form_input($thuesday_theme); ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="col-sm-3 control-label">Wednesday</label>
                                                                        <div class="col-sm-9">
                                                                             <?php echo form_input($wednesday_theme); ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="col-sm-3 control-label">Thursday</label>
                                                                        <div class="col-sm-9">
                                                                             <?php echo form_input($thursday_theme); ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="col-sm-3 control-label">Friday</label>
                                                                        <div class="col-sm-9">
                                                                             <?php echo form_input($friday_theme); ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="col-sm-3 control-label">Saturday</label>
                                                                        <div class="col-sm-9">
                                                                             <?php echo form_input($saturday_theme); ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="col-sm-3 control-label">Sunday</label>
                                                                        <div class="col-sm-9">
                                                                             <?php echo form_input($sunday_theme); ?>
                                                                        </div>
                                                                    </div>
                                                                </p>
                                                            </div>

                                                        </div>
                                                </div>
                                            </div>



                                            <br>
                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <?php echo form_input($submit_attr); ?>
                                                    <?php echo form_input($cancel_attr); ?>
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

        <script src="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.css" type="text/css" media="screen" />

        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.css" type="text/css" media="screen" />

        <script src="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript" charset="utf-8"></script>


        <script src="http://maps.google.com/maps/api/js?sensor=false"></script>

        <script>
                var flag = 0;

            jQuery(document).ready(function()
            {
                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                $("#validateForm").validate({
                    rules: {
                        vEmail: {
                            required: true,
                            email: true
                        },
                        vPassword: {
                            required: true,
                            minlength: 5
                        },
                        confirm_password: {
                            required: true,
                            minlength: 5,
                            equalTo: "#vPassword"
                        },
                    },
                    messages: {
                        vEmail: "Please enter a Email",
                        vPassword: {
                            required: "Please provide a password",
                            minlength: "Your password must be at least 5 characters long"
                        },
                        confirm_password: {
                            required: "Please provide a password",
                            minlength: "Your password must be at least 5 characters long",
                            equalTo: "Please enter the same password as above"
                        },
                    }
                });

                // $('.fileinput').fileinput()
                $('.uniform').uniform();

                $('#vDaysOpen').multiselect({
                    includeSelectAllOption: true,
                    selectAllText: 'All Days!',
                    selectAllValue: 'all'
                });

                $('#iFacilityID, #iMusicID, #iCuisineID, #iCategoryID').select2();

                $("#time-range").slider({
                    range: true,
                    min: 0,
                    max: 23,
                    step: 1,
                    values: [6, 18],
                    slide: function(event, ui) {
                        $("#time-value").val("" + ui.values[0] + " - " + ui.values[1]);
                        $("#iMinTime").val(ui.values[0]);
                        $("#iMaxTime").val(ui.values[1]);
                        // $("#slider-range .ui-slider-handle:first").tooltip({title:ui.values[ 0 ] ,trigger:"manual"}).tooltip("show");
                        // $("#slider-range .ui-slider-handle:eq(1)").tooltip({title:ui.values[ 1 ],trigger:"manual"}).tooltip("show");
                    }
                });


                var iMinTime = parseInt("<?= (isset($iMinTime) && $iMinTime != '') ? $iMinTime : -1; ?>");
                var iMaxTime = parseInt("<?= (isset($iMaxTime) && $iMaxTime != '') ? $iMaxTime : -1; ?>");

                if (iMinTime != -1 && iMaxTime != -1) {
                    $("#time-range").slider({
                        values: [iMinTime,iMaxTime],
                        slide: function(event, ui) {
                            $("#time-value").val("" + ui.values[0] + " - " + ui.values[1]);
                            $("#iMinTime").val(ui.values[0]);
                            $("#iMaxTime").val(ui.values[1]);
                            // $("#slider-range .ui-slider-handle:first").tooltip({title:ui.values[ 0 ] ,trigger:"manual"}).tooltip("show");
                            // $("#slider-range .ui-slider-handle:eq(1)").tooltip({title:ui.values[ 1 ],trigger:"manual"}).tooltip("show");
                        }
                    });
                    $("#time-value").val("" + iMinTime + " - " + iMaxTime);
                } else {
                    $("#time-value").val("" + 6 + " - " + 18);
                }

                $("#slider-range").slider({
                    range: true,
                    min: 0,
                    max: 20000,
                    step: 50,
                    values: [0, 2000],
                    slide: function(event, ui) {
                        $("#slider-value").val("" + ui.values[0] + " - " + ui.values[1]);
                        $("#iMinPrice").val(ui.values[0]);
                        $("#iMaxPrice").val(ui.values[1]);
                        // $("#slider-range .ui-slider-handle:first").tooltip({title:ui.values[ 0 ] ,trigger:"manual"}).tooltip("show");
                        // $("#slider-range .ui-slider-handle:eq(1)").tooltip({title:ui.values[ 1 ],trigger:"manual"}).tooltip("show");
                    }
                });

                var iMinPrice = parseInt("<?= (isset($iMinPrice) && $iMinPrice != '') ? $iMinPrice : -1; ?>");
                var iMaxPrice = parseInt("<?= (isset($iMaxPrice) && $iMaxPrice != '') ? $iMaxPrice : -1; ?>");

                if (iMinPrice != -1 && iMaxPrice != -1) {
                    $("#slider-range").slider({
                        values: [iMinPrice, iMaxPrice],
                        slide: function(event, ui) {
                            $("#slider-value").val("" + ui.values[0] + " - " + ui.values[1]);
                            $("#iMinPrice").val(ui.values[0]);
                            $("#iMaxPrice").val(ui.values[1]);
                            // $("#slider-range .ui-slider-handle:first").tooltip({title:ui.values[ 0 ] ,trigger:"manual"}).tooltip("show");
                            // $("#slider-range .ui-slider-handle:eq(1)").tooltip({title:ui.values[ 1 ],trigger:"manual"}).tooltip("show");
                        }
                    });
                    $("#slider-value").val("" + iMinPrice + " - " + iMaxPrice);
                } else {
                    $("#slider-value").val("" + 0 + " - " + 2000);
                }

                //    $("#slider-range .ui-slider-handle:first").tooltip({title:"" + $( "#slider-range" ).slider( "values", 0 ) ,trigger:"manual"}).tooltip("show");
                //  $("#slider-range .ui-slider-handle:eq(1)").tooltip({title:"" + $( "#slider-range" ).slider( "values", 1 ),trigger:"manual"}).tooltip("show");

                //$("#amount").val("" + $("#slider-range").slider("values", 0) + " - " + $("#slider-range").slider("values", 1));

                //$("#vMinAmount").val($("#slider-range").slider("values", 0));
                //$("#vMaxAmount").val($("#slider-range").slider("values", 1));

            });


            $(document).on('click', '#removebtn', function(event) {
                $("#removepic").val('1');
            });
            $(document).on('click', '.select_pic', function(event) {
                $("#removepic").val('0');
            });



    var geocoder = new google.maps.Geocoder();
    function geocodePosition(pos) {
        //console.log(pos);
        geocoder.geocode({
        latLng: pos
        }, function(responses) {
        //  console.log(responses);
        if (responses && responses.length > 0)
            {
                if (flag == 0)
                    {
                        flag++;
                    }
                    else
                    {
                        updateMarkerAddress(responses[0].formatted_address,responses[0].address_components);
                    }
            }
        else
            {
                updateMarkerAddress('Cannot determine address at this location.','');
            }
        });
    }

    function updateMarkerStatus(str) {
        document.getElementById('markerStatus').innerHTML = str;
    }

    function updateMarkerPosition(latLng) {
        document.getElementById('info').innerHTML = [
        latLng.lat(),
        latLng.lng()
        ].join(', ');
        document.getElementById('lat').value = latLng.lat();
        document.getElementById('log').value = latLng.lng();

    }
     infowindow = new google.maps.InfoWindow({
    'size': new google.maps.Size(292, 120)
    });


    function updateMarkerAddress(str,str1) {
        $("#address").val(str);
    //document.getElementById('address').text = str;
    //$('#vAreaName').text(str1[3]['long_name']);
        $.each(str1, function(index, val) {
            var arr = val.types;
            if(arr[0] == 'route'){
                infowindow.setContent("Address : "+str+"<br/> Locality : "+val.long_name);
                //$('#vAreaName').val(val.long_name);
            }
            if(arr[0] == 'sublocality'){
                infowindow.setContent("Address : "+str+"<br/> Locality : "+val.long_name);
                //$('#vAreaName').val(val.long_name);
            }
        });
    }

    function initialize() {

        var lat = $('#lat').val();
        var log = $('#log').val();

        var latLng = new google.maps.LatLng(lat, log);
        var map = new google.maps.Map(document.getElementById('mapCanvas'), {
            zoom: 8,
            center: latLng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        var marker = new google.maps.Marker({
            position: latLng,
            title: 'Select Outlet Point',
            map: map,
            draggable: true
        });
        // Update current position info.
        updateMarkerPosition(latLng);
        geocodePosition(latLng);
        // Add dragging event listeners.
        google.maps.event.addListener(marker, 'dragstart', function() {
            //updateMarkerAddress('Dragging...');
        });
        google.maps.event.addListener(marker, 'drag', function() {
            //updateMarkerStatus('Dragging...');
            updateMarkerPosition(marker.getPosition());
        });
        google.maps.event.addListener(marker, 'dragend', function() {
            //updateMarkerStatus('Drag ended');
            geocodePosition(marker.getPosition());
        });
        google.maps.event.addListener(marker, 'click', function(e) {
            //updateMarkerStatus('Drag ended');
            //geocodePosition(marker.getPosition());
        geocoder.geocode(
                    {'latLng': e.latLng},
                    function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            if (marker) {
                                marker.setPosition(e.latLng);
                            } else {
                                marker = new google.maps.Marker({
                                position: e.latLng,
                                map: map});
                            }
                            updateMarkerAddress(results[0].formatted_address ,results[0].address_components);
                            //infowindow.setContent("Address : "+results[0].formatted_address+"<br/> Locality : ");
                            //responses[0].address_components
                            infowindow.open(map, marker);
                        }
                    }
                    });

            //openInfoWindow(geocodePosition(marker.getPosition()),marker);
        });

        var openInfoWindow = function(result, marker) {
            google.maps.fitBounds(result.geometry.viewport);
            infowindow.setContent(getAddressComponentsHtml(result.address_components));
            infowindow.open(map, marker);
        }
    }
// Onload handler to fire off the app.




    google.maps.event.addDomListener(window, 'load', initialize);


        </script>
    </body>
</html>
