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
                                            <h3 class="content-title pull-left"><?php echo $this->uppercase; ?></h3>
                                        </div>
                                        <div class="description">Voucher Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
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
                                                        <th>Voucher Title</th>
                                                        <th>Voucher Code</th>
                                                        <th>Valid From</th>
                                                        <th>Valid Upto</th>
                                                        <th>Value</th>
                                                        <th>Value Type</th>
                                                        <th>Description</th>
                                                        <th>Min. Order Value</th>
                                                        <th>User Specific</th>
                                                        <th>One Time Usable</th>
                                                        <th>Public</th>
                                                        <th>Voucher Use Count</th>
                                                        <th>Created On</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Voucher Title</th>
                                                        <th>Voucher Code</th>
                                                        <th>Valid From</th>
                                                        <th>Valid Upto</th>
                                                        <th>Value</th>
                                                        <th>Value Type</th>
                                                        <th>Description</th>
                                                        <th>Min. Order Value</th>
                                                        <th>User Specific</th>
                                                        <th>One Time Usable</th>
                                                        <th>Public</th>
                                                        <th>Voucher Use Count</th>
                                                        <th>Created On</th>
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
            var oTable, controller = '<?php echo $this->controller; ?>';
            var url;
            url = controller + '/paginate';
            $(document).ready(function () {
                $(".fancybox").fancybox();
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');

                var target = [
                    {
                        "aTargets": [13], // Column to target
                        "mRender": function (data, type, full) {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = ' <a title="Edit" href="<?= BASEURL; ?>' + controller + '/add/' + full['iVoucherID'] + '/y"  class="btn btn-sm btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a> \n <button title="Delete" class="btn btn-sm btn-danger  marginright10 margintop10"  onclick="return validateRemove(' + data + ',' + "'" + controller + "/deleteAll'" + ');"><i class="fa fa-times"></i> Delete</button>';
                            if (full['eStatus'] == "Active") {
                                buttons += '<a title="Click here to inactive" id="atag' + full['iVoucherID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iVoucherID'] + "/y'" + ')"  class="btn btn-sm btn-success marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Active </a>';
                            } else {
                                buttons += '<a title="Click here to Active" id="atag' + full['iVoucherID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iVoucherID'] + "/y'" + ')"  class="btn btn-sm btn-inverse marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Inactive </a>';
                            }
                            return buttons;
                        }
                    },
                    {
                        "aTargets": [12], // Column to target
                        "mRender": function (data, type, full) {
                            return full['tCreatedAt'];
                        }
                    }
                ];
                var aoculumn = [
                {"mData": "vTitle", "sWidth": "10%"},
                {"mData": "vCode", "sWidth": "10%"},
                {"mData": "tStartDate", "sWidth": "10%"},
                {"mData": "tEndDate", "sWidth": "10%"},
                {"mData": "dValue", "sWidth": "10%"},
                {"mData": "eValueType", "sWidth": "10%"},
                {"mData": "vDescription", "sWidth": "10%"},
                {"mData": "dMinOrderValue", "sWidth": "10%"},
                {"mData": "eUserSpecific", "sWidth": "10%"},
                {"mData": "eOneTimeUsable", "sWidth": "10%"},
                {"mData": "ePublic", "sWidth": "10%"},
                {"mData": "useCount", "sWidth": "10%"},
                {"mData": "tCreatedAt", "sWidth": "10%"},
                    /*3*/ {"mData": "iVoucherID", bSortable: false, bSearchable: false, "sWidth": "15%"}
                ];
                getdatatable(controller + '/deleteAll', url, aoculumn, target, 0, 'desc');


            });

        </script>

    </body>