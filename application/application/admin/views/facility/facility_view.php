<?php
$headerData = $this->headerlib->data();

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 24);
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
                                        <div class="description">Facilities Listing</div>
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
                                                        <th>Facility</th>
                                                        <th>View Restaurants</th>
                                                        <th>Created On</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Facility</th>
                                                        <th>View Restaurants</th>
                                                        <th>Created On</th>
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
                                            <h4 class="modal-title">Restaurant(s)</h4>
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
        <script>
            var oTable, controller = '<?php echo $this->controller; ?>';
            var url;
            url = controller + '/paginate';
            $(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                App.setPage("dynamic_table"); //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');

                var target = [
                    {
                        "aTargets": [3], // Column to target
                        "mRender": function (data, type, full) {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = '';
                            if (permission.indexOf('2') >= 0) {
                                buttons += ' <a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + full['iFacilityID'] + '/y"  class="btn btn-sm btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a> ';
                            }
                            if (permission.indexOf('3') >= 0) {
                                buttons += ' \n <button title="Delete" class="btn btn-sm btn-danger  marginright10 margintop10"  onclick="return validateRemove(' + data + ',' + "'" + controller + "/deleteAll'" + ');"><i class="fa fa-times"></i> Delete</button>';
                            }
                            if (permission.indexOf('4') >= 0) {
                                if (full['eStatus'] == "Active") {
                                    buttons += '<a title="Click here to inactive" id="atag' + full['iFacilityID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iFacilityID'] + "/y'" + ')"  class="btn btn-sm btn-success  marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Active </a>'
                                } else {
                                    buttons += '<a title="Click here to Active" id="atag' + full['iFacilityID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iFacilityID'] + "/y'" + ')"  class="btn btn-sm btn-inverse  marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Inactive </a>'
                                }
                            }
                            return buttons;
                        }
                    },
                    {
                        "aTargets": [1], // Column to target
                        "mRender": function (data, type, full) {
                            if (full['total_restaurants'] == "0") {
                                var buttons = ' <a title="View Restaurants" href="javascript:void(0)"  class="btn btn-sm btn-primary  marginright10 margintop10"><i class="fa fa-pencil-square-o"></i> View Restaurants ( ' + full['total_restaurants'] + ' ) </a>';
                            } else {
                                var buttons = ' <a title="View Restaurants" href="#table-modal" data-toggle="modal" class="btn btn-sm btn-primary marginright10 margintop10 btn-view-detail" data-id="' + full['iFacilityID'] + '"><i class="fa fa-pencil-square-o"></i> View Restaurants ( ' + full['total_restaurants'] + ' ) </a>';
//                                var buttons = ' <a title="View Restaurants" href="javascript:void(0)" class="btn btn-sm btn-primary marginright10 margintop10 " ><i class="fa fa-pencil-square-o"></i> View Restaurants ( ' + full['total_restaurants'] + ' ) </a>';
                            }
                            return buttons;
                        }
                    },
                    {
                        "aTargets": [0], // Column to target
                        "mRender": function (data, type, full) {
                            return full['vFacilityName'];
                        }
                    },
                    {
                        "aTargets": [2], // Column to target
                        "mRender": function (data, type, full) {
                            return full['tCreatedAt'];
                        }
                    }
                ];
                var aoculumn = [
                    /*0*/ {"mData": "vFacilityName", "sWidth": "25%"},
                    /*1*/ {"mData": "total_restaurants", "sWidth": "20%", bSortable: false, bSearchable: false},
                    /*2*/ {"mData": "tCreatedAt1", "sWidth": "25%"},
                    /*3*/ {"mData": "iFacilityID", bSortable: false, bSearchable: false, "sWidth": "25%"}
                ];
                getdatatable(controller + '/deleteAll', url, aoculumn, target, 2, 'desc');
            });
            $('.btn-view-detail').live('click', function() {
                var $btn = $(this);
                var target_id = $btn.data('id');

                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    data: {},
                    url: BASEURL + controller + '/viewRestaurantDetail/' + target_id,
                    success: function(resp) {
                        $('#pickup-asset-collection').html(resp);
                    }
                });
            });

        </script>

    </body>