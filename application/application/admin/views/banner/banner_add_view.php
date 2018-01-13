<?php
$headerData = $this->headerlib->data();
if (isset($bannerData) && $bannerData != '')
    extract($bannerData);

if (isset($bannerDataArray) && $bannerDataArray != '')
    extract($bannerDataArray);

//print_r($getVoucherData);

$form_attr = array(
    'name' => 'banner-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);

$hiddeneditattr = array(
    "action" => "edit",
    "type"  => $bannerType
);
$hiddenaddattr = array(
    "action" => "add",
    "type"  => $bannerType
);

$banner_id = array(
    "iBannerId" => (isset($iBannerId) && $iBannerId != '') ? $iBannerId :  ''
);

$startHours     = "00";
$startMinutes   = "00";
$endHours       = "23";
$endMinutes     = "59";
if(!empty($tStartDate) && !empty($tEndDate) ){
    $startTime  = explode(" ", $tStartDate);
    if(count($startTime)==2){
        $startTime      = explode(":", $startTime[1]);
        $startHours     = $startTime[0];
        $startMinutes   = $startTime[1];
    }
    $endTime  = explode(" ", $tEndDate);
    if(count($endTime)==2){
        $endTime      = explode(":", $endTime[1]);
        $endHours       = $endTime[0];
        $endMinutes     = $endTime[1];
    }
}

$submit_attr = array(
    'class' => 'submit btn-sm btn btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Banner",
    'type' => 'submit'
);
$cancel_attr = array(
    'class' => 'btn btn-inverse ',
    'value' => "Reset",
    'type' => 'reset'
);

$hours = array();
for ($i = 0; $i <= 23; $i++) {
    $hours[str_pad($i, 2, 0, STR_PAD_LEFT)] = str_pad($i, 2, 0, STR_PAD_LEFT);
}
$minutes = array();
for ($i = 0; $i <= 59; $i++) {
    $minutes[str_pad($i, 2, 0, STR_PAD_LEFT)] = str_pad($i, 2, 0, STR_PAD_LEFT);
}
$pic = (isset($vBannerImage) && $vBannerImage != '') ? $vBannerImage : '';
$uid = (isset($iBannerId) && $iBannerId != '') ? $iBannerId : '';

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
                                            <li>Banner</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Banner</h3>
                                        </div>
                                        <div class="description">Add/Edit <?php echo ucfirst($bannerType); ?> Banner</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i>Save <?php echo ucfirst($bannerType); ?> Banner</h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("banner/add", $form_attr);
                                            if (isset($iBannerId) && $iBannerId != '') {
                                                echo form_hidden($banner_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>
                                            <?php if($bannerType != 'featured') { ?>
                                           <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iRestaurantId">Select Restaurant<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="iRestaurantId" id="iRestaurantId" class="maxwidth500 col-lg-12" onchange="getBanner(this.value,'<?php echo $bannerType ?>');">
                                                        <option value="">- Select -</option>
                                                        <?php foreach($bannerArray AS $banner) { if(!isset($iRestaurantId)) {$iRestaurantId = '';}?>
                                                            <option value="<?php echo $banner['id']?>" <?php echo $banner['id']==$iRestaurantId?"selected='selected'":''?>><?php echo $banner['name']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <?php if($bannerType != 'featured') { ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iTypeId">Select <?php echo ucfirst($bannerType); ?><span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="iTypeId" id="iTypeId" class="maxwidth500 col-lg-12">
                                                        <option value="">- Select -</option>
                                                        <?php foreach($bannerDataArray AS $banner) { if(!isset($iTypeId)) {$iTypeId = '';}?>
                                                            <option value="<?php echo $banner['id']?>" <?php echo $banner['id']==$iTypeId?"selected='selected'":''?>><?php echo $banner['name']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iTypeId">Select Restaurant<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="iTypeId" id="iTypeId" class="maxwidth500 col-lg-12">
                                                        <option value="">- Select -</option>
                                                        <?php foreach($bannerArray AS $banner) { if(!isset($iTypeId)) {$iTypeId = '';}?>
                                                            <option value="<?php echo $banner['id']?>" <?php echo $banner['id']==$iTypeId?"selected='selected'":''?>><?php echo $banner['name']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="vLabel">Banner Label<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" id="vLabel" name="vLabel"  value="<?php echo (isset($vLabel) && $vLabel != '') ? $vLabel : ''; ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tText">Banner Text<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" id="tText" name="tText" value="<?php echo (isset($tText) && $tText != '') ? $tText : ''; ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tStartDate">Start Date<span class="required">*</span></label>
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
                                                <label class="col-sm-3 control-label" for="startMinutes">Start Time<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    Hours:
                                                    <select name="startHours" id="startHours">
                                                        <option value="">- Hours -</option>
                                                        <?php foreach($hours AS $hourKey=>$hour) {?>
                                                            <option value="<?php echo $hourKey?>" <?php echo $hourKey==$startHours?"selected='selected'":''?>><?php echo $hour?></option>
                                                        <?php } ?>
                                                    </select>
                                                    &nbsp;Minutes:&nbsp;
                                                    <select name="startMinutes" id="startMinutes">
                                                        <option value="">- Minutes -</option>
                                                        <?php foreach($minutes AS $minuteKey=>$minute) { ?>
                                                            <option value="<?php echo $minuteKey?>" <?php echo $minuteKey==$startMinutes?"selected='selected'":''?>><?php echo $minute?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tEndDate">Expiry Date <span class="required">*</span></label>
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
                                                <label class="col-sm-3 control-label" for="endMinutes">Expiry Time<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    Hours:
                                                    <select name="endHours" id="endHours">
                                                        <option value="">- Hours -</option>
                                                        <?php foreach($hours AS $hourKey=>$hour) { ?>
                                                            <option value="<?php echo $hourKey?>" <?php echo $hourKey==$endHours?"selected='selected'":''?>><?php echo $hour?></option>
                                                        <?php } ?>
                                                    </select>
                                                    &nbsp;Minutes:&nbsp;
                                                    <select name="endMinutes" id="endMinutes">
                                                        <option value="">- Minutes -</option>
                                                        <?php foreach($minutes AS $minuteKey=>$minute) { ?>
                                                            <option value="<?php echo $minuteKey?>" <?php echo $minuteKey==$endMinutes?"selected='selected'":''?>><?php echo $minute?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Banner Image</label>
                                                <div class="col-sm-9">

                                                    <?php
                                                    if ($pic != '' && $uid != '') {
                                                        $pic_str = '<img src="' . IMGURL . '/banner/' . $uid . '/thumb/' . $pic . '">';
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
                                                        <input type="hidden" name="vBannerImage" value="<?php echo $pic; ?>"/>
                                                        <div>
                                                            <span class="btn btn-default btn-file">
                                                                <span class="<?php echo $sel_text ?> select_pic" data-trigger="fileinput"  >Select image</span>
                                                                <span class="<?php echo $change_text ?> select_pic" data-trigger="fileinput">Change</span>
                                                                <input type="file" 
                                                                       name="vCuisineImage" 
                                                                       accept="image/jpg, image/JPG,image/JPEG, image/jpeg, image/png, image/PNG" 
                                                                       id="vCuisineImage" />
                                                            </span>
                                                            <a href="#" 
                                                               class="btn btn-default fileinput-exists" 
                                                               data-dismiss="fileinput" id="removebtn">Remove</a>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <?php echo form_input($submit_attr); ?>
                                                    <a class="btn btn-sm btn-grey" href="<?= BASEURL .''. $this->controller; ?>/index/<?= $bannerType ?>">Cancel</a>
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
            var _minDay=_maxDay=-1;
            var $datepicker1 = $("#tStartDate");
            var $datepicker2 = $("#tEndDate");            
            jQuery(document).ready(function ()
            {
                 var _URL = window.URL || window.webkitURL;
                $("#vCuisineImage").change(function (e) {
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
             //   document.getElementById('removepic').value = 0;

                $(document).on('click', '#removebtn', function (event) {
                    $("#removepic").val('1');
                });
                $(document).on('click', '.select_pic', function (event) {
                    $("#removepic").val('0');
                });

                // $('.fileinput').fileinput()
                $('.uniform').uniform();

//                $('#tStartDate').datepicker({
//                    minDate: 0,
//                    changeMonth: true,
//                    changeYear: true,
//                    onClose: function(selectedDate) {
//                        $("#tStartDate").datepicker("option", "minDate", selectedDate);
//                    },
//                    onSelect: function (dateText, inst) {
//                        var date = $(this).datepicker('getDate');
//                        _minDay = date.getDay();
//                        dayShowHide();
//                    }
//                });
//
//                $('#tEndDate').datepicker({
//                    minDate: 0,
//                    changeMonth: true,
//                    changeYear: true,
//                    onClose: function(selectedDate) {
//                        $("#tEndDate").datepicker("option", "minDate", selectedDate);
//                    },
//                    onSelect: function (dateText, inst) {
//                        var date = $(this).datepicker('getDate');
//                        _maxDay = date.getDay();
//                        dayShowHide();
//                    }
//                });
                
                $datepicker1.datepicker({
                    minDate: 0,
                    changeMonth: true,
                    changeYear: true,
                    onClose: function(selectedDate) {
                        $("#tEndDate").datepicker("option", "minDate", selectedDate);
                    },
                    onSelect: function(dateText, inst) {
                        var date = $(this).datepicker('getDate');
                        _minDay = date.getDay();
                        dayShowHide();
                    }
                });
//                if (iEventId > 0) {
//                    var date_exp = '<?//= isset($dEventStartDate) ? $dEventStartDate : ''; ?>';
//                    date_exp = date_exp.split('-');
//                    var yr = date_exp[0];
//                    var mn = parseInt(date_exp[1]) - 1;
//                    var dt = date_exp[2].split(' ');
//                    dt = dt[0];
//
//                    $datepicker2.datepicker({
//                        yearRange: yr + ':c+1',
//                        minDate: new Date(yr, mn, dt),
//                        changeMonth: true,
//                        changeYear: true,
//                        onSelect: function(dateText, inst) {
//                            var date = $(this).datepicker('getDate');
//                            _maxDay = date.getDay();
//                            dayShowHide();
//                        }
//                    });
//                } else {
                    $datepicker2.datepicker({
                        changeMonth: true,
                        changeYear: true,
                        onSelect: function(dateText, inst) {
                            var date = $(this).datepicker('getDate');
                            _maxDay = date.getDay();
                            dayShowHide();
                        }
                    });
               // }
                
                
                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                $("#validateForm").validate({
                    rules: {
                        vLabel: {required: true},
                        tText: {required: true},
                        iRestaurantId: {required: true },
                        iTypeId: {required: true },
                        tStartDate: {required: true },
                        tEndDate: {required: true },
                        startHours: {required: true },
                        startMinutes: {required: true },
                        endHours: {required: true },
                        endMinutes: {required: true }
                    },
                    messages: {
                        vLabel: {required: "Please Enter Label."},
                        tText: {required: "Please Enter Text."},
                        iRestaurantId: {required: "Please Select Restaurant." },
                        iTypeId: {required: "Please Select <?php echo $bannerType; ?>." },
                        tStartDate: {required: "Please Select Valid From Date." },
                        tEndDate: {required: "Please Select Valid End Date." },
                        startHours: {required: "Please Select Start Hours." },
                        startMinutes: {required: "Please Select Start Minutes." },
                        endHours: {required: "Please Select End Hours." },
                        endMinutes: {required: "Please Select End Minutes." }
                    }
                });
                $('#iRestaurantId').select2();
                $('#iTypeId').select2();

                $('.uniform').uniform();
            });
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
   
       function  getBanner(restId, bannerType) {
        var form_data = {restId: restId, bannerType: bannerType}
        
        $.ajax({
            url: BASEURL + 'banner/getBanner',
            type: 'POST',
            data: form_data,
            success: function (outputData) {
                 var selectOffer = document.getElementById('iTypeId');
                  $(selectOffer).empty();
                   $("#s2id_iTypeId .select2-chosen").html("- Select -");
                  outputData = $.parseJSON(outputData);
                if (outputData != '') {
                    $(selectOffer).append('<option value="">- Select -</option>');
                    $.each( outputData, function( key, value ) {
                        $(selectOffer).append('<option value=' + value.id + '>' + value.name + '</option>');
                     });
                }  
           }
        });
}
        
        </script>
    </body>
</html>
