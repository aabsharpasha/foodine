<?php
$headerData = $this->headerlib->data();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 33);
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
                                        <div class="description">Restaurant Booking Listing</div>
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
                                                        <th>Booking ID</th>
                                                        <th>Restaurant</th>
                                                        <th>Location Name</th>
                                                        <th>Restaurant Address</th>
                                                        <!--<th>Restaurant Contact</th>-->
                                                        <th>User</th>
                                                        <th>User Contact</th>
                                                        <th>Guest</th>
                                                        <th>Booking On</th>
                                                        <th>Booking Created On</th>
                                                        <th>Selected Offer</th>
                                                        <th>Occasion</th>
                                                        <th>Platform</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Booking ID</th>
                                                        <th>Restaurant</th>
                                                        <th>Location Name</th>
                                                        <th>Restaurant Address</th>
                                                        <!--<th>Restaurant Contact</th>-->
                                                        <th>User</th>
                                                        <th>User Contact</th>
                                                        <th>Guest</th>
                                                        <th>Booking On</th>
                                                        <th>Booking Created On</th>
                                                        <th>Selected Offer</th>
                                                        <th>Occasion</th>
                                                        <th>Platform</th>
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
        <script src="<?= JS_URL; ?>js/bootbox/bootbox.min.js"></script>
        <script>
            var oTable, controller = '<?php echo $this->controller; ?>';
            var url;
            url = controller + '/paginate';
                function changeVisitedStatus(adrs,element){
                    $.ajax({
                        url: BASEURL + adrs,
                        type: 'POST',
                        data: '',
                        success: function (output_string) {
                            if (output_string === '1') {
                                if ($("#" + element).hasClass('btn-success')) {
                                    $("#" + element).html('<i class="fa fa-times-circle-o "></i> Not Visited ');
                                    $("#" + element).switchClass('btn-success', 'btn-inverse');
                                } else if ($("#" + element).hasClass('btn-inverse')) {
                                    $("#" + element).html('<i class="fa fa-check-circle-o "></i> Visited ');
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
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');
                //$('#datatable').DataTable();

                var btn_show_hide = parseInt('<?= ($ADMINTYPE == 1 || $ADMINTYPE == 2) ? 1 : 0; ?>');

                var target = [
                    {
                        "aTargets": [12], // Column to target 
                        "mRender": function (data, type, full) {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = '';

                            if (full['eBookingStatus'] === "Pending") {
                                if (btn_show_hide) {
                                    buttons += '<button title="Delete" class="btn btn-danger btn-xs marginright10 margintop10"  onclick="return validateRemove(' + data + ',' + "'" + controller + "/deleteAll'" + ');"><i class="fa fa-times"></i> Delete</button>';
                                }
                                buttons += '<a title="Click here to accept" id="atag' + full['iBookingID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iBookingID'] + "/y'" + ', 1)"  class="btn btn-warning  btn-xs  marginright10 margintop10"><i class="fa fa-check-circle"></i> Accept </a>'
                                buttons += '<a title="Click here to set wait time" id="wtag' + full['iBookingID'] + '" onclick="return updateStausWithTime(' + data + ',' + "'" + controller + "/waitstatus/" + full['iBookingID'] + "/y'" + ', 1)" class="btn btn-inverse  btn-xs  marginright10 margintop10"><i class="fa fa-clock-o"></i> Time</a>';
                            } else if (full['eBookingStatus'] === "Accept") {
                                buttons += '<a title="Click here to reject" id="atag' + full['iBookingID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iBookingID'] + "/y'" + ', 1)"  class="btn btn-success  btn-xs  marginright10 margintop10"><i class="fa fa-check-circle"></i> Accepted </a>';
                                buttons += '<br><strong>Accepted</strong><br>';
//                                if (full['eVisited'] === "Yes") {
//                                    buttons += '<a title="Click to set Not Visited" id="visit' + full['iBookingID'] + '"  onclick="return changeVisitedStatus(' + "'" + controller + "/visitedStatus/" + full['iBookingID'] + "/y'" + ', \'visit' + full['iBookingID'] + '\')"  class="btn btn-success  btn-xs  marginright10 margintop10"><i class="fa fa-check-circle"></i> Visited </a>';
//                                }else{
//                                    buttons += '<a title="Click to set Visited" id="visit' + full['iBookingID'] + '"  onclick="return changeVisitedStatus(' + "'" + controller + "/visitedStatus/" + full['iBookingID'] + "/y'" + ', \'visit' + full['iBookingID'] + '\')"  class="btn btn-inverse  btn-xs  marginright10 margintop10"><i class="fa fa-check-circle"></i> Not Visited </a>';
//                                }
                            } else if (full['eBookingStatus'] === "Reject") {
                                //|| full['eBookingStatus'] === "Waiting"
                                //buttons += '<a title="Click here to Active" id="atag' + full['iBookingID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iBookingID'] + "/y'" + ', 1)"  class="btn btn-inverse  btn-xs  marginright10 margintop10"><i class="fa fa-crosshairs"></i> Rejected </a>'
                                buttons += '<strong>Rejected</strong>';
                            } else if (full['eBookingStatus'] === "Cancel") {
                                //|| full['eBookingStatus'] === "Waiting"
                                //buttons += '<a title="Click here to Active" id="atag' + full['iBookingID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iBookingID'] + "/y'" + ', 1)"  class="btn btn-inverse  btn-xs  marginright10 margintop10"><i class="fa fa-crosshairs"></i> Rejected </a>'
                                buttons += '<strong>Cancelled</strong>';
                            } else if (full['eBookingStatus'] === "Waiting") {
                                //buttons += '<button class="btn btn-inverse btn-xs marginright10 margintop10 btn-disabled" disabled=""><i class="fa fa-clock-o"></i> ' + full['iWaitTime'] + ' min to wait</button>';
                                buttons += '<strong>' + full['iWaitTime'] + ' min to wait</strong>';
                            }
                            return buttons;
                        }
                    },
                    {
                        "aTargets": [7], // Column to target
                        "mRender": function (data, type, full) {
                            return full['dtBookingDate_sort'];
                        }
                    }
                ];
                var aoculumn = [
                    /*0*/ {"mData": "BookingID", "sWidth": "10%"},
                    /*0*/ {"mData": "vRestaurantName", "sWidth": "20%"},
                    /*1*/ {"mData": "location_name", "sWidth": "20%"},
                    /*2*/ {"mData": "address_line", "sWidth": "20%"},
//                    /*5*/ {"mData": "restaurantContact", "sWidth": "15%"},
                    /*3*/ {"mData": "vFullName", "sWidth": "15%"},
                    /*4*/ {"mData": "userContact", "sWidth": "15%"},
                    /*6*/ {"mData": "total_person", "sWidth": "15%"},
                    /*7*/ {"mData": "dtBookingDate_sort", "sWidth": "10%"},
                    /*11*/ {"mData": "bookingCaptureDate", "sWidth": "10%"},
                    /*8*/ {"mData": "offer_name", "sWidth": "10%"},
                    /*9*/ {"mData": "occasion", "sWidth": "10%"},
                     /*9*/ {"mData": "platform", "sWidth": "10%"},
                    /*10*/ {"mData": "iBookingID", bSortable: false, bSearchable: false, "sWidth": "15%"}
                ];
                var delete_val = btn_show_hide == 1 ? controller + '/deleteAll' : '';
                getdatatable(delete_val, url, aoculumn, target, 2, 'desc');

                $('#ToolTables_datatable_6').on('click', function () {
                    $('#datatable tbody').children().each(function () {
                        var $tr = $(this);
                        if (!$tr.hasClass('row_selected')) {
                            $tr.addClass('row_selected');
                        }
                    });
                });
                $('#ToolTables_datatable_7').on('click', function () {
                    $('#datatable tbody').children().each(function () {
                        var $tr = $(this);
                        if ($tr.hasClass('row_selected')) {
                            $tr.removeClass('row_selected');
                        }
                    });
                });
                

            });

        </script>

    </body>