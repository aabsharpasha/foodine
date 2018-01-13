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
                                        <div class="description">Offers Purchase History</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i><?php echo $this->uppercase; ?> Purchase History</h4>
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
                                                        <th>Offer</th>
                                                        <th>Restaurant</th>
                                                        <th>User Name</th>
                                                        <th>User Email</th>
                                                        <th>User Mobile</th>
                                                        <th>Qty.</th>
                                                        <th>Price (per unit)</th>
                                                        <th>Price (total)</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Offer</th>
                                                        <th>Restaurant Name</th>
                                                        <th>User Name</th>
                                                        <th>Email</th>
                                                        <th>Phone</th>
                                                        <th>Qty.</th>
                                                        <th>Price (per unit)</th>
                                                        <th>Price (total)</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
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
            url = controller + '/historypaginate';
            $(document).ready(function () {
                $(".fancybox").fancybox();
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');
                var btn_show_hide = parseInt('<?= $ADMINTYPE == 1 ? 1 : 1; ?>');

                var target = [
                    {
                        "aTargets": [8], // Column to target
                        "mRender": function (data, type, full) {
                            var buttons = '';
                            if (full["eAvailedStatus"] == "Availed") { 
                                buttons += '<a title="Click here to cancel redeem" id="aatag'+ full["iUserComboID"] +'" onclick="return changeStatus('+ full["iUserComboID"] +',\'offers/comboStatus/'+ full["iUserComboID"] +'/y\')"  class="btn btn-sm btn-success  marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Redeemed </a>';
                            } else {
                                buttons += '<a title="Click here to redeem" id="aatag'+ full["iUserComboID"] +'" onclick="return changeStatus('+ full["iUserComboID"] +',\'offers/comboStatus/'+ full["iUserComboID"] +'/y\')"  class="btn btn-sm btn-inverse  marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Not Redeemed </a>';
                            }
                            return buttons;
                        }
                    },
                ];
                var aoculumn = [
                    /*0*/ {"mData": "offerText", "sWidth": "25%"},
                    /*1*/ {"mData": "vRestaurantName", "sWidth": "10%"},
                    /*2*/ {"mData": "userName", "sWidth": "10%"},
                    /*3*/ {"mData": "vEmail", "sWidth": "10%"},
                    /*4*/ {"mData": "vMobileNo", "sWidth": "10%"},
                    /*5*/ {"mData": "qty", "sWidth": "10%"},
                    /*6*/ {"mData": "tDiscountedPrice", "sWidth": "10%"},
                    /*7*/ {"mData": "iTotal", "sWidth": "10%"},
                    /*8*/ {"mData": "iUserComboID", bSortable: false, bSearchable: false, "sWidth": "5%"}
                ];
                var delete_val = btn_show_hide == 1 ? controller + '/deleteAll' : '';
                getdatatable(delete_val, url, aoculumn, target, 2, 'desc');
            });

        </script>

    </body>