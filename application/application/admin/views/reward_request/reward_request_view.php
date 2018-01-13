<?php
$headerData = $this->headerlib->data();

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 45);
?>
<!doctype html>
<html lang="en-us">
    <head>
        <title><?= $title ?></title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets']; ?>

    </head>

    <style type="text/css">
        .modal-body { display:inline; }.delete_comment { position: absolute; right:0px; top:5px; }
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
                                        <div class="description">Reward Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages(); ?>
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
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table  table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th>Reward For</th>
                                                        <th>User</th>
                                                        <th>Phone</th>
                                                        <th>Email</th>
                                                        <th>Points</th>
                                                        <th>Voucher Value</th>
                                                        <th>Request Date</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Reward For</th>
                                                        <th>User</th>
                                                         <th>Phone</th>
                                                         <th>Email</th>
                                                        <th>Points</th>
                                                        <th>Voucher Value</th>
                                                        <th>Request Date</th>
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
                                            <h4 class="modal-title">Points Earned by User</h4>
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
            var oTable, controller = '<?php echo $this->controller; ?>', imagepath = '<?php echo REWARD_IMAGE_PATH ?>', no_img_url = '<?php echo DOMAIN_URL; ?>/admin/img/no-image.png';
            var url;
            url = controller + '/paginate';
            $(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');
                var btn_show_hide = parseInt('<?= $ADMINTYPE == 1 ? 1 : 0; ?>');

                var target = [{
                        "aTargets": [7], // Column to target
                        "mRender": function (data, type, full) {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            //var buttons = ' <a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + full['iRewardID'] + '/y"  class="btn btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a>';
                            var buttons = '';
                                //buttons += '<button title="Delete" class="btn btn-danger  marginright10 margintop10"  onclick="return validateRemove(' + data + ',' + "'" + controller + "/deleteAll'" + ');"><i class="fa fa-times"></i> Delete</button>';
                                if (full['eStatus'] === "Pending") {
                                    buttons += '<div id="aceept-reject-' + full['iRewardRequestID'] + '">';
                                    buttons += '<a title="Click here to inactive" id="atag' + full['iRewardRequestID'] + '" onclick="return changeActiveDeactive(' + data + ',' + "'" + controller + "/status/" + full['iRewardRequestID'] + "/y'" + ',1)"  class="btn btn-success btn-sm marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Accept </a>';
                                    buttons += '<a title="Click here to Active" id="atag' + full['iRewardRequestID'] + '" onclick="return changeActiveDeactive(' + data + ',' + "'" + controller + "/status/" + full['iRewardRequestID'] + "/n'" + ',2)"  class="btn btn-inverse btn-sm marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Reject </a>';
                                    buttons += '</div>';
                                } else {
                                    buttons += '<div id="aceept-reject">';
                                    buttons += '<strong>' + full['eStatus'] + 'ed </strong>';
                                    buttons += '</div>';
                                }
                                var detailButton    = '<a href="#table-modal" data-toggle="modal" class="btn btn-sm btn-primary marginright10 margintop10 btn-view-detail btn-xs " data-user-id="' + full['iUserID'] + '">Point History</a>';
                                buttons  += detailButton;
                                //}

                            return buttons;
                        },
                    }];
                var aoculumn = [
                    /*0*/ {"mData": "vRewardTitle", "sWidth": "25%"},
                    /*1*/ {"mData": "vFullName", "sWidth": "25%"},
                    /*1*/ {"mData": "vMobileNo", "sWidth": "15%"},
                    /*1*/ {"mData": "vEmail", "sWidth": "25%"},
                    /*2*/ {"mData": "iRewardPoint", "sWidth": "15%"},
                    /*3*/ {"mData": "iRewardVoucher", "sWidth": "15%"},
                    /*4*/ {"mData": "requestDate", "sWidth": "15%"},
                    /*5*/ {"mData": "iRewardRequestID", bSortable: false, bSearchable: false, "sWidth": "20%"}
                ];
                var delete_val = btn_show_hide === 1 ? controller + '/deleteAll' : '';
                getdatatable(delete_val, url, aoculumn, target, 0, 'desc');
                $('.btn-view-detail').live('click', function () {
                    var $btn = $(this);
                    var target_id = $btn.data('user-id');
                    //alert(asset_id);

                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        data: {},
                        url: BASEURL + controller + '/userPointHistory/' + target_id,
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