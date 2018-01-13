<?php
$headerData = $this->headerlib->data();
if (isset($getPointsData) && $getPointsData != '')
    extract($getPointsData);


$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 30);
//print_r($getFacilityData);

$form_attr = array(
    'name' => 'facility-form',
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
    "action" => "backoffice.pointsedit"
);
$hiddenaddattr = array(
    "action" => "backoffice.pointsadd"
);
$points_id = array(
    "iUserPointSystemID" => (isset($iUserPointSystemID) && $iUserPointSystemID != '') ? $iUserPointSystemID : ''
);
$submit_attr = array(
    'class' => 'submit btn-sm btn btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Point",
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
                                            <li>Points</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Points</h3>
                                        </div>
                                        <div class="description">Add/Edit Point</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Point"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("points/add", $form_attr);
                                            if (isset($iUserPointSystemID) && $iUserPointSystemID != '') {
                                                echo form_hidden($points_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Point For<span class="required">*</span> </label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vType" value="<?php echo (isset($vType) && $vType != '') ? $vType : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Point Value<span class="required">*</span> </label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="iPoints" value="<?php echo (isset($iPoints) && $iPoints != '') ? $iPoints : ''; ?>">
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

        <script>
            var pntId = parseInt('<?= (isset($iUserPointSystemID) && $iUserPointSystemID != '') ? $iUserPointSystemID : 0; ?>');
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (pntId > 0 && permission.indexOf('2') >= 0) {

                } else if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }
                
                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                $("#validateForm").validate({
                    rules: {
                        vType: {
                            required: true
                        },
                        iPoints: {
                            required: true
                        }
                    },
                    messages: {
                        vType: "Please enter a Point Name",
                        iPoints: "Please enter a Point Value"
                    }
                });
                $('.uniform').uniform();
            });
        </script>
    </body>
</html>
