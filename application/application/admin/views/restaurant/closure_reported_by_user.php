<?php
$headerData = $this->headerlib->data();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 36);

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
                                        <div class="description">Restaurant Closure Reported By User</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i>Restaurant Closure Reported By User List</h4>
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
                                                        <th>Restaurant</th>
                                                        <th>User</th>
                                                        <th>Created</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Restaurant</th>
                                                        <th>User</th>
                                                        <th>Created</th>
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
        <script>
            var oTable, controller = '<?php echo $this->controller; ?>';
            var url;
            url = controller + '/paginate_closureReportedByUser';
            
            function changeAcceptedStatus(adrs,element){
                $.ajax({
                    url: BASEURL + adrs,
                    type: 'POST',
                    data: '',
                    success: function (output_string) {
                        if (output_string === '1') {
                            if ($("#" + element).hasClass('btn-success')) {
                                $("#" + element).html('<i class="fa fa-times-circle-o "></i> Not Accepted ');
                                $("#" + element).switchClass('btn-success', 'btn-inverse');
                            } else if ($("#" + element).hasClass('btn-inverse')) {
                                $("#" + element).html('<i class="fa fa-check-circle-o "></i> Accepted ');
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

                var target = [
                    {
                        "aTargets": [3], // Column to target
                        "mRender": function (data, type, full) {
                            var buttons = '';
                            if (full['is_accepted'] === "1") {
                                buttons += '<a id="status' + full['iReportClosureId'] + '"  onclick="return changeAcceptedStatus(' + "'" + controller + "/closureReportedByUserStatus/" + full['iReportClosureId'] + "/y'" + ', \'status' + full['iReportClosureId'] + '\')"  class="btn btn-success btn-xs  marginright10 margintop10"><i class="fa fa-check-circle"></i> Accepted </a>';
                            }else{
                                buttons += '<a id="status' + full['iReportClosureId'] + '"  onclick="return changeAcceptedStatus(' + "'" + controller + "/closureReportedByUserStatus/" + full['iReportClosureId'] + "/y'" + ', \'status' + full['iReportClosureId'] + '\')"  class="btn btn-inverse btn-xs  marginright10 margintop10"><i class="fa fa-check-circle"></i> Not Accepted </a>';
                            }
                            return buttons;
                        }
                    }
                ];
                var aoculumn = [
                    /*0*/ {"mData": "restaurantName", "sWidth": "15%"},
                    /*1*/ {"mData": "userName", "sWidth": "20%"},
                    /*7*/ {"mData": "tCreatedAt", "sWidth": "15%"},
                    /*8*/ {"mData": "iReportClosureId", "sWidth": "15%"}
                ];
                getdatatable(controller + '/deleteAll', url, aoculumn, target, 1, 'desc');


            });

        </script>

    </body>