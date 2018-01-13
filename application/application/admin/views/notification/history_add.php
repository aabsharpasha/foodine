<?php
$headerData = $this->headerlib->data();
if (isset($getNotificationData) && $getNotificationData != '')
    extract($getNotificationData);

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 35);
//print_r($getMusicData);

$form_attr = array(
    'name' => 'music-form',
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
$notify_id = array(
    "iPushNotifyID" => (isset($iPushNotifyID) && $iPushNotifyID != '') ? $iPushNotifyID : ''
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
                                            <li>Notification</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Notification</h3>
                                        </div>
                                        <div class="description">Send Notification</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Notification"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("notification/addhistory", $form_attr);
                                            if (isset($iNotificationID) && $iNotificationID != '') {
                                                echo form_hidden($notify_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Notification Title<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vNotifyTitle" value="<?php echo (isset($vNotifyTitle) && $vNotifyTitle != '') ? $vNotifyTitle : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Notification Text<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control maxwidth500" name="vNotifyText"><?php echo (isset($vNotifyText) && $vNotifyText != '') ? $vNotifyText : ''; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Schedule Notification<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <div class="col-lg-12 margin0auto padding0">
                                                        <label><input type="checkbox" name="scheduleJob" id="scheduleJob" value="schedule" /> Schedule Job</label>
                                                    </div>
                                                    <div class="col-lg-12  margin0auto padding0">
                                                        <div class="margin-top-10">
                                                            <input type="text" class="form-control datepicker-custom maxwidth500 hide" 
                                                                   size="10" 
                                                                   id="scheduleDate" 
                                                                   placeholder="Enter Date"
                                                                   name="scheduleDate"/>
                                                        </div>
                                                        <div class="margin-top-10">
                                                            <input type="text" 
                                                                   class="form-control timepicker-custom maxwidth500 hide" 
                                                                   size="10" 
                                                                   id="scheduleTime" 
                                                                   placeholder="Enter Time"
                                                                   name="scheduleTime"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <br>
                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <input type="submit" name="senditnow" value="Send" class="btn btn-sm btn-primary"/>
                                                    &nbsp;<a class="btn btn-sm btn-grey" href="<?= BASEURL .''. $this->controller; ?>/history">Cancel</a>
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
        <!-- DATE PICKER -->
        <script type="text/javascript" src="<?= JS_URL; ?>js/datepicker/picker.js"></script>
        <script type="text/javascript" src="<?= JS_URL; ?>js/datepicker/picker.date.js"></script>
        <script type="text/javascript" src="<?= JS_URL; ?>js/datepicker/picker.time.js"></script>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>js/magic-suggest/magicsuggest-1.3.1-min.css" />
        <script type="text/javascript" src="<?= base_url() ?>js/magic-suggest/magicsuggest-1.3.1-min.js"></script>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>js/colorpicker/css/colorpicker.min.css" />
        <script type="text/javascript" src="<?= base_url() ?>js/colorpicker/js/bootstrap-colorpicker.min.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/jquery-raty/jquery.raty.min.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/timeago/jquery.timeago.min.js"></script>

        <script>
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }
                
                $('.datepicker-custom').pickadate({
                    selectYears: true,
                    selectMonths: true,
                    min: new Date()
                });
                $('.timepicker-custom').pickatime({
                    //min: new Date()
                });

                $('#scheduleJob').change(function () {
                    var $check = $(this);
                    var isChecked = $check.is(':checked');
                    //alert(isChecked);
                    if (isChecked) {
                        $('#scheduleDate').removeClass('hide').addClass('show');
                        $('#scheduleTime').removeClass('hide').addClass('show');
                    } else {
                        $('#scheduleDate').removeClass('show').addClass('hide');
                        $('#scheduleTime').removeClass('show').addClass('hide');
                    }
                });
                $("#validateForm").validate({
                    rules: {
                        vNotifyTitle: {
                            required: true
                        },
                        vNotifyText: {
                            required: true
                        },
                        scheduleDate: {
                            required: {
                                depends: function () {
                                    return $('#scheduleJob').is(":checked");
                                }
                            }
                        },
                        scheduleTime: {
                            required: {
                                depends: function () {
                                    return $('#scheduleJob').is(":checked");
                                }
                            }
                        }
                    },
                    messages: {
                        vNotifyTitle: "Please enter a Notify Title Name",
                        vNotifyText: "Please enter a Notify Message Text",
                        scheduleDate: "Please enter Notify Date",
                        scheduleDate: "Please enter Notify Time"
                    }
                });

                App.setPage("elements");  //Set current page
                App.init(); //Initialise plugins and elements


                // $('.fileinput').fileinput()
                $('.uniform').uniform();



            });



        </script>
    </body>
</html>
