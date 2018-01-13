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
                                            <li><a href="<?= BASEURL ?>deals/index"><?php echo $this->uppercase; ?></a></li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Walk In Discount</h3>
                                        </div>
                                        <div class="description">Deal Redemption Report</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i>Deal Redemption List</h4>
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
                                                        <th>Customer Name</th>
                                                        <th>Customer Phone</th>
                                                        <th>Restaurant Name</th>
                                                        <th>Offer Redeemed</th>
                                                        <th>Offer Code</th>
                                                        <th>Redemption Date & Time</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Customer Name</th>
                                                        <th>Customer Phone</th>
                                                        <th>Restaurant Name</th>
                                                        <th>Offer Redeemed</th>
                                                        <th>Offer Code</th>
                                                        <th>Redemption Date & Time</th>
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
            url = controller + '/reportPaginate/';
                function changeVisitedStatus(adrs,element){
                    $.ajax({
                        url: BASEURL + adrs,
                        type: 'POST',
                        data: '',
                        success: function (output_string) {
                            if (output_string === '1') {
                                if ($("#" + element).hasClass('btn-success')) {
                                    $("#" + element).html('<i class="fa fa-times-circle-o "></i> Not Availed ');
                                    $("#" + element).switchClass('btn-success', 'btn-inverse');
                                } else if ($("#" + element).hasClass('btn-inverse')) {
                                    $("#" + element).html('<i class="fa fa-check-circle-o "></i> Availed ');
                                    $("#" + element).switchClass('btn-inverse', 'btn-success');
                                }
                            }
                            else
                            {
                            }
                        }
                    });
                }
            $(document).ready(function () {
                $(".fancybox").fancybox();
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');
                var btn_show_hide = parseInt('<?= $ADMINTYPE == 1 ? 1 : 1; ?>');

                var target = [
                    {
                        "aTargets": [6], // Column to target
                        "mRender": function (data, type, full) {
                            var buttons='';
                            if (full['eStatus'] === "availed") {
                                buttons += '<a title="Click to set Not Availed" id="visit' + full['iCodeId'] + '"  onclick="return changeVisitedStatus(' + "'" + controller + "/changeAvailedStatus/" + full['iCodeId'] + "/y'" + ', \'visit' + full['iCodeId'] + '\')"  class="btn btn-success  btn-xs  marginright10 margintop10"><i class="fa fa-check-circle"></i> Availed </a>';
                            }else if (full['eStatus'] === "unavailed"){
                                buttons += '<a title="Click to set Availed" id="visit' + full['iCodeId'] + '"  onclick="return changeVisitedStatus(' + "'" + controller + "/changeAvailedStatus/" + full['iCodeId'] + "/y'" + ', \'visit' + full['iCodeId'] + '\')"  class="btn btn-inverse  btn-xs  marginright10 margintop10"><i class="fa fa-check-circle"></i> Not Availed </a>';
                            }else{
                                buttons += '<strong>Expired</strong>';
                            }
                            return buttons;
                        }
                    }
                ];
                var aoculumn = [
                    /*0*/ {"mData": "userName", "sWidth": "15%"},
                    /*0*/ {"mData": "userMobile", "sWidth": "15%"},
                    /*1*/ {"mData": "vRestaurantName", "sWidth": "15%"},
                    /*2*/ {"mData": "vOfferText", "sWidth": "15%"},
                    /*3*/ {"mData": "vDealCode", "sWidth": "15%"},
                    /*4*/ {"mData": "availedDate", bSearchable: false, "sWidth": "15%"},
                    /*5*/ {"mData": "iCodeId", bSortable: false, bSearchable: false, "sWidth": "10%"}
                ];
                var delete_val = btn_show_hide == 1 ? controller + '/deleteAll' : '';
                getdatatable(delete_val, url, aoculumn, target, 5, 'desc');

            });

        </script>

    </body>