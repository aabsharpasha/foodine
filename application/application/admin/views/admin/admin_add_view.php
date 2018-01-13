<?php
$headerData = $this->headerlib->data();
if (isset($getUserData) && $getUserData != '')
    extract($getUserData);

$form_attr = array(
    'name' => 'admin-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form'
);
$first_name = array(
    'name' => 'vFirstName',
    'id' => 'vFirstName',
    'placeholder' => 'Please provide first name',
    'value' => (isset($vFirstName) && $vFirstName != '') ? $vFirstName : '',
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
    "data-errortext" => "This is admin's email address!",
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
    "data-errortext" => "This is admin's password!",
    'value' => '',
    'type' => 'password',
    'class' => 'form-control maxwidth500'
);
$confirm_password = array(
    'name' => 'confirm_password',
    'id' => 'confirm_password',
    "required" => "required",
    'placeholder' => 'Please Confirm Password',
    "data-errortext" => "This is admin's password!",
    'value' => '',
    'type' => 'password',
    'minlength' => '5',
    'equalTo' => "#password",
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
    "action" => "backoffice.adminedit"
);
$hiddenaddattr = array(
    "action" => "backoffice.adminadd"
);
$user_id = array(
    "iAdminID" => (isset($iAdminID) && $iAdminID != '') ? $iAdminID : ''
);
$submit_attr = array(
    'class' => 'submit btn btn-primary marginright20',
    'value' => "$ACTION_LABEL Admin",
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
                                            <h4><i class="fa fa-bars"></i><?php echo $ACTION_LABEL . " Admin"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="#box-config" data-toggle="modal" class="config">
                                                    <i class="fa fa-cog"></i>
                                                </a>
                                                <a href="javascript:;" class="reload">
                                                    <i class="fa fa-refresh"></i>
                                                </a>
                                                <a href="javascript:;" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                                <!--  <a href="javascript:;" class="remove">
                                                   <i class="fa fa-times"></i>
                                                 </a> -->
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("admin/add", $form_attr);
                                            if (isset($iAdminID) && $iAdminID != '') {
                                                echo form_hidden($user_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">First Name</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($first_name); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Last Name</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($last_name); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Email<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($email); ?>
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
                                            <br>
                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <?php echo form_input($submit_attr); ?>
                                                    <a href="<?= BASEURL; ?>" class="btn btn-inverse">Cancel</a>
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
        <script>
            jQuery(document).ready(function ()
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
            });
        </script>
    </body>
</html>  
