<?php
$headerData = $this->headerlib->data();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 31);
?><!doctype html>
<html lang="en-us">
    <head>
        <title><?= $title ?></title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets']; ?>
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
                                            <h3 class="content-title pull-left"><?php echo $this->uppercase; ?></h3>
                                        </div>
                                        <div class="description">Restaurant Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i><?php echo $this->uppercase; ?> list</h4>
                                            <div class="tools ">
                                                <a id="fa-refresh" href="javascript:;" class="reload">
                                                    <i class="fa fa-refresh"></i>
                                                </a>
                                                <a href="javascript:;" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                                <!-- <a href="javascript:;" class="remove">
                                                  <i class="fa fa-times"></i>
                                                </a> -->
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table  table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th>Restaurant Name</th>
                                                        <th>Email</th>
                                                        <th>Contact</th>
                                                        <th>Address</th>
                                                        <th>Restaurant Banner</th>
                                                        <th>Check In / Rate</th>
                                                        <th>Photos</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Contact</th>
                                                        <th>Address</th>
                                                        <th>Restaurant Banner</th>
                                                        <th>Check In / Rate</th>
                                                        <th>Photos</th>
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
                                            <h4 class="modal-title">Restaurant Record</h4>
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

        <script>


            var oTable, controller = '<?php echo $this->controller; ?>', imagepath = '<?php echo RESTAURANT_IMAGE_PATH ?>', no_img_url = '<?php echo IMGURL; ?>/admin/img/no-image.png';
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

                var target = [{
                        "aTargets": [7], // Column to target
                        "mRender": function (data, type, full)
                        {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = '';
                            if (permission.indexOf('2') >= 0) {
                                buttons += ' <a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-sm btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a>';
                            }
                            if (permission.indexOf('3') >= 0) {
                                buttons += '<button title="Delete" class="btn btn-sm btn-danger marginright10 margintop10"  onclick="return validateRemove(' + data + ',' + "'" + controller + "/deleteAll'" + ');"><i class="fa fa-times"></i> Delete</button>';
                            }
                            if (permission.indexOf('4') >= 0) {
                                if (full['eStatus'] == "Active") {
                                    buttons += '<a title="Click here to inactive" id="atag' + full['iRestaurantID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iRestaurantID'] + "/y'" + ')"  class="btn btn-sm btn-success marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Active </a>'
                                } else {
                                    buttons += '<a title="Click here to Active" id="atag' + full['iRestaurantID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iRestaurantID'] + "/y'" + ')"  class="btn btn-sm btn-inverse marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Inactive </a>'
                                }
                            }


                            buttons += '<a href="<?= BASEURL . $this->controller; ?>/report/' + full['iRestaurantID'] + '" title="Report" class="btn btn-sm btn-warning marginright10 margintop10" ><i class="fa fa-download"></i> Download Report</button>';
                            buttons += '<a href="javascript:void(0)" class="btn btn-sm btn-danger marginright10 margintop10 resend-mail" data-id="' + full['iRestaurantID'] + '">Resend Email</a>';
                            return buttons;
                        }
                    },
                    {
                        "aTargets": [6], // Column to target
                        "mRender": function (data, type, full)
                        {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = '';
                            if (full['total_picture'] == "0") {
                                buttons = ' <a title="View Photos" href="<?= BASEURL ?>image/restaurant_image/' + full['iRestaurantID'] + '/y"  class="btn btn-sm btn-primary marginright10 margintop10 btn-xs  "><i class="fa fa-pencil-square-o"></i> View Photos ( ' + full['total_picture'] + ' ) </a>';
                            } else {
                                buttons = ' <a title="View Photos" href="<?= BASEURL ?>image/restaurant_image/' + full['iRestaurantID'] + '/y"  class="btn btn-sm btn-primary marginright10 margintop10 btn-xs "><i class="fa fa-pencil-square-o"></i>  View Photos ( ' + full['total_picture'] + ' ) </a>';
                            }

                            if (full['total_menu'] == "0") {
                                buttons += ' <a title="View Menu" href="<?= BASEURL ?>image/restaurant_menu/' + full['iRestaurantID'] + '/y"  class="btn btn-sm btn-primary marginright10 margintop10 btn-xs "><i class="fa fa-pencil-square-o"></i> View Menu ( ' + full['total_menu'] + ' ) </a>';
                            } else {
                                buttons += ' <a title="View Menu" href="<?= BASEURL ?>image/restaurant_menu/' + full['iRestaurantID'] + '/y"  class="btn btn-sm btn-primary marginright10 margintop10 btn-xs  "><i class="fa fa-pencil-square-o"></i>  View Menu ( ' + full['total_menu'] + ' ) </a>';
                            }

                            buttons += '<a href="#table-modal" data-toggle="modal" class="btn btn-sm btn-primary marginright10 margintop10 btn-view-detail btn-xs " data-id="' + full['iRestaurantID'] + '">View Detail</a>';
                            //buttons += '<span class="btn btn-sm btn-success marginright10 margintop10 btn-xs " >Like (' + full['total_like'] + ')</span>';
                           // buttons += '<span class="btn btn-sm btn-danger marginright10 margintop10 btn-xs " >Dislike (' + full['total_dislike'] + ')</span>';

                            return buttons;
                        }
                    },
                    {
                        "aTargets": [5], // Column to target
                        "mRender": function (data, type, full)
                        {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = '';
                            if (full['total_checkin'] == "0") {
                                buttons = ' <a title="View Check-In Users" href="<?= BASEURL ?>restaurant/checkin/' + full['iRestaurantID'] + '/y"  class="btn btn-sm btn-primary btn-xs marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> View Check-In ( ' + full['total_checkin'] + ' ) </a>';
                            } else {
                                buttons = ' <a title="View Check-In Users" href="<?= BASEURL ?>restaurant/checkin/' + full['iRestaurantID'] + '/y"  class="btn btn-sm btn-primary btn-xs marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i>  View Check-In ( ' + full['total_checkin'] + ' ) </a>';
                            }
                            buttons += ' <a title="View Ratting" href="<?= BASEURL ?>restaurant/ratting/' + full['iRestaurantID'] + '/y"  class="btn btn-sm btn-primary btn-xs marginright10 margintop10 "><i class="fa fa-star"></i>  View Rate ( ' + (full['total_rate'] != null ? full['total_rate'] : 0.00) + ' ) </a>';
                            buttons += ' <a title="View Ratting" href="<?= BASEURL ?>restaurant/review/' + full['iRestaurantID'] + '/y"  class="btn btn-sm btn-primary btn-xs marginright10 margintop10 "><i class="fa fa-eye"></i>  View Review ( ' + (full['total_review'] != null ? full['total_review'] : 0) + ' ) </a>';
                            //buttons += ' <a title="Book Table" href="<?= BASEURL ?>book/add/' + full['iRestaurantID'] + '/y"  class="btn btn-sm btn-danger btn-xs marginright10 margintop10 "><i class="fa fa-book"></i>  Book Table </a>';

                            return buttons;
                        }
                    },
                    {
                        "aTargets": [3], // Column to target
                        "mRender": function (data, type, full)
                        {
                            return full['tAddress'] + '<br>' + full['vCityName'] + '<br>' + full['vStateName'] + '<br>' + full['vCityName'];
                        }
                    },
                    {
                        "aTargets": [4], // Column to target
                        "mRender": function (data, type, full)
                        {
                            if (full['vRestaurantLogo'] != '') {
                                return '<a class="fancybox" rel="group" href="' + imagepath + full['iRestaurantID'] + '/' + full['vRestaurantLogo'] + '"><img class="thumbnail img-responsive" src="' + imagepath + full['iRestaurantID'] + '/thumb/' + full['vRestaurantLogo'] + '"  height="70" width="90" /></a>';
                            } else {
                                return '<img class="thumbnail img-responsive" src="' + no_img_url + '"  height="70" width="90" />';
                            }
                        }
                    },
                ];
                var aoculumn = [
                    /*0*/ {"mData": "vRestaurantName", "sWidth": "20%"},
                    /*1*/ {"mData": "vEmail", "sWidth": "20%"},
                    /*2*/ {"mData": "vContactNo", "sWidth": "10%"},
                    /*3*/ {"mData": "tAddress", "sWidth": "25%"},
                    /*4*/ {"mData": "vRestaurantLogo", bSortable: false, bSearchable: false, "sWidth": "15%"},
                    /*4*/ {"mData": "iRestaurantID", bSortable: false, bSearchable: false, "sWidth": "15%"},
                    /*5*/ {"mData": "iRestaurantID", bSortable: false, bSearchable: false, "sWidth": "10%"},
                    /*6*/ {"mData": "iRestaurantID", bSortable: false, bSearchable: false, "sWidth": "10%"}
                ];
                var delete_val = btn_show_hide == 1 ? controller + '/deleteAll' : '';
                getdatatable(delete_val, controller + '/paginate', aoculumn, target);
                $('.resend-mail').live('click', function () {
                    var $btn = $(this);
                    var target_id = $btn.data('id');

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        beforeSend: function () {
                            $btn.attr('disabled', true);
                        },
                        data: {
                            target_id: target_id
                        },
                        url: BASEURL + controller + '/resendMail',
                        success: function (resp) {
                            if (resp.STATUS == 200) {
                                alert(resp.MSG);
                                $btn.attr('disabled', false);
                            }
                        }
                    });
                });

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