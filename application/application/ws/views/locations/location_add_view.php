<?php
$headerData = $this->headerlib->data();
if (isset($getLocationData) && $getLocationData != '')
    extract($getLocationData);

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 29);
//print_r($getFacilityData);

$form_attr = array(
    'name' => 'location-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);

$vLocationNameField = array(
    'name' => 'vLocationName',
    'id' => 'vLocationName',
    "required" => "required",
    'placeholder' => 'Please enter location name',
    "data-errortext" => "This is location name!",
    'value' => (isset($vLocationName) && $vLocationName != '') ? $vLocationName : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$vCityField = array(
    'name' => 'vCity',
    'id' => 'vCity',
    'placeholder' => 'Please provide city name',
    'value' => (isset($vCity) && $vCity != '') ? $vCity : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);
$vStateField = array(
    'name' => 'vState',
    'id' => 'vState',
    'placeholder' => 'Please provide state name',
    'value' => (isset($vState) && $vState != '') ? $vState : '',
    'type' => 'text',
    'class' => 'form-control maxwidth500'
);

$vCountryField = array(
    'name' => 'vCountry',
    'id' => 'vCountry',
    'placeholder' => 'Please provide Country Name',
    'value' => (isset($vCountry) && $vCountry != '') ? $vCountry : '',
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
    "action" => "backoffice.locationedit"
);

$hiddenaddattr = array(
    "action" => "backoffice.locationadd"
);

$deal_id = array(
    "iLocationID" => (isset($iLocationID) && $iLocationID != '') ? $iLocationID : ''
);

$submit_attr = array(
    'class' => 'submit btn btn-sm btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Location",
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
                                            <li>Deal</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Location</h3>
                                        </div>
                                        <div class="description">Add/Edit Location</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Location"; ?></h4>
                                            <div class="tools hidden-xs">

                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>

                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("locations/add", $form_attr);
                                            if (isset($iLocationID) && $iLocationID != '') {
                                                echo form_hidden($deal_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">City</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" name="iLocZoneID" id="iLocZoneID" required="required">
                                                        <option value=""> - Select City - </option>
                                                        <?php
                                                        for ($i = 0; $i < count($getZone); $i++) {
                                                            echo '<option value="' . $getZone[$i]['iLocZoneID'] . '" ' . (isset($getZone) && $iLocZoneID == $getZone[$i]['iLocZoneID'] ? 'selected="selected"' : '') . '>' . $getZone[$i]['vZoneName'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="vLocationName">Location Name<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <?= form_input($vLocationNameField); ?>
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

        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places&sensor=false"></script>

        <script>
            var locId = parseInt('<?= (isset($iLocationID) && $iLocationID != '') ? $iLocationID : 0; ?>');
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if ((locId > 0 && permission.indexOf('2') >= 0) || (locId == 0 && permission.indexOf('1') >= 0)) {

                } else if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                var iLocationID = '<?= isset($iLocationID) ? $iLocationID : 0; ?>';
                $("#validateForm").validate({
                    rules: {
                        vLocationName: {
                            required: true
                        },
                        vCity: {
                            required: true
                        },
                        vState: {
                            required: true
                        },
                        vCountry: {
                            required: true
                        }
                    },
                    messages: {
                        vLocationName: "Please enter location name",
                        vCity: "Please enter city name",
                        vState: "Please enter state name",
                        vCountry: "Please enter country name"
                    }
                });

                // $('.fileinput').fileinput()
                $('.uniform').uniform();
            });

        </script>
    </body>
</html>
