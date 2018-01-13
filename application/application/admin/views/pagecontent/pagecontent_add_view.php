<?php
$headerData = $this->headerlib->data();
if (isset($getPageContentData) && $getPageContentData != '')
    extract($getPageContentData);

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 46);

$form_attr = array(
            'name' => 'validateForm',
            "id" => "validateForm",
            'method' => 'post',
            'class' => "form-horizontal",
            'role' => 'form'
);
$page_title = array(
            'name' => 'vPageTitle',
            'id' => 'vPageTitle',
            'placeholder' => 'Please provide page name',
            'value' => (isset($vPageTitle) && $vPageTitle != '') ? $vPageTitle : '',
            'type' => 'text',
            'class' => 'form-control maxwidth500'
);
$page_keywords = array(
            'name' => 'tMetaKeywords',
            'id' => 'tMetaKeywords',
            'placeholder' => 'Please provide keywords',
            'value' => (isset($tMetaKeywords) && $tMetaKeywords != '') ? $tMetaKeywords : '',
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
$hiddeneditattr = array("action" => "backoffice.pagecontentedit");
$hiddenaddattr = array("action" => "backoffice.pagecontentadd");
$page_id = array("iPageID" => (isset($iPageID) && $iPageID != '') ? $iPageID : '');
$submit_attr = array(
    'class' => 'submit btn btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Page",
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
                                            <li>Content management</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Content management</h3>
                                        </div>
                                        <div class="description">Tittle, Keywords , Content and more</div>
                                    </div>
                                </div>
                            </div>
<?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-bars"></i><?php echo $ACTION_LABEL . " Content"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <!-- <a href="#box-config" data-toggle="modal" class="config">
                                                  <i class="fa fa-cog"></i>
                                                </a> -->
                                                <!-- <a href="javascript:;" class="reload">
                                                  <i class="fa fa-refresh"></i>
                                                </a> -->
                                                <a href="javascript:;" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                                <!--  <a href="javascript:;" class="remove">
                                                   <i class="fa fa-times"></i>
                                                 </a> -->
                                            </div>
                                        </div>
                                        <div class="box-body big">
<?php
echo form_open("pagecontent/add", $form_attr);
if (isset($iPageID) && $iPageID != '') {

    echo form_hidden($page_id);
    echo form_hidden($hiddeneditattr);
} else {
    echo form_hidden($hiddenaddattr);
}
?>		
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Page Title<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                            <?php echo form_input($page_title); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Page Content</label>
                                                <div class="col-sm-9">
                                                    <!-- CKE -->
                                                    <div class="box border primary">
                                                        <div class="box-title">
                                                            <h4><i class="fa fa-pencil-square"></i>Content Editor</h4>
                                                            <div class="tools hidden-xs">

                                                                <!-- 	<a href="javascript:;" class="reload">
                                                                                <i class="fa fa-refresh"></i>
                                                                        </a> -->
                                                                <a href="javascript:;" class="collapse">
                                                                    <i class="fa fa-chevron-up"></i>
                                                                </a>
                                                                <!-- 	<a href="javascript:;" class="remove">
                                                                                <i class="fa fa-times"></i>
                                                                        </a> -->
                                                            </div>
                                                        </div>
                                                        <div class="box-body">
                                                            <textarea class="ckeditor" name="tContent"> <?= (isset($tContent) && $tContent != '') ? $tContent : '' ?></textarea>
                                                        </div>
                                                    </div>
                                                    <!-- /CKE -->

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
        <script type="text/javascript" src="<?= base_url() ?>js/ckeditor/ckeditor.js"></script>
        <script>
            var pgId = parseInt('<?= (isset($iPageID) && $iPageID != '') ? $iPageID : 0; ?>');
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (pgId > 0 && permission.indexOf('2') >= 0) {

                } else if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                App.setPage("rich_text_editors");  //Set current page
                App.init(); //Initialise plugins and elements
                $("#validateForm").validate({
                    rules: {
                        // vCountry: {
                        //  required: true,
                        // },
                        vPageTitle: {
                            required: true
                        },
                        // tContent: {
                        //   required: true
                        // }



                    },
                    messages: {
                        vPageTitle: "Please enter a title",
                        tContent: "Please enter a content"

                    }
                });
            });
        </script>
    </body>
</html>  
