<?php
$headerData = $this->headerlib->data();

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 19);
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
                                        <div class="description">Jobs Listing</div>
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
                                                        <th>Job Title</th>
                                                        <th>Job Location</th>
                                                        <th>Exp Required</th>
                                                        <th>Created On</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                      <th>Job Title</th>
                                                        <th>Job Location</th>
                                                        <th>Exp Required</th>
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


        <script>
            var oTable, controller = '<?php echo $this->controller; ?>', imagepath = '<?php echo TEAM_IMAGE_PATH; ?>', no_img_url = '<?php echo DOMAIN_URL; ?>/admin/img/no-image.png';
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

                var target = [
                    {
                        "aTargets": [4], // Column to target
                        "mRender": function (data, type, full) {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = '';
                            if (permission.indexOf('2') >= 0) {
                                buttons += ' <a title="Edit" href="<?= BASEURL ?>' + controller + '/addJobs/' + full['iJobDetailID'] + '/y"  class="btn btn-sm btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a> ';
                            }
                            if (permission.indexOf('3') >= 0) {
                                buttons += '\n <button title="Delete" class="btn btn-sm btn-danger marginright10 margintop10"  onclick="return validateRemove(' + full['iJobDetailID'] + ',' + "'" + controller + "/deleteAll'" + ');"><i class="fa fa-times"></i> Delete</button>';
                            }
                            if (permission.indexOf('4') >= 0) {
                                if (full['eStatus'] == "Active") {
                                    buttons += '<a title="Click here to inactive" id="atag' + full['iJobDetailID'] + '" onclick="return changeStatus(' + full['iJobDetailID'] + ',' + "'" + controller + "/status/" + full['iJobDetailID'] + "/y'" + ')"  class="btn btn-sm btn-success  marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Active </a>'
                                } else {
                                    buttons += '<a title="Click here to Active" id="atag' + full['iJobDetailID'] + '" onclick="return changeStatus(' + full['iJobDetailID'] + ',' + "'" + controller + "/status/" + full['iJobDetailID'] + "/y'" + ')"  class="btn btn-sm btn-inverse  marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Inactive </a>'
                                }
                            }
                            return buttons;
                        }
                    },
                    {
                        "aTargets": [2], // Column to target
                        "mRender": function (data, type, full) {
                            
                            return full['iMinExp'];
                        }
                    },
                    {
                        "aTargets": [0], // Column to target
                        "mRender": function (data, type, full) {
                            return full['vJobTitle'];
                        }
                    },
                    {
                        "aTargets": [3], // Column to target
                        "mRender": function (data, type, full) {
                            return full['tCreatedAt'];
                        }
                    },
                    {
                        "aTargets": [1], // Column to target
                        "mRender": function (data, type, full) {
                         return full['vZoneName'];
                        }
                    }
                ];
                var aoculumn = [
                    /*0*/ {"mData": "vJobTitle", "sWidth": "20%"},
                    /*1*/ {"mData": "vJobLocation", "sWidth": "20%", bSortable: false},
                    /*2*/ {"mData": "iMinExp", "sWidth": "20%", bSortable: false, bSearchable: false},
                    /*3*/ {"mData": "tCreatedAt", bSortable: false, bSearchable: false, "sWidth": "25%"},
                    /*3*/ {"mData": "iJobDetailID", bSortable: false, bSearchable: false, "sWidth": "25%"}
                ];
                
               

                getdatatable(controller + '/deleteAll', url, aoculumn, target, 0, 'desc');
            });
        </script>

    </body>