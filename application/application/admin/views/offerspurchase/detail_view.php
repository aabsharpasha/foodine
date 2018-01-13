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
                                            <li><a href="<?= BASEURL ?>offerspurchase"><?php echo $this->uppercase; ?></a></li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left"><?php echo $this->uppercase; ?> </h3>
                                        </div>
                                        <div class="description">Offers Purchase Details</div>
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
                                            <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table  table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th>Username</th>
                                                        <th>Email</th>
                                                        <th>Phone</th>
                                                        <th>Order Id</th>
                                                        <th>Redemption Date & Time</th>
                                                        <th>Sale Through</th>
                                                        <th>Redemption Type</th>
<!--                                                        <th>Action</th>-->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Username</th>
                                                        <th>Email</th>
                                                        <th>Phone</th>
                                                        <th>Order Id</th>
                                                        <th>Redemption Date & Time</th>
                                                        <th>Sale Through</th>
                                                        <th>Redemption Type</th>
<!--                                                        <th>Action</th>-->
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
            url = controller + '/purchaseDetailPaginate/'+ "<?php echo $iComboOffersID ?>";
            $(document).ready(function () {
                $(".fancybox").fancybox();
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');
                var btn_show_hide = parseInt('<?= $ADMINTYPE == 1 ? 1 : 1; ?>');

                var target = [
                ];
                var aoculumn = [
                    /*0*/ {"mData": "userName", "sWidth": "15%"},
                    /*1*/ {"mData": "userEmail", "sWidth": "15%"},
                    /*2*/ {"mData": "userPhone", "sWidth": "15%"},
                    /*3*/ {"mData": "orderId", "sWidth": "15%"},
                    /*4*/ {"mData": "redemptionDate", bSearchable: false, "sWidth": "15%"},
                    /*5*/ {"mData": "saleThrough", bSearchable: false, "sWidth": "15%"},
                    /*6*/ {"mData": "redemptionType", bSearchable: false, "sWidth": "10%"}
//                    /*5*/ {"mData": "iComboSubOffersID", bSortable: false, bSearchable: false, "sWidth": "10%"}
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
                        url: BASEURL + controller + '/viewOfferDetail/' + target_id,
                        success: function (resp) {
                            $('#pickup-asset-collection').html(resp);
                        }
                    });
                });
                $('#table-modal').on('hidden.bs.modal', function () {
                    //alert();
                    $('#pickup-asset-collection').html('<div class="text-center"><img src="<?= BASEURL; ?>img/ajax-loader.gif" title="Please Wait..." alt="Please wait..."/></div>');
                });

            });

        </script>

    </body>