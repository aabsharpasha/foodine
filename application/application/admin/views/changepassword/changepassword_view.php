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
$oldPassword = array(
    'name' => 'vOldPassword',
    'id' => 'vOldPassword',
    'title' => 'Your old password',
    "required" => "required",
    "data-errortext" => "This is your old password!",
    'class' => 'form-control maxwidth500'
);
$newPassword = array(
    'name' => 'vPassword',
    'id' => 'vPassword',
    'title' => 'Your new password',
    "required" => "required",
    "data-errortext" => "This is your new password!",
    "class" => "form-control maxwidth500"
);
$confirmPassword = array(
    'name' => 'vConfirmPassword',
    'id' => 'vConfirmPassword',
    'title' => 'Your confirm password',
    "required" => "required",
    "data-errortext" => "This is your confirm password!",
    "class" => "form-control maxwidth500"
);
$submit_attr = array(
    'class' => 'submit btn btn-primary marginright20',
    'value' => "Submit",
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
                                            <h3 class="content-title pull-left">Change password</h3>
                                        </div>
                                        <div class="description">Change password and more</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-bars"></i>Change Password</h4>
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
                                                <a href="javascript:;" class="remove">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php echo form_open("changepassword", $form_attr); ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Old Password</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_password($oldPassword); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">New Password</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_password($newPassword); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Confirm Password</label>
                                                <div class="col-sm-9">
                                                    <?php echo form_password($confirmPassword); ?>
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
            jQuery(document).ready(function () {
                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                $("#validateForm").validate({
                    rules: {
                        vOldPassword: {
                            required: true,
                            minlength: 5
                        },
                        vPassword: {
                            required: true,
                            minlength: 5
                        },
                        vConfirmPassword: {
                            required: true,
                            equalTo: '#vPassword'
                        }
                    },
                    messages: {
                        vOldPassword: {
                            required: "Please provide a password",
                            remote: jQuery.format('{0} is already in use, please choose a different name'),
                            minlength: "Your password must be at least 5 characters long",
                        },
                        vPassword: {
                            required: "Please provide a password",
                            minlength: "Your password must be at least 5 characters long"
                        },
                        vConfirmPassword: {
                            required: 'Please provide your confirm password',
                            equalTo: 'Your new password and confirm password are not same'
                        }
                    }
                });
            });
        </script>
    </body>
</html>  
