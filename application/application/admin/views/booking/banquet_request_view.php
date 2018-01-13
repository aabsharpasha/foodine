<?php
$headerData = $this->headerlib->data();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 68);
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
                                            <h3 class="content-title pull-left">Banquet enquiry</h3>
                                        </div>
                                        <div class="description">Banquet enquiry</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i>Banquet enquiry List</h4>
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
                                                        <th>Mobile</th>
                                                        <th>Email</th>
                                                        <th>Date Time</th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Customer Name</th>
                                                        <th>Mobile</th>
                                                        <th>Email</th>
                                                        <th>Date Time</th>
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
                                            <h4 class="modal-title">Banquet Enquiry Detail</h4>
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
        <script src="<?= JS_URL; ?>js/bootbox/bootbox.min.js"></script>
        <script>
            var oTable, controller = '<?php echo $this->controller; ?>';
            var url;
            url = controller + '/paginate_banquetEnquiry';
                function changePartyStatus(status,id) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        data: {},
                        url: BASEURL + controller + '/changePartyBookingStatus/' + status + "/" +id,
                        success: function (resp) {
                            if(resp !==''){
                                $("#statusSpan"+id).html(resp);
                                var html  = "";
                                if(resp=="Pending"){
                                    html += '<a href="javascript:changePartyStatus(\'In Process\','+id+');" class="btn btn-sm btn-primary marginright10 margintop10 btn-view-detail btn-xs " data-id="' + id + '">In Process</a>';
                                    html += '<a href="javascript:changePartyStatus(\'Addressed\','+id+');" class="btn btn-sm btn-primary marginright10 margintop10 btn-view-detail btn-xs " data-id="' + id + '">Addressed</a>';
                                }else if(resp=="In Process"){
                                    html += '<a href="javascript:changePartyStatus(\'Pending\','+id+');" class="btn btn-sm btn-primary marginright10 margintop10 btn-view-detail btn-xs " data-id="' + id + '">Pending</a>';
                                    html += '<a href="javascript:changePartyStatus(\'Addressed\','+id+');" class="btn btn-sm btn-primary marginright10 margintop10 btn-view-detail btn-xs " data-id="' + id + '">Addressed</a>';
                                }
                                $("#statusButtonSpan"+id).html(html);
                            }
                        }
                    });
                };
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
                            var buttons  = '';
                            var detailButton    = '<a href="#table-modal" data-toggle="modal" class="btn btn-sm btn-primary marginright10 margintop10 btn-view-detail btn-xs " data-id="' + full['iBookPartyID'] + '">View Detail</a>';
                            // if(full['eStatus']=="Pending"){
                            //     buttons += '<a href="javascript:changePartyStatus(\'In Process\','+full['iBookPartyID']+');" class="btn btn-sm btn-primary marginright10 margintop10 btn-xs " data-id="' + full['iBookPartyID'] + '">In Process</a>';
                            //     buttons += '<a href="javascript:changePartyStatus(\'Addressed\','+full['iBookPartyID']+');" class="btn btn-sm btn-primary marginright10 margintop10 btn-xs " data-id="' + full['iBookPartyID'] + '">Addressed</a>';
                            // }else if(full['eStatus']=="In Process"){
                            //     buttons += '<a href="javascript:changePartyStatus(\'Pending\','+full['iBookPartyID']+');" class="btn btn-sm btn-primary marginright10 margintop10 btn-xs " data-id="' + full['iBookPartyID'] + '">Pending</a>';
                            //     buttons += '<a href="javascript:changePartyStatus(\'Addressed\','+full['iBookPartyID']+');" class="btn btn-sm btn-primary marginright10 margintop10 btn-xs " data-id="' + full['iBookPartyID'] + '">Addressed</a>';
                            // }
                            buttons  = detailButton + "<span id='statusButtonSpan"+full['iBookPartyID']+"'>"+buttons+"</span>";
                            return buttons;
                        }
                    },
                    {
                        "aTargets": [13], // Column to target
                        "mRender": function (data, type, full) {
                            var buttons  = "<span id='statusSpan"+full['iBookPartyID']+"'>"+full['eStatus']+"</span>";
                            return buttons;
                        }
                    }
                ];
                var aoculumn = [
                    /*0*/ {"mData": "vCustomerName", "sWidth": "20%"},
                    /*0*/ {"mData": "mobile", "sWidth": "20%"},
                    /*1*/ {"mData": "email", "sWidth": "20%"},
                    /*2*/ {"mData": "tCreatedAt", "sWidth": "20%"},
                ];
                var delete_val = btn_show_hide == 1 ? controller + '/deleteAllPartyBooking' : '';
                target = {};
                getdatatable(delete_val, url, aoculumn, target, 3, 'desc');
                $('.btn-view-detail').live('click', function () {
                    var $btn = $(this);
                    var target_id = $btn.data('id');
                    //alert(asset_id);

                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        data: {},
                        url: BASEURL + controller + '/viewPartyDetail/' + target_id,
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
