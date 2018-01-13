<?php
$headerData = $this->headerlib->data();
if (isset($getDealsData) && $getDealsData != '')
    extract($getDealsData);

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
if ($ADMINTYPE == 3) {
    $ADMIN_iRestaurantID = $this->session->userdata('iRestaurantID');
} else {
    $ADMIN_iRestaurantID = isset($iRestaurantID) ? $iRestaurantID : 0;
}


$permission = get_page_permission($ADMINTYPE, 40);

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
    'placeholder' => 'Please enter offer title',
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
    'readonly' => 'readonly',
    'class' => 'form-control maxwidth500'
);

$vDealCode_text = array(
    'name' => 'vDealCode',
    'id' => 'vDealCode',
    'placeholder' => 'Please enter deal code',
    "data-errortext" => "This is start date text!",
    'value' => (isset($vDealCode) && $vDealCode != '') ? $vDealCode : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$dtExpiryDateField = array(
    'name' => 'dtExpiryDate',
    'id' => 'dtExpiryDate',
    "required" => "required",
    'readonly' => 'readonly',
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
    'class' => 'submit btn btn-sm btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Deal",
    'type' => 'submit'
);
$cancel_attr = array(
    'class' => 'btn btn-inverse ',
    'value' => "Reset",
    'type' => 'reset'
);

$pic = (isset($vDealImage) && $vDealImage != '') ? $vDealImage : '';
$uid = (isset($iDealID) && $iDealID != '') ? $iDealID : '';
$offerTypeId = (isset($iOfferType) && $iOfferType != '') ? $iOfferType : '';
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
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Deal"; ?></h4>
                                            <div class="tools hidden-xs">

                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>

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

                                            <?php if ($ADMINTYPE != 3) { ?>
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
                                                <label class="col-sm-3 control-label" for="iRestaurantID">Offer Type <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="iOfferType" id="iOfferType" class="maxwidth500 col-lg-12" required="required">
                                                        <option value=""> - Select Offer Type - </option>
                                                        <?php
                                                        foreach ($getOfferTypeData as $key => $value) {
                                                            if (isset($offerTypeId) && $value['offerTypeId'] == $offerTypeId) {
                                                                echo '<option value="' . $value['offerTypeId'] . '" selected="selected">' . $value['offerTypeName'] . '</option>';
                                                            } else {
                                                                echo '<option value="' . $value['offerTypeId'] . '">' . $value['offerTypeName'] . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="vOfferText">Offer Title <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?= form_input($vOfferTextField); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Deal Image</label>
                                                <div class="col-sm-9">

                                                    <?php
                                                    if ($pic != '' && $uid != '') {
                                                        $pic_str = '<img src="' . IMGURL . '/deal/' . $uid . '/thumb/' . $pic . '">';
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
                                                        <input type="hidden" name="vOfferUrl" value="<?php echo $pic; ?>"/>
                                                        <div>
                                                            <span class="btn btn-default btn-file">
                                                                <span class="<?php echo $sel_text ?> select_pic" data-trigger="fileinput"  >Select image</span>
                                                                <span class="<?php echo $change_text ?> select_pic" data-trigger="fileinput">Change</span>
                                                                <input type="file" 
                                                                       name="vDealImage" 
                                                                       accept="image/jpg, image/JPG,image/JPEG, image/jpeg, image/png, image/PNG" 
                                                                       id="vDealImage" />
                                                            </span>
                                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput" id="removebtn">Remove</a>
                                                        </div>
                                                        <input type="hidden" id="removepic" name="removepic" value="0" />
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tOfferDetail" required="required">Offer Detail </label>
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
                                                            <textarea class="ckeditor" id="tOfferDetail" name="tOfferDetail" placeholder="Please enter offer details"><?= isset($tOfferDetail) && $tOfferDetail != '' ? $tOfferDetail : ''; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <!-- /CKE --> 
                                                </div>
                                            </div>
                                           
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tTermsOfUse" required="required">Terms Of Use </label>
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
                                                            <textarea class="ckeditor" id="tTermsOfUse" name="tTermsOfUse" placeholder="Please enter terms of use"><?= isset($tTermsOfUse) && $tTermsOfUse != '' ? $tTermsOfUse : ''; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <!-- /CKE --> 
                                                </div>
                                            </div> 

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="vDealCode">Deal Code<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?= form_input($vDealCode_text); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="dtStartDate">Start Date <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?= form_input($dtStartDateField); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="dtExpiryDate">Expiry Date <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?= form_input($dtExpiryDateField); ?>
                                                </div>
                                            </div>

                                            <?php
                                            $vDaysAllowValue = array();
                                            if (isset($vDaysAllow)) {
                                                $vDaysAllowValue = explode(',', $vDaysAllow);
                                            }
                                            ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="vDaysAllow">Days Allow</label>
                                                <div class="col-sm-9">
                                                    <select name="vDaysAllow[]" id="vDaysAllow" multiple="multiple">
                                                        <option value="1" <?= in_array('1', $vDaysAllowValue) ? 'selected="selected"' : NULL; ?>>Sunday</option>
                                                        <option value="2" <?= in_array('2', $vDaysAllowValue) ? 'selected="selected"' : NULL; ?>>Monday</option>
                                                        <option value="3" <?= in_array('3', $vDaysAllowValue) ? 'selected="selected"' : NULL; ?>>Tuesday</option>
                                                        <option value="4" <?= in_array('4', $vDaysAllowValue) ? 'selected="selected"' : NULL; ?>>Wednesday</option>
                                                        <option value="5" <?= in_array('5', $vDaysAllowValue) ? 'selected="selected"' : NULL; ?>>Thursday</option>
                                                        <option value="6" <?= in_array('6', $vDaysAllowValue) ? 'selected="selected"' : NULL; ?>>Friday</option>
                                                        <option value="7" <?= in_array('7', $vDaysAllowValue) ? 'selected="selected"' : NULL; ?>>Saturday</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!--                                            <div class="form-group">
                                                                                            <label class="col-sm-3 control-label">Specific Deal</label>
                                                                                            <div class="col-sm-9">
                                                                                                <input type="checkbox" 
                                                                                                       id="eSpecific" 
                                                                                                       name="eSpecific" 
                                                                                                       value="1" 
                                                                                                       <?//= isset($eSpecific) && $eSpecific == 'yes' ? 'checked="checked"' : ''; ?>/>
                                                                                                <label class="font-12 error">Check to set as a specific deal</label>
                                                                                            </div>
                                                                                        </div>-->

                                            <?php /* <div class="form-group">
                                              <label class="col-sm-3 control-label">Active Timing</label>
                                              <div class="col-sm-9">
                                              <input type="text"
                                              class="form-control maxwidth500"
                                              placeholder="Active timing"
                                              id="vOfferTiming"
                                              name="vOfferTiming"
                                              value="<?= isset($vOfferTiming) ? $vOfferTiming : ''; ?>"/>
                                              </div>
                                              </div> */ ?>

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
        <script type="text/javascript" src="<?= base_url() ?>js/ckeditor/ckeditor.js"></script>
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.css" type="text/css" media="screen" />
        <script src="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript" charset="utf-8"></script>
        <script>
            var _minDay, _maxDay;
            _minDay = _maxDay = -1;
            var $datepicker1 = $("#dtStartDate");
            var $datepicker2 = $("#dtExpiryDate");
            var dealId = parseInt('<?= (isset($iDealID) && $iDealID != '') ? $iDealID : 0; ?>');
            jQuery(document).ready(function() {
                var permission = <?= json_encode($permission); ?>;
                if ((dealId > 0 && permission.indexOf('2') >= 0) || (dealId == 0 && permission.indexOf('1') >= 0)) {

                } else if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                var iDealID = '<?= isset($iDealID) ? $iDealID : 0; ?>';
                $("#validateForm").validate({
                    rules: {
                        iRestaurantID: {
                            required: true
                        },
                        iOfferType: {
                            required: true
                        },
                        vDealCode: {
                            required: true,
                            remote: {
                                url: BASEURL + '<?= $this->controller; ?>/checkDealCode',
                                type: 'post',
                                data: {
                                    iDealID: iDealID
                                }
                            }
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
                        iOfferType: "Please select offer type",
                        vDealCode: {
                            required: 'Please enter deal code.',
                            remote: 'Entered deal code is already exists.'
                        },
                        vOfferText: 'Please enter offer text',
                        tTermsOfUse: 'Please enter terms of use',
                        dtStartDate: 'Please select start date',
                        dtExpiryDate: 'Please select expiry date'
                    }
                });

                // $('.fileinput').fileinput()
                $('.uniform').uniform();
                $('#iRestaurantID').select2();
                $('#iOfferType').select2();

                $('#vDaysAllow').multiselect({
                    includeSelectAllOption: true,
                    selectAllText: 'All Days!',
                    selectAllValue: 'all'
                });

                $datepicker1.datepicker({
                    minDate: 0,
                    changeMonth: true,
                    changeYear: true,
                    onClose: function(selectedDate) {
                        $("#dtExpiryDate").datepicker("option", "minDate", selectedDate);
                    },
                    onSelect: function(dateText, inst) {
                        var date = $(this).datepicker('getDate');
                        _minDay = date.getDay();
                        dayShowHide();
                    }
                });
                if (iDealID > 0) {
                    var date_exp = '<?= isset($dtStartDate) ? $dtStartDate : ''; ?>';
                    date_exp = date_exp.split('-');
                    var yr = date_exp[0];
                    var mn = parseInt(date_exp[1]) - 1;
                    var dt = date_exp[2].split(' ');
                    dt = dt[0];

                    $datepicker2.datepicker({
                        yearRange: yr + ':c+1',
                        minDate: new Date(yr, mn, dt),
                        changeMonth: true,
                        changeYear: true,
                        onSelect: function(dateText, inst) {
                            var date = $(this).datepicker('getDate');
                            _maxDay = date.getDay();
                            dayShowHide();
                        }
                    });
                } else {
                    $datepicker2.datepicker({
                        changeMonth: true,
                        changeYear: true,
                        onSelect: function(dateText, inst) {
                            var date = $(this).datepicker('getDate');
                            _maxDay = date.getDay();
                            dayShowHide();
                        }
                    });
                }


                var _URL = window.URL || window.webkitURL;
                $("#vDealImage").change(function(e) {
                    var $file = $(this);
                    var fileExt = ($file.val()).split('.').pop().toUpperCase();

                    var image, file;
                    if (file = this.files[0]) {
                        image = new Image();
                        image.onload = function() {
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

                $(document).on('click', '#removebtn', function(event) {
                    $("#removepic").val('1');
                });
                $(document).on('click', '.select_pic', function(event) {
                    $("#removepic").val('0');
                });

                $('.uniform').uniform();
            });

            function dayShowHide() {
                /*
                 * RESET DROPDOWN
                 */
                $('.multiselect.dropdown-toggle.btn.btn-default').attr('title', '').html('None selected <b class="caret"></b>');
                $('ul.multiselect-container.dropdown-menu li').each(function() {
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
                    $('ul.multiselect-container.dropdown-menu li').each(function() {
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
                return array.filter(function(el, index, arr) {
                    return index == arr.indexOf(el);
                });
            }
        </script>
    </body>
</html>
