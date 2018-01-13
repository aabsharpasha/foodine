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
                                        <div class="description">Combo Banner Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i>Combo <?php echo $this->uppercase; ?> List</h4>
                                            <?php echo anchor('banner/add/combo', '<h5><i class="fa fa-plus-circle">&nbsp;Add Banner</i></h5>', 'id="fa-plus-circle"  class="colorfff pull-right"'); ?>
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
                                                        <th>Label</th>
                                                        <th>Text</th>
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                        <th>Combo</th>
                                                        <th>Created On</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Label</th>
                                                        <th>Text</th>
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                        <th>Combo</th>
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
            url = controller + '/paginate/combo';
            $(document).ready(function () {
                $(".fancybox").fancybox();
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');

                var target = [
                    {
                        "aTargets": [6], // Column to target
                        "mRender": function (data, type, full) {
                            
                            var buttons = '<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/combo/' + data + '"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\ <button title="Delete" class="btn btn-sm btn-danger  marginright10 margintop10"  onclick="return validateRemove(' + data + ',' + "'" + controller + "/deleteAll'" + ');"><i class="fa fa-times"></i> Delete</button>';
                            if (full['eStatus'] == "Active") {
                                buttons += '<a title="Click here to inactive" id="atag' + full['iBannerId'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iBannerId'] + "/y'" + ')"  class="btn btn-sm btn-success marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Active </a>';
                            } else {
                                buttons += '<a title="Click here to Active" id="atag' + full['iBannerId'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iBannerId'] + "/y'" + ')"  class="btn btn-sm btn-inverse marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Inactive </a>';
                            }
                            return buttons;
                        }
                    },
                    {
                        "aTargets": [5], // Column to target
                        "mRender": function (data, type, full) {
                            return full['dCreatedAt'];
                        }
                    }
                ];
                var aoculumn = [
                {"mData": "vLabel", "sWidth": "10%"},
                {"mData": "tText", "sWidth": "10%"},
                {"mData": "tStartDate", "sWidth": "10%"},
                {"mData": "tEndDate", "sWidth": "10%"},
                {"mData": "bannerName", "sWidth": "10%"},
                {"mData": "dCreatedAt", "sWidth": "10%"},
                    /*3*/ {"mData": "iBannerId", bSortable: false, bSearchable: false, "sWidth": "10%"}
                ];
                getdatatable(controller + '/deleteAll', url, aoculumn, target, 0, 'desc');


            });

        </script>

    </body>