<?php
$headerData = $this->headerlib->data();
if (isset($getCategoryData) && $getCategoryData != '')
    extract($getCategoryData);

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 20);

//print_r($getCategoryData); 

$form_attr = array(
    'name' => 'category-form',
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
    "action" => "backoffice.categoryedit"
);
$hiddenaddattr = array(
    "action" => "backoffice.categoryadd"
);
$category_id = array(
    "iCategoryID" => (isset($iCategoryID) && $iCategoryID != '') ? $iCategoryID : ''
);
$submit_attr = array(
    'class' => 'submit btn-sm btn btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Category",
    'type' => 'submit'
);
$cancel_attr = array(
    'class' => 'btn btn-inverse ',
    'value' => "Reset",
    'type' => 'reset'
);
$pic = (isset($vCategoryImage) && $vCategoryImage != '') ? $vCategoryImage : '';
$uid = (isset($iCategoryID) && $iCategoryID != '') ? $iCategoryID : '';
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
                                            <li>Category</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Category</h3>
                                        </div>
                                        <div class="description">Add/Edit Category</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Category"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("category/add", $form_attr);
                                            if (isset($iCategoryID) && $iCategoryID != '') {
                                                echo form_hidden($category_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Category Name <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vCategoryName" value="<?php echo (isset($vCategoryName) && $vCategoryName != '') ? $vCategoryName : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Category Image</label>
                                                <div class="col-sm-9">

                                                    <?php
                                                    
                                                    if ($pic != '' && $uid != '') {
                                                        $pic_str = '<img src="' . IMGURL . '/category/' . $uid . '/thumb/' . $pic . '">';
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
                                                        <input type="hidden" name="vCategoryUrl" value="<?php echo $pic; ?>"/>
                                                        <div>
                                                            <span class="btn btn-default btn-file">
                                                                <span class="<?php echo $sel_text ?> select_pic" data-trigger="fileinput"  >Select image</span>
                                                                <span class="<?php echo $change_text ?> select_pic" data-trigger="fileinput">Change</span>
                                                                <input type="file" 
                                                                       name="vCategoryImage" 
                                                                       accept="image/jpg, image/JPG,image/JPEG, image/jpeg, image/png, image/PNG" 
                                                                       id="vCategoryImage" />
                                                            </span>
                                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput" id="removebtn">Remove</a>
                                                        </div>
                                                        <input type="hidden" id="removepic" name="removepic" value="0" />
                                                    </div>

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
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.css" type="text/css" media="screen" />
        <script src="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.js" type="text/javascript" charset="utf-8"></script>

        <script>
            var catId = parseInt('<?= (isset($iCategoryID) && $iCategoryID != '') ? $iCategoryID : 0; ?>');
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if ((catId > 0 && permission.indexOf('2') >= 0) || (catId == 0 && permission.indexOf('1') >= 0)) {

                } else if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                $("#validateForm").validate({
                    rules: {
                        vCategoryName: {
                            required: true
                        },
                    },
                    messages: {
                        vCategoryName: "Please enter a Category Name",
                    }
                });

                var _URL = window.URL || window.webkitURL;
                $("#vCategoryImage").change(function (e) {

                    var $file = $(this);
                    var fileExt = ($file.val()).split('.').pop().toUpperCase();
                    //document.getElementById('removepic').value = 0;

                    var image, file;
                    if (file = this.files[0]) {
                        image = new Image();
                        image.onload = function () {
                            //alert(this.width + ' ' + this.height);
                            if (fileExt == 'JPG' || fileExt == 'JPEG' || fileExt == 'PNG') {
                                /*if (parseInt(this.width) > 640 && parseInt(this.height) > 640) {
                                 alert('File dimension should be 640 x 640');
                                 $file.replaceWith($file.val('').clone(true));
                                 $('#removebtn').trigger('click');
                                 }*/

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

                $(document).on('click', '#removebtn', function () {
                    //document.getElementById('removepic').value = 1;
                    $("#removepic").val('1');
                });
                $(document).on('click', '.select_pic', function () {
                    //document.getElementById('removepic').value = 0;
                    $("#removepic").val('0');
                });

                // $('.fileinput').fileinput()
                $('.uniform').uniform();
            });



        </script>
    </body>
</html>
