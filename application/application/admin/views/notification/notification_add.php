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

$pic = (isset($vImage) && $vImage != '') ? $vImage : '';
$uid = (isset($iPushNotifyID) && $iPushNotifyID != '') ? $iPushNotifyID : '';

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
                                            echo form_open("notification/addNotification", $form_attr);
                                            if (isset($iPushNotifyID) && $iPushNotifyID != '') {
                                                echo form_hidden($notify_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Criteria</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" name="eCriteria" id="eCriteria">
                                                        <option value="All" > - All Users - </option>
<!--                                                        <option value="Location" <?php echo (isset($eCriteria) && $eCriteria == 'Location' ? 'selected="selected"' : ''); ?> >Location</option>-->
                                                      <!--   <option value="Age" <?php echo (isset($eCriteria) && $eCriteria == 'Age' ? 'selected="selected"' : ''); ?>>Age</option> -->
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group" id="locationDiv">
                                                <label class="col-sm-3 control-label">Location<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" name="iLocationID" id="iLocationID" value="<?php echo (isset($iLocationID) && $iLocationID != '') ? $iLocationID : ''; ?>">
                                                        <option value=""> - Select - </option>
                                                        <?php foreach($locationData As $location){ ?>
                                                        <option value="<?php echo $location['iLocationID']?>" <?php echo (isset($iLocationID) && $iLocationID == $location['iLocationID'] ? 'selected="selected"' : ''); ?> ><?php echo $location['vLocationName']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group" id="ageDiv">
                                                <label class="col-sm-3 control-label">Age<span class="required">*</span></label>
                                                <div class="col-sm-2 ">
                                                    <input class="form-control maxwidth200" name="iMinAge" id="iMinAge" value="<?php echo (isset($iMinAge) && $iMinAge != '') ? $iMinAge : ''; ?>" placeholder="minimum age" type="number">
                                                </div>
                                                <div class="col-sm-1 center">
                                                    To
                                                </div>
                                                <div class="col-sm-2">
                                                    <input class="form-control maxwidth200" name="iMaxAge" id="iMaxAge" value="<?php echo (isset($iMaxAge) && $iMaxAge != '') ? $iMaxAge : ''; ?>" placeholder="maximum age" type="number">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Title<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input  type = "text" class="form-control maxwidth500" name="vNotifyTitle" value ="<?php echo (isset($vNotifyTitle) && $vNotifyTitle != '') ? $vNotifyTitle : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Content<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control maxwidth500" name="vNotifyText"><?php echo (isset($vNotifyText) && $vNotifyText != '') ? $vNotifyText : ''; ?></textarea>
                                                </div>
                                            </div>

                                         <!--    <div class="form-group">
                                                <label class="col-sm-3 control-label">Link</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" name="eLink" id="eLink">
                                                        <option value=""> - Select - </option>
                                                        <option value="Restaurant" <?php echo (isset($eLink) && $eLink == 'Restaurant' ? 'selected="selected"' : ''); ?> >Restaurant Detail</option>
                                                        <option value="Featured" <?php echo (isset($eLink) && $eLink == 'Featured' ? 'selected="selected"' : ''); ?> >Featured Restaurant Listing</option>
                                                        <option value="HandPicks" <?php echo (isset($eLink) && $eLink == 'Restaurant' ? 'selected="selected"' : ''); ?> >Hand Picks Listing</option>
                                                        <option value="Nearby" <?php echo (isset($eLink) && $eLink == 'Restaurant' ? 'selected="selected"' : ''); ?> >Nearby Listing</option>
                                                        <option value="EventsListing" <?php echo (isset($eLink) && $eLink == 'Events' ? 'selected="selected"' : ''); ?> >Events Listing</option>
                                                        <option value="Events" <?php echo (isset($eLink) && $eLink == 'Events' ? 'selected="selected"' : ''); ?> >Events Detail</option>
                                                        <option value="ComboListing" <?php echo (isset($eLink) && $eLink == 'Combo' ? 'selected="selected"' : ''); ?> >Combo Listing</option>
                                                        <option value="Combo" <?php echo (isset($eLink) && $eLink == 'Combo' ? 'selected="selected"' : ''); ?> >Combo Detail</option>
                                                        <option value="OfferListing" <?php echo (isset($eLink) && $eLink == 'Offer' ? 'selected="selected"' : ''); ?> >Offer Listing</option>
                                                        <option value="Offer" <?php echo (isset($eLink) && $eLink == 'Offer' ? 'selected="selected"' : ''); ?> >Offer Detail</option>
                                                    </select>
                                                </div>
                                            </div> -->

                                            <!-- <div class="form-group" id="linkRestaurantDiv">
                                                <label class="col-sm-3 control-label">Link Restaurant</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" name="linkedRestaurant" id="linkedRestaurant" >
                                                        <option value=""> - Select - </option>
                                                        <?php foreach($restaurantData As $restaurant){ ?>
                                                        <option value="<?php echo $restaurant['iRestaurantID']?>" <?php echo (isset($linkedRestaurant) && $linkedRestaurant == $restaurant['iRestaurantID'] ? 'selected="selected"' : ''); ?> ><?php echo $restaurant['vRestaurantName']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group" id="linkFeaturedDiv">
                                                <label class="col-sm-3 control-label">Link Featured Restaurant</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" name="linkedFeaturedRestaurant" id="linkedFeaturedRestaurant" >
                                                        <option value=""> - Select - </option>
                                                        <?php foreach($featuredRestaurantData As $restaurant){ ?>
                                                        <option value="<?php echo $restaurant['iRestaurantID']?>" <?php echo (isset($linkedFeaturedRestaurant) && $linkedFeaturedRestaurant == $restaurant['iRestaurantID'] ? 'selected="selected"' : ''); ?> ><?php echo $restaurant['vRestaurantName']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group" id="linkEventDiv">
                                                <label class="col-sm-3 control-label">Link Event</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" name="linkedEvent" id="linkedEvent" >
                                                        <option value="" restaurantId=""> - Select - </option>
                                                        <?php foreach($eventData As $event){ ?>
                                                        <option value="<?php echo $event['iEventId']?>" restaurantId="<?php echo $event['iRestaurantId']?>" <?php echo (isset($linkedEvent) && $linkedEvent == $event['iEventId'] ? 'selected="selected"' : ''); ?> ><?php echo $event['iEventTitle']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group" id="linkComboDiv">
                                                <label class="col-sm-3 control-label">Link Combo</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" name="linkedCombo" id="linkedCombo" >
                                                        <option value="" restaurantId=""> - Select - </option>
                                                        <?php foreach($comboData As $combo){ ?>
                                                        <option value="<?php echo $combo['iComboOffersID']?>" restaurantId="<?php echo $combo['iRestaurantID']?>" <?php echo (isset($linkedCombo) && $linkedCombo == $combo['iComboOffersID'] ? 'selected="selected"' : ''); ?> ><?php echo $combo['vOfferText']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group" id="linkOfferDiv">
                                                <label class="col-sm-3 control-label">Link Offer</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" name="linkedOffer" id="linkedOffer" >
                                                        <option value="" restaurantId=""> - Select - </option>
                                                        <?php foreach($offerData As $offer){ ?>
                                                        <option value="<?php echo $offer['iDealID']?>" restaurantId="<?php echo $offer['iRestaurantID']?>" <?php echo (isset($linkedOffer) && $linkedOffer == $offer['iDealID'] ? 'selected="selected"' : ''); ?> ><?php echo $offer['vOfferText']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div> -->
                                            
                                           <!--  <div class="form-group">
                                                <label class="col-sm-3 control-label">Schedule Date<span class="required">*</span></label>
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
                                                <label class="col-sm-3 control-label">Schedule Time<span class="required">*</span></label>
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
                                            </div> -->
                                            
                                           <!--  <div class="form-group">
                                                <label class="col-sm-3 control-label">Image</label>
                                                <div class="col-sm-9">

                                                    <?php
                                                    if ($pic != '' && $uid != '') {
                                                        $pic_str = '<img src="' . IMGURL . '/pushNotification/' . $uid . '/thumb/' . $pic . '">';
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
                                                        <input type="hidden" name="vNotifyUrl" value="<?php echo $pic; ?>"/>
                                                        <div>
                                                            <span class="btn btn-default btn-file">
                                                                <span class="<?php echo $sel_text ?> select_pic" data-trigger="fileinput"  >Select image</span>
                                                                <span class="<?php echo $change_text ?> select_pic" data-trigger="fileinput">Change</span>
                                                                <input type="file" 
                                                                       name="vImage" 
                                                                       accept="image/jpg, image/JPG,image/JPEG, image/jpeg, image/png, image/PNG" 
                                                                       id="vImage" />
                                                            </span>
                                                            <a href="#" 
                                                               class="btn btn-default fileinput-exists" 
                                                               data-dismiss="fileinput" id="removebtn">Remove</a>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div> -->

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Status<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" name="eStatus">
                                                        <option value=""> - Select - </option>
                                                        <option value="Active" <?php echo (isset($eStatus) && $eStatus == 'Active' ? 'selected="selected"' : ''); ?> >Active</option>
                                                        <option value="Inactive" <?php echo (isset($eStatus) && $eStatus == 'Inactive' ? 'selected="selected"' : ''); ?> >Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <br>
                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <input type="submit" name="senditnow" value="Save" class="btn btn-sm btn-primary"/>
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
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.css" type="text/css" media="screen" />
        <script src="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.js" type="text/javascript" charset="utf-8"></script>

        <script>
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                function changeCriteria(){
                    if ( $("#eCriteria").val()=="Location") {
                        $('#locationDiv').show();
                        $('#ageDiv').hide();
                    } else if ( $("#eCriteria").val()=="Age") {
                        $('#ageDiv').show();
                        $('#locationDiv').hide();
                    } else {
                        $('#ageDiv').hide();
                        $('#locationDiv').hide();
                    }
                }
                
                function changeLinkedRestaurant(){
                    var restaurant  = $("#linkedRestaurant").val();
                    $("#linkedEvent option[restaurantid!='"+restaurant+"']").hide();
                    $("#linkedCombo option[restaurantid!='"+restaurant+"']").hide();
                    $("#linkedOffer option[restaurantid!='"+restaurant+"']").hide();
                    $("#linkedEvent option[restaurantid='"+restaurant+"']").show();
                    $("#linkedCombo option[restaurantid='"+restaurant+"']").show();
                    $("#linkedOffer option[restaurantid='"+restaurant+"']").show();
                }
                
                function changeLink(){
                    if ( $("#eLink").val()=="Restaurant") {
                        $("#linkRestaurantDiv").show();
                        $("#linkFeaturedDiv").hide();
                        $("#linkEventDiv").hide();
                        $("#linkComboDiv").hide();
                        $("#linkOfferDiv").hide();
                    } else if ( $("#eLink").val()=="Featured") {
                        $("#linkRestaurantDiv").hide();
                        $("#linkFeaturedDiv").hide();
                        $("#linkEventDiv").hide();
                        $("#linkComboDiv").hide();
                        $("#linkOfferDiv").hide();
                    } else if ( $("#eLink").val()=="Events") {
                        $("#linkRestaurantDiv").show();
                        $("#linkFeaturedDiv").hide();
                        $("#linkEventDiv").show();
                        $("#linkComboDiv").hide();
                        $("#linkOfferDiv").hide();
                    } else if ( $("#eLink").val()=="Combo") {
                        $("#linkRestaurantDiv").show();
                        $("#linkFeaturedDiv").hide();
                        $("#linkEventDiv").hide();
                        $("#linkComboDiv").show();
                        $("#linkOfferDiv").hide();
                    } else if ( $("#eLink").val()=="Offer") {
                        $("#linkRestaurantDiv").show();
                        $("#linkFeaturedDiv").hide();
                        $("#linkEventDiv").hide();
                        $("#linkComboDiv").hide();
                        $("#linkOfferDiv").show();
                    }else{
                        $("#linkRestaurantDiv").hide();
                        $("#linkFeaturedDiv").hide();
                        $("#linkEventDiv").hide();
                        $("#linkComboDiv").hide();
                        $("#linkOfferDiv").hide();
                    }
                }
                changeCriteria();
                changeLink();
                changeLinkedRestaurant();
                
                $('.datepicker-custom').pickadate({
                    selectYears: true,
                    selectMonths: true,
                    min: new Date()
                });
                $('.timepicker-custom').pickatime({
                    //min: new Date()
                });
                $('#eCriteria').change(changeCriteria);
                $("#linkedRestaurant").change(changeLinkedRestaurant);
                $('#eLink').change(function(){
                    $("#linkedRestaurant").val("");
                    $("#linkedRestaurant").val("");
                    $("#linkedCombo").val("");
                    $("#linkedFeaturedRestaurant").val("");
                    $("#linkedOffer").val("");
                    $("#linkedEvent option").hide();
                    $("#linkedCombo option").hide();
                    $("#linkedOffer option").hide();
                    changeLink();
                });
                
                $("#validateForm").validate({
                    rules: {
//                        eCriteria: {
//                            required: true
//                        },
                        iLocationID: {
                            required: {
                                depends: function () {
                                    return $("#eCriteria").val()=="Location";
                                }
                            }
                        },
                        iMinAge: {
                            required: {
                                depends: function () {
                                    return $("#eCriteria").val()=="Age";
                                }
                            }
                        },
                        iMaxAge: {
                            required: {
                                depends: function () {
                                    return $("#eCriteria").val()=="Age";
                                }
                            }
                        },
                        vNotifyText: {
                            required: true
                        },
                        scheduleDate: {
                            required: true
                        },
                        scheduleTime: {
                            required: true
                        },
                        eStatus: {
                            required: true
                        }
                    },
                    messages: {
//                        eCriteria   : "Please select criteria",
                        iLocationID : "Please select location",
                        iMinAge      : "Please enter valid minimum age.",
                        iMaxAge      : "Please enter valid maximum age.",
                        vNotifyText : "Please enter a Notify Message Text",
                        scheduleDate: "Please enter Schedule Date",
                        scheduleTime: "Please enter Schedule Time",
                        eStatus     : "Please select status"
                    }
                });

                App.setPage("elements");  //Set current page
                App.init(); //Initialise plugins and elements


                //code for image starts
                var _URL = window.URL || window.webkitURL;
                $("#vImage").change(function (e) {
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
                //code for image ends
                //
                // $('.fileinput').fileinput()
                $('.uniform').uniform();



            });



        </script>
    </body>
</html>
