<?php
$headerData = $this->headerlib->data();
if (isset($getEmailData) && $getEmailData != ''){
    extract($getEmailData);
}
if(empty($users)){
    $users  = array(); 
}
//print_r($userList);exit;
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 77);
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
    "action" => "backoffice.edit"
);
$hiddenaddattr = array(
    "action" => "backoffice.add"
);
$notify_id = array(
    "iPromotionalEmailId" => (isset($iPromotionalEmailId) && $iPromotionalEmailId != '') ? $iPromotionalEmailId : ''
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
            .multiselect-container{
                max-height: 400px;
                overflow-y: auto;
                width:100% !important;
                max-width: 500px !important;
            }
            .btn-group{
                width:100% !important;
            }
            .btn{
                width:100% !important;
                max-width: 500px !important;
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
                                            <li>Promotional Email</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Promotional Email</h3>
                                        </div>
                                        <div class="description">Send Promotional Email</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Promotional Email"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("promotional_email/add", $form_attr);
                                            if (isset($iPromotionalEmailId) && $iPromotionalEmailId != '') {
                                                echo form_hidden($notify_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Email Subject<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vSubject" value="<?php echo (isset($vSubject) && $vSubject != '') ? $vSubject : ''; ?>">
                                                </div>
                                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="tTermsOfUse" required="required">Email Content </label>
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
                                            <textarea class="ckeditor" id="tContent" name="tContent" placeholder=""><?= isset($tContent) && $tContent != '' ? $tContent : ''; ?></textarea>
                                        </div>
                                    </div>
                                    <!-- /CKE --> 
                                </div>
                            </div>

<!--                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Email Content<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control maxwidth500" name="tContent"><?php echo (isset($tContent) && $tContent != '') ? $tContent : ''; ?></textarea>
                                                </div>
                                            </div>
-->

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tUsers">Select Users<span class="required">*</span></label>
                                                <div class="col-sm-9 user-select">
                                                    <select class="form-control maxwidth500" name="tUsers[]" id="selectUsers" multiple="multiple">
                                                        <?php foreach($userList AS $user){ ?>
                                                        <option value="<?php echo $user["iUserID"]; ?>" <?php if(in_array($user["iUserID"], $users)) echo "selected" ?>><?php echo ucfirst($user["vFirstName"])." ".$user["vLastName"]; ?>[<?php echo $user["iUserID"]; ?>]</option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Other Recipients<br><span style="font-size: 12px;color:red;">(comma seperated)</span></label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control maxwidth500" name="tOtherRecipients"><?php echo (isset($tOtherRecipients) && $tOtherRecipients != '') ? $tOtherRecipients : ''; ?></textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Shedule Time<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <div class="margin-top-10">
                                                        <input type="text" class="form-control datepicker-custom maxwidth500" 
                                                               size="10" 
                                                               id="scheduleDate" 
                                                               placeholder="Enter Date"
                                                               value="<?php echo (isset($scheduleDate)? $scheduleDate:''); ?>"
                                                               name="scheduleDate"/>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Shedule Date<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <div class="margin-top-10">
                                                        <input type="text" 
                                                               class="form-control timepicker-custom maxwidth500" 
                                                               size="10" 
                                                               id="scheduleTime" 
                                                               placeholder="Enter Time"
                                                               value="<?php echo (isset($scheduleTime)? $scheduleTime:''); ?>"
                                                               name="scheduleTime"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <br>
                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <input type="submit" name="senditnow" value="Send" class="btn btn-sm btn-primary"/>
                                                    &nbsp;<a class="btn btn-sm btn-grey" href="<?= BASEURL .''. $this->controller; ?>/index">Cancel</a>
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
        <script type="text/javascript" src="<?= base_url() ?>js/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="<?= JS_URL; ?>js/datepicker/picker.js"></script>
        <script type="text/javascript" src="<?= JS_URL; ?>js/datepicker/picker.date.js"></script>
        <script type="text/javascript" src="<?= JS_URL; ?>js/datepicker/picker.time.js"></script>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>js/magic-suggest/magicsuggest-1.3.1-min.css" />
        <script type="text/javascript" src="<?= base_url() ?>js/magic-suggest/magicsuggest-1.3.1-min.js"></script>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>js/colorpicker/css/colorpicker.min.css" />
        <script type="text/javascript" src="<?= base_url() ?>js/colorpicker/js/bootstrap-colorpicker.min.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/jquery-raty/jquery.raty.min.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/timeago/jquery.timeago.min.js"></script>
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.css" type="text/css" media="screen" />
        <script src="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript" charset="utf-8"></script>

        <script>
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }
                $('#selectUsers').multiselect({
                    includeSelectAllOption: true,
                    selectAllText: 'All Users!',
                    enableFiltering: true,
//                    filterBehavior: 'value'
//                    selectAllValue: 'all'
                });
                
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
                $('td[name*=tcol]')
                $("#validateForm").validate({
                    rules: {
                        vSubject: {
                            required: true
                        },
                        tContent: {
                            required: true
                        },
                        scheduleDate: {
                            required: true
                        },
                        scheduleTime: {
                            required: true
                        },
                        tUsers:{
                            required: true
                        }
                            
                    },
                    messages: {
                        vSubject: "Please enter a email subject",
                        tContent: "Please enter email Content",
                        scheduleDate: "Please enter Notify Date",
                        tUsers: "Please select atleast one user",
                        scheduleTime : "Please enter Notify Time"
                    },
                    submitHandler: function(form) {
                        if( $("input[type=checkbox]:checked").length ==0 ){ 
                            alert("Please select atleat one user !");
                            return;
                        }
                        //return;
                      form.submit();
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
