<?php
$headerData = $this->headerlib->data();
if (isset($data) && $data != '')
    extract($data);
//mprd($event);
?>
<style>
    .left-float {
        float:left; 
        width:20%;
    }
</style>
<!doctype html>
<html lang="en-us">
    <head>
        <title><?= $title ?></title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets']; ?>
        <link rel="stylesheet" href="<?php echo JS_URL; ?>css/cloud-admin.css" type="text/css" media="screen" />
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
                                            <h3 class="content-title pull-left">Manage <?php echo $this->uppercase; ?></h3>
                                        </div>
                                        <div class="description">Banner Management</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12 main-rest-list-cms">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i>Featured <?php echo $this->uppercase; ?> list</h4>
                                        </div>
                                        <div class="box-body big">
                                            <?php for($i=1; $i<=5 ; $i++) {?>
                                            <div class="form-group left-float">
                                                <div class="col-sm-12">
                                                    <select name="rest_featured_<?php echo $i;?>" id="rest_featured_<?php echo $i;?>" class="maxwidth500 col-lg-12" required="required">
                                                        <option value="" > - Select Restaurant - </option>
                                                        <?php
                                                        foreach ($data as $key => $value) {
                                                            if (isset($featured[$i]) && ($value['iRestaurantID'] == $featured[$i]['iTypeId'])) {
                                                                echo '<option value="' . $value['iRestaurantID'] . '" selected="selected">' . $value['vRestaurantName'] . '</option>';
                                                            } else {
                                                                echo '<option value="' . $value['iRestaurantID'] . '">' . $value['vRestaurantName'] . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php }?>
                                        </div>
                                    </div>
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i>Event <?php echo $this->uppercase; ?> list</h4>
                                        </div>
                                        <?php if(isset($eventData) && $eventData != '') {?>
                                        <div class="box-body big">
                                            <?php for($i=1; $i<=5 ; $i++) {?>
                                            <div class="form-group left-float">
                                                <div class="col-sm-12">
                                                    <select name="rest_event_<?php echo $i;?>" id="rest_event_<?php echo $i;?>" class="maxwidth500 col-lg-12" required="required">
                                                        <option value="" > - Select Restaurant - </option>
                                                        <?php
                                                        foreach ($eventData as $key => $value) {
                                                            if (isset($event[$i]) && ($value['iEventId'] == $event[$i]['iTypeId'])) {
                                                                echo '<option value="' . $value['iEventId'] . '" selected="selected">' . $value['iEventTitle'] . '</option>';
                                                            } else {
                                                                echo '<option value="' . $value['iEventId'] . '">' . $value['iEventTitle'] . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php }?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i>Deals <?php echo $this->uppercase; ?> list</h4>
                                        </div>
                                        <?php if(isset($dealData) && $dealData != '') {?>
                                        <div class="box-body big">
                                            <?php for($i=1; $i<=5 ; $i++) {?>
                                            <div class="form-group left-float">
                                                <div class="col-sm-12">
                                                    <select name="rest_deals_<?php echo $i;?>" id="rest_deals_<?php echo $i;?>" class="maxwidth500 col-lg-12" required="required">
                                                        <option value="" > - Select Restaurant - </option>
                                                        <?php
                                                        foreach ($dealData as $key => $value) {
                                                            if (isset($deals[$i]) && ($value['iDealID'] == $deals[$i]['iTypeId'])) {
                                                                echo '<option value="' . $value['iDealID'] . '" selected="selected">' . $value['vOfferText'] . '</option>';
                                                            } else {
                                                                echo '<option value="' . $value['iDealID'] . '">' . $value['vOfferText'] . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php }?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i>Combo <?php echo $this->uppercase; ?> list</h4>
                                        </div>
                                        <?php if(isset($comboData) && $comboData != '') {?>
                                        <div class="box-body big">
                                            <?php for($i=1; $i<=5 ; $i++) {?>
                                            <div class="form-group left-float">
                                                <div class="col-sm-12">
                                                    <select name="rest_combo_<?php echo $i;?>" id="rest_combo_<?php echo $i;?>" class="maxwidth500 col-lg-12" required="required">
                                                        <option value="" > - Select Restaurant - </option>
                                                        <?php
                                                        foreach ($comboData as $key => $value) {
                                                            if (isset($combo[$i]) && ($value['iComboOffersID'] == $combo[$i]['iTypeId'])) {
                                                                echo '<option value="' . $value['iComboOffersID'] . '" selected="selected">' . $value['vOfferText'] . '</option>';
                                                            } else {
                                                                echo '<option value="' . $value['iComboOffersID'] . '">' . $value['vOfferText'] . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php }?>
                                        </div>
                                        <?php } ?>
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
        <?php $ADMINTYPE = $this->session->userdata('ADMINTYPE'); ?>
        <script>
            var oTable, controller = '<?php echo $this->controller; ?>', imagepath = '<?php echo RESTAURANT_IMAGE_PATH ?>', no_img_url = '<?php echo DOMAIN_URL; ?>/admin/img/no-image.png';
            $(document).ready(function () {
                $(".fancybox").fancybox();
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                $("[id^='rest_']").change(function(){
                    var id = $(this).attr('id');
                    var typeArray = id.split('_');
                    var type = typeArray[1];
                    var restId = $('#'+id).val();
                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        data: {restId:restId, type:typeArray[1], id:typeArray[2]},
                        url: BASEURL + controller + '/saveBanner/',
                        success: function () {
                        }
                    });
                });
            });
        </script>
    </body>
</html>
<?php
$this->session->unset_userdata('SUCCESS');
$this->session->unset_userdata('ERROR');
?>