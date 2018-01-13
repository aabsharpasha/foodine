<?php
$headerData = $this->headerlib->data();
if (isset($getVoucherData) && $getVoucherData != '')
    extract($getVoucherData);

//print_r($getVoucherData);

$form_attr = array(
    'name' => 'voucher-form',
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
    "action" => "backoffice.voucheredit"
);
$hiddenaddattr = array(
    "action" => "backoffice.voucheradd"
);
$voucher_id = array(
    "iVoucherID" => (isset($iVoucherID) && $iVoucherID != '') ? $iVoucherID :  ''
);
$submit_attr = array(
    'class' => 'submit btn-sm btn btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Voucher",
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
                                            <li>Voucher</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Voucher</h3>
                                        </div>
                                        <div class="description">Add/Edit Voucher</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Voucher"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("voucher/add", $form_attr);
                                            if (isset($iVoucherID) && $iVoucherID != '') {
                                                echo form_hidden($voucher_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="vTitle">Voucher Title<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" id="vTitle" name="vTitle" value="<?php echo (isset($vTitle) && $vTitle != '') ? $vTitle : ''; ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="vCode">Voucher Code<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" id="vCode" name="vCode" value="<?php echo (isset($vCode) && $vCode != '') ? $vCode : ''; ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="vDescription" required="required">Voucher Description<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <textarea class="maxwidth500 form-control" id="vDescription" name="vDescription"><?= isset($vDescription) && $vDescription != '' ? $vDescription : ''; ?></textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tStartDate">Valid From <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?=
                                                    form_input(array(
                                                        'name' => 'tStartDate',
                                                        'id' => 'tStartDate',
                                                        "required" => "required",
//                                                        'placeholder' => 'Please select start date',
                                                        "data-errortext" => "This is start date text!",
                                                        'value' => (isset($tStartDate) && $tStartDate != '') ? date('m/d/Y', strtotime($tStartDate)) : '',
                                                        'type' => 'text',
                                                        'class' => 'form-control maxwidth500'
                                                    ));
                                                    ?>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tEndDate">Valid Upto <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?=
                                                    form_input(array(
                                                        'name' => 'tEndDate',
                                                        'id' => 'tEndDate',
                                                        "required" => "required",
//                                                        'placeholder' => 'Please select start date',
                                                        "data-errortext" => "This is end date text!",
                                                        'value' => (isset($tEndDate) && $tEndDate != '') ? date('m/d/Y', strtotime($tEndDate)) : '',
                                                        'type' => 'text',
                                                        'class' => 'form-control maxwidth500'
                                                    ));
                                                    ?>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="eValueType">Value Type<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="eValueType" id="eValueType">
                                                        <option value="percentage" <?= (isset($eValueType) && $eValueType=='percentage') ? 'selected="selected"' : ''; ?>>Percentage</option>
                                                        <option value="fixed" <?= (isset($eValueType) && $eValueType=='fixed') ? 'selected="selected"' : ''; ?>>Fixed</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="dValue">Voucher Value<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" id="dValue" name="dValue" value="<?php echo (isset($dValue) && $dValue != '') ? $dValue : ''; ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="dMinOrderValue">Minimum Order Value<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" id="dMinOrderValue" name="dMinOrderValue" value="<?php echo (isset($dMinOrderValue) && $dMinOrderValue != '') ? $dMinOrderValue : ''; ?>">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="eUserSpecific">User Specific<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="eUserSpecific" id="eUserSpecific" onchange="voucherpublic(this.value)">
                                                        <option value="No" <?= (isset($eUserSpecific) && $eUserSpecific=='No') ? 'selected="selected"' : ''; ?>>No</option>
                                                        <option value="Yes" <?= (isset($eUserSpecific) && $eUserSpecific=='Yes') ? 'selected="selected"' : ''; ?>>Yes</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="eOneTimeUsable">One Time Usable<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="eOneTimeUsable" id="eOneTimeUsable">
                                                        <option value="No" <?= (isset($eOneTimeUsable) && $eOneTimeUsable=='No') ? 'selected="selected"' : ''; ?>>No</option>
                                                        <option value="Yes" <?= (isset($eOneTimeUsable) && $eOneTimeUsable=='Yes') ? 'selected="selected"' : ''; ?>>Yes</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group" id='public-voucher'>
                                                <label class="col-sm-3 control-label" for="publicvoucher">Public<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="publicvoucher" id="publicvoucher">
                                                        <option value="No" <?= (isset($ePublic) && $ePublic=='No') ? 'selected="selected"' : ''; ?>>No</option>
                                                        <option value="Yes" <?= (isset($ePublic) && $ePublic=='Yes') ? 'selected="selected"' : ''; ?>>Yes</option>
                                                    </select>
                                                    (If User Specific is 'Yes' then this value can not be public) 
                                                </div>
                                            </div>
<!--                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iVoucherUseId">Voucher Use <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="iVoucherUseId" id="iVoucherUseId" class="maxwidth500 col-lg-12" required="required">
                                                        <option value="" > - Select Voucher Use - </option>
                                                        <?php
//                                                        foreach ($voucherUseData as $key => $value) {
//                                                            if (isset($iVoucherUseId) && $value['iVoucherUseId'] == $iVoucherUseId) {
//                                                                echo '<option value="' . $value['iVoucherUseId'] . '" selected="selected">' . $value['vDescription'] . '</option>';
//                                                            } else {
//                                                                echo '<option value="' . $value['iVoucherUseId'] . '">' . $value['vDescription'] . '</option>';
//                                                            }
//                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>-->
                                            
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
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.css" type="text/css" media="screen" />
        <script src="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript" charset="utf-8"></script>
        <script>
            jQuery(document).ready(function ()
            {

                $('#tStartDate').datepicker({
                    minDate: 0,
                    changeMonth: true,
                    changeYear: true,
//                    onClose: function(selectedDate) {
//                        $("#iDateofEvent").datepicker("option", "minDate", selectedDate);
//                    },
                    onSelect: function (dateText, inst) {
                        var date = $(this).datepicker('getDate');
//                        _minDay = date.getDay();
//                        dayShowHide();
                    }
                });

                $('#tEndDate').datepicker({
                    minDate: 0,
                    changeMonth: true,
                    changeYear: true,
//                    onClose: function(selectedDate) {
//                        $("#iDateofEvent").datepicker("option", "minDate", selectedDate);
//                    },
                    onSelect: function (dateText, inst) {
                        var date = $(this).datepicker('getDate');
//                        _minDay = date.getDay();
//                        dayShowHide();
                    }
                });
                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                $("#validateForm").validate({
                    rules: {
                        vTitle: {required: true},
                        vCode: {required: true},
                        vDescription: {required: true },
                        tStartDate: {required: true },
                        tEndDate: {required: true },
                        dValue: {required: true },
                        eValueType: {required: true },
                        dMinOrderValue: {required: true },
                        eUserSpecific: {required: true },
                        eOneTimeUsable: {required: true },
//                        iVoucherUseId: {required: true }
                    },
                    messages: {
                        vTitle: {required: "Please Enter Title."},
                        vCode: {required: "Please Enter Code."},
                        vDescription: {required: "Please Enter Description." },
                        tStartDate: {required: "Please Select Valid From Date." },
                        tEndDate: {required: "Please Select Valid Upto Date." },
                        dValue: {required: "Please Enter Value." },
                        eValueType: {required: "Please Select Value Type." },
                        dMinOrderValue: {required: "Please Enter minimum Order Value." },
                        eUserSpecific: {required: "Please Select User Specific." },
                        eOneTimeUsable: {required: "Please Select One Time Usable." },
//                        iVoucherUseId: {required: "Please Select Voucher Use." }
                    }
                });

                $('.uniform').uniform();
            });
            function voucherpublic(val) {
                var userSpecific = $('#eUserSpecific').val();
                if(val=='Yes') {
                    $('#public-voucher').hide();
                }else {
                    $('#public-voucher').show();
                }
            }
            function dayShowHide() {
                /*
                 * RESET DROPDOWN
                 */
                $('.multiselect.dropdown-toggle.btn.btn-default').attr('title', '').html('None selected <b class="caret"></b>');
                $('ul.multiselect-container.dropdown-menu li').each(function () {
                    var $li = $(this);
                    if (!$li.hasClass('multiselect-item')) {
                        var $chk = $li.find('input[type="checkbox"]');
                        $chk.attr({checked: false}).removeAttr('disabled');
                        $li.removeClass('active').show();

                    }
                });

                if (_minDay != -1 && _maxDay != -1) {
                    var disallow = [];
                    var fromDate = $datepicker1.datepicker('getDate');
                    var toDate = $datepicker2.datepicker('getDate');
                    // date difference in millisec
                    var diff = new Date(toDate - fromDate);
                    // date difference in days
                    var days = diff / 1000 / 60 / 60 / 24;
                    if (days < 6) {
                        for (var i = _minDay; i <= 6; i++) {
                            if (i >= _minDay)
                                disallow.push(i);
                        }
                        if (_maxDay > _minDay) {
                            for (var i = _minDay; i <= _maxDay; i++) {
                                if (i <= _maxDay)
                                    disallow.push(i);
                            }
                        } else {
                            for (var i = 0; i <= _maxDay; i++) {
                                if (i <= _maxDay)
                                    disallow.push(i);
                            }
                        }
                        var uniq = unique(disallow);
                        uniq.sort();
                        //console.log(uniq);
                        var return_arr = [];
                        for (var i = 0; i < 7; i++) {
                            if (!(uniq.indexOf(i) >= 0))
                                return_arr.push(i);
                        }
                        disallow = return_arr;
                    } else {
                        disallow = [];
                    }

                    //alert('in')

                    /*
                     * GOING TO HIDE DROP DOWN LIST ~LI~
                     */
                    var inc = 0;
                    $('ul.multiselect-container.dropdown-menu li').each(function () {
                        var $li = $(this);
                        if (!$li.hasClass('multiselect-item')) {
                            //console.log(inc + ' >> ' + disallow.indexOf(inc));
                            if (disallow.indexOf(inc) >= 0) {
                                var $chk = $li.find('input[type="checkbox"]');
                                $chk.attr({checked: false, disabled: 'disabled'});
                                //console.log($li);
                                $li.hide();
                            }
                            inc++;
                        }
                    });
                }
            }

            function unique(array) {
                return array.filter(function (el, index, arr) {
                    return index == arr.indexOf(el);
                });
            }


        </script>
    </body>
</html>
