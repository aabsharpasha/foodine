<?php
$headerData = $this->headerlib->data();
if (isset($getPermissionTypeData) && $getPermissionTypeData != '')
    extract($getPermissionTypeData);


//print_r($getMusicData);

$form_attr = array(
    'name' => 'music-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
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
    "action" => "backoffice.edit"
);
$hiddenaddattr = array(
    "action" => "backoffice.add"
);
$admin_id = array(
    "iAdminID" => (isset($iAdminID) && $iAdminID != '') ? $iAdminID : ''
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
?>
<!doctype html>
<html lang="en-us">
    <head>
        <title><?= $title ?></title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets_form']; ?>
        <style type="text/css"> .genderlabel{ display:inline-block; margin-right: 10px; }  .genderlabel .radio{ padding-top: 5px;} </style>
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
                                            <li>Permission User</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Permission User</h3>
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
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Permission User"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("permission/add_user", $form_attr);
                                            if (isset($iAdminID) && $iAdminID != '') {
                                                echo form_hidden($admin_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">First Name <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vFirstName" id="vFirstName" value="<?= isset($vFirstName) && $vFirstName != '' ? $vFirstName : ''; ?>" />
                                                </div>
                                            </div> 

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Last Name <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vLastName" id="vLastName" value="<?= isset($vLastName) && $vLastName != '' ? $vLastName : ''; ?>" />
                                                </div>
                                            </div> 

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Email<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vEmail" id="vEmail" value="<?= isset($vEmail) && $vEmail != '' ? $vEmail : ''; ?>" />
                                                </div>
                                            </div> 
                                            <?php if (@$iAdminID != ''): ?>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label">Change Password</label>
                                                    <div class="col-sm-9 center maxwidth500">
                                                        <button id="passchange" class="btn btn-light-grey">Change Password</button>
                                                    </div>
                                                </div>
                                            <?php endif ?>

                                            <div class="form-group <?= (@$iAdminID != '') ? 'passtohide' : '' ?>">
                                                <label class="col-sm-3 control-label">Password<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" id="vPassword" name="vPassword" type="password" />
                                                </div>
                                            </div> 

                                            <div class="form-group <?= (@$iAdminID != '') ? 'passtohide' : '' ?>">
                                                <label class="col-sm-3 control-label">Confirm Password<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" id="vConfirmPassword" name="vConfirmPassword" type="password" />
                                                </div>
                                            </div> 

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Admin Type<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select class="maxwidth500 col-lg-12" name="iAdminTypeID" id="admin_type">
                                                        <option value="">- Select Admin Type - </option>
                                                        <?php foreach ($admin_types as $val) { ?>
                                                            <option value="<?= $val['iAdminTypeID']; ?>" <?= isset($iAdminTypeID) && $iAdminTypeID == $val['iAdminTypeID'] ? 'selected="selected"' : ''; ?>><?= $val['vAdminTitle']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div> 

                                            <div class="form-group" id="rest_contain">
                                                <label class="col-sm-3 control-label">Restaurant<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select class="maxwidth500 col-lg-12" name="iRestaurantID" id="restaurant_id">
                                                        <option value="">- Select Restaurant -</option>
                                                        <?php foreach ($get_restaurants as $val) { ?>
                                                            <option value="<?= $val['iRestaurantID']; ?>" <?= isset($iRestaurantID) && $iRestaurantID == $val['iRestaurantID'] ? 'selected="selected"' : ''; ?>><?= $val['vRestaurantName']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <?php echo form_input($submit_attr); ?>
                                                    <a class="btn btn-sm btn-grey" href="<?= BASEURL .''. $this->controller; ?>/user">Cancel</a>
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
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.css" type="text/css" media="screen" />
        <script src="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript" charset="utf-8"></script>

        <script>
            jQuery(document).ready(function () {
                var resId = parseInt('<?= isset($iRestaurantID) ? $iRestaurantID : 0 ?>');
                $('#admin_type,#restaurant_id').select2();
                if (resId <= 0) {
                    $('#rest_contain').hide();
                }

                $('#admin_type').change(function () {
                    if ($(this).val() == '3') {
                        $('#rest_contain').show();
                    } else {
                        $('#rest_contain').hide();
                    }
                });

                App.setPage("forms");
                App.init();
                $("#validateForm").validate({
                    rules: {
                        vFirstName: {
                            required: true
                        },
                        vLastName: {
                            required: true
                        },
                        vEmail: {
                            required: true,
                            email: true
                        },
                        vPassword: {
                            required: true,
                            minlength: 6
                        },
                        vConfirmPassword: {
                            required: true,
                            equalTo: "#vPassword"
                        },
                        iAdminTypeID: {
                            required: true
                        }
                    },
                    messages: {
                        vFirstName: "Please enter first Name",
                        vLastName: "Please enter last Name",
                        vEmail: {
                            required: "Please enter email address",
                            email: "Please enter valid email address"
                        },
                        vPassword: {
                            required: "Please enter password",
                            minlength: "Your password must be at least 6 characters long"
                        },
                        vConfirmPassword: {
                            requried: "Please enter confirm password",
                            equalTo: "Please enter the same password as above"
                        },
                        iAdminTypeID: "Please select admin type"
                    }
                });
                $('.uniform').uniform();
            });
        </script>
    </body>
</html>
