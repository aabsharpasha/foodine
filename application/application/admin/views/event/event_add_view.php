<?php
$headerData = $this->headerlib->data();
if (isset($getEventData) && $getEventData != '')
    extract($getEventData);


//print_r($getEventData);

$form_attr = array(
    'name' => 'event-form',
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
    "action" => "backoffice.eventedit"
);
$hiddenaddattr = array(
    "action" => "backoffice.eventadd"
);
$event_id = array(
    "iEventID" => (isset($iEventId) && $iEventId != '') ? $iEventId : ''
);
$submit_attr = array(
    'class' => 'submit btn-sm btn btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Event",
    'type' => 'submit'
);
$cancel_attr = array(
    'class' => 'btn btn-inverse ',
    'value' => "Reset",
    'type' => 'reset'
);

$minutes = array();
for ($i = 0; $i <= 59; $i++) {
    $minutes[str_pad($i, 2, 0, STR_PAD_LEFT)] = str_pad($i, 2, 0, STR_PAD_LEFT);
}

$pic = (isset($iEventImage) && $iEventImage != '') ? $iEventImage : '';
$uid = (isset($iRestaurantId) && $iRestaurantId != '') ? $iRestaurantId : '';
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
                                            <li>Event</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Event</h3>
                                        </div>
                                        <div class="description">Add/Edit Event</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Event"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("event/add", $form_attr);
                                            if (isset($iEventId) && $iEventId != '') {
                                                echo form_hidden($event_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iRestaurantID">Restaurant <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="iRestaurantID" id="iRestaurantID" class="maxwidth500 col-lg-12" required="required">
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
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Event Title<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" id="iEventTitle" name="iEventTitle" value="<?php echo (isset($iEventTitle) && $iEventTitle != '') ? $iEventTitle : ''; ?>">
                                                </div>
                                            </div>
                                             <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iEventDescription" required="required">Event Description<span class="required">*</span></label>
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
                                                            <textarea class="ckeditor" id="iEventDescription" name="iEventDescription" placeholder="Please enter event description"><?= isset($iEventDescription) && $iEventDescription != '' ? $iEventDescription : ''; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <!-- /CKE --> 
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Event Image<span class="required">*</span></label>
                                                <div class="col-sm-9">

                                                    <?php
                                                    if ($pic != '' && $uid != '') {
                                                        $pic_str = '<img src="' . IMGURL . '/event/' . $uid . '/thumb/' . $pic . '">';
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
                                                        <input type="hidden" name="iEventUrl" value="<?php echo $pic; ?>"/>
                                                        <div>
                                                            <span class="btn btn-default btn-file">
                                                                <span class="<?php echo $sel_text ?> select_pic" data-trigger="fileinput"  >Select image</span>
                                                                <span class="<?php echo $change_text ?> select_pic" data-trigger="fileinput">Change</span>
                                                                <input type="file" 
                                                                       name="iEventImage" 
                                                                       accept="image/jpg, image/JPG,image/JPEG, image/jpeg, image/png, image/PNG" 
                                                                       id="iEventImage" />
                                                            </span>
                                                            <a href="#" 
                                                               class="btn btn-default fileinput-exists" 
                                                               data-dismiss="fileinput" id="removebtn">Remove</a>
                                                        </div>
                                                        <input type="hidden" id="removepic" name="removepic" value="0" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="dEventStartDate">Event Start Date <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?=
                                                    form_input(array(
                                                        'name' => 'dEventStartDate',
                                                        'id' => 'dEventStartDate',
                                                        "required" => "required",
                                                        'readonly' => 'readonly',
                                                        "data-errortext" => "This is start date text!",
                                                        'value' => (isset($dEventStartDate) && $dEventStartDate != '') ? date('m/d/Y', strtotime($dEventStartDate)) : '',
                                                        'type' => 'text',
                                                        'class' => 'form-control maxwidth500'
                                                    ));
                                                    ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="dEventEndDate">Event End Date <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?=
                                                    form_input(array(
                                                        'name' => 'dEventEndDate',
                                                        'id' => 'dEventEndDate',
                                                        "required" => "required",
                                                        'readonly' => 'readonly',
                                                        "data-errortext" => "This is end date text!",
                                                        'value' => (isset($dEventEndDate) && $dEventEndDate != '') ? date('m/d/Y', strtotime($dEventEndDate)) : '',
                                                        'type' => 'text',
                                                        'class' => 'form-control maxwidth500'
                                                    ));
                                                    ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Event Time</label>
                                                <div class="col-sm-9">
                                                    <div class="row padding0 margin0auto">
                                                        <label class="col-sm-12 padding0" for="iMinTime">From<span class="required">*</span></label>
                                                        <div class="col-sm-2 padding0">
                                                            <?php
                                                            $minRec = array();
                                                            if ($event_id != '' && isset($vEventStartTime)) {
                                                                $minRec = explode(':', $vEventStartTime);
                                                                $minRec1 = explode(' ', $minRec[1]);
                                                            } else {
                                                                $minRec[0] = 12;
                                                                $minRec1[0] = 0;
                                                            }
                                                            ?>
                                                            <select class="col-sm-6 form-control" name="iMinTime" id="iMinTime" required="required">
                                                                <option value="">Hour</option>
                                                                <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                                    <option value="<?= $i; ?>"
                                                                            <?= isset($minRec[0]) && $minRec[0] == $i ? 'selected="selected"' : ''; ?>>
                                                                                <?= $i; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <select class="col-sm-6 form-control" name="iMinMin" id="iMinMin" required="">
                                                                <option value="">Minute</option>
                                                                <?php foreach($minutes AS $minuteKey=>$minute) { ?>
                                                                    <option value="<?= $minuteKey; ?>"
                                                                            <?= isset($minRec1[0]) && $minRec1[0] == $minuteKey ? 'selected="selected"' : ''; ?>>
                                                                                <?= $minute; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2 padding0">
                                                            <select class="col-sm-6 form-control" name="iMinTimeMaradian" id="iMinTimeMaradian">
                                                                <option value="1" <?= isset($minRec1[1]) && $minRec1[1] == 'AM' ? 'selected="selected"' : ''; ?>>AM</option>
                                                                <option value="2" <?= isset($minRec1[1]) && $minRec1[1] == 'PM' ? 'selected="selected"' : ''; ?>>PM</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="padding-top-10">
                                                        <label class="col-sm-12 padding0" for="iMaxTime">To<span class="required">*</span></label>
                                                        <?php
                                                        $maxRec = array();
                                                        if ($event_id != '' && isset($vEventEndTime)) {
                                                            $maxRec = explode(':', $vEventEndTime);
                                                            $maxRec1 = explode(' ', $maxRec[1]);
                                                        } else {
                                                            $maxRec[0] = 12;
                                                            $maxRec1[0] = 0;
                                                        }
                                                        ?>
                                                        <div class="col-sm-2 padding0">
                                                            <select class="col-sm-6 form-control" name="iMaxTime" id="iMaxTime" required="required">
                                                                <option value="">Hour</option>
                                                                <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                                    <option value="<?= $i; ?>"
                                                                            <?= isset($maxRec[0]) && $maxRec[0] == $i ? 'selected="selected"' : ''; ?>>
                                                                                <?= $i; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <select class="col-sm-6 form-control" name="iMaxMin" id="iMaxMin" required="">
                                                                <option value="">Minute</option>
                                                                <?php foreach($minutes AS $minuteKey=>$minute) { ?>
                                                                    <option value="<?= $minuteKey; ?>"
                                                                            <?= isset($maxRec1[0]) && $maxRec1[0] == $minuteKey ? 'selected="selected"' : ''; ?>>
                                                                                <?= $minute; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2 padding0">
                                                            <select class="col-sm-6 form-control" name="iMaxTimeMaradian" id="iMaxTimeMaradian">
                                                                <option value="1" <?= isset($maxRec1[1]) && $maxRec1[1] == 'AM' ? 'selected="selected"' : ''; ?>>AM</option>
                                                                <option value="2" <?= isset($maxRec1[1]) && $maxRec1[1] == 'PM' ? 'selected="selected"' : ''; ?>>PM</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php
                                            $vDaysAllowValue = array();
                                            if (isset($iDayofEvent)) {
                                                $vDaysAllowValue = explode(',', $iDayofEvent);
                                            }
                                            ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="vDaysAllow">Days Allow</label>
                                                <div class="col-sm-9">
                                                    <select name="iDayofEvent[]" id="iDayofEvent" multiple="multiple">
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
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Event Venue<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="iVenueofEvent" id="iVenueofEvent" value="<?php echo (isset($iVenueofEvent) && $iVenueofEvent != '') ? $iVenueofEvent : ''; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Event URL</label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="URL" id="URL" value="<?php echo (isset($URL) && $URL != '') ? $URL : ''; ?>">
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
        <script type="text/javascript" src="<?= base_url() ?>js/ckeditor/ckeditor.js"></script>
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.css" type="text/css" media="screen" />
        <script src="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript" charset="utf-8"></script>
        <script>
            var _minDay, _maxDay;
            _minDay = _maxDay = -1;
            var $datepicker1 = $("#dEventStartDate");
            var $datepicker2 = $("#dEventEndDate");
            var iEventId = '<?= isset($iEventId) ? $iEventId : 0; ?>';
            jQuery(document).ready(function()
            {
                $('#iDayofEvent').multiselect({
                    includeSelectAllOption: true,
                    selectAllText: 'All Days!'
//                    selectAllValue: 'all'
                });
                $('#iRestaurantID').select2();
                $datepicker1.datepicker({
                    minDate: 0,
                    changeMonth: true,
                    changeYear: true,
                    onClose: function(selectedDate) {
                        $("#dEventEndDate").datepicker("option", "minDate", selectedDate);
                    },
                    onSelect: function(dateText, inst) {
                        var date = $(this).datepicker('getDate');
                        _minDay = date.getDay();
                        dayShowHide();
                    }
                });
                if (iEventId > 0) {
                    var date_exp = '<?= isset($dEventStartDate) ? $dEventStartDate : ''; ?>';
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


                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                $("#validateForm").validate({
                    rules: {
                        iRestaurantID: {required: true},
                        iEventTitle: {required: true},
                        dEventStartDate: {required: true},
                        dEventEndDate: {required: true},
                        iEventDescription: {required: true, },
//                        iEventImage: {required: true, },
                        //iDayofEvent: {required: true, },
                        iVenueofEvent: {required: true, },
                        URL: {
                            url: true
                        }

                    },
                    messages: {
                        iRestaurantID: {required: "Please Select Restaurant."},
                        iEventTitle: {required: 'Please enter Event Title'},
                        dEventStartDate: {required: 'Please enter Event Start Date'},
                        dEventEndDate: {required: 'Please enter Event End Date'},
                        iEventDescription: {required: 'Please enter Event Name', },
//                        iEventImage: {required: 'Please upload Event Image', },
                        //iDayofEvent: {required: 'Please enter Event Day', },
                        iVenueofEvent: {required: 'Please enter Event Venue.', },
                        URL: {
                            url: 'Please enter a valid URL.'
                        }
                    }
                });

                var _URL = window.URL || window.webkitURL;
                $("#iEventImage").change(function(e) {
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

                // $('.fileinput').fileinput()
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
