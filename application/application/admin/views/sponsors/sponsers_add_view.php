<?php
$headerData = $this->headerlib->data();
if (isset($getSponsersData) && $getSponsersData != '')
    extract($getSponsersData);
//mprd($iSponserKeywords);

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$ADMIN_iRestaurantId = $this->session->userdata('iRestaurantId');


//print_r($getFacilityData);

$form_attr = array(
    'name' => 'deals-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
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
    "action" => "backoffice.sponsersedit"
);

$hiddenaddattr = array(
    "action" => "backoffice.sponsersadd"
);

$deal_id = array(
    "iSponserId" => (isset($iSponserId) && $iSponserId != '') ? $iSponserId : ''
);

$submit_attr = array(
    'class' => 'submit btn btn-sm btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Sponsor",
    'type' => 'submit'
);
$cancel_attr = array(
    'class' => 'btn btn-inverse ',
    'value' => "Reset",
    'type' => 'reset'
);

$pic = (isset($vOfferImage) && $vOfferImage != '') ? $vOfferImage : '';
$uid = (isset($iSponserId) && $iSponserId != '') ? $iSponserId : '';
$restid = (isset($iRestaurantId) && $iRestaurantId != '') ? $iRestaurantId : '';
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
                                            <h3 class="content-title pull-left">Sponsors</h3>
                                        </div>
                                        <div class="description">Add/Edit Sponsor</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Sponsor"; ?></h4>
                                            <div class="tools hidden-xs">

                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>

                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("sponsors/add", $form_attr);
                                            if (isset($iSponserId) && $iSponserId != '') {
                                                echo form_hidden($deal_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <?php if ($ADMINTYPE == 1 || $ADMINTYPE == 2) { ?>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label" for="iRestaurantId">Restaurant <span class="required">*</span></label>
                                                    <div class="col-sm-9">
                                                        <select name="iRestaurantId" id="iRestaurantId" class="maxwidth500 col-lg-12" required="required">
                                                            <option value="" > - Select Restaurant - </option>
                                                            <?php
                                                            foreach ($getRestaurantData as $key => $value) {
                                                                
                                                                if (isset($iRestaurantId) && $value['iRestaurantID'] == $iRestaurantId) {
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
                                                <input type="hidden" name="iRestaurantId" value="<?= $ADMIN_iRestaurantId; ?>">
                                            <?php } ?>
                                                <div class="form-group">
                                                <label class="col-sm-3 control-label">Keywords<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="iSponserKeywords[]" id="iSponserKeywords" class="maxwidth500 col-lg-12 required" multiple>
                                                        <?php
                                                        $sponsor = array();
                                                        if(isset($iSponserKeywords) && !empty($iSponserKeywords)){
                                                            $iSponserKeywords = explode(',', $iSponserKeywords);
                                                            $iSponserKeywords = array_unique($iSponserKeywords);
                                                            foreach ($iSponserKeywords as $sponvalue) {
                                                                array_push($sponsor, $sponvalue);
                                                                echo '<option value="' . $sponvalue . '" selected="selected">' . $sponvalue . '</option>';
                                                            }
//                                                            
//                                                            foreach ($iSponserKeywords as $key => $value) {
//                                                            //print_r($value);
//                                                            if (isset($getCategoryData) && !empty($getCategoryData)) {
//                                                                if (in_array($value['iCategoryID'], $cat_array)) {
//                                                                    echo '<option value="' . $value['vCategoryName'] . '" selected="selected">' . $value['vCategoryName'] . '</option>';
//                                                                } else {
//                                                                    echo '<option value="' . $value['vCategoryName'] . '">' . $value['vCategoryName'] . '</option>';
//                                                                }
//                                                            } else {
//                                                                echo '<option value="' . $value['vCategoryName'] . '">' . $value['vCategoryName'] . '</option>';
//                                                            }
//                                                        }
                                                        }
                                                        $category = $this->restaurant_model->getCategoryDataAll();
                                                        $cuisine = $this->restaurant_model->getCuisineDataAll();
                                                        $facility = $this->restaurant_model->getFacilityDataAll();
                                                        $musics = $this->restaurant_model->getMusicDataAll();
                                                        //print_r($getCategoryData);
                                                        $cat_array = array();
                                                        if (isset($getCategoryData) && !empty($getCategoryData)) {
                                                            foreach ($getCategoryData as $key1 => $cat_value) {
                                                                array_push($cat_array, $cat_value['iCategoryID']);
                                                            }
                                                        }
                                                        //print_r($cat_array);

                                                        foreach ($category as $key => $value) {
                                                            //print_r($value);
                                                            if (isset($getCategoryData) && !empty($getCategoryData)) {
                                                                if (in_array($value['iCategoryID'], $cat_array)) {
                                                                    echo '<option value="' . $value['vCategoryName'] . '" selected="selected">' . $value['vCategoryName'] . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $value['vCategoryName'] . '">' . $value['vCategoryName'] . '</option>';
                                                                }
                                                            } else {
                                                                echo '<option value="' . $value['vCategoryName'] . '">' . $value['vCategoryName'] . '</option>';
                                                            }
                                                        }
                                                        $music_array = array();
                                                        if (isset($getMusicData)) {
                                                            foreach ($getMusicData as $key1 => $mus_value) {
                                                                array_push($music_array, (int) $mus_value['iMusicID']);
                                                            }
                                                        }
                                                        foreach ($musics as $key => $value) {
                                                            if (isset($getMusicData) && !empty($getMusicData)) {
                                                                if (in_array((int) $value['iMusicID'], $music_array)) {
                                                                    echo '<option value="' . $value['vMusicName'] . '" selected="selected">' . $value['vMusicName'] . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $value['vMusicName'] . '">' . $value['vMusicName'] . '</option>';
                                                                }
                                                            } else {
                                                                echo '<option value="' . $value['vMusicName'] . '">' . $value['vMusicName'] . '</option>';
                                                            }
                                                        }
                                                        $fac_array = array();
                                                        if (isset($getFacilityData)) {
                                                            foreach ($getFacilityData as $key1 => $fac_value) {
                                                                array_push($fac_array, $fac_value['iFacilityID']);
                                                            }
                                                        }
                                                        foreach ($facility as $key => $value) {
                                                            if (isset($getFacilityData) && !empty($getFacilityData)) {
                                                                if (in_array($value['iFacilityID'], $fac_array)) {
                                                                    echo '<option value="' . $value['vFacilityName'] . '" selected="selected">' . $value['vFacilityName'] . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $value['vFacilityName'] . '">' . $value['vFacilityName'] . '</option>';
                                                                }
                                                            } else {
                                                                echo '<option value="' . $value['vFacilityName'] . '">' . $value['vFacilityName'] . '</option>';
                                                            }
                                                        }
                                                        $cui_array = array();
                                                        if (isset($getCuisineData)) {
                                                            foreach ($getCuisineData as $key1 => $cat_value) {
                                                                array_push($cui_array, $cat_value['iCuisineID']);
                                                            }
                                                        }
                                                        foreach ($cuisine as $key => $value) {
                                                            if (isset($getCuisineData) && !empty($getCuisineData)) {
                                                                if (in_array($value['iCuisineID'], $cui_array)) {
                                                                    echo '<option value="' . $value['vCuisineName'] . '" selected="selected">' . $value['vCuisineName'] . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $value['vCuisineName'] . '">' . $value['vCuisineName'] . '</option>';
                                                                }
                                                            } else {
                                                                echo '<option value="' . $value['vCuisineName'] . '">' . $value['vCuisineName'] . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>

                                                </div>
                                            </div>

<!--                                          <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iSponserKeywords" required="required">Keywords <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <textarea class="maxwidth500 form-control" id="iSponserKeywords" name="iSponserKeywords" placeholder="Please enter comma separated keywords"><?= isset($iSponserKeywords) && $iSponserKeywords != '' ? $iSponserKeywords : ''; ?></textarea>
                                                <div class="col-sm-12" style="padding:0;">
                                                    <span class="required"><strong>NOTE:</strong>
                                                        Enter comma separated keywords
                                                     </span>
                                                </div>
                                                </div>
                                            </div>-->
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
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>js/magic-suggest/magicsuggest-1.3.1-min.css" />
        <script type="text/javascript" src="<?= base_url() ?>js/magic-suggest/magicsuggest-1.3.1-min.js"></script>
        <script>
            var _minDay, _maxDay;
            _minDay = _maxDay = -1;
            var $datepicker1 = $("#dtStartDate");
            var $datepicker2 = $("#dtExpiryDate");
            jQuery(document).ready(function() {
                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                var iSponserId = '<?= isset($iSponserId) ? $iSponserId : 0; ?>';
                $('#iSponserKeywords').select2();
                $("#validateForm").validate({
                    rules: {
                        iRestaurantId: {
                            required: true
                        },
//                        vDealCode: {
//                            required: true,
//                            remote: {
//                                url: BASEURL + '<?//= $this->controller; ?>/checkDealCode',
//                                type: 'post',
//                                data: {
//                                    iDealID: iDealID
//                                }
//                            }
//                        },
//                        vOfferText: {
//                            required: true
//                        },
                        iSponserKeywords: {
                            required: true
                        },
//                        tActualPrice: {
//                            required: true,
//                            number: true
//                        },
//                        tDiscountedPrice: {
//                            required: true,
//                            number: true
//                        },
                        dtStartDate: {
                            required: true
                        },
                        dtExpiryDate: {
                            required: true
                        }
                    },
                    messages: {
                        iRestaurantId: "Please select restaurant",
//                        vDealCode: {
//                            required: 'Please enter deal code.',
//                            remote: 'Entered deal code is already exists.'
//                        },
//                        vOfferText: 'Please enter offer text',
                        iSponserKeywords: 'Please enter keywords',
//                        tActualPrice: {required : 'Please enter actual price',
//                        number: 'Please enter a valid amount'},
//                        tDiscountedPrice: {required : 'Please enter discounted price',
//                        number: 'Please enter a valid amount'},
                        dtStartDate: 'Please select start date',
                        dtExpiryDate: 'Please select expiry date'
                    }
                });

                // $('.fileinput').fileinput()
                $('.uniform').uniform();
                $('#iRestaurantId').select2();

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
                $datepicker2.datepicker({
                    changeMonth: true,
                    changeYear: true,
                    onSelect: function(dateText, inst) {
                        var date = $(this).datepicker('getDate');
                        _maxDay = date.getDay();
                        dayShowHide();
                    }
                });

//                var _URL = window.URL || window.webkitURL;
//                $("#vOfferImage").change(function(e) {
//                    var $file = $(this);
//                    var fileExt = ($file.val()).split('.').pop().toUpperCase();
//
//                    var image, file;
//                    if (file = this.files[0]) {
//                        image = new Image();
//                        image.onload = function() {
//                            //alert(this.width + ' ' + this.height);
//                            if (fileExt == 'JPG' || fileExt == 'JPEG' || fileExt == 'PNG') {
//
//                            } else {
//                                alert('Please upload valid image type.');
//                                //$file.replaceWith($file.val('').clone(true));
//                                $('#removebtn').trigger('click');
//                            }
//                            // alert("The image width is " + this.width + " and image height is " + this.height);
//                        };
//                        image.src = _URL.createObjectURL(file);
//                    }
//                });
//                document.getElementById('removepic').value = 0;
//
//                $(document).on('click', '#removebtn', function(event) {
//                    $("#removepic").val('1');
//                });
//                $(document).on('click', '.select_pic', function(event) {
//                    $("#removepic").val('0');
//                });

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