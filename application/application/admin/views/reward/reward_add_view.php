<?php
$headerData = $this->headerlib->data();
if (isset($getRewardData) && $getRewardData != '')
    extract($getRewardData);

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$ADMIN_iRestaurantID = $this->session->userdata('iRestaurantID');


$permission = get_page_permission($ADMINTYPE, 44);

//print_r($getFacilityData);

$form_attr = array(
    'name' => 'deals-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);

$vRewardTitleField = array(
    'name' => 'vRewardTitle',
    'id' => 'vRewardTitle',
    "required" => "required",
    'placeholder' => 'Reward Title',
    "data-errortext" => "This is reward's title!",
    'value' => (isset($vRewardTitle) && $vRewardTitle != '') ? $vRewardTitle : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$iRewardPointField = array(
    'name' => 'iRewardPoint',
    'id' => 'iRewardPoint',
    "required" => "required",
    'placeholder' => 'Reward Point',
    "data-errortext" => "This is reward's point!",
    'value' => (isset($iRewardPoint) && $iRewardPoint != '') ? $iRewardPoint : '',
    'type' => 'number',
    'class' => 'form-control maxwidth500'
);
$iRewardVoucherField = array(
    'name' => 'iRewardVoucher',
    'id' => 'iRewardVoucher',
    "required" => "required",
    'placeholder' => 'Reward Voucher Value',
    "data-errortext" => "This is reward's Voucher value!",
    'value' => (isset($iRewardVoucher) && $iRewardVoucher != '') ? $iRewardVoucher : '',
    'type' => 'number',
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
    "action" => "backoffice.rewardedit"
);
$hiddenaddattr = array(
    "action" => "backoffice.rewardadd"
);
$reward_id = array(
    "iRewardID" => (isset($iRewardID) && $iRewardID != '') ? $iRewardID : ''
);
$submit_attr = array(
    'class' => 'submit btn btn-sm btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Reward",
    'type' => 'submit'
);
$cancel_attr = array(
    'class' => 'btn btn-inverse ',
    'value' => "Reset",
    'type' => 'reset'
);

$pic = (isset($vRewardImage) && $vRewardImage !== '') ? $vRewardImage : '';
$uid = (isset($iRewardID) && $iRewardID != '') ? $iRewardID : '';
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
                                            <li>Reward</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Reward</h3>
                                        </div>
                                        <div class="description">Add/Edit Reward</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Deal"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("reward/add", $form_attr);
                                            if (isset($iRewardID) && $iRewardID != '') {
                                                echo form_hidden($reward_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="vRewardTitle">Reward Title <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?= form_input($vRewardTitleField); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iRewardPoint">Reward Point <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?= form_input($iRewardPointField); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iRewardVoucher">Reward Voucher Value <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?= form_input($iRewardVoucherField); ?>
                                                    <input type="hidden" name="removepic" id="removepic" value="0">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tRewardDesc">Reward Description<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <textarea name="tRewardDesc" required id="tRewardDesc" placeholder="Reward Description" class="form-control maxwidth500"><?= isset($tRewardDesc) && $tRewardDesc !== '' ? $tRewardDesc : ''; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="dtRewardValidUpto">Reward Valid Date Upto<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="dtRewardValidUpto" id="dtRewardValidUpto" placeholder="Reward Valid Date" class="form-control maxwidth500" value="<?= isset($dtRewardValidUpto) && $dtRewardValidUpto != '' ? date('m/d/Y', strtotime($dtRewardValidUpto)) : ''; ?>"/>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Reward Image</label>
                                                <div class="col-sm-9">

                                                    <?php
                                                    if ($pic != '' && $uid != '') {
                                                        $pic_str = '<img src="' . DOMAIN_URL . '/images/reward/' . $uid . '/thumb/' . $pic . '">';
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
                                                                       name="vRewardImage" 
                                                                       accept="image/jpg, image/JPG,image/JPEG, image/jpeg, image/png, image/PNG" 
                                                                       id="vRewardImage" />
                                                            </span>
                                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput" id="removebtn">Remove</a>
                                                        </div>
                                                        <input type="hidden" id="removepic" name="removepic" value="0" />
                                                    </div>

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
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.css" type="text/css" media="screen" />
        <script src="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript" charset="utf-8"></script>

        <script>
            var rwrdId = parseInt('<?= (isset($iRewardID) && $iRewardID != '') ? $iRewardID : 0; ?>');
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if ((rwrdId > 0 && permission.indexOf('2') >= 0) || (rwrdId == 0 && permission.indexOf('2') >= 0)) {

                } else if (permission.indexOf('6') >= 0 || permission.indexOf('1') >= 0 || permission.indexOf('2') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }


                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                $("#validateForm").validate({
                    rules: {
                        vRewardTitle: {
                            required: true
                        },
                        iRewardPoint: {
                            required: true
                        },
                        iRewardVoucher: {
                            required: true
                        },
                        tRewardDesc: {
                            required: true
                        },
                        dtRewardValidUpto: {
                            required: true
                        }
                    },
                    messages: {
                        vRewardTitle: {
                            required: "Please enter reward title"
                        },
                        iRewardPoint: {
                            required: 'Please enter reward point'
                        },
                        iRewardVoucher: {
                            required: 'Please enter voucher value'
                        },
                        tRewardDesc: {
                            required: 'Please enter reward desciption'
                        },
                        dtRewardValidUpto: {
                            required: 'Please select reward validity date'
                        }
                    }
                });

                var _URL = window.URL || window.webkitURL;
                $("#vRewardImage").change(function (e) {
                    var $file = $(this);
                    var fileExt = ($file.val()).split('.').pop().toUpperCase();

                    var image, file;
                    if (file = this.files[0]) {
                        image = new Image();
                        image.onload = function () {
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
                document.getElementById('removepic').value = 0;
                $(document).on('click', '#removebtn', function (event) {
                    $("#removepic").val('1');
                });
                $(document).on('click', '.select_pic', function (event) {
                    $("#removepic").val('0');
                });

                // $('.fileinput').fileinput()
                $('.uniform').uniform();

                var rewardId = parseInt('<?= isset($iRewardID) && $iRewardID != '' ? $iRewardID : '0' ?>');
                //alert(rewardId);
                if (rewardId === 0) {
                    $('#dtRewardValidUpto').datepicker({
                        minDate: 0,
                        changeMonth: true,
                        changeYear: true
                    });
                } else {
                    $('#dtRewardValidUpto').datepicker({
                        changeMonth: true,
                        changeYear: true
                    });
                }

            });


        </script>
    </body>
</html>
