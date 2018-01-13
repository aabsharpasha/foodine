<?php
$headerData = $this->headerlib->data();

if (isset($getRestaurantData) && $getRestaurantData != '')
    extract($getRestaurantData);

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 32);
//mprd($this->session);

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
$sess_userdata = $this->session->userdata;
if (($sess_userdata['ADMINTYPE'] != 1 && $sess_userdata['ADMINTYPE'] != 2) && (isset($vEmail) && $vEmail != '')) {
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

$restaurantpasscode = array(
    'name' => 'vPasscode',
    'id' => 'vPasscode',
    'value' => (isset($vPasscode) && $vPasscode != '') ? $vPasscode : '',
    'type' => 'text',
    'maxlength' => '10',
    'class' => 'form-control maxwidth500'
);

$parentCompanyName = array(
    'name' => 'vParentCompanyName',
    'id' => 'vParentCompanyName',
    'placeholder' => 'Please provide Resturant Parent Company name',
    'value' => (isset($vParentCompanyName) && $vParentCompanyName != '') ? $vParentCompanyName : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$cityname = array(
    'name' => 'vCityName',
    'id' => 'vCityName',
    'placeholder' => 'Please provide City name',
    'value' => (isset($vCityName) && $vCityName != '') ? $vCityName : '',
    'type' => 'hidden',
    'class' => 'form-control maxwidth500'
);

$statename = array(
    'name' => 'vStateName',
    'id' => 'vStateName',
    'placeholder' => 'Please provide State name',
    'value' => (isset($vStateName) && $vStateName != '') ? $vStateName : '',
    'type' => 'hidden',
    'class' => 'form-control maxwidth500'
);

$country = array(
    'name' => 'vCountryName',
    'id' => 'vCountryName',
    'placeholder' => 'Please provide Country name',
    'value' => (isset($vCountryName) && $vCountryName != '') ? $vCountryName : 'India',
    'type' => 'text',
    'readonly'=>'readonly',
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
$sms_contact_field = array(
    'name' => 'sms_contact',
    'id' => 'sms_contact',
    //'required' => 'required',
    'placeholder' => 'Please provide SMS Communication Mobile Number',
    'value' => isset($sms_contact) ? $sms_contact : '',
    'type' => 'text',
    'maxlength' => '10',
    'minlength' => '10',
    'class' => 'form-control maxwidth500'
);
$contactno1 = array(
    'name' => 'vContactNo1',
    'id' => 'vContactNo1',
    'placeholder' => 'Please provide Contact no-1',
    'value' => trim($contact_number1),
    'type' => 'text',
    //'maxlength' => '10',
    //'minlength' => '10',
    'class' => 'form-control maxwidth500'
);
$contactno2 = array(
    'name' => 'vContactNo2',
    'id' => 'vContactNo2',
    'placeholder' => 'Please provide Contact no-2',
    'value' => trim($contact_number2),
    'type' => 'text',
    //'maxlength' => '10',
    //'minlength' => '10',
    'class' => 'form-control maxwidth500'
);
$contactno3 = array(
    'name' => 'vContactNo3',
    'id' => 'vContactNo3',
    'placeholder' => 'Please provide Contact no-3',
    'value' => trim($contact_number3),
    'type' => 'text',
    //'maxlength' => '10',
    //'minlength' => '10',
    'class' => 'form-control maxwidth500'
);
$contactno4 = array(
    'name' => 'vContactNo4',
    'id' => 'vContactNo4',
    'placeholder' => 'Please provide Contact no-4',
    'value' => trim($contact_number4),
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$headManagerName = array(
    'name' => 'vheadManagerName',
    'id' => 'vheadManagerName',
    'placeholder' => 'Please provide Resturant Head Manager name',
    'value' => (isset($vheadManagerName) && $vheadManagerName != '') ? $vheadManagerName : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$headManagerEmail = array(
    'name' => 'vheadManagerEmail',
    'id' => 'vheadManagerEmail',
    'placeholder' => 'Please provide head manager email',
    "data-errortext" => "This is restaurant's Head Manager email address!",
    'value' => (isset($vheadManagerEmail) && $vheadManagerEmail != '') ? $vheadManagerEmail : '',
    'type' => 'email',
    'class' => 'form-control maxwidth500'
);

$headManagerPhone = array(
    'name' => 'vheadManagerPhone',
    'id' => 'vheadManagerPhone',
    'placeholder' => 'Please provide Contact no of Head Manager',
    'value' => (isset($vheadManagerPhone) && $vheadManagerPhone != '') ? $vheadManagerPhone : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$primRestManagerName = array(
    'name' => 'vPrimRestManagerName',
    'id' => 'vPrimRestManagerName',
    'placeholder' => 'Please provide Resturant Primary Manager name',
    'value' => (isset($vPrimRestManagerName) && $vPrimRestManagerName != '') ? $vPrimRestManagerName : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$primRestManagerEmail = array(
    'name' => 'vPrimRestManagerEmail',
    'id' => 'vPrimRestManagerEmail',
    'placeholder' => 'Please provide primary manager email',
    "data-errortext" => "This is restaurant's primary Manager email address!",
    'value' => (isset($vPrimRestManagerEmail) && $vPrimRestManagerEmail != '') ? $vPrimRestManagerEmail : '',
    'type' => 'email',
    'class' => 'form-control maxwidth500'
);

$primRestManagerPhone = array(
    'name' => 'vPrimRestManagerPhone',
    'id' => 'vPrimRestManagerPhone',
    'placeholder' => 'Please provide Contact no of Primary Manager',
    'value' => (isset($vPrimRestManagerPhone) && $vPrimRestManagerPhone != '') ? $vPrimRestManagerPhone : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$secRestManagerName = array(
    'name' => 'vSecRestManagerName',
    'id' => 'vSecRestManagerName',
    'placeholder' => 'Please provide Resturant Secondary Manager name',
    'value' => (isset($vSecRestManagerName) && $vSecRestManagerName != '') ? $vSecRestManagerName : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$secRestManagerEmail = array(
    'name' => 'vSecRestManagerEmail',
    'id' => 'vSecRestManagerEmail',
    'placeholder' => 'Please provide secondary manager email',
    "data-errortext" => "This is restaurant's secondary Manager email address!",
    'value' => (isset($vSecRestManagerEmail) && $vSecRestManagerEmail != '') ? $vSecRestManagerEmail : '',
    'type' => 'email',
    'class' => 'form-control maxwidth500'
);

$secRestManagerPhone = array(
    'name' => 'vSecRestManagerPhone',
    'id' => 'vSecRestManagerPhone',
    'placeholder' => 'Please provide Contact no of Secondary Manager',
    'value' => (isset($vSecRestManagerPhone) && $vSecRestManagerPhone != '') ? $vSecRestManagerPhone : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$thirdRestManagerName = array(
    'name' => 'vThirdRestManagerName',
    'id' => 'vThirdRestManagerName',
    'placeholder' => 'Please provide Resturant Third Manager name',
    'value' => (isset($vThirdRestManagerName) && $vThirdRestManagerName != '') ? $vThirdRestManagerName : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$thirdRestManagerEmail = array(
    'name' => 'vThirdRestManagerEmail',
    'id' => 'vThirdRestManagerEmail',
    'placeholder' => 'Please provide Third manager email',
    "data-errortext" => "This is restaurant's Third Manager email address!",
    'value' => (isset($vThirdRestManagerEmail) && $vThirdRestManagerEmail != '') ? $vThirdRestManagerEmail : '',
    'type' => 'email',
    'class' => 'form-control maxwidth500'
);

$thirdRestManagerPhone = array(
    'name' => 'vThirdRestManagerPhone',
    'id' => 'vThirdRestManagerPhone',
    'placeholder' => 'Please provide Contact no of Third Manager',
    'value' => (isset($vThirdRestManagerPhone) && $vThirdRestManagerPhone != '') ? $vThirdRestManagerPhone : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$noManagers = array(
    'name' => 'tNoManagers',
    'id' => 'tNoManagers',
    'placeholder' => 'Please provide No. of Managers',
    'value' => (isset($tNoManagers) && $tNoManagers != '') ? $tNoManagers : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$waitStaff = array(
    'name' => 'tWaitStaff',
    'id' => 'tWaitStaff',
    'placeholder' => 'Please provide number of Wait Staff',
    'value' => (isset($tWaitStaff) && $tWaitStaff != '') ? $tWaitStaff : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$peoplePerTable = array(
    'name' => 'peoplePerTable',
    'id' => 'peoplePerTable',
    'placeholder' => 'Please provide number of People Per Table',
    'value' => (isset($peoplePerTable) && $peoplePerTable != '') ? $peoplePerTable : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

/*$iMinPerson = array(
    'name' => 'iMinPerson',
    'id' => 'iMinPerson',
    'placeholder' => 'Please provide minimum number of People',
    'value' => (isset($iMinPerson) && $iMinPerson != '') ? $iMinPerson : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$iMaxPerson = array(
    'name' => 'iMaxPerson',
    'id' => 'iMaxPerson',
    'placeholder' => 'Please provide Maximum number of People',
    'value' => (isset($iMaxPerson) && $iMaxPerson != '') ? $iMaxPerson : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
); */

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
    'class' => 'submit btn-sm btn btn-primary marginright20',
    'value' => ($ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Restaurant"),
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

$monday_theme_m = array(
    'name' => 'vMondayThemeM',
    'id' => 'vMondayThemeM',
    'placeholder' => 'Please provide Monday music theme',
    'value' => (isset($vMondayThemeM) && $vMondayThemeM != '') ? $vMondayThemeM : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$thuesday_theme_m = array(
    'name' => 'vThuesdayThemeM',
    'id' => 'vThuesdayThemeM',
    'placeholder' => 'Please provide Thuesday music theme',
    'value' => (isset($vThuesdayThemeM) && $vThuesdayThemeM != '') ? $vThuesdayThemeM : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$wednesday_theme_m = array(
    'name' => 'vWednesdayThemeM',
    'id' => 'vWednesdayThemeM',
    'placeholder' => 'Please provide Wednesday music theme',
    'value' => (isset($vWednesdayThemeM) && $vWednesdayThemeM != '') ? $vWednesdayThemeM : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$thursday_theme_m = array(
    'name' => 'vThursdayThemeM',
    'id' => 'vThursdayThemeM',
    'placeholder' => 'Please provide Thursday music theme',
    'value' => (isset($vThursdayThemeM) && $vThursdayThemeM != '') ? $vThursdayThemeM : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$friday_theme_m = array(
    'name' => 'vFridayThemeM',
    'id' => 'vFridayThemeM',
    'placeholder' => 'Please provide Friday music theme',
    'value' => (isset($vFridayThemeM) && $vFridayThemeM != '') ? $vFridayThemeM : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$saturday_theme_m = array(
    'name' => 'vSaturdayThemeM',
    'id' => 'vSaturdayThemeM',
    'placeholder' => 'Please provide Saturday music theme',
    'value' => (isset($vSaturdayThemeM) && $vSaturdayThemeM != '') ? $vSaturdayThemeM : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$sunday_theme_m = array(
    'name' => 'vSundayThemeM',
    'id' => 'vSundayThemeM',
    'placeholder' => 'Please provide Sunday music theme',
    'value' => (isset($vSundayThemeM) && $vSundayThemeM != '') ? $vSundayThemeM : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);


$pic = (isset($vRestaurantLogo) && $vRestaurantLogo != '') ? $vRestaurantLogo : '';
$picM = (isset($vRestaurantMobLogo) && $vRestaurantMobLogo != '') ? $vRestaurantMobLogo : '';
$picL = (isset($vRestaurantListing) && $vRestaurantListing != '') ? $vRestaurantListing : '';
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
                                                <i class="fa fa-tachometer"></i>
                                                <a href="<?= BASEURL ?>">Home</a>
                                            </li>
                                            <li>Restaurant</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Restaurant</h3>
                                        </div>
                                        <div class="description">Add / Edit Restaurant</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Restaurant"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
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
                                                    <select name="iCategoryID[]" id="iCategoryID" class="maxwidth500 col-lg-12 required" multiple>
                                                        <?php
                                                        $category = $this->restaurant_model->getCategoryDataAll();
                                                        //print_r($getCategoryData);
                                                        $cat_array = array();
                                                        if (isset($getCategoryData) && !empty($getCategoryData)) {
                                                            foreach ($getCategoryData as $key1 => $cat_value) {
                                                                array_push($cat_array, $cat_value['iCategoryID']);
                                                            }
                                                        }
                                                        //print_r($cat_array);

                                                        foreach ($category as $key => $value) {
                                                            //print_r($value);
                                                            if (isset($getCategoryData) && !empty($getCategoryData)) {
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
                                                <label class="col-sm-3 control-label">Major Cuisine Category<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="iCuisineID[]" id="iCuisineID" class="maxwidth500 col-lg-12 required" multiple>
                                                        <?php
                                                        $cuisine = $this->restaurant_model->getCuisineDataAll();
                                                        $cui_array = array();
                                                        if (isset($getCuisineData)) {
                                                            foreach ($getCuisineData as $key1 => $cat_value) {
                                                                array_push($cui_array, $cat_value['iCuisineID']);
                                                            }
                                                        }
                                                        //print_r($cat_value);
                                                        foreach ($cuisine as $key => $value) {
                                                            //print_r($getCategoryData);
                                                            if (isset($getCuisineData) && !empty($getCuisineData)) {
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
                                                <label class="col-sm-3 control-label">Minor Cuisine Category</label>
                                                <div class="col-sm-9">
                                                    <select name="iCuisineIDM[]" id="iCuisineIDM" class="maxwidth500 col-lg-12" multiple>
                                                        <?php
                                                        $cuisine = $this->restaurant_model->getCuisineDataAll();
                                                        $cui_array = array();
                                                        if (isset($getMinorCuisineData)) {
                                                            foreach ($getMinorCuisineData as $key1 => $cat_value) {
                                                                array_push($cui_array, $cat_value['iCuisineID']);
                                                            }
                                                        }
                                                        foreach ($cuisine as $key => $value) {
                                                            //print_r($getCategoryData);
                                                            if (isset($getMinorCuisineData) && !empty($getMinorCuisineData)) {
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
                                                <label class="col-sm-3 control-label">Restaurant Facility<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="iFacilityID[]" id="iFacilityID" class="maxwidth500 col-lg-12 required" multiple>
                                                        <?php
                                                        $facility = $this->restaurant_model->getFacilityDataAll();
                                                        $fac_array = array();
                                                        if (isset($getFacilityData)) {
                                                            foreach ($getFacilityData as $key1 => $fac_value) {
                                                                array_push($fac_array, $fac_value['iFacilityID']);
                                                            }
                                                        }
                                                        //print_r($cat_value);
                                                        foreach ($facility as $key => $value) {
                                                            //print_r($getCategoryData);
                                                            if (isset($getFacilityData) && !empty($getFacilityData)) {
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
                                                        if (isset($getMusicData)) {
                                                            foreach ($getMusicData as $key1 => $mus_value) {
                                                                array_push($music_array, (int) $mus_value['iMusicID']);
                                                            }
                                                        }
                                                        //var_dump($musics);
                                                        //exit;
                                                        foreach ($musics as $key => $value) {
                                                            //print_r($getCategoryData);
                                                            if (isset($getMusicData) && !empty($getMusicData)) {
                                                                if (in_array((int) $value['iMusicID'], $music_array)) {
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
                                                <label class="col-sm-3 control-label">Restaurant Passcode</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($restaurantpasscode); ?>
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

<!--                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Map </label>
                                                <div class="col-sm-6">

                                                    <div id="mapCanvas" style="display:block; height: 300px"></div>
                                                </div>
                                            </div>-->

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Area Details</label>
                                                <div class="col-sm-9" id="infoPanel">
                                                    <b>Current Address position:</b>
                                                    <div id="info"></div>
                                                    <input type="text" name="vLat" id="lat" value="<?= (isset($vLat) && $vLat != '') ? $vLat : '31.645547' ?>">
                                                    <input type="text" name="vLog" id="log" value="<?= (isset($vLog) && $vLog != '') ? $vLog : '74.864084' ?>">

                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Primary Address<span class="required">*</span> </label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control maxwidth500"  id="pac-input" name="tAddress"><?= (isset($tAddress) && $tAddress != '') ? $tAddress : '' ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Secondary Address <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control maxwidth500" name="tAddress2" name="tAddress2" required=""><?= (isset($tAddress2) && $tAddress2 != '') ? $tAddress2 : '' ?></textarea>
                                                </div>
                                            </div>
                                            <!-- Location New Work start-->
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Country Name</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($country); ?>
                                                </div>
                                            </div>
                                           
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iStateID">Select State<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="iStateID" id="iStateID" class="maxwidth500 col-lg-12" onchange="getCity(this.value,this.text);">
                                                        <option value="">- Select -</option>
                                                        <?php foreach($getStates AS $state) { if(!isset($iStateID)) {$iStateID = '';}?>
                                                            <option value="<?php echo $state['iStateID']?>" <?php echo $state['iStateID']==$iStateID?"selected='selected'":''?>><?php echo $state['vStateName']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iLocZoneID">Select City<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="iLocZoneID" id="iLocZoneID" class="maxwidth500 col-lg-12" onchange="getLocation(this.value);">
                                                        <option value="">- Select -</option>
                                                        <?php foreach($getCities AS $city) { if(!isset($iLocZoneID)) {$iLocZoneID = '';}?>
                                                            <option value="<?php echo $city['iLocZoneID']?>" <?php echo $city['iLocZoneID']==$iLocZoneID?"selected='selected'":''?>><?php echo $city['vZoneName']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iLocationID">Location Reference<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="iLocationID" id="iLocationID" class="maxwidth500 col-lg-12">
                                                        <option value="">- Select -</option>
                                                        <?php foreach($getLocations AS $location) { if(!isset($iLocationID)) {$iLocationID = '';}?>
                                                            <option value="<?php echo $location['iLocationID']?>" <?php echo $location['iLocationID']==$iLocationID?"selected='selected'":''?>><?php echo $location['vLocationName']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <?php echo form_input($cityname); ?>
                                            <?php echo form_input($statename); ?>
                                            <!--LOcation New Work -->
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Restaurant Listing (Web & Mobile)</label>
                                                <div class="col-sm-9">

                                                    <?php
                                                    if ($pic != '' && $uid != '') {
                                                        $pic_str = '<img src="' . IMGURL . '/restaurant/' . $uid . '/thumb/' . $pic . '">';
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
                                                        <input type="hidden" name="vRestImgW" value="<?php echo $pic; ?>"/>
                                                        <div>
                                                            <span class="btn btn-default btn-file">
                                                                <span class="<?php echo $sel_text ?> select_pic" data-trigger="fileinput"  >Select image</span>
                                                                <span class="<?php echo $change_text ?> select_pic" data-trigger="fileinput">Change</span>
                                                                <input type="file" 
                                                                       name="vRestaurantLogo" 
                                                                       accept="image/jpg, image/JPG,image/JPEG, image/jpeg, image/png, image/PNG" 
                                                                       id="vRestaurantLogo" />
                                                            </span>
                                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput" id="removebtn">Remove</a>
                                                        </div>
                                                        <input type="hidden" id="removepic" name="removepic" value="0" />
                                                    </div>


                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Restaurant PDP (Mobile)</label>
                                                <div class="col-sm-9">

                                                    <?php
                                                    if ($picM != '' && $uid != '') {
                                                        $pic_str = '<img src="' . IMGURL . '/restaurantMobile/' . $uid . '/thumb/' . $picM . '">';
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
                                                        <input type="hidden" name="vRestImgM" value="<?php echo $picM; ?>"/>
                                                        <div>
                                                            <span class="btn btn-default btn-file">
                                                                <span class="<?php echo $sel_text ?> select_picM" data-trigger="fileinput"  >Select image</span>
                                                                <span class="<?php echo $change_text ?> select_picM" data-trigger="fileinput">Change</span>
                                                                <input type="file" 
                                                                       name="vRestaurantMobLogo" 
                                                                       accept="image/jpg, image/JPG,image/JPEG, image/jpeg, image/png, image/PNG" 
                                                                       id="vRestaurantMobLogo" />
                                                            </span>
                                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput" id="removebtnM">Remove</a>
                                                        </div>
                                                        <input type="hidden" id="removepicM" name="removepicM" value="0" />
                                                    </div>


                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Restaurant PDP (Web)</label>
                                                <div class="col-sm-9">

                                                    <?php
                                                    if ($picL != '' && $uid != '') {
                                                        $pic_str = '<img src="' . IMGURL . '/restaurantListing/' . $uid . '/thumb/' . $picL . '">';
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
                                                        <input type="hidden" name="vRestImgL" value="<?php echo $picL; ?>"/>
                                                        <div>
                                                            <span class="btn btn-default btn-file">
                                                                <span class="<?php echo $sel_text ?> select_picL" data-trigger="fileinput"  >Select image</span>
                                                                <span class="<?php echo $change_text ?> select_picL" data-trigger="fileinput">Change</span>
                                                                <input type="file" 
                                                                       name="vRestaurantListing" 
                                                                       accept="image/jpg, image/JPG,image/JPEG, image/jpeg, image/png, image/PNG" 
                                                                       id="vRestaurantListing" />
                                                            </span>
                                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput" id="removebtnL">Remove</a>
                                                        </div>
                                                        <input type="hidden" id="removepicL" name="removepicL" value="0" />
                                                    </div>


                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tSpecialty">Food Speciality</label>
                                                <div class="col-sm-9">
                                                    <?php
                                                    $tSpecialtyComma = '';
                                                    $tSpecialtyArry = array();
                                                    $arryKeys = array();
                                                    if (isset($tSpecialty) && $tSpecialty != '') {

                                                        $tSpecialtyComma = addslashes($tSpecialty);
                                                        // echo $tSpecialtyComma;
                                                        $tSpecialtyArry = explode(',', $tSpecialtyComma);
                                                        $arryKeys = ($tSpecialtyArry);

                                                        $tSpecialty = ("'" . implode("','", $tSpecialtyArry) . "'");
                                                        // $tSpecialtyComma = implode(",", $tSpecialty);
                                                    } else {
                                                        $tSpecialty = '';
                                                    }

                                                    $customArry = array();
                                                    if (!empty($tSpecialtyArry)) {
                                                        for ($i = 0; $i < count($tSpecialtyArry); $i++) {
                                                            $customArry[$i]['id'] = $i;
                                                            $customArry[$i]['value'] = ($tSpecialtyArry[$i]);
                                                        }
                                                    }

                                                    //print_r(json_encode($customArry));
                                                    //echo(implode(',',$arryKeys)); exit;
                                                    ?>
                                                    <input type="text" class="form-control maxwidth500" id="tSpecialty" name="tSpecialty" value="[<?= '\'' . implode('\',\'', $arryKeys) . '\''; ?>]"/>

                                                    <div class="row-fluid text-dark font-11">Enter specialty and press enter to add another</div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tDrinkSpecialty">Drink Speciality</label>
                                                <div class="col-sm-9">
                                                    <?php
                                                    $tDrinkSpecialtyComma = '';
                                                    $tDrinkSpecialtyArry = array();
                                                    $arryKeysDrink = array();
                                                    //echo $tDrinkSpecialty;die;
                                                    if (isset($tDrinkSpecialty) && $tDrinkSpecialty != '') {

                                                        $tDrinkSpecialtyComma = addslashes($tDrinkSpecialty);
                                                        // echo $tSpecialtyComma;
                                                        $tDrinkSpecialtyArry = explode(',', $tDrinkSpecialtyComma);
                                                        $arryKeysDrink = ($tDrinkSpecialtyArry);

                                                        $tDrinkSpecialty = ("'" . implode("','", $tDrinkSpecialtyArry) . "'");
                                                        // $tSpecialtyComma = implode(",", $tSpecialty);
                                                    } else {
                                                        $tDrinkSpecialty = '';
                                                    }

                                                    $customArryDrink = array();
                                                    if (!empty($tDrinkSpecialtyArry)) {
                                                        for ($i = 0; $i < count($tDrinkSpecialtyArry); $i++) {
                                                            $customArryDrink[$i]['id'] = $i;
                                                            $customArryDrink[$i]['value'] = ($tDrinkSpecialtyArry[$i]);
                                                        }
                                                    }
                                                    //print_r($arryKeysDrink);die;
                                                    //print_r(json_encode($customArry));
                                                    //echo(implode(',',$arryKeys)); exit;
                                                    ?>
                                                    <input type="text" class="form-control maxwidth500" id="tDrinkSpecialty" name="tDrinkSpecialty" value="[<?= '\'' . implode('\',\'', $arryKeysDrink) . '\''; ?>]"/>

                                                    <div class="row-fluid text-dark font-11">Enter specialty and press enter to add another</div>
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
                                                    <select name="vDaysOpen[]" id="vDaysOpen" multiple="multiple" required="required">
                                                        <option value="1" <?= in_array('1', $vDaysOpenValue) ? 'selected="selected"' : NULL; ?>>Sunday</option>
                                                        <option value="2" <?= in_array('2', $vDaysOpenValue) ? 'selected="selected"' : NULL; ?>>Monday</option>
                                                        <option value="3" <?= in_array('3', $vDaysOpenValue) ? 'selected="selected"' : NULL; ?>>Tuesday</option>
                                                        <option value="4" <?= in_array('4', $vDaysOpenValue) ? 'selected="selected"' : NULL; ?>>Wednesday</option>
                                                        <option value="5" <?= in_array('5', $vDaysOpenValue) ? 'selected="selected"' : NULL; ?>>Thursday</option>
                                                        <option value="6" <?= in_array('6', $vDaysOpenValue) ? 'selected="selected"' : NULL; ?>>Friday</option>
                                                        <option value="7" <?= in_array('7', $vDaysOpenValue) ? 'selected="selected"' : NULL; ?>>Saturday</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Open & Closing Time</label>
                                                <div class="col-sm-9">
                                                    <div class="row padding0 margin0auto">
                                                        <label class="col-sm-12 padding0" for="iMinTime">Opening Time<span class="required">*</span></label>
                                                        <div class="col-sm-2 padding0">
                                                            <?php
                                                            $minRec = array();
                                                            if ($restaurant_id != '' && isset($iMinTime)) {
                                                                $minRec = explode('-', $iMinTime);
                                                            }
                                                            ?>
                                                            <select class="col-sm-6 form-control" name="iMinTime" id="iMinTime" required="required">
                                                                <option value="">Hour</option>
                                                                <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                                    <option value="<?= $i; ?>"
                                                                            <?= isset($minRec[0]) && $minRec[0] == $i ? 'selected="selected"' : ''; ?>>
                                                                                <?= $i; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <select class="col-sm-6 form-control" name="iMinMin" id="iMinMin" required="">
                                                                <option value="">Minute</option>
                                                                <?php for ($i = 0; $i <= 59; $i++) { ?>
                                                                    <option value="<?= $i; ?>"
                                                                            <?= isset($minRec[1]) && $minRec[1] == $i ? 'selected="selected"' : ''; ?>>
                                                                                <?= $i; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2 padding0">
                                                            <select class="col-sm-6 form-control" name="iMinTimeMaradian" id="iMinTimeMaradian">
                                                                <option value="1" <?= isset($minRec[2]) && $minRec[2] == '1' ? 'selected="selected"' : ''; ?>>AM</option>
                                                                <option value="2" <?= isset($minRec[2]) && $minRec[2] == '2' ? 'selected="selected"' : ''; ?>>PM</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="padding-top-10">
                                                        <label class="col-sm-12 padding0" for="iMaxTime">Closing Time<span class="required">*</span></label>
                                                        <?php
                                                        $maxRec = array();
                                                        if ($restaurant_id != '' && isset($iMaxTime)) {
                                                            $maxRec = explode('-', $iMaxTime);
                                                        }
                                                        ?>
                                                        <div class="col-sm-2 padding0">
                                                            <select class="col-sm-6 form-control" name="iMaxTime" id="iMaxTime" required="required">
                                                                <option value="">Hour</option>
                                                                <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                                    <option value="<?= $i; ?>"
                                                                            <?= isset($maxRec[0]) && $maxRec[0] == $i ? 'selected="selected"' : ''; ?>>
                                                                                <?= $i; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <select class="col-sm-6 form-control" name="iMaxMin" id="iMaxMin" required="">
                                                                <option value="">Minute</option>
                                                                <?php for ($i = 0; $i <= 59; $i++) { ?>
                                                                    <option value="<?= $i; ?>"
                                                                            <?= isset($maxRec[1]) && $maxRec[1] == $i ? 'selected="selected"' : ''; ?>>
                                                                                <?= $i; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2 padding0">
                                                            <select class="col-sm-6 form-control" name="iMaxTimeMaradian" id="iMaxTimeMaradian">
                                                                <option value="1" <?= isset($maxRec[2]) && $maxRec[2] == '1' ? 'selected="selected"' : ''; ?>>AM</option>
                                                                <option value="2" <?= isset($maxRec[2]) && $maxRec[2] == '2' ? 'selected="selected"' : ''; ?>>PM</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Allow Table Booking</label>
                                                <div class="col-sm-9">
                                                    <input type="checkbox" 
                                                           id="allow_book" 
                                                           name="allow_book" 
                                                           value="1" 
                                                           <?= isset($allow_book) && $allow_book == 'yes' ? 'checked="checked"' : ''; ?>/>
                                                    <label class="font-12 error">Check to allow restaurant booking</label>
                                                </div>
                                            </div>

                                            <div id="booking_slots_div">
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label">Booking Time (Slot 1)</label>
                                                    <div class="col-sm-9">
                                                        <div class="row padding0 margin0auto">
                                                            <label class="col-sm-12 padding0" for="iMinBookTime">Minimum Time<span class="required">*</span></label>
                                                            <div class="col-sm-2 padding0">
                                                                <?php
                                                                $minBookRec = array();
                                                                if ($restaurant_id != '' && isset($iMinBookTime)) {
                                                                    $minBookRec = explode('-', $iMinBookTime);
                                                                }
                                                                ?>
                                                                <select class="col-sm-6 form-control" name="iMinBookTime" id="iMinBookTime">
                                                                    <option value="">Hour</option>
                                                                    <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                                        <option value="<?= $i; ?>" <?= isset($minBookRec[0]) && $minBookRec[0] == $i ? 'selected="selected"' : ''; ?>><?= $i; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <select class="col-sm-6 form-control" name="iMinBookMin" id="iMinBookMin" >
                                                                    <option value="">Minute</option>
                                                                    <?php for ($i = 0; $i <= 59; $i++) { ?>
                                                                        <option value="<?= $i; ?>" <?= isset($minBookRec[1]) && $minBookRec[1] == $i ? 'selected="selected"' : ''; ?>><?= $i; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2 padding0">
                                                                <select class="col-sm-6 form-control" name="iMinBookTimeMaradian" id="iMinBookTimeMaradian">
                                                                    <option value="1" <?= isset($minBookRec[2]) && $minBookRec[2] == '1' ? 'selected="selected"' : ''; ?>>AM</option>
                                                                    <option value="2" <?= isset($minBookRec[2]) && $minBookRec[2] == '2' ? 'selected="selected"' : ''; ?>>PM</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="padding-top-10">
                                                            <label class="col-sm-12 padding0" for="iMaxBookTime">Maximum Time<span class="required">*</span></label>
                                                            <?php
                                                            $maxBookRec = array();
                                                            if ($restaurant_id != '' && isset($iMaxBookTime)) {
                                                                $maxBookRec = explode('-', $iMaxBookTime);
                                                            }
                                                            ?>
                                                            <div class="col-sm-2 padding0">
                                                                <select class="col-sm-6 form-control" name="iMaxBookTime" id="iMaxBookTime" >
                                                                    <option value="">Hour</option>
                                                                    <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                                        <option value="<?= $i; ?>" <?= isset($maxBookRec[0]) && $maxBookRec[0] == $i ? 'selected="selected"' : ''; ?>><?= $i; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <select class="col-sm-6 form-control" name="iMaxBookMin" id="iMaxBookMin" >
                                                                    <option value="">Minute</option>
                                                                    <?php for ($i = 0; $i <= 59; $i++) { ?>
                                                                        <option value="<?= $i; ?>" <?= isset($maxBookRec[1]) && $maxBookRec[1] == $i ? 'selected="selected"' : ''; ?>><?= $i; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2 padding0">
                                                                <select class="col-sm-6 form-control" name="iMaxBookTimeMaradian" id="iMaxBookTimeMaradian">
                                                                    <option value="1" <?= isset($maxBookRec[2]) && $maxBookRec[2] == '1' ? 'selected="selected"' : ''; ?>>AM</option>
                                                                    <option value="2" <?= isset($maxBookRec[2]) && $maxBookRec[2] == '2' ? 'selected="selected"' : ''; ?>>PM</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label">Booking Time (Slot 2)</label>
                                                    <div class="col-sm-9">
                                                        <div class="row padding0 margin0auto">
                                                            <label class="col-sm-12 padding0" for="iMinBookTime2">Minimum Time<span class="required">*</span></label>
                                                            <div class="col-sm-2 padding0">
                                                                <?php
                                                                $minBookRec2 = array();
                                                                if ($restaurant_id != '' && isset($iMinBookTime2)) {
                                                                    $minBookRec2 = explode('-', $iMinBookTime2);
                                                                }
                                                                ?>
                                                                <select class="col-sm-6 form-control" name="iMinBookTime2" id="iMinBookTime2">
                                                                    <option value="">Hour</option>
                                                                    <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                                        <option value="<?= $i; ?>" <?= isset($minBookRec2[0]) && $minBookRec2[0] == $i ? 'selected="selected"' : ''; ?>><?= $i; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <select class="col-sm-6 form-control" name="iMinBookMin2" id="iMinBookMin2" >
                                                                    <option value="">Minute</option>
                                                                    <?php for ($i = 0; $i <= 59; $i++) { ?>
                                                                        <option value="<?= $i; ?>" <?= isset($minBookRec2[1]) && $minBookRec2[1] == $i ? 'selected="selected"' : ''; ?>><?= $i; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2 padding0">
                                                                <select class="col-sm-6 form-control" name="iMinBookTimeMaradian2" id="iMinBookTimeMaradian2">
                                                                    <option value="1" <?= isset($minBookRec2[2]) && $minBookRec2[2] == '1' ? 'selected="selected"' : ''; ?>>AM</option>
                                                                    <option value="2" <?= isset($minBookRec2[2]) && $minBookRec2[2] == '2' ? 'selected="selected"' : ''; ?>>PM</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="padding-top-10">
                                                            <label class="col-sm-12 padding0" for="iMaxBookTime2">Maximum Time<span class="required">*</span></label>
                                                            <?php
                                                            $maxBookRec2 = array();
                                                            if ($restaurant_id != '' && isset($iMaxBookTime2)) {
                                                                $maxBookRec2 = explode('-', $iMaxBookTime2);
                                                            }
                                                            ?>
                                                            <div class="col-sm-2 padding0">
                                                                <select class="col-sm-6 form-control" name="iMaxBookTime2" id="iMaxBookTime2" >
                                                                    <option value="">Hour</option>
                                                                    <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                                        <option value="<?= $i; ?>" <?= isset($maxBookRec2[0]) && $maxBookRec2[0] == $i ? 'selected="selected"' : ''; ?>><?= $i; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <select class="col-sm-6 form-control" name="iMaxBookMin2" id="iMaxBookMin2" >
                                                                    <option value="">Minute</option>
                                                                    <?php for ($i = 0; $i <= 59; $i++) { ?>
                                                                        <option value="<?= $i; ?>" <?= isset($maxBookRec2[1]) && $maxBookRec2[1] == $i ? 'selected="selected"' : ''; ?>><?= $i; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2 padding0">
                                                                <select class="col-sm-6 form-control" name="iMaxBookTimeMaradian2" id="iMaxBookTimeMaradian2">
                                                                    <option value="1" <?= isset($maxBookRec2[2]) && $maxBookRec2[2] == '1' ? 'selected="selected"' : ''; ?>>AM</option>
                                                                    <option value="2" <?= isset($maxBookRec2[2]) && $maxBookRec2[2] == '2' ? 'selected="selected"' : ''; ?>>PM</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="min_person">Minimum Person Allow<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="number" name="min_person" id="min_person" class="form-control maxwidth500" required="required" value="<?= isset($iMinPerson) && $iMinPerson != '' ? $iMinPerson : ''; ?>" min="1"/>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="max_person">Maximum Person Allow<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="number" name="max_person" id="max_person" class="form-control maxwidth500" required="required" value="<?= isset($iMaxPerson) && $iMaxPerson != '' ? $iMaxPerson : ''; ?>" min="1"/>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iPriceValue">Price<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-control maxwidth500 currency-input-div">
                                                                <span class="currency-span">₹ </span>
                                                                <input type="number" 
                                                                       id="iPriceValue" 
                                                                       name="iPriceValue" 
                                                                       required 
                                                                       class="form-control maxwidth500 currency-input" 
                                                                       value="<?= (isset($iPriceValue) && $iPriceValue != '') ? $iPriceValue : ''; ?>" />
                                                            </div>
                                                            <label class="font-11 error" style="padding-top: 18px;">e.g. 2000</label>
                                                            <label>
                                                                <input type="radio" 
                                                                       id="eAlcoholYes" 
                                                                       name="eAlcohol" 
                                                                       class="alchol-check" 
                                                                       value="yes" 
                                                                       <?= isset($eAlcohol) && $eAlcohol == 'yes' ? 'checked="checked"' : 'checked="checked"'; ?>/> with alcohol     
                                                            </label> &nbsp;
                                                            <label>
                                                                <input type="radio" 
                                                                       id="eAlcoholNo" 
                                                                       name="eAlcohol" 
                                                                       class="alchol-check" 
                                                                       value="no" 
                                                                       <?= isset($eAlcohol) && $eAlcohol == 'no' ? 'checked="checked"' : ''; ?>/> without alcohol
                                                            </label>
                                                            <br/>
                                                            <label class="font-12 error" id="alchol-msg">The above price includes alcohol</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--                                            <div class="form-group">
                                                                                            <label class="col-sm-3 control-label" for="iMinPrice">Minimum Price<span class="required">*</span></label>
                                                                                            <div class="col-sm-9">
                                                                                                <input type="number" name="iMinPrice" id="iMinPrice" class="form-control maxwidth500" required="required" value="<?= isset($iMinPrice) && $iMinPrice != '' ? $iMinPrice : ''; ?>" min="0"/>
                                                                                            </div>
                                                                                        </div>
                                            
                                                                                        <div class="form-group">
                                                                                            <label class="col-sm-3 control-label" for="iMaxPrice">Maximum Price<span class="required">*</span></label>
                                                                                            <div class="col-sm-9">
                                                                                                <input type="number" name="iMaxPrice" id="iMaxPrice" class="form-control maxwidth500" required="required" value="<?= isset($iMaxPrice) && $iMaxPrice != '' ? $iMaxPrice : ''; ?>" min="0"/>
                                                                                            </div>
                                                                                        </div>
                                            -->
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Featured Restaurant</label>
                                                <div class="col-sm-9">
                                                    <input type="checkbox" 
                                                           id="eFeatured" 
                                                           name="eFeatured" 
                                                           value="1" 
                                                           <?= isset($eFeatured) && $eFeatured == 'yes' ? 'checked="checked"' : ''; ?>/>
                                                    <label class="font-12 error">Check to set as a featured restaurant</label>
                                                </div>
                                            </div>




                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="sms_contact">SMS communication</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($sms_contact_field); ?>
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

                                                <fieldset style="margin-bottom: 15px !important;">
                                                    <legend class="row font-15" style="border-bottom: 0px !important;">
                                                        <label class="col-sm-3 control-label">Manager 1</label>
                                                    </legend>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">Name</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control maxwidth500" id="mngr1_name" placeholder="Manager Name" name="mngr1_name" value="<?= isset($mngr1_name) ? $mngr1_name : '' ?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">Contact Number</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control maxwidth500" id="mngr1_contact" placeholder="Manager Contact Number" name="mngr1_contact" value="<?= isset($mngr1_contact) ? $mngr1_contact : '' ?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">Contact Email</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control maxwidth500" id="mngr1_email" placeholder="Manager Email Address" name="mngr1_email" value="<?= isset($mngr1_email) ? $mngr1_email : '' ?>"/>
                                                        </div>
                                                    </div>
                                                </fieldset>

                                                <fieldset style="margin-bottom: 15px !important;">
                                                    <legend class="row font-15" style="border-bottom: 0px !important;">
                                                        <label class="col-sm-3 control-label">Manager 2</label>
                                                    </legend>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">Name</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control maxwidth500" id="mngr2_name" placeholder="Manager Name" name="mngr2_name" value="<?= isset($mngr2_name) ? $mngr2_name : '' ?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">Contact Number</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control maxwidth500" id="mngr2_contact" placeholder="Manager Contact Number" name="mngr2_contact" value="<?= isset($mngr2_contact) ? $mngr2_contact : '' ?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">Contact Email</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control maxwidth500" id="mngr2_email" placeholder="Manager Email Address" name="mngr2_email" value="<?= isset($mngr2_email) ? $mngr2_email : '' ?>"/>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                                <fieldset style="margin-bottom: 15px !important;">
                                                    <legend class="row font-15" style="border-bottom: 0px !important;">
                                                        <label class="col-sm-3 control-label">Manager 3</label>
                                                    </legend>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">Name</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control maxwidth500" placeholder="Manager Name" id="mngr3_name" name="mngr3_name" value="<?= isset($mngr3_name) ? $mngr3_name : '' ?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">Contact Number</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control maxwidth500" placeholder="Manager Contact Number" id="mngr3_contact" name="mngr3_contact" value="<?= isset($mngr3_contact) ? $mngr3_contact : '' ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">Contact Email</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control maxwidth500" placeholder="Manager Email Address" id="mngr3_email" name="mngr3_email" value="<?= isset($mngr3_email) ? $mngr3_email : '' ?>"/>
                                                        </div>
                                                    </div>
                                                </fieldset>


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
                                                                <label class="col-sm-3 control-label">Tuesday</label>
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

        <script src="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.css" type="text/css" media="screen" />
        <script src="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript" charset="utf-8"></script>
        <!--<script src="http://maps.google.com/maps/api/js?sensor=false"></script>-->
<!--        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp'+'&signed_in=true&callback=initialize"></script>-->
        <!--<script type="text/javascript" src="http://www.google.com/jsapi?key=<?= gmapkey; ?>"></script>-->

<!--        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&sensor=false"></script>-->
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>js/magic-suggest/magicsuggest-1.3.1-min.css" />
        <script type="text/javascript" src="<?= base_url() ?>js/magic-suggest/magicsuggest-1.3.1-min.js"></script>

        <script>
            var flag = 0;
            jQuery(document).ready(function (){
                $('#iStateID').select2();
                $('#iLocZoneID').select2();
                $('#iLocationID').select2();
                $('.uniform').uniform();
            });
            function toObject(arr) {
                var rv = {};
                //alert(arr.length);
                for (var i = 0; i < arr.length; i++)
                    rv[i] = arr[i];

                //console.log(rv);
                return rv;
            }

            var restId = parseInt('<?= (isset($iRestaurantID) && $iRestaurantID != '') ? $iRestaurantID : 0; ?>');
            jQuery(document).ready(function() {
                var permission = <?= json_encode($permission); ?>;
                if ((restId > 0 && permission.indexOf('2') >= 0) || (restId == 0 && permission.indexOf('1') >= 0)) {

                } else if (permission.indexOf('6') >= 0 || permission.indexOf('1') >= 0 || permission.indexOf('2') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }
<?php if (!(isset($allow_book) && $allow_book == 'yes')) { ?>
                    $('#booking_slots_div').hide();
<?php } ?>

                $('#allow_book').change(function() {
                    var $check = $(this);
                    if ($check.is(':checked')) {
                        $('#booking_slots_div').show();
                    } else {
                        $('#booking_slots_div').hide();
                    }
                });

                var $alcholmsg = $('#alchol-msg');
                if ($('#eAlcoholYes').is(':checked'))
                    $alcholmsg.html('The above price includes alchol');
                else
                    $alcholmsg.html('The above price does not include alchol');

                $('.alchol-check').change(function() {
                    var $check = $(this);
                    switch ($check.val()) {
                        case 'yes' :
                            $alcholmsg.html('The above price includes alchol');
                            break;

                        case 'no' :
                            $alcholmsg.html('The above price does not include alchol');
                            break;
                    }
                });
                var msT = $('#tSpecialty').magicSuggest({
                    data: toObject(<?= json_encode($customArry); ?>),
                    valueField: 'value',
                    displayField: 'value',
                    value: [<?= '"' . strip_slashes(implode('","', $arryKeys)) . '"'; ?>],
                    width: '50%',
                    maxDropHeight: 0
                });

                setTimeout(function() {
                    $('#tSpecialty').find('.ms-trigger').remove();
                }, 0);

                var msTDrink = $('#tDrinkSpecialty').magicSuggest({
                    data: toObject(<?= json_encode($customArryDrink); ?>),
                    valueField: 'value',
                    displayField: 'value',
                    value: [<?= '"' . strip_slashes(implode('","', $arryKeysDrink)) . '"'; ?>],
                    width: '50%',
                    maxDropHeight: 0
                });

                setTimeout(function() {
                    $('#tDrinkSpecialty').find('.ms-trigger').remove();
                }, 0);

                App.setPage("forms"); //Set current page
                App.init(); //Initialise plugins and elements


                $("#validateForm").validate({
                    rules: {
                        vRestaurantName: {
                            required: true
                        },
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
                        iStateID: {
                            required: true
                        },
                        iLocZoneID: {
                            required: true
                        },
                        iLocationID: {
                            required: true
                        },
                        tAddress2: {
                            required: true
                        },
                        iMinTime: {
                            required: true
                        },
                        iMinMin: {
                            required: true
                        },
                        iMaxTime: {
                            required: true
                        },
                        iMaxMin: {
                            required: true
                        },
                        iPriceValue: {
                            required: true
                        },
                        vDaysOpen: {needsSelection: true}
//                        iMinPrice: {required: true},
//                        iMaxPrice: {required: true}
                    },
                    messages: {
                        vRestaurantName: {
                            required: "Please enter restaurant name"
                        },
                        vEmail: {
                            required: "Please enter an email.",
                            email: "Pleae enter valid email address."
                        },
                        vPassword: {
                            required: "Please provide a password",
                            minlength: "Your password must be at least 5 characters long"
                        },
                        confirm_password: {
                            required: "Please provide a confirm password",
                            minlength: "Your password must be at least 5 characters long",
                            equalTo: "Please enter the same password as above"
                        },
                        iStateID: {
                            required: "Please select state"
                        },
                        iLocZoneID: {
                            required: "Please select city"
                        },
                        iLocationID: {
                            required: "Please select location reference"
                        },
                        tAddress2: {
                            required: 'Please enter secondary address'
                        },
                        iMinTime: {
                            required: "Please select opening time hour"
                        },
                        iMinMin: {
                            required: "Please select opening time minute"
                        },
                        iMaxTime: {
                            required: "Please select closing time hour"
                        },
                        iMaxMin: {
                            required: "Please select closing time minute"
                        },
                        iPriceValue: {
                            required: "Please enter valid price value"
                        },
                        vDaysOpen: {
                            required: "Please enter Number of days Open"
                        }
//                        iMinPrice: {required: "Please enter valid minimum price value"},
//                        iMaxPrice: {required: "Please enter valid maximum price value"}
                    }
                });

                var _URL = window.URL || window.webkitURL;
                $("#vRestaurantLogo").change(function(e) {
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
                $("#vRestaurantMobLogo").change(function(e) {
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
                                $('#removebtnM').trigger('click');
                            }
                            // alert("The image width is " + this.width + " and image height is " + this.height);
                        };
                        image.src = _URL.createObjectURL(file);
                    }
                });
                $("#vRestaurantListing").change(function(e) {
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
                                $('#removebtnL').trigger('click');
                            }
                            // alert("The image width is " + this.width + " and image height is " + this.height);
                        };
                        image.src = _URL.createObjectURL(file);
                    }
                });
                document.getElementById('removepic').value = 0;
                document.getElementById('removepicM').value = 0;
                document.getElementById('removepicL').value = 0;
                // $('.fileinput').fileinput()
                $('.uniform').uniform();
                $('#vDaysOpen').multiselect({
                    includeSelectAllOption: true,
                    selectAllText: 'All Days!',
                    selectAllValue: 'all'
                });
                $('#iFacilityID, #iMusicID, #iCuisineID, #iCategoryID, #iCuisineIDM').select2();
                function getHourMinute(requireVal) {
                    var hours1 = Math.floor(requireVal / 60);
                    var minutes1 = requireVal - (hours1 * 60);
                    if (hours1.length === 1)
                        hours1 = '0' + hours1;
                    if (minutes1.length === 1)
                        minutes1 = '0' + minutes1;
                    if (minutes1 === 0)
                        minutes1 = '00';
                    if (hours1 >= 12) {
                        if (hours1 === 12) {
                            minutes1 = minutes1 + " PM";
                        } else {
                            hours1 = hours1 - 12;
                            minutes1 = minutes1 + " PM";
                        }
                    } else {
                        minutes1 = minutes1 + " AM";
                    }
                    if (hours1 === 0) {
                        hours1 = 12;
                    }

                    return Array(hours1, minutes1);
                }
                $('#time-range').slider({
                    range: true,
                    min: 0, max: 1440,
                    step: 15,
                    values: [600, 720],
                    slide: function(e, ui) {
                        //console.log(ui.values[0] + ' ' + ui.values[1]);

                        var minTimeVal = getHourMinute(ui.values[0]);
                        //$('.slider-time').html(hours1 + ':' + minutes1);
                        var time1 = minTimeVal[0] + ':' + minTimeVal[1];
                        minTimeVal = getHourMinute(ui.values[1]);
                        //$('.slider-time2').html(hours2 + ':' + minutes2);
                        var time2 = minTimeVal[0] + ':' + minTimeVal[1];
                        $("#time-value").val(time1 + " - " + time2);
                        $("#iMinTime").val(ui.values[0]);
                        $("#iMaxTime").val(ui.values[1]);
                    }
                });
                var iMinTime = parseInt("<?= (isset($iMinTime) && $iMinTime != '') ? $iMinTime : -1; ?>");
                var iMaxTime = parseInt("<?= (isset($iMaxTime) && $iMaxTime != '') ? $iMaxTime : -1; ?>");
                if (iMinTime !== -1 && iMaxTime !== -1) {
                    $("#time-range").slider({
                        range: true,
                        min: 0,
                        max: 1440,
                        step: 15,
                        values: [iMinTime, iMaxTime],
                        slide: function(e, ui) {
                            //console.log(ui.values[0] + ' ' + ui.values[1]);
                            //console.log(ui.values[0] + ' ' + ui.values[1]);

                            var minTimeVal = getHourMinute(ui.values[0]);                             //$('.slider-time').html(hours1 + ':' + minutes1);
                            var time1 = minTimeVal[0] + ':' + minTimeVal[1];
                            minTimeVal = getHourMinute(ui.values[1]);
                            //$('.slider-time2').html(hours2 + ':' + minutes2);
                            var time2 = minTimeVal[0] + ':' + minTimeVal[1];
                            $("#time-value").val(time1 + " - " + time2);
                            $("#iMinTime").val(ui.values[0]);
                            $("#iMaxTime").val(ui.values[1]);
                        }
                    });
                    var minTimeVal = getHourMinute(iMinTime);
                    //$('.slider-time').html(hours1 + ':' + minutes1);
                    var time11 = minTimeVal[0] + ':' + minTimeVal[1];
                    minTimeVal = getHourMinute(iMaxTime);
                    //$('.slider-time2').html(hours2 + ':' + minutes2);
                    var time22 = minTimeVal[0] + ':' + minTimeVal[1];
                    $("#time-value").val("" + time11 + " - " + time22);
                } else {
                    var minTimeVal = getHourMinute(600);
                    //$('.slider-time').html(hours1 + ':' + minutes1);
                    var time11 = minTimeVal[0] + ':' + minTimeVal[1];
                    minTimeVal = getHourMinute(720);
                    //$('.slider-time2').html(hours2 + ':' + minutes2);
                    var time22 = minTimeVal[0] + ':' + minTimeVal[1];
                    $("#time-value").val("" + time11 + " - " + time22);
                }

                $("#slider-range").slider({
                    range: true,
                    min: 0, max: 20000,
                    step: 50, values: [0, 2000],
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
                if (iMinPrice !== -1 && iMaxPrice !== -1) {
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
            $(document).on('click', '#removebtnM', function(event) {
                $("#removepicM").val('1');
            });
            $(document).on('click', '.select_picM', function(event) {
                $("#removepicM").val('0');
            });
            $(document).on('click', '#removebtnL', function(event) {
                $("#removepicL").val('1');
            });
            $(document).on('click', '.select_picL', function(event) {
                $("#removepicL").val('0');
            });
//            function loadMap(lat, log) {
//                //alert('load');
//                var latLng = new google.maps.LatLng(lat, log);
//                var map = new google.maps.Map(document.getElementById('mapCanvas'), {
//                    zoom: 12,
//                    center: latLng,
//                    mapTypeId: google.maps.MapTypeId.ROADMAP
//                });
//                var marker = new google.maps.Marker({
//                    position: latLng,
//                    title: 'Select Event Point',
//                    map: map,
//                    draggable: true
//                });
//                marker.setPosition(place.geometry.location);
//                marker.setVisible(true);
//                initialize();
//            }

//            var geocoder = new google.maps.Geocoder();
//            function geocodePosition(pos) {
//                geocoder.geocode({latLng: pos}, function(responses) {
//                    console.log('GeoCode');
//                    console.log(responses);
//                    if (responses && responses.length > 0) {
//                        //console.log(responses[0].address_components);
//                        updateMarkerAddress(responses[0].formatted_address, responses[0].address_components);
//                    } else {
//                        updateMarkerAddress('Cannot determine address at this location.', '');
//                    }
//                });
//            }

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
//            infowindow = new google.maps.InfoWindow({
//                'size': new google.maps.Size(292, 120)
//            });
//            function updateMarkerAddress(str, str1) {
//                if (restId <= 0) {
//                    $("#pac-input").val(str);
//                } else {
//                    restId = 0;
//                }
//                //document.getElementById('address').text = str;                 //$('#vAreaName').text(str1[3]['long_name']);
//                $.each(str1, function(index, val) {
//                    var arr = val.types;
//                    if (arr[0] === 'route') {
//                        infowindow.setContent("Address : " + str + "<br/> Locality : " + val.long_name);
//                        //$('#vAreaName').val(val.long_name);
//                    }
//                    if (arr[0] === 'sublocality') {
//                        infowindow.setContent("Address : " + str + "<br/> Locality : " + val.long_name);
//                        //$('#vAreaName').val(val.long_name);
//                    }
//                    if (arr[0] === 'country') {
//                        //infowindow.setContent("Address : " + str + "<br/> Locality : " + val.long_name);
//                        $('#vCountryName').val(val.long_name);
//                    }
//                    if (arr[0] === 'administrative_area_level_1') {
//                        $('#vStateName').val(val.long_name);
//                    }
//                    if (arr[0] === 'administrative_area_level_2') {
//                        $('#vCityName').val(val.long_name);
//                    }
//                });
//            }
//
//            function initialize() {
//                var lat = $('#lat').val(); //
//                var log = $('#log').val();//
//                var latLng = new google.maps.LatLng(lat, log);
//                var map = new google.maps.Map(document.getElementById('mapCanvas'), {
//                    zoom: 15, center: latLng,
//                    mapTypeId: google.maps.MapTypeId.ROADMAP
//                });
//                var input = document.getElementById('pac-input');
//                //alert('init 1');
//                var types = document.getElementById('type-selector');
//                //   map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
//                //alert('init 11');
//                var autocomplete = new google.maps.places.SearchBox(input);
//                //alert('init 2');
//                autocomplete.bindTo('bounds', map);
//                //alert('init 3');
//                var marker = new google.maps.Marker({
//                    position: latLng,
//                    title: 'Select Event Point',
//                    map: map,
//                    draggable: true
//                });
//                // Update current position info.
//
//                google.maps.event.addListener(autocomplete, 'places_changed', function() {
//                    infowindow.close();
//                    marker.setVisible(false);
//                    //alert('a');
//                    //console.log(autocomplete.getPlaces());
//
//
//                    var place = autocomplete.getPlaces()[0];
//                    if (!place.geometry)
//                        return;
//
//                    // If the place has a geometry, then present it on a map.
//                    if (place.geometry.viewport) {
//                        map.fitBounds(place.geometry.viewport);
//                    } else {
//                        map.setCenter(place.geometry.location);
//                        map.setZoom(16);
//                    }
//
//                    //console.log(place.geometry);
//
//                    /*if (!place.geometry) {
//                     return;
//                     }
//                     
//                     // If the place has a geometry, then present it on a map.
//                     if (place.geometry.viewport) {
//                     map.fitBounds(place.geometry.viewport);
//                     } else {
//                     map.setCenter(place.geometry.location);
//                     map.setZoom(17); // Why 17? Because it looks good.
//                     } */
//                    /*marker.setIcon(({
//                     url: place.icon,
//                     size: new google.maps.Size(71, 71),
//                     origin: new google.maps.Point(0, 0),
//                     anchor: new google.maps.Point(17, 34),
//                     scaledSize: new google.maps.Size(35, 35)
//                     }));
//                     */
//                    marker.setPosition(place.geometry.location);
//                    marker.setVisible(true);
//                    console.log('Auto Complete');
//                    console.log(place.geometry.location);
//                    var address = '';
//                    if (place.address_components) {
//                        address = [
//                            (place.address_components[0] && place.address_components[0].short_name || ''),
//                            (place.address_components[1] && place.address_components[1].short_name || ''),
//                            (place.address_components[2] && place.address_components[2].short_name || '')
//                        ].join(' ');
//                    }
//
//                    infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
//                    $("#pac-input").val(address);
//                    updateMarkerPosition(marker.getPosition());
//                    infowindow.open(map, marker);
//                });
//                updateMarkerPosition(latLng);
//                geocodePosition(latLng);
//                // Add dragging event listeners.
//                google.maps.event.addListener(marker, 'dragstart', function() {
//                    //updateMarkerAddress('Dragging...');
//                });
//                google.maps.event.addListener(marker, 'drag', function() {
//                    //updateMarkerStatus('Dragging...');
//                    updateMarkerPosition(marker.getPosition());
//                });
//                google.maps.event.addListener(marker, 'dragend', function() {
//                    //updateMarkerStatus('Drag ended');
//                    geocodePosition(marker.getPosition());
//                });
//                google.maps.event.addListener(marker, 'click', function(e) {
//                    //updateMarkerStatus('Drag ended');
//                    //geocodePosition(marker.getPosition());
//                    geocoder.geocode(
//                            {'latLng': e.latLng},
//                    function(results, status) {
//                        if (status === google.maps.GeocoderStatus.OK) {
//                            if (results[0]) {
//                                if (marker) {
//                                    marker.setPosition(e.latLng);
//                                } else {
//                                    marker = new google.maps.Marker({
//                                        position: e.latLng,
//                                        map: map});
//                                }
//                                updateMarkerAddress(results[0].formatted_address, results[0].address_components);
//                                //infowindow.setContent("Address : "+results[0].formatted_address+"<br/> Locality : ");
//                                //responses[0].address_components
//                                infowindow.open(map, marker);
//                            }
//                        }
//                    });                     //openInfoWindow(geocodePosition(marker.getPosition()),marker);
//                });
//                var openInfoWindow = function(result, marker) {
//                    google.maps.fitBounds(result.geometry.viewport);
//                    infowindow.setContent(getAddressComponentsHtml(result.address_components));
//                    infowindow.open(map, marker);
//                };
//            }
//            google.maps.event.addDomListener(window, 'load', initialize);
            // Onload handler to fire off the app.
            
            
     function getCity(stateId) {
        var txt = $("#iStateID :selected").text();
        $("#vStateName").val(txt);
        var form_data = {stateId: stateId}
        $.ajax({
            url: BASEURL + 'restaurant/getCity',
            type: 'POST',
            data: form_data,
            success: function (outputData) {
                 var selectCity = document.getElementById('iLocZoneID');
                 var selectLoc = document.getElementById('iLocationID');
                  $(selectCity).empty();
                  $(selectLoc).empty();
                   $("#s2id_iLocZoneID .select2-chosen").html("- Select -");
                   $("#s2id_iLocationID .select2-chosen").html("- Select -");
                  outputData = $.parseJSON(outputData);
                if (outputData != '') {
                    $(selectCity).append('<option value="">- Select -</option>');
                    $.each( outputData, function( key, value ) {
                        $(selectCity).append('<option value=' + value.iLocZoneID + '>' + value.vZoneName + '</option>');
                     });
                }  
           }
        });
    }
    
    function getLocation(cityId) {
        var txt = $("#iLocZoneID :selected").text();
        $('#vCityName').val(txt);
        var form_data = {cityId: cityId}
        $.ajax({
            url: BASEURL + 'restaurant/getLocation',
            type: 'POST',
            data: form_data,
            success: function (outputData) {
                 var selectLoc = document.getElementById('iLocationID');
                  $(selectLoc).empty();
                   $("#s2id_iLocationID .select2-chosen").html("- Select -");
                  outputData = $.parseJSON(outputData);
                if (outputData != '') {
                    $(selectLoc).append('<option value="">- Select -</option>');
                    $.each( outputData, function( key, value ) {
                        $(selectLoc).append('<option value=' + value.iLocationID + '>' + value.vLocationName + '</option>');
                     });
                }  
           }
        });
    }
            
        </script>
    </body>
</html>
