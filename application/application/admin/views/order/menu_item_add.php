<?php
$headerData = $this->headerlib->data();
if (isset($itemData) && $itemData != '')
    extract($itemData);

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 80);
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
$item_id = array(
    "iItemId" => (isset($iItemId) && $iItemId != '') ? $iItemId : ''
);

$pic = (isset($vItemImage) && $vItemImage != '') ? $vItemImage : '';
$uid = (isset($iItemId) && $iItemId != '') ? $iItemId : '';

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
                                            <li>Online Order</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Menu Item</h3>
                                        </div>
                                        <div class="description">Menu Item</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Menu Item"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("order/addMenuItem", $form_attr);
                                            if (isset($iItemId) && $iItemId != '') {
                                                echo form_hidden($item_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Restaurant<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" name="iRestaurantID" id="iRestaurantID" value="<?php echo (isset($iRestaurantID) && $iRestaurantID != '') ? $iRestaurantID : ''; ?>">
                                                        <option value=""> - Select - </option>
                                                        <?php foreach($restaurants As $restaurant){ ?>
                                                        <option value="<?php echo $restaurant['iRestaurantID']?>" <?php echo (isset($iRestaurantID) && $iRestaurantID == $restaurant['iRestaurantID'] ? 'selected="selected"' : ''); ?> ><?php echo $restaurant['vRestaurantName']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Item Name<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vItemName" value="<?php echo (isset($vItemName) && $vItemName != '') ? $vItemName : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Item Description<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control maxwidth500" name="tItemDesc"><?php echo (isset($tItemDesc) && $tItemDesc != '') ? $tItemDesc : ''; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Item Price<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="dItemPrice" value="<?php echo (isset($dItemPrice) && $dItemPrice != '') ? $dItemPrice : ''; ?>" number>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Item Image</label>
                                                <div class="col-sm-9">

                                                    <?php
                                                    if ($pic != '' && $uid != '') {
                                                        $pic_str = '<img src="' . IMGURL . '/orderMenuItem/' . $uid . '/thumb/' . $pic . '">';
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
                                                        <input type="hidden" name="vItemImage" value="<?php echo $pic; ?>"/>
                                                        <div>
                                                            <span class="btn btn-default btn-file">
                                                                <span class="<?php echo $sel_text ?> select_pic" data-trigger="fileinput"  >Select image</span>
                                                                <span class="<?php echo $change_text ?> select_pic" data-trigger="fileinput">Change</span>
                                                                <input type="file" 
                                                                       name="vItemImage" 
                                                                       accept="image/jpg, image/JPG,image/JPEG, image/jpeg, image/png, image/PNG" 
                                                                       id="vItemImage" />
                                                            </span>
                                                            <a href="#" 
                                                               class="btn btn-default fileinput-exists" 
                                                               data-dismiss="fileinput" id="removebtn">Remove</a>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Item Category<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" name="iItemCategoryId" id="iItemCategoryId" value="<?php echo (isset($iItemCategoryId) && $iItemCategoryId != '') ? $iItemCategoryId : ''; ?>">
                                                        <option value=""> - Select - </option>
                                                        <?php foreach($menuItemCategories As $menuItemCategory){ ?>
                                                        <option value="<?php echo $menuItemCategory['iItemCategoryId']?>" <?php echo (isset($iItemCategoryId) && $iItemCategoryId == $menuItemCategory['iItemCategoryId'] ? 'selected="selected"' : ''); ?> ><?php echo $menuItemCategory['vName']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Meal Type<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" name="iMealTypeId" id="iMealTypeId" value="<?php echo (isset($iMealTypeId) && $iMealTypeId != '') ? $iMealTypeId : ''; ?>">
                                                        <option value=""> - Select - </option>
                                                        <?php foreach($mealTypes As $mealType){ ?>
                                                        <option value="<?php echo $mealType['iMealTypeId']?>" <?php echo (isset($iMealTypeId) && $iMealTypeId == $mealType['iMealTypeId'] ? 'selected="selected"' : ''); ?> ><?php echo $mealType['vName']?></option>
                                                        <?php } ?>
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
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>js/magic-suggest/magicsuggest-1.3.1-min.css" />
        <script type="text/javascript" src="<?= base_url() ?>js/magic-suggest/magicsuggest-1.3.1-min.js"></script>

        <script>
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }
                $("#validateForm").validate({
                    rules: {
                        iRestaurantID: {
                            required: true
                        },
                        vItemName: {
                            required: true
                        },
                        tItemDesc: {
                            required: true
                        },
                        dItemPrice: {
                            required: true
                        },
                        iItemCategoryId: {
                            required: true
                        },
                        iMealTypeId: {
                            required: true
                        }
                    },
                    messages: {
                        iRestaurantID   : "Please select restaurant",
                        vItemName       : "Please enter item name",
                        tItemDesc       : "Please enter item desription",
                        dItemPrice      : "Please enter valid item price",
                        iItemCategoryId : "Please select item category",
                        iMealTypeId     : "Please select meal type",
                    }
                });

                App.setPage("elements");  //Set current page
                App.init(); //Initialise plugins and elements


                //code for image starts
                var _URL = window.URL || window.webkitURL;
                $("#vItemImage").change(function (e) {
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
//                document.getElementById('removepic').value = 0;

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
