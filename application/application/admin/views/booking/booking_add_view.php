<?php
$headerData = $this->headerlib->data();
if (isset($getDealsData) && $getDealsData != '')
    extract($getDealsData);

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$ADMIN_iRestaurantID = $this->session->userdata('iRestaurantID');


//print_r($getFacilityData);

$form_attr = array(
    'name' => 'deals-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);

$vOfferTextField = array(
    'name' => 'vOfferText',
    'id' => 'vOfferText',
    "required" => "required",
    'placeholder' => 'Please Offer Text',
    "data-errortext" => "This is offer's text!",
    'value' => (isset($vOfferText) && $vOfferText != '') ? $vOfferText : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$dtStartDateField = array(
    'name' => 'dtStartDate',
    'id' => 'dtStartDate',
    "required" => "required",
    'placeholder' => 'Please select start date',
    "data-errortext" => "This is start date text!",
    'value' => (isset($dtStartDate) && $dtStartDate != '') ? date('m/d/Y', strtotime($dtStartDate)) : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$dtExpiryDateField = array(
    'name' => 'dtExpiryDate',
    'id' => 'dtExpiryDate',
    "required" => "required",
    'placeholder' => 'Please select expiry date',
    "data-errortext" => "This is expiry date text!",
    'value' => (isset($dtExpiryDate) && $dtExpiryDate != '') ? date('m/d/Y', strtotime($dtExpiryDate)) : '',
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
    "action" => "backoffice.dealsedit"
);

$hiddenaddattr = array(
    "action" => "backoffice.dealsadd"
);

$deal_id = array(
    "iDealID" => (isset($iDealID) && $iDealID != '') ? $iDealID : ''
);

$submit_attr = array(
    'class' => 'submit btn btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Booking",
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
                                                <i class="fa fa-home"></i>
                                                <a href="<?= BASEURL ?>">Home</a>
                                            </li>
                                            <li>Deal</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Deal</h3>
                                        </div>
                                        <div class="description">Add/Edit Deal</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-bars"></i><?php echo $ACTION_LABEL . " Deal"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <!--                           <a href="#box-config" data-toggle="modal" class="config">
                                                  <i class="fa fa-cog"></i>
                                                </a> -->

                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                                <!--  <a href="javascript:void(0);" class="remove">
                                                   <i class="fa fa-times"></i>
                                                 </a> -->
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("deals/add", $form_attr);
                                            if (isset($iDealID) && $iDealID != '') {
                                                echo form_hidden($deal_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <?php if ($ADMINTYPE == 1) { ?>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label" for="iRestaurantID">Restaurant</label>
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
                                                <label class="col-sm-3 control-label" for="vOfferText">Offer Text</label>
                                                <div class="col-sm-9">
                                                    <?= form_input($vOfferTextField); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tTermsOfUse" required="required">Terms Of Use</label>
                                                <div class="col-sm-9">
                                                    <textarea class="maxwidth500 form-control" id="tTermsOfUse" name="tTermsOfUse"><?= isset($tTermsOfUse) && $tTermsOfUse != '' ? $tTermsOfUse : ''; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="dtStartDate">Start Date</label>
                                                <div class="col-sm-9">
                                                    <?= form_input($dtStartDateField); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="dtExpiryDate">Expiry Date</label>
                                                <div class="col-sm-9">
                                                    <?= form_input($dtExpiryDateField); ?>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <?php echo form_input($submit_attr); ?>
                                                    <?php echo form_input($cancel_attr); ?>
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
                        iRestaurantID: {
                            required: true
                        },
                        vOfferText: {
                            required: true
                        },
                        tTermsOfUse: {
                            required: true
                        },
                        dtStartDate: {
                            required: true
                        },
                        dtExpiryDate: {
                            required: true
                        }
                    },
                    messages: {
                        iRestaurantID: "Please select restaurant",
                        vOfferText: 'Please enter offer text',
                        tTermsOfUse: 'Please enter terms of use',
                        dtStartDate: 'Please select start date',
                        dtExpiryDate: 'Please select expiry date'
                    }
                });

                // $('.fileinput').fileinput()
                $('.uniform').uniform();
                $('#iRestaurantID').select2();

                $('#dtStartDate').datepicker({
                    minDate: 0,
                    changeMonth: true,
                    changeYear: true,
                    onClose: function(selectedDate) {
                        $("#dtExpiryDate").datepicker("option", "minDate", selectedDate);
                    }
                });
                $('#dtExpiryDate').datepicker({
                    changeMonth: true,
                    changeYear: true
                });

            });
        </script>
    </body>
</html>
