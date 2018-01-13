<?php
$headerData = $this->headerlib->data();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 39);
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
                                            <h3 class="content-title pull-left"><?php echo $this->uppercase; ?></h3>
                                        </div>
                                        <div class="description">Deals Listing</div>
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
                                                        <th>Deal</th>
                                                        <th>Restaurant</th>
                                                        <th>Restaurant Address</th>
                                                        <th>Deal Code</th>
                                                        <th>Image</th>
                                                        <th>Start On</th>
                                                        <th>Expiry On</th>
                                                        <th>Like</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Deal</th>
                                                        <th>Restaurant</th>
                                                        <th>Restaurant Address</th>
                                                        <th>Deal Code</th>
                                                        <th>Image</th>
                                                        <th>Start On</th>
                                                        <th>Expiry On</th>
                                                        <th>Like</th>
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
                                            <h4 class="modal-title">Deal Record</h4>
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
            var oTable, controller = '<?php echo $this->controller; ?>', imagepath = '<?php echo DEAL_IMAGE_PATH ?>', no_img_url = '<?php echo IMGURL; ?>/admin/img/no-image.png';
            var url;
            url = controller + '/paginate';
            $(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                $(".fancybox").fancybox();
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');
                var btn_show_hide = parseInt('<?= $ADMINTYPE == 1 ? 1 : 0; ?>');

                var target = [
                    {
                        "aTargets": [8], // Column to target
                        "mRender": function (data, type, full) {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = '';
                            if (permission.indexOf('2') >= 0) {
                                buttons += ' <a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + full['iDealID'] + '/y"  class="btn btn-sm btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a>';
                            }
                            if (permission.indexOf('3') >= 0) {
                                buttons += '<button title="Delete" class="btn btn-danger btn-sm   marginright10 margintop10"  onclick="return validateRemove(' + data + ',' + "'" + controller + "/deleteAll'" + ');"><i class="fa fa-times"></i> Delete</button>';
                            }
                            if (permission.indexOf('4') >= 0) {
                                if (full['eStatus'] === "Active") {
                                    buttons += '<a title="Click here to inactive" id="atag' + full['iDealID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iDealID'] + "/y'" + ')"  class="btn btn-sm btn-success  marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Active </a>'
                                } else {
                                    buttons += '<a title="Click here to Active" id="atag' + full['iDealID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iDealID'] + "/y'" + ')"  class="btn btn-sm btn-inverse  marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Inactive </a>'
                                }
                            }

                            return buttons;
                        }
                    },
                    {
                        "aTargets": [7], // Column to target
                        "mRender": function (data, type, full) {
                            var buttons = ' <a title="View" href="<?= BASEURL ?>' + controller + '/likes/' + full['iDealID'] + '" class="btn btn-sm btn-primary marginright10 margintop10 "><i class="fa fa-eye"></i> View Likes (' + full['total_like'] + ') </a>';
                            buttons += '<a href="#table-modal" data-toggle="modal" class="btn btn-sm btn-primary marginright10 margintop10 btn-view-detail" data-id="' + full['iDealID'] + '">View Detail</a>';

                            return buttons;
                        }
                    },
                    {
                        "aTargets": [4], // Column to target
                        "mRender": function (data, type, full)
                        {
                            if (full['vDealImage'] != '') {
                                return '<a class="fancybox" rel="group" href="' + imagepath + full['iDealID'] + '/' + full['vDealImage'] + '"><img class="thumbnail img-responsive" src="' + imagepath + full['iDealID'] + '/thumb/' + full['vDealImage'] + '"  height="70" width="90" /></a>';
                            } else {
                                return '<img class="thumbnail img-responsive" src="' + no_img_url + '"  height="70" width="90" />';
                            }
                        }
                    }
                ];
                var aoculumn = [
                    /*0*/ {"mData": "vOfferText", "sWidth": "35%"},
                    /*1*/ {"mData": "vRestaurantName", "sWidth": "25%"},
                    /*1*/ {"mData": "tAddress", "sWidth": "25%"},
                    /*2*/ {"mData": "vDealCode", "sWidth": "25%"},
                    /*2*/ {"mData": "vDealImage", "sWidth": "25%"},
                    /*2*/ {"mData": "dtStartDate", "sWidth": "25%"},
                    /*3*/ {"mData": "dtExpiryDate", "sWidth": "25%"},
                    /*4*/ {"mData": "total_like", "sWidth": "10%"},
                    /*5*/ {"mData": "iDealID", bSortable: false, bSearchable: false, "sWidth": "10%"}
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
                $('#table-modal').on('hidden.bs.modal', function () {
                    //alert();
                    $('#pickup-asset-collection').html('<div class="text-center"><img src="<?= BASEURL; ?>img/ajax-loader.gif" title="Please Wait..." alt="Please wait..."/></div>');
                });

            });

        </script>

    </body>