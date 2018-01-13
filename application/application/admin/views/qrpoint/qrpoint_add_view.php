<?php
$headerData = $this->headerlib->data();
if (isset($getQRPointData) && $getQRPointData != '')
    extract($getQRPointData);

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$ADMIN_iRestaurantID = $this->session->userdata('iRestaurantID');

$permission = get_page_permission($ADMINTYPE, 42);

//print_r($getFacilityData);

$form_attr = array(
    'name' => 'deals-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);

$iQRCodePoitnsField = array(
    'name' => 'iQRCodePoitns',
    'id' => 'iQRCodePoitns',
    "required" => "required",
    'placeholder' => 'Please QRCode Points',
    "data-errortext" => "This is QRCode points!",
    'value' => (isset($iQRCodePoitns) && $iQRCodePoitns != '') ? $iQRCodePoitns : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$iMinBillAmountField = array(
    'name' => 'iMinBillAmount',
    'id' => 'iMinBillAmount',
    "required" => "required",
    'placeholder' => 'Please enter minimum payable amount',
    "data-errortext" => "This is minimum payable amount!",
    'value' => (isset($iMinBillAmount) && $iMinBillAmount != '') ? $iMinBillAmount : '',
    'type' => 'number',
    'class' => 'form-control maxwidth500'
);
$iMaxBillAmountField = array(
    'name' => 'iMaxBillAmount',
    'id' => 'iMaxBillAmount',
    "required" => "required",
    'placeholder' => 'Please enter maximum payable amount',
    "data-errortext" => "This is maximum payable amount!",
    'value' => (isset($iMaxBillAmount) && $iMaxBillAmount != '') ? $iMaxBillAmount : '',
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
    "action" => "backoffice.qrcodeedit"
);

$hiddenaddattr = array(
    "action" => "backoffice.qrcodeadd"
);

$qrcode_id = array(
    "iQRCodeID" => (isset($iQRCodeID) && $iQRCodeID != '') ? $iQRCodeID : ''
);

$submit_attr = array(
    'class' => 'submit btn-sm btn btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL QRCode",
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
                                            <li>Deal</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">QRCode</h3>
                                        </div>
                                        <div class="description">Add/Edit QRCode</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " QRCode"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("qrpoint/add", $form_attr);
                                            if (isset($iQRCodeID) && $iQRCodeID != '') {
                                                echo form_hidden($qrcode_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <?php if ($ADMINTYPE == 1) { ?>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label" for="iRestaurantID">Restaurant <span class="required">*</span></label>
                                                    <div class="col-sm-9">
                                                        <select name="iRestaurantID" id="iRestaurantID" class="maxwidth500 col-lg-12" required="required">
                                                            <option value="" > - Select Restaurant - </option>
                                                            <?php
                                                            foreach ($getRestaurantData as $key => $value) {
                                                                if (isset($iRestaurantID) && $value['iRestaurantID'] == $iRestaurantID) {
                                                                    echo '<option value="' . $value['iRestaurantID'] . '" selected="selected">' . $value['vRestaurantName'] . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $value['iRestaurantID'] . '">' . $value['vRestaurantName'] . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <input type="hidden" name="iRestaurantID" value="<?= $ADMIN_iRestaurantID; ?>">
                                            <?php } ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iQRCodePoitns">QRCode Points <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?= form_input($iQRCodePoitnsField); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iMinBillAmount">Minimum Amount <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?= form_input($iMinBillAmountField); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iMaxBillAmount">Maximum Amount <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?= form_input($iMaxBillAmountField); ?>
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

        <script>
            var qrId = parseInt('<?= (isset($iQRCodeID) && $iQRCodeID != '') ? $iQRCodeID : 0; ?>');
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if ((qrId > 0 && permission.indexOf('2') >= 0) || (qrId == 0 && permission.indexOf('1') >= 0)) {

                } else if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements

                $("#validateForm").validate({
                    rules: {
                        iRestaurantID: {
                            required: true
                        },
                        iQRCodePoitns: {
                            required: true
                        },
                        iMinBillAmount: {
                            required: true
                        },
                        iMaxBillAmount: {
                            required: true
                        }
                    },
                    messages: {
                        iRestaurantID: "Please select restaurant",
                        iQRCodePoitns: 'Please enter QRCode Points',
                        iMinBillAmount: 'Please enter minimum bill amount',
                        iMaxBillAmount: 'Please select maximu bill amount'
                    }
                });

                // $('.fileinput').fileinput()
                $('.uniform').uniform();
                $('#iRestaurantID').select2();
            });
        </script>
    </body>
</html>
