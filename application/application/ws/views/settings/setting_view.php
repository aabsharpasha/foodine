<?php
$headerData = $this->headerlib->data();
if (isset($record_set) && $record_set != '')
    extract($record_set);
$form_attr =
        array(
            'name' => 'admin-form',
            "id" => "validateForm",
            'method' => 'post',
            'class' => "form-horizontal",
            'role' => 'form'
);
$Contactmail =
        array(
            'name' => 'vContactmail',
            'id' => 'vContactmail',
            'placeholder' => 'Please provide ContactUs Email',
            'value' => (isset($vContactmail) && $vContactmail != '') ? $vContactmail : '',
            'type' => 'text',
            'class' => 'form-control maxwidth500'
);
$Othermail =
        array(
            'name' => 'vCompanymail',
            'id' => 'vCompanymail',
            'placeholder' => 'Please provide Company Email',
            'value' => (isset($vCompanymail) && $vCompanymail != '') ? $vCompanymail : '',
            'type' => 'text',
            'class' => 'form-control maxwidth500'
);

// Setting Hidden action attributes for Add/Edit functionality.
$hiddeneditattr =
        array(
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
    'value' => "Edit Setting",
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
                                            <li>Settings</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Settings</h3>
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
                                            <h4><i class="fa fa-bars"></i><?php "Edit Admin"; ?></h4>
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
                                            <?php
                                            echo form_open("setting", $form_attr);
                                            if (isset($iAdminID) && $iAdminID != '') {
                                                echo form_hidden($user_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Contactus Email<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($Contactmail); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Company Email<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?php echo form_input($Othermail); ?>
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
            jQuery(document).ready(function() {
                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                $("#validateForm").validate({
                    rules: {
                        // vCountry: {
                        //  required: true,
                        // },
                        vContactmail: {
                            required: true,
                            email: true
                        },
                        vCompanymail: {
                            required: true,
                            email: true
                        }


                    },
                    messages: {
                        vContactmail: "Please enter a Email",
                        vCompanymail: "Please enter a Email",
                    }
                });
            });
        </script>
    </body>
</html>  
