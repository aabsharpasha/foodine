<?php
$headerData = $this->headerlib->data();
if (isset($getUserData) && $getUserData != '')
    extract($getUserData);

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 18);
//mprd($permission);
//print_r($getUserData);

$form_attr = array(
    'name' => 'user-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);
$first_name = array(
    'name' => 'vFullName',
    'id' => 'vFullName',
    'placeholder' => 'Please provide name',
    'value' => (isset($vFullName) && $vFullName != '') ? $vFullName : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$last_name = array(
    'name' => 'vLastName',
    'id' => 'vLastName',
    'placeholder' => 'Please provide last name',
    'value' => (isset($vLastName) && $vLastName != '') ? $vLastName : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$email = array(
    'name' => 'vEmail',
    'id' => 'vEmail',
    "required" => "required",
    'placeholder' => 'Please provide email',
    "data-errortext" => "This is user's email address!",
    'value' => (isset($vEmail) && $vEmail != '') ? $vEmail : '',
    'type' => 'email',
    'class' => 'form-control maxwidth500'
);
$password = array(
    'name' => 'vPassword',
    'id' => 'vPassword',
    "required" => "required",
    'minlength' => '5',
    'placeholder' => 'Please provide Password',
    "data-errortext" => "This is user's password!",
    'value' => '',
    'type' => 'password',
    'class' => 'form-control maxwidth500'
);
$confirm_password = array(
    'name' => 'confirm_password',
    'id' => 'confirm_password',
    "required" => "required",
    'placeholder' => 'Please Confirm Password',
    "data-errortext" => "This is user's password!",
    'value' => '',
    'type' => 'password',
    'minlength' => '5',
    'equalTo' => "#password",
    'class' => 'form-control maxwidth500'
);

$username = array(
    'name' => 'vUserName',
    'id' => 'vUserName',
    'placeholder' => 'Please provide username',
    'value' => (isset($vUserName) && $vUserName != '') ? $vUserName : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$mobileno = array(
    'name' => 'vMobileNo',
    'id' => 'vMobileNo',
    'placeholder' => 'Please provide Mobile Number',
    'value' => (isset($vMobileNo) && $vMobileNo != '') ? $vMobileNo : '',
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
    "action" => "backoffice.useredit"
);
$hiddenaddattr = array(
    "action" => "backoffice.useradd"
);
$user_id = array(
    "iUserID" => (isset($iUserID) && $iUserID != '') ? $iUserID : ''
);
$submit_attr = array(
    'class' => 'submit btn-sm btn btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL User",
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


$pic = (isset($vProfilePicture) && $vProfilePicture != '') ? $vProfilePicture : '';
$uid = (isset($iUserID) && $iUserID != '') ? $iUserID : '';
$gender = (isset($eGender) && $eGender != '') ? $eGender : '';
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
                                            <li><?= $this->controller ?></li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">User</h3>
                                        </div>
                                        <div class="description">Add/Edit User</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " User"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("user/add", $form_attr);
                                            if (isset($iUserID) && $iUserID != '') {
                                                echo form_hidden($user_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Name</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($first_name); ?>
                                                    <input type="hidden" name="removepic" id="removepic" value="0">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Email<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($email); ?>
                                                </div>
                                            </div>

                                            <?php /* <div class="form-group">
                                              <label class="col-sm-3 control-label">User Name<span class="required">*</span></label>
                                              <div class="col-sm-9">
                                              <?php echo form_input($username); ?>
                                              </div>
                                              </div> */ ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Mobile No<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($mobileno); ?>
                                                </div>
                                            </div>

                                            <?php if (@$getUserData != ''): ?>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label">Change Password</label>
                                                    <div class="col-sm-9 center maxwidth500">
                                                        <button id="passchange" class="btn btn-light-grey">Change Password</button>
                                                    </div>
                                                </div>
                                            <?php endif ?>
                                            <div class="form-group <?= (@$getUserData != '') ? 'passtohide' : '' ?>">
                                                <label class="col-sm-3 control-label">Password<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($password); ?>
                                                </div>
                                            </div>
                                            <div class="form-group <?= (@$getUserData != '') ? 'passtohide' : '' ?>">
                                                <label class="col-sm-3 control-label"> Confirm Password<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($confirm_password); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Profile Pic</label>
                                                <div class="col-sm-9">


                                                    <?php
                                                    if ($pic != '' && $uid != '') {
                                                        $pic_str = '<img src="' . IMGURL . '/user/' . $uid . '/thumb/' . $pic . '">';
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
                                                                <input type="file" 
                                                                       name="vProfilePicture" 
                                                                       accept="image/jpg, image/JPG,image/JPEG, image/jpeg, image/png, image/PNG" 
                                                                       id="vProfilePicture" />
                                                            </span>
                                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput" id="removebtn">Remove</a>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12 padding0">
                                                        <span class="required">
                                                            <strong>NOTE:</strong> Upload file format <strong>jpg, jpeg, png</strong> are allowed.
                                                            <br/>File dimension  should be <strong>640 x 640</strong>.
                                                        </span>
                                                    </div>

                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Gender</label>
                                                <div class="col-sm-9">

                                                    <?php
                                                    if ($gender != '') {

                                                        if ($gender == 'Male') {
                                                            $male_check = 'checked="checked"';
                                                            $female_check = '';
                                                            $notdisclose_check = '';
                                                        } else if ($gender == 'Female') {
                                                            $female_check = 'checked="checked"';
                                                            $male_check = '';
                                                            $notdisclose_check = '';
                                                        } else if ($gender == 'Notdisclose') {
                                                            $notdisclose_check = 'checked="checked"';
                                                            $male_check = '';
                                                            $female_check = '';
                                                        }
                                                    } else {
                                                        $notdisclose_check = 'checked="checked"';
                                                        $male_check = '';
                                                        $female_check = '';
                                                    }
                                                    ?>
                                                    <label class="genderlabel">
                                                        <input type="radio" <?php echo $male_check ?> value="Male" name="eGender" class="uniform"> Male
                                                    </label> 

                                                    <label class="genderlabel">
                                                        <input type="radio" <?php echo $female_check ?> value="Female" name="eGender" class="uniform"> Female
                                                    </label> 

                                                    <label class="genderlabel">
                                                        <input type="radio" <?php echo $notdisclose_check ?> value="Notdisclose" name="eGender" class="uniform"> Not to Disclose
                                                    </label> 
                                                </div>

                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Cuisine Choice</label>
                                                <div class="col-sm-9">
                                                    <select name="iCuisineID[]" id="iCuisineID" class="maxwidth500 col-lg-12" multiple>
                                                        <?php
                                                        $cuisine = $this->user_model->getCuisineDataAll();
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
                                                <label class="col-sm-3 control-label">User Interest</label>
                                                <div class="col-sm-9">
                                                    <select name="iFacilityID[]" id="iFacilityID" class="maxwidth500 col-lg-12" multiple>
                                                        <?php
                                                        $facility = $this->user_model->getFacilityDataAll();
                                                        $fac_array = array();
                                                        foreach ($getFacilityData as $key1 => $cat_value) {
                                                            array_push($fac_array, $cat_value['iInterestID']);
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
                                                <label class="col-sm-3 control-label">Music</label>
                                                <div class="col-sm-9">
                                                    <select name="iMusicID[]" id="iMusicID" class="maxwidth500 col-lg-12" multiple>
                                                        <?php
                                                        $music = $this->user_model->getMusicDataAll();
                                                        $music_array = array();
                                                        foreach ($getMusicData as $key1 => $cat_value) {
                                                            array_push($music_array, $cat_value['iMusicID']);
                                                        }
//print_r($cat_value);
                                                        foreach ($music as $key => $value) {
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
        <script>
            function load_image(id, ext) {
                alert(ext + ' admin ');
                if (validateExtension(ext) == false) {
                    alert("Upload only JPEG or PNG format.");
                    return;
                }
            }

            function validateExtension(v) {
                var allowedExtensions = new Array("jpg", "JPG", "jpeg", "JPEG", "png", "PNG");
                for (var ct = 0; ct < allowedExtensions.length; ct++) {
                    sample = v.lastIndexOf(allowedExtensions[ct]);
                    if (sample != -1) {
                        return true;
                    }
                }
                return false;
            }
            function resetFileInput($element) {
                var clone = $element.clone();
                $element.replaceWith(clone);
            }
            var usrId = parseInt('<?= (isset($iUserID) && $iUserID != '') ? $iUserID : 0; ?>');
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if ((usrId > 0 && permission.indexOf('2') >= 0) || (usrId == 0 && permission.indexOf('1') >= 0)) {

                } else if (permission.indexOf('6') >= 0 || permission.indexOf('1') >= 0 || permission.indexOf('2') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }


                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                $("#validateForm").validate({
                    rules: {
                        vEmail: {
                            required: true,
                            email: true
                        },
                        vMobileNo: {
                            required: true,
                            number: true,
                            maxlength: 10
                        },
                        vPassword: {
                            required: true,
                            minlength: 5
                        },
                        confirm_password: {
                            required: true,
                            minlength: 5,
                            equalTo: "#vPassword"
                        }
                    },
                    messages: {
                        vEmail: {
                            required: "Please enter email address",
                            email: "Please enter valid email address"
                        },
                        vMobileNo: {
                            required: "Please enter mobile number",
                            number: "Please enter valid mobile number",
                            maxlength: "You are not allow to enter more number."
                        },
                        vPassword: {
                            required: "Please provide a password",
                            minlength: "Your password must be at least 5 characters long"
                        },
                        confirm_password: {
                            required: "Please provide a password",
                            minlength: "Your password must be at least 5 characters long",
                            equalTo: "Please enter the same password as above"
                        }
                    }
                });

                var _URL = window.URL || window.webkitURL;
                $("#vProfilePicture").change(function (e) {
                    var $file = $(this);
                    var fileExt = ($file.val()).split('.').pop().toUpperCase();

                    var image, file;
                    if (file = this.files[0]) {
                        image = new Image();
                        image.onload = function () {
                            if (fileExt == 'JPG' || fileExt == 'JPEG' || fileExt == 'PNG') {
                                if (parseInt(this.width) > 640 && parseInt(this.height) > 640) {
                                    alert('File dimension should be 640 x 640');
                                    //$file.replaceWith($file.val('').clone(true));
                                    $('#removebtn').trigger('click');
                                }
                            } else {
                                alert('Please upload valid image type.');
                                //$file.replaceWith($file.val('').clone(true));
                                $('#removebtn').trigger('click');
                            }
                        };
                        image.src = _URL.createObjectURL(file);
                    }
                });
                document.getElementById('removepic').value = 0;

                // $('.fileinput').fileinput()
                $('.uniform').uniform();
                $('#iCuisineID').select2();
                $('#iFacilityID').select2();
                $('#iMusicID').select2();
            });


            $(document).on('click', '#removebtn', function (event) {
                $("#removepic").val('1');
            });
            $(document).on('click', '.select_pic', function (event) {
                $("#removepic").val('0');
            });

        </script>
    </body>
</html>
