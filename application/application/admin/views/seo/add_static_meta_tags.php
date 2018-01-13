<?php
$headerData = $this->headerlib->data();
if (isset($tagData) && $tagData != ''){
    extract($tagData);
}
//print_r($userList);exit;
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 79);
//print_r($getMusicData);

$form_attr = array(
    'name' => 'music-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);
$hiddenaddattr = array(
    "action" => "backoffice.add"
);
$notify_id = array(
    "iMetaTagId" => isset($iMetaTagId) ? $iMetaTagId : ''
);
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
            .multiselect-container{
                max-height: 400px;
                overflow-y: auto;
                width:100% !important;
                max-width: 500px !important;
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
                                            <li>SEO</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">SEO Meta Tags</h3>
                                        </div>
                                        <div class="description">SEO Static Meta Tags</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i>Edit Static Meta Tags</h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("seo/addStaticMetaTags", $form_attr);
                                            echo form_hidden($notify_id);
                                            echo form_hidden($hiddenaddattr);
                                            ?>


                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Page </label>
                                                <div class="col-sm-9">
                                                    <span><strong><?php echo $vPageName;?></strong></span>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Page Title<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vPageTitle" id="vPageTitle" value="<?php echo (isset($vPageTitle) && $vPageTitle != '') ? $vPageTitle : ''; ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Meta Title<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vMetaTitle" id="vMetaTitle" value="<?php echo (isset($vMetaTitle) && $vMetaTitle != '') ? $vMetaTitle : ''; ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Meta Description<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vMetaDescription" id="vMetaDescription" value="<?php echo (isset($vMetaDescription) && $vMetaDescription != '') ? $vMetaDescription : ''; ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Meta Keywords<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vMetaKeywords" id="vMetaKeywords" value="<?php echo (isset($vMetaKeywords) && $vMetaKeywords != '') ? $vMetaKeywords : ''; ?>">
                                                </div>
                                            </div>

                                            <br>
                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <input type="submit" name="senditnow" value="Update" class="btn btn-sm btn-primary"/>
                                                    &nbsp;<a class="btn btn-sm btn-grey" href="<?= BASEURL .''. $this->controller; ?>/staticMetaTags">Cancel</a>
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

        <script>
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }
                $("#validateForm").validate({
                    rules: {
                        vPageTitle: {
                            required: true
                        },
                        vMetaTitle: {
                            required: true
                        },
                        vMetaDescription: {
                            required: true
                        },
                        vMetaKeywords: {
                            required: true
                        }
                            
                    },
                    messages: {
                        vPageTitle: "Please enter a page title",
                        vMetaTitle: "Please enter meta title",
                        vMetaDescription: "Please enter meta description",
                        vMetaKeywords: "Please select meta keywords"
                    }
                });

                App.setPage("elements");  //Set current page
                App.init(); //Initialise plugins and elements


                // $('.fileinput').fileinput()
                $('.uniform').uniform();



            });



        </script>
    </body>
</html>
