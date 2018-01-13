<?php
$headerData = $this->headerlib->data();
if (isset($getPictureData) && $getPictureData != '') {
    extract($getPictureData);
}



$form_attr = array(
    'name' => 'picture-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);
$picturename = array(
    'name' => 'vpictureName',
    'id' => 'vpictureName',
    'placeholder' => 'Please provide picturename',
    'value' => (isset($vPictureName) && $vPictureName != '') ? $vPictureName : '',
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
    "action" => "backoffice.imageedit"
);
$hiddenaddattr = array(
    "action" => "backoffice.imageadd"
);

$iRestaurantID=isset($getImageData['iRestaurantID'])?$getImageData['iRestaurantID']:$iRestaurantID;
$restaurant_id = array(
    "iRestaurantID" => (isset($iRestaurantID) && $iRestaurantID != '') ? $iRestaurantID : ''
);
$picture_id = array(
    "iMenuPictureID" => (isset($iMenuPictureID) && $iMenuPictureID != '') ? $iMenuPictureID : ''
);
$submit_attr = array(
    'class' => 'submit btn btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Photo",
    'type' => 'submit'
);
$cancel_attr = array(
    'class' => 'btn btn-inverse ',
    'value' => "Reset",
    'type' => 'reset'
);


$pic = (isset($vProfilePicture) && $vProfilePicture != '') ? $vProfilePicture : '';
$uid = (isset($ipictureID) && $ipictureID != '') ? $ipictureID : '';
$gender = (isset($eGender) && $eGender != '') ? $eGender : '';
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
                                            <li>Restaurant Photo</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Restaurant Menu Photo</h3>
                                        </div>
                                        <div class="description">Add / Edit Restaurant Menu Photo</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-bars"></i><?php echo $ACTION_LABEL . " Menu Photo"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("image/restaurant_menu_add/" . $iRestaurantID, $form_attr);
                                            if (isset($iMenuPictureID) && $iMenuPictureID != '') {
                                                echo form_hidden($picture_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            echo form_hidden($restaurant_id);
                                            ?>

                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Image Type</label> 
                                                <div class="col-md-6">
                                                    <select class="form-control" name="menu_type" id="menu_type" required="">
                                                        <option value="food" <?= isset($eMenuType) && $eMenuType == 'food' ? 'selected="selected"' : ''; ?>>Food</option>
                                                        <option value="bar" <?= isset($eMenuType) && $eMenuType == 'bar' ? 'selected="selected"' : ''; ?>>Bar</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-4 control-label">File Upload</label> 
                                                <div class="col-md-6">
                                                    <input type="file" 
                                                           name="res_image[]" 
                                                           accept="image/jpg, image/JPG,image/JPEG, image/jpeg, image/png, image/PNG" 
                                                           id="res_image" 
                                                           <?= !(isset($iMenuPictureID) && $iMenuPictureID != '') ? 'multiple=""' : '' ?>
                                                           <?= isset($iMenuPictureID) && $iMenuPictureID != '' ? '' : 'required=""'; ?>/>
                                                    <br />
                                                    <div class="col-lg-6">
                                                        <label class="error" id="file_error"></label>
                                                        <br/>
                                                        <?php if (isset($iMenuPictureID) && $iMenuPictureID != '') { ?>
                                                            <img src="<?= RESTAURANT_MENU_IMAGE_PATH  . $iRestaurantID . '/thumb/' . $vPictureName; ?>" style="width:250px;height:150px">
                                                        <?php } ?>
                                                    </div>
                                                    <div class="col-sm-12 padding0">
                                                        <span class="required">
<!--                                                            <strong>NOTE:</strong> Upload file format <strong>jpg, jpeg, png</strong> are allowed.
                                                            <br/>File dimension  should be <strong>140px x 140px</strong>.-->
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <br>
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

        <script src="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.js" type="text/javascript" charset="utf-8"></script>

        <script>
            jQuery(document).ready(function () {
                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                $("#validateForm").validate();

                var _URL = window.URL || window.webkitURL;
                $("#res_image").change(function (e) {
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


            });
            //document.getElementById('removepic').value = 0;
            // $('.fileinput').fileinput()
            $('.uniform').uniform();

            $(document).on('click', '#removebtn', function (event) {
                $("#removepic").val('1');
            });
            $(document).on('click', '.select_pic', function (event) {
                $("#removepic").val('0');
            });

            var picID = parseInt('<?= isset($iPictureID) && $iPictureID != '' ? '1' : '0'; ?>');

            $("input[type='submit']").click(function () {
                if (picID) {
                    var $fileUpload = $("input[type='file']");
                    if (parseInt($fileUpload.get(0).files.length) > 1) {
                        //alert("You can only upload a maximum of 2 files");
                        $('#file_error').html('You can upload only one file.');
                        return false;
                    }
                } else {
                    return true;
                }
            });

        </script>
    </body>
</html>
