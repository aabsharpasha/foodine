<?php
$headerData = $this->headerlib->data();
if (isset($getNotificationData) && $getNotificationData != '')
    extract($getNotificationData);


$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 37);

//print_r($getMusicData);

$form_attr = array(
    'name' => 'referal-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);

// Setting Hidden action attributes for Add/Edit functionality.
$hiddeneditattr = array(
    "action" => "backoffice.notificationedit"
);
$hiddenaddattr = array(
    "action" => "backoffice.notificationadd"
);
$referal_id = array(
    "iReferalID" => (isset($iReferalID) && $iReferalID != '') ? $iReferalID : ''
);
?>
<!doctype html>
<html lang="en-us">
    <head>
        <title><?= $title ?></title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets_form']; ?>
        <!-- DATE PICKER -->
        <link rel="stylesheet" type="text/css" href="<?= JS_URL; ?>js/datepicker/themes/default.min.css" />
        <link rel="stylesheet" type="text/css" href="<?= JS_URL; ?>js/datepicker/themes/default.date.min.css" />
        <link rel="stylesheet" type="text/css" href="<?= JS_URL; ?>js/datepicker/themes/default.time.min.css" />

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
                                            <li>Reference Code</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Reference Code</h3>
                                        </div>
                                        <div class="description">Add/Edit Reference Code</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Reference Code"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("referal/add", $form_attr);
                                            if (isset($iReferalID) && $iReferalID != '') {
                                                echo form_hidden($referal_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Reference Name<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vReferalName" 
                                                           id="vRefenceName"
                                                           value="<?php echo (isset($vReferalName) && $vReferalName != '') ? $vReferalName : ''; ?>" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Reference Code<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vReferalCode" 
                                                           id="vRefenceCode"
                                                           value="<?php echo (isset($vReferalCode) && $vReferalCode != '') ? $vReferalCode : ''; ?>" />
                                                </div>
                                            </div>

                                            <br>
                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <input type="submit" name="senditnow" value="Save" class="btn btn-sm btn-primary"/>
                                                    &nbsp;<a class="btn btn-sm btn-grey" href="<?= BASEURL .''. $this->controller; ?>/view">Cancel</a>
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
            var referalId = parseInt('<?= (isset($iReferalID) && $iReferalID != '') ? $iReferalID : 0; ?>');
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if ((referalId > 0 && permission.indexOf('2') >= 0) || (referalId == 0 && permission.indexOf('1') >= 0)) {

                } else if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                $("#validateForm").validate({
                    rules: {
                        vReferalName: {
                            required: true
                        },
                        vReferalCode: {
                            required: true
                        }
                    },
                    messages: {
                        vReferalName: "Please enter a reference name",
                        vReferalCode: "Please enter a reference code"
                    }
                });

                App.setPage("");  //Set current page
                App.init(); //Initialise plugins and elements

                // $('.fileinput').fileinput()
                $('.uniform').uniform();
            });
        </script>
    </body>
</html>
