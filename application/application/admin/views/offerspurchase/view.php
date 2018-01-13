<?php
$headerData = $this->headerlib->data();
?>
<!doctype html>
<html lang="en-us">
    <head>
        <title><?= $title ?></title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets']; ?>
    </head>
    <style type="text/css">
        .modal-body{
            display:inline;
        }
        .delete_comment{
            position: absolute;
            right:0px;
            top:5px;
        }
    </style>
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
                                            <h3 class="content-title pull-left"><?php echo $this->uppercase; ?> </h3>
                                        </div>
                                        <div class="description">Offers Purchase Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i><?php echo $this->uppercase; ?> List</h4>
                                            <div class="tools ">
                                                <a id="fa-refresh" href="javascript:;" class="reload">
                                                    <i class="fa fa-refresh"></i>
                                                </a>
                                                <a href="javascript:;" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table  table-bordered " style="overflow-x: auto;display: block !important;">
                                                <thead>
                                                    <tr>
                                                        <th>Restaurant Name</th>
                                                        <th>Address</th>
                                                        <th>Phone Number</th>
                                                        <th>Email</th>
                                                        <th>Combo Id</th>
                                                        <th>Combo Details</th>
                                                        <th>Unit Price</th>
                                                        <th>Total Combo Sold</th>
                                                        <th>Redeemed Combo</th>
                                                        <th>Unredeemed Combo</th>
                                                        <th>Total Redeemed Cost</th>
                                                        <th>Total Unredeemed Cost</th>
                                                        <th>Total Commission</th>
                                                        <th>Payable Amount</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Restaurant Name</th>
                                                        <th>Address</th>
                                                        <th>Phone Number</th>
                                                        <th>Email</th>
                                                        <th>Combo Id</th>
                                                        <th>Combo Details</th>
                                                        <th>Unit Price</th>
                                                        <th>Total Combo Sold</th>
                                                        <th>Redeemed Combo</th>
                                                        <th>Unredeemed Combo</th>
                                                        <th>Total Redeemed Cost</th>
                                                        <th>Total Unredeemed Cost</th>
                                                        <th>Total Commission</th>
                                                        <th>Payable Amount</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="table-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content modal-table">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Offer Record</h4>
                                        </div>
                                        <div class="modal-body" id="pickup-asset-collection">
                                            <div class="text-center">
                                                <img src="<?= BASEURL; ?>img/ajax-loader.gif" title="Please Wait..." alt="Please wait..."/>
                                            </div>
                                        </div>
                                        <div class="modal-footer"></div>
                                    </div>
                                </div>
                            </div>
                             <div class="modal fade" id="table-modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content modal-table">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Purchase History</h4>
                                        </div>
                                        <div class="modal-body" id="pickup-asset-collection1">
                                            <div class="text-center">
                                                <img src="<?= BASEURL; ?>img/ajax-loader.gif" title="Please Wait..." alt="Please wait..."/>
                                            </div>
                                        </div>
                                        <div class="modal-footer"></div>
                                    </div>
                                </div>
                            </div>
                             <div class="modal fade" id="table-modal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content modal-table">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Redeem History</h4>
                                        </div>
                                        <div class="modal-body" id="pickup-asset-collection2">
                                            <div class="text-center">
                                                <img src="<?= BASEURL; ?>img/ajax-loader.gif" title="Please Wait..." alt="Please wait..."/>
                                            </div>
                                        </div>
                                        <div class="modal-footer"></div>
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
        <?php $ADMINTYPE = $this->session->userdata('ADMINTYPE'); ?>
        <link rel="stylesheet" href="<?php echo JS_URL; ?>/js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
        <script type="text/javascript" src="<?php echo JS_URL; ?>/js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
        <!-- Optionally add helpers - button, thumbnail and/or media -->
        <link rel="stylesheet" href="<?php echo JS_URL; ?>/js/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
        <script type="text/javascript" src="<?php echo JS_URL; ?>/js/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
        <script type="text/javascript" src="<?php echo JS_URL; ?>/js/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
        <link rel="stylesheet" href="<?php echo JS_URL; ?>/js/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />
        <script type="text/javascript" src="<?php echo JS_URL; ?>/js/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
        <script src="<?= JS_URL; ?>js/bootbox/bootbox.min.js"></script>

        <script>
           var oTable, controller = '<?php echo $this->controller; ?>', imagepath = '<?php echo COMBOOFFER_IMAGE_PATH ?>', no_img_url = '<?php echo DOMAIN_URL; ?>/admin/img/no-image.png';
            var url;
            url = controller + '/paginate';
            $(document).ready(function () {
                $(".fancybox").fancybox();
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');
                var btn_show_hide = parseInt('<?= $ADMINTYPE == 1 ? 1 : 1; ?>');

                var target = [
                    {
                        "aTargets": [14], // Column to target
                        "mRender": function (data, type, full) {
                            var buttons = ' <a title="Variants" href="<?= BASEURL ?>' + controller + '/detail/' + full['iComboSubOffersID'] + '"  class="btn btn-sm btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> View Details </a>';
                            return buttons;
                        }
                    }
                ];
                var aoculumn = [
                    /*0*/ {"mData": "vRestaurantName", "sWidth": "6%"},
                    /*1*/ {"mData": "restaurantAddress", bSearchable: false, "sWidth": "6%"},
                    /*2*/ {"mData": "restaurantPhone", bSearchable: false, "sWidth": "6%"},
                    /*3*/ {"mData": "restaurantEmail", bSearchable: false, "sWidth": "6%"},
                    /*4*/ {"mData": "iComboSubOffersID", "sWidth": "6%"},
                    /*5*/ {"mData": "comboDetail", bSearchable: false, "sWidth": "16%"},
                    /*6*/ {"mData": "unitPrice", bSearchable: false, "sWidth": "6%"},
                    /*7*/ {"mData": "quantitySold", bSearchable: false, "sWidth": "6%"},
                    /*8*/ {"mData": "quantityRedeemed", bSearchable: false, "sWidth": "6%"},
                    /*9*/ {"mData": "quantityUnredeemed", bSearchable: false, "sWidth": "6%"},
                    /*10*/ {"mData": "totalRedeemedCost", bSearchable: false, "sWidth": "6%"},
                    /*11*/ {"mData": "totalUnredeemedCost", bSearchable: false, "sWidth": "6%"},
                    /*12*/ {"mData": "totalCommission", bSearchable: false, "sWidth": "6%"},
                    /*13*/ {"mData": "totalPayable", bSearchable: false, "sWidth": "6%"},
                    /*14*/ {"mData": "iComboSubOffersID", bSortable: false, bSearchable: false, "sWidth": "6%"}
                ];
                var delete_val = btn_show_hide == 1 ? controller + '/deleteAll' : '';
                getdatatable(delete_val, url, aoculumn, target, 2, 'desc');

                $('.btn-view-detail').live('click', function () {
                    var $btn = $(this);
                    var target_id = $btn.data('id');
                    //alert(asset_id);

                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        data: {},
                        url: BASEURL + controller + '/viewDetail/' + target_id,
                        success: function (resp) {
                            $('#pickup-asset-collection').html(resp);
                        }
                    });
                });
                
                $('.btn-view-purchase-history').live('click', function () {
                    var $btn = $(this);
                    var target_id = $btn.data('id');
                    //alert(asset_id);

                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        data: {},
                        url: BASEURL + controller + '/viewPurchaseHistory/' + target_id,
                        success: function (resp) {
                            $('#pickup-asset-collection1').html(resp);
                        }
                    });
                });
                
                $('.btn-view-redeem-history').live('click', function () {
                    var $btn = $(this);
                    var target_id = $btn.data('id');
                    //alert(asset_id);

                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        data: {},
                        url: BASEURL + controller + '/viewRedeemHistory/' + target_id,
                        success: function (resp) {
                            $('#pickup-asset-collection2').html(resp);
                            
                        }
                    });
                });
                
                $('#table-modal').on('hidden.bs.modal', function () {
                    //alert();
                    $('#pickup-asset-collection').html('<div class="text-center"><img src="<?= BASEURL; ?>img/ajax-loader.gif" title="Please Wait..." alt="Please wait..."/></div>');
                });
                $('#table-modal1').on('hidden.bs.modal', function () {
                    //alert();
                    $('#pickup-asset-collection1').html('<div class="text-center"><img src="<?= BASEURL; ?>img/ajax-loader.gif" title="Please Wait..." alt="Please wait..."/></div>');
                });
                $('#table-modal2').on('hidden.bs.modal', function () {
                    //alert();
                    $('#pickup-asset-collection2').html('<div class="text-center"><img src="<?= BASEURL; ?>img/ajax-loader.gif" title="Please Wait..." alt="Please wait..."/></div>');
                });

            });

        </script>

    </body>