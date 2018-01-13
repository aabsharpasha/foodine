<?php
$headerData = $this->headerlib->data();

$form_attr = array(
    'name' => 'music-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);
?>
<!doctype html>
<html lang="en-us">
    <head>
        <title><?= $title ?></title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets']; ?>
        <link rel="stylesheet" href="<?php echo JS_URL; ?>css/cloud-admin.css" type="text/css" media="screen" />

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
            .btn-group{
                width:100% !important;
            }
            .btn{
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
                            <div id="divtoappend" class="row">
                                <div class="col-sm-12">
                                    <div class="page-header">
                                        <ul class="breadcrumb">
                                            <li>
                                                <i class="fa fa-tachometer"></i>
                                                <a href="<?= BASEURL ?>">Home</a>
                                            </li>
                                            <li><?php echo $this->uppercase; ?></li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Featured <?php echo $this->uppercase; ?></h3>
                                        </div>
                                        <div class="description">Featured Restaurant</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12 main-rest-list-cms">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i><?php echo $this->uppercase; ?> list</h4>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("restaurant/saveFeaturedRestaurants", $form_attr);
                                            ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tUsers">Select Restaurants<span class="required">*</span></label>
                                                <div class="col-sm-9 user-select">
                                                    <select class="form-control maxwidth500" name="restaurants[]" id="selectRestaurants" multiple="multiple">
                                                        <?php foreach($restaurantFeatured AS $value){ ?>
                                                        <option value="<?php echo $value["Id"]; ?>" <?php if($value['eFeatured'] == 'yes') echo "selected" ?>><?php echo $value["vRestaurantName"]?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <input type="submit" name="senditnow" value="Save" class="btn-sm btn-primary"/>
                                                    &nbsp;<a class="btn-sm btn-grey" href="<?= BASEURL . $this->controller; ?>/handpickRest">Cancel</a>
                                                </div>
                                            </div>
                                            <?php echo form_close(); ?>
<!--                                        <div class="threerow">
                                        <?php foreach ($restaurantFeatured as $key => $value) {
//                                            foreach ($restaurantFeatured as $key => $featured) { ?>
                                            <div class="box-body col-sm-4">
                                                <div class="form-group">
                                                    <label class="col-sm-6 control-label"><?= $value['vRestaurantName'] ?></label>
                                                    <div class="col-sm-6 alignLeft">
                                                        <input type="checkbox" 
                                                               id="rest-<?= $value['Id'] ?>" 
                                                               name="<?= $value['vRestaurantName'] ?>" 
                                                               value="1" 
                                                               data-id="<?= $value['Id'] ?>"
                                                               <?= ($value['eFeatured'] == 'yes') ? 'checked="checked"' : ''; ?>/>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } //}?>
                                        </div>-->
                                    </div>
                                </div>
                            </div>

                            <?php $this->load->view('include/footer_view') ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?= $headerData['javascript_view']; ?>
        <script type="text/javascript" src="<?php echo JS_URL; ?>/js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>   
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.css" type="text/css" media="screen" />
        <script src="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript" charset="utf-8"></script>
        <?php $ADMINTYPE = $this->session->userdata('ADMINTYPE'); ?>
        <script>
            var oTable, controller = '<?php echo $this->controller; ?>', imagepath = '<?php echo RESTAURANT_IMAGE_PATH ?>', no_img_url = '<?php echo DOMAIN_URL; ?>/admin/img/no-image.png';
             $(document).ready(function () {
                $(".fancybox").fancybox();
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                $('#selectRestaurants').multiselect({
                    includeSelectAllOption: true,
                    selectAllText: 'Select All Restaurants',
                    enableFiltering: true,
//                    filterBehavior: 'value'
//                    selectAllValue: 'all'
                });
            });
    
         $('[id^="rest-"]').click(function(){
                var id = parseInt($(this).attr('data-id'));
                var featured = false;
                if($(this).is(':checked')){
                    featured = true;
                }
                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        data: {id:id, featured:featured},
                        url: BASEURL + controller + '/saveFeatured/',
                        success: function () {
                        }
                    });
            });
        </script>
    </body>
</html>
<?php
$this->session->unset_userdata('SUCCESS');
$this->session->unset_userdata('ERROR');
?>