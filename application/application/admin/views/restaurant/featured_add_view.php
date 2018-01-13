<?php
$headerData = $this->headerlib->data();
if (isset($record) && $record != '')
    extract($record);


//print_r($getCategoryData);

$form_attr = array(
    'name' => 'featured-form',
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
    "action" => "backoffice.featurededit"
);
$hiddenaddattr = array(
    "action" => "backoffice.featuredadd"
);
$featured_id = array(
    "iFeaturedID" => (isset($iFeaturedID) && $iFeaturedID != '') ? $iFeaturedID : ''
);
$submit_attr = array(
    'class' => 'submit btn-sm btn btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Featured Restaurant",
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
                                            <li>Featured Restaurant</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Featured Restaurant</h3>
                                        </div>
                                        <div class="description">Add/Edit Featured Restaurant</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Featured Restaurant"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open($this->controller . "/featured/submit", $form_attr);
                                            if (isset($iFeaturedID) && $iFeaturedID != '') {
                                                echo form_hidden($featured_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Category Name <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" required="" name="iCategoryID" id="iCategoryID">
                                                        <option value="">Select any category</option>
                                                        <?php
                                                        for ($i = 0; $i < count($listCat); $i++) {
                                                            echo '<option value="' . $listCat[$i]['iCategoryID'] . '" ' . (isset($record['iCategoryID']) && $record['iCategoryID'] == $listCat[$i]['iCategoryID'] ? 'selected="selected"' : '') . '>' . $listCat[$i]['vCategoryName'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Restaurant Name <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" required="" name="iRestaurantID" id="iRestaurantID">
                                                        <option value="">Select any restaurant</option>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <?php echo form_input($submit_attr); ?>
                                                    <a class="btn btn-sm btn-grey" href="<?= BASEURL .''. $this->controller; ?>/featured">Cancel</a>
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
            jQuery(document).ready(function ()
            {
                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements


                var _URL = window.URL || window.webkitURL;
                $("#iCategoryID").change(function (e) {
                    var $cat = $(this);
                    var cat_id = $cat.val();
                    if (cat_id !== '') {
                        $.ajax({
                            method: 'POST',
                            url: '<?= BASEURL . $this->controller; ?>/ajaxRest',
                            dataType: 'json',
                            data: {
                                val: cat_id
                            },
                            success: function (resp) {
                                if (resp.status == 200) {
                                    $('#iRestaurantID').html(resp.html);
                                }
                            }
                        });
                    }

                });


            });



        </script>
        <?php
        if (isset($record)) {
            ?>
            <script>
                setTimeout(function () {
                    $("#iCategoryID").trigger('change');
                }, 500);
            </script>
            <?php
        }
        ?>
    </body>
</html>
